<?php
// Include database connection
include("../../dbcon.php");

// Define default status
$status = 'pending';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate if required form data exists
    if (!empty($_POST['playerId']) && !empty($_POST['paymentDetails']) && !empty($_POST['paymentOption']) && !empty($_POST['userId'])) {
        
        // Retrieve form data
        $playerId = $_POST['playerId'];
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];
        $userId = $_POST['userId'];

        // Prepare SQL query to insert data into netflix_website_orders table
        $query = "INSERT INTO unpin_website_orders (player_id, payment_details, payment_option, user_id, status) 
                  VALUES (?, ?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {

            // Bind parameters to prevent SQL injection
            $stmt->bind_param("sssss", $playerId, $paymentDetails, $paymentOption, $userId, $status);

            // Execute the query
            if ($stmt->execute()) {
                // Success: Show alert and redirect
                echo "<script>
                        alert('Order successfully placed!');
                        setTimeout(function() {
                            window.location.href = '../../order.php'; // Redirect to order page
                        }, 1000);
                      </script>";
            } else {
                // Failure: Show error alert
                echo "<script>
                        alert('Error: " . $stmt->error . "');
                        setTimeout(function() {
                            window.location.href = 'error_page.php'; // Redirect to error page (optional)
                        }, 3000);
                      </script>";
            }

            // Close statement
            $stmt->close();
        } else {
            echo "<script>alert('Database error: Unable to prepare SQL statement');</script>";
        }

        // Close database connection
        $conn->close();
        
    } else {
        echo "<script>alert('Missing required fields');</script>";
    }

} else {
    echo "<script>alert('Invalid request method');</script>";
}
?>
