<!DOCTYPE html>
<html>
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv='refresh' content='60'>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
    $navigation_tab = "statistics";
    require_once("./template/$OJ_TEMPLATE/contest-header.php");
?>
<div class="container">
<center><h3>Contest Statistics</h3>
<table class='table'>
<thead>
<tr align=center class=toprow><th><th>AC<th>PE<th>WA<th>TLE<th>MLE<th>OLE<th>RE<th>CE<th>Total<th><th>C<th>C++<th>Pascal<th>Java<th>Ruby<th>Bash<th>Python<th>PHP<th>Perl<th>C#<th>Obj-c<th>FreeBasic</tr>
</thead>
<tbody>
<?php
    for ($i=0;$i<$pid_cnt;$i++){
      if(!isset($PID[$i])) $PID[$i]="";

      if ($i&1)
        echo "<tr align=center class=oddrow><td>";
      else
        echo "<tr align=center class=evenrow><td>";
      echo "<a href='problem.php?cid=$cid&pid=$i'>$PID[$i]</a>";
      for ($j=0;$j<22;$j++) {
        if(!isset($R[$i][$j])) $R[$i][$j]="";
        echo "<td>".$R[$i][$j];
      }
      echo "</tr>";
    }
    echo "<tr align=center class=evenrow><td>Total";	
    for ($j=0;$j<22;$j++) {
      if(!isset($R[$i][$j])) $R[$i][$j]="";
      echo "<td>".$R[$i][$j];
    }
    echo "</tr>";
?>
</tbody>
</table>
</center>
</div>

<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>

</body>
</html>
