<?php
require("admin-header.php");
require_once("../include/set_get_key.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
  exit(1);
}
if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>
<title>User List</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_LIST?></h3></center>
<div class='' style="overflow:auto">
<?php
$sql = "select COUNT('user_id') AS ids FROM `users`";
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
  $sql = "select `user_id`,`nick`,email,`accesstime`,`reg_time`,`expiry_date`,`ip`,`school`,`group_name`,`parent_phone`,`defunct`,`coin_earned`,`coin_bonus`,`coin_spent` FROM `users` WHERE (user_id LIKE ?) OR (nick LIKE ?) OR (school LIKE ?)  OR (group_name LIKE ?) OR (parent_phone LIKE ?) or (ip like ?) ORDER BY `user_id` DESC";
  $result = pdo_query($sql,$keyword,$keyword,$keyword,$keyword,$keyword,$keyword);
}else if(isset($_GET['trash'])){
  $trash="&trash";
  $sql = "select `user_id`,`nick`,email,`accesstime`,`reg_time`,`expiry_date`,`ip`,`school`,`group_name`,`parent_phone`,`defunct`,`coin_earned`,`coin_bonus`,`coin_spent` FROM `users` where defunct='Y' ORDER BY `accesstime` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}else{
  $sql = "select `user_id`,`nick`,email,`accesstime`,`reg_time`,`expiry_date`,`ip`,`school`,`group_name`,`parent_phone`,`defunct`,`coin_earned`,`coin_bonus`,`coin_spent` FROM `users` where defunct='N' ORDER BY `accesstime` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}
?>

<center>
<form action=user_list.php class="form-search form-inline">
  <input type="text" name="keyword"  value="<?php echo htmlentities($gkeyword,ENT_QUOTES) ?>"  class="form-control search-query" placeholder="<?php echo $MSG_USER_ID.', '.$MSG_NICK.', '.$MSG_SCHOOL?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
  <a href="user_list.php?trash" title="<?php echo $MSG_VIEW_DISABLED_USER?>" ><i class='icon large trash color grey' ></i></a>
</form>
</center>

<center>
  <table width=100% border=1 style="text-align:center;" class="ui striped aligned table">
<thead>
    <tr>
    <th><?php echo $MSG_USER_ID?></th>
      <th><?php echo $MSG_NICK?></th>
      <th>IP</th>
      <th><?php echo $MSG_EMAIL?></th>
      <th><?php echo $MSG_SCHOOL?></th>
      <th><?php echo $MSG_GROUP_NAME?></th>
      <th><?php echo $MSG_PARENT_PHONE?></th>
      <th><?php echo $MSG_LAST_LOGIN?></th>
      <th><?php echo $MSG_REGISTER?></th>
      <th><?php echo $MSG_EXPIRY_DATE?></th>
      <th><?php echo $MSG_EARNED.$MSG_COIN?></th>
      <th><?php echo $MSG_BONUS.$MSG_COIN?></th>
      <th><?php echo $MSG_SPENT.$MSG_COIN?></th>
      <th><?php echo $MSG_COIN_BALANCE ?></th>
      <th><?php echo $MSG_STATUS?></th>
      <th><?php echo $MSG_ADMIN ?></th>
      <th><?php echo $MSG_SETPASSWORD?></th>
      <th><?php echo $MSG_PRIVILEGE."-".$MSG_ADD ?></th>
      </tr>
</thead>

    <?php
    foreach($result as $row){
      echo "<tr>";
        echo "<td><a href='../userinfo.php?user=".htmlentities(urlencode($row['user_id']))."'>".$row['user_id']."</a></td>";
        if($row['nick']=="") $row['nick']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='nick' user_id='".$row['user_id']."'>".$row['nick']."</span></td>";
        echo "<td><a href='user_list.php?keyword=".htmlentities(urlencode($row['ip']))."' >".$row['ip']."</td>";
        if($OJ_SaaS_ENABLE && $domain == $DOMAIN){
                echo "<td><a href='http://".$row['user_id'].".$DOMAIN' target=_blank >".$row['email']."&nbsp;</a></td>";
        }else{
                echo "<td>".$row['email']." </td>";
        }
        if($row['school']=="") $row['school']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='school' user_id='".$row['user_id']."'>".$row['school']."</span></td>";
        if($row['group_name']=="") $row['group_name']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='group_name' user_id='".$row['user_id']."'>".$row['group_name']."</span></td>";
        if($row['parent_phone']=="") $row['parent_phone']="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<td><span fd='parent_phone' user_id='".$row['user_id']."'>".$row['parent_phone']."</span></td>";
        echo "<td>".$row['accesstime']."</td>";
        echo "<td>".$row['reg_time']."</td>";
        $color="red";
        $edate= new DateTime($row['expiry_date']);
        $tomorrow= new DateTime(add_days(7));   // 7 日临期预警蓝色
        $today= new DateTime(add_days(0));
        if($edate>$tomorrow) $color="green";
        else if($edate>=$today) $color="blue";
        echo "<td><span fd='expiry_date' user_id='".$row['user_id']."' class='".$color."' >".$row['expiry_date']."</span></td>";

        echo "<td>".intval($row['coin_earned'])."</td>";
        echo "<td><span fd='coin_bonus' user_id='".$row['user_id']."'>".intval($row['coin_bonus'])."</span></td>";
        echo "<td><span fd='coin_spent' user_id='".$row['user_id']."'>".intval($row['coin_spent'])."</span></td>";
        echo "<td>".intval($row['coin_earned']+$row['coin_bonus']-$row['coin_spent'])."</td>";
        echo "<td>".($row['defunct']=="N"?"<span class=green >$MSG_NORMAL</span>":"<span class=red>$MSG_DELETED</span>")."</td>";
      if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) && $row['user_id']!=$_SESSION[$OJ_NAME."_user_id"]){
        echo "<td><a href=user_df_change.php?cid=".$row['user_id']."&getkey=".htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8').">".
           ($row['defunct']=="N"?"<span class='label label-danger' title='$MSG_CLICK_TO_DELETE'>$MSG_CLICK_TO_DELETE</span>":"<span class='label label-success' title='$MSG_CLICK_TO_RECOVER'>$MSG_CLICK_TO_RECOVER</span>")
            ."</a></td>";
      }else{
      	   echo "<td>&nbsp;</td>";
      }
        echo "<td><a class='label label-warning' href=changepass.php?uid=".$row['user_id']."&getkey=".htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8').">".$MSG_RESET."</a></td>";
        echo "<td><a class='label label-success' href=privilege_add.php?uid=".$row['user_id']."&getkey=".htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8').">".$MSG_ADD."</a></td>";
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
<script src="../include/qrcode.min.js"></script>
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
        $("span[fd=parent_phone]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let parent_phone=sp.text().trim();
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_parent_phone'><input type='hidden' name='user_id' value='"+user_id+"'><input type='text' name='parent_phone' value='"+parent_phone+"' selected='true' class='input-large' size=20 pattern='1[3-9][0-9]{9}' placeholder='11位手机号'></form>");
                        let ipt=sp.find("input[name=parent_phone]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newphone=sp.find("input[name=parent_phone]").val().trim();
                                if(newphone!=='' && !/^1[3-9][0-9]{9}$/.test(newphone)){
                                        alert('<?php echo $MSG_PARENT_PHONE; ?>: ' + newphone);
                                        sp.html(parent_phone || '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                        return;
                                }
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new parent_phone"+newphone);
                                        sp.html(newphone || '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
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
        $("span[fd=expiry_date]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let expiry_date=sp.text();
                        console.log("user_id:"+user_id+"  expiry_date:"+expiry_date);
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_expiry_date'><input type='hidden' name='user_id' value='"+user_id+"'><input type='date' name='expiry_date' value='"+expiry_date+"' selected='true' class='input-mini' size=2 ></form>");
                        let ipt=sp.find("input[name=expiry_date]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newexpiry_date=sp.find("input[name=expiry_date]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new expiry_date:"+newexpiry_date);
                                        sp.html(newexpiry_date);
                                });

                        });
                });

        });
        $("span[fd=coin_bonus]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let val=sp.text();
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_coin_bonus'><input type=hidden name='user_id' value='"+user_id+"'><input type='number' name='coin_bonus' value='"+val+"' selected='true' class='input-mini' size=6 ></form>");
                        let ipt=sp.find("input[name=coin_bonus]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newval=sp.find("input[name=coin_bonus]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new coin_bonus:"+newval);
                                        sp.html(newval);
                                });

                        });
                });


        });
        $("span[fd=coin_spent]").each(function(){
                let sp=$(this);
                let user_id=$(this).attr('user_id');
                $(this).dblclick(function(){
                        let val=sp.text();
                        sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='user_update_coin_spent'><input type=hidden name='user_id' value='"+user_id+"'><input type='number' name='coin_spent' value='"+val+"' selected='true' class='input-mini' size=6 ></form>");
                        let ipt=sp.find("input[name=coin_spent]");
                        ipt.focus();
                        ipt[0].select();
                        sp.find("input").change(function(){
                                let newval=sp.find("input[name=coin_spent]").val();
                                $.post("ajax.php",sp.find("form").serialize()).done(function(){
                                        console.log("new coin_spent:"+newval);
                                        sp.html(newval);
                                });

                        });
                });


        });

}
$(document).ready(function(){
        admin_mod();
        // 家长手机号悬浮 → 鼠标附近弹出查询二维码
        initParentPhoneQR();
});

