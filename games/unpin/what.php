<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if required POST data exists
    if (!empty($_POST['playerId']) && !empty($_POST['paymentDetails']) && !empty($_POST['paymentOption'])) {

        // Retrieve form data
        $playerId = $_POST['playerId'];
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];
        $status = 'pending';

        // Include database connection
        include("../../dbcon.php");

        // Prepare SQL query to insert data into pubg_whatsapp_orders table
        $query = "INSERT INTO unpin_whatsapp_orders (player_id, username_details, payment_option, status) 
                  VALUES (?, ?, ?, ?)";

        // Prepare statement to prevent SQL injection
        if ($stmt = $conn->prepare($query)) {
            
            // Bind parameters to the SQL statement
            $stmt->bind_param("ssss", $playerId, $paymentDetails, $paymentOption, $status);

            // Execute the query
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Order successfully processed']);
                
                // Redirect after successful order placement
                header("Location: ../../index.php");
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
            }

            // Close statement
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
        }

        // Close database connection
        $conn->close();

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
