<?php 
$dbCon = dbCon($user, $DBpassword);

// Fetch the welcome message
$privacyQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 2");
$privacyQuery->execute();
$privacyPolicy = $privacyQuery->fetch(PDO::FETCH_ASSOC);

$termsQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 3");
$termsQuery->execute();
$termsConditions = $termsQuery->fetch(PDO::FETCH_ASSOC);
?>
<body>
    
 <!-- Tab Navigation -->
 <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'general')" id="defaultOpen">General</button>
        <button class="tablinks" onclick="openTab(event, 'user')">User</button>
        <button class="tablinks" onclick="openTab(event, 'privacy')">Privacy</button>
        <button class="tablinks" onclick="openTab(event, 'terms')">Terms</button>
    </div>

    <!-- Posts Tab Content -->
    <div id="general" class="tabcontent">
       
    </div>

    <!-- Users Tab Content -->
    <div id="user" class="tabcontent">
        
    </div>

    <!-- Style Tab Content -->
    <div id="privacy" class="tabcontent">
        <div class="text"><?= nl2br(htmlspecialchars($privacyPolicy['content'])) ?></div>
    </div>

     <!-- Site info Tab Content -->
     <div id="terms" class="tabcontent">
        <div class="text"><?= nl2br(htmlspecialchars($termsConditions['content'])) ?></div>
    </div>

</body>

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