<?php
session_set_cookie_params([
    'secure' => true, 
    'httponly' => true, 
    'samesite' => 'Strict', 
]);
session_start();
require_once '../dbcon.php';

$ipAddress = $_SERVER['REMOTE_ADDR'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $recaptchaSecret = "6LfT6ZkqAAAAAE2I3mIl-YuYh1GEvUL2bHiRnKzt";
    $recaptchaVerifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    $recaptchaResponseData = file_get_contents("$recaptchaVerifyUrl?secret=$recaptchaSecret&response=$recaptchaResponse&remoteip=$ipAddress");
    $recaptchaResult = json_decode($recaptchaResponseData, true);

    if (!$recaptchaResult['success']) {
        echo "CAPTCHA validation failed. Please try again.";
        exit();
    }

    $dbcon = dbcon($user, $DBpassword);


    $sql = "SELECT * FROM Users WHERE username = :username";
    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            if ($user['isBanned']) {
                echo "Your account is banned.";
                exit();
            }

            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['userRole'] = $user['userRole'];
            echo "Login successful!";
            header("Location: ../../index.php?page=home");
            exit();
        } else {
            recordFailedLogin($dbcon, $ipAddress); 
            echo "Incorrect password!";
        }
    } else {
        recordFailedLogin($dbcon, $ipAddress); 
        echo "User not found!";
    }
}

function recordFailedLogin($dbcon, $ipAddress) {
    $stmt = $dbcon->prepare("INSERT INTO failed_logins (ip_address, attempt_time) VALUES (:ip, NOW())");
    $stmt->bindParam(':ip', $ipAddress);
    $stmt->execute();
}
