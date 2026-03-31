<?php
session_start();

session_unset();
session_destroy();

setcookie("ingat_user", "", time() - 3600, "/");
setcookie("ingat_nama", "", time() - 3600, "/");

header("Location: login.php");
exit();
?>