function openTab(event, tabName) {
    const tabContents = document.querySelectorAll('.tabcontent');
    tabContents.forEach(content => content.classList.remove('active'));

    const tabButtons = document.querySelectorAll('.tab button');
    tabButtons.forEach(button => button.classList.remove('active'));

    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.tab button').click();

    const followButton = document.getElementById('followToggle');
    if (followButton) {
        followButton.addEventListener('click', function() {
            const button = this;
            const followedID = button.getAttribute('data-followed-id');
            fetch('queries/users/follow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ followedID })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'followed') {
                    button.textContent = 'Following';
                    button.classList.add('following');
                    button.classList.remove('not-following');
                } else if (data.status === 'unfollowed') {
                    button.textContent = 'Follow';
                    button.classList.add('not-following');
                    button.classList.remove('following');
                }
            });
        });
    }
});