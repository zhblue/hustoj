<!-- /web/discuss.php -->

<?php
require_once("discuss_func.inc.php");
$parm = "";
if(isset($_GET['pid']))
{
  $pid = intval($_GET['pid']);
  $parm = "pid=".$pid;
}
else
{
  $pid = 0;
}

if(isset($_GET['cid']))
{
  $cid = intval($_GET['cid']);
}
else
{
  $cid = 0;
}

$parm .= "&cid=".$cid;
$prob_exist = problem_exist($pid, $cid);

echo "<title>$MSG_BBS</title>";
?>

<center>
  <div style="width:90%">
    <?php if($prob_exist) {?>
    <div style="float:left;text-align:left;font-size:80%">
      Location :
      <?php
      if($cid!=null)
        echo "<a href=\"discuss.php?cid=".$cid."\">Contest ".$cid."</a>";
      else
        echo "<a href=\"discuss.php\">MainBoard</a>";

      if($pid!=null && $pid!=0)
      {
        $query = "?pid=$pid";
        if($cid!=0)
        {
          $query .= "&cid=$cid";
          $PAL = pdo_query("SELECT num FROM contest_problem WHERE contest_id=? AND problem_id=?",$cid,$pid)[0][0];
          echo " >> <a href=\"discuss.php".$query."\">Problem ".$PID[$PAL]."</a>";
        }
        else
        {
          echo " >> <a href=\"discuss.php".$query."\">Problem ".$pid."</a>";
        }
      }
      ?>
    </div>

    <div style="float:right;font-size:80%;color:red;font-weight:bold">
      <?php if ($pid!=null && $pid!=0 && ($cid=='' || $cid==null)){?>
        <a href="../problem.php?id=<?php echo $pid?>">See the problem</a>
      <?php }?>
    </div>

    <?php }?>

    <?php
    $sql = "SELECT `tid`, `title`, `top_level`, `t`.`status`, `cid`, `pid`, CONVERT(MIN(`r`.`time`),DATE) `posttime`, MAX(`r`.`time`) `lastupdate`, `t`.`author_id`, COUNT(`rid`) `count` FROM `topic` t LEFT JOIN `reply` r on t.tid=r.topic_id WHERE `t`.`status`!=2";

    if(isset($_REQUEST['cid']))
    {
      $cid = intval($_REQUEST['cid']);

      $sql = "SELECT `tid`, t.`title`, `top_level`, `t`.`status`, `cid`, `pid`, CONVERT(MIN(`r`.`time`),DATE) `posttime`, MAX(`r`.`time`) `lastupdate`, `t`.`author_id`, COUNT(`rid`) `count`,cp.num FROM `topic` t LEFT JOIN `reply` r on t.tid=r.topic_id LEFT JOIN contest_problem cp on t.pid=cp.problem_id and cp.contest_id=$cid WHERE `t`.`status`!=2";
      //echo $sql;
    }

    if(isset($_REQUEST["cid"])&&$_REQUEST['cid']!='')
      $sql .= " AND (`cid`='".intval($_REQUEST['cid'])."'";
    else
      $sql .= " AND (`cid`=0 ";

    $sql .= " OR `top_level`=3)";

    if(isset($_REQUEST["pid"])&&$_REQUEST['pid']!='')
    {
      $sql .= " AND (`pid`='".intval($_REQUEST['pid'])."' OR `top_level`>=2)";
      $level = "";
    }
    else
    {
      $level = " - (`top_level`=1)";
    }

    $sql .= " GROUP BY t.tid ORDER BY `top_level`$level DESC, MAX(`r`.`time`) DESC";
    //$sql .= " LIMIT 30";
    //echo $sql;

    $result = pdo_query($sql);
    $rows_cnt = count($result);
    $cnt = 0;
    $isadmin = isset($_SESSION[$OJ_NAME.'_'.'administrator']);
    ?>

    <table id='discuss' width='80%' style='width:80%;' table class='table table-hover'>
      <thead>
        <tr class='toprow' align='center'>
          <td class='center'>
          </td>          
          <td class='center'>
            <?php echo "$MSG_USER"?>
          </td>
          <td class='center'>
            <?php echo "$MSG_PROBLEM_ID"?>
          </td>
          <td class='center'>
            <?php echo "$MSG_TITLE"?>
          </td>
          <td class='center'>
            <?php echo "$MSG_LAST_REPLY"?>
          </td>
          <td class='center'>
            <?php echo "$MSG_REPLY_COUNTS"?>
          </td>
        </tr>
      </thead>

      <tbody>
        <?php
        if($rows_cnt==0)
          echo("<tr class='evenrow'><td colspan=6 style='text-align:center'>".$MSG_NO_QUESTIONS."</td></tr>");
      
        foreach($result as $row)
        {
          $cnt = 1-$cnt;

          if($row['top_level']==3)
          {
            echo "<tr align='center' class='bg-success'>";
          }
          else if($row['top_level']==2)
          {
            echo "<tr align='center' class='bg-warning'>";
          }
          else
          {
            if($cnt)
              echo "<tr align='center'>";
            else
              echo "<tr align='center'>";
          }
 
          echo "<td>";
          if($row['top_level']!=0)
          {
            if($row['top_level']!=1 || $row['pid']==($pid==''?0:$pid))
            {
              if($row['top_level']==3)
              {
                echo "<span class='glyphicon glyphicon-tags' aria-hidden='true'> <b>".$MSG_DISCUSS_NOTICE."</b>";
              }
              else if($row['top_level']==2)
              {
                echo "<span class='glyphicon glyphicon-tag' aria-hidden='true'> <b>".$MSG_DISCUSS_NOTE."</b>";
              }
            }
          }
          else if($row['status']==1)
            echo"<b class=\"Lock\">Lock</b>";
          else if($row['count']>20)
            echo"<b class=\"Hot\">Hot</b>";
          echo "</td>";

          if($row['author_id']=="admin")
          {
            echo "<td>admin</td>";            
          }
          else
          {
            echo "<td><a href=\"../userinfo.php?user={$row['author_id']}\">{$row['author_id']}</a></td>";
          }

          echo "<td>";
          if($row['pid']!=0)
          {
            if($row['cid'])
            { 
              echo "<a href=\"problem.php?id={$row['pid']}"."&cid={$row['cid']}\">";
              echo "{$PID[$row['num']]}</a>";
            }
            else
            {
              echo "<a href=\"problem.php?id={$row['pid']}\">";
              echo "{$row['pid']}</a>";
            }
          }
          echo "</td>";

          if($row['cid'])
            echo "<td align='left'><a href=\"thread.php?tid={$row['tid']}&cid={$row['cid']}\">".htmlentities($row['title'],ENT_QUOTES,"UTF-8")."</a></td>";
          else
            echo "<td align='left'><a href=\"thread.php?tid={$row['tid']}\">".htmlentities($row['title'],ENT_QUOTES,"UTF-8")."</a></td>";


          $timestamp = strtotime($row['lastupdate']);
          $oldstamp = strtotime("-1 days");
          if($oldstamp >= $timestamp)
          {
            $dateouts = date("m-d", strtotime($row['lastupdate']));
            echo "<td>{$dateouts}</td>";
          }
          else
          {
            $dateouts = "new!!";
            echo "<td style='color:red;'>{$dateouts}</td>";
          }
          echo "<td>".($row['count']-1)."</td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
    <hr>
    <table width='90%'>
      <tr align='right'>
        <td>
         <form class="form-inline" action="newpost.php" >
                <?php if (!empty($pid))  echo "<input type='hidden' name='pid' value='$pid' >"  ?>
                <?php if (!empty($cid))  echo "<input type='hidden' name='cid' value='$cid' >"  ?>
            <button class="form-control" type='submit'><?php echo "$MSG_WRITE_QUESTION";?></button>
         </form>
 
        </td>
      </tr>
    </table>
    <br>

  </div>
</center>

<?php require_once("template/$OJ_TEMPLATE/discuss.php")?>
