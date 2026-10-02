<?php
/**
 * Payment proof upload endpoint.
 *
 * Replaces the legacy admin/ss.php endpoint. Requires a logged-in user,
 * validates the order belongs to them, and enforces the shared upload rules.
 */

session_start();
require_once __DIR__ . '/config/uploads.php';
require_once __DIR__ . '/dbcon.php';

header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo 'You must be logged in to submit a payment photo.';
    exit;
}

$orderId = (int) ($_POST['id'] ?? 0);
$gameType = (string) ($_POST['game_type'] ?? '');

// Only allow tables the application actually uses.
$validTables = [
    'efootball_website_orders', 'efootballios_website_orders', 'clash_website_orders',
    'pubglobal_website_orders', 'pubg_website_orders', 'netflix_website_orders',
    'unpin_website_orders', 'indonesiamobilelegends_website_orders',
    'freefire_website_orders', 'indonesiafreefire_website_orders',
    'mlbb_website_orders', 'mobilelegends_website_orders', 'tiktok_website_orders',
];

if ($orderId <= 0 || !in_array($gameType, $validTables, true)) {
    http_response_code(400);
    echo 'Invalid order reference.';
    exit;
}

// Verify ownership before accepting the upload.
$check = $conn->prepare("SELECT id FROM `$gameType` WHERE id = ? AND user_id = ?");
$check->bind_param("is", $orderId, $_SESSION['user_id']);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    http_response_code(403);
    echo 'Order not found for this account.';
    exit;
}

if (!isset($_FILES['transaction_photo'])) {
    http_response_code(400);
    echo 'No payment photo submitted.';
    exit;
}

$result = sujan_store_upload(
    $_FILES['transaction_photo'],
    __DIR__ . '/uploads',
    sujan_image_allowlist(),
    5 * 1024 * 1024,
    'payment_'
);

if (!$result['ok']) {
    http_response_code(400);
    echo $result['error'];
    exit;
}

// Store a path relative to the web root for display.
$relative = 'uploads/' . basename($result['path']);

$stmt = $conn->prepare("UPDATE `$gameType` SET image = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sis", $relative, $orderId, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo 'Payment photo stored successfully.';
} else {
    error_log('payment_proof update failed: ' . $stmt->error);
    echo 'Could not save the payment photo.';
}
