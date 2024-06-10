document.addEventListener('DOMContentLoaded', function() {
    var payNowButton = document.getElementById('pay-now-btn');

    if (payNowButton) {
        payNowButton.addEventListener('click', function() {
            const confirmed = confirm('Are you sure you want to proceed with the payment?');
            if (confirmed) {
                document.getElementById('payment-form').submit();
            }
        });
    }
});

