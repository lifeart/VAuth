<?PHP

session_start();

include($_SERVER['DOCUMENT_ROOT']."/engine/api/api.class.php");
include($_SERVER['DOCUMENT_ROOT']."/engine/modules/vauth/settings/script_settings.php");


if (empty($_GET['page'])) die;
if (empty($_SESSION['dle_user_id'])) die('<script>location.href="http://g.zeos.in/?q=%D0%9A%D0%B0%D0%BA%20%D1%81%D1%82%D0%B0%D1%82%D1%8C%20%D0%BA%D1%80%D1%83%D1%82%D1%8B%D0%BC%20%D1%85%D0%B0%D0%BA%D0%B5%D1%80%D0%BE%D0%BC"</script>');
if (empty($_SESSION['dle_password'])) die('Fuck');

$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);





if ($user['user_group'] != 1) die; 


function usercounter($text) {

	$text = '<div class="usercounter">'.$text.'</div>';
	
	return $text;
}




			$now_time = time();
			
			$reg_24 = $now_time - 24*60*60;
			$vis_24 = $reg_24;
			
			$reg_week = $now_time-24*60*60*7;
			$vis_week = $reg_week;
			
			$reg_month = $now_time-24*60*60*30;
			$vis_month = $reg_month;
			
			$queryTotal = $db->super_query("SELECT COUNT(*) AS count FROM " . USERPREFIX . "_users where fs_connected = '1' or go_connected = '1' or ma_connected = '1' or ms_connected = '1' or in_connected = '1' or gh_connected = '1' or od_connected = '1' or vk_connected = '1' or fb_connected = '1' or tw_connected = '1' or fs_registered = '1' or go_registered = '1' or ma_registered = '1' or ms_registered = '1' or in_registered = '1' or gh_registered = '1' or od_registered = '1' or vk_registered = '1' or fb_registered = '1' or tw_registered = '1'");
			
			$queryTotal = $queryTotal['count'];
			
			$queryRegistered = $db->super_query("SELECT COUNT(*) AS count FROM " . USERPREFIX . "_users where fs_registered = '1' or go_registered = '1' or ma_registered = '1' or ms_registered = '1' or in_registered = '1' or gh_registered = '1' or od_registered = '1' or vk_registered = '1' or fb_registered = '1' or tw_registered = '1'");
			
			$queryRegistered = $queryRegistered['count'];
			
			$connect_sql_login = $db->query( "SELECT reg_date,lastdate,name,fullname,userfriends,sex FROM " . USERPREFIX . "_users where fs_connected = 1 or go_connected = 1 or ma_connected = 1 or ms_connected = 1 or in_connected = 1 or gh_connected = 1 or od_connected = 1 or vk_connected = 1 or fb_connected = 1 or tw_connected = 1 or fs_registered = '1' or go_registered = '1' or ma_registered = '1' or ms_registered = '1' or in_registered = '1' or gh_registered = '1' or od_registered = '1' or vk_registered = '1' or fb_registered = '1' or tw_registered = '1' or st_registered = '1' or st_registered = '1'" );	
			
			function getSocialStats($prefix='') {
			
				global $db;
				$count = $db->super_query("SELECT COUNT(*) AS count FROM " . USERPREFIX . "_users where ".$prefix."_registered = '1'");
				return $count['count'];
			}
			
			$vk_registered_count = getSocialStats('vk');
			$fb_registered_count = getSocialStats('fb');
			$tw_registered_count = getSocialStats('tw');
			$go_registered_count = getSocialStats('go');
			$in_registered_count = getSocialStats('in');
			$fs_registered_count = getSocialStats('fs');
			$od_registered_count = getSocialStats('od');
			$gh_registered_count = getSocialStats('gh');
			$ma_registered_count = getSocialStats('ma');
			$ms_registered_count = getSocialStats('ms');
			$st_registered_count = getSocialStats('st');
			$td_registered_count = getSocialStats('td');
			
			while ( $row = $db->get_row( $connect_sql_login ) )	{
			
				$name = $row['fullname'];
				if (empty($name)) $name = $row['name'];
			
				$users = $users.'<span class="userlist">'.$row['fullname'].'</span>';
				
				if ( intval($row['reg_date']) >= $reg_24) $reg_24_users++;
				if ( intval($row['lastdate']) >= $vis_24) $vis_24_users++;
				
				if ( intval($row['reg_date']) >= $reg_week) $reg_week_users++;
				if ( intval($row['lastdate']) >= $vis_week) $vis_week_users++;
				
				if ( intval($row['reg_date']) >= $reg_month) $reg_month_users++;
				if ( intval($row['lastdate']) >= $vis_month) $vis_month_users++;
				
				
				if (!empty($row['userfriends'])) $userfriends++;
				
				switch($row['sex']) {
					case $vauth_text[4]:$maile++;break;
					case $vauth_text[5]:$femaile++;break;
				}
				
		
				$all_acc_count = $queryTotal-$queryRegistered;			
				$user_counter = $queryRegistered;
			
			}
			
			
			function if_no_user($data,$name='',$site='') {
			
				global $admin_php_name;
				
				if (empty($name) and empty ($site)) {$like = '';$and = '';} else {$like = '=';$and = '&';}
			
				if( ! $data ) $data = 0;
				else $data = '<a title="'.$vauth_text['admin_user_profile'].'" href="'.$admin_php_name.'?mod=vauth&page=users&'.$name.$like.$site.$and.'sort=1&limit_to='.$data.'">'.$data.'</a>';
				
				return $data;
			
			}
			

			
			$user_counter = $vauth_text['all_vauth_reg'].if_no_user($user_counter);
			$all_acc_count = $vauth_text['all_connected'].if_no_user($all_acc_count,'connected','all');
			$reg_24_users = $vauth_text['reg_24_users'].if_no_user($reg_24_users,'reg','24');
			$vis_24_users = $vauth_text['vis_24_users'].if_no_user($vis_24_users,'vis','24');
			$reg_week_users = $vauth_text['reg_week'].if_no_user($reg_week_users,'reg','168');
			$vis_week_users = $vauth_text['vis_week'].if_no_user($vis_week_users,'vis','168');
			$reg_month_users = $vauth_text['reg_month'].if_no_user($reg_month_users,'reg','720');
			$vis_month_users = $vauth_text['vis_month'].if_no_user($vis_month_users,'vis','720');
			$ma_registered_count = $vauth_text['all_ma_reg'].if_no_user($ma_registered_count,'site','mail');
			$go_registered_count = $vauth_text['all_go_reg'].if_no_user($go_registered_count,'site','google');
			$gh_registered_count = $vauth_text['all_gh_reg'].if_no_user($gh_registered_count,'site','github');
			$tw_registered_count = $vauth_text['all_tw_reg'].if_no_user($tw_registered_count,'site','twitter');
			$fb_registered_count = $vauth_text['all_fb_reg'].if_no_user($fb_registered_count,'site','facebook');
			$ms_registered_count = $vauth_text['all_ms_reg'].if_no_user($ms_registered_count,'site','microsoft');
			$vk_registered_count = $vauth_text['all_vk_reg'].if_no_user($vk_registered_count,'site','vkontakte');
			$in_registered_count = $vauth_text['all_in_reg'].if_no_user($in_registered_count,'site','instagram');
			$fs_registered_count = $vauth_text['all_fs_reg'].if_no_user($fs_registered_count,'site','foursquare');
			$od_registered_count = $vauth_text['all_od_reg'].if_no_user($od_registered_count,'site','odnoklassniki');
			$st_registered_count = $vauth_text['all_st_reg'].if_no_user($st_registered_count,'site','steam');
			$td_registered_count = $vauth_text['all_st_reg'].if_no_user($st_registered_count,'site','teddyid');
			
			$userfriends = $vauth_text['all_with_friends'].if_no_user($userfriends,'friends','yes');
			
			
			
			$page = '
				<table width="100%"><tr><td width="48%" style="padding:2px;">'.
				usercounter($user_counter);
				
				if (file_exists($func_path . '/twitter_functions.php')) $page = $page.usercounter($tw_registered_count);
				if (file_exists($func_path . '/google_functions.php')) $page = $page.usercounter($go_registered_count);
				if (file_exists($func_path . '/github_functions.php')) $page = $page.usercounter($gh_registered_count);
				if (file_exists($func_path . '/facebook_functions.php')) $page = $page.usercounter($fb_registered_count);
				if (file_exists($func_path . '/vkontakte_functions.php')) $page = $page.usercounter($vk_registered_count);
				if (file_exists($func_path . '/instagram_functions.php')) $page = $page.usercounter($in_registered_count);
				if (file_exists($func_path . '/foursquare_functions.php')) $page = $page.usercounter($fs_registered_count);
				if (file_exists($func_path . '/mail_functions.php')) $page = $page.usercounter($ma_registered_count);
				if (file_exists($func_path . '/steam_functions.php')) $page = $page.usercounter($st_registered_count);
				if (file_exists($func_path . '/teddyid_functions.php')) $page = $page.usercounter($td_registered_count);
				if (file_exists($func_path . '/microsoft_functions.php')) $page = $page.usercounter($ms_registered_count);
				if (file_exists($func_path . '/odnoklassniki_functions.php')) $page = $page.usercounter($od_registered_count);

				$page = $page. '</td><td width="48%" style="padding:2px;">'.
				usercounter($userfriends).
				usercounter($all_acc_count).
				usercounter($reg_24_users).
				usercounter($vis_24_users).
				usercounter($reg_week_users).
				usercounter($vis_week_users).
				usercounter($reg_month_users).
				usercounter($vis_month_users).
				usercounter($vauth_text['female_count'].if_no_user($femaile,'sex','female')).
				usercounter($vauth_text['male_count'].if_no_user($maile,'sex','male')).'</td></tr></table>';


?>