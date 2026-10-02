<?php
// Start the session
session_start();

// Central application config (constants + helpers such as e()).
require_once dirname(__DIR__) . '/config/app.php';


// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: sign/login.php");
    exit;
}
// Session timeout settings (in seconds)
$timeout_duration =19000*909*90*90; // 10 minutes of inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    // Destroy session and redirect to login if session expired due to inactivity
    session_unset();
    session_destroy();
    header("Location: sign/login.php?timeout=true");
    exit;
}

// Update last activity time to track user activity
$_SESSION['last_activity'] = time();

// CSRF token for admin state-changing AJAX operations.
if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

// Maximum session duration (in seconds), i.e., 90 days
$max_session_duration = 30*90 * 24 * 60 * 60; // 90 days in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $max_session_duration)) {
    // Destroy session and redirect to login if session expired due to max session time
    session_unset();
    session_destroy();
    header("Location: sign/login.php?expired=true");
    exit;
}

// If user is logged in and session is active, allow access

?>
