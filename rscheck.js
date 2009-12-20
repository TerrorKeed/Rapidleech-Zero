/*
* ===========================
* Location    : /rscheck.js
* Machine     :
* Property of : RL OLC 36B
*
* Re-generated   : 0210-09 // ddmm-yy
* ===========================
*/
var http_req_rs = false, d = document;
var section, rslinks, startFrm = 0;
var array_links=new Array();
var arResp=new Array();

function makePOSTReqRs(url, parameters) {
	  	if (typeof XMLHttpRequest == 'undefined') {
			objects = Array('Microsoft.XmlHttp','MSXML2.XmlHttp','MSXML2.XmlHttp.3.0','MSXML2.XmlHttp.4.0','MSXML2.XmlHttp.5.0'	);
			for (i = 0; i < objects.length; i++) {
				try { http_req_rs = new ActiveXObject(objects[i]);	} catch (e) {}
			}
		} else {
			http_req_rs = new XMLHttpRequest();
		}
	  if (!http_req_rs) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      http_req_rs.open('POST', url, true);
      http_req_rs.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_req_rs.setRequestHeader("Content-length", parameters.length);
      http_req_rs.setRequestHeader("Connection", "close");
	  http_req_rs.onreadystatechange = function(){handleResults(http_req_rs)};
      http_req_rs.send(parameters);
}
 
   
function handleResults (xhr) {
	if (xhr.readyState == 4) {
	  if (xhr.status == 200) {
		if (xhr.responseText == 'url.blank') {
			d.body.innerHTML = 'Enter a valid URL';
		} else {
			var resp = xhr.responseText, j=0, txrest;
			var aneh = unescape("%EF%BB%BF");
			//alert(resp);
			rtime = getBetween(resp,'<timerequire>','</timerequire>');
			resp = getBetween(resp,'<result>','</result>');
			resp = resp.replace(aneh,"");
			arResp=resp.split("\n");
			if(undefined!=arResp){
			 if(section=='audl'){ // Fired from audl
			  for(var i=startFrm; i<array_links.length; i++)
			  {
				d.getElementById('status'+i).innerHTML=arResp[j];
				d.getElementById('statusTitle').innerHTML="&nbsp;<b><a href='javascript:void(0);' onclick='reLink(); loadHandler();'>Status</a></b>&nbsp;";
				j++;
			  }
			  d.getElementById('btnaddlinks').disabled=false;
			 }else {
			  txrest = '<div id="listlink">';cntunknow = 0; cntgood = 0;
			  linkonly = d.getElementById('d').checked; 
			  for(var i=startFrm; i<array_links.length; i++){
			    good = (arResp[j].indexOf("good link")!=-1);if(good){cntgood++;}
			    unknow = (arResp[j].indexOf("unknown link")!=-1); if(unknow){cntunknow++;}
			    txrest += '<a class='+(good?'"g"':'"r"')+' href="'+array_links[i]+'" target="_blank">'+array_links[i]+'</a>'+(linkonly?'':'&nbsp;'+arResp[j])+'<br>\n';
			    j++;
			  }
			  txrest +='</div>';
			  spc = '&nbsp;'; lnk = 'Link:&nbsp;';
			  
			  txrest +='<br><hr><center><small style="color:#fff"><b class="o">' + array_links.length +'</b> '+rtime+'<br>\n';
			  txrest +='Dead&nbsp;'+lnk+'<b class="r">' + eval(array_links.length-cntgood-cntunknow) +'</b>\n';
			  txrest +=spc+spc+'&#8212;'+spc+spc+'Good&nbsp;'+lnk+'<b class="g">' + eval(cntgood) +'</b>\n';
			  txrest +=spc+spc+'&#8212;'+spc+spc+'Unknown&nbsp;'+lnk+'<b class="y">' + eval(cntunknow) +'</b></small></center>\n';
			  d.getElementById('linkresult').innerHTML =txrest;
			  disableIt(0);
			 }
			 if(section=='audl'){
			  d.getElementById('btnaddlinks').disabled=false;			 
			 }else{
			  disableIt(0);
			 }			 
			}
		}
		
	  }
	  else {
        alert('There was a problem with the request.');
      }
	}
}

