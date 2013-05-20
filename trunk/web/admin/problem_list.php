<?php require("admin-header.php");
require_once("../include/set_get_key.php");
if (!(isset($_SESSION['administrator'])
                ||isset($_SESSION['contest_creator'])
                ||isset($_SESSION['problem_editor'])
                )){
        echo "<a href='../loginpage.php'>Please Login First!</a>";
        exit(1);
}
?>
<html>
    <head>
        <title>OJ Administration</title>
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Language" content="zh-cn">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel=stylesheet href='admin.css' type='text/css'>
    </head>
<body>

<?php
    $sql="SELECT max(`problem_id`) as upid FROM `problem`";
    $page_cnt=50;
    $result=mysql_query($sql);
    echo mysql_error();
    $row=mysql_fetch_object($result);
    $cnt=intval($row->upid)-1000;
    $cnt=intval($cnt/$page_cnt)+(($cnt%$page_cnt)>0?1:0);
    if (isset($_GET['page'])){
            $page=intval($_GET['page']);
    }else $page=$cnt;
    $pstart=1000+$page_cnt*intval($page-1);
    $pend=$pstart+$page_cnt;
?>

<div class="container-fluid">
    <?php require_once("admin-bar.php"); ?>
    <div class="row-fluid top-space">
        <div class="span2" >
            <div class="menu-group"  >
                <?php require_once("menu.php") ?>
            </div>
        </div>
        <div class="span10">
            <div class="">
                <center><h2 class="h-head">Problem List</h2></center>

                <?php
                $sql="select `problem_id`,`title`,`in_date`,`defunct` FROM `problem` where problem_id>=$pstart and problem_id<=$pend order by `problem_id` desc";
                //echo $sql;
                $result=mysql_query($sql) or die(mysql_error());
                 echo "<center><table width=90% border=1 class='table table-striped col'>";
                echo "<form method='post' action='contest_add.php'>";
                echo "<thead><tr><th>PID<th>Title<th>Date";
                if(isset($_SESSION['administrator'])||isset($_SESSION['problem_editor'])){
                        if(isset($_SESSION['administrator']))   echo "<th>Status<th>Delete";
                        echo "<th>Edit<th>TestData</tr></thead>";
                }
                for (;$row=mysql_fetch_object($result);){
                        echo "<tr>";
                        echo "<td>".$row->problem_id;
                        echo "<input type=checkbox name='pid[]' value='$row->problem_id'>";
                        echo "<td><a href='../problem.php?id=$row->problem_id'>".$row->title."</a>";
                        echo "<td>".$row->in_date;
                        if(isset($_SESSION['administrator'])||isset($_SESSION['problem_editor'])){
                                if(isset($_SESSION['administrator'])){
                                        echo "<td><a href=problem_df_change.php?id=$row->problem_id&getkey=".$_SESSION['getkey'].">"
                                        .($row->defunct=="N"?"<span class=green>Available</span>":"<span class=red>Reserved</span>")."</a><td>";
                                        if($OJ_SAE||function_exists("system")){
                                              ?>
                                              <a href=# onclick='javascript:if(confirm("Delete?")) location.href="problem_del.php?id=<?php echo $row->problem_id?>&getkey=<?php echo $_SESSION['getkey']?>";'>
                                              Delete</a>
                                              <?php
                                        }
                                }
                                if(isset($_SESSION['administrator'])||isset($_SESSION["p".$row->problem_id])){
                                        echo "<td><a href=problem_edit.php?id=$row->problem_id&getkey=".$_SESSION['getkey'].">Edit</a>";
                                        echo "<td><a href=quixplorer/index.php?action=list&dir=$row->problem_id&order=name&srt=yes>TestData</a>";
                                }
                        }
                        echo "</tr>";
                }
                echo "<tr><td colspan=7><input type='submit' name='problem2contest' value='CheckToNewContest'>";
                echo "</tr></form>";
                echo "</table></center>";


                echo "<div class='btn-group align-center bottom-space'>";
                for ($i=1;$i<=$cnt;$i++){
                        if ($i>1) echo '';
                        if ($i==$page) echo "<button class='btn clear-float' style='color:red;'>$i</button>";
                        else echo "<button class='btn clear-float' >"."<a href='problem_list.php?page=".$i."' >".$i."</a>"."</button>";
                }

                echo "</div>";

                ?>
                                     
            </div>
        </div>
    </div>
</div>

</body>
</html>



									 