function _dictInit(){
  if(_dict_init==1){
    _dictUpdateStatus();
  	return true;
  }
  if(! document || ! document.body || !document.body.firstChild){
    setTimeout("_dictInit()",800);
  	return true;
  }
  var agt = navigator.userAgent.toLowerCase();
  var b='border:none;padding:0px;margin:0px;';
  var f='font-weight:normal;font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;';
  _dict_is_ie = (agt.indexOf("msie")!=-1 && document.all);
  _dict_opera = (agt.indexOf('opera')!=-1 && window.opera && document.getElementById);
  var h = '<table width="300" border="0" cellspacing="0" cellpadding="0" ';
  h += 'style="border-top:1px solid #7E98D6;border-left:1px solid #7E98D6;';
  h += 'border-right:1px solid #7E98D6;border-bottom:1px solid #7E98D6;';
  h += '"><tr><td width="100%" style="'+b+'">';
  h += '<div style="width:300px;height:20px;cursor:move;background-color:#C8DAF3;display:inline;'+b+'" onmouseover="_dict_onmove=1;" onmouseout="_dict_onmove=0;">' ;
  h += '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="left" width="60%" height="20" style="background-color:#C8DAF3;color:#1A9100;font-size:14px;line-height:20px;border:none;padding:0 3px;margin:0px;'+f+'" id="_dict_title" name="_dict_title">';
  h += '&#21010;&#35789;&#32763;&#35793; - Dict.CN';
  h += '</td>';
  h += '<td align="right" height="20" style="width:35%;text-align:right;background-color:#C8DAF3;line-height:20px;border:none;padding:0 3px;margin:0px;'+f+'" valign="middle">';
  h += '<a href="javascript:_dictClose()" title="&#20851;&#38381;" target="_self" style="'+b+f+'">';
  h += '<img src="'+_dict_host+'img/close.gif" border="0" style="border:none;display:inline;'+b+'" align="absmiddle">';
  h += '</a>';
  h += '</td></tr></table>';
  h += '</div>';

  h += '<table border="0" cellspacing="4" cellpadding="3" width="100%" align="center" onmouseover="_dict_onlayer=1;" onmouseout="_dict_onlayer=0;" style="'+b+'">';
  h += '<tr><td style="'+b+'"><fieldset color="#00c0ff" style="padding:0 2px;margin:0px;'+f+'">';
  h += '<legend align="center" style="padding:0px;margin:0px;"></legend>';
  h += '<table border="0" cellspacing="0" cellpadding="0" align="center" style="'+b+'">';
  h += '<tr><td width="100%" height="120" style="'+b+'" id="_dictContent" name="_dictContent">';
  h += '<iframe id="_dictFrame" name="_dictFrame" HEIGHT="120" src="about:blank" FRAMEBORDER="0" width="100%"></iframe>';
  h += '</td></tr><tr align="center"><td width="100%" height="18" style="color:#999999;font-size:10px;line-height:18px;'+b+f+'" valign="bottom">';
  h += '&copy;2003-2010 ';
  h += '</td></tr></table></fieldset></td></tr></table>';
  h += '</td></tr></table>';
  try{
  	var els=document.getElementsByTagName("*");
	var zmax=97;
	for(var i=0;i<els.length;i++){
	     if(zmax< els[i].style.zIndex) zmax=els[i].style.zIndex
	}
    var el = document.createElement('div');
    el.id='_dict_layer';
    if(typeof el.style == "undefined") return;
    el.style.position='absolute';
    el.style.display='none';
    el.style.padding='0px';
    el.style.margin='0px';
    el.style.width='300px';
    el.style.zIndex=zmax+1;
    el.style.backgroundColor='#FFF';
    el.style.filter='Alpha(Opacity=96)';

    document.body.insertBefore(el,document.body.firstChild);
    _dictSet(el, h);


    el = document.createElement('div');
    el.id='_dict_status';
    if(typeof el.style == "undefined") return;
    el.style.position='absolute';
    el.style.backgroundColor='#e7f7f7';
    el.style.padding='1px';
    el.style.margin='0px';
    el.style.filter='Alpha(Opacity=80)';
    el.style.fontSize='14px';
    el.style.left = '3px';
    el.style.top = '3px';
    el.style.width='138px';
    el.style.height='22px';
    el.style.textAlign='center';
    el.style.zIndex=zmax+2;
    el.style.border = '1px solid #7E98D6';
    el.style.display='none';
    document.body.insertBefore(el,document.body.firstChild);
  }catch(x){
    _dict_init = 2;
    return;
  }
  _dictClose();


  if(document.addEventListener){
    document.addEventListener("mousemove", _dictMove, true);
    document.addEventListener("dblclick", _dictQuery, true);
    document.addEventListener("mouseup", _dictQuery, true);
    document.addEventListener("mousedown", _dictCheck, true);
    document.addEventListener("keydown", _dictKey, true);
    document.addEventListener("load", _dictUpdateStatus, true);
  }else if (document.attachEvent) {
    document.attachEvent("onmousemove", _dictMove);
    document.attachEvent("ondblclick", _dictQuery);
    document.attachEvent("onmouseup", _dictQuery);
    document.attachEvent("onmousedown", _dictCheck);
    document.attachEvent("onkeydown", _dictKey);
    document.attachEvent("onload", _dictUpdateStatus);
  }else{
    var oldmove = (document.onmousemove) ? document.onmousemove : function () {};
  	document.onmousemove =  function () {oldmove(); _dictMove();};
  	var olddblclick = (document.ondblclick) ? document.ondblclick : function () {};
    document.ondblclick = function () {olddblclick(); _dictQuery();};
    var oldmouseup = (document.onmouseup) ? document.onmouseup : function () {};
    document.onmouseup = function () {oldmouseup(); _dictQuery();};
    var oldmousedown = (document.onmousedown) ? document.onmousedown : function () {};
    document.onmousedown = function () {oldmousedown(); _dictCheck();};
    var oldkeydown = (document.onkeydown) ? document.onkeydown : function () {};
    document.onkeydown = function () {oldkeydown(); _dictKey();};
    var oldload = (document.onload) ? document.onload : function () {};
    document.onload = function () {oldload(); _dictUpdateStatus();};
  }
  _dict_oldselectstart = (document.onselectstart) ? document.onselectstart : function () {};
  document.onselectstart = function () {if(_dict_moving == 2) return false; else return true;};
  _dict_onselect = 1;
  var img = new Image();
  img.src = _dict_host+"imgs/loading.gif";
  _dict_layer = _dict_getObj('_dict_layer');
  _dict_status = _dict_getObj('_dict_status');
  _dict_iframe = _dict_getObj('_dictFrame');
  _dict_mode = 1;
  if( _dict_GetCookie("dicthuaci") == "off"){
  	_dict_enable = false;
  }
  setTimeout("_dictUpdateStatus()",1000);
  _dictUpdateStatus();
  _dict_init = 1;
}
function _dict_SetCookie(name,value,day) {
try{
    var domain = document.domain + ":";
    domain = domain.toLowerCase();
	var arydomain = new Array(".com",".com.cn",".net",".net.cn",".cc",".org",".org.cn",".gov.cn",".info",".biz",".tv",".name");
	var tmpdomain = "";
	var strdomain = "";
	for(var i=0;i<arydomain.length; i++){
	    tmpdomain = arydomain[i]+":";
	    if(domain.indexOf(tmpdomain)!=-1){
			domain = domain.replace(tmpdomain,"");
			domain = domain.substring(domain.lastIndexOf(".")+1,domain.length);
			domain = domain + tmpdomain;
			strdomain = "; domain=." + domain.replace(":","");
			break;
		}
	}
	if(domain.indexOf("dict.cn:")!=-1){
  		strdomain = "; domain=.dict.cn";
    }
    var date = new Date();
	date.setTime(date.getTime()+(day*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	document.cookie = name+"="+value+expires+"; path=/"+strdomain;
}catch(x){;}
}
function _dict_GetCookie(name)
{
    var cookie=String(document.cookie);
    var pos=cookie.indexOf(name+"=");
    if(pos!=-1){
        var end=cookie.indexOf("; ",pos);
        return cookie.substring(pos+name.length+1,end==-1?cookie.length:end);
    }
    return "";
}
function _dict_getObj(id) {
	if (document.getElementById) return document.getElementById(id);
	else if (document.all) return document.all[id];
	else if (document.layers) return document.layers[id];
	else {return null;}
}
var _dict_hexchars = "0123456789ABCDEF";
function _dict_toHex(n) {
  return _dict_hexchars.charAt(n>>4)+_dict_hexchars.charAt(n & 0xF);
}

var _dict_okURIchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
function _dict_toutf8(wide) {
  var c, s;
  var enc = "";
  var i = 0;
  while(i<wide.length) {
    c= wide.charCodeAt(i++);
    // handle UTF-16 surrogates

    if (c>=0xDC00 && c<0xE000) continue;
    if (c>=0xD800 && c<0xDC00) {
      if (i>=wide.length) continue;
      s= wide.charCodeAt(i++);
      if (s<0xDC00 || c>=0xDE00) continue;
      c= ((c-0xD800)<<10)+(s-0xDC00)+0x10000;
    }
    // output value
    if (c<0x80) enc += String.fromCharCode(c);
    else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
    else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
    else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
  }
  return enc;
}
function _dict_encodeURIComponentNew(s) {
  s = _dict_toutf8(s);
  var c;
  var enc = "";
  for (var i= 0; i<s.length; i++) {
    if (_dict_okURIchars.indexOf(s.charAt(i))==-1)
      enc += "%"+_dict_toHex(s.charCodeAt(i));
    else
      enc += s.charAt(i);
  }
  return enc;
}

function _dict_URL(w)
{
	var s = "";
	if (typeof encodeURIComponent == "function")
	{
		s = encodeURIComponent(w);
	}
	else
	{
		s = _dict_encodeURIComponentNew(w);
	}
	return s;
}
function _dictSet(el, htmlCode) {
	if(!el || 'undefined' == typeof el) return;
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('msie') >= 0 && ua.indexOf('opera') < 0) {
        el.innerHTML = '<div style="display:none">for IE</div>' + htmlCode;
        el.removeChild(el.firstChild);
    }
    else {
        var el_next = el.nextSibling;
        var el_parent = el.parentNode;
        el_parent.removeChild(el);
        el.innerHTML = htmlCode;
        if (el_next) {
            el_parent.insertBefore(el, el_next)
        } else {
            el_parent.appendChild(el);
        }
    }
}

function _dictGetSel()
{
	if (window.getSelection) return window.getSelection();
	else if (document.getSelection) return document.getSelection();
	else if (document.selection) return document.selection.createRange().text;
	else return '';
}

function _dictGetPos(event){
try{
  if(_dict_opera){
    _dict_x = event.clientX + window.pageXOffset;;
    _dict_y = event.clientY + window.pageYOffset;;
  }else if (_dict_is_ie) {
    _dict_x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    _dict_y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }else {
    _dict_x = event.clientX + window.scrollX;
    _dict_y = event.clientY + window.scrollY;
  }
}catch(x){}
  if(!_dict_isInteger(_dict_x)) _dict_x = 200;
  if(!_dict_isInteger(_dict_y)) _dict_y = 200;
}

function _dictKey(e){
_dictClose();
return true;
}
function _dictCheck(e) {
	if(window.Event){
	  	if(e.which == 2 || e.which == 3) {_dictClose(); return true;}
	}else{
	    if(event.button == 2 || event.button == 3) {_dictClose(); return true;}
	}
    var cx = 0;
    var cy = 0;
    var obj = _dict_layer;
    if (obj.offsetParent){
        while (obj.offsetParent){
            cx += obj.offsetLeft;
            cy += obj.offsetTop;
            obj = obj.offsetParent;
        }
    }else if (obj.x){
        cx += obj.x;
        cy += obj.y;
    }

 	_dictGetPos(e);
    if(_dict_moving>0){
        _dict_startx = _dict_x;
        _dict_starty = _dict_y;
        if(_dict_onmove == 1){
		   _dict_moving = 2;
        }else if(_dict_x < cx || _dict_x > (cx + 300) || _dict_y < cy || (!_dict_onlayer && _dict_y > (cy + 100) ) ){
	    	_dictClose();
        }else{
            _dict_moving = 1;
        }
    }

}

function _dictQuery(e)  {
	if(window.Event){
	  	if(e.which == 2 || e.which == 3) {_dictClose(); return true;}
	}else{
	    if(event.button == 2 || event.button == 3) {_dictClose(); return true;}
	}
    if(_dict_moving == 1){
        if (_dict_is_ie) {
            window.event.cancelBubble = true;
            window.event.returnValue = false;
        }else{
            e.preventDefault();
        }
        return false;
    }
    _dictGetPos(e);
    if(_dict_moving == 2) {
        _dict_moving = 1;
        _dict_cx = _dict_nx;
        _dict_cy = _dict_ny;
        return false;
    }

    if (!_dict_enable) return true;

    var word = _dictGetSel();
    if(document.f && document.f.q && document.f.q.value && word == document.f.q.value) return true;
    word=""+word;
    word=word.replace(/^\s*|\s*$/g,"");
    if(word == "" || word.length > 76 || _dict_old_word == word) return true;

    _dictShow(word);

}

function _dictDisplay(){
    var dx=262;
    var dy=264;
    _dict_startx = _dict_x;
    _dict_starty = _dict_y;
    _dict_y += 8;
    _dict_x += 16;
    if(_dict_opera){
    	_dict_x -= 4;
    }else if(_dict_is_ie){
        if (document.documentElement.offsetHeight && document.body.scrollTop+document.documentElement.scrollTop+document.documentElement.offsetHeight - _dict_y < dy){
            _dict_y = document.body.scrollTop+document.documentElement.scrollTop + document.documentElement.offsetHeight - dy;
            _dict_x += 14;
        }
        if (document.documentElement.offsetWidth && document.body.scrollLeft+document.documentElement.scrollLeft+document.documentElement.offsetWidth - _dict_x < dx){
            _dict_x = document.body.scrollLeft+document.documentElement.scrollLeft + document.documentElement.offsetWidth - dx;
        }
    }else{
        dx-=1;
        dy+=11;
        if (self.innerHeight && document.body.scrollTop+document.documentElement.scrollTop + self.innerHeight - _dict_y < dy) {
            _dict_y = document.body.scrollTop+document.documentElement.scrollTop + self.innerHeight - dy;
            _dict_x += 14;
        }
        if (self.innerWidth && document.body.scrollLeft+document.documentElement.scrollLeft + self.innerWidth - _dict_x < dx) {
            _dict_x = document.body.scrollLeft+document.documentElement.scrollLeft + self.innerWidth - dx;
        }
    }
    _dict_nx = _dict_cx = _dict_x;
    _dict_ny = _dict_cy = _dict_y;
    _dict_layer.style.left = _dict_nx+'px';
    _dict_layer.style.top = _dict_ny+'px';
    _dict_layer.style.filter="Alpha(Opacity=96)";
    _dict_layer.style.opacity = 0.96;
    _dict_layer.style.display = "inline";
    _dict_moving = 1;
}
function _dict_isInteger(s) {
return (s.toString().search(/^-?[0-9]+$/) == 0);
}

function dictShow(q){
	if(_dict_mode != 1){
		_dictSet(_dict_getObj('_dict_title'), '&#21010;&#35789;&#32763;&#35793; - Dict.CN');
		_dict_mode = 1;
	}
	var d = _dict_getObj('_dict_add');
	if(d){
		d.href = _dict_host + 'scb/?utf8=1&word=' + q;
		d.onclick = function(){ _dictScb(q); return false; };
	}
	d = _dict_getObj('_dict_detail');
	if(d) d.href = _dict_host + 'search.php?q='+q;
    if(_dict_moving==0)_dictDisplay();
    _dict_iframe.src = _dict_host+'mini.php?utf8=1&q='+q;
}
function _dictShow(word){
	var q = _dict_URL(word);
	if(_dict_mode != 1){
		_dictSet(_dict_getObj('_dict_title'), '&#21010;&#35789;&#32763;&#35793; - Dict.CN');
		_dict_mode = 1;
	}
	var d = _dict_getObj('_dict_add');
	if(d){
		d.href = _dict_host + 'scb/?utf8=1&word=' + q;
		d.onclick = function(){ _dictScb(q); return false; };
	}
	d = _dict_getObj('_dict_detail');
	if(d) d.href = _dict_host + 'search.php?q='+q;
    if(_dict_moving==0)_dictDisplay();
    _dict_old_word = word;
    _dict_iframe = false;
    _dict_geturl(_dict_host+'mini.php?utf8=1&q='+q,word);
}

function _dict_geturl(u,word){
    try{
    	if(_dict_frametimer){clearTimeout(_dict_frametimer);_dict_frametimer = 0;}
		if(!_dict_iframe){
			_dict_frameid ++;
			_dictSet(_dict_getObj('_dictContent'),'<iframe id="_dictFrame'+_dict_frameid+'" name="_dictFrame'+_dict_frameid+'" HEIGHT="120" src="about:blank" FRAMEBORDER="0" width="100%"></iframe>');
			_dict_iframe = _dict_getObj('_dictFrame'+_dict_frameid);
			if(!_dict_iframe){
				_dict_frametimer = setTimeout(function(){_dict_geturl(u,word)},1000);
				return;
			}
			var iframeWin = window.frames['_dictFrame'+_dict_frameid];
	        // alert(iframeWin);
	        iframeWin.document.open();
	        iframeWin.document.write('<html><body><div><span style="color:#666666;font-weight:bold;">Define </span><span style="color:green;font-weight:bold;">'+word+'</span> :<br /></div><center><img src="'+_dict_host+'imgs/loading.gif" width="80" height="62" /></center></body></html>');
	        iframeWin.document.close();
    	}
    }catch(x){
    }
    _dict_iframe.src = u;
}
function dictAdd(word,autoclose){
	autoclose = (typeof autoclose == 'undefined') ? 0 : 1;
	var q = _dict_URL(word.replace("%27","'"))
	_dictScb(q, autoclose);
}
function _dictScb(word,autoclose){
	if(word == "") return false;
	autoclose = (typeof autoclose == 'undefined') ? 0 : 1;
	if(_dict_mode != 2){
		_dictSet(_dict_getObj('_dict_title'), '&#28155;&#21152;&#29983;&#35789; - Dict.CN');
		_dict_mode = 2;
	}
	var d = _dict_getObj('_dict_add');
	if(d){
		d.href = _dict_host + 'scb/';
		d.onclick = function(){return true;};
	}
	d = _dict_getObj('_dict_detail');
	if(d) d.href = _dict_host + 'search.php?utf8=1&q='+word;
    if(_dict_moving ==0) _dictDisplay();
    if(autoclose){
    	_dict_iframe.src = _dict_host+'scb/add.php?utf8=1&autoclose=1&word='+word;
    }else{
    	_dict_iframe.src = _dict_host+'scb/add.php?utf8=1&word='+word;
    }
}

function _dictScbclose(){
	_dict_scbtimer = 0;
	if(_dict_mode==2 && _dict_moving >0){
		_dictClose();
	}
}
var _dict_addscb_fade = {
	    '_timer':false,
	    'setopacity':function(el,opaval){
	        if(opaval<0 || opaval>100 || !el)return false;
	        try{
	            el.style.filter="Alpha(Opacity="+opaval+")";
	            el.style.opacity = opaval/100;
	        }
	        catch(e){}
	        return true;
	    },
	    'fading':function(el,opacity_start,step){
	        var now = opacity_start + step;
	        if(_dict_addscb_fade.setopacity(el,now))
	            _dict_addscb_fade._timer = setTimeout(function(){_dict_addscb_fade.fading(el,now,step)},100);
	        else {
	        	_dictScbclose();
	        }
	    }
	}
function _dictMove(e){
	try{
	    if(_dict_moving==2) {
	    	_dictGetPos(e);
	        _dict_nx = _dict_x-_dict_startx+_dict_cx;
	        _dict_ny = _dict_y-_dict_starty+_dict_cy;
	        if (!_dict_opera && document.documentElement.scrollWidth && document.documentElement.scrollWidth - _dict_nx < 262) {
	            _dict_nx = document.documentElement.scrollWidth - 262;
	        }
	        if(_dict_nx<0) _dict_nx = 0;
	        if(_dict_ny<0) _dict_ny = 0;
	        _dict_layer.style.left = _dict_nx+'px';
	        _dict_layer.style.top = _dict_ny+'px';
	        _dict_layer.focus();
	        _dict_layer.blur();
	    }
    }catch (x)
    {
    }
}

function _dictClose() {
	if(_dict_addscb_fade._timer){
		clearTimeout(_dict_addscb_fade._timer);
		_dict_addscb_fade._timer=false;
	}
    try
    {
    	if(_dict_moving){
		  var scrOfY = 0;
		  if( document.body && document.body.scrollTop ) {
		    scrOfY = document.body.scrollTop;
		  } else if( document.documentElement && document.documentElement.scrollTop) {
		    scrOfY = document.documentElement.scrollTop;
		  }
	       if(scrOfY < 50 &&_dict_mode == 2 && document.f && document.f.q && document.f.q.value) document.f.q.focus();
	        _dict_moving = 0;
	        _dict_onmove = 0;
	        _dict_onlayer = 0;
	        _dict_mode = 0;
	        _dict_layer.style.display="none";
	        setTimeout(function(){_dict_old_word = "";},500);
		}
    }
    catch (x)
    {
    }

}


function _dictRemove() {
    try
    {
        _dict_moving = 0;
        _dict_onmove = 0;
        _dict_onlayer = 0;
        _dict_mode = 0;
        if(_dict_onselect){
	        document.onselectstart = _dict_oldselectstart;
	  		_dict_onselect = 0;
	  	}
    	_dict_enable = false;
    	_dict_layer.style.display="none";
		_dict_status.style.display="none";
    }
    catch (x)
    {
    }

}
function _dictDisable(){
  _dict_SetCookie("dicthuaci","off",30);
  _dict_enable = false;
  _dictUpdateStatus();
}

function _dictEnable(){
  if (_dict_enable){
	_dict_SetCookie("dicthuaci","off",30);
	_dict_enable = false;
  }else{
    _dict_enable = true;
	_dict_SetCookie("dicthuaci","",-1);
  }
  _dictUpdateStatus();
}

function dictRemove(){
  _dictRemove();
}
function dictDisable(){
  _dict_enable = false;
  _dict_SetCookie("dicthuaci","off",30);
  _dictUpdateStatus();
}

function dictEnable(){
  _dict_enable = true;
  _dict_SetCookie("dicthuaci","",-1);
  _dictUpdateStatus();
}

function _dictUpdateStatus(){
  var d = _dict_getObj('dict_status');
  if(d){
    if (_dict_enable){
       _dictSet(d,'[&#21010;&#35789;&#32763;&#35793;&nbsp;<a href="javascript:dictDisable()" title="&#25105;&#35201;&#31105;&#29992;&#21010;&#35789;&#32763;&#35793;">&#24320;&#21551;</a>]');
    }else{
	   _dictSet(d,'[&#21010;&#35789;&#32763;&#35793;&nbsp;<a href="javascript:dictEnable()" title="&#25105;&#35201;&#24320;&#21551;&#21010;&#35789;&#32763;&#35793;">&#31105;&#29992;</a>]');
    }
  }
  var h = _dict_getObj('huaci_status');
  if(h){
  	if(_dict_enable){
  		h.href = "javascript:dictDisable()";
  		// h.onclick = function() {dictDisable();return false;};
  		h.innerHTML = "&#21010;&#35789;&#30050;&#24320;";
  	}else{
	    h.href = "javascript:dictEnable()";
	    // h.onclick = function() {dictEnable();return false;};
  		h.innerHTML ="&#21010;&#35789;&#30050;&#20851;";
  	}
  }
  h = _dict_getObj('huaci0_status');
  if(h && h.tagName && h.tagName.toLowerCase() == "a"){
  	if(_dict_enable){
  		h.href = "javascript:dictDisable()";
  		// h.onclick = function() {dictDisable();return false;};
  		h.innerHTML = "&#21010;&#35789;&#30050;&#24320;";
  	}else{
	    h.href = "javascript:dictEnable()";
	    // h.onclick = function() {dictEnable();return false;};
  		h.innerHTML ="&#21010;&#35789;&#30050;&#20851;";
  	}
  }
  if(0){
  	_dict_status.style.display="inline";
  	_dictSet(_dict_status, _dictStatus());
  }
}

function _dictStatus(){
	var b='line-height:20px;background-color:#e7f7f7;font-weight:normal;padding:0px;margin:0px;font-size:14px;text-decoration:none;font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;';
    var h='<span style="color:#000000;'+b+'">[<a href="'+_dict_help+'" title="&#25105;&#35201;&#26597;&#30475;&#21010;&#35789;&#24110;&#21161;" target="_blank" style="color:#1A9100;'+b+'">&#21010;&#35789;&#32763;&#35793;</a>&#30050;';
    if (_dict_enable){
      h += '<a href="javascript:dictDisable()" title="&#25105;&#35201;&#31105;&#29992;&#21010;&#35789;&#32763;&#35793;" target="_self" style="color:#1A9100;'+b+'">&#24320;&#21551;</a>';
    }else{
      h += '<a href="javascript:dictEnable()" title="&#25105;&#35201;&#24320;&#21551;&#21010;&#35789;&#32763;&#35793;" target="_self" style="color:#1A9100;'+b+'">&#31105;&#29992;</a>';
    }
    h +='] <a href="javascript:dictRemove();" target="_self" style="'+b+'"><img src='+_dict_host+'img/close.gif border=0 align=absmiddle style="padding:0px;margin:0px;"></a>';
    return h;
}
function _dict_load(){
   if(! document || ! document.body || !document.body.firstChild){
	  if(document.addEventListener){
	    window.addEventListener("load", _dictInit, true);
	  }else if (document.attachEvent) {
	    window.attachEvent("onload", _dictInit);
	  }else{
	    var oldload = (document.onload) ? document.onload : function () {};
	    window.onload = function () {oldload(); _dictInit();};
	  }
   }else{
   	  _dictInit();
   }
}
function dictInit(){
	_dictInit();
}
if(typeof(_dict_loaded) != "string" || _dict_loaded != "yes"){
var _dict_is_ie = true;
var _dict_host = 'http://dict.cn/';
var _dict_help = "http://dict.cn/foot/help.htm";
var _dict_old_word = "";
var _dict_oldselectstart = function () {};
var _dict_onselect = 0;
var _dict_opera = 0;
var _dict_frameid = 0;
var _dict_frametimer = 0;
var _dict_scbtimer = 0;
var _dict_moving = 0;
var _dict_onmove = 0;
var _dict_onlayer = 0;
var _dict_startx = 0;
var _dict_starty = 0;
var _dict_cx = 0;
var _dict_cy = 0;
var _dict_x = 0;
var _dict_y = 0;
var _dict_nx = 0;
var _dict_ny = 0;
var _dict_enable = true;
var _dict_layer = null;
var _dict_status = null;
var _dict_iframe = null;
var _dict_mode = 0;
var _dict_init = 0;
var _dict_loaded = "yes";
_dict_load();
}else{
    try{
    _dict_enable = true;
    _dictUpdateStatus();
    if(_dict_onselect == 0){
    	document.onselectstart = function () {if (_dict_moving == 2) return false;};
  		_dict_onselect = 1;
  	}
    }catch(x){;}
}
dict_enable = false;
