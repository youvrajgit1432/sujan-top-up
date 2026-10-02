<?php
/**
 * Sends the registration OTP.
 *
 * Email credentials are read from .env via the shared mailer. When SMTP is
 * not configured the demo does not crash: the OTP is stored in the session
 * and the user is told email delivery is unavailable.
 */

session_start();
require_once __DIR__ . '/config/mailer.php';

if (!isset($_SESSION['registration_data'])) {
    echo "No registration data found in the session.";
    exit;
}

$registrationData = $_SESSION['registration_data'];
$email = $registrationData['email'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email address is missing or invalid.";
    exit;
}

// OTP generation and session setup
$otp = random_int(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_expiration'] = time() + 120; // 2 minutes

$result = sujan_send_mail($email, 'Your OTP Code', sujan_otp_email_body((string) $otp));

if ($result['ok']) {
    header('Location: sign/verify_otp.php');
    exit;
}

// Graceful fallback: no SMTP configured (or delivery failed).
if (!mail_configured()) {
    echo "<p>Email delivery is not configured in this local demo.</p>";
    echo "<p>For development you can proceed to <a href='sign/verify_otp.php'>OTP verification</a>.";
    if (APP_DEBUG) {
        echo "<p><em>Debug:</em> your OTP is <strong>" . htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8') . "</strong></p>";
    }
    echo "</p>";
} else {
    echo "Mailer Error: " . htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8');
}
