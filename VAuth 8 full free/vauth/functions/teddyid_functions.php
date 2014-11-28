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
			$oauth['app_secret'] = $vauth_config['teddyid_app_secret'];
			//$oauth['scope'] = 'email,phone';
			$oauth['disconnect_str'] = "updtime='', ".$oauth['prefix']."_user_id=''";
			$oauth['redirect_uri'] = $site_url.'/engine/modules/vauth/auth.php?auth_site=teddyid';
			$oauth['auth_url'] = $oauth['api_url'] . 'oauth_access.php?node_id=' . $oauth['app_id'] .'&redirect_uri='.$oauth['redirect_uri'] . (isset($oauth['scope']) ? '&scope=' . $oauth['scope'] : '');
			$oauth['group']	= $vauth_config['teddyid_user_group'];
			if (empty($oauth['app_id'])) die('Не указан идентификатор приложения TeddyID');
			if (empty($oauth['app_secret'])) die('Не указан секретный код приложения TeddyID');
			return $oauth;
		}

		function vauth_auth($oauth) {
			global $auth_code;

			$_SESSION['auth_from'] = 'teddyid';
			if ( empty($oauth['access_token']) and empty($auth_code) ) {
				header('Location: '.$oauth['auth_url'] . '&grant_type=client_credentials');
				die;
			}

			if ( !empty($auth_code) ) {
				$oauth_auth = $oauth['auth_url'] . '&grant_type=authorization_code&code='.$auth_code;
				$userinfo = json_decode($this->vauth_get_contents($oauth_auth), FALSE);

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