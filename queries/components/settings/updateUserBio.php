<?php
session_start();
require_once "../../dbcon.php";

if (!isset($_SESSION['userID'])) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $userBio = trim($_POST['userBio']);

    

    $dbCon = dbCon($user, $DBpassword);

    $query = $dbCon->prepare("UPDATE Users SET userBio = :userBio WHERE userID = :userID");
    $query->bindParam(':userBio', $userBio);
    $query->bindParam(':userID', $userID);

    if ($query->execute()) {
        header("Location: ../../../index.php?page=settings&message=bioUpdated");
        exit();
    } else {
        echo "Error updating bio.";
        exit();
    }
} else {
    echo "Invalid request method!";
    exit();
}

