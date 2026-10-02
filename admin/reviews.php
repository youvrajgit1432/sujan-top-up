<?php
// Start the session
include('seson.php');
// Include database connection
include("dbcon.php");

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

    header("Location: admin.php");
    exit();
}
$serial_number = 1;
// Fetch data from the database
$query = "SELECT * FROM feedback";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>
<?php
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .action-buttons button {
            margin-right: 10px;
            padding: 5px 10px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .action-buttons button.delete {
            background-color: #dc3545;
        }
        .action-buttons button:hover {
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
        }

        .modal-header,
        .modal-footer {
            padding: 10px;
            background-color: #f1f1f1;
            text-align: center;
        }

        .modal-header h2 {
            margin: 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form Styles inside Modal */
        form label {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        form input, form textarea {
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        form textarea {
            resize: vertical;
            height: 150px;
        }

        button.submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        button.submit:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <h1>Feedback Management</h1>

                <!-- Success/Error Messages -->
                <?php if (!empty($_SESSION['success_message'])): ?>
                    <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                <?php endif; ?>
                <?php if (!empty($_SESSION['error_message'])): ?>
                    <p style="color: red;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
                <?php endif; ?>

                <!-- Feedback Table -->
                <table>
                    <thead>
                        <tr><th>SN</th>
                            <th>ID</th>
                            <th>Username</th>
                          
                            <th>Type</th>
                            <th>Rating</th>
                            <th>Review</th>
                        
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                            <td><?php echo $serial_number++; ?></td> 
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                
                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                                <td><?php echo htmlspecialchars($row['review']); ?></td>
                               
                                <td class="action-buttons">
             <button class="edit-btn" data-id="<?php echo $row['id']; ?>" data-username="<?php echo $row['username']; ?>" 
             data-type="<?php echo $row['type']; ?>
             " data-rating="<?php echo $row['rating']; ?>" data-review="<?php echo $row['review']; ?>">Edit</button>
              <form action="admin.php" method="POST" style="display:inline;" id="deleteForm<?php echo $row['id']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
    <button type="button" class="delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
</form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Edit Feedback</h2>
            </div>
            <form action="edit.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="feedback_id">
                
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
 
                <label for="type">Type</label>
                <input type="text" name="type" id="type" required>

                <label for="rating">Rating</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>

                <label for="review">Review</label>
                <textarea name="review" id="review" required></textarea>

                <button type="submit" class="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        // Open modal when edit button is clicked
        const editButtons = document.querySelectorAll('.edit-btn');
        const modal = document.getElementById('editModal');
        const closeModal = document.querySelector('.close');

        editButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const id = button.getAttribute('data-id');
                const username = button.getAttribute('data-username');
             
                const type = button.getAttribute('data-type');
                const rating = button.getAttribute('data-rating');
                const review = button.getAttribute('data-review');
                
                document.getElementById('feedback_id').value = id;
                document.getElementById('username').value = username;
        
                document.getElementById('type').value = type;
                document.getElementById('rating').value = rating;
                document.getElementById('review').value = review;

                modal.style.display = "block";
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = "none";
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
        function confirmDelete(deleteId) {
        // Show a confirmation dialog
        if (confirm("Are you sure you want to delete this feedback?")) {
            // If confirmed, submit the delete form
            document.getElementById('deleteForm' + deleteId).submit();
        }
    }
    </script>
</body>
</html>



  <?php
include('includes/footer.php');
?>