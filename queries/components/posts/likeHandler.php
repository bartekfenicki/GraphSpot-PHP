<?php
require_once '../../../queries/dbcon.php';
session_start();
$dbCon = dbCon($user, $DBpassword);

// Get data from AJAX request
$data = json_decode(file_get_contents("php://input"), true);
$postID = $data['postID'];
$userID = $_SESSION['userID'];

$likeCheck = $dbCon->prepare("SELECT * FROM Likes WHERE postID = :postID AND userID = :userID");
$likeCheck->bindParam(':postID', $postID);
$likeCheck->bindParam(':userID', $userID);
$likeCheck->execute();

if ($likeCheck->rowCount() > 0) {
    $removeLike = $dbCon->prepare("DELETE FROM Likes WHERE postID = :postID AND userID = :userID");
    $removeLike->bindParam(':postID', $postID);
    $removeLike->bindParam(':userID', $userID);
    $removeLike->execute();
    $userLiked = false;
} else {
    $addLike = $dbCon->prepare("INSERT INTO Likes (postID, userID) VALUES (:postID, :userID)");
    $addLike->bindParam(':postID', $postID);
    $addLike->bindParam(':userID', $userID);
    $addLike->execute();
    $userLiked = true;
}

$likeCountQuery = $dbCon->prepare("SELECT COUNT(*) FROM Likes WHERE postID = :postID");
$likeCountQuery->bindParam(':postID', $postID);
$likeCountQuery->execute();
$likeCount = $likeCountQuery->fetchColumn();

echo json_encode(['likeCount' => $likeCount, 'userLiked' => $userLiked]);
?>
