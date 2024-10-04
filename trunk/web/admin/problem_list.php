<?php
require("admin-header.php");
require_once("../include/set_get_key.php");
require_once('../include/const.inc.php');

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
        || isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
        || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])
        )){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}


if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>Problem List</title>
<hr>
<center><h3><?php echo $MSG_PROBLEM."-".$MSG_LIST?></h3></center>

<div class='container'>

<?php
$sql = "SELECT COUNT('problem_id') AS ids FROM `problem`";
$result = pdo_query($sql);
$row = $result[0];

$ids = intval($row['ids']);

$idsperpage = 50;
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
  $sql = "SELECT `problem_id`,`title`,`accepted`,`in_date`,`defunct`,`source`,`remote_oj`,`remote_id`  FROM `problem` WHERE (problem_id LIKE ?) OR (title LIKE ?) OR (description LIKE ?) OR (source LIKE ?) OR (hint LIKE ?) ORDER BY `problem_id` ASC";
  $result = pdo_query($sql,$keyword,$keyword,$keyword,$keyword,$keyword);
}else{
  $sql = "SELECT `problem_id`,`title`,`accepted`,`in_date`,`defunct`,`source`,`remote_oj`,`remote_id`  FROM `problem` ORDER BY `problem_id` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}
?>

<center>
<form action=problem_list.php class="form-search form-inline">
  <input type="text" name=keyword value="<?php if(isset($_GET['keyword']))echo htmlentities($_GET['keyword'],ENT_QUOTES,"utf-8")?>" class="form-control search-query" placeholder="<?php echo $MSG_PROBLEM_ID.', '.$MSG_TITLE.', '.$MSG_Description.', '.$MSG_SOURCE?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
</form>
</center>

<?php
/*
echo "<select class='input-mini' onchange=\"location.href='problem_list.php?page='+this.value;\">";
for ($i=1;$i<=$cnt;$i++){
        if ($i>1) echo '&nbsp;';
        if ($i==$page) echo "<option value='$i' selected>";
        else  echo "<option value='$i'>";
        echo $i+9;
        echo "**</option>";
}
echo "</select>";
*/
?>

<center>
<table width=100% border=1 style="text-align:center;">
  <form id='pform' method=post action=contest_add.php >
<input type="hidden" name=keyword value="<?php if(isset($_GET['keyword']))echo htmlentities($_GET['keyword'],ENT_QUOTES,"utf-8")?>">
<input type="hidden" name=hlist value="" >
    <tr>
      <td width=60px><?php echo $MSG_PROBLEM_ID?><input type=checkbox style='vertical-align:2px;' onchange='$("input[type=checkbox]").prop("checked", this.checked)'></td>
      <td><?php echo $MSG_TITLE?></td>
      <td><?php echo $MSG_AC?></td>	 
      <td><?php echo $MSG_SAVED_DATE?></td>
	   <td><?php echo $MSG_SOURCE ?></td><!--分类-->
      <?php
      if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
        if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))
          echo "<td>$MSG_PROBLEM_STATUS</td><td>$MSG_DELETE</td>";
        echo "<td>$MSG_EDIT</td><td>$MSG_TESTDATA</td>";
      }
      ?>
    </tr>
        <tr>
        <td colspan=2 style="height:40px;"><?php echo "$MSG_CHECK_TO"?></td>
      <td colspan=6>
      <input type=submit name='problem2contest' value='<?php echo $MSG_NEW_CONTEST?>'>
      <input type=submit name='enable' value='<?php echo $MSG_AVAILABLE ?>' onclick='$("form").attr("action","problem_df_change.php")'>
      <input type=submit name='disable' value='<?php echo $MSG_RESERVED ?>' onclick='$("form").attr("action","problem_df_change.php")'>
      <input type=submit name='plist' value='<?php echo $MSG_NEW_PROBLEM_LIST?>' onclick='$("form").attr("action","news_add_page.php")'>
      </td>
    </tr>
    <?php    
	
    foreach($result as $row){
		$view = "";
        echo "<tr>";
        echo "<td>".$row['problem_id']." <input type=checkbox style='vertical-align:2px;' name='pid[]' value='".$row['problem_id']."'></td>";
        echo "<td><a href='../problem.php?id=".$row['problem_id']."'>".$row['title']."</a>";
               if(!empty($row['remote_oj']))echo "&nbsp;<a href='".$row['source']."' target=_blank>  ".$row['remote_oj'].$row['remote_id']."</a>";   	
        echo "</td>";
        echo "<td>".$row['accepted']."</td>";
        echo "<td>".$row['in_date']."</td>";
		echo "<td>";//分类
		
		$category = array();
	    $cate = explode(" ",$row['source']);
	    foreach($cate as $cat){
                        $cat=trim($cat);
                        if(mb_ereg("^http",$cat)){
                                echo "<a class='label' style='display: inline-block;' href=\"".htmlentities($cat,ENT_QUOTES,'utf-8').'" target="_blank">Remote</a>' ;
                        }
		        else array_push($category,trim($cat));
        }
		foreach ($category as $cat) {
			if(trim($cat)==""||trim($cat)=="&nbsp")
				continue;

			$hash_num = hexdec(substr(md5($cat),0,7));
			$label_theme = $color_theme[$hash_num%count($color_theme)];

			if ($label_theme=="") $label_theme = "default";
			$view .= "<a title='".htmlentities($cat,ENT_QUOTES,'UTF-8')."' class='label label-$label_theme' style='display: inline-block;' href='problem_list.php?keyword=".htmlentities(urlencode($cat),ENT_QUOTES,'UTF-8')."'>".mb_substr($cat,0,10,'utf8')."</a>&nbsp;";
		}
		echo "<div class=\"show_tag_controled\" style=\"float: right; \">";		
    	echo $view;		
		echo "</div></td>";
		
        if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
          if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
            echo "<td><a href=problem_df_change.php?id=".$row['problem_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">".($row['defunct']=="N"?"<span titlc='click to reserve it' class=green>$MSG_AVAILABLE</span>":"<span class=red title='click to be available'>$MSG_RESERVED</span>")."</a><td>";
		    if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$row['problem_id']]) ){
			    ?>
			             <a href=# onclick='javascript:if(confirm("<?php echo $MSG_DELETE."[".htmlentities($row['title'],ENT_QUOTES,"UTF-8")?>] ?    ")) 
					     location.href="problem_del.php?id=<?php echo $row['problem_id']?>&getkey=<?php echo $_SESSION[$OJ_NAME.'_'.'getkey']?>"'>
						<?php echo $MSG_DELETE ?>
			              </a>
			        <?php
		    }
          }
      if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$row['problem_id']]) ){
        echo "<td><a href=problem_edit.php?id=".$row['problem_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">$MSG_EDIT</a>";
        echo "<td><a href='javascript:phpfm(".$row['problem_id'].");'>$MSG_TEST_DATA</a>";
      }
    }
    echo "</tr>";
  }
