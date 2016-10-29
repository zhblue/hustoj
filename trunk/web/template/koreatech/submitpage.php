<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
$navigation_tab = "problem";
if (isset($_GET['id'])) {
  require_once("./template/$OJ_TEMPLATE/oj-header.php");
} else {
  require_once("./template/$OJ_TEMPLATE/contest-header.php");
}
?>
<div class="container">
<?php
$problem_tab = "submitpage";
require_once("./template/$OJ_TEMPLATE/problem-header.php");

$OJ_EDITE_AREA=false;

?>

<form id="frmSolution" action="submit.php" method="post">
<?php if (isset($id)){?>
<input id=problem_id type='hidden'  value='<?php echo $id?>' name="id"><br>
<?php }else{
?>
<input id="cid" type='hidden' value='<?php echo $cid?>' name="cid">
<input id="pid" type='hidden' value='<?php echo $pid?>' name="pid"><br>
<?php }?>
<select id="language" name="language" class="form-control">
<?php
  $lang_count=count($language_ext);

  if(isset($_GET['langmask']))
    $langmask=$_GET['langmask'];
  else
    $langmask=$OJ_LANGMASK;
  $lang=(~((int)$langmask))&((1<<($lang_count))-1);
  if(isset($_COOKIE['lastlang'])) $lastlang=$_COOKIE['lastlang'];
  else $lastlang=0;
  for($i=0;$i<$lang_count;$i++){
    if($lang&(1<<$i))
      echo"<option value=$i ".( $lastlang==$i?"selected":"").">".$language_name[$i]."</option>";
  }
?>
</select>
<br>
<div id="source"></div>
<textarea name="source"><?php echo $view_src?></textarea><br>
<div class="text-right">
<button id="Submit" class="btn btn-primary" onclick=do_submit();><?php echo $MSG_SUBMIT?></button>
</div>
<?php echo $MSG_Input?>
<textarea class="form-control col-md-12" rows="5" id="input_text" name="input_text" ><?php echo $view_sample_input?></textarea>
<br>
<div class="text-right">
<button id="TestRun" class="btn btn-default"  type="button" onclick=do_test_run();><?php echo $MSG_TEST_RUN?></button>
</div>
<?php echo $MSG_Output?> <span  class="text"  id=result></span>
  <textarea class="form-control col-md-12" rows="5" id="out" name="out" >SHOULD BE:
<?php echo $view_sample_output?>
</textarea>
</form>

<iframe name=testRun width=0 height=0 src="about:blank" style="visibility: hidden"></iframe>
  <script>
  var sid=0;
  var i=0;
  var judge_result=[<?php
  foreach($judge_result as $result){
    echo "'$result',";
  }
?>''];

function print_result(solution_id) {
  sid=solution_id;
  $("#out").load("status-ajax.php?tr=1&solution_id="+solution_id);
}

function fresh_result(solution_id) {
  sid=solution_id;
  var xmlhttp;
  // code for IE7+, Firefox, Chrome, Opera, Safari
  if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
  } else {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      var tb=window.document.getElementById('result');
      var r=xmlhttp.responseText;
      var ra=r.replace(/[\n\r]/g,"").split(",");
      var loader="<img width=18 src=image/loader.gif>";  
      var tag="span";
      if(ra[0]<4) tag="span disabled=true";
      else tag="a";
      tb.innerHTML="<"+tag+" href='reinfo.php?sid="+solution_id+"' class='badge badge-info' target=_blank>"+judge_result[ra[0]]+"</"+tag+"> ";
      if(ra[0]<4)tb.innerHTML+=loader;
      tb.innerHTML+=" Memory:"+ra[1]+"kb&nbsp;&nbsp;";
      tb.innerHTML+="Time:"+ra[2]+"ms";
      if(ra[0]<4)
        window.setTimeout("fresh_result("+solution_id+")",2000);
      else
        window.setTimeout("print_result("+solution_id+")",2000);
    }
  }
  xmlhttp.open("GET","status-ajax.php?solution_id="+solution_id,true);
  xmlhttp.send();
}

var count=0;
function do_submit(){
  if (typeof(eAL) != "undefined") {
    eAL.toggle("source");eAL.toggle("source");
  }

  var mark="<?php echo isset($id)?'problem_id':'cid';?>";
  var problem_id=document.getElementById(mark);

  if (mark=='problem_id') {
    problem_id.value='<?php echo $id?>';
  } else {
    problem_id.value='<?php echo $cid?>';
  }

  document.getElementById("frmSolution").target="_self";
  document.getElementById("frmSolution").submit();
}

var handler_interval;
function do_test_run(){
  if (handler_interval) {
    return;
  }
  var loader="<img width=18 src=image/loader.gif>";
  var tb=window.document.getElementById('result');
  tb.innerHTML=loader;
  if (typeof(eAL) != "undefined") {
    eAL.toggle("source");
    eAL.toggle("source");
  }

  var mark="<?php echo isset($id)?'problem_id':'cid';?>";
  var problem_id=document.getElementById(mark);
  problem_id.value=0;
  document.getElementById("frmSolution").target="testRun";
  document.getElementById("frmSolution").submit();
  document.getElementById("TestRun").disabled = true;
  document.getElementById("Submit").disabled = true;
  count = 20;
  handler_interval = window.setTimeout("resume();",1000);
}

function resume(){
  count--;
  var s=document.getElementById('Submit');
  var t=document.getElementById('TestRun');
  if(count<0){
    s.disabled=false;
    t.disabled=false;
    s.textContent="<?php echo $MSG_SUBMIT?>";
    t.textContent="<?php echo $MSG_TR?>";
    if (handler_interval) {
      window.clearInterval(handler_interval);
    }
    handler_interval = false;
  } else {
    s.textContent="<?php echo $MSG_SUBMIT?> ("+count+")";
    t.textContent="<?php echo $MSG_TR?> ("+count+")";
    handler_interval = window.setTimeout("resume();",1000);
  }
}
</script>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
<script src="/ace/src/ace.js"></script>
<script>
  var editor = ace.edit("source");
  var textarea = $('textarea[name="source"]').hide();
  editor.getSession().setValue(textarea.val());
  editor.getSession().on('change', function() {
    textarea.val(editor.getSession().getValue());
  });
  editor.setTheme("ace/theme/monokai");
  editor.setOptions({
    "minLines" : 20,
    "maxLines" : Infinity,
    "fontSize" : 14
  });
  editor.commands.removeCommand('find');
  editor.commands.removeCommand('replace');
  var changeMode = function() {
    var language = $("#language option:selected").text().toLowerCase();
    if (language == "c++" || language == "c") language = "c_cpp";
    editor.getSession().setMode("ace/mode/" + language);
  };
  $("#language").change(changeMode);
  changeMode();
</script>
</body>
</html>
