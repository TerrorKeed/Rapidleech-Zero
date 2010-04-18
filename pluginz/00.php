<?php    
if (!defined('RAPIDLEECH')){
  require_once("404.php");
  exit;
}
	  
	Download( $LINK );
	function Download( $link )
	{
	   
	    global $premium_acc;		
		if ($_REQUEST["premium_acc"] == "on" && (( $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ( $premium_acc["filefactory"]["user"] && $premium_acc["filefactory"]["pass"])) ){
			DownloadPremium($link);
		} else {
			DownloadFree($link);
		}
	}

	function DownloadFree( $link )
	{

	  $Referer = $link;
	  $page = GetPage( $link, 0, 0, $Referer );
	  
	  if( preg_match('/ocation: ([\w\.\:\/]+)/i', $page, $loca)) {	  
        $redir = rtrim($loca[1]);        
		$Href= 'http://filefactory.com'.$redir;
	    $page = GetPage( $Href, 0, 0, $link );
      }
	  
	  $btn=cut_str($page,'class="basicBtn">','</div>');	  
	  $out=cut_str($btn,'href="','"');
	  $one = str_replace('/dlf', 'http://www.filefactory.com/dlf', $out);	  
	  $page = GetPage( $one, 0, 0, $Referer );
      
	  if(preg_match('/http:\/\/.+filefactory\.com\/dl\/f\/[^\'"]+/i', $page, $link) ){	     
	     $Href = $link[0];
	     $Url = parse_url($Href);
	  }else{
	     html_error('Error get link', 0);
	  }
      $FileName = basename($Url["path"]); 
      $wait = cut_str($page,'startWait" value="','"');
	  insert_timer($wait, "Preparing Your File");
	  RedirectDownload( $Href, $FileName, $lcook[1] );
	  exit ();
	  
	}  // end DownloadFree


	function DownloadPremium($link)
	{
	   global $premium_acc;
	   $Referer = $link;
	   $page = GetPage( $link );
	   if( preg_match('/ocation: (.+)/i', $page, $loca)){
          $redir = rtrim($loca[1]);
          $Href= "http://filefactory.com".$redir;
	      $page = GetPage( $Href, 0, 0, $Referer );
       }
	   is_present($page, 'File Not Found', 'Error - File was not found!');
	   $post = array();
	   $post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["filefactory"]["user"]  ;
	   $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["filefactory"]["pass"];
	   $post['redirect'] = "http://www.filefactory.com/";
	   
	   $HrefLogin = $post['redirect'] . "member/login.php";
	   $page = GetPage( $HrefLogin, 0, $post, $Referer );
	   
	   if (!preg_match('%(ff_membership=.+); expires%', $page, $lcook)) html_error('Not logged in please check your credentials in config.php', 0);
	   
	   $cook = $lcook[1];
	   $page = GetPage( $link, $cook, $post, $Referer );
	   preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$page,$status);
	   switch($status[1]){
	      case "200":
	        if (!preg_match('%(http://dl\d{3}\.filefactory\.com/dlp/.+)">Download with FileFactory Premium%U', $page, $redir2)) {
              if (!preg_match('%(http://archive\d{2}\.filefactory\.com/dlp/.+)">Download with FileFactory Premium%U', $page, $redir2)) {
                $dwn = false;
              }
	        }
	        $dwn=$redir2[1];
	        break;
	      case "302": case "301":
			if( preg_match('/ocation: ([\w\.\:\/]+)/i', $page, $loca)){
			   $dwn = $loca[1];
			   if(stristr("filefactory.com", $dwn)===false) $dwn = "http://filefactory.com" . $dwn;
			   if($status[1]=="301"){
			     $page = GetPage( $dwn, $cook, 0, $Referer );
				 if( preg_match('/ocation: ([\w\.\:\/]+)/i', $page, $loca)){
				    $dwn = $loca[1];
				 }else{
				    $dwn = false;
				 }
			   }
			}else{
	           $dwn = false;
			}	        
	      break;
	      default:
	        $dwn = false;
	      break;
	   }
	   

	   if(!$dwn) html_error('Error getting redirect 2', 0);
	   $Href = $dwn;
	   $Url = parse_url($dwn);
	   $FileName = basename($Url['path']);
	   RedirectDownload( $Href, $FileName, $cook );
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



/************************filefactory.com**********************************
	szal 18-jun-09
	filefactory download plugin by mrbrownee70, created june 22, 2009 
	update by kaox 19-oct-2009
    Update Idx 18-apr-2010
************************filefactory.com*********************************/
?>