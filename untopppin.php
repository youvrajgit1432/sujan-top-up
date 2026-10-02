<?php
session_start();
require_once __DIR__ . '/config/app.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) ? true : false;

// Pass the value to JavaScript
echo "<script>var isLoggedIn = " . json_encode($isLoggedIn) . ";</script>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Favicons -->
   <link href="assets/img/logo11.png" rel="icon" sizes="32x32">
   <link href="assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">
 
   <!-- Link to manifest -->
   <link rel="manifest" href="manifest.json">
 
   <!-- Open Graph Meta Tags -->
   <meta property="og:title" content="Sujan Top-Up | Best Topup Service Provider In Banepa And All Over Nepal,Buy Gaming Coins, Gems, Diamonds & UC for All Games">
   <meta property="og:description" content="Buy coins, gems, diamonds, and UC for popular games like Free Fire, PUBG, and TikTok. Fast and affordable top-up services.">
   <meta property="og:image" content="assets/img/logo11.png">
   <meta property="og:url" content="<?php echo e(APP_URL); ?>">
   <meta property="og:type" content="website">
 
   <!-- Twitter Card Meta Tags -->
   <meta name="twitter:card" content="summary_large_image">
   <meta name="twitter:title" content="Sujan Top-Up | Buy Gaming Coins, Gems, Diamonds & UC for All Games">
   <meta name="twitter:description" content="Affordable top-up services for Free Fire, PUBG, and more. Get gaming coins, gems, and diamonds instantly!">
   <meta name="twitter:image" content="assets/img/logo11.png">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="assets/css/sec.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    /* Reset and base styles to prevent overlap */
 body {
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  position: relative;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 10px; /* Even more reduced */
  z-index: 1000;
  overflow-y: auto;
}

.modal-content {
  background: white;
  padding: 12px; /* Even more reduced */
  border-radius: 8px; /* Slightly smaller radius */
  width: 95%; /* Slightly wider to use more space */
  max-width: 500px;
  position: relative;
  margin: 5px auto; /* Minimal margin */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.close-btn {
  position: absolute;
  top: 5px;
  right: 10px;
  font-size: 22px; /* Slightly smaller close button */
  cursor: pointer;
  z-index: 1001;
}

#form-content {
  position: relative;
  z-index: 1;
}
  </style>
  <script>
    function submitForm() {
    try {
        // Get selected game type from localStorage
        const selectedGame = localStorage.getItem("selectedGame");

        if (!selectedGame) {
            alert("No game selected. Please choose a game and try again.");
            return;
        }

        // Form data
        
        const playerId = document.getElementById("playerId")?.value.trim();
        const paymentDetails = document.getElementById("paymentDetails")?.value.trim();
        const paymentOption = document.getElementById("paymentOption")?.value;
        const communicationOpt = document.getElementById("communicationopt")?.value;

        // Validate the form
        if (  !playerId || !paymentDetails || !communicationOpt) {
            alert("Please fill in all required fields.");
            return;
        }

        const formData = {
            game: selectedGame,
             
            playerId: playerId,
            paymentDetails: paymentDetails,
            paymentOption: paymentOption,
            communicationOpt: communicationOpt,
            userId: userId // Add userId to form data
        };

        const websiteForm = document.getElementById("topUpForm");

        // Add hidden inputs for form data
        for (const [key, value] of Object.entries(formData)) {
            let inputElement = document.getElementById(key);
            if (!inputElement) {
                inputElement = document.createElement("input");
                inputElement.type = "hidden";
                inputElement.id = key;
                inputElement.name = key;
                websiteForm.appendChild(inputElement);
            }
            inputElement.value = value;
        }

        if (communicationOpt === "whatsapp") {
            // WhatsApp submission
            const whatsappMessage = encodeURIComponent(
                `Type: ${formData.game}\nPlayer ID: ${formData.playerId}\nPayment Details: ${formData.paymentDetails}\nPayment Option: ${formData.paymentOption}`
            );
            const whatsappNumber = "<?php echo WHATSAPP_NUMBER; ?>";
            const whatsappLink = `https://wa.me/${whatsappNumber}?text=${whatsappMessage}`;
            window.open(whatsappLink, "_blank");

            // Submit form to save data
            websiteForm.action = "games/unpin/what.php";
            websiteForm.method = "POST";
            websiteForm.submit();

            // Open WhatsApp after submission
            setTimeout(() => {
                window.open(whatsappLink, "_blank");
            }, 1000);
        } else if (communicationOpt === "website") {
            // Check if the user is logged in
            if (!userId) {
                // If not logged in, redirect to login page
                window.location.href = "sign/login.php"; // Adjust the login page URL as needed
                return;
            }

            // Website submission
            websiteForm.action = "games/unpin/web.php";
            websiteForm.method = "POST";
            websiteForm.submit();
        }

    } catch (error) {
        console.error("An error occurred during form submission:", error);
        alert("An unexpected error occurred. Please try again.");
    }
}

  </script>
