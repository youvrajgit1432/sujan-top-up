<?php
/**
 * Password reset (token) page.
 *
 * Validates the token against the database and its expiry, then lets the
 * user set a new password. The reset token is single-use.
 */

session_start();
require_once __DIR__ . '/dbcon.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$success_message = '';
$token = (string) ($_GET['token'] ?? ($_POST['token'] ?? ''));

$tokenValid = false;

if ($token !== '') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > ?");
    $stmt->bind_param("si", $token, $now = time());
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tokenValid = true;
        $userId = (int) $result->fetch_assoc()['id'];

        if (isset($_POST['reset_password'])) {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token'])) {
                $error_message = "Invalid CSRF token.";
            } else {
                $new_password = (string) ($_POST['password'] ?? '');
                if (strlen($new_password) < 8) {
                    $error_message = "Password must be at least 8 characters long.";
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                    $stmt_update->bind_param("si", $hashed, $userId);
                    $stmt_update->execute();
                    $tokenValid = false;
                    $success_message = "Password has been reset successfully! You can now log in.";
                }
            }
        }
    } else {
        $error_message = "Invalid or expired token.";
    }
} else {
    $error_message = "Invalid reset link!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; color: #333; }
        form { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #4CAF50; }
        input[type="password"], input[type="submit"] { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        input[type="submit"] { background-color: #4CAF50; color: #fff; border: none; cursor: pointer; font-weight: bold; }
        .error-message { text-align: center; padding: 10px; border-radius: 5px; background-color: #f44336; color: #fff; }
        .success-message { text-align: center; padding: 10px; border-radius: 5px; background-color: #4CAF50; color: #fff; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Reset Password</h2>
        <?php if ($error_message !== '') { echo "<p class='error-message'>" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
        <?php if ($success_message !== '') { echo "<p class='success-message'>" . htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
        <?php if ($tokenValid): ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" minlength="8" required />
            <input type="submit" name="reset_password" value="Reset Password" />
        <?php endif; ?>
    </form>
</body>
</html>
