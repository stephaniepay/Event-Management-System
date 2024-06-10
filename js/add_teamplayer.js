
function applyAddPlayerSorting() {
    var list = document.getElementById('team-players-list');
    if (!list) {
        console.error('The team players list was not found in the DOM.');
        return;
    }

    var order = document.getElementById('sort-order').value;
    sortTeamPlayers(list, order);
}

function sortTeamPlayers(list, order) {
    var players = Array.from(list.children);
    players.sort(function(a, b) {
        var nameA = a.querySelector('span').textContent.toUpperCase();
        var nameB = b.querySelector('span').textContent.toUpperCase();
        return order === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
    });
    players.forEach(player => list.appendChild(player));
}
