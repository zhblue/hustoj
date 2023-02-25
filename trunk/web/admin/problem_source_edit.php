<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit Problem</title>
</head>
<hr>

<?php
require_once("../include/db_info.inc.php");
require_once("admin-header.php");
require_once("../include/my_func.inc.php");

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}
echo "<center><h3>"."Edit-".$MSG_SOURCE."</h3></center>";
include_once("kindeditor.php") ;
?>

<body leftmargin="30" >
  <div class="container">
    <?php
    if (isset($_GET['id'])) {
      ;//require_once("../include/check_get_key.php");
    ?>

    <form method=POST action=problem_source_edit.php>
      <?php
      $sql = "SELECT * FROM `problem_source` WHERE `id`=?";
      $result = pdo_query($sql,intval($_GET['id']));
      $row = $result[0];
      ?>

      <input type=hidden name=id value='<?php echo $row['id']?>'>

      <p align=left>
        <?php echo "<h4>".$MSG_SOURCE."</h4>"?>
        <textarea name=source style="width:100%;" rows=1><?php echo htmlentities($row['source'],ENT_QUOTES,"UTF-8")?></textarea><br><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_SOURCE_SUMMARY."</h4>"?>
        <textarea  class="input input-large" style="width:100%;" rows=13 name=summary><?php echo htmlentities($row['summary'],ENT_QUOTES,"UTF-8")?></textarea><br><br>
      </p>

      <div align=center>
        <?php require_once("../include/set_post_key.php");?>
        <input type=submit value='<?php echo $MSG_SAVE?>' name=submit>
      </div>
    </form>

    <?php
    }
    else {
      require_once("../include/check_post_key.php");
      $id = intval($_POST['id']);

      if (!(isset($_SESSION[$OJ_NAME.'_'."p$id"]) || isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']) )) exit();

      $summary = stripslashes($_POST['summary']);

      $sql = "UPDATE `problem_source` SET `summary`=?,`in_date`=NOW() WHERE `id`=?";

      @pdo_query($sql, $summary, $id);

      echo "Edit OK!<br>";
      echo "<a href='/admin/problem_source_list.php'>See The List!</a>";
    }
    ?>
  </div>
</body>
</html>
