<?php
require_once("../include/db_info.inc.php");
require_once("admin-header.php");
require_once("../include/my_func.inc.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))) {
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}
?>
  <html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit Problem</title>
</head>
<hr>
  
  <?php
echo "<center><h3>"."Edit-".$MSG_PROBLEM."</h3></center>";
include_once("kindeditor.php") ;
?>


<body leftmargin="30" >
  <div id="main" class="container">
    <?php
    if (isset($_GET['id'])) {
      ;//require_once("../include/check_get_key.php");
        $pid=intval($_GET['id']);
        if(! (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$pid])) ){
                echo "No Privilege.";
                exit(0);
        }
    ?>

    <form method=POST action=problem_edit.php>
      <?php
      $sql = "SELECT * FROM `problem` WHERE `problem_id`=?";
      $result = pdo_query($sql,intval($_GET['id']));
      $row = $result[0];
      ?>

      <input type=hidden name=problem_id value='<?php echo $row['problem_id']?>'>
      <p align=left>
        <center>
          <h3>
          <?php echo $row['problem_id']?>: <input class="input input-xxlarge" style='width:90%' type=text name=title value='<?php echo htmlentities($row['title'],ENT_QUOTES,"UTF-8")?>'>
          </h3>
        </center>
      </p>
        <p align=left>
          <?php echo $MSG_Time_Limit?>
          <input class="input input-mini" type=number min="0.001" max="300" step="0.001" name=time_limit size=20 value="<?php echo $row['time_limit']?>"> sec
          <?php echo $MSG_Memory_Limit?>
          <input class="input input-mini" type=number min="1" max="1024" step="1" name=memory_limit size=20 value="<?php echo $row['memory_limit']?>"> MiB
        </p>
      <p align=left>
        <?php echo "<h4>".$MSG_Description."</h4>"?>
        <textarea class="kindeditor" rows=13 name=description cols=80><?php echo htmlentities($row['description'],ENT_QUOTES,"UTF-8")?></textarea><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_Input."</h4>"?>
        <textarea class="kindeditor" rows=13 name=input cols=80><?php echo htmlentities($row['input'],ENT_QUOTES,"UTF-8")?></textarea><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_Output."</h4>"?>
        <textarea  class="kindeditor" rows=13 name=output cols=80><?php echo htmlentities($row['output'],ENT_QUOTES,"UTF-8")?></textarea><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_Sample_Input."</h4>"?>
        <textarea  class="input input-large" style="width:100%;" rows=13 name=sample_input><?php echo htmlentities($row['sample_input'],ENT_QUOTES,"UTF-8")?></textarea><br><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_Sample_Output."</h4>"?>
        <textarea  class="input input-large" style="width:100%;" rows=13 name=sample_output><?php echo htmlentities($row['sample_output'],ENT_QUOTES,"UTF-8")?></textarea><br><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_HINT."</h4>"?>
        <textarea class="kindeditor" rows=13 name=hint cols=80><?php echo htmlentities($row['hint'],ENT_QUOTES,"UTF-8")?></textarea><br>
      </p>

      <p>
        <?php echo "<h4>".$MSG_SPJ."</h4>"?>
        <?php echo "(".$MSG_HELP_SPJ.")"?><br>
        <input type=radio name=spj value='0' <?php echo $row['spj']=="0"?"checked":""?> title='Normal Judger'><?php echo $MSG_NJ?><br>
        <input type=radio name=spj value='1' <?php echo $row['spj']=="1"?"checked":""?> title='Special Judger'><?php echo $MSG_SPJ?><br>
        <input type=radio name=spj value='2' <?php echo $row['spj']=="2"?"checked":""?> title='Raw Text Judger' ><?php echo $MSG_RTJ?><br>
      </p>

      <p align=left>
        <?php echo "<h4>".$MSG_SOURCE."</h4>"?>
        <textarea name=source style="width:100%;" rows=1><?php echo htmlentities($row['source'],ENT_QUOTES,"UTF-8")?></textarea><br>

        <?php echo "<h4>".$MSG_REMOTE_OJ."</h4>"?>
        <input name=remote_oj value='<?php echo htmlentities($row['remote_oj'],ENT_QUOTES,"UTF-8")?>' >
        <input name=remote_id value='<?php echo htmlentities($row['remote_id'],ENT_QUOTES,"UTF-8")?>' ><br>
      </p>

      <div align=center>
        <?php require_once("../include/set_post_key.php");?>
        <input type=submit value='<?php echo $MSG_SAVE?>' name=submit>
      </div>
    </form>

  </div>

