<?php
/**
 * Sends the admin login OTP.
 *
 * Uses the shared mailer and central configuration. Never hard-codes
 * credentials, and degrades gracefully when SMTP is not configured.
 */

session_start();
require_once __DIR__ . '/config/mailer.php';

// Generate OTP and store in session
$otp = random_int(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_expiration'] = time() + 120;

$email = $_SESSION['login_email'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // No verified destination email; send the admin back to login.
    header('Location: admin/sign/login.php?error=' . urlencode('Could not determine an email for OTP delivery.'));
    exit;
}

$result = sujan_send_mail($email, 'Your OTP Code', sujan_otp_email_body((string) $otp));

if ($result['ok']) {
    header('Location: admin/sign/verifyOtp.php');
    exit;
}

if (!mail_configured()) {
    echo "<p>Email delivery is not configured in this local demo.</p>";
    echo "<p>Proceed to <a href='admin/sign/verifyOtp.php'>OTP verification</a>.";
    if (APP_DEBUG) {
        echo " <em>Debug:</em> your OTP is <strong>" . htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8') . "</strong>";
    }
    echo "</p>";
} else {
    echo "Mailer Error: " . htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8');
}
