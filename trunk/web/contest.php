 <?php
if(isset($_POST['keyword']))
  $cache_time = 1;
else
  $cache_time = 10;

$OJ_CACHE_SHARE = false;//!(isset($_GET['cid'])||isset($_GET['my']));
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');
$view_title= $MSG_CONTEST;

function formatTimeLength($length) {
  $hour = 0;
  $minute = 0;
  $second = 0;
  $result = '';

  global $MSG_SECONDS, $MSG_MINUTES, $MSG_HOURS, $MSG_DAYS;

  if ($length>=60) {
    $second = $length%60;
    
    if ($second>0 && $second<10) {
    	$result = '0'.$second.' '.$MSG_SECONDS;}
    else if ($second>0) {
    	$result = $second.' '.$MSG_SECONDS;
    }

    $length = floor($length/60);
    if ($length >= 60) {
      $minute = $length%60;
      
      if ($minute==0) {
      	if ($result != '') {
      		$result = '00'.' '.$MSG_MINUTES.' '.$result;
      	}
      }
      else if ($minute>0 && $minute<10) {
      	if ($result != '') {
      		$result = '0'.$minute.' '.$MSG_MINUTES.' '.$result;}
				}
				else {
					$result = $minute.' '.$MSG_MINUTES.' '.$result;
				}
				
				$length = floor($length/60);

				if ($length >= 24) {
					$hour = $length%24;

				if ($hour==0) {
					if ($result != '') {
						$result = '00'.' '.$MSG_HOURS.' '.$result;
					}
				}
				else if ($hour>0 && $hour<10) {
					if($result != '') {
						$result = '0'.$hour.' '.$MSG_HOURS.' '.$result;
					}
				}
				else {
					$result = $hour.' '.$MSG_HOURS.' '.$result;
				}

				$length = floor($length / 24);
				$result = $length .$MSG_DAYS.' '.$result;
			}
			else {
				$result = $length.' '.$MSG_HOURS.' '.$result;
			}
		}
		else {
			$result = $length.' '.$MSG_MINUTES.' '.$result;
		}
	}
	else {
		$result = $length.' '.$MSG_SECONDS;
	}
	return $result;
}
$now = time();

