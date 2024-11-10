<?php
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);

function getLikes($postId, $dbCon) {
    $likeQuery = $dbCon->prepare("SELECT COUNT(*) FROM Likes WHERE postID = :postID");
    $likeQuery->bindParam(':postID', $postId);
    $likeQuery->execute();
    return $likeQuery->fetchColumn();
}

function userHasLiked($postId, $userId, $dbCon) {
    $userLikeQuery = $dbCon->prepare("SELECT COUNT(*) FROM Likes WHERE postID = :postID AND userID = :userID");
    $userLikeQuery->bindParam(':postID', $postId);
    $userLikeQuery->bindParam(':userID', $userId);
    $userLikeQuery->execute();
    return $userLikeQuery->fetchColumn() > 0;
}

$queryPost = $dbCon->prepare("
    SELECT p.*, u.username
    FROM Posts p
    LEFT JOIN Users u ON p.userID = u.userID
    WHERE p.type = 'post'   -- Only fetch posts, excluding comments
    ORDER BY p.created_at DESC
");
$queryPost->execute();
$getPosts = $queryPost->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
<?php foreach ($getPosts as $getPost): ?>
    <div class="post-card">
        <div class="post-header">
            <div class="post-user-info">
                <strong><?= htmlspecialchars($getPost['username']) ?></strong>
                <span class="post-location"><?= htmlspecialchars($getPost['location']) ?></span>
            </div>
        </div>

        <div class="post-content">
            <p><?= nl2br(htmlspecialchars($getPost['content'])) ?></p>
            <p class="post-tag">
                <?php 
                $tagQuery = $dbCon->prepare("
                    SELECT t.tag 
                    FROM Post_Tags pt 
                    JOIN Tags t ON pt.tagID = t.tagID 
                    WHERE pt.postID = :postID
                ");
                $tagQuery->bindParam(':postID', $getPost['postID']);
                $tagQuery->execute();
                $tags = $tagQuery->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($tags as $tag): ?>
                    <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </p>
        </div>

        <div class="post-image-slider" id="slider-<?= $getPost['postID'] ?>">
            <div class="slider">
                <?php 
                $imageQuery = $dbCon->prepare("
                    SELECT i.media 
                    FROM Post_Images pi 
                    JOIN Images i ON pi.imageID = i.imageID 
                    WHERE pi.postID = :postID
                ");
                $imageQuery->bindParam(':postID', $getPost['postID']);
                $imageQuery->execute();
                $images = $imageQuery->fetchAll(PDO::FETCH_COLUMN);
                ?>

                <?php if ($images): ?>
                    <?php foreach ($images as $index => $imageData): 
                        $imageDataEncoded = base64_encode($imageData); ?>
                        <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                            <img src="data:image/jpeg;base64,<?= $imageDataEncoded ?>" alt="Post Image">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-image">No images available</div>
                <?php endif; ?>
            </div>
            <button class="prev" onclick="changeSlide(event, '<?= $getPost['postID'] ?>', -1)">&#10094;</button>
            <button class="next" onclick="changeSlide(event, '<?= $getPost['postID'] ?>', 1)">&#10095;</button>
        </div>
        <?php 
            $likeCount = getLikes($getPost['postID'], $dbCon);
            $userLiked = userHasLiked($getPost['postID'], $_SESSION['userID'], $dbCon);
        ?>
        <div class="like-section">
            <span id="like-count-<?= $getPost['postID'] ?>"><?= $likeCount ?></span> likes
            <button class="like-button" 
                    onclick="toggleLike(<?= $getPost['postID'] ?>)" 
                    id="like-btn-<?= $getPost['postID'] ?>" 
                    style="color: <?= $userLiked ? 'blue' : 'gray' ?>;">
                <?= $userLiked ? 'Unlike' : 'Like' ?>
            </button>
        <button class="save-button" data-postid="<?= $getPost['postID'] ?>" onclick="savePost(this)">Save</button>  </div>
        <a href="index.php?page=singlePost&postID=<?= $getPost['postID'] ?>" class="comment-button">Comment</a>
          </div>
    <?php endforeach; ?>
</body>

<style>

    /* Style for each post card */
    .post-card {
        max-width: 500px;
        margin: 20px auto;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        color: #333;
    }

    /* Header with user info and location */
    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .post-user-info {
        font-size: 1em;
    }

    .post-location {
        font-size: 0.9em;
        color: #888;
    }

    /* Post content styling */
    .post-content {
        font-size: 1em;
        margin-bottom: 15px;
    }

    .post-tag {
        color: #3498db;
        font-weight: bold;
    }

    /* Slider styles */
    .post-image-slider {
        position: relative;
        max-width: 100%;
        overflow: hidden;
        text-align: center;
    }

    /* Adjusting the slider styles */
.slider {
    display: flex;
    transition: transform 0.5s ease;
    width: 100%; /* Ensure the slider takes the full width */
}

.slide {
    min-width: 100%; /* Each slide should take up the full width */
    opacity: 0; /* Initially hide slides */
    transition: opacity 0.5s ease;
    margin-top: auto;
    margin-bottom: auto;
}

.slide.active {
    opacity: 1; /* Show the active slide */
    margin-top: auto;
    margin-bottom: auto;
}

/* Add this to ensure overflow is handled correctly */
.post-image-slider {
    overflow: hidden; /* Hide overflow to prevent white space */
}

    .post-image-slider img {
        max-width: 100%;
        border-radius: 8px;
        align-items: center;
    }

    .no-image {
        color: #aaa;
        font-style: italic;
    }

    /* Button styles */
    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        padding: 10px;
        border-radius: 50%;
        font-size: 18px;
        transition: background-color 0.3s;
        z-index: 1;
    }

    .prev:hover, .next:hover {
        background-color: rgba(255, 255, 255, 0.9);
    }

    .prev {
        left: 10px;
    }

    .next {
        right: 10px;
    }

    .tag {
        background-color: #e1f5fe; /* Light blue background for tags */
        color: #0d47a1; /* Dark blue text */
        padding: 5px 10px;
        border-radius: 15px;
        margin-right: 5px;
        display: inline-block; /* Make them inline-block for spacing */
    }
    .like-section {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

    .like-button {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        padding: 5px;
    }
    /* Save Button Styling */
.save-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
}

.save-button.saved {
    background-color: #28a745;
}
</style>

<script>
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
    fetch(`posts/likeHandler.php`, {
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

    fetch(`posts/savePost.php?postID=${postID}`, {
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

</script>