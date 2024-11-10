<?php require_once "dbcon.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start</title>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://use.typekit.net/mkl4slp.css">
</head>

<?php
//mysqli_real_escape_string_  recaptcha docs   trim()  htmlspecialchars() <input type="token"name="token" value=""
$dbCon = dbCon($user, $DBpassword);
$query = $dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1 LIMIT 1");
$query->execute();
$logoData = $query->fetch(PDO::FETCH_ASSOC);

$logoPath = $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';

// Fetch the welcome message
$welcomeQuery = $dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 1");
$welcomeQuery->execute();
$welcomeData = $welcomeQuery->fetch(PDO::FETCH_ASSOC);

?>

<body>

<div class="container-fluid">
    <div class="welcome-info">
        <div class="logo">
                <img src="<?= $logoPath ?>" alt="Logo">
        </div>
        <div class="welcome">
            <h3>Welcome to</h3>
            <h1 class="graphspot">GraphSpot!</h1>
        </div>
            <div class="text"><?= nl2br(htmlspecialchars($welcomeData['content'])) ?></div>
    </div>
    <div class="half-container">
        <div class="form-container">
            <div class="tab">
                <button id="loginTab" class="active" onclick="showLogin()">Login</button>
                <button id="registerTab" onclick="showRegister()">Register</button>
            </div>
            <div id="loginForm" class="form">
                <form action="registeration/login.php" method="post">
                    <h2>Login</h2>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
                <div class="graph">
                    <img src="assets/images/logPage.png" alt="graph">
                </div>
            </div>
            <div id="registerForm" class="form" style="display: none;">
                <form action="registeration/register.php" method="post" enctype="multipart/form-data">
                    <h2>Register</h2>
                    <input type="text" name="fname" placeholder="First Name" required>
                    <input type="text" name="lname" placeholder="Last Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="file" name="profilePic" accept="image/*">
                    <button type="submit">Register</button>
                </form>
                <div class="graph">
                    <img src="assets/images/logPage2.png" alt="graph">
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    function showLogin() {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('loginTab').classList.add('active');
        document.getElementById('registerTab').classList.remove('active');
    }

    function showRegister() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('registerTab').classList.add('active');
        document.getElementById('loginTab').classList.remove('active');
    }

    // Show the login form by default
    document.addEventListener('DOMContentLoaded', function() {
        showLogin();
    });
</script>

<style scoped>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E6E0E9;
        }

        .container-fluid {
            display: flex;
            height: 100vh; 
        }

        .half-container {
            width: 50%;
            display: flex;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .logo {
            display: block;
            margin: 20px;
        }
        .logo img {
            width: 40px;
            height: auto;
        }
        .welcome-info {
            width: 50%;
            display: block;
            
        }
        .welcome {
            display: flex;
            gap: 10px;
           justify-content: center;
           align-items: baseline;
        }
        .text {
            font-size: 16px;
            width: 500px;
            text-align: justify;
            margin-left: auto;
            margin-right: auto;
        }
        .graphspot{
            font-family: "westgate", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 100px; 
            color: #662483;
        }

        .welcome-info p {
            font-size: 1.2em;
            margin-bottom: 30px;
            width: 400px;
            margin-right: auto;
            margin-left: auto;
        }

        .form-container {
            background-color: white; /* Change background color for forms */
            color: #333; /* Text color for forms */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 400px; /* Fixed width for form box */
            transition: all 0.3s ease;
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #662483;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            border-bottom: 2px solid #662483; 
        }

        .tab {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .tab button {
            padding: 10px;
            border: none;
            background: none;
            color: #662483;
            font-size: 1em;
            cursor: pointer;
            transition: border-bottom 0.3s;
        }

        .tab button.active {
            border-bottom: 2px solid #662483; /* Active tab underline */
        }
        .graph {
            position: absolute;
            bottom: 30px;
        }
        .graph img {
           width: 100px;
           height: auto;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .half-container {
                width: 100%;
                padding: 10px;
            }

            .form-container {
                width: 90%; /* Allow more space on smaller screens */
            }
        }
    </style>