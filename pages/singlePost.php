<?php 
require_once "queries/dbcon.php";
require_once "queries/pages/singlePostQuery.php";

$dbCon = dbCon($user, $DBpassword);
$singlePostQueries = new singlePostQueries($dbCon);

if (!isset($_GET['postID'])) {
    echo "No post specified!";
    exit();
}

$postID = $_GET['postID'];
$post = $singlePostQueries->getPost($postID);
if (!$post) {
    echo "Post not found!";
    exit();
}

$comments = $singlePostQueries->getComments($postID);
$images = $singlePostQueries->getPostImages($postID);
$likeCount = $singlePostQueries->getLikesCount($postID);
$userLiked = $singlePostQueries->userHasLiked($postID, $_SESSION['userID']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Single Post</title>
    <link rel="stylesheet" href="style/pageStyles/singlePostStyle.css">
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
                            onclick="toggleLike(<?= $postID ?>, false)" 
                            id="like-btn-<?= $postID ?>" 
                            style="color: <?= $userLiked ? 'blue' : 'gray' ?>;">
                        <?= $userLiked ? 'Unlike' : 'Like' ?>
                    </button>
                    <button class="save-button" data-postid="<?= $postID ?>" onclick="savePost(this)">Save</button>
                    <?php if ($_SESSION['userID'] == $post['userID']): ?>
                        <button><a href="queries/components/posts/deletePostUser.php?ID=<?= $post['postID'] ?>">Delete Post</a></button>
                    <?php endif; ?>
            </div>
        </div>
        <div class="post-info-section">
        <?php

        $profileLink = $post['userID'] == $_SESSION['userID'] 
            ? 'index.php?page=profile' 
            : 'index.php?page=userProfile&userID=' . htmlspecialchars($post['userID']);
        ?>
        <a href="<?= $profileLink ?>">
            <strong><?= htmlspecialchars($post['username']) ?></strong>
        </a>
            <p class="location"><?= htmlspecialchars($post['location']) ?></p>
            <p class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        

            <div class="comment-section">
                <h3>Comments</h3>
                
                <div class="comments">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                            <?php
                                $profileLink = $comment['userID'] == $_SESSION['userID'] 
                                    ? 'index.php?page=profile' 
                                    : 'index.php?page=userProfile&userID=' . htmlspecialchars($comment['userID']);
                                ?>
                                <a href="<?= $profileLink ?>">
                                    <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                </a>
                                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                <?php if (!empty($comment['commentImage'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($comment['commentImage']) ?>" alt="Comment Image" class="comment-image">
                                <?php endif; ?>
                                <?php 
                                    $commentLikeCount = $singlePostQueries->getLikesCount($comment['postID']);
                                    $userLikedComment = $singlePostQueries->userHasLiked($comment['postID'], $_SESSION['userID']);
                                ?>
                                <span><?= $commentLikeCount ?> likes</span>
                                <button class="like-button" 
                                        onclick="toggleLike(<?= $comment['postID'] ?>, true)" 
                                        id="like-btn-<?= $comment['postID'] ?>" 
                                        style="color: <?= $userLikedComment ? 'blue' : 'gray' ?>;">
                                    <?= $userLikedComment ? 'Unlike' : 'Like' ?>
                                </button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div> 
            <?php
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    // echo "Generated CSRF Token: " . $_SESSION['csrf_token'];
                }
                $csrfToken = $_SESSION['csrf_token'];
                ?>

        
        <!-- comment form -->
        <form action="queries/components/posts/addComment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="parentPostID" value="<?= $postID ?>">
            <textarea name="content" placeholder="Add a comment..." required></textarea>
            <input type="file" name="commentImage" accept="image/*">
            <button type="submit">Post Comment</button>
        </form>
    </div>
        </div>
    </div>

    <script src="scripts/pages/singlePost.js"></script> 
</body>
</html>
