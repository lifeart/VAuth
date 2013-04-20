<?PHP

session_start();

include($_SERVER['DOCUMENT_ROOT']."/engine/api/api.class.php");
include($_SERVER['DOCUMENT_ROOT']."/engine/modules/vauth/settings/script_settings.php");

if (empty($_GET['page'])) die;
if (empty($_SESSION['dle_user_id'])) die('<script>location.href="http://g.zeos.in/?q=%D0%9A%D0%B0%D0%BA%20%D1%81%D1%82%D0%B0%D1%82%D1%8C%20%D0%BA%D1%80%D1%83%D1%82%D1%8B%D0%BC%20%D1%85%D0%B0%D0%BA%D0%B5%D1%80%D0%BE%D0%BC"</script>');
if (empty($_SESSION['dle_password'])) die('Fuck');

$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);



if ($user['user_group'] != 1) die; 

	$menus = 'users';
	
	// ** ‘ункци€ удалени€ пользовател€ с сайта
	if (!empty($_GET['del_user']) and is_numeric($_GET['del_user']) ) {
		
	$id = $_GET['del_user'];
	
				$row_deluser = $db->super_query( "SELECT user_id, user_group, name, foto FROM " . USERPREFIX . "_users WHERE user_id='$id'" );

				if( ! $row_deluser['user_id'] ) die( "User not found" );

				if ($row_deluser['user_group'] <= 1 ) die( $lang['user_undel'] );

				$db->query( "DELETE FROM " . USERPREFIX . "_pm WHERE user_from = '{$row_deluser['name']}' AND folder = 'outbox'" );
				
				@unlink( ROOT_DIR . "/uploads/fotos/" . $row_deluser['foto'] );
				
				$db->query( "delete FROM " . USERPREFIX . "_users WHERE user_id='$id'" );
				$db->query( "delete FROM " . USERPREFIX . "_banned WHERE users_id='$id'" );
				$db->query( "delete FROM " . USERPREFIX . "_pm WHERE user='$id'" );
				$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '65', '{$row_deluser['name']}')" );
				
				header('Location: ./'.$admin_php_name.'?mod=vauth&page=users');
			}
	
			
			$selectlist = '';			

			if (file_exists($func_path . '/vkontakte_functions.php')) $selectlist = $selectlist.'<option  value="vkontakte">vkontakte</option>';
			if (file_exists($func_path . '/facebook_functions.php')) $selectlist = $selectlist.'<option  value="facebook">facebook</option>';
			if (file_exists($func_path . '/twitter_functions.php')) $selectlist = $selectlist.'<option  value="twitter">twitter</option>';
			if (file_exists($func_path . '/instagram_functions.php')) $selectlist = $selectlist.' <option  value="instagram">instagram</option>';
			if (file_exists($func_path . '/odnoklassniki_functions.php')) $selectlist = $selectlist.'<option  value="odnoklassniki">odnoklassniki</option>';
			if (file_exists($func_path . '/foursquare_functions.php')) $selectlist = $selectlist.'<option  value="foursquare">foursquare</option>';
			if (file_exists($func_path . '/google_functions.php')) $selectlist = $selectlist.'<option  value="google">google</option>';
			if (file_exists($func_path . '/github_functions.php')) $selectlist = $selectlist.'<option  value="github">github</option>';
			if (file_exists($func_path . '/mail_functions.php')) $selectlist = $selectlist.'<option  value="mail">mail</option>';
			if (file_exists($func_path . '/microsoft_functions.php')) $selectlist = $selectlist.'<option  value="microsoft">microsoft</option>';
			
			
			$vauth_search = '
			<script type="text/javascript" src="engine/skins/calendar.js"></script>
			<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />
			<table width="100%" id="ap_fields">
			<tr>
			<td width="140" style="padding:2px;">'.$vauth_text['admin_searchform_login'].'</td>
			<td style="padding-bottom:4px;"><input size="21" class="edit bk" type="text" name="search_name" id="search_name" value="'.$_GET['search_name'].'"><a href="#" class="hintanchor" onMouseover="showhint(\''.$vauth_text['admin_searchform_help1'].'\', this, event, \'300px\')">[?]</a></td>
			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_regdate'].'</td>
			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_from'].'</td>
			<td><input type="text" name="fromregdate" id="fromregdate" size="17" maxlength="16" class="edit bk" value="'.$_GET['fromregdate'].'">
			<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_reg" style="cursor: pointer; border: 0" title="'.$vauth_text['admin_searchform_date_pick'].'"/>
			<script type="text/javascript">
				Calendar.setup({
				  inputField     :    "fromregdate",     // id of the input field
				  ifFormat       :    "%Y-%m-%d",      // format of the input field
				  button         :    "f_trigger_reg",  // trigger for the calendar (button ID)
				  align          :    "Br",           // alignment 
					  timeFormat     :    "24",
					  showsTime      :    true,
				  singleClick    :    true,
				  onClose: function(obj) { $("[name=\'fromregdate\']").trigger("change"); obj.hide(); },
				});
			</script></td>
			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_to'].'</td>
			<td><input type="text" name="toregdate" id="toregdate" size="17" maxlength="16" class="edit bk" value="'.$_GET['toregdate'].'">
			<img src="engine/skins/images/img.gif"  align="absmiddle" id="t_trigger_reg" style="cursor: pointer; border: 0" title="'.$vauth_text['admin_searchform_date_pick'].'"/>
			<script type="text/javascript">
				Calendar.setup({
				  inputField     :    "toregdate",     // id of the input field
				  ifFormat       :    "%Y-%m-%d",      // format of the input field
				  button         :    "t_trigger_reg",  // trigger for the calendar (button ID)
				  align          :    "Br",           // alignment 
					  timeFormat     :    "24",
					  showsTime      :    true,
				  singleClick    :    true,
				  onClose: function(obj) { $("[name=\'toregdate\']").trigger("change"); obj.hide(); },
				});
			</script></td>

			</tr>
			<tr>
			<td style="padding:2px;">'.$vauth_text['admin_searchform_email'].'</td>
			<td><input size="21" class="edit bk" type="text" name="search_mail" id="search_mail" value="'.$_GET['search_mail'].'"><a href="#" class="hintanchor" onMouseover="showhint(\''.$vauth_text['admin_searchform_email_help'].'\', this, event, \'300px\')">[?]</a></td>

			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_lastdate'].'</td>
			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_from'].'</td>
			<td><input type="text" name="fromentdate" id="fromentdate" size="17" maxlength="16" class="edit bk" value="'.$_GET['fromentdate'].'">
			<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_ent" style="cursor: pointer; border: 0" title="'.$vauth_text['admin_searchform_date_pick'].'"/>
			<script type="text/javascript">
				Calendar.setup({
				  inputField     :    "fromentdate",     // id of the input field
				  ifFormat       :    "%Y-%m-%d",      // format of the input field
				  button         :    "f_trigger_ent",  // trigger for the calendar (button ID)
				  align          :    "Br",           // alignment 
					  timeFormat     :    "24",
					  showsTime      :    true,
				  singleClick    :    true,
				  onClose: function(obj) { $("[name=\'fromentdate\']").trigger("change"); obj.hide(); },
				});
			</script></td>
			<td style="padding-left:5px;">'.$vauth_text['admin_searchform_to'].'</td>
			<td><input type="text" name="toentdate" id="toentdate" size="17" maxlength="16" class="edit bk" value="'.$_GET['toentdate'].'">
			<img src="engine/skins/images/img.gif"  align="absmiddle" id="t_trigger_ent" style="cursor: pointer; border: 0" title="'.$vauth_text['admin_searchform_date_pick'].'"/>
			<script type="text/javascript">
				Calendar.setup({
				  inputField     :    "toentdate",     // id of the input field
				  ifFormat       :    "%Y-%m-%d",      // format of the input field
				  button         :    "t_trigger_ent",  // trigger for the calendar (button ID)
				  align          :    "Br",           // alignment 
					  timeFormat     :    "24",
					  showsTime      :    true,
				  singleClick    :    true,
				  onClose: function(obj) { $("[name=\'toentdate\']").trigger("change"); obj.hide(); },
				});
			</script></td>

			</tr>
			<tr>
				<td style="padding:2px;">'.$vauth_text['admin_searchform_ban'].'</td>
				<td><input type="checkbox" name="search_banned" id="search_banned" value="yes" ></td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_pubnum'].'</td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_from'].'</td>
				<td><input class="edit bk" type="text" name="search_news_f" id="search_news_f" size="8" maxlength="7" value="'.$_GET['search_news_f'].'"><a href="#" class="hintanchor" onMouseover="showhint(\'¬ведите количество новостей дл€ поиска.\', this, event, \'300px\')">[?]</a></td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_to'].'</td>
				<td><input class="edit bk" type="text" name="search_news_t" id="search_news_t" size="8" maxlength="7" value="'.$_GET['search_news_t'].'"><a href="#" class="hintanchor" onMouseover="showhint(\'¬ведите количество новостей дл€ поиска.\', this, event, \'300px\')">[?]</a></td>
				</tr>
				<tr>
				<td style="padding:2px;">'.$vauth_text['admin_searchform_group'].'</td>
				<td><select name="search_reglevel" id="search_reglevel">
				<option selected value="0">'.$vauth_text['admin_searchform_group_all'].'</option>
				'.get_groups().'
				</select>
				</td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_comnum'].'</td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_from'].'</td>
				<td><input class="edit bk" type="text" name="search_coms_f" id="search_coms_f" size="8" maxlength="7" value="'.$_GET['search_coms_f'].'"><a href="#" class="hintanchor" onMouseover="showhint(\'¬ведите количество комментариев дл€ поиска.\', this, event, \'300px\')">[?]</a></td>
				<td style="padding-left:5px;">'.$vauth_text['admin_searchform_to'].'</td>
				<td><input class="edit bk" type="text" name="search_coms_t" id="search_coms_t" size="8" maxlength="7" value="'.$_GET['search_coms_t'].'"><a href="#" class="hintanchor" onMouseover="showhint(\'¬ведите количество комментариев дл€ поиска.\', this, event, \'300px\')">[?]</a></td>

				</tr>
				<tr>
					<td colspan="7"><div class="hr_line"></div></td>
				</tr>
				<tr>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_sort'].'</td>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_mod'].'</td>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_usex'].'</td>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_site'].'</td>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_isfriend'].'</td>
				<td style="padding:5px;">'.$vauth_text['admin_searchform_regtime'].'</td>
				</tr>
				<tr>
				<td style="padding-left:2px;"><select name="sort" id="search_order_u">
				   <option selected value="">'.$vauth_text['admin_searchform_nd'].'</option>
				   <option  value="desc">'.$vauth_text['admin_searchform_nu'].'</option>
					</select>
				</td>
				<td style="padding-left:2px;"><select name="style" id="search_order_r">
				   <option selected value="'.$vauth_config['userlist_style'].'">'.$vauth_text['admin_searchform_lis'].'</option>
				   <option  value="list">'.$vauth_text['admin_searchform_list'].'</option>
				   <option  value="avatar">'.$vauth_text['admin_searchform_avatar'].'</option>
					</select>
				</td>
				<td style="padding-left:2px;"><select name="sex" id="search_order_l">
				   <option selected value="">'.$vauth_text['admin_searchform_nm'].'</option>
				   <option  value="female">'.$vauth_text[5].'</option>
				   <option  value="male">'.$vauth_text[4].'</option>
					</select>
				</td>
				<td style="padding-left:2px;"><select name="site" id="search_order_n">
				   <option selected value="">'.$vauth_text['admin_searchform_allsites'].'</option>
				   '.$selectlist.'
					</select>
				</td>
				<td style="padding-left:2px;" ><select name="friends" id="search_order_cs">
				   <option selected value="">'.$vauth_text['admin_searchform_nm'].'</option>
				   <option  value="yes">'.$vauth_text['admin_searchform_wfr'].'</option>
					</select>
				</td>
				<td style="padding-left:2px;" ><select name="reg" id="search_order_c">
				   <option selected value="">'.$vauth_text['admin_searchform_allt'].'</option>
				   <option  value="24">'.$vauth_text['admin_searchform_24h'].'</option>
				   <option  value="168">'.$vauth_text['admin_searchform_168h'].'</option>
				   <option  value="720">'.$vauth_text['admin_searchform_720h'].'</option>
					</select>
				</td>
				</tr>
				<tr>
					<td colspan="7"><div class="hr_line"></div></td>
				</tr>
	    <tr>
        <td>&nbsp;</td>
        <td style="padding-top:10px;"><input type="submit" class="buttons" value="'.$vauth_text['admin_searchform_find'].'" style="width:100px;"></td>
        <td style="padding-top:10px;"><a class="buttons" style="width:100px;padding:15px;padding-top:3px;padding-bottom:3px;"  href="'.$admin_php_name.'?mod=vauth&page=users">'.$vauth_text['admin_searchform_clean'].'</a></td>
        <td colspan="4" style="padding-top:10px;"><input type="reset" class="buttons" value="'.$vauth_text['admin_searchform_back'].'" style="width:100px;">

			<input type="hidden" name="page" value="users">
			<input type="hidden" name="mod" value="vauth">
			<input type="hidden" name="action" id="action" value="list">
			<input type="hidden" name="search" id="search" value="search">
			<input type="hidden" name="start_from" id="start_from" value=""></td>
		
		</tr>
				</table>
			';

			$page = $users;
?>