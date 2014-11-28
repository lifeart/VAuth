<?php
	
	#header('Access-Control-Allow-Origin: *');
	// ** Запускаем сессию ** //
	session_start();
	
	if (!empty($_GET['ref'])) $_SESSION['referrer'] = $_GET['ref'];
	
	// ** Получаем настройки скрипта ** //
	include_once("settings/script_settings.php");
	
	$new_user = '';
	
	if (empty($_POST['regform'])) {
	
		// ** Получаем имя сайта для авторизации ** //
		$vauth_api->go_auth($auth_site);
		
		// ** Логин и сопряжение аккантов пользователя ** //
		$new_user = $vauth_api->go_login($auth_site,$ac_connect);
	
	}
	

	// ** Регистрация нового пользователя ** //
	$vauth_api->go_register($auth_site,$new_user);
	
?>