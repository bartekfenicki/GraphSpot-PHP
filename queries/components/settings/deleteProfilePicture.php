<?php
session_start();
require_once "../../dbcon.php";

if (!isset($_SESSION['userID'])) {
    echo "Unauthorized access!";
    exit();
}

$userID = $_SESSION['userID'];

$dbCon = dbCon($user, $DBpassword);

$query = $dbCon->prepare("SELECT profilePic FROM Users WHERE userID = :userID");
$query->bindParam(':userID', $userID);
$query->execute();
$currentPicture = $query->fetchColumn();

if ($currentPicture && file_exists($currentPicture)) {
    unlink($currentPicture);
}

$query = $dbCon->prepare("UPDATE Users SET profilePic = NULL WHERE userID = :userID");
$query->bindParam(':userID', $userID);

if ($query->execute()) {
    header("Location: ../../../index.php?page=settings&message=profilePicRemoved");
    exit();
} else {
    echo "Error removing profile picture.";
    exit();
}
?>
