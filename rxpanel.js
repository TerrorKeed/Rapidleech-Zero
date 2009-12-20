/*
* ===========================
* Location    : /rxpanel.js
* Machine     :
* Property of : RL OLC 36B
*
* Re-generated   : 1609-09 // ddmm-yy
* ===========================
*/
var mydate = new Date();
var cklive = eval(1000 * 3600 * 12); /* 12 hours */

function GenR(meth, txusr, txpass, stspan, dkey, hideres)
{
 clearTimeout(timer);
 method = meth; 
 hdret = hideres;
 stRet = stspan;
 
 if(meth!="singlemd5")
 {
  var artext = new Array();
  artext[0] = d.getElementById(txusr).value;
  artext[1] = d.getElementById(txpass).value;
  if(dkey!='0') {artext[2] = d.getElementById(dkey).value;}
 }
 else
 {
	txplain = dkey;
 }
 
 if(meth!="singlemd5"){
 if((artext[0]!="")&&(artext[1]!=""))
 {
	if(meth=="rkey")
	{
	txplain = artext[0].replace(/^\s+|\s+$/g, '') +':::'+artext[1].replace(/^\s+|\s+$/g, '') +':::'+artext[2];
	}
	else 
	{
	txplain = artext[0]+":::"+artext[1];
	}
	d.getElementById(stspan).style.display = '';
	d.getElementById(stspan).innerHTML = imgloading;
	d.getElementById(stspan).style.color = 'yellow';
	d.getElementById('saveAll').disabled = true;
 }
 else
 {
  d.getElementById(stspan).innerHTML="";
  d.getElementById(hdret).value="";
  return false;
 }
}
 else
 {
	d.getElementById(stspan).innerHTML = imgloading;
 }
 
}

function getResults (plaintxt,rndkey) {
 var param='', ky='', lnk;
 switch(method)
 {
	case 'rkey':
		param = '&rs=';
		ky = '&key='+rndkey;
		break;
	case 'md5':
		param = '&md5=';
		break;
	default:
		param = '&sg_md5=';
		break;
 }
  lnk = 'hash.php?' + param + plaintxt + ky;
  xml.open('get', lnk);
  xml.onreadystatechange = handleResults;
  xml.send(null);
}
function loadHandler(txt)
{
  var txttoload, dkey='';
  if(method == 'rkey')
  {
   var txtpart = txt.split(':::');
   txttoload = txtpart[0] +':::'+ txtpart[1];
   dkey = txtpart[2];
  } 
  else 
  {txttoload = txt;}
  
  getResults(txttoload,dkey);
}

function xmlObject () {
 if (typeof XMLHttpRequest == 'undefined') {
  objects = Array(
   'Microsoft.XmlHttp',
   'MSXML2.XmlHttp',
   'MSXML2.XmlHttp.3.0',
   'MSXML2.XmlHttp.4.0',
   'MSXML2.XmlHttp.5.0'
  );
  for (i = 0; i < objects.length; i++) {
   try {
    return new ActiveXObject(objects[i]);
   } catch (e) {}
  }
 } else {
  return new XMLHttpRequest();
 }
}
function handleResults () {
 if (xml.readyState == 4) {
  var ddst = d, ret, dsthide=d;
  ddst = ddst.getElementById(stRet);
  ret = xml.responseText;
  if (ret == 'null') {
   ddst.value = 'Enter valid text..!';
   ddst.style.color = 'red';
  }
  else 
  {
	if(method!='singlemd5')
	{
	dsthide = dsthide.getElementById(hdret);
	dsthide.value = getBetween(ret,'_hash_','_/hash_');
	ddst.innerHTML = '<small class="s">Encrypted..!</small>';
	ddst.style.color = 'aqua';	
	d.getElementById('saveAll').disabled = false;
	}
	else
	{
	ddst.innerHTML = getBetween(ret,'_hash_','_/hash_');
	}
  }
 }
}


function getBetween(txsrc,lh,rh) {
	var poslh = txsrc.indexOf(lh);
	var posrh = txsrc.indexOf(rh);
	var pure = txsrc.substring(eval(poslh+lh.length), posrh);
	return pure;
}

function doCancel(uTx,pTx,stTx,hslTx,btnCh,inpBx) {
	d.getElementById(uTx).value = '';
	d.getElementById(pTx).value = '';
	d.getElementById(stTx).innerHTML = '';
	d.getElementById(hslTx).value = '';
	d.getElementById(inpBx).style.display = 'none';
	d.getElementById(btnCh).style.display = '';	
}

