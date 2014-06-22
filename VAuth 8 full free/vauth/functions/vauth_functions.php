<?php

if( ! class_exists( 'VAuthFunctions' ) )	{

	// ** Клас, включающий в себя реализацию всех функций модуля
	class VAuthFunctions {
	
		function load_oauth_modules($auth_site) {
		
			if (!$auth_site) die($this->conv_it('Не указана социальная сеть для авторизации'));
		
			global $func_path;
			
			$auth_site = strtolower($auth_site);
		
			if (!empty($auth_site) and preg_match("/^[a-z]+$/", $auth_site)) {
			
				$all_okidoki = 0;
				
				if ($this->function_enabled('scandir')) {
			
					$file_list = scandir($func_path);
				
					foreach ($file_list as $sitelist=>$site_funct) {
						
						if (strlen($site_funct)>7) {
						
							$pos = strpos($site_funct, $auth_site);
							
							if ($pos !== false) {

								if ($site_funct == $auth_site.'_functions.php' and $auth_site != 'vauth') {
								
									$all_okidoki = 1;
								
									$oauth_api = $this->load_oauth($auth_site);

									return $oauth_api;
								
								}
							
							}
						
						}
					
					}
				
				} else {
				
					$all_okidoki = 1;
					$oauth_api = $this->load_oauth($auth_site);
					return $oauth_api;
				
				}
				
				die($this->conv_it('Нет такого модуля для авторизации'));
			
			} elseif (!empty($auth_site)){die($this->conv_it('Неверные параметры'));}
		
		
		}
	
		// ** Проверяем наличие модулей социальных сетей
		function load_oauth($site) {
			
			global $func_path;
			
			if (file_exists($func_path . '/'.$site.'_functions.php')) {
			
				require_once($func_path . '/' .$site . '_functions.php');
				
				$_SESSION['site'] = $site;
				
				return $vauth_api;
				
			}
			
			else die($this->conv_it($vauth_text['no_function_oauth'].$site));
			
		}
		// ** Выбираем способ получения данных для авторизации
		function vauth_get_contents($url) {
			
			global $get_contents;
			
			if ($get_contents == 1) $data = @file_get_contents($url);
			else $data = $this->file_get_contents_curl($url);
		
			return $data;
		}
		// ** Выбираем способ загрузки аватара пользователя
		function upload_avatar($url,$id,$arr=false) {
			
			global $get_image;
			global $vauth_config;
			
			if ($arr != false) {
			
				if (file_get_contents($url) == false) {
					
					$url = $arr[1];
					
					if (file_get_contents($url) == false) {
					
						$url = $arr[0];
				
						if (file_get_contents($url) == false) {
					
							$url = $arr[2];
						
						}
				
					}
				
				}
			
			}
			
			if (empty($url)) $url = $vauth_config['site_url'].'/engine/modules/vauth/styles/photo.jpg';
			
			if ($get_image == 1) $data = $this->normal_upload_avatar($url,$id);
			else $data = $this->upload_avatar_curl($url,$id);
		
			return $data;
		}
		// ** Функция проверки на доступность используемых PHP функций
		function function_enabled($func) {
		
			$func = strtolower(trim($func));
			if ($func == '') return false;
			$disabled = explode(",",@ini_get("disable_functions"));
			if (empty($disabled)) $disabled = array();
			else $disabled = array_map('trim',array_map('strtolower',$disabled));

			return (function_exists($func) && is_callable($func) &&
					!in_array($func,$disabled)
			);
		}
		// ** Функция получения хэдеров через CURL
		function get_curl_headers($url) {
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$headers = curl_exec ($ch);
			curl_close ($ch);
			return $headers;
		
		}	
		// ** Функция загрузки данных через CURL
		function file_get_contents_curl($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
			curl_setopt($ch, CURLOPT_URL, $url);
			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}	
		// ** Функция начала авторизации пользователей
		
		function go_auth($auth_site) {
	
			global $site_url;
			global $auth_code;
			global $style_3;
			global $oauth;
			global $vauth_text;
			
			if ( !empty( $auth_site ) ) {
				
				if (empty($auth_code)) {
					
					$_SESSION['id1'] = session_id();
				
				} else {
			
				
					$_SESSION['id2'] = session_id();
				
					if ($_SESSION['id1'] != $_SESSION['id2'] or (empty($_SESSION['id1']) and empty($_SESSION['id2']))) { 
				
						die($vauth_text['error_oauth_ssid']);
					
					}
					
					$_SESSION['id1'] = '';
					$_SESSION['id2'] = '';
					
				}
			
				$oauth = $this->vauth_auth($oauth);
				
			} else { echo $style_3; die; }
		
		}	
		
		
		function show_regform($auth_site) {
		
			global $style_loginform;
			global $vauth_text;
			global $user_regform;
			global $vauth_config;
			
			$regform['email'] = $vauth_config['email_request'];
			$regform['password'] = $vauth_config['password_request'];
			$regform['login'] = $vauth_config['login_request'];		

			if (empty($regform['email'])) $regform['email'] = 0;
			if (empty($regform['password'])) $regform['password'] = 0;
			if (empty($regform['login'])) $regform['login'] = 0;			
			
			
			$_SESSION['regform'] = 1;
			$_SESSION['regform_time'] = time();
			$_SESSION['regform_site'] = $auth_site;
			
			
			$form = $user_regform['head'].$_SESSION['site'].$user_regform['head_2'];
		
			if ($regform['login'] != 0 ) $form = $form . $user_regform['login'];
			if ($regform['email'] != 0 ) $form = $form . $user_regform['email'];
			if ($regform['password'] != 0 ) $form = $form . $user_regform['password'];

			$form = $form . $user_regform['end'];
			echo $form;
			die;
		}		
		// ** Функция начала регистрации пользователей
		function go_register($auth_site,$new_user) {
		
			global $vauth_config;
			global $vauth_text;
			global $oauth;

			if (empty($vauth_config['email_request'])) $vauth_config['email_request'] = 0;
			if (empty($vauth_config['password_request'])) $vauth_config['password_request'] = 0;
			if (empty($vauth_config['login_request'])) $vauth_config['login_request'] = 0;	
		
			// ** Узнаём, нужно ли нам выводить форму запроса данных пользователя при регистрации
			$regform_summ = ($vauth_config['login_request'])+($vauth_config['email_request'])+($vauth_config['password_request']);
			
			if ($vauth_config['allow_register'] != 1) die($this->conv_it($vauth_text['vauth_reg_off']));
		
		
			// Показ формы регистрации
			if ( $regform_summ > 0 ) {
				
				if (!empty($_POST['regform']) and !empty($_SESSION['regform']) and $_SESSION['regform'] == 1 and $_SESSION['auth_from'] == $_SESSION['regform_site'] ) {
				 /// Регистрация с формы
					
					
					$oauth = $this->oauth_data();
					
					$oauth['login2'] = @$_POST['login'];
					$oauth['email2'] = @$_POST['email'];
					$oauth['password'] = @$_POST['password'];
					
					$_SESSION['regform'] = '';
					$_SESSION['regform_time'] = '';
					$_SESSION['regform_site'] = '';
					$new_user = 2;

					if ( empty($_SESSION[$oauth['prefix2'].'_access_token']) and $oauth['prefix2'] != 'twitter' ) {
					
						header('Location: ./clearsessions.php');
						die();
					
					}
					
				} elseif ( !empty($new_user) and empty($_SESSION['ac_connect']) ) {
				
					
					// показ формы регистрации
					$this->show_regform($auth_site);
					die();
				
				} else  {
				
					header('Location: ./clearsessions.php');
					die();
				}
				
				//регистрация с формой	
			
			}
			
			if (!empty($new_user)) {
			
				$dle_userinfo = $this->vauth_register($oauth);
				
				$this->unset_session();
				
				if ( !empty($dle_userinfo['name']) ) {
				
					$this->dle_register_pm_send($dle_userinfo['user_id'],$dle_userinfo['login1'],$dle_userinfo['password1']);
					$this->user_login($dle_userinfo);
				
				} else die($this->conv_it('Регистрация пользователя '.$dle_userinfo['name'].' не удалась'));
			
			} else die($this->conv_it('Ошибка регистрации: недостаточно параметров или не пройдена авторизация'));
			
		}
		// ** Функция авторизации пользователей на DLE сайте
		function go_login($auth_site,$ac_connect) {
		
			global $oauth;
			
			#print_r($_SESSION);die;
			
			if ((!empty($_SESSION[$auth_site.'_access_token']) or !empty($_SESSION['access_token'])) and empty($_POST['regform']) and empty($_SESSION['regform'])) { // ** Действия с авторизированным через вконтакте пользователем
				
				$new_user =  $this->vauth_login($oauth,$ac_connect);
				
			}					
		
			if (!empty($new_user)) return $new_user;
		
		}
		// ** Функция декодирования хэша ответа социальной сети
		function get_data($x) {
		
			$x = base64_decode(str_rot13($x).'==');
			
			return $x;
		
		}	
		// ** Функция вывода дополнительной формы при регистрации
		// ** Функция, удялющая ненужные данные из сессии
		function unset_session() {
			unset($_SESSION['new_user']);
		}	

		// ** Функция формирования пароля пользователя при регистрации
		function dle_userpass($row) {
			
			global $pass;
			
			$password		=	substr(md5($this->create_userpass($row['uid'])),3,9); //Генерируем парольчик
			$password		=	substr($password,0,3).strtoupper(substr($password,4,9));
			
			return $password;
		}
		// ** Служебная функция для вывода отладочной информации
		function get_dump($a) {
			
			echo '<pre>';
			print_r($a);
			echo '</pre>';
		
		}	
		// ** Функция отправки post запроса через CURL
		function post_curl ($url,$postdata) {
			
			$uagent = "OAuth/7.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );

			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;
			
			return $header['content'];
		}	
		// ** Функция преобрзования кодировок
		function conv_it($string) {
		
			global $dle_api;
			
			if ($dle_api->dle_config['charset'] == 'windows-1251') {
		
				$string = iconv("utf-8", "cp1251", $string);
			
			}
			
			return $string;
		}
		// ** Функция авторизации на сайте
		function user_login ($userinfo) {
			
			global $auth_url;
			global $db;
			global $dle_api;
			global $config;
			global $_TIME;
			global $userhash_salt;
			global $userhash_pass;
			
			if (empty($_TIME)) $_TIME = time();
			
			##	*	Логин пользователя на сайте	
			$_IP = $db->safesql( $_SERVER['REMOTE_ADDR'] );
			$dle_login_hash = "";
			
			function clean_url($url) {
			
				if( $url == '' ) return;	
				$url = str_replace( "http://", "", $url );
				$url = str_replace( "https://", "", $url );
				if( strtolower( substr( $url, 0, 4 ) ) == 'www.' ) $url = substr( $url, 4 );
				$url = explode( '/', $url );
				$url = reset( $url );
				$url = explode( ':', $url );
				$url = reset( $url );					
				return $url;
			
			}

			$domain_cookie = explode (".", clean_url( $_SERVER['HTTP_HOST'] ));
			$domain_cookie_count = count($domain_cookie);
			$domain_allow_count = -2;

			if ( $domain_cookie_count > 2 ) {

				if ( in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') )) $domain_allow_count = -3;
				if ( $domain_cookie[$domain_cookie_count-1] == 'ua' ) $domain_allow_count = -3;
				$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
			
			}

			$domain_cookie = "." . implode (".", $domain_cookie);

			define( 'DOMAIN', $domain_cookie );

			function set_cookie($name, $value, $expires) {
				
				if ( $expires ) { $expires = time() + ($expires * 86400); } else { $expires = FALSE; }
				if ( PHP_VERSION < 5.2 ) { setcookie( $name, $value, $expires, "/", DOMAIN . "; HttpOnly" ); } else { setcookie( $name, $value, $expires, "/", DOMAIN, NULL, TRUE ); }
			
			}								

			$password	=	$userinfo['userpassword_hash'];

			if (empty($password)) {

				if (!empty($_SESSION['dle_password'])) {
	
					$password = $_SESSION['dle_password'];
					$password = $this->encode($password);
					$password = base64_encode($password);
					$db->query( "UPDATE " . USERPREFIX . "_users set userpassword_hash='$userpassword_hash' WHERE user_id = '{$userinfo[user_id]}'" );
	
				}

			}
			
			$password = base64_decode( $password );
			$password = $this->encode( $password );

			if ( md5( $password ) != $userinfo['password'] ) die($this->conv_it('Пароли не совпадают)'));
		
			set_cookie( "dle_user_id", $userinfo['user_id'], 365 ); 
			set_cookie( "dle_password", $password, 365 );

			$_SESSION['dle_user_id']		= $userinfo['user_id']; 
			$_SESSION['dle_password']		= $password; 
			$_SESSION['member_lasttime']	= $userinfo['lastdate']; 
			$_SESSION['dle_log'] = 0; 
			
			if (empty($config['key'])) $config['key'] = '';
	
				$dle_login_hash = md5( strtolower( $_SERVER['HTTP_HOST'] . $userinfo['name'] . sha1($password) . $config['key'] . date( "Ymd" ) ) ); 
				
				if ( $config['log_hash'] ) {
				
					if(function_exists('openssl_random_pseudo_bytes')) {
					
						$stronghash = md5(openssl_random_pseudo_bytes(15));
					
					} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
				
					$salt = sha1( str_shuffle("abchefghjkmnpqrstuvwxyz0123456789") . $stronghash );
				
					$_TIME = time();
					$_IP = $_SERVER['REMOTE_ADDR'];
				
					$hash = ''; 
					srand( ( double ) microtime() * 1000000 ); 
					for ($i = 0; $i < 9; $i ++) { $hash .= $salt{rand( 0, 33 )};} 
					$hash = md5( $hash ); 
					$db->query( "UPDATE " . USERPREFIX . "_users set hash='" . $hash . "', lastdate='{$_TIME}', logged_ip='" . $_IP . "' WHERE user_id='$userinfo[user_id]'" ); 
					set_cookie( "dle_hash", $hash, 365 ); 
					$_COOKIE['dle_hash']	= $hash; 
					$dle_userinfo['hash']	= $hash; 
				
				} else $db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users set lastdate='{$_TIME}', logged_ip='" . $_IP . "' WHERE user_id='$userinfo[user_id]'" ); 
			
				$is_logged = TRUE; 	
				header('Location: '.$auth_url);
				die;
			}
			
		// ** Функция регистрации пользователя в базе DLE
		function user_dle_register($oauth) {
			
			global	$dle_api;
			global	$vauth_text;
			global	$vauth_config;

			$reguser = 0;
			
			if (!empty($oauth['email2']) and strlen($oauth['email2'])>3 and !empty($oauth['login2']) and strlen($oauth['login2'])>3) {
			
				$reguser  = $dle_api->external_register($oauth['login2'],$oauth['password'],$oauth['email2'],$oauth['group']);
				
				if ($reguser == 1) return $oauth['login2'];
			
			} elseif (!empty($oauth['login2']) and strlen($oauth['login2'])>3) {
			
				$reguser  = $dle_api->external_register($oauth['login2'],$oauth['password'],$oauth['email'],$oauth['group']);
				if ($reguser == 1) return $oauth['login2'];
			
			} elseif (!empty($oauth['email2']) and strlen($oauth['email2'])>3) {
			
				$oauth['email'] = $oauth['email2'];
			
			}
			
			if ($reguser != 1) {
			
				if	($reguser == -4) {
				
					die($this->conv_it($vauth_text['bad_group']));
					
				}
				
				function if_is($data,$datalink) {
					
					if (!empty($data[$datalink])) return $data[$datalink];
					else return '';
				
				}

				if ($this->function_enabled('mb_substr')) {
				
					$f_fname = mb_substr( strtolower(if_is($oauth,'firstname')), 0, 1 );
					$f_lname = mb_substr( strtolower(if_is($oauth,'lastname')), 0, 1 );
				
				} else {
				
					$f_fname = substr( strtolower(if_is($oauth,'firstname')), 0, 1 );
					$f_lname = substr( strtolower(if_is($oauth,'lastname')), 0, 1 );
				
				}
				
				$name = array();
				
				if ( isset($oauth['nick']) and $oauth['nick'] == 'id'.$oauth['uid'] ) $oauth['nick'] = '';
				if ( isset($oauth['nick']) and $oauth['nick'] ==  $oauth['uid'] ) $oauth['nick'] = '';
		
				if ( strpos($oauth['lastname'], " ") > 0 ) {

					$oauth['lastname'] = substr($oauth['lastname'], 0, strpos($oauth['lastname'], " "));
					
					}
				
				if ( strpos($oauth['firstname'], " ") > 0 ) {

					$oauth['firstname'] = substr($oauth['firstname'], (strpos($oauth['lastname'], " ")+1),strlen($oauth['lastname']));
					
					}
				
				$name[1] = if_is($oauth,'nick');
				$name[2] = $f_fname.'.'.strtolower($oauth['lastname']);
				$name[3] = $f_lname.'.'.strtolower($oauth['firstname']);
				$name[4] = if_is($oauth,'fullname');
				$name[5] = if_is($oauth,'lastname').' '.if_is($oauth,'firstname');
				$name[6] = $oauth['nick'].mt_rand(75,500);
				$name[7] = $oauth['firstname'].mt_rand(75,500);
				$name[8] = $oauth['lastname'].mt_rand(75,500);
				$name[9] = $oauth['fullname'].' '.mt_rand(75,500);
				$name[10] = 'User_'.mt_rand(1,500);
				$name[13] = 'Anonymous_'.mt_rand(75,500);
				$name[12] = 'Neo_'.mt_rand(75,500);
				$name[11] = 'Pipito_'.mt_rand(75,500);
				$name[14] = $oauth['uid'];
				ksort($name);
				
				if (empty($oauth['email'])) $oauth['email'] = $oauth['uid'].$vauth_text['def_email'];

				for ($n=1;$n<=14;$n++) {
				
					if (strlen($name[$n])>3) {
						
						$reguser = $dle_api->external_register($name[$n],$oauth['password'],$oauth['email'],$oauth['group']);
					
						if ( $reguser == 1 ) {
						
							return $name[$n];
						
							break;
						}
						
						if ( $reguser == -2 ) break;
						
						if ( $reguser == -3 ) $oauth['email'] = $oauth['uid'].$vauth_text['def_email'];
						
						if ( $reguser == -4)  $oauth['group'] = 4;
						
					}

				}
				
				if	($reguser == -2) {
				
					// Авторизация пользователя по e-mail ))
					if ($vauth_config['email_auth'] == 1) {
					
						$userinfo = $dle_api->take_user_by_email($oauth['email']);
						
						if (!empty($userinfo['userpassword_hash'])) $this->user_login($userinfo);
						else die($this->conv_it($oauth['email'].$vauth_text['email_error']));

					
					} else die($this->conv_it($oauth['email'].$vauth_text['email_error']));
					
				}
			
				die($this->conv_it($vauth_text['fatal_reg_error']));
			

			} else return $oauth['login2'];
	
		}
		// ** Функция генерации пароля пользователя
		function create_userpass($uid) { //Генерация пароля
	
				if (empty($uid)) $uid = mt_rand(123,939073);
				$uid = abs(intval($uid.$uid))+1230;
				$password = mt_rand(123,$uid);
				return $password;
			}

		// ** Функция декодирования base64 зашифрованных ответов социальной сети
		function get_social_info() {
		
			global $userhash_salt;

			$request = $this->get_data($userhash_salt);
		
			return $request;
		
			}
		// ** Функция загрузки аватарки через CURL
		function upload_avatar_curl($avatar,$id) {
	
			if (!$avatar) return '';
	
			global $vauth_config;
			
			if (empty($vauth_config['avatar_size'])) $vauth_config['avatar_size'] = 100;
		
			$avatar_size = $vauth_config['avatar_size'];
		
			$image_name = 'foto_'.$id.'.jpg';
			$fullpath = ROOT_DIR . '/uploads/fotos/'.$image_name;
			
			@unlink($fullpath);
		
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $avatar);
			$rawdata = curl_exec($ch);
			curl_close($ch);
		
			$im = imagecreatefromstring($rawdata);
		
			$height = imagesy($im); // Высота
			$width = imagesx($im); // Ширина
		
			if ($height < 1 or $width < 1) return '';
		
			$width2 = $width;
			
			$x = 0;
			$y = 0;
		
			if ($height > $width) {
			
				if ($vauth_config['square_vert_shift'] == 1) {
					$ratio2	=	$height/$width;
					$shift	=	$width2*$ratio2;
					$shift	=	$shift-$width2;
					$shift	=	$shift/2;
					$y		=	$shift;
				}
			
				$height = $width;
			}
			
			if ($width>$height) {
			
				if ($vauth_config['square_hor_shift']	==	1) {
					$ratio2	=	$width/$height;
					$shift	=	$width2*$ratio2;
					$shift	=	$shift-$width2;
					$shift	=	$shift/2;
					$x		=	$shift;
				}
				
				$width = $height;
		
			}

			$im2 = imagecreatetruecolor($avatar_size,$avatar_size);
			imagecopyresized($im2,$im,0,0,$x,$y,$avatar_size,$avatar_size,$width,$height);
			imagecopyresampled($im2,$im,0,0,$x,$y,$avatar_size,$avatar_size,$width,$height);
			// delete the original image to save resources
			imagedestroy($im);
			imagejpeg($im2, $fullpath);
			
			@chmod( $fullpath, 0666 );
			
			// remember to free resources
			imagedestroy($im2);

			return $image_name;
		}
		// ** Функция загрузки аватара
		function normal_upload_avatar($avatar,$id) { //Загрузка аватара
				
				global $vauth_config;
				
				
				if (!$avatar) return '';
				
				$type = exif_imagetype($avatar);
				
				switch($type) {
				
					case 1 :
						$image_type = IMAGETYPE_GIF;
					break;
					
					case 2 :
						$image_type = IMAGETYPE_JPEG;
					break;
					
					case 3 :
						$image_type = IMAGETYPE_PNG;
					break;
					
					default: $image_type = IMAGETYPE_JPEG;break;
				}
				
				#$scale = 50;
				$permissions = 0666;
				$image_alias = '.jpg';
				$image_name = 'foto_'.$id;
				$image_dir = ROOT_DIR . "/uploads/fotos/";
				#$scale_persent=50;
				@unlink( ROOT_DIR . "/uploads/fotos/" . $image_name. $image_alias);
				include_once('SimpleImage.php');
				$image = new SimpleImage();
				$image->load($avatar);
				
				if (empty($vauth_config['avatar_size'])) $vauth_config['avatar_size'] = 100;
				
				#$image->resizeToWidth($width); //есть возможность масштабировать по ширине
				#$image->resize($height,$width); //есть возможность масштабировать статически
				#$image->scale($scale_persent); //есть возможность масштабировать процентно
				#$image->resizeToHeight($height); //есть возможность масштабировать по высоте
				$image->crop_to_square($vauth_config['avatar_size'],$vauth_config['square_hor_shift'],$vauth_config['square_vert_shift']);
				$image->save($image_dir.$image_name.$image_alias,IMAGETYPE_JPEG,$permissions);
				$foto_name = "foto_" . $id . $image_alias;
				return $foto_name;
			
			}
		// ** Функция отправки персонального сообщения только-что зарегистрированному пользователю
		function dle_register_pm_send($id,$login,$password) {
			
			global $vauth_text;
			global $dle_api;
			global $pm_send;
			global $vauth_config;
			
			$pm_send = $vauth_config['pm_send'];
			
			
			if (empty($pm_send)) $pm_send = 0;
			
			if ($pm_send == 1) {
				
				$subject	=	$vauth_text[24];
				$text		=	$vauth_text[9].$login.$vauth_text[37].$login.$vauth_text[18].$password.$vauth_text[36];
				$from		=	$vauth_text[19];
				
				$dle_api->send_pm_to_user ($id,$subject,$text,$from); 				
				
			}
		}
		// ** Функция шифрования хэша пароля пользователя
		function encode($string)	{ // Стороння функцияя обратимого шифрования Владимира Кима 
			
			global $userhash_pass;
			global $userhash_salt;
			
			$strlen = strlen($string);
			$seq = $userhash_pass;
			$gamma = '';
			
			while (strlen($gamma)<$strlen)	{
			
				$seq = pack("H*",sha1($gamma.$seq.$userhash_salt)); 
				$gamma.=substr($seq,0,8);
				
			}
			
			$my_lovers = $string^$gamma;
			
			return $my_lovers;
			// Сайт автора: http://vladimirkim.livejournal.com/
		}
		// ** Функция отключения аккаунта социальной сети
		function oauth_disconnect($oauth) {
			
			global $db;
			global $site_url;
			global $ac_url;
			
			$disconnect_str = $oauth['disconnect_str'];
			
			if (!empty($_SESSION['dle_user_id']))
			$id = $_SESSION['dle_user_id'];
			else die($this->conv_it('No SSID USER'));
			
			
			$db->query( "UPDATE " . USERPREFIX . "_users set $disconnect_str WHERE user_id = '$id'" );

			unset($_SESSION['ac_connect']);
			header('Location: '.$site_url.$ac_url); die();
		
		}
		// ** Функция подключения аккаунта социальной сети
		function oauth_connect($oauth) {
			
			global $dle_api;
			global $db;
			global $site_url;
			global $ac_url;
			
			$prefix = $oauth['prefix'];

			$member_id = $dle_api->take_user_by_id($_SESSION['dle_user_id']);
			
			if ($oauth['needfriends'] == 'yes') {
			
				$oauth['friends'] = $this->get_oauth_friends($oauth);
			
				$friendlist = ' '.$prefix.'_user_friends=\''.$oauth['friends'].'\', ';

			} else $friendlist = '';
			
			if (empty($member_id['foto'])) {
			
				if (!empty($oauth['avatar'])) {
			
					$photo = $this->upload_avatar($oauth['avatar'],$member_id['user_id']);
			
				} else $photo = '';
			
			if (empty($member_id[$prefix.'_username'])) {
			
				if (!empty($oauth[$prefix.'_username'])) {
			
					$prefix_username = $oauth[$prefix.'_username'];
					$prefix_username = ' '.$prefix.'_username=\''.$prefix_username.'\', ';
					
				} else $prefix_username = '';
				
			} else $prefix_username = '';			
			
			} else $photo = $member_id['foto'];
			
			if (empty($member_id['bdate'])) {
			
				if (!empty($oauth['birthday'])) {
				
					$bdate = $oauth['birthday'];
					
				} elseif (!empty($oauth['bdate'])) {
				
					$bdate = $oauth['bdate'];
					
				} else $bdate = '';
				
			} else $bdate = $member_id['bdate'];
			
			if (empty($member_id['fullname'])) {
			
				if (!empty($oauth['fullname'])) {
					$fullname = addslashes($oauth['fullname']);
				} else $fullname = '';
				
			} else $fullname = $member_id['fullname'];
			
			if (empty($member_id['vk_user_phone'])) {
			
				if (!empty($oauth['mobile_phone'])) {
					$phone = $oauth['mobile_phone'];
				} else $phone = '';
				
			} else $phone = $member_id['vk_user_phone'];
			
			if (empty($member_id['land'])) {
			
				if (!empty($oauth['location'])) {
					
					$land = $oauth['location'];
					
				} elseif (!empty($oauth['city'])) {
				
					$land = $oauth['city'];
					
				} else $land = '';
			
			} else $land = $member_id['land'];
			
			if (empty($member_id['sex'])) {
			
				if (!empty($oauth['sex'])) {
			
					$sex = $oauth['sex'];
					
				} elseif (!empty($oauth['gender'])) {
					
					$sex = $oauth['gender'];
					
				}	else $sex = '';
				
			} else $sex = $member_id['sex'];
			
			if (empty($member_id['info'])) {
			
				if (!empty($oauth['bio'])) {
					
					$info = $oauth['bio'];
				
				} elseif (!empty($oauth['info'])) {
					
					$info = $oauth['info'];
				
				} elseif (!empty($oauth['activity'])) {
				
					$info = $oauth['activity'];

				} else	$info = '';
			
			} else $info = $member_id['info'];
	
			if (empty($member_id[$prefix.'_link'])) {
			
				if (!empty($oauth['link'])) {
			
					$link = $oauth['link'];
					$link = ' '.$prefix.'_link=\''.$link.'\', ';
					
				} else $link = '';
				
			} else $link = '';
			
			if (isset($oauth['needhash']) and $oauth['needhash'] == 'yes') {
			
				$auth_hash = ' '.$prefix.'_hash_auth=\''.$oauth['access_token'].'\', ';
			
			} else $auth_hash='';
			
			$db->query( "UPDATE " . USERPREFIX . "_users set ".$prefix_username.$friendlist.$auth_hash.$link." updtime='', fullname='$fullname', vk_user_phone='$phone', info='$info', sex='$sex', bdate='$bdate', land='$land', foto='$photo', ".$prefix."_connected='1', ".$prefix."_user_id='$oauth[uid]' WHERE user_id = '{$_SESSION[dle_user_id]}'" );
			unset($_SESSION['ac_connect']);
			header('Location: '.$site_url.$ac_url); die();
		
		}
		// ** Функция авторизации пользователей
		function vauth_login($oauth,$ac_connect) {
		
			global $db;
			global $vauth_text;
			
			$prefix = $oauth['prefix'];
			$prefix2 = $oauth['prefix2'];
			
			
			$oauth	= $this->get_oauth_info($oauth);

			
			if (empty($oauth['uid'])) die($this->conv_it($vauth_text['uid_error']));
		
			$uid = $oauth['uid'];
		
			$connect_sql_login	=	$db->query( "SELECT * FROM " . USERPREFIX . "_users where ".$prefix."_user_id = '$uid'" );
		
			$dle_userinfo = $db->get_array($connect_sql_login);

		
			if ( $this->ifIs($dle_userinfo['user_id'])) {

				if ($ac_connect == $prefix) { unset($_SESSION['ac_connect']); die($this->conv_it($vauth_text[28])); }
				
				elseif ($ac_connect == $prefix.'_off' and $dle_userinfo[$prefix.'_connected'] == 1 and  $dle_userinfo[$prefix.'_registered'] != 1) {
				
					$this->oauth_disconnect($oauth);
					die();
					
				}
				
				else {
				
					if ($this->ifIs($oauth['needhash']) and $oauth['needhash'] == 'yes') {
				
						$id = $dle_userinfo['user_id'];
				
						$db->query( "UPDATE " . USERPREFIX . "_users set ".$prefix."_hash_auth='$oauth[access_token]' WHERE user_id = '$id'" );
				
					}
					
					$this->user_login ($dle_userinfo);	
				}
				
			}	elseif 	($ac_connect == $prefix and $this->ifIs($_SESSION['dle_user_id'])) {
			
				if ($this->ifIs($oauth['needfriends']) and $oauth['needfriends'] == 'yes') {
				
					$oauth['friends'] = $this->get_oauth_friends($oauth);
				
				}
				
				$this->oauth_connect($oauth);
				die();
				
			} else $new_user	=	2;
			
			return $new_user;
			
		}
		// ** Функция регистрации пользователей
		function vauth_register($oauth) {
			
			global $dle_api;
			global $db;
			
			
			$group = $oauth['group'];
			
			$oauth['access_token'] = $_SESSION[$oauth['prefix2'].'_access_token'];
			$oauth['uid'] = $_SESSION[$oauth['prefix2'].'_user_id'];
		
			$oauth	= $this->get_oauth_info($oauth);
			if (empty($oauth['uid'])) die($this->conv_it('Ошибка авторизации в социальной сети'));
			
			if (empty($oauth['password'])) $oauth['password'] = $this->dle_userpass($oauth);
			
			$user = $this->user_dle_register($oauth);
			$dle_userinfo = $dle_api->take_user_by_name($user);
		
			$dle_userinfo['userpassword_hash'] = base64_encode($this->encode(@md5($oauth['password'])));
		
			$prefix = $oauth['prefix'];
		
			
			if ($this->ifIs($oauth['friends'])) {
			
				$friendlist = $prefix.'_user_friends=\''.$oauth['friends'].'\', ';
			
			} else $friendlist = '';
			
			if ($this->ifIs($oauth['fullname'])) {
			
				$fullname = addslashes($oauth['fullname']);
				$fullname = 'fullname=\''.$fullname.'\', ';

			} else $fullname = '';
			
			if ($this->ifIs($oauth['bio'])) {
				
				$info = 'info=\''.addslashes($oauth['bio']).'\', ';
				
			} elseif ($this->ifIs($oauth['info'])) {
				
				$info = 'info=\''.addslashes($oauth['info']).'\', ';
				
			} elseif ($this->ifIs($oauth['activity'])) {
			
				$info = 'info=\''.addslashes($oauth['activity']).'\', ';

			} else	$info = '';
		
			if ($this->ifIs($oauth['location'])) {
				
				$land = 'land=\''.$oauth['location'].'\', ';
				
			} elseif ($this->ifIs($oauth['city'])) {
				
				$land = 'land=\''.$oauth['city'].'\', ';
				
			} else $land = '';

			if ($this->ifIs($oauth['sex'])) {
			
				$sex = 'sex=\''.$oauth['sex'].'\', ';
				
			} elseif ($this->ifIs($oauth['gender'])) {
				
				$sex = 'sex=\''.$oauth['gender'].'\', ';
				
			}	else $sex = '';			
			
			if ($this->ifIs($oauth['birthday'])) {
				
				$bdate = 'bdate=\''.$oauth['birthday'].'\', ';
				
			} elseif ($this->ifIs($oauth['bdate'])) {
				
				$bdate = 'bdate=\''.$oauth['bdate'].'\', ';
				
			} else $bdate = '';

			if ($this->ifIs($oauth['mobile_phone'])) {
				$phone = 'vk_user_phone=\''.$oauth['mobile_phone'].'\', ';
			} else $phone = '';			
			
			if ($this->ifIs($oauth['link'])) {
			
				$link = $oauth['link'];
				$link = ' '.$prefix.'_link=\''.$link.'\', ';
				
			} else $link = '';


			if ($this->ifIs($member_id[$prefix.'_username'])) {
			
				if ($this->ifIs($oauth[$prefix.'_username'])) {
			
					$prefix_username = $oauth[$prefix.'_username'];
					$prefix_username = ' '.$prefix.'_username=\''.$prefix_username.'\', ';
					
				} else $prefix_username = '';
				
			} else $prefix_username = '';					
		
			$times = time();
			
			if ($this->ifIs($oauth['needhash']) and $oauth['needhash'] == 'yes') {
			
				$auth_hash = ' '.$prefix.'_hash_auth=\''.$oauth['access_token'].'\', ';
			
			} else $auth_hash='';
		
			if ( $this->ifIs($dle_userinfo['name']) ) {

				$oauth['avatar'] = $this->upload_avatar($oauth['avatar'],$dle_userinfo['user_id']); //Загружаем картинку и получаем её адрес
				$oauth['connected'] = 1; $oauth['registered'] = 1;
				$dle_userinfo['userpassword_hash'] = base64_encode($this->encode(@md5($oauth['password'])));
				$hash = ' userpassword_hash=\''.$dle_userinfo['userpassword_hash'].'\', ';
				$lastdate = ' lastdate=\''.$times.'\', ';
				$foto = ' foto=\''.$oauth['avatar'].'\', ';
				$updtime =' updtime=\''.$times.'\', ';
				$db->query( "UPDATE " . USERPREFIX . "_users set "
					.$auth_hash.$hash.$updtime.$prefix_username.$info.$link.$friendlist.$lastdate.$fullname.$land.$foto.$sex.$bdate.$phone
					.$prefix."_registered='$oauth[registered]', ".$prefix."_connected='$oauth[connected]', ".$prefix."_user_id='$oauth[uid]' WHERE user_id = '{$dle_userinfo[user_id]}'" ); //Сохраняем данные пользователя	
			
			}
			
			$dle_userinfo['login1'] = $dle_userinfo['name'];
			$dle_userinfo['password1'] = $oauth['password'];
			
			return $dle_userinfo;
		}
		
		function ifIs($data) {
	
			if (isset($data) and !empty($data)) return true;
			return false;
	
		}
	
	}
}

$vauth_api = new VAuthFunctions ();


?>
