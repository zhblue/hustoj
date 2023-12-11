<?php
// 接口描述参考文件末尾注释
require_once(realpath(dirname(__FILE__)."/..")."/include/db_info.inc.php");
require_once(realpath(dirname(__FILE__)."/..")."/include/init.php");
require_once(dirname(__FILE__)."/curl.php");
function do_submit_one($remote_site,$username,$password,$sid){	
	$langMap= array(
    		0 => 7, //C
    		1 => 7, //C++
		6 => 5  //Python
	);
	$problem_id=3001;
	$language=7;
	$source="";
	$sql="select * from solution where  result=16 and solution_id=?";
 	$data=pdo_query($sql,$sid);	
	if(count($data)>0){
		$row=$data[0];
	        if(isset($langMap[ $row['language']])) $language=$langMap[ $row['language']];
	        $problem_id=$row['problem_id'];
		$sql="select remote_oj,remote_id from problem where problem_id=?";
		$data=pdo_query($sql,$problem_id);
		if(count($data)>0){
			$row=$data[0];
			$problem_id=$row['remote_id'];
			if ($problem_id<=0) {
				echo "请修复题目的remote_id，否则无法评测。";
				return -1;
			}
		}else{
			return -1;
		}
	}else{
		return -1;
	}
	$sql="select * from source_code where solution_id=?";
 	$data=pdo_query($sql,$sid);	
	if(count($data)>0){
		$row=$data[0];
		$source=$row['source'];
		if(strlen($source)>20000){
                          $source=substr($source,0,19999);
                }
	}
	$form=array(
		'user_id' => $username,
		'password' => $password,
		'problem_id' => $problem_id, 
		'language' => "$language",
		'data_id' => "bas",
		'source' => $source,
		'submit' => '提交'
	);
	$data=curl_post($remote_site."/acx.php",$form);
	if(str_contains($data,"-2")) {
		$sid=0;
		echo "too frequently";
	}else{
	       	$sid=explode("\n",$data);
	       	$sid=intval($sid[1]);
		echo htmlentities($data)."--".$sid;	
	}
	return $sid;
}
function do_submit($remote_site,$remote_user,$remote_pass){ 
	global $remote_oj;
	$sql="select solution_id from solution where result=16 and remote_oj=? order by solution_id";
	$tasks=pdo_query($sql,$remote_oj);
	foreach($tasks as $task){
		//echo $task[0]."<br>";
		$sid=$task[0];	
		$rid=do_submit_one($remote_site,$remote_user,$remote_pass,$sid); 
		if($rid>0){
			$sql="update solution set remote_oj=?,remote_id=?,result=17 where solution_id=?";
			pdo_query($sql,$remote_oj,$rid,$sid);
		}else{
			continue;
		}
		usleep(500);
	}
}
function getResult($short){
	//echo "short:$short<br>";
	$map=array(
		"AC" => 4,		"RE" => 10,		"CE" => 11,
		"WA" => 6,		"PE" => 5,		"TLE" => 7,
		"MLE" => 8,		"OLE" => 9,		"RF" => 10,
	);
	return $map[$short];
}
function do_result_one($remote_site,$username,$password,$sid,$rid){
	$form=array(
		'user_id' => $username,
		'password' => $password,
		'runid' => $rid,
		'submit' => '提交'
	);
	$html=curl_post($remote_site."/stux.php",$form);
	$data=explode("\n",$html);
	if ( intval($data[0])<0) return  intval($data[0]);
	$reinfo="";
	$ac=0;
	$result=5;
	$time=0;
	$memory=0;
	if(substr($html,3)=="-1"){
                pdo_query("update solution set result=16,remote_id=0 where solution_id=?",$sid);
                echo "previous submission failed , pending another submiting ";
                return -1;
        }
	echo "<br>==".htmlentities($html)."==";
        if($data[2]=="Waiting"||$data[2]=="Judging"){
                $sql="update solution set result=17,judgetime=now()  where solution_id=?";
                pdo_query($sql,$sid);
                return -1;
        }else if($data[2]=="Compile Error"){
                $reinfo=$html;
                $sql="insert into compileinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
                pdo_query($sql,$sid,$reinfo,$reinfo);
                $result=11;
                $sql="update solution set result=?,pass_rate=?,time=?,memory=?,judgetime=now()  where solution_id=?";
                pdo_query($sql,$result,0,$time,$memory,$sid);
                return $result;
        }else if(str_contains($data[2],"Accepted")){
                $result=4;
        }else if(str_contains($data[2],"Unaccepted")){
                $result=5;
        }

	
	$summary=explode(":",$data[2]);
	$detail=explode(",",$summary[1]);
	$total=count($detail)-1;
	$i=0;
	foreach($detail as $line){
		if ($line=="") continue;
		$i++;
		$re=explode("|",$line);
		echo $re[0]."<br>";
		if($re[0]=="AC") $ac++;
		else $result=getResult($re[0]);
		$m_t=explode("_",$re[1]);
		$memory+=intval($m_t[0]);
		$time+=intval($m_t[1]);
		$reinfo.= $i.":   ". $re[0]." ".intval($m_t[0])."kb  ".intval($m_t[1])."ms \n";
	}
	if($ac==$i) {
		$result=4;
	}
	if($result==4&&$time==0&&$memory==0) return -1;
	//get user_id
	$data=pdo_query("select user_id from solution where solution_id=?",$sid);
	$user_id=$data[0]['user_id'];
	//update user
	$sql="UPDATE `users` SET `submit`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=?               ) WHERE `user_id`=?";
	pdo_query($sql,$user_id,$user_id);
	
	$sql="insert into runtimeinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
	pdo_query($sql,$sid,$reinfo,$reinfo);
	if($total>0)
	    $pass_rate=floatval($ac)/$total;
	else if ($result==4)
		$pass_rate=1;
	else $pass_rate=0;
	//echo "$sid : $pass_rate<br>";
	$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
	pdo_query($sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid);
	//echo "$sql,$result,$pass_rate,$time,$memory,$sid";
	if($result==4){
                $pc=pdo_query("select problem_id,contest_id from solution where solution_id=?",$sid)[0];
                $pid=$pc[0];
                $cid=$pc[1];
                $sql="update problem set accepted=(select count(1) from solution where result=4 and problem_id=?) where problem_id=?";
                pdo_query($sql,$pid,$pid);
                if($cid>0){
                     $sql="UPDATE `contest_problem` SET `c_accepted`=(SELECT count(*) FROM `solution` WHERE `problem_id`=? AND `result`=4 and contest_id=?) WHERE `problem_id`=? and contest_id=?";
                     pdo_query($sql,$pid,$cid, $pid,$cid);
                }
		$sql="UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=? AND `result`=4) WHERE `user_id`=?";
		pdo_query($sql,$user_id,$user_id);
	}
	return $result;
}
function do_result($remote_site,$remote_user,$remote_pass){
	global $remote_oj;
	$sql="select solution_id,remote_id from solution where remote_oj=? and result=17 order by solution_id ";
	$data=pdo_query($sql,$remote_oj);
	foreach($data as $row){
		$sid=$row['solution_id'];
		$rid=$row['remote_id'];
		echo "$sid=>$rid";
		$ret=do_result_one($remote_site,$remote_user,$remote_pass,$sid,$rid);
		if($ret<0) {
			echo "error code:".$ret;
			break;
		}else{
			usleep(150000);
		}
	}
}
// 本组件由一本通系列教材作者董永建老师委托开发，以GPL v2形式开源，参考本组件的代码进行二次开发，请注意遵守开源协议。
// 判题API由一本通系列OJ开发维护者文仲友老师提供，使用时请遵守基本的互联网礼仪，若出现访问频率过快，提交恶意程序，可能会禁用相关测试账号，敬请谅解。

