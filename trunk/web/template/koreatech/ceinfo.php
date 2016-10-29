<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
  $navigation_tab = "status";
  require_once("oj-header.php");
?>
<div class="container">
  <div id='editor_load'></div>
  <pre id='errtxt' class="alert alert-error"><?php echo $view_reinfo?></pre>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
<script src="/ace/src/ace.js"></script>
<script>
<?php
$ace_editor_language = strtolower($language_name[$slanguage]);
if($ace_editor_language == "c" || $ace_editor_language == "c++") {
  $ace_editor_language = "c_cpp";
}
?>
var editor;

var editorViewFunction = function() {
  var modeName = $("#editor").data("mode");
  editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.getSession().setMode("ace/mode/" + modeName);
  editor.setReadOnly(true);
  editor.setOptions({
    "maxLines" : Infinity,
    "fontSize" : 14
  });
  editor.commands.removeCommand('find');
}
$("#editor_load").load("showsource.php?id=<?php echo $id?> #editor", editorViewFunction);
</script>
</body>
</html>
