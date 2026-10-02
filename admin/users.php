<?php
include('seson.php');
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* General Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        /* Table Header Styling */
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* Alternate Row Colors */
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Hover Effect for Table Rows */
        tr:hover {
            background-color: #e1f7e0;
        }

        /* Action Buttons Styling */
        td a {
            color: white;
            padding: 8px 12px;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }

        td a:hover {
            background-color: rgb(38, 90, 149);
            color: white;
        }

        /* Delete Button */
        td a.delete, td .link-button {
            background-color: #DC3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        td a.delete:hover, td .link-button:hover {
            background-color: #c82333;
        }

        /* Reset and Edit Button */
        td a.edit, td a.reset {
            background-color: #28A745;
        }

        td a.edit:hover, td a.reset:hover {
            background-color: #218838;
        }

        /* Button Styling for Confirmations */
        a:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            <?php
            include('dbcon.php'); // Database connection

            // Display status message if available
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'password_reset_success') {
                    echo "<p style='color: green;'>Password reset successfully!</p>";
                } elseif ($_GET['status'] == 'error') {
                    echo "<p style='color: red;'>Error occurred. Try again!</p>";
                }
            }

            // Fetch users from the database
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);

            // Check if there are any users in the database
            if ($result->num_rows > 0) {
                echo "<table border='1' cellpadding='10' cellspacing='0'>";
                echo "<thead>
                        <tr>
                            <th>S/N</th>
                           
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                      </thead>";
                echo "<tbody>";

                // Initialize serial number
                $serialNumber = 1;

                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $serialNumber++ . "</td>
                        
                            <td>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . "</td>
                            <td>
                                <a href='reset_password.php?id=" . (int) $row['id'] . "' onclick='return confirmPasswordReset()'>Reset</a> |
                                <form method='POST' action='delete_user.php' style='display:inline' onsubmit='return confirmDeleteUser()'>
                                  <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['admin_csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                                  <input type='hidden' name='id' value='" . (int) $row['id'] . "'>
                                  <button type='submit' class='link-button'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "No users found.";
            }

            $conn->close();
            ?>

        </div>
    </section>
</div>

<!-- JavaScript for Confirmations -->
<script>
    function confirmPasswordReset() {
        var result = confirm("Are you sure you want to reset the password? The password will be set to the default password for this user.");
        return result;
    }

    function confirmDeleteUser() {
        var result = confirm("Are you sure you want to delete this user?");
        return result;
    }
</script>

</body>
</html>

<?php
include('includes/footer.php');
?>
