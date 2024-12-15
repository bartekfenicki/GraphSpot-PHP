<?php
session_start();
require_once "../../../queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['userID'])) {
    echo "Please log in to add a post.";
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo "Invalid or missing CSRF token.";
    exit;
}

unset($_SESSION['csrf_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parentPostID = filter_input(INPUT_POST, 'parentPostID', FILTER_VALIDATE_INT);
    if (!$parentPostID) {
        echo "Invalid Post ID.";
        exit;
    }

    $userID = $_SESSION['userID'];
    $content = $_POST['content'];
    $image = null;

    if (!empty($_FILES['commentImage']['tmp_name']) && $_FILES['commentImage']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['commentImage']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($fileType, $allowedTypes) && $_FILES['commentImage']['size'] < 5 * 1024 * 1024) { // 5MB size limit
            $image = file_get_contents($_FILES['commentImage']['tmp_name']);
        } else {
            echo "Invalid file type or size.";
            exit;
        }
    }

    $sql = "INSERT INTO Posts (location, content, type, userID, created_at, parentPostID"
         . ($image ? ", commentImage" : "") . ") 
            VALUES (NULL, :content, 'comment', :userID, NOW(), :parentPostID"
         . ($image ? ", :commentImage" : "") . ")";

    $insertComment = $dbCon->prepare($sql);
    $insertComment->bindParam(':content', $content);
    $insertComment->bindParam(':userID', $userID);
    $insertComment->bindParam(':parentPostID', $parentPostID);

    if ($image) {
        $insertComment->bindParam(':commentImage', $image, PDO::PARAM_LOB);
    }

    try {
        $insertComment->execute();
        header("Location: ../../../index.php?page=singlePost&postID=" . urlencode($parentPostID));
        exit();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "Failed to insert comment.";
    }
}

?>