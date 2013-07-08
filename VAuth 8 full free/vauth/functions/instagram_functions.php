<?php

if( ! class_exists( 'InFunctions' ) )	{

	class InFunctions extends VAuthFunctions {
	
		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['prefix'] = 'in';
			$oauth['prefix2'] = 'instagram';
			$oauth['disconnect_str'] 	=	$oauth['prefix']."_username='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
			
			$oauth['redirect_uri']		=	$site_url.'/engine/modules/vauth/callback.php';
			$oauth['app_id']			=	$vauth_config['instagram_app_id'];
			$oauth['app_secret']		=	$vauth_config['instagram_app_secret'];
			$oauth['auth_url']			=	'https://api.instagram.com/oauth/authorize/?client_id='.$oauth['app_id'].'&redirect_uri='.$oauth['redirect_uri'].'&response_type=code';
			$oauth['group']				=	$vauth_config['instagram_user_group'];
	
			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения instagram');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения instagram');
		
			return $oauth;
		
		}

		// ** Функция авторизации в instagram
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'instagram';
		
			if (empty($_SESSION['instagram_access_token']) and empty($_SESSION['uid']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
			}
			
			if ( !empty($auth_code) ) {

				$oauth_auth = 'https://api.instagram.com/oauth/access_token';
				
				$datascope = 'client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&grant_type=authorization_code&redirect_uri='.$oauth['redirect_uri'].'&code='.$auth_code;
			
				$userinfo = json_decode($this->post_curl($oauth_auth,$datascope), FALSE); // * Плучаем секретный хэшкод	
			
				if (	!empty($userinfo->access_token) ) {
					
					$_SESSION['instagram_access_token']	=	$userinfo->access_token;
					$_SESSION['uid']	=	$userinfo->user->id;

				} else die($vauth_text['in_token_error']);
			
			}
			
			return $oauth;
		}

		// ** Функция получения информации пользователя из Facebook
		function get_oauth_info($oauth) {
		
			global $vauth_text;
			global $db;
			global $site_url;
			
			if (!empty($_SESSION['instagram_access_token'])) $oauth['access_token'] = $_SESSION['instagram_access_token'];
			if (!empty($_SESSION['uid'])) $oauth['uid'] = $_SESSION['uid'];
			
			$oauth_info		=	json_decode($this->vauth_get_contents('https://api.instagram.com/v1/users/'.$oauth['uid'].'/?access_token='.$oauth['access_token']), FALSE); //Получаем информцию о пользователе

			$oauth['uid']		=	$this->conv_it($oauth_info->data->id);
			if (!is_numeric($oauth['uid'])) { header('Location: '.$site_url); die(); }

		
			$oauth['bio']		=	$this->conv_it($oauth_info->data->bio);
			$oauth['nick']		=	$this->conv_it($oauth_info->data->username);
			$oauth['in_username']=	$oauth['nick'];
			$oauth['email']		=	$oauth['uid'].'@instagram.com'; //Мыло
			$oauth['avatar']	=	$this->conv_it($oauth_info->data->profile_picture);
			$oauth['fullname']	=	$this->conv_it($oauth_info->data->full_name);
			$oauth['bio']		=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['bio'] ) ) ) );	
			
			return $oauth;
		}
	
	}
	
}

$vauth_api = new InFunctions ();			
	
?>