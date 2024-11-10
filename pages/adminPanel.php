<?php
// DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Materialize CSS Framework -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>

<body>

    <!-- Tab Navigation -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Posts')" id="defaultOpen">Posts</button>
        <button class="tablinks" onclick="openTab(event, 'Users')">Users</button>
        <button class="tablinks" onclick="openTab(event, 'Styling')">Style</button>
        <button class="tablinks" onclick="openTab(event, 'SiteInfo')">Site Information</button>
    </div>

    <!-- Posts Tab Content -->
    <div id="Posts" class="tabcontent">
        <?php include "adminPanel/adminPosts.php";?>
    </div>

    <!-- Users Tab Content -->
    <div id="Users" class="tabcontent">
        <?php include "adminPanel/adminUsers.php";?>
    </div>

    <!-- Style Tab Content -->
    <div id="Styling" class="tabcontent">
        <?php include "adminPanel/adminStyling.php";?>
    </div>

     <!-- Site info Tab Content -->
     <div id="SiteInfo" class="tabcontent">
        <?php include "adminPanel/adminSiteInfo.php";?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>

        //tab switcher  
        function openTab(evt, tabName) {
            const tabcontent = document.getElementsByClassName("tabcontent");
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            const tablinks = document.getElementsByClassName("tablinks");
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Default tab open
        document.getElementById("defaultOpen").click();
    </script>

    <!-- Styling -->
    <style scoped>
        .container {
            display: flex;
            flex-direction: column;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }
        .container div {
            margin: 5px 0;
        }

        /* Tab styles */
        .tab {
            overflow: hidden;
            background-color: #fff;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: center;
        }
        .tab button {
            background-color: inherit;
            border: none;
            cursor: pointer;
            padding: 14px 16px;
            font-size: 16px;
            transition: 0.3s;
        }
        .tab button:hover {
            border-bottom: 2px solid #66248377;
        }
        .tab button.active {
            border-bottom: 2px solid #662483;
        }
        .tabcontent {
            display: none;
            padding: 12px;
        }
    </style>

</body>
</html>
