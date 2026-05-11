<?php


require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    public static function sendVerification($toEmail, $toName, $token) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'khoukhmouhssine1@gmail.com'; 
            $mail->Password   = 'wanj pgvf ycqb ipmi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('khoukhmouhssine1@gmail.com', 'Bricola');
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = 'Vérifiez votre email — Bricola';

            $verifyLink = url('verify-email') . '?token=' . $token;
            $mail->Body = '
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;background:#f5f5f5;margin:0;padding:20px;">
<div style="max-width:500px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;">
    <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:30px;text-align:center;">
        <h1 style="color:#fff;margin:0;">🔧 Bricola</h1>
    </div>
    <div style="padding:30px;">
        <h2>Bonjour, ' . htmlspecialchars($toName) . ' 👋</h2>
        <p style="color:#64748b;">Cliquez ci-dessous pour vérifier votre email.</p>
        <div style="text-align:center;margin:30px 0;">
            <a href="' . $verifyLink . '" style="background:#6366f1;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;">
                ✅ Vérifier mon email
            </a>
        </div>
        <p style="color:#94a3b8;font-size:13px;">Ou copiez : <a href="' . $verifyLink . '">' . $verifyLink . '</a></p>
    </div>
</div>
</body></html>';
            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Erreur email: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
