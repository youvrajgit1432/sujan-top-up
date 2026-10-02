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
    .disclaimer-notice {
      background: linear-gradient(135deg, #ff6b6b, #ee5a24);
      color: white;
      padding: 15px;
      border-radius: 8px;
      margin: 15px 0;
      text-align: center;
      border-left: 5px solid #c23616;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .disclaimer-notice strong {
      font-size: 1.1em;
      display: block;
      margin-bottom: 8px;
    }
    .disclaimer-notice p {
      margin: 5px 0;
      font-size: 0.9em;
      line-height: 1.4;
    }
    .disclaimer-icon {
      font-size: 1.2em;
      margin-right: 8px;
    }
    
    /* Fixed header styles */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      padding: 15px 10px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 10px;
      margin-bottom: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
      min-height: 80px;
    }
    
    .page-header h3 {
      margin: 0;
      font-size: 1.5em;
      font-weight: bold;
      text-align: center;
      flex: 1;
    }
    
    .k-logo {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    /* Modal content adjustments */
    .modal-content {
      padding: 20px;
      overflow-y: auto;
      max-height: 90vh;
    }
    
    /* Ensure proper spacing */
    #form-content {
      padding-top: 0;
    }
    
    @media (max-width: 768px) {
      .page-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
        padding: 15px 5px;
      }
      
      .page-header h3 {
        font-size: 1.3em;
      }
      
      .k-logo {
        width: 50px;
        height: 50px;
      }
    }
    
    @media (max-width: 480px) {
      .page-header h3 {
        font-size: 1.1em;
      }
      
      .k-logo {
        width: 45px;
        height: 45px;
      }
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

        // Form data for Free Fire
        const diamondAmount = document.querySelector('input[name="ucAmount"]:checked')?.value;
        const userId = document.getElementById("userId")?.value.trim();
        const paymentDetails = document.getElementById("paymentDetails")?.value.trim();
        const paymentOption = document.getElementById("paymentOption")?.value;

        // Validate the form
        if (!diamondAmount || !userId || !paymentDetails) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create form data structure for Free Fire
        const formData = {
            game: "Free Fire",
            diamondAmount: diamondAmount,
            userId: userId,
            paymentDetails: paymentDetails,
            paymentOption: paymentOption,
            userid: userid 
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

        // Handle form submission based on communication option
        const communicationOpt = document.getElementById("communicationopt")?.value;

        if (communicationOpt === "whatsapp") {
            // WhatsApp submission
            const whatsappMessage = encodeURIComponent(
                `Game: ${formData.game}\nDiamond Amount: ${formData.diamondAmount}\nUser ID: ${formData.userId}\nPayment Details: ${formData.paymentDetails}\nPayment Option: ${formData.paymentOption}`
            );
            const whatsappNumber = "<?php echo e(whatsapp_client_number()); ?>";
            const whatsappLink = whatsappNumber ? `https://wa.me/${whatsappNumber}?text=${whatsappMessage}` : "";
            if (whatsappLink) window.open(whatsappLink, "_blank");

            // Submit form to save data
            websiteForm.action = "games/free-indo/whats.php";
            websiteForm.method = "POST";
            websiteForm.submit();

            // Open WhatsApp after submission
            setTimeout(() => {
                if (whatsappLink) window.open(whatsappLink, "_blank");
            }, 1000);
        } else if (communicationOpt === "website") {
            // Check if the user is logged in
            const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

            if (!isLoggedIn) {
                // If not logged in, redirect to login page
                window.location.href = "sign/login.php"; // Adjust the login page URL as needed
                return;
            }

            // Website submission
            websiteForm.action = "games/free-indo/web.php";
            websiteForm.method = "POST";
            websiteForm.submit();
        }

    } catch (error) {
        console.error("An error occurred during form submission:", error);
        alert("An unexpected error occurred. Please try again.");
    }
}

function toggleCards() {
    const hiddenCards = document.querySelectorAll('.card.hidden');
    const button = document.getElementById('toggleButton');

    if (hiddenCards.length > 0) {
      // Show more cards
      hiddenCards.forEach(card => card.classList.remove('hidden'));
      button.textContent = 'Show Less';
    } else {
      // Hide extra cards
      const allCards = document.querySelectorAll('.card');
      allCards.forEach((card, index) => {
        if (index >= 6) card.classList.add('hidden');
      });
      button.textContent = 'Show More';
    }
  }

// Function to ensure modal is properly displayed
function ensureModalVisibility() {
    const modal = document.getElementById('form-modal');
    if (modal) {
        modal.style.display = 'block';
        modal.style.opacity = '1';
        modal.style.visibility = 'visible';
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    ensureModalVisibility();
});
</script>

</head>
<body>

  <!-- Secondary Modal for Form -->
  <div id="form-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div id="form-content">

        <!-- Fixed Header Section -->
        <div class="page-header">
          <h3>Free Fire (Indonesia) Diamonds Top-Up</h3>
          <img src="assets/img/freediamond.jpg" alt="Free Fire Diamonds Top-Up" class="k-logo">
        </div>
         
         <!-- Disclaimer Notice -->
         <div class="disclaimer-notice">
           <strong><i class="fas fa-exclamation-triangle disclaimer-icon"></i>IMPORTANT NOTICE</strong>
           <p>This is a Top-Up service page and NOT an official Free Fire page.</p>
           <p>We take orders from here and purchase diamonds from authorized Free Fire dealers.</p>
           <p>Your account security is our priority - we only facilitate top-ups through legitimate channels.</p>
         </div>

         <!-- Offer message -->
         <div class="offer-message">
           <p><strong>Special Offer:</strong> Spend more than Rs 1000 and get an extra 50 <i class="fas fa-gem" style="color: rgb(255, 0, 157); margin-right: 5px;"></i> for free!</p>
         </div>
  
         <form id="topUpForm" method="POST"> 
           <label for="ucAmount">Diamond Amount:</label>
           <div class="card-container">
             <?php 
             include("dbcon.php");
             $query = "SELECT * FROM games WHERE game_type = 'free-fire-indonesia'";
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
                   <span> <i class="fas fa-gem" style="color: rgb(255, 0, 157); margin-right: 5px;"></i></span> - 
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
             
           <label for="userId">User ID:</label>
           <input type="text" name="userId" id="userId" placeholder="Enter Free Fire User ID" required>

           <label for="paymentDetails">Payment Details:</label>
           <input type="text" name="paymentDetails" id="paymentDetails" placeholder="Enter Remarks" required>
           
           <label for="paymentOption">Payment Option:</label>
           <select name="paymentOption" id="paymentOption">
             <option value="esewa">Esewa</option>
             <option value="bankTransfer">Bank Transfer</option>
           </select>
 
           <label for="communicationopt">Contact Option:</label>
           <select name="communicationopt" id="communicationopt">
             <option value="whatsapp">WhatsApp</option>
             <option value="website">Place Order on Website</option>
           </select>

           <!-- Buy button (Initially hidden and controlled by JavaScript) -->
           <button id="buyButton" type="button" onclick="submitForm()">Buy</button>

           <p>Delivery Time: Instant</p>
           <p><strong>Platform:</strong> Android, iOS</p>
           <p><strong>Region:</strong> Indonesia</p>
         </form>

      </div>
    </div>
  </div>

  <script src="assets/js/script.js"></script>
  <script>
    // Pass PHP session user_id to JavaScript
    const userid = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
  </script>
</body>
</html>