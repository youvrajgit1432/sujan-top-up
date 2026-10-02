<?php
session_start();
include('dbcon.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and capture form inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));
  
    $password = htmlspecialchars($_POST['pass']);
    $address = htmlspecialchars(trim($_POST['address']));
    $confirmPassword = htmlspecialchars($_POST['re_pass']);

    // Validate input fields
    if (empty($name) || empty($phone)|| empty($address) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "All fields are required. Please try again.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please try again.";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Store data temporarily in the session for OTP verification
    $_SESSION['registration_data'] = [
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'address' =>$address,
        'password' => $hashedPassword
    ];

    // Generate OTP and store it in the session
    $_SESSION['otp'] = rand(100000, 999999);

    // Redirect to the OTP sending page
    header('Location: ../sendOtp.php');
    exit;

} else {
    // Handle invalid request methods
    echo "Invalid request method.";
    exit;
}
?>
