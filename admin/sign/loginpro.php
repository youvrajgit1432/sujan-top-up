<?php
/**
 * Admin login processor.
 *
 * Authenticates against the admin_users table only. There is no built-in
 * default administrator: the demo admin lives in database/demo_seed.sql.
 */

session_start();
require_once dirname(__DIR__) . '/dbcon.php';
require_once dirname(__DIR__, 2) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['admin_csrf_token'])
    || !hash_equals($_SESSION['admin_csrf_token'], (string) $_POST['csrf_token'])) {
    header('Location: login.php?error=' . urlencode('Invalid session token. Please try again.'));
    exit;
}

$login = trim((string) ($_POST['login'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($login === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Please enter your credentials.'));
    exit;
}

// Resolve the account by email, phone or username.
if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
    $stmt = $conn->prepare("SELECT id, username, email, password, login_attempts FROM admin_users WHERE email = ?");
} elseif (ctype_digit($login)) {
    $stmt = $conn->prepare("SELECT id, username, email, password, login_attempts FROM admin_users WHERE phone_number = ?");
} else {
    $stmt = $conn->prepare("SELECT id, username, email, password, login_attempts FROM admin_users WHERE username = ?");
}

$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Use a generic message for both unknown user and wrong password.
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: login.php?error=' . urlencode('Invalid login credentials.'));
    exit;
}

// Successful password check: establish a fresh admin session.
session_regenerate_id(true);
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();

header('Location: ../index.php');
exit;
