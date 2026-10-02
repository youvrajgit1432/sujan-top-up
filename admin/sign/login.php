<?php
session_start();
if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Sujan Top-Up</title>
    <link href="../assets/img/logo11.png" rel="icon" sizes="32x32">
    <link href="../assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.login-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,.1);
    width: 320px;
}
h2 { text-align: center; margin-bottom: 20px; }
input[type="text"], input[type="password"] {
    width: 100%; padding: 10px; margin: 10px 0;
    border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;
}
button {
    width: 100%; padding: 10px; background-color: #4CAF50; color: #fff;
    border: none; border-radius: 5px; cursor: pointer;
}
button:hover { background-color: #45a049; }
.error { color: #b00020; text-align: center; margin-bottom: 20px; }
  </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error !== '') { echo "<p class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
        <form action="loginpro.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="login">Username/Email/Phone:</label>
            <input type="text" name="login" id="login" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
