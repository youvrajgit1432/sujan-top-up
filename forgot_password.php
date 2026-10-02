<?php
/**
 * Forgot password - request a reset link.
 *
 * Credentials come from .env. When email is not configured the page tells
 * the user instead of crashing.
 */

require_once __DIR__ . '/config/mailer.php';
require_once __DIR__ . '/dbcon.php';

$error_message = '';
$success_message = '';

if (isset($_POST['submit'])) {
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ?");
        if (!$stmt) {
            error_log('forgot_password prepare failed: ' . $conn->error);
            $error_message = "Something went wrong. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Always show the same message to avoid account enumeration.
            $success_message = "If that email is registered, a reset link has been sent.";

            if ($result->num_rows > 0) {
                $reset_token = bin2hex(random_bytes(16));
                $expiry_time = time() + 3600;

                $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
                if ($stmt_update) {
                    $stmt_update->bind_param("sis", $reset_token, $expiry_time, $email);
                    $stmt_update->execute();

                    $base = rtrim(APP_URL, '/');
                    $reset_link = $base . "/r.php?token=" . urlencode($reset_token) . "&expiry=" . $expiry_time;
                    $body = "Click the link below to reset your password:<br><a href='" . htmlspecialchars($reset_link, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($reset_link, ENT_QUOTES, 'UTF-8') . "</a>";

                    $mail = sujan_send_mail($email, 'Password Reset Request', $body);
                    if (!$mail['ok'] && !mail_configured()) {
                        $success_message = "Email delivery is not configured in this local demo, so no reset link was sent.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; color: #333; }
        h2 { text-align: center; color: #4CAF50; }
        form { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="submit"] { width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        input[type="submit"] { background-color: #4CAF50; color: #fff; border: none; cursor: pointer; font-weight: bold; }
        input[type="submit"]:hover { background-color: #45a049; }
        .error-message, .success-message { text-align: center; margin: 10px 0; padding: 10px; border-radius: 5px; font-weight: bold; }
        .error-message { background-color: #f44336; color: #fff; }
        .success-message { background-color: #4CAF50; color: #fff; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Forgot Password</h2>
        <?php if ($error_message !== '') { echo "<p class='error-message'>" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
        <?php if ($success_message !== '') { echo "<p class='success-message'>" . htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
        <div class="form-group">
            <label for="email">Enter your email:</label>
            <input type="email" name="email" id="email" placeholder="Your Email" required />
        </div>
        <div class="form-group">
            <input type="submit" name="submit" value="Send Reset Link" />
        </div>
    </form>
</body>
</html>
