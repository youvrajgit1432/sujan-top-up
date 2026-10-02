<?php
// Include database connection and admin session guard
include('seson.php');
include('dbcon.php');

// Deletion is a destructive action: require POST + CSRF.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php?status=error');
    exit;
}

if (!isset($_POST['csrf_token'], $_SESSION['admin_csrf_token'])
    || !hash_equals($_SESSION['admin_csrf_token'], (string) $_POST['csrf_token'])) {
    header('Location: users.php?status=error');
    exit;
}

$userId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($userId <= 0) {
    header('Location: users.php?status=error');
    exit;
}

// Back up the user before deletion.
$select = $conn->prepare("SELECT id, name, phone, email, password FROM users WHERE id = ?");
$select->bind_param("i", $userId);
$select->execute();
$result = $select->get_result();

if ($result->num_rows === 0) {
    header('Location: users.php?status=not_found');
    exit;
}

$user = $result->fetch_assoc();

$backup = $conn->prepare("INSERT INTO usersbackup (id, name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
$backup->bind_param("issss", $user['id'], $user['name'], $user['phone'], $user['email'], $user['password']);

if (!$backup->execute()) {
    error_log('delete_user backup failed: ' . $backup->error);
    header('Location: users.php?status=error');
    exit;
}

$delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $userId);

if ($delete->execute()) {
    header('Location: users.php?status=user_deleted_success');
} else {
    error_log('delete_user delete failed: ' . $delete->error);
    header('Location: users.php?status=error');
}
exit;
