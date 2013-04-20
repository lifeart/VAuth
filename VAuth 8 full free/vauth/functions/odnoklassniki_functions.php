<?php

if( ! class_exists( 'OdFunctions' ) )	{

	class OdFunctions extends VAuthFunctions {
	

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['prefix'] = 'od';
			$oauth['prefix2'] = 'odnoklassniki';
			$oauth['disconnect_str'] 	=	"updtime='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";			
			$oauth['app_id']			=	$vauth_config['odnoklassniki_app_id'];
			$oauth['pub_key']			=	$vauth_config['odnoklassniki_pub_key'];
			$oauth['app_secret']		=	$vauth_config['odnoklassniki_app_secret'];
			$oauth['redirect_uri']		=	$site_url.'/engine/modules/vauth/callback.php';
			$oauth['auth_url']			=	'http://www.odnoklassniki.ru/oauth/authorize?client_id='.$oauth['app_id'].'&response_type=code&scope=VALUABLE ACCESS&redirect_uri='.$oauth['redirect_uri'];
		
			$oauth['group']				=	$vauth_config['odnoklassniki_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения instagram');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения instagram');
			
			return $oauth;
			
		}	
	
		function get_oauth_info($oauth) {
			
			global $vauth_text;
			global $db;
			global $site_url;
			
			$sig_array = array(
			
				'application_key='.$oauth['pub_key'],
				'method=users.getCurrentUser',

			);
			
			sort($sig_array);
			
			$sig = strtolower(md5(join('', $sig_array).md5($oauth['access_token'].$oauth['app_secret'])));
			
			$oauth_url = 'http://api.odnoklassniki.ru/fb.do?access_token=' . $oauth['access_token'] . '&application_key=' . $oauth['pub_key'] . '&method=users.getCurrentUser&sig='.$sig;
	
			
			$oauth_info		=	json_decode($this->vauth_get_contents($oauth_url), FALSE); //Получаем информцию о пользователе
			
			
			
			$oauth['uid']			=	$this->conv_it($oauth_info->uid);

			
			if (!is_numeric($oauth['uid'])) { header('Location: '.$site_url); die(); }
			
			$oauth['sex']			=	$this->conv_it($oauth_info->gender);
			$oauth['nick']			=	$this->conv_it($oauth_info->name);
			$oauth['bdate']		=	$this->conv_it($oauth_info->birthday);
			$oauth['email']		=	$oauth['uid'].'@odnoklassniki.ru';
			$oauth['avatar']		=	$this->conv_it($oauth_info->pic_2);
			$oauth['fullname']		=	$this->conv_it($oauth_info->name);
			$oauth['last_name']	=	$this->conv_it($oauth_info->last_name);
			$oauth['first_name']	=	$this->conv_it($oauth_info->first_name);
			
			if ( !empty($oauth['bdate']) ) {
			
				$bdate = explode('-',$oauth['bdate']);
				$oauth['bdate'] = $bdate[0].'.'.$bdate[1].'.'.$bdate[2];
			
			}
			
			switch(	$oauth['sex']	) {
			
				case 'male'	: $oauth['sex'] = $vauth_text[4];	break;
				case 'female'	: $oauth['sex'] = $vauth_text[5];	break;
			
			}
			
			return $oauth;
			#$oauth['friends']	=	$this->get_od_friends($oauth);
		}		
	
		// ** Функция авторизации в одноклассниках
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'odnoklassniki';
		
			if (empty($oauth['access_token']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
				
				$oauth_auth = 'http://api.odnoklassniki.ru/oauth/token.do';
				
				$datascope = 'code='.$auth_code.'&redirect_uri='.$oauth['redirect_uri'].'&grant_type=authorization_code&client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'];
			
		
			
				$userinfo = json_decode($this->post_curl($oauth_auth,$datascope), FALSE); // * Плучаем секретный хэшкод	
				
				if (	!empty($userinfo->access_token) ) {
				
					$access_token	= $userinfo->access_token;
					
					$_SESSION['odnoklassniki_access_token']	=	$access_token;
				
					$oauth['access_token'] = $access_token;
					
					
				} else die($vauth_text['od_token_error']);
			
			}
			
			return $oauth;
		}			
	
		// ** Функция получения полного токена авторизации пользователя Одноклассников
		function full_auth_on_od($oauth) {
			
				$sig_array = array(
			
					'application_key='.$oauth['pub_key'],
					'method=auth.loginByToken',
					'uid='.$oauth['uid'],

				);
				
				sort($sig_array);
				
				$sig = strtolower(md5(join('', $sig_array).md5($oauth['access_token'].$oauth['app_secret'])));
				
				$oauth_url = 'http://api.odnoklassniki.ru/fb.do?uid='.$oauth['uid'].'&access_token=' . $oauth['access_token'] . '&application_key=' . $oauth['pub_key'] . '&method=auth.loginByToken&sig='.$sig;
				
				$oauth_info		=	json_decode($this->vauth_get_contents($oauth_url), FALSE); //Получаем информцию о пользователе
					
				print_r($oauth_info);die;
			
			
				return $fs;
		}		
	
	}
}

$vauth_api = new OdFunctions ();			
	
?>