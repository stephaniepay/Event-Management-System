document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-organizer');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to delete this organizer?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
