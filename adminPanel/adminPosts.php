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
            FROM Post_Images pi 
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

        <!-- Delete Post Link -->
        <div><a href="adminPanel/deletePost.php?ID=<?= $getPost['postID'] ?>" class="btn materialize-red">Delete Post</a></div>

        <!-- Comments Dropdown -->
        <div class="comments-dropdown">
            <button onclick="toggleDropdown('comments-<?= $getPost['postID'] ?>')">View Comments</button>
            <div id="comments-<?= $getPost['postID'] ?>" class="dropdown-content">
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

                if ($comments):
                    foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong>Comment by:</strong> <?= htmlspecialchars($comment['username']) ?>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <a href="adminPanel/deleteComment.php?ID=<?= $comment['postID'] ?>" class="btn materialize-red">Delete Comment</a>
                        </div>
                    <?php endforeach;
                else: ?>
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
    .comments-dropdown {
        margin-top: 10px;
    }
    .dropdown-content {
        display: none; /* Hidden by default */
        position: absolute; /* Position relative to the container */
        top: 100%; /* Position directly below the button */
        left: 0;
        width: 100%; /* Full width of the container */
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        border-radius: 8px;
        z-index: 1; /* Ensure it displays above other elements */
    }
    .comment {
        border-bottom: 1px solid #ddd;
        padding: 8px 0;
    }
</style>

<script>
    // Toggle dropdown visibility for comments
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        
        // Toggle display
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    }
</script>


