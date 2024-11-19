<?php
// Initialize session and DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);


if (!isset($_GET['userID']) || !is_numeric($_GET['userID'])) {
    header("Location: index.php?page=home");
    exit();
}

$profileUserID = intval($_GET['userID']); // Securely fetch and sanitize userID

// Fetch user info
$queryUser = $dbCon->prepare("
    SELECT username, Fname, Lname, profilePic 
    FROM Users 
    WHERE userID = :userID
");
$queryUser->bindParam(':userID', $profileUserID);
$queryUser->execute();
$userInfo = $queryUser->fetch(PDO::FETCH_ASSOC);

if (!$userInfo) {
    echo "User not found.";
    exit();
}

// Query to fetch user's posts
$queryUserPosts = $dbCon->prepare("
    SELECT p.*, u.username, i.media AS first_image
    FROM Posts p
    LEFT JOIN Users u ON p.userID = u.userID
    LEFT JOIN post_images pi ON p.postID = pi.postID
    LEFT JOIN Images i ON pi.imageID = i.imageID
    WHERE p.userID = :userID AND p.type = 'post'
    GROUP BY p.postID
    ORDER BY p.created_at DESC
");
$queryUserPosts->bindParam(':userID', $profileUserID);
$queryUserPosts->execute();
$userPosts = $queryUserPosts->fetchAll(PDO::FETCH_ASSOC);

// Query to fetch liked posts
$queryLikedPosts = $dbCon->prepare("
    SELECT p.*, u.username, i.media AS first_image
    FROM Likes l
    JOIN Posts p ON l.postID = p.postID
    JOIN Users u ON p.userID = u.userID
    LEFT JOIN post_images pi ON p.postID = pi.postID
    LEFT JOIN Images i ON pi.imageID = i.imageID
    WHERE l.userID = :userID AND p.type = 'post'
    GROUP BY p.postID
");
$queryLikedPosts->bindParam(':userID', $profileUserID);
$queryLikedPosts->execute();
$likedPosts = $queryLikedPosts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($userInfo['username']) ?>'s Profile</title>
    <style>
        /* General Page Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Profile Header */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        .profile img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .profile h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .profile p {
            font-size: 16px;
            color: #666;
        }

        /* Tab Container */
        .tab {
            display: flex;
            justify-content: center;
            border-bottom: 1px solid #ddd;
            background-color: white;
            padding: 10px 0;
        }

        .tab button {
            background-color: inherit;
            border: none;
            outline: none;
            padding: 14px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .tab button:hover {
            border-bottom: 2px solid #662483;
        }

        .tab button.active {
            border-bottom: 2px solid #662483;
            font-weight: bold;
        }

        /* Tab Content */
        .tabcontent {
            display: none;
            padding: 20px;
            background-color: white;
        }

        .tabcontent.active {
            display: block;
        }

        /* Post Grid */
        .post-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            padding: 20px;
        }

        .post {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            width: 300px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 8px;
        }

        .post p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .post img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
    <script>
        function openTab(event, tabName) {
            const tabContents = document.querySelectorAll('.tabcontent');
            tabContents.forEach(content => content.classList.remove('active'));

            const tabButtons = document.querySelectorAll('.tab button');
            tabButtons.forEach(button => button.classList.remove('active'));

            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.tab button').click();
        });
    </script>
</head>
<body>
    <div class="profile">
        <img src="data:image/jpeg;base64,<?= base64_encode($userInfo['profilePic']) ?>" alt="Profile Picture">
        <h1><?= htmlspecialchars($userInfo['username']) ?></h1>
        <p><?= htmlspecialchars($userInfo['Fname']) ?> <?= htmlspecialchars($userInfo['Lname']) ?></p>
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
</body>
</html>
