var xmlHttpRequestHandler = new Object();
var requestObject;
var alpha = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];

xmlHttpRequestHandler.createXmlHttpRequest = function(){
	var XmlHttpRequestObject;
	if(typeof XMLHttpRequest != "undefined"){
		XmlHttpRequestObject = new XMLHttpRequest();
		}
	else if(window.ActiveXObject){
		var tryPossibleVersions = ["MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp", "Microsoft.XMLHttp"];
		for(i=0;i<tryPossibleVersions.length;i++){
			try{
				XmlHttpRequestObject = new ActiveXObject(tryPossibleVersions[i]);
				break;
				}
			catch(xmlHttpRequestObjectError){
				// Ignore Exception
				}
			}
		}
		return XmlHttpRequestObject;
	}


function checkall(thisform){
	id = thisform.id;
	count = thisform.id.length;
	
	check = thisform.checkb.checked;
	
	if (count > 1) {
		for ( var i = 0; i < count; ++i ) {
			if (check){
				id[i].checked = true;
				}
			else{
				id[i].checked = false;
				}
			}
		}
	}
	
function openwindow(url,h,w){
	//var Thiswidth=screen.width;
	//var Thisheight=screen.height;
	//window.moveTo(0,0);
	//window.resizeTo(Thiswidth,Thisheight-30);
	//w = Thiswidth;
	//h = Thisheight-30;
	var id = document.getElementById("id");
	window.open(url+"&idd="+id.value,'',"toolbar=no,alwaysRaised=yes,location=no,directories=no,status=no,menubar=no,scrollbars=yes,height="+h+",width="+w+",modal=1");
	//window.moveTo(0,0);
	}
	
function createpayroll(url,h,w){
	var f = document.getElementById("fdate");
	var t = document.getElementById("tdate");
	var cid = document.getElementById("company_id");
	var svar = document.getElementById("svar");
	var syy = document.getElementById("syy");
	var smm = document.getElementById("smm");
	var payday = svar.value + syy.value + "-" + smm.value;
	var pay_id = document.getElementById("pay_id");
	var type = document.getElementById("type");
	var days = document.getElementById("days");
	url = url + "&date1="+f.value+"&date2="+t.value+"&cid="+cid.value+"&payday=" + payday +"&pay_id=" + pay_id.value + "&type=" + type.value + "&days=" + days.value+"";
	window.open(url,"Payroll","toolbar=no,alwaysRaised=yes,location=no,directories=no,status=no,menubar=no,scrollbars=yes,height="+h+",width="+w+",modal=1");
	}
	
function openTimeCard(filename){
	h = screen.heigth;
	w = screen.width
	url = "viewtimecard.php?filename=" + filename;
	window.open(url,"Payroll","toolbar=no,alwaysRaised=yes,location=no,directories=no,status=no,menubar=no,scrollbars=yes,height="+h+",width="+w+",modal=1");
	}
	
function ClickButton(id){
	document.getElementById(id).click();
	}
	
function deleteID(hidden,id){
	hidden.value = id;
	}
	
function OnPop(FormId, parentId,posX, posY,id){
	var it = document.getElementById(FormId);
	var img = document.getElementById(parentId); 
    
	x = xstooltip_findPosX(img) + posX;
	y = xstooltip_findPosY(img) + posY;
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	
    	it.style.visibility = 'visible'; 
	
	if (id == 1){
		window.frames["pop"].location = 'emsearch.php?search=' + img.value;
		}
	if (id == 2){
		window.frames["pop"].location = 'usersearch.php?search=' + img.value;
		}
	}
	
function xstooltip_findPosX(obj){
	var curleft = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
			}
		}
	else if (obj.x){
		curleft += obj.x;
		}
	return curleft;
	}

function xstooltip_findPosY(obj){
	var curtop = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop
			obj = obj.offsetParent;
			}
		}
	else if (obj.y){
		curtop += obj.y;
		}
	return curtop;
	}
	
function PopHide(id){
	setTimeout("PopHideDelay('" + id + "')",500);
	}
	
function PopHideDelay(id){
	it = document.getElementById(id); 
	it.style.visibility = 'hidden'; 	
	}
	
function PutID(id){
	window.parent.document.getElementById("keyword").value=id; 
	}
	
