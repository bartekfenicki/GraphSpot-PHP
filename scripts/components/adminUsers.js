function updateBanStatus(userID, status) {
    const formData = new FormData();
    formData.append('userID', userID);
    formData.append('status', status);

    fetch('queries/components/adminPanel/updateBanStatus.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload(); 
    })
    .catch(error => console.error('Error:', error));
}