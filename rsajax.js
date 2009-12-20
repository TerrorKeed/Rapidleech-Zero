/*
* ===========================
* Location    : /rsajax.js
* Machine     :
* Property of : RL OLC 36B
*
* Re-generated   : 1812-09 // ddmm-yy
* ===========================
*/
var xhttp = new Array();
var myacc = new Object();
myacc['acc'] = new Array();
myacc.j = 0;
myacc.cnt_valid = 0;
myacc.unsec = 0;
myacc.res = '';
myacc.valid = '';
myacc.longacc = '';
myacc.shortacc = '';
myacc.multicheck = false;
myacc.index_status = false;
myacc.head = '';
myacc.param = '';
myacc.idbuttonrschk = 'btnchkacc';
myacc.divwaitrschk = 'waiting';
myacc.chead = '<table cellspacing=\"0\" cellpadding=\"2\" class=\"tbl_acc\"><tr id="tr_acc_header"><th>Username</th> <th>Password</th><th title=\"Free RapidPoints\">fPoints</th><th title=\"Premium RapidPoints\">pPoints</th><th title=\"Refferer RapidPoints\">rfPoints</th><th title=\"TrafficShare left\">Trafics</th><th title=\"Expiration date\">Expired</th><th title=\"Traffic Left\">Trafic&nbsp;left</th><th title=\"Anti Hack\">Security</th><th>Email</th></tr>';
var d=document;

var _debug2 = false; // debug: alert(bufFile); show innerHtml process result
var _debug = false; // debug direct xml result

//============

function AJAXInteraction(url, callback, dvwait, dvresult, sendmyacc, req_resp) {
    var req = init();	
	req.onreadystatechange = processRequest;

    function init() {
		if (typeof XMLHttpRequest == 'undefined') {
			objects = Array('Microsoft.XmlHttp','MSXML2.XmlHttp','MSXML2.XmlHttp.3.0','MSXML2.XmlHttp.4.0','MSXML2.XmlHttp.5.0'	);
			for (i = 0; i < objects.length; i++) {
				try { return new ActiveXObject(objects[i]);	} catch (e) {}
			}
		} else {
			return new XMLHttpRequest();
		}
	};
    
    function processRequest () {
	  var stloading_acc = ''; var response;
	  
      if (req.readyState == 4) {
        if (req.status == 200) {
          if (callback) {
		   //response = (req_resp!='xml' && _debug==false ? req.responseText : req.responseXML);
		   response = (_debug==true ? req.responseText : (req_resp!='xml' ? req.responseText : req.responseXML));		   
		   callback(response,dvwait,dvresult,sendmyacc,req_resp);		   
		  }
		  
        }
	  }else{
		if(sendmyacc.multicheck>0){
		  wprogress=parseInt((sendmyacc.j+1)*100/sendmyacc.acc.length);
		  stloading_acc="<b class='g'>"+sendmyacc.acc[sendmyacc.j]+"</b><table align='center'><tr><td><div style='border:#BBBBBB 1px solid; width:300px; height:10px;'><div id='progress' style='background-color:#18f20d; margin:1px; width:"+wprogress+"%; height:8px;'></div></div></td></tr></table><span id='nfoacc'>"+"#[<b class='g'>"+eval(sendmyacc.j+1)+"</b>] of "+sendmyacc.acc.length+"</span>";}
		  d.getElementById(dvwait).innerHTML=stloading_acc+'&nbsp;<span style="background-color:#800000;">&nbsp;<img alt="loading" src="misc/fbload.gif">&nbsp;&nbsp;<b class="o">Please&nbsp;wait..</b>&nbsp;</span>';
	  }
    };

    this.doGet = function() {
      req.open("GET", url, true);
      req.send(null);
    };
    
    this.doPost = function(body) {
      req.open("POST", url, true);
      req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      req.setRequestHeader("Content-length",body.length);
      req.send(body);
    };
};
//====end static XHR lib

//-------------------
  // Function inside function to handle request result