function GenFrame(url,id){
	window.frames["xframe"].location = url;
	for (x=1;x<23;x++){
		document.getElementById("td" + x).style.backgroundColor='white';
		}
	document.getElementById(id).style.backgroundColor='lightblue';
	var frm = document.getElementById('xframe');
	if(id == "td1"){
		frm.style.height="900px";
		}
	else if(id == "td3"){
		frm.style.height="2800px";
		}
	else if(id == "td4"){
		frm.style.height="1000px";
		}
	else if(id == "td14"){
		frm.style.height="1000px";
		}
	else if(id == "td20"){
		frm.style.height="1200px";
		}
	else if(id == "td21"){
		frm.style.height="1000px";
		}
	else if(id == "td22"){
		frm.style.height="1000px";
		}
	else{
		frm.style.height="500px";
		}
	}

 function picupload(upload_field){
	var re_text = /\.gif|\.txt|\.png|\.jpg/i;
	var filename = upload_field.value;
	if (filename.search(re_text) == -1){
		alert("we do not support this file");
		upload_field.form.reset();
		return false;
		}
	//upload_field.form.submit();
	return true;
	}
	
function opacity(id, opacStart, opacEnd, millisec) {
	var speed = Math.round(millisec / 100);
	var timer = 0;
	
	if(opacStart > opacEnd) {
		for(i = opacStart; i >= opacEnd; i--) {
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
			}
		} 
	else if(opacStart < opacEnd) {
		for(i = opacStart; i <= opacEnd; i++){
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
			}
		}
	}

function changeOpac(opacity, id) {
	var object = document.getElementById(id).style;
	object.opacity = (opacity / 100);
	object.MozOpacity = (opacity / 100);
	object.KhtmlOpacity = (opacity / 100);
	object.filter = "alpha(opacity=" + opacity + ")";
	}

function findPosX(obj){
	var curleft = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
			}
		}
	else if (obj.x){
		curleft += obj.x;
		}
	return curleft;
	}

function findPosY(obj){
	var curtop = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop
			obj = obj.offsetParent;
			}
		}
	else if (obj.y){
		curtop += obj.y;
		}
	return curtop;
	}
	
function langs(posX,posY){
	var it = document.getElementById('language');
    	var parent = document.getElementById('lingual');
	
	x = findPosX(parent) + posX;
	y = findPosY(parent) + posY;
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	opacity('language', 0, 100, 500);
	it.style.visibility='visible';
	}

function langsh(){
	opacity('language', 100, 0, 500);
	}
	
	
function setsched(url,parent){
	x = findPosX(parent) +40;
	y = findPosY(parent) -60;
	var edit = document.getElementById('edit');
	var keyword = document.getElementById('keyword');
	if(keyword.value.length == 0){
		keyword.focus();
		}
	else{
		edit.style.visibility = 'visible';
		edit.style.top = y + 'px';
		edit.style.left = x + 'px';
		opacity('edit', 0, 100, 500);
		getHtml(url);
		}
	}
	
function getHtml(url){
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponse;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponse(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var con = document.getElementById('edit');
		con.innerHTML = txt;
		}
	}
	
function getHtmlCopy(parent){
	x = findPosX(parent) -385;
	y = findPosY(parent) +0;
	var copyto = document.getElementById('copyto');
	var mmmm = document.getElementById('mmmm');
	var yyyy = document.getElementById('yyyy');
	var date1 = document.getElementById('date1');
	var date2 = document.getElementById('date2');
	yymm = daysInMonth(parseInt(mmmm.value)-1, parseInt(yyyy.value));
	date1.value=yyyy.value+"-"+mmmm.value+"-01";
	date2.value=yyyy.value+"-"+mmmm.value+"-"+yymm;
	
	if(copyto.style.visibility == 'visible'){
		copyto.style.visibility = 'hidden';
		copyto.style.top = '0px';
		copyto.style.left = '0px';
		}
	else{
		var keyword = document.getElementById('keyword');
		var paycode = document.getElementById('paycode');
		if(keyword.value.length == 0){
			keyword.focus();
			}
		else{
			copyto.style.visibility = 'visible';
			copyto.style.top = y + 'px';
			copyto.style.left = x + 'px';
			opacity('copyto', 0, 100, 500);
			puthtml('server_copy.php?paycode='+paycode.value)
			}
		}
	
	
	}
	
function puthtml(url){
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponseCopy;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponseCopy(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var con = document.getElementById('searchbody');
		con.innerHTML = txt;
		}
	}
	
function closeSet(){
	var con = document.getElementById('edit');
	con.innerHTML = "";
	con.style.visibility = 'hidden';
	con.style.top = '0px';
	con.style.left = '0px';
	}
	
function checkall(){
	var count = document.getElementById('count');
	var ca = document.getElementById('ca');
	for(x=0;x<parseInt(count.value);x++){
		var cb = document.getElementById('cb'+x);
		if(ca.checked == true){
			cb.checked = true;
			}
		else{
			cb.checked = false;
			}
		}
	}
	
function check(id){
	var idx = document.getElementById(id);
	idx.click();
	}
	
function daysInMonth(iMonth, iYear){
	return 32 - new Date(iYear, iMonth, 32).getDate();
	}

