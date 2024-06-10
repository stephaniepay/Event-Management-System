document.addEventListener('DOMContentLoaded', function() {
    const denyButtons = document.querySelectorAll('.deny-organizer-btn');
    const approveButtons = document.querySelectorAll('.approve-organizer-btn');


    denyButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to deny this organizer request?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    approveButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to approve this organizer request?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

});