function chklinkOnly(flag){
	var txrest='';
	try{d_target = d.getElementById('listlink'); d_target = (d_target && arResp.length); }
	   catch(e){}
	if(d_target==null){return false;}
	for(var i=0; i<array_links.length; i++){
	  good = (arResp[i].indexOf("good link")!=-1);
	  txrest += '<a class='+(good?'"g"':'"r"')+' href="'+array_links[i]+'" target="_blank">'+array_links[i]+'</a>'+(flag?'':'&nbsp;'+arResp[i])+'<br>\n';
	}
	d.getElementById('listlink').innerHTML = txrest;
}

function getBetween(txsrc,lh,rh) {
	var poslh = txsrc.indexOf(lh);
	var posrh = txsrc.indexOf(rh);
	var pure = txsrc.substring(eval(poslh+lh.length), posrh);
	return pure;
}

function strrev(strin){
	var text=strin,reversed = "";
	for (i=text.length; i>=0; i--) {
	 reversed = reversed + text.charAt(i);
	}
	hsl = encodeBase64(reversed);
	return hsl;
}

function jlnkfilter(jlnk)
  {
	var sbuf=jlnk, charsuspect =  new Array("*");
	var rsabs = "";
	srcL:for(var i=0; i<charsuspect.length; i++)
	{
	  if(sbuf.indexOf(charsuspect[i])!=-1){
	   sbuf=sbuf.replace(charsuspect[i],rsabs);
	   break srcL;
	  }
	}
	return sbuf;
  }

function LnkChkHandler(links, lsisa) {
	var regexRN = new RegExp('\r\n',"g");
	var regexN = new RegExp('\n',"g");
	var http = /((^http)*).\:\/\/.([a-zA-Z0-9\.])/; 
	dlinks = links;
	dlinks = dlinks.replace(regexRN, "\n");
	arlinks = dlinks.split(regexN);	dlinks = '';
	var lnk_width = (lsisa > 0 ? lsisa : arlinks.length);
	for(i=0; i<lnk_width; i++)
	{ if(arlinks[i].substring(0,7)!='ht'+'tp://'){arlinks[i]='ht'+'tp://'+arlinks[i];}
	  if(http.test(arlinks[i]))
		{  dlinks += jlnkfilter(arlinks[i])+';'; }
	}
	dlinks = dlinks.substring(0, dlinks.length-1);
	arlinks = dlinks.split(";");
	return arlinks;
}

// Link Check
function disableIt(mode){
	var dval = new Array;
	var dvalOn = new Array(true,'redtxtareadisable',true,'');  //( readonly, buttondisabled, displayprogres )
	var dvalOff = new Array(false,'redtxtarea',false,'none');  //( readonly, buttondisabled, displayprogres )
	if(mode==1){
	 dval = dvalOn;
	}else{dval = dvalOff;}
	d.getElementById('links').readOnly = dval[0];
	d.getElementById('links').className = dval[1];
	d.getElementById('btnlnkchk').disabled = dval[2];
	d.getElementById('lnkwait').style.display = dval[3];
}

function checkLink() {
  var retlink = Array();
  disableIt(1);
  links = d.getElementById('links').value;
  retlink = LnkChkHandler(links);
  if(retlink.length!=0){
	 getResults(dlinks, retlink, 'index');
	}
	else{
		alert('Bad Link URL');
		disableIt(0);
	}	
}

// MAIN Function
function getResults (sb_link, ar_link, whr) {
	var _curl = ''; section = whr;
	try{ _curl += (d.getElementById('lcurl').checked==true ? '|_curl=on;' : '');}catch(e){}
	var posturl = '&url=' + _curl + '|_section=' + section + ';' + '|_url=' +  strrev(unescape(sb_link));
	array_links = ar_link; 
	makePOSTReqRs('ajax_lnk.php', posturl);
}


