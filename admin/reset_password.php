<?php
// Include database connection and session management
include('session.php');
include('dbcon.php');

// Check if the user ID is passed in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Define the default password
    $defaultPassword = '4545';

    // Hash the default password before saving it to the database
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // SQL query to update the password
    $sql = "UPDATE users SET password='$hashedPassword' WHERE id='$userId'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect back to the user management page with a success message
        header("Location: users.php?status=password_reset_success");
    } else {
        // Handle error if the update fails
        echo "Error: " . $conn->error;
    }
} else {
    // Redirect to user management page if no ID is passed
    header("Location: users.php?status=error");
}

// Close the database connection
$conn->close();
?>
