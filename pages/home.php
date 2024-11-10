<?php
// DB connection
require_once "dbcon.php";
$dbCon = dbCon($user, $DBpassword);


$query = $dbCon->prepare("SELECT profilePic FROM Users WHERE userID = :userID");
$query->bindParam(':userID', $_SESSION['userID']); 
$query->execute();
$profilePic = $query->fetch(PDO::FETCH_ASSOC);

if ($profilePic && !empty($profilePic['profilePic'])) {
    $profilePicture = 'data:image/png;base64,' . base64_encode($profilePic['profilePic']);
} else {
    echo "no image found";
}
?>

<body>
    <div class="user-info">
        <?php if (isset($_SESSION['userID'])): ?>
            <p><?= htmlspecialchars($_SESSION['username']) ?></p>
            <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-pic">
            <form action="registeration/logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        <?php else: ?>
            <p>You are not logged in.</p>
            <form action="registeration/logout.php" method="get">
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
            <!-- posts display -->
    <?php include 'posts/postDisplay.php'; ?>
    
</body>

<style>
    .user-info {
        position: absolute; 
        top: 10px; 
        right: 10px; 
        display: flex; 
        align-items: center;
        gap: 5px;
    }

    .profile-pic {
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
        margin-right: 10px; 
    }
</style>

