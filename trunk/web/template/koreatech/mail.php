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
  <form class="form-horizontal" method="post" action="mail.php">
<?php
if($view_content) {
?>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <h3>
          <?php echo htmlspecialchars(str_replace("\n\r","\n",$view_title));?> <small>from <strong><?php echo $to_user?></strong></small>
        </h3>
      </div>
      <div class="col-sm-offset-2 col-sm-10">
        <pre><?php echo htmlspecialchars(str_replace("\n\r","\n",$view_content));?></pre>
      </div>
    </div>
<?php
}
?>
    <div class="form-group">
      <label for="inputToUser" class="col-sm-2 control-label">받는사람</label>
      <div class="col-sm-10">
        <input id="inputToUser" name="to_user" class="form-control" value="<?php echo $to_user?>">
      </div>
    </div>
    <div class="form-group">
      <label for="inputTitle" class="col-sm-2 control-label">제목</label>
      <div class="col-sm-10">
        <input id="inputTitle" name="title" class="form-control" value="<?php echo $title?>">
      </div>
    </div>
    <div class="form-group">
      <label for="textareaContent" class="col-sm-2 control-label">내용</label>
      <div class="col-sm-10">
        <textarea name="content" rows="10" class="form-control"></textarea>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-2">
        <button type="submit" class="btn btn-primary">보내기</button>
      </div>
    </div>
  </form>
  <table class="table table-hover">
    <thead>
      <tr>
        <td>ID</td>
        <td>보낸사람</td>
        <td>제목</td>
        <td>날짜</td>
      </tr>
    </thead>
    <tbody>
<?php 
foreach($view_mail as $row){
  echo "<tr>";
  foreach($row as $table_cell){
    echo "<td>";
    echo $table_cell;
    echo "</td>";
  }
  echo "</tr>";
}
?>
    </tbody>
  </table>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
