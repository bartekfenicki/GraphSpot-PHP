<?php 
require_once "queries/indexQuery.php";
require_once "header.php";
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: startPage.php");
    exit();
}

$dbHelper = new domQuery($user, $DBpassword);

$logoPath = $dbHelper->fetchLogoPath();
$styleData = $dbHelper->fetchStyleSettings();
$sidemenuBackground = $styleData['sidemenu_background'];
$bodyBackground = $styleData['body_background'];
$profilePicture = $dbHelper->fetchProfilePicture($_SESSION['userID']);

$page = htmlspecialchars($_GET['page']);

$titles = [
    'home' => 'Home | GraphSpot',
    'browse' => 'Browse Our Collection | GraphSpot',
    'profile' => 'Your Profile | GraphSpot',
    'settings' => 'Settings | GraphSpot',
    'adminPanel' => 'Admin Panel | GraphSpot',
    'singlePost' => 'Post | GraphSpot', 
    'userProfile' => 'Users Profile | GraphSpot', 
    'followers' => 'Followers | GraphSpot', 
    'following' => 'Following | GraphSpot',
];

$descriptions = [
    'home' => 'Discover the latest updates and features on our homepage.',
    'browse' => 'Explore our curated collection of items and posts.',
    'profile' => 'View and edit your user profile.',
    'settings' => 'Manage your account settings.',
    'adminPanel' => 'Admin controls and site settings.',
    'singlePost' => 'See the Post, leave a like or a comment.', 
    'userProfile' => 'View another user´s profile.', 
    'followers' => 'Display your Followers | GraphSpot', 
    'following' => 'Display the users you are Following | GraphSpot',
];

$currentTitle = $titles[$page] ?? 'Page Not Found | Your Site';
$currentDescription = $descriptions[$page] ?? 'The page you are looking for does not exist.';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($currentDescription) ?>">
    <title><?= htmlspecialchars($currentTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" style="width: 100%; height: auto;">
    <style scoped>
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
</style>
</head>

<body>
    <div class="sidemenu">
        <div class="logo">
            <a href="index.php?page=home">
                <img src="<?= $logoPath ?>" alt="Logo">
            </a>
        </div>
        <div class="menu-item"><a href="?page=home">Home</a></div>
        <div class="menu-item"><a href="?page=browse">Browse</a></div>
        <div class="menu-item"><a href="?page=profile">Profile</a></div>
        <div class="menu-item"><a href="?page=settings">Settings</a></div>
        <?php if ($_SESSION['userRole'] === 'admin'): ?>
        <div class="menu-item"><a href="?page=adminPanel">Admin Panel</a></div>
        <?php endif; ?>
            <button class="addMedia" onclick="openModal()">+</button>
        
    </div>

    <!-- Include the Popup Form -->
    <?php include 'components/posts/addPostForm.php'; ?>

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
        $allowedPages = ['home', 'browse', 'profile', 'settings', 'adminPanel', 'singlePost', 'userProfile', 'followers', 'following'];

        
    
        if (in_array($page, $allowedPages)) {
            include "pages/{$page}.php";
        } else {
            include "pages/404.php";
        }
    } else {
        include "pages/home.php";
    }
        ?>
    </div>

    <div class="user-info">
        <?php if (isset($_SESSION['userID'])): ?>
            <?php if (!isset($_GET['page']) || $_GET['page'] !== 'profile'): ?>
                <p><a href="index.php?page=profile"><?= htmlspecialchars($_SESSION['username']) ?></a></p>
                <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-pic">
            <?php endif; ?>
            <form action="queries/registeration/logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        <?php else: ?>
            <p>You are not logged in.</p>
            <form action="queries/registeration/logout.php" method="get">
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
    

    <script src="scripts/index.js"></script> 
</body>
</html>
