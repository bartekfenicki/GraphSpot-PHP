<?php
session_start();
require_once "../../dbcon.php";

if (!isset($_SESSION['userID'])) {
    echo "Unauthorized access!";
    exit();
}

$userID = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePicture'])) {
    $dbCon = dbCon($user, $DBpassword);


    $file = $_FILES['profilePicture'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        exit();
    }

    $fileData = file_get_contents($file['tmp_name']);

    $query = $dbCon->prepare("UPDATE Users SET profilePic = :profilePic WHERE userID = :userID");
    $query->bindParam(':profilePic', $fileData, PDO::PARAM_LOB); // Store file as binary data
    $query->bindParam(':userID', $userID);

    if ($query->execute()) {
        header("Location: ../../../index.php?page=settings&message=profilePicUpdated");
        exit();
    } else {
        echo "Error updating profile picture in the database.";
        exit();
    }
} else {
    echo "No file uploaded or invalid request.";
    exit();
}
?>

