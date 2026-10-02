<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
        <section class="verify-otp">
            <div class="container">
                <div class="content">
                    <h2>Verify OTP</h2>
                    <form method="POST" action="complete_registration.php">
                        <div class="form-group">
                            <label for="otp"></label>
                            <input type="text" name="otp" id="otp" placeholder="Enter Your OTP" required>
                        </div>
                        <div class="form-button">
                            <input type="submit" name="verifyOtp" value="Verify OTP">
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
