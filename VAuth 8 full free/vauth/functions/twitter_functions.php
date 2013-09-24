<?php

if( ! class_exists( 'TwFunctions' ) )	{

	class TwFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			global $func_path;

			$oauth = array();
			$oauth['prefix'] = 'tw';
			$oauth['prefix2'] = 'twitter';
			$oauth['disconnect_str'] 	=	"updtime='', tw_user_friends='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
		

			define('CONSUMER_KEY'		, $vauth_config['twitter_app_id']		);
			define('CONSUMER_SECRET'	, $vauth_config['twitter_app_secret']	);
			define('OAUTH_CALLBACK'		, $site_url . '/engine/modules/vauth/callback.php'	);
			
			$oauth['app_id'] = $vauth_config['twitter_app_id'];
			$oauth['app_secret'] = $vauth_config['twitter_app_secret'];
			$oauth['callback'] = $site_url . '/engine/modules/vauth/callback.php';
			$oauth['access_token'] = @$_SESSION['access_token'];
		
			$oauth['group']				=	$vauth_config['twitter_user_group'];
	
			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения Twitter');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения Twitter');
		
			return $oauth;
		
		}	

		// ** Функция получения данных пользователя из твиттера
		function get_tw_content($oauth) {
		
			global $func_path;
			require_once(	$func_path . 'twitteroauth.php'	);
			
			
			$connection 	= 	new TwitterOAuth($oauth['app_id'], $oauth['app_secret'], $oauth['access_token']['oauth_token'], $oauth['access_token']['oauth_token_secret']);
			$oauth_content		=	$connection->get('account/verify_credentials');
		
			return $oauth_content;
		}

		// ** Функция авторизации в Твиттера
		function vauth_auth($oauth) {
			
			$_SESSION['auth_from']	=	'twitter';
			
			global $func_path;
			
			require_once(	$func_path . 'twitteroauth.php'	);
			
			if (empty($oauth['access_token']) || empty($oauth['access_token']['oauth_token']) || empty($oauth['access_token']['oauth_token_secret'])) {
				$connection = new TwitterOAuth($oauth['app_id'], $oauth['app_secret']);
				$request_token = $connection->getRequestToken($oauth['callback']);
				$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
				$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
				switch ($connection->http_code) {
					case 200:
						$url = $connection->getAuthorizeURL($token);
						header('Location: ' . $url); die();
						break;
					default:
						echo 'Не могу соединиться с сервером Twitter, попробуйте позднее.';
				}
			} else return $oauth;
			
		}

		// ** Функция получения информации из Твиттера
		function get_oauth_info($oauth) {
			
			global $db;
			global $func_path;
			
			require_once(	$func_path . 'twitteroauth.php'	);

			$oauth['access_token'] = $_SESSION['access_token'];
			
			$connection 	= 	new TwitterOAuth($oauth['app_id'], $oauth['app_secret'], $oauth['access_token']['oauth_token'], $oauth['access_token']['oauth_token_secret']);
			$oauth_content	=	$connection->get('account/verify_credentials');	
			

			$oauth['uid']	=	$oauth_content->id; //ID
			$oauth['loc']	=	$oauth_content->location; //Местоположение
			$oauth['img']	=	$oauth_content->profile_image_url_https; //Аватар
			$oauth['url']	=	$oauth_content->url; //Сайт
			$oauth['name']	=	$oauth_content->name; //Имя пользователя
			$oauth['lang']	=	$oauth_content->lang; //Язык пользователя
			$oauth['info']	=	$oauth_content->description; //Описание
			$oauth['nick']	=	$oauth_content->screen_name; //Описание
			
			$oauth['loc']	=	$this->conv_it($oauth['loc']);
			$oauth['name']	=	$this->conv_it($oauth['name']);
			$oauth['info']	=	$this->conv_it($oauth['info']);
			$oauth['lang']	=	$this->conv_it($oauth['lang']);
			$oauth['fullname']	=	$oauth['name'];

			$oauth['email']	=	$oauth['nick'].'@twitter.com';
			
			$oauth['info']		=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['info'] ) ) ) );	
			$oauth['name']		=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['name'] ) ) ) );
			
			$oauth['avatar']	=	$oauth['img'];
			
			return $oauth;
		}		

		// ** Функция получения друзей из твиттера
		function get_oauth_friends($oauth) {
			
			$oauth_friendlist='';

			$oauth['friends']	=	json_decode($this->vauth_get_contents('https://api.twitter.com/1.1/followers/ids.json?user_id='.$oauth['uid']),FALSE);

			foreach($oauth['friends']->ids as $k=>$v) {
			
				if (is_numeric($v)) {
					$v = sprintf("%.0f",$v);
					$oauth_friendlist	= $oauth_friendlist.'&'.$v;
				}
				
			}
			
			$oauth['friends']	= substr($oauth_friendlist,1);

			return $oauth['friends'];

		}
	
	}
}

	$vauth_api = new TwFunctions ();	

?>