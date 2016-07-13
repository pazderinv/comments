<?php 

	define('WEBIN4', true);
	
	define('SITE', "http://comments.loc/");
	
	session_start();
	//echo session_name();
	//echo session_id();
	//unset($_SESSION['captcha_keystring']);
	//$_SESSION['captcha_keystring'] = "123465789";
	
	require_once "functions/functions.php";
		
	
		
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comments</title>
	<link rel="stylesheet" href="libs/font-awesome-4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/jquery.modal-window.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="js/ajaxupload.js"></script>
	<script>
		$(function(){
			
		});
	</script>
	<style>
	
	</style>
</head>
<body>
	<div class="image_wrap">
	</div> 
</body>
</html>