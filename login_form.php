<?php
session_start();
// If user logged in, send to main interface
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Project Hermes</title>
    <link rel="icon" type="image/png" href="images/favicon.jpg">
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/login_mobile.css">
</head>
<body>
    <header><img src="images/favicon.jpg" alt="Hermes Logo" id="headerIconLeft">
    Project Hermes<span id = "headerSubtitle"> - Integrated Data Synchronization</span>
    <img src="images/faviconMirrored.jpg" alt="Hermes Logo" id="headerIconRight">
    </header>    
    <main>
        <!-- User authentication/ registration page -->
        <section id="authorizationForm">
            <!-- Section for login -->
            <div id="loginForm">
                <h2>Login</h2>
                <input id="loginUsername" type="username" placeholder="Username" required>
                <input id="loginPassword" type="password" placeholder="Password" required>
                <button id="btnLogin">Login</button>
                <p id="loginMessage" style="color:red"></p>
            </div>

            <!-- Section for registration -->
            <div id="registerForm">
                <h2>Register</h2>
                <input id="registerUsername" type="username" placeholder="Username" required>
                <input id="registerPassword" type="password" placeholder="Password" required>
                <button id="btnRegister">Register</button>
                <p id="registerMessage" style="color:red"></p>
            </div>
        </section>
    </main>
<footer>
    <p>2025 Project Hermes - Built using XAMPP + PHP + MySQL - Jordan Waite</p>
</footer>
<script>
async function postJSON(url, obj) {
    const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(obj)
    });
    const text = await res.text();
    try { return JSON.parse(text); }
    catch (e) { throw new Error('Incorrect username or password') }
}

// Login button functionality
document.getElementById('btnLogin').addEventListener('click', async () => {
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const msg = document.getElementById('loginMessage');
    msg.textContent = '';

    if (!username || !password) { 
        msg.textContent = 'Enter email and password'; 
        return; 
    }
    try {
        const payload = await postJSON('server/login.php', { username, password });
        if (payload.ok) {
            window.location = 'index.php';
        } else {
            msg.textContent = payload.error || 'Login failed';
        }
    } catch (err) {
        msg.textContent = err.message;
    }
    document.getElementById('loginUsername').value = '';
    document.getElementById('loginPassword').value = '';
});

// Register button functionality
document.getElementById('btnRegister').addEventListener('click', async () => {
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;
    const msg = document.getElementById('registerMessage');
    msg.textContent = '';

    if (!username || !password) {
        msg.textContent = 'Enter username and password';
        return;
    }
    try {
        const payload = await postJSON('server/register.php', { username: username, password});
        if (payload.ok) {
            window.location = 'index.php';
        } else {
            msg.textContent = payload.error || 'Registration failed';
        }
    } catch (err) {
        msg.textContent = err.message;
    }
});
</script>
</body>
</html>