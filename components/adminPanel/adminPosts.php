<?php
require_once "queries/dbcon.php";
require_once "queries/components/adminPanel/adminPostQuery.php";

$dbCon = dbCon($user, $DBpassword);

$adminPostQueries = new AdminPostQueries($dbCon);

$getPosts = $adminPostQueries->getAllPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Posts</title>
    <link rel="stylesheet" href="style/componentStyles/adminPosts.css">
</head>
<body>
<?php foreach ($getPosts as $getPost): ?>
    <div class="container" style="border: 1px solid #ccc; padding: 16px; margin: 16px auto; border-radius: 8px; position: relative;">
        <div><strong>Post ID:</strong> <?= htmlspecialchars($getPost['postID']) ?></div>
        <div><strong>Posted by:</strong> <?= htmlspecialchars($getPost['username']) ?></div>
        <div><strong>Location:</strong> <?= htmlspecialchars($getPost['location']) ?></div>
        <div><strong>Content:</strong> <?= nl2br(htmlspecialchars($getPost['content'])) ?></div>
        
        <!-- Fetch and Display Post Images -->
        <?php 
        $images = $adminPostQueries->getPostImages($getPost['postID']);
        if ($images): ?>
            <div class="image-box">
                <?php foreach ($images as $imageData): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($imageData) ?>" alt="Post Image">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div>No images available</div>
        <?php endif; ?>

        <div class="button-row">
            <div><a href="queries/components/adminPanel/deletePost.php?ID=<?= $getPost['postID'] ?>" class="btn materialize-red">Delete Post</a></div>
            <div>
                <a href="queries/components/adminPanel/deleteAndBan.php?postID=<?= $getPost['postID'] ?>&userID=<?= $getPost['userID'] ?>" 
                class="btn materialize-grey">
                Delete & Ban User
                </a>
            </div>
            <div>
                <?php if ($getPost['isPinned']): ?>
                    <button onclick="updatePinStatus(<?= $getPost['postID'] ?>, 0)">Unpin</button>
                <?php else: ?>
                    <button onclick="updatePinStatus(<?= $getPost['postID'] ?>, 1)">Pin</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments Dropdown -->
        <div class="comments-section">
            <button onclick="toggleComments('comments-<?= $getPost['postID'] ?>')" class="btn materialize-blue">Toggle Comments</button>
            <div id="comments-<?= $getPost['postID'] ?>" class="comments-content">
                <?php 
                $comments = $adminPostQueries->getComments($getPost['postID']);
                if ($comments): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong>Comment by:</strong> <?= htmlspecialchars($comment['username']) ?><br>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <?php if (!empty($comment['commentImage'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($comment['commentImage']) ?>" alt="Comment Image" class="comment-image">
                            <?php endif; ?>
                            <a href="queries/components/adminPanel/deletePost.php?ID=<?= $comment['postID'] ?>" class="btn materialize-red">Delete Comment</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>No comments available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="scripts/components/adminPosts.js"></script> 
</body>
</html>