</head>
<body>

  <!-- Secondary Modal for Form -->
  <div id="form-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div id="form-content">
        <h3 class="k">Unpin Voucher
          <img src="assets/img/p.jpg" alt="PUBG UC Top-Up" class="k-logo">
        </h3> 
        <div id="offer-message" class="offer-message" style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
          <p>
            <strong>Notice:</strong> Level up your experience! For fast and hassle-free transactions, connect with us directly on WhatsApp. 
            <a href="<?php echo e(whatsapp_link()); ?>" target="_blank"><?php echo e(SUPPORT_PHONE); ?></a>.
            <button onclick="window.open('<?php echo e(whatsapp_link()); ?>', '_blank')" style="margin-left: 10px; padding: 5px 10px; background-color: #25D366; color: white; border: none; border-radius: 5px; cursor: pointer;">
              Click Here
            </button>
          </p>
        </div>

        <form id="topUpForm" method="POST" > 
  <label for="ucAmount"> Diamond Amount:</label>
  <div class="card-container">
    <?php 
    include("dbcon.php");
    $query = "SELECT * FROM games WHERE game_type = 'unpin'";
    $result = $conn->query($query);

    $counter = 0; // Counter to track the number of cards
    if ($result->num_rows > 0): 
        while ($row = $result->fetch_assoc()): 
            $counter++;
    ?>
      <div class="card <?= $counter > 6 ? 'hidden' : '' ?>">
        <label>
          <input type="radio" name="ucAmount" value="<?= htmlspecialchars($row['uc_number']) ?> UC = Rs <?= htmlspecialchars($row['discounted_price']) ?>"> 
          <?= htmlspecialchars($row['uc_number']) ?> 
          <span> <i  class="fas fa-ticket-alt nav-icon" style="color: rgb(214, 134, 30);
     margin-right: 5px;"></i></span> - 
          <?php if (!empty($row['original_price'])): ?>
            <span class="original-price" style="text-decoration: line-through; color: red;">
              Rs <?= htmlspecialchars($row['original_price']) ?>
            </span>
          <?php endif; ?>
          <span class="discounted-price">
            Rs <?= htmlspecialchars($row['discounted_price']) ?>
          </span>
        </label>
      </div>
    <?php 
        endwhile; 
    else: 
    ?>
      <p>No UC options available at the moment.</p>
    <?php endif; ?>
  </div>
   

          <button type="button" id="toggleButton" onclick="toggleCards()">Show More</button>

          <!-- Form fields for player details and payment -->
          <label for="playerId">Your Name:</label>
          <input type="text" name="playerId" id="playerId" placeholder="Enter Your Full Name" required>

          <label for="paymentDetails">Contact Details:</label>
          <input type="text" name="paymentDetails" id="paymentDetails" placeholder="Contact details, we can send voucher pin and others details like phone number instagram and others." required>

          <label for="paymentOption">Payment Option:</label>
          <select name="paymentOption" id="paymentOption">
          <option>Choose</option>
            <option value="esewa">Esewa</option>
            <option value="bankTransfer">Bank Transfer</option>
          </select>

          <label for="communicationopt">Contact Option:</label>
          <select name="communicationopt" id="communicationopt">
          <option>Choose</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="website">Place Order on Website</option>
          </select>

          <!-- Buy button -->
        <!-- Remove the onclick="submitForm()" from the button -->
        <button id="buyButton" type="button" onclick="submitForm()">Buy</button>


          <!-- Delivery info and platform/region details -->
          <p>Delivery Time: 30-60 minutes</p>
          <p><strong>Platform:</strong> Android, iOS</p>
          <p><strong>Region:</strong> Nepal</p>
        </form>
      </div>
    </div>
  </div>
  <script>
    // Pass PHP session user_id to JavaScript
    const userId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
</script>

   <script src="assets/js/script.js"></script>
</body>
</html>