if (isset($_GET['cid'])) {
	
	 require_once("contest-check.php");
	
	//$sql = "SELECT * FROM (SELECT `problem`.`title` AS `title`,`problem`.`problem_id` AS `pid`,source AS source, contest_problem.num as pnum FROM `contest_problem`,`problem` WHERE `contest_problem`.`problem_id`=`problem`.`problem_id` AND `contest_problem`.`contest_id`=? ORDER BY `contest_problem`.`num`) problem LEFT JOIN (SELECT problem_id pid1,count(distinct(user_id)) accepted FROM solution WHERE result=4 AND contest_id=? GROUP BY pid1) p1 ON problem.pid=p1.pid1 LEFT JOIN (SELECT problem_id pid2,count(1) submit FROM solution WHERE contest_id=? GROUP BY pid2) p2 ON problem.pid=p2.pid2 ORDER BY pnum";//AND `problem`.`defunct`='N'

	//$result = pdo_query($sql,$cid,$cid,$cid);
	$sql = "select p.title,p.problem_id,p.source,cp.num as pnum,cp.c_accepted accepted,cp.c_submit submit from problem p inner join contest_problem cp on p.problem_id = cp.problem_id and cp.contest_id=$cid order by cp.num";
	$result = mysql_query_cache($sql);
	$view_problemset = Array();

	$cnt = 0;
	$noip = (time()<$end_time) && (stripos($view_title,$OJ_NOIP_KEYWORD)!==false);
	if(isset($_SESSION[$OJ_NAME.'_'."administrator"])||
		isset($_SESSION[$OJ_NAME.'_'."m$cid"])||
		isset($_SESSION[$OJ_NAME.'_'."source_browser"])||
		isset($_SESSION[$OJ_NAME.'_'."contest_creator"])
	   ) $noip=false;
	foreach($result as $row) {
		$view_problemset[$cnt][0] = "";
		if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
                        $ac=check_ac($cid,$cnt,$noip);
                        $sub="";
                        if($ac!="") $sub="?";
                        if($noip)
                          	$view_problemset[$cnt][0] = "$sub";
                        else
                          	$view_problemset[$cnt][0] = "$ac" ;

		}else
			$view_problemset[$cnt][0] = "";


		if($now < $end_time) { //during contest/exam time
			$view_problemset[$cnt][1] = "<a href='problem.php?cid=$cid&pid=$cnt'>".$PID[$cnt]."</a>";
			$view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>".$row['title']."</a>"; 
		}
		else {               //over contest time
			//check the problem will be use remained contest/exam
			$tpid = intval($row['problem_id']);
			$sql = "SELECT `problem_id` FROM `problem` WHERE `problem_id`=? AND `problem_id` IN (
				SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
					SELECT `contest_id` FROM `contest` WHERE (`defunct`='N' AND now()<`start_time`)
				)
			)";

			$tresult = pdo_query($sql, $tpid);

			if (intval($tresult) != 0 && !isset($_SESSION[$OJ_NAME.'_'."m$cid"]) ) { 
				//if the problem will be use remained contes/exam don't show to other teachers and students
				$view_problemset[$cnt][1] = $PID[$cnt]; //hide the title after contest
				$view_problemset[$cnt][2] = '--using in another private contest--';
			}
			else {
				$view_problemset[$cnt][1] = "<a href='problem.php?id=".$row['problem_id']."'>".$PID[$cnt]."</a>";
				if($contest_ok)
					$view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>".$row['title']."</a>";
				else
					$view_problemset[$cnt][2] = $row['title'];
			}
		}

		//$view_problemset[$cnt][3] = $row['source'];

		if (!$noip)
			$view_problemset[$cnt][3] = $row['accepted'];
		else
			$view_problemset[$cnt][3] = "";
    
    $view_problemset[$cnt][4] = $row['submit'];
    $cnt++;
  }
}
else {
	$page = 1;
	if (isset($_GET['page']))
		$page = intval($_GET['page']);

	$page_cnt = 25;
	$pstart = $page_cnt*$page-$page_cnt;
	$pend = $page_cnt;
	$rows = pdo_query("select count(1) from contest where defunct='N'");

	if ($rows)
		$total = $rows[0][0];
  
  $view_total_page = intval($total/$page_cnt)+1;
  $keyword = "";

	if (isset($_POST['keyword'])) {
		$keyword = "%".$_POST['keyword']."%";}

	//echo "$keyword";
	$mycontests = "";
	$wheremy = "";
	if (isset($_SESSION[$OJ_NAME.'_user_id'])) {
		$sql = "select distinct contest_id from solution where contest_id>0 and user_id=?";
		$result = pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id']);

		foreach ($result as $row) {
			$mycontests .= ",".$row[0];
	        }

		$len = mb_strlen($OJ_NAME.'_');
                                $user_id = $_SESSION[ $OJ_NAME . '_' . 'user_id' ];

                if($user_id){
                        // 已登录的
                        $sql = "SELECT * FROM `privilege` WHERE `user_id`=?";
                        $result = pdo_query( $sql, $user_id );

                        // 刷新各种权限
                        foreach ( $result as $row ){
                                if(isset($row[ 'valuestr' ])){
                                        $_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = $row[ 'valuestr' ];
                                }else {
                                        $_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = true;
                                }
                        }
                       if(isset($_SESSION[ $OJ_NAME . '_vip' ])) {  // VIP mark can access all [VIP] marked contest
                                $sql="select contest_id from contest where title like '%[VIP]%'";
                                $result=pdo_query($sql);
                                foreach ($result as $row){
                                        $_SESSION[ $OJ_NAME . '_c' .$row['contest_id'] ] = true;
                                }
                        };
                }

		foreach ($_SESSION as $key => $value) {
			if ((mb_substr($key,$len,1)=='m' || mb_substr($key,$len,1)=='c') && intval(mb_substr($key,$len+1))>0) {
                         	//echo substr($key,1)."<br>";
				$mycontests .= ",".intval(mb_substr($key,$len+1));
			}
		}

		//echo "=====>$mycontests<====";

		if (strlen($mycontests)>0)
			$mycontests=substr($mycontests,1);
		if (isset($_GET['my'])&&$mycontests!="")
	  		if(isset($_GET['my'])) $wheremy=" and( contest_id in ($mycontests) or user_id='".$_SESSION[$OJ_NAME.'_user_id']."')";
	}

  $sql = "SELECT * FROM `contest` WHERE `defunct`='N' ORDER BY `contest_id` DESC LIMIT 1000";

	if ($keyword) {
		$sql = "SELECT *  FROM contest WHERE contest.defunct='N' AND contest.title LIKE ? $wheremy  ORDER BY contest_id DESC";
		$sql .= " limit ".strval($pstart).",".strval($pend); 

		$result = pdo_query($sql,$keyword);
	}
	else {
		$sql = "SELECT *  FROM contest WHERE contest.defunct='N' $wheremy  ORDER BY contest_id DESC";
		$sql .= " limit ".strval($pstart).",".strval($pend); 
		//echo $sql;
		$result = mysql_query_cache($sql);
	}

	$view_contest = Array();
	$i = 0;

	foreach ($result as $row) {
		$view_contest[$i][0] = $row['contest_id'];

		if (trim($row['title'])=="")
			$row['title'] = $MSG_CONTEST.$row['contest_id'];

		$view_contest[$i][1] = "<a href='contest.php?cid=".$row['contest_id']."'>".$row['title']."</a>";
		$start_time = strtotime($row['start_time']);
		$end_time = strtotime($row['end_time']);
		$now = time();

		$length = $end_time-$start_time;
		$left = $end_time-$now;

		if ($end_time<=$now) {
			//past
			$view_contest[$i][2] = "<span class=text-muted>$MSG_Ended</span>"." "."<span class=text-muted>".$row['end_time']."</span>";

    }
    else if ($now<$start_time) {
			//pending
			$view_contest[$i][2] = "<span class=text-success>$MSG_Start</span>"." ".$row['start_time']."&nbsp;";
			$view_contest[$i][2] .= "<span class=text-success>$MSG_TotalTime</span>"." ".formatTimeLength($length);
		}
		else {
			//running
			$view_contest[$i][2] = "<span class=text-danger>$MSG_Running</span>"." ".$row['start_time']."&nbsp;";
			$view_contest[$i][2] .= "<span class=text-danger>$MSG_LeftTime</span>"." ".formatTimeLength($left)."</span>";
    }

    $private = intval($row['private']);
    if ($private==0)
    	$view_contest[$i][4] = "<span class=text-primary>$MSG_Public</span>";
    else
    	$view_contest[$i][5] = "<span class=text-danger>$MSG_Private</span>";

    $view_contest[$i][6] = $row['user_id'];

    $i++;
  }
}

/////////////////////////Template
if (isset($_GET['cid']))
	require("template/".$OJ_TEMPLATE."/contest.php");
else
	require("template/".$OJ_TEMPLATE."/contestset.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
