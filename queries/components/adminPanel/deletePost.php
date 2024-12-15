<?php
require_once("../../dbcon.php");

if (isset($_GET["ID"])) {
    $post_ID = $_GET["ID"];

    $dbCon = dbCon($user, $DBpassword);

    $query = $dbCon->prepare("DELETE FROM Posts WHERE postID = :postID");

    $query->bindParam(':postID', $post_ID, PDO::PARAM_INT);

    if ($query->execute()) {
        header("Location: ../../../index.php?page=adminPanel");

    } else {
        header("Location: ../index.php?status=error");
    }
} else {
    header("Location: ../index.php?status=missingID");
}
exit();

