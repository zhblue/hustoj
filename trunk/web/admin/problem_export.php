<?php 
  require_once("../include/db_info.inc.php");
  require_once("admin-header.php");

  if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_importer']) )) {
    exit(1);
  }
  if(isset($_GET['last_file'])){
	  $last_file=$_SESSION[$OJ_NAME."_last_file"];
	  if($_SESSION[$OJ_NAME."_".$last_file]==100&&file_exists($last_file)){
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=export_".$_SESSION[$OJ_NAME.'_'.'user_id']."_".basename($last_file));
		header("Content-Length: " . filesize($last_file));
		if (ob_get_level() > 0) {
		    ob_clean();
		}
		readfile($last_file);
	  	unlink($last_file);
		rmdir(dirname($last_file));
		unset($_SESSION[$OJ_NAME."_".$last_file]);
		unset($_SESSION[$OJ_NAME."_last_file"]);
		exit();
	  }
  
  }
  echo "<center><h3>".$MSG_PROBLEM."-".$MSG_EXPORT."</h3></center>";

?>
<html>
<head>
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Content-Language" content="zh-cn">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Problem Export</title>
</head>
<hr>

<body leftmargin="30" >
  <div class="container">
    <br><br>
    - Export Problem XML<br><br>
    <form class="form-inline" action="problem_export_xml.php" method="post"  onsubmit="setTimeout('location.reload()',3000)" >
      <div class="form-group">
        <label>1) Continuous Problem IDs:</label>
        <input class="form-control" name="start" type="text" placeholder="1001">
      </div>
      <div class="form-group">
        <label> ~ </label>
        <input class="form-control" name="end" type="text" placeholder="1009">
      </div>
      <br><br>
      <div class="form-group">
        <label>2) Separate&nbsp;&nbsp;&nbsp;&nbsp; Problem IDs:</label>
        <input class="form-control" name="in" type="text" placeholder="1001,1003,1005, ... ">
      </div>
      <br><br>
     <div class="form-group">
        <label>3) 为远程判题导出题面填写远程别名，正常导出请留空 for Remote judge only :</label>
        <input class="form-control" name="remote_name" type="text" placeholder="my">
      </div>
      <br><br>

      <center>
      <div class='form-group'>
	<input type='checkbox' name='zip' >ZIP 
        <input type="hidden" name="do" value="do">
        <!-- <input type="submit" name="submit" value="Export to XML Script"> -->
        <button class='btn btn-default btn-sm' type=submit>Download to XML File</button>
      </div>
      </center>

      <?php require_once("../include/set_post_key.php");?>
    </form>

   <?php if (isset($_SESSION[$OJ_NAME."_last_file"])){
		$last_file=$_SESSION[$OJ_NAME."_last_file"];		
		echo "<a class='btn btn-success btn-sm' href='problem_export.php?last_file=".htmlentities($_SESSION[$OJ_NAME."_last_file"])."'>下载前个任务".$_SESSION[$OJ_NAME."_".$last_file]."%</a>";
      }
	?>
    <br><br>
    <!--
    * from-to will working if empty IN <br>
    * if using IN,from-to will not working.<br>
    * IN can go with "," seperated problem_ids like [1000,1020]
    -->
    - Continuous Problem IDs fields will be applied when Seperate Problem IDs fields was empty.<br>
    - Seperate Problem IDs fields will be applied when Continuous Problem IDs fields was empty.
  </div>

</body>
</html>

