<?php
include('dbcon.php');

if (isset($_POST['id']) && isset($_POST['category'])) {
    $id = $_POST['id'];
    $category = $_POST['category'];

    if ($category === 'image') {
        $query = "DELETE FROM image_gallery WHERE id = ?";
    } else if ($category === 'video') {
        $query = "DELETE FROM videos WHERE id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting item.']);
    }
    $stmt->close();
}

mysqli_close($conn);
?>
