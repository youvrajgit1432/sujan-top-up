<?php
 include('seson.php');
include("dbcon.php");

// CSRF protection for this state-changing operation.
if (!isset($_POST['csrf_token'], $_SESSION['admin_csrf_token'])
    || !hash_equals($_SESSION['admin_csrf_token'], (string) $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Check if the AJAX request contains necessary parameters
if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id'], $_POST['status'], $_POST['game_type'])) {
    $order_id = (int) $_POST['id'];
    $status = trim((string) $_POST['status']);
    $game_type = trim((string) $_POST['game_type']);

    // Validate input
    $valid_statuses = ['pending', 'confirmed', 'completed', 'rejected'];
    $valid_tables = [
        'tiktok_whatsapp_orders' => 'tiktok_whatsapp_orders',
        'pubglobal_whatsapp_orders' => 'pubglobal_whatsapp_orders',
        'netflix_whatsapp_orders' => 'netflix_whatsapp_orders',
        'unpin_whatsapp_orders' => 'unpin_whatsapp_orders',
        'pubg_whatsapp_orders' => 'pubg_whatsapp_orders',
        'indonesiamobilelegends_whatsapp_orders' => 'indonesiamobilelegends_whatsapp_orders',
        'mobilelegends_whatsapp_orders' => 'mobilelegends_whatsapp_orders',
        'mlbb_whatsapp_orders' => 'mlbb_whatsapp_orders',
        'freefire_whatsapp_orders' => 'freefire_whatsapp_orders',
        'indonesiafreefire_whatsapp_orders' => 'indonesiafreefire_whatsapp_orders',
        'efootball_whatsapp_orders' => 'efootball_whatsapp_orders',
        'efootballios_whatsapp_orders' => 'efootballios_whatsapp_orders',
        'clash_whatsapp_orders' => 'clash_whatsapp_orders'
    ];
    

    // Validate status and game type
    if (in_array($status, $valid_statuses) && array_key_exists($game_type, $valid_tables)) {
        $table_name = $valid_tables[$game_type];
        $query = "UPDATE $table_name SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("si", $status, $order_id);

            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => "Order status updated to '$status' successfully!"
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Failed to update order status! Error: " . $stmt->error
                ];
            }

            $stmt->close();
        } else {
            $response = [
                'success' => false,
                'message' => "Database query preparation failed!"
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => "Invalid status or game type!"
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => "Order ID, status, or game type not provided!"
    ];
}

// Close the database connection
$conn->close();

// Send JSON response back to AJAX
echo json_encode($response);
?>
