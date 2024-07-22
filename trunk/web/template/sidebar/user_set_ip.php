<?php 
  include("template/$OJ_TEMPLATE/header.php");
?>
<div class="container">

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
        <a href="status.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_MY_SUBMISSIONS?></a>
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

	  <?php
	  if ($result2=="changed")
	  	echo "<center><h4 class='text-danger'>User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')."'s Login IP Changed to ".$ip."</h4></center>";
	  else
	  	echo "<center><h4 class='text-danger'>Login IP Change</h4></center>";
	 	?>

	  <center>
		<form action=user_set_ip.php?cid=<?php echo $view_cid?> method=post class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-offset-2 col-sm-3 control-label"><?php echo $MSG_USER_ID?></label>
				<?php if(isset($_GET['uid'])) { ?>
				<div class="col-sm-3"><input name="user_id" class="form-control" value="<?php echo htmlentities($_GET['uid'], ENT_QUOTES, 'UTF-8');?>" type="text" required ></div>
		  	<?php } else if(isset($_POST['user_id'])) { ?>
				<div class="col-sm-3"><input name="user_id" class="form-control" value="<?php echo htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8');?>" type="text" required ></div>
				<?php } else { ?>
				<div class="col-sm-3"><input name="user_id" class="form-control" placeholder="<?php echo $MSG_USER_ID."*"?>" type="text" required ></div>
				<?php } ?>
			</div>

			<div class="form-group">
				<label class="col-sm-offset-2 col-sm-3 control-label"><?php echo "New IP"?></label>
				<?php if(isset($_POST['ip'])) { ?>
				<div class="col-sm-3"><input name="ip" class="form-control" value="<?php echo htmlentities($_POST['ip'], ENT_QUOTES, 'UTF-8')?>" type="text"  autocomplete="off" required ></div>
				<?php } else { ?>
				<div class="col-sm-3"><input name="ip" class="form-control" placeholder="<?php echo "?.?.?.?*"?>" type="text"  autocomplete="off" required ></div>
				<?php } ?>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-2">
					<button name="do" type="hidden" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE; ?></button>
				</div>
				<div class="col-sm-2">
					<button name="submit" type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
				</div>
			</div>
		</form>
		</center>

	</div>
</div>

<!-- /container -->
<!-- Bootstrap core JavaScrip
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

<?php include("template/$OJ_TEMPLATE/footer.php");?>
