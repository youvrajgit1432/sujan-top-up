<?php
session_start();
require_once __DIR__ . '/config/app.php';

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
?>

<!-- Add this JavaScript to automatically refresh the page after a certain period -->
<script>
    // Set a timer to refresh the page after 10 minutes (600,000 milliseconds)
    setTimeout(function() {
        window.location.reload(); // Reloads the page to check session expiration
    }, 600000); // 600000ms = 10 minutes
</script>


<!-- HTML content goes here for the index page -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title for SEO -->
    <title>Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal</title>

    <!-- Meta Description for SEO -->
    <meta name="description"
        content="Best Top-Up Service Provider in Banepa and all over Nepal. Sujan Top-Up offers fast and affordable gaming top-up services. Buy gaming coins, Free Fire diamonds, PUBG UC, TikTok coins, and more instantly. Trusted and reliable service across cities like Kathmandu, Pokhara, Lalitpur, Bhaktapur, Biratnagar, and more.">

    <!-- Meta Keywords for SEO -->
    <meta name="keywords"
        content="best top-up service in Nepal, top-up service Banepa, gaming top-up Nepal, Free Fire diamonds Nepal, PUBG UC Nepal, TikTok coins Nepal, online game recharge Nepal, affordable top-up services, game currency Nepal, gaming recharge Kathmandu, cheap gaming coins Nepal">

    <!-- Favicons -->
    <link href="assets/img/logo11.png" rel="icon" sizes="32x32">
    <link href="assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Link to manifest -->
    <link rel="manifest" href="manifest.json">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal">
    <meta property="og:description"
        content="Fast and affordable gaming top-up services for Free Fire, PUBG, TikTok, and more in Banepa and all over Nepal. Buy coins, diamonds, and gems instantly!">
    <meta property="og:image" content="assets/img/logo11.png">
    <meta property="og:url" content="<?php echo e(APP_URL); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal">
    <meta name="twitter:description"
        content="Sujan Top-Up offers the best gaming recharge services in Banepa and across Nepal. Buy gaming coins, Free Fire diamonds, PUBG UC, and more at affordable prices.">
    <meta name="twitter:image" content="assets/img/logo11.png">

    <!-- Additional Locations for Local SEO -->
    <meta name="description"
        content="Available in cities: Kathmandu, Pokhara, Lalitpur, Bhaktapur, Biratnagar, Birgunj, Janakpur, Butwal, Dharan, Nepalgunj, Hetauda, Dhulikhel, Tansen, Ilam, Bharatpur, Gorkha, Lumbini, Jumla, Sindhuli, Bhadrapur, Dhangadhi, Itahari, Kakadvitta, Banepa, Panauti, Chitwan, Tulsipur, Kalaiya, Siddharthanagar, Rajbiraj, Damak, Kirtipur, Patan, Tikapur, Mahendranagar, Baglung, Manang, Phidim, Diktel, Ramechhap, Beni, Dolakha, Solukhumbu, Taplejung, Bardibas, Arghakhanchi, Charikot, Nuwakot, Gulmi, Bhimdatta, Sandhikharka, Waling, Parasi, Lekhnath, Thimi, Kawasoti, Amlekhgunj, Damauli, Kusma, Sankhuwasabha, Salyan, Rolpa, Rukum, Pyuthan, Lamjung, Bhojpur, Okhaldhunga, Siraha, Gaighat, Darchula, Rasuwa, Bajhang, Dadeldhura, Bajura, Achham, Kalikot, Dolpa, Jajarkot, Humla, Bardiya, Baitadi, Sindhupalchok, Tanahun, Palpa, Syangja, Parbat, Makwanpur, Udayapur, Mahottari, Saptari, Kapilvastu, Nawalparasi, Rautahat, Bara, Chautara, Gaur.">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <link rel="manifest" href="manifest.json">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/main.css">

<!-- =======================================================
* Website Name: Sujan Top-Up
* Website URL: https://example.test
* Updated: Dec 03 2024
* Author:Yubi Tech
======================================================== -->
<style>
  /* Style for the floating button */
.floating-button {
    position: fixed;
    bottom: 20px; /* Position the button 20px from the bottom of the screen */
    left: 20px; /* Position the button 20px from the left of the screen */
    background-color: #007bff; /* Button color */
    color: white;
    padding: 15px 30px; /* Padding for the button */
    border-radius: 30px; /* Rounded corners */
    font-size: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none; /* Hide button by default */
    z-index: 9999; /* Ensure it appears over other content */
    cursor: pointer;
    text-align: center;
}

/* Style for the button link */
.button-link {
    color: white;
    text-decoration: none;
}

/* Show the button only on small screens (mobile devices) */
@media only screen and (max-width: 767px) {
    .floating-button {
        display: block; /* Show the button on mobile screens */
    }
}    /* Modal Styles */
 /* Modal Styles */
 /* Modal Styles */ .mmodal.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Transparent background */
    display: none;
    align-items: center;
    justify-content: center;
    overflow: hidden; /* Prevent any page scroll */
    z-index: 1000;
}

.lodal-dialog {
    position: relative;
    background-color: transparent; /* No background for the modal dialog */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1001; /* Make sure the image is above everything else */
}

.lodal-body {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-image {
    max-width: 90%;
    max-height: 80vh;
    object-fit: contain;
}

/* Navigation buttons */
.navigation-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    font-size: 30px;
    color: white;
    background: transparent;
    border: none;
    padding: 20px;
    cursor: pointer;
    z-index: 1002;
}

.prev-btn {
    left: 10px;
}

.next-btn {
    right: 10px;
}

.navigation-btn:hover {
    background: rgba(231, 28, 28, 0.7);
}

/* Close button */
.close-btn {
    position: absolute;
    top: 1px;
    right: 65px;
    font-size: 29px;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    z-index: 1000;
}

.close-btn:hover {
    color: red;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    /* For small screens like tablets and smaller devices */
    .modal-image {
        max-width: 95%; /* Allow slightly more space for the image */
        max-height: 70vh; /* Less height for better mobile viewing */
    }

    .navigation-btn {
        font-size: 29px; /* Smaller font size for buttons on mobile */
        padding: 15px; /* Smaller padding */
    }

    .prev-btn,
    .next-btn {
        font-size: 25px; /* Reduce size of navigation arrows */
        padding: 15px;
    }

    .close-btn {
        font-size: 29px; /* Smaller close button */
        top: 2px;
        right: 35px;
    }
}

@media (max-width: 480px) {
    /* For very small screens like smartphones */
    .modal-image {
        max-width: 95%; /* Allow image to take almost all the width */
        max-height: 60vh; /* Further reduce height on very small screens */
    }

    .navigation-btn {
        font-size: 20px; /* Even smaller font size */
        padding: 10px; /* Small padding for tight screens */
    }

    .prev-btn,
    .next-btn {
        font-size: 20px;
        padding: 10px;
    }

    .close-btn {
        font-size: 18px; /* Further reduce the close button size */
        top: 1px;
        right: 13px;
    }
}   
 
 /* Warning Modal Styles */
.mmmodal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    padding-top: 60px;
    overflow: auto;
}

