<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['diamondAmount'], $_POST['userId'], $_POST['paymentDetails'], $_POST['paymentOption'])) {
        $diamondAmount = $_POST['diamondAmount'];
        $userId = $_POST['userId'];
        $paymentDetails = $_POST['paymentDetails'];
        $paymentOption = $_POST['paymentOption'];
        $userid = $_POST['userid'];
        $status='pending';
        include("../../dbcon.php");

        $query = "INSERT INTO mlbb_website_orders (diamond_amount, payment_details, payment_option,mlb_userid,user_id,status) 
                  VALUES (?, ?,?, ?, ?,?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
            exit;
        }

        $stmt->bind_param("ssssss", $diamondAmount, $paymentDetails, $paymentOption, $userId,$userid,$status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order successfully processed']);
            header("Location: ../../order.php");
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
