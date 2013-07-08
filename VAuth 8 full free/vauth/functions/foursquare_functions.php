<?php

if( ! class_exists( 'FsFunctions' ) )	{

	class FsFunctions extends VAuthFunctions {
	
		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['needhash'] = 'yes';
			$oauth['needfriends'] = 'yes';
			$oauth['prefix'] = 'fs';
			$oauth['prefix2'] = 'foursquare';
			$oauth['disconnect_str'] 	=	"updtime='', fs_hash_auth='', fs_user_friends='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
			
			
			$oauth['app_id']			=	$vauth_config['foursquare_app_id'];
			$oauth['app_secret']		=	$vauth_config['foursquare_app_secret'];
			$oauth['redirect_uri']		=	$site_url.'/engine/modules/vauth/callback.php';
			$oauth['auth_url']			=	'https://foursquare.com/oauth2/authenticate?client_id='.$oauth['app_id'].'&response_type=code&redirect_uri='.$oauth['redirect_uri'];
		
			$oauth['group']				=	$vauth_config['foursquare_user_group'];

			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения foursquare');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения foursquare');
			
			return $oauth;
			
		}	
	
		// ** Функция авторизации в foursquare
		function vauth_auth($oauth) {
		
			global $auth_code;
			global $vauth_text;
		
			$_SESSION['auth_from']	=	'foursquare';
		
			if (empty($oauth['access_token']) and empty($auth_code)) {
				header('Location: '.$oauth['auth_url']);
				die;
			}
			
			if ( !empty($auth_code) ) {
			
				$oauth_auth = 'https://foursquare.com/oauth2/access_token?client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&grant_type=authorization_code&redirect_uri='.$oauth['redirect_uri'].'&code='.$auth_code;			
			
				$userinfo = json_decode($this->vauth_get_contents($oauth_auth), FALSE); // * Плучаем секретный хэшкод	
				
				if (	!empty($userinfo) ) {
				
					$access_token	= $userinfo->access_token;
					
					$_SESSION['foursquare_access_token']	=	$access_token;
				
					$oauth['access_token'] = $access_token;
					
				} else die($vauth_text['fs_token_error']);
			
			}
			
			return $oauth;
		}

		// ** Функция получения друзей из foursquare
		function get_oauth_friends($oauth) {
			
			$oauth_friendlist='';
			
			$oauth['friends']	=	json_decode($this->vauth_get_contents('https://api.foursquare.com/v2/users/self/friends?oauth_token='.$oauth['access_token']),FALSE);
			
		
			
			foreach($oauth['friends']->response->friends->items as $k=>$v) {
				if (is_numeric($v->id)) {
					$v = sprintf("%.0f",$v->id);
					$oauth_friendlist	= $oauth_friendlist.'&'.$v;
				}
			}
		
			$oauth['friends']	= substr($oauth_friendlist,1);
			
			return $oauth['friends'];

		}
	
		function get_oauth_info($oauth) {
			
			global $vauth_text;
			global $db;
			global $site_url;
			
			$oauth_info		=	json_decode($this->vauth_get_contents('https://api.foursquare.com/v2/users/self?oauth_token='.$oauth['access_token']), FALSE); //Получаем информцию о пользователе
			
			$oauth['uid']		=	$this->conv_it($oauth_info->response->user->id);
			if (!is_numeric($oauth['uid'])) { header('Location: '.$site_url); die(); }
			
			$oauth['bio']	=	$this->conv_it($oauth_info->response->user->bio);
			$oauth['sex']		=	$this->conv_it($oauth_info->response->user->gender);
			$oauth['city']		=	$this->conv_it($oauth_info->response->user->homeCity);
			$oauth['avatar']	=	$this->conv_it($oauth_info->response->user->photo);
			$oauth['firstname']=	$this->conv_it($oauth_info->response->user->firstName);
			$oauth['lastname']	=	$this->conv_it($oauth_info->response->user->lastName);
			$oauth['fullname'] =	$oauth['firstname'].' '.$oauth['lastname'];
			$oauth['nick'] =	$oauth['firstname'].' '.$oauth['lastname'];
			
			switch(	$oauth['sex']	) {
			
				case 'male'	: $oauth['sex'] = $vauth_text[4];	break;
				case 'female'	: $oauth['sex'] = $vauth_text[5];	break;
			
			}
			
			$oauth['phone']	=	$this->conv_it($oauth_info->response->user->contact->phone);
			$oauth['email']	=	$this->conv_it($oauth_info->response->user->contact->email);
		
			$oauth['phone']	=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['phone'] ) ) ) );
			$oauth['bio']	=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['bio'] ) ) ) );
			$oauth['city']	=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['city'] ) ) ) );
			
			return $oauth;
		}		
	
	
	}
}

$vauth_api = new FsFunctions ();			
	
?>