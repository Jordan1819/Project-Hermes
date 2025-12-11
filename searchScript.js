// ---------------- Globals ----------------------------
const input = document.getElementById('searchInput');
const searchButton = document.getElementById('btnExecuteSearch');
const searchMessage = document.getElementById('searchMsg');
const resultsMeta = document.getElementById('resultsMeta');
const resultsList = document.getElementById('resultsList');
let btnLogout = document.getElementById('btnLogout');


// executeSearch(): Searches all 'note' fields in db for %input%
async function executeSearch(query) {
    // Clear any potential error messages and empty previous search results
    searchMessage.textContent = '';
    resultsList.innerHTML = '';
    resultsMeta.textContent = 'Searching...';

    // AJAX - asynchronous HTTP request to server
    try {
        const res = await fetch('server/search_notes.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ q: query })
        });
        
        // Parse response as json
        const payload = await res.json();

        // Check http status code (ok if 200-299)
        if (!res.ok) {
            searchMessage.textContent = payload.error || `Server returned ${res.status}`;
            resultsMeta.textContent = '';
            return;
        }
        // Check application level success
        if (!payload.ok) {
            searchMessage.textContent = payload.error || 'Search failed';
            resultsMeta.textContent = '';
            return;
        }
        // Destructure payload - extract total & notes from payload object
        const { total = 0, notes = [] } = payload;
        resultsMeta.textContent = `Found ${total} match${total !== 1 ? 'es' : ''}`;



        // For each note - create list elements w/o updating page
        notes.forEach(n => {
            const li = document.createElement('li');
            li.style.borderBottom = '1px solid white';
            li.style.color = 'white';
            li.style.marginBottom = '5px';
            // Metadata for note - username and date created
            const meta = document.createElement('div');
            meta.textContent = `${n.username} | ${n.created_at}`

            const text = document.createElement('div');
            text.textContent = n.note_text;

            li.appendChild(meta);
            li.appendChild(text);
            resultsList.appendChild(li);
        });
    } catch (err) {
        console.error('Search error: ', err);
        searchMessage.textContent = 'Network error';
        resultsMeta.textContent = '';
    }
}

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

// Search button logic
searchButton.addEventListener('click', () => {
    const q = input.value.trim();
    if (!q) { searchMessage.textContent = 'Please enter keywords to search'; return; }
    executeSearch(q);
});

// Allow enter button to execute search
input.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') searchButton.click();
});

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

// -----------DOMContentLoaded Event Listener---------------------
window.addEventListener('DOMContentLoaded', () => {
    // Initialize theme from cookie
    initializeTheme();
});

//------------Theme Toggle Functions-------------------------

// Initialize theme on page load
function initializeTheme() {
    // Check for saved theme preference in cookie
    const savedTheme = getCookie('theme') || 'light';

    // Apply theme
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Set toggle state based on saved theme
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.checked = savedTheme === 'dark';
    }
}

// Toggle theme function - called by checkbox onchange event
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    // Apply new theme
    document.documentElement.setAttribute('data-theme', newTheme);

    // Save preference to cookie
    setCookie('theme', newTheme, 30);
}