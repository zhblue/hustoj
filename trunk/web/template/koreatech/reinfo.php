<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php require_once("oj-header.php");?>
<div class="container">
<pre id='errtxt' class="alert alert-error"><?php echo $view_reinfo?></pre>
</div>
<?php require_once("oj-footer.php");?>
</body>
</html>

