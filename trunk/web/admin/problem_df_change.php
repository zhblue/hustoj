<?php
require_once("admin-header.php");

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if($_SERVER['REQUEST_METHOD']=="POST"){
  //require_once("../include/check_post_key.php");
}else{
  require_once("../include/check_get_key.php");
}
?>

<?php
$plist = "";
if(isset($_POST['pid'])&&!empty($_POST['pid'])) {
        sort($_POST['pid']);
        foreach($_POST['pid'] as $i){
          if($plist)
            $plist.=','.intval($i);
          else
            $plist = $i;
        }
}

//echo "===".$plist;
  $plist = trim($_POST['hlist']);
  $pieces = explode(",",$plist );
  $pieces = array_unique($pieces);
  if($pieces[0]=="")unset($pieces[0]);
  $plist=implode(",",$pieces);

if(isset($_POST['enable'])&&$plist){
  $sql = "UPDATE `problem` SET defunct='N' WHERE `problem_id` IN ($plist)";           
  pdo_query($sql);
}else if(isset($_POST['disable'])&&$plist){
  $sql = "UPDATE `problem` SET defunct='Y' WHERE `problem_id` IN ($plist)";           
  pdo_query($sql);
}else{
  $id = intval($_GET['id']);
  $sql = "SELECT `defunct` FROM `problem` WHERE `problem_id`=?";
  $result = pdo_query($sql,$id);

  $row = $result[0];
  $defunct = $row[0];
  echo $defunct;

  if($defunct=='Y') $sql = "UPDATE `problem` SET `defunct`='N' WHERE `problem_id`=?";
  else $sql = "UPDATE `problem` SET `defunct`='Y' WHERE `problem_id`=?";
   if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$id]) ){
        pdo_query($sql,$id) ;
   }
}
?>

<script language=javascript>history.go(-1);</script>
