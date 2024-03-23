<?php 
  require_once("../include/db_info.inc.php");
  require_once("admin-header.php");

  if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))) {
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
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
    <form class="form-inline" action="problem_export_xml.php" method=post>
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

      <center>
      <div class='form-group'>
        <input type="hidden" name="do" value="do">
        <!-- <input type="submit" name="submit" value="Export to XML Script"> -->
        <button class='btn btn-default btn-sm' type=submit>Download to XML File</button>
      </div>
      </center>

      <?php require_once("../include/set_post_key.php");?>
    </form>

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
