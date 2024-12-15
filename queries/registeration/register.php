<?php
session_start();
require_once '../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['token'], $_POST['token'] ?? '')) {
        die("Invalid CSRF token.");
    }

    $fname = htmlspecialchars(trim($_POST['fname']));
    $lname = htmlspecialchars(trim($_POST['lname']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $profilePic = $_FILES['profilePic'];

    if (!$email) {
        die("Invalid email address.");
    }

    $dbcon = dbcon($user, $DBpassword);

    $sql_check = "SELECT * FROM Users WHERE username = :username OR email = :email";
    $stmt_check = $dbcon->prepare($sql_check);
    $stmt_check->bindParam(':username', $username);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        die("Username or Email already exists.");
    }

    $profilePicData = null;
    if (is_uploaded_file($profilePic['tmp_name'])) {
        $validTypes = ['image/jpeg', 'image/png'];
        if (!in_array(mime_content_type($profilePic['tmp_name']), $validTypes)) {
            die("Invalid file type.");
        }
        $profilePicData = file_get_contents($profilePic['tmp_name']);
    }

    $defaultRole = 'user';
    $sql = "INSERT INTO Users (Fname, Lname, email, password, username, profilePic, userRole) 
            VALUES (:fname, :lname, :email, :password, :username, :profilePic, :userRole)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':profilePic', $profilePicData, PDO::PARAM_LOB);
    $stmt->bindParam(':userRole', $defaultRole);

    $usernameRegex = '/^[a-zA-Z0-9_]{3,20}$/';
if (!preg_match($usernameRegex, $username)) {
    echo "Invalid username. Only alphanumeric characters and underscores are allowed (3-20 characters).";
    exit();
}
    if ($stmt->execute()) {
        session_regenerate_id(true);
        $_SESSION['userID'] = $dbcon->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['userRole'] = $defaultRole;
        header("Location: ../../index.php?page=home");
        exit();
    } else {
        die("Error: Could not register user.");
    }
}
?>
