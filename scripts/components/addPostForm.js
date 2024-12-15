document.querySelector('form').addEventListener('submit', function (e) {
    const tags = document.getElementById('tags').value.split(',');
    const cleanedTags = tags.map(tag => tag.trim()).filter(tag => tag !== '');

    if (cleanedTags.length === 0) {
        e.preventDefault();
        alert('Please enter at least one valid tag.');
    } else {
        document.getElementById('tags').value = cleanedTags.join(',');
    }
});