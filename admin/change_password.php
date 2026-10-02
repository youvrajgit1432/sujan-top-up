<?php
session_start();
include('dbcon.php');  // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign/login.php");  // Redirect to login page if not logged in
    exit();
}

$admin_id = $_SESSION['id'];  // Get the admin ID from session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Sanitize and get the form data
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate the inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "All fields are required.";
        exit();
    }

    // Check if the new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo "New password and confirmation do not match.";
        exit();
    }

    // Fetch the current password from the database
    $sql = "SELECT password FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verify the current password (assuming it's stored as a hashed password)
        if (!password_verify($current_password, $hashed_password)) {
            echo "Current password is incorrect.";
            exit();
        }

        // Hash the new password before updating
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_sql = "UPDATE admin_users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_hashed_password, $admin_id);

        if ($update_stmt->execute()) {
            echo "Password changed successfully!";
            // Redirect to the profile page or any success page
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating password. Please try again.";
        }

        // Close the update statement
        $update_stmt->close();
    } else {
        echo "User not found.";
        exit();
    }

    // Close the prepared statements
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
