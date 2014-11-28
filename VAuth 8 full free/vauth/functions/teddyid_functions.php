<?php
if( ! class_exists( 'TdFunctions' ) ) {
	class TdFunctions extends VAuthFunctions
	{
		function oauth_data() {
			global $site_url;
			global $vauth_config;
			$oauth = array();
			$oauth['needhash'] = 'yes';
			$oauth['prefix'] = 'td';
			$oauth['prefix2'] = 'teddyid';
			$oauth['api_url'] = 'https://www.teddyid.com/';
			$oauth['app_id'] = $vauth_config['teddyid_app_id'];
			$oauth['app_secret'] = sha1("from=".$vauth_config['teddyid_app_id'].";to=1;".$vauth_config['teddyid_app_secret']);
			//$oauth['scope'] = 'email,phone';
			$oauth['disconnect_str'] = "updtime='', ".$oauth['prefix']."_user_id=''";
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/auth.php?auth_site=teddyid';
			$oauth['auth_url'] = $oauth['api_url'] . 'oauth_access.php';
			$oauth['group']	= $vauth_config['teddyid_user_group'];
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения TeddyID');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения TeddyID');
			return $oauth;
		}

		function vauth_auth($oauth) {
			global $auth_code;
			var_dump($_REQUEST);
			$_SESSION['auth_from'] = 'teddyid';
			if ( empty($oauth['access_token']) and empty($auth_code) ) {
				
				$oauth_auth = $oauth['auth_url'];
				$datascope = 'node_id=' . $oauth['app_id'] .'&redirect_uri='.$oauth['redirect_uri'] . (isset($oauth['scope']) ? '&scope=' . $oauth['scope'] : '');
				$datascope = $datascope.'&grant_type=client_credentials&token='.$oauth['app_secret'];
				$oauth_info = json_decode($this->post_curl($oauth_auth,$datascope), FALSE);

				var_dump($oauth_info);
				

				if (isset($oauth_info->access_token)) {

					$access_token	= $oauth_info->access_token;
					$_SESSION['teddyid_access_token'] = $access_token;
					$oauth['access_token'] = $access_token;
					//http://jeeraf.ru/engine/modules/vauth/auth.php?auth_site=teddyid
					$redirect = 'https://teddyid.com/auth/index.php?access_token='.$access_token.'&back_url='.$oauth['redirect_uri'];
					header('Location: '.$redirect);
					//$data_uri = $oauth['api_url'].'oauth_resource.php';
					//$datascope2 = 'access_token='.$access_token.'&token='.$oauth['app_secret'].'&node_id='.$oauth['app_id'];
					//$oauth_info2 = json_decode($this->post_curl($data_uri,$datascope2), FALSE);
					//var_dump($oauth_info);
				} else {
					//var_dump($oauth_info);
					die();
				}


				die;
			}

			if ( !empty($auth_code) ) {
				#$oauth_auth = $oauth['auth_url'] . '&grant_type=authorization_code&code='.$auth_code;

				$oauth_auth = $oauth['api_url'];
					
				$datascope = 'grant_type=authorization_code&code='.$auth_code;
				
				$oauth_info = json_decode($this->post_curl($oauth_auth,$datascope), FALSE);


				#$userinfo = json_decode($this->vauth_get_contents($oauth_auth), FALSE);

				if ( !empty($userinfo) ) {
					$access_token	= $userinfo->access_token;
					$_SESSION['teddyid_access_token'] = $access_token;
					$oauth['access_token'] = $access_token;
				} else die('te_token_error');
			}

			return $oauth;
		}

		function get_oauth_info($oauth) {
			global $vauth_text;
			global $db;
			global $site_url;

			$oauth_info = json_decode($this->vauth_get_contents($oauth['api_url'] . 'access_token.php?access_token=' . $oauth['access_token']), FALSE); //Получаем информцию о пользователе
			$oauth['uid']	=	$this->conv_it($oauth_info->response->user_id);
			if (!is_numeric($oauth['uid'])) { header('Location: '.$site_url); die(); }

			if ( isset($oauth_info->response->phone) ) {
				$oauth['phone']	=	$this->conv_it($oauth_info->response->phone);
			}

			if ( isset($oauth_info->response->email) ) {
				$oauth['email']	=	$this->conv_it($oauth_info->response->email);
			}

			return $oauth;
		}
	}
}
$vauth_api = new TdFunctions ();
?>