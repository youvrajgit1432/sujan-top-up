<?php
// Start session at the very top
include('seson.php');

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
  <style>
    /* General styles */

.section {
    background: #f4f4f4;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.filter-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 7px 15px;
    margin: 3px;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-button:hover {
    background-color: #0056b3;
}

/* Styles for small screens */
@media screen and (max-width: 768px) {
    .section {
        padding: 10px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .filter-button {
        padding: 6px 12px;
        margin: 2px;
        font-size: 14px;
    }
}

@media screen and (max-width: 480px) {
    .section {
        padding: 8px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filter-button {
        padding: 5px 10px;
        margin: 2px;
        font-size: 12px;
    }
}

  </style>
</head>
<body>

  <main class="content">
    <section id="scrollable-content" class="scrollable-content">
        <div class="section">
            <div class="center-container">
                <h3 style="text-align: center; margin-top: 20px;">Recent Users Reviews</h3>
                <div style="text-align: center; margin: 20px 0;">
                    <!-- Filter Buttons -->
                    <a href="?filter=latest" class="filter-button">Latest</a>
                    <a href="?filter=best" class="filter-button">Best</a>
                    <a href="?filter=worst" class="filter-button">Worst</a>
                    <a href="?filter=oldest" class="filter-button">Oldest</a>
                </div>
                <hr>
                <?php while ($row = $result->fetch_assoc()) { 
                    $avatar = !empty($row['avatar']) ? 'uploads/' . $row['avatar'] : 'assets/img/default-avatar.png';
                    $rating = $row['rating'];
                ?>
                <div class="review">
                    <div class="review-header">
                        <img src="<?php echo $avatar; ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px;">
                        <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                    </div>
                    <div class="review-rating">
    <?php
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            // This star should be gold
            echo '<span style="color: gold;font-size: 20px;">⭐</span>';
        } else {
            // This star should be white (empty)
            echo '<span style="color: black;font-size: 20px;">☆</span>';
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
