<?php
// Return details for the currently logged-in admin as JSON.
require_once __DIR__ . '/seson.php';
require_once __DIR__ . '/dbcon.php';

header('Content-Type: application/json');

$adminId = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;
$name    = isset($_SESSION['username']) ? (string) $_SESSION['username'] : 'Admin';
$email   = isset($_SESSION['email']) ? (string) $_SESSION['email'] : '';

if ($adminId > 0 && isset($conn) && $conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT username, email FROM admin_users WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $name  = (string) ($row['username'] ?? $name);
            $email = (string) ($row['email'] ?? $email);
        }
        $stmt->close();
    }
}

echo json_encode([
    'name'  => $name,
    'role'  => 'Admin',
    'email' => $email,
    // No profile photo is stored for demo accounts; the UI falls back to a default avatar.
    'image' => null,
]);
