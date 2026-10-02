
<?php
include('seson.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Game Image</title>
    <style>
        /* General Form Styling */
form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Label Styling */
form label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

/* Input Field Styling */
form input[type="text"],
form input[type="file"],
form select,
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    color: #333;
    box-sizing: border-box;
}

/* Button Styling */
form button[type="submit"] {
    display: inline-block;
    padding: 10px 20px;
    font-size: 14px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

form button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
    form {
        padding: 15px;
    }

    form button[type="submit"] {
        width: 100%;
        font-size: 16px;
    }
}

    </style>
</head>
<body>
    <form action="upload_image.php" method="POST" enctype="multipart/form-data">
        <label for="game_type">Game Type:</label>
        <input type="text" id="game_type" name="game_type" required>

        <label for="gname">Game Name:</label>
        <input type="text" id="gname" name="gname" required>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Upload</button>
    </form>
</body>
</html>
