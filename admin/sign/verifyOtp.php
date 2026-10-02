<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputOtp = $_POST['otp'];

    // Validate OTP only if the attempts are below 10
    if (isset($_SESSION['otp']) && $inputOtp == $_SESSION['otp'] && time() < $_SESSION['otp_expiration']) {
        // OTP valid, log the user in
        $_SESSION['admin_logged_in'] = true;

        // Redirect to index page (logged in)
        header('Location: ../index.php');
        exit;
    } else {
        // OTP failed, reset login attempts and generate new OTP
        $_SESSION['login_attempts'] = 0;
        header('Location: verifyOtp.php?error=Invalid OTP or OTP expired. Please request a new OTP.');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form method="POST" action="verifyOtp.php">
        <label for="otp">Enter OTP: </label>
        <input type="text" name="otp" required />
        <button type="submit">Verify OTP</button>
    </form>

    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color:red;'>".$_GET['error']."</p>";
    }
    ?>
</body>
</html>
