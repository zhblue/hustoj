<?php $show_title="$MSG_SUBMIT - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

  <style>
#source {
    width: 80%;
    height: 600px;
}

.ace-chrome .ace_marker-layer .ace_active-line{   /*当前行*/
   background-color: rgba(0,0,199,0.3);
}
            .button, input, optgroup, select, textarea {  /*选择题的题号大小*/
    font-family: sans-serif;
    font-size: 150%;
    line-height: 1.2;

}
  </style>
    
<center>

<script src="<?php echo $OJ_CDN_URL?>include/checksource.js"></script>
<form id=frmSolution action="submit.php<?php if (isset($_GET['spa'])) echo "?spa" ?>" method="post" onsubmit='do_submit()' enctype="multipart/form-data" >
<?php if (!isset($_GET['spa']) || $solution_name ) {?>
        <input type='file' name='answer' placeholder='Upload answer file' >
<?php } ?>

<?php if (isset($id)){?>
<span style="color:#0000ff">Problem <b><?php echo $id?></b></span>
<input id=problem_id type='hidden' value='<?php echo $id?>' name="id" >
<?php }else{
//$PID="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//if ($pid>25) $pid=25;
?>
Problem <span class=blue><b><?php echo chr($pid+ord('A'))?></b></span> of Contest <span class=blue><b><?php echo $cid?></b></span>
<input id="cid" type='hidden' value='<?php echo $cid?>' name="cid">
<input id="pid" type='hidden' value='<?php echo $pid?>' name="pid">
<?php }?>
<span id="language_span">Language: 
<select id="language" name="language" onChange="reloadtemplate($(this).val());" >
<?php
$lang_count=count($language_ext);
if(isset($_GET['langmask']))
	$langmask=$_GET['langmask'];
$langmask|=$OJ_LANGMASK;

$lang=(~((int)$langmask))&((1<<($lang_count))-1);
//$lastlang=$_COOKIE['lastlang'];
//if($lastlang=="undefined") $lastlang=1;
for($i=0;$i<$lang_count;$i++){
if($lang&(1<<$i))
echo"<option value=$i ".( $lastlang==$i?"selected":"").">
".$language_name[$i]."
</option>";
}
?>
</select>
<?php if($OJ_VCODE){?>
<?php echo $MSG_VCODE?>:
<input name="vcode" size=4 type=text autocomplete=off ><img id="vcode" alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()">
<?php }?>
<button  id="Submit" type="button" class="ui primary icon button"  onclick="do_submit();"><?php echo $MSG_SUBMIT?></button>
<?php if (isset($OJ_ENCODE_SUBMIT)&&$OJ_ENCODE_SUBMIT){?>
<input class="btn btn-success" title="WAF gives you reset ? try this." type=button value="Encoded <?php echo $MSG_SUBMIT?>"  onclick="encoded_submit();">
<input type=hidden id="encoded_submit_mark" name="reverse2" value="reverse"/>
<?php }?>
<!--选择题状态-->
<?php if ($spj>1 || !$OJ_TEST_RUN ){?>
<span class="btn" id=result><?php echo $MSG_STATUS?></span>	
<?php }?>
</span>
<?php if($spj <= 1 &&  !$solution_name){ ?>
    <button onclick="toggleTheme(event)" style="background-color: bisque; position: absolute; top: 5px; right:70px;" v-if="false">
        <i>🌗</i>
    </button>
    <button onclick="increaseFontSize(event)" style="background-color: bisque; position: absolute; top: 5px; right:40px;" v-if="false">
        <i>➕</i>
    </button>
    <button onclick="decreaseFontSize(event)" style="background-color: bisque; position: absolute; top: 5px; right:10px;" v-if="false">
        <i>➖</i>
    </button>
<?php } ?>

