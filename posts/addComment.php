<?php
session_start();
require_once "../dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parentPostID = $_POST['parentPostID'];
    $userID = $_SESSION['userID'];
    $content = $_POST['content'];
    $image = null;

    if (!empty($_FILES['commentImage']['tmp_name']) && $_FILES['commentImage']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['commentImage']['tmp_name']);
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
        header("Location: ../index.php?page=singlePost&postID=" . $parentPostID);
        exit();
    } catch (PDOException $e) {
        echo "Failed to insert comment: " . $e->getMessage();
    }
}
?>