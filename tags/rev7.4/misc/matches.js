/*
* Script by   : Daniel_X_x
*
* ===========================
* Location    : misc/matches.js
* Machine     :
* Adapted for : RL OLC 36B
*
* Re-generated   : 3009-09 // ddmm-yy
* ===========================
*/

//script for Check text matches
//function flist_match_blink(id, times) { if (times <= 0) { return false; } var color_d = '#D49659'; var color_b = '#F5A249'; d.getElementById(id).bgColor = color_b; setTimeout('d.getElementById(\''+id+'\').bgColor=\''+color_d+'\';', 500); times = times - 1; setTimeout('flist_match_blink(\''+id+'\', '+times+');', 1000);}

var fl_rwhide=new Array(); // buffer global table object and selected rows array

function flist_match_hide() {
  var obj_container = d.getElementById('flist_match_hitems');
  var tmp = obj_container.style.display == 'none' ? 'block' : 'none';
  var obj_inp = obj_container.getElementsByTagName('input')[0];  
  obj_container.style.display = tmp;
  if(tmp!='none'){obj_inp.focus();}else{flist_toogle_rrestore();}
}
function flist_match() {
  var text = d.getElementById('flist_match_search').value;
  var rows = d.getElementById("intbl").tBodies[0].rows;
  if (text == '') { clear_chk_row(rows); return false; }
  var csensitive = d.getElementById('flist_match_ins').checked;
  var hdunmatch = d.getElementById('flist_match_hide').checked;  
  var tmp; var cell_text; var cell_matches; var text_regexp;
  text = '*' + text + '*'; while (text != text.replace(/\*\*/g, '*')) { text = text.replace(/\*\*/g, '*'); }
  text = text.replace(/\^|\$|\?|\+|\||\(|\{|\[|\\/g, '');
  text = text.replace(/\./g, '\\.'); text = text.replace(/\*/g, '.*?');
  text_regexp = new RegExp(text, csensitive ? '' : 'i');
  clear_chk_row(rows);

  //flist_match_blink(rows[i].id, (rows.length > 15 ? 10 : 5));  
  fl_rwhide = {"table":"", "baris":new Array()};	
  fl_rwhide["table"] = rows;
    
  for (var i=0; i<rows.length; i++) {
    if (rows[i].id == '' || rows[i].id == null) { rows[i].id = 'table_filelist_tbody_trs' + i; }
    tmp = rows[i].cells[1].getElementsByTagName('*');
    cell_text = (tmp.length > 0) ? tmp[0].innerHTML : rows[i].cells[1].innerHTML;
    cell_matches = text_regexp.exec(cell_text);
	if (cell_matches != null) {
      tmp = rows[i].cells[0].getElementsByTagName('input');
      for (var j=0; j<tmp.length; j++) {
        if (tmp[j].type == 'checkbox') {
          tmp[j].checked = true;
          d.getElementById(rows[i].id).className = 'rowlist_checked';
          break;
        }
      }
    }else{
	  if(hdunmatch) {
	   d.getElementById(rows[i].id).style.display = 'none'; 
	   fl_rwhide["baris"].push(i)
	  }
	}
  }
  return false;
}
function clear_chk_row(obj){
  var tmp;
  for (var i=0; i<obj.length; i++) {
    tmp = obj[i].cells[0].getElementsByTagName('input');
    for (var j=0; j<tmp.length; j++) {
      if (tmp[j].type == 'checkbox') { tmp[j].checked = false; break; }	  
    }
	d.getElementById(obj[i].id).style.display = '';
	d.getElementById(obj[i].id).className = 'rowlist';
  }
}
function flist_toogle_rrestore(){
 if(fl_rwhide["baris"] && fl_rwhide["baris"].length>0){flist_restore_row(fl_rwhide); fl_rwhide=new Array();}
}
function flist_restore_row(ar_rw){ 
 for(var i=0; i<ar_rw["baris"].length; i++){
  d.getElementById(ar_rw["table"][ar_rw["baris"][i]].id).style.display = '';
 } 
}
/*Mod by:~Idx*/