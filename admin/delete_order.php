<?php
 include('seson.php');
include("dbcon.php");

// CSRF protection for this destructive operation.
if (!isset($_POST['csrf_token'], $_SESSION['admin_csrf_token'])
    || !hash_equals($_SESSION['admin_csrf_token'], (string) $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Check if the AJAX request contains the required parameters
if (isset($_POST['id'], $_POST['game_type'])) {
    $order_id = (int) $_POST['id'];
    $game_type = (string) $_POST['game_type'];

    // Validate game type
    $valid_tables = [
        'efootball_website_orders' => 'efootball_website_orders',
        'efootballios_website_orders' => 'efootballios_website_orders',
        'clash_website_orders' => 'clash_website_orders',
        'pubglobal_website_orders' => 'pubglobal_website_orders',
        'pubg_website_orders' => 'pubg_website_orders',
              'netflix_website_orders' => 'netflix_website_orders',
        'unpin_website_orders' => 'unpin_website_orders',
        'indonesiamobilelegends_website_orders' => 'indonesiamobilelegends_website_orders',
        'freefire_website_orders' => 'freefire_website_orders',
        'indonesiafreefire_website_orders' => 'indonesiafreefire_website_orders',
        'mlbb_website_orders' => 'mlbb_website_orders',
        'mobilelegends_website_orders' => 'mobilelegends_website_orders',
        'tiktok_website_orders' => 'tiktok_website_orders'
    ];
  

    if (array_key_exists($game_type, $valid_tables)) {
        $table_name = $valid_tables[$game_type];

        // Prepare and execute the DELETE query
        $query = "DELETE FROM $table_name WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);

        // Prepare the response
        $response = [];
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Order ID $order_id deleted successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to delete order ID $order_id.";
        }
        $stmt->close();

        // Send the JSON response back to the AJAX request
        echo json_encode($response);
    } else {
        // Invalid game type
        echo json_encode(['success' => false, 'message' => 'Invalid game type!']);
    }
} else {
    // Missing parameters
    echo json_encode(['success' => false, 'message' => 'Order ID or game type not provided!']);
}

// Close the database connection
$conn->close();
?>