function clTab(thIs,cnt,url){
	for(x=0;x<cnt;x++){
		var tab = document.getElementById('tab'+x);
		tab.style.backgroundColor='';
		tab.style.borderBottomColor='';
		}
	thIs.style.backgroundColor='#eaead5';
	thIs.style.borderBottomColor='#eaead5';
	putCron(url)
	}
	
function putCron(url){
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponseCron;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponseCron(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var con = document.getElementById('contentsched');
		con.innerHTML = txt;
		}
	}
	
function onfreaky(thIs){
	var file_name = document.getElementById('file_name');
	file_name.value=thIs.value;
	}
	
function onups(txt){
	var tx = document.getElementById(txt);
	if(tx.value){
		a = (tx.value).split(".");
		b = a.length;
		c = a[b-1];
		if(c == 'xml'){
			return true;
			}
		alert('Invalid XML File');
		return false;
		}
	alert('No file to be uploaded');
	return false;
	}
	
function showTip(parent){
	x = findPosX(parent) +40;
	y = findPosY(parent) -60;
	var help = document.getElementById('help');
	opacity('help', 100, 0, 0);
	help.style.visibility = 'visible';
	help.style.top = y + 'px';
	help.style.left = x + 'px';
	opacity('help', 0, 100, 500);
	}
	
function ondeletez(id){
	var idz = document.getElementById('id');
	idz.value=id;
	if (confirm("Are you sure to delete this record?")==false){
		return false;
		}
	else{
		return true;
		}
	}
	
function showbday(dt,thIs){
	x = findPosX(thIs) +100;
	y = findPosY(thIs) -40;
	var edit = document.getElementById('bdate');
	opacity('bdate', 0, 0, 0);
	edit.style.visibility = 'visible';
	edit.style.top = y+'px';
	edit.style.left = x+'px';
	putBday('showbday.php?date='+dt);
	opacity('bdate', 0, 100, 300);
	}

function hidebday(){
	var edit = document.getElementById('bdate');
	edit.style.visibility = 'hidden';
	edit.style.top = '0px';
	edit.style.left = '0px';
	edit.innerHTML = "";
	}
	
function putBday(url){
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponseBday;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponseBday(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var edit = document.getElementById('bdate');
		edit.innerHTML = txt;
		}
	}
	
function showpayid(id){
	var thIs = document.getElementById(id)
	x = findPosX(thIs)-310;
	y = findPosY(thIs) +30;
	var tabmainview = document.getElementById('tabmainview');
	if(tabmainview.style.visibility == 'visible'){
		opacity('tabmainview', 100, 100, 0);
		tabmainview.style.visibility = 'hidden';
		tabmainview.style.top = '0px';
		tabmainview.style.left = '0px';
		opacity('tabmainview', 100, 0, 300);
		}
	else{
		opacity('tabmainview', 0, 0, 0);
		tabmainview.style.visibility = 'visible';
		tabmainview.style.top = y+'px';
		tabmainview.style.left = x+'px';
		opacity('tabmainview', 0, 100, 300);
		}
	}
	
function showSH(id){
	var thIs = document.getElementById(id)
	x = findPosX(thIs);
	y = findPosY(thIs) +27;
	var SHheader = document.getElementById('SHheader');
	if(SHheader.style.visibility == 'visible'){
		opacity('SHheader', 100, 100, 0);
		SHheader.style.visibility = 'hidden';
		SHheader.style.top = '0px';
		SHheader.style.left = '0px';
		opacity('SHheader', 100, 0, 300);
		}
	else{
		opacity('SHheader', 0, 0, 0);
		SHheader.style.visibility = 'visible';
		SHheader.style.top = y+'px';
		SHheader.style.left = x+'px';
		opacity('SHheader', 0, 100, 300);
		}
	}
	
function onclickpayID(thIs){
	var payid = document.getElementById('pay_id');
	var cb = document.getElementById('cb'+thIs);
	for(x=0;x<1000;x++){
		var cbx = document.getElementById('cbw'+thIs+x);
		if(cbx){
			if(cb.checked){
				cbx.checked = true;
				}
			else{
				cbx.checked = false;
				}
			}
		}
	}
	
function onClck(id){
	var cb = document.getElementById('cb' + id);
	var yyy = document.getElementById('yyy');
	var loading = document.getElementById('load');
	loading.style.visibility = 'visible';
	
	cb.click();
	for(x=0;x<parseInt(yyy.value);x++){
		var td = document.getElementById('tdz' + alpha[parseInt(id)] + '' + x);
		if(cb.checked){
			td.style.visibility = 'visible';
			td.style.width="70px";
			}
		else{
			td.style.visibility = 'hidden';
			td.style.width="0px";
			}
		}
	loading.style.visibility = 'hidden';
	}
	
