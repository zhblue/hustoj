<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <?php require_once("./template/$OJ_TEMPLATE/include-header.php");?>
</head>
<body>
<?php
$navigation_tab = "problem";
require_once("./template/$OJ_TEMPLATE/contest-header.php");
?>
<div class="container">
  <div class="row text-center">
    <h3>
      <?php echo $view_title ?>
    </h3>
  </div>
<?php
if($view_title != "No Such Contest!" && $rows_cnt > 0) {
?>
  <div class="row text-center">
    <p><?php echo $view_description?></p>
    <br><span class='text-info'><?php echo $view_start_time?></span> ~ <span class='text-info'><?php echo $view_end_time?></span><br>
    서버시간 : <span class='text-info' id='nowdate'><?php echo date("Y-m-d H:i:s")?></span><br>
<?php
if ($now > $end_time) {
  echo "<span class='text-mute'>대회가 종료 되었습니다</span>";
} else if ($now < $start_time) {
  echo "<span class='text-warning'>대회 시작 전 입니다</span>";
} else {
  echo "<span class='text-danger'>대회가 진행 중 입니다</span>";
}
?>
  </div>

  <div class="col-md-12"><div class="table-responsive">
  <table id='problemset' class='table table-hover table-striped'>
    <thead>
      <tr>
        <th width='5'></th>
        <th ><?php echo $MSG_PROBLEM_ID?></th>
        <th width='60%'><?php echo $MSG_TITLE?></th>
        <th ><?php echo $MSG_AC?></th>
        <th ><?php echo $MSG_SUBMIT?></th>
      </tr>
    </thead>
    <tbody>
<?php 
$PID = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
foreach($view_problemset as $row){
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
<?php
}
?>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
<script>
var diff=new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
//alert(diff);
function clock()
{
  var x,h,m,s,n,xingqi,y,mon,d;
  var x = new Date(new Date().getTime()+diff);
  y = x.getYear()+1900;
  if (y>3000) y-=1900;
  mon = x.getMonth()+1;
  d = x.getDate();
  xingqi = x.getDay();
  h=x.getHours();
  m=x.getMinutes();
  s=x.getSeconds();

  n=y+"-"+mon+"-"+d+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);
  //alert(n);
  document.getElementById('nowdate').innerHTML=n;
  setTimeout("clock()",1000);
}
clock();
</script>
</html>

