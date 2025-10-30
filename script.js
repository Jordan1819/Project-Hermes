// Global variables
let btnClear = document.getElementById('btnClear');
let btnSave = document.getElementById('btnSave');
let textArea = document.getElementById('mainTextArea');
const WEBAPP_URL = 'https://script.google.com/macros/s/AKfycbz4g9RQM-5JpeNXjnsrLwjMfoOuPE6nSA7GyszeRuTC3dtZjhmV2EXr9NNr63Fj8iZTmA/exec';

// Clear button functionality
btnClear.addEventListener('click', function() {
    textArea.value = '';
});

// Save button functionality
btnSave.addEventListener('click', function() {
    const text = textArea.value.trim();
    if (!text) return alert('Text area is empty!');

    fetch(WEBAPP_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text: text })
    });
    textArea.value = '';
});
