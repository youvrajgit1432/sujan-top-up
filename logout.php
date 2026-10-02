<?php
session_start(); // Start the session

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page or homepage
header("Location: index.php"); // Redirect to login page (or index.php if you want to go to the homepage)
exit(); // Make sure no further code is executed
?>
