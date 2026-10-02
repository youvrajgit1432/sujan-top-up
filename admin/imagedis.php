<?php
// game_list.php
include('seson.php'); // Start session
include('dbcon.php');   // Database connection
include('includes/header.php');  // Header section
include('includes/top.php');     // Top section
include('includes/sidebar.php'); // Sidebar section

// Fetch all games
$query = "SELECT id, game_name, image_path FROM game_images";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game List</title>
    <link rel="stylesheet" href="css/image.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Game List</h1>
    <div class="card-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Game Image" class="game-image">
                    <h3><?php echo htmlspecialchars($row['game_name']); ?></h3>
                    <button class="action-btn" onclick="openModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['game_name']); ?>', '<?php echo htmlspecialchars($row['image_path']); ?>')">Edit</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No games found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal Form -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Game</h2>
            <form id="editForm" enctype="multipart/form-data">
                <input type="hidden" name="game_id" id="game_id">
                <div>
                    <label for="game_name">Game Name:</label>
                    <input type="text" id="game_name" name="game_name" required>
                </div>
                <div>
                    <label for="image">Select New Image:</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>
                <div>
                    <img id="current_image" src="" alt="Current Game Image" style="width: 100px; height: auto; margin-top: 10px;">
                </div>
                <button type="submit" class="action-btn">Save Changes</button>
            </form>
            <div id="responseMessage" style="display: none; margin-top: 10px;"></div>
        </div>
    </div>

    <script>
        // Open modal function
        function openModal(gameId, gameName, currentImage) {
            document.getElementById("game_id").value = gameId;
            document.getElementById("game_name").value = gameName;
            document.getElementById("current_image").src = currentImage;
            document.getElementById("editModal").style.display = "block";
        }

        // Close modal function
        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

        // Close modal if clicked outside of the modal content
        window.onclick = function(event) {
            if (event.target == document.getElementById("editModal")) {
                closeModal();
            }
        }

        // AJAX form submission
        $(document).ready(function () {
            $('#editForm').submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'edit_game_process.php', 
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        var result = JSON.parse(response);

                        // Show success or error message
                        if (result.status === 'success') {
                            $('#responseMessage').html('<span style="color: green;">' + result.message + '</span>');
                            $('#responseMessage').show();

                            // Close modal
                            closeModal();

                            // Update the image and game name on the page without reloading
                            var gameId = $('#game_id').val();
                            var newImagePath = result.new_image_path;
                            var newGameName = $('#game_name').val();

                            // Update the image and name on the correct card
                            $('h3:contains("' + result.old_game_name + '")').text(newGameName);
                            $('img[src="' + $('#current_image').attr('src') + '"]').attr('src', newImagePath);
                        } else {
                            $('#responseMessage').html('<span style="color: red;">' + result.message + '</span>');
                            $('#responseMessage').show();
                        }
                    },
                    error: function() {
                        $('#responseMessage').html('<span style="color: red;">Error updating the game.</span>');
                        $('#responseMessage').show();
                    }
                });
            });
        });
    </script>

</body>
</html>

<?php
$conn->close();  // Close the database connection
include('includes/footer.php'); // Footer section
?>