var fcallback = function(ret,retdvwait,retdvresult,_myacc, _response) {
	var result = ret;
	
	if(_response=='xml'){createTable(result,retdvwait,retdvresult); return;}
	//alert(result);
	_myacc.res=_myacc.res+result;
	if(_myacc.multicheck&&result!=''){
		d_acc=getBetween(result,'<<acc>>','<</acc>>');
		_myacc.longacc+=d_acc+'\n';
		_myacc.cnt_valid+=1;
		startcode=_myacc.res.indexOf('<!end>');
		if(startcode!=-1){_myacc.res=_myacc.res.substr(0,startcode)}
		if(result.indexOf('<blink>OFF</blink>')!=-1){_myacc.unsec+=1}
		_myacc.valid=_myacc.valid+_myacc.acc[_myacc.j]+'\n';
	}
	if(_myacc.multicheck){
		_myacc.head=_myacc.chead; _myacc.index_status=false;
		_myacc.shortacc=cleanacc(_myacc.valid);
		txtarea = '</table><b class="g">Premium ACC</b>&nbsp;Formatted Output:<br><textarea id="validacc" class="redtxtarea" style="width: 100%; height: 140px">'+_myacc.longacc+'</textarea>';
		txtarea+='<table style="width: 100%;"><tr><td width="50%" align="left"><small>Checked acc: <b class="o">'+(_myacc.j+1)+'</b><br>Security OFF: <b class="y"><blink>'+_myacc.unsec+'</blink></b><br>Dead acc: <b class="r">'+((_myacc.j+1)-_myacc.cnt_valid)+'</b></small></td><td align="right" valign="top"><label><input type="checkbox" id="nodet" onclick="NoDetail(myacc.shortacc,myacc.longacc);">No Detail</label></td></tr></table>';
		}
	if(_myacc.res!='' && _myacc.multicheck){ // Partialy show result -multi acc
	  d.getElementById(retdvresult).innerHTML= _myacc.head + _myacc.res + txtarea;
  
	}
	if(_myacc.index_status==true){
	  try{	// try this to avoid other than index page
		d.getElementById(_myacc.idbuttonrschk).disabled=false;
		d.getElementById(_myacc.divwaitrschk).innerHTML='';
		d.getElementById(_myacc.divwaitrschk).style.display='none';
	  }catch(e){}
	}
	if(!_myacc.multicheck || _myacc.j>=(_myacc.acc.length-1)){ // Final show result
		d.getElementById(retdvresult).style.display='';
		if(_myacc.multicheck){
		  d.getElementById(_myacc.idbuttonrschk).disabled=false;
		  if(_myacc.res==''){d.getElementById(retdvresult).innerHTML='<center><small class="y">no&nbsp;one&nbsp;acc&nbsp;available</small></center>';}
		  try{d.getElementById('refresh').style.display='';}catch(e){}
		}else{
		  d.getElementById(retdvresult).innerHTML=result; //try{_gLoad();}catch(e){}
		  result = ''; _myacc.res = '';
		}
		if(retdvwait!=retdvresult){ 
		  d.getElementById(retdvwait).innerHTML = '';
		  d.getElementById(retdvwait).style.display = 'none';
		}
		_myacc.head='';txtarea='';
		clearTimeout(document.ajxtmr); 
	}else{ // Check next acc
	  _myacc.j+=1;
	  callAjax('ajax_main.php', retdvwait, retdvresult, _myacc, _response);
	}
	
}; // end inner function handle result
  //--------------------------------------

function callAjax(url, dvwait, dvresult, objmyacc, rsp_meth) {
  var resp = (rsp_meth==undefined ? 'text' : rsp_meth);
  ai = null;
  ai = new AJAXInteraction(url, fcallback, dvwait, dvresult, objmyacc, resp);
  if(objmyacc.multicheck){
	ai.doPost(objmyacc.param+encodeURI(objmyacc.acc[objmyacc.j]));
  }else{
	ai.doPost(objmyacc.param);
  }
};

//===========================
// server files Table Handler 
//===========================

