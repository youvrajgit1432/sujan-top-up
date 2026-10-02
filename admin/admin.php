<?php
// Start the session
include('seson.php');
// Include database connection
include("dbcon.php");

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Record deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
    }

    header("Location: reviews.php");
    exit();
}

// Fetch data from the database
$query = "SELECT * FROM feedback";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!-- The HTML part for displaying the table with feedbacks -->
