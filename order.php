<?php 
session_start();
include("dbcon.php");

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
        // Default values if no user found
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

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch filters from GET request
$statusFilter = $_GET['status'] ?? 'both'; // Default to 'both' if no filter is applied
$orderBy = ($_GET['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

// Modify the query to include both 'pending' and 'confirmed' when no filter is provided or if filter is 'both'
if ($statusFilter === 'both') {
    $statusCondition = "WHERE status IN ('pending', 'confirmed')";
} else {
    $statusCondition = "WHERE status = ?";
}

// Order configuration
$orderTables = [
    "efootball_website_orders" => ["E-Football", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "efootballios_website_orders" => ["E-Football iOS", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "clash_website_orders" => ["Clash of Clans", ["gem_amount", "supercell_email", "payment_details", "payment_option"]],
    "pubglobal_website_orders" => ["PUBG Global", ["uc_amount", "player_id", "payment_details", "payment_option"]],
    "pubg_website_orders" => ["PUBG", ["uc_amount", "player_id", "payment_details", "payment_option"]],
    "netflix_website_orders" => ["netflix", [  "player_id", "payment_details", "payment_option"]],
    "unpin_website_orders" => ["unpin", [  "player_id", "payment_details", "payment_option"]],
    "indonesiamobilelegends_website_orders" => ["Mobile Legends Indonesia", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "freefire_website_orders" => ["Free Fire", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "indonesiafreefire_website_orders" => ["Free Fire Indonesia", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "mlbb_website_orders" => ["Mobile Legends: Bang Bang", ["diamond_amount", "payment_details", "payment_option", "mlb_userid"]],
    "mobilelegends_website_orders" => ["Mobile Legends", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "tiktok_website_orders" => ["TikTok", ["coin_amount", "email_or_whatsapp", "payment_details", "payment_option", "tiktokuser_id"]]
];

$orders = [];

// Retrieve orders from all tables
foreach ($orderTables as $table => $details) {
    $gameName = $details[0];
    $fields = $details[1];

    // Updated query to consider both 'pending' and 'confirmed'
    $query = "SELECT * FROM $table $statusCondition ORDER BY created_at $orderBy";
    
    $stmt = $conn->prepare($query);

    // Bind parameters for filtering by status
    if ($statusFilter === 'both') {
        // No need for binding in case of 'both', as we are not filtering by a single status
        $stmt->execute();
    } else {
        $stmt->bind_param("s", $statusFilter);
        $stmt->execute();
    }
    
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orderDetails = [
            'game' => $gameName,
            'fields' => array_intersect_key($row, array_flip($fields)),
            'status' => $row['status'],
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'game_type' => $table
        ];
        $orders[] = $orderDetails;
    }

    $stmt->close();
}

$conn->close();
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Include Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

 
  <link rel="stylesheet" href="ostyle.css">
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
 

<br><br>



<div class="container">
    <h1>Your Orders</h1>
    <div id="paymentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Payment Details</h3>
        <p>Please complete your payment to proceed.</p>
        <div>
            <img src="assets/img/pay.jpg" alt="Payment Image" id="paymentImage" style="max-width: 100%; max-height: 200px; object-fit: contain;">
        </div>
        <p>Submit a photo of your payment transaction:</p>
        <input type="file" id="transactionPhoto" accept="image/*">
        <button onclick="submitTransactionPhoto()">Submit Payment Photo</button>
       </div>
</div>
 


   
    <!-- Filter Form (same as before) -->
    <form method="GET" action="">
        <label for="status">Filter by Status:</label>
        <select name="status" id="status">
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
            <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            <option value="" <?= $statusFilter === '' ? 'selected' : ''; ?>>All</option>
        </select>
        <label for="order">Sort by Created At:</label>
        <select name="order" id="order">
            <option value="DESC" <?= $orderBy === 'DESC' ? 'selected' : ''; ?>>Newest</option>
            <option value="ASC" <?= $orderBy === 'ASC' ? 'selected' : ''; ?>>Oldest</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="order-cards">
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($order['game']); ?></h2>
                    <ul>
                        <?php foreach ($order['fields'] as $key => $value): ?>
                            <li><strong><?= htmlspecialchars(ucwords(str_replace("_", " ", $key))); ?>:</strong> <?= htmlspecialchars($value); ?></li>
                        <?php endforeach; ?>
                        <li><strong>Status:</strong> <?= htmlspecialchars(ucwords($order['status'])); ?></li>
                    </ul>

                    <div style="display: flex; gap: 15px;">
                        <?php if ($order['status'] !== 'completed'): ?>
                            <!-- Trigger the modal with additional gameType and userId -->
                            <button onclick="showModal(<?= $order['id']; ?>, '<?= htmlspecialchars($order['user_id']); ?>', '<?= htmlspecialchars($order['game']); ?>', '<?= htmlspecialchars($order['game_type']); ?>')">
                                Pay Now
                            </button>
                        <?php endif; ?>

                        <button>
                            <a href="index.php#pricing" style="color: white; text-decoration: none; display: block;">Buy More</a>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</div>

 
  <!-- Footer -->
  <footer class="footer">
    <p>© 2025 Your Company. All rights reserved.</p>
  </footer>
  
  <script>
    // Show the modal with payment details
    function showModal(orderId, userId, gameName, gameType) {
        document.getElementById('paymentImage').src = 'assets/img/pay.jpg'; // Display the payment image
        document.getElementById('paymentModal').setAttribute('data-order-id', orderId);
        document.getElementById('paymentModal').setAttribute('data-user-id', userId);

        const modalContent = document.querySelector('#paymentModal .modal-content');
        modalContent.innerHTML = `
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Payment Details</h3>
            <p>Please complete your payment to proceed.</p>
            <div>
                <img src="assets/img/pay.jpg" alt="Payment Image" id="paymentImage" style="max-width: 100%; max-height: 200px; object-fit: contain;">
            </div>
            <p>Submit a photo of your payment transaction:</p>
            <input type="file" id="transactionPhoto" accept="image/*"><br>
            <button id="submitBtn" onclick="submitTransactionPhoto()">Submit Payment Photo</button>
        <p class="game-name" style="display: none;">Game: ${gameName}</p>
        <p class="game-type" style="display: none;">Game Type: ${gameType}</p>
        `;

        document.getElementById('paymentModal').style.display = 'block';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    // Function to handle the photo submission
    function submitTransactionPhoto() {
        // Disable the submit button to prevent multiple submissions
        const submitButton = document.getElementById('submitBtn');
        submitButton.disabled = true;
        submitButton.innerText = "Submitting..."; // Change button text to indicate submission

        const id = document.getElementById('paymentModal').getAttribute('data-order-id');
        const userId = document.getElementById('paymentModal').getAttribute('data-user-id');
        const transactionPhoto = document.getElementById('transactionPhoto').files[0];

        const gameName = document.querySelector('#paymentModal .game-name').innerText.replace('Game: ', '');
        const gameType = document.querySelector('#paymentModal .game-type').innerText.replace('Game Type: ', '');

        if (!transactionPhoto) {
            alert("Please upload a transaction photo.");
            submitButton.disabled = false;
            submitButton.innerText = "Submit Payment Photo"; // Re-enable and reset button text
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('user_id', userId);
        formData.append('game_name', gameName);
        formData.append('game_type', gameType);
        formData.append('transaction_photo', transactionPhoto);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'payment_proof.php', true);
        xhr.onload = function () {
            console.log(xhr.responseText); // Log response for debugging
            if (xhr.status === 200 && xhr.responseText === "Payment photo stored successfully.") {
                alert("Your payment photo has been submitted successfully.");
                closeModal();
            } else {
                alert("There was an error submitting your payment photo: " + xhr.responseText);
            }
            // Re-enable the submit button after response
            submitButton.disabled = false;
            submitButton.innerText = "Submit Payment Photo"; // Reset button text
        };
        xhr.send(formData);
    }

    var modal = document.getElementById("contactModal");
    var btn = document.getElementById("contactBtn");
    var span = document.getElementsByClassName("close")[0];

    // Open the modal when the button is clicked
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Close the modal when the close button is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal when clicking outside of the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
 



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




// Get the dropdown button and content
const dropdownBtn = document.querySelector('.dropdown-btn');
const dropdownContent = document.querySelector('.dropdown-content');

// Toggle the dropdown visibility when the button is clicked
dropdownBtn.addEventListener('click', function() {
    // Check if the dropdown content is currently visible
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none"; // Hide dropdown
    } else {
        dropdownContent.style.display = "block"; // Show dropdown
    }
});

// Close the dropdown if the user clicks outside of it
window.addEventListener('click', function(event) {
    if (!event.target.matches('.dropdown-btn')) {
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        }
    }
});

  </script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
