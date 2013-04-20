<?PHP
	#Translation by Som
	#Помилки
	$vauth_error_1='Контакт не сприйняв авторизацію додатка';
	$vauth_error_2='';
	$vauth_error_3='';
	$vauth_error_4='';
	$vauth_error_5='';
	$vauth_error_6='';
	$vauth_error_7='';
	
	$vauth_text='Інформація оновиться через ';
	$vauth_text_1='Покищо нікого немає...';
	$vauth_text_2='1.2.1908 (Незазначена)';
	$vauth_text_3='Білі кролики бігали по пухнастому снігу колами';
	$vauth_text_4='Чоловіча';
	$vauth_text_5='Жіноча';
	$vauth_text_6='Невідома';
	$vauth_text_7=' хвилин.';
	$vauth_text_8='Ви обрали управління аккаунтами соціальних мереж на сайті';
	$vauth_text_9='Привіт, ';
	$vauth_text_10='Під’єднати vkontakte';
	$vauth_text_11='Від’єднати vkontakte';
	$vauth_text_12='Під’єднати facebook';
	$vauth_text_13='Від’єднати facebook';
	$vauth_text_14='Цей аккаунт вже під’єднаний до користувача ';
	$vauth_text_15='Такий аккаунт вже окремо зареєстрований з ім’ям користувача ';
	$vauth_text_16='Щось трапилось.. поверніться, будь ласка, назад…';
	$vauth_text_17='Спряження аккаунтів, зміна паролю';
	$vauth_text_18='! <br /> Ваш новий DLE пароль: ';
	$vauth_text_19='DLE VAuth module';
	$vauth_text_20='Anonym, Please Login first!';
	$vauth_text_21='Невідомо';
	
	$vauth_text_22='Введіть бажаний нік, або залишіть поле пустим, для встановлення ніка із ';
	$vauth_text_23='Продовжити';
	$vauth_text_24='Реєстрація на сайті, Ваш новий пароль';
	$vauth_text_25=', <br /> для успішної авторизації через соціальні мережі рекомендовано його не змінювати.';
	$vauth_text_26='Ввійти';
	$vauth_text_27='назад';
	
	
	$style_1='
				<style>
				body {background-color:#222;}
				#login {background-color:#222;}
				#forma {background-color:#42668c;border-radius:20px;padding:10px;width:40%;margin-left:30%;height:150px;margin-top:10%;min-width:600px;box-shadow: 0px 0px 7px #141414;}
				#forma:active {}
				#name {width:300px;height:40px;border:0px;font-size:20pt;margin-top:30px;float:left;clear:left;box-shadow: 0px 0px 7px #141414;margin-left:30px;border-top-left-radius:10px;border-bottom-left-radius:10px;}
				#name:hover {box-shadow: 0px 0px 7px #aaa;}
				.fbutton {border-top-right-radius:10px;border-bottom-right-radius:10px;width:200px;height:42px;border:0px;font-size:20pt;background-color:#fff;float:left;margin-top:30px;margin-left:10px;box-shadow: 0px 0px 7px #141414;}
				.fbutton:hover {cursor:pointer;box-shadow: 0px 0px 7px #888;background-color:#fed900;}
				#text_str {float:left;clear:left;width:500px;height:30px;font-size:18pt;color:#fff;margin-left:60px;text-shadow: #555 1px 1px 1px;margin-top:10px;}
				</style>';
	
	$style_2='
				<style>
				* {margin:0px;padding:0px;}
				img {margin:15px;borser-radius:5px;max-width:200px;box-shadow: 0px 0px 5px #888;display:block;}
				img:hover {opacity:0.9;}
				#login_name, #login_password {width:0px;height:0px;opacity:1;display:none}
				button {width:200px;margin:15px;borser-radius:5px;height:20px;background:#fff;border:0px;box-shadow: 0px 0px 5px #888;margin-top:0px;}
				button:hover {background:#f7f7f7;border:0px;box-shadow: 0px 0px 7px #888;cursor:pointer;}
				</style>';
				
	$style_3='
			<style>
			body {background-color:#666;}
			a {margin-bottom:0px;text-decoration:none;box-shadow:1px 1px 3px #66717e;float:left;clear:left;margin:5px;color:#ececec;background-color:gray;padding:3px;width:90%;border-radius:3px;padding-left:10px;padding-right:10px;}
			a:hover {    -webkit-transition: all 0.5s ease-in;
			padding-right:0px;  -moz-transition: all 0.5s ease-in; -o-transition: all 0.5s ease-in; -ms-transition: all 0.5s ease-in;opacity:0.85;background-color:#79a7da;}
			a:active {box-shadow:0px 0px 2px #66717e;}
			#login {margin:0 auto; width:200px;height:182px;background-color:#c2d3e6;border-radius:3px;overflow-right:hidden;box-shadow: inset 0px 0px 2px #b4bdc6;}
			#site {color:#eeeeee;font-size:16pt;margin-top:40px;}
			#footer {margin-top:20%;width:100%;height:100px;color:#acafb2;}
			h1 {opacity:0.75;}
			#hi {width:400px;}
			</style>
			
			<body>
				<center>	<h1 id="hi"><div id="site">Hello, '.substr($site_url,7,strlen($site_url)).' user!</div></h1>	</center><br/>
				
				<div id="login">
					<a href="/">Main page</a>
					<a href="auth.php?auth_site=vkontakte">[ vkontakte ]</a>
					<a href="auth.php?auth_site=facebook">[ facebook ]</a>
					<a href="auth.php?auth_site=twitter">[ twitter ]</a>
					<a href="clearsessions.php">Clear sessions</a>
				</div>
				
				<div id="footer">
					<center>DLE VAuth 1.7 by lifeart, social networks autorization module main page.</center>
				</div>
				
			</body>
			
		';
?>
