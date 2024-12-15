function changeSlide(button, direction) {
    const postID = button.getAttribute('data-post-id');
    const slides = document.querySelectorAll(`#slider-${postID} .slide`);
    const totalSlides = slides.length;

    if (totalSlides === 0) return;

    // Find the currently active slide
    let currentSlideIndex = Array.from(slides).findIndex(slide => slide.classList.contains('active'));

    // Remove 'active' class from the current slide
    slides[currentSlideIndex].classList.remove('active');

    // Calculate the new slide index
    currentSlideIndex = (currentSlideIndex + direction + totalSlides) % totalSlides;

    // Add 'active' class to the new slide
    slides[currentSlideIndex].classList.add('active');

    // Adjust the slider position
    const slider = document.querySelector(`#slider-${postID} .slider`);
    slider.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
}
function toggleLike(postID, isComment = false) {
    fetch(`queries/components/posts/likeHandler.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ postID: postID, isComment: isComment }),
    })
    .then(response => response.json())
    .then(data => {
        // Update like count and button state
        const likeCountElement = document.getElementById(`like-count-${postID}`);
        if (likeCountElement) {
            likeCountElement.innerText = data.likeCount;
        }

        const likeButton = document.getElementById(`like-btn-${postID}`);
        if (likeButton) {
            likeButton.innerText = data.userLiked ? 'Unlike' : 'Like';
            likeButton.style.color = data.userLiked ? 'blue' : 'gray';
        }
    })
    .catch(error => console.error('Error:', error));
}

function savePost(button) {
alert("Button clicked for postID: " + button.getAttribute('data-postid'));
const postID = button.getAttribute('data-postid'); // Get postID from button attribute

fetch(`queries/components/posts/savePost.php?postID=${postID}`, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    },
})
.then(response => response.text())
.then(data => {
    alert(data); 
})
.catch(error => console.error('Error saving post:', error));
}
