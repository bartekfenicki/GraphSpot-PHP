<?php
require_once "queries/dbcon.php";
require_once "queries/pages/followersQuery.php";

$dbCon = dbCon($user, $DBpassword);
$followersQueries = new FollowersQueries($dbCon);
$userID = $_GET['userID'] ?? null;

if (!$userID) {
    die("User ID not specified.");
}

$followersList = $followersQueries->getFollowers($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followers</title>
    <link rel="stylesheet" href="style/pageStyles/followListStyle.css">
</head>
<body>
    <h1>Followers</h1>
    <div class="followers-list">
        <?php if ($followersList): ?>
            <ul>
                <?php foreach ($followersList as $follower): ?>
                    <li>
                        <img 
                            src="data:image/jpeg;base64,<?= base64_encode($follower['profilePic']) ?>" 
                            alt="Profile Picture" 
                            class="follower-profile-pic"
                        >
                        <a href="index.php?page=userProfile&userID=<?= $follower['userID'] ?>">
                            <?= htmlspecialchars($follower['username']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="no-followers">No followers found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
