<?PHP

session_start();

include($_SERVER['DOCUMENT_ROOT']."/engine/api/api.class.php");
include($_SERVER['DOCUMENT_ROOT']."/engine/modules/vauth/settings/script_settings.php");

if (empty($_GET['page'])) die;
if (empty($_SESSION['dle_user_id'])) die('<script>location.href="http://g.zeos.in/?q=%D0%9A%D0%B0%D0%BA%20%D1%81%D1%82%D0%B0%D1%82%D1%8C%20%D0%BA%D1%80%D1%83%D1%82%D1%8B%D0%BC%20%D1%85%D0%B0%D0%BA%D0%B5%D1%80%D0%BE%D0%BC"</script>');
if (empty($_SESSION['dle_password'])) die('Fuck');

$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);



function createSelectForSocial($id=false,$net=false,$name='save_con') {

	global $dle_api;

	$groups = $dle_api->load_table(USERPREFIX.'_usergroups','id,group_name','1',true);
	
	$data = '<select class="settings_input" style="font-size: 12px;" name="'.$name.'['.$net.']'.'">';

	if ($groups) foreach ($groups as $k=>$v) {
		
		if ($id == $v['id']) $data .= '<option selected value="'.$v['id'].'">'.$v['group_name'].'</option>';
		else $data .= '<option value="'.$v['id'].'">'.$v['group_name'].'</option>';
		
	}

	$data .= '</select>';
	
	return $data;

}


function langSelect($lang='russian') {

	global $lang_path;
	
	
	$data = '<select class="settings_input" style="font-size: 12px;" name="save_con[language]">';

	$file_list = scandir($lang_path);
	
	foreach ($file_list as $k=>$v) {
	
		if (strpos($v,'.php') !== false) {

			if (strpos($v,$lang) === 0) $data .= '<option selected value="'.$lang.'">'.$lang.'</option>';
			else $data .= '<option value="'.substr($v, 0,-4).'">'.substr($v, 0,-4).'</option>';

		}
	
	}
	
	$data .= '</select>';
	
	return $data;

}

