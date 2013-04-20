<?PHP

session_start();

include($_SERVER['DOCUMENT_ROOT']."/engine/api/api.class.php");
include($_SERVER['DOCUMENT_ROOT']."/engine/modules/vauth/settings/script_settings.php");


if (empty($_SESSION['dle_user_id'])) die('<script>location.href="http://g.zeos.in/?q=%D0%9A%D0%B0%D0%BA%20%D1%81%D1%82%D0%B0%D1%82%D1%8C%20%D0%BA%D1%80%D1%83%D1%82%D1%8B%D0%BC%20%D1%85%D0%B0%D0%BA%D0%B5%D1%80%D0%BE%D0%BC"</script>');
if (empty($_SESSION['dle_password'])) die('Fuck');

$user = $dle_api->take_user_by_id($_SESSION['dle_user_id']);



if ($user['user_group'] != 1) die; 

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
		
		
		return 1;
		header('Location: ./'.$admin_php_name.'?mod=vauth&page=users');	
		
	}
?>