// 悬浮 QR 浮层（鼠标附近弹出该学员的消课查询二维码）
function initParentPhoneQR(){
    // 计算 xiaoke.php 的绝对 URL（适应子目录部署）
    var basePath = window.location.pathname.replace(/\/admin\/.*$/, '/');
    var baseUrl  = window.location.protocol + '//' + window.location.host + basePath;

    $("span[fd=parent_phone]").each(function(){
        var sp = $(this);
        var user_id = sp.attr('user_id');
        var phone = sp.text().trim();
        if(!phone || !/^1[3-9][0-9]{9}$/.test(phone)) return;  // 没手机号就不显示

        sp.hover(function(e){
            // mouseenter: 创建浮层 + 生成 QR
            if($('#xiaoke-qr-float').length) $('#xiaoke-qr-float').remove();
            var qrd = $('<div id="xiaoke-qr-float"></div>').css({
                position: 'fixed',
                top:  e.clientY + 16,
                left: e.clientX + 16,
                'z-index': 99999,
                background: '#fff',
                padding: '10px',
                border: '1px solid #888',
                'border-radius': '6px',
                'box-shadow': '0 4px 16px rgba(0,0,0,0.25)',
                cursor: 'default'
            }).appendTo('body');

            var url = baseUrl + 'xiaoke.php?mode=parent'
                    + '&phone=' + encodeURIComponent(phone)
                    + '&student=' + encodeURIComponent(user_id);
            try {
                new QRCode(qrd[0], {
                    text: url,
                    width: 180, height: 180,
                    colorDark: '#000', colorLight: '#fff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch(err) {
                qrd.html('<div style="color:red;font-size:12px">QR 生成失败</div>');
            }
            // 加个说明文字
            qrd.append('<div style="font-size:11px;color:#666;margin-top:6px;text-align:center">扫码查 <b>'
                + $('<div>').text(user_id).html() + '</b> 消课</div>');
        }, function(){
            // mouseleave: 移除浮层
            $('#xiaoke-qr-float').remove();
        }).mousemove(function(e){
            // 跟随鼠标
            var qrd = $('#xiaoke-qr-float');
            if(qrd.length){
                qrd.css({ top: e.clientY + 16, left: e.clientX + 16 });
            }
        });
    });
}

</script>
