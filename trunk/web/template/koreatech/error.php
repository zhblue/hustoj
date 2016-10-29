<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
  if(isset($_GET['cid'])) {
    require_once("./template/$OJ_TEMPLATE/contest-header.php");
  } else {
    require_once("oj-header.php");
  }
?>
<div class="container text-center">
  <?php echo $view_errors?>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
