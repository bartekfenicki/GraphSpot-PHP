<?php
session_start();
require_once "../dbcon.php";
$dbCon = dbCon($user, $DBpassword);

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo "Please log in to add a post.";
    exit;
}

$userID = $_SESSION['userID'];

// Collect form data
$content = $_POST['content'];
$location = $_POST['location'] ?? null;
$tags = explode(',', $_POST['tags']); // Assume tags are input as comma-separated values

try {
    // Start a transaction
    $dbCon->beginTransaction();

    // Insert post data into the `posts` table
    $postQuery = $dbCon->prepare("INSERT INTO posts (content, location, type, userID) VALUES (:content, :location, 'post', :userID)");
    $postQuery->bindParam(':content', $content);
    $postQuery->bindParam(':location', $location);
    $postQuery->bindParam(':userID', $userID);
    $postQuery->execute();
    $postID = $dbCon->lastInsertId();

    // Handle tags
    $tagQuery = $dbCon->prepare("INSERT INTO tags (tag) VALUES (:tag) ON DUPLICATE KEY UPDATE tagID = LAST_INSERT_ID(tagID)");
    $postTagQuery = $dbCon->prepare("INSERT INTO post_tags (postID, tagID) VALUES (:postID, :tagID)");
    
    foreach ($tags as $tag) {
        $tag = trim($tag);
        $tagQuery->bindParam(':tag', $tag);
        $tagQuery->execute();
        $tagID = $dbCon->lastInsertId();

        $postTagQuery->bindParam(':postID', $postID);
        $postTagQuery->bindParam(':tagID', $tagID);
        $postTagQuery->execute();
    }

    // Handle image uploads (up to 5 images)
    $postImageQuery = $dbCon->prepare("INSERT INTO post_images (postID, imageID) VALUES (:postID, :imageID)");
    $imageQuery = $dbCon->prepare("INSERT INTO images (media) VALUES (:media)");

    foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
        if ($index >= 5) break; // Limit to 5 images

        $imageData = file_get_contents($tmpName);
        $imageQuery->bindParam(':media', $imageData, PDO::PARAM_LOB);
        $imageQuery->execute();
        $imageID = $dbCon->lastInsertId();

        $postImageQuery->bindParam(':postID', $postID);
        $postImageQuery->bindParam(':imageID', $imageID);
        $postImageQuery->execute();
    }

    // Commit transaction
    $dbCon->commit();
    echo "Post added successfully!";
    header("Location: ../index.php?page=adminPanel");

} catch (Exception $e) {
    $dbCon->rollBack();
    echo "Failed to add post: " . $e->getMessage();
}
?>