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

<table class="table table-hover">
  <thead>
    <tr>
      <th><?php echo $MSG_RUNID?></th>
      <th><?php echo $MSG_USER?></th>
      <th><?php echo $MSG_PROBLEM?></th>
      <th><?php echo $MSG_RESULT?></th>
      <th><?php echo $MSG_MEMORY?></th>
      <th><?php echo $MSG_TIME?></th>
      <th><?php echo $MSG_LANG?></th>
      <th><?php echo $MSG_CODE_LENGTH?></th>
      <th><?php echo $MSG_SUBMIT_TIME?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $id?></td>
      <td><a href='userinfo.php?user=<?php echo $suser_id?>'><?php echo $suser_id?></a></td>
      <td><?php echo $sproblem_id?></td>
      <td><?php echo $judge_result[$sresult]?></td>
      <td><?php echo $smemory?></td>
      <td><?php echo $stime?></td>
      <td><?php echo $language_name[$slanguage]?></td>
      <td><?php echo $scode_length?></td>
      <td><?php echo $scode_date?></td>
    </tr>
  </tbody>
</table>
<?php
$ace_editor_language = strtolower($language_name[$slanguage]);
if($ace_editor_language == "c" || $ace_editor_language == "c++") {
  $ace_editor_language = "c_cpp";
}

if ($ok==true){
  if($view_user_id!=$_SESSION['user_id'])
    echo "<a href='mail.php?to_user=$view_user_id&title=$MSG_SUBMIT $id'>Mail the auther</a>";
  echo "<div id='editor' data-mode='".$ace_editor_language."'><pre>";
  echo htmlspecialchars(str_replace("\n\r","\n",$view_source));
  echo "</pre></div>";

}else{
  echo "죄송하지만, 이 코드를 볼 수 없습니다.";
}
?>
</div>
<script src="/ace/src/ace.js"></script>
<script>
  var editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.getSession().setMode("ace/mode/<?php echo $ace_editor_language;?>");
  editor.setReadOnly(true);
  editor.setOptions({
    "maxLines" : Infinity,
    "fontSize" : 14
  });
  editor.commands.removeCommand('find');
</script>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
