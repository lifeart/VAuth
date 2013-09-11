<?php

if( ! class_exists( 'VkFunctions' ) )	{

	class VkFunctions extends VAuthFunctions {

		function oauth_data() {
		
			global $site_url;
			global $vauth_config;
			
			$oauth = array();
			$oauth['needhash'] = 'yes';
			$oauth['needfriends'] = 'yes';
			$oauth['prefix'] = 'vk';
			$oauth['prefix2'] = 'vkontakte';
			$oauth['disconnect_str'] 	=	"updtime='', vk_user_phone='', vk_hash_auth='', ".$oauth['prefix']."_user_friends='', ".$oauth['prefix']."_connected='0', ".$oauth['prefix']."_user_id=''";
			
			$oauth['api_url']			=	'https://api.vk.com/method/';
			$oauth['app_id']			=	$vauth_config['vkontakte_app_id'];		
			$oauth['app_secret']		=	$vauth_config['vkontakte_app_secret'];
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/auth.php?auth_site=vkontakte';
			$oauth['auth_url']			=	'https://oauth.vk.com/authorize?client_id=' . $oauth['app_id'] .'&scope=friends,offline&redirect_uri='.$oauth['redirect_uri'].'&response_type=code';
			$oauth['group']				=	$vauth_config['vkontakte_user_group'];
		
			if (empty($oauth['group'])) $oauth['group'] = 4;
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения вконтакте');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения вконтакте');
		
			return $oauth;
		
		}
	
		// ** Функция авторизации в Vkontakte
		function vauth_auth($oauth) {
		
			global $vauth_text;
			global $auth_code;
		
			$_SESSION['auth_from']	=	'vkontakte';
		
			if (empty($auth_code) and empty($oauth['access_token']) and empty($oauth['access_code'])) {
				header('Location: '.$oauth['auth_url'].'&display=page');
				die();
			}
			
			if ( !empty($auth_code) ) {

				$oauth_auth = 'https://oauth.vk.com/access_token?client_id='.$oauth['app_id'].'&client_secret='.$oauth['app_secret'].'&code='.$auth_code.'&redirect_uri='.$oauth['redirect_uri'];
	
				$userinfo = json_decode($this->vauth_get_contents($oauth_auth), FALSE); // * Плучаем секретный хэшкод	
				
				if (	!empty($userinfo->access_token) ) {
				
					$access_token	= $userinfo->access_token;
					$oauth_user_id		= $userinfo->user_id;
					
					$_SESSION['vkontakte_user_id']			=	$oauth_user_id;
					$_SESSION['vkontakte_access_token']	=	$access_token;
					$_SESSION['vkontakte_access_code']		=	$auth_code;
					$oauth['uid'] = $oauth_user_id;
					$oauth['access_token'] = $access_token;


				} else die($vauth_text['vk_token_error']);
			
			}
			
			return $oauth;
		}
		
		// ** Функция получения информации из Вконтакте
		function get_oauth_info($oauth) {

			global $vauth_text;
			global $db;
			
			$query_url = 'https://api.vk.com/method/users.get?uids='.$oauth['uid'].'&fields=photo_200,photo_200_orig,photo_max_orig,nickname,screen_name,sex,bdate,photo,contacts,activity,relation,activities,interests,movies,tv,books,games,about,quotes,connections,city,country,education&access_token='.$oauth['access_token'];
			
			$oauth_info = json_decode($this->vauth_get_contents($query_url), FALSE);

			if (!$this->get_vk_from_json($oauth_info,'email')) $oauth['email'] =	$oauth['uid'].'@vk.com';
			else $oauth['email']		=	$this->get_vk_from_json($oauth_info,'email');
						
			$oauth['avatar']		=	$this->get_vk_from_json($oauth_info,'photo_200');
			
			
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo_200_orig');
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo_max_orig');
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo');
			
			$oauth['last_name']	=	$this->get_vk_from_json($oauth_info,'last_name');	
			$oauth['first_name']	=	$this->get_vk_from_json($oauth_info,'first_name');
			$oauth['screen_name'] 	=	$this->get_vk_from_json($oauth_info,'screen_name');
			$oauth['nick']		 	=	$oauth['screen_name'];
			$oauth['fullname']		=	$oauth['first_name'] . ' ' . $oauth['last_name']; // Делаем полное имя
			
			$oauth['mobile_phone']	=	$this->get_vk_from_json($oauth_info,'mobile_phone');
			
			#страна проживания пользователя
			$oauth['country']	=	$this->get_vk_from_json($oauth_info,'country');
			$info_country	=	json_decode($this->vauth_get_contents('https://api.vk.com/method/places.getCountryById?cids='.$oauth['country'].'&access_token='.$oauth['access_token']),FALSE);
			$oauth['country']	=	$this->get_vk_from_json($info_country,'name');
			#страна проживания пользователя
			
			#город пльзователя
			$oauth['city']		=	$this->get_vk_from_json($oauth_info,'city'); //Берём город пользователя
			$info_city		=	json_decode($this->vauth_get_contents('https://api.vk.com/method/places.getCityById?cids='.$oauth['city'].'&access_token='.$oauth['access_token']),FALSE); //Загружаем инфу города
			$oauth['city']		=	$this->get_vk_from_json($info_city,'name'); //Записываем имя города в переменную
			if (empty($oauth['city'])) $oauth['city'] = 'Silent Hill';
			#город пльзователя
			
			#статус пользователя вконтакте
			$oauth['activity']	=	$this->get_vk_from_json($oauth_info,'activity');
			$oauth['skype']	=	$this->get_vk_from_json($oauth_info,'skype');
			$oauth['facebook']	=	$this->get_vk_from_json($oauth_info,'facebook');
			$oauth['facebook_name']	=	$this->get_vk_from_json($oauth_info,'facebook_name');
			$oauth['twitter']	=	$this->get_vk_from_json($oauth_info,'twitter');
			$oauth['livejournal']	=	$this->get_vk_from_json($oauth_info,'livejournal');
			$oauth['university_name']	=	$this->get_vk_from_json($oauth_info,'university_name');
			#статус пользователя вконтакте
			
			$oauth['sex']		=	$this->get_vk_from_json($oauth_info,'sex');

			switch(	$oauth['sex']	) {
			
				case 2	: $oauth['sex']	=	$vauth_text[4];	break;
				case 1	: $oauth['sex']	=	$vauth_text[5];	break;
				case 0	: $oauth['sex']	=	'';	break;
			
			}
			
			$oauth['quotes'] = $this->get_vk_from_json($oauth_info,'quotes');
			$oauth['bio']			=	 $this->get_vk_from_json($oauth_info,'about');
			$oauth['bio']			=		str_replace("\r\n","<br/>",$oauth['bio']);
			$oauth['bio']			=		'<br/>'.$oauth['bio'];
			
			#получаем дату рождения пользователя
			$oauth['bdate']	=	$this->get_vk_from_json($oauth_info,'bdate');//Получаем дату рождения
			
			$oauth['update_time']		=	time();
			$oauth['mobile_phone']		=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['mobile_phone'] ) ) ) );
			$oauth['activity']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['activity'] ) ) ) );
			$oauth['fullname']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['fullname'] ) ) ) );
			$oauth['country']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['country'] ) ) ) );
			$oauth['quotes']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['quotes'] ) ) ) );
			$oauth['bio']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['bio'] ) ) ) );
			$oauth['city']				=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['city'] ) ) ) );
			
			if (!empty($oauth['country']) and !empty($oauth['city'])) $oauth['land'] = $oauth['country'].', '.$oauth['city'];
			if (empty($oauth['country']) or empty($oauth['city'])) $oauth['land'] = $oauth['country'].$oauth['city'];

			
			return $oauth;
		}

		// ** Функция получения друзей из vkontakte
		function get_oauth_friends($oauth) {

			$site_friends	=	json_decode($this->vauth_get_contents('https://api.vk.com/method/friends.getAppUsers?access_token='.$oauth['access_token']),FALSE); 					
			
			$site_friends	=	$site_friends->response;
			
			if (is_array($site_friends)) {
			
				foreach($site_friends as $k=>$v) {
					if (is_numeric($v)) {
						$v = sprintf("%.0f",$v);
						$oauth_friendlist	= @$oauth_friendlist.'&'.$v;
					}
				}
			
			} else $oauth_friendlist = '';
			
			$oauth['friends']	= substr($oauth_friendlist,1);
			
			return $oauth['friends'];
			
		}		

		// ** Функция обработки JSON данных пользователя из Вконтакте
		function get_vk_from_json($string,$value) { //Вытягивание информации из ответа в формате json
				if(isset( $string->response[0]->{$value})) {
					return $this->conv_it($string->response[0]->{$value});
				} else return false;
			}	
	}
}

$vauth_api = new VkFunctions ();			
		
?>