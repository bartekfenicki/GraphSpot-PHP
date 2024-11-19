<?php
// DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);


?>

<body>

<!-- Tab links -->
<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'General')" id="defaultOpen">General</button>
    <button class="tablinks" onclick="openTab(event, 'Pinned')">Pinned</button>
</div>

 

<!-- Tab content -->
<div id="General" class="tabcontent">
         <!-- posts display -->
    <?php include 'posts/postDisplay.php'; ?>

</div>

<div id="Pinned" class="tabcontent">
    <?php include 'posts/pinnedPosts.php'; ?>
</div>

    
</body>

<script>
        function openTab(event, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " active";
        }

        // Automatically open the "Posts" tab on page load
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("defaultOpen").click();
        });
</script>

<style>
     /* Tab Container */
     .tab {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            border-bottom: 1px solid #ddd;
            background-color: white;
            padding: 10px 0;
        }

        .tab button {
            background-color: inherit;
            border: none;
            outline: none;
            padding: 14px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .tab button:hover {
            border-bottom: 2px solid #662483;
        }

        .tab button.active {
            border-bottom: 2px solid #662483;
            font-weight: bold;
        }

        /* Tab Content */
        .tabcontent {
            display: none;
            padding: 20px;
            background-color: white;
        }

</style>

