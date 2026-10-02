<?php 
// Start the session
include('seson.php');
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');
include('dbcon.php'); 
// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Check if user is logged in
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $uc_number = htmlspecialchars($_POST['uc_number']);
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
    $discounted_price = floatval($_POST['discounted_price']);
    $game_type = "PUBG"; // Hidden game type field
// Insert data into the database
$stmt = $conn->prepare("INSERT INTO games (game_type, uc_number, original_price, discounted_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdd", $game_type, $uc_number, $original_price, $discounted_price);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Record added successfully.";
} else {
    $_SESSION['error_message'] = "Error adding record: " . $stmt->error;
}

$stmt->close();

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

// Fetch data from the database
$query = "SELECT * FROM games";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUBG UC Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="css/games.css">
</head>
<body>
    <div class="container">

        <!-- Data Table -->
        <h2>All Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Serial No</th>
                    <th>Game</th>
                    <th>Items</th>
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
                            <td><?= htmlspecialchars(ucfirst(strtolower($row['game_type']))); ?></td>

                            <td><?= htmlspecialchars($row['uc_number']); ?></td>
                            <td style="color: red; <?= empty($row['original_price']) ? '' : 'text-decoration: line-through;' ?>">
    <?= empty($row['original_price']) ? '-' : htmlspecialchars($row['original_price']); ?>
</td>

 
<td style="color: green;">
    <?= htmlspecialchars($row['discounted_price']); ?>
</td>
 <td><a href="?delete=<?= $row['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a></td>
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