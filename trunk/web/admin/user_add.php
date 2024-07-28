<?php require_once("admin-header.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'user_adder']) )){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>Add User</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_ADD?></h3></center>

<div class='container'>

<?php
if(isset($_POST['do'])){
  require_once("../include/check_post_key.php");
  require_once("../include/my_func.inc.php");

  $pieces = explode("\n", trim($_POST['ulist']));
  //想支持中文，修改下行代码为：$pieces = preg_replace("/[^\x20-\x7e\x{4e00}-\x{9fa5}]/u", " ", $pieces);  //修改代码支持user_id为中文
  $pieces = preg_replace("/[^\x20-\x7e]/", " ", $pieces);  //!!important

  $ulist = "";
  if(count($pieces)>0 && strlen($pieces[0])>0){
    for($i=0; $i<count($pieces); $i++){
      $id_pw = explode(" ", trim($pieces[$i]));
      if(count($id_pw) != 2){
        echo "&nbsp;&nbsp;&nbsp;&nbsp;".$id_pw[0]." ... Error : Line format error!<br>";
        for($j=0; $j<count($id_pw); $j++)
        {
          $ulist = $ulist.$id_pw[$j]." ";
        }
        $ulist = trim($ulist)."\n";
      } else {
        $sql = "SELECT `user_id` FROM `users` WHERE `users`.`user_id` = ?";
        $result = pdo_query($sql, $id_pw[0]);
        $rows_cnt = count($result);

        if($rows_cnt == 1){
          echo "&nbsp;&nbsp;&nbsp;&nbsp;".$id_pw[0]." ... Error : User already exist!<br>";
          $ulist = $ulist.$id_pw[0]." ".$id_pw[1]."\n";
        } else {
          $passwd = pwGen($id_pw[1]);
          $sql = "INSERT INTO `users` (`user_id`, `password`, `reg_time`, `nick`) VALUES (?, ?, NOW(), ?);";
          pdo_query($sql, $id_pw[0], $passwd, $id_pw[0]);
          echo $id_pw[0]." is added!<br>";

          $ip = ($_SERVER['REMOTE_ADDR']);
          $sql="INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,?,?,NOW())";
          pdo_query($sql, $id_pw[0], "user added", $ip);
        }
      }
    }
    echo "<br>Remained lines have error!<hr>";
  }
}
?>

<form action=user_add.php method=post class="form-horizontal">
  <div>
    <label class="col-sm"><?php echo $MSG_USER_ID?> <?php echo $MSG_PASSWORD?></label>
  </div>
  <div>
    <?php echo "( Add new user & password with newline &#92;n )"?>
    <br>
    <table width="100%">
      <tr>
        <td height="*">
          <p align=left>
            <textarea name='ulist' rows='10' style='width:100%;' placeholder='userid1 password1<?php echo "\n"?>userid2 password2<?php echo "\n"?>userid3 password3<?php echo "\n"?>
            <?php echo $MSG_PRIVATE_USERS_ADD?><?php echo "\n"?>'><?php if(isset($ulist)){ echo $ulist;}?></textarea>
          </p>
        </td>
      </tr>
    </table>
  </div>

  <div class="form-group">
    <?php require_once("../include/set_post_key.php");?>
    <div class="col-sm-offset-4 col-sm-2">
      <button name="do" type="hidden" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE?></button>
    </div>
    <div class="col-sm-2">
      <button name="submit" type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
    </div>
  </div>

</form>

</div>


