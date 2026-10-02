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

// Fetch total number of reviews and overall rating
$totalQuery = "SELECT COUNT(*) AS total_reviews, AVG(rating) AS avg_rating FROM feedback";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc();
$totalReviews = $totalData['total_reviews'];
$overallRating = round($totalData['avg_rating'], 1);
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

  <main class="content">
    <section id="scrollable-content" class="scrollable-content">
        <div class="section">
            <div class="center-container">
                <h3 style="text-align: center; margin-top: 20px;">Recent Users Reviews</h3>
                
                <!-- Display total reviews and overall rating -->
              

                <div style="text-align: center; margin: 20px 0;">
                    <!-- Filter Buttons -->
                    <a href="?filter=latest" class="filter-button">Latest</a>
                    <a href="?filter=best" class="filter-button">Best</a>
                    <a href="?filter=worst" class="filter-button">Worst</a>
                    <a href="?filter=oldest" class="filter-button">Oldest</a>
                </div>
             
                <div class="reviews-container" style="display: flex;font-weight:bold; justify-content: left; margin: 20px 0;">
    <h4 style="font-size: 1.3rem; color: #333; margin: 0;">
        Overall Rating: <?php echo $overallRating; ?> ⭐ 
        <span style="margin-left: 10px; font-size: 1.2rem; color: #555;">Total Reviews: <?php echo $totalReviews; ?></span>
    </h4>
</div>
<hr>

                <?php while ($row = $result->fetch_assoc()) { 
                    $avatar = !empty($row['avatar']) ? 'uploads/' . $row['avatar'] : 'assets/img/s1.png';
                    $rating = $row['rating'];
                    
                    // Set the game type
                    $gameType = $row['type']; // Assuming 'type' is a column in your feedback table
                    
                    // Determine the icon based on the game type
                    switch ($gameType) {
                        case 'Efootball(Ios)':
                            $iconImage = 'assets/img/54.jpeg'; // Path to the game-specific icon
                            break;
                        case 'Efootball(Android)':
                            $iconImage = 'assets/img/92.jpeg'; // Path to the game-specific icon
                            break;
                        case 'Clash Of Clan':
                            $iconImage = 'assets/img/90.jpg'; // Path to the game-specific icon
                            break;
                        case 'Mobile legend':
                            $iconImage = 'assets/img/92.jpeg'; // Path to the game-specific icon
                            break;
                        case 'Free Fire':
                            $iconImage = 'assets/img/93.jpeg'; // Path to the game-specific icon
                            break;
                        case 'PUBG':
                            $iconImage = 'assets/img/94.jpeg'; // Path to the game-specific icon
                            break;
                        case 'Overal Website and Service':
                            $iconImage = 'assets/img/s1.png'; // Path to the game-specific icon
                            break;
                        default:
                            $iconImage = 'assets/img/pubg.jpg'; // Default icon if no match
                            break;
                    }

                    // Check if the image exists before using it
                    if (!file_exists($iconImage)) {
                        $iconImage = 'assets/img/pubg.jpg'; // Use default if image not found
                    }
                ?>
                <div class="review">
                    <div class="review-header">
                        <!-- Display the game-specific icon -->
                        <img src="<?php echo $iconImage; ?>" alt="<?php echo htmlspecialchars($gameType); ?> Icon" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px;">
                        <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                    </div>

                    <div class="review-rating">
                        <?php
                        // Display stars for rating
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                // This star should be gold
                                echo '<span style="color: gold; font-size: 20px;">⭐</span>';
                            } else {
                                // This star should be white (empty)
                                echo '<span style="color: black; font-size: 20px;">☆</span>';
                            }
                        }
                        ?>
                    </div>

                    <div class="review-text">
                        <p><?php echo htmlspecialchars($row['review']); ?></p>
                    </div>
                </div>
                <hr>
                <?php } ?>

                <!-- Load More Button -->
                <div style="text-align: center; margin: 20px 0;">
                    <button id="load-more" class="filter-button">More</button>
                </div>
            </div>
        </div>
    </section>
</main>

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
  </script>
  
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
