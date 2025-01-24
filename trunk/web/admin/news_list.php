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

<title>News List</title>
<hr>
<center><h3><?php echo $MSG_NEWS."-".$MSG_LIST?></h3></center>

<div class='padding'>

<?php
$sql = "SELECT COUNT('news_id') AS ids FROM `news`";
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
  $sql = "SELECT `news_id`,`user_id`,`title`,`time`,`defunct`,`importance`,`menu` FROM `news` WHERE (title LIKE ?) OR (content LIKE ?) ORDER BY `news_id` DESC";
  $result = pdo_query($sql,$keyword,$keyword);
}else{
  $sql = "SELECT `news_id`,`user_id`,`title`,`time`,`defunct`,`importance`,`menu` FROM `news` ORDER BY `news_id` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}
?>

<center>
<form action=news_list.php class="form-search form-inline">
  <input type="text" name=keyword class="form-control search-query" placeholder="<?php echo $MSG_TITLE.', '.$MSG_CONTENTS?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
</form>
</center>

<center>
  <table width=100% border=1 style="text-align:center;">
    <tr style='height:22px;'>
      <td>ID</td>
      <td>TITLE</td>
      <td>UPDATE</td>
      <td>NOW</td>
      <td>COPY</td>
      <td>IMPORTANCE</td>
      <td>MENU</td>
    </tr>
    <?php
    foreach($result as $row){
      echo "<tr style='height:22px;' news_id='".$row['news_id']."'>";
        echo "<td>".$row['news_id']."</td>";
        echo "<td><a href='news_edit.php?id=".$row['news_id']."'>".($row['title']==""?"Empty":$row['title'])."</a>"."</td>";
        echo "<td>".$row['time']."</td>";
        echo "<td><a href=news_df_change.php?id=".$row['news_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">".($row['defunct']=="N"?"<span class=green>On</span>":"<span class=red>Off</span>")."</a>"."</td>";
        echo "<td><a href=news_add_page.php?cid=".$row['news_id'].">Copy</a></td>";
        echo "<td fd='importance'>" . ($row['importance']). "</td>";
        echo "<td fd='menu'>" . ($row['menu'] == 1 ? "YES" : "NO") . "</td>";
      echo "</tr>";
    }
    ?>
  </table>
</center>
- <?php echo $MSG_HELP_ADD_FAQS?>

<?php
if(!(isset($_GET['keyword']) && $_GET['keyword']!=""))
{
  echo "<div style='display:inline;'>";
  echo "<nav class='center'>";
  echo "<ul class='pagination pagination-sm'>";
  echo "<li class='page-item'><a href='news_list.php?page=".(strval(1))."'>&lt;&lt;</a></li>";
  echo "<li class='page-item'><a href='news_list.php?page=".($page==1?strval(1):strval($page-1))."'>&lt;</a></li>";
  for($i=$spage; $i<=$epage; $i++){
    echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='news_list.php?page=".$i."'>".$i."</a></li>";
  }
  echo "<li class='page-item'><a href='news_list.php?page=".($page==$pages?strval($page):strval($page+1))."'>&gt;</a></li>";
  echo "<li class='page-item'><a href='news_list.php?page=".(strval($pages))."'>&gt;&gt;</a></li>";
  echo "</ul>";
  echo "</nav>";
  echo "</div>";
}
?>

</div>
<script>
function admin_mod(){
	var tb_name="news";
	var fd_name="importance";
	let fd_array = ["importance", "menu"];

// 使用 for...of 循环
	for (let fd_name of fd_array) {
		$("td[fd=importance]").each(function(){
			let sp=$(this);
			let fd=$(this).attr("fd");
			let data_id=$(this).parent().attr(tb_name+'_id');
			$(this).dblclick(function(){
				let data=sp.text();
				sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='"+tb_name+"_update_"+fd+"'><input type='hidden' name='"+tb_name+"_id' value='"+data_id+"'><input type='text' name='"+fd+"' value='"+data+"' selected='true' class='input-mini' size=20 ></form>");
				let ipt=sp.find("input[name="+fd+"]");
				ipt.focus();
				ipt[0].select();
				sp.find("input").change(function(){
					let newdata=sp.find("input[name="+fd+"]").val();
					$.post("ajax.php",sp.find("form").serialize()).done(function(){
						console.log("new "+fd );
						sp.html(newdata);
					});

				});
			});
		});
	}
}
$(document).ready(function(){
        admin_mod();
});

</script>

