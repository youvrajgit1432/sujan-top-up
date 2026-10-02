<?php
// Include necessary files
include('seson.php');
include('dbcon.php');
include('includes/header.php');
include('includes/top.php');
include('includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/imggal.css">
</head>
<body>
    <h1>Upload Image</h1>

    <!-- Image Upload Form -->
    <form id="imageUploadForm" enctype="multipart/form-data">
        <label for="imageName">Image Name:</label>
        <input type="text" name="image_name" id="imageName" required>
        
        <label for="image">Select Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <label for="imageDescription">Description:</label>
        <textarea name="image_description" id="imageDescription"></textarea>

        <button type="submit">Upload Image</button>
    </form>

    <div id="responseMessage"></div>

    <script>
        // Handle file upload validation (size limit)
        $(document).ready(function() {
    $('#imageUploadForm').submit(function(e) {
        e.preventDefault(); // Prevent form submission

        var formData = new FormData(this);

        $.ajax({
            url: 'upload_media.php', 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#responseMessage').html('<span style="color: green;">' + result.message + '</span>');
                    $('#responseMessage').show();
                    
                    // Redirect after a successful upload (optional delay for user experience)
                    setTimeout(function() {
                        window.location.href = "gal.php"; // Redirect to another page (replace with your target URL)
                    }, 2000); // 2-second delay before redirection
                } else {
                    $('#responseMessage').html('<span style="color: red;">' + result.message + '</span>');
                    $('#responseMessage').show();
                }
            },
            error: function() {
                $('#responseMessage').html('<span style="color: red;">Error uploading image.</span>');
                $('#responseMessage').show();
            }
        });
    });
});

    </script>
</body>
</html>
