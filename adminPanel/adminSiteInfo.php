<?php
require_once "dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

$welcomeQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 1");
$welcomeQuery->execute();
$welcomeData = $welcomeQuery->fetch(PDO::FETCH_ASSOC);

$privacyQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 2");
$privacyQuery->execute();
$privacyPolicy = $privacyQuery->fetch(PDO::FETCH_ASSOC);

$termsQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 3");
$privacyQuery->execute();
$termsConditions = $privacyQuery->fetch(PDO::FETCH_ASSOC);

?>

<h2>Edit Welcome Content</h2>
<form action="adminPanel/update_info.php" method="post">
    <label for="content">Welcome Message:</label><br>
    <textarea id="content" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($welcomeData['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>

<h2>Edit Privacy and Policy</h2>
<form action="adminPanel/update_privacy.php" method="post">
    <label for="content">Welcome Message:</label><br>
    <textarea id="content" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($privacyPolicy['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>

<h2>Edit Terms and Conditons</h2>
<form action="adminPanel/update_terms.php" method="post">
    <label for="content">Welcome Message:</label><br>
    <textarea id="content" style="height: 400px;" name="content" rows="5" cols="50" required><?= htmlspecialchars($termsConditions['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>