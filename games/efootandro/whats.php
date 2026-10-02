<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary data exists in the POST request
    if (isset($_POST['coinAmount'], $_POST['konamiEmail'], $_POST['password'], $_POST['paymentDetails'], $_POST['paymentOption'])) {

        // Retrieve the form data from the POST request
        $coinAmount = $_POST['coinAmount'];
        $konamiEmail = $_POST['konamiEmail'];
        $password = $_POST['password']; // For security reasons, you may not want to store passwords directly
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];
        $status='pending';
        // Include the database connection file (dbcon.php should contain the database connection logic)
        include("../../dbcon.php");

        // Prepare SQL query to insert data into the efootball_whatsapp_orders table
        $query = "INSERT INTO efootball_whatsapp_orders (coin_amount, konami_email, password, payment_details, payment_option,status) 
                  VALUES (?, ?, ?, ?, ?,?)";

        // Prepare the statement to prevent SQL injection
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
            exit;
        }

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ssssss", $coinAmount, $konamiEmail, $password, $paymentDetails, $paymentOption,$status);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order successfully processed']);
            header("Location: ../../index.php");
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
