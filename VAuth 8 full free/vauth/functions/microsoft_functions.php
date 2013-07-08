<?php

#$vauth_config['facebook_app_id'];$facebook_app_secret = $vauth_config['facebook_app_secret'];
if (empty($group['microsoft'])) $group['microsoft'] = 4;

if( ! class_exists( 'MsFunctions' ) )	{

	class MsFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['prefix'] = 'ms';
			$oauth['prefix2'] = 'microsoft';
			$oauth['disconnect_str'] 	=	"updtime='', ms_link='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
			
			$oauth['app_id'] = $vauth_config['microsoft_app_id'];
			$oauth['app_secret'] = $vauth_config['microsoft_app_secret'];
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/callback.php';
			$oauth['auth_url'] = 'https://login.live.com/oauth20_authorize.srf?response_type=code&client_id='.$oauth['app_id'].'&redirect_uri='.$oauth['redirect_uri'].'&scope=wl.signin,wl.basic,wl.birthday,wl.photos,wl.emails';

			$oauth['group']				=	$vauth_config['microsoft_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения Facebook');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения Facebook');
			
			return $oauth;
			
		}		
	
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'microsoft';
		
			if (empty($oauth['access_token']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
			
				$oauth_auth = 'https://login.live.com/oauth20_token.srf';
				
				$datascope = 'redirect_uri='.$oauth['redirect_uri'].'&grant_type=authorization_code&client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&code='.$auth_code;
			
				$userinfo = json_decode($this->post_curl($oauth_auth,$datascope), FALSE);
				
				
				if (!empty($userinfo->access_token) ) {
					
					$_SESSION['microsoft_access_token']	= $userinfo->access_token;
					$_SESSION['microsoft_authentication_token']	= $userinfo->authentication_token;
					$oauth['access_token']	=	$userinfo->access_token;
					$oauth['authentication_token']	=	$userinfo->authentication_token;
	
				} else die($vauth_text['ms_token_error']);
			
			}
			
			return $oauth;
		}	

		function get_oauth_info($oauth) {
			
			global $vauth_text;
			global $db;
			global $site_url;
			
			$oauth['access_token'] = $_SESSION['microsoft_access_token'];
			

			$info	=	json_decode($this->vauth_get_contents('https://apis.live.net/v5.0/me?access_token='.$oauth['access_token']), FALSE); //Получаем информцию о пользователе
			
			$oauth['uid']		=	$this->conv_it($info->id);
			if (empty($oauth['uid'])) { header('Location: '.$site_url); die(); }
			
			$oauth['fullname'] =	$this->conv_it($info->name);
			$oauth['firstname'] =	$this->conv_it($info->first_name);
			$oauth['lastname'] =	$this->conv_it($info->last_name);
			
			$oauth['sex'] =	$this->conv_it($info->gender);
			$oauth['link'] =	$this->conv_it($info->link);
			
			$oauth['nick'] =	$oauth['fullname'];
			
			$avatar 			=	$this->get_curl_headers('https://apis.live.net/v5.0/'.$oauth['uid'].'/picture');
			
			
			preg_match("!http(?)://(.*?\s)!Ui",$avatar,$avatar);
			
			$avatar[0] = substr($avatar[0], 0, -2);
			
			$avatar = $avatar[0];
			
			$oauth['avatar']	=	$avatar;
			

			$oauth['nick'] =	$oauth['fullname'];
			
			switch(	$oauth['sex']	) {
			
				case 'male'	: $oauth['sex'] = $vauth_text[4];	break;
				case 'female'	: $oauth['sex'] = $vauth_text[5];	break;
			
			}
			

			$oauth['email']	=	$this->conv_it($info->emails->account);
			
			#$oauth['friends']	=	$this->get_oauth_friends($oauth);
			
			return $oauth;
		}
		
		function get_oauth_friends($oauth) {
			
			$friendlist='';
			
			if (empty($oauth['access_token'])) $this->vauth_auth($oauth);
			
			$oauth['friends']	=	json_decode($this->vauth_get_contents('https://apis.live.net/v5.0/me/friends?access_token='.$oauth['access_token']),FALSE);
			
			print_r($oauth['friends']);die;
			
			foreach($fb['friends'] as $k=>$v) {
				if (is_numeric($v)) {
					$v = sprintf("%.0f",$v);
					$fb_friendlist	= $fb_friendlist.'&'.$v;
				}
			}
		
			$fb['friends']	= substr($fb_friendlist,1);
			
			return $fb['friends'];

		}	
	}
	
}

$vauth_api = new MsFunctions ();			
	
?>		