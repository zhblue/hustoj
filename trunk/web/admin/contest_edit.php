<html>
<head>
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Content-Language" content="zh-cn">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit Contest</title>
</head>
<hr>

<?php 
  require_once("../include/db_info.inc.php");
  require_once("../lang/$OJ_LANG.php");
  require_once("../include/const.inc.php");

  require_once("admin-header.php");
  if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))){
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
  }
  echo "<center><h3>"."Edit-".$MSG_CONTEST."</h3></center>";
  include_once("kindeditor.php") ;
?>

<body leftmargin="30" >
<?php
if(isset($_POST['startdate'])){
  require_once("../include/check_post_key.php");

  $starttime = $_POST['startdate']." ".intval($_POST['shour']).":".intval($_POST['sminute']).":00";
  $endtime = $_POST['enddate']." ".intval($_POST['ehour']).":".intval($_POST['eminute']).":00";
  //echo $starttime;
  //echo $endtime;
 
  $title = $_POST['title'];
  $private = $_POST['private'];
  $password = $_POST['password'];
  $description = $_POST['description'];
 
  if(false){
    $title = stripslashes($title);
    $private = stripslashes($private);    
    $password = stripslashes($password);
    $description = stripslashes($description);
  }

  $lang = $_POST['lang'];
  $langmask=0;
  foreach($lang as $t){
    $langmask += 1<<$t;
  } 

  $langmask = ((1<<count($language_ext))-1)&(~$langmask);
  //echo $langmask; 

  $cid=intval($_POST['cid']);

  if(!(isset($_SESSION[$OJ_NAME.'_'."m$cid"])||isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) exit();

  $description = str_replace("<p>", "", $description); 
  $description = str_replace("</p>", "<br />", $description);
  $description = str_replace(",", "&#44;", $description);


  $sql = "UPDATE `contest` SET `title`=?,`description`=?,`start_time`=?,`end_time`=?,`private`=?,`langmask`=?,`password`=? WHERE `contest_id`=?";
  //echo $sql;
  pdo_query($sql,$title,$description,$starttime,$endtime,$private,$langmask,$password,$cid);

  $sql = "DELETE FROM `contest_problem` WHERE `contest_id`=?";
  pdo_query($sql,$cid);
  $plist=trim($_POST['cproblem']);
  $pieces = explode(',', $plist);

  if(count($pieces)>0 && strlen($pieces[0])>0){
    $sql_1 = "INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) VALUES (?,?,?)";
    
    $plist="";
    pdo_query("update solution set num=-1 where contest_id=?",$cid);
    $num=0;
    for($i=0; $i<count($pieces); $i++){
      $sql="select problem_id from problem where problem_id=?";
      $pid=intval($pieces[$i]);
      $has=pdo_query($sql,$pid);
      if(count($has) > 0) {
         if($plist) $plist.=",";
         $plist.=intval($pieces[$i]);
         pdo_query($sql_1,$cid,$pieces[$i],$num);
	 $sql="UPDATE `contest_problem` SET `c_accepted`=(SELECT count(1) FROM `solution` WHERE `problem_id`=? and contest_id=? AND `result`=4) WHERE `problem_id`=? and contest_id=?";
	 pdo_query($sql,$pid,$cid,$pid,$cid);
	 $sql="UPDATE `contest_problem` SET `c_submit`=(SELECT count(1) FROM `solution` WHERE `problem_id`=? and contest_id=?) WHERE `problem_id`=? and contest_id=?";
	 pdo_query($sql,$pid,$cid,$pid,$cid);
      	 $sql_2 = "update solution set num=? where contest_id=? and problem_id=?;";
      	 pdo_query($sql_2,$num,$cid,$pid);
         $num++;
      }else{
         print("Problem not exists:".$pieces[$i]."<br>\n");
      }
    }

    $sql = "update `problem` set defunct='N' where `problem_id` in ($plist)";
    pdo_query($sql) ;
  }

  $sql = "DELETE FROM `privilege` WHERE `rightstr`=?";
  pdo_query($sql,"c$cid");
  $pieces = explode("\n", trim($_POST['ulist']));
  $pieces = array_unique($pieces);
  if(count($pieces)>0 && strlen($pieces[0])>0){
    $sql_1 = "INSERT INTO `privilege`(`user_id`,`rightstr`) VALUES (?,?)";
    for($i=0; $i<count($pieces); $i++){
      pdo_query($sql_1,trim($pieces[$i]),"c$cid") ;
    }
  }

  echo "<script>window.location.href=\"contest_list.php\";</script>";
  exit();
}else{
  $cid = intval($_GET['cid']);
  $sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
  $result = pdo_query($sql,$cid);

  if(count($result)!=1){
    echo "No such Contest!";
    exit(0);
  }

  $row = $result[0];
  $starttime = $row['start_time'];
  $endtime = $row['end_time'];
  $private = $row['private'];
  $password = $row['password'];
  $langmask = $row['langmask'];
  $description = $row['description'];
  $title = htmlentities($row['title'],ENT_QUOTES,"UTF-8");

  $plist = "";
  $sql = "SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? ORDER BY `num`";
  $result=pdo_query($sql,$cid);

  foreach($result as $row){
    if($plist) $plist .= ",";
    $plist.=$row[0];
  }

  $ulist = "";
  $sql = "SELECT `user_id` FROM `privilege` WHERE `rightstr`=? order by user_id";
  $result = pdo_query($sql,"c$cid");

  foreach($result as $row){
    if($ulist) $ulist .= "\n";
    $ulist .= $row[0];
  } 
}
?>

<div class="container">
  <form method=POST>
    <?php require_once("../include/set_post_key.php");?>
    <input type=hidden name='cid' value=<?php echo $cid?>>
    <p align=left>
      <?php echo "<h3>".$MSG_CONTEST."-".$MSG_TITLE."</h3>"?>
      <input class="input input-xxlarge" style="width:100%;" type=text name=title value="<?php echo $title?>"><br><br>
    </p>
    <p align=left>
      <?php echo $MSG_CONTEST.$MSG_Start?>:
      <input class=input-large type=date name='startdate' value='<?php echo substr($starttime,0,10)?>' size=4 >
      Hour: <input class=input-mini type=text name=shour size=2 value='<?php echo substr($starttime,11,2)?>'>&nbsp;
      Minute: <input class=input-mini type=text name=sminute value='<?php echo substr($starttime,14,2)?>' size=2 >
    </p>
    <p align=left>
      <?php echo $MSG_CONTEST.$MSG_End?>:
      <input class=input-large type=date name='enddate' value='<?php echo substr($endtime,0,10)?>' size=4 >
      Hour: <input class=input-mini type=text name=ehour size=2 value='<?php echo substr($endtime,11,2)?>'>&nbsp;
      Minute: <input class=input-mini type=text name=eminute value='<?php echo substr($endtime,14,2)?>' size=2 >
    </p>
    <br>
    <p align=left>
      <?php echo $MSG_CONTEST."-".$MSG_PROBLEM_ID?>
      <?php echo "( Add problemIDs with coma , )"?><br>
      <input id="plist" onchange="showTitles()" class=input-xxlarge type=text style="width:100%" name=cproblem value='<?php echo $plist?>'>
      <div id="ptitles"></div>
    </p>
    <br>
    <p align=left>
      <?php echo "<h4>".$MSG_CONTEST."-".$MSG_Description."</h4>"?>
      <textarea class=kindeditor rows=13 name=description cols=80>
        <?php echo htmlentities($description,ENT_QUOTES,'UTF-8')?>
      </textarea>
      <br>
      <table width="100%">
        <tr>
          <td rowspan=2>
            <p aligh=left>
              <?php echo $MSG_CONTEST."-".$MSG_LANG?>
              <?php echo "( Add PLs with Ctrl+click )"?><br>
              <?php echo $MSG_PLS_ADD?><br>
              <select name="lang[]" multiple="multiple" style="height:220px">
              <?php
              $lang_count = count($language_ext);
              $lang = (~((int)$langmask))&((1<<$lang_count)-1);

              if(isset($_COOKIE['lastlang'])) $lastlang=$_COOKIE['lastlang'];
              else $lastlang = 0;

              for($i=0; $i<$lang_count; $i++){
		       if( (1<<$i) & $OJ_LANGMASK ) continue;
                echo "<option value=$i ".( $lang&(1<<$i)?"selected":"").">".$language_name[$i]."</option>";
              }
              ?>
              </select>
            </p>
          </td>

          <td height="10px">
            <p align=left>
              <?php echo $MSG_CONTEST."-".$MSG_Public?>:
              <select name=private style="width:150px;">
                <option value=0 <?php echo $private=='0'?'selected=selected':''?>><?php echo $MSG_Public?></option>
                <option value=1 <?php echo $private=='1'?'selected=selected':''?>><?php echo $MSG_Private?></option>
              </select>
              <?php echo $MSG_CONTEST."-".$MSG_PASSWORD?>:
              <input type=text name=password style="width:150px;" value='<?php echo htmlentities($password,ENT_QUOTES,'utf-8')?>'>
            </p>
          </td>
        </tr>
        <tr>
          <td height="*">
            <p align=left>
              <?php echo $MSG_CONTEST."-".$MSG_USER?>
              <?php echo "( Add private contest's userIDs with newline &#47;n )"?>
              <br>
              <textarea name='ulist' rows='10' style='width:100%;' placeholder='user1<?php echo "\n"?>user2<?php echo "\n"?>user3<?php echo "\n"?>
              <?php echo $MSG_PRIVATE_USERS_ADD?><?php echo "\n"?>'><?php if(isset($ulist)){ echo $ulist;}?></textarea>
            </p>
          </td>
        </tr>
      </table>

      <div align=center>
        <?php require_once("../include/set_post_key.php");?>
        <input type=submit value='<?php echo $MSG_SAVE?>' name=submit> <input type=reset value=Reset name=reset>
      </div>
    </p>
  </form>
</div>
<script>
	function showTitles(){
		let ts=$("#ptitles");
		let pids=$("#plist").val().split(",");
		let html="";
		pids.forEach(function(v,i,a){
			let title=$.ajax({url:"ajax.php",method:"post",data:{"pid":v,"m":"problem_get_title"},async:false}).responseText;
			html+=(v)+":<a href='../problem.php?id="+v+"' target='_blank'>"+title+"</a><br>\n";
			console.log(v);
		});
		ts.html(html);
		
	}
	$(document).ready(function(){
		showTitles();
	
	});

</script>
</body>
</html>

