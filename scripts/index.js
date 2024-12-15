function openModal() {
    document.getElementById("addPostModal").style.display = "block";
}
function closeModal() {
    document.getElementById("addPostModal").style.display = "none";
}

// Close the modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById("addPostModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}