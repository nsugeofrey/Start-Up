<?php
ob_start();
session_start();
$_SESSION = Array();
session_destroy();
header("location: admin-login.php");
exit;
?>