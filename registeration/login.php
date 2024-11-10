<?php
session_start();
require_once '../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $dbcon = dbcon($user, $DBpassword);

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];

            // Redirect after a short delay
            header("Refresh: 1; url=../index.php");
            echo "Login successful! Redirecting...";
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

