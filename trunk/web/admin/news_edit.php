<?php
require_once("admin-header.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");

echo "<hr>";
echo "<center><h3>".$MSG_NEWS."-"."Edit"."</h3></center>";

include_once("kindeditor.php");
?>

<div class="container">
<?php
if(isset($_POST['news_id'])){
  require_once("../include/check_post_key.php");

  $title = $_POST['title'];
  $content = $_POST['content'];
  $showInMenu = $_POST['showInMenu'];
  $menu = $showInMenu == "on" ? 1 : 0;

  $content = str_replace("<p>", "", $content);
  $content = str_replace("</p>", "<br />", $content);
//  $content = str_replace(",", "&#44;", $content);

  $user_id = $_SESSION[$OJ_NAME.'_'.'user_id'];
  $news_id = intval($_POST['news_id']);

  $sql = "UPDATE `news` SET `title`=?,`time`=now(),`content`=?,user_id=?,`menu`=? WHERE `news_id`=?";
  //echo $sql;
  pdo_query($sql,$title,$content,$user_id,$menu,$news_id);
  $sessionDataKey = $OJ_NAME.'_'."_MENU_NEWS_CACHE";
  unset($_SESSION[$sessionDataKey]);
  header("location:news_list.php");
  exit();
}else{
  $news_id = intval($_GET['id']);
  $sql = "SELECT * FROM `news` WHERE `news_id`=?";
  $result = pdo_query($sql,$news_id);
  if(count($result)!=1){
    echo "No such News!";
    exit(0);
  }

  $row = $result[0];

  $title = htmlentities($row['title'],ENT_QUOTES,"UTF-8");
  $content = $row['content'];
  $showInMenu = $row['menu'] == 1;
}
?>

  <form method=POST action=news_edit.php>
    <input type=hidden name='news_id' value=<?php echo $news_id?>>
    <p align=left>
      <label class="col control-label"><?php echo $MSG_TITLE?></label>
      <input type=text name=title size=71 value='<?php echo $title?>'>
    </p>
    <p align=left>
      <label class="col control-label"><?php echo $MSG_NEWS_MENU?>
        <input style="display: inline-block;" type="checkbox" name=showInMenu <?php if($showInMenu) { echo "checked"; } ?> />
      </label>
    </p>
    <p align=left>
      <textarea class=kindeditor name=content rows=41 ><?php echo htmlentities($content,ENT_QUOTES,"UTF-8")?>
      </textarea>
    </p>
    <?php require_once("../include/set_post_key.php");?>
    <p>
      <center>
      <input type=submit value='<?php echo $MSG_SAVE?>' name=submit>
      </center>
    </p>
  </form>
</div>
