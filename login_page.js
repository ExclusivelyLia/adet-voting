// Show loading screen
function showLoadingScreen(destination) {
    document.getElementById('loadingOverlay').style.display = 'flex';

    // Loading delay
    setTimeout(function() {
        window.location.href = destination;
    }, 2000);
}