<?php
session_start();
require_once '../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profilePic = $_FILES['profilePic'];


    $profilePicData = null;
    if (is_uploaded_file($profilePic['tmp_name'])) {
        $profilePicData = file_get_contents($profilePic['tmp_name']);
    }

    $dbcon = dbcon($user, $DBpassword);

    $sql_check = "SELECT * FROM users WHERE username = :username OR email = :email";
    $stmt_check = $dbcon->prepare($sql_check);
    $stmt_check->bindParam(':username', $username);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        echo "Username or Email already exists!";
        exit();
    }

    $sql = "INSERT INTO users (Fname, Lname, email, password, username, profilePic) 
            VALUES (:fname, :lname, :email, :password, :username, :profilePic)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':profilePic', $profilePicData, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        $_SESSION['userID'] = $dbcon->lastInsertId();
        $_SESSION['username'] = $username;

     header("Refresh: 1; url=../index.php");
     echo "Registration successful! Redirecting...";
 } else {
     echo "Error: Could not register user.";
 }
}
?>
