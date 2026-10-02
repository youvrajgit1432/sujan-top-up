<?php
// Database connection
include('seson.php');
include('dbcon.php');
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');

// Default order is 'newest'
$order = isset($_GET['order']) ? $_GET['order'] : 'newest';

// Modify queries to order based on the selected filter
$imageQuery = "SELECT id, image_name, image_path, description, created_at FROM image_gallery ORDER BY created_at " . ($order == 'oldest' ? 'ASC' : 'DESC');
$imageResult = $conn->query($imageQuery);

// Fetch all videos with the same ordering logic
$videoQuery = "SELECT id, name, file_path, description, created_at, thumbnail_path FROM videos ORDER BY created_at " . ($order == 'oldest' ? 'ASC' : 'DESC');
$videoResult = $conn->query($videoQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="css/gall.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="main-content">

    <h1>Gallery</h1>

    <div class="filter-options">
        <a href="image_gallery.php"><button id="showImagesBtn">Upload Image</button></a>
        <a href="videoupload.php"><button id="showVideosBtn">Upload Video</button></a>
      
    </div>

    <!-- Image Gallery -->
    <h2>Images</h2>
    <div class="gallery" id="imageGallery">
        <?php if ($imageResult && $imageResult->num_rows > 0): ?>
            <?php while ($row = $imageResult->fetch_assoc()): ?>
                <div class="gallery-item" data-category="image">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['image_name']); ?>" class="gallery-image">
                  
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <button class="edit-image-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['image_name']; ?>" data-description="<?php echo $row['description']; ?>">Edit</button>
                    <button class="delete-image-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No images found.</p>
        <?php endif; ?>
    </div>

    <!-- Video Gallery -->
    <h2>Videos</h2>
    <div class="gallery" id="videoGallery">
        <?php if ($videoResult && $videoResult->num_rows > 0): ?>
            <?php while ($row = $videoResult->fetch_assoc()): ?>
                <div class="gallery-item" data-category="video">
                    <div class="video-thumbnail" data-video-id="<?php echo $row['id']; ?>" 
                    data-video-file="<?php echo htmlspecialchars($row['name']); ?>">
                        <img src="<?php echo htmlspecialchars($row['thumbnail_path']); ?>" alt="Thumbnail for
                         <?php echo htmlspecialchars($row['name']); ?>" class="thumbnail-image">
                        <div class="controls">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    </div>
                    <div class="video-container" id="video-<?php echo $row['id']; ?>" style="display:none;">
                        <video controls>
                            <source src="<?php echo htmlspecialchars($row['file_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                     
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <button class="edit-video-btn" data-id="<?php echo $row['id']; ?>" 
                    
                    data-name="<?php echo $row['name']; ?>" 
                    data-description="<?php echo $row['description']; ?>">Edit</button>
                    <button class="delete-video-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No videos found.</p>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
       <div id="editModal" style="display:none;">
         <div class="modal-content">
            <h2>Edit Details</h2>
            <input type="text" id="editTitle" placeholder="Title">
            <textarea id="editDescription" placeholder="Description"></textarea>
            <button id="saveEditBtn">Save</button>
            <button id="closeModalBtn">Close</button>
          </div>
         </div>
  </div>

    <script>
    $(document).ready(function() {
        var editingItemId = null;
        var editingCategory = null;

        // Open Edit Modal for Image or Video
        $('.edit-image-btn, .edit-video-btn').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            
            editingItemId = id;
            editingCategory = $(this).closest('.gallery-item').data('category');

            $('#editTitle').val(name);
            $('#editDescription').val(description);
            $('#editModal').show();
        });

        // Close the Edit Modal
        $('#closeModalBtn').on('click', function() {
            $('#editModal').hide();
        });

        // Save the edited data
        $('#saveEditBtn').on('click', function() {
            var newTitle = $('#editTitle').val();
            var newDescription = $('#editDescription').val();

            $.ajax({
                url: 'edit_item.php',
                type: 'POST',
                data: {
                    id: editingItemId,
                    category: editingCategory,
                    title: newTitle,
                    description: newDescription
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert(result.message);
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(result.message);
                    }
                },
                error: function() {
                    alert('Error saving the data');
                }
            });
        });

        // Delete an Image
        $('.delete-image-btn').on('click', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: 'delete_item.php',
                    type: 'POST',
                    data: { id: id, category: 'image' },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting the image');
                    }
                });
            }
        });

        // Delete a Video
        $('.delete-video-btn').on('click', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this video?')) {
                $.ajax({
                    url: 'delete_item.php',
                    type: 'POST',
                    data: { id: id, category: 'video' },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting the video');
                    }
                });
            }
        });

        // Filter by order
        $('#filterOrder').on('change', function() {
            var order = $(this).val();
            window.location.href = 'gallery.php?order=' + order;
        });
    });
    
            // Function to handle showing video when thumbnail is clicked
            $(document).ready(function() {
            // Handle thumbnail click to show video
            $('.video-thumbnail').click(function() {
                var videoId = $(this).data('video-id');
                var videoFile = $(this).data('video-file');
                
                // Hide the thumbnail and show the video
                $(this).hide();
                $('#video-' + videoId).show();
            });

            // Optionally, you can add a function to hide the video when clicked again (if needed)
            $('.video-container').click(function() {
                var videoId = $(this).attr('id').split('-')[1];
                $(this).hide();
                $('.video-thumbnail[data-video-id="' + videoId + '"]').show();
            });
        });
    </script>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>

<?php
include('includes/footer.php');
?>
