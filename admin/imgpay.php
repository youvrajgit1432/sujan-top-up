<?php
include('seson.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('dbcon.php');

// Check if delete request is made
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Ensure it's an integer for security
    $delete_query = "DELETE FROM payimg WHERE id = ?";
    
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully!'); window.location.href='imgpay.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record!'); window.location.href='imgpay.php';</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Transactions</title>
    <style>
      /* General Table Styling */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    margin-left: 20px;
}

.table th, .table td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 14px;
}

.table th {
    background-color: #f4f4f4;
    color: #333;
    font-weight: bold;
}

.table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table tr:hover {
    background-color: #f1f1f1;
}

.table td img {
    width: 80px;
    height: auto;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.table td img:hover {
    transform: scale(1.1);
}

/* Delete Button */
.delete-btn {
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 5px;
}

.delete-btn:hover {
    background-color: darkred;
}

/* Fullscreen Image Styles */
.fullscreen-img {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.fullscreen-img img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 5px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .table th, .table td {
        font-size: 12px;
        padding: 8px;
    }
}

    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Received Transaction Images</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sn</th>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Game Name</th>
                <th>Game Type</th>
                <th>Transaction Photo</th>
                <th>Uploaded At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT payimg.*, users.name AS user_name, users.phone, users.email FROM payimg LEFT JOIN users ON payimg.user_id = users.id ORDER BY payimg.created_at DESC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                $sn = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $sn++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['preid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_name'] ?? 'Unknown') . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['email'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['game_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['game_type']) . "</td>";

                    $imagePath = htmlspecialchars($row['transaction_photo']);
                    if (!empty($row['transaction_photo']) && file_exists($imagePath)) {
                        echo "<td><img src='$imagePath' alt='Transaction Photo' onclick='openImage(this)'></td>";
                    } else {
                        echo "<td class='text-center text-danger'>No Image</td>";
                    }

                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    
                    echo "<td><a href='?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<!-- Fullscreen Image Container -->
<div class="fullscreen-img" id="fullscreenContainer" ondblclick="closeImage()">
    <img id="fullscreenImg" src="" alt="Fullscreen Image">
</div>

<script>
    // Open the image in fullscreen mode
    function openImage(imgElement) {
        let fullscreenContainer = document.getElementById('fullscreenContainer');
        let fullscreenImg = document.getElementById('fullscreenImg');
        fullscreenImg.src = imgElement.src;
        fullscreenContainer.style.display = 'flex';
    }

    // Close the fullscreen image on double click
    function closeImage() {
        document.getElementById('fullscreenContainer').style.display = 'none';
    }
</script>
</body>
</html>

<?php
include('includes/footer.php');
?>
