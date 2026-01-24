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
	
  if(isset($_POST['hlist']))$plist = trim($_POST['hlist']);
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
	  <input class="input input-large" style="width:100%;" size=71 value='<?php echo isset($title)?$title."-Copy":""?>' type=text name='title' id='title' > 
	  <input type=submit class='btn btn-success' value='<?php echo $MSG_SAVE?>' name=submit> 
	  <input class='btn btn-primary' id='ai_bt' type=button value='AI一下' onclick='ai_gen()' >
	  <input class='btn btn-danger'  type=reset value='<?php echo $MSG_RESET?>' onclick='setTimeout("ai_gen()",500);' >
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
<script>

	function ai_gen(filename){
		    let oldval=$('#ai_bt').val();
		    $('#ai_bt').val('AI思考中...请稍候...');
		    $('#ai_bt').prop('disabled', true);;
		    let title=$('#title').val();
		    $.ajax({
		    	url: '../<?php echo $OJ_AI_API_URL?>', 
			type: 'GET',
			data: { title: title },
			success: function(data) {
			    console.log(title);
			    if(title==""){
				    $('#title').val(data);
			    }else{
				    let description="<span class='md'>"+(data)+"</span>";
				    let preview=$("#previewFrame").contents();
				    $("textarea").eq(0).val(description); // 假设 #file_data 是 div
			    }
		    	    $('#ai_bt').prop('disabled', false);;
			    $('#ai_bt').val('AI一下');
			},
			error: function() {
			    $('#ai_bt').val('获取数据失败');
		    	    $('#ai_bt').prop('disabled', false);;
			}
		    });
	}
</script>
