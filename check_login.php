<?php
session_start();


if(isset($_COOKIE['is_login']) && $_COOKIE['is_login'] == '1'){
	$redirectUrl = $site_ur.'index.php';
	header('Location: '.$redirectUrl);

} elseif(isset($_SESSION['is_login']) && $_SESSION['is_login'] == '1'){
	$redirectUrl = $site_ur.'index.php';
	header('Location: '.$redirectUrl);
}  

