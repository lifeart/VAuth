<?PHP

	// ** Определяем константы, необходимые для работы модуля
	
	if (!defined('DATALIFEENGINE')) 	define	(	'DATALIFEENGINE',	true	);
	if (!defined('ROOT_DIR')) 			define	(	'ROOT_DIR',			substr(dirname (__FILE__),0,strpos( dirname ( __FILE__ ),"engine" )-1) );
	if (!defined('ENGINE_DIR')) 		define	(	'ENGINE_DIR',		ROOT_DIR . '/engine'	);

	#настройки скрипта
	
		#расположение дополнительных файлов скрипта
		$func_path = ENGINE_DIR . '/modules/vauth/functions/'; //Папка функций
		$lang_path = ENGINE_DIR . '/modules/vauth/langfiles/'; //Папка языков
		
		
		// ** Проверяем, все ли яйца в корзине, и подключаем их:

		if ( file_exists( ENGINE_DIR . '/api/api.class.php' ) )					include_once( ENGINE_DIR . '/api/api.class.php' );					else die('Нет файла DLE API функций модуля в папке ' . ENGINE_DIR . '/api/' );
		if ( file_exists( $func_path . '/vauth_functions.php' ) )				require_once( $func_path . '/vauth_functions.php' );				else die('Нет файла основных функций модуля в папке ' . $func_path );
		if ( file_exists( dirname (__FILE__) . '/user_settings.php' ) )			require_once( dirname (__FILE__) . '/user_settings.php' );			else die('Нет файла пользовательских настроек модуля - ' . dirname (__FILE__) . '/user_settings.php' );	
		if ( file_exists( $lang_path . $vauth_config['language'] . '.php') )	include_once( $lang_path . $vauth_config['language'] . '.php' );	elseif (file_exists( $lang_path.'russian.php')) include_once( $lang_path.'russian.php'); else die('Нет доступных языковых файлов в папке ' . $lang_path);
		if ( file_exists( ENGINE_DIR . '/modules/vauth/styles/styles.php') )	include_once( ENGINE_DIR . '/modules/vauth/styles/styles.php' );	else die('Нет файла стилей модуля в папке '.ENGINE_DIR . '/modules/vauth/styles/' );
		
		// ** Узнаём адрес сайта и сайт для авторизации
		
		$auth_site = @$_GET['auth_site'];
		// $site_url = trim(mb_strtolower($vauth_config['site_url']));
		$site_url = trim(mb_strtolower($dle_api->dle_config['http_home_url']));
		if (substr($site_url, -1) == '/') $site_url = substr($site_url, 0, -1);
		if (strpos($site_url,'http:') === false) $site_url = 'http://'.$site_url;
		if (empty($site_url) and empty($_GET['mod'])) die('Для корректной работы модуля VAuth необходимо указать адрес сайта на котором он работает <br/> это можно сделать в админ-панели.');
		
		if ( file_exists( $func_path . '/ibcurlemu.inc.php' ) ) require_once($func_path . '/libcurlemu.inc.php');
		
		// ** Если к нам пришёл запрос авторизации
		if ($auth_site) {
		
			$vauth_api = $vauth_api->load_oauth_modules($auth_site);
			$oauth = $vauth_api->oauth_data();
		
		} else $vauth_api = new VAuthFunctions ();
		
		// ** Другие настройки модуля
			
			// ** Хэши для генерации и шифрования паролей пользователей в бд движка (Менять с осторожностью, а то пользователи не будут авторизироваться)	
			$userhash_pass = 'n3WioTye94u39djee'; // ** При смене хэша старые пользователи не смогут авторизироваться
			$userhash_salt = 'nUE0pQbiY3MuqKEbAl5xwodihwe8do33qdw'; // ** При смене хэша старые пользователи не смогут авторизироваться
			// ** Хэши для генерации и шифрования паролей пользователей в бд движка (Менять с осторожностью, а то пользователи не будут авторизироваться)
			
			// ** Разрешаем регистрацию пользователя через модуль (1 - да, 0 - нет)
			$vauth_config['allow_register'] = 1;
			
			// ** Имя админки движка
			$admin_php_name = $dle_api->dle_config['admin_path'];
			if (empty($admin_php_name)) $admin_php_name =  'admin.php';
			
			// ** Адрес URL сопряжения акканутов (если меняем тут, то и в engine.php тоже нужно поменять)
			$ac_url	=	'/index.php?do=account_connect';

	
		#Ещё настройки скрипта
		
		// ** Переназначаем переменные
		$ac_connect = @$_SESSION['ac_connect'];
		
		// ** Важная штука!
		$auth_code = htmlentities(@$_GET['code']);
		
		// ** Важная штука2
		$cancel = @$_GET['cancel'];
		
		if ( empty( $_SESSION['referrer'] ) ) $auth_url = $site_url; else $auth_url = $_SESSION['referrer'];
		if ( !empty( $cancel ) ) header('Location: ' . $site_url);

		
		// ** Говорим модулю, что иногда нужно выводить форму запроса данных
		
		// ** Проверяем на работоспособность необходимые для работы модуля функции PHP
		if ($vauth_api->function_enabled('file_get_contents')) {
		
			$get_contents = 0;
			
		} else $get_contents = 0;
	
		
		if ($vauth_api->function_enabled('getimagesize')) {
		
			if ($vauth_api->function_enabled('exif_imagetype')) {

				if ($vauth_api->function_enabled('imagecreatefromjpeg')) {
					
					$get_image = 1;
				
				} else $get_image = 0;
			
			} else $get_image = 0;
		
		} else $get_image = 0;
		// ** На этом конфигурация модуля завершена
?>