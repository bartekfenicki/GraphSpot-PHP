<?php 
require_once "queries/dbcon.php";
require_once "queries/pages/settingsQuery.php";

$dbCon = dbCon($user, $DBpassword);
$settingsQueries = new settingsQueries($dbCon);

$userID = $_SESSION['userID'];
$privacyPolicy = $settingsQueries->getPrivacyPolicy();
$termsConditions = $settingsQueries->getTermsConditions();
$bio = $settingsQueries->getUserBio($userID);
$profilePic = $settingsQueries->getProfilePic($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="style/pageStyles/settingsStyle.css">
</head>

<body>
    
 <!-- Tabs -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'user')" id="defaultOpen">Profile Settings</button>
        <button class="tablinks" onclick="openTab(event, 'privacy')">Privacy</button>
        <button class="tablinks" onclick="openTab(event, 'terms')">Terms</button>
    </div>


    <div id="user" class="tabcontent">
            <h2>Update Bio</h2>
                <form action="queries/components/settings/updateUserBio.php" method="post">
                    <textarea id="userBio" name="userBio" placeholder="Write your bio here..." rows="5" cols="40"><?= htmlspecialchars($bio['userBio']) ?></textarea>
                    <br>
                    <button class="settings-btn" type="submit">Save Bio</button>
                </form>

            <h2>Change Profile Picture</h2>
                <div>
                <?php if (!empty($profilePic['profilePic'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($profilePic['profilePic']) ?>" alt="Profile Picture">
                <?php else: ?>
                    <p>No profile picture set.</p>
                <?php endif; ?>
                </div>

                <form action="queries/components/settings/updateProfilePicture.php" method="post" enctype="multipart/form-data">
                    <label for="profilePicture">Upload New Profile Picture:</label>
                    <input type="file" name="profilePicture" id="profilePicture" accept="image/*">
                    <br>
                    <button class="settings-btn" type="submit">Upload</button>
                </form>

                <form action="queries/components/settings/deleteProfilePicture.php" method="post">
                    <button class="settings-btn" type="submit" style="color: red;">Remove Profile Picture</button>
                </form>

                <h2>Delete User</h2>
                <form action="queries/components/settings/deleteUser.php" method="post" onsubmit="return confirm('Are you sure you want to delete your profile? This action cannot be undone.')">
                    <button class="settings-btn" type="submit" style="color: red;">Delete Account</button>
                </form>
            
                
    </div>

    <div id="privacy" class="tabcontent">
        <div class="text"><?= nl2br(htmlspecialchars($privacyPolicy['content'])) ?></div>
    </div>

     <div id="terms" class="tabcontent">
        <div class="text"><?= nl2br(htmlspecialchars($termsConditions['content'])) ?></div>
    </div>

    <script src="scripts/pages/settings.js"></script> 
</body>
</html>