function chbox(emid, thIs, date1, date2, idx){
	var cb = thIs;
	var divcb = document.getElementById('div'+emid);
	var boxcb = document.getElementById('box'+emid);
	var id = document.getElementById('id'+emid);
	var name = document.getElementById('name'+emid);
	if(cb.checked){
		//divcb.style.backgroundColor='';
		id.style.backgroundColor='';
		name.style.backgroundColor='';
		boxcb.style.backgroundColor='';
		}
	else{
		//divcb.style.backgroundColor='#dbab77';
		id.style.backgroundColor='#dbab77';
		name.style.backgroundColor='#dbab77';
		boxcb.style.backgroundColor='#dbab77';
		}
	
	onclbox('emp',emid, '',date1,date2,idx,idx);
	}
	
function onkey(emid, qq){
	var net = document.getElementById('net'+emid);
	var netz = document.getElementById('netz'+emid);
	var qqq = document.getElementById('qqq');
	var tot = 0;
	var qq = parseInt(qqq.value);
	for (x=0;x<parseInt(qq);x++){
		var box = document.getElementById(emid+'pal'+x);
		var hid = document.getElementById(emid+'hid'+x);
		tot = tot + (parseFloat(box.value) - parseFloat(hid.value));
		//alert(box.value);
		}
	net.value = parseFloat(netz.value) - tot;
	}
	
function onclk(type, thIs, emid, xxx){
	var basic = document.getElementById(type+emid);
	var em_id = document.getElementById('em_id');
	var basic_ses = document.getElementById(em_id.value);
	var txt = document.getElementById('text'+type+emid);
	x = findPosX(thIs) +xxx;
	y = findPosY(thIs);
	if(basic.style.visibility == 'visible'){
		basic.style.visibility = 'hidden';
		txt.style.backgroundColor='';
		txt.style.color='';
		//thIs.style.backgroundColor='';
		basic.style.top = '0px';
		basic.style.left = '0px';
		}
	else{
		basic.style.visibility = 'visible';
		txt.style.backgroundColor='#bc0a0a';
		txt.style.color='#FFF';
		//thIs.style.backgroundColor='#fcff00';
		basic.style.top = y+'px';
		basic.style.left = x+'px';
		
		aaa = type+emid;
		if(aaa != em_id.value){
			if(basic_ses){
				var txt_ses = document.getElementById('text'+em_id.value);
				txt_ses.style.backgroundColor='';
				txt_ses.style.color='';
				basic_ses.style.visibility = 'hidden';
				basic_ses.style.top = '0px';
				basic_ses.style.left = '0px';
				
				}
			}
		em_id.value = type+emid;
		}
	}
	
function oncldiv(box){
	var box = document.getElementById(box);
	box.click();
	}
	
function onclbox(type,emid,tag,date1,date2,chck,misc,tmp){
	if(type == 'emp'){
		var box = document.getElementById(misc);
		var text = document.getElementById('text'+type+emid);
		}
	else{
		var box = document.getElementById(tag+'cb'+emid);
		var text = document.getElementById('text'+type+emid);
		}
	
	var em_idorig = document.getElementById('em_idorig');
	
	if(type=='nt'){
		xtype = tag
		}
	else{
		xtype = type
		}
	var typ = document.getElementById('typ');
	var chk = document.getElementById('chk');
	var d1 = document.getElementById('date1');
	var d2 = document.getElementById('date2');
	var msc = document.getElementById('msc');
	typ.value=xtype;
	d1.value=date1;
	d2.value=date2;
	msc.value=misc;
	
	chk.value=chck;
	
	em_idorig.value = emid;
	if(box.checked == true){
		num1 = parseFloat(text.value);
		num2 = parseFloat(box.value);
		eq = num1 + num2;
		text.value = eq.toFixed(2);
				
		if(type=='ded'){
			var net = document.getElementById('textnet'+emid);
			netz = parseFloat(net.value);
			ez = netz - num2;
			net.value = ez.toFixed(2);
			}
		if(type=='nt'){
			var net = document.getElementById('textnet'+emid);
			netz = parseFloat(net.value);
			ez = netz + num2;
			net.value = ez.toFixed(2);
			
			var date1 = document.getElementById('date1');
			var date2 = document.getElementById('date2');
			var typ = document.getElementById('typ');
			var chck = document.getElementById('chk');
			var chkz = document.getElementById(chck.value);
			execType(chkz.checked,type,emid,date1.value,date2.value,msc.value);
			}
		else{
			var textbasic = document.getElementById('textbasic'+emid);
			var textabs = document.getElementById('textabs'+emid);
			var textot = document.getElementById('textot'+emid);
			var textlate = document.getElementById('textlate'+emid);
			var textut = document.getElementById('textut'+emid);
			var textoth = document.getElementById('textoth'+emid);
			var textstatus = document.getElementById('textstatus'+emid);
			var texttype = document.getElementById('texttype'+emid);
			salary = parseFloat(textbasic.value) - parseFloat(textabs.value) - parseFloat(textlate.value) - parseFloat(textut.value);
			salary = salary + parseFloat(textot.value) + parseFloat(textoth.value)
			GetTin(salary,texttype.value,textstatus.value,emid);
			}
		}
	else{
		num1 = parseFloat(text.value);
		num2 = parseFloat(box.value);
		eq = num1 - num2;
		//~ if(eq < 0){
			//~ eq = 0;
			//~ }
		text.value = eq.toFixed(2);
		if(type=='ded'){
			var net = document.getElementById('textnet'+emid);
			netz = parseFloat(net.value);
			ez = netz + num2;
			net.value = ez.toFixed(2);
			}
		if(type=='nt'){
			var net = document.getElementById('textnet'+emid);
			netz = parseFloat(net.value);
			ez = netz - num2;
			net.value = ez.toFixed(2);
			
			var date1 = document.getElementById('date1');
			var date2 = document.getElementById('date2');
			var typ = document.getElementById('typ');
			var chck = document.getElementById('chk');
			var chkz = document.getElementById(chck.value);
			execType(chkz.checked,type,emid,date1.value,date2.value,msc.value);
			}
		else{
			var textbasic = document.getElementById('textbasic'+emid);
			var textabs = document.getElementById('textabs'+emid);
			var textot = document.getElementById('textot'+emid);
			var textlate = document.getElementById('textlate'+emid);
			var textut = document.getElementById('textut'+emid);
			var textoth = document.getElementById('textoth'+emid);
			var textstatus = document.getElementById('textstatus'+emid);
			var texttype = document.getElementById('texttype'+emid);
			salary = parseFloat(textbasic.value) - parseFloat(textabs.value) - parseFloat(textlate.value) - parseFloat(textut.value);
			salary = salary + parseFloat(textot.value) + parseFloat(textoth.value)
			GetTin(salary,texttype.value,textstatus.value,emid);
			}
		}
	}
	