<?php 
        if(!$solution_name){
                if($OJ_ACE_EDITOR){
                        if (isset($OJ_TEST_RUN)&&$OJ_TEST_RUN)
                                $height="400px";
                        else
                                $height="500px";
                ?>
                <pre style="width:90%;height:<?php echo $height?>" cols=180 rows=16 id="source"><?php echo htmlentities($view_src,ENT_QUOTES,"UTF-8")?></pre>
                <input type=hidden id="hide_source" name="source" value=""/>

        <?php }else{ ?>
                <textarea style="width:80%;height:600" cols=180 rows=30 id="source" name="source"><?php echo htmlentities($view_src,ENT_QUOTES,"UTF-8")?></textarea>
        <?php }

        }else{
                echo "<br><h2>指定上传文件：$solution_name</h2>";

        }

	?>
<style>
            .button, input, optgroup, select, textarea {
    font-family: sans-serif;
    font-size: 150%;
    line-height: 1.15;
    margin: 0;
    background: border-box;
}
        </style>
         <div class="row">
            <div class="column" style="display: flex;">
<?php if ( isset($OJ_TEST_RUN) && $OJ_TEST_RUN && $spj<=1 && !$solution_name  ){?>
<div style="
   
     margin-left: 60px;
    width: 40%;
     padding: 14px;
    flex-direction: column;">
        <div style="  
        display: flex;
   
    border-radius: 8px;
    
    background-color: rgb(255,255,255,0.4);" id="language_span"><?php echo $MSG_Input?></div>
         <textarea style="width:100%" cols=40 rows=5 id="input_text" name="input_text" ><?php echo $view_sample_input?></textarea>
    </div>
    <div style="
   
    width: 40%;
    flex-direction: column;
    ">
         <div style="    display: flex;
   
    border-radius: 8px;
    background-color: rgb(255,255,255,0.4);justify-content: space-between;" id="language_span"><?php echo $MSG_Output ?>
    
   <span class="btn" id=result><?php echo $MSG_STATUS?></span>	
    
    </div>


          <textarea style="
          width:100%;background-color: white;
          " cols=10 rows=5 id="out" name="out" disabled="true" placeholder='<?php echo htmlentities($view_sample_output,ENT_QUOTES,'UTF-8')?>' ></textarea>    
     </div>
<?php } ?>
<?php if (isset($OJ_TEST_RUN)&&$OJ_TEST_RUN && $spj<=1 && !$solution_name  ){?>
        <!--运行按钮-->
            <input style="
             margin-top: 30px;
            margin-left: 0 auto;
            width: 7%;background-color: #22ba46a3;border-color: #00fff470;height: 130px;
            " id="TestRun" class="btn btn-info" type=button value="<?php echo $MSG_TR?>" onclick=do_test_run();>
            
            <?php }?>
            
        </div>
 </div>
        </div>
         <input type="hidden" value="0" id="problem_id" name="problem_id"/>
    </form>
<?php if (isset($OJ_BLOCKLY)&&$OJ_BLOCKLY){?>
	<input id="blockly_loader" type=button class="btn" onclick="openBlockly()" value="<?php echo $MSG_BLOCKLY_OPEN?>" style="color:white;background-color:rgb(169,91,128)">
	<input id="transrun" type=button  class="btn" onclick="loadFromBlockly() " value="<?php echo $MSG_BLOCKLY_TEST?>" style="display:none;color:white;background-color:rgb(90,164,139)">
<div id="blockly" class="center">Blockly</div>
<?php }?> 
</form>
</center>

