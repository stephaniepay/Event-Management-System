document.addEventListener("DOMContentLoaded", function() {
    jQuery('[data-toggle="tooltip"]').tooltip();
});

function filterSessions() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById('session-search');
    filter = input.value.toUpperCase();
    table = document.getElementById("session-table");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
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
