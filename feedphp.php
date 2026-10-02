<?php
// Start session at the very top
session_start();
$current_time = time();

// If last activity time is set and 10 minutes have passed, destroy session
if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity'] > 10 *10* 60)) {
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
    $query = "SELECT name, email, phone FROM users WHERE id = ?";

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

        // Default CDN profile icon
        $profileImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200'; // Default CDN profile image
    } else {
        // Default values if no user found (e.g., user not logged in or session expired)
        $userName = 'Guest';
        $userEmail = 'N/A';
        $userPhone = 'N/A';
        $profileImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200'; // Default CDN profile image
    }
} else {
    // If user is not logged in, use default values
    $userName = 'Guest';
    $userEmail = 'N/A';
    $userPhone = 'N/A';
    $profileImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200'; // Default CDN profile image
}

// CSRF Protection - Generate CSRF Token if it does not exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database Connection
include("dbcon.php");

// Default filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'latest';

// Filter query
switch ($filter) {
    case 'latest':
        $query = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT 10";
        break;
    case 'best':
        $query = "SELECT * FROM feedback ORDER BY rating DESC, created_at DESC LIMIT 10";
        break;
    case 'worst':
        $query = "SELECT * FROM feedback ORDER BY rating ASC, created_at DESC LIMIT 10";
        break;
    case 'oldest':
        $query = "SELECT * FROM feedback ORDER BY created_at ASC LIMIT 10";
        break;
    default:
        $query = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT 10";
        break;
}

// Fetch reviews from database
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
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
 


  <div>  
    <!-- Feedback Form Section -->
 
    <form action="feed.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">


    <h3 style="text-align: center;color:#2e2e2e; margin-top: 20px;font-weight:bold;">Share Your Feedback</h3>
   
  
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="username">Your Name:</label>
    <input type="text" id="username" name="username" required>

 
 


    <label for="type"> Select Game for Service Rating</label>
    <select name="type" id="type">
        <option value="Overal Website and Service">Overal Website and Service</option>
        <option value="PUBG">PUBG</option>
        <option value="Free Fire">Free Fire</option>
        <option value="Mobile legend">Mobile legend</option>
        <option value="Clash Of Clan">Clash Of Clan</option>
        <option value="Efootball(Android)">Efootball(Android)</option>
        <option value="Efootball(Ios)">Efootball(Ios)</option>
    
    </select>

    
    <label for="rating">Your Rating:</label>
    <select id="rating" name="rating" required>
        <option value="">Select a Rating</option>
        <option value="5">★★★★★ - Excellent</option>
        <option value="4">★★★★☆ - Very Good</option>
        <option value="3">★★★☆☆ - Good</option>
        <option value="2">★★☆☆☆ - Fair</option>
        <option value="1">★☆☆☆☆ - Poor</option>
    </select>

    <label for="review">Your Review:</label>
    <textarea id="review" name="review" rows="4" required></textarea>

    <button type="submit" class="submit-feedback-btn">Submit Feedback</button>
   
</form>

    </div>
    



<div>  
    
    </div>
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
document.getElementById('load-more').addEventListener('click', function () {
    const filter = new URLSearchParams(window.location.search).get('filter') || 'latest';
    let offset = document.querySelectorAll('.review').length;

    fetch(`load_more_reviews.php?filter=${filter}&offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            const container = document.querySelector('.review-container');
            const buttonContainer = document.getElementById('load-more').parentElement;

            // Insert the new reviews just before the button
            buttonContainer.insertAdjacentHTML('beforebegin', data);
        });
});


function validateForm() {
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address').value.trim();
    const rating = document.getElementById('rating').value;
    const review = document.getElementById('review').value.trim();
    const avatar = document.getElementById('avatar').files[0]; // Access the uploaded avatar file

    // Username Validation: Only letters and spaces, min 3 characters
    const usernameRegex = /^[A-Za-z\s]{3,}$/;
    if (!usernameRegex.test(username)) {
        alert('Username must contain only letters and spaces, with at least 3 characters.');
        return false;
    }

 
 

    // Rating Validation: Must select a rating
    if (rating === '') {
        alert('Please select a valid rating.');
        return false;
    }

    // Review Validation: Minimum 5 characters
    if (review.length < 5) {
        alert('Your review must be at least 5 characters long.');
        return false;
    }

 

    // Final Confirmation
    alert('Are You Sure to Submit?');
    return true; // Allow form submission
}


  </script>
  
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
