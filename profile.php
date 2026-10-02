<?php
// Start session at the very top
session_start();
$current_time = time();

// If last activity time is set and 10 minutes have passed, destroy session
if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity'] > 10 * 60)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Update the last activity time to the current time
$_SESSION['last_activity'] = $current_time;

// Check for session expiration after 90 days of login
if (isset($_SESSION['login_time']) && ($current_time - $_SESSION['login_time'] > 90 * 24 * 60 * 60)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Set login time if not already set
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = $current_time;
}

include('dbcon.php'); // Database connection

// Fetch user data if logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Updated query to fetch name, email, and phone number
    $query = "SELECT name, email, phone, address FROM users WHERE id = ?";

    // Prepare the query and handle any errors
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Query preparation failed: ' . $conn->error);
    }

    // Bind the parameter and execute
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userName = $user['name'];
        $userEmail = $user['email'];
        $userPhone = $user['phone'];
        $userAddress = $user['address'];

        // Default CDN profile icon
        $profileImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200'; // Default CDN profile image
    } else {
        // Default values if no user found (e.g., user not logged in or session expired)
        $userName = 'Guest';
        $userEmail = 'N/A';
        $userPhone = 'N/A';
        $userAddress = 'N/A';
        $profileImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200'; // Default CDN profile image
    }
} else {
    // Not logged in: send the visitor to the login page.
    header('Location: sign/login.php');
    exit();
}

// CSRF Protection - Generate CSRF Token if it does not exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal</title>

    <meta name="description"
        content="Best Top-Up Service Provider in Banepa and all over Nepal. Sujan Top-Up offers fast and affordable gaming top-up services. Buy gaming coins, Free Fire diamonds, PUBG UC, TikTok coins, and more instantly. Trusted and reliable service across cities like Kathmandu, Pokhara, Lalitpur, Bhaktapur, Biratnagar, and more.">
    <meta name="keywords"
        content="best top-up service in Nepal, top-up service Banepa, gaming top-up Nepal, Free Fire diamonds Nepal, PUBG UC Nepal, TikTok coins Nepal, online game recharge Nepal, affordable top-up services, game currency Nepal, gaming recharge Kathmandu, cheap gaming coins Nepal">
    <link href="assets/img/logo11.png" rel="icon" sizes="32x32">
    <link href="assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">
    <link rel="manifest" href="manifest.json">
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fetch.css">
    <link rel="stylesheet" href="assets/css/pro.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="brand">
      <img src="assets/img/logo11.png" alt="sujan">
      <span>SUJAN TOPUP</span>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">
      <div></div>
      <div></div>
      <div></div>
    </div>
    <div class="menu">
    <a href="index.php">Home</a>
    <a href="index.php#pricing">Topup</a>
    <a href="index.php#team">Team</a>
    <a href="index.php#contact">Contact</a>
    <a href="index.php#about">About</a>

    <?php
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // If logged in, show profile dropdown with user info
        echo '<div class="nav-item dropdown no-arrow" style="display: inline-block; margin-left: 20px;">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                   style="display: flex; align-items: center; color: #4e73df; text-decoration: none;">
                    <img class="img-profile rounded-circle" src="' . $profileImage . '" style="width: 30px; height: 30px; margin-right: 10px;">
                    <span class="ml-2 text-gray-600 small" style="font-size: 14px;">' . $userName . '</span>
                </a> 
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown" 
                     style="min-width: 200px; padding: 10px 0; border-radius: 6px; box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 20px;">
                    <span class="ml-2 text-gray-600 small" style="font-size: 14px; padding-left:15px; color:rgb(6, 6, 7);">' . $userName . '</span><br>
                    <span class="ml-2 text-gray-600 small" style="font-size: 14px;padding-left:15px; color:rgb(8, 8, 10);">' . $userPhone . '</span><br>
                    <span class="ml-2 text-gray-600 small" style="font-size: 14px;padding-left:15px; color:rgb(5, 5, 5);">' . $userEmail . '</span>
                    <a class="dropdown-item" href="logout.php" style="font-size: 14px; padding: 8px 16px; font-weight:bold; color:rgb(10, 10, 10); text-decoration: none; display: flex; align-items: center;">
                        Logout
                    </a>
                </div>
            </div>';
    } else {
        // If not logged in, show login button
        echo '<a href="sign/login.php" style="margin-left: 20px; color: #4e73df; text-decoration: none; font-size: 14px;">Login</a>';
    }
    ?>
