<?php
session_start();
// If user not logged in, redirect to login_form page
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Hermes</title>
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/mobile.css">
    <link rel="icon" type="image/png" href="images/favicon.jpg">
</head>
<body>
    <!-- Header section -->
    <header><img src="images/favicon.jpg" alt="Hermes Logo" id="headerIconLeft">
    Project Hermes<span id="headerSubtitle">-Integrated Data Synchronization</span>
    <img src="images/faviconMirrored.jpg" alt="Hermes Logo" id="headerIconRight">

        <!-- Theme Toggle -->
        <div class="theme-toggle-container">
            <span class="theme-toggle-label">☀️</span>
            <label class="theme-toggle">
                <input type="checkbox" id="themeToggle" onchange="toggleTheme()">
                <span class="slider">
                    <span class="slider-icon sun-icon"></span>
                    <span class="slider-icon moon-icon"></span>
                </span>
            </label>
            <span class="theme-toggle-label">🌙</span>
        </div>
    </header>
    <div id="mainContent">
        <!-- Text area section -->
        <section id="textSection">
            <textarea id="mainTextArea" rows="20" cols="50" placeholder="Enter notes here..."></textarea>
        </section>
        <!-- Buttons section below textarea -->
        <section id="buttonSection">
            <button id="btnSave">Save</button>
            <button id="btnClear">Clear</button>
        </section>
    </div>
    <!-- Logout & navigate to search page button -->
    <section id="lowerButtonSection">
        <button id="btnLogout">Logout</button>
        <a href="search_page.php">
            <button id="btnSearch">Search</button>
        </a>
    </section>
    <footer>
        <p>2025 Project Hermes - Built using XAMPP + PHP + MySQL - Jordan Waite</p>
    </footer>
    <script>
    // safe JSON encoding prevents XSS
    const CURRENT_USERNAME = <?php echo json_encode($_SESSION['username'] ?? ''); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>