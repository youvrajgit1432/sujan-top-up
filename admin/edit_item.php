<?php
include('dbcon.php');

if (isset($_POST['id']) && isset($_POST['category']) && isset($_POST['title']) && isset($_POST['description'])) {
    $id = $_POST['id'];
    $category = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    if ($category === 'image') {
        $query = "UPDATE image_gallery SET image_name = ?, description = ? WHERE id = ?";
    } else if ($category === 'video') {
        $query = "UPDATE videos SET name = ?, description = ? WHERE id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $description, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating item.']);
    }
    $stmt->close();
}

mysqli_close($conn);
?>