?>
    <tr>
      <td colspan=2 style="height:40px;"><?php echo "$MSG_CHECK_TO"?></td>
      <td colspan=6>
      <input type=submit name='problem2contest' value='<?php echo $MSG_NEW_CONTEST?>'>
      <input type=submit name='enable' value='<?php echo $MSG_AVAILABLE ?>' onclick='$("form").attr("action","problem_df_change.php")'>
      <input type=submit name='disable' value='<?php echo $MSG_RESERVED ?>' onclick='$("form").attr("action","problem_df_change.php")'>
      <input type=submit name='plist' value='<?php echo $MSG_NEW_PROBLEM_LIST?>' onclick='$("form").attr("action","news_add_page.php")'>
      </td>
    </tr>

  </form>
</table>
</center>

<script src='../template/bs3/jquery.min.js' ></script>

<script>
function phpfm(pid){
  //alert(pid);
  $.post("phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
    if(status=="success"){
      document.location.href="phpfm.php?frame=3&pid="+pid;
    }
  });
}
function delPid(pid){

		let plist=sessionStorage.getItem('plist');
		if(typeof(plist)=='undefined'||plist==null) plist="";
		let oldArray=plist.split(',');
		oldArray=oldArray.filter(onlyUnique);
		let index=oldArray.indexOf(pid);
		//	console.log("remove:"+pid+" index:"+index);
			if(index>-1){
				oldArray.splice(index,1);
			}
			plist=oldArray.join();
		if(!!sessionStorage){
		        sessionStorage.setItem('plist',plist);
			$("input[name=hlist]").attr("value",plist);
		//	console.log(plist);
		}
}
function onlyUnique(value, index, array) {
  return array.indexOf(value) === index;
}
function addPid(pid){

		let plist=sessionStorage.getItem('plist');
		if(typeof(plist)=='undefined'||plist==null) plist="";
		let oldArray=plist.split(',');
		//oldArray=oldArray.filter(onlyUnique);
		let index=oldArray.indexOf(pid);
		//	console.log("add:"+pid);
			plist=oldArray.join();
			if(index<0){
				plist+=","+pid;
			}
		if(!!sessionStorage){
		        sessionStorage.setItem('plist',plist);
			$("input[name=hlist]").attr("value",plist);
		
		//	console.log(plist);
		}
}
$(document).ready(function(){
	let plist=sessionStorage.getItem('plist');
	if(typeof(plist)=="undefined" || plist == null) plist="";
	let oldArray=plist.split(',');
	oldArray=oldArray.filter(onlyUnique);
	for(let i=0;i<oldArray.length;i++){
		if(oldArray[i]!="")
			$('input[value='+oldArray[i]+']').prop("checked",true);
	}
	$("input[name=hlist]").val(plist);

	$('input[type=checkbox]').change(function(){
		
		let plist=sessionStorage.getItem('plist');
		if(typeof(plist)=='undefined'||plist==null) plist="";
		let oldArray=plist.split(',');
		oldArray=oldArray.filter(onlyUnique);
		let pid=$(this).attr('value');
		let index=oldArray.indexOf(pid);
//		console.log("pid:"+pid);
//	console.log("before:"+plist);
		if(typeof(pid)=='undefiend'||pid==null ){
//			console.log("all");
			$('input[type=checkbox]').each(function(){
				let pid=parseInt($(this).val());
				if(pid>0){
					if($(this).prop('checked')){
						addPid(""+pid);
					}else{
						delPid(""+pid);
					}
				}
			});
			return;
		}else{
			if($(this).prop('checked')){
				addPid(""+pid);
			}else{
				delPid(""+pid);
			}
		}
//	console.log("after:"+plist);
//		console.log(plist);

	
	});

});
</script>
</div>

<?php
if(!(isset($_GET['keyword']) && $_GET['keyword']!=""))
{
  echo "<div style='display:inline;'>";
  echo "<nav class='center'>";
  echo "<ul class='pagination pagination-sm'>";
  echo "<li class='page-item'><a href='problem_list.php?page=".(strval(1))."'>&lt;&lt;</a></li>";
  echo "<li class='page-item'><a href='problem_list.php?page=".($page==1?strval(1):strval($page-1))."'>&lt;</a></li>";
  for($i=$spage; $i<=$epage; $i++){
    echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='problem_list.php?page=".$i."'>".$i."</a></li>";
  }
  echo "<li class='page-item'><a href='problem_list.php?page=".($page==$pages?strval($page):strval($page+1))."'>&gt;</a></li>";
  echo "<li class='page-item'><a href='problem_list.php?page=".(strval($pages))."'>&gt;&gt;</a></li>";
  echo "</ul>";
  echo "</nav>";
  echo "</div>";
}
?>

</div>
