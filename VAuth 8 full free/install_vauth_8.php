<?PHP
include_once('engine/api/api.class.php');

echo '<b>Добавляю дополнительные поля..</b></br>';

$db->query("INSERT INTO " . USERPREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('vauth', 'VAuth DLE', 'Модуль авторизации и регистрации пользователей через социальные сети', 'vauth.png', '1') ");

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_id			VARCHAR(30)	NOT NULL");}
catch {echo 'Поле vk_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле vk_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле vk_registered уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_hash_auth		TEXT		NOT NULL");}
catch {echo 'Поле vk_hash_auth уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_phone		VARCHAR(30)	NOT NULL");}
catch {echo 'Поле vk_user_phone уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_screen_name		VARCHAR(30)	NOT NULL");}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_friends		TEXT		NOT NULL");}
catch {echo 'Поле vk_user_friends уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_activity	TEXT		NOT	NULL");}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_id			VARCHAR(30)	NOT NULL");}
catch {echo 'Поле fb_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле fb_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле fb_registered уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_hash_auth		TEXT		NOT NULL");}
catch {echo 'Поле fb_hash_auth уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_quotes		TEXT		NOT NULL");}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_screen_name		VARCHAR(30) NOT NULL");}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_friends		TEXT		NOT NULL");}
catch {echo 'Поле fb_user_friends уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fb_user_activity	TEXT		NOT NULL");}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле tw_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле tw_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле tw_registered уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_screen_name		VARCHAR(30) NOT NULL");}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_user_friends		TEXT		NOT NULL");}
catch {echo 'Поле tw_user_friends уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	tw_user_activity	TEXT		NOT NULL");}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_user_id			VARCHAR(30)	NOT NULL");}
catch {echo 'Поле od_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле od_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	od_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле od_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_user_id			VARCHAR(30)	NOT NULL");}
catch {echo 'Поле in_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_username			VARCHAR(50)	NOT NULL");}
catch {echo 'Поле in_username уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле in_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	in_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле in_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_user_id			VARCHAR(30)	NOT NULL");}
catch {echo 'Поле gh_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_username			VARCHAR(50)	NOT NULL");}
catch {echo 'Поле gh_username уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле gh_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	gh_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле gh_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле ms_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_link				VARCHAR(90)	NOT NULL");}
catch {echo 'Поле ms_link уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле ms_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ms_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле ms_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле ma_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_link				VARCHAR(90)	NOT NULL");}
catch {echo 'Поле ma_link уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле ma_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	ma_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле ma_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле fs_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле fs_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле fs_registered уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_hash_auth		TEXT		NOT NULL");}
catch {echo 'Поле fs_hash_auth уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	fs_user_friends		TEXT		NOT NULL");}
catch {echo 'Поле fs_user_friends уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле go_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле go_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	go_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле go_registered уже присутствует';}

try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_user_id			VARCHAR(40)	NOT NULL");}
catch {echo 'Поле st_user_id уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_connected		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле st_connected уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	st_registered		INT(1)		NOT NULL	DEFAULT  '0'");}
catch {echo 'Поле st_registered уже присутствует';}


try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	sex					VARCHAR(30)	NOT NULL");}
catch {echo 'Поле sex уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	bdate				VARCHAR(30)	NOT NULL");}
catch {echo 'Поле bdate уже присутствует';}
#$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	status				TEXT		NOT NULL");}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	updtime				VARCHAR(30)	NOT NULL");}
catch {echo 'Поле updtime уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	userfriends			TEXT		NOT NULL");}
catch {echo 'Поле userfriends уже присутствует';}
try {$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	userpassword_hash	TEXT		NOT NULL");}
catch {echo 'Поле userpassword_hash уже присутствует';}

echo 'Если не вылезло ошибок базы данных, то поля успешно добавлены! Вы можете закрыть эту страницу и удалить её с сайта.';
?>