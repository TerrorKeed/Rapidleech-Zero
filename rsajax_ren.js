/*
* ===========================
* Location    : /rsajax_ren.js
* Machine     :
* Property of : RL OLC 36B
*
* Re-generated   : 1812-09 // ddmm-yy
* ===========================
*/
// AJAX RENAME Proc
var objFName = new Object();
var RenCarier = new Object();
var cRow=-1;

//event pencet Enter
function fieldEnter(caller, evt) {
 evt = (evt) ? evt : window.event;
 if (evt.keyCode == 13 && caller.value!="") {
   ren(RenCarier[cRow]["obj"], 'fn_'+cRow, RenCarier[cRow]["fid"]);
   return false;
 } else {
   return true;
 }
}

//filter strip quotes
function strip_quotes(text){
  var re = /[\'\"\s\\\\\/\?\:\`\~\%\#\&\@\*\+\=\^\|\>\<\;]/g;
  return text.replace(re, "_").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

//filter alphanumeric value only
function recode_escape(text){
  text = unescape(text);
  return text.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/&/g, "&amp;");
}

//dapetin path dan filename dr td.innerhtml
function parseFN(text){
 var dumyret = new Array();
 var regexPath = /.*href="(.+?)">/i;
 var regexFN = /<a.*">(.+?)<\/a>/i;
 dumyret[0] = regexFN.exec(text);
 dumyret[1] = regexPath.exec(text);
 var hasil = new Array();
 hasil[0] = dumyret[0][1];
 hasil[1] = dumyret[1][1].substring(0, dumyret[1][1].lastIndexOf("/") + 1); 
 return hasil; 
}


function ren(obj, fnrowID, fileid){
 var row_id = fnrowID.replace("fn_",""); var inp_fn;
 var td_fn = d.getElementById(fnrowID); 
 var rgexClsName = /class="(.+?)">/i; 
 var clsName = rgexClsName.exec(obj.innerHTML);
 if(clsName[1]=="rename_es"){
  if(cRow != -1){restoreRow(cRow);}
  cRow = row_id;
  var fnvalue = parseFN(td_fn.innerHTML);
  objFName[cRow] = new Object();
  objFName[cRow]["filename"] = fnvalue[0];
  objFName[cRow]["path"] = fnvalue[1];
  
  td_fn.innerHTML = '<input type="text" onkeypress="return fieldEnter(this, event)" name="fn_edit'+cRow+'" id="fn_edit'+cRow+'"  style="width:100%;padding-left:2px;" value="'+objFName[cRow]["filename"]+ '"/><div style="display:none;" id="waitRename"></div><span id="resultRename" style="display:none;position:absolute;left:-1000px;"></span><input id="renhiddenval" style="display:none;" value="' + fileid +'"/>';   
  inp_fn = d.getElementById("fn_edit"+cRow);
  inp_fn.focus();
  obj.innerHTML = '<div class="' + clsName[1] + "_save" + '"></div>';
  RenCarier[cRow] = new Object();
  RenCarier[cRow]["obj"] = obj;
  RenCarier[cRow]["td"] = td_fn;
  RenCarier[cRow]["fid"] = fileid;
  RenCarier[cRow]["newFN"] = inp_fn.value;
  RenCarier[cRow]["clsRen"] = clsName[1].replace("_save","");
  
 }else{
  inp_fn = d.getElementById("fn_edit"+cRow);  
  // CHECK & pregmatch filter new filename 
  var inpFiltered = strip_quotes (inp_fn.value);
  var oldFilename = objFName[row_id]["filename"].replace(/\&amp;/g, "&");
  if(inpFiltered != oldFilename){
   inp_fn.style.disabled="true";
   objFName[cRow]["newfilename"] = (inpFiltered);
   d.getElementById("renhiddenval").value+= "|" + encodeBase64(encodeURI(inpFiltered)) + "|" + encodeBase64(encodeURI(oldFilename));
    // DO Saving Procedure here...
   doRename();
  }else{
   // Nothin change, restore initial state
   restoreRow(cRow);
  }
 }
}

function doRename(){
  ajxtmr = setTimeout("get('ajxRename','waitRename','resultRename','')",300);
  var ajxchkRen = setTimeout("renameDone()", 700);
  
}
function renameDone(){
 var result = d.getElementById("resultRename");
 if(result.innerHTML==""){
  setTimeout("renameDone()", 700);
 }else{
  var finRes = result.innerHTML;
  var success = parseInt(getBetween(finRes,'((suc))','((/suc))'));
  if(success==1) {
     objFName[cRow]["filename"] = recode_escape(objFName[cRow]["newfilename"]);
  }else{ alert(getBetween(finRes,'<msg>','</msg>'));  };
  restoreRow(cRow);
  result.style.display="none";
 }
}

function restoreRow(lrow){
  RenCarier[lrow]["td"].innerHTML = '<b><a href="' + (objFName[lrow]["path"] + (objFName[lrow]["filename"])) + '">' + (objFName[lrow]["filename"]) +'</a></b>';
  RenCarier[lrow]["obj"].innerHTML = '<div class="' + RenCarier[lrow]["clsRen"] + '"></div>';
  cRow = -1;
}
// -END-AJAX RENAME Proc