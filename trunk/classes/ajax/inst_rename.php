<?php
if(!defined('RAPIDLEECH')){ 
 require_once("404.php"); exit; 
}

if(isset($_POST['ren'])) {
 @clearstatcache();
 define('PATH_SPLITTER', (strstr(ROOT_DIR, "\\") ? "\\" : "/"));
 $vpage = "index";
 require_once(LANG_DIR."language.$lang.inc.php");
 _create_list();
 //$charSet = 'charset=ISO-8859-1';
 $charSet = 'charset=UTF-8';
 header("Content-type: text/html; $charSet");
 
 global $list, $forbidden_filetypes, $optxt; 

 $postFName = (string)$_POST['ren'];
 $partFN = explode("|", $postFName, 3); 
  
 $smthExists = FALSE;
 foreach($list as $time => $record){
   if(rawurldecode(base64_decode($partFN[2]))==basename($record["name"])){
    $partFN[0]=$time; break;
   }
 }
 $file = $list[$partFN[0]];
 
 
 if(file_exists($file["name"])) {
  
  
  $oldName = $file["name"];
  $fname = str_replace("~","", iconv("UTF-8", "ISO-8859-1//TRANSLIT", rawurldecode(base64_decode($partFN[1])) ));
  $newName = dirname($oldName) . PATH_SPLITTER . $fname;
  
  /*
  $rest = substr($newName, -1);
  if(eregi('[^a-zA-Z0-9_]', $rest)){$alpnum = false;}else{$alpnum = true;}
  while(!$alpnum and (strlen($newName)>0)){
	$newName = substr($newName, 0, -1);
	$rest = substr($newName, -1);
	if(eregi('[^a-zA-Z0-9_]', $rest)){$alpnum = false;}else{$alpnum = true;}									
  }
  */
  $filetype = strrchr($newName, ".");                      
  if(is_array($forbidden_filetypes) && in_array(strtolower($filetype), $forbidden_filetypes)){
	// ERROR
	echo "((suc))0((/suc))<msg>The filetype ".$filetype." is forbidden to be renamed</msg>";
  }
  else{
	if(@rename($oldName, $newName)){
	  $smthExists = TRUE;
	  //echo $optxt['_file']." <b>".htmlspecialchars(basename($oldName))."</b> ".$optxt['rename_to']." <b>".htmlspecialchars(basename($newName))."</b>";
	  $list[$partFN[0]]["name"] = $newName;
	  echo "((suc))1((/suc))<msg>".$nemu.$optxt['_file']." ".(basename($oldName))." ".$optxt['rename_to']." <b>".(basename($newName))."</msg>";
	}
	else{
	  // ERROR
	  //echo $optxt['couldnt_rename_to']." <b>".$oldName."</b>!";
	  echo "((suc))0((/suc))<msg>The filetype ".$filetype." is forbidden to be renamed</msg>";
	}
  }
 }else{
   echo "((suc))0((/suc))<msg>".$optxt['_file']." [{$partFN[0]}] ".rawurldecode(base64_decode($partFN[2]))." ".$optxt['not_found']."\n".print_r($list,true)."</msg>";
 }
 if($smthExists){
	_create_list();
    if(!updateListInFile($list)){
	  echo "((suc))0((/suc))<msg>".$optxt['couldnt_upd']."</msg>";
    }
 }
 
}
?>