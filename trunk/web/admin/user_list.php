<?php
require("admin-header.php");
require_once("../include/set_get_key.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}
if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>
<title>User List</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_LIST?></h3></center>
<div class='container'>
<?php
$sql = "SELECT COUNT('user_id') AS ids FROM `users`";
$result = pdo_query($sql);
$row = $result[0];
$ids = intval($row['ids']);
$idsperpage = 25;
$pages = intval(ceil($ids/$idsperpage));
if(isset($_GET['page'])){ 
  $page = intval($_GET['page']);
}else{ 
  $page = 1;
}
$pagesperframe = 5;
$frame = intval(ceil($page/$pagesperframe));
$spage = ($frame-1)*$pagesperframe+1;
$epage = min($spage+$pagesperframe-1, $pages);
$sid = ($page-1)*$idsperpage;
$sql = "";
$gkeyword="";
$trash="";
if(isset($_GET['keyword']) && $_GET['keyword']!=""){
  $gkeyword = $_GET['keyword'];
  $keyword = "%$gkeyword%";
  $sql = "SELECT `user_id`,`nick`,email,`accesstime`,`reg_time`,`ip`,`school`,`group_name`,`defunct` FROM `users` WHERE (user_id LIKE ?) OR (nick LIKE ?) OR (school LIKE ?) or (ip like ?) ORDER BY `user_id` DESC";
  $result = pdo_query($sql,$keyword,$keyword,$keyword,$keyword);
}else if(isset($_GET['trash'])){
  $trash="&trash";
  $sql = "SELECT `user_id`,`nick`,email,`accesstime`,`reg_time`,`ip`,`school`,`group_name`,`defunct` FROM `users` where defunct='Y' ORDER BY `accesstime` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}else{
  $sql = "SELECT `user_id`,`nick`,email,`accesstime`,`reg_time`,`ip`,`school`,`group_name`,`defunct` FROM `users` where defunct='N' ORDER BY `accesstime` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}
?>

<center>
<form action=user_list.php class="form-search form-inline">
  <input type="text" name="keyword"  value="<?php echo htmlentities($gkeyword,ENT_QUOTES) ?>"  class="form-control search-query" placeholder="<?php echo $MSG_USER_ID.', '.$MSG_NICK.', '.$MSG_SCHOOL?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
   <a href="user_list.php?trash"><i class='icon large trash color grey' ></i></a>
</form>
</center>

<center>
  <table width=100% border=1 style="text-align:center;">
    <tr>
    <td><?php echo $MSG_USER_ID?></td>
      <td><?php echo $MSG_NICK?></td>
      <td>IP</td>
      <td><?php echo $MSG_EMAIL?></td>
      <td><?php echo $MSG_SCHOOL?></td>
      <td><?php echo $MSG_GROUP_NAME?></td>
      <td><?php echo $MSG_LAST_LOGIN?></td>
      <td><?php echo $MSG_REGISTER?></td>
      <td><?php echo $MSG_STATUS?></td>
      <td><?php echo $MSG_SETPASSWORD?></td>
      <td><?php echo $MSG_PRIVILEGE."-".$MSG_ADD ?></td>
      </tr>
    <?php
    foreach($result as $row){
      echo "<tr>";
        echo "<td><a href='../userinfo.php?user=".$row['user_id']."'>".$row['user_id']."</a></td>";
        echo "<td><span fd='nick' user_id='".$row['user_id']."'>".$row['nick']."</span></td>";
        echo "<td><a href='user_list.php?keyword=".htmlentities(urlencode($row['ip']))."' >".$row['ip']."</td>";
        if($OJ_SaaS_ENABLE && $domain == $DOMAIN){
                echo "<td><a href='http://".$row['user_id'].".$DOMAIN' target=_blank >".$row['email']."&nbsp;</a></td>";
        }else{
                echo "<td>".$row['email']."</td>";
        }
        if($row['school']=="") $row['school']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='school' user_id='".$row['user_id']."'>".$row['school']."</span></td>";
        if($row['group_name']=="") $row['group_name']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='group_name' user_id='".$row['user_id']."'>".$row['group_name']."</span></td>";
        echo "<td>".$row['accesstime']."</td>";
        echo "<td>".$row['reg_time']."</td>";
      if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
        echo "<td><a href=user_df_change.php?cid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">".
                  ($row['defunct']=="N"?"<span class=green>$MSG_NORMAL</span>":"<span class=red>$MSG_DELETED</span>")."</a></td>";
      }
      else {
        echo "<td>".($row['defunct']=="N"?"<span>$MSG_NORMAL</span>":"<span>$MSG_DELETED</span>")."</td>";
      }
        echo "<td><a href=changepass.php?uid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">"."Reset"."</a></td>";
        echo "<td><a href=privilege_add.php?uid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">"."Add"."</a></td>";
      echo "</tr>";
    } ?>
  </table>
</center>

<?php
if(!(isset($_GET['keyword']) && $_GET['keyword']!=""))
{
  echo "<div style='display:inline;'>";
  echo "<nav class='center'>";
  echo "<ul class='pagination pagination-sm'>";
  echo "<li class='page-item'><a href='user_list.php?page=".(strval(1))."$trash'>&lt;&lt;</a></li>";
  echo "<li class='page-item'><a href='user_list.php?page=".($page==1?strval(1):strval($page-1))."$trash'>&lt;</a></li>";
  for($i=$spage; $i<=$epage; $i++){
    echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='user_list.php?page=".$i."$trash'>".$i."</a></li>";
  }
  echo "<li class='page-item'><a href='user_list.php?page=".($page==$pages?strval($page):strval($page+1))."$trash'>&gt;</a></li>";
  echo "<li class='page-item'><a href='user_list.php?page=".(strval($pages))."$trash'>&gt;&gt;</a></li>";
  echo "</ul>";
  echo "</nav>";
  echo "</div>";
}
?>

</div>
<script>
function admin_mod(){
        $("span[fd=group_name]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let group_name=sp.text();
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_group_name'><input type='hidden' name='user_id' value='"+user_id+"'><input type='text' name='group_name' value='"+group_name+"' selected='true' class='input-large' size=20 ></form>");
                        let ipt=sp.find("input[name=group_name]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newgroup_name=sp.find("input[name=group_name]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new group_name"+newgroup_name);
                                        sp.html(newgroup_name);
                                });

                        });
                });
        });
        $("span[fd=school]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let school=sp.text();
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_school'><input type='hidden' name='user_id' value='"+user_id+"'><input type='text' name='school' value='"+school+"' selected='true' class='input-large' size=20 ></form>");
                        let ipt=sp.find("input[name=school]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newschool=sp.find("input[name=school]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new school"+newschool);
                                        sp.html(newschool);
                                });

                        });
                });
        });

        $("span[fd=nick]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let nick=sp.text();
                        console.log("user_id:"+user_id+"  nick:"+nick);
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_nick'><input type='hidden' name='user_id' value='"+user_id+"'><input type='text' name='nick' value='"+nick+"' selected='true' class='input-mini' size=2 ></form>");
                        let ipt=sp.find("input[name=nick]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newnick=sp.find("input[name=nick]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new nick:"+newnick);
                                        sp.html(newnick);
                                });

                        });
                });


        });

}
$(document).ready(function(){
        admin_mod();
});

</script>