/* Modal Content */
.mmmodal-content {
    background-color: black;
    color: yellow;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    width: 60%;
    text-align: center;
    position: relative;
    box-shadow: 0 0 10px 5px rgba(255, 255, 255, 0.5);
    animation: fadeIn 1s ease-in-out;
}

/* Close button */
.close {
    color: gold;
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: 5px;
    right: 10px;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color: red;
}

/* Warning Icon */
.mmmodal img {
    width: 100px;
    margin-bottom: 20px;
}

/* Blinking Text Animation */
.blinking-text {
    animation: blink 1.5s infinite step-start;
    color: red;
    font-size: 2em;
    font-weight: bold;
}

/* Blinking Effect */
@keyframes blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
}

/* Warning Note Style */
.warning-note {
    font-size: 1.2em;
    color: red;
    font-weight: bold;
    margin-top: 20px;
}

/* Mobile-Friendly */
@media (max-width: 480px) {
    .mmmodal-content {
        width: 90%;
        margin-top: 15%;
    }

    .close {
        font-size: 25px;
        right: 5px;
    }

    .mmmodal img {
        width: 70px;
    }

    .blinking-text {
        font-size: 1.5em;
    }
}

/* Modal fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

</style>


</head>

<body class="index-page">
 
 

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <div class="logo-img">
          <img src="assets/img/logo11.png" alt="Logo">
        </div>
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">SUJAN TOPUP</h1>
      </a>

      <nav id="navmenu" class="navmenu">
  <ul>
    <li><a href="#hero" class="active">Home</a></li>
    <li><a href="#pricing">Topup</a></li>
    <li><a href="#team">Team</a></li>
    <li><a href="fetch.php">Reviews</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#gallery">Gallery</a></li>
    <li><a href="feedphp.php">Give Review</a></li>
    <?php
            // Check if the user is logged in
            if (isset($_SESSION['user_id'])) {
              echo '    <li><a href="order.php" style="font-size: 15px; padding: 15px 25px; font-weight: bold; color: white;">
              <i class="fa fa-shopping-cart" style="font-size: 15px; margin-right: 10px;"></i> Order</a></li>';
        
          
                // Profile Dropdown for large screens
                echo '<li class="nav-item dropdown no-arrow profile-dropdown" style="list-style-type: none; margin-right: 15px;">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                           style="display: flex; align-items: center; color: #4e73df; text-decoration: none;">
                            <div class="d-flex align-items-center">
                                <img class="img-profile rounded-circle" src="' . $profileImage . '" style="width: 30px; height: 30px; margin-right: 10px;">
                            </div>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown"
                             style="min-width: 200px; padding: 10px 0; border-radius: 6px; box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 20px;">
                            <div class="dropdown-divider" style="margin: 0;"></div>
                            <span class="ml-2 text-gray-600 small" style="font-size: 14px; padding-left:15px; color:rgb(6, 6, 7);">' . $userName . '</span><br>
                            <span class="ml-2 text-gray-600 small" style="font-size: 14px;padding-left:15px; color:rgb(8, 8, 10);">' . $userPhone . '</span><br>
                            <span class="ml-2 text-gray-600 small" style="font-size: 14px;padding-left:15px; color:rgb(5, 5, 5);">' . $userEmail . '</span>
                             <a class="dropdown-item" href="profile.php" style="font-size: 14px; padding: 8px 16px;
                             font-weight:bold; color:rgb(10, 10, 10); text-decoration: none; display: flex; align-items: center;">
                                Manage Account
                            </a>
                            <a class="dropdown-item" href="logout.php" style="font-size: 14px; padding: 8px 16px;
                             font-weight:bold; color:rgb(10, 10, 10); text-decoration: none; display: flex; align-items: center;">
                                Logout
                            </a>
                        </div>
                    </li>';

                // Mobile Profile Icon Link (visible on small screens)
                echo '<li class="profile-icon profile-icon-mobile">
                        <a href="profile.php">
                            <i class="fas fa-user"></i> <!-- Font Awesome user icon -->
                        Account</a>
                    </li>';
            } else {
                // If not logged in, show login button
                echo '<li><a href="sign/login.php">Login</a></li>';
            }
            ?>

        </ul>
  <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>



    </div>
  </header>

  <div class="floating-button">
        <a href="order.php" class="button-link">
        <i class="fa fa-shopping-cart" style="font-size: 10px; margin-right: 10px;"></i>  Your Order
        </a>
    </div>
  <main class="main">

    <!-- Hero Section -->
  <!-- Hero Section -->



   


<!-- Hero Section with Carousel -->
<section id="hero" class="hero section dark-background" >
  <img src="assets/img/hero-bg-2.jpg" alt="" class="hero-bg">

  <div class="container">
    <div class="row gy-4 justify-content-between">
      <!-- Carousel for multiple images -->
      <div id="heroCarousel" class="carousel slide col-lg-4 order-lg-last hero-img" data-bs-ride="carousel" data-aos="zoom-out" data-aos-delay="100">
        <div class="carousel-inner">
        


             <div class="carousel-item" style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='11'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>"onclick="openModal('pubg')"
                 alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>




    <div class="carousel-item" style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='2'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>"  onclick="openModal('clash')" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>



          <div  class="carousel-item active" style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='10'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" onclick="openModal('mobilelegends')"alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>



    <div  class="carousel-item"style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='4'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>"onclick="openModal('freefire')" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
        

    <div  class="carousel-item" style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='6'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%;
                 object-fit: cover;" class="game-image"  onclick="openModal('efootball-pes-2025')">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>


        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      
      <!-- Hero Text and Button -->
      <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-in">
        <h1>Fast and Easy <span>Gaming Topup</span></h1>
        <p>Get your favorite games topped up instantly with Sujan Topup — your trusted gaming partner! 
            <span> For Contact <a href="#contact" style="font-size: 20px;font-weight: bold;text-decoration-line: underline;">Click here</a></span></p>
        <div class="d-flex">
          <a href="#pricing" class="btn-get-started">Top Up Now</a>
        </div>
      </div>
    </div>
  </div>

      <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
        <defs>
          <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
        </defs>
        <g class="wave1">
          <use xlink:href="#wave-path" x="50" y="3"></use>
        </g>
        <g class="wave2">
          <use xlink:href="#wave-path" x="50" y="0"></use>
        </g>
        <g class="wave3">
          <use xlink:href="#wave-path" x="50" y="9"></use>
        </g>
      </svg>

    </section><!-- /Hero Section -->
 






 <!-- Pricing Section with Filter -->
<section id="pricing" class="pricing section" style="background: url('assets/img/b3.jpg') no-repeat center center/cover; padding: 50px 0;">
  <div class="container section-title" data-aos="fade-up" style="color: #fff;">
    <h2 style="color: #333; font-size: 35px; font-weight: 800; margin-bottom: 22px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Top-Up Pricing</h2>
    <div><span>Check Our</span> <span style="color: #9f0505;" class="description-title">Top-Up Pricing</span></div>
  </div>

  <!-- Filter Buttons -->
  <div class="text-center mb-4">
    
    <button class="btn btn-secondary" data-aos-delay="1700" data-aos="zoom-in" onclick="filterItems('game')">Games</button>
    <button class="btn btn-secondary" data-aos-delay="2200" data-aos="zoom-in" onclick="filterItems('movie')">Others</button>
  </div>

  <div class="container">

    <div class="row gy-4 pricing-cards">
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('pubg')">
        <div  style="width: 100%; height: 300px; overflow: hidden; ">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type,game_name, image_path FROM game_images WHERE id='11'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; 
                object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
            <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('freefire')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='4'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('mobilelegends')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='10'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('tiktok')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type,game_name, image_path FROM game_images WHERE id='13'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('efootball')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type,game_name, image_path FROM game_images WHERE id='3'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('clash')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='2'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('weekly-diamond-pass-mlbb')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type,game_name, image_path FROM game_images WHERE id='8'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('efootball-pes-2025')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='6'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('pubg-global')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='12'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('mobile-legends-indonesia')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type,game_name, image_path FROM game_images WHERE id='9'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Game Item -->
      <div class="col-lg-4 col-md-6 filter-item game" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item"  onclick="openModal('free-fire-indonesia')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='5'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      <!-- Movie Item -->
      <div class="col-lg-4 col-md-6 filter-item movie" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('netflix')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='21'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

     
      <!-- Movie Item -->
      <div class="col-lg-4 col-md-6 filter-item movie" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('unpin')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='23'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" 
                alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>',
                 '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>



      <div class="col-lg-4 col-md-6 filter-item movie" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('prime')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='22'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>"
                 alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>',
                 '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>



          <!-- Movie Item -->
          <div class="col-lg-4 col-md-6 filter-item movie" data-aos="zoom-in" data-aos-delay="200">
        <div class="pricing-item" onclick="openModal('spotify')">
        <div  style="width: 100%; height: 300px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, game_name,image_path FROM game_images WHERE id='20'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>"
                 alt="Game Image"style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
                <h3><?php echo htmlspecialchars($row['game_type']); ?></h3>
                <button class="action-btn" onclick="openModal('<?php echo urlencode($row['game_type']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
           
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>
          <div style="text-align: center; margin-top: 10px;">
          <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
            <a href="#pricing" class="cta-btn">Top-up Now</a>
          </div>
        </div>
      </div>

      
      
    </div>
  </div>
</section>

<script>
function filterItems(category) {
  let items = document.querySelectorAll('.filter-item');
  items.forEach(item => {
    if (category === 'all') {
      item.style.display = 'block';
    } else {
      item.style.display = item.classList.contains(category) ? 'block' : 'none';
    }
  });
}
</script>

















<!-- Modal Structure (Hidden by Default) -->
<div id="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; justify-content: center; align-items: center;">
  <div style="background: #fff; padding: 20px; border-radius: 10px; max-width: 1000px; width: 90%; position: relative;">
    <button onclick="closeModal()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
    <h3 id="modal-title"></h3>
    <p id="modal-description"></p>
  </div>
</div>



<!-- Modals for Each Game -->
<div id="modal-container">
  <!-- PUBG Modal -->
  <div id="pubg" class="modal">

  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'PUBG'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('pubg')">&times;</span>
    <h3>PUBG UC Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
              <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
    <img src="assets/img/ud.png" alt="" style="width: 20px; height: 20px; margin-left: 5px; vertical-align: middle;">
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>

    <h4>More Details:</h4>
    <p><strong>Delivery Time:</strong> 30-60 minutes</p>
    <p><strong>Platform:</strong> Android, iOS</p>
    <p><strong>Region:</strong> Nepal</p>
    <p><strong>Publisher:</strong> PUBG Corporation</p> 
    <p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the UC will be added to your account.</p>

    <!-- Button to trigger secondary form modal -->
    <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('pubg')" >Top-up Now</button>
  </div>
</div>





<div id="netflix" class="modal">
<?php 
include("dbcon.php");
$query = "SELECT * FROM games WHERE game_type = 'netflix'";
$result = $conn->query($query);
?>

<div class="modal-content">
  <span class="close-btn" onclick="closeModal('netflix')">&times;</span>
  <h3>Netflix Pricing</h3>
  <ul>
    <?php if ($result->num_rows > 0): ?>
        <?php $serial_number = 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
<strong></strong>
<span><?= htmlspecialchars($row['uc_number']); ?> 
  <img src="assets/img/ud.png" alt="" style="width: 20px; height: 20px; margin-left: 5px; vertical-align: middle;">
</span> = Rs
<span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>
        <?php endwhile; ?>
    <?php else: ?>
        <li>No records found.</li>
    <?php endif; ?>
  </ul>

  <h4>More Details:</h4>
 
<!-- Netflix Details -->
<h5>Netflix</h5>
<p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the subscription will be activated.</p>
<p><strong>Platform:</strong> Android, iOS, Web</p>
<p><strong>Region:</strong> Nepal</p>
<p><strong>Publisher:</strong> Netflix Inc.</p>
  <!-- Button to trigger secondary form modal -->
  <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
  text-align: center; text-decoration: none; 
       display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
     " onclick="redirectToForm('netflix')" >Top-up Now</button>
</div>
</div>

 


<div id="unpin" class="modal">
<?php 
include("dbcon.php");
$query = "SELECT * FROM games WHERE game_type = 'unpin'";
$result = $conn->query($query);
?>

<div class="modal-content">
  <span class="close-btn" onclick="closeModal('unpin')">&times;</span>
  <h3> Unpin Voucher Pricing</h3>
  <ul>
    <?php if ($result->num_rows > 0): ?>
        <?php $serial_number = 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
<strong></strong>
<span><?= htmlspecialchars($row['uc_number']); ?> 
<i  class="fas fa-ticket-alt nav-icon" style="color: rgb(214, 134, 30);
     margin-right: 5px;"></i>
</span> = Rs
<span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>
        <?php endwhile; ?>
    <?php else: ?>
        <li>No records found.</li>
    <?php endif; ?>
  </ul>

  <h4>More Details:</h4>
  <!-- Unpin Voucher Details -->
  <h5>Unpin Voucher</h5>
  <p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the voucher will be sent.</p>
  <p><strong>Platform:</strong> Android, iOS, Web</p>
  <p><strong>Region:</strong> Bangladesh</p>
  <p><strong>Publisher:</strong> Unpin Inc.</p>
  <!-- Button to trigger secondary form modal -->
  <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
  text-align: center; text-decoration: none; 
       display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
     " onclick="redirectToForm('unpin')" >Top-up Now</button>
</div>
</div>





<div id="prime" class="modal">
<?php 
include("dbcon.php");
$query = "SELECT * FROM games WHERE game_type = 'prime'";
$result = $conn->query($query);
?>

<div class="modal-content">
  <span class="close-btn" onclick="closeModal('prime')">&times;</span>
  <h3>Prime Pricing</h3>
  <ul>
    <?php if ($result->num_rows > 0): ?>
        <?php $serial_number = 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
<strong></strong>
<span><?= htmlspecialchars($row['uc_number']); ?> 
  <img src="assets/img/ud.png" alt="" style="width: 20px; height: 20px; margin-left: 5px; vertical-align: middle;">
</span> = Rs
<span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>
        <?php endwhile; ?>
    <?php else: ?>
        <li>No records found.</li>
    <?php endif; ?>
  </ul>

  <h4>More Details:</h4>
  <h2 style="color:white; text-decoration: underline; font-weight: bold;">Sorry, Out of Stock</h2>
<!-- Netflix Details -->
 
<!-- Prime Video Details -->
<h5>Prime Video</h5>
<p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the subscription will be activated.</p>
<p><strong>Platform:</strong> Android, iOS, Web</p>
<p><strong>Region:</strong> Nepal</p>
<p><strong>Publisher:</strong> Amazon Inc.</p>
  <!-- Button to trigger secondary form modal -->
  <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
  text-align: center; text-decoration: none; 
       display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
     " onclick="redirectToForm('prime')" >Top-up Now</button>
</div>
</div>



<div id="spotify" class="modal">
<?php 
include("dbcon.php");
$query = "SELECT * FROM games WHERE game_type = 'spotify'";
$result = $conn->query($query);
?>

<div class="modal-content">
  <span class="close-btn" onclick="closeModal('spotify')">&times;</span>
  <h3>Spotify Pricing</h3>
  <ul>
    <?php if ($result->num_rows > 0): ?>
        <?php $serial_number = 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
<strong></strong>
<span><?= htmlspecialchars($row['uc_number']); ?> 
  <img src="assets/img/ud.png" alt="" style="width: 20px; height: 20px; margin-left: 5px; vertical-align: middle;">
</span> = Rs
<span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>
        <?php endwhile; ?>
    <?php else: ?>
        <li>No records found.</li>
    <?php endif; ?>
  </ul>
  <h2 style="color:white; text-decoration: underline; font-weight: bold;">Sorry, Out of Stock</h2>
  <h4>More Details:</h4>
  <h5>Spotify</h5>
<p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the premium subscription will be activated.</p>
<p><strong>Platform:</strong> Android, iOS, Web</p>
<p><strong>Region:</strong> Nepal</p>
<p><strong>Publisher:</strong> Spotify AB</p>
  <!-- Button to trigger secondary form modal -->
  <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
  text-align: center; text-decoration: none; 
       display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
     " onclick="redirectToForm('spotify')" >Top-up Now</button>
</div>
</div>











  <!-- Weekly Pass Modal -->
  <div id="weekly-diamond-pass-mlbb" class="modal">
    
  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'MLBB'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('weekly-diamond-pass-mlbb')">&times;</span>
    <h3>MLBB Product </h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> Price
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i>
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the Weekly Pass will be activated.</p>
      <p><strong>Platform:</strong> Android, iOS</p>
      <p><strong>Region:</strong> Nepal</p>
      <p><strong>Publisher:</strong> PUBG Corporation</p> 
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('weekly-diamond-pass-mlbb')">Buy Now</button>
    </div>
  </div>

  <!-- PUBG Global Modal -->
  <div id="pubg-global" class="modal">

  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'pubg-global'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('pubg-global')">&times;</span>
    <h3>PUBG Global UC Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
    <img src="assets/img/ud.png" alt="" style="width: 20px; height: 20px; margin-left: 5px; vertical-align: middle;">
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> 30-60 minutes</p>
      <p><strong>Platform:</strong> Androids</p>
      <p><strong>Region:</strong> Global</p>
      <p><strong>Publisher:</strong> PUBG Corporation</p> e</p>
      <p><strong>Delivery Time:</strong> Instant. Upon completing the payment, the UC will be added to your account.</p>
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('pubg-global')">Top-up Now</button>
    </div>
  </div>



  <!-- Mobile Legends (Indonesia) Modal -->
  <div id="mobile-legends-indonesia" class="modal">
   
  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'Mobile Legend Indonesia'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('v')">&times;</span>
    <h3>Mobile Legends (Indonesia) Diamonds </h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i> 
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> Instant</p>
      <p><strong>Platform:</strong> Nepal & Indonesia (Mobile Legends: Indonesia)</p>
      <p><strong>Region:</strong> Nepal & Indonesia</p>
      <p><strong>All Purchases:</strong> NON-REFUNDABLE and NON-RETURNABLE</p>
      <p><strong>Payment Methods:</strong> MyPay, eSewa, IMEpay, Khalti</p>
      <p><strong>How to Top-Up:</strong> Enter your ML User ID and Zone ID, select Diamond denomination, choose payment method, and diamonds will be credited to your account.</p>
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('mobile-legends-indonesia')">Top-up Now</button>
    </div>
  </div>

  <!-- eFootball PES 2025 (iOS Only) Modal -->
  <div id="efootball-pes-2025" class="modal">
    
  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'Efootball Ios'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('efootball-pes-2025')">&times;</span>
    <h3>eFootball PES 2025 Coins Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-coins" style="color: #ff7b29;"></i></span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> 30 Minutes</p>
      <p><strong>Platform:</strong> iOS Only</p>
      <p><strong>Region:</strong> Nepal</p>
      <p><strong>All Purchases:</strong> NON-REFUNDABLE and NON-RETURNABLE</p>
      <p><strong>Payment Methods:</strong> MyPay, eSewa, IMEpay, Khalti</p>
      <p><strong>How to Top-Up:</strong> Select your Coin denomination, choose your payment method, and the coins will be credited to your account shortly.</p>
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('efootball-pes-2025')">Top-up Now</button>
    </div>
  </div>



  <!-- Free Fire (Indonesia) Modal -->
  <div id="free-fire-indonesia" class="modal">
  
  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'free-fire-indonesia'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('free-fire-indonesia')">&times;</span>
    <h3>free Fire (Indonesia) Diamond Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i> </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> Instant</p>
      <p><strong>Region:</strong> Indonesia Account Only</p>
      <p><strong>All Purchases:</strong> NON-REFUNDABLE and NON-RETURNABLE</p>
      <p><strong>Payment Methods:</strong> Khalti, eSewa, IMEpay, MyPay</p>
      <p><strong>How to Top-Up:</strong> Enter your Free Fire Player ID, select diamond amount, complete payment, and diamonds will be credited instantly to your account.</p>
      <p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('free-fire-indonesia')">Top-up Now</button>
    </div>
  </div>







<!-- Mobile Legends Modal -->
<div id="mobilelegends" class="modal">
 
<?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'mobilelegends'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('mobilelegends')">&times;</span>
    <h3>Mobile legends Diamond Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i>  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>

    <h4>More Details:</h4>
    <p><strong>Delivery Time:</strong> Instant.</p><p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
    <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('mobilelegends')">Top-up Now</button>
  </div>
</div>

<!-- TikTok Modal -->
<div id="tiktok" class="modal">
 
<?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'tiktok'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('tiktok')">&times;</span>
    <h3>TikTok Coins Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-coins" style="color: #ff7b29;"></i>
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>

    

    <h4>More Details:</h4>
    <p><strong>100% Safe and Secure Coins</strong></p>
    <p><strong>No Minus Problem</strong></p>
    <p><strong>Delivery Time:</strong> Instant.</p>
    <p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
    <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('tiktok')">Top-up Now</button>
   
  </div>
</div>

<!-- Clash of Clans Modal -->
<div id="clash" class="modal">
 
<?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'clash of clan'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('clash')">&times;</span>
    <h3>Clash of Clans Gems Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i> </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>

    <h4>More Details:</h4>
    <p><strong>Delivery Time:</strong> Instant.</p>
    <p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
    <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('clash')">Top-up Now</button>
  </div>
</div>

<!-- Free Fire Modal -->
<div id="freefire" class="modal">

<?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'freefire'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('freefire')">&times;</span>
    <h3>Freefire Diamond Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-gem" style="color: rgb(255, 0, 157);
     margin-right: 5px;"></i> 
  </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>

    <h4>More Details:</h4>
    <p><strong>Delivery Time:</strong> Instant.</p>
    <p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
    <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('freefire')">Top-up Now</button>
  </div>
</div>

  <!-- eFootball Modal -->
  <div id="efootball" class="modal">
   
  <?php 
  // Fetch data from the database
  include("dbcon.php");
  $query = "SELECT * FROM games WHERE game_type = 'Efootball Android'";
  $result = $conn->query($query);
  ?>

  <div class="modal-content">
    <span class="close-btn" onclick="closeModal('efootball')">&times;</span>
    <h3>efootball (Android) Coins Pricing</h3>

    <ul>
      <?php if ($result->num_rows > 0): ?>
          <?php $serial_number = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
  <strong></strong>
  <span><?= htmlspecialchars($row['uc_number']); ?> 
  <i class="fas fa-coins" style="color: #ff7b29;"></i> </span> = Rs
  <span><?= htmlspecialchars($row['discounted_price']); ?></span>
</li>

          <?php endwhile; ?>
      <?php else: ?>
          <li>No records found.</li>
      <?php endif; ?>
    </ul>
  
      <h4>More Details:</h4>
      <p><strong>Delivery Time:</strong> Instant. Coins will be added to your account after payment.</p>
      <p><strong>Platform:</strong> Android</p>
      <p><strong>Region:</strong> Nepal</p>
      <p><strong>Publisher:</strong> KONAMI</p>
     
      <p><strong>Delivery Time:</strong> Instant after successful payment.</p>
      <!-- Button to trigger secondary form modal -->
      <p><strong>Important Note:</strong> Ensure that the details are correct, as no refunds will be given for incorrect details.</p>
      <button style="background-color:rgb(40, 117, 240); color: white; font-weight:bold;padding: 15px 32px; 
    text-align: center; text-decoration: none; 
         display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px; border: none; 
       " onclick="redirectToForm('efootball')">Top-up Now</button>
    </div>
  </div>



</div>
<!-- Testimonials Section -->
<section id="testimonials" class="testimonials section dark-background">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <h3 class="section-title">Customer Reviews:</h3>

    <div class="testimonial-item">
      <img src="assets/img/avatar-placeholder.svg" class="testimonial-img" alt="Demo customer">
      <div class="stars">
        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i>
      </div>
      <p>
        <i class="bi bi-quote quote-icon-left"></i>
        <span>"Babbal Dami Ak Dam Chito Very Fast,"</span>
        <i class="bi bi-quote quote-icon-right"></i>
      </p>
    </div>

   

    <div class="text-center mt-4" data-aos="zoom-in" data-aos-delay="500">
  <a href="fetch.php" class="btn btn-primary">More Reviews</a>
  <a href="feedphp.php" class="btn btn-secondary">Give Review</a>
</div>

  </div>
</section>



 <!-- About Section -->
<section id="about" class="about section">

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row align-items-xl-center gy-5">

      <div class="col-xl-5 content">
        <h3>About Us</h3>
        <h2>Top-Up Made Simple and Fast</h2>
        <p>At Sujan Topup, we specialize in providing seamless and instant top-up solutions for your favorite games. Our mission is to enhance your gaming experience by offering secure, affordable, and reliable services with just a few clicks.</p>
        <a href="#" class="read-more"><span>Learn More</span><i class="bi bi-arrow-right"></i></a>
      </div>

      <div class="col-xl-7">
        <div class="row gy-4 icon-boxes">

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-box">
              <i class="bi bi-controller"></i>
              <h3>Wide Game Selection</h3>
              <p>From mobile games to online PC games, we provide top-up services for a wide range of platforms.</p>
            </div>
          </div> <!-- End Icon Box -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-box">
              <i class="bi bi-shield-lock"></i>
              <h3>Secure Transactions</h3>
              <p>Your security is our priority. We ensure safe and encrypted payment processes.</p>
            </div>
          </div> <!-- End Icon Box -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="icon-box">
              <i class="bi bi-lightning-charge"></i>
              <h3>Instant Delivery</h3>
              <p>No waiting! Get your top-ups credited instantly, so you can continue gaming uninterrupted.</p>
            </div>
          </div> <!-- End Icon Box -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="icon-box">
              <i class="bi bi-wallet2"></i>
              <h3>Affordable Rates</h3>
              <p>Enjoy competitive pricing and unbeatable value for your gaming needs.</p>
            </div>
          </div> <!-- End Icon Box -->

        </div>
      </div>

    </div>
  </div>

</section><!-- /About Section -->


<!-- Features Section -->
<section id="features" class="features section">
  <div class="container">
    <div class="row gy-4" id="features-list">
      <!-- First 4 Features -->
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="features-item">
          <i class="bi bi-wallet2" style="color: #ffbb2c;"></i>
          <h3><a href="#pricing" class="stretched-link">Instant Top-Ups</a></h3>
        </div>
      </div>
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="features-item">
          <i class="bi bi-shield-check" style="color: #5578ff;"></i>
          <h3><a href="#features" class="stretched-link">Secure Transactions</a></h3>
        </div>
      </div>
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="features-item">
          <i class="bi bi-controller" style="color: #e80368;"></i>
          <h3><a href="#pricing" class="stretched-link">Wide Game Selection</a></h3>
        </div>
      </div>
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="400">
        <div class="features-item">
          <i class="bi bi-clock-history" style="color: #e361ff;"></i>
          <h3><a href="#contact" class="stretched-link">24/7 Service</a></h3>
        </div>
      </div>
    </div>

    <!-- Hidden Features -->
    <div class="row gy-4" id="hidden-features" style="display: none;">
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="500">
        <div class="features-item">
          <i class="bi bi-cash" style="color: #47aeff;"></i>
          <h3><a href="#features" class="stretched-link">Affordable Pricing</a></h3>
        </div>
      </div>
    
      <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="1200">
        <div class="features-item">
          <i class="bi bi-globe" style="color: #29cc61;"></i>
          <h3><a href="#features" class="stretched-link">Global Access</a></h3>
        </div>
      </div>
    </div>

  
  </div>
</section>

<!-- Stats Section -->
<section id="stats" class="stats section light-background">

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row gy-4">

      <!-- Happy Users -->
      <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
        <i class="bi bi-emoji-smile"></i>
        <div class="stats-item">
          <span data-purecounter-start="0" data-purecounter-end="10000" data-purecounter-duration="1" class="purecounter"></span>
          <p>Happy Users</p>
        </div>
      </div><!-- End Stats Item -->

      <!-- Transactions Completed -->
      <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
        <i class="bi bi-currency-exchange"></i>
        <div class="stats-item">
          <span data-purecounter-start="0" data-purecounter-end="30000" data-purecounter-duration="1" class="purecounter"></span>
          <p>Successful Transactions</p>
        </div>
      </div><!-- End Stats Item -->

      <!-- Support Hours -->
      <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
        <i class="bi bi-headset"></i>
        <div class="stats-item">
          <span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="1" class="purecounter"></span>
          <p>Days of Support</p>
        </div>
      </div><!-- End Stats Item -->

      <!-- Partnerships -->
      <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
        <i class="bi bi-people"></i>
        <div class="stats-item">
          <span data-purecounter-start="0" data-purecounter-end="50" data-purecounter-duration="1" class="purecounter"></span>
          <p>Partner Networks</p>
        </div>
      </div><!-- End Stats Item -->

    </div>

  </div>

</section><!-- /Stats Section -->




    
<!-- Team Section -->
<section id="team" class="team section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2 style="color: #333;font-size: 20px">Our Team</h2>
    <div><span>Meet Our</span> <span class="description-title">Team</span></div>
  </div><!-- End Section Title -->

  <div class="container">

    <div class="row gy-5">

      <!-- Team Member 1 (fictional demo persona) -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="member">
        <div  style="width: 100%; height: 450px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='17'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
          
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

          <div class="member-info">
            <h4>Demo Founder</h4>
            <span style="font-size: 15px; font-weight: bold; color: #333; font-family: Arial, sans-serif;">Founder (Demo)</span>
            <p>This is a fictional team member shown in the open-source demo. The original project was built by a small team several years ago; their personal details have been intentionally removed from the public repository.</p>
            <div class="social">
              <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
              <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><i class="bi bi-envelope"></i></a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="member">
        <div  style="width: 100%; height: 450px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='7'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
          
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

          <div class="member-info">
            <h4>Demo Co-Founder</h4>
            <span style="font-size: 15px; font-weight: bold; color: #333; font-family: Arial, sans-serif;">Co-Founder (Demo)</span>
            <p>Fictional team member for the public demo. Real names and contact details are not included in this repository.</p>
            <div class="social">
              <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
              <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><i class="bi bi-envelope"></i></a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="member">
        <div  style="width: 100%; height: 450px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='16'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
          
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

          <div class="member-info">
            <h4>Demo Strategist</h4>
            <span style="font-size: 15px; font-weight: bold; color: #333; font-family: Arial, sans-serif;">Strategy (Demo)</span>
            <p>Fictional team member for the public demo. Real names and contact details are not included in this repository.</p>
            <div class="social">
              <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
              <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><i class="bi bi-envelope"></i></a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="member">
        <div  style="width: 100%; height: 450px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='14'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
          
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

          <div class="member-info">
            <h4>Demo Manager</h4>
            <span style="font-size: 15px; font-weight: bold; color: #333; font-family: Arial, sans-serif;">Manager (Demo)</span>
            <p>Fictional team member for the public demo. Real names and contact details are not included in this repository.</p>
            <div class="social">
              <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
              <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><i class="bi bi-envelope"></i></a>
            </div>
          </div>
        </div>
      </div>


<div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
  <div class="member">
  <div  style="width: 100%; height: 450px; overflow: hidden; border-radius: 10px;">
    <?php
    // Database connection
    include('dbcon.php');
    
    // Fetch a single Pubg game image based on id
    $query = "SELECT game_type, image_path FROM game_images WHERE id='15'";
    $result = $conn->query($query);
    ?>
 
        <?php if ($result->num_rows > 0): ?>
            <?php $row = $result->fetch_assoc(); // Fetch the single result ?>
            
                <!-- Adjust the path here based on the location of uploads directory -->
                <img src="<?php echo e(game_image_src($row["image_path"])); ?>" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;" class="game-image">
          
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

    <div class="member-info">
      <h4>Demo Developer</h4>
      <span>Developer (Demo)</span>
      <p>Fictional team member for the public demo. Real names and contact details are not included in this repository.</p>
      <div class="social">
        <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
        <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><i class="bi bi-envelope"></i></a>
      </div>
    </div>
  </div>
</div>


      <!-- End Team Member -->

    </div>

  </div>

</section><!-- /Team Section -->

<!-- Details Section -->
<section id="details" class="details section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2 style="color: #333;font-size: 20px">Details</h2>
    <div><span>Explore Our</span> <span class="description-title">Services</span></div>
  </div><!-- End Section Title -->

  <div class="container">
    <!-- Feature Item 1 (Visible by Default) -->
    <div class="row gy-4 align-items-center features-item">
      <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="100">
        <img src="assets/img/details-1.png" class="img-fluid" alt="Easy Recharge">
      </div>
      <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
        <h3>Fast and Easy Mobile Top-Up</h3>
        <p class="fst-italic">
          Recharge your mobile anytime, anywhere with our seamless and quick top-up service.
        </p>
        <ul>
          <li><i class="bi bi-check"></i> <span>Instant top-up for all major telecom operators.</span></li>
          <li><i class="bi bi-check"></i> <span>User-friendly platform for hassle-free transactions.</span></li>
          <li><i class="bi bi-check"></i> <span>Supports multiple payment methods for convenience.</span></li>
        </ul>
      </div>
    </div><!-- End Feature Item 1 -->

    <!-- Hidden Features (Initially Hidden) -->
    <div id="hidden-details" style="display: none;">
      <!-- Feature Item 2 -->
      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
          <img src="assets/img/details-2.png" class="img-fluid" alt="Discounts and Offers">
        </div>
        <div class="col-md-7 order-2 order-md-1" data-aos="fade-up" data-aos-delay="200">
          <h3>Exclusive Discounts and Rewards</h3>
          <p class="fst-italic">
            Enjoy exciting discounts and cashback offers on every recharge.
          </p>
          <p>
            Save more with our regular promotional campaigns, reward points, and special deals designed just for you.
          </p>
        </div>
      </div><!-- End Feature Item 2 -->

      <!-- Feature Item 3 -->
      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
          <img src="assets/img/details-3.png" class="img-fluid" alt="Secure Payment">
        </div>
        <div class="col-md-7" data-aos="fade-up">
          <h3>Safe and Secure Transactions</h3>
          <p>
            Your security is our priority. We use advanced encryption technologies to ensure your payments are protected.
          </p>
          <ul>
            <li><i class="bi bi-check"></i> <span>End-to-end encrypted payment system.</span></li>
            <li><i class="bi bi-check"></i> <span>Secure integration with leading payment gateways.</span></li>
            <li><i class="bi bi-check"></i> <span>Transaction alerts to keep you informed.</span></li>
          </ul>
        </div>
      </div><!-- End Feature Item 3 -->

      <!-- Feature Item 4 -->
      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out">
          <img src="assets/img/details-4.png" class="img-fluid" alt="24/7 Customer Support">
        </div>
        <div class="col-md-7 order-2 order-md-1" data-aos="fade-up">
          <h3>24/7 Customer Support</h3>
          <p class="fst-italic">
            Facing an issue? Our dedicated support team is here to assist you anytime.
          </p>
          <p>
            From transaction queries to resolving errors, we ensure prompt assistance so you can enjoy uninterrupted services.
          </p>
        </div>
      </div><!-- End Feature Item 4 -->
    </div><!-- End Hidden Features -->

    <!-- More Button -->
    <div class="text-center mt-4">
      <button id="details-more-btn" class="btn btn-primary">More</button>
    </div>
  </div>
</section><!-- End Details Section -->



<!-- Faq Section -->
<section id="faq" class="faq section light-background">

  <div class="container-fluid">

    <div class="row gy-4">

      <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">

        <div class="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
          <h3><span>Frequently Asked </span><strong>Questions</strong></h3>
          <p>
            Find answers to some of the most common questions about our top-up services. If you have any further inquiries, feel free to contact our support team.
          </p>
        </div>

        <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

          <div class="faq-item faq-active">
            <i class="faq-icon bi bi-question-circle"></i>
            <h3>How do I top up my mobile balance?</h3>
            <div class="faq-content">
              <p>To top up your mobile, simply visit our website, select your mobile operator, enter your phone number, and choose the amount you wish to recharge. Complete the payment and your balance will be updated instantly.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div><!-- End Faq item-->

          <div class="faq-item">
            <i class="faq-icon bi bi-question-circle"></i>
            <h3>Is my mobile number safe with your service?</h3>
            <div class="faq-content">
              <p>Your privacy is important to us. We use secure encryption methods to protect your personal information, and your mobile number is only used for the top-up process.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div><!-- End Faq item-->

          <div class="faq-item">
            <i class="faq-icon bi bi-question-circle"></i>
            <h3>What payment methods do you accept for mobile top-ups?</h3>
            <div class="faq-content">
              <p>We accept various payment methods including credit/debit cards, mobile wallets, and bank transfers. You can choose the method most convenient for you during checkout.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div><!-- End Faq item-->

          <div class="faq-item">
            <i class="faq-icon bi bi-question-circle"></i>
            <h3>What should I do if my mobile top-up fails?</h3>
            <div class="faq-content">
              <p>If your top-up fails, please check the payment status and confirm the details. If the issue persists, contact our support team at <strong><?php echo e(SUPPORT_EMAIL); ?></strong>, and we will assist you promptly.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div><!-- End Faq item-->

        </div>

      </div>

      <div class="col-lg-5 order-1 order-lg-2">
        <img src="assets/img/faq.jpg" class="img-fluid" alt="FAQ Image" data-aos="zoom-in" data-aos-delay="100">
      </div>
    </div>

  </div>

</section><!-- /Faq Section -->



<!-- Contact Section -->
<section id="contact" class="contact section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2 style="color: #333;font-size: 20px">Contact</h2>
    <div><span>Connect with</span> <span class="description-title">Sujan TopUp</span></div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade" data-aos-delay="100">
    <div class="row gy-4">
      <div class="col-lg-4">
        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
          <i class="bi bi-geo-alt flex-shrink-0"></i>
          <div>
            <h3>Address</h3>
            <p>Demo Address, Nepal</p>
          </div>
        </div>

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
          <i class="bi bi-telephone flex-shrink-0"></i>
          <div>
            <h3>Call Us</h3>
            <a href="<?php echo e(phone_link()); ?>"><p><?php echo e(SUPPORT_PHONE); ?></p></a>
          </div>
        </div>

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
          <i class="bi bi-envelope flex-shrink-0"></i>
          <div>
            <h3>Email Us</h3>
            <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><p><?php echo e(SUPPORT_EMAIL); ?></p></a>
          </div>
        </div>

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
          <i class="bi bi-whatsapp flex-shrink-0"></i>
          <div>
            <h3>WhatsApp</h3>
            <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><p>Contact or Message Us</p><p><?php echo e(SUPPORT_PHONE); ?></p></a>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <!-- Embedded Map -->
        <iframe 
        <div class="mt-3 text-center">
          <p style="font-size: 16px; font-weight: 600; color: #333; background-color: #f8f9fa; padding: 10px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            Map disabled in the public demo. Configure your own location in local settings.
          </p>
        </div>
      </div>
    </div>
  </div>
 </section>


 <?php
// Include the database connection
include 'dbcon.php';

// Get the selected filter (default is 'newest')
$order = isset($_GET['order']) ? $_GET['order'] : 'newest';

// Determine the filter type (image, video, or both)
$filterType = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Image query (ordered by created_at)
$imageQuery = "SELECT id, image_name, image_path, description, created_at FROM image_gallery ORDER BY created_at " . ($order == 'oldest' ? 'ASC' : 'DESC');
$imageResult = $conn->query($imageQuery);

// Video query (ordered by created_at)
$videoQuery = "SELECT id, name, file_path, description, created_at, thumbnail_path FROM videos ORDER BY created_at " . ($order == 'oldest' ? 'ASC' : 'DESC');
$videoResult = $conn->query($videoQuery);
?>
<!-- Section for Filter and Gallery -->
<section id="gallery">
<h2 style="color: #333;font-weight:bold;font-size: 30px">Our Works Collection</h2>
    <!-- Filter Section -->
    <div id="filter-section">
        <button onclick="showGallery('image')">Image Gallery</button>
        <button onclick="showGallery('video')">Video Gallery</button>
    </div>

    <!-- Gallery Grid for Images and Videos -->
    <div id="gallery-grid" class="grid-container">
    <div id="image-gallery" class="gallery-section" style="display: <?= $filterType == 'image' || $filterType == 'all' ? 'block' : 'none' ?>;">
    <div class="gallery-grid">
        <?php if ($filterType == 'all' || $filterType == 'image') {
            while ($image = $imageResult->fetch_assoc()) { ?>
                <div class="gallery-item">
                    <img src="<?= e(game_image_src($image["image_path"])) ?>" alt="<?= $image['image_name'] ?>" class="gallery-image" onclick="openImageModal(<?= $image['id'] ?>)">
                    <!-- Description below the image -->
                    <p class="gallery-description"><?= $image['description'] ?></p>
                </div>
            <?php }
        } ?>
    </div>
</div>
 <!-- Full-Screen Modal to display the image -->
<div class="mmodal custom-modal" id="imageModal" style="display: none;">
    <div class="lodal-dialog">
        <div class="lodal-content">
            <div class="lodal-body">
                <!-- Close Button -->
                <span class="close-btn" onclick="closemodal()">×</span> <!-- The Close button (×) -->

                <!-- Image inside the modal -->
                <img id="modalImage" class="modal-image" src="" alt="Full Screen Image" ondblclick="closeModal()">
                
                <!-- Next and Previous buttons as image navigation -->
                <button id="prevBtn" class="navigation-btn prev-btn" onclick="prevImage()">❮</button>
                <button id="nextBtn" class="navigation-btn next-btn" onclick="nextImage()">❯</button>
            </div>
        </div>
    </div>
</div>



        <!-- Video Gallery -->
        <div id="video-gallery" style="display: <?= $filterType == 'video' || $filterType == 'all' ? 'block' : 'none' ?>;">
          
            <div class="gallery" id="videoGallery">
                <?php if ($videoResult && $videoResult->num_rows > 0): ?>
                    <?php while ($row = $videoResult->fetch_assoc()): ?>
                        <div class="gallery-item" data-category="video">
                            <!-- Video Thumbnail -->
                            <div class="video-thumbnail" data-video-id="<?= $row['id']; ?>" 
                                 data-video-file="<?= htmlspecialchars($row['name']); ?>">
                                <img src="<?= e(game_image_src($row["thumbnail_path"])) ?>" 
                                     alt="Thumbnail for <?= htmlspecialchars($row['name']); ?>" 
                                     class="thumbnail-image">
                                <div class="controls">
                                    <button class="play-button" onclick="playVideo(<?= $row['id']; ?>)">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Video Container (hidden initially) -->
                            <div class="video-container" id="video-<?= $row['id']; ?>" style="display:none;">
                                <video id="video-element-<?= $row['id']; ?>" controls>
                                    <source src="<?= e(game_image_src($row["file_path"])) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>

                            <!-- Directly place description below the video -->
                            <p class="gallery-description"><?= htmlspecialchars($row['description']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No videos found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

 

<script>  
// Function to close the modal
function closemodal() {
    const modal = document.getElementById("imageModal");
    if (modal.style.display === 'flex') {
        modal.style.display = 'none';
    }
}

// Ensure the modal is properly opened and closed with console logs for debugging
function openImageModal(imageId) {
    currentImageId = imageId;
    imageIds = []; // Clear previous image IDs

    // Fetch all image IDs dynamically (PHP logic)
    <?php
        // Fetching all image IDs for navigation
        $query = "SELECT id FROM image_gallery ORDER BY created_at " . ($order == 'oldest' ? 'ASC' : 'DESC');
        $result = $conn->query($query);
        $imageIds = [];
        while ($row = $result->fetch_assoc()) {
            $imageIds[] = $row['id'];
        }
        echo "imageIds = " . json_encode($imageIds) . ";";
    ?>

    // Get the selected image source and display it
    const imgSrc = document.querySelector(`img[onclick="openImageModal(${imageId})"]`).src;
    document.getElementById("modalImage").src = imgSrc;

    // Show the modal
    const modal = document.getElementById("imageModal");
    modal.style.display = 'flex';
    console.log("Modal opened:", modal.style.display); // Debugging log
}

// Function to navigate to the next image
function nextImage() {
    let nextIndex = (imageIds.indexOf(currentImageId) + 1) % imageIds.length;
    currentImageId = imageIds[nextIndex];
    const nextImageSrc = document.querySelector(`img[onclick="openImageModal(${currentImageId})"]`).src;
    document.getElementById("modalImage").src = nextImageSrc;
}

// Function to navigate to the previous image
function prevImage() {
    let prevIndex = (imageIds.indexOf(currentImageId) - 1 + imageIds.length) % imageIds.length;
    currentImageId = imageIds[prevIndex];
    const prevImageSrc = document.querySelector(`img[onclick="openImageModal(${currentImageId})"]`).src;
    document.getElementById("modalImage").src = prevImageSrc;
}

// Function to toggle full-screen mode on double-click
document.getElementById("modalImage").ondblclick = function() {
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        document.documentElement.requestFullscreen();
    }
}

    // Show play button on hover
    $(document).ready(function() {
        $('.video-thumbnail').hover(function() {
            $(this).find('.play-button').show(); // Show play button on hover
        }, function() {
            $(this).find('.play-button').hide(); // Hide play button when not hovered
        });
    });

    // Function to play video when thumbnail is clicked
    function playVideo(videoId) {
        var videoContainer = document.getElementById('video-' + videoId);
        var videoThumbnail = document.querySelector('.video-thumbnail[data-video-id="' + videoId + '"]');
        var videoElement = document.getElementById('video-element-' + videoId);
        
        // Hide the thumbnail
        videoThumbnail.style.display = 'none';

        // Show the video container
        videoContainer.style.display = 'block';

        // Play the video
        videoElement.play();

        // Hide the play button once the video starts playing
        videoThumbnail.querySelector('.play-button').style.display = 'none';

        // Add event listener to reset thumbnail when the video ends
        videoElement.onended = function() {
            // Show the thumbnail again when the video ends
            videoThumbnail.style.display = 'block';
            videoContainer.style.display = 'none';
            videoThumbnail.querySelector('.play-button').style.display = 'block';  // Show play button again
        };
    }

    // Function to switch between image, video, or both galleries
    function showGallery(type) {
        let galleryType = type === 'image' ? 'image-gallery' : 'video-gallery';
        let displayStyle = type === 'all' ? 'block' : 'none';
        
        document.getElementById('image-gallery').style.display = type === 'image' || type === 'all' ? 'block' : 'none';
        document.getElementById('video-gallery').style.display = type === 'video' || type === 'all' ? 'block' : 'none';
    }

 
 
</script>


<!-- Footer -->
<footer id="footer" class="footer dark-background">
  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="index.php" class="logo d-flex align-items-center">
          <span class="sitename">Sujan TopUp</span>
        </a>
        <div class="footer-contact pt-3">
          <p>Demo Address</p>
          <p>Nepal</p>
          <p class="mt-3"><strong>Phone:</strong> <span><?php echo e(SUPPORT_PHONE); ?></span></p>
          <p><strong>Email:</strong> <span><?php echo e(SUPPORT_EMAIL); ?></span></p>
        </div>
        <div class="social-links d-flex mt-4">
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#service">Services</a></li>
          <li><a href="#">Top-Up Guide</a></li>
          <li><a href="#">FAQs</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Our Services</h4>
        <ul>
          <li><a href="#pricing">Game Top-Ups</a></li>
          <li><a href="#">Voucher Sales</a></li>
          <li><a href="#">Custom Gaming Accounts</a></li>
          <li><a href="#">Reward Points</a></li>
          <li><a href="#">Customer Support</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-12 footer-newsletter">
        <h4>Stay Updated</h4>
        <p>Subscribe to our newsletter for gaming offers and exclusive updates!</p>
        <form action="#" method="post" class="php-email-form" onsubmit="return false;">
          <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
          <div class="loading">Loading</div>
          <div class="error-message"></div>
          <div class="sent-message">Subscription request received. Thank you!</div>
        </form>
      </div>
    </div>
  </div>
  <div class="container copyright text-center mt-4">
    <p>© <span>Copyright</span> <strong class="px-1 sitename">Sujan Top-Up</strong> <span>(Demo)</span></p>
    <div class="credits">
      Sujan Top-Up &mdash; open-source portfolio project | Contact: <a href="mailto:<?php echo e(SUPPORT_EMAIL); ?>"><?php echo e(SUPPORT_EMAIL); ?></a>
    </div>
  </div>
  
</footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>
<!-- First, load jQuery -->
 

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
 

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    
<script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.1.0/glightbox.min.js"></script>
<!-- Bootstrap JS and necessary dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


  <!-- Main JS File -->
  <script src="assets/js/mmain.js"></script>
<script>
  
      // Automatically remove the message after 3 seconds
      window.onload = function() {
            const message = document.getElementById("success-message");
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none'; // Hide the message
                }, 3000); // Wait for 3 seconds
            }
        };  
</script>
</body>

</html>