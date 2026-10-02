<?php 
session_start();
include('dbcon.php');

// Check if the request method is POST (for OTP verification)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpEntered = htmlspecialchars($_POST['otp']);

    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otpEntered) {
        // OTP matches, proceed with registration
        $data = $_SESSION['registration_data'];
        unset($_SESSION['otp'], $_SESSION['registration_data']);

        // Check if the user is already registered
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the user already exists, show the message and button to login
        if ($result->num_rows > 0) {
            echo "
            <div style='text-align: center; padding: 40px; background-color: #f0f8ff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 400px; margin: auto;'>
                <h3 style='font-size: 24px; color: #2c3e50; font-family: Arial, sans-serif;'>You Have Already Registered</h3>
                <p style='font-size: 18px; color: #7f8c8d; font-family: Arial, sans-serif;'>It seems you have already registered with this email.</p>
                <p style='font-size: 16px; color: #34495e; margin-bottom: 20px; font-family: Arial, sans-serif;'>Please click the button below to login.</p>
                <form action='login.php' method='get'>
                    <button type='submit' style='padding: 12px 24px; background-color: #3498db; border: none; color: white; font-size: 18px; cursor: pointer; border-radius: 5px; transition: background-color 0.3s ease;'>Login</button>
                </form>
            </div>
            ";
        } else {
            // User is not registered, save new user data to the database
            $stmt = $conn->prepare("INSERT INTO users (name, phone, email, password, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $data['name'], $data['phone'], $data['email'], $data['password'], $data['address']); // Corrected this line to use $data['address']
            if ($stmt->execute()) {
                // Redirect to the index page after successful registration
                echo "Registration successful!";
                header('Location: ../index.php');
                exit(); // Ensure the script stops executing after the redirect
            } else {
                echo "Error occurred while registering: " . $stmt->error;
            }
        }
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>
