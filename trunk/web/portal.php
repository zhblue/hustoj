<?php
	ini_set("display_errors", "On");  // 当我们需要调试的时候，把这里设成On，在出问题的时候php会尝试在页面上输出错误信息
	require_once("include/db_info.inc.php"); // 导入基本配置和数据库操作函数
	require_once('include/const.inc.php');   // 导入需要的一些可选常量
	require_once('include/my_func.inc.php'); // 导入自定义的一些可选函数
	require_once('include/curl.php'); // 导入与爬虫有关的一些可选函数
	if(isset($_SESSION[$OJ_NAME.'_user_id'])){  //如果用户已经登录
		$user_id=$_SESSION[$OJ_NAME.'_user_id'];
		$show_title="Hello ".$_SESSION[$OJ_NAME.'_user_id']."!";
		$sub_arr = Array(); //已经提交的题目号
		$acc_arr = Array(); //已经通过的题目号
		if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])){ 
			$sql = "SELECT problem_id, MIN(result) AS result FROM solution WHERE user_id=? and result>=4 GROUP BY problem_id ";
			$result = pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
			foreach ($result as $row){
				$sub_arr[$row['problem_id']] = true;
				if($row['result'] == 4) $acc_arr[$row['problem_id']] = true;		
			}		
		}
	}else{    //没有登录的用户按Guest处理
		$user_id="Guest";
		$show_title="HelloWorld!";

	}
	$sql="select * from problem p inner join (
 			select distinct problem_id,contest_id from solution where result!=4 and user_id=? and problem_id not in 
    				(select distinct problem_id from solution where result=4 and user_id=?)
			) f on p.problem_id=f.problem_id ";
	$result=pdo_query($sql,$user_id,$user_id);      // 查找做了但是没做对的题
$cnt = 0;
$view_problemset = Array();  //用于传递查询结果给显示页的二维数组
$i = 0;
foreach ($result as $row) {
	$view_problemset[$i] = Array();    //第$i行的数组初始化
	if (isset($sub_arr[$row['problem_id']])) {
		if (isset($acc_arr[$row['problem_id']])) 
			$view_problemset[$i][0] = "<div class='label label-success'>Y</div>";
		else
			$view_problemset[$i][0] = "<div class='label label-danger'>N</div>";
	}
	else {
		$view_problemset[$i][0] = "<div class=none> </div>";
	}
	$category = array();  
	$cate = explode(" ",$row['source']);  //分类标签
	foreach($cate as $cat){
                        $cat=trim($cat);
                        if(mb_ereg("^http",$cat)){
                                $cat=get_domain($cat);   // 标签是URL地址的显示成域名,get_domain在curl.php里
                        }
                        array_push($category,trim($cat));
        }
	$view_problemset[$i][1] = "<div fd='problem_id' class='center'>".$row['problem_id']."</div>";
	$view_problemset[$i][2] = "<div class='left'><a href='problem.php?id=".$row['problem_id']."'>".$row['title']."</a></div>";;
	$view_problemset[$i][3] = "<div pid='".$row['problem_id']."' fd='source' class='center'>";

	foreach ($category as $cat) {
		if(trim($cat)==""||trim($cat)=="&nbsp")
			continue;
		$hash_num = hexdec(substr(md5($cat),0,7));
		$label_theme = $color_theme[$hash_num%count($color_theme)];   //$color_theme 在 const.inc.php 里
		if ($label_theme=="")
			$label_theme = "default";
		$view_problemset[$i][3] .= "<a title='".htmlentities($cat,ENT_QUOTES,'UTF-8')."' class='label label-$label_theme' style='display: inline-block;' href='problemset.php?search=".htmlentities(urlencode($cat),ENT_QUOTES,'UTF-8')."'>".mb_substr($cat,0,10,'utf8')."</a>&nbsp;";
	}

	$view_problemset[$i][3] .= "</div >";
	$view_problemset[$i][4] = "<div class='center'><a href='status.php?problem_id=".$row['problem_id']."&jresult=4'>".$row['accepted']."</a></div>";
	$view_problemset[$i][5] = "<div class='center'><a href='status.php?problem_id=".$row['problem_id']."'>".$row['submit']."</a></div>";
	$i++;
}
	$mycontests=$wheremy="";
	
	if (isset($_SESSION[$OJ_NAME.'_user_id'])) {
		$sql = "select distinct contest_id from solution where contest_id>0 and user_id=?";   
		$result = pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id']);  //有提交记录的比赛
		foreach ($result as $row) {
			$mycontests .= ",".$row[0];
	        }
		$len = mb_strlen($OJ_NAME.'_');
                $user_id = $_SESSION[ $OJ_NAME . '_' . 'user_id' ];
                if(!empty($user_id)){
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

		foreach ($_SESSION as $key => $value) {   //有权限的比赛 
			if ((mb_substr($key,$len,1)=='m' || mb_substr($key,$len,1)=='c') && intval(mb_substr($key,$len+1))>0) {
                         	//echo substr($key,1)."<br>";
				$mycontests .= ",".intval(mb_substr($key,$len+1));  
			}
		}

		//echo "=====>$mycontests<====";
		if (!empty($mycontests)){
			$mycontests=substr($mycontests,1);
			$wheremy=" and  contest_id in ($mycontests) ";  
		}
		$wheremy.= " and start_time < now() and end_time > now() ";   //运行中的比赛 
	}

		$sql = "SELECT *  FROM contest WHERE contest.defunct='N' $wheremy  ORDER BY contest_id DESC";
		//echo htmlentities($sql);
		$result = pdo_query($sql);
		$view_contest=array();
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
			$view_contest[$i][2] = $row['start_time']."&nbsp;";
			$view_contest[$i][2] .= "<span class=text-danger>$MSG_LeftTime</span>"." ".intval($left/60)."$MSG_MINUTES</span>";
		$private = intval($row['private']);
		    if ($private==0)
			$view_contest[$i][4] = "<span class=text-primary>$MSG_Public</span>";
		    else
			$view_contest[$i][5] = "<span class=text-danger>$MSG_Private</span>";
		    $view_contest[$i][6] = $row['user_id'];
	    $i++;
  	}
	require("template/".$OJ_TEMPLATE."/".basename(__FILE__));

