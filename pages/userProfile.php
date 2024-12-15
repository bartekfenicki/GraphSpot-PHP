<?php
$dbCon = dbCon($user, $DBpassword);
require_once "queries/dbcon.php";
require_once "queries/pages/userProfileQuery.php";

if (!isset($_SESSION['userID'])) {
    header("Location: startPage.php");
    exit();
}

$userID = $_SESSION['userID']; 

if (!isset($_GET['userID']) || !is_numeric($_GET['userID'])) {
    header("Location: index.php?page=home");
    exit();
}
$profileUserID = intval($_GET['userID']);

$userProfileQueries = new UserProfileQueries($dbCon);

$userInfo = $userProfileQueries->getUserProfile($profileUserID);
if (!$userInfo) {
    echo "User not found.";
    exit();
}

$userPosts = $userProfileQueries->getUserPosts($profileUserID);
$likedPosts = $userProfileQueries->getLikedPosts($profileUserID);
$isFollowing = $userProfileQueries->isUserFollowing($userID, $profileUserID);
$followerCount = $userProfileQueries->getFollowerCount($profileUserID);
$followingCount = $userProfileQueries->getFollowingCount($profileUserID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($userInfo['username']) ?>'s Profile</title>
    <link rel="stylesheet" href="style/pageStyles/userProfileStyle.css">
</head>
<body>
    <div class="profile">
        <div class="follow-info">
            <a href="index.php?page=followers&userID=<?= $profileUserID ?>">
                Followers: <?= $followerCount ?>
            </a>
            <a href="index.php?page=following&userID=<?= $profileUserID ?>">
                Following: <?= $followingCount ?>
            </a>
        </div>
        <img src="data:image/jpeg;base64,<?= base64_encode($userInfo['profilePic']) ?>" alt="Profile Picture">
        <h1><?= htmlspecialchars($userInfo['username']) ?></h1>
        <p><?= htmlspecialchars($userInfo['Fname']) ?> <?= htmlspecialchars($userInfo['Lname']) ?></p>
        <div id="follow-button">
        <button 
            id="followToggle" 
            class="<?= $isFollowing ? 'following' : 'not-following' ?>" 
            data-followed-id="<?= $profileUserID ?>">
            <?= $isFollowing ? 'Following' : 'Follow' ?>
        </button>
    </div>
    </div>

    <div class="tab">
        <button onclick="openTab(event, 'Posts')" class="active">Posts</button>
        <button onclick="openTab(event, 'Liked')">Liked</button>
    </div>

    <div id="Posts" class="tabcontent active">
        <div class="post-grid">
            <?php if ($userPosts): ?>
                <?php foreach ($userPosts as $post): ?>
                    <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>">
                        <div class="post">
                            <h3><?= htmlspecialchars($post['location']) ?></h3>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['first_image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">This user hasn't posted anything yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="Liked" class="tabcontent">
        <div class="post-grid">
            <?php if ($likedPosts): ?>
                <?php foreach ($likedPosts as $post): ?>
                    <a href="index.php?page=singlePost&postID=<?= $post['postID'] ?>">
                        <div class="post">
                            <h3><?= htmlspecialchars($post['username']) ?></h3>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['first_image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($post['first_image']) ?>" alt="Post Image">
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">This user hasn't liked any posts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="scripts/pages/userProfile.js"></script> 
</body>
</html>
