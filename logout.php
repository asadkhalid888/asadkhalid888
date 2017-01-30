<?php
session_start();

setcookie("user_name", "");
setcookie("user_id", ""); 
setcookie("is_login", "0");

session_destroy();

$redirectUrl = 'index.php?page=log';
header('Location: '.$redirectUrl);




