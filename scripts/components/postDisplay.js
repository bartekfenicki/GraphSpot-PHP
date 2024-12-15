function changeSlide(event, postID, direction) {
    const slides = document.querySelectorAll(`#slider-${postID} .slide`);
    const totalSlides = slides.length;

    if (totalSlides === 0) return; // If there are no slides, do nothing

    // Find the currently active slide
    let currentSlide = Array.from(slides).findIndex(slide => slide.classList.contains('active'));

    // Hide the current slide
    slides[currentSlide].classList.remove('active');

    // Calculate the new slide index
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;

    // Show the new slide
    slides[currentSlide].classList.add('active');

    // Ensure the slides are positioned correctly
    const slider = document.querySelector(`#slider-${postID} .slider`);
    slider.style.transform = `translateX(-${currentSlide * 100}%)`; // Move the slider based on currentSlide
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
        document.querySelectorAll(`#like-count-${postID}`).forEach(el => {
            el.innerText = data.likeCount;
        });

        document.querySelectorAll(`#like-btn-${postID}`).forEach(btn => {
            btn.innerText = data.userLiked ? 'Unlike' : 'Like';
            btn.style.color = data.userLiked ? 'blue' : 'gray';
        });
    })
    .catch(error => console.error('Error:', error));
}

function savePost(button) {
    alert("Button clicked for postID: " + button.getAttribute('data-postid'));
    const postID = button.getAttribute('data-postid'); 

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
