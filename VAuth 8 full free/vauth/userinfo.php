<?php

	include_once("settings/script_settings.php"); //Получаем системные настройки
	
			if ( file_exists($func_path . '/twitter_functions.php')) require_once($func_path . '/twitter_functions.php');
			if ( file_exists($func_path . '/facebook_functions.php')) require_once($func_path . '/facebook_functions.php');
			if ( file_exists($func_path . '/vkontakte_functions.php')) require_once($func_path . '/vkontakte_functions.php');
			if ( file_exists($func_path . '/foursquare_functions.php')) require_once($func_path . '/foursquare_functions.php');

	

	if (empty($tpl)) die('Не нужно так заходить на эту страницу');

	if (empty($row['updtime'])) $row['updtime'] = 999;
	
	$update_period = $vauth_config['update_period'];
	
	if (empty($update_period)) $update_period = 8400;
	
	if (empty($_TIME)) $_TIME = time();
	
	if ($_TIME - $row['updtime'] > $update_period) {
		
		$vk_friends = ''; $fb_friends = ''; $tw_friends = '';
		
		if ($row['vk_connected'] == 1) {

			$vauth_api = new VkFunctions ();
			$oauth = $vauth_api->oauth_data();
			$oauth['access_token']	= $row['vk_hash_auth'];	
			$vk_friends = $vauth_api->get_oauth_friends($oauth);
			if ( strlen($vk_friends) < strlen($row['vk_user_friends']) and $row['vk_user_friends']!='Пока никого нет..' ) $vk_friends = $row['vk_user_friends'];
		}
		
		if ($row['fb_connected'] == 1) {
			$vauth_api = new FbFunctions ();
			$oauth = $vauth_api->oauth_data();
			$oauth['access_token'] = $row['fb_hash_auth'];	
			$fb_friends = $vauth_api->get_oauth_friends($oauth);

			if ( strlen($fb_friends) < strlen($row['fb_user_friends']) ) $fb_friends = $row['fb_user_friends'];
		}
		
		if ($row['tw_connected'] == 1) {
			$vauth_api = new TwFunctions ();
			$oauth = $vauth_api->oauth_data();
			$oauth['uid'] = $row['tw_user_id'];
			$tw_friends = $vauth_api->get_oauth_friends($oauth);

			if ( strlen($tw_friends) < strlen($row['tw_user_friends']) ) $tw_friends = $row['tw_user_friends'];
		}
		
		if ($row['fs_connected'] == 1) {
			$vauth_api = new FsFunctions ();
			$oauth = $vauth_api->oauth_data();
			$oauth['access_token'] = $oauth['fs_hash_auth'];
			$fs_friends = $vauth_api->get_oauth_friends($oauth);

			if ( strlen($fs_friends) < strlen($row['fs_user_friends']) ) $fs_friends = $row['fs_user_friends'];
		}
		
		$fb_friends2 = $fb_friends;
		$vk_friends2 = $vk_friends;
		$tw_friends2 = $tw_friends;
		$fs_friends2 = $fs_friends;

		
		$vk_friends = str_replace("&"," OR vk_user_id = ",$vk_friends);
		$fb_friends = str_replace("&"," OR fb_user_id = ",$fb_friends);
		$tw_friends = str_replace("&"," OR tw_user_id = ",$tw_friends);
		$fs_friends = str_replace("&"," OR tw_user_id = ",$fs_friends);
		
		if (strlen($vk_friends)<4) $vk_friends = 00001;
		if (strlen($fb_friends)<4) $fb_friends = 00001;
		if (strlen($tw_friends)<4) $tw_friends = 00001;
		if (strlen($fs_friends)<4) $fs_friends = 00001;
	
		$friendlist = $db->query( "SELECT * FROM " . USERPREFIX . "_users where fs_user_id = $fs_friends OR vk_user_id = $vk_friends OR fb_user_id = $fb_friends OR tw_user_id = $tw_friends" );
		
		$_show = false;
		
		while ( $friend = $db->get_row( $friendlist ) )	{

			if ($_show == true) {

				$user_avatar = $friend['foto'];
				if (empty($friend['foto'])) $user_avatar = '<img src="/engine/modules/vauth/styles/noavatar.png"></img>';
				else $user_avatar  = '<img src="/uploads/fotos/'.$user_avatar.'"></img>';
				
				$groupinfo = $dle_api->load_table (PREFIX."_usergroups", "*", "id = '$friend[user_group]'");
				
				if ($groupinfo) {
				
					$body = '<a href="/user/' . urlencode($friend['name']) . '">
					'.$user_avatar.'
					<span class="rcols"><h5>'.$friend['fullname'] .'</h5>
					'.$vauth_text['group'].': '.$groupinfo['group_prefix'].$groupinfo['group_name'].$groupinfo['group_suffix'].'<br>
					<em>'.$vauth_text['registration'].': '.date("d.m.Y",$friend['reg_date']).'</em>
					</span>
					</a>
					';	
					
					$friend_dle.='<a classs="vauth_userfriend_link" href="/user/' . urlencode($friend['name']) . '">'.$body.'</a>';
			
				}
			
			}
			else 
			$friend_dle = $friend_dle . '<a class="userfriend" href="/user/' . urlencode($friend['name']) . '">' . $friend['fullname'] . '</a>, ';
		}
		
		$friend_dle = substr($friend_dle,0,strlen($friend_dle)-2);
		
		$base_friends = base64_encode($friend_dle);
		
		$db->query( "UPDATE " . USERPREFIX . "_users set userfriends='$base_friends', updtime='$_TIME', fb_user_friends='$fb_friends2', vk_user_friends='$vk_friends2', fs_user_friends='$fs_friends2', tw_user_friends='$tw_friends2' WHERE user_id = '{$row[user_id]}'" );
		
	} else $friend_dle = base64_decode($row['userfriends']);
	
	if ($row['vk_connected'] == 1) { $accounts = $accounts . '<a class="account_link vk_account" href="http://vk.com/id' . $row['vk_user_id'] .'">'.$vauth_text['profile_vk'].'</a> '; }
	if ($row['fb_connected'] == 1) { $accounts = $accounts . '<a class="account_link fb_account" href="http://facebook.com/' . $row['fb_user_id'] .'">'.$vauth_text['profile_fb'].'</a> '; }
	if ($row['tw_connected'] == 1) { $accounts = $accounts . '<a class="account_link tw_account" href="https://twitter.com/account/redirect_by_id?id=' . $row['tw_user_id'] .'">'.$vauth_text['profile_tw'].'</a> '; }
	if ($row['fs_connected'] == 1) { $accounts = $accounts . '<a class="account_link fs_account" href="https://foursquare.com/user/' . $row['fs_user_id'] .'">'.$vauth_text['profile_fs'].'</a> '; }
	if ($row['od_connected'] == 1) { $accounts = $accounts . '<a class="account_link od_account" href="http://www.odnoklassniki.ru/profile/' . $row['od_user_id'] .'">'.$vauth_text['profile_od'].'</a> '; }
	if ($row['go_connected'] == 1) { $accounts = $accounts . '<a class="account_link go_account" href="https://plus.google.com/' . $row['go_user_id'] .'">'.$vauth_text['profile_go'].'</a> '; }
	if ($row['gh_connected'] == 1) { $accounts = $accounts . '<a class="account_link gh_account" href="https://github.com/' . $row['gh_username'] .'">'.$vauth_text['profile_gh'].'</a> '; }
	if ($row['ma_connected'] == 1) { $accounts = $accounts . '<a class="account_link ma_account" href="'.$row['ma_link'].'">'.$vauth_text['profile_ma'].'</a> '; }
	if ($row['ms_connected'] == 1) { $accounts = $accounts . '<a class="account_link ms_account" href="'.$row['ms_link'].'">'.$vauth_text['profile_ms'].'</a> '; }
	if ($row['in_connected'] == 1) { $accounts = $accounts . '<a class="account_link in_account" href="http://instagram.com/'.$row['in_username'].'">'.$vauth_text['profile_in'].'</a> '; }
	
	
	if (!empty($friend_dle)) {
	
			$tpl->set('{friends}', $friend_dle);
			$tpl->set( '[vauth-friends]', "" );
			$tpl->set( '[/vauth-friends]', "" );

		} else {
			
			$tpl->set_block( "'\\[vauth-friends\\](.*?)\\[/vauth-friends\\]'si", "<!-- vauth info -->" );
		
		}
		
	if (!empty($row['sex'])) {
		
			$tpl->set('{sex}', $row['sex']);
			$tpl->set( '[vauth-sex]', "" );
			$tpl->set( '[/vauth-sex]', "" );
	
		} else {
			
			$tpl->set_block( "'\\[vauth-sex\\](.*?)\\[/vauth-sex\\]'si", "<!-- vauth info -->" );
		
		}
	
	if (!empty($row['vk_user_phone'])) {
		
			$tpl->set('{mobile_phone}', $row['vk_user_phone']);
			$tpl->set( '[vauth-mobile_phone]', "" );
			$tpl->set( '[/vauth-mobile_phone]', "" );
	
		} else {
			
			$tpl->set_block( "'\\[vauth-mobile_phone\\](.*?)\\[/vauth-mobile_phone\\]'si", "<!-- vauth info -->" );
		
		}
	
	if (!empty($row['bdate'])) {
		
			$tpl->set('{bdate}', $row['bdate']);
			$tpl->set( '[vauth-bdate]', "" );
			$tpl->set( '[/vauth-bdate]', "" );
	
		} else {
			
			$tpl->set_block( "'\\[vauth-bdate\\](.*?)\\[/vauth-bdate\\]'si", "<!-- vauth info -->" );
		
		}
	
	$tpl->set('{friends}', $friend_dle);
	$tpl->set('{accounts}', $accounts);
	$tpl->set('{userifo-style}', $style['userinfo']);
	$tpl->set( '[vauth]', '<div id="vauth_user_accounts">' );
	$tpl->set( '[/vauth]', "</div>" );

?>