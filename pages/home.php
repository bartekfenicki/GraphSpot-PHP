<?php
require_once "queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);
?>
<head>
<meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="style/pageStyles/homeStyle.css">
</head>
<body>
<!-- Tabs -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'General')" id="defaultOpen">General</button>
        <button class="tablinks" onclick="openTab(event, 'Pinned')">Pinned</button>
    </div>

    <!-- Tab content -->
    <div id="General" class="tabcontent">
        <?php include 'components/posts/postDisplay.php'; ?>
    </div>

    <div id="Pinned" class="tabcontent">
        <?php include 'components/posts/pinnedPosts.php'; ?>
    </div>

    <script src="scripts/pages/home.js"></script> 
</body>
</html>