function createHeader(retXML, resultNode){
 var div1= d.createElement("div"); div1.id = "parentcontainer";  
 var div2= d.createElement("div"); 
 div2.setAttribute("style", "text-align:center;margin-top:-10px;");
 var a1=d.createElement("a");
 a1.setAttribute("href","javascript:void(0)");
 a1.setAttribute("onclick","d=document; dcnt=d.getElementById(\'flcontainer\'); ddcnt=d.getElementById(\'divcontainer\'); if(dcnt) {dcnt.style.overflow=\'visible\';d.getElementById(\'parentcontainer\').innerHTML=ddcnt.innerHTML;};try{gosetfacebook();waitandload();}catch(e){};");
 
 var sp1 = d.createElement("span");
 sp1.setAttribute("id","release");
 sp1.setAttribute("title","release me..");
 sp1.appendChild(d.createTextNode("release"));
 a1.appendChild(sp1);
 div2.appendChild(a1);
 div1.appendChild(div2);
 
 div2= d.createElement("div"); 
 div2.setAttribute("id", "divcontainer");
 //div2.setAttribute("style", "overflow: auto; width: 100%;");
 var tbL = d.createElement("table"); 
 tbL.setAttribute("id", "intbl");
 tbL.setAttribute("class", "filelist sortable");
 tbL.setAttribute("cellpadding", "2");
 tbL.setAttribute("cellspacing", "1");
 var thead1 = d.createElement("thead"); 
 thead1.setAttribute("id", "flheader");
 
 //thead1.setAttribute("style", "width: 100%; white-space: nowrap;");
 
 tr = d.createElement("tr"); 
 tr.setAttribute("class", "filelist_title");
 tr.setAttribute("align", "center");
 tr.setAttribute("valign", "bottom");

 //itung total files
 var n = retXML.getElementsByTagName('totalfiles').item(0);
 n = n.childNodes.item(0).data;
 
 //query header
 var headerTbl = new Object(); var b1;
 var tagkolom = retXML.getElementsByTagName('columns').item(0);
 var ajaxDel = '';
 ajaxDel = retXML.getElementsByTagName('ajaxdelete').item(0); 
 ajaxDel = (ajaxDel ? ajaxDel.childNodes.item(0).data : 0);
 for (var iNode = 0; iNode < tagkolom.childNodes.length; iNode++) {
  var nodekol = tagkolom.childNodes.item(iNode);
  for (var i = 0; i < nodekol.childNodes.length; i++) {
   var sibl = nodekol.childNodes.item(i);
   var hdCntfile=''; var fieldTxt='';
   td = d.createElement("td");   
   if (nodekol.tagName == "filename") {
      var optTag = retXML.getElementsByTagName('option').item(0);
      if (optTag != null) {
        var select1 = d.createElement("select");        
        select1.id = "sel_actionfile";
        select1.setAttribute("name", "act");
        select1.setAttribute("style", "text-align:left; float:left;");
        select1.setAttribute("onChange", "javascript:void(document.flist.submit());");
        var actTag = retXML.getElementsByTagName('actions').item(0);
        for (var x = 0; x < actTag.childNodes.length; x++) {
		  var siblAct = actTag.childNodes.item(x);
		  for (var j = 0; j < siblAct.childNodes.length; j++) {
            var chAct = siblAct.childNodes.item(j);
            act_ret = chAct.data.split(":", 3);
			var option1 = d.createElement("option");
            if (act_ret[0] != "-1") {
              option1.setAttribute("value", act_ret[0])
            }
            fieldTxt = d.createTextNode(act_ret[2]);
            option1.appendChild(fieldTxt);
            select1.appendChild(option1);
		  }
		}
		td.appendChild(select1);
		hdCntfile = "(" + n + " file" + (n > 1 ? "s" : "") + ") ";
	  }   
   }
   b1 = d.createElement("b");
   if(nodekol.tagName == "ajax_del" && ajaxDel != "1") continue;
   if(nodekol.tagName == "chkbox" || nodekol.tagName == "ajax_del" || nodekol.tagName == "fileage") {
     b1.setAttribute("class", "sorttable_nosort");
	 if(nodekol.tagName == "ajax_del") td.setAttribute("style", "padding-right:20px;");
   }
   fieldTxt = d.createTextNode((hdCntfile != '' ? hdCntfile : '') + sibl.data);
   b1.appendChild(fieldTxt); td.appendChild(b1); 
   tr.appendChild(td);
   headerTbl[nodekol.tagName] = sibl.data;   
  }
 }
 
 thead1.appendChild(tr); 
 tbL.appendChild(thead1);
 //--/end header
 
 var tbody2 = d.createElement("tbody");
 var varcustHeight = (n >= 13 ? "280px" : "100%");
 tbody2.id = "flcontainer";
 tbody2.setAttribute("style", "height: " + varcustHeight + ";");
 
 var bodyrow_container = '((__row_container__))';
 var rowcontainer = d.createTextNode(bodyrow_container);
 tbody2.appendChild(rowcontainer);
 
 //======
 //footer
 if(n>0){
  var tfoot1 = d.createElement("tfoot");
  tr = d.createElement("tr"); 
  tr.id = "sffooter";
  td = d.createElement("td"); 
  tr.appendChild(td); 
  td = d.createElement("td");  
  fieldTxt = d.createTextNode("Total : "+n+ " file" + (n>1?'s':'')); 
  td.appendChild(fieldTxt);
   tr.appendChild(td); 
  td = d.createElement("td"); 
  var tagtotsize = retXML.getElementsByTagName('totalsize').item(0);
  fieldTxt = d.createTextNode(cleanTxt(tagtotsize.childNodes.item(0).data));
  td.appendChild(fieldTxt);
   tr.appendChild(td);
  td = d.createElement("td");

  var tagcspan = retXML.getElementsByTagName('columnspan').item(0);
  tagcspan = (tagcspan ? tagcspan.childNodes.item(0).data : -1);
  if (tagcspan > 0) {
    //tagcspan = (ajaxDel=="1" ? parseInt(tagcspan) + 1 : tagcspan);
    fieldTxt = d.createTextNode(tagcspan); // tambah atu 
    td.setAttribute("colspan", "" + fieldTxt.nodeValue + "");
    tr.appendChild(td)
  }
  tfoot1.appendChild(tr); 
 }

 var procesTag = retXML.getElementsByTagName('proceestime').item(0);
 var div3 = d.createElement("div");
 var small1 = d.createElement("small");
 b1 = d.createElement("b");
 fieldTxt = d.createTextNode(n);
 b1.appendChild(fieldTxt);
 small1.setAttribute("class", "y");
 small1.appendChild(b1);
 div3.appendChild(small1);
 small1 = d.createElement("small");
 small1.setAttribute("class", "footer3");
 fieldTxt = d.createTextNode(' file' + (n > 1 ? 's' : '') + ' loaded in ' + procesTag.childNodes.item(0).data + ' sec');
 small1.appendChild(fieldTxt);
 div3.appendChild(small1); 
 tbL.appendChild(tbody2);
 tbL.appendChild(tfoot1);
 div2.appendChild(tbL); 
 div1.appendChild(div2);
 resultNode.appendChild(div1);
 resultNode.appendChild(div3);
 
 //Continue fill content table --babak selanjutnya >,<
 createContent(retXML, resultNode, bodyrow_container, headerTbl);
};


