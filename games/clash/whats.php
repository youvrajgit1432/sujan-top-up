<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary data exists in the POST request
    if (isset($_POST['gemAmount']) && isset($_POST['supercellEmail']) && isset($_POST['paymentDetails']) && isset($_POST['paymentOption'])) {
        $status='pending';
        // Retrieve the form data from the POST request
        $gemAmount = $_POST['gemAmount'];
        $supercellEmail = $_POST['supercellEmail'];
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];

        // Include the database connection file (dbcon.php should contain the database connection logic)
        include("../../dbcon.php");

        // Prepare SQL query to insert data into the clash_whatsapp_orders table
        $query = "INSERT INTO clash_whatsapp_orders (gem_amount, supercell_email, payment_details, payment_option,status) VALUES (?, ?,?, ?, ?)";

        // Prepare the statement to prevent SQL injection
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
            exit;
        }

        // Bind the parameters to the SQL statement
        $stmt->bind_param("sssss", $gemAmount, $supercellEmail, $paymentDetails, $paymentOption,$status);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to another page after successful order placement
            echo json_encode(['success' => true, 'message' => 'Order successfully processed']);
            // Redirecting to the "thank you" or confirmation page after the order is processed
            header("Location: ../../index.php"); // Change this to the desired page
            exit(); // Make sure no further code is executed after the redirection
        } else {
            // Return error message if the query fails
            echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        // Handle the case where required data is missing
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
 