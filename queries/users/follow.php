<?php
require_once '../dbcon.php';
$dbCon = dbCon($user, $DBpassword);

session_start();

if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$followerID = $_SESSION['userID'];
$followedID = $_POST['followedID'] ?? null;

if (!$followedID || $followerID == $followedID) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$checkFollowQuery = $dbCon->prepare("
    SELECT COUNT(*) 
    FROM UserFollows 
    WHERE followerID = :followerID AND followedID = :followedID
");
$checkFollowQuery->execute([':followerID' => $followerID, ':followedID' => $followedID]);
$isFollowing = $checkFollowQuery->fetchColumn() > 0;

if ($isFollowing) {
    // Unfollow
    $unfollowQuery = $dbCon->prepare("
        DELETE FROM UserFollows 
        WHERE followerID = :followerID AND followedID = :followedID
    ");
    $unfollowQuery->execute([':followerID' => $followerID, ':followedID' => $followedID]);
    echo json_encode(['status' => 'unfollowed']);
} else {
    // Follow
    $followQuery = $dbCon->prepare("
        INSERT INTO UserFollows (followerID, followedID) 
        VALUES (:followerID, :followedID)
    ");
    $followQuery->execute([':followerID' => $followerID, ':followedID' => $followedID]);
    echo json_encode(['status' => 'followed']);
}
?>