function createContent(retXML, resultNode, row_container, hdrTabel){
 var rn = "\n"; 
 //query content files
 // ngambil node files dari XML
 var bufFile = new Object(); var idxfile = -1;
 var tagfiles = retXML.getElementsByTagName('files').item(0);
 for (var iNode = 0; iNode < tagfiles.childNodes.length; iNode++) {
   var filekol = tagfiles.childNodes.item(iNode);
   for (i = 0; i < filekol.childNodes.length; i++) {
     var sibl = filekol.childNodes.item(i);    
	 if(filekol.tagName=='fileid'){
	   idxfile++; bufFile['file'+idxfile] = new Object();	  
	 }
	 bufFile['file'+idxfile][filekol.tagName] = sibl.data;	 
    }
 } //end- ngambil node files
 
 if(_debug2){alert(dump(bufFile));} 
 
 // use instant rename ? 
 var feat_ajax = new Object();
 feat_ajax["tagIRen"] = retXML.getElementsByTagName('instantrename').item(0); 
 feat_ajax["tagIRen"] = (feat_ajax["tagIRen"] ? feat_ajax["tagIRen"].childNodes.item(0).data : 0);
 feat_ajax["ajaxDel"] = retXML.getElementsByTagName('ajaxdelete').item(0); 
 feat_ajax["ajaxDel"] = (feat_ajax["ajaxDel"] ? feat_ajax["ajaxDel"].childNodes.item(0).data : 0);
 
 var tline = '';
 var adt = new Object();
 tline = '<tr id="brs((chkidx))" class="rowlist" onmousedown="clk(\'cek_((chkidx))\', this);" onmouseout="if(document.getElementById(\'cek_((chkidx))\').checked){this.className=\'rowlist_checked\';}else{this.className=\'rowlist\';}" title="((filename))" align="center">'+rn;
 tline+= '<td>';
 tline+= (feat_ajax["tagIRen"] == 1 ? '<div id="rename_container" onmouseup="ren(this, \'fn_((chkidx))\', \'((dateid))\')"><div class="rename_es"></div></div>' : ''); 
 
 tline+= '<input name="files[]" id="cek_((chkidx))" value="((dateid))" onclick="clk(\'cek_((chkidx))\')" type="checkbox"></td>'+rn; 
 tline+= '<td id="fn_((chkidx))"><b><a href="((filepath))">((filename))</a></b></td>'+rn;
 tline+= '<td>((filesize))</td>';
 tline+= (undefined!=hdrTabel['filelink'] ? '<td title="((filelink))"><a href="((filelink))">((filelinktr))</a></td>'+rn:'');
 tline+= (undefined!=hdrTabel['filemd5'] ? '<td class="txtmd5">((filemd5))</td>'+rn:''); 
 tline+= (undefined!=hdrTabel['filecomment'] ? '<td>((filecomment))</td>'+rn:''); 
 tline+= (undefined!=hdrTabel['filedate'] ? '<td>((filedate))</td>'+rn:'');
 tline+= (undefined!=hdrTabel['fileage'] ? '<td><small>((fileage))</small></td>'+rn:'');
 tline+= (undefined!=hdrTabel['fileip'] ? '<td>((fileip))</td>'+rn:'');
 tline+= (feat_ajax["ajaxDel"] == 1 ? '<td><a id="dL-((chkidx))" href="del.php?d=((filedelkey))&lineid=((chkidx))" rel="facebox"> <img src="'+IMG_DIR_TPL+'rldel.png" alt="X"></a></td>' : ''); 
 tline+= '</tr>'; 
 var _tpl = "", text="";
 for(var fileke in bufFile)
 {
   pfile = bufFile[fileke];
   _tpl = tline;
   _tpl = _tpl.replace(/\(\(chkidx\)\)/g, pfile["fileid"].replace(/brs/g,''));
   _tpl = _tpl.replace(/\(\(filename\)\)/g, pfile["filename"]);
   _tpl = _tpl.replace(/\(\(filepath\)\)/g,  pfile["filepath"]);
   _tpl = _tpl.replace(/\(\(dateid\)\)/g, pfile["filedateid"]);
   _tpl = _tpl.replace(/\(\(filesize\)\)/g, pfile["filesize"]);
   _tpl = _tpl.replace(/\(\(filelink\)\)/g, pfile["filelink"]);
   _tpl = _tpl.replace(/\(\(filelinktr\)\)/g, pfile["filelinktr"]);
   _tpl = _tpl.replace(/\(\(filecomment\)\)/g, (pfile["filecomment"]!=undefined ? pfile["filecomment"]:"-"));
   _tpl = _tpl.replace(/\(\(filemd5\)\)/g, pfile["filemd5"]);
   _tpl = _tpl.replace(/\(\(filedate\)\)/g, pfile["filedate"]);
   _tpl = _tpl.replace(/\(\(fileage\)\)/g, pfile["fileage"]);
   _tpl = _tpl.replace(/\(\(fileip\)\)/g, pfile["fileip"]);
   _tpl = _tpl.replace(/\(\(filedelkey\)\)/g, pfile["filedelkey"]);
   text+= _tpl;
   //alert( unescape(pfile["filepath"]));
 }
 text = cleanTxt(text);
 bufRes = resultNode.innerHTML;
 bufRes = bufRes.replace(row_container, text);
 resultNode.innerHTML=bufRes;
 
}; //--/end createContent

