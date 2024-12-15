<?php
require_once "queries/dbcon.php";
require_once "queries/pages/browseQuery.php";

$dbCon = dbCon($user, $DBpassword);
$browseQueries = new BrowseQueries($dbCon);
$postResults = [];
$userResults = [];
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchQuery = trim($_POST['search_query']);

    if (empty($searchQuery)) {
        $errorMessage = "Please provide a search query.";
    } else {
        try {
            $postResults = $browseQueries->searchPosts($searchQuery);
            $userResults = $browseQueries->searchUsers($searchQuery);
        } catch (Exception $e) {
            $errorMessage = "An error occurred while searching: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse</title>
    <link rel="stylesheet" href="style/pageStyles/browseStyle.css">
</head>
<body>
    <div class="container">
        <h1>Browse</h1>
        
        <form class="search-form" method="POST">
            <label for="search_query">Search:</label>
            <input type="text" id="search_query" name="search_query" placeholder="Enter location, tag, or username" value="<?= htmlspecialchars($_POST['search_query'] ?? '') ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <div class="results">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                 <?php if ($userResults): ?>
                    <h3>Users</h3>
                    <div class="users-grid">
                        <?php foreach ($userResults as $user): ?>
                            <div class="user-card">
                                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                                <a href="index.php?page=userProfile&userID=<?= $user['userID'] ?>">View Profile</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($postResults): ?>
                <h3>Posts</h3>
                <div class="posts-grid">
                    <?php foreach ($postResults as $post): ?>
                        <div class="post-card">
                            <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>">
                                <div class="post">
                                    <p class="username-post-display"> <?= htmlspecialchars($post['username']) ?></p>
                                    <p><strong>Location:</strong> <?= htmlspecialchars($post['location']) ?></p>
                                    <p> <?= nl2br(htmlspecialchars($post['content'])) ?></p>
                                    <?php if (!empty($post['first_image'])): ?>
                                        <div class="post-image">
                                            <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
