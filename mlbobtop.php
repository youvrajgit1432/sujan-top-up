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
  <script>
 

function submitForm() {
    try {
        // Get selected game type from localStorage
        const selectedGame = localStorage.getItem("selectedGame");

        if (!selectedGame) {
            alert("No game selected. Please choose a game and try again.");
            return;
        }

        const websiteForm = document.getElementById("topUpForm");

        if (selectedGame === "weekly-diamond-pass-mlbb") {
            const diamondAmount = document.querySelector('input[name="ucAmount"]:checked')?.value;
            const userId = document.getElementById("userId")?.value.trim();
            const paymentDetails = document.getElementById("paymentDetails")?.value.trim();
            const paymentOption = document.getElementById("paymentOption")?.value;

            // Validate form fields
            if (!diamondAmount || !userId || !paymentDetails) {
                alert("Please fill in all required fields.");
                return;
            }

            const formData = {
                game: "Weekly Diamond Pass MLBB",
                diamondAmount,
                userId,
                paymentDetails,
                paymentOption,
                userid: userid 
            };

            // Append hidden inputs to the form
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

            const communicationOpt = document.getElementById("communicationopt")?.value;

            if (communicationOpt === "whatsapp") {
                // Prepare WhatsApp message
                const whatsappMessage = encodeURIComponent(
                    `Game: ${formData.game}\nDiamond Amount: ${formData.diamondAmount} Diamonds\nUser ID: ${formData.userId}\nPayment Details: ${formData.paymentDetails}\nPayment Option: ${formData.paymentOption}`
                );
                const whatsappNumber = "<?php echo e(whatsapp_client_number()); ?>";
                const whatsappLink = whatsappNumber ? `https://wa.me/${whatsappNumber}?text=${whatsappMessage}` : "";
                if (whatsappLink) window.open(whatsappLink, "_blank");

                // Submit form to save data
                websiteForm.action = "games/mlbb/whats.php";
                websiteForm.method = "POST";
                websiteForm.submit();

                // Open WhatsApp again after submission
                setTimeout(() => {
                    if (whatsappLink) window.open(whatsappLink, "_blank");
                }, 1000);
            } else if (communicationOpt === "website") {
                // Check if user is logged in
                const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

                if (!isLoggedIn) {
                    window.location.href = "sign/login.php"; // Redirect to login
                    return;
                }

                // Submit the form to the website
                websiteForm.action = "games/mlbb/web.php";
                websiteForm.method = "POST";
                websiteForm.submit();
            }
        }
    } catch (error) {
        console.error("An error occurred during form submission:", error);
        alert("An unexpected error occurred. Please try again.");
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("topUpForm");

    if (!form) return;

    // Sanitize input to prevent XSS and injection attacks
    function sanitizeInput(input) {
        const temp = document.createElement("div");
        temp.innerText = input;
        return temp.innerHTML;
    }

    // Validate form fields
    function validateForm() {
        const userId = document.getElementById("userId")?.value.trim();
        const paymentDetails = document.getElementById("paymentDetails")?.value.trim();
        const paymentOption = document.getElementById("paymentOption")?.value;
        const diamondAmount = document.querySelector('input[name="diamondAmount"]:checked')?.value;

        if (!userId || !paymentDetails || !paymentOption || !diamondAmount) {
            alert("Please fill in all required fields.");
            return false;
        }

        // Enforce safe input values
        if (/[<>\/'"]/.test(userId) || /[<>\/'"]/.test(paymentDetails)) {
            alert("Invalid characters detected in input fields.");
            return false;
        }

        return true;
    }

    // Attach submit event listener
    form.addEventListener("submit", function (event) {
        if (!validateForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
            return;
        }

        // Additional security headers or logs
        console.log("Form passed validation. Proceeding with secure submission...");
    });

    // Prevent form from being autofilled by bots
    form.addEventListener("input", function (event) {
        const target = event.target;
        if (target.tagName === "INPUT" || target.tagName === "TEXTAREA") {
            target.value = sanitizeInput(target.value);
        }
    });

    // CSRF token injection
    const csrfToken = "<?= bin2hex(random_bytes(32)); ?>"; // Replace with server-generated token
    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "csrf_token";
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Disable right-click to mitigate basic bot attempts
    document.addEventListener("contextmenu", (e) => e.preventDefault());

    // Limit repeated submissions
    let isSubmitting = false;
    form.addEventListener("submit", function (event) {
        if (isSubmitting) {
            alert("Form is already being submitted. Please wait.");
            event.preventDefault();
        }
        isSubmitting = true;
    });
});


    </script>
</head>
<body>

  <!-- Secondary Modal for Form -->
  <div id="form-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div id="form-content">

      <h3 class="k">Weekly Diamond Pass MLBB
      <img src="assets/img/m1.jpg" alt="Weekly Diamond Pass MLBB"  class="k-logo">
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
  <label for="ucAmount">Product Amount:</label>
  <div class="card-container">
    <?php 
    include("dbcon.php");
    $query = "SELECT * FROM games WHERE game_type = 'mlbb'";
    $result = $conn->query($query);

    $counter = 0; // Counter to track the number of cards
    if ($result->num_rows > 0): 
        while ($row = $result->fetch_assoc()): 
            $counter++;
    ?>
      <div class="card <?= $counter > 6 ? 'hidden' : '' ?>">
        <label>
          <input type="radio" name="ucAmount" value="<?= htmlspecialchars($row['uc_number']) ?>  = Rs <?= htmlspecialchars($row['discounted_price']) ?>"> 
          <?= htmlspecialchars($row['uc_number']) ?> 
          <span><img src="assets/img/ud.png" alt="PUBG UC Icon" style="width: 20px; height: 20px;" /></span> - 
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
      <label for="userId">User ID:</label>
      <input type="text" name="userId" id="userId" placeholder="Enter Mobile Legends User ID" required>
   
      <label for="paymentDetails">Zone ID:</label>
      <input type="text" name="paymentDetails" id="paymentDetails" placeholder="Enter Your Zone ID" required>
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
      <p><strong>Region:</strong> Nepal</p>
    </form>



      </div>
    </div>
  </div>

  <script>
    // Pass PHP session user_id to JavaScript
    const userid = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
</script>
  <script src="assets/js/script.js"></script>
</body>
</html>
