<?php
$user = "c116fj3im_graphspotdb";
$DBpassword = "123456";

function dbcon($user, $DBpassword) {
    try {
        $dbcon = new PDO('mysql:host=localhost;dbname=c116fj3im_graphspotdb;charset=utf8', $user, $DBpassword);
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbcon;
    } catch (PDOException $err) {
        echo "something went wrong u suck: " . $err->getMessage();
        die();
    }
}
?>