<?php
namespace PHPMailer\PHPMailer;

/**
 * PHPMailer RFC821 SMTP email transport class.
 * Implements RFC 821 SMTP commands and provides some
 * extensions for POP3 and ESMTP.
 * Version: 3.0.2 (minimal version for Bricola)
 */
class SMTP
{
    const VERSION = '6.9.1';
    const LE = "\r\n";
    const DEFAULT_PORT = 25;
    const DEFAULT_TIMEOUT = 300;
    const MAX_LINE_LENGTH = 998;
    const MAX_REPLY_LENGTH = 512;
    const DEBUG_OFF = 0;
    const DEBUG_CLIENT = 1;
    const DEBUG_SERVER = 2;
    const DEBUG_CONNECTION = 3;
    const DEBUG_LOWLEVEL = 4;

    public $Version = self::VERSION;
    public $SMTP_PORT = 25;
    public $CRLF = self::LE;
    public $do_debug = self::DEBUG_OFF;
    public $Debugoutput = 'echo';
    public $do_verp = false;
    public $Timeout = self::DEFAULT_TIMEOUT;
    public $Timelimit = self::DEFAULT_TIMEOUT;
    public $smtp_transaction_id_patterns = [
        'exim' => '/[\d]{3} OK id=(.*)/',
        'sendmail' => '/[\d]{3} 2.0.0 (.*) Message/',
        'postfix' => '/[\d]{3} 2.0.0 Ok: queued as (.*)/',
        'Microsoft_ESMTP' => '/[A-Z]{2}[\d]* (.*)/',
        'Amazon_SES' => '/[\d]{3} Ok (.*)/',
        'SendGrid' => '/[\d]{3} Ok: queued as (.*)/',
        'CampaignMonitor' => '/[\d]{3} 2.0.0 OK:(.*)/',
        'Haraka' => '/[\d]{3} Message Queued \((.*)\)/',
        'ZoneMTA' => '/[\d]{3} Message queued as (.*)/',
        'Mailjet' => '/[\d]{3} OK queued as (.*)/',
    ];
    public $last_smtp_transaction_id;
    protected $smtp_conn;
    protected $error = ['error' => '', 'detail' => '', 'smtp_code' => '', 'smtp_code_ex' => ''];
    protected $helo_rply;
    protected $server_caps;
    protected $last_reply = '';

    protected function edebug($str, $level = 0)
    {
        if ($level > $this->do_debug) {
            return;
        }
        if (is_callable($this->Debugoutput) && !in_array($this->Debugoutput, ['error_log', 'html', 'echo'])) {
            call_user_func($this->Debugoutput, $str, $level);
            return;
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo gmdate('Y-m-d H:i:s'), ' ', htmlspecialchars($str, ENT_QUOTES), "<br>\n";
                break;
            case 'echo':
            default:
                $str = preg_replace('/(\r\n|\r|\n)/ms', "\n", $str);
                echo gmdate('Y-m-d H:i:s'), "\t", trim($str), "\n";
        }
    }

    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if ($this->connected()) {
            $this->setError('Already connected to a server');
            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_PORT;
        }
        $this->edebug("Connection: opening to $host:$port, timeout=$timeout, options=" . (count($options) > 0 ? var_export($options, true) : 'array()'), self::DEBUG_CONNECTION);
        $errno = 0;
        $errstr = '';
        if (count($options) > 0 && PHP_VERSION_ID >= 50600 && PHP_MAJOR_VERSION >= 7) {
            $socket_context = stream_context_create($options);
            set_error_handler([$this, 'errorHandler']);
            $this->smtp_conn = stream_socket_client($host . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $socket_context);
        } else {
            set_error_handler([$this, 'errorHandler']);
            $this->smtp_conn = fsockopen($host, $port, $errno, $errstr, $timeout);
        }
        restore_error_handler();
        if (!is_resource($this->smtp_conn)) {
            $this->setError('Failed to connect to server', $errno, $errstr);
            $this->edebug('SMTP ERROR: ' . $this->error['error'] . ": $errstr ($errno)", self::DEBUG_CLIENT);
            return false;
        }
        $this->edebug('Connection: opened', self::DEBUG_CONNECTION);
        stream_set_timeout($this->smtp_conn, $timeout, 0);
        $announce = $this->get_lines();
        $this->edebug('SERVER -> CLIENT: ' . $announce, self::DEBUG_SERVER);
        return true;
    }

    public function startTLS()
    {
        if (!$this->sendCommand('STARTTLS', 'STARTTLS', 220)) {
            return false;
        }
        $streamCryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $streamCryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $streamCryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }
        set_error_handler([$this, 'errorHandler']);
        $crypto_ok = stream_socket_enable_crypto($this->smtp_conn, true, $streamCryptoMethod);
        restore_error_handler();
        return (bool) $crypto_ok;
    }

    public function authenticate($username, $password, $authtype = null, $OAuth = null)
    {
        if (!$this->server_caps) {
            $this->setError('Authentication is not allowed before HELO/EHLO');
            return false;
        }
        if (array_key_exists('EHLO', $this->server_caps)) {
            if (!array_key_exists('AUTH', $this->server_caps)) {
                $this->setError('AUTH command is not supported by the server');
                return false;
            }
            $this->server_caps['AUTH'];
        }
        if (empty($authtype)) {
            foreach (['CRAM-MD5', 'LOGIN', 'PLAIN', 'XOAUTH2'] as $authtype) {
                if (in_array($authtype, $this->server_caps['AUTH'] ?? [])) {
                    break;
                }
            }
        }
        switch ($authtype) {
            case 'PLAIN':
                if (!$this->sendCommand('AUTH', 'AUTH PLAIN ' . base64_encode("\0" . $username . "\0" . $password), 235)) {
                    return false;
                }
                break;
            case 'LOGIN':
                if (!$this->sendCommand('AUTH', 'AUTH LOGIN', 334)) {
                    return false;
                }
                if (!$this->sendCommand('Username', base64_encode($username), 334)) {
                    return false;
                }
                if (!$this->sendCommand('Password', base64_encode($password), 235)) {
                    return false;
                }
                break;
            case 'XOAUTH2':
                $oauthStr = base64_encode('user=' . $username . "\001auth=Bearer " . ($OAuth ? $OAuth->getOauth64() : '') . "\001\001");
                if (!$this->sendCommand('AUTH', 'AUTH XOAUTH2 ' . $oauthStr, [235, 334])) {
                    return false;
                }
                break;
            case 'CRAM-MD5':
                if (!$this->sendCommand('AUTH CRAM-MD5', 'AUTH CRAM-MD5', 334)) {
                    return false;
                }
                $challenge = base64_decode(substr($this->last_reply, 4));
                $response = $username . ' ' . $this->hmac($challenge, $password);
                if (!$this->sendCommand('Username', base64_encode($response), 235)) {
                    return false;
                }
                break;
            default:
                $this->setError("Authentication method \"$authtype\" is not supported");
                return false;
        }
        return true;
    }

    protected function hmac($data, $key)
    {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }
        $bytelen = 64;
        if (strlen($key) > $bytelen) {
            $key = pack('H*', md5($key));
        }
        $key    = str_pad($key, $bytelen, chr(0x00));
        $ipad   = str_pad('', $bytelen, chr(0x36));
        $opad   = str_pad('', $bytelen, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }

    public function connected()
    {
        if (is_resource($this->smtp_conn)) {
            $sock_status = stream_get_meta_data($this->smtp_conn);
            if ($sock_status['eof']) {
                $this->edebug('SMTP NOTICE: EOF caught while checking if connected', self::DEBUG_CLIENT);
                $this->close();
                return false;
            }
            return true;
        }
        return false;
    }

    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->smtp_conn)) {
            fclose($this->smtp_conn);
            $this->smtp_conn = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    public function data($msg_data)
    {
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $msg_data));
        foreach ($lines as $field) {
            if (strlen($field) > 0 && $field[0] === '.') {
                $field = '.' . $field;
            }
            $this->client_send($field . self::LE, 'DATA');
        }
        return $this->sendCommand('DATA END', '.', 250);
    }

    public function hello($host = '')
    {
        return $this->sendHello('EHLO', $host) or $this->sendHello('HELO', $host);
    }

    protected function sendHello($hello, $host)
    {
        $noerror = $this->sendCommand($hello, $hello . ' ' . $host, 250);
        $this->helo_rply = $this->last_reply;
        if ($noerror) {
            $this->parseCapabilities($this->last_reply);
        }
        return $noerror;
    }

    public function mail($from)
    {
        $useVerp = ($this->do_verp ? ' XVERP' : '');
        return $this->sendCommand('MAIL FROM', 'MAIL FROM:<' . $from . '>' . $useVerp, 250);
    }

    public function quit($close_on_error = true)
    {
        $noerror = $this->sendCommand('QUIT', 'QUIT', 221);
        $err = $this->error;
        if ($noerror || $close_on_error) {
            $this->close();
            $this->error = $err;
        }
        return $noerror;
    }

    public function recipient($address, $dsn = '')
    {
        if (empty($dsn)) {
            $rcpt = 'RCPT TO:<' . $address . '>';
        } else {
            $dsn = strtoupper($dsn);
            $notify = [];
            if (strpos($dsn, 'NEVER') !== false) {
                $notify[] = 'NEVER';
            } else {
                foreach (['SUCCESS', 'FAILURE', 'DELAY'] as $value) {
                    if (strpos($dsn, $value) !== false) {
                        $notify[] = $value;
                    }
                }
            }
            $rcpt = 'RCPT TO:<' . $address . '> NOTIFY=' . implode(',', $notify);
        }
        return $this->sendCommand('RCPT TO', $rcpt, [250, 251]);
    }

    public function reset()
    {
        return $this->sendCommand('RSET', 'RSET', 250);
    }

    public function sendCommand($command, $commandstring, $expect)
    {
        if (!$this->connected()) {
            $this->setError("Called $command without being connected");
            return false;
        }
        $commandstring = str_replace(["\r", "\n"], '', $commandstring);
        $this->client_send($commandstring . self::LE, $command);
        $this->last_reply = $this->get_lines();
        $matches = [];
        if (preg_match('/^(\d{3})[ -]/', $this->last_reply, $matches)) {
            $code = (int)$matches[1];
        } else {
            $code = 0;
        }
        $this->edebug('SERVER -> CLIENT: ' . $this->last_reply, self::DEBUG_SERVER);
        if (!in_array($code, (array) $expect)) {
            $this->setError("$command command failed", '', $code, $this->last_reply);
            $this->edebug('SMTP ERROR: ' . $this->error['error'] . ': ' . $this->last_reply, self::DEBUG_CLIENT);
            return false;
        }
        $this->setError('');
        return true;
    }

    public function client_send($data, $command = '')
    {
        $this->edebug('CLIENT -> SERVER: ' . $data, self::DEBUG_CLIENT);
        set_error_handler([$this, 'errorHandler']);
        if (in_array($command, ['User & Password', 'Username', 'Password']) || $command === '') {
            $this->edebug('CLIENT -> SERVER: [credentials hidden]', self::DEBUG_CLIENT);
        } else {
            $this->edebug("CLIENT -> SERVER: $data", self::DEBUG_CLIENT);
        }
        $result = fwrite($this->smtp_conn, $data);
        restore_error_handler();
        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getServerExtList()
    {
        return $this->server_caps;
    }

    public function getServerExt($name)
    {
        if (!$this->server_caps) {
            $this->setError('No SMTP connection open');
            return null;
        }
        if (!array_key_exists($name, $this->server_caps)) {
            return false;
        }
        return $this->server_caps[$name];
    }

    public function getLastReply()
    {
        return $this->last_reply;
    }

    protected function get_lines()
    {
        if (!is_resource($this->smtp_conn)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->smtp_conn, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->smtp_conn];
        $selW = null;
        while (is_resource($this->smtp_conn) && !feof($this->smtp_conn)) {
            set_error_handler([$this, 'errorHandler']);
            $n = stream_select($selR, $selW, $selW, $this->Timelimit);
            restore_error_handler();
            if ($n === false) {
                break;
            }
            $str = @fgets($this->smtp_conn, 515);
            $this->edebug("SMTP -> get_lines(): \$data is \"$data\"", self::DEBUG_LOWLEVEL);
            $this->edebug("SMTP -> get_lines(): \$str is  \"$str\"", self::DEBUG_LOWLEVEL);
            $data .= $str;
            if (!isset($str[3]) || ($str[3] === ' ' || $str[3] === "\r" || $str[3] === "\n") || substr($data, -2) === self::LE) {
                break;
            }
            $info = stream_get_meta_data($this->smtp_conn);
            if ($info['timed_out']) {
                $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): timelimit reached (' . $this->Timelimit . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
        }
        return $data;
    }

    protected function parseCapabilities($resp)
    {
        $this->server_caps = [];
        $lines = explode("\n", $resp);
        foreach ($lines as $n => $s) {
            $s = trim(rtrim($s, "\r"));
            if (!$n) {
                $this->server_caps['HELO'] = $s;
                continue;
            }
            if (!empty($s)) {
                $fields = explode(' ', $s);
                if (!empty($fields)) {
                    if (!$n) {
                        $name  = $s;
                        $fields = [];
                    } else {
                        $name   = array_shift($fields);
                        $fields = array_values($fields);
                    }
                    if (strlen($name) > 4) {
                        $name = substr($name, 4);
                    }
                    $this->server_caps[$name] = $fields;
                }
            }
        }
    }

    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error'       => $message,
            'detail'      => $detail,
            'smtp_code'   => $smtp_code,
            'smtp_code_ex'=> $smtp_code_ex,
        ];
    }

    protected function errorHandler($errno, $errmsg, $errfile = '', $errline = 0)
    {
        $notice = 'Connection failed.';
        $this->setError($notice, $errno, $errmsg);
        $this->edebug("$notice Error #$errno: $errmsg [$errfile line $errline]", self::DEBUG_CONNECTION);
    }

    public function getLastTransactionID()
    {
        $reply = $this->getLastReply();
        if (empty($reply)) {
            return false;
        }
        foreach ($this->smtp_transaction_id_patterns as $pattern) {
            $matches = [];
            if (preg_match($pattern, $reply, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }

    // -------------------------------------------------------
    // Méthodes requises par PHPMailer — ajoutées manuellement
    // -------------------------------------------------------

    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    public function setDebugLevel($level)
    {
        $this->do_debug = $level;
    }

    public function setDebugOutput($output)
    {
        $this->Debugoutput = $output;
    }

    public function setVerp($enabled)
    {
        $this->do_verp = $enabled;
    }
}