<script>
var sid=0;
var i=0;
var using_blockly=false;
var judge_result=[<?php
foreach($judge_result as $result){
    echo "'$result',";
}
?>''];
function print_result(solution_id){
	sid=solution_id;
	$("#out").load("status-ajax.php?tr=1&solution_id="+solution_id);
}
function fancy(td){
        $("body",parent.document).append("<div id='bannerFancy' style='position:absolute;top:0px;left:0px;width:100%;z-index:3' class='ui main container'></div><audio autoplay=\"autoplay\" preload=\"auto\" src=\"<?php echo $OJ_FANCY_MP3 ?>\"> </audio>");
        window.setTimeout("$(\"#bannerFancy\",parent.document).html(\"<iframe border=0 src='fancy.php' width='100%' height='800px'></iframe>\");",500);
        window.setTimeout("$(\"#bannerFancy\",parent.document).remove();",10000);
}
function fresh_result(solution_id)
{
	var tb=window.document.getElementById('result');
	if(solution_id==undefined){
		tb.innerHTML="Vcode Error!";		
		if($("#vcode")!=null) $("#vcode").click();
		return ;
	}
	
	sid=parseInt(solution_id);
        if(sid<=0){
                tb.innerHTML="<?php echo  str_replace("10",$OJ_SUBMIT_COOLDOWN_TIME,$MSG_BREAK_TIME) ?>";
                if($("#vcode")!=null) $("#vcode").click();
                return ;
        }

	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var r=xmlhttp.responseText;
			var ra=r.split(",");
			// alert(r);
			// alert(judge_result[r]);
			var loader="<img width=18 src=image/loader.gif>";
			var tag="span";
			if(ra[0]<4) tag="span disabled=true";
			else tag="a";
			{
				if(ra[0]==11||ra[0]>15)
					tb.innerHTML="<"+tag+" href='ceinfo.php?sid="+solution_id+"' class='badge badge-info' target=_blank>"+judge_result[ra[0]]+"</"+tag+">";
				else
					tb.innerHTML="<"+tag+" href='reinfo.php?sid="+solution_id+"' class='badge badge-info' target=_blank>"+judge_result[ra[0]]+"AC:"+ra[4]+"</"+tag+">";
			}
			if(ra[0]<4||ra[0]>15)
				tb.innerHTML+=loader;
			tb.innerHTML+="Memory:"+ra[1]+"&nbsp;&nbsp;";
			tb.innerHTML+="Time:"+ra[2]+"";
			if(ra[0]<4||ra[0]>15)
				window.setTimeout("fresh_result("+solution_id+")",2000);
			else{
				window.setTimeout("print_result("+solution_id+")",2000);
				count=1;
			}
			<?php if ( $OJ_FANCY_RESULT ) {?>
				if(ra[0]==4) fancy(tb);
			<?php } ?>
		}
	}
	xmlhttp.open("GET","status-ajax.php?solution_id="+solution_id,true);
	xmlhttp.send();
}
function getSID(){
var ofrm1 = document.getElementById("testRun").document;
var ret="0";
if (ofrm1==undefined)
{
ofrm1 = document.getElementById("testRun").contentWindow.document;
var ff = ofrm1;
ret=ff.innerHTML;
}
else
{
var ie = document.frames["frame1"].document;
ret=ie.innerText;
}
return ret+"";
}
var count=0;
 
function encoded_submit(){

      var mark="<?php echo isset($id)?'problem_id':'cid';?>";
        var problem_id=document.getElementById(mark);

	if(typeof(editor) != "undefined")
		$("#hide_source").val(editor.getValue());
        if(mark=='problem_id')
                problem_id.value='<?php if(isset($id)) echo $id?>';
        else
                problem_id.value='<?php if(isset($cid))echo $cid?>';

        document.getElementById("frmSolution").target="_self";
        document.getElementById("encoded_submit_mark").name="encoded_submit";
        var source=$("#source").val();
	if(typeof(editor) != "undefined") {
		source=editor.getValue();
        	$("#hide_source").val(encode64(utf16to8(source)));
	}else{
        	$("#source").val(encode64(utf16to8(source)));
	}
//      source.value=source.value.split("").reverse().join("");
//      alert(source.value);
        document.getElementById("frmSolution").submit();
}

