<?php
require_once "queries/dbcon.php";
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

function getCommentCount($postId, $dbCon) {
    $commentCountQuery = $dbCon->prepare("SELECT COUNT(*) FROM Posts WHERE parentPostID = :postID AND type = 'comment'");
    $commentCountQuery->bindParam(':postID', $postId);
    $commentCountQuery->execute();
    return $commentCountQuery->fetchColumn();
}

$queryPost = $dbCon->prepare("
    SELECT p.*, u.userID, u.username
    FROM Posts p
    LEFT JOIN Users u ON p.userID = u.userID
    WHERE p.type = 'post'
    ORDER BY p.created_at DESC
");
$queryPost->execute();
$getPosts = $queryPost->fetchAll(PDO::FETCH_ASSOC);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/componentStyles/postDisplay.css">
</head>

<body>
<?php foreach ($getPosts as $getPost): ?>
    <div class="post-card">
        <div class="post-header">
            <div class="post-user-info">
            <?php
                $profileLink = $getPost['userID'] == $_SESSION['userID'] 
                    ? 'index.php?page=profile' 
                    : 'index.php?page=userProfile&userID=' . htmlspecialchars($getPost['userID']);
                ?>
                <a href="<?= $profileLink ?>">
                    <strong><?= htmlspecialchars($getPost['username']) ?></strong>
                </a>
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

        <!-- Slider for images -->
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
        <!-- Like button and count -->
        <div class="like-section">
            <span id="like-count-<?= $getPost['postID'] ?>"><?= $likeCount ?></span> likes
            <button class="like-button" 
                    onclick="toggleLike(<?= $getPost['postID'] ?>)" 
                    id="like-btn-<?= $getPost['postID'] ?>" 
                    style="color: <?= $userLiked ? 'blue' : 'gray' ?>;">
                <?= $userLiked ? 'Unlike' : 'Like' ?>
            </button>
            <?php 
                $commentCount = getCommentCount($getPost['postID'], $dbCon); 
            ?>
            <a href="index.php?page=singlePost&postID=<?= $getPost['postID'] ?>" class="comment-button">
                Comment (<?= $commentCount ?>)
            </a>
            <button class="save-button" data-postid="<?= $getPost['postID'] ?>" onclick="savePost(this)">Save</button>  </div>
            
            <?php
            $commentQuery = $dbCon->prepare("
                SELECT c.*, u.username 
                FROM Posts c 
                LEFT JOIN Users u ON c.userID = u.userID 
                WHERE c.type = 'comment' AND c.parentPostID = :postID 
                ORDER BY c.created_at DESC 
                LIMIT 2
            ");
                $commentQuery->bindParam(':postID', $getPost['postID']);
                $commentQuery->execute();
                $comments = $commentQuery->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="comments">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <?php if (!empty($comment['media'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($comment['media']) ?>" alt="Comment Media" class="comment-media">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php endif; ?>
                <a href="index.php?page=singlePost&postID=<?= $getPost['postID'] ?>" class="view-comments-button">
                    View the Post
                </a>
            </div>
        </div>
          
    <?php endforeach; ?>

    <script src="scripts/components/postDisplay.js"></script> 
</body>