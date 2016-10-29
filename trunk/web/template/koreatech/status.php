<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv='refresh' content='60'>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
  $navigation_tab = "status";
if ($isContest) {
  require_once("./template/$OJ_TEMPLATE/contest-header.php");
} else {
  require_once("./template/$OJ_TEMPLATE/oj-header.php");
}
?>
  <div class="container">
    <div class="col-md-12 text-center">
    <form id="simform" class="form-inline" action="status.php" method="get">
      <div class="form-group">
        <input class="form-control" type="text" name="problem_id" placeholder="<?php echo $MSG_PROBLEM_ID?>" value="<?php echo $problem_id?>">
        <input class="form-control" type="text" name="user_id" placeholder="<?php echo $MSG_USER?>" value="<?php echo $user_id?>">
        <?php if (isset($cid)) echo "<input type='hidden' name='cid' value='$cid'>";?>
        <select class="form-control"  name="language">
<?php
  if (isset($_GET['language'])) $language=$_GET['language'];
  else $language=-1;
  if ($language<0||$language>=count($language_name)) $language=-1;
  if ($language==-1) echo "<option value='-1' selected>모든언어</option>";
  else echo "<option value='-1'>모든언어</option>";
  $i=0;
  foreach ($language_name as $lang){
    if ($i==$language)
      echo "<option value=$i selected>$language_name[$i]</option>";
    else
      echo "<option value=$i>$language_name[$i]</option>";
    $i++;
  }
?>
        </select>
        <select class="form-control" name="jresult">
<?php
  if (isset($_GET['jresult'])) $jresult_get=intval($_GET['jresult']);
  else $jresult_get=-1;
  if ($jresult_get>=12||$jresult_get<0) $jresult_get=-1;
  if ($jresult_get==-1) echo "<option value='-1' selected>모든결과</option>";
  else echo "<option value='-1'>모든결과</option>";
  for ($j=0;$j<12;$j++){
    $i=($j+4)%12;
    if ($i==$jresult_get) echo "<option value='".strval($jresult_get)."' selected>".$jresult[$i]."</option>";
    else echo "<option value='".strval($i)."'>".$jresult[$i]."</option>";
  }
?>
        </select>
        <button type="submit" class="btn"><?php echo $MSG_SEARCH;?></button>
      </div>
    </form>
    </div>
<div class="col-md-12"><div class="table-responsive">
<table id="result-tab" class="table table-hover table-striped">
  <thead>
    <tr>
      <th><?php echo $MSG_RUNID?></th>
      <th><?php echo $MSG_USER?></th>
      <th><?php echo $MSG_PROBLEM?></th>
      <th><?php echo $MSG_RESULT?></th>
      <th><?php echo $MSG_MEMORY?></th>
      <th><?php echo $MSG_TIME?></th>
      <th><?php echo $MSG_LANG?></th>
      <th><?php echo $MSG_CODE_LENGTH?></th>
      <th><?php echo $MSG_SUBMIT_TIME?></th>
    </tr>
  </thead>
  <tbody>
<?php
foreach($view_status as $row){
  echo "<tr>";
  foreach($row as $table_cell){
    echo "<td>";
    echo "\t".$table_cell;
    echo "</td>";
  }
  echo "</tr>";
}
?>
  </tbody>
</table></div></div>
<div class="col-md-12">
<nav>
  <ul class="pager">
<?php
if ($prev_top != -1) {
  echo "<li class='previous'><a href=status.php?".$str2."&top=".$prev_top.">Previous Page</a></li>";
} else {
  echo "<li class='previous disabled'><a href=#>Previous Page</a></li>";
}
echo "<li class=''><a href=status.php?".$str2.">Top</a></li>";
echo "<li class='next'><a href=status.php?".$str2."&top=".$bottom.">Next Page</a></li>";
?>
</nav>
</div>
</div>


<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
<script type="text/javascript">
var i=0;
var judge_result=[<?php
foreach($judge_result as $result){
  echo "'$result',";
}
?>''];
function auto_refresh(){
  var tb=window.document.getElementById('result-tab');
  var rows=tb.rows;
  for(var  i=1;i<rows.length;i++){
    var cell=rows[i].cells[3].innerHTML;
    var sid=rows[i].cells[0].innerHTML;
    if(cell.indexOf(judge_result[0])!=-1||cell.indexOf(judge_result[2])!=-1||cell.indexOf(judge_result[3])!=-1){
      fresh_result(sid);
    }
  }
}
function findRow(solution_id){
  var tb=window.document.getElementById('result-tab');
  var rows=tb.rows;

  for(var i=1;i<rows.length;i++){
    var cell=rows[i].cells[0];
    if(cell.innerHTML==solution_id) return rows[i];
      }
}

function fresh_result(solution_id)
{
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
    var tb=window.document.getElementById('result-tab');
    var row=findRow(solution_id);
    var r=xmlhttp.responseText;
    var ra=r.replace(/[\n\r]/g,"").split(",");
    var loader=" <img width=18 src=image/loader.gif>";
    row.cells[3].innerHTML="<span class='text-mute'>"+judge_result[ra[0]]+"</span>"+loader;
    row.cells[4].innerHTML=ra[1];
    row.cells[5].innerHTML=ra[2];
    if(ra[0]<4)
      window.setTimeout("fresh_result("+solution_id+")",2000);
    else
      window.location.reload();

    }
  }
xmlhttp.open("GET","status-ajax.php?solution_id="+solution_id,true);
xmlhttp.send();
}
//<?php if ($last>0&&$_SESSION['user_id']==$_GET['user_id']) echo "fresh_result($last);";?>

auto_refresh();
</script>

</html>