function clk(id,id2,id3,chk){
 parent_check = id.checked;
 var displ=parent_check?'':'none';
 var displ2=parent_check?'none':'';
 d.getElementById(id2).style.display=displ;
 if(chk==true) { d.getElementById(id3).innerHTML = ''; } 
 d.getElementById(id3).style.display=displ2;
 mydate.setTime(eval(mydate.getTime() + cklive));
 // to mark this acc is going to be filled
 d.cookie=id.id + "=" + "0; path="+dirpath+"; expires=" + mydate.toGMTString() + "; ";
}

function trclick(tr){
 var id = tr.id, view, hidetrcookie, plusmin;
 var bd = d.getElementById('bd_'+tr.id);
 var nod = d.getElementById('nod_'+tr.id);
 view = bd.style.display;
 bd.style.display = (view==''?'none':'');
 hidetrcookie = (view==''?'1':'0');
 plusmin = (view==''?'plus':'min');
 nod.innerHTML = '<input type="image" src="'+imgpath + plusmin + '.png" onclick="" style="vertical-align:middle;">&nbsp;';
 d.cookie = id + "=" + hidetrcookie + ";path="+dirpath+"; expires=" + escape(mydate.toGMTString() + ";");
}

//==========================
// Cookie tools
function deleteCookie(name, path, domain)
  {
  if (getCookie(name))
    {	
	mydate.setTime(mydate.getTime() - 1);
    d.cookie = name + "=" + ((path) ? "; path=" + path : "") +((domain) ? "; domain=" + domain : "") + "; expires=" + mydate.toGMTString() + "; ";
    }
  }
  
function getCookie(name)
  {
  var dc = d.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1)
    {
    begin = dc.indexOf(prefix);
    if (begin != 0)
      { return null; }
    }
  else
    { begin += 2; }
  var end = d.cookie.indexOf(";", begin);
  if (end == -1)
    { end = dc.length; }
  return unescape(dc.substring(begin + prefix.length, end));
}


function addElement(idparent, id){
  var myid = eval(id+1);  
  var dparent = d.getElementById(idparent);
  var newdiv = d.createElement('div');
  var newinput = new Array();
   newinput[0] = d.createElement('input');
   newinput[1] = d.createElement('input');
  var newspan = d.createElement('span');
  newdiv.setAttribute('id','addition'+myid);
  newdiv.appendChild(d.createTextNode('user: '));
   newinput[0].setAttribute('type','text');
   newinput[0].setAttribute('name','rpl'+myid);
   newinput[0].setAttribute('id','rpl'+myid);
  newdiv.appendChild(newinput[0]);
  newdiv.appendChild(d.createTextNode('\npass: '));
   newinput[1].setAttribute('type','password');
   newinput[1].setAttribute('name','rppl'+myid);
   newinput[1].setAttribute('id','rppl'+myid);
  newdiv.appendChild(newinput[1]);
  newspan.setAttribute('id','buttonplus'+myid);
  newspan.innerHTML = '&nbsp;<input id="'+myid+'" type="image" src="'+imgpath+'plus.png" onclick="addElement(\''+idparent+'\', '+myid+');return false;" style="vertical-align:middle;">&nbsp;';
  newspan.innerHTML += '<input id="'+myid+'" type="image" src="'+imgpath+'min.png" onclick="remElement(\''+idparent+'\', \'addition'+myid+'\');return false;" style="vertical-align:middle;">';
  newdiv.appendChild(newspan);
  dparent.appendChild(newdiv);
  d.getElementById('buttonplus'+id).style.display='none';
  mydate.setTime(mydate.getTime() + cklive);
  d.cookie = "rs_com_premix = "+myid+"; path="+dirpath+"; expires=" + mydate.toGMTString() + "; ";
  return false;
}

function remElement(parentDiv, childDiv){
	var idx = childDiv.replace(/addition/, "");
	var RE = /^-{0,1}\d*\.{0,1}\d+$/;
	idx = (RE.test(idx) ? eval(idx-1) : '');
     if (childDiv == parentDiv) {
          alert("The parent div cannot be removed.");
     }
     else if (d.getElementById(childDiv)) {
          var child = d.getElementById(childDiv);
          var parent = d.getElementById(parentDiv);
          parent.removeChild(child);
 	      d.getElementById('buttonplus'+idx).style.display='';
		  mydate.setTime(mydate.getTime() + cklive);
		  d.cookie = "rs_com_premix = "+idx+"; path="+dirpath+"; expires=" + mydate.toGMTString() + "; ";
     }
     else {
          alert("Child div has already been removed or does not exist.");
          return false;
     }
}

