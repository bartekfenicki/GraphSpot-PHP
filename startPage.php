<?php 
require_once "queries/dbcon.php";
require_once "header.php";


session_start();

if (isset($_SESSION['userID'])) {
    header("Location: index.php?page=home"); // Redirect logged-in users
    exit();
}

function generateToken() {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

$token = generateToken();

$dbCon = dbCon($user, $DBpassword);
$query = $dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1 LIMIT 1");
$query->execute();
$logoData = $query->fetch(PDO::FETCH_ASSOC);

$logoPath = $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';

$welcomeQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 1");
$welcomeQuery->execute();
$welcomeData = $welcomeQuery->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to GraphSpot! Share and Explore your StreetArt and Graffiti ideas.">
    <title>Welcome | GraphSpot</title>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" style="width: 100%; height: auto;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="style/pageStyles/startPageStyle.css">
</head>
<body>
    <div class="container-fluid">
        <div class="welcome-info">
            <div class="logo">
                <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo">
            </div>
            <div class="welcome">
                <h3>Welcome to</h3>
                <h1 class="graphspot">GraphSpot!</h1>
            </div>
            <div class="text"><?= nl2br(htmlspecialchars($welcomeData['content'] ?? 'Welcome to our site!')) ?></div>
        </div>
        <div class="half-container">
            <div class="form-container">
                <div class="tab">
                    <button id="loginTab" class="active" onclick="showLogin()">Login</button>
                    <button id="registerTab" onclick="showRegister()">Register</button>
                </div>
                <div id="loginForm" class="form">
                    <form action="queries/registeration/login.php" method="post">
                        <h2>Login</h2>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <div class="g-recaptcha" data-sitekey="6LfT6ZkqAAAAAOPpq3_ejGbya1xosy3JFVc6TaSU"></div>
                        <button type="submit">Login</button>
                    </form>
                </div>
                <div id="registerForm" class="form" style="display: none;">
                    <form action="queries/registeration/register.php" method="post" enctype="multipart/form-data">
                        <h2>Register</h2>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <input type="text" name="fname" placeholder="First Name" required>
                        <input type="text" name="lname" placeholder="Last Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="file" name="profilePic" accept="image/*">
                        <div class="g-recaptcha" data-sitekey="6LfT6ZkqAAAAAOPpq3_ejGbya1xosy3JFVc6TaSU"></div>
                        <button type="submit">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts/pages/startPage.js"></script>
</body>
</html>

