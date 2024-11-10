<?php
session_start();
session_unset();
session_destroy();
header("Location: ../startPage.php"); // Redirect to the login page
exit();
?>



 <?php // for the pages that require being logged in
// session_start();
// if (!isset($_SESSION['userID'])) {
//     header("Location: login.html");
//     exit();
// }
?> 