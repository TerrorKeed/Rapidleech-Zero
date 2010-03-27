<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}
  
  
	Download( $LINK );	
	function Download($link) 
	{
		DownloadFree($link);
	}	

	function DownloadFree( $link )
	{
        global $Referer;
		
        // force lang page to english
	    //$enUrl = "http://www.4shared.com/main/translate/setLang.jsp?silent=true&lang=en";	    
		//$page = GetPage( $enUrl, $cookie, 0, $Referer);	
	    $cookie = "4langcookie=en; ";		
		$page = GetPage( $link, $cookie, 0, $Referer);
		
	    preg_match_all('/Set-Cookie: ([^;]+)/', $page, $cook);
	    $cookie .= implode('; ', $cook[1]);
		
		if(preg_match('/Location: (.*)/', $page, $newredir))
        {
			$dUrl = $newredir[1];
        }
		elseif(preg_match('/href="([^;^"]+).*Download\sNow/i', $page, $newredir))
        {
            $dUrl = $newredir[1];
        }
		
		$page = GetPage( $dUrl, $cookie, 0, $Referer);
		is_page($page);
		
		if(preg_match('/var c = ([0-9]+);/', $page, $count))
	    {
	       $countDown = $count[1];
	       insert_timer($countDown, "Waiting link timelock");
	    }
		
		if(preg_match('%window\.location = "(http://.+?)";%', $page, $redir)){
            $nlink = $redir[1];
        }elseif(preg_match('/(http.*?)\'.*Click here to download/', $page, $redir)){
            $nlink = $redir[1];
        }else{
            html_error("Download-link not found.", 0);
        }
		
		$FileName = str_replace ( " " , "_" , basename($nlink) );		
		RedirectDownload( $nlink, $FileName, $cookie, 0, $Referer );
		exit ();		
	}
	
	function DownloadPremium( $link )
	{
	   // not imlemented yet
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
			(isset($_GET["audl"]) ? "&audl=doum" : "") . $addon;
		
		insert_location ( $loc );
	}
	
/***********4shared.com**************\
 WRITTEN BY KAOX xx-xx-xxxx
 Upate By Idx 25-mar-2010
 
 Upate By Idx 27-mar-2010 Rewrite in OOP based on Raj Malhotra code 
\***********4shared.com**************/
?>