function GetTin(salary,type,status,emid){
	var payday = document.getElementById('payday');
	url = "server_tax.php?salary="+salary+"&type="+type+"&status="+status+"&payday="+ payday.value+"&em_id="+emid;
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponseTin;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponseTin(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var textemid = document.getElementById('em_idorig');
		emid = textemid.value;
		//
		var textbasic = document.getElementById('textbasic'+emid);
		var textabs = document.getElementById('textabs'+emid);
		var textot = document.getElementById('textot'+emid);
		var textlate = document.getElementById('textlate'+emid);
		var textut = document.getElementById('textut'+emid);
		var textoth = document.getElementById('textoth'+emid);
		var textsss = document.getElementById('textsss'+emid);
		var textpi = document.getElementById('textpi'+emid);
		var textph = document.getElementById('textph'+emid);
		var textnet = document.getElementById('textnet'+emid);
		var textded = document.getElementById('textded'+emid);
		var textnt = document.getElementById('textnt'+emid);
		var texttin = document.getElementById('texttin'+emid);
		var textgat = document.getElementById('textgat'+emid);
		
		var paytype = document.getElementById('paytype');
		var date1 = document.getElementById('date1');
		var date2 = document.getElementById('date2');
		var typ = document.getElementById('typ');
		var chck = document.getElementById('chk');
		var chkz = document.getElementById(chck.value);
		var msc = document.getElementById('msc');
		
		salary = parseFloat(textbasic.value) - parseFloat(textabs.value) - parseFloat(textlate.value) - parseFloat(textut.value);
		salary = salary + parseFloat(textot.value) + parseFloat(textoth.value);
		vaR = txt.split("@@");
		
		texttin.value = vaR[0];
		
		if(paytype.value=='RESIGNED'){
			gross = salary - parseFloat(vaR[0]);
			}
		else{
			textsss.value = vaR[3];
			textpi.value = vaR[1];
			textph.value = vaR[2];
			gross = salary - parseFloat(vaR[0]) - parseFloat(vaR[1]) - parseFloat(vaR[2]) - parseFloat(vaR[3]);
			}
		textgat.value = gross;
			
		
		net = gross + parseFloat(textnt.value) - parseFloat(textded.value);
		if(!net){
			net=0;
			}
		textnet.value=net.toFixed(2);
			
		
			
		execType(chkz.checked,typ.value,emid,date1.value,date2.value,msc.value);
		}
	}
	
