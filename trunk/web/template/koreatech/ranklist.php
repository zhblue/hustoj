<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
$navigation_tab = "ranklist";
require_once("oj-header.php");
?>

<div class="container">
  <div class="col-sm-6 text-left">
    <form action="userinfo.php" class="form-inline">
      <div class="form-group">
        <input class="form-control" type="text" placeholder="<?php echo $MSG_USER?>" name="user">
        <button class="btn btn-mini" type="submit">Go</button>
      </div>
    </form>
  </div>
  <div class="col-sm-6 text-right">
    <a class="btn btn-link" href="ranklist.php?scope=d">오늘</a>
    <a class="btn btn-link" href="ranklist.php?scope=w">이번주</a>
    <a class="btn btn-link" href="ranklist.php?scope=m">이번달</a>
    <a class="btn btn-link" href="ranklist.php?scope=y">올해</a>
  </div>
</div>
<div class="container">
  <div class="col-md-12"><div class="table-responsive">
  <table class="table table-hover table-striped">
    <thead>
      <tr class="toprow">
        <th width="10%" align="center"><b><?php echo $MSG_Number?></b></th>
        <th width="10%" align="center"><b><?php echo $MSG_USER?></b></th>
        <th width="50%" align="center"><b><?php echo $MSG_NICK?></b></th>
        <th width="10%" align="center"><b><?php echo $MSG_AC?></b></th>
        <th width="10%" align="center"><b><?php echo $MSG_SUBMIT?></b></th>
        <th width="10%" align="center"><b><?php echo $MSG_RATIO?></b></th>
      </tr>
    </thead>
    <tbody>
<?php
foreach($view_rank as $row){
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
  </div></div>
  <div class="col-md-12 text-center">
<?php
for($i = 0; $i <$view_total ; $i += $page_size) {
  echo "<a href='./ranklist.php?start=" . strval ( $i ).($scope?"&scope=$scope":"") . "'>";
  echo strval ( $i + 1 );
  echo "-";
  echo strval ( $i + $page_size );
  echo "</a>&nbsp;";
  if ($i % 250 == 200)
    echo "<br>";
}
?>
  </div>
</div>

<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
