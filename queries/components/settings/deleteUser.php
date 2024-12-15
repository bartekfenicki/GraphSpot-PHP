<?php
session_start();
require_once "../../dbcon.php";

if (!isset($_SESSION['userID'])) {
    echo "Unauthorized access!";
    exit();
}

$userID = $_SESSION['userID'];

$dbCon = dbCon($user, $DBpassword);

$query = $dbCon->prepare("DELETE FROM Users WHERE userID = :userID");
$query->bindParam(':userID', $userID);

if ($query->execute()) {
    session_destroy();
    header("Location: ../../../startPage.php");
    exit();
} else {
    echo "Error deleting account.";
    exit();
}
?>
