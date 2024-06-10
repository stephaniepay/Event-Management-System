document.addEventListener("DOMContentLoaded", function() {
    jQuery('[data-toggle="tooltip"]').tooltip();
});

document.addEventListener("DOMContentLoaded", async function () {
    const latitude = eventDetails.latitude;
    const longitude = eventDetails.longitude;
    const weatherElement = document.getElementById('weather');

    const sessions = eventDetails.sessions;

    for (const session of sessions) {

        const sessionStartTime = new Date(session.start_time);
        const sessionEndTime = new Date(session.end_time);
        const now = new Date();
        let url;

        const daysUntilSession = (sessionStartTime - now) / (1000 * 60 * 60 * 24);

        function getForecastUrl(days) {
            if (days <= 7) {
                return `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&hourly=temperature_2m,precipitation&timezone=Asia/Singapore`;
            } else if (days <= 14) {
                return `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&hourly=temperature_2m,precipitation&timezone=Asia/Singapore&forecast_days=14`;
            } else if (days <= 16) {
                return `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&hourly=temperature_2m,precipitation&timezone=Asia/Singapore&forecast_days=16`;
            } else {
                return null;
            }
        }

        if (sessionStartTime < now) {
            const pastDays = Math.ceil((now - sessionStartTime) / (1000 * 60 * 60 * 24));
            url = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&past_days=${pastDays}&hourly=temperature_2m,precipitation&timezone=Asia/Singapore`;
        } else {
            url = getForecastUrl(daysUntilSession);
        }

        if (!url) {
            displayUnavailableMessage(sessionStartTime);
            continue;
        }

        try {
            const response = await fetch(url);
            const data = await response.json();
            const weatherData = data.hourly;


            const sessionWeatherData = weatherData.time.reduce((acc, time, index) => {
                const forecastTime = new Date(time);
                if (forecastTime >= sessionStartTime && forecastTime <= sessionEndTime) {
                    if (acc.lastTime === null || forecastTime - acc.lastTime >= 1 * 60 * 60 * 1000) {
                        acc.data.push({
                            time: weatherData.time[index],
                            temperature: weatherData.temperature_2m[index],
                            precipitation: weatherData.precipitation[index]
                        });
                        acc.lastTime = forecastTime;
                    }
                }
                return acc;
            }, { data: [], lastTime: null }).data;

            const sessionTimeFormatted = formatDateToDMY(sessionStartTime);

            if (sessionWeatherData.length === 0) {

                displayUnavailableMessage(sessionStartTime);

            } else {

                const sessionWeatherContainer = document.createElement('div');
                sessionWeatherContainer.classList.add('card', 'mb-3');

                const sessionWeatherBody = document.createElement('div');
                sessionWeatherBody.classList.add('card-body');

                const sessionWeatherTitle = document.createElement('h5');
                sessionWeatherTitle.classList.add('card-title', 'mb-3');
                sessionWeatherTitle.textContent = `Weather for Session on ${sessionTimeFormatted}`;

                const weatherCardsContainer = document.createElement('div');
                weatherCardsContainer.classList.add('session-weather-container');

                sessionWeatherBody.appendChild(sessionWeatherTitle);
                sessionWeatherBody.appendChild(weatherCardsContainer);

                sessionWeatherData.forEach(weather => {
                    const weatherTime = new Date(weather.time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const weatherType = determineWeatherIcon(weather.precipitation, weather.time);
                    const iconPath = getWeatherIconPath(weatherType);
                    const weatherCard = document.createElement('div');
                    weatherCard.classList.add('session-weather');
                    weatherCard.innerHTML = `
                        <img src="${iconPath}" alt="${weatherType}" class="weather-icon me-2">
                        <div class="weather-details">
                            <div>${weatherTime}</div>
                            <div>${weather.temperature} °C</div>
                            <div>Precipitation: ${weather.precipitation} mm</div>
                        </div>
                    `;
                    // <div>${weatherType}</div>
                    weatherCardsContainer.appendChild(weatherCard);
                });

                sessionWeatherContainer.appendChild(sessionWeatherBody);
                weatherElement.appendChild(sessionWeatherContainer);
            }


        } catch (error) {
            console.error('Error fetching weather data:', error);
            const errorMessage = document.createElement('div');
            errorMessage.classList.add('no-weather-data');
            errorMessage.textContent = 'Unable to retrieve weather data.';
            weatherElement.appendChild(errorMessage);
        }
    }

    function displayUnavailableMessage(sessionStartTime) {
        const sessionTimeFormatted = formatDateToDMY(sessionStartTime);
        const sessionWeatherContainer = document.createElement('div');
        sessionWeatherContainer.classList.add('card', 'mb-3');
        const sessionWeatherBody = document.createElement('div');
        sessionWeatherBody.classList.add('card-body');
        const sessionWeatherTitle = document.createElement('h5');
        sessionWeatherTitle.classList.add('card-title', 'mb-3');
        sessionWeatherTitle.textContent = `Weather for Session on ${sessionTimeFormatted}`;
        const noDataMessage = document.createElement('div');
        noDataMessage.classList.add('no-weather-data');
        noDataMessage.textContent = 'Weather forecasts are available for up to 16 days in advance. Please check back later for updates.';
        sessionWeatherBody.appendChild(sessionWeatherTitle);
        sessionWeatherBody.appendChild(noDataMessage);
        sessionWeatherContainer.appendChild(sessionWeatherBody);
        weatherElement.appendChild(sessionWeatherContainer);
    }

    function formatDateToDMY(date) {
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
    }

    function determineWeatherIcon(precipitation, time) {
        const hour = new Date(time).getHours();
        const isDaytime = hour > 6 && hour < 20;
        let weatherType = 'Clear';
        if (precipitation > 0.0) {
            if (precipitation <= 0.5) {
                weatherType = isDaytime ? 'Slightly Rain' : 'Rainy Night';
            } else if (precipitation > 0.5 && precipitation <= 4.0) {
                weatherType = 'Moderate Rain';
            } else if (precipitation > 4.0 && precipitation <= 8.0) {
                weatherType = 'Heavy Rain';
            } else if (precipitation > 8.0) {
                weatherType = 'Thunderstorm Rain';
            } else {
                weatherType = 'Default';
            }
        } else {
            if (isDaytime) {
                weatherType = 'Sunny';
            } else {
                weatherType = 'Night';
            }
        }
        return weatherType;
    }

    function getWeatherIconPath(description) {
        const mapping = {
            'Clear': "clear.png",
            'Cloudy': "cloudy.png",
            'Night': "night.png",
            'Partly Cloudy': "partly_cloudy.png",
            'Slightly Rain': "slightly_rain.png",
            'Moderate Rain': "moderate_rain.png",
            'Heavy Rain': "heavy_rain.png",
            'Rainy Night': "rainy_night.png",
            'Sunny Rain': "sunny_rain.png",
            'Sunny': "sunny.png",
            'Thunderstorm Rain': "thunderstorm_rain.png",
            'Default': "default.png"
        };


        return eventDetails.basePath + (mapping[description] || mapping['Default']);
    }

});
