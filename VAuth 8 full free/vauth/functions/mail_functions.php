<?php

if( ! class_exists( 'MaFunctions' ) )	{

	class MaFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['prefix'] = 'ma';
			$oauth['prefix2'] = 'mail';
			$oauth['disconnect_str'] 	=	"updtime='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";			
			
			$oauth['pub_key']  = $vauth_config['mail_pub_key'];
			$oauth['app_id'] = $vauth_config['mail_app_id'];
			$oauth['app_secret'] = $vauth_config['mail_app_secret'];
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/callback.php';
			$oauth['auth_url'] = 'https://connect.mail.ru/oauth/authorize?client_id='.$oauth['app_id'].'&response_type=code&redirect_uri='.$oauth['redirect_uri'];
			$oauth['group']	=	$vauth_config['mail_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения Facebook');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения Facebook');
			
			return $oauth;
			
		}
		
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'mail';
		
			if (empty($oauth['access_token']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
			
				$oauth_auth = 'https://connect.mail.ru/oauth/token';
				
				$datascope = 'redirect_uri='.$oauth['redirect_uri'].'&grant_type=authorization_code&client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&code='.$auth_code;
			
				$userinfo = json_decode($this->post_curl($oauth_auth,$datascope), FALSE);
				
				
				if (!empty($userinfo->access_token) ) {

					
					$_SESSION['mail_access_token']	= $userinfo->access_token;
					$_SESSION['mail_uid']	= $userinfo->x_mailru_vid;
					$oauth['access_token']	=	$userinfo->access_token;
					$oauth['uid']	=	$userinfo->x_mailru_vid;
	
				} else die($vauth_text['ma_token_error']);
			
			}
			
			return $oauth;
		}

		function get_oauth_info($oauth) {
			
			global $vauth_text;
			global $db;
			global $site_url;
			
			$oauth['access_token'] = $_SESSION['mail_access_token'];
			$oauth['uid'] = $_SESSION['mail_uid'];
			
			$sig_array = array(
			
				'method=users.getInfo',
				'uids='.$oauth['uid'],
				'secure=1',
				'app_id='.$oauth['app_id'],
				'session_key='.$oauth['access_token'],
			);
			
			sort($sig_array);
			
			$sig = md5(join('', $sig_array).$oauth['app_secret']);

			$info	=	json_decode($this->vauth_get_contents('http://www.appsmail.ru/platform/api?'.join('&', $sig_array).'&sig='.$sig), FALSE); //Получаем информцию о пользователе
				
			$info = $info[0];
			
			$oauth['uid']		=	$this->conv_it($info->uid);
			
			if (empty($oauth['uid'])) { header('Location: '.$site_url); die(); }
			
			$oauth['firstname'] =	$this->conv_it($info->first_name);
			$oauth['lastname'] =	$this->conv_it($info->last_name);
			$oauth['fullname'] =	$oauth['firstname'].' '.$oauth['lastname'];
			$oauth['sex'] =	$this->conv_it($info->sex);
			$oauth['nick'] = $this->conv_it($info->nick);
			$oauth['link'] = $this->conv_it($info->link);
			$oauth['bdate'] = $this->conv_it($info->birthday);
			$oauth['avatar']	=	$this->conv_it($info->pic_big);
			
			
			switch(	$oauth['sex']	) {
			
				case 0	: $oauth['sex'] = $vauth_text[4];	break;
				case 1	: $oauth['sex'] = $vauth_text[5];	break;
			
			}
			

			$oauth['email']	=	$this->conv_it($info->email);
			
			if (!empty($info->location->country->name)) $oauth['city'] = $this->conv_it($info->location->country->name);
			
			if (!empty($info->location->city->name)) $oauth['country'] = $this->conv_it($info->location->city->name);
			
			
			if (!empty($oauth['country']) and !empty($oauth['city'])) $oauth['city'] = $oauth['city'].', '.$oauth['country'];
			elseif (!empty($oauth['country'])) $oauth['city'] = $oauth['country'];
			elseif (empty($oauth['city'])) $oauth['city'] = '';
			#$oauth['friends']	=	$this->get_oauth_friends($oauth);
			
			return $oauth;
		}		
		
	}
}

$vauth_api = new MaFunctions ();			
	
?>