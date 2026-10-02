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
    <title>Upload Video</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/video.css">
</head>
<body>
    <h1>Upload Video</h1>

    <!-- Video Upload Form -->
    <form id="videoUploadForm" enctype="multipart/form-data">
        <label for="videoName">Video Name:</label>
        <input type="text" name="video_name" id="videoName" required>
        
        <label for="video">Select Video:</label>
        <input type="file" name="video" id="video" accept="video/*" required>

        <label for="videoThumbnail">Select Thumbnail Image:</label>
        <input type="file" name="thumbnail" id="videoThumbnail" accept="image/*">

        <label for="videoDescription">Description:</label>
        <textarea name="video_description" id="videoDescription"></textarea>

        <button type="submit">Upload Video</button>
    </form>

    <div id="responseMessage"></div>

    <script>
   $(document).ready(function() {
    let currentVideo = null; // Keep track of the currently playing video
    let currentThumbnail = null; // Keep track of the current thumbnail

    // When a video thumbnail is clicked
    $('.video-thumbnail').on('click', function() {
        var videoId = $(this).data('video-id');
        var videoFile = $(this).data('video-file');
        var thumbnailPath = $(this).find('.thumbnail-image').attr('src');

        // Check if a video is already playing
        if (currentVideo) {
            currentVideo.pause(); // Pause the current video
            currentVideo.currentTime = 0; // Reset the video
            currentThumbnail.find('.thumbnail-image').show(); // Show the previous thumbnail again
            currentThumbnail.find('.controls').show(); // Show play button again
        }

        // Hide the clicked thumbnail
        $(this).find('.thumbnail-image').hide();

        // Show the video player
        $('#videoPlayer').show();
        $('#currentVideo').attr('src', videoFile)[0].play(); // Set the new video source and play it

        // Update the current video and thumbnail
        currentVideo = $('#currentVideo')[0];
        currentThumbnail = $(this);

        // Optionally, you can hide the play button if necessary
        $(this).find('.controls').hide();
    });

    // When the video is paused or finished, revert everything to its original state
    $('#currentVideo').on('pause ended', function() {
        if (currentVideo) {
            currentVideo.pause();
            currentVideo.currentTime = 0;
            $(currentThumbnail).find('.thumbnail-image').show();
            $(currentThumbnail).find('.controls').show();
            $('#videoPlayer').hide();
        }
    });

    // Handling video upload form submission
    $('#videoUploadForm').submit(function(e) {
        e.preventDefault(); // Prevent form submission

        var videoFile = $('#video')[0].files[0];
        var thumbnailFile = $('#videoThumbnail')[0].files[0];

        // Disable the upload button to prevent multiple submissions
        $('button[type="submit"]').prop('disabled', true).text('Uploading...');

        // Validate video file size (limit: 50MB)
        if (videoFile.size > 250 * 1024 * 1024) {
            $('#responseMessage').html('<span style="color: red;">Error: Video size exceeds 250MB.</span>');
            $('#responseMessage').show();
            $('button[type="submit"]').prop('disabled', false).text('Upload Video'); // Re-enable button
            return;
        }

        // Validate file type (only .mp4, .avi, .mov, .mkv, etc.)
        var allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];
        if (!allowedTypes.includes(videoFile.type)) {
            $('#responseMessage').html('<span style="color: red;">Error: Invalid video format. Please upload .mp4, .avi, .mov, or .mkv files only.</span>');
            $('#responseMessage').show();
            $('button[type="submit"]').prop('disabled', false).text('Upload Video'); // Re-enable button
            return;
        }

        // Validate video length (max 5 minutes)
        var video = document.createElement('video');
        video.src = URL.createObjectURL(videoFile);
        video.onloadedmetadata = function() {
            if (video.duration > 1800) {  // 5 minutes = 300 seconds
                $('#responseMessage').html('<span style="color: red;">Error: Video duration exceeds 5 minutes.</span>');
                $('#responseMessage').show();
                $('button[type="submit"]').prop('disabled', false).text('Upload Video'); // Re-enable button
                return;
            }

            // If all validations pass, continue the form submission
            var formData = new FormData($('#videoUploadForm')[0]);
            
            $.ajax({
                url: 'videpro.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        $('#responseMessage').html('<span style="color: green;">' + result.message + '</span>');
                        $('#responseMessage').show();
                        setTimeout(function() {
                            window.location.href = "gal.php"; 
                        }, 2000);
                    } else {
                        $('#responseMessage').html('<span style="color: red;">' + result.message + '</span>');
                        $('#responseMessage').show();
                    }
                },
                error: function() {
                    $('#responseMessage').html('<span style="color: red;">Error uploading video.</span>');
                    $('#responseMessage').show();
                    $('button[type="submit"]').prop('disabled', false).text('Upload Video'); // Re-enable button
                }
            });
        };
    });
});

    </script>
</body>
</html>
