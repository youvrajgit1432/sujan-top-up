<?php
// Set secure session cookie parameters before starting the session.
// cookie_secure is only enabled on HTTPS, so the demo also runs over HTTP.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) == 443);
ini_set('session.cookie_secure', $isHttps ? '1' : '0');
ini_set('session.cookie_httponly', '1'); // Prevent JavaScript access to session cookies
ini_set('session.use_only_cookies', '1'); // Ensure only cookies are used for sessions

session_start(); // Start the session

include('dbcon.php'); // Database connection file

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// CSRF Token generation (if not already set)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
}

// Brute force protection setup
$max_attempts = 5;
$lockout_time = 300; // Lockout for 5 minutes (300 seconds)

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user has exceeded the maximum attempts and the lockout time has passed
if ($_SESSION['login_attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
    die('Too many login attempts. Please try again later.');
} else {
    // Reset login attempts after lockout period
    if ((time() - $_SESSION['last_attempt_time']) >= $lockout_time) {
        $_SESSION['login_attempts'] = 0;
    }

    // Increment login attempts after a failed login
    if (isset($error_message)) {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    }
}

// Check if the form is submitted
if (isset($_POST['signin'])) {
    // Validate CSRF token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }

    // Get the input values and sanitize to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $_POST['your_name']);
    $password = mysqli_real_escape_string($conn, $_POST['your_pass']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        // Prepare the query to check if the user exists in the database
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Redirect to a secure page (e.g., dashboard)
                header('Location: ../index.php');
                exit();
            } else {
                // Incorrect password
                $error_message = "Invalid password!";
            }
        } else {
            // User not found
            $error_message = "User not found!";
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign In</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="main">
        <!-- Sign in Form -->
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="images/signin-image.jpg" alt="Sign up image"></figure>
                        <a href="signup.php" class="signup-image-link">Create an account</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Login</h2>

                        <?php 
                        if (isset($error_message)) {
                            echo "<p class='error-message'>$error_message</p>";
                        }
                        ?>

                        <form method="POST" class="register-form" id="login-form">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />

                            <div class="form-group">
                                <label for="your_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="your_name" id="your_name" placeholder="Your Email" required />
                            </div>
                            <div class="form-group">
                                <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="your_pass" id="your_pass" placeholder="Password" required />
                            </div>
   
                            <div class="form-group">
                                <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                                <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Log in"/>
                            </div>
                        </form>

                        <div class="social-login">
                            <span class="social-label">Or login with</span>
                            <ul class="socials">
                                <li><a href="signup.php"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="signup.php"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                <li><a href="signup.php"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
