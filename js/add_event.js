function updateCategoryDescription() {
    const descriptions = {
        'teamSports': 'Eg. Soccer, basketball, baseball, hockey, etc. (Local leagues, tournaments, or friendly matches)',
        'individualSports': 'Eg. Athletics, swimming, gymnastics, silat, taekwondo, karate, judo, etc.',
        'racquetSports': 'Eg. Tennis, badminton, squash, table tennis, etc.',
        'waterSports': 'Eg. Swimming, diving, water polo, rowing, sailing, etc.',
        'outdoorAdventure': 'Eg. Rock climbing, mountain biking, trail running, obstacle races, etc.',
        'fitnessHealth': 'Eg. Yoga events, wellness retreats, etc.'
    };

    const selectedCategory = document.getElementById('category').value;
    document.getElementById('category-description').textContent = descriptions[selectedCategory] || '';
}

document.addEventListener('DOMContentLoaded', function() {

    let sessionIndex = 0;
    let addedSessionCount = 0;
    const sessionsList = document.getElementById('sessions-list');

    document.getElementById('add-session-btn').addEventListener('click', function() {
        const lastSessionDiv = sessionsList.querySelector('.session:last-child');
        if (lastSessionDiv) {
            const lastDateInput = lastSessionDiv.querySelector('input[name^="sessions"][name$="[date]"]');
            const lastStartTimeInput = lastSessionDiv.querySelector('input[name^="sessions"][name$="[start_time]"]');
            const lastEndTimeInput = lastSessionDiv.querySelector('input[name^="sessions"][name$="[end_time]"]');
            if (!lastDateInput.value || !lastStartTimeInput.value || !lastEndTimeInput.value) {
                alert('Please fill in the date and time for the current session before adding a new one.');
                return;
            }
        }

        addSession();
    });

    function addSession() {
        const sessionDiv = document.createElement('div');
        sessionDiv.className = 'session';
        sessionDiv.setAttribute('data-index', sessionIndex);
        sessionDiv.innerHTML = `
            <strong>Session ${addedSessionCount + 1}:</strong>
            <label>Date:</label>
            <input type="text" class="session-date-picker" name="sessions[${sessionIndex}][date]" required>
            <label>Start Time:</label>
            <input type="text" class="start-time-picker" name="sessions[${sessionIndex}][start_time]" required>
            <label>End Time:</label>
            <input type="text" class="end-time-picker" name="sessions[${sessionIndex}][end_time]" required>
            <button type="button" class="btn btn-warning cancel-session-btn">Cancel</button>
        `;
        sessionsList.appendChild(sessionDiv);

        flatpickr(sessionDiv.querySelector('.session-date-picker'), {
            enableTime: false,
            dateFormat: "Y-m-d",
        });

        flatpickr(sessionDiv.querySelectorAll('.start-time-picker, .end-time-picker'), {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });

        sessionDiv.querySelector('.cancel-session-btn').addEventListener('click', function() {
            this.closest('.session').remove();
            addedSessionCount--;
        });


        sessionIndex++;
        addedSessionCount++;
    }

    async function fetchCoordinates() {
        const addressLine = document.getElementById('address_line').value;
        const city = document.getElementById('city').value;
        const state = document.getElementById('state').value;
        const zipCode = document.getElementById('zip_code').value;
        const fullAddress = `${addressLine}, ${city}, ${state}, ${zipCode}`;

        let coordinates = await attemptGeocode(`${addressLine}, ${city}, ${state}, ${zipCode}`);
        if (!coordinates) {
            coordinates = await attemptGeocode(`${city}, ${state}, ${zipCode}`);
        }
        if (!coordinates) {
            coordinates = await attemptGeocode(`${state}, ${zipCode}`);
        }

        if (coordinates) {
            document.getElementById('latitude').value = coordinates.lat;
            document.getElementById('longitude').value = coordinates.lon;
        } else {
            return;
        }
    }

    async function attemptGeocode(address) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
            const data = await response.json();
            return data.length > 0 ? data[0] : null;
        } catch (error) {
            console.error('Error fetching coordinates:', error);
            return null;
        }
    }

    document.getElementById('address_line').addEventListener('change', fetchCoordinates);
    document.getElementById('city').addEventListener('change', fetchCoordinates);
    document.getElementById('state').addEventListener('change', fetchCoordinates);
    document.getElementById('zip_code').addEventListener('change', fetchCoordinates);

    function sessionTimeConflict(newSession, existingSessions) {
        const newStart = new Date(newSession.date + 'T' + newSession.startTime);
        const newEnd = new Date(newSession.date + 'T' + newSession.endTime);

        return existingSessions.some(session => {
            const existingStart = new Date(session.date + 'T' + session.startTime);
            const existingEnd = new Date(session.date + 'T' + session.endTime);
            return newStart < existingEnd && newEnd > existingStart;
        });
    }

    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const sessions = document.querySelectorAll('.session');

        let existingSessions = [];

        if (sessions.length === 0) {
            alert('You must add at least one session.');
            event.preventDefault();
            return;
        }
        for (let i = 0; i < sessions.length; i++) {
            const sessionDate = sessions[i].querySelector('.session-date-picker').value;
            const startTime = sessions[i].querySelector('.start-time-picker').value;
            const endTime = sessions[i].querySelector('.end-time-picker').value;

            if (!sessionDate || !startTime || !endTime) {
                alert('All date and time fields must be filled out before submitting the event.');
                event.preventDefault();
                return;
            }

            const sessionData = { date: sessionDate, startTime, endTime };
            if (sessionTimeConflict(sessionData, existingSessions)) {
                alert('Session times cannot overlap. Please adjust the session times.');
                event.preventDefault();
                return;
            }

            existingSessions.push(sessionData);

            const startDateTime = new Date(sessionDate + 'T' + startTime);
            const endDateTime = new Date(sessionDate + 'T' + endTime);

            if (endDateTime <= startDateTime) {
                alert('End time must be later than the start time for each session.');
                event.preventDefault();
                return;
            }
        }

        // Max capacity
        const maxCapacityInput = document.getElementById('max_capacity_per_session');
        const maxCapacity = parseInt(maxCapacityInput.value, 10);
        if (maxCapacity < 1 || maxCapacity > 1000) {
            alert('Max Capacity Per Session must be between 1 and 1000.');
            event.preventDefault();
            maxCapacityInput.focus();
            return false;
        }

        // Price per seat
        const pricePerSeatInput = document.getElementById('price_per_seat');
        const pricePerSeat = parseFloat(pricePerSeatInput.value);
        if (pricePerSeat < 10 || pricePerSeat > 500) {
            alert('Price Per Seat must be between RM10 and RM500.');
            event.preventDefault();
            pricePerSeatInput.focus();
            return false;
        }
    });
});
