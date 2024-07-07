window.onload = function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error') && urlParams.get('error') === 'invalid_credentials') {
        alert('Incorrect student ID or reference ID');
    }
}