function createTable(ret, retdvwait, retdvresult){
 var retXML = false;
 if(_debug){alert('Table Creator\n'+ret);}else{retXML = ret;}
 var nodeRes = d.getElementById(retdvresult);
 nodeRes.innerHTML='';
 if(retXML){ 
   	var rootdata = retXML.getElementsByTagName('data').item(0);
	var succeed = retXML.getElementsByTagName('succeed').item(0).childNodes.item(0).data;
	if(!rootdata){alert('Invalid XML root');}
	if(succeed==0){
	  nodeRes.innerHTML= retXML.getElementsByTagName('msg').item(0).firstChild.data;	  
	}else if(rootdata){
	  //Build header and content of table	  
	  createHeader(retXML, nodeRes);	  
	  if(_debug2){nodeRes.innerHTML+=dbPar(nodeRes.innerHTML);}
	} //--end succeed=1
    d.getElementById(retdvwait).innerHTML = '';
	d.getElementById(retdvwait).style.display = 'none';
	try{
	  gosetfacebook(); // re set facebook confirm delete
	  waitandload(); // re set tablesort
	}catch(e){};
 }
};

function cleanTxt(_text){return _text.replace(/\(\(space\)\)/g, " ");};
function isset(varname){return(typeof(window[varname])!='undefined');};
function dbPar(text){return bufText = "<br>Debug2:<br><textarea rows='10' cols='70'>"+text.replace(/>/g, "&gt;").replace(/</g, "&lt;")+"</textarea>";};
function dump(arr,level) {
 var dumped_text = ""; if(!level){level = 0;}
 //The padding given at the beginning of the line.
 var level_padding = "";
 for(var j=0;j<level+1;j++) level_padding += "    ";
 if(typeof(arr) == 'object') { //Array/Hashes/Objects 
  for(var item in arr) {
   var value = arr[item];		
    if(typeof(value) == 'object') { //If it is an array,
     dumped_text += level_padding + "'" + item + "' ...\n";
     dumped_text += dump(value,level+1);
    } else {
 	dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
    }
   }
 } else { //Stings/Chars/Numbers etc.
 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
 }
 return dumped_text;
};

