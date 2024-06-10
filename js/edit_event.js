document.addEventListener('DOMContentLoaded', function() {
    updateCategoryDescription();

    let sessionIndex = eventEdit.sessions.length;
    const sessionsList = document.getElementById('sessions-list');

    document.getElementById('add-session-edit-event-btn').addEventListener('click', function() {
        const lastSessionDiv = sessionsList.lastElementChild;
        if (lastSessionDiv && lastSessionDiv.classList.contains('session')) {
            const lastDateInput = lastSessionDiv.querySelector('.session-date-picker');
            const lastStartTimeInput = lastSessionDiv.querySelector('input[name$="[start_time]"]');
            const lastEndTimeInput = lastSessionDiv.querySelector('input[name$="[end_time]"]');

            if (!lastDateInput.value || !lastStartTimeInput.value || !lastEndTimeInput.value) {
                alert('Please fill in the date and time for the current session before adding a new one.');
                return;
            }
            else {
                sessionIndex++;
                addSession();
            }
        }
    });

    function addSession() {
        const sessionDiv = document.createElement('div');
        sessionDiv.className = 'session';
        sessionDiv.innerHTML = `
            <strong>Session ${sessionIndex}:</strong>
            <label>Date:</label>
            <input type="text" class="session-date-picker" name="sessions[new][${sessionIndex}][date]" required>
            <label>Start Time:</label>
            <input type="text" class="time-picker" name="sessions[new][${sessionIndex}][start_time]" required>
            <label>End Time:</label>
            <input type="text" class="time-picker" name="sessions[new][${sessionIndex}][end_time]" required>
            <button type="button" class="btn btn-warning cancel-session-btn">Cancel</button>
        `;
        sessionsList.appendChild(sessionDiv);

        initializeFlatpickr(sessionDiv);

        sessionDiv.querySelector('.cancel-session-btn').addEventListener('click', function() {
            sessionDiv.remove();
            sessionIndex--;
        });

    }

    function initializeFlatpickr(container) {
        flatpickr(container.querySelector('.session-date-picker'), {
            enableTime: false,
            dateFormat: "Y-m-d",
        });

        flatpickr(container.querySelectorAll('.time-picker'), {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    }

    document.querySelectorAll('.session').forEach(sessionDiv => initializeFlatpickr(sessionDiv));

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
        let isValid = true;

        let existingSessions = [];

        sessions.forEach(function(session, index) {
            const date = session.querySelector('.session-date-picker').value;
            const startTime = session.querySelector('input[name$="[start_time]"]').value;
            const endTime = session.querySelector('input[name$="[end_time]"]').value;

            if (!date || !startTime || !endTime) {
                alert('Please fill in all date and time fields for each session.');
                isValid = false;
            }

            if (new Date(`${date}T${startTime}`) >= new Date(`${date}T${endTime}`)) {
                alert('The end time must be later than the start time for each session.');
                isValid = false;
            }

            const sessionData = { date: date, startTime: startTime, endTime: endTime };
            if (sessionTimeConflict(sessionData, existingSessions)) {
                alert('Session times cannot overlap. Please adjust the session times.');
                isValid = false;
                return;
            }

            existingSessions.push(sessionData);
        });

        if (!isValid) {
            event.preventDefault();
        }

        validateEventImages(event);
    });

    function validateEventImages(event) {
        const imageInputs = document.getElementById('event_images');
        const existingImages = document.querySelectorAll('.image-container input[type="checkbox"]:not(:checked)');
        const newImagesCount = imageInputs.files.length;
        const totalRemainingImages = existingImages.length + newImagesCount;

        if (totalRemainingImages < 1 ) {
            alert('Please ensure that at least one image is available for the event.');
            event.preventDefault();
        }
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
            console.error('Geocoding failed: no results found');
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
    document.getElementById('category').addEventListener('change', updateCategoryDescription);

});

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