function onclknet(type, thIs, emid, xxx){
	var basic = document.getElementById(type);
	var txt = document.getElementById('text'+type+emid);
	var em_id = document.getElementById('em_id');
	
	x = findPosX(thIs) +xxx; 
	y = findPosY(thIs);
	
	if(y < 320){
		y = y -100;
		}
	else{
		y = y -310;
		}
	
	if(basic.style.visibility == 'visible'){
		if(emid != em_id.value){
			basic.style.top = y+'px';
			basic.style.left = x+'px';
			}
		else{
			basic.style.visibility = 'hidden';
			basic.style.top = '0px';
			basic.style.left = '0px';
			}
		}
	else{
		basic.style.visibility = 'visible';
		basic.style.top = y+'px';
		basic.style.left = x+'px';
		}
	var textbasic = document.getElementById('textbasic'+emid);
	var textabs = document.getElementById('textabs'+emid);
	var textot = document.getElementById('textot'+emid);
	var textlate = document.getElementById('textlate'+emid);
	var textut = document.getElementById('textut'+emid);
	var textoth = document.getElementById('textoth'+emid);
	var textsss = document.getElementById('textsss'+emid);
	var textph = document.getElementById('textph'+emid);
	var textpi = document.getElementById('textpi'+emid);
	var textded = document.getElementById('textded'+emid);
	var textnt = document.getElementById('textnt'+emid);
	var textnet = document.getElementById('textnet'+emid);
	var texttin = document.getElementById('texttin'+emid);
	
	var nbasic = document.getElementById('nbasic');
	var nabs = document.getElementById('nabs');
	var not = document.getElementById('not');
	var nlate = document.getElementById('nlate');
	var nut = document.getElementById('nut');
	var noth = document.getElementById('noth');
	var nsss = document.getElementById('nsss');
	var nph = document.getElementById('nph');
	var npi = document.getElementById('npi');
	var nded = document.getElementById('nded');
	var nnp = document.getElementById('nnp');
	var ngt = document.getElementById('ngt');
	var ngat = document.getElementById('ngat');
	var ntax = document.getElementById('ntax');
	var nnt = document.getElementById('nnt');
			
	nbasic.innerHTML = textbasic.value; 
	nabs.innerHTML = textabs.value; 
	not.innerHTML = textot.value; 
	nlate.innerHTML = textlate.value; 
	nut.innerHTML = textut.value; 
	noth.innerHTML = textoth.value; 
	nsss.innerHTML = textsss.value; 
	nph.innerHTML = textph.value; 
	npi.innerHTML = textpi.value; 
	nded.innerHTML = textded.value; 
	nnt.innerHTML = textnt.value; 
	nnp.innerHTML = '<b>' + textnet.value + '</b>'; 
		
	grosstaxable = (parseFloat(textbasic.value) + parseFloat(textot.value) + parseFloat(textoth.value)) - (parseFloat(textabs.value) + parseFloat(textlate.value) + parseFloat(textut.value));
	grossaftertax = grosstaxable - texttin.value;
	tin = parseFloat(texttin.value);
			
	ngt.innerHTML = '<b>' + grosstaxable.toFixed(2) + '</b>'; 
	ngat.innerHTML = '<b>' + grossaftertax.toFixed(2) + '</b>'; 
	ntax.innerHTML = tin.toFixed(2); 	
	
	em_id.value = emid;
	}
	
function basicEdit(emid){
	var em_idorig = document.getElementById('em_idorig');
	em_idorig.value = emid;
	var post_type = document.getElementById('post_type');
	var basic = document.getElementById('textbasic' + emid);
	var type = document.getElementById('texttype' + emid);
	var status = document.getElementById('textstatus' + emid);
	var net = document.getElementById('textnet' + emid);
	if(post_type.value == 'COMMISSION' || post_type.value == 'BONUS' || post_type.value == 'RESIGNED'){
		GetTinX(basic.value,type.value,status.value)
		}
	else if(post_type.value == '13TH MONTH'){
		net.value = basic.value;
		}
	}
	
function GetTinX(salary,type,status){
	url = "server_tax.php?salary="+salary+"&type="+type+"&status="+status;
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=onReadyStateChangeResponseTinX;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function onReadyStateChangeResponseTinX(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var textemid = document.getElementById('em_idorig');
		emid = textemid.value;
		var net = document.getElementById('textnet' + emid);
		var tax = document.getElementById('texttin'+emid);
		var gat = document.getElementById('textgat'+emid);
		var gross = document.getElementById('textgross' + emid);
		var basic = document.getElementById('textbasic' + emid);
		vaR = txt.split("@@");
		tax.value = vaR[0];
		gross.value = basic.value;
		gat.value = parseFloat(gross.value) - parseFloat(tax.value)
		net.value = parseFloat(gat.value).toFixed(2);
		}
	}
	
function OclkMisc(thIs,emid){
	var misc = document.getElementById('misc');
	var em_id = document.getElementById('em_id');
	
	x = findPosX(thIs) + 39; 
	y = findPosY(thIs);
	if(misc.style.visibility == 'visible'){
		if(emid != em_id.value){
			misc.style.top = y+'px';
			misc.style.left = x+'px';
			}
		else{
			misc.style.visibility = 'hidden';
			misc.style.top = '0px';
			misc.style.left = '0px';
			}
		}
	else{
		misc.style.visibility = 'visible';
		misc.style.top = y+'px';
		misc.style.left = x+'px';
		}
	em_id.value = emid;
	}
	
