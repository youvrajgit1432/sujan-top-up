<?php
// Start session at the very beginning to handle CSRF tokens
session_start();

// Check if the CSRF token is set in the session, if not, generate one
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generate a random CSRF token
}

// Database Connection
include("dbcon.php");

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }

    // Sanitize user inputs and validate them
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    
    $type = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);
    $review = filter_var(trim($_POST['review']), FILTER_SANITIZE_STRING);
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT); // Assuming the feedback ID is passed for editing

    // Validate if required fields are missing or incorrect
    if ( !$rating || !$type || !$id) {
        die("Invalid input data.");
    }

    // Prepare SQL Query to update the existing feedback based on ID
    $stmt = $conn->prepare("UPDATE feedback SET username = ?,    type = ?, rating = ?, review = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $username,   $type, $rating, $review, $id);

    // Execute Query
    if ($stmt->execute()) {
        // Set the success message in the session
        $_SESSION['success_message'] = "Feedback editted successfully!";
        // Redirect to the main page
        header("Location: reviews.php");
        exit(); // Stop further execution
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close Prepared Statement and Connection
    $stmt->close();
    $conn->close();
}
?>
