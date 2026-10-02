<?php
// Include database connection
include("../../dbcon.php");
$status='pending';
// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Access data from the form submission
    $ucAmount = $_POST['ucAmount'];
    $playerId = $_POST['playerId'];
    $paymentDetails = $_POST['paymentDetails'];
    $paymentOption = $_POST['paymentOption'];
    $userId = $_POST['userId']; // Add user_id from the form data

    // Prepare SQL query to insert the data into the database
    $query = "INSERT INTO pubg_website_orders (uc_amount, player_id, payment_details, payment_option, user_id,status) VALUES (?, ?, ?  , ?, ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $ucAmount, $playerId, $paymentDetails, $paymentOption, $userId,$status);

    // Execute the query
    if ($stmt->execute()) {
        // Order successfully placed, show an alert and redirect
        echo "<script>
                alert('Order successfully placed!');
                setTimeout(function() {
                    window.location.href = '../../order.php'; // Redirect to the thank you page (or any page you choose)
                }, 1000); // Delay of 1 second (1000 milliseconds)
              </script>";
    } else {
        // Error occurred, show an alert and redirect to error page or stay on the same page
        echo "<script>
                alert('Error: " . $stmt->error . "');
                setTimeout(function() {
                    window.location.href = 'error_page.php'; // Redirect to an error page (optional)
                }, 3000);
              </script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
