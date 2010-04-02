<?php 
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}
  
  
	Download( $LINK );
	function Download( $link )
	{
	   /*
	    global $premium_acc, $hf_cookie_auth_value;		
		if( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["ifile_it"] ["user"] && $premium_acc ["ifile_it"] ["pass"])			
		  )
		{
			DownloadPremium($link);
		}
		else
		*/
		{
			DownloadFree($link);
		}
	}

	function DownloadFree( $link )
	{
	  global $download_dir, $PHP_SELF, $nn;
	
	  $Referer = $link;
	  $dl_link = "http://ifile.it/dl";
      if($_POST["ffi"] == "ok"){
        $cookie=$_POST["cookie"];
        $ttemp= $_POST["link"]  ;
        $ddlreq= str_replace("type=na&esn=1","type=simple&esn=1&".$_POST["vimg"]."=".$_POST["captcha_check"],$ttemp);
		
        $page = GetPage( $ddlreq, $cookie, 0, $Referer );
        is_present($page, "show_captcha", $strerror = "The captha inserted is wrong", 0) ;
        
		$page = GetPage( $dl_link, $cookie, 0, $Referer );        
        $snap = cut_str ( $page ,'id="req_btn' ,'>download' );
        $Href  = cut_str ( $snap ,'href="' ,'"' );
        
        $FileName = basename($Href);
        
		RedirectDownload( $Href, $FileName );
		exit ();
        
      }else{

        $page = GetPage( $link );
		$cookie = GetCookies( $page );
		
        if(preg_match('/ocation: (.*)/',$page,$loc)){
           $loc = trim($loc[1]);
		   $Referer = $loc;
           $page = GetPage( $loc, $cookie, 0, $Referer);
		
		   if(preg_match("/var\s__alias_id.+=(?:[\t]+)*\'(.+)'/", $page, $cocok)){
		     $alias_id = $cocok[1];
			 $dllink = "http://ifile.it/download:dl_request?alias_id=".$alias_id."&type=na&esn=1";
		   }
		   
		   $page = GetPage( $dllink, $cookie, 0, $Referer);
		}else{
		   html_error( "Error get link ifile.it/dl, probably changes have been made on the page." , 0 );
		}
        
		
        //if (strpos($page,'captcha":"none'))
        if (strpos($page,'captcha":0') )
        {
		   $page = GetPage( $loc, $cookie, 0, $Referer);           
           $snap = cut_str ( $page ,'id="req_btn' ,'>download' );
           $Href  = cut_str ( $snap ,'href="' ,'"' );
           
		   $FileName = basename($Href);        
		   RedirectDownload( $Href, $FileName );
		   exit ();

	   } elseif (strpos($page,'captcha":"simple')){
        
           $rnd='0.'.idnum(16);
		   $Referer = "http://ifile.it/dl";
           $access_image_url='http://ifile.it/download:captcha?'.$rnd;
		   $page = GetPage( $access_image_url, $cookie, 0, $Referer);
               
           $headerend = strpos($page,"JFIF");
           $pass_img = substr($page,$headerend-6);
           $imgfile=$download_dir."ifile_captcha.jpg"; 
           if (file_exists($imgfile)){ unlink($imgfile);} 
           write_file($imgfile, $pass_img);        	

           print "<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["idx"]) ? "?idx=".$_GET["idx"] : "")."\">$nn";
           print "<b>Please enter code:</b><br>$nn";
           print "<img src=\"$imgfile\">$nn";
           print "<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
           print "<input name=\"ffi\" value=\"ok\" type=\"hidden\">$nn";
           print "<input name=\"link\" value=\"$dllink\" type=\"hidden\">$nn";
           print "<input name=\"vimg\" value=\"$__x_c\" type=\"hidden\">$nn";	
           print "<input name=\"captcha_check\" type=\"text\" >";
           print "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";       

        }else{
           html_error( "If you have already reattempted more times then this plugin not work , probably changes have been made on the page. Report it in the Rapidleech forum" , 0 );
        }
      }
	
	}  // end DownloadFree


	function DownloadPremium($link)
	{
	   // not implemented yet =====================
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

    function idnum ($ll){
      $id = "";
      for($i=0; $i<$ll; $i++)
         $id .= floor(rand(0, 9));
      return $id;
    }

/************************ifile.it**********************************
// written by kaox 24-may-2009
// update by kaox 15-nov-2009
// update by Idx 01-apr-2010 - +Rebuild with Malhotra's OOP code
************************ifile.it*********************************/
?>