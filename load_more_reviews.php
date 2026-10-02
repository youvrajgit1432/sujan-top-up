<?php
include("dbcon.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'latest';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$query = "";
switch ($filter) {
    case 'latest':
        $query = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT 10 OFFSET $offset";
        break;
    case 'best':
        $query = "SELECT * FROM feedback ORDER BY rating DESC, created_at DESC LIMIT 10 OFFSET $offset";
        break;
    case 'worst':
        $query = "SELECT * FROM feedback ORDER BY rating ASC, created_at DESC LIMIT 10 OFFSET $offset";
        break;
    case 'oldest':
        $query = "SELECT * FROM feedback ORDER BY created_at ASC LIMIT 10 OFFSET $offset";
        break;
}

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $avatar = !empty($row['avatar']) ? 'uploads/' . $row['avatar'] : 'assets/img/s1.png';
    $rating = $row['rating'];

    // Set the game type
    $gameType = $row['type']; // Assuming 'type' is a column in your feedback table

    // Determine the icon based on the game type
    switch ($gameType) {
        case 'Efootball(Ios)':
            $iconImage = 'assets/img/54.jpeg'; // Path to the game-specific icon
            break;
        case 'Efootball(Android)':
            $iconImage = 'assets/img/92.jpeg'; // Path to the game-specific icon
            break;
        case 'Clash Of Clan':
            $iconImage = 'assets/img/90.jpg'; // Path to the game-specific icon
            break;
        case 'Mobile legend':
            $iconImage = 'assets/img/92.jpeg'; // Path to the game-specific icon
            break;
        case 'Free Fire':
            $iconImage = 'assets/img/93.jpeg'; // Path to the game-specific icon
            break;
        case 'PUBG':
            $iconImage = 'assets/img/94.jpeg'; // Path to the game-specific icon
            break;
        case 'Overall Website and Service':
            $iconImage = 'assets/img/s1.png'; // Path to the game-specific icon
            break;
        default:
            $iconImage = 'assets/img/pubg.jpg'; // Default icon if no match
            break;
    }

    // Check if the image exists before using it
    if (!file_exists($iconImage)) {
        $iconImage = 'assets/img/pubg.jpg'; // Use default if image not found
    }
?>
    <div class="review">
        <div class="review-header">
            <!-- Display the game-specific icon -->
            <img src="<?php echo $iconImage; ?>" alt="<?php echo htmlspecialchars($gameType); ?> Icon" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px;">
            <strong><?php echo htmlspecialchars($row['username']); ?></strong>
        </div>
        <div class="review-rating">
            <?php
            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $rating ? '<span style="color: gold;">⭐</span>' : '<span style="color: lightgray;">⭐</span>';
            }
            ?>
        </div>
        <div class="review-text">
            <p><?php echo htmlspecialchars($row['review']); ?></p>
        </div>
    </div>
    <hr>
<?php } ?>
