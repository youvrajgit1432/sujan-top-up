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

    // Validate if required fields are missing or incorrect
    if (  !$rating || !$type) {
        die("Invalid input data.");
    }
 

    // Check if the user has already submitted the same review for the selected type more than 2 times
    $stmt = $conn->prepare("SELECT COUNT(*) FROM feedback WHERE username = ? AND review = ? AND type = ?");
    $stmt->bind_param('sss', $username, $review, $type);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count >= 2) {
        die("You have already submitted this review for the selected service more than 2 times. Please avoid duplicate feedback.");
    }

    // Prepare SQL Query with Prepared Statements (to prevent SQL injection)
    $stmt = $conn->prepare("INSERT INTO feedback (username,   type, rating, review) VALUES ( ?, ?, ?, ?)");
    $stmt->bind_param('ssss', $username,      $type, $rating, $review );
    session_start(); // Start session to manage flash messages

    // Execute Query
    if ($stmt->execute()) {
        // Set the success message in the session
        $_SESSION['success_message'] = "You have successfully submitted your feedback!";
        // Redirect to the main page
        header("Location: index.php");
        exit(); // Stop further execution
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close Prepared Statement and Connection
    $stmt->close();
    $conn->close();
}
?>
