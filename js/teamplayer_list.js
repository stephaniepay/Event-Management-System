function filterPlayers() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.querySelector('.input-group input');
    filter = input.value.toUpperCase();
    table = document.getElementById("team-players-table");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function applySorting() {
    var table = document.getElementById('team-players-table');
    if (!table) {
        return;
    }
    var rows = Array.from(table.tBodies[0].rows);
    var sortOrder = document.getElementById('sort-order').value;

    if (!rows[0].hasAttribute('data-original-order')) {
        rows.forEach((row, index) => row.setAttribute('data-original-order', index));
    }

    rows.sort(function(a, b) {
        switch (sortOrder) {
            case 'name-asc':
                return a.cells[0].innerText.localeCompare(b.cells[0].innerText);
            case 'name-desc':
                return b.cells[0].innerText.localeCompare(a.cells[0].innerText);
            case 'votes-asc':
                return parseInt(a.cells[5].getAttribute('data-votes')) - parseInt(b.cells[5].getAttribute('data-votes'));
            case 'votes-desc':
                return parseInt(b.cells[5].getAttribute('data-votes')) - parseInt(a.cells[5].getAttribute('data-votes'));
            case 'default':
                return a.getAttribute('data-original-order') - b.getAttribute('data-original-order');
        }
    });

    rows.forEach(function(row) {
        table.tBodies[0].appendChild(row);
    });
}
