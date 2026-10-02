<?php 
include('seson.php');
include("dbcon.php");
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');

// Set the filter to 'pending' to fetch only pending orders
$statusFilter = 'completed';  // Hardcoded to fetch completed orders
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// List of order tables to query
$orderTables = [
    "efootball_website_orders" => ["E-Football", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "efootballios_website_orders" => ["E-Football iOS", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "clash_website_orders" => ["Clash of Clans", ["gem_amount", "supercell_email", "payment_details", "payment_option"]],
    "pubglobal_website_orders" => ["PUBG Global", ["uc_amount", "player_id", "payment_details", "payment_option"]],
    "unpin_website_orders" => ["unpin", [  "player_id", "payment_details", "payment_option"]],
    "pubg_website_orders" => ["PUBG", ["uc_amount", "player_id", "payment_details", "payment_option"]],
    "indonesiamobilelegends_website_orders" => ["Mobile Legends Indonesia", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "freefire_website_orders" => ["Free Fire", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "indonesiafreefire_website_orders" => ["Free Fire Indonesia", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "mlbb_website_orders" => ["Mobile Legends: Bang Bang", ["diamond_amount", "payment_details", "payment_option", "mlb_userid"]],
    "mobilelegends_website_orders" => ["Mobile Legends", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "tiktok_website_orders" => ["TikTok", ["coin_amount", "email_or_whatsapp", "payment_details", "payment_option", "tiktokuser_id"]]
];

$orders = [];
foreach ($orderTables as $table => $details) {
    $gameName = $details[0];
    $fields = $details[1];

    // Query to fetch only pending orders along with user details
    $query = "SELECT o.*, u.name, u.phone, u.email FROM $table o
              LEFT JOIN users u ON o.user_id = u.id
              WHERE o.status = ? ORDER BY o.created_at $orderBy";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $statusFilter);  // Bind status as 'completed'
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orderDetails = [
            'game' => $gameName, 
            'fields' => [], 
            'user_id' => $row['user_id'], 
            'user_name' => $row['name'],  // User details
            'user_phone' => $row['phone'], 
            'user_email' => $row['email'],
            'status' => $row['status'], 
            'id' => $row['id'],
            'game_type' => $table
        ];
        foreach ($fields as $field) {
            if (isset($row[$field])) {
                $orderDetails['fields'][$field] = $row[$field];
            }
        }
        $orders[$gameName][] = $orderDetails;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
    <link rel="stylesheet" href="css/webstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handle delete action
            $(".delete-button").click(function (e) {
                e.preventDefault();
                if (confirm("Are you sure you want to delete this order?")) {
                    const id = $(this).data("id");
                    const gameType = $(this).data("game-type");
                    $.ajax({
                        url: "delete_order.php",
                        method: "POST",
                        data: { action: "delete", id: id, game_type: gameType, csrf_token: "<?php echo e($_SESSION['admin_csrf_token']); ?>" },
                        dataType: "json",
                        success: function (response) {
                            alert(response.message);
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function () {
                            alert("An error occurred. Please try again.");
                        }
                    });
                }
            });

            // Handle update status action
            $(".update-status").click(function (e) {
                e.preventDefault();
                const id = $(this).data("id");
                const gameType = $(this).data("game-type");
                const status = $(this).data("status");
                $.ajax({
                    url: "update_status.php",
                    method: "POST",
                    data: { action: "update", id: id, game_type: gameType, status: status, csrf_token: "<?php echo e($_SESSION['admin_csrf_token']); ?>" },
                    dataType: "json",
                    success: function (response) {
                        alert(response.message);
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function () {
                        alert("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="container">
        <h1>Completed Orders</h1>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $gameName => $gameOrders): ?>
                <h2><?php echo htmlspecialchars($gameName); ?></h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>User Name</th> <!-- User Name Column -->
                            <th>Phone</th> <!-- User Phone Column -->
                            <th>Email</th> <!-- User Email Column -->
                            <?php 
                            if (count($gameOrders) > 0) {
                                foreach ($gameOrders[0]['fields'] as $field => $value): ?>
                                    <th><?php echo htmlspecialchars(ucwords(str_replace("_", " ", $field))); ?></th>
                                <?php endforeach; 
                            }
                            ?>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $serialNumber = 1; ?>
                        <?php foreach ($gameOrders as $order): ?>
                            <tr>
                                <td><?php echo $serialNumber++; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td> <!-- Display User Name -->
                                <td><?php echo htmlspecialchars($order['user_phone']); ?></td> <!-- Display User Phone -->
                                <td><?php echo htmlspecialchars($order['user_email']); ?></td> <!-- Display User Email -->
                                <?php foreach ($order['fields'] as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td>
                                    <button class="update-status" data-id="<?php echo $order['id']; ?>" data-game-type="<?php echo $order['game_type']; ?>" data-status="confirmed">Confirm</button>
                                    <button class="update-status" data-id="<?php echo $order['id']; ?>" data-game-type="<?php echo $order['game_type']; ?>" data-status="completed">Complete</button>
                                    <button class="update-status" data-id="<?php echo $order['id']; ?>" data-game-type="<?php echo $order['game_type']; ?>" data-status="rejected">Reject</button>
                                    <button class="delete-button" data-id="<?php echo $order['id']; ?>" data-game-type="<?php echo $order['game_type']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No completed orders found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php
include('includes/footer.php');
?>
