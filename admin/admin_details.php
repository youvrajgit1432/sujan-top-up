<?php 
include('seson.php');

include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');

include('dbcon.php'); 

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script> window.location.href='admin_details.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$sql = "SELECT * FROM admin_users";
$result = $conn->query($sql);

if (isset($_POST['update'])) {
    // Update logic here
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    
    $update_sql = "UPDATE admin_users SET username = ?, email = ?, phone_number = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $username, $email, $phone_number, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='';</script>";
    } else {
        echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users Management</title>
 <link rel="stylesheet" href="css/admindetail.css">
</head>
<body>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <h1>Admin Users Management</h1>
                <button class="add-btn"><a href="sign.php">Add Admin</a></button>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                    <td>
                                        <button onclick="showPopup('edit', <?php echo $row['id']; ?>)">Edit</button>
                                        <a href="?delete_id=<?php echo $row['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this record?');">
                                           <button class="delete-btn">Delete</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Popup Modal -->
    <div id="popup-overlay"></div>
    <div id="popup-form">
        <h3 id="popup-title"></h3>
        <form method="POST">
            <input type="hidden" id="admin-id" name="id">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="phone_number">Phone Number:</label><br>
            <input type="text" id="phone_number" name="phone_number" required><br><br>
            <button type="submit" name="update">Update</button>
            <button type="button" onclick="closePopup()">Close</button>
        </form>
    </div>

    <script>
        function showPopup(type, id = null) {
            document.getElementById('popup-overlay').style.display = 'block';
            document.getElementById('popup-form').style.display = 'block';
            document.getElementById('popup-title').innerText = type === 'add' ? 'Add Admin' : 'Edit Admin';
            
            // If editing, fetch data from database
            if (type === 'edit' && id) {
                // Use AJAX to fetch data based on id and populate form fields
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_user_data.php?id=" + id, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        document.getElementById('admin-id').value = data.id;
                        document.getElementById('username').value = data.username;
                        document.getElementById('email').value = data.email;
                        document.getElementById('phone_number').value = data.phone_number;
                    }
                };
                xhr.send();
            }
        }

        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
            document.getElementById('popup-form').style.display = 'none';
        }

        document.getElementById('popup-overlay').onclick = closePopup;
    </script>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>

<?php $conn->close(); ?>
