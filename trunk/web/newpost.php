<!-- /web/newpost.php -->

<?php
require_once("discuss_func.inc.php");

echo "<title>HUST Online Judge WebBoard >> New Thread</title>";

if(!isset($_SESSION[$OJ_NAME.'_'.'user_id']))
{
  echo "<script>location.replace('loginpage.php')</script>";
  exit(0);
}

if(isset($_GET['pid']))
  $pid = intval($_GET['pid']);
else  
  $pid = "";
if(isset($_GET['cid']))
{
  $cid = intval($_GET['cid']);
  if($pid>0)
  {
    $pid = pdo_query("SELECT num FROM contest_problem WHERE problem_id=? AND contest_id=?",$pid,$cid)[0][0];
    $pid = $PID[$pid];
  }
}
else
{
  $cid = 0;
}
?>

<center>
<table style='width:80%;' table class='table table-hover'>
  <thead>
    <tr class='toprow' align='center'>
      <td width='5%' class='center'>
      </td>
      <td width='15%' class='center'>
        <?php echo "$MSG_USER"?>
      </td>          
      <td width='65%' class='center'>
        <?php echo "$MSG_QUESTION"?>
      </td>
      <td width='15%' class='center'>
      </td>
    </tr>
  </thead>
  <tbody>
    <tr class='bg-success'>
      <td width='5%' class='center'>
      </td>
      <td width='15%' class='hidden-xs center'>
      <?php
        echo "<a href='../userinfo.php?user={$_SESSION[$OJ_NAME.'_'.'user_id']}'>{$_SESSION[$OJ_NAME.'_'.'user_id']}</a>";
      ?>
      </td>
      <td colspan=2>
        <form action="post.php?action=new" method="post">
          <table width='80%'>
            <tr>
              <td>
                <input type=hidden name=cid value="<?php if (isset($_REQUEST['cid'])) echo intval($_REQUEST['cid']);?>">
                <?php echo $MSG_PROBLEM_ID?> : 
                <div>
                  <input name=pid style="border:1px dashed #8080FF; width:100px; height:20px; font-size:100%;margin:0 10px; padding:2px 10px" value="<?php echo $pid;?>">
                </div><br>
                <?php echo $MSG_TITLE?> :
                <div>
                  <input name=title style="border:1px dashed #8080FF; width:700px; height:20px; font-size:100%;margin:0 10px; padding:2px 10px">
                </div><br>
                <?php echo $MSG_QUESTION?> :
                <div>
                  <textarea name=content style="border:1px dashed #8080FF; width:700px; height:200px; font-size:100%; margin:0 10px; padding:10px"></textarea>
                </div>
                <div style="float:right;font-size:80%">
                  <?php if($OJ_VCODE){?>
                    <?php echo $MSG_VCODE?>:<input name="vcode" size=4 type="text">
                    <img id="vcode-img" alt="click to change" onclick="this.src='vcode.php?'+Math.random()">*
                  <?php }?>
                  <input type="submit" style="margin:5px 10px" value="<?php echo $MSG_REGISTER_QUESTION?>"></input>
                </div>
              </td>
            </tr>
          </table>
        </form>
      </td>
    </tr>
  </tbody>
</table>
</center>


<script src="<?php echo $OJ_CDN_URL.$path_fix."include/"?>jquery-latest.js"></script>

<script>
<?php if ($OJ_VCODE) { ?>
  $(document).ready(function () {
    $("#vcode-img").attr("src", "vcode.php?" + Math.random());
  })
<?php } ?>
</script>

<?php require_once("template/$OJ_TEMPLATE/discuss.php")?>
