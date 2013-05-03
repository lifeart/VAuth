<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Установка VAuth 8 DLE</title>

</head>


<body>

<style>
body {background-color: #333;}
h1 {
margin-left: auto;
color: #fff;
display: block;
}
h2 {
color: rgb(67, 212, 231);
margin-left: 30px;
display: block;
}

h2 a {
color: rgb(223, 73, 73);
}
</style>

<?PHP

include_once('engine/api/api.class.php');

if (!$_GET['do']) {

echo '

	<h1>Вас приветствует мастер установки модуля VAuth</h1>
	<h2>Для установки модуля нажмите <a href="?do=install">сюда</a></h2>


';

} elseif ($_GET['do'] == 'install') {

echo '<h1>Вас приветствует мастер установки модуля VAuth</h1>';
echo '<h2>Добавляю дополнительные поля..</h2>';

$db->query("INSERT INTO " . USERPREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('vauth', 'VAuth DLE', 'Модуль авторизации и регистрации пользователей через социальные сети', 'vauth.png', '1') ");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_id			VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_registered		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_hash_auth		TEXT		NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_phone		VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_friends		TEXT		NOT NULL");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_id			VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_registered		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_hash_auth		TEXT		NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_friends		TEXT		NOT NULL");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_registered		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_user_friends		TEXT		NOT NULL");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_user_id			VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_user_id			VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_username			VARCHAR(50)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_user_id			VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_username			VARCHAR(50)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_link				VARCHAR(90)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_link				VARCHAR(90)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_registered		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_hash_auth		TEXT		NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_user_friends		TEXT		NOT NULL");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_registered		INT(1)		NOT NULL	DEFAULT  '0'");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_user_id			VARCHAR(40)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_connected		INT(1)		NOT NULL	DEFAULT  '0'");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_registered		INT(1)		NOT NULL	DEFAULT  '0'");


$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	sex					VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	bdate				VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	updtime				VARCHAR(30)	NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	userfriends			TEXT		NOT NULL");
$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	userpassword_hash	TEXT		NOT NULL");

echo '<h2>Если не вылезло ошибок базы данных, то поля успешно добавлены! Вы можете закрыть эту страницу и удалить её с сайта.</h2>';

header('Location: ?do=finish');

} else {

echo '<h1>Вас приветствует мастер установки модуля VAuth</h1>';
echo '<h2>Поля успешно добавлены в базу, можете удалить этот файл с сайта</h2>';

}
?>


</body>

</html>