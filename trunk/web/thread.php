<?php
require_once("discuss_func.inc.php");
$tid = intval($_REQUEST['tid']);
if(isset($_GET['cid']))
  $cid = intval($_GET['cid']);
$sql = "SELECT t.`title`, `cid`, `pid`, `status`, `top_level` FROM `topic` t LEFT JOIN contest_problem cp on cp.problem_id=t.pid   WHERE `tid`=? AND `status`<=1";
$result = pdo_query($sql,$tid) ;
$rows_cnt = count($result) ;
$row = $result[0];

if($row['cid']>0)
      $cid = $row['cid'];

if($row['pid']>0 && $row['cid']>0){
      $pid = pdo_query("SELECT num FROM contest_problem WHERE problem_id=? AND contest_id=?",$row['pid'],$row['cid'])[0][0];
      $pid = $PID[$pid];
}else{
      $pid = $row['pid'];
}

$problemid = $row['pid'];
$titles = $row['title'];
$toplevels = $row['top_level'];

$isadmin = isset($_SESSION[$OJ_NAME.'_'.'administrator']);
?>
<center>
<table style='width:80%;' table class='table table-hover'>
  <thead>
    <tr class='toprow' align='center'>
      <td width='5%' class='center'><?php echo "$MSG_REPLY_NUMBER"?></td>
      <td width='15%' class='center'><?php echo "$MSG_USER"?></td>
      <td width='65%' class='center'><?php echo "$MSG_QUESTION"?></td>
      <td width='15%' class='center'><?php echo "$MSG_REGISTERED"?></td>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT `rid`, `author_id`, `time`, `content`, `status` FROM `reply` WHERE `topic_id`=? AND `status`<=1 ORDER BY `rid` LIMIT 30";
    $result = pdo_query($sql,$tid);
    $rows_cnt = count($result);
    $cnt = 0;
    $i = 0;
    foreach($result as $row){
                  $url = "threadadmin.php?target=reply&rid=".$row['rid']."&tid={$tid}&action=";
                  if(isset($_SESSION[$OJ_NAME.'_'.'user_id']))
                      $isuser = strtolower($row['author_id'])==strtolower($_SESSION[$OJ_NAME.'_'.'user_id']);
                  else
                      $isuser = false;
                ?>
                <tr <?php if($i==0) echo "class='bg-success'";?>>
                  <td width='5%'>
                         <span style="width:5em;text-align:center;display:inline-block;margin:0 10px">#<?php echo $i+1;?></span>
                  </td>
                  <td width='15%' class='center'>
                    <?php
                    if($row['author_id']=="admin")
                    {
                        echo "admin";
                    }
                    else
                    {
                        echo "<a href='../userinfo.php?user={$row['author_id']}'>{$row['author_id']}</a>";
                    }
                    ?>
                  </td>
                  <td>
                         <?php if($i==0) echo "$MSG_PROBLEM_ID : ".(($problemid==0)?"----":("<a href=\"problem.php?id=$problemid\">"."$problemid</a>"))."<br>";?>
                         <?php if($i==0) echo "$MSG_QUESTION : ".$titles."<br><br>";?>
            
                    <div id="post<?php echo $row['rid'];?>" class="md" style="text-align:left; clear:both;">
                      <?php
                      if($row['status']==0){
                          echo htmlentities($row['content'],ENT_QUOTES,"UTF-8");
                      }else{
                          if(!$isuser || $isadmin){
                              echo "<div style=\"border-left:10px solid gray\"><font color=gray><i>".$MSG_BLOCKED."</i></font></div>";
                          }
                          if($isuser || $isadmin){
                              echo nl2br(htmlentities($row['content'],ENT_QUOTES,"UTF-8"));
                          }
                      }
                      ?>
                    </div>
            
                    <div class="mon" style="display:inline;text-align:right;float:left;font-size:80%;">
                      <?php
                      if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
                          echo "<span>[ <a onclick=\"reply(".$row['rid'].")\">".$MSG_REPLY."</a> ]</span>";
                      }
                      if($isuser || $isadmin){
                            //echo "<span>[ <a href=\"#\">Edit</a> ]</span>";
                      }
                      if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) && $i!=0){
                        if($row['status']==0){
                            echo "<span>[ <a href=\"".$url."disable\">".$MSG_DISABLE."</a> ]</span>";
                        }else{
                            echo "<span>[ <a href=\"".$url."resume\">".$MSG_RESUME."</a> ]</span>";
                        }
                      }
                      if(($isuser || $isadmin) && $i!=0){
                            echo "<span>[ <a href=\"".$url."delete\">".$MSG_DISCUSS_DELETE."</a> ]</span> ";
                      }
                      ?>
                    </div>
                  </td>
                  <td style='text-align:center;'>
                  <?php
                  $timestamp = strtotime($row['time']);
                  $oldstamp = strtotime("-1 days");
            
                  if($oldstamp >= $timestamp){
                      $dateouts = $row['time'];
                      echo "<div>{$dateouts}</div>";
                  }else{
                      $dateouts = "new!!";
                      echo "<div style='color:red;'>{$dateouts}</div>";
                  }
            
                  echo "<div style='font-size:80%;'>";
                  if($i==0){
                      $adminurl = "threadadmin.php?target=thread&tid={$tid}&action=";
              
                      if($isadmin){
                           if($toplevels==0 || $toplevels==1){
                               echo "[ <a href=\"{$adminurl}sticky&level=3\">".$MSG_DISCUSS_NOTICE."</a> ]";
                               echo "[ <a href=\"{$adminurl}sticky&level=2\">".$MSG_DISCUSS_NOTE."</a> ]";
                               echo "[ ".$MSG_DISCUSS_NORMAL." ]";
                           }else if($toplevels==2){
                               echo "[ <a href=\"{$adminurl}sticky&level=3\">".$MSG_DISCUSS_NOTICE."</a> ]";
                               echo "[ ".$MSG_DISCUSS_NOTE." ]";
                               echo "[ <a href=\"{$adminurl}sticky&level=1\">".$MSG_DISCUSS_NORMAL."</a> ]";
                           }else if($toplevels==3){
                               echo "[ ".$MSG_DISCUSS_NOTICE." ]";
                               echo "[ <a href=\"{$adminurl}sticky&level=2\">".$MSG_DISCUSS_NOTE."</a> ]";
                               echo "[ <a href=\"{$adminurl}sticky&level=1\">".$MSG_DISCUSS_NORMAL."</a> ]";
                           }
                           echo "[ <a href=\"{$adminurl}delete\">".$MSG_DISCUSS_DELETE."</a> ]";
                      }else if($isuser){
                          echo " [ <a href=\"{$adminurl}delete\">".$MSG_DISCUSS_DELETE."</a> ]";
                      }
                  }
                  echo "</div>";
                  ?>
                  </td>
                </tr>
            
              <?php
                $i++;
    }
  ?>

    <?php if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {?>
    <tr>
      <td width='5%' class='center'>
        #?
      </td>
      <td width='15%' class='center'>
      <?php
        echo "<a href='../userinfo.php?user={$_SESSION[$OJ_NAME.'_'.'user_id']}'>{$_SESSION[$OJ_NAME.'_'.'user_id']}</a>";
      ?>
      </td>
      <td colspan=2>
        <form action="post.php?action=reply" method="post">
                <table width='80%'>
                  <tr>
                    <td>
                           <input type="hidden" name="tid" value="<?php echo $tid;?>">
                           <textarea id="replyContent" name=content style="border:1px dashed #8080FF; width:100%; height:200px; font-size:100%;"></textarea>
                           <div style="float:right;font-size:80%">
                                 <?php if($OJ_VCODE){?>
                                   <?php echo $MSG_VCODE?>:<input name="vcode" size=4 type="text">
                                   <img id="vcode-img" alt="click to change" onclick="this.src='vcode.php?'+Math.random()">*
                                 <?php }?>
                                 <input type="submit" style="margin:5px 10px" value="<?php echo $MSG_REGISTER_REPLY?>"></input>
                           </div>
                        </td>
                      </tr>
                </table>
        </form>
      </td>
    </tr>
    <?php }?>
  </tbody>
</table>
<hr>
<table width='80%'>
  <tr align='right'>
    <td>
      <form class=form-inline action="newpost.php<?php echo "?".$parm ?>" >
        <button class="form-control" type='submit'><?php echo $MSG_WRITE_QUESTION; ?></button>
      </form>
    </td>
  </tr>
</table>
</center>


<script src="<?php echo $OJ_CDN_URL.$path_fix."include/"?>jquery-latest.js"></script>
<script>
<?php if ($OJ_VCODE) { ?>
  $(document).ready(function () {
    $("#vcode-img").attr("src", "vcode.php?" + Math.random());
  })
<?php } ?>

function trim() {
    return this.replace(/^\s+|\s+$/g,"");
}

function reply(rid)
{
  var origin = $("#post"+rid).text();
  //console.log(origin);
  origin = origin.trim();
  origin = origin+"\n----------------------\n";
  $("#replyContent").text(origin);
  $("#replyContent").focus();
}
</script>

