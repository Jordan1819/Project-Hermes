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


        // For each note - create dynamic html list elements
        notes.forEach(n => {
            const li = document.createElement('li');
            li.style.borderBottom = '1px solid black';
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