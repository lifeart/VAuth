<?PHP

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_blockip'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

$info = '';$users = '';$settings = '';

include_once ENGINE_DIR . '/modules/vauth/settings/script_settings.php';
	/* Подключаем меню админки */
include_once ENGINE_DIR . '/modules/vauth/admin/menu.php';

	/* Подключаем настройки модуля */

	switch($_GET['page']) {
	
		case "info" :		include_once ENGINE_DIR . '/modules/vauth/admin/info.php';	break;
		
		case "users":		include_once ENGINE_DIR . '/modules/vauth/admin/users.php';	break;
		
		case "settings" :	include_once ENGINE_DIR . '/modules/vauth/admin/settings.php';	break;
		
		case 'vauthinfo' :
			$page = '<div class="hello">'.$vauth_text['hellopage'].'</br></br><b><a href="http://vk.com/vauth">'.$vauth_text['support_group'].'</a></b></br></br></div>';
			break;
		
		default :
			$page = '<div class="hello">'.$vauth_text['hellopage'].'</br></br><b><a href="http://vk.com/vauth">'.$vauth_text['support_group'].'</a></b></br></br></div>';
		break;
		

	}

echoheader( "vauth", $lang[vauth] );

$vauth_style = $style['admin'];

$vauth_header = '<table width="100%"><tr><td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td><td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td><td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td></tr><tr><td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td><td style="padding:5px;" bgcolor="#FFFFFF" class="ajax_pic"><table width="100%"><tr><td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">'.
		
		$vauth_text['vauth_header2']
		
		.'</div></td></tr></table><div class="unterline"></div>';

	
$vauth_footer = '</td><td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td></tr><tr><td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td><td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td><td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td></tr></table>';
$vauth_table_1 = '<table width="100%"><tr><td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td><td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td><td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td></tr><tr><td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td><td style="padding:5px;" bgcolor="#FFFFFF" ><table width="100%"><tr><td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">'.$vauth_text['vauth_header'].'</div></td></tr></table><div class="unterline"></div>';
$vauth_table_2 = '</td><td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td></tr><tr><td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td><td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td><td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td></tr></table>';



echo $vauth_table_1.$vauth_menu.$vauth_table_2;
if ($menus == 'users') echo $vauth_table_1.$vauth_search.$vauth_table_2;
echo $vauth_style;
echo $vauth_header;
echo $page;
echo $vauth_footer;

echofooter();
?>


<script>
	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			//vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	var uv = getUrlVars();
	
	
	
	var ap_loading = false;
	function ap_load(append, name, val) {
		if(uv['page']!='users') return;
		if(ap_loading) return;
		ap_loading = true;
		var cnt = $(".ajax_pic").find(($("[name='style']").val()=='list' ? ".userlist_list":".userlist")).length;		
		if(append!=undefined) cnt = 0;
		var params = {
			limit_from: cnt,
			style				: $("[name='style']").val(),
			sort				: $("[name='sort']").val(),
			site				: $("[name='site']").val(),
			sex					: $("[name='sex']").val(),
			friends				: $("[name='friends']").val(),
			vis					: $("[name='vis']").val(),
			connected			: $("[name='connected']").val(),
			reg					: $("[name='reg']").val(),
			search_name			: $("[name='search_name']").val(),
			search_mail			: $("[name='search_mail']").val(),
			search_banned		: $("[name='search_banned']").attr("checked"),
			fromregdate			: $("[name='fromregdate']").val(),
			toregdate			: $("[name='toregdate']").val(),
			fromentdate			: $("[name='fromentdate']").val(),
			toentdate			: $("[name='toentdate']").val(),
			search_news_f		: $("[name='search_news_f']").val(),
			search_news_t		: $("[name='search_news_t']").val(),
			search_coms_f		: $("[name='search_coms_f']").val(),
			search_coms_t		: $("[name='search_coms_t']").val(),
			search_reglevel		: $("[name='search_reglevel']").val(),			
			}
		for(x in uv) {
			eval('('+"params."+x+"=\""+uv[x]+'")');
			}
		
		$.get("/engine/modules/vauth/admin/userlist.php", params, 
			function(data) {
				if(append==undefined) $(".ajax_pic").append(data);
					else $(".ajax_pic").html(data);
				ap_loading = false;
				
				
				
				});
		}
		
	ap_load();
	
	function ap_scroll() {
		if($(document).height()-$(window).height() <= $(window).scrollTop() - 0) {      
			ap_load();
			}	
		}
	$(window).scroll(function(){ 
		 ap_scroll(); 
		 });	
		
	$("#ap_fields").find("input").change(function() {
		ap_load(true);
		});	
	$("#ap_fields").find("input").keyup(function() {
		ap_load(true);
		});	
	$("#ap_fields").find("select").change(function() {
		ap_load(true);
		});			
		
		
		
		
		
		
		
</script>