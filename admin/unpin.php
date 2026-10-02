<?php
// Start the session
include('seson.php');
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');

// Database connection
include("dbcon.php");

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission for adding or updating records
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $uc_number = htmlspecialchars($_POST['uc_number']);
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
    $discounted_price = floatval($_POST['discounted_price']);
    $game_type = "unpin"; // Hidden game type field

    if (isset($_POST['edit_id'])) {
        // Update existing record
        $edit_id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE games SET uc_number = ?, original_price = ?, discounted_price = ? WHERE id = ?");
        $stmt->bind_param("sddi", $uc_number, $original_price, $discounted_price, $edit_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Record updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO games (game_type, uc_number, original_price, discounted_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $game_type, $uc_number, $original_price, $discounted_price);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Record added successfully.";
        } else {
            $_SESSION['error_message'] = "Error adding record: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt_delete = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);

    if ($stmt_delete->execute()) {
        $_SESSION['success_message'] = "Record deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting record: " . $stmt_delete->error;
    }

    $stmt_delete->close();
}

// Fetch data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt_edit = $conn->prepare("SELECT * FROM games WHERE id = ?");
    $stmt_edit->bind_param("i", $edit_id);

    if ($stmt_edit->execute()) {
        $result = $stmt_edit->get_result();
        $edit_data = $result->fetch_assoc();
    }

    $stmt_edit->close();
}

// Fetch all records
$query = "SELECT * FROM games WHERE game_type = 'unpin'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Product Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/games.css">
</head>
<body>
    <div class="container">
        <h1>Unpin Voucher Entry</h1>

        <!-- Success and Error Messages -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <p class="success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error_message'])): ?>
            <p class="error"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <!-- Entry Form -->
        <form method="POST">
            <label for="uc_number">Product Type</label>
            <input type="text" name="uc_number" id="uc_number" placeholder="Enter Product Name" value="<?= $edit_data['uc_number'] ?? ''; ?>" required>

            <label for="original_price">Original Price (Optional)</label>
            <input type="number" name="original_price" id="original_price" placeholder="Enter Original Price" value="<?= $edit_data['original_price'] ?? ''; ?>">

            <label for="discounted_price">Discounted Price</label>
            <input type="number" name="discounted_price" id="discounted_price" placeholder="Enter Discounted Price" value="<?= $edit_data['discounted_price'] ?? ''; ?>" required>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <?php if ($edit_data): ?>
                <input type="hidden" name="edit_id" value="<?= $edit_data['id']; ?>">
                <button type="submit" name="submit">Update</button>
            <?php else: ?>
                <button type="submit" name="submit">Submit</button>
            <?php endif; ?>
        </form>

        <!-- Data Table -->
        <h2>Existing Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Serial No</th>
                    <th>Product</th>
                    <th>Original Price</th>
                    <th>Discounted Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $serial_number = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $serial_number++; ?></td>
                            <td><?= htmlspecialchars($row['uc_number']); ?></td>
                            <td style="color: red; <?= empty($row['original_price']) ? '' : 'text-decoration: line-through;' ?>">
                                <?= empty($row['original_price']) ? '-' : htmlspecialchars($row['original_price']); ?>
                            </td>
                            <td style="color: green;">
                                <?= htmlspecialchars($row['discounted_price']); ?>
                            </td>
                            <td>
    <a href="?edit=<?= $row['id']; ?>" style="background-color: #4CAF50; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; font-size: 14px; margin-right: 5px; display: inline-block;">Edit</a>
    <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')" style="background-color: #f44336; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; font-size: 14px; display: inline-block;">Delete</a>
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
</body>
</html>

<?php
include('includes/footer.php');
?>
