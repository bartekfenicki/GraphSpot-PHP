<?php
require_once "queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);

if (!isset($_SESSION['userID']) || $_SESSION['userRole'] !== 'admin') {
    header("Location: ../index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style/pageStyles/adminStyle.css">
</head>

<body>
    <!-- Tabs-->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Posts')" id="defaultOpen">Posts</button>
        <button class="tablinks" onclick="openTab(event, 'Users')">Users</button>
        <button class="tablinks" onclick="openTab(event, 'Styling')">Style</button>
        <button class="tablinks" onclick="openTab(event, 'SiteInfo')">Site Information</button>
    </div>

    <!-- Posts Tab -->
    <div id="Posts" class="tabcontent">
        <?php include "components/adminPanel/adminPosts.php";?>
    </div>

    <!-- Users Tab -->
    <div id="Users" class="tabcontent">
        <?php include "components/adminPanel/adminUsers.php";?>
    </div>

    <!-- Style Tab -->
    <div id="Styling" class="tabcontent">
        <?php include "components/adminPanel/adminStyling.php";?>
    </div>

     <!-- Site info Tab -->
     <div id="SiteInfo" class="tabcontent">
        <?php include "components/adminPanel/adminSiteInfo.php";?>
    </div>

    <script src="scripts/pages/adminPanel.js"></script> 
</body>
</html>
