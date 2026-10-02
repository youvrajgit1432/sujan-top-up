<?php
// cancel_order.php

header("Content-Type: application/json");
session_start();
include("dbcon.php");

// Only a logged-in user may cancel an order.
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to cancel an order.']);
    exit;
}

// CSRF protection.
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

if (isset($_POST['action'], $_POST['id'], $_POST['game_type'], $_POST['status']) && $_POST['action'] === 'update') {
    $order_id = (int) $_POST['id'];
    $game_type = trim((string) $_POST['game_type']);
    $status = trim((string) $_POST['status']);

    $valid_statuses = ['cancelled'];
    $valid_tables = [
        'efootball_website_orders',
        'efootballios_website_orders',
        'clash_website_orders',
        'pubglobal_website_orders',
        'pubg_website_orders',
        'indonesiamobilelegends_website_orders',
        'freefire_website_orders',
        'indonesiafreefire_website_orders',
        'mlbb_website_orders',
        'mobilelegends_website_orders',
        'tiktok_website_orders'
    ];

    if (in_array($status, $valid_statuses) && in_array($game_type, $valid_tables)) {
        // Scope the cancellation to the logged-in user's own order.
        $query = "UPDATE $game_type SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sis", $status, $order_id, $_SESSION['user_id']);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Order #$order_id has been successfully cancelled!"
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => "Order not found for your account."
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => "Failed to update the order status. Error: " . $stmt->error
                ]);
            }

            $stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Failed to prepare the SQL statement."
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Invalid status or game type."
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Invalid request parameters or action."
    ]);
}

$conn->close();
?>
