<?php
include('seson.php'); // Admin session guard
include('dbcon.php'); // Include database connection
require_once dirname(__DIR__) . '/config/uploads.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_id = (int) ($_POST['game_id'] ?? 0);
    $game_name = (string) ($_POST['game_name'] ?? '');
    $new_image_path = '';

    if ($game_id <= 0 || $game_name === '') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid game data.']);
        exit;
    }

    // Handle file upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = sujan_store_upload(
            $_FILES['image'],
            dirname(__DIR__) . '/uploads',
            sujan_image_allowlist(),
            5 * 1024 * 1024,
            'game_'
        );

        if (!$upload['ok']) {
            echo json_encode(['status' => 'error', 'message' => $upload['error']]);
            exit;
        }

        $new_image_path = '../uploads/' . basename($upload['path']);
    }

    // Update the game information in the database
    if ($new_image_path === '') {
        // If no new image, keep the old one
        $query = "UPDATE game_images SET game_name = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $game_name, $game_id);
    } else {
        // If new image, update the image path as well
        $query = "UPDATE game_images SET game_name = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $game_name, $new_image_path, $game_id);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Game updated successfully!',
            'new_image_path' => $new_image_path ?: $_POST['current_image'],
            'old_game_name' => $_POST['game_name']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update game.']);
    }

    $stmt->close();
    $conn->close();
}
?>
