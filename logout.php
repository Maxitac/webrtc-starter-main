<?php
session_start();
session_unset();
session_destroy();
header("Location: https://192.168.100.138:8181/login.php");
exit();
?>
