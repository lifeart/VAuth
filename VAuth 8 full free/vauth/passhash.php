<?PHP 

include (ENGINE_DIR . '/modules/vauth/settings/script_settings.php');
//userpassword_hash

function get_passhash($password) {
	
	global $userhash_pass;
	global $userhash_salt;

	$strlen = strlen($password);
	$seq = $userhash_pass;
	$gamma = '';
	
	while (strlen($gamma)<$strlen)	{
		$seq = pack("H*",sha1($gamma.$seq.$userhash_salt)); 
		$gamma.=substr($seq,0,8);
	}
	
	$my_lovers = $password^$gamma;
	
	$password = base64_encode($my_lovers);

	return $password;
}

?>