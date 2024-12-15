<?php
require_once "queries/dbcon.php";
require_once "queries/pages/profileQuery.php";

$dbCon = dbCon($user, $DBpassword);
$queries = new profileQueries($dbCon);

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
$profileUserID = isset($_GET['userID']) ? intval($_GET['userID']) : $_SESSION['userID'];

$userProfileData = $queries->getUserProfile($profileUserID);
if (!$userProfileData) {
    echo "<p>User profile not found.</p>";
    exit();
}

$userPosts = $queries->getUserPosts($profileUserID);
$likedPosts = $queries->getLikedPosts($_SESSION['userID']);
$savedPosts = $queries->getSavedPosts($_SESSION['userID']);
$followersList = $queries->getFollowers($profileUserID);
$followingList = $queries->getFollowing($profileUserID);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style/pageStyles/profileStyles.css">
</head>
<body>
    <div class="follow-info">
        <a href="index.php?page=followers&userID=<?= $profileUserID ?>">
            Followers: <?= htmlspecialchars($userProfileData['followerCount']) ?>
        </a>
        <a href="index.php?page=following&userID=<?= $profileUserID ?>">
            Following: <?= htmlspecialchars($userProfileData['followingCount']) ?>
        </a>
    </div>
    <div class="profile">
        <h1><?= htmlspecialchars($userProfileData['username']) ?></h1>
        <p><?= htmlspecialchars($userProfileData['Fname']) ?> <?= htmlspecialchars($userProfileData['Lname']) ?></p>
        <img src="data:image/jpeg;base64,<?= base64_encode($userProfileData['profilePic']) ?>" alt="Profile Picture" class="profile-picture">
        <p><?= nl2br(htmlspecialchars($userProfileData['userBio'])) ?></p>
    </div>

    <!-- Tabs -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Posts')" id="defaultOpen">Posts</button>
        <button class="tablinks" onclick="openTab(event, 'Liked')">Liked</button>
        <button class="tablinks" onclick="openTab(event, 'Saved')">Saved</button>
    </div>

    <!-- Tab content -->
    <div id="Posts" class="tabcontent">
        <div class="post-grid" id="saved-posts-grid">
            <?php if ($userPosts): ?>
                <?php foreach ($userPosts as $post): ?>
                    <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>">
                        <div class="post">
                            <p><?= htmlspecialchars($post['location']) ?></p>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['first_image'])): ?>
                                <div class="post-image">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't posted anything yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="Liked" class="tabcontent">
        <div class="post-grid" id="saved-posts-grid">
            <?php if ($likedPosts): ?>
                <?php foreach ($likedPosts as $post): ?>
                    <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>" class="post-link">
                        <div class="post">
                            <h3><?= htmlspecialchars($post['username']) ?></h3>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['first_image'])): ?>
                                <div class="post-image">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't liked any posts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="Saved" class="tabcontent">
        <div class="post-grid" id="saved-posts-grid">
            <?php if ($savedPosts): ?>
                <?php foreach ($savedPosts as $post): ?>
                    <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>">
                        <div class="post">
                            <h3><?= htmlspecialchars($post['username']) ?></h3>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['first_image'])): ?>
                                <div class="post-image">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't saved any posts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="scripts/pages/profile.js"></script> 
</body>
</html>

