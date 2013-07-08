<?php

if( ! class_exists( 'GoFunctions' ) )	{

	class GoFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			
			$oauth['prefix'] = 'go';
			$oauth['prefix2'] = 'google';
			$oauth['disconnect_str'] 	=	"updtime='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
			
			$oauth['redirect_uri']		=	$site_url.'/engine/modules/vauth/callback.php';
			$oauth['app_id']			=	$vauth_config['google_app_id'];
			$oauth['app_secret']		=	$vauth_config['google_app_secret'];
			$oauth['auth_url']			=	'https://accounts.google.com/o/oauth2/auth?scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&state=%2Fprofile&redirect_uri='.$oauth['redirect_uri'].'&response_type=code&client_id='.$oauth['app_id'].'&approval_prompt=auto&access_type=offline';
		
			$oauth['group']				=	$vauth_config['google_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения Google');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения Google');
			
			return $oauth;
			
		}	
	
		// ** Функция авторизации в google+
		function vauth_auth($go) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'google';
		
			if (empty($go['access_token']) and empty($auth_code)) {
				header('Location: '.$go['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
			
				$go_auth = 'https://accounts.google.com/o/oauth2/token';
				
				$datascope = 'code='.$auth_code.'&client_id='.$go['app_id'].'&client_secret='.$go['app_secret'].'&redirect_uri='.$go['redirect_uri'].'&grant_type=authorization_code';
			
				$userinfo = json_decode($this->post_curl($go_auth,$datascope), FALSE); // * Плучаем секретный хэшкод	
				
				if (	!empty($userinfo->access_token) ) {
				
					$access_token	= $userinfo->access_token;
					$id_token	= $userinfo->id_token;
					
					$_SESSION['google_access_token']	=	$access_token;
					$_SESSION['google_id_token']	=	$id_token;
				
					$go['access_token'] = $access_token;
					
				} else die($vauth_text['go_token_error']);
			
			}
			
			return $go;
		}

		function get_oauth_info($go) {
			
			global $vauth_text;
			global $db;
			global $site_url;
			
			$go['access_token'] = $_SESSION['google_access_token'];
			
			$go_info		=	json_decode($this->vauth_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$go['access_token']), FALSE); //Получаем информцию о пользователе
			
			$go['uid']		=	$this->conv_it($go_info->id);
			if (!is_numeric($go['uid'])) { header('Location: '.$site_url); die(); }
			$go['gender']		=	$this->conv_it($go_info->gender);
			$go['email']		=	$this->conv_it($go_info->email);
			$go['avatar']		=	$this->conv_it($go_info->picture);
			
			if ( empty($go['avatar']) ) $go['avatar'] = $site_url . '/engine/modules/vauth/styles/photo.jpg';
			
			$go['firstname']	=	$this->conv_it($go_info->given_name);
			$go['lastname']		=	$this->conv_it($go_info->family_name);
			$go['fullname']		=	$this->conv_it($go_info->name);
			$go['nick']			=	$this->conv_it($go_info->name);
			
			
			switch(	$go['gender']	) {
			
				case 'male'	: $go['sex'] = $vauth_text[4];	break;
				case 'female'	: $go['sex'] = $vauth_text[5];	break;
			
			}
			
			return $go;
		}	

	}

}

$vauth_api = new GoFunctions ();			
	
?>