<?php 
$dir = dirname(__FILE__).'/';
$site_url = 'http://'.$_SERVER['HTTP_HOST'].'/video-survey/';
//$site_url = 'http://j-vids.com/';

//error_reporting( E_ALL );
require_once('db.php');
require_once('model.php');
$model= new Model();


$redirectUrl = 'index.php?page=log&error=1';
if(isset($_POST['action']) && $_POST['action'] == 'login') {
$loginValue='`vs_users_id`';
$loginTableName = 'vs_users';
$loginWhereCondtion='`username` = "'.$_POST['user_name'].'" and `password` = "'.$_POST['user_pass'].'"';

$TableData = $model->selectTableData($loginValue, $loginTableName, $loginWhereCondtion);
	if($TableData!=""){
			setcookie("user_name", $_POST['user_name']);
			setcookie("user_id", $TableData['vs_users_id']); 
			setcookie("is_login", "1");
			
			$_SESSION['user_name']=$_POST['user_name'];
			$_SESSION['user_id']=$TableData['vs_users_id'];
			$_SESSION['is_login']="1";
			
			$redirectUrl = 'index.php?u='.$_POST['user_name'].'&p='.$_POST['user_pass'];
			
			
	} 
}

header('Location: '.$redirectUrl);


		