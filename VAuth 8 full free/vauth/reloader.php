<?php

	session_start();
	
	if (!empty($_GET['ref'])) $_SESSION['referrer'] = $_GET['ref'];
	
	// ** Получаем настройки скрипта ** //
	include_once("settings/script_settings.php");		
	
	
	$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);

	if ($user['user_group'] != 1) die; 
	
	if (isset($_GET['rebuild'])) {
		
			require_once($func_path . '/vkontakte_functions.php');
		
			function rebuild_vk_user($k,$v) {
			
				global $vauth_api;
				global $dle_api;
				global $db;
			
				$vk = new VkFunctions();
				$oauth = $vk->oauth_data();
				$oauth['user_id'] = $v['user_id'];
				$oauth['namme'] = $v['name'];
				$oauth['uid'] = $v['vk_user_id'];
				$oauth['access_token'] = $v['vk_hash_auth'];

				$prefix = $oauth['prefix'];
				
				$dle_userinfo['user_id'] = $oauth['user_id'];
				
				$oauth = $vk->get_oauth_info($oauth);

				if ($vauth_api->ifIs($oauth['friends'])) {
				
					$friendlist = $prefix.'_user_friends=\''.$oauth['friends'].'\', ';
				
				} else $friendlist = '';
				
				if ($vauth_api->ifIs($oauth['fullname'])) {
				
					$fullname = $oauth['fullname'];
					$fullname = 'fullname=\''.$fullname.'\', ';

				} else $fullname = '';
				
				if ($vauth_api->ifIs($oauth['bio'])) {
					
					$info = 'info=\''.$oauth['bio'].'\', ';
					
				} elseif ($vauth_api->ifIs($oauth['info'])) {
					
					$info = 'info=\''.$oauth['info'].'\', ';
					
				} elseif ($vauth_api->ifIs($oauth['activity'])) {
				
					$info = 'info=\''.$oauth['activity'].'\', ';

				} else	$info = '';
			
				if ($vauth_api->ifIs($oauth['location'])) {
					
					$land = 'land=\''.$oauth['location'].'\', ';
					
				} elseif ($vauth_api->ifIs($oauth['city'])) {
					
					$land = 'land=\''.$oauth['city'].'\', ';
					
				} else $land = '';

				if ($vauth_api->ifIs($oauth['sex'])) {
				
					$sex = 'sex=\''.$oauth['sex'].'\', ';
					
				} elseif ($vauth_api->ifIs($oauth['gender'])) {
					
					$sex = 'sex=\''.$oauth['gender'].'\', ';
					
				}	else $sex = '';			
				
				if ($vauth_api->ifIs($oauth['birthday'])) {
					
					$bdate = 'bdate=\''.$oauth['birthday'].'\', ';
					
				} elseif ($vauth_api->ifIs($oauth['bdate'])) {
					
					$bdate = 'bdate=\''.$oauth['bdate'].'\', ';
					
				} else $bdate = '';

				if ($vauth_api->ifIs($oauth['mobile_phone'])) {
					$phone = 'vk_user_phone=\''.$oauth['mobile_phone'].'\', ';
				} else $phone = '';			
				
				if ($vauth_api->ifIs($oauth['link'])) {
				
					$link = $oauth['link'];
					$link = ' '.$prefix.'_link=\''.$link.'\', ';
					
				} else $link = '';
				
				
				$current_user = $dle_api->take_user_by_id($oauth['user_id']);
				

				if ($vauth_api->ifIs($oauth['nick'])) {
				
					if ($dle_api->take_user_by_name($oauth['nick'])==false) {
						$name = $oauth['nick'];
						$name = ' name=\''.$oauth['nick'].'\', ';
					} else {

						$name = ' name=\''.$oauth['fullname'].'\', ';
					
					}
					
				} else $name = $oauth['namme'];

				if (is_numeric(trim($current_user['name'])) ){
				
					$name = ' name=\''.$oauth['fullname'].'\', ';
				
				}


				if ($vauth_api->ifIs($member_id[$prefix.'_username'])) {
				
					if ($vauth_api->ifIs($oauth[$prefix.'_username'])) {
				
						$prefix_username = $oauth[$prefix.'_username'];
						$prefix_username = ' '.$prefix.'_username=\''.$prefix_username.'\', ';
						
					} else $prefix_username = '';
					
				} else $prefix_username = '';					
			
				$times = time();
				
				

				$oauth['avatar'] = $vauth_api->upload_avatar($oauth['avatar'],$dle_userinfo['user_id'],$oauth['avatars']); //Загружаем картинку и получаем её адрес
				$oauth['connected'] = 1; $oauth['registered'] = 1;
		
				$lastdate = ' lastdate=\''.$times.'\', ';
				$updtime =' updtime=\''.$times.'\', ';
				$db->query( "UPDATE " . USERPREFIX . "_users set "
						.$updtime.$prefix_username.$info.$link.$friendlist.$lastdate.$fullname.$land.$foto.$sex.$bdate.$phone.$name
						.$prefix."_registered='$oauth[registered]', ".$prefix."_connected='$oauth[connected]', ".$prefix."_user_id='$oauth[uid]' WHERE user_id = '{$dle_userinfo[user_id]}'" ); //Сохраняем данные пользователя	
			
			}
		
			if ($_GET['rebuild'] == 'all') {
			
				$fields = 'user_id,vk_user_id,vk_hash_auth, name';

				$condition = "vk_connected = 1 and vk_registered =1 and land = 'Silent Hill'";
				$users =  $dle_api->load_table (PREFIX."_users", $fields, $condition, true);
		
				foreach ($users as $k=>$v) {
				
					rebuild_vk_user($k,$v);
			
				}
			
			}
			
			if ($_GET['rebuild'] != 'all') {

				$user = $dle_api->take_user_by_id(abs($_GET['rebuild']));
			
				if ($user['vk_registered'] == 1) {
			
					rebuild_vk_user('',$user);
				
				}
			
			}
		}

?>		