<?php
// DB connection
require_once "../dbcon.php";

if (isset($_GET['postID']) && isset($_GET['userID'])) {
    $postID = $_GET['postID'];
    $userID = $_GET['userID'];
    
    $dbCon = dbCon($user, $DBpassword);

    try {
        // Start a transaction
        $dbCon->beginTransaction();

        // Delete the post
        $deletePostQuery = $dbCon->prepare("DELETE FROM Posts WHERE postID = :postID");
        $deletePostQuery->bindParam(':postID', $postID, PDO::PARAM_INT);
        $deletePostQuery->execute();

        // Ban the user
        $banUserQuery = $dbCon->prepare("UPDATE Users SET isBanned = 1 WHERE userID = :userID");
        $banUserQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
        $banUserQuery->execute();

        // Commit the transaction
        $dbCon->commit();

        // Redirect back to adminPosts.php with success message
        header("Location: ../index.php?page=adminPanel");
    } catch (Exception $e) {
        // Rollback on error
        $dbCon->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid parameters provided.";
}
?>