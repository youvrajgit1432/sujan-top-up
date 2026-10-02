<?php
// Start session
session_start();

// Include database connection
include('dbcon.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get and sanitize form data
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING); // Assuming the phone is numeric
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format!";
            exit();
        }

        // Validate phone number (example validation for numeric input)
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            echo "Invalid phone number format!";
            exit();
        }
        
        // Update the user profile in the database
        $query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die('Query preparation failed: ' . $conn->error);
        }

        // Bind parameters and execute the query
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $userId);
        if ($stmt->execute()) {
            // Redirect to the profile page or show a success message
            header('Location: profile.php');
            exit();
        } else {
            // Handle error if the update fails
            echo "Error updating profile: " . $stmt->error;
        }
    }
} else {
    // Redirect to the login page if the user is not logged in
    header('Location: login.php');
    exit();
}
?>
