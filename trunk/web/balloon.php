<?php
    require_once('./include/db_info.inc.php');
    require_once('./include/const.inc.php');
    require_once('./include/memcache.php');
        require_once('./include/setlang.php');
        $view_title=$MSG_SUBMIT;
 if (!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){

        $view_errors= "<a href=loginpage.php>$MSG_Login</a>";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
//      $_SESSION[$OJ_NAME.'_'.'user_id']="Guest";
 }
if ($_SERVER['REQUEST_METHOD']=="POST"){
        require_once("include/check_post_key.php");
}
if(isset($_SESSION[$OJ_NAME.'_'.'balloon'])){
        $school=pdo_query("select school from users where user_id=?",$_SESSION[$OJ_NAME."_user_id"])[0][0];
        $cid=intval($_GET['cid']);
        if($cid==0) $cid=1000;
                if(isset($_GET['id'])){
                        $id=intval($_GET['id']);
                        pdo_query("update balloon set status=1 where balloon_id=?",$id);
                }
                if(isset($_POST['clean'])){
                        pdo_query("delete from balloon where cid=? and user_id like ?",$cid,"$school%");
                }
                $sql="select * from solution where result=4 and contest_id=? and user_id like ? and solution_id not in (select sid from balloon where cid=?) order by solution_id;";
                $result=pdo_query($sql,$cid,"$school%",$cid);
                foreach($result as $row){
                     $user_id=$row['user_id'];
                     $sid=$row['solution_id'];
                     $pid=$row['num'];
                     $sql="select balloon_id from balloon where user_id=? and cid=? and pid=?";
                     if(count(pdo_query($sql,$user_id,$cid,$pid))==0){
                        $sql="insert into balloon(user_id,sid,cid,pid,status) value(?,?,?,?,0)";
                        pdo_query($sql,$user_id,$sid,$cid,$pid);
                     }
                }
                $sql="select s.num,s.user_id from solution s ,
                (select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id =c.minId";
                $fb = pdo_query($sql,$cid);
                if($fb) $rows_cnt=count($fb);
                else $rows_cnt=0;
                for ($i=0;$i<$rows_cnt;$i++){
                        $row=$fb[$i];
                        $first_blood[$row['num']]=$row['user_id'];
                }
                $view_balloon=Array();
                $result=pdo_query("select * from balloon b left join users u on b.cid= ? and  b.user_id like ? and b.user_id=u.user_id order by status,balloon_id desc limit 50",$cid,"$school%");
                $i=0;
                foreach ($result as $row){
                        $mypid=chr(ord('A')+$row['pid']);
                        $view_balloon[$i]=Array();
                        $view_balloon[$i][0]=$row['balloon_id'];
                        $view_balloon[$i][1]=$row['user_id']."_".$row['nick'];
                        $view_balloon[$i][2]= $mypid." - <font color='".$ball_color[$row['pid']]."'>";
                        $view_balloon[$i][2].=$ball_name[$row['pid']];
                        if($first_blood[$row['pid']]==$row['user_id']) $view_balloon[$i][2].=" First Blood!";
                        $view_balloon[$i][2].="</font>";
                        $view_balloon[$i][3]="";
                        if($row['status']==1)$view_balloon[$i][3].="<span class='btn btn-success'>$MSG_BALLOON_DONE</span>";
                        else $view_balloon[$i][3].="<span class='btn btn-danger'>$MSG_BALLOON_PENDING</span>";
                        $view_balloon[$i][4]="<a class='btn btn-info' href='balloon_view.php?id=".$row['balloon_id']."&fb=".($first_blood[$row['pid']]==$row['user_id']?1:0)."' target='_self'>$MSG_PRINTER</a>";
                        $view_balloon[$i][4].="<a class='btn btn-primary'  href='balloon.php?id=".$row['balloon_id']."&cid=$cid' target='_self'>$MSG_PRINT_DONE</a>";
                        $i++;
                }
                require("template/".$OJ_TEMPLATE."/balloon_list.php");
                exit(0);

 }else{

        $view_errors= "$MSG_BALLOON not available!";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
 }
/////////////////////////Template
/////////////////////////Common foot
