<?php
session_start();
require_once "../../../queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if (!isset($_SESSION['userID']) || !isset($_GET['postID'])) {
    echo "Unauthorized access!";
    exit();
}

$userID = $_SESSION['userID'];
$postID = $_GET['postID'];

$queryCheck = $dbCon->prepare("SELECT * FROM Saves WHERE userID = :userID AND postID = :postID");
$queryCheck->bindParam(':userID', $userID);
$queryCheck->bindParam(':postID', $postID);
$queryCheck->execute();

if ($queryCheck->rowCount() > 0) {
    echo "Post already saved!";
} else {
    $querySave = $dbCon->prepare("INSERT INTO Saves (userID, postID) VALUES (:userID, :postID)");
    $querySave->bindParam(':userID', $userID);
    $querySave->bindParam(':postID', $postID);

    if ($querySave->execute()) {
        echo "Post saved!";
    } else {
        echo "Error saving post!";
    }
}
?>