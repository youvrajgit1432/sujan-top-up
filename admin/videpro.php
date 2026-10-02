<?php
// Include necessary files
include('seson.php');
include('dbcon.php');
require_once dirname(__DIR__) . '/config/uploads.php';

if (!isset($_FILES['video']) || !isset($_FILES['thumbnail'])) {
    echo json_encode(['status' => 'error', 'message' => 'No files uploaded.']);
    exit;
}

$videoUpload = sujan_store_upload(
    $_FILES['video'],
    dirname(__DIR__) . '/uploads',
    sujan_video_allowlist(),
    250 * 1024 * 1024,
    'video_'
);

if (!$videoUpload['ok']) {
    echo json_encode(['status' => 'error', 'message' => $videoUpload['error']]);
    exit;
}

$thumbnailUpload = sujan_store_upload(
    $_FILES['thumbnail'],
    dirname(__DIR__) . '/uploads',
    sujan_image_allowlist(),
    5 * 1024 * 1024,
    'thumb_'
);

if (!$thumbnailUpload['ok']) {
    echo json_encode(['status' => 'error', 'message' => 'Thumbnail: ' . $thumbnailUpload['error']]);
    exit;
}

$videoPath = '../uploads/' . basename($videoUpload['path']);
$thumbnailPath = '../uploads/' . basename($thumbnailUpload['path']);
$videoName = basename($videoUpload['path']);
$videoDescription = trim((string) ($_POST['video_description'] ?? ''));

$stmt = $conn->prepare("INSERT INTO videos (name, description, file_path, thumbnail_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $videoName, $videoDescription, $videoPath, $thumbnailPath);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Video uploaded successfully!']);
} else {
    error_log('videpro insert failed: ' . $stmt->error);
    echo json_encode(['status' => 'error', 'message' => 'Database error while saving the video.']);
}
$stmt->close();
