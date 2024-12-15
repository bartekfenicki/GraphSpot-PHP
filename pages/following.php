<?php
require_once "queries/dbcon.php";
require_once "queries/pages/followingQuery.php";

$dbCon = dbCon($user, $DBpassword);
$followingQueries = new FollowingQueries($dbCon);

$userID = $_GET['userID'] ?? null;

if (!$userID) {
    die("User ID not specified.");
}

$followingList = $followingQueries->getFollowings($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Following</title>
    <link rel="stylesheet" href="style/pageStyles/followListStyle.css">
</head>
<body>
    <header>
        <h1>Following List</h1>
    </header>

    <main>
        <div class="following-container">
            <?php if ($followingList && count($followingList) > 0): ?>
                <ul class="following-list">
                    <?php foreach ($followingList as $following): ?>
                        <li class="following-item">
                            <!-- Display their profile picture -->
                            <img 
                                src="data:image/jpeg;base64,<?= base64_encode($following['followedProfilePic']) ?>" 
                                alt="Profile Picture"
                                class="follower-profile-pic"
                            >
                            <!-- Display the username and link to their profile -->
                            <a href="index.php?page=userProfile&userID=<?= $following['followedID'] ?>">
                                <?= htmlspecialchars($following['followedUsername']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-followings">You aren't following anyone yet.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
