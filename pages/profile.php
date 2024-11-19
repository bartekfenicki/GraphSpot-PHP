<?php
// Initialize session and DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info
$queryUser = $dbCon->prepare("
    SELECT username, Fname, Lname, profilePic 
    FROM Users 
    WHERE userID = :userID
");
$queryUser->bindParam(':userID', $_SESSION['userID']);
$queryUser->execute();
$userInfo = $queryUser->fetch(PDO::FETCH_ASSOC);


// Query to fetch user's own posts
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
$queryUserPosts->bindParam(':userID', $_SESSION['userID']);
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
$queryLikedPosts->bindParam(':userID', $_SESSION['userID']);
$queryLikedPosts->execute();
$likedPosts = $queryLikedPosts->fetchAll(PDO::FETCH_ASSOC);

// Query to fetch saved posts with their first image
$querySavedPosts = $dbCon->prepare("
    SELECT p.*, u.username, i.media AS first_image
    FROM Saves sp
    JOIN Posts p ON sp.postID = p.postID
    JOIN Users u ON p.userID = u.userID
    LEFT JOIN post_images pi ON p.postID = pi.postID
    LEFT JOIN Images i ON pi.imageID = i.imageID
    WHERE sp.userID = :userID
    GROUP BY p.postID
    ORDER BY sp.saved_at DESC
");
$querySavedPosts->bindParam(':userID', $_SESSION['userID']);
$querySavedPosts->execute();
$savedPosts = $querySavedPosts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <script>
        function openTab(event, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " active";
        }

        // Automatically open the "Posts" tab on page load
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("defaultOpen").click();
        });
    </script>
</head>
<body>

<div class="profile">
    <h1><?= htmlspecialchars($userInfo['username']) ?></h1>
    <p><?= htmlspecialchars($userInfo['Fname']) ?> <?= htmlspecialchars($userInfo['Lname']) ?></p>
    <img src="data:image/jpeg;base64,<?= base64_encode($userInfo['profilePic']) ?>" alt="Profile Picture" class="profile-picture">
</div>

<!-- Tab links -->
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

</body>
<style scoped>
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

        .profile h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .profile p {
            font-size: 16px;
            color: #666;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        /* Tab Container */
        .tab {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
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

        /* Post Grid */
        .post-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px;
            justify-content: start;
        }

        .post {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            width: 300px;
            text-align: center;
            position: relative;
        }
        .post h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .post p {
            font-size: 14px;
            color: #666;
        }

        .save-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .save-button.saved {
            background-color: #28a745;
        }

     /* Style for posts */
.tabcontent .post {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.post h3 {
    font-size: 18px;
    margin: 0 0 8px;
}

.post p {
    font-size: 14px;
    color: #555;
}

.post-image img {
    width: 100%;
    max-width: 250px;
    height: auto;
    border-radius: 8px;
    margin-top: auto;
    margin-bottom: auto;
}
    </style>

</html>
