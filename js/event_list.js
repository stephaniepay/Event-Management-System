document.addEventListener('DOMContentLoaded', function() {

    jQuery('[data-toggle="tooltip"]').tooltip();
    const deleteButtons = document.querySelectorAll('.delete-event-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to delete this event?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
