<?php
include('seson.php');
  include("dbcon.php");
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');

// Filter values
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Define the orders' table configurations
$orderTables = [
    // Website orders
 
    // WhatsApp orders
    "pubglobal_whatsapp_orders" => ["PUBG Global (WhatsApp)", ["uc_amount", "player_id", "payment_option"]],
    "pubg_whatsapp_orders" => ["PUBG (WhatsApp)", ["uc_amount", "player_id", "payment_option"]],
    "netflix_whatsapp_orders" => ["netflix (WhatsApp)", ["uc_amount", "player_id", "payment_option"]],
    "indonesiamobilelegends_whatsapp_orders" => ["Mobile Legends Indonesia (WhatsApp)", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "mobilelegends_whatsapp_orders" => ["Mobile Legends (WhatsApp)", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "mlbb_whatsapp_orders" => ["Mobile Legends: Bang Bang (WhatsApp)", ["diamond_amount", "user_id", "payment_details", "payment_option"]],
    "freefire_whatsapp_orders" => ["Free Fire (WhatsApp)", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "indonesiafreefire_whatsapp_orders" => ["Free Fire Indonesia (WhatsApp)", ["diamond_amount", "player_id", "payment_details", "payment_option"]],
    "efootball_whatsapp_orders" => ["E-Football (WhatsApp)", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "efootballios_whatsapp_orders" => ["E-Football iOS (WhatsApp)", ["coin_amount", "konami_email", "password", "payment_details", "payment_option"]],
    "clash_whatsapp_orders" => ["Clash of Clans (WhatsApp)", ["gem_amount", "supercell_email", "payment_details", "payment_option"]]
];

// Fetch orders for each table and filter based on status
$orders = [];
foreach ($orderTables as $table => $details) {
    $gameName = $details[0];
    $fields = $details[1];
    $query = "SELECT * FROM $table";
    
    // Apply filter if provided
    if ($statusFilter) {
        $query .= " WHERE status = ?";
    }
    $query .= " ORDER BY created_at $orderBy";

    $stmt = $conn->prepare($query);
    if ($statusFilter) {
        $stmt->bind_param("s", $statusFilter);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Group orders by game name
    while ($row = $result->fetch_assoc()) {
        $orderDetails = [
            'game' => $gameName,
            'fields' => [],
         
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
    <title>Manage Orders</title>
    <link rel="stylesheet" href="css/webstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/wh.js"></script>
</head>
<body>

<div class="content-wrapper">
    <div class="container">
        <h1>Manage Orders</h1>

        <form method="GET" action="">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>

            <label for="order">Sort by Created At:</label>
            <select name="order" id="order">
                <option value="DESC" <?php echo $orderBy === 'DESC' ? 'selected' : ''; ?>>Newest</option>
                <option value="ASC" <?php echo $orderBy === 'ASC' ? 'selected' : ''; ?>>Oldest</option>
            </select>

            <button type="submit">Search</button>
        </form>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $gameName => $gameOrders): ?>
                <h2><?php echo htmlspecialchars($gameName); ?></h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>User ID</th>
                            <?php 
                            // Dynamically generate the table columns based on order fields
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
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <?php foreach ($order['fields'] as $field => $value): ?>
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
            <p>No orders found for the selected filters.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>


<?php
include('includes/footer.php');
?>