function do_submit(){
	 $("#Submit").attr("disabled","true");   // mouse has a bad key1
	if(using_blockly) 
		 translate();
	if(typeof(editor) != "undefined"){ 
		$("#hide_source").val(editor.getValue());
	}
	var mark="<?php echo isset($id)?'problem_id':'cid';?>";
	var problem_id=document.getElementById(mark);
	if(mark=='problem_id')
	problem_id.value='<?php if (isset($id))echo $id?>';
	else
	problem_id.value='<?php if (isset($cid))echo $cid?>';
	document.getElementById("frmSolution").target="_self";
	
<?php if(isset($_GET['spa'])){?>
	<?php if($solution_name) { ?>document.getElementById("frmSolution").submit(); <?php } ?>  //如果是指定文件名，则强制用文件post方式提交。
        $.post("submit.php?ajax",$("#frmSolution").serialize(),function(data){fresh_result(data);});
        $("#Submit").prop('disabled', true);
        $("#TestRun").prop('disabled', true);
        count=<?php echo $OJ_SUBMIT_COOLDOWN_TIME?> * 2 ;
        handler_interval= window.setTimeout("resume();",1000);
	 <?php if(isset($OJ_REMOTE_JUDGE)&&$OJ_REMOTE_JUDGE) {?>$("#sk").attr("src","remote.php"); <?php } ?>
<?php }else{?>
        document.getElementById("frmSolution").submit();
<?php }?>

}
var handler_interval;
function do_test_run(){
	if( handler_interval) window.clearInterval( handler_interval);
	var loader="<img width=18 src=image/loader.gif>";
	var tb=window.document.getElementById('result');
        var source=$("#source").val();
	if(typeof(editor) != "undefined") {
		source=editor.getValue();
        	$("#hide_source").val(source);
	}
	if(source.length<10) return alert("too short!");
	if(tb!=null)tb.innerHTML=loader;

	var mark="<?php echo isset($id)?'problem_id':'cid';?>";
	var problem_id=document.getElementById(mark);
	problem_id.value=-problem_id.value;
	document.getElementById("frmSolution").target="testRun";
	//$("#hide_source").val(editor.getValue());
	//document.getElementById("frmSolution").submit();
	$.post("submit.php?ajax",$("#frmSolution").serialize(),function(data){fresh_result(data);});
  	$("#Submit").prop('disabled', true);
  	$("#TestRun").prop('disabled', true);
	problem_id.value=-problem_id.value;
	count=<?php echo isset($OJ_SUBMIT_COOLDOWN_TIME)?$OJ_SUBMIT_COOLDOWN_TIME:5  ?> * 2 ;
	handler_interval= window.setTimeout("resume();",1000);
}
function resume(){
	count--;
	var s=$("#Submit")[0];
	var t=$("#TestRun")[0];
	if(count<0){
		 $("#Submit").attr("disabled",false);
		 $("#Submit").text("<?php echo $MSG_SUBMIT?>");
		if(t!=null) $("#TestRun").attr("disabled",false);
		if(t!=null) $("#TestRun").text("<?php echo $MSG_TR?>");
		if( handler_interval) window.clearInterval( handler_interval);
		if($("#vcode")!=null) $("#vcode").click();
	}else{
		 $("#Submit").text("<?php echo $MSG_SUBMIT?>("+count+")");
		if(t!=null)t.value="<?php echo $MSG_TR?>("+count+")";
		window.setTimeout("resume();",1000);
	}
}
function switchLang(lang){
   var langnames=new Array("c_cpp","c_cpp","pascal","java","ruby","sh","python","php","perl","csharp","objectivec","vbscript","scheme","c_cpp","c_cpp","lua","javascript","golang","sql","fortran","matlab","cobol","r");
   editor.getSession().setMode("ace/mode/"+langnames[lang]);

}
function reloadtemplate(lang){
   if(lang==undefined){
        switchLang(1);
           return;
   }
   console.log("reload:<?php echo $lastlang?> -->"+lang);
   document.cookie="lastlang="+lang;
   var url=window.location.href;
   var i=url.indexOf("sid=");
   switchLang(lang);
   if(lang!=<?php echo $lastlang?>)
        document.location.href=url;
}

