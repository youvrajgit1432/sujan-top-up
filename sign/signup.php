<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up Form by Colorlib</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Password container to align input and eye icon inline */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container input {
            padding-right: 35px; /* Adds space on the right for the eye icon */
            width: 100%;
        }

        .password-container .zmdi-eye, .password-container .zmdi-eye-off {
            position: absolute;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
        }

        /* Adjust the form styles for consistency */
        .form-group {
            margin-bottom: 20px;
        }

        .form-submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .form-submit:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="main">
        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Sign up</h2>
                        <form method="POST" action="process_registration.php" id="register-form" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="name" id="name" placeholder="Your Name" required />
                            </div>
                            <div class="form-group">
                                <label for="phone"><i class="zmdi zmdi-phone"></i> </label>
                                <input type="tel" name="phone" id="phone" placeholder="Your Contact Number" required />
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Your Email" required />
                            </div>
                            
                            <div class="form-group">
    <label for="address"><i class="zmdi zmdi-home"></i> </label>
    <input type="text" name="address" id="address" placeholder="Billing Address" required />
</div>

                            <!-- Password Field with Eye Icon at the End -->
                            <div class="form-group password-container">
                                <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="pass" id="pass" placeholder="Password" required />
                                <span id="toggle-password" class="zmdi zmdi-eye" style="cursor: pointer;"></span>
                            </div>

                            <!-- Confirm Password Field with Eye Icon at the End -->
                            <div class="form-group password-container">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="Repeat your password" required />
                                <span id="toggle-re-pass" class="zmdi zmdi-eye" style="cursor: pointer;"></span>
                            </div>

                            <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>I agree all statements in <a href="#" class="term-service">Terms of service</a></label>
                            </div>

                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Register" />
                            </div>
                        </form>
                    </div>

                    <div class="signup-image">
                        <figure><img src="images/signup-image.jpg" alt="sign up image"></figure>
                        <a href="login.php" class="signup-image-link">I am already a member</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            var passField = document.getElementById('pass');
            var type = passField.type === 'password' ? 'text' : 'password';
            passField.type = type;

            // Toggle the eye icon (open/close)
            this.classList.toggle('zmdi-eye-off');
        });

        // Toggle confirm password visibility
        document.getElementById('toggle-re-pass').addEventListener('click', function() {
            var rePassField = document.getElementById('re_pass');
            var type = rePassField.type === 'password' ? 'text' : 'password';
            rePassField.type = type;

            // Toggle the eye icon (open/close)
            this.classList.toggle('zmdi-eye-off');
        });

        function validateForm() {
            // Disable the submit button to prevent multiple submissions
            document.getElementById('signup').disabled = true;

            // Get form data
            const password = document.getElementById('pass').value;
            const confirmPassword = document.getElementById('re_pass').value;

            // Validate password strength
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(password)) {
                alert('Password must be at least 8 characters long and include at least one letter, one number, and one special character.');
                document.getElementById('signup').disabled = false;
                return false;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                document.getElementById('signup').disabled = false;
                return false;
            }

            // All validations passed
            return true;
        }
    </script>
</body>
</html>
