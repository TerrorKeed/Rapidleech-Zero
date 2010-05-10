<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

  

  	Download( $LINK );
	function Download( $link )
	{
		global $premium_acc;
		
		if ($_REQUEST["premium_acc"] == "on" && 
		    ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || 
			($premium_acc["mediafire"]["user"] && $premium_acc["mediafire"]["pass"])
		   )
		{
			//DownloadPremium($link);
			
			echo "<p><b>Download with Premium is un-implemented, trying with free download</b></p>...";
			DownloadFree($link);
		}
		else
		{
			DownloadFree($link);
		}
	}
	

	function DownloadFree( $link )
	{
	    global $PHP_SELF, $download_dir, $nn;
		
	    $Referer = $link;
		
	    if($_POST["step"]){
           $cookie=$_POST["cookie"];
           $post["recaptcha_challenge_field"]=$_POST["ch"];
           $post['recaptcha_response_field']=urlencode($_POST['captcha']);
		   $page = GetPage( $link, $cookie, $post, $Referer );
        }
		
        if($_POST["passfile"]){
            $post=array();
            $post["downloadp"]=$_POST["downloadp"];
            $cookie=$_POST["cookie"];
			$page = GetPage( $link, $cookie, $post, $Referer );
        }else{
            $page = GetPage( $link, 0, 0, $Referer );
            $cookie = GetCookies($page); 
            	  
            if(preg_match('/Location: (.*)/i', $page, $redir))
            {
              preg_match('/Location:.*error/i', $page) ? html_error("The Link is Invalid or the File is Deleted.", 0) : '';
              $Href = trim($redir[1]);
              if( strpos( $Href ,"http://mediafire.com")!== false ){
              }else{
                $Href="http://www.mediafire.com".$Href;
              }
              $page = GetPage( $Href, 0, 0, $Referer );
            }
        }		
		
       	$ev = cut_str($page, "Eo(); ", "; ").";"; 

        if(preg_match("/dh\('(.*)'\)/i", $ev, $pass)){
            
            echo("<div style=\"text-align: center\"><br><br>");            
            if($pass[1]) {
                echo ("<div style=\"text-align: center\">The password  '".$pass[1]."'  is INVALID please correct the error.</div>");
            } else {
                echo ("<div style=\"text-align: center\">The file is password protect, please enter the password</div>");
            }
            $code = '<div style="text-align: center"><form method="post" action="'.$PHP_SELF.'">'.$nn;
            $code .= '<input type="text" name="downloadp"> <input type="submit" value="Send password">'.$nn;
            $code .= '<input type="hidden" name="cookie" value="'.$cookie.'">'.$nn;
            $code .= '<input type="hidden" name="passfile" value="true">'.$nn;
            $code .= '<input type="hidden" name="link" value="'.urlencode($link).'">'.$nn;
            $code .= '</form></div>';
			exit($code);            
        }
          
        if( strpos( $page ,"GetCaptcha('")!== false ){
		    $page = GetPage( "http://api.recaptcha.net/challenge?k=6LextQUAAAAAALlQv0DSHOYxqF3DftRZxA5yebEe", 0, 0, $Referer );
            is_present($page,"Expired session", "Expired session . Go to main page and reattempt", 0);
            
            $cook = GetCookies($page);
            $ch = cut_str ( $page ,"challenge : '" ,"'" );
			
			$page = GetPage( "http://api.recaptcha.net/image?c=".$ch, $cook, 0, $Referer );
            $headerend = strpos($page,"\r\n\r\n");
            $pass_img = substr($page,$headerend+4);
            $imgfile=$download_dir."mediafire_captcha.jpg";
            
            if (file_exists($imgfile)){ unlink($imgfile);} 
            @write_file($imgfile, $pass_img);
          
            $post['recaptcha_challenge_field'] = $ch;          
            $code = '<form method="post" action="'.$PHP_SELF.'">'.$nn;
            $code .= '<input type="hidden" name="step" value="1">'.$nn;
            $code .= '<input type="hidden" name="link" value="'.urlencode($link).'">'.$nn;
            $code .= '<input type="hidden" name="ch" value="'.$ch.'">'.$nn;
            $code .= '<input type="hidden" name="cookie" value="'.$cookie.'">'.$nn;
            $code .= 'Please enter : <img src="'.$imgfile.'?'.rand(1,10000).'"><br><br>'.$nn;
            $code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
            $code .= '</form>';
            echo ($code) ;
            die;
        }
		
		$string=DecoMfire($ev);		 
    
        $fid = cut_str (";".$string ,';' ,'(' );
        $snap = cut_str ( $page ,$fid ,'io.style' );
        $frid = cut_str ( $snap ,"io=document.getElementById('" ,"'" );
        $meta1= cut_str ( $string ,"('" ,"')" );
        $dat=explode("','",$meta1);
        
        $startLink="http://www.mediafire.com/dynamic/download.php?qk=".$dat[0]."&pk=".$dat[1]."&r=".$dat[2];
	    $page = GetPage( $startLink, $cookie, 0, $Referer );     
		
        if(strpos($page, "Click here to start download") !== false ){
		    if(strpos($page, "unescape") === false){
                $page = str_replace("Click here to start download", "\n", $page);
                preg_match_all("/\=(?:\s|)parent\.document\.getElementById\(\'(\w+)(?:[^<]+)(.+)(?:[^>])/",$page,$matches);
		        
                foreach($matches[1] as $idx => $idval){
                  if($idval==$frid){
                    $final_Link=Finalize($matches[2][$idx],$page);	
                    break;
                  }
                }
		        if(!isset($final_Link)) html_error("E101. Failed get Final Link", 0 );
            
		    } else {
			    
			    $unEscs = cut_str ( $page, 'case 15:' ,' break;' );
			    $unEscs = explode ( "'';", $unEscs );
				foreach($unEscs as $tmp){
				    $string=DecoMfire($tmp);
					if(preg_match("/\=(?:\s|)parent\.document\.getElementById\(\'(\w+)(?:[^<]+)(.+)(?:[^>])/", $string, $matches)){
					  if($matches[1] == $frid){
					    $final_Link=urldecode(Finalize($matches[2], $page ));
						break;
					  }
					}
				}
				if(!isset($final_Link)) html_error("E103. Failed get Final Link", 0 );		    
		    
		    }
		} else {
		
            preg_match_all("/;var.+?unescape\(.+?eval/",$page,$matches);
            foreach($matches[0] as $tmp){
             $string=DecoMfire($tmp);
             if( strpos( $string ,$frid)!== false ){
               $final_Link=Finalize($string,$page);
               break;
             }
            }
		    if(!isset($final_Link)) html_error("E100. Failed get Final Link", 0 );

        }		
     	
        $torep = array(" ", "+");
        $FileName = str_replace($torep, "_", basename($final_Link));		
		RedirectDownload( $final_Link, $FileName, $cookie, 0, $Referer );
		exit ();		
	}
	//////////////////////////////////////////////////////////// END FREE ///////////////////////////////////////////////////////////////


	function DownloadPremium($link)
	{
	//////////////////////////////////////////////////////////// START PREMIUM /////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////// END PREMIUM ///////////////////////////////////////////////////////////////
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
	
	/* - Decode Mfire - */
    function DecoMfire ($string){
      do
      {
        $snap = cut_str ( $string ,'unescape(' ,';eval' );
        $cont = cut_str ( $snap ,"i<" ,";" );
        if(!is_numeric($cont)){
          $cont = cut_str ( $string ,$cont."=" ,";" );  
        }
        $data = cut_str ( $snap.";" ,"'" ,";" );
        $el = cut_str ( $snap ,"charCodeAt(i)^" ,")" ); 
        $elev=explode("^",$el); 
        $udec = urldecode($data);
        for($i=0;$i<$cont;$i++){
          $op=substr($udec,$i,1);
          $op2=ord($op);
          foreach($elev as $ee){
            $op2=$op2^$ee;
          }
          //$op2=ord($op)^$el;
          $tmp.=chr($op2);
          $string=$tmp;
        }
        $tmp="";
      }
      while(strpos( $string ,"eval(")!== false);
      return $string;
    }
	
    /* - Finalize - */
    function Finalize ($string,$page){
     $vall = cut_str ( $string ,"'/' +" ,"+'g/'" );
     $hst =  cut_str ( $string ,"http://\" + '" ,"'" ); 
     $id =   cut_str ( $string ,"g/' + '" ,"'" ); 
     $fl =   cut_str ( $string ,$id."' + '/" ,"\"" ); 
     $temps = explode("+",$vall);
     foreach ($temps as $temp)
     {
         if (empty($temp)) continue;
         preg_match('/'.trim($temp).' ?= ?\'(.*?)\';/', $page, $temp2);
         $mpath1.= $temp2[1];
     }
     $Href = 'http://'.$hst.'/'.$mpath1.'g/'.$id.'/'.$fl;
     return $Href;
    }

/************mediafire.com*************\
 WRITTEN BY KAOX 11-mar-10
 Upate By Idx 30-mar-2010 Split By Func
 Upate By Idx 11-may-2010 Dcd.Lvl2
\************mediafire.com*************/
?>