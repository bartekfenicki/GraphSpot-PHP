<?php
session_start();
require_once "../../../queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if (!isset($_SESSION['userID'])) {
    echo "Please log in to add a post.";
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo "Invalid or missing CSRF token.";
    exit;
}

unset($_SESSION['csrf_token']);

$userID = $_SESSION['userID'];
$content = $_POST['content'];
$location = $_POST['location'] ?? null;
$tags = explode(',', $_POST['tags']); 

try {
    $dbCon->beginTransaction();

    $postQuery = $dbCon->prepare("INSERT INTO Posts (content, location, type, userID) VALUES (:content, :location, 'post', :userID)");
    $postQuery->bindParam(':content', $content);
    $postQuery->bindParam(':location', $location);
    $postQuery->bindParam(':userID', $userID);
    $postQuery->execute();
    $postID = $dbCon->lastInsertId();

    $tagQuery = $dbCon->prepare("INSERT INTO Tags (tag) VALUES (:tag) ON DUPLICATE KEY UPDATE tagID = LAST_INSERT_ID(tagID)");
    $postTagQuery = $dbCon->prepare("INSERT INTO post_tags (postID, tagID) VALUES (:postID, :tagID)");

    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $tagQuery->bindParam(':tag', $tag);
            $tagQuery->execute();
            $tagID = $dbCon->lastInsertId();

            $postTagQuery->bindParam(':postID', $postID);
            $postTagQuery->bindParam(':tagID', $tagID);
            $postTagQuery->execute();
        }
    }


    $maxFileSize = 5 * 1024 * 1024; 
    $maxTotalSize = 25 * 1024 * 1024; 

    $totalFileSize = 0;
    $postImageQuery = $dbCon->prepare("INSERT INTO post_images (postID, imageID) VALUES (:postID, :imageID)");
    $imageQuery = $dbCon->prepare("INSERT INTO Images (media) VALUES (:media)");

    foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
        if ($index >= 5) break; // Limit to 5 images

        if (is_uploaded_file($tmpName)) {
            $fileSize = $_FILES['images']['size'][$index];
            $totalFileSize += $fileSize;

            if ($fileSize > $maxFileSize) {
                throw new Exception("File {$index} exceeds the maximum allowed size of 5MB.");
            }
            if ($totalFileSize > $maxTotalSize) {
                throw new Exception("The total size of uploaded files exceeds the maximum allowed limit of 25MB.");
            }

            $imageData = file_get_contents($tmpName);
            $imageQuery->bindParam(':media', $imageData, PDO::PARAM_LOB);
            $imageQuery->execute();
            $imageID = $dbCon->lastInsertId();

            $postImageQuery->bindParam(':postID', $postID);
            $postImageQuery->bindParam(':imageID', $imageID);
            $postImageQuery->execute();
        }
    }

    $dbCon->commit();
    echo "Post added successfully!";
    header("Location: ../../../index.php?page=home");

} catch (Exception $e) {
    if ($dbCon->inTransaction()) {
        $dbCon->rollBack();
    }

    error_log($e->getMessage());

    echo "Failed to add post: " . htmlspecialchars($e->getMessage());
}