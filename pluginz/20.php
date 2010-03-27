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
        global $Referer, $PHP_SELF, $nn;
		
		
		if(isset($_POST["userPass2"]) && $_POST["userPass2"]!=""){
	      $post=array();
          $post["userPass2"]=$_POST["userPass2"];
          $cookie=$_POST["cookie"];
		  $Referer=$_POST["link"];		  
		  
		}else{
		  $post = $cookie = 0;		  
		}
		$page = GetPage( $link, $cookie, $post, $Referer );
		$cookie = GetCookies($page); 
		
		// passworded
        if(preg_match("/<form.+\"theForm\"/i", $page)){
		  global $PHP_SELF, $nn;
		
          $code = $nn.$nn."<div style=\"text-align: center\"><br><br>\n";
		  if(strpos($page, "passError();") !== false)
          {
            $code .= "<div style=\"text-align: center\">The password you have entered is not valid. <br> Please enter a correct password.</div>";
          }else{
            $code .= "<div style=\"text-align: center\">Please enter password to access this file:</div><br>";
          }
		  
          $code .= '<div style="text-align: center">'.$nn;
		  $code .= '<form name="theForm" method="post" action="'.$PHP_SELF.'">'.$nn;
          $code .= ' <input type="password" name="userPass2" style="width:175px"/>'.$nn;
          $code .= ' <input type="submit" value="Send password" />'.$nn;
          $code .= ' <input type="hidden" name="cookie" value="'.$cookie.'">'.$nn;
          $code .= ' <input type="hidden" name="link" value="'.urlencode($link).'">'.$nn;
          $code .= '</form></div></div></body></html>';
          echo $code;
          die();
        } // end passworded
		
        if (preg_match('/var c = ([0-9]+);/', $page, $count))
        {
            $countDown = $count[1];
            insert_timer($countDown, "Waiting link timelock");
        }        
		
        preg_match('/location = "(.*)";/', $page, $loc);
		$Href = $loc[1];
		$FileName = str_replace ( " " , "_" , basename($Href) );
		
		RedirectDownload( $Href, $FileName, $cookie, 0, $Referer );
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
	
/***********2shared.com**************\
 WRITTEN BY KAOX xx-xx-xxxx
 Upate By Idx 25-mar-2010
 
 Upate By Idx 27-mar-2010 Rewrite in OOP based on Raj Malhotra code 
\***********2shared.com**************/
?>