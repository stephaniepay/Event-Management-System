document.addEventListener('DOMContentLoaded', function() {
    const confirmCartButton = document.querySelectorAll('.btn-confirm-cart');
    const deleteCartButton = document.querySelectorAll('.remove-cart-btn');


    confirmCartButton.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to proceed to payment?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    deleteCartButton.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm('Are you sure you want to remove these items from your cart?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