function onclktypMisc(){
	var type = document.getElementById('misctype');
	var categ = document.getElementById('categ');
	
	if(type.value == '1' || type.value == '5'){
		categ.innerHTML = "<div class='inlinemisc1'>Days : </div> \
			<div class='inlinemisc2'> \
				<select id='miscdays'> \
					<option value='1'>1</option> \
					<option value='1.5'>1.5</option> \
					<option value='2'>2</option> \
					<option value='2.5'>2.5</option> \
					<option value='3'>3</option> \
					<option value='3.5'>3.5</option> \
					<option value='4'>4</option> \
					<option value='4.5'>4.5</option> \
					<option value='5'>5</option> \
					<option value='5.5'>5.5</option> \
				</select> \
			</div>";
		}
	else if(type.value == '2' || type.value == '6'){
		categ.innerHTML = "<div class='inlinemisc1'>Min. : </div> \
			<div class='inlinemisc2'> \
				<input type='text' id='miscmin' style='width:70px'> \
			</div>";
		}
	else if(type.value == '3'){
		categ.innerHTML = "<div class='inlinemisc1'>Amount : </div> \
			<div class='inlinemisc2'> \
				<input type='text' id='miscamount' style='width:70px'> \
			</div>";
		}
	else if(type.value == '4'){
		categ.innerHTML = "<div class='inlinemisc1'>Min : </div> \
			<div class='inlinemisc2'> \
				<input type='text' id='miscmin' style='width:70px'> \
			</div>";
		}
	}
	
///////////////////////////////////////////////////////////////////////


function GetXurl(url,subx){
	requestObject = xmlHttpRequestHandler.createXmlHttpRequest();
	requestObject.onreadystatechange=subx;
	requestObject.open("Get",url, true);
	requestObject.send(null);
	}
	
function saveMisc(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		var emid = document.getElementById('em_id');
		url = self.location + "";
		url = url.split("&&");
		url = url[0] + "&&emid="+emid.value+"&trxn="+txt
		
		self.location=url;
		}
	}	
	
