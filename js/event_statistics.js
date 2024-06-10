function filterEvents() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.querySelector('.input-group input');
    filter = input.value.toUpperCase();
    table = document.getElementById("events-statistics-table");
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
