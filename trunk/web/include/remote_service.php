<?php
require_once(realpath(dirname(__FILE__)."/..")."/include/db_info.inc.php");
require_once(realpath(dirname(__FILE__)."/..")."/include/init.php");
require_once(dirname(__FILE__)."/curl.php");
 if(!$OJ_REMOTE_JUDGE) exit(0);
ini_set("display_errors", "Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
function is_login($remote_site){
	$html=curl_get($remote_site);
	//echo $html;
	return str_contains($html,"query")); 
}
function show_vcode($remote_site){
	$url = $remote_site.'/submit'; 
	$imgData=curl_get($url);
	//$pos=mb_strpos($imgData,"lighttpd/1.4.35")+19;
	//$imgBase64 = base64_encode(mb_substr($imgData,$pos,mb_strlen($imgData)-$pos));
	$imgBase64 = base64_encode($imgData);
	return '<img width=200px src="data:image/jpg;base64,'.$imgBase64.'" />';
}
function do_login($remote_site,$username,$password){
	$form= array(
    		'user_id' => $username,
    		'password' => $password,
		'action' => 'login'
	);
	$form=array("m"=>json_encode($form));
	//echo "try login...";
	$data=curl_post_urlencoded($remote_site,$form);
	//echo htmlentities($remote_site.'/login');
	if(str_contains($data,"fail")) return false;
	else return true;
}
function do_submit_one($remote_site,$username,$sid){
	$problem_id=1000;
	$language=1;
	$source="";
	$sql="select * from solution where result=16 and solution_id=?";
 	$data=pdo_query($sql,$sid);	
	if(count($data)>0){
		$row=$data[0];
	        $language=$row['language'];
	        $problem_id=$row['problem_id'];
		$sql="select remote_oj,remote_id from problem where problem_id=?";
		$data=pdo_query($sql,$problem_id);
		if(count($data)>0){
			$row=$data[0];
			$problem_id=$row['remote_id'];
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
	}
	$form=array(
		'problem_id' => $problem_id, 
		'language' => $language,
		'source' => ($source),
		'action' => 'submit'
	);
	//var_dump($form);
	$form=array("m"=>json_encode($form));
        $data=curl_post_urlencoded($remote_site,$form);
        if(str_contains($data,"error")) {
                $sid=0;
	}else{
		$json=json_decode($data);
		$sid=$json->solution_id;	
        }
        echo intval($sid);
        return $sid;
}
function do_submit($remote_site,$remote_user){ 
	global $remote_oj;
	//$sid=4496;
	$sql="select solution_id from solution where result=16 and remote_oj=? order by solution_id";
	$tasks=pdo_query($sql,$remote_oj);
	foreach($tasks as $task){
		//echo $task[0]."<br>";
		$sid=$task[0];	
		$rid=do_submit_one($remote_site,$remote_user,$sid); 
		if($rid>0){
			$sql="update solution set remote_oj=?,remote_id=?,result=17 where solution_id=?";
			pdo_query($sql,$remote_oj,$rid,$sid);
		}else{
			//40s once
			break;
		}
		usleep(150000);  //150ms
	}
}
function do_result_one($remote_site,$sid,$rid){
	$form=array(
		'solution_id' => ($rid),
		'action' => 'query'
	);
	//var_dump($form);
	$form=array("m"=>json_encode($form));
	$json=json_decode(curl_post_urlencoded($remote_site,$form));
        if(isset($json->error)) {
		echo $json->error;
                $sid=0;
		return -1;	
	}else{
		$reinfo="";
		$ac=0;
		$result=$json->result;
		$time=$json->time;
		$memory=$json->memory;
		echo "$sid : $result -> ";
		if($result<4) return -1;
		if($result==11) {
				$form=array(
					'solution_id' => $rid,
					'action' => 'ce'
				);
				//var_dump($form);
				$form=array("m"=>json_encode($form));
				$json=json_decode(curl_post_urlencoded($remote_site,$form));
				$reinfo=$json->error;
				$sql="insert into compileinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
				pdo_query($sql,$sid,$reinfo,$reinfo);
				$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judgetime=now()  where solution_id=?";
				pdo_query($sql,$result,0,$time,$memory,$sid);
				return $result;	
		}
		if($result==10||$ressult==4||$result==6) {
                                $form=array(
                                        'solution_id' => $rid,
                                        'action' => 're'
                                );
                                $form=array("m"=>json_encode($form));
                                $json=json_decode(curl_post_urlencoded($remote_site,$form));
                                $reinfo=$json->error;
                                $sql="insert into runtimeinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
                                pdo_query($sql,$sid,$reinfo,$reinfo);
                }
		if($result==4) $pass_rate=1;else $pass_rate=0;
		$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
		$ret=pdo_query($sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid);
		if($ret<0) echo $sql." ".$result." ".$pass_rate." ".$time." ".$memory." ".get_domain($remote_site)." ".$sid;
		else echo $ret . " <br>";
		 //get user_id
		$data=pdo_query("select user_id from solution where solution_id=?",$sid);
		$user_id=$data[0]['user_id'];
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
		$sql="UPDATE `users` SET `submit`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=?               ) WHERE `user_id`=?";
		pdo_query($sql,$user_id,$user_id);
		
        }
	return $result;
}
function do_result($remote_site){
	global $remote_oj;
	$sql="select solution_id,remote_id from solution where remote_oj=? and result=17 order by solution_id ";
	$data=pdo_query($sql,$remote_oj);
	foreach($data as $row){
		$sid=$row['solution_id'];
		$rid=$row['remote_id'];
		$ret=do_result_one($remote_site,$sid,$rid);
		if($ret<0) {
			echo "error code:".$ret;
			break;
		}else{
			usleep(1500);
		}		
	}
}
$remote_oj="service";   // problem表的remote_oj字段设demo，这里就设demo，本文件复制一份改名成remote_demo.php，并在../remote.php中增加扫描项。
$remote_site="http://demo.hustoj.com/service.php";  // 需要远程服务器运行开启service_port的最新版本HUSTOJ
$remote_user='账号';    //  远程系统具有 service_port 的可用状态(正常登录、未到期，有权限)账号
$remote_pass='密码';      //账号、密码 注意保存，更新时可能覆盖此文件
$remote_cookie=$OJ_DATA.'/'.get_domain($remote_site).'.cookie';
$remote_delay=5;
if(isset($_POST[$OJ_NAME.'_refer'])){
	header("location:".$_SESSION[$OJ_NAME.'_refer']);
	unset($_SESSION[$OJ_NAME.'_refer']);
}else{
	if(time()-fileatime($remote_cookie.".sub")>$remote_delay && is_login($remote_site) ){
		touch($remote_cookie.".sub");
		do_submit($remote_site,$remote_user);
	}
	if (!is_login($remote_site)){
		var_dump(do_login($remote_site,$remote_user,$remote_pass));
	}else if(isset($_SESSION[$OJ_NAME.'_refer'])){
		header("location:".$_SESSION[$OJ_NAME.'_refer']);
		unset($_SESSION[$OJ_NAME.'_refer']);
	}
}
if(time()-fileatime(__FILE__)>$remote_delay){
	touch(__FILE__);
	do_result($remote_site);
}
if(isset($_GET['check'])){
	$remote_delay*=2;
	echo "<meta http-equiv='refresh' content='$remote_delay'>";
	echo "$remote_oj<br>";
}
chmod($remote_cookie,0600);