function sTr(str){
	str = str.replace(/'/gi, "&#39;");
	str = str.replace(/"/gi, "&#34;");
	str = str.replace(/\n/gi, "<br>");
	return str
	}
	
function onClkSaveMisc(){
	var type= document.getElementById('misctype');
	var remarks = document.getElementById('miscremarks');
	var misc = document.getElementById('misc');
	var emid = document.getElementById('em_id');
	var perday = document.getElementById('textperday' + emid.value);
	var permin = document.getElementById('textpermin' + emid.value);
	
	misc.style.visibility = 'hidden';
	misc.style.top = '0px';
	misc.style.left = '0px';
	
	
	if(type.value=='1'){
		var varz = document.getElementById('miscdays');
		}
	else if(type.value=='2'){
		var varz = document.getElementById('miscmin');
		}
	else if(type.value=='3'){
		var varz = document.getElementById('miscamount');
		}
	else if(type.value=='4'){
		var varz = document.getElementById('miscmin');
		}
	else if(type.value=='5'){
		var varz = document.getElementById('miscdays');
		}
	else if(type.value=='6'){
		var varz = document.getElementById('miscmin');
		}
	
	var typ = sTr(type.value);
	var rem = sTr(remarks.value);
	var vars = sTr(varz.value);
	url = "savemisc.php?type=" + typ + "&var=" + vars + "&rem=" + rem + "&perday=" + perday.value + "&permin=" + permin.value + "&emid=" + emid.value;
	GetXurl(url,saveMisc);
	}
	
function clspOp(par){
	var par = document.getElementById(par);
	par.style.visibility = 'hidden';
	par.style.top = '0px';
	par.style.left = '0px';
	}
	
function onMicx(emid){
	thIs = document.getElementById('textoth' + emid);
	onclk('oth',thIs,emid,-410);
	}
	
/////////////////////////////////////////////////////////////////
	
function saveReq(){
	var ready, status;
	try{
		ready = requestObject.readyState;
		status = requestObject.status;
		}
	catch(e) {}
	if(ready == 4 && status == 200){
		txt = requestObject.responseText;
		}
	}
	
function execType(chk,typ,emid,date1,date2,msc){
	GetXurl('req_update.php?type='+typ+'&chk='+chk+'&emid='+emid+'&date1='+date1+'&date2='+date2+'&msc='+msc,saveReq);
	}
	
function checkAttendance(){
	var count = document.getElementById('count');
	a = 0
	for(x=0;x<parseInt(count.value);x++){
		var tin = document.getElementById('in'+x);
		var tout = document.getElementById('out'+x);
		var status = document.getElementById('status'+x);
		
		if(tin.value=='00:00' && tout.value=='00:00'){
			if(status.value=='REGULAR'){
				a = a + 1
				tin.focus();
				}
			if(status.value=='HALF DAY'){
				a = a + 1
				}
			if(status.value=='UNDER TIME'){
				a = a + 1
				}
			if(status.value=='VALACION LEAVE 0.5'){
				a = a + 1
				}
			if(status.value=='MATERNITY LEAVE 0.5'){
				a = a + 1
				}
			if(status.value=='PATERNITY LEAVE 0.5'){
				a = a + 1
				}
			if(status.value=='BEREAVEMENT LEAVE 0.5'){
				a = a + 1
				}
			if(status.value=='EMERGENCY LEAVE 0.5'){
				a = a + 1
				}
			if(status.value=='BIRTHDAY LEAVE 0.5'){
				a = a + 1
				}
			}
		}
		
	if(a>0){
		alert('Some Satus is Incorrect and without timein and out');
		return false;
		}
	else{
		return true;
		}
	}
	
function eDitsalary(thIs,pop){
	var misc= document.getElementById(pop);
	x = findPosX(thIs) + 50; 
	y = findPosY(thIs);
	if(misc.style.visibility == 'visible'){
		misc.style.visibility = 'hidden';
		misc.style.top = '0px';
		misc.style.left = '0px';
		}
	else{
		misc.style.visibility = 'visible';
		misc.style.top = y+'px';
		misc.style.left = x+'px';
		}
	}
	
function chsalary(){
	var newsalary = document.getElementById('newsalary');
	var saltype = document.getElementById('saltype');
	var salrem = document.getElementById('salrem');
	var salary = document.getElementById('salary');
	
	if(parseFloat(salary.value) > parseFloat(newsalary.value) && saltype.value == 'INCREASE'){
		alert('Invalid Increase salary process');
		return false;
		}
	if(salrem.value == ''){
		alert('Remarks is required');
		return false;
		}
	if(!parseFloat(newsalary.value)){
		alert('Invalid salary');
		return false;
		}
	}
	
function h2m(tm){
	ot = tm.split(":");
	hh = parseFloat(ot[0]);
	return (hh * 60)+parseFloat(ot[1])
	}	
	
function GetHours(){
	var ots = document.getElementById('ots');
	var ote = document.getElementById('ote');
	var hours = document.getElementById('hours');
	var mins = document.getElementById('mins');
	
	if(ots.value.length==5 && ote.value.length==5){
		otsm = h2m(ots.value);
		otem = h2m(ote.value);
		mm = parseFloat(otem)-parseFloat(otsm);
		
		if(mm >= 300 && mm <= 540 ){
			mm = mm - 60;
			}
		else if(mm > 840){
			mm = mm - 120;
			}
		
		hours.value=(mm/60).toFixed(2);
		mins.value=mm;
		}
	else{
		mins.value=0;
		hours.value=0;
		}
	}
	
function onSaveOt(){
	var mins = document.getElementById('mins');
	var dt = document.getElementById('date');
	if(parseInt(mins.value)>0 && dt.value!=''){
		return true;
		}
	else{
		alert('Some fields are missing');
		return false;
		}
	}
	
function PutTime(thIs){
	if(thIs.value.length==2){
		thIs.value = thIs.value + ":";
		}
	}
	
function maskTime(thIs,key){
	if ((key>47&&key<58)||(key>95&&key<106)){
		PutTime(thIs);
		}
	else if(key==8 || key==9){
		return true;
		}
	else{
		return false
		}
	}
	
	
function checkPosting(){
	var count = document.getElementById('count');
	for(x=0;x<parseInt(count.value);x++){
		var emid = document.getElementById('em_idx'+x);
		var netpay = document.getElementById('textnet'+emid.value);
		
		if(parseFloat(netpay.value) < 0){
			var name = document.getElementById('name'+emid.value);
			netpay.focus();
			alert(name.innerHTML + "'s netpay is negative.");
			return false;
			break;
			}
		}
		
	return true;
	}

function onSaveUTrxn(){
	var date1 = document.getElementById('date1');
	var date2 = document.getElementById('date2');
	var salary = document.getElementById('salary');
	var salary_based = document.getElementById('salary_based');
	
	if(date1.value=='' || date2.value==''){
		alert('Invalid Date');
		return false;
		}
	if(salary.checked==false && salary_based.checked==false){
		alert('Invalid Type');
		return false;
		}
	return true
	}
	
function oncmpposting(){
	var compa = document.getElementById('company_id');
	var payid = document.getElementById('pay_id');
	
	if(compa.value == 'ALL'){
		payid.style.visibility = 'hidden';
		}
	else{
		payid.style.visibility = 'visible';
		}
	}
	