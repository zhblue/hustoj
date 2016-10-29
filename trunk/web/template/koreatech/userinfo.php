<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php require_once("oj-header.php");?>

<div class="container">
<table class="table table-striped" id="statics">
<caption>
<?php echo "<h2>$user <small><a href=mail.php?to_user=$user>$MSG_MAIL</a></small></h2>";?>
<blockquote>
<?php
  if (empty($nick)) {
    echo "한마디가 없습니다";
  } else {
    echo htmlspecialchars($nick);
  }
?>
</blockquote>
</caption>
<tr><td width=15%><?php echo $MSG_Number?><td width=25% align=center><?php echo $Rank?><td width=70% align=center>Solved Problems List</tr>
<tr><td><?php echo $MSG_SOVLED?><td align=center><a href='status.php?user_id=<?php echo $user?>&jresult=4'><?php echo $AC?></a>
<td rowspan=14 align=center>
<script language='javascript'>
function p(id){document.write("<a href=problem.php?id="+id+">"+id+" </a>");}
<?php $sql="SELECT DISTINCT `problem_id` FROM `solution` WHERE `user_id`='$user_mysql' AND `result`=4 ORDER BY `problem_id` ASC";	
if (!($result=mysql_query($sql))) echo mysql_error();
while ($row=mysql_fetch_array($result))
  echo "p($row[0]);";
mysql_free_result($result);
?>
</script>
<div id=submission style="width:600px;height:300px" ></div>

  </td>
</tr>
<tr><td><?php echo $MSG_SUBMIT?><td align=center><a href='status.php?user_id=<?php echo $user?>'><?php echo $Submit?></a></tr>
<?php
foreach($view_userstat as $row){

  //i++;
  echo "<tr><td>".$jresult[$row[0]]."<td align=center><a href=status.php?user_id=$user&jresult=".$row[0]." >".$row[1]."</a></tr>";
}


//}
echo "<tr id=pie><td>Statistics<td><div id='PieDiv' style='position:relative;height:105px;width:120px;'></div></tr>";

?>


<tr><td>School:<td align=center><?php echo $school?></tr>
<tr><td>Email:<td align=center><?php echo $email?></tr>
</table>
<?php
if(isset($_SESSION['administrator'])){
?>
  <h3>로그인 기록</h3>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>UserID</th>
        <th>Password</th>
        <th>IP</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach($view_userinfo as $row){
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
  </table>
<?php
}
?>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
<script type="text/javascript" src="include/wz_jsgraphics.js"></script>
<script type="text/javascript" src="include/pie.js"></script>
<script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
<script type="text/javascript">
$(function () {
  var d1 = [];
  var d2 = [];
<?php
foreach($chart_data_all as $k=>$d){
?>
  d1.push([<?php echo $k?>, <?php echo $d?>]);
  <?php }?>
<?php
  foreach($chart_data_ac as $k=>$d){
?>
    d2.push([<?php echo $k?>, <?php echo $d?>]);
    <?php }?>
    //var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

    // a null signifies separate line segments
    var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];

    $.plot($("#submission"), [
  {label:"<?php echo $MSG_SUBMIT?>",data:d1,lines: { show: true }},
  {label:"<?php echo $MSG_AC?>",data:d2,bars:{show:true}} ],{

    xaxis: {
      mode: "time"
        //,    max:(new Date()).getTime()
        //,min:(new Date()).getTime()-100*24*3600*1000
            },
            grid: {
              backgroundColor: { colors: ["#fff", "#fff"] },
        }
        });
});
//alert((new Date()).getTime());
var y= new Array ();
var x = new Array ();
var dt=document.getElementById("statics");
var data=dt.rows;
var n;
for(var i=3;dt.rows[i].id!="pie";i++){
  n=dt.rows[i].cells[0];
  n=n.innerText || n.textContent;
  x.push(n);
  n=dt.rows[i].cells[1].firstChild;
  n=n.innerText || n.textContent;
  n=parseInt(n);
  y.push(n);
  }
  var mypie=  new Pie("PieDiv");
  mypie.drawPie(y,x);
  </script>
</body>
</html>
