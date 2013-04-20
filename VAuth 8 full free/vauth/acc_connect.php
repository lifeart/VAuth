<?php

	session_start();
	
	include_once(dirname (__FILE__).'/settings/script_settings.php'); //Получаем системные настройки
	
	define('DATALIFEENGINE', true);	
	
	$tpl->load_template( 'acc_connect.tpl' );	
	$tpl->set( '{header}', $vauth_text[8]);
	
	if ($is_logged == true) {
	
		if (empty($member_id['userpassword_hash'])) {
			
			$password	= $_SESSION['dle_password'];
			$password	= $vauth_api->encode($password,$userhash_pass,$userhash_salt);
			$password	= base64_encode($password);
			$member_id['userpassword_hash'] = $password;
			
			$db->query( "UPDATE " . USERPREFIX . "_users set userpassword_hash='$password' WHERE user_id = '{$member_id[user_id]}'" );

		}
	
		$tpl->set( '{static}', '<a class="back_to_profile" href="'.$site_url.'/user/'.$member_id["name"].'">'.$vauth_text[34].'</a>');
		$tpl->set( '{userinfo-hello}', '');
	
		$id				=	$member_id["user_id"]; //Берём ID пользователя
		$dle_photo		=	$member_id["foto"]; //Берём аватарку пользователя
		$dle_email		=	$member_id["email"]; //Берём e-mail пользователя
		$dle_banned		=	$member_id["banned"]; //Забанен ли
		$dle_user_group	=	$member_id["user_group"]; //Берём группу пользователя
		
		$account_connect	=	$_POST['dle_connect'];
		
		if (!empty($account_connect)) {
			
			sleep(5);
			
			switch($account_connect) {
			
				case 'vk' :
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=vkontakte'); die();
					break;
				case 'vk_off' :
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=vkontakte'); die();
					break;
				case 'fb' :
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=facebook'); die();
					break;
				case 'fb_off' :
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=facebook'); die();
					break;
				case 'tw'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=twitter'); die();
					break;
				case 'tw_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=twitter'); die();
					break;
				case 'fs'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=foursquare'); die();
					break;
				case 'fs_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=foursquare'); die();
					break;
					
				case 'od'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=odnoklassniki'); die();
					break;
				case 'od_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=odnoklassniki'); die();
					break;		
				case 'in'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=instagram'); die();
					break;
				case 'in_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=instagram'); die();
					break;
				case 'go'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=google'); die();
					break;
				case 'go_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=google'); die();
					break;						
				case 'gh'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=github'); die();
					break;
				case 'gh_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=github'); die();
					break;
				case 'ms'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=microsoft'); die();
					break;
				case 'ms_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=microsoft'); die();
					break;
				case 'ma'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=mail'); die();
					break;
				case 'ma_off'	:
					$_SESSION['ac_connect']	= $account_connect;
					header('location: '.$site_url . '/engine/modules/vauth/auth.php?auth_site=mail'); die();
					break;						
				default:
					header('location: '.$site_url); die();
					break;
				
			}
			
		}
		
		if (file_exists($func_path . '/vkontakte_functions.php')) {
		
			if ($member_id["vk_connected"] == 1 and $member_id["vk_registered"] != 1) $tpl->set( '{vk_link}', $txt_vk_off);
				elseif ($member_id["vk_connected"] == 0 and $member_id["vk_registered"] != 1) $tpl->set( '{vk_link}', $txt_vk_on);
				elseif ($member_id["vk_registered"] == 1) $tpl->set( '{vk_link}', '');
			
		} else $tpl->set( '{vk_link}', '');

		if (file_exists($func_path . '/facebook_functions.php')) {
		
			if ($member_id["fb_connected"] == 1 and $member_id["fb_registered"] != 1) $tpl->set( '{fb_link}', $txt_fb_off);
			elseif ($member_id["fb_connected"] == 0 and $member_id["fb_registered"] != 1) $tpl->set( '{fb_link}', $txt_fb_on);
			elseif ($member_id["fb_registered"] == 1) $tpl->set( '{fb_link}', '');			
		
		} else $tpl->set( '{fb_link}', '');			
		
		if (file_exists($func_path . '/twitter_functions.php')) {
		
			if ($member_id["tw_connected"] == 1 and $member_id["tw_registered"] != 1) $tpl->set( '{tw_link}', $txt_tw_off);
				elseif ($member_id["tw_connected"] == 0 and $member_id["tw_registered"] != 1) $tpl->set( '{tw_link}', $txt_tw_on);
				elseif ($member_id["tw_registered"] == 1) $tpl->set( '{tw_link}', '');
		
		} else $tpl->set( '{tw_link}', '');

		if (file_exists($func_path . '/foursquare_functions.php')) {
		
			if ($member_id["fs_connected"] == 1 and $member_id["fs_registered"] != 1) $tpl->set( '{fs_link}', $txt_fs_off);
				elseif ($member_id["fs_connected"] == 0 and $member_id["fs_registered"] != 1) $tpl->set( '{fs_link}', $txt_fs_on);
				elseif ($member_id["fs_registered"] == 1) $tpl->set( '{fs_link}', '');

		} else $tpl->set( '{fs_link}', '');
		
		if (file_exists($func_path . '/odnoklassniki_functions.php')) {
			
			if ($member_id["od_connected"] == 1 and $member_id["od_registered"] != 1) $tpl->set( '{od_link}', $txt_od_off);
				elseif ($member_id["od_connected"] == 0 and $member_id["od_registered"] != 1) $tpl->set( '{od_link}', $txt_od_on);
				elseif ($member_id["od_registered"] == 1) $tpl->set( '{od_link}', '');
			
		} else $tpl->set( '{od_link}', '');

		if (file_exists($func_path . '/instagram_functions.php')) {
		
			if ($member_id["in_connected"] == 1 and $member_id["in_registered"] != 1) $tpl->set( '{in_link}', $txt_in_off);
				elseif ($member_id["in_connected"] == 0 and $member_id["in_registered"] != 1) $tpl->set( '{in_link}', $txt_in_on);
				elseif ($member_id["in_registered"] == 1) $tpl->set( '{in_link}', '');				
			
		} else $tpl->set( '{in_link}', '');	
		
		
		if (file_exists($func_path . '/google_functions.php')) {
		
			if ($member_id["go_connected"] == 1 and $member_id["go_registered"] != 1) $tpl->set( '{go_link}', $txt_go_off);
				elseif ($member_id["go_connected"] == 0 and $member_id["go_registered"] != 1) $tpl->set( '{go_link}', $txt_go_on);
				elseif ($member_id["go_registered"] == 1) $tpl->set( '{go_link}', '');
			
		} else $tpl->set( '{go_link}', '');
		
		if (file_exists($func_path . '/microsoft_functions.php')) {
		
			if ($member_id["ms_connected"] == 1 and $member_id["ms_registered"] != 1) $tpl->set( '{ms_link}', $txt_ms_off);
				elseif ($member_id["ms_connected"] == 0 and $member_id["ms_registered"] != 1) $tpl->set( '{ms_link}', $txt_ms_on);
				elseif ($member_id["ms_registered"] == 1) $tpl->set( '{ms_link}', '');
		
		} else $tpl->set( '{ms_link}', '');
		
		
		if (file_exists($func_path . '/github_functions.php')) {
		
			if ($member_id["gh_connected"] == 1 and $member_id["gh_registered"] != 1) $tpl->set( '{gh_link}', $txt_gh_off);
				elseif ($member_id["gh_connected"] == 0 and $member_id["gh_registered"] != 1) $tpl->set( '{gh_link}', $txt_gh_on);
				elseif ($member_id["gh_registered"] == 1) $tpl->set( '{gh_link}', '');
		
		} else $tpl->set( '{gh_link}', '');
		
		if (file_exists($func_path . '/mail_functions.php')) {
		
			if ($member_id["ma_connected"] == 1 and $member_id["ma_registered"] != 1) $tpl->set( '{ma_link}', $txt_ma_off);
				elseif ($member_id["ma_connected"] == 0 and $member_id["ma_registered"] != 1) $tpl->set( '{ma_link}', $txt_ma_on);
				elseif ($member_id["ma_registered"] == 1) $tpl->set( '{ma_link}', '');
			
		} else $tpl->set( '{ma_link}', '');
			
		$tpl->set( '{userinfo-connect}', '');
		$tpl->set( '{vauth_info}', $vauth_text[35]);
		
		if( $member_id['foto'] and (file_exists( ROOT_DIR . "/uploads/fotos/" . $member_id['foto'] )) ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $member_id['foto'] );
		else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
		
		$tpl->set( '{name}', $member_id['name']);
		
	}	else	{
		
		$tpl->set( '{userinfo-hello}', $vauth_text[20]);
		$tpl->set( '{fb_link}', '');
		$tpl->set( '{in_link}', '');
		$tpl->set( '{vk_link}', '');
		$tpl->set( '{tw_link}', '');
		$tpl->set( '{go_link}', '');
		$tpl->set( '{fs_link}', '');
		$tpl->set( '{ma_link}', '');
		$tpl->set( '{ms_link}', '');
		$tpl->set( '{gh_link}', '');
		$tpl->set( '{od_link}', '');
		$tpl->set( '{userinfo-connect}', '');
		$tpl->set( '{static}', '');
		$tpl->set( '{vauth_info}', '');
		$tpl->set( '{name}', 'Anonim');
		$tpl->set( '{foto}', '');
		$tpl->set( '{name}', '');
	}

$tpl->compile( 'content' );
$tpl->clear();
?>