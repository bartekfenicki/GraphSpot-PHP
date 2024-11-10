<?php
require_once "../dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

$checkQuery = $dbCon->prepare("SELECT COUNT(*) FROM LogoDisplay WHERE logoID = 1");
$checkQuery->execute();

if ($checkQuery->fetchColumn() == 0) {
    $insertQuery = $dbCon->prepare("INSERT INTO LogoDisplay (logoID, logo) VALUES (1, :logo)");
    $defaultLogoPath = '../assets/images/logo1.png';
    $defaultLogoData = file_get_contents($defaultLogoPath); 
    $insertQuery->bindParam(':logo', $defaultLogoData, PDO::PARAM_LOB);
    $insertQuery->execute();
}

if (isset($_FILES['newLogo']) && $_FILES['newLogo']['error'] === UPLOAD_ERR_OK) {
    $image = $_FILES['newLogo'];
    $fileType = mime_content_type($image['tmp_name']);
    
    if (strpos($fileType, 'image') === false) {
        header("Location: ../index.php?page=adminPanel&status=invalidfile");
        exit();
    }

    $logoData = file_get_contents($image['tmp_name']);
    
    $query = $dbCon->prepare("UPDATE LogoDisplay SET logo = :logo WHERE logoID = 1");
    $query->bindParam(':logo', $logoData, PDO::PARAM_LOB);

    if ($query->execute()) {
        header("Location: ../index.php?page=adminPanel&status=success");
    } else {
        header("Location: ../index.php?page=adminPanel&status=dberror");
    }
} else {
    header("Location: ../index.php?page=adminPanel&status=nofile");
}
exit();