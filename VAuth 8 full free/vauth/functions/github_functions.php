<?php

if( ! class_exists( 'GhFunctions' ) )	{

	class GhFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['prefix'] = 'gh';
			$oauth['prefix2'] = 'github';
			$oauth['disconnect_str'] 	=	$oauth['prefix']."_username='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";

			$oauth['app_id'] = $vauth_config['github_app_id'];
			$oauth['app_secret'] = $vauth_config['github_app_secret'];
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/callback.php';
			$oauth['auth_url'] = 'https://github.com/login/oauth/authorize?client_id='.$oauth['app_id'].'&redirect_uri='.$oauth['redirect_uri'];
			
			$oauth['group']				=	$vauth_config['github_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения Github');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения Github');
			
			return $oauth;
			
		}	
	
		// ** Функция авторизации в instagram
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'github';
		
			if (empty($oauth['access_token']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
				
				$oauth_auth = 'https://github.com/login/oauth/access_token';
				
				$datascope = 'client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&code='.$auth_code;
			
				$userinfo = $this->post_curl($oauth_auth,$datascope); // * Плучаем секретный хэшкод	
				
				parse_str($userinfo);
				
				if (	!empty($access_token) ) {
					
					$_SESSION['github_access_token']	=	$access_token;
					$oauth['access_token']	=	$access_token;
	
				} else die($vauth_text['gh_token_error']);
			
			}
			
			return $oauth;
		}

		// ** Функция получения информации пользователя из Facebook
		function get_oauth_info($oauth) {
		
			global $vauth_text;
			global $db;
			global $site_url;
			
			$oauth['access_token'] = $_SESSION['github_access_token'];
			
			$oauth_info		=	json_decode($this->vauth_get_contents('https://api.github.com/user?access_token='.$oauth['access_token']), FALSE); //Получаем информцию о пользователе
			
			$oauth['uid']		=	$this->conv_it($oauth_info->id);
			if (!is_numeric($oauth['uid'])) { header('Location: '.$site_url); die(11); }
			$oauth['nick']		=	$this->conv_it($oauth_info->login);
			$oauth['gh_username']		=	$oauth['nick'];
			$oauth['fullname']	=	$this->conv_it($oauth_info->login);
			$oauth['email']	=	$oauth['uid'].'@github.com'; //Мыло
			$oauth['avatar']	=	$this->conv_it($oauth_info->avatar_url);
			
			
			return $oauth;
		}
	
	}
	
}

$vauth_api = new GhFunctions ();			
	
?>