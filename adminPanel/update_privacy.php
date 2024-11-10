<?php
require_once "../dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    // Update the content in the database
    $query = $dbCon->prepare("UPDATE siteInformation SET content = :content WHERE infoID = 2");
    $query->bindParam(':content', $content);
    
    if ($query->execute()) {
        echo "Welcome content updated successfully!";
        // Redirect back to the admin page
        header("Location: ../index.php?page=adminPanel");
        exit;
    } else {
        echo "Error updating content.";
    }
}
?>