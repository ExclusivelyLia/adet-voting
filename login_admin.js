// See Password
function togglePasswordVisibility() {
    var passwordField = document.getElementById('password');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
    } else {
        passwordField.type = 'password';
    }
}

// Check if there's an error parameter in the URL and display alert
window.onload = function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error') && urlParams.get('error') === 'invalid_credentials') {
        alert('Incorrect username or password');
    }
}