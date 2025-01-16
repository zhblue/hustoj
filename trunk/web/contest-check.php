<?php
  require_once("include/curl.php");
  $unixnow = time();
  $cid = abs(intval($_GET['cid']));
  if(isset($_SESSION[$OJ_NAME."_user_id"])) 
      $user_id=$_SESSION[$OJ_NAME."_user_id"];
  else 
      $user_id="";
  if ($cid<0) $cid=-$cid;
        $view_cid = $cid;
        //print $cid;
        //check contest valid
        $sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
        $result = pdo_query($sql,$cid);
        $rows_cnt = count($result);

        if($rows_cnt>0){
                $row = $result[0];
                $start_time = strtotime($row['start_time']);
                $end_time = strtotime($row['end_time']);
                $view_description = $row['description'];
                $view_title = $row['title'];
                $view_start_time = $row['start_time'];
                $view_end_time = $row['end_time'];
                $subnet= $row['subnet'];
        }
        $contest_ok = true;
        $password = "";

        if (isset($_POST['password']))
                $password = $_POST['password'];

        if (false) {
                $password = stripslashes($password);
        }
	if(!in_subnet_of_contest($ip,$cid)){
		$contest_ok = false;
                $view_description = "Not in $MSG_SUBNET $subnet";
	}

        if ($rows_cnt==0) {
                $view_title = "比赛已经关闭!";
        }
        else{

                $view_private = $row['private'];
                $view_contest_creator= $row['user_id'];
                if ($password!="" && $password==$row['password'])
                        $_SESSION[$OJ_NAME.'_'.'c'.$cid] = true;

                if ($row['private'] && !isset($_SESSION[$OJ_NAME.'_'.'c'.$cid])){

                      $sql = "SELECT count(*) FROM `privilege` WHERE `user_id`=? AND `rightstr`=?";
                      $result = pdo_query($sql, $user_id, "c$cid");
                      $row = $result[0];
                      $ccnt = intval($row[0]);
                      if($ccnt>0){
                              $contest_ok = true;
                              $_SESSION[$OJ_NAME.'_'.'c'.$cid] = true;
                      }else{
                              $contest_ok = false;
                      }
                }
//              if($row['defunct']=='Y')  //defunct problem not in contest/exam list
//                      $contest_ok = false;

                if (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))
                        $contest_ok = true;
          
                if ($unixnow>$end_time) $contest_is_over=true;    // 已经结束的比赛，按练习方式提交
                else $contest_is_over=false;

                if (!isset($_SESSION[$OJ_NAME.'_'.'administrator']) && $unixnow<$start_time) {
                        $view_errors = "<center>";
                        $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
                        $view_errors .= "<p>$view_description</p>";
                        $view_errors .= "<br>";
                        $view_errors .= "<span class=text-success>$MSG_TIME_WARNING</span>";
                        $view_errors .= "</center>";
                        $view_errors .= "<br><br>";

                        require("template/".$OJ_TEMPLATE."/error.php");
                        exit(0);
                }
        }
        if(!isset($OJ_CONTEST_LIMIT_KEYWORD)) $OJ_CONTEST_LIMIT_KEYWORD="限时";
        if($contest_ok && str_contains($view_description,$OJ_CONTEST_LIMIT_KEYWORD)&&isset($_SESSION[$OJ_NAME."_user_id"])){
                echo "<!-- 个人限时赛  -->";
                $contest_limit_minutes=intval(getPartByMark($view_description,$OJ_CONTEST_LIMIT_KEYWORD,"分钟"));  //允许比赛描述中用 "限时xx分钟" 规定个人做题时间。
                if($contest_limit_minutes==0) $contest_limit_minutes=120;
                echo "<!-- $contest_limit_minutes mins -->";
                $user_id=$_SESSION[$OJ_NAME."_user_id"];
                $first_login_contest=pdo_query("select time from loginlog where user_id=? and password=?",$user_id,"c".$cid);
                if(empty($first_login_contest)){
                        echo "<!-- 首次访问  -->";
                                $ip = ($_SERVER['REMOTE_ADDR']);
                                if( isset($_SERVER['HTTP_X_FORWARDED_FOR'] )&&!empty( trim( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ){
                                    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
                                    $tmp_ip=explode(',',$REMOTE_ADDR);
                                    $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
                                } else if(isset($_SERVER['HTTP_X_REAL_IP'])&& !empty( trim( $_SERVER['HTTP_X_REAL_IP'] ) ) ){
                                    $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
                                    $tmp_ip=explode(',',$REMOTE_ADDR);
                                    $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
                                }
                                $sql="INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,?,?,NOW())";
                                pdo_query($sql,$user_id,"c".$cid,$ip);
                         $first_login_contest=time();
                }else{
                        $first_login_contest=strtotime($first_login_contest[0][0]);
                }
                $unixnow=time();
                echo "<!-- $unixnow - $first_login_contest = ".($unixnow-$first_login_contest)." -->";
                if($unixnow-$first_login_contest>=$contest_limit_minutes*60 && !isset($_SESSION[$OJ_NAME."_m".$cid]) && !isset($_SESSION[$OJ_NAME."_administrator"])){
                        $contest_ok=false;

                        $view_errors = "<center>";
                        $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
                        $view_errors .= "<p>$view_description</p>";
                        require("template/".$OJ_TEMPLATE."/error.php");
                        exit(0);
                }else{
                        $time_left=$contest_limit_minutes*60-($unixnow-$first_login_contest);
                        if($time_left>0)
                        $view_description.="<br><span id='time_left'>$MSG_LeftTime :". intval($time_left/60)." $MSG_MINUTES" .($time_left % 60). $MSG_SECONDS."<span>";
                }

        };


        if (!$contest_ok) {
                $view_errors = "<center>";
                $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
                $view_errors .= "<p>$view_description</p>";
                $view_errors .= "<span class=text-danger>$MSG_PRIVATE_WARNING</span>";

                $view_errors .= "<br><br>";

                $view_errors .= "<div class='btn-group'>";
                $view_errors .= "<a href=contestrank.php?cid=$view_cid class='btn btn-primary'>$MSG_STANDING</a>";
                $view_errors .= "<a href=contestrank-oi.php?cid=$view_cid class='btn btn-primary'>OI$MSG_STANDING</a>";
                $view_errors .= "<a href=conteststatistics.php?cid=$view_cid class='btn btn-primary'>$MSG_STATISTICS</a>";
                $view_errors .= "</div>";

                $view_errors .= "<br><br>";
                $view_errors .= "<table align=center width=80%>";
                $view_errors .= "<tr align='center'>";
                $view_errors .= "<td>";
                $view_errors .= "<form class=form-inline method=post action=contest.php?cid=$cid>";
                $view_errors .= "<input class=input-mini type=password name=password value='' placeholder=$MSG_CONTEST-$MSG_PASSWORD>";
                $view_errors .= "<button class='form-control'>$MSG_SUBMIT</button>";
                $view_errors .= "</form>";
                $view_errors .= "</td>";
                $view_errors .= "</tr>";
                $view_errors .= "</table>";
                $view_errors .= "<br>";

                require("template/".$OJ_TEMPLATE."/error.php");
                exit(0);
        }
