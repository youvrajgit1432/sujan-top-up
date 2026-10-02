<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['diamondAmount'], $_POST['userId'], $_POST['paymentDetails'], $_POST['paymentOption'])) {
        $diamondAmount = $_POST['diamondAmount'];
        $userId = $_POST['userId'];
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];
        $status='pending';
        include("../../dbcon.php");

        $query = "INSERT INTO mlbb_whatsapp_orders (diamond_amount, user_id, payment_details, payment_option,status) 
                  VALUES (?, ?, ?, ?,?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
            exit;
        }

        $stmt->bind_param("sssss", $diamondAmount, $userId, $paymentDetails, $paymentOption,$status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order successfully processed']);
            header("Location: ../../index.php");
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
