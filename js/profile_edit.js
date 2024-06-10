function previewImage() {
    var preview = document.getElementById('profilePicturePreview');
    var file = document.getElementById('profilePicture').files[0];
    var reader = new FileReader();
    var removeButton = document.querySelector('.remove-profile-picture');

    reader.onloadend = function () {
        if (file) {
            preview.src = reader.result;
            lastPreviewUrl = reader.result;
            preview.classList.remove('hidden');
            if (removeButton) removeButton.hidden = false;;
        }
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

function removeProfilePicture() {
    var preview = document.getElementById('profilePicturePreview');
    preview.src = defaultProfilePictureUrl;
    preview.classList.add('hidden');
    document.getElementById('profilePicture').value = '';

    document.getElementById('removeProfilePictureFlag').value = '1';
}
