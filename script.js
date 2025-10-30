// Global variables
let btnClear = document.getElementById('btnClear');
let btnSave = document.getElementById('btnSave');
let textArea = document.getElementById('mainTextArea');


// Clear button functionality
btnClear.addEventListener('click', function() {
    textArea.value = '';
});

