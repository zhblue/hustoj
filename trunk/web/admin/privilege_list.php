<?php
require("admin-header.php");
require_once("../include/set_get_key.php");

if(!isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>Privilege List</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_PRIVILEGE."-".$MSG_LIST?></h3></center>

<div class='container'>

<?php
$sql = "SELECT COUNT(*) AS ids FROM privilege WHERE rightstr IN ('administrator','source_browser','contest_creator','http_judge','problem_editor','problem_importer','password_setter','printer','balloon','vip','problem_start','problem_end') ORDER BY user_id, rightstr";
$result = pdo_query($sql);
$row = $result[0];

$ids = intval($row['ids']);

$idsperpage = 25;
$pages = intval(ceil($ids/$idsperpage));

if(isset($_GET['page'])){ $page = intval($_GET['page']);}
else{ $page = 1;}

$pagesperframe = 5;
$frame = intval(ceil($page/$pagesperframe));

$spage = ($frame-1)*$pagesperframe+1;
$epage = min($spage+$pagesperframe-1, $pages);

$sid = ($page-1)*$idsperpage;

$sql = "";
if(isset($_GET['keyword']) && $_GET['keyword']!=""){
  $keyword = $_GET['keyword'];
  $keyword = "%$keyword%";
  $sql = "SELECT * FROM privilege WHERE (user_id LIKE ?) OR (rightstr LIKE ?) ORDER BY user_id, rightstr";
  $result = pdo_query($sql,$keyword,$keyword);
}else{
  $sql = "SELECT * FROM privilege WHERE rightstr IN ('administrator','source_browser','contest_creator','http_judge','problem_editor','problem_importer','password_setter','printer','balloon','vip','problem_start','problem_end') ORDER BY user_id, rightstr LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}
?>

<center>
<form action=privilege_list.php class="form-search form-inline">
  <input type="text" name=keyword class="form-control search-query" placeholder="<?php echo $MSG_USER_ID.', '.$MSG_PRIVILEGE?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
</form>
</center>

<center>
  <table width=100% border=1 style="text-align:center;">
    <tr>
      <td>ID</td>
      <td>PRIVILEGE</td>
      <td>REMOVE</td>
    </tr>
    <?php
    foreach($result as $row){
      echo "<tr>";
        echo "<td>".$row['user_id']."</td>";
        echo "<td>".$row['rightstr'];
	if($row['valuestr']!="true") echo ":".$row['valuestr'];
	echo "</td>";
        echo "<td><a href='privilege_delete.php?uid=".htmlentities($row['user_id'],ENT_QUOTES,"UTF-8")."&rightstr={$row['rightstr']}&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey']."'>Delete</a></td>";
      echo "</tr>";
    }
    ?>
  </table>
</center>

<?php
if(!(isset($_GET['keyword']) && $_GET['keyword']!=""))
{
  echo "<div style='display:inline;'>";
  echo "<nav class='center'>";
  echo "<ul class='pagination pagination-sm'>";
  echo "<li class='page-item'><a href='privilege_list.php?page=".(strval(1))."'>&lt;&lt;</a></li>";
  echo "<li class='page-item'><a href='privilege_list.php?page=".($page==1?strval(1):strval($page-1))."'>&lt;</a></li>";
  for($i=$spage; $i<=$epage; $i++){
    echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='privilege_list.php?page=".$i."'>".$i."</a></li>";
  }
  echo "<li class='page-item'><a href='privilege_list.php?page=".($page==$pages?strval($page):strval($page+1))."'>&gt;</a></li>";
  echo "<li class='page-item'><a href='privilege_list.php?page=".(strval($pages))."'>&gt;&gt;</a></li>";
  echo "</ul>";
  echo "</nav>";
  echo "</div>";
}
?>

</div>
