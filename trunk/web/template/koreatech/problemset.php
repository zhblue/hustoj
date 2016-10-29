<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
$navigation_tab = "problem";
require_once("oj-header.php");
?>

<div class="container">

  <div class="col-md-12 text-right">
    <form class="form-inline">
      <div class="form-group">
        <label class="sr-only" for="searchTerm">search term</label>
        <input class="form-control" type="text" name="search" id="searchTerm">
      </div>
      <button class="btn btn-mini" type="submit"><?php echo $MSG_SEARCH?></button>
    </form>
  </div>

  <div class="col-md-12"><div class="table-responsive">
  <table id='problemset' class='table table-hover table-striped'>
    <thead>
      <tr>
        <th width="120"><?php echo $MSG_PROBLEM_ID?></th>
        <th><?php echo $MSG_TITLE?></th>
        <th width="60" ><?php echo $MSG_AC?></th>
        <th width="60" ><?php echo $MSG_SUBMIT?></th>
      </tr>
    </thead>
    <tbody>
<?php
foreach ($view_problemset as $row) {
?>
      <tr>
        <td><?php echo $row[1]?></td>
        <td>
<?php
if (strcmp($row[0], "success") == 0) {
?>
          <span class='label label-success'>성공</span>&nbsp;
<?php
} else if (strcmp($row[0], "fail") == 0) {
?>
          <span class='label label-danger'>실패</span>&nbsp;
<?php
}
?>
          <a href="problem.php?id=<?php echo $row[1]?>"><?php echo $row[2]?></a>
        </td>
        <td><a href="status.php?problem_id=<?php echo $row[1]?>&jresult=4"><?php echo $row[3]?></a></td>
        <td><a href="status.php?problem_id=<?php echo $row[1]?>"><?php echo $row[4]?></a></td>
      </tr>
<?php
}
?>
    </tbody>
  </table>
  </div></div>
<nav class="text-center">
  <ul class="pagination pagination-sm">
<?php
for ($i=1;$i<=$view_total_page;$i++){
  if ($i==$page) echo "<li class='active'><span>".$i."<span class='sr-only'>(current)</span></span></li>";
  else echo "<li><a href='problemset.php?page=".$i."'>".$i."</a></li>";
}
?>
  </ul>
</nav>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
