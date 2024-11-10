<?php 
require_once "dbcon.php";
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: startPage.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <link rel="stylesheet" href="https://use.typekit.net/mkl4slp.css">
</head>

<body>
<?php
//mysqli_real_escape_string_  recaptcha docs   trim()  htmlspecialchars() <input type="token"name="token" value=""
$dbCon = dbCon($user, $DBpassword);
$query = $dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1 LIMIT 1");
$query->execute();
$logoData = $query->fetch(PDO::FETCH_ASSOC);

$logoPath = $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';

// Fetch the active style settings
$styleQuery = $dbCon->prepare("SELECT sidemenu_background, body_background FROM styles WHERE is_active = 1");
$styleQuery->execute();
$styleData = $styleQuery->fetch(PDO::FETCH_ASSOC);

$sidemenuBackground = $styleData['sidemenu_background'];
$bodyBackground = $styleData['body_background'];
?>


    <!-- Side Menu -->
    <div class="sidemenu">
        <div class="logo">
            <img src="<?= $logoPath ?>" alt="Logo">
        </div>
        <div class="menu-item"><a href="?page=home">Home</a></div>
        <div class="menu-item"><a href="?page=browse">Browse</a></div>
        <div class="menu-item"><a href="?page=profile">Profile</a></div>
        <div class="menu-item"><a href="?page=saved">Saved</a></div>
        <div class="menu-item"><a href="?page=settings">Settings</a></div>
        <div class="menu-item"><a href="?page=adminPanel">Admin Panel</a></div>
        <button class="addMedia" onclick="openModal()">+</button>
    </div>



    <!-- Include the Popup Form -->
    <?php include 'posts/addPostForm.php'; ?>

    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] === 'postadded') {
            echo "<p>Post added successfully!</p>";
        } elseif ($_GET['status'] === 'posterror') {
            echo "<p>Error adding post. Please try again.</p>";
        }
    }
?>

    <!-- Page Content -->
    <div class="main">
        <?php
  
            if (isset($_GET['page'])) {
                $page = $_GET['page'];

                $allowedPages = ['home', 'browse', 'profile', 'saved', 'settings', 'adminPanel', 'singlePost'];

                if (in_array($page, $allowedPages)) {

                    include "pages/{$page}.php";

                } else {
                    include "404.php";
                }
            } else {
   
                include "pages/home.php";
            }
        ?>
    </div>
</body>

<script>
    function openModal() {
        document.getElementById("addPostModal").style.display = "block";
    }
    function closeModal() {
        document.getElementById("addPostModal").style.display = "none";
    }

    // Close the modal when clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById("addPostModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

</script>

<style>
    body {
        background-color: <?= htmlspecialchars($bodyBackground); ?>;
    }
.sidemenu {
    position: fixed;
    background: <?= htmlspecialchars($sidemenuBackground); ?>; 
    width: 20%;
    height: 100%;
    display: block;
    justify-content: center;
    top: 0;
}
.logo {
    width:50px;
    margin: 20px auto 20px auto;
}
.logo img {
    width: 50px;
    height: auto;
}
.menu-item {
    padding: 15px;
    width: 100%;
    display: flex;
    justify-content: center;
    border-bottom: 1px solid #662483;
}
.menu-item a {
    font-size: 22px;
    font-family: "westgate", sans-serif;
    font-weight: 400;
    font-style: normal;
    color: #662483;
}
.addMedia {
    position: absolute;
    bottom: 0;
    left: 40%;
    font-size: 20px;
    margin-bottom: 30px;
    display: block;
    padding: 15px 20px;
    border-radius: 30px;
    border-color: #662483;
    background-color: #ffffff00;
}
.main {
    margin-left: 20%;
}

</style>
</html>