<?php
session_start();
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
    <!-- Header -->
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
    <!-- Search controls -->
    <h1 style="color:white;margin:20px auto;">Search Previous Notes</h1>
    <div id="searchControlsWrapper" style="margin:5px;display:flex;align-items:center;flex-direction:column;flex:1;">
        <div id="searchControls">
            <input id="searchInput" type="search" placeholder="Enter keyword(s)" style="width:70%;" />
            <button id="btnExecuteSearch">Search</button>
        </div>
        <div id="searchMsg" style="color:crimson;margin-top:8px;"></div>
    </div>
    <!-- Results section -->
    <section id="results">
        <div id="resultsMeta"></div>
        <ul id="resultsList" style="list-style:none;padding:5px;"></ul>
    </section>
    <!-- Lower buttons & footer -->
    <section id="lowerButtonSection">
        <button id="btnLogout">Logout</button>
        <a href="index.php">
            <button id="btnHome">Home</button>
        </a>
    </section>
    <footer>
        <p>2025 Project Hermes - Built using XAMPP + PHP + MySQL - Jordan Waite</p>
    </footer>
    <script src="searchScript.js"></script>
</body>
</html>