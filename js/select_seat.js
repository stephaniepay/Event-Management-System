document.addEventListener('DOMContentLoaded', function() {
    let totalAmount = 0;
    const totalPriceDisplay = document.getElementById('total_price');
    const selectedSeatInput = document.getElementById('selected_seat_id');
    let selectedSeats = [];

    if (typeof selectSeat === 'undefined' || !selectSeat.pricePerSeat) {
        return;
    }

    document.querySelectorAll('.seat.available').forEach(function(seat) {
        seat.addEventListener('click', function() {
            this.classList.toggle('selected');
            const seatId = this.getAttribute('data-seat-id');
            const price = selectSeat.pricePerSeat;

            if (this.classList.contains('selected')) {
                totalAmount += price;
                selectedSeats.push(seatId);
            } else {
                totalAmount -= price;
                selectedSeats = selectedSeats.filter(id => id !== seatId);
            }


            if (totalPriceDisplay) {
                totalPriceDisplay.textContent = totalAmount.toFixed(2);
            }

            if (selectedSeatInput) {
                selectedSeatInput.value = JSON.stringify(selectedSeats);
            }
        });
    });


    const seatSelectionForm = document.getElementById('seat-selection-form');

    if (seatSelectionForm) {
        seatSelectionForm.addEventListener('submit', function(event) {
            const confirmedSeats = document.querySelectorAll('.seat.selected');
            confirmedSeats.forEach(seat => {
                seat.classList.remove('selected');
                seat.classList.add('selected-cart');
            });

            if (selectedSeats.length === 0) {
                alert('Please select at least one seat.');
                event.preventDefault();
            } else {
                const seatNumbers = selectedSeats.join(', ');
                const confirmSelection = confirm(`Are you sure you want to add the following seats to your cart: ${seatNumbers}?`);
                if (!confirmSelection) {
                    event.preventDefault();
                    confirmedSeats.forEach(seat => {
                        seat.classList.add('selected');
                        seat.classList.remove('selected-cart');
                    });
                }
            }
        });
    }
});
