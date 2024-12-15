<?php
require_once "queries/dbcon.php";
require_once "queries/components/adminPanel/adminSiteInfoQuery.php";

$dbCon = dbCon($user, $DBpassword);

$siteInfoQueries = new AdminSiteInfoQueries($dbCon);

$welcomeData = $siteInfoQueries->getSiteInfo(1);
$privacyPolicy = $siteInfoQueries->getSiteInfo(2);
$termsConditions = $siteInfoQueries->getSiteInfo(3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Site Info</title>
    <link rel="stylesheet" href="style/componentStyles/adminSiteInfo.css">
</head>
<body>

<h2>Edit Welcome Content</h2>
<form action="queries/components/adminPanel/update_info.php" method="post">
    <label for="welcomeContent">Welcome Message:</label><br>
    <textarea id="welcomeContent" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($welcomeData['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>

<h2>Edit Privacy Policy</h2>
<form action="queries/components/adminPanel/update_privacy.php" method="post">
    <label for="privacyContent">Privacy Policy:</label><br>
    <textarea id="privacyContent" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($privacyPolicy['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>

<h2>Edit Terms and Conditions</h2>
<form action="queries/components/adminPanel/update_terms.php" method="post">
    <label for="termsContent">Terms and Conditions:</label><br>
    <textarea id="termsContent" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($termsConditions['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>

</body>
</html>
