<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

  //error_reporting(E_ALL);

	Download( $LINK );
	function Download( $link ){
		global $premium_acc;

		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["sharingmatrix"] ["user"] && $premium_acc ["sharingmatrix"] ["pass"]) 
			)
		{
			//DownloadPremium($link);
		}
		else
		{
			DownloadFree($link);
		}		
	}

	function DownloadFree( $link )
	{
	    global $PHP_SELF, $nn;
	    $Referer = $link;
		if( isset($_POST['step']) && $_POST['step'] == "1"){
		  $post=array();
	      //$post["?&code"]= $_POST["captcha"];
	      $post["code"]= $_POST["captcha"];
          $cookie=urldecode($_POST["cookie"]); 
		  $FileName = urldecode($_POST["filename"]);
		  $id=$_POST["id"];
		  $verify = "http://sharingmatrix.com/ajax_scripts/verifier.php";
		  $page = GetPage( $verify, $cookie, $post, $Referer );
		  
		  $snap = substr ( stristr ( $page, "\r\n\r\n" ), strlen ( "\r\n\r\n" ) );
	      $st = explode("\r\n", $snap);
          $st = join("", $st);
		  if(preg_match("/7.*\r\n(\d{1})\r\n.*/", $snap, $cucok)){
		    $match = $cucok[1];
		  }

	      if( isset($match) && $match == "1" ){
		    $dl = "http://sharingmatrix.com/ajax_scripts/dl.php";
		    $page = GetPage( $dl, $cookie, 0, $Referer );
			
		    preg_match('/\d+\r\n(\d+)\r\n\d+/i', $page, $mh);
	        $did=$mh[1];	      
	        $_get = "http://sharingmatrix.com/ajax_scripts/_get.php?link_id=".$id."&link_name=".$FileName."&dl_id=".$did."&password=";
		    $page = GetPage( $_get, $cookie, 0, $Referer );
		    $sv = cut_str ( $page ,'serv:"' ,'"' );
	        $hs = cut_str ( $page ,'hash:"' ,'"' );
	        $dwn=$sv."/download/".$hs."/".$did."/";
		    
		    RedirectDownload( $dwn, $FileName, $cookie );
		    exit ();
		  }else{
		    $err_msg = "Captcha incorrect! Please reattempt";
		  }
		  
		}
		
		$page = GetPage( $link );
		is_present($page,"File has been deleted", "File has been deleted", 0);
	    is_present($page,"already downloading file", "You are already downloading file. Only premium users can download several files at the same time.", 0);
        //if($snap) html_error("download link not found , please verify the link in your browser" , 0 );
		$cookie = GetCk($page);

		preg_match('/\/file\/(\d+)/i', $link, $mh);
	    $id = $mh[1];
	    $req="http://sharingmatrix.com/ajax_scripts/download.php?type_membership=free&link_id=".$id ;
		$page = GetPage( $req, $cookie, 0, $Referer );

		$timer = cut_str ( $page ,"ctjv = '" ,"'" );
	    $link_name = cut_str ( $page ,"link_name = '" ,"'" );
	    $capcay_url = cut_str ( $page ,"CAPTCHA_IMAGE_URL = '" ,"'" );
		if(isset($err_msg)) echo $err_msg."<br>";
		insert_timer($timer, "Please wait");
		
	    $ses_url="http://sharingmatrix.com/ajax_scripts/check_timer.php?tmp=".rand(1,999);
		$page = GetPage( $ses_url, $cookie, 0, $Referer );
		
		if(preg_match("/img:([\w]+)/i", $page, $cucok)){
	      $img_url=$capcay_url . $cucok[1] . ".jpg";
          $code = '<div align=center >'.(isset($err_msg) ? $err_msg:'').
		           '<form method="post" action="'.$PHP_SELF.(isset($_GET["idx"]) ? "?idx=".$_GET["idx"] : "").'">'.$nn;
          $code .= '<input type="hidden" name="step" value="1">'.$nn;
          $code .= '<input type="hidden" name="link" value="'.urlencode($link).'">'.$nn;
          $code .= '<input type="hidden" name="id" value="'.$id.'">'.$nn;
          $code .= '<input type="hidden" name="filename" value="'.urlencode($link_name).'">'.$nn;
          $code .= '<input type="hidden" name="cookie" value="'.urlencode($cookie).'">'.$nn;
          $code .= 'Please enter : <img src="'.$img_url.'?'.rand(1,10000).'"><br><br>'.$nn;
          $code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
          $code .= '</form></div>';
          exit($code);
		
		}else{
		
		    preg_match('/\d+\r\n(\d+)\r\n\d+/i', $page, $mh);
	        $did=$mh[1];	      
	        $_get = "http://sharingmatrix.com/ajax_scripts/_get.php?link_id=".$id."&link_name=".$FileName."&dl_id=".$did."&password=";
		    $page = GetPage( $_get, $cookie, 0, $Referer );
		    $sv = cut_str ( $page ,'serv:"' ,'"' );
	        $hs = cut_str ( $page ,'hash:"' ,'"' );
	        $dwn=$sv."/download/".$hs."/".$did."/";
		    
		    RedirectDownload( $dwn, $FileName, $cookie );
		}
		
	}
	
	function DownloadPremium($link)
	{
		global $premium_acc;
		$Referer = $link;
	    $sm_user = $_REQUEST["premium_user"] ? trim( $_REQUEST["premium_user"] ) : $premium_acc["sharingmatrix"]["user"];
		$sm_pass = $_REQUEST["premium_pass"] ? trim( $_REQUEST["premium_pass"] ) : $premium_acc["sharingmatrix"]["pass"];
		$loginUrl='http://sharingmatrix.com/ajax_scripts/login.php?email='.$sm_user.'&password='.$sm_pass.'&remember_me=false';
		$page = GetPage( $loginUrl, 0, $post, $Referer );
		$cookie = "PHPSESSID=".cut_str ( $page ,'Set-Cookie: PHPSESSID=' ,';' );
	    $snap = cut_str ( $page ,"\r\n\r\n" ,"\r\n\r\n" );
	    $st= explode("\r\n",$snap);
        $st= join("", $st);
	    if( $st !== "110" ){
	       html_error( "Bad username/password combination" , 0 );
	    }
		
		$page = GetPage( $link, $cookie, 0, $Referer );
		$snap = cut_str ( $page ,"sUrl='http://" ,"'" );
	    is_present($page,"File has been deleted", "File has been deleted", 0);
	    if($snap) html_error("download link not foun , please verify the link in your browser" , 0 );
		$FileName = cut_str ( $page ,'link_name=' ,'&' );
		
		$page = GetPage( "http://".$snap, $cookie, 0, $Referer );
		$sv = cut_str ( $page ,'serv:"' ,'"' );
	    $hs = cut_str ( $page ,'hash:"' ,'"' );
	    $dwn=$sv."/download/".$hs."/0/";
		 
		RedirectDownload( $dwn, $FileName, $cookie, 0, $Referer );
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
	

	// Manual get cookie
	function GetCk($content){
      $parthead = preg_replace('/Set-Cookie: .+deleted.+|Set-Cookie: .+=;.+/i', '', $content);
      preg_match_all('/Set-Cookie: (.*);/U',$parthead,$temp);
      $cookie = $temp[1];
      $cook = implode('; ',$cookie);
      return $cook;
    }
	
/************************sharingmatrix.com**********************************	
 WRITTEN BY KAOX 08-oct-09
 Update by Idx 10-May-2010
************************sharingmatrix.com**********************************/
?>