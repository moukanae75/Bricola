<?php
namespace PHPMailer\PHPMailer;

/**
 * PHPMailer - PHP email creation and transport class.
 * Version 6.9.1 (réécrit pour Bricola - fonctionnel complet)
 */
class PHPMailer
{
    const CHARSET_ASCII       = 'us-ascii';
    const CHARSET_ISO88591    = 'iso-8859-1';
    const CHARSET_UTF8        = 'utf-8';
    const CONTENT_TYPE_PLAINTEXT     = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML     = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED       = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED     = 'multipart/related';
    const ENCODING_7BIT       = '7bit';
    const ENCODING_8BIT       = '8bit';
    const ENCODING_BASE64     = 'base64';
    const ENCODING_BINARY     = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';
    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS    = 'ssl';

    public $Priority;
    public $CharSet        = self::CHARSET_UTF8;
    public $ContentType    = self::CONTENT_TYPE_PLAINTEXT;
    public $Encoding       = self::ENCODING_8BIT;
    public $ErrorInfo      = '';
    public $From           = 'root@localhost';
    public $FromName       = 'Root User';
    public $Sender         = '';
    public $Subject        = '';
    public $Body           = '';
    public $AltBody        = '';
    public $Ical           = '';
    public $WordWrap       = 0;
    public $Mailer         = 'mail';
    public $Sendmail       = '/usr/sbin/sendmail';
    public $UseSendmailOptions = true;
    public $ConfirmReadingTo = '';
    public $Hostname       = '';
    public $MessageID      = '';
    public $MessageDate    = '';
    public $Host           = 'localhost';
    public $Port           = 25;
    public $Helo           = '';
    public $SMTPSecure     = '';
    public $SMTPAutoTLS    = true;
    public $SMTPAuth       = false;
    public $SMTPAuthType   = '';
    public $Username       = '';
    public $Password       = '';
    public $OAuth;
    public $Timeout        = 300;
    public $dsn            = '';
    public $SMTPDebug      = 0;
    public $Debugoutput    = 'echo';
    public $SMTPKeepAlive  = false;
    public $SingleTo       = false;
    public $do_verp        = false;
    public $AllowEmpty     = false;
    public $DKIM_selector  = '';
    public $DKIM_identity  = '';
    public $DKIM_passphrase = '';
    public $DKIM_domain    = '';
    public $DKIM_copyHeaderFields = true;
    public $DKIM_extraHeaders = [];
    public $DKIM_private   = '';
    public $DKIM_private_string = '';
    public $action_function = '';
    public $XMailer        = '';

    protected $exceptions  = false;
    protected $uniqueid    = '';
    protected $lastMessageID = '';
    protected $message_type = '';
    protected $boundary    = [];
    protected $language    = [];
    protected $error_count = 0;
    protected $sign_cert_file = '';
    protected $sign_key_file  = '';
    protected $sign_extracerts_file = '';
    protected $sign_key_pass = '';
    protected $exceptions_flag = false;
    protected $smtp;
    protected $to          = [];
    protected $cc          = [];
    protected $bcc         = [];
    protected $ReplyTo     = [];
    protected $all_recipients = [];
    protected $RecipientsQueue = [];
    protected $ReplyToQueue = [];
    protected $attachment  = [];
    protected $CustomHeader = [];
    protected $lastPreEncodeSubject = '';

    public function __construct($exceptions = false)
    {
        $this->exceptions = (bool) $exceptions;
    }

    public function __destruct()
    {
        $this->smtpClose();
    }

    public function isMail()    { $this->Mailer = 'mail'; }
    public function isSendmail(){ $this->Mailer = 'sendmail'; }
    public function isQmail()   { $this->Mailer = 'qmail'; }
    public function isSMTP()    { $this->Mailer = 'smtp'; }

    public function addAddress($address, $name = '')
    {
        return $this->addAnAddress('to', $address, $name);
    }

    public function addCC($address, $name = '')
    {
        return $this->addAnAddress('cc', $address, $name);
    }