//======================



//---------------------
// MAIN Function to 
function get(txacc,dvwait,dvres,btnsubmit) {
  //--------------------------------------
  var t_cnt=''; var __respon = 'text';
  myacc.unsec=0; myacc.cnt_valid=0; myacc.longacc='';
  ibtnsubmit=btnsubmit; myacc.param = txacc; 
  myacc.multicheck=false; myacc.index_status=false;
  switch(txacc){
    case 'staccounts':
		myacc.param='staccounts=';
		myacc.index_status = true;
		try{ // try this. avoid error on page other than index
		 d.getElementById(myacc.idbuttonrschk).disabled=true;
		 d.getElementById(myacc.divwaitrschk).innerHTML='<span style="color:#239FD9;">Please Wait ..<br>currently checking acc</span>';
		 d.getElementById(myacc.divwaitrschk).style.display='';
		}catch(e){}
		
	break; 
    case 'accounts':
		t_cnt=(d.getElementById(txacc).value);
		if(t_cnt.indexOf(":")==-1){return;}
		myacc.param='accounts=';
		myacc.multicheck=true;		
		try{d.getElementById('refresh').style.display='none'}catch(e){}
	break;
    case 'posttraffic':
		myacc.param='fntrafic=' + d.getElementById('posttraffic').value;
	break; 
    case 'misionMd5':
		myacc.param='md5fn=' + d.getElementById('misionMd5').value;
	break; 
	case 'doTBLoad':
		//myacc.param='TBLoad='+d.getElementById('rfr').value+':::';
		myacc.param='TBLoad='+d.getElementById('rfr').value+':::';
		__respon = 'xml';
	break;
    case 'deletelog':
		myacc.param='dellog=' + d.getElementById('deletelog').value;
	break;
    case 'ajxRename':
		myacc.param='ren=' + d.getElementById('renhiddenval').value;
	break;
    default:myacc.param=''; break;
  };
  
  d.getElementById(dvwait).innerHTML='&nbsp;<span id="waiting_tbload" style="background-color:#800000;">&nbsp;<img alt="loading" src="misc/fbload.gif">&nbsp;&nbsp;<b class="o">Please&nbsp;wait..</b>&nbsp;</span>';
  d.getElementById(dvwait).style.display='';

  if(myacc.param!=''){
	if(myacc.multicheck){
		myacc.acc=makeAray(t_cnt);
		myacc.j=0; myacc.res=''; myacc.valid='';
		myacc.head=myacc.chead;
		d.getElementById(ibtnsubmit).disabled=true;
	}
	//alert(myacc.param);
	callAjax('ajax_main.php', dvwait, dvres, myacc, __respon);
  }
};



