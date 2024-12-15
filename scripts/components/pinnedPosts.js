function changeSlide(event, postID, direction) {
    const slides = document.querySelectorAll(`#slider-${postID} .slide`);
    const totalSlides = slides.length;

    if (totalSlides === 0) return; 

    let currentSlide = Array.from(slides).findIndex(slide => slide.classList.contains('active'));

    slides[currentSlide].classList.remove('active');

    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;

    slides[currentSlide].classList.add('active');

    const slider = document.querySelector(`#slider-${postID} .slider`);
    slider.style.transform = `translateX(-${currentSlide * 100}%)`; 
}

function toggleLike(postID) {
    fetch(`queries/components/posts/likeHandler.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ postID: postID }),
    })
    .then(response => response.json())
    .then(data => {
        // Update like count and button state
        document.getElementById(`like-count-${postID}`).innerText = data.likeCount;
        const likeButton = document.getElementById(`like-btn-${postID}`);
        likeButton.innerText = data.userLiked ? 'Unlike' : 'Like';
        likeButton.style.color = data.userLiked ? 'blue' : 'gray';
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