function openBlockly(){
   $("#source").hide();
   $("#TestRun").hide();
   $("#language")[0].scrollIntoView();
   $("#language").val(6).hide();
   //$("#language_span").hide();
   $("#EditAreaArroundInfos_source").hide();
   $('#blockly').html('<iframe name=\'frmBlockly\' width=90% height=580 src=\'blockly/demos/code/index.html\'></iframe>'); 
  $("#blockly_loader").hide();
  $("#transrun").show();
  //$("#Submit").prop('disabled', true);
  using_blockly=true;
  
}
function translate(){
  var blockly=$(window.frames['frmBlockly'].document);
  var tb=blockly.find('td[id=tab_python]');
  var python=blockly.find('pre[id=content_python]');
  tb.click();
  blockly.find('td[id=tab_blocks]').click();
  if(typeof(editor) != "undefined") editor.setValue(python.text());
  else $("#source").val(python.text());
  $("#language").val(6);
 
}
function loadFromBlockly(){
 translate();
 do_test_run();
  $("#frame_source").hide();
//  $("#Submit").prop('disabled', false);
}
</script>
<script language="Javascript" type="text/javascript" src="<?php echo $OJ_CDN_URL?>include/base64.js"></script>
<?php if (!empty($remote_oj)){
                echo "<iframe src=remote.php height=0px width=0px ></iframe>";
      }
?>
<?php if($OJ_ACE_EDITOR){ ?>
<script src="<?php echo $OJ_CDN_URL?>ace/ace.js"></script>
<script src="<?php echo $OJ_CDN_URL?>ace/ext-language_tools.js"></script>
<script>
    ace.require("ace/ext/language_tools");
    var editor = ace.edit("source");
    editor.setTheme("ace/theme/xcode");
    switchLang(<?php echo $lastlang ?>);
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true,  //改为true,打开自动补齐功能，改为false关闭
        // fontFamily: "Consolas",  // MacOS missing align
	// theme: "ace/theme/ambiance",   // Black theme
        fontSize: "18px"
    });
   reloadtemplate($("#language").val()); 
   function autoSave(){
        var mark="<?php echo isset($id)?'problem_id':'cid';?>";
        var problem_id=$("#"+mark).val();
	if(!!localStorage){
		 let key="<?php echo $_SESSION[$OJ_NAME.'_user_id']?>source:"+location.href;
		if(typeof(editor) != "undefined")
			$("#hide_source").val(editor.getValue());
		localStorage.setItem(key,$("#hide_source").val());
		//console.log("autosaving "+key+"..."+new Date());
	}
   }
   $(document).ready(function(){
   	$("#source").css("height",window.innerHeight-180);  
	if($("#vcode")!=undefined) $("#vcode").click();
	if(!!localStorage){
		let key="<?php echo $_SESSION[$OJ_NAME.'_user_id']?>source:"+location.href;
		let saved=localStorage.getItem(key);
		   if(saved!=null&&saved!=""&&saved.length>editor.getValue().length){
                        //let load=confirm("发现自动保存的源码，是否加载？（仅有一次机会）");
                        //if(load){
                                console.log("loading "+saved.length);
                                if(typeof(editor) != "undefined")
                                        editor.setValue(saved);
                        //}
                }

	}
	if(typeof(editor) != "undefined") editor.resize();
	window.setInterval('autoSave();',5000);
	$("body").dblclick(function(){
                 if (event.ctrlKey==1) formatCode();
        }).attr("title","Ctrl+双击鼠标,自动整理缩进");
   });
</script>
<script>
    function increaseFontSize(event) {
        event.preventDefault();
        var currentSize = parseInt(editor.getFontSize());
        editor.setFontSize(currentSize + 3);
    }

    function decreaseFontSize(event) {
        event.preventDefault();
        var currentSize = parseInt(editor.getFontSize());
        editor.setFontSize(currentSize - 3);
    }
   function toggleTheme(event) {
    event.preventDefault();
    
    if (editor.getTheme() === "ace/theme/xcode") {
        editor.setTheme("ace/theme/monokai");
    } else {
        editor.setTheme("ace/theme/xcode");
    }
}

	var level = 0;