//============
function makeAray(v){var txtArray=v.split('\n');for(var i=0;i<txtArray.length;i++){/\s$/.test(txtArray[i])?txtArray[i]=txtArray[i].replace(/\s$/,''):null;if(txtArray[i].length==0){txtArray.splice(i,1);}}return txtArray;};

function login(u,p,_ltype){var d=document;var d=d,loginuser; var rshost="https://ssl.rapidshare.com/cgi-bin/"; var formact=d.getElementById('myform'); if(_ltype=="cz"){loginuser="username";formact.action = rshost + "collectorszone.cgi";}else{loginuser="login";formact.action = rshost + "premiumzone.cgi";} d.getElementById(loginuser).value=u; d.getElementById("password").value=p;d.myform.submit();};

function pr(percent){var d=document;d.getElementById("progress").style.width=percent+'%';return true;};

function setzeTT(zahl){var t=zahl;var tren=" ";if(zahl.length>3){t=setzeTT(zahl.substr(0,zahl.length-3))+tren+zahl.substr(zahl.length-3)}return t;};

function NoDetail(s,L){var d=document;var val_nodetail=d.getElementById('nodet').checked?true:false;var toinput='';if(val_nodetail){toinput=s}else{toinput=L;}d.getElementById('validacc').innerHTML=toinput;}
function cleanacc(theacc)
{	var part_acc,part_acc_b,bufacc='';
	part_acc=theacc.split('\n');
	for(var i=0;i<part_acc.length;i++)
	{
		if(part_acc[i].indexOf(':')!=-1)
		{
		 part_acc_b = part_acc[i].split(':');
		 bufacc+=part_acc_b[0] + ':' + part_acc_b[1] + '\n';
		}
	}
	return bufacc;
};

function getBetween(txsrc,lh,rh){var poslh=txsrc.indexOf(lh);var posrh=txsrc.indexOf(rh);var pure=txsrc.substring(eval(poslh+lh.length),posrh);return pure;};

//=====================
// TBLoad
//=========
// Server Files
function _gLoad(_dwait, _dresult){
	frmTB = d.getElementById('frmTBLoad');
	frmTB.action = 'javascript:get("doTBLoad", "'+_dwait+'", "'+_dresult+'", "")';
	frmTB.submit();
};
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
};

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
};
