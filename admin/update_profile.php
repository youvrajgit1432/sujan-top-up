<?php
session_start();
include('dbcon.php');  // Make sure to include the database connection file

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign/login.php");  // Redirect to login if not logged in
    exit();
}

$admin_id = $_SESSION['id'];  // Use the session variable to get the admin ID

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the form data and sanitize it
    $edit_name = trim($_POST['edit_name']);
    $edit_email = trim($_POST['edit_email']);
    $edit_phone = trim($_POST['edit_phone']);

    // Validate the data (you can add further validation here)
    if (empty($edit_name) || empty($edit_email) || empty($edit_phone)) {
        echo "All fields are required.";
        exit();
    }

    // Update query
    $sql = "UPDATE admin_users SET username = ?, email = ?, phone_number = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $edit_name, $edit_email, $edit_phone, $admin_id);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Successfully updated, redirect back to profile page
        header("Location: profile.php");  // Redirect to profile page
        exit();
    } else {
        echo "Error updating profile. Please try again.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
