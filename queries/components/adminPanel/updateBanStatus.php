<?php
require_once "../../dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'];
    $status = $_POST['status'];

    if (!isset($userID) || !isset($status)) {
        echo "Invalid input.";
        exit;
    }

    $query = $dbCon->prepare("UPDATE Users SET isBanned = :status WHERE userID = :userID");
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':userID', $userID, PDO::PARAM_INT);

    if ($query->execute()) {
        echo $status ? "User has been banned." : "User has been unbanned.";
    } else {
        echo "Failed to update ban status.";
    }
}
?>