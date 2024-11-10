<?php
session_start();
require_once "../dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parentPostID = $_POST['parentPostID'];
    $userID = $_SESSION['userID'];
    $content = $_POST['content'];
    $image = null;

    // Check if an image is uploaded and has no upload errors
    if (!empty($_FILES['commentImage']['tmp_name']) && $_FILES['commentImage']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['commentImage']['tmp_name']);
    }

    // Prepare SQL statement with conditional binding for the image
    $sql = "INSERT INTO Posts (location, content, type, userID, created_at, parentPostID"
         . ($image ? ", commentImage" : "") . ") 
            VALUES (NULL, :content, 'comment', :userID, NOW(), :parentPostID"
         . ($image ? ", :commentImage" : "") . ")";

    $insertComment = $dbCon->prepare($sql);
    $insertComment->bindParam(':content', $content);
    $insertComment->bindParam(':userID', $userID);
    $insertComment->bindParam(':parentPostID', $parentPostID);

    // Only bind image if provided
    if ($image) {
        $insertComment->bindParam(':commentImage', $image, PDO::PARAM_LOB);
    }

    try {
        $insertComment->execute();
        // Redirect back to the post page
        header("Location: ../index.php?page=singlePost&postID=" . $parentPostID);
        exit();
    } catch (PDOException $e) {
        // Error handling (you may log this or echo for debugging)
        echo "Failed to insert comment: " . $e->getMessage();
    }
}
?>