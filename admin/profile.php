<?php
// Include necessary files for session, headers, top section, sidebar, and database connection
include('seson.php');

include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');
include('dbcon.php');

// Check if the user is logged in (session exists)
if (isset($_SESSION['email'])) {
    $admin_id = $_SESSION['email'];

    // Fetch the admin details from the database using a prepared statement
    $sql = "SELECT * FROM admin_users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a valid admin record is found
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Set session variables after successfully fetching user details
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['id'] = $user['id'];

        } else {
            // If the user does not exist, redirect to the login page
            header("Location: sign/login.php");
            exit();
        }
    } else {
        // If query preparation fails, handle the error
        die("Error preparing statement: " . $conn->error);
    }
} else {
    // If session is not set, redirect to the login page
    header("Location: sign/login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Management</title>
    <link rel="stylesheet" href="css/profile.css"> <!-- Include your Bootstrap CSS -->
    <!-- Add Bootstrap CSS for modal functionality -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="profile-info">
        <h3>Profile Information</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
    </div>

    <div class="actions">
        <!-- Button to trigger Edit Profile Modal -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        <!-- Button to trigger Change Password Modal -->
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
    </div>
</div>

<!-- Modal for Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="update_profile.php" method="POST">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="edit_name" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="edit_email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="editPhone" name="edit_phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="change_password.php" method="POST" id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS for Modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Regular expression for password strength (at least 8 characters, including a number and a letter)
    const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

    // Function to validate password fields
    function validatePasswordForm(event) {
        // Get values from the password fields
        const currentPassword = document.getElementById('currentPassword').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();

        // Check if the new password field is empty
        if (newPassword === "") {
            alert("New password cannot be empty.");
            event.preventDefault();  // Prevent form submission
            return false;
        }

        // Password strength check
        if (!passwordPattern.test(newPassword)) {
            alert("New password must be at least 8 characters long, and contain both letters and numbers.");
            event.preventDefault();
            return false;
        }

        // Ensure the new password and confirmation password match
        if (newPassword !== confirmPassword) {
            alert("New password and confirmation password do not match.");
            event.preventDefault();
            return false;
        }

        // Optionally, check if the current password is correct (this check should be done server-side too)
        // For now, we can skip that check as we don't have access to the current password hash in JS.
        
        // If everything is valid, return true to allow form submission
        return true;
    }
    
    // Attach the validation function to the form submission
    document.getElementById('changePasswordForm').addEventListener('submit', validatePasswordForm);
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>

<?php 
$stmt->close(); 
?>
