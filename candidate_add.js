// Function to confirm submission
function confirmCreate() {
    if (confirm("Are you sure you want to create this candidate?")) {
        document.getElementById("createCandidateForm").submit();
    }
}

// Handle image upload preview
const uploadInput = document.getElementById('file-upload');
const uploadedImage = document.getElementById('uploaded-image');
const removePhotoButton = document.querySelector('.Remove-Photo');

uploadInput.addEventListener('change', function() {
    const file = this.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Handle removing uploaded photo
    removePhotoButton.addEventListener('click', function() {
    uploadInput.value = '';
    uploadedImage.src = 'placeholder.png';
});