</div>

  </nav>
 <br><br><br>
 
<!-- Profile Page Content -->
<div class="divter">
    <h2>Manage Your Account</h2>
    <h3><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h3>
    <p>Email: <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Phone: <?php echo htmlspecialchars($userPhone, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Address: <?php echo htmlspecialchars($userAddress, ENT_QUOTES, 'UTF-8'); ?></p>
    

    <div class="file-buttons">
        <!-- Edit Profile Button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">Edit Details</button>

        <!-- Change Password Button -->
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="update_profile.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" value="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" value="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" value="<?php echo htmlspecialchars($userPhone, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <input type="text" class="form-control" id="editAddress" name="address" value="<?php echo htmlspecialchars($userAddress, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="update_password.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Function to validate form input
    function validateForm(event) {
        // Prevent form submission until validation passes
        event.preventDefault();
        
        // Validate Name (only letters and spaces)
        const name = document.getElementById("editName").value;
        if (!/^[a-zA-Z\s]+$/.test(name)) {
            alert("Name should only contain letters and spaces.");
            return false;
        }

        // Validate Email format
        const email = document.getElementById("editEmail").value;
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Validate Phone (only numbers)
        const phone = document.getElementById("editPhone").value;
        if (!/^\d{10}$/.test(phone)) {
            alert("Phone number should be exactly 10 digits.");
            return false;
        }

        // Validate Address (not empty)
        const address = document.getElementById("editAddress").value;
        if (address.trim() === "") {
            alert("Address cannot be empty.");
            return false;
        }

        // If all validations pass, submit the form
        alert("Personal Data submitted successfully!");
        document.querySelector("form").submit();
    }

    // Function to validate password strength
    function validatePasswordStrength() {
        const password = document.getElementById("newPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;

        // Password must be at least 8 characters, with at least one number and one special character
        const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordPattern.test(password)) {
            alert("Password must be at least 8 characters long, contain at least one number, and one special character.");
            return false;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        // If password is strong, submit the form
        alert("Password changed successfully!");
        return true;
    }

    // Attach form validation to the edit profile form and change password form
    document.querySelector("form[action='update_profile.php']").addEventListener("submit", validateForm);
    document.querySelector("form[action='update_password.php']").addEventListener("submit", function(event) {
        if (!validatePasswordStrength()) {
            event.preventDefault();
        }
    });

    // Escape user input to prevent XSS attacks
    function escapeHtml(str) {
        return str.replace(/[&<>"'`=/]/g, function(s) {
            return '&#' + s.charCodeAt(0) + ';';
        });
    }

    // Protect against SQL injection by sanitizing inputs (demo purposes)
    function sanitizeInput(input) {
        return input.replace(/[^a-zA-Z0-9_@.-\s]/g, "");
    }

    // Secure submission of data via AJAX (alternative to traditional form submission)
    function submitFormViaAjax(event, form) {
        event.preventDefault();
        const formData = new FormData(form);
        
        // Securely sanitize inputs before submitting
        formData.forEach((value, key) => {
            formData.set(key, sanitizeInput(value));
        });

        fetch(form.action, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => alert("Data submitted successfully: " + data))
        .catch(error => console.error("Error:", error));
    }
</script>

<!-- Include Bootstrap JS and jQuery (necessary for modal functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


         
  <!-- Footer -->
  <footer class="footer">
    <p>© 2025 Your Company. All rights reserved.</p>
  </footer>

  <script>
    // Toggle menu for responsive navbar
    function toggleMenu() {
  const menu = document.querySelector('.navbar .menu');
  menu.classList.toggle('active');
}

// Close the menu if the user clicks outside of the menu or the menu toggle
document.addEventListener('click', function(event) {
  const menu = document.querySelector('.navbar .menu');
  const menuToggle = document.querySelector('.navbar .menu-toggle');
  
  // Check if the clicked element is not the menu or the menu toggle
  if (!menu.contains(event.target) && !menuToggle.contains(event.target)) {
    menu.classList.remove('active');
  }
}); 
  </script>
  
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
