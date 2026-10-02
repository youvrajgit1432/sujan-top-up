<?php
/**
 * Sujan Top-Up - Shared mailer helper.
 *
 * Centralises PHPMailer configuration so credentials live in .env only.
 * All callers must handle the "not configured" case gracefully: the public
 * demo must run without SMTP.
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

if (!function_exists('sujan_send_mail')) {
    /**
     * Attempt to send an email.
     *
     * @return array{ok: bool, error: string}
     */
    function sujan_send_mail(string $to, string $subject, string $htmlBody): array
    {
        if (!mail_configured()) {
            return [
                'ok' => false,
                'error' => 'Email delivery is not configured in this local demo.',
            ];
        }

        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            return [
                'ok' => false,
                'error' => 'Email library is not installed. Run "composer install".',
            ];
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = (string) env('MAIL_USERNAME');
            $mail->Password = (string) env('MAIL_PASSWORD');
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) env('MAIL_PORT', 587);

            $from = (string) env('MAIL_FROM', (string) env('MAIL_USERNAME'));
            $fromName = (string) env('MAIL_FROM_NAME', APP_NAME);
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;

            $mail->send();

            return ['ok' => true, 'error' => ''];
        } catch (\Throwable $e) {
            error_log('Mail send failed: ' . $mail->ErrorInfo);
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        }
    }
}

if (!function_exists('sujan_otp_email_body')) {
    /** Standard OTP email body. */
    function sujan_otp_email_body(string $otp): string
    {
        $otp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        return "
        <html><body style='font-family:Arial,sans-serif;background:#f9f9f9;color:#333;padding:20px;'>
          <div style='max-width:600px;margin:auto;background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;'>
            <h2 style='text-align:center;color:#4CAF50;'>Welcome to " . htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') . "!</h2>
            <p>Hi Gamer,</p>
            <p>Use the following one-time code to continue:</p>
            <p style='font-size:24px;font-weight:bold;color:#ff5722;'>$otp</p>
            <p>This code is valid for <strong>2 minutes</strong>. Please do not share it with anyone.</p>
            <p style='font-size:12px;color:#999;text-align:center;'>This is an automated message. Please do not reply.</p>
          </div>
        </body></html>";
    }
}