<script src="<?php echo $OJ_CDN_URL."/template/bs3/"?>marked.min.js"></script>
<script>
  function transform(){
        let height=document.body.clientHeight;
        let width=parseInt(document.body.clientWidth*0.6);
        let width2=parseInt(document.body.clientWidth*0.4);
	if(width<500) width2=300;
	let submitURL="../problem.php?id=<?php echo $pid ?>";
        console.log(width);
        let main=$("#main");
        let problem=main.html();
                main.removeClass("container");
                main.css("width",width2);
                main.css("margin-left","10px");
                main.parent().append("<div id='preview' class='container' style='opacity:0.95;position:fixed;z-index:1000;top:49px;right:-"+width2+"px'></div>");
                $("#preview").html("<iframe id='previewFrame' src='"+submitURL+"&spa' width='"+width+"px' height='"+height+"px' ></iframe>");
        $("#submit").remove();
        setTimeout('hide()',1500);	
	$("input").keyup(sync);
	$("textarea").keyup(sync);
  }
  function hide(){
	let preview=$("#previewFrame").contents();
	preview.find(".ui.buttons").hide();
	preview.find("span.ui.label").eq(2).hide();
	preview.find("span.ui.label").eq(3).hide();
	preview.find("span.ui.label").eq(4).hide();
	preview.find("span.ui.label").eq(5).hide();
	preview.find("#show_tag_div").parent().hide();
	sync();
//	preview.find("h1:first").parent().parent().hide();
  }
  function sync(){
	console.log("sync...");
	let preview=$("#previewFrame").contents();
	let title=$("input[name=title]").val();
	preview.find("h1:first").html(title);
	let time=$("input[name=time_limit]").val();
	preview.find("span.ui.label").eq(0).html("<?php echo $MSG_Time_Limit ?>："+time);
	let memory=$("input[name=memory_limit]").val();
	preview.find("span.ui.label").eq(1).html("<?php echo $MSG_Memory_Limit ?>："+memory);
	
	let description=$("textarea").eq(1).val();
	preview.find("#description").html(description);
	preview.find("#description .md").each(function(){
		$(this).html(marked.parse($(this).html()));
	});
  
	let input=$("textarea").eq(3).val();
	preview.find("#input").html(input);
	preview.find("#input .md").each(function(){
		$(this).html(marked.parse($(this).html()));
	});
	let output=$("textarea").eq(5).val();
	preview.find("#output").html(output);
	preview.find("#output .md").each(function(){
		$(this).html(marked.parse($(this).html()));
	});

	let sinput=$("textarea").eq(6).val();
	preview.find("#sinput").html(sinput);
	let soutput=$("textarea").eq(7).val();
	preview.find("#soutput").html(soutput);
	let hint=$("textarea").eq(9).val();
	preview.find("#hint").html(hint);
	preview.find("#hint .md").each(function(){
		$(this).html(marked.parse($(this).html()));
	});
	$("#previewFrame")[0].contentWindow.MathJax.typeset();
  }
  $(document).ready(function(){
  	 <?php if (!(isset($OJ_OLD_FASHINED) && $OJ_OLD_FASHINED ) echo " transform();" ?>
  
  }); 
</script>
    <?php
    }
    else {
      require_once("../include/check_post_key.php");
      $id = intval($_POST['problem_id']);
      if(! (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$id])) ){
                echo "No Privilege.";
                exit(0)    ;
      }
      if (!(isset($_SESSION[$OJ_NAME.'_'."p$id"]) || isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']) )) exit();

      $title = $_POST['title'];
      $title = str_replace(",", "&#44;", $title);

      $time_limit = $_POST['time_limit'];

      $memory_limit = $_POST['memory_limit'];

      $description = $_POST['description'];
     // $description = str_replace("<p>", "", $description);
     // $description = str_replace("</p>", "<br />", $description);
      $description = str_replace(",", "&#44;", $description);

      $input = $_POST['input'];
     // $input = str_replace("<p>", "", $input);
     // $input = str_replace("</p>", "<br />", $input);
      $input = str_replace(",", "&#44;", $input);

      $output = $_POST['output'];
     // $output = str_replace("<p>", "", $output);
     // $output = str_replace("</p>", "<br />", $output);
      $output = str_replace(",", "&#44;", $output);

      $sample_input = $_POST['sample_input'];
      $sample_output = $_POST['sample_output'];
      //if ($sample_input=="") $sample_input="\n";
      //if ($sample_output=="") $sample_output="\n";

      $hint = $_POST['hint'];
     // $hint = str_replace("<p>", "", $hint);
    //  $hint = str_replace("</p>", "<br />", $hint);
      $hint = str_replace(",", "&#44;", $hint);

      $source = $_POST['source'];
      $remote_oj= $_POST['remote_oj'];
      $remote_id = $_POST['remote_id'];
      $spj = $_POST['spj'];

      if (false) {
        $title = stripslashes($title);
        $time_limit = stripslashes($time_limit);
        $memory_limit = stripslashes($memory_limit);
        $description = stripslashes($description);
        $input = stripslashes($input);
        $output = stripslashes($output);
        $sample_input = stripslashes($sample_input);
        $sample_output = stripslashes($sample_output);
        //$test_input = stripslashes($test_input);
        //$test_output = stripslashes($test_output);
        $hint = stripslashes($hint);
        $source = stripslashes($source);
        $spj = stripslashes($spj);
      }

      $title = ($title);
      $description = ($description);
      $input = ($input);
      $output = ($output);
      $hint = ($hint);
      $basedir = $OJ_DATA."/$id";

      echo "Problem Updated!<br>";

      if ($sample_input && file_exists($basedir."/sample.in")) {
        //mkdir($basedir);
        $fp = fopen($basedir."/sample.in","w");
        fputs($fp,preg_replace("(\r\n)","\n",$sample_input));
        fclose($fp);

        $fp = fopen($basedir."/sample.out","w");
        fputs($fp,preg_replace("(\r\n)","\n",$sample_output));
        fclose($fp);
      }

      $spj = intval($spj);

      $sql = "UPDATE `problem` SET `title`=?,`time_limit`=?,`memory_limit`=?, `description`=?,`input`=?,`output`=?,`sample_input`=?,`sample_output`=?,`hint`=?,`source`=?,`spj`=?,remote_oj=?,remote_id=?,`in_date`=NOW() WHERE `problem_id`=?";

      @pdo_query($sql,$title,$time_limit,$memory_limit,$description,$input,$output,$sample_input,$sample_output,$hint,$source,$spj,$remote_oj,$remote_id,$id);

      echo "Edit OK!<br>";
      echo "<a href='../problem.php?id=$id'>See The Problem!</a>";
    }
    ?>
</body>
</html>
