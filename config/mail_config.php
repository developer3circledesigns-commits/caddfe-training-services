<?php

require_once __DIR__ . '/../lib/PHPMailer.php';
require_once __DIR__ . '/../lib/SMTP.php';
require_once __DIR__ . '/../lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function getMailConfig(): array
{
    $mailHost = getenv('SMTP_HOST');
    $mailUser = getenv('SMTP_USERNAME');
    $mailPass = getenv('SMTP_PASSWORD');

    if (empty($mailHost) || empty($mailUser) || empty($mailPass)) {
        $cfgFile = __DIR__ . '/app.php';
        if (file_exists($cfgFile)) {
            $cfg = require $cfgFile;
            $mailHost = $cfg['mail']['host'] ?? 'smtp.gmail.com';
            $mailUser = $cfg['mail']['username'] ?? 'Caddfe90@gmail.com';
            $mailPass = $cfg['mail']['password'] ?? '';
        } else {
            $mailHost = 'smtp.gmail.com';
            $mailUser = 'Caddfe90@gmail.com';
            $mailPass = '';
        }
    }

    return [$mailHost, $mailUser, $mailPass];
}

function sendContactEmail(array $data): bool
{
    $mail = new PHPMailer(true);

    try {
        [$mailHost, $mailUser, $mailPass] = getMailConfig();

        $mail->isSMTP();
        $mail->Host       = $mailHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailUser;
        $mail->Password   = $mailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $dkimPrivate = __DIR__ . '/dkim/private.pem';
        if (file_exists($dkimPrivate)) {
            $mail->DKIM_domain      = 'ghostwhite-donkey-624151.hostingersite.com';
            $mail->DKIM_private     = $dkimPrivate;
            $mail->DKIM_selector    = 'default';
            $mail->DKIM_passphrase  = '';
            $mail->DKIM_identity    = $mail->From;
        }

        $mail->setFrom('Caddfe90@gmail.com', 'CADDFE Contact Form');
        $mail->addAddress('Caddfe90@gmail.com', 'CADDFE Admin');
        $mail->addReplyTo($data['email'], $data['full_name']);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->addCustomHeader('X-Mailer', 'CADDFE-Contact/1.0');
        $mail->Subject = 'New Contact Form Submission: ' . $data['subject'];

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;'>
            <h2 style='color:#d8000d;border-bottom:2px solid #d8000d;padding-bottom:10px;'>New Contact Inquiry</h2>
            <table style='width:100%;border-collapse:collapse;margin-top:15px;'>
                <tr><td style='padding:8px 12px;background:#f8fafc;font-weight:700;width:120px;'>Name</td><td style='padding:8px 12px;'>{$data['full_name']}</td></tr>
                <tr><td style='padding:8px 12px;background:#f8fafc;font-weight:700;'>Email</td><td style='padding:8px 12px;'><a href='mailto:{$data['email']}'>{$data['email']}</a></td></tr>
                <tr><td style='padding:8px 12px;background:#f8fafc;font-weight:700;'>Phone</td><td style='padding:8px 12px;'>" . ($data['phone'] ?: 'N/A') . "</td></tr>
                <tr><td style='padding:8px 12px;background:#f8fafc;font-weight:700;'>Subject</td><td style='padding:8px 12px;'>{$data['subject']}</td></tr>
                <tr><td style='padding:8px 12px;background:#f8fafc;font-weight:700;vertical-align:top;'>Message</td><td style='padding:8px 12px;'>" . nl2br($data['message']) . "</td></tr>
            </table>
            <p style='margin-top:20px;font-size:12px;color:#94a3b8;'>Submitted via CADDFE Training Services contact form</p>
        </div>";

        $mail->Body = $body;
        $mail->AltBody = "New Contact Form Submission\n\nName: {$data['full_name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nSubject: {$data['subject']}\n\nMessage:\n{$data['message']}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail send failed: ' . $e->getMessage());
        return false;
    }
}

