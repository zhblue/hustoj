<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv='refresh' content='60'>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
  <script type="text/javascript" src="include/jquery-latest.js"></script> 
</head>
<body>
<?php
$navigation_tab = "ranklist";
require_once("./template/$OJ_TEMPLATE/contest-header.php");
?>

<div class="container">
<div class="row text-center">
  <h3><?php echo $title?></h3>
  <!--a href="contestrank.xls.php?cid=<?php echo $cid?>" >Download</a-->
</div>

<div class="col-md-12"><div class="table-responsive">
<table id=rank class='table'>
  <thead>
    <tr class=toprow align=center>
      <th width=5%>순위</th>
      <th width=10%>아이디</th>
      <th width=5%>문제</th>
      <th width=5%>걸린시간</th>
<?php
for ($i=0;$i<$pid_cnt;$i++)
  echo "<th class='text-center'><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></th>";

echo "</tr></thead>\n<tbody>";

$rank=1;
for ($i=0;$i<$user_cnt;$i++){
  echo "<tr><td>";
  $uuid=$U[$i]->user_id;
  $nick=$U[$i]->nick;
  if($nick[0]!="*")
    echo $rank++;
  else
    echo "*";

  $usolved=$U[$i]->solved;
  if($uuid==$_GET['user_id']) echo "<td bgcolor=#ffff77>";
  else echo"<td>";

  echo "<a name=\"$uuid\" href=userinfo.php?user=$uuid>$uuid</a></td>";
  echo "<td><a href=status.php?user_id=$uuid&cid=$cid>$usolved</a></td>";
  echo "<td>".sec2str($U[$i]->time)."</td>";
  for ($j=0;$j<$pid_cnt;$j++){
    $bg_color="eeeeee";
    if (isset($U[$i]->p_ac_sec[$j])&&$U[$i]->p_ac_sec[$j]>0){
      $aa=0x66+$U[$i]->p_wa_num[$j]*32;
      $aa=$aa>0xaa?0xaa:$aa;
      $aa=dechex($aa);
      $bg_color="$aa"."ff"."$aa";

      //$bg_color="aaffaa";
      if($uuid==$first_blood[$j]){
        $bg_color="aaaaff";
      }
    }else if(isset($U[$i]->p_wa_num[$j])&&$U[$i]->p_wa_num[$j]>0) {
      $aa=0xaa-$U[$i]->p_wa_num[$j]*10;
      $aa=$aa>16?$aa:16;
      $aa=dechex($aa);
      $bg_color="ff$aa$aa";
    }

    echo "<td class='text-center' style='background-color:#$bg_color'>";
    if(isset($U[$i])){
      if (isset($U[$i]->p_ac_sec[$j])&&$U[$i]->p_ac_sec[$j]>0)
        echo sec2str($U[$i]->p_ac_sec[$j]);
      if (isset($U[$i]->p_wa_num[$j])&&$U[$i]->p_wa_num[$j]>0)
        echo "<small>(-".$U[$i]->p_wa_num[$j].")<small>";
    }
    echo "</td>";
  }
  echo "</tr>\n";
}
?>
  </tbody></table></div></div>

<script>
function getTotal(rows){
  var total=0;
  for(var i=0;i<rows.length&&total==0;i++){
    try{
      total=parseInt(rows[rows.length-i].cells[0].innerHTML);
      if(isNaN(total)) total=0;
      }catch(e){

      }
    }
    return total;

  }
function metal(){
  var tb=window.document.getElementById('rank');
  var rows=tb.rows;
  try{
    var total=getTotal(rows);
    for(var i=1;i<rows.length;i++){
      var cell=rows[i].cells[0];
      var acc=rows[i].cells[2];
      var ac=parseInt(acc.innerText);
      if (isNaN(ac)) ac=parseInt(acc.textContent);


      if(cell.innerHTML!="*"&&ac>0){
        var r=parseInt(cell.innerHTML);
        if(r==1){
          cell.innerHTML="Winner";
          cell.className="badge alert-warning";
        }
        else if(r>1&&r<=total*.05+1)
          cell.className="badge alert-warning";
        else if(r>total*.05+1&&r<=total*.20+1)
          cell.className="badge alert-success";
        else if(r>total*.20+1&&r<=total*.45+1)
          cell.className="badge alert-info";
        else if(ac>0)
          cell.className="badge alert-danger";
      }
    }
  }catch(e){
    //alert(e);
  }
}
metal();


</script>
</div>

<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
