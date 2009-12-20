<?php
if (!defined('RAPIDLEECH'))
  {require_once("404.php");exit;}

$link = $_POST["link"];
preg_match('%/get/(.+?)/%', $link, $id);
$id = $id[1];
$getlink = "http://www.storage.to/getlink/$id/";
$Url = parse_url($getlink);
$page = $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
preg_match('/Object\((.+?)\)/', $page, $json);
if (!$json[1])
{
	html_error("No Json string returned! Seems downloading system has changed. This plugin need to update.",0);
}
$json = str_replace("'", "\"", $json[1]);
$jsarray = json_decode($json, true);
if (!$jsarray)
{
	html_error("Cannot decode Json string!",0);
}

if ($jsarray['state'] == "failed")
{
	html_error("The download failed. Please try again later",0);
}
else if ($jsarray['state'] == "wait")
{
	html_error("Please wait. Free users may only download a few files per hour."); 
}
else if ($jsarray['state'] == "ok")
{
	insert_timer($jsarray['countdown'], "Countdown...."); 
}

$Url = parse_url($jsarray['link']);
$FileName = basename($jsarray['link']);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($Referer).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["idx"]) ? "&idx=".$_GET["idx"] : ""));
?>