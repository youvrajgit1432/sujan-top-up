<?php
  include('seson.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// Database connection
include('dbcon.php');

// Function to get the count of orders for different statuses
function getOrderCount($conn, $table, $status = '', $dateRange = '') {
    // Initialize conditions for status and date range
    $statusCondition = $status ? "AND o.status = ?" : "";
    $dateCondition = $dateRange ? "AND DATE(o.created_at) = ?" : ""; // Date comparison only
    
    // Prepare the query with conditions
    $query = "SELECT COUNT(*) AS total_orders FROM $table o WHERE 1=1 $statusCondition $dateCondition";
    $stmt = $conn->prepare($query);

    // Check if prepare() was successful
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters based on the conditions
    if ($status && $dateRange) {
        $stmt->bind_param("ss", $status, $dateRange);
    } elseif ($status) {
        $stmt->bind_param("s", $status);
    } elseif ($dateRange) {
        $stmt->bind_param("s", $dateRange); // Bind only the date
    }

    // Execute the statement and check if successful
    if (!$stmt->execute()) {
        die('Execute error: ' . $stmt->error);
    }

    // Get the result and return the total orders
    $result = $stmt->get_result();
    $stmt->close(); // Always close the statement after use
    return $result->fetch_assoc()['total_orders'];
}

// Function to get the count of registered admin and users
function getUserCount($conn, $table) {
    $query = "SELECT COUNT(*) AS total_users FROM $table";
    $stmt = $conn->prepare($query);

    // Check if prepare() was successful
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Execute the statement and check if successful
    if (!$stmt->execute()) {
        die('Execute error: ' . $stmt->error);
    }

    // Get the result and return the total users
    $result = $stmt->get_result();
    $stmt->close(); // Always close the statement after use
    return $result->fetch_assoc()['total_users'];
}

// Fetch total orders across website and WhatsApp orders
$websiteTables = [
    "efootball_website_orders", "efootballios_website_orders", "clash_website_orders", 
    "pubglobal_website_orders", "netflix_website_orders",  "unpin_website_orders","pubg_website_orders", "indonesiamobilelegends_website_orders", 
    "freefire_website_orders", "indonesiafreefire_website_orders", "mlbb_website_orders", 
    "mobilelegends_website_orders", "tiktok_website_orders"
];

$whatsAppTables = [
    "pubglobal_whatsapp_orders", "pubg_whatsapp_orders","netflix_whatsapp_orders","unpin_whatsapp_orders", "indonesiamobilelegends_whatsapp_orders",
    "mobilelegends_whatsapp_orders", "mlbb_whatsapp_orders", "freefire_whatsapp_orders", 
    "indonesiafreefire_whatsapp_orders", "efootball_whatsapp_orders", "efootballios_whatsapp_orders", 
    "clash_whatsapp_orders"
];

// Initialize counts for Website and WhatsApp orders
$totalWebsiteOrders = $totalWhatsAppOrders = $completedWebsiteOrders = $completedWhatsAppOrders = $pendingWebsiteOrders = $pendingWhatsAppOrders = $confirmedWebsiteOrders = $confirmedWhatsAppOrders = 0;
$totalTodayWebsiteOrders = $totalTodayWhatsAppOrders = 0;

// Get today's date for comparison
$todayDate = date('Y-m-d');

// Loop through tables to get counts
foreach ($websiteTables as $table) {
    $totalWebsiteOrders += getOrderCount($conn, $table);
    $completedWebsiteOrders += getOrderCount($conn, $table, 'completed');
    $pendingWebsiteOrders += getOrderCount($conn, $table, 'pending');
    $confirmedWebsiteOrders += getOrderCount($conn, $table, 'confirmed');
    // Today's Orders (Date comparison only)
    $totalTodayWebsiteOrders += getOrderCount($conn, $table, '', $todayDate);
}

foreach ($whatsAppTables as $table) {
    $totalWhatsAppOrders += getOrderCount($conn, $table);
    $completedWhatsAppOrders += getOrderCount($conn, $table, 'completed');
    $pendingWhatsAppOrders += getOrderCount($conn, $table, 'pending');
    $confirmedWhatsAppOrders += getOrderCount($conn, $table, 'confirmed');
    // Today's Orders (Date comparison only)
    $totalTodayWhatsAppOrders += getOrderCount($conn, $table, '', $todayDate);
}

// Fetch total number of users and admins
$totalAdmins = getUserCount($conn, 'admin_users');
$totalUsers = getUserCount($conn, 'users');

// Handle Date Range Filter
$dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title for SEO -->
    <title>Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal</title>

    <!-- Meta Description for SEO -->
    <meta name="description"
        content="Best Top-Up Service Provider in Banepa and all over Nepal. Sujan Top-Up offers fast and affordable gaming top-up services. Buy gaming coins, Free Fire diamonds, PUBG UC, TikTok coins, and more instantly. Trusted and reliable service across cities like Kathmandu, Pokhara, Lalitpur, Bhaktapur, Biratnagar, and more.">

    <!-- Meta Keywords for SEO -->
    <meta name="keywords"
        content="best top-up service in Nepal, top-up service Banepa, gaming top-up Nepal, Free Fire diamonds Nepal, PUBG UC Nepal, TikTok coins Nepal, online game recharge Nepal, affordable top-up services, game currency Nepal, gaming recharge Kathmandu, cheap gaming coins Nepal">

    <!-- Favicons -->
    <link href="../assets/img/logo11.png" rel="icon" sizes="32x32">
    <link href="../assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Link to manifest -->
    <link rel="manifest" href="../manifest.json">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal">
    <meta property="og:description"
        content="Fast and affordable gaming top-up services for Free Fire, PUBG, TikTok, and more in Banepa and all over Nepal. Buy coins, diamonds, and gems instantly!">
    <meta property="og:image" content="../assets/img/logo11.png">
    <meta property="og:url" content="<?php echo e(APP_URL); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sujan Top-Up | Best Top-Up Service Provider in Banepa and All Over Nepal">
    <meta name="twitter:description"
        content="Sujan Top-Up offers the best gaming recharge services in Banepa and across Nepal. Buy gaming coins, Free Fire diamonds, PUBG UC, and more at affordable prices.">
    <meta name="twitter:image" content="../assets/img/logo11.png">

    <!-- Additional Locations for Local SEO -->
    <meta name="description"
        content="Available in cities: Kathmandu, Pokhara, Lalitpur, Bhaktapur, Biratnagar, Birgunj, Janakpur, Butwal, Dharan, Nepalgunj, Hetauda, Dhulikhel, Tansen, Ilam, Bharatpur, Gorkha, Lumbini, Jumla, Sindhuli, Bhadrapur, Dhangadhi, Itahari, Kakadvitta, Banepa, Panauti, Chitwan, Tulsipur, Kalaiya, Siddharthanagar, Rajbiraj, Damak, Kirtipur, Patan, Tikapur, Mahendranagar, Baglung, Manang, Phidim, Diktel, Ramechhap, Beni, Dolakha, Solukhumbu, Taplejung, Bardibas, Arghakhanchi, Charikot, Nuwakot, Gulmi, Bhimdatta, Sandhikharka, Waling, Parasi, Lekhnath, Thimi, Kawasoti, Amlekhgunj, Damauli, Kusma, Sankhuwasabha, Salyan, Rolpa, Rukum, Pyuthan, Lamjung, Bhojpur, Okhaldhunga, Siraha, Gaighat, Darchula, Rasuwa, Bajhang, Dadeldhura, Bajura, Achham, Kalikot, Dolpa, Jajarkot, Humla, Bardiya, Baitadi, Sindhupalchok, Tanahun, Palpa, Syangja, Parbat, Makwanpur, Udayapur, Mahottari, Saptari, Kapilvastu, Nawalparasi, Rautahat, Bara, Chautara, Gaur.">
  <!-- Fonts -->
 <link rel="manifest" href="../manifest.json">
  <!-- Vendor CSS Files -->
 


    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                 

                <div class="row">
                <div class="col-xl-6 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                Today's Orders
            </div>
            <div class="h6 mb-0 font-weight-bold text-gray-800">
                Total Today's Website Orders: <?php echo $totalTodayWebsiteOrders; ?>
            </div>
            <div class="h6 mb-0 font-weight-bold text-gray-800">
                Total Today's WhatsApp Orders: <?php echo $totalTodayWhatsAppOrders; ?>
            </div>
            <!-- Adding buttons for navigating to Website and WhatsApp orders -->
            <div class="mt-3">
                <a href="webtoday.php" class="btn btn-primary btn-sm">  Website Orders</a>
                <a href="whattoday.php" class="btn btn-success btn-sm">  WhatsApp Orders</a>
            </div>
        </div>
    </div>
</div>

                    <!-- User Stats -->
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Registered Users
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Total Admins: <?php echo $totalAdmins; ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Total Users: <?php echo $totalUsers; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Orders -->
                 

                    <!-- Website Orders -->
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Website Orders
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Total Website Orders: <?php echo $totalWebsiteOrders; ?>
                                </div>
                                <div class="nested-cards">
                                    <div>Completed Orders: <?php echo $completedWebsiteOrders; ?></div>
                                    <div>Pending Orders: <?php echo $pendingWebsiteOrders; ?></div>
                                    <div>Confirmed Orders: <?php echo $confirmedWebsiteOrders; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Orders -->
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    WhatsApp Orders
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Total WhatsApp Orders: <?php echo $totalWhatsAppOrders; ?>
                                </div>
                                <div class="nested-cards">
                                    <div>Completed Orders: <?php echo $completedWhatsAppOrders; ?></div>
                                    <div>Pending Orders: <?php echo $pendingWhatsAppOrders; ?></div>
                                    <div>Confirmed Orders: <?php echo $confirmedWhatsAppOrders; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
</body>
</html>