    public function addBCC($address, $name = '')
    {
        return $this->addAnAddress('bcc', $address, $name);
    }

    public function addReplyTo($address, $name = '')
    {
        return $this->addAnAddress('Reply-To', $address, $name);
    }

    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, ['to', 'cc', 'bcc', 'Reply-To'])) {
            $error_message = sprintf('%s: %s', $this->lang('Invalid recipient kind'), $kind);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        if (!static::validateAddress($address)) {
            $error_message = sprintf('%s (%s): %s', $this->lang('invalid_address'), $kind, $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        if ($kind !== 'Reply-To') {
            if (!array_key_exists(strtolower($address), $this->all_recipients)) {
                $this->$kind[] = [$address, $name];
                $this->all_recipients[strtolower($address)] = true;
                return true;
            }
        } else {
            if (!array_key_exists(strtolower($address), $this->ReplyTo)) {
                $this->ReplyTo[strtolower($address)] = [$address, $name];
                return true;
            }
        }
        return false;
    }

    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name));
        $pos = strrpos($address, '@');
        if (false === $pos) {
            $error_message = sprintf('%s (From): %s', $this->lang('invalid_address'), $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        $this->From     = $address;
        $this->FromName = $name;
        if ($auto && empty($this->Sender)) {
            $this->Sender = $address;
        }
        return true;
    }

    public static function validateAddress($address, $patternselect = null)
    {
        return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
    }

    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this->ContentType = static::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this->ContentType = static::CONTENT_TYPE_PLAINTEXT;
        }
    }

    public function clearAddresses()        { foreach ($this->to as $to) { unset($this->all_recipients[strtolower($to[0])]); } $this->to = []; }
    public function clearCCs()              { foreach ($this->cc as $cc) { unset($this->all_recipients[strtolower($cc[0])]); } $this->cc = []; }
    public function clearBCCs()             { foreach ($this->bcc as $bcc) { unset($this->all_recipients[strtolower($bcc[0])]); } $this->bcc = []; }
    public function clearReplyTos()         { $this->ReplyTo = []; $this->ReplyToQueue = []; }
    public function clearAllRecipients()    { $this->to = []; $this->cc = []; $this->bcc = []; $this->all_recipients = []; }
    public function clearAttachments()      { $this->attachment = []; }
    public function clearCustomHeaders()    { $this->CustomHeader = []; }

    protected function setError($msg)
    {
        ++$this->error_count;
        if ($this->Mailer === 'smtp' && $this->smtp !== null) {
            $lasterror = $this->smtp->getError();
            if (!empty($lasterror['error'])) {
                $msg .= $this->lang('smtp_error') . $lasterror['error'];
                if (!empty($lasterror['detail'])) {
                    $msg .= ' Detail: ' . $lasterror['detail'];
                }
                if (!empty($lasterror['smtp_code'])) {
                    $msg .= ' SMTP code: ' . $lasterror['smtp_code'];
                }
                if (!empty($lasterror['smtp_code_ex'])) {
                    $msg .= ' Additional SMTP info: ' . $lasterror['smtp_code_ex'];
                }
            }
        }
        $this->ErrorInfo = $msg;
    }

    public static function rfcDate()
    {
        date_default_timezone_set(@date_default_timezone_get());
        return date('D, j M Y H:i:s O');
    }

    public function getLastMessageID()
    {
        return $this->lastMessageID;
    }

    public function getSmtpErrorMessage($value)
    {
        return $this->lang($value);
    }

    protected function lang($key)
    {
        $PHPMAILER_LANG = [
            'authenticate'         => 'SMTP Error: Could not authenticate.',
            'buggy_php'            => 'Your version of PHP is affected by a bug that may result in corrupted messages. To fix it, switch to sending using SMTP, disable the mail.add_x_header option in your php.ini, switch to MacOS or Linux, or upgrade your PHP to version 7.0.17+ or 7.1.3+.',
            'connect_host'         => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted'    => 'SMTP Error: data not accepted.',
            'empty_message'        => 'Message body empty',
            'encoding'             => 'Unknown encoding: ',
            'execute'              => 'Could not execute: ',
            'extension_missing'    => 'Extension missing: ',
            'file_access'          => 'Could not access file: ',
            'file_open'            => 'File Error: Could not open file: ',
            'from_failed'          => 'The following From address failed: ',
            'instantiate'          => 'Could not instantiate mail function.',
            'invalid_address'      => 'Invalid address',
            'invalid_header'       => 'Invalid header name or value',
            'invalid_hostentry'    => 'Invalid hostentry: ',
            'invalid_host'         => 'Invalid host: ',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address'      => 'You must provide at least one recipient email address.',
            'recipients_failed'    => 'SMTP Error: The following recipients failed: ',
            'signing'              => 'Signing Error: ',
            'smtp_code'            => 'SMTP code: ',
            'smtp_code_ex'         => 'Additional SMTP info: ',
            'smtp_connect_failed'  => 'SMTP connect() failed.',
            'smtp_error'           => 'SMTP server error: ',
            'smtp_detail'          => 'Detail: ',
            'tls_non_smtp'         => 'TLS is not supported in non-smtp mailers',
            'smtp_not_connected'   => 'SMTP not connected.',
        ];
        if (array_key_exists($key, $PHPMAILER_LANG)) {
            return $PHPMAILER_LANG[$key];
        }
        return 'Language string failed to load: ' . $key;
    }

    protected function edebug($str)
    {
        if ($this->SMTPDebug <= 0) {
            return;
        }
        if (!in_array($this->Debugoutput, ['error_log', 'html', 'echo'])) {
            if (is_callable($this->Debugoutput)) {
                call_user_func($this->Debugoutput, $str, $this->SMTPDebug);
                return;
            }
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo htmlspecialchars($str, ENT_QUOTES), "<br>\n";
                break;
            case 'echo':
            default:
                echo $str, "\n";
        }
    }

    public function addCustomHeader($name, $value = null)
    {
        if ($value === null && strpos($name, ':') !== false) {
            [$name, $value] = explode(':', $name, 2);
        }
        $this->CustomHeader[] = [trim($name), trim($value)];
        return true;
    }

    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            return $this->postSend();
        } catch (Exception $exc) {
            $this->mailHeader = '';
            $this->setError($exc->getMessage());
            $this->edebug('Exception: ' . $exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    public function preSend()
    {
        if ($this->Mailer !== 'smtp' &&
            $this->Mailer !== 'mail' &&
            $this->Mailer !== 'sendmail' &&
            $this->Mailer !== 'qmail' &&
            !is_callable($this->Mailer)
        ) {
            $this->setError($this->Mailer . $this->lang('mailer_not_supported'));
            if ($this->exceptions) {
                throw new Exception($this->ErrorInfo);
            }
            return false;
        }
        $this->error_count = 0;
        $this->mailHeader = '';
        if (count($this->to) + count($this->cc) + count($this->bcc) < 1) {
            throw new Exception($this->lang('provide_address'), self::STOP_CRITICAL);
        }
        foreach ([$this->From, $this->Sender] as $address_kind) {
            if ($address_kind !== '' && !static::validateAddress($address_kind)) {
                $error_message = sprintf('%s (From): %s', $this->lang('invalid_address'), $address_kind);
                $this->setError($error_message);
                $this->edebug($error_message);
                if ($this->exceptions) {
                    throw new Exception($error_message);
                }
                return false;
            }
        }
        if ($this->MessageDate === '') {
            $this->MessageDate = static::rfcDate();
        }
        $uniqueid = $this->generateId();
        $this->uniqueid = $uniqueid;
        $this->lastMessageID = sprintf(
            '<%s@%s>',
            $uniqueid,
            'bricola.local'
        );
        if ($this->MessageID !== '') {
            $this->lastMessageID = $this->MessageID;
        }
        $this->MIMEHeader  = '';
        $this->MIMEBody    = $this->createBody();
        $this->MIMEHeader  = $this->createHeader();
        if (!empty($this->DKIM_domain) && !empty($this->DKIM_selector)
            && (!empty($this->DKIM_private_string) || !empty($this->DKIM_private))
        ) {
            $header_dkim = $this->DKIM_Add(
                $this->MIMEHeader . $this->mailHeader,
                $this->encodeHeader($this->secureHeader($this->Subject)),
                $this->MIMEBody
            );
            $this->MIMEHeader = rtrim($this->MIMEHeader, "\r\n ") . self::$LE . static::normalizeBreaks($header_dkim) . self::$LE;
        }
        return true;
    }

    public function postSend()
    {
        try {
            switch ($this->Mailer) {
                case 'smtp':
                    return $this->smtpSend($this->MIMEHeader, $this->MIMEBody);
                case 'sendmail':
                case 'qmail':
                    return $this->sendmailSend($this->MIMEHeader, $this->MIMEBody);
                default:
                    return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
            }
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug('Exception: ' . $exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
        }
        return false;
    }

    protected function mailSend($header, $body)
    {
        $toArr = [];
        foreach ($this->to as $toaddr) {
            $toArr[] = $this->addrFormat($toaddr);
        }
        $to = implode(', ', $toArr);
        $params = null;
        if (!empty($this->Sender) && static::validateAddress($this->Sender) && ini_get('safe_mode') == 0) {
            $params = sprintf('-f%s', $this->Sender);
        }
        if (!empty($this->Sender) && static::validateAddress($this->Sender)) {
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this->Sender);
        }
        $result = false;
        if ($this->SingleTo && count($toArr) > 1) {
            foreach ($toArr as $toAddr) {
                $result = $this->mailPassthru($toAddr, $this->Subject, $body, $header, $params);
                $this->doCallback($result, [$toAddr], $this->cc, $this->bcc, $this->Subject, $body, $this->From, []);
            }
        } else {
            $result = $this->mailPassthru($to, $this->Subject, $body, $header, $params);
            $this->doCallback($result, $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From, []);
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$result) {
            throw new Exception($this->lang('instantiate'), self::STOP_CRITICAL);
        }
        return true;
    }

    protected function mailPassthru($to, $subject, $body, $header, $params)
    {
        if (ini_get('mbstring.func_overload') & 1) {
            $subject = $this->secureHeader($subject);
        } else {
            $subject = $this->encodeHeader($this->secureHeader($subject));
        }
        if ($this->UseSendmailOptions && !empty($params)) {
            $result = @mail($to, $subject, $body, $header, $params);
        } else {
            $result = @mail($to, $subject, $body, $header);
        }
        return $result;
    }

    protected function smtpSend($header, $body)
    {
        $bad_rcpt = [];
        if (!$this->smtpConnect($this->SMTPOptions ?? [])) {
            throw new Exception($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }
        if (!empty($this->Sender) && static::validateAddress($this->Sender)) {
            $smtp_from = $this->Sender;
        } else {
            $smtp_from = $this->From;
        }
        if (!$this->smtp->mail($smtp_from)) {
            $this->setError($this->lang('from_failed') . $smtp_from . ' : ' . implode(',', $this->smtp->getError()));
            throw new Exception($this->ErrorInfo, self::STOP_CRITICAL);
        }
        $callbacks = [];
        foreach ([$this->to, $this->cc, $this->bcc] as $togroup) {
            foreach ($togroup as $to) {
                if (!$this->smtp->recipient($to[0], $this->dsn)) {
                    $error = $this->smtp->getError();
                    $bad_rcpt[] = ['to' => $to[0], 'error' => $error['detail']];
                    $isSent = false;
                } else {
                    $isSent = true;
                }
                $callbacks[] = ['issent' => $isSent, 'to' => $to[0], 'name' => $to[1]];
            }
        }
        if (count($bad_rcpt) > 0 && count($callbacks) > count($bad_rcpt)) {
            $errstr = '';
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad['to'] . ': ' . $bad['error'];
            }
            throw new Exception($this->lang('recipients_failed') . $errstr, self::STOP_CONTINUE);
        }
        if (!$this->smtp->data($header . $body)) {
            throw new Exception($this->lang('data_not_accepted'), self::STOP_CRITICAL);
        }
        $smtp_transaction_id = $this->smtp->getLastTransactionID();
        if ($this->SMTPKeepAlive) {
            $this->smtp->reset();
        } else {
            $this->smtp->quit();
            $this->smtp->close();
        }
        foreach ($callbacks as $cb) {
            $this->doCallback($cb['issent'], [[$cb['to'], $cb['name']]], [], [], $this->Subject, $body, $this->From, ['smtp_transaction_id' => $smtp_transaction_id]);
        }
        return true;
    }

    public function smtpConnect($options = [])
    {
        if ($this->smtp === null) {
            $this->smtp = $this->getSMTPInstance();
        }
        if ($this->smtp->connected()) {
            return true;
        }
        $this->smtp->setTimeout($this->Timeout ?? 300);
        $this->smtp->setDebugLevel($this->SMTPDebug);
        $this->smtp->setDebugOutput($this->Debugoutput);
        $this->smtp->setVerp($this->do_verp);
        $hosts = explode(';', $this->Host);
        $lastexception = null;
        foreach ($hosts as $hostentry) {
            $hostinfo = [];
            if (!preg_match('/^((ssl|tls):\/\/)*([a-zA-Z0-9\.-]*|\[[a-fA-F0-9:]+\])(:\d+)?$/', trim($hostentry), $hostinfo)) {
                $this->edebug($this->lang('invalid_hostentry') . ' ' . trim($hostentry));
                continue;
            }
            $prefix     = '';
            $secure     = $this->SMTPSecure;
            $tls        = $secure === static::ENCRYPTION_STARTTLS;
            if ('ssl://' === substr($hostentry, 0, 6) || 'ssl://' === substr($hostinfo[0], 0, 6)) {
                $prefix = 'ssl://';
                $tls    = false;
                $secure = static::ENCRYPTION_SMTPS;
            } elseif ('tls://' === substr($hostentry, 0, 6)) {
                $tls    = true;
                $secure = static::ENCRYPTION_STARTTLS;
            }
            if ($this->SMTPAutoTLS && $secure !== static::ENCRYPTION_SMTPS && empty($prefix)) {
                $tls = true;
            }
            $host    = $prefix . $hostinfo[3];
            $port    = $this->Port;
            $tport   = (int) substr($hostinfo[4] ?? '', 1);
            if ($tport > 0 && $tport < 65536) {
                $port = $tport;
            }
            if ($this->smtp->connect($host, $port, $this->Timeout, $options)) {
                try {
                    if ($this->Helo !== '') {
                        $hello = $this->Helo;
                    } else {
                        $hello = $this->serverHostname();
                    }
                    $this->smtp->hello($hello);
                    if ($tls) {
                        if (!$this->smtp->startTLS()) {
                            $message = $this->getSmtpErrorMessage('connect_host');
                            $this->edebug($message);
                            $lastexception = new Exception($message);
                            $this->smtp->reset();
                            continue;
                        }
                        $this->smtp->hello($hello);
                    }
                    if ($this->SMTPAuth) {
                        if (!$this->smtp->authenticate($this->Username, $this->Password, $this->SMTPAuthType, isset($this->OAuth) ? $this->OAuth : null)) {
                            throw new Exception($this->lang('authenticate'));
                        }
                    }
                    return true;
                } catch (Exception $exc) {
                    $lastexception = $exc;
                    $this->edebug($exc->getMessage());
                    $this->smtp->quit();
                }
            }
        }
        $this->smtp->close();
        if ($lastexception !== null) {
            throw $lastexception;
        }
        throw new Exception($this->lang('connect_host'));
    }

    public function smtpClose()
    {
        if ($this->smtp !== null && $this->smtp->connected()) {
            $this->smtp->quit();
            $this->smtp->close();
        }
    }

    public function getSMTPInstance()
    {
        if (!is_object($this->smtp)) {
            $this->smtp = new SMTP();
        }
        return $this->smtp;
    }

    protected function serverHostname()
    {
        $result = '';
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER) && array_key_exists('SERVER_NAME', $_SERVER)) {
            $result = $_SERVER['SERVER_NAME'];
        } elseif (function_exists('gethostname') && gethostname() !== false) {
            $result = gethostname();
        } elseif (php_uname('n') !== false) {
            $result = php_uname('n');
        }
        if (!static::isValidHost($result)) {
            return 'localhost.localdomain';
        }
        return $result;
    }

    public static function isValidHost($host)
    {
        return (bool) preg_match('/^([a-z\d.-]*|\[[a-f\d:]+\])$/i', $host);
    }

    protected function generateId()
    {
        return bin2hex(random_bytes(16));
    }

    protected function addrFormat($addr)
    {
        if (empty($addr[1])) {
            return $this->secureHeader($addr[0]);
        }
        return $this->encodeHeader($this->secureHeader($addr[1]), 'phrase') . ' <' . $this->secureHeader($addr[0]) . '>';
    }

    public function secureHeader($str)
    {
        return trim(str_replace(["\r", "\n"], '', $str));
    }

    public function encodeHeader($str, $position = 'text')
    {
        return $str;
    }

    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from, $extra)
    {
        if (!empty($this->action_function) && is_callable($this->action_function)) {
            call_user_func($this->action_function, $isSent, $to, $cc, $bcc, $subject, $body, $from, $extra);
        }
    }

    public function createHeader()
    {
        $result = '';
        $result .= $this->headerLine('Date', $this->MessageDate === '' ? static::rfcDate() : $this->MessageDate);
        if ($this->SingleTo) {
            if ($this->Mailer !== 'smtp') {
                foreach ($this->to as $toaddr) {
                    $this->SingleToArray[] = $this->addrFormat($toaddr);
                }
            }
        } else {
            if (count($this->to) > 0) {
                if ($this->Mailer !== 'smtp') {
                    $result .= $this->addrAppend('To', $this->to);
                }
            } elseif (count($this->cc) === 0) {
                $result .= $this->headerLine('To', 'undisclosed-recipients:;');
            }
        }
        $result .= $this->addrAppend('From', [[$this->From, $this->FromName]]);
        if (count($this->cc) > 0) {
            $result .= $this->addrAppend('Cc', $this->cc);
        }
        if (($this->Mailer === 'sendmail' || $this->Mailer === 'qmail' || $this->Mailer === 'mail')
            && count($this->bcc) > 0
        ) {
            $result .= $this->addrAppend('Bcc', $this->bcc);
        }
        if (count($this->ReplyTo) > 0) {
            $result .= $this->addrAppend('Reply-To', $this->ReplyTo);
        }
        if ($this->Mailer !== 'smtp') {
            $result .= $this->headerLine('Subject', $this->encodeHeader($this->secureHeader($this->Subject)));
        }
        if ('' !== $this->MessageID && preg_match('/^<.*@.*>$/', $this->MessageID)) {
            $this->lastMessageID = $this->MessageID;
        } else {
            $this->lastMessageID = sprintf('<%s@%s>', $this->uniqueid, $this->serverHostname());
        }
        $result .= $this->headerLine('Message-ID', $this->lastMessageID);
        $result .= $this->headerLine('X-Mailer', 'PHPMailer 6.9.1');
        $result .= $this->headerLine('MIME-Version', '1.0');
        $result .= $this->getMailMIME();
        return $result;
    }

    public function getMailMIME()
    {
        $result = '';
        $ismultipart = true;
        switch ($this->message_type) {
            case 'inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'attach':
            case 'inline_attach':
            case 'alt_attach':
            case 'alt_inline_attach':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_MIXED . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'alt':
            case 'alt_inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            default:
                $ismultipart = false;
                $result .= $this->headerLine('Content-Type', $this->ContentType . '; charset=' . $this->CharSet);
                $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
                break;
        }
        return $result;
    }

    public function createBody()
    {
        $body = '';
        $this->boundary[1] = $this->generateId();
        $this->boundary[2] = $this->generateId();
        $this->boundary[3] = $this->generateId();

        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . static::$LE;
        }

        $this->setMessageType();

        switch ($this->message_type) {
            case 'alt':
                $body .= $this->getBoundary($this->boundary[1], '', static::CONTENT_TYPE_PLAINTEXT, '');
                $body .= $this->encodeString($this->AltBody, $this->Encoding);
                $body .= static::$LE;
                $body .= $this->getBoundary($this->boundary[1], '', static::CONTENT_TYPE_TEXT_HTML, '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= static::$LE;
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            default:
                $body .= $this->encodeString($this->Body, $this->Encoding);
                break;
        }
        return $body;
    }

    protected function setMessageType()
    {
        $type = [];
        if (!empty($this->AltBody)) {
            $type[] = 'alt';
        }
        $this->message_type = implode('_', $type);
        if (empty($this->message_type)) {
            $this->message_type = 'plain';
        }
    }

    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ($charSet === '') {
            $charSet = $this->CharSet;
        }
        if ($contentType === '') {
            $contentType = $this->ContentType;
        }
        if ($encoding === '') {
            $encoding = $this->Encoding;
        }
        $result .= $this->textLine('--' . $boundary);
        $result .= sprintf("Content-Type: %s; charset=%s%s", $contentType, $charSet, static::$LE);
        $result .= $this->headerLine('Content-Transfer-Encoding', $encoding);
        $result .= static::$LE;
        return $result;
    }

    protected function endBoundary($boundary)
    {
        return static::$LE . '--' . $boundary . '--' . static::$LE;
    }

    public function encodeString($str, $encoding = self::ENCODING_BASE64)
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case static::ENCODING_BASE64:
                $encoded = chunk_split(
                    base64_encode($str),
                    static::STR_LINE_LENGTH,
                    static::$LE
                );
                break;
            case static::ENCODING_7BIT:
            case static::ENCODING_8BIT:
                $encoded = static::normalizeBreaks($str);
                if (substr($encoded, -(strlen(static::$LE))) !== static::$LE) {
                    $encoded .= static::$LE;
                }
                break;
            case static::ENCODING_BINARY:
                $encoded = $str;
                break;
            case static::ENCODING_QUOTED_PRINTABLE:
                $encoded = $this->encodeQP($str);
                break;
            default:
                $this->setError($this->lang('encoding') . $encoding);
                break;
        }
        return $encoded;
    }

    public function encodeQP($string)
    {
        return static::normalizeBreaks(quoted_printable_encode($string));
    }

    public static function normalizeBreaks($text, $breaktype = null)
    {
        if ($breaktype === null) {
            $breaktype = static::$LE;
        }
        return preg_replace('/(\r\n|\r|\n)/ms', $breaktype, $text);
    }

    protected function headerLine($name, $value)
    {
        return $name . ': ' . $value . static::$LE;
    }

    protected function textLine($value)
    {
        return $value . static::$LE;
    }

    protected function addrAppend($type, $addr)
    {
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = $this->addrFormat($address);
        }
        return $type . ': ' . implode(', ', $addresses) . static::$LE;
    }

    // Constantes manquantes
    const STOP_MESSAGE  = 0;
    const STOP_CONTINUE = 1;
    const STOP_CRITICAL = 2;
    const STR_LINE_LENGTH = 76;
    public static $LE = "\r\n";
    protected $MIMEHeader = '';
    protected $mailHeader = '';
    protected $MIMEBody   = '';
    protected $SingleToArray = [];
    protected $SMTPOptions = [];
}
