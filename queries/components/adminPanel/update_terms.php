<?php
require_once "../../dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    $query = $dbCon->prepare("UPDATE siteInformation SET content = :content WHERE infoID = 3");
    $query->bindParam(':content', $content);
    
    if ($query->execute()) {
        echo "Welcome content updated successfully!";
        header("Location: ../../../index.php?page=adminPanel");
        exit;
    } else {
        echo "Error updating content.";
    }
}
?>