<?php
// Database connection
include('dbcon.php');
require_once dirname(__DIR__) . '/config/uploads.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $game_type = trim($_POST['game_type'] ?? '');
    $game_name = trim($_POST['gname'] ?? '');

    // Validate inputs
    if ($game_type === '' || $game_name === '' || !isset($_FILES['image'])) {
        die("Game type, game name, and image are required.");
    }

    // Validate and store using the shared secure upload helper.
    $upload = sujan_store_upload(
        $_FILES['image'],
        dirname(__DIR__) . '/uploads',
        sujan_image_allowlist(),
        5 * 1024 * 1024,
        'game_'
    );

    if (!$upload['ok']) {
        die(htmlspecialchars($upload['error'], ENT_QUOTES, 'UTF-8'));
    }

    $filePath = '../uploads/' . basename($upload['path']);

    // Check if the game type already exists in the database
    $stmt = $conn->prepare("SELECT image_path FROM game_images WHERE game_type = ?");
    $stmt->bind_param("s", $game_type);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Game type exists, delete the existing image if it lives in uploads/.
        $stmt->bind_result($existingImagePath);
        $stmt->fetch();

        $realExisting = realpath(__DIR__ . '/' . $existingImagePath);
        $uploadRoot = realpath(dirname(__DIR__) . '/uploads');
        if ($realExisting && $uploadRoot && str_starts_with($realExisting, $uploadRoot) && is_file($realExisting)) {
            unlink($realExisting);
        }

        $stmt->close();
        $stmt = $conn->prepare("UPDATE game_images SET image_path = ?, game_name = ? WHERE game_type = ?");
        $stmt->bind_param("sss", $filePath, $game_name, $game_type);
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO game_images (game_type, game_name, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $game_type, $game_name, $filePath);
    }

    if ($stmt->execute()) {
        header("Location: uploadform.php");
        exit;
    } else {
        error_log('upload_image insert/update failed: ' . $stmt->error);
        echo "Failed to save image data.";
    }

    $stmt->close();
}
$conn->close();
?>