$remote_oj="bas";
$remote_site="http://www.ssoier.cn:18087/pubtest/";  //备用地址："http://117.176.123.236:18087/pubtest/"
$remote_user='用户名';    //测试期到2024-8-1结束，一个机构一个账号，请勿外借。
$remote_pass='密码';      //账号、密码加群23361372，找群主登记： 学校或机构	email	手机  后可以申请。
$remote_cookie=$OJ_DATA.'/'.get_domain($remote_site).'.cookie';
$remote_delay=1;
if(time()-fileatime($remote_cookie.".sub")>$remote_delay){
	touch($remote_cookie.".sub");
	do_submit($remote_site,$remote_user,$remote_pass);	
}
if(isset($_SESSION[$OJ_NAME.'_refer'])){
	header("location:".$_SESSION[$OJ_NAME.'_refer']);
	unset($_SESSION[$OJ_NAME.'_refer']);
}
if(time()-fileatime(__FILE__)>$remote_delay){
	touch(__FILE__);
	do_result($remote_site,$remote_user,$remote_pass);
}
if(isset($_GET['check'])){
	$remote_delay*=2;
	echo "<meta http-equiv='refresh' content='$remote_delay'>";
	echo "$remote_oj<br>";
}
chmod($remote_cookie,0600);

/* 
以下接口描述，由文老师提供，供其他OJ系统开发者参考。
-----------------------------------
题面前台:
http://bas.ssoier.cn:8086/problem_list.php?page=10,10
一、程序提交：
   网址：http://www.ssoier.cn:8087/pubtest/index1.php
   返回值：
   -2：访问频繁（低于50Ms访问一次）
   -1：访问出错
   0：提交成功（首行为0，第二行为运行结果id,即runid,为一个正整数）
二、获取结果：
   网址：http://www.ssoier.cn:8087/pubtest/index2.php
   返回值：
   -2：访问频繁（低于50Ms访问一次）
   -1：访问出错
   首行为0：访问成功，第2行是runid,第3行开始有以下情况：
   （1）Waiting(等待评测)
   （2）Judging(正在评测)
   （3）"Compile Error",第4行开始为具体的编译信息
   （4）Accepted...(通过，具体评测信息)
   （5）Unaccepted...(未通过，具体评测信息)
*/
