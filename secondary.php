<?php
/**
 * Sujan Top-Up - game selection router.
 *
 * The homepage "Top-up Now" buttons store the chosen game in localStorage and
 * land here; this shim forwards the visitor to the matching top-up page. Kept
 * as a separate page so existing links/bookmarks keep working.
 */
require_once __DIR__ . '/config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link href="assets/img/logo11.png" rel="icon" sizes="32x32">
    <link href="assets/img/logo11.png" rel="apple-touch-icon" sizes="180x180">

    <!-- Link to Manifest -->
    <link rel="manifest" href="manifest.json">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Sujan Top-Up | Best Topup Service Provider In Banepa And All Over Nepal, Buy Gaming Coins, Gems, Diamonds & UC for All Games">
    <meta property="og:description" content="Buy coins, gems, diamonds, and UC for popular games like Free Fire, PUBG, and TikTok. Fast and affordable top-up services.">
    <meta property="og:image" content="assets/img/logo11.png">
    <meta property="og:url" content="<?php echo e(APP_URL); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sujan Top-Up | Buy Gaming Coins, Gems, Diamonds & UC for All Games">
    <meta name="twitter:description" content="Affordable top-up services for Free Fire, PUBG, and more. Get gaming coins, gems, and diamonds instantly!">
    <meta name="twitter:image" content="assets/img/logo11.png">

    <!-- CSS and Font Links -->
    <link href="assets/css/sec.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <title>Sujan Top-Up</title>
</head>
<body>
    <!-- Modal for Form -->
    <div id="form-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div id="form-content">
                <!-- Game-specific form will be dynamically injected here -->
            </div>
        </div>
    </div>

    <script>
        // Get the selected game type from localStorage
        const selectedGame = localStorage.getItem("selectedGame");

        // Function to redirect to the corresponding form based on the selected game
        function showGameForm(game) {
            switch (game) {
                case "pubg":
                    window.location.href = "ptptop.php";
                    break;
                case "pubg-global":
                    window.location.href = "ptpglobal.php";
                    break;
                case "free-fire-indonesia":
                    window.location.href = "ftpind.php";
                    break;
                case "tiktok":
                    window.location.href = "tittop.php";
                    break;
                case "freefire":
                    window.location.href = "ftptop.php";
                    break;
                case "efootball-pes-2025":
                    window.location.href = "eftpiostop.php";
                    break;
                case "efootball":
                    window.location.href = "eftptopandroid.php";
                    break;
                case "clash":
                    window.location.href = "clatop.php";
                    break;
                case "mobile-legends-indonesia":
                    window.location.href = "mobileindo.php";
                    break;
                case "mobilelegends":
                    window.location.href = "topmobilegen.php";
                    break;
                case "weekly-diamond-pass-mlbb":
                    window.location.href = "mlbobtop.php";
                    break;
                case "netflix":
                    window.location.href = "nettop.php";
                    break;
                case "prime":
                    window.location.href = "index.php";
                    break;
                case "unpin":
                    window.location.href = "untopppin.php";
                    break;
                case "spotify":
                    window.location.href = "index.php";
                    break;

                default:
                    console.error("Invalid game selection");
            }
        }

        // Redirect immediately for the selected game.
        if (selectedGame) {
            showGameForm(selectedGame);
        } else {
            // Nothing selected (e.g. opened directly) - fall back to the homepage.
            window.location.href = "index.php";
        }

        // Function to close the modal
        function closeModal() {
            const modal = document.getElementById("form-modal");
            modal.style.display = "none";
        }

        // Ensure the modal closes if clicked outside of content
        window.onclick = function(event) {
            const modal = document.getElementById("form-modal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
