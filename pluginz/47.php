<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

	Download( $LINK );
	function Download( $link )
	{
		global $premium_acc, $hf_cookie_auth_value;
		
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["hotfile_com"] ["user"] && $premium_acc ["hotfile_com"] ["pass"]) ||
			($_REQUEST["hf_acc"] == "on" && ($_REQUEST ["hf_cookie"] || $_REQUEST["hf_hash"] || $hf_cookie_auth_value))
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
		$page = GetPage( $link );
		
	    is_present($page,"File not found","File not found, the file is not present or bad link","0");
	    is_present($page,"due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");
	    is_present($page,"You are currently downloading","You are currently downloading. Only one connection with server allow for free users","0");
		
		preg_match_all( '/timerend=d\.getTime\(\)\+(\d+)/i', $page, $arraytime ); 
		$wtime = $arraytime[1][1]/1000;    
		if ( $wtime > 0 ) 
		{
			insert_timer( $wtime, "You reached your hourly traffic limit" ); 
		}
		
		$action = trim ( cut_str( $page, "action value=",">" ) );
		$tm = trim ( cut_str( $page, "tm value=",">" ) );
		$tmhash = trim ( cut_str( $page, "tmhash value=",">" ) );
		$wait = trim ( cut_str( $page,"wait value=",">" ) );
		$waithash = trim ( cut_str( $page, "waithash value=",">" ) );
		
		$post = array();
		$post["action"] = $action;
		$post["tm"] = $tm;
		$post["tmhash"] = $tmhash;
		$post["wait"] = $wait;
		$post["waithash"] = $waithash;
		insert_timer( $wait, "Waiting link timelock" );   
		
		$page = GetPage( $link, 0, $post );
		
		preg_match( '/http:\/\/.+get\/[^\'"]+/i', $page, $loca );
		$Href = trim( $loca[0] );
		
		$page = GetPage( $Href );
		
		preg_match('/ocation: *(.+)/i', $page, $newredir );
		$Href = trim ( $newredir [1] );
		
		if ( strpos( $Href,"http://" ) === false ) 
		{ 
			html_error("Server problem. Please try again after", 0 );
		}
		
		$Url = parse_url( $Href );
		$FileName = basename($Url["path"]);
		RedirectDownload( $Href, $FileName );
		exit ();
	}
	
	function DownloadPremium($link)
	{
		global $premium_acc, $Referer, $hf_cookie_auth_value;
		$Referer1 = "http://hotfile.com/";
		$post = array();
		if($_REQUEST["premium_acc"] == "on" && 
		   ( ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || 
		     ($premium_acc ["hotfile_com"]["user"] && $premium_acc ["hotfile_com"]["pass"])
		   ) ){
		 $loginUrl = "http://hotfile.com/login.php";		
		 $post["returnto"] = "/";
		 $post["user"] = $_REQUEST["premium_user"] ? trim( $_REQUEST["premium_user"] ) : $premium_acc["hotfile_com"]["user"];
		 $post["pass"] = $_REQUEST["premium_pass"] ? trim( $_REQUEST["premium_pass"] ) : $premium_acc["hotfile_com"]["pass"];
		 
		 $page = GetPage( $loginUrl, 0, $post, $Referer1 );

		 $cookie = GetCookies( $page );
		}
		$Referer = $link;
		
		if(!preg_match('/auth=\w+;/i', $page, $ook)){
		  if($_GET ["hf_acc"]=="on"){
		    if($cookie!="" && substr(trim($cookie),-1)!=";")$cookie.="; ";
		    if($_GET["hf_cookie"]) {
		       $cookie .= 'auth='.$_GET["hf_cookie"];
		    }elseif ($_GET["hf_hash"]) {
		       $cookie .= 'auth='.strrev(dcd($_GET["hf_hash"]));
		    }elseif ($hf_cookie_auth_value) {
		       $cookie .= 'auth='.$hf_cookie_auth_value;			
		    }
		  }else{
		    html_error("Login Failed , Bad username/password combination.",0);
		  }
		}


		is_present( $page, "suspended.html", "Account has been suspended","0" );
			
		$page = GetPage( "http://hotfile.com/?lang=en", $cookie, 0, $Referer1 );
		
		//$findpre = "Premium\s\|(?:[^\/]+)\/premiuminfo\.html";
		$findpre = "Premium<\/sp";
				
		if( !preg_match("/{$findpre}/", $page) )
		{		    
			is_present( $page, "Free</sp", "Account found with no longer as Premium","0" );			
			html_error( "Login Failed , Bad username/password combination.",0 );
		}		
		
		$page = GetPage( $link, $cookie, 0, $Referer );		
		
		is_present( $page, "File not found", "File not found, the file is not present or bad link","0" );
		is_present( $page, "due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");

	    
	    preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$page,$status);
        
		if ($status[1] == 200) {
          preg_match('/http:\/\/.+get[^"\'\s]+/i', $page, $loca);
          $Href = rtrim($loca[0]);		  
          $page = GetPage( $Href, $cookie, 0, $Referer );		
		  preg_match('/Location: *(.+)/i', $page, $newredir );
		  $Href = trim ( $newredir [1] );		  
        } else{
          preg_match('/ocation:.+(http:\/\/s\d+\..+)\b/i', $page, $loca);
          $Href = rtrim($loca[1]);
        }		

		$FileName = str_replace(" ","_",basename(urldecode($Href)) );
		RedirectDownload( $Href, $FileName, $cookie, 0, $Referer );
		exit ();
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
	

/************************hotfile.com**********************************	
written by kaox 15-oct-2009
update by kaox 10-jan-2010

Fixed  downloading from free and premium account, Converted in OOPs format, removed un-neccesary code by Raj Malhotra on 27 Feb 2010
Update by Idx 03-apr-2010
Update by Idx 10-May-2010 - upd $findpre HF pattern
************************hotfile.com**********************************/
?>