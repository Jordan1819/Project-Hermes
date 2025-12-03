// ----------------Global variables-------------------------------

let btnClear = document.getElementById('btnClear');
let btnSave = document.getElementById('btnSave');
let btnLogout = document.getElementById('btnLogout');
let textArea = document.getElementById('mainTextArea');
const WEBAPP_URL = 'https://script.google.com/macros/s/AKfycbxMdvf6JFYvnHrQtYwYBE0CG7XKVdCb9KEIyEyjOK5vL969QpEK-Cjz5oK-rtYjAu8orQ/exec';


// ----------- Button Click Event Listeners ---------------------

// Clear button: Clear the text area input
btnClear.addEventListener('click', function() {
    textArea.value = '';
});

// Save button: Save input text to Google Docs and backend DB
btnSave.addEventListener('click', async function() {
    const text = textArea.value.trim();
    if (!text) return alert('Text area is empty!');

    // Save to db
    try {
        const res = await fetch('server/save_note.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type' : 'application/json' },
            body: JSON.stringify({ note: text })
        });

        const dbPayload = await res.json();
        if (!dbPayload.ok) {
            console.error('DB save error: ', dbPayload);
            alert('DB save failed.' + dbPayload.error);
            return;
        }
    } catch (err) {
        console.error('Error saving note to db');
        alert('Network or server error in saving note: ' + err.message);
    }

    // Check if username is defined - if not, username='anonymous'
    // Should always be defined because of login req
    const username = (typeof CURRENT_USERNAME !== 'undefined' && CURRENT_USERNAME) ? CURRENT_USERNAME : 'Anonymous';

    // Create payload for google doc apps script
    const payload = {
        username: username,
        text: text
    };

    // Send text to google doc web app endpoint - fire & forget
    fetch(WEBAPP_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload) // send username + text
    });
    // Clear text area after saving 
    textArea.value = '';
    alert('Data saved to database and Google Docs.');
});

// Logout button: Log user out and navigate to login screen
btnLogout.addEventListener('click', async () => {
    console.log('Logout button clicked'); //test
    try {
        const res = await fetch('server/logout.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json'}
        });
        const data = await res.json();
        console.log('Server response: ', data);
        if (data.ok) {
            window.location = 'login_form.php';
        } else {
            alert('Logout failed');
        }
    } catch(err) {
        console.error('Logout error:', err);
    }
});

// -----------DOMContentLoaded Event Listener---------------------
//window.addEventListener('DOMContentLoaded', () => {

//});


//------------Cookie Functions and Handling-------------------------
// setCookie(): Set cookie name, value, and expiration
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = `${name}=${value}; ${expires}; path=/`;
}

// getCookie(): Retrieve cookie by name
function getCookie(name) {
    const cookieName = name + "=";
    // Get all cookies as a single string
    const decodedCookies = decodeURIComponent(document.cookie);
    const cookies = decodedCookies.split(';');

    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        // If cookie name found
        if (cookie.indexOf(cookieName) === 0) {
            // Return value associated with key
            return cookie.substring(cookieName.length, cookie.length);
        }
    }
    return null;
}
