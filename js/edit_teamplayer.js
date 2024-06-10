function deletePlayer() {
    if(confirm('Are you sure you want to delete this player?')) {
        document.getElementById('delete-form').submit();
    }
}
