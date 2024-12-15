function toggleComments(id) {
    const commentsDiv = document.getElementById(id);

    if (commentsDiv.classList.contains('active')) {
        commentsDiv.style.maxHeight = null; // Collapse
        commentsDiv.classList.remove('active');
    } else {
        commentsDiv.style.maxHeight = commentsDiv.scrollHeight + "px"; // Expand
        commentsDiv.classList.add('active');
    }
}
function updatePinStatus(postID, status) {
    const formData = new FormData();
    formData.append('postID', postID);
    formData.append('status', status);

    fetch(' queries/components/adminPanel/postPinned.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload(); // Refresh the page to reflect changes
    })
    .catch(error => console.error('Error:', error));
}
