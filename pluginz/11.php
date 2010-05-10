<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

	Download( $LINK );
	function Download( $link )
	{
		global $premium_acc, $hf_cookie_auth_value;
		
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["megashares"] ["user"] && $premium_acc ["megashares"] ["pass"])
		   )
		{
			DownloadPremium($link);
		}
		else
		{
			DownloadFree($link);
		}
	}

	function DownloadFree( $link )
	{
	    global $PHP_SELF, $download_dir, $nn;
		
		if(isset($_POST["ms"]) && $_POST["ms"] == "ok"){
		   $cookies = $_POST["cookies"];		   
		   $Href = $_POST["dllink"];
		   $name = $_POST["name"];
		   
		   $param = unserialize(base64_decode($_POST["param_str"]));
		   $param["captcha"] = $_POST["captcha"];
		   $param["rndtime"] = $_POST["rndtime"];
		   
		   $final = "http://".$param["mhost"].$param["fquery"]."&rs=check_passport_renewal&rsargs[]=".$param["captcha"]."&rsargs[]=".$param["randnum"]."&rsargs[]=".$param["pass"]."&rsargs[]=replace_sec_pprenewal&rsrnd=".$param["rndtime"];
           
		   $page = GetPage( $final, $cookies, 0, $link );

		   if(preg_match('/Thank you for reactivating your passport/', $page)){
		     
		     $FileName = $name;
			 RedirectDownload( $Href, $FileName, $cookies );
			 exit();			 
		     
		   }else{		   
		   	 html_error("An error occured", 0);			 
		   }
	    }
		
		$page = GetPage( $link );		
		$cookies = GetCookies($page);
		
		$page = GetPage( $link, $cookies );		
		is_present($page, "This link requires a password to continue:", "This file is password protected");
		is_present($page, "Link was deleted as it was not downloaded", "File not found");
		is_present($page, "Invalid link", "File not found");
		is_present($page, "Link not found", "File not found");
		is_present($page, "Link was removed", "File not found");
		is_present($page, "This link's <u>filesize is larger</u> than what you have left on your Passport.");
		is_present($page, "You can download again when your download completes", "This IP address is already downloading a file");
		$cookies .= "; " . GetCookies($page);		
		preg_match('%http://.+megashares\.com/index\.php\?d01=[^"\']+%', $page, $dllink);
		$name = cut_str ( $dllink[0] ,'fln=/' ,'"' );
		
		if(preg_match('/Your Passport needs to be reactivated/', $page)){
            is_present($page,"All download slots for this link are currently filled", "The server response is: All download slots for this link are currently filled. Please try again momentarily.", 0);
			
			preg_match('/var request_uri *= *"(.*)";/', $page, $fquery);
			preg_match('/src="index\.php\?secgfx=gfx&random_num=(.+?)"/', $page, $randnum);
			preg_match('/name="passport_num".*value="(.*?)"/', $page, $pass);
			
			if(!$fquery || !$randnum || !$pass){
			   html_error("Missing parameter", 0);
			}
			
			$ms_Url = parse_url($link);
			$param["fquery"] = $fquery[1];
			$param["randnum"] = $randnum[1];
			$param["pass"] = $pass[1];
			$param["mhost"] = $ms_Url["host"];
			$param_str = base64_encode(serialize($param));
			
			$img = "http://".$ms_Url["host"]."/index.php?secgfx=gfx&random_num=".$randnum[1];
			
			$page = GetPage( $img, $cookies );
			
			$headerend = strpos($page,"\r\n\r\n");
			$pass_img = substr($page,$headerend+4);
			write_file($download_dir."megashare_captcha.png", $pass_img);
			
			print  "<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["idx"]) ? "?idx=".$_GET["idx"] : "")."\" name=\"msform\">$nn";
			print "<p><b>Enter the passport reactivation code in the graphic, then hit the 'Reactivate Passport' button.</b></p>$nn";
			print "<p><img src=\"{$download_dir}megashare_captcha.png?id=".rand(10000, 100000)."\" ></p>$nn";
			print "<input name=\"link\" value=\"$link\" type=\"hidden\">$nn";
			print "<input name=\"param_str\" value=\"$param_str\" type=\"hidden\">$nn";
			print "<input name=\"cookies\" value=\"$cookies\" type=\"hidden\">$nn";
			print "<input name=\"dllink\" value=\"$dllink[0]\" type=\"hidden\">$nn";
			print "<input name=\"name\" value=\"$name\" type=\"hidden\">$nn";
			print "<input name=\"captcha\" type=\"text\" >$nn";
			print "<input type=\"hidden\" name=\"rndtime\">\n";
			print "<input name=\"ms\" value=\"ok\" type=\"hidden\">$nn";
			print "<script type=\"text/javascript\">function check() { document.msform.rndtime.value=new Date().getTime(); return true; }</script>$nn";
			print "<input name=\"Submit\" onclick=\"return check()\" value=\"Reactivate Passport\" type=\"submit\"></form>";
		
		}else{
		    // No need reactived
			$Href = $dllink[0];			
			$FileName = $name;
		    RedirectDownload( $Href, $FileName, $cookies );
		}		
		exit();
	}
	
	function DownloadPremium($link)
	{
		global $premium_acc;
		$ms_Url = "http://www.megashares.com/";
		
		$page = GetPage( $ms_Url );
		
		if(preg_match('/\|.*href="(.+)"\s.*<span>Login<\/span>/', $page, $match)){
		  $login_Url = $match[1];		
		}else{
		  html_error("Unable get Login link", 0);
		}
		
		$post = Array();
        $post["mymslogin_name"] = (($_GET["premium_user"] && $_GET["premium_pass"]) ? $_GET["premium_user"] : $premium_acc["megashares"]["user"]);
        $post["mymspassword"] = (($_GET["premium_user"] && $_GET["premium_pass"]) ? $_GET["premium_pass"] : $premium_acc["megashares"]["pass"]);
        $post["myms_login"] = "Login";
		$page = GetPage( $login_Url, 0, $post, $ms_Url );

        is_present($page, "Failed login message:", "Wrong username or password");
        is_notpresent($page, "Set-Cookie: myms=", "Cannot use premium account");
		$cookies = GetCookies($page);
		
		$page = GetPage( $link, $cookies, 0, $ms_Url );
		
		is_present($page, "This link requires a password to continue:", "This file is password protected");
        is_present($page, "Link was deleted as it was not downloaded", "File not found");
        is_present($page, "Invalid link", "File not found");
        is_present($page, "Link not found", "File not found");
        is_present($page, "Link was removed", "File not found");
        if (preg_match('/href="(http:\/\/.+)">.+download\sfile/', $page, $href)) {
			$Href = $href[1];
			$FileName = str_replace(" ","_",basename(urldecode($Href)) );			
		    RedirectDownload( $Href, $FileName, $cookies, 0, $link );
		    exit ();
        } else {
        	html_error("Download link not found", 0);
        }
	}
	
	/**
	 * You can use this function to retrieve pages without parsing the link
	 * 
	 * @param string $link The link of the page to retrieve
	 * @param string $cookie The cookie value if you need
	 * @param array $post name=>value of the post data
	 * @param string $referer The referer of the page, it might be the value you are missing if you can't get plugin to work
	 * @param string $auth Page authentication, unneeded in most circumstances
	 */
	function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}

		$Url = parse_url(trim($link));
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $referer, $cookie, $post, 0, $_GET ["proxy"], $pauth, $auth );
		is_page ( $page );
		return $page;
	}
	
	/**
	 * Use this function instead of insert_location so that we can improve this feature in the future
	 * 
	 * @param string $link The download link of the file
	 * @param string $FileName The name of the file
	 * @param string $cookie The cookie value
	 * @param array $post The post value will be serialized here
	 * @param string $referer The page that refered to this link
	 * @param string $auth In format username:password
	 * @param array $params This parameter allows you to add extra _GET values to be passed on
	 */
	function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $auth = "", $params = array()) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$Url = parse_url($link);
		
		if (substr($auth,0,6) != "&auth=") $auth = "&auth=" . $auth;
		if (!is_array($params)) {
			// Some problems with the plugin, quit it
			html_error('Plugin problem! Please report, error: "The parameter passed must be an array"');
		}
		$addon = "";
		if (count((array) $params) > 0) {
			foreach ($params as $name => $value) {
				if (is_array($value)) {
					$value = serialize($value);
				}
				$addon .= '&'.$name.'='.urlencode($value).'&';
			}
			$addon = substr($addon,0,-1);
		}
		$loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode ( $FileName ) . 
			"&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . 
			urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
			"&referer=" . urlencode ( $referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
			"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . 
			"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . 
			"&link=" . urlencode ( $link ) . ($_GET ["add_comment"] == "on" ? "&comment=" . 
			urlencode ( $_GET ["comment"] ) : "") . $auth . ($pauth ? "&pauth=$pauth" : "") . 
			($_GET ["uploadlater"] ? "&uploadlater=".$_GET["uploadlater"]."&uploadtohost=".$_GET['uploadtohost'] : "") .
			"&cookie=" . urlencode($cookie) .
			"&post=" . urlencode ( serialize ( $post ) ) .
			($_POST ["uploadlater"] ? "&uploadlater=".$_POST["uploadlater"]."&uploadtohost=".urlencode($_POST['uploadtohost']) : "").
			($_POST ['autoclose'] ? "&autoclose=1" : "").
			(isset($_GET["idx"]) ? "&idx=".$_GET["idx"] : "") . $addon;

		insert_location ( $loc );
	}
	

/************************megashares.com**********************************	
update by kaox 09-jan-2010 (FIX for free download)
Update by Idx 10-May-2010
************************megashares.com**********************************/
?>