/*
* ===========================
* Location    : /rsajax_ren.js
* Machine     :
* Property of : RL OLC 36B
*
* Re-generated   : 1812-09 // ddmm-yy
* ===========================
*/
var _rendebug=false;var objFName=new Object();var RenCarier=new Object();var cRow=-1;function fieldEnter(a,b){b=(b)?b:window.event;if(b.keyCode==13&&a.value!=""){ren(RenCarier[cRow]["obj"],'fn_'+cRow,RenCarier[cRow]["fid"]);return false}else{return true}}function strip_quotes(a){var b=/[\'\"\s\\\\\/\?\:\`\~\%\#\&\@\*\+\=\^\|\>\<\;]/g;return a.replace(b,"_").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function recode_escape(a){a=unescape(a);return a.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/&/g,"&amp;")}function parseFN(a){var b=new Array();var c=/.*href="(.+?)">/i;var d=/<a.*">(.+?)<\/a>/i;b[0]=d.exec(a);b[1]=c.exec(a);var e=new Array();e[0]=b[0][1];e[1]=b[1][1].substring(0,b[1][1].lastIndexOf("/")+1);return e}function ren(a,b,c){var e=b.replace("fn_","");var f;var g=d.getElementById(b);var h=/class="(.+?)">/i;var i=h.exec(a.innerHTML);if(i[1]=="rename_es"){if(cRow!=-1){restoreRow(cRow)}cRow=e;var j=parseFN(g.innerHTML);objFName[cRow]=new Object();objFName[cRow]["filename"]=j[0];objFName[cRow]["path"]=j[1];g.innerHTML='<input type="text" onkeypress="return fieldEnter(this, event)" name="fn_edit'+cRow+'" id="fn_edit'+cRow+'"  style="width:100%;padding-left:2px;" value="'+objFName[cRow]["filename"]+'"/><div style="display:none;" id="waitRename"></div><span id="resultRename" style="display:none;position:absolute;top:-1000px;"></span><input id="renhiddenval" style="display:none;" value="'+c+'"/>';f=d.getElementById("fn_edit"+cRow);f.focus();a.innerHTML='<div class="'+i[1]+"_save"+'"></div>';RenCarier[cRow]=new Object();RenCarier[cRow]["obj"]=a;RenCarier[cRow]["td"]=g;RenCarier[cRow]["fid"]=c;RenCarier[cRow]["newFN"]=f.value;RenCarier[cRow]["clsRen"]=i[1].replace("_save","")}else{f=d.getElementById("fn_edit"+cRow);var k=strip_quotes(f.value);var l=objFName[e]["filename"].replace(/\&amp;/g,"&");if(k!=l){f.style.disabled="true";objFName[cRow]["newfilename"]=(k);d.getElementById("renhiddenval").value+="|"+encodeBase64(encodeURI(k))+"|"+encodeBase64(encodeURI(l));doRename()}else{restoreRow(cRow)}}}function doRename(){ajxtmr=setTimeout("get('ajxRename','waitRename','resultRename','')",300);var a=setTimeout("renameDone()",700)}function renameDone(){var a=d.getElementById("resultRename");if(a.innerHTML==""){setTimeout("renameDone()",700)}else{var b=a.innerHTML;if(_rendebug){showDebug(b);}var c=parseInt(getBetween(b,'((suc))','((/suc))'));if(c==1){objFName[cRow]["filename"]=recode_escape(objFName[cRow]["newfilename"])}else{if(!_rendebug){alert(getBetween(b,'<msg>','</msg>'))}};restoreRow(cRow);a.style.display="none"}}function restoreRow(a){RenCarier[a]["td"].innerHTML='<b><a href="'+(objFName[a]["path"]+(objFName[a]["filename"]))+'">'+(objFName[a]["filename"])+'</a></b>';RenCarier[a]["obj"].innerHTML='<div class="'+RenCarier[a]["clsRen"]+'"></div>';cRow=-1}
function showDebug(txt){
 var pcon = document.getElementById('parentcontainer');
 var log = document.getElementById('log_rename');
 if(!log){
  var log = document.createElement('textarea');
  log.id = 'log_rename';
  log.setAttribute("style", "width:100%;height:120px;font-size:11px;");
  log.appendChild(document.createTextNode(txt)); 
  document.body.insertBefore(log, document.body.firstChild); 
 }else{ log.innerHTML = txt; }
}