<?php
require_once "../../dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postID = $_POST['postID'];
    $status = $_POST['status'];

    if (!isset($postID) || !isset($status)) {
        echo "Invalid input.";
        exit;
    }

    $query = $dbCon->prepare("UPDATE Posts SET isPinned = :status WHERE postID = :postID");
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':postID', $postID, PDO::PARAM_INT);

    if ($query->execute()) {
        echo $status ? "Post has been Pinned." : "Post has been Unpinned.";
    } else {
        echo "Failed to update pin status.";
    }
}
?>