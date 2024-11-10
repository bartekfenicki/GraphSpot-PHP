<?php
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if (!isset($_GET['postID'])) {
    echo "No post specified!";
    exit();
}

$postID = $_GET['postID'];

//post details
$queryPost = $dbCon->prepare("
    SELECT p.*, u.username 
    FROM Posts p 
    JOIN Users u ON p.userID = u.userID 
    WHERE p.postID = :postID
");
$queryPost->bindParam(':postID', $postID, PDO::PARAM_INT);
$queryPost->execute();
$post = $queryPost->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post not found!";
    exit();
}

//comments
$commentQuery = $dbCon->prepare("
    SELECT p.*, u.username 
    FROM Posts p 
    JOIN Users u ON p.userID = u.userID 
    WHERE p.parentPostID = :postID AND p.type = 'comment'
    ORDER BY p.created_at ASC
");
$commentQuery->bindParam(':postID', $postID, PDO::PARAM_INT);
$commentQuery->execute();
$comments = $commentQuery->fetchAll(PDO::FETCH_ASSOC);

//images
$imageQuery = $dbCon->prepare("
    SELECT i.media 
    FROM Post_Images pi 
    JOIN Images i ON pi.imageID = i.imageID 
    WHERE pi.postID = :postID
");
$imageQuery->bindParam(':postID', $postID, PDO::PARAM_INT);
$imageQuery->execute();
$images = $imageQuery->fetchAll(PDO::FETCH_COLUMN);

//likes count
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

$likeCount = getLikes($postID, $dbCon);
$userLiked = userHasLiked($postID, $_SESSION['userID'], $dbCon);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Single Post</title>
</head>
<body>

<div class="single-post-container">
    <div class="all-post">
            <div class="post-image-slider" id="slider-<?= $postID ?>">
                <div class="slider">
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
                <button class="prev" data-post-id="<?= $postID ?>" onclick="changeSlide(this, -1)">&#10094;</button>
                <button class="next" data-post-id="<?= $postID ?>" onclick="changeSlide(this, 1)">&#10095;</button>
            </div>
        <div class="interaction-buttons">
                <span id="like-count-<?= $postID ?>"><?= $likeCount ?></span> likes
                <button class="like-button" 
                        onclick="toggleLike(<?= $postID ?>)" 
                        id="like-btn-<?= $postID ?>" 
                        style="color: <?= $userLiked ? 'blue' : 'gray' ?>;">
                    <?= $userLiked ? 'Unlike' : 'Like' ?>
                </button>
                <button class="save-button" data-postid="<?= $postID ?>" onclick="savePost(this)">Save</button>
        </div>
    </div>
    <!-- Post Information Section -->
    <div class="post-info-section">
        <h2><?= htmlspecialchars($post['username']) ?></h2>
        <p class="location"><?= htmlspecialchars($post['location']) ?></p>
        <p class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></p>


        <!-- Comment Section Placeholder -->
        <div class="comment-section">
    <h3>Comments</h3>
    
    <div class="comments">
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <strong><?= htmlspecialchars($comment['username']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    <?php if (!empty($comment['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($comment['image']) ?>" alt="Comment Image" class="comment-image">
                    <?php endif; ?>
                    
                    <!-- Like button for each comment -->
                    <?php 
                        $commentLikeCount = getLikes($comment['postID'], $dbCon);
                        $userLikedComment = userHasLiked($comment['postID'], $_SESSION['userID'], $dbCon);
                    ?>
                    <span><?= $commentLikeCount ?> likes</span>
                    <button onclick="toggleLike(<?= $comment['postID'] ?>)" 
                            style="color: <?= $userLikedComment ? 'blue' : 'gray' ?>;">
                        <?= $userLikedComment ? 'Unlike' : 'Like' ?>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>
    
    <!-- Form for posting a new comment -->
    <form action="posts/addComment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="parentPostID" value="<?= $postID ?>">
        <textarea name="content" placeholder="Add a comment..." required></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Post Comment</button>
    </form>
</div>
    </div>
</div>

</body>
<style>
    /* Container for single post */
.single-post-container {
    display: flex;
    gap: 20px;
    padding: 20px;
}

.post-image-slider {
    overflow: hidden;
    position: relative;
    width: 100%;
    max-width: 600px;
    height: 600px;
    border: 2px solid #662483;
    border-radius: 20px;
    background-color: #00000022;
    display: flex;
    align-items: center;
}

.slider {
    display: flex;
    transition: transform 0.5s ease;
    align-items: center;
}

.slide {
    min-width: 100%;
    transition: opacity 0.5s ease;
}

.slide.active {
    opacity: 1;
}

    .post-image-slider img {
        max-width: 100%;
        min-width: 100%;
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

/* Post information section */
.post-info-section {
    flex: 1;
    padding: 10px;
}

.post-info-section h2 {
    font-size: 24px;
    margin: 0;
}

.location {
    color: gray;
}

.content {
    margin-top: 10px;
    font-size: 18px;
}

.interaction-buttons {
    margin-top: 15px;
}

.like-button, .save-button {
    cursor: pointer;
    font-size: 16px;
    color: gray;
}

/* Comment Section */
.comment-section {
    margin-top: 20px;
}

.comment-section h3 {
    margin-bottom: 10px;
}

.comments {
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.comments p {
    font-size: 14px;
    color: #333;
}

textarea {
    width: 100%;
    padding: 8px;
    margin-top: 10px;
}
</style>

<script>
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
</html>
