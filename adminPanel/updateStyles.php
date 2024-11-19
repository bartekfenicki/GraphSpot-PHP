<?php
require_once "../dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedStyleID = $_POST['selected_style'];

    // Reset all styles to inactive
    $dbCon->prepare("UPDATE styles SET is_active = 0")->execute();

    // Set the selected style as active
    $updateQuery = $dbCon->prepare("UPDATE styles SET is_active = 1 WHERE styleID = :styleID");
    $updateQuery->bindParam(':styleID', $selectedStyleID);

    if ($updateQuery->execute()) {
        // Redirect back to adminstyling.php with success status
        header("Location: ../index.php?page=adminPanel");
        exit;
    } else {
        echo "Error updating the active style.";
    }
}
?>