var LOOP_SIZE = 100;
function finishTabifier(code) {
    code = code.replace(/\n\s*\n/g, '\n');  //blank lines
    code = code.replace(/^[\s\n]*/, ''); //leading space
    code = code.replace(/[\s\n]*$/, ''); //trailing space
    level = 0;
    var session = editor.getSession();
    session.setValue(code);
    return code;
}

function cleanCStyle(code) {
    var i = 0;
    code = code.replace(/\)\n[\s]*/g,')\n    '); //single line if while for
    function cleanAsync() {
        var iStart = i;
        for (; i < code.length && i < iStart + LOOP_SIZE; i++) {
            c = code.charAt(i);

            if (incomment) {
                if ('//' == incomment && '\n' == c) {
                    incomment = false;
                } else if ('/*' == incomment && '*/' == code.substr(i, 2)) {
                    incomment = false;
                    c = '*/\n';
                    i++;
                }
                if (!incomment) {
                    while (code.charAt(++i).match(/\s/)); ; i--;
                    c += tabs();
                }
                out += c;
            } else if (instring) {
                if (instring == c && // this string closes at the next matching quote
                // unless it was escaped, or the escape is escaped
          ('\\' != code.charAt(i - 1) || '\\' == code.charAt(i - 2))
        ) {
                    instring = false;
                }
                out += c;
            } else if (infor && '(' == c) {
                infor++;
                out += c;
            } else if (infor && ')' == c) {
                infor--;
                out += c;
            } else if ('else' == code.substr(i, 4)) {
                out = out.replace(/\s*$/, '') + ' e';
            } else if (code.substr(i).match(/^for\s*\(/)) {
                infor = 1;
                out += 'for (';
                while ('(' != code.charAt(++i)); ;
            } else if ('//' == code.substr(i, 2)) {
                incomment = '//';
                out += '//';
                i++;
            } else if ('/*' == code.substr(i, 2)) {
                incomment = '/*';
                out += '\n' + tabs() + '/*';
                i++;
            } else if ('"' == c || "'" == c) {
                if (instring && c == instring) {
                    instring = false;
                } else {
                    instring = c;
                }
                out += c;
            } else if ('{' == c) {
                level++;
                out = out.replace(/\s*$/, '') + ' {\n' + tabs();
                while (code.charAt(++i).match(/\s/)); ; i--;
            } else if ('}' == c) {
                out = out.replace(/\s*$/, '');
                level--;
                out += '\n' + tabs() + '}\n' + tabs();
                while (code.charAt(++i).match(/\s/)); ; i--;
            } else if (';' == c && !infor) {
                out += ';\n' + tabs();
                while (code.charAt(++i).match(/\s/)); ; i--;
            } else if ('\n' == c) {
                out += '\n' + tabs();
            } else {
                out += c;
            }
        }

        if (i < code.length) {
            setTimeout(cleanAsync, 0);
        } else {
            level = li;
            out = out.replace(/[\s\n]*$/, '');
            finishTabifier(out);
        }
    }

    code = code.replace(/^[\s\n]*/, ''); //leading space
    code = code.replace(/[\s\n]*$/, ''); //trailing space
    code = code.replace(/[\n\r]+/g, '\n'); //collapse newlines

    var out = tabs(), li = level, c = '';
    var infor = false, forcount = 0, instring = false, incomment = false;
    cleanAsync();
}
function tabs() {
    var s = '';
    for (var j = 0; j < level; j++) s += '\t';
    return s;
}
// Functions
function formatCode() {
  var session = editor.getSession();
  cleanCStyle(session.getValue());
}



</script>
<?php }?>

  </body>
</html>
<?php //include("template/$OJ_TEMPLATE/footer.php");?>
