<?php
// Include necessary files
include('seson.php');
include('dbcon.php');
require_once dirname(__DIR__) . '/config/uploads.php';

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if (isset($_FILES['image']) && isset($_POST['image_name'])) {
    $imageName = trim((string) $_POST['image_name']);
    $imageDescription = trim((string) ($_POST['image_description'] ?? ''));

    $upload = sujan_store_upload(
        $_FILES['image'],
        dirname(__DIR__) . '/uploads',
        sujan_image_allowlist(),
        5 * 1024 * 1024,
        'gallery_'
    );

    if ($upload['ok']) {
        $relative = '../uploads/' . basename($upload['path']);
        $stmt = $conn->prepare("INSERT INTO image_gallery (image_name, image_path, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $imageName, $relative, $imageDescription);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Image uploaded successfully!'];
        } else {
            error_log('upload_media insert failed: ' . $stmt->error);
            $response = ['status' => 'error', 'message' => 'Failed to save image to the database.'];
        }
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => $upload['error']];
    }
}

echo json_encode($response);
