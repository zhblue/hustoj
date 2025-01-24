<?php
require_once("admin-header.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

echo "<hr>";
echo "<center><h3>".$MSG_NEWS."-".$MSG_ADD."</h3></center>";

include_once("kindeditor.php");
?>

<?php
if(isset($_GET['cid'])){
  $cid = intval($_GET['cid']);
  $sql = "SELECT * FROM news WHERE `news_id`=?";
  $result = pdo_query($sql,$cid);
  $row = $result[0];
  $title = $row['title'];
  $content = $row['content'];
  $defunct = $row['defunct'];
}
$plist = "";
if(isset($_POST['pid'])){
	sort($_POST['pid']);
	foreach($_POST['pid'] as $i){
	  if($plist)
	    $plist.=','.intval($i);
	  else
	    $plist = $i;
	}
	
  $plist = trim($_POST['hlist']);
  $pieces = explode(",",$plist );
  $pieces = array_unique($pieces);
  if($pieces[0]=="") unset($pieces[0]);
  $plist=implode(",",$pieces);

	  $content="[plist=".$plist."]".htmlentities($_POST['keyword'],ENT_QUOTES,"utf-8")."[/plist]";
}
?>

<div class="padding">
  <form method=POST action=news_add.php>
    <p align=left>
      <label class="col control-label"><?php echo $MSG_TITLE?></label>
      <input type=text name=title size=71 value='<?php echo isset($title)?$title."-Copy":""?>'>
    </p>
    <p align=left>
      <label class="col control-label"><?php echo $MSG_NEWS_MENU?>
        <input style="display: inline-block;" type="checkbox" name=showInMenu />
      </label>
    </p>
    <p align=left>
      <textarea class=kindeditor name=content rows=41 >
        <?php echo isset($content)?$content:""?>
      </textarea>
    </p>
    <p>
      <center>
      <input type=submit value='<?php echo $MSG_SAVE?>' name=submit>
      </center>
    </p>
    <?php require_once("../include/set_post_key.php");?>
  </form>
</div>
