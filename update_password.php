<?php
// Start session
session_start();

// Include database connection
include('dbcon.php');

// Ensure that the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input data
        $currentPassword = trim($_POST['current_password']);
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        // Check if the new password and confirm password match
        if ($newPassword !== $confirmPassword) {
            echo "New passwords do not match!";
            exit();
        }

        // Validate password strength (example: at least 8 characters, mix of letters, numbers, and special characters)
        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/", $newPassword)) {
            echo "Password must be at least 8 characters long and contain a mix of letters, numbers, and special characters.";
            exit();
        }

        // Fetch the current password from the database
        $query = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die('Query preparation failed: ' . $conn->error);
        }

        // Bind parameters and execute the query
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $hashedPassword = $user['password'];

            // Verify the current password
            if (password_verify($currentPassword, $hashedPassword)) {
                // Hash the new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateQuery);

                if ($updateStmt === false) {
                    die('Query preparation failed: ' . $conn->error);
                }

                // Bind parameters and execute the update query
                $updateStmt->bind_param("si", $newHashedPassword, $userId);
                if ($updateStmt->execute()) {
                    // Redirect to the profile page or show a success message
                    header('Location: profile.php');
                    exit();
                } else {
                    echo "Error changing password: " . $updateStmt->error;
                }
            } else {
                echo "Current password is incorrect!";
            }
        } else {
            echo "User not found!";
        }
    }
} else {
    // Redirect to the login page if the user is not logged in
    header('Location: login.php');
    exit();
}
?>
