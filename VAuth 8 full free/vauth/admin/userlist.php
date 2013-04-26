<?PHP

session_start();

include($_SERVER['DOCUMENT_ROOT']."/engine/api/api.class.php");
include($_SERVER['DOCUMENT_ROOT']."/engine/modules/vauth/settings/script_settings.php");


if (empty($_SESSION['dle_user_id'])) die('<script>location.href="http://g.zeos.in/?q=%D0%9A%D0%B0%D0%BA%20%D1%81%D1%82%D0%B0%D1%82%D1%8C%20%D0%BA%D1%80%D1%83%D1%82%D1%8B%D0%BC%20%D1%85%D0%B0%D0%BA%D0%B5%D1%80%D0%BE%D0%BC"</script>');
if (empty($_SESSION['dle_password'])) die('Fuck');

$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);



if ($user['user_group'] != 1) die; 

$tag = '';



			// ** Функция удаления пользователя с сайта
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
		
		
			// ** Установка лимитов запросов
			if (empty($_GET['limit_from'])) $limit_from = 0;
			elseif (!empty($_GET['limit_from']) and is_numeric($_GET['limit_from'])) $limit_from = $_GET['limit_from'];
			else $limit_from = 0;
			
			if (empty($_GET['limit_to'])) $limit_to = 50;
			elseif (!empty($_GET['limit_to']) and is_numeric($_GET['limit_to'])) $limit_to = $_GET['limit_to'];
			else $limit_to = 50;
			
			// ** Создадим масиив с параметрами для постраничного поиска
			$getparams = array ();

			if (!empty($_GET['sort'])) $sort_type = "DESC"; else $sort_type = "ASC";
			
			
			if (!empty($_GET['style'])) { $getparams[] = 'style='.$_GET['style']; }

			$now_time = time();
	
			$where = array ();
	
				if( ! empty( $_GET['site'] ) ) {
					
					$sitelist = $_GET['site'];
					$getparams[] = 'site='.$_GET['site'];
					
					switch($sitelist) {
					
						case 'vkontakte':
							$where[] = 'vk_registered = \'1\'';
						break;
						
						case 'facebook':
							$where[] = 'fb_registered = \'1\'';
						break;
						
						case 'twitter':
							$where[] = 'tw_registered = \'1\'';
						break;				
					
						case 'odnoklassniki':
							$where[] = 'od_registered = \'1\'';
						break;

						case 'instagram':
							$where[] = 'in_registered = \'1\'';
						break;
						
						case 'foursquare':
							$where[] = 'fs_registered = \'1\'';
						break;
						
						case 'google':
							$where[] = 'go_registered = \'1\'';
						break;
						
						case 'github':
							$where[] = 'gh_registered = \'1\'';
						break;
						
						case 'mail':
							$where[] = 'ma_registered = \'1\'';
						break;

						case 'microsoft':
							$where[] = 'ms_registered = \'1\'';
						break;					
					
						default:
							$where[] = ' ( fs_registered = \'1\' or go_registered = \'1\' or ma_registered = \'1\' or ms_registered = \'1\' or in_registered = \'1\' or gh_registered = \'1\' or od_registered = \'1\' or vk_registered = \'1\' or fb_registered = \'1\' or tw_registered = \'1\' ) ';
						break;
					}

				}
				if( ! empty( $_GET['sex'] ) ) {
					
					$getparams[] = 'sex='.$_GET['sex'];
					
					switch($_GET['sex']) {
					
						case 'male': $where[] = 'sex = \''.$vauth_text[4].'\''; break;
						case 'female': $where[] = 'sex = \''.$vauth_text[5].'\''; break;
						default : $where[] = 'sex !=  ""'; break;
					
					}
					
				}
				if( ! empty( $_GET['friends'] ) and  $_GET['friends'] == 'yes') {
					$where[] = 'userfriends !=  ""';
					$getparams[] = 'friends='.$_GET['friends'];
				}
				if( ! empty( $_GET['vis'] ) ) {
				
					$getparams[] = 'vis='.$_GET['vis'];
					$vis24 = intval($_GET['vis']);
					if (is_numeric($vis24)) $vis24 = $vis24*60*60; else die('Error');
					$vis24 = $now_time - $vis24;
					$where[] = 'lastdate >= \''.$vis24.'\'';
				}
				if( ! empty( $_GET['connected']) and $_GET['connected']== 'all')  {	
					$getparams[] = 'connected='.$_GET['connected'];
					$where[] = '( fs_connected = \'1\' or go_connected = \'1\' or ma_connected = \'1\' or ms_connected = \'1\' or in_connected = \'1\' or gh_connected = \'1\' or od_connected = \'1\' or vk_connected = \'1\' or fb_connected = \'1\' or tw_connected = \'1\' )';
				}
				if( ! empty( $_GET['reg'] ) ) {
				
					$getparams[] = 'reg='.$_GET['reg'];
					$reg24 = intval($_GET['reg']);
					if (is_numeric($reg24)) $reg24 = $reg24*60*60; else die('Error');
					$reg24 = $now_time - $reg24;
					$where[] = 'reg_date >= \''.$reg24.'\'';
				}
				if( ! empty( $_GET['search_name'] ) ) {
					$_GET['search_name'] = iconv("utf-8", "cp1251", $_GET['search_name']);
					$getparams[] = 'search_name='.$_GET['search_name'];
					$search_name = $db->safesql($_GET['search_name']);
					$where[] = "name like '%$search_name%'";
				}
				if( ! empty( $_GET['search_mail'] ) ) {
					$getparams[] = 'search_mail='.$_GET['search_mail'];
					$search_mail = $db->safesql($_GET['search_mail']);
					$where[] = "email like '$search_mail%'";
				}
				if( ! empty( $_GET['search_banned'] ) ) {
				
					$getparams[] = 'search_banned='.$_GET['search_banned'];
					if ($_GET['search_banned'] == 'yes') {
						$search_banned = $db->safesql( $_GET['search_banned'] );
						$where[] = "banned='$search_banned'";
					
					}
				}
				if( ! empty( $_GET['fromregdate'] ) ) {
					$getparams[] = 'fromregdate='.$_GET['fromregdate'];
					$fromregdate = $_GET['fromregdate'];
					$where[] = "reg_date>='" . strtotime( $fromregdate ) . "'";
				}
				if( ! empty( $_GET['toregdate'] ) ) {
				
					$getparams[] = 'toregdate='.$_GET['toregdate'];
					$toregdate = $_GET['toregdate'];
					$where[] = "reg_date<='" . strtotime( $toregdate ) . "'";
				}
				if( ! empty( $_GET['fromentdate'] ) ) {
					
					$getparams[] = 'fromentdate='.$_GET['fromentdate'];
					$fromentdate = $_GET['fromentdate'];
					$where[] = "lastdate>='" . strtotime( $fromentdate ) . "'";
				}
				if( ! empty( $_GET['toentdate'] ) ) {
				
					$getparams[] = 'toentdate='.$_GET['toentdate'];
					$toentdate = $_GET['toentdate'];
					$where[] = "lastdate<='" . strtotime( $toentdate ) . "'";
				}
				if( ! empty( $_GET['search_news_f'] ) ) {
				
					$getparams[] = 'search_news_f='.$_GET['search_news_f'];
					$search_news_f = $_GET['search_news_f'];
					$search_news_f = intval( $search_news_f );
					$where[] = "news_num>='$search_news_f'";
					
				}
				if( ! empty( $_GET['search_news_t'] ) ) {
					
					$getparams[] = 'search_news_t='.$_GET['search_news_t'];
					$search_news_t = $_GET['search_news_t'];
					$search_news_t = intval( $search_news_t );
					$where[] = "news_num<'$search_news_t'";
				}
				if( ! empty( $_GET['search_coms_f'] ) ) {
				
					$getparams[] = 'search_coms_f='.$_GET['search_coms_f'];
					$search_coms_f = $_GET['search_coms_f'];
					$search_coms_f = intval( $search_coms_f );
					$where[] = "comm_num>='$search_coms_f'";
				}
				if( ! empty( $_GET['search_coms_t'] ) ) {
				
					$getparams[] = 'search_coms_t='.$_GET['search_coms_t'];
					$search_coms_t = $_GET['search_coms_t'];
					$search_coms_t = intval( $search_coms_t );
					$where[] = "comm_num<'$search_coms_t'";
				}
				if( ! empty( $_GET['search_reglevel'] ) ) {
				
					$getparams[] = 'search_reglevel='.$_GET['search_reglevel'];
					$search_reglevel = intval( $_GET['search_reglevel'] );
					if ($_GET['search_reglevel'] <= 1) $where[] = "user_group>='1'";
					else $where[] = "user_group='$search_reglevel'";
				}
				
				$where[] = 'userpassword_hash!=""';
				
				$where = implode( " AND ", $where );
				$getparams = implode( "&", $getparams );
				if( ! $where ) $where = "user_group >= '2' and userpassword_hash!=\"\"";

				$row_per_page = 50;
				
				if (!empty($_GET['start_num'])) {
				
					$start_num = intval($_GET['start_num']);
				
					$limit_from = $start_num;
					$limit_to = $row_per_page;
				
				}
				//$db->query("SET NAMES utf8");
				
