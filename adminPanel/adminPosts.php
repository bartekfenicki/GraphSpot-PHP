<?php
// DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);

// Fetch only regular posts with type 'post'
$queryPost = $dbCon->prepare("
    SELECT p.*, u.username
    FROM Posts p
    LEFT JOIN Users u ON p.userID = u.userID
    WHERE p.type = 'post'
    ORDER BY p.created_at DESC
");
$queryPost->execute();
$getPosts = $queryPost->fetchAll(PDO::FETCH_ASSOC);

foreach ($getPosts as $getPost): ?>
    <div class="container" style="border: 1px solid #ccc; padding: 16px; margin: 16px auto; border-radius: 8px; position: relative;">
        <div><strong>Post ID:</strong> <?= htmlspecialchars($getPost['postID']) ?></div>
        <div><strong>Posted by:</strong> <?= htmlspecialchars($getPost['username']) ?></div>
        <div><strong>Location:</strong> <?= htmlspecialchars($getPost['location']) ?></div>
        <div><strong>Content:</strong> <?= nl2br(htmlspecialchars($getPost['content'])) ?></div>
        
        <!-- Fetch and Display Post Images -->
        <?php 
        $imageQuery = $dbCon->prepare("
            SELECT i.media 
            FROM post_images pi 
            JOIN Images i ON pi.imageID = i.imageID 
            WHERE pi.postID = :postID
        ");
        $imageQuery->bindParam(':postID', $getPost['postID']);
        $imageQuery->execute();
        $images = $imageQuery->fetchAll(PDO::FETCH_COLUMN);
        
        if ($images): ?>
            <div class="image-box">
                <?php foreach ($images as $imageData):
                    $imageDataEncoded = base64_encode($imageData); ?>
                    <img src="data:image/jpeg;base64,<?= $imageDataEncoded ?>" alt="Post Image">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div>No images available</div>
        <?php endif; ?>

        <div class="button-row">
            <div><a href="adminPanel/deletePost.php?ID=<?= $getPost['postID'] ?>" class="btn materialize-red">Delete Post</a></div>
            <div>
                <a href="adminPanel/deleteAndBan.php?postID=<?= $getPost['postID'] ?>&userID=<?= $getPost['userID'] ?>" 
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
                    // Fetch comments for this post
                    $commentsQuery = $dbCon->prepare("
                        SELECT c.postID, c.content, u.username
                        FROM Posts c
                        LEFT JOIN Users u ON c.userID = u.userID
                        WHERE c.parentPostID = :postID AND c.type = 'comment'
                        ORDER BY c.created_at ASC
                    ");
                    $commentsQuery->bindParam(':postID', $getPost['postID']);
                    $commentsQuery->execute();
                    $comments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);

                    if ($comments): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong>Comment by:</strong> <?= htmlspecialchars($comment['username']) ?><br>
                                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                <?php if (!empty($comment['commentImage'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($comment['commentImage']) ?>" alt="Comment Image" class="comment-image">
                                <?php endif; ?>
                                <a href="adminPanel/deletePost.php?ID=<?= $comment['postID'] ?>" class="btn materialize-red">Delete Comment</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>No comments available</div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
<?php endforeach; ?>


<style>
    .image-box {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .image-box img {
        max-width: 200px;
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .comments-section {
        margin-top: 20px;
    }
    .comments-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background-color: #f9f9f9;
        padding: 0 10px;
        border-radius: 8px;
    }
    .comments-content.active {
        max-height: 500px; /* Set an appropriate max height */
        padding: 10px;
    }
    .comment {
        border-bottom: 1px solid #ddd;
        padding: 8px 0;
        margin-bottom: 10px;
    }
    .button-row {
        display: flex;
        gap: 15px;
    }
</style>

<script>
    function toggleComments(id) {
        const commentsDiv = document.getElementById(id);

        if (commentsDiv.classList.contains('active')) {
            commentsDiv.style.maxHeight = null; // Collapse
            commentsDiv.classList.remove('active');
        } else {
            commentsDiv.style.maxHeight = commentsDiv.scrollHeight + "px"; // Expand
            commentsDiv.classList.add('active');
        }
    }
    function updatePinStatus(postID, status) {
        const formData = new FormData();
        formData.append('postID', postID);
        formData.append('status', status);

        fetch('adminPanel/postPinned.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload(); // Refresh the page to reflect changes
        })
        .catch(error => console.error('Error:', error));
    }
</script>