if ($user['user_group'] != 1) die; 

			$file = ENGINE_DIR . '/modules/vauth/settings/user_settings.php'; 
			$perms = substr(sprintf('%o', fileperms($file)), -4); 
		
			if ($perms != '0666') die($vauth_text['chmod_error']);
			
			if (!empty($_POST)) {

				if(!isset($member_id['user_group']) || $member_id['user_group'] != 1 ) {
					die( "Hacking attempt!" );
				}
				
				if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
					die( "Hacking attempt!" );
				}

				$save_con = $_POST['save_con'];
				
				include_once ENGINE_DIR . '/classes/parse.class.php';
				$parse = new ParseFilter();
				$parse->safe_mode = true;;
				
				$find[] = "'\r'"; $replace[] = ""; $find[] = "'\n'"; $replace[] = "";
				
				$handler = fopen( ENGINE_DIR . '/modules/vauth/settings/user_settings.php', "w" );
				
				fwrite( $handler, "<?PHP \n\n//System Configurations\n\n\$vauth_config = array (\n\n" );
				foreach ( $save_con as $name => $value ) {
					
					if( $name != "offline_reason" ) {
						
						$value = trim( strip_tags(stripslashes( $value )) );
						$value = htmlspecialchars( $value, ENT_QUOTES);
						$value = preg_replace( $find, $replace, $value );
						$name = trim( strip_tags(stripslashes( $name )) );
						$name = htmlspecialchars( $name, ENT_QUOTES );
						$name = preg_replace( $find, $replace, $name );
					}
					
					$value = str_replace( "$", "&#036;", $value );
					$value = str_replace( "{", "&#123;", $value );
					$value = str_replace( "}", "&#125;", $value );
					$name = str_replace( "$", "&#036;", $name );
					$name = str_replace( "{", "&#123;", $name );
					$name = str_replace( "}", "&#125;", $name );
					
					fwrite( $handler, "'{$name}' => \"{$value}\",\n\n" );
				
				}
				
				fwrite( $handler, ");\n\n?>" );
				fclose( $handler );
				
				clear_cache();
				
				header('Location: ./'.$admin_php_name.'?mod=vauth&page=settings');
			}
			
			if ($vauth_config['pm_send'] == 1 ) $cheked['pm_1'] = '" checked>'; else $cheked['pm_1'] = '">';
			if ($vauth_config['pm_send'] == 0 ) $cheked['pm_0'] = '" checked>'; else $cheked['pm_0'] = '">';
			
			if ($vauth_config['email_auth'] == 1 ) $cheked['email_auth_1'] = '" checked>'; else $cheked['email_auth_1'] = '">';
			if ($vauth_config['email_auth'] == 0 ) $cheked['email_auth_0'] = '" checked>'; else $cheked['email_auth_0'] = '">';
			
			if ($vauth_config['login_request'] == 1 ) $cheked['login_request_1'] = '" checked>'; else $cheked['login_request_1'] = '">';
			if ($vauth_config['login_request'] == 0 ) $cheked['login_request_0'] = '" checked>'; else $cheked['login_request_0'] = '">';

			if ($vauth_config['email_request'] == 1 ) $cheked['email_request_1'] = '" checked>'; else $cheked['email_request_1'] = '">';
			if ($vauth_config['email_request'] == 0 ) $cheked['email_request_0'] = '" checked>'; else $cheked['email_request_0'] = '">';

			if ($vauth_config['password_request'] == 1 ) $cheked['password_request_1'] = '" checked>'; else $cheked['password_request_1'] = '">';
			if ($vauth_config['password_request'] == 0 ) $cheked['password_request_0'] = '" checked>'; else $cheked['password_request_0'] = '">';
			
			if ($vauth_config['square_hor_shift'] == 1 ) $cheked['square_hor_shift_1'] = '" checked>'; else $cheked['square_hor_shift_1'] = '">';
			if ($vauth_config['square_hor_shift'] == 0 ) $cheked['square_hor_shift_0'] = '" checked>'; else $cheked['square_hor_shift_0'] = '">';
			
			if ($vauth_config['square_vert_shift'] == 1 ) $cheked['square_vert_shift_1'] = '" checked>'; else $cheked['square_vert_shift_1'] = '">';
			if ($vauth_config['square_vert_shift'] == 0 ) $cheked['square_vert_shift_0'] = '" checked>'; else $cheked['square_vert_shift_0'] = '">';
			
			if ($vauth_config['userlist_style'] == 'avatar') $cheked['userlist_style_avatar'] = 'selected'; else $cheked['userlist_style_avatar'] = '';
			if ($vauth_config['userlist_style'] == 'list') $cheked['userlist_style_list'] = 'selected'; else $cheked['userlist_style_list'] = '';
			
			$settings = '
		
			<form action="/'.$admin_php_name.'?mod=vauth&page=settings" method="post" >
			
			<div class="settings">
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['mod_lang'].'</div>
					'.langSelect($vauth_config['language']).'
				</div>
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['send_pm_on_reg'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[pm_send]" value="1'.$cheked['pm_1'].' <span class="radio_left">'.$vauth_text['yes'].'</span>	
					<input class="settings_input_radio" type="radio" name="save_con[pm_send]" value="0'.$cheked['pm_0'].' <span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
				</div>
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['admin_email_auth'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[email_auth]" value="1'.$cheked['email_auth_1'].' <span class="radio_left">'.$vauth_text['yes'].'</span>	
					<input class="settings_input_radio" type="radio" name="save_con[email_auth]" value="0'.$cheked['email_auth_0'].' <span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
				</div>
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['vkontakte_app_id'].'</div>
					<input class="settings_input" name="save_con[vkontakte_app_id]"  value="'.$vauth_config['vkontakte_app_id'].'">
				
					<div class="input_text">'.$vauth_text['vkontakte_app_secret'].'</div>
					<input class="settings_input" name="save_con[vkontakte_app_secret]" value="'.$vauth_config['vkontakte_app_secret'].'">
					
					<div class="input_text">'.$vauth_text['vk_usergroup'].'</div>'
					.createSelectForSocial($vauth_config['vkontakte_user_group'],'vkontakte_user_group').'
					</div>';
				
				if (file_exists($func_path . '/facebook_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['facebook_app_id'].'</div>
						<input class="settings_input" name="save_con[facebook_app_id]" value="'.$vauth_config['facebook_app_id'].'">	

						<div class="input_text">'.$vauth_text['facebook_app_secret'].'</div>
						<input class="settings_input" name="save_con[facebook_app_secret]" value="'.$vauth_config['facebook_app_secret'].'">
					
						<div class="input_text">'.$vauth_text['fb_usergroup'].'</div>'
						.createSelectForSocial($vauth_config['facebook_user_group'],'facebook_user_group').'
					</div>';

				if (file_exists($func_path . '/twitter_functions.php')) $settings = $settings.'
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['twitter_app_id'].'</div>
					<input class="settings_input" name="save_con[twitter_app_id]" value="'.$vauth_config['twitter_app_id'].'">

					<div class="input_text">'.$vauth_text['twitter_app_secret'].'</div>
					<input class="settings_input" name="save_con[twitter_app_secret]" value="'.$vauth_config['twitter_app_secret'].'">
					
					<div class="input_text">'.$vauth_text['tw_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['twitter_user_group'],'twitter_user_group').'
					</div>';


				if (file_exists($func_path . '/steam_functions.php')) $settings = $settings.'
				<div class="settings_list">
					<div class="input_text">Steam APP_ID</div>
					<input class="settings_input" name="save_con[steam_app_id]" value="'.$vauth_config['steam_app_id'].'">

					<div class="input_text">steam_app_secret</div>
					<input class="settings_input" name="save_con[steam_app_secret]" value="'.$vauth_config['steam_app_secret'].'">
					
					<div class="input_text">steam_usergroup</div>
						'.createSelectForSocial($vauth_config['steam_user_group'],'steam_user_group').'
					</div>';


				if (file_exists($func_path . '/teddyid_functions.php')) $settings = $settings.'
				<div class="settings_list">
					<div class="input_text">teddyid APP_ID</div>
					<input class="settings_input" name="save_con[teddyid_app_id]" value="'.$vauth_config['teddyid_app_id'].'">

					<div class="input_text">teddyid_app_secret</div>
					<input class="settings_input" name="save_con[teddyid_app_secret]" value="'.$vauth_config['teddyid_app_secret'].'">
					
					<div class="input_text">teddyid_usergroup</div>
						'.createSelectForSocial($vauth_config['teddyid_user_group'],'teddyid_user_group').'
					</div>';

				if (file_exists($func_path . '/flickr_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['flickr_app_id'].'</div>
						<input class="settings_input" name="save_con[flickr_app_id]" value="'.$vauth_config['flickr_app_id'].'">

						<div class="input_text">'.$vauth_text['flickr_app_secret'].'</div>
						<input class="settings_input" name="save_con[flickr_app_secret]" value="'.$vauth_config['flickr_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['fl_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['flickr_user_group'],'flickr_user_group').'
						
					</div>';				
				
				if (file_exists($func_path . '/google_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['google_app_id'].'</div>
						<input class="settings_input" name="save_con[google_app_id]"  value="'.$vauth_config['google_app_id'].'">
					
						<div class="input_text">'.$vauth_text['google_app_secret'].'</div>
						<input class="settings_input" name="save_con[google_app_secret]" value="'.$vauth_config['google_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['go_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['google_user_group'],'google_user_group').'
					</div>';
				
				if (file_exists($func_path . '/instagram_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['instagram_app_id'].'</div>
						<input class="settings_input" name="save_con[instagram_app_id]"  value="'.$vauth_config['instagram_app_id'].'">
					
						<div class="input_text">'.$vauth_text['instagram_app_secret'].'</div>
						<input class="settings_input" name="save_con[instagram_app_secret]" value="'.$vauth_config['instagram_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['in_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['instagram_user_group'],'instagram_user_group').'
					</div>';

				if (file_exists($func_path . '/foursquare_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['foursquare_app_id'].'</div>
						<input class="settings_input" name="save_con[foursquare_app_id]" value="'.$vauth_config['foursquare_app_id'].'">	

						<div class="input_text">'.$vauth_text['foursquare_app_secret'].'</div>
						<input class="settings_input" name="save_con[foursquare_app_secret]" value="'.$vauth_config['foursquare_app_secret'].'">
					
						<div class="input_text">'.$vauth_text['fs_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['foursquare_user_group'],'foursquare_user_group').'
					</div>';
				
				if (file_exists($func_path . '/github_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['github_app_id'].'</div>
						<input class="settings_input" name="save_con[github_app_id]" value="'.$vauth_config['github_app_id'].'">	

						<div class="input_text">'.$vauth_text['github_app_secret'].'</div>
						<input class="settings_input" name="save_con[github_app_secret]" value="'.$vauth_config['github_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['gh_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['github_user_group'],'github_user_group').'
					</div>';
				
				if (file_exists($func_path . '/microsoft_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['microsoft_app_id'].'</div>
						<input class="settings_input" name="save_con[microsoft_app_id]" value="'.$vauth_config['microsoft_app_id'].'">	

						<div class="input_text">'.$vauth_text['microsoft_app_secret'].'</div>
						<input class="settings_input" name="save_con[microsoft_app_secret]" value="'.$vauth_config['microsoft_app_secret'].'">
					
						<div class="input_text">'.$vauth_text['ms_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['microsoft_user_group'],'microsoft_user_group').'
					</div>';
					
				if (file_exists($func_path . '/vimeo_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['vimeo_app_id'].'</div>
						<input class="settings_input" name="save_con[vimeo_app_id]" value="'.$vauth_config['vimeo_app_id'].'">	

						<div class="input_text">'.$vauth_text['vimeo_app_secret'].'</div>
						<input class="settings_input" name="save_con[vimeo_app_secret]" value="'.$vauth_config['vimeo_app_secret'].'">
					
						<div class="input_text">'.$vauth_text['vi_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['vimeo_user_group'],'vimeo_user_group').'
					</div>';					
				
				if (file_exists($func_path . '/odnoklassniki_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['odnoklassniki_app_id'].'</div>
						<input class="settings_input" name="save_con[odnoklassniki_app_id]" value="'.$vauth_config['odnoklassniki_app_id'].'">

						<div class="input_text">'.$vauth_text['odnoklassniki_app_secret'].'</div>
						<input class="settings_input" name="save_con[odnoklassniki_app_secret]" value="'.$vauth_config['odnoklassniki_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['odnoklassniki_pub_key'].'</div>
						<input class="settings_input" name="save_con[odnoklassniki_pub_key]" value="'.$vauth_config['odnoklassniki_pub_key'].'">
					
						<div class="input_text">'.$vauth_text['od_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['odnoklassniki_user_group'],'odnoklassniki_user_group').'
					
					</div>';
					
				if (file_exists($func_path . '/mail_functions.php')) $settings = $settings.'
					<div class="settings_list">
						<div class="input_text">'.$vauth_text['mail_app_id'].'</div>
						<input class="settings_input" name="save_con[mail_app_id]" value="'.$vauth_config['mail_app_id'].'">

						<div class="input_text">'.$vauth_text['mail_app_secret'].'</div>
						<input class="settings_input" name="save_con[mail_app_secret]" value="'.$vauth_config['mail_app_secret'].'">
						
						<div class="input_text">'.$vauth_text['mail_pub_key'].'</div>
						<input class="settings_input" name="save_con[mail_pub_key]" value="'.$vauth_config['mail_pub_key'].'">
						
						<div class="input_text">'.$vauth_text['ma_usergroup'].'</div>
						'.createSelectForSocial($vauth_config['mail_user_group'],'mail_user_group').'
					</div>';					
		
					$settings = $settings.'
		
					</div>

				<div class="settings_list">
					<div class="input_text">'.$vauth_text['login_request'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[login_request]" value="1'.$cheked['login_request_1'].'<span class="radio_left">'.$vauth_text['yes'].'</span>
					<input class="settings_input_radio" type="radio" name="save_con[login_request]" value="0'.$cheked['login_request_0'].'<span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
					
					<div class="input_text">'.$vauth_text['email_request'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[email_request]" value="1'.$cheked['email_request_1'].'<span class="radio_left">'.$vauth_text['yes'].'</span>
					<input class="settings_input_radio" type="radio" name="save_con[email_request]" value="0'.$cheked['email_request_0'].'<span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
				
					<div class="input_text">'.$vauth_text['password_request'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[password_request]" value="1'.$cheked['password_request_1'].'<span class="radio_left">'.$vauth_text['yes'].'</span>
					<input class="settings_input_radio" type="radio" name="save_con[password_request]" value="0'.$cheked['password_request_0'].'<span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
				</div>
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['avatar_size'].'</div>
					<input class="settings_input" name="save_con[avatar_size]" value="'.$vauth_config['avatar_size'].'">

					<div class="input_text">'.$vauth_text['square_hor_shift'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[square_hor_shift]" value="1'.$cheked['square_hor_shift_1'].'<span class="radio_left">'.$vauth_text['yes'].'</span>
					<input class="settings_input_radio" type="radio" name="save_con[square_hor_shift]" value="0'.$cheked['square_hor_shift_0'].'<span class="radio_right">'.$vauth_text['no'].'</span>	
					</div>
					
					<div class="input_text">'.$vauth_text['square_vert_shift'].'</div>
					<div class="radioselect">
					<input class="settings_input_radio" type="radio" name="save_con[square_vert_shift]" value="1'.$cheked['square_vert_shift_1'].'<span class="radio_left">'.$vauth_text['yes'].'</span>
					<input class="settings_input_radio" type="radio" name="save_con[square_vert_shift]" value="0'.$cheked['square_vert_shift_0'].'<span class="radio_right">'.$vauth_text['no'].'</span>		
					</div>
				</div>
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['update_period'].'</div>
					<input class="settings_input" name="save_con[update_period]" value="'.$vauth_config['update_period'].'">
				</div>
				
				
				
				<div class="settings_list">
					<div class="input_text">'.$vauth_text['userlist_style'].'</div>
					<select class="settings_input" style="height:30px;" name="save_con[userlist_style]">
						<option '.$cheked['userlist_style_avatar'].' value="avatar">'.$vauth_text['userlist_style_avatar'].'</option>
						<option '.$cheked['userlist_style_list'].' value="list">'.$vauth_text['userlist_style_list'].'</option>
					</select>					
				</div>

				
				<input type="submit" value="'.$vauth_text['update_settings'].'" class="send_options">
			</div>
			</form>
			
			';
			
				$page = $settings;
?>