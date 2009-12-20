<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page,"This file has been deleted");
	is_present($page,"The one-hour limit has been reached");
	
	if(preg_match_all('/Set-Cookie: *(.+);/', $page, $cook)){
		$cookie = implode(';', $cook[1]);
	}else{
		exit('<center>Cookie not found.</center>');
	}

	if(preg_match('/<a.+?(http.+?data.+?)">/', $page, $loc)){
		$newurl = $loc[1];
	}
	$Url = parse_url($newurl);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/ocation: (.+)/', $page, $location)){
		$link = trim($location[1]);
		$Url = parse_url($link);
	}
	
	$FileName = basename($Url["path"]);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$_POST["link2"].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["idx"]) ? "&idx=".$_GET["idx"] : ""));
?>