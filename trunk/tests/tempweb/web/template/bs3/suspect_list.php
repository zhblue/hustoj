<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="../../favicon.ico">

<title>
  <?php echo $OJ_NAME?>
</title>

<?php include("template/$OJ_TEMPLATE/css.php");?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
<script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
<div class="container">
  <?php include("template/$OJ_TEMPLATE/nav.php");?>
  <!-- Main component for a primary marketing message or call to action -->
  <div class="jumbotron">
    <center>
    <div>
      <h3><?php echo $MSG_CONTEST_ID?> : <?php echo $view_cid?> - <?php echo $view_title ?></h3>
      <p>
        <?php echo $view_description?>
      </p>
      <br>
      <?php echo $MSG_SERVER_TIME?> : <span id=nowdate > <?php echo date("Y-m-d H:i:s")?></span>
      <br>
      
      <?php if (isset($OJ_RANK_LOCK_PERCENT)&&$OJ_RANK_LOCK_PERCENT!=0) { ?>
      Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br/>
      <?php } ?>
      
      <?php if ($now>$end_time) {
        echo "<span class=text-muted>$MSG_Ended</span>";
      }
      else if ($now<$start_time) {
        echo "<span class=text-success>$MSG_Start&nbsp;</span>";
        echo "<span class=text-success>$MSG_TotalTime</span>"." ".formatTimeLength($end_time-$start_time);
      }
      else {
        echo "<span class=text-danger>$MSG_Running</span>&nbsp;";
        echo "<span class=text-danger>$MSG_LeftTime</span>"." ".formatTimeLength($end_time-$now);
      }
      ?>

      <br><br>

      <?php echo $MSG_CONTEST_STATUS?> : 
      
      <?php
      if ($now>$end_time)
        echo "<span class=text-muted>".$MSG_End."</span>";
      else if ($now<$start_time)
        echo "<span class=text-success>".$MSG_Start."</span>";
      else
        echo "<span class=text-danger>".$MSG_Running."</span>";
      ?>
      &nbsp;&nbsp;

      <?php echo $MSG_CONTEST_OPEN?> : 

      <?php if ($view_private=='0')
        echo "<span class=text-primary>".$MSG_Public."</span>";
      else
        echo "<span class=text-danger>".$MSG_Private."</span>";
      ?>

      <br>

      <?php echo $MSG_START_TIME?> : <?php echo $view_start_time?>
      <br>
      <?php echo $MSG_END_TIME?> : <?php echo $view_end_time?>
      <br><br>

      <div class="btn-group">
        <a href="contest.php?cid=<?php echo $cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_PROBLEMS?></a>
        <a href="status.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_SUBMIT?></a>
        <a href="contestrank.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_STANDING?></a>
        <a href="contestrank-oi.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo "OI".$MSG_STANDING?></a>
        <a href="conteststatistics.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_STATISTICS?></a>
        <a href="suspect_list.php?cid=<?php echo $view_cid?>" class="btn btn-warning btn-sm"><?php echo $MSG_IP_VERIFICATION?></a>
        <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])) {?>
          <a href="user_set_ip.php?cid=<?php echo $view_cid?>" class="btn btn-success btn-sm"><?php echo $MSG_SET_LOGIN_IP?></a>
          <a target="_blank" href="../../admin/contest_edit.php?cid=<?php echo $view_cid?>" class="btn btn-success btn-sm"><?php echo "EDIT"?></a>
        <?php } ?>
      </div>
    </div>
    </center>

    <br>

    <div>
      <center>
        <?php echo $MSG_CONTEST_SUSPECT1?><br><br>
        <table class="table-hover table-striped text-center" align=center width=90% border=0>
          <tr>
            <td>IP address</td>
            <td>Used ID</td>
            <td>Trace</td>
            <td>Time</td>
            <td>IP address count</td>
          </tr>

          <?php
          foreach ($result1 as $row) {
            echo "<tr>";
              echo "<td>".$row['ip']."</td>";
              echo "<td>".$row['user_id']."</td>";
              echo "<td>";
                echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
                echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
              echo "</td>";
              echo "<td>".$row['in_date'];
              echo "<td>".$row['c']."</td>";
            echo "</tr>";
          }
          ?>

        </table>
      </center>
    </div>

    <br><br>

    <div>
      <center>
        <?php echo $MSG_CONTEST_SUSPECT2?><br><br>
        <table class="table-hover table-striped text-center" align=center width=90% border=0>
          <tr>
            <td>Used ID</td>
            <td>Trace</td>
            <td>Used IP address</td>
            <td>Time</td>
            <td>IP address count</td>
          </tr>

          <?php
          foreach ($result2 as $row) {
            echo "<tr>";
              echo "<td>".$row['user_id']."</td>";
              echo "<td>";
                echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
                echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
              echo "</td>";
              echo "<td>".$row['ip'];
              echo "<td>".$row['time'];
              echo "<td>".$row['c'];
              echo "</tr>";
          }
          ?>
        </table>
      </center>
    </div>

  </div>

</div>

<!-- /container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php include("template/$OJ_TEMPLATE/js.php");?>      

<script src="<?php echo $OJ_CDN_URL?>include/sortTable.js"></script>

<script>
  var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
  //alert(diff);
  function clock() {
    var x,h,m,s,n,xingqi,y,mon,d;
    var x = new Date(new Date().getTime()+diff);
    y = x.getYear()+1900;

    if (y>3000)
      y -= 1900;

    mon = x.getMonth()+1;
    d = x.getDate();
    xingqi = x.getDay();
    h = x.getHours();
    m = x.getMinutes();
    s = x.getSeconds();
    n = y+"-"+(mon>=10?mon:"0"+mon)+"-"+(d>=10?d:"0"+d)+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);

    //alert(n);
    document.getElementById('nowdate').innerHTML = n;
    setTimeout("clock()",1000);
  }
  clock();
</script>

</body>
</html>