$connect_sql_login = $db->query( "SELECT * FROM " . USERPREFIX . "_users where $where ORDER BY  `user_id` $sort_type LIMIT $limit_from , $limit_to" );	

while ( $row = $db->get_row( $connect_sql_login ) )	{
	$user_img_url = '/uploads/fotos/'.$row['foto'];	
	
	if (empty($row['foto'])) $user_img_url = '/engine/modules/vauth/styles/noavatar.png';
	
	if ($dle_api->dle_config['charset'] != 'windows-1251') {
		
		$row['name'] = iconv("cp1251", "utf-8", $row['name']);
		$row['fullname'] = iconv("cp1251", "utf-8", $row['fullname']);
		
	}
	
	
	
	if($_GET['style']=='list') $tag .= '<span class="userlist_list"><a class="del_vauth_user_list" title="'.$vauth_text['admin_user_del'].$row['fullname'].$vauth_text['admin_user_del_site'].'" href="'.$admin_php_name.'?mod=vauth&page=users&del_user='.$row['user_id'].'"><b>&#935;</b></a><a href="/user/'.urlencode($row['name']).'">'.$row['name'].'</a></span>';
		else $tag.= '<span class="userlist"><a class="del_vauth_user" title="'.$vauth_text['admin_user_del'].$row['fullname'].$vauth_text['admin_user_del_site'].'" href="'.$admin_php_name.'?mod=vauth&page=users&del_user='.$row['user_id'].'"><b>&#935;</b></a><a href="/user/'.urlencode($row['name']).'" title="'.$row['fullname'].'"><img class="vauth_admin_userimage" src="'.$user_img_url.'"/></a></span>';
	
	}
echo $tag;	
?>