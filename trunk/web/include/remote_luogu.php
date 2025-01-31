<?php

// by Baoshuo <i@baoshuo.ren> ( https://baoshuo.ren )

// alter table solution modify `remote_id` varchar(32) DEFAULT NULL;

require_once(realpath(dirname(__FILE__)."/..")."/include/db_info.inc.php");
require_once(realpath(dirname(__FILE__)."/..")."/include/init.php");
 if(!$OJ_REMOTE_JUDGE) exit(0);
require_once(dirname(__FILE__)."/curl.php");
function is_login($remote_site){
	return true; 
}
function show_vcode($remote_site){
	return '';
}
function do_login($remote_site,$username,$password){
	// $form= array(
	// 	'username' => $username,
	// 	'userpass' => $password,
	// );
	//echo "try login...";
	// $data=curl_post_urlencoded($remote_site.'/userloginex.php?action=login&cid=0&notice=0',$form);
	//echo htmlentities($remote_site.'/login');
	// if(str_contains($data,"No such user or wrong password.")) return false;
	return true;
}

function rmj_lg_curl_post_urlencoded($url,$form){
	global $curl,$OJ_DATA,$remote_cookie,$remote_user,$remote_pass;
	$curl = curl_init($url);
	//curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=buiebpv91e0cdhpmm6a320j1l7; path=/');
	//// 设置header
	// curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_USERPWD, $remote_user . ":" . $remote_pass);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $remote_cookie); // use saved cookies
	curl_setopt($curl, CURLOPT_COOKIEJAR, $remote_cookie);  // save coockies
	curl_setopt($curl, CURLOPT_REFERER, "$url"); 
	curl_setopt($curl, CURLOPT_USERAGENT, "HUSTOJ RemoteJudge (By baoshuo)"); 
	curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Requested-With: HUSTOJ RemoteJudge (By baoshuo)"]);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // 不要打印内容
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	// 设置 post 方式提交
	curl_setopt($curl, CURLOPT_POST, true);
	// 设置 post 数据
	$data="";
	foreach($form as $key => $value){
		$data.="$key=".urlencode($value)."&";
	}
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$data = curl_exec($curl);
	
	return $data;
}

function rmj_lg_curl_get($url){
	global $curl,$OJ_DATA,$remote_cookie,$remote_user,$remote_pass;
	$curl = curl_init($url);
	//curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=buiebpv91e0cdhpmm6a320j1l7; path=/');
        //curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_USERPWD, $remote_user . ":" . $remote_pass);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $remote_cookie); // use saved cookies
	curl_setopt($curl, CURLOPT_COOKIEJAR, $remote_cookie);  // save coockies
	curl_setopt($curl, CURLOPT_REFERER, "$url"); 
	curl_setopt($curl, CURLOPT_USERAGENT, "HUSTOJ RemoteJudge (By baoshuo)"); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Requested-With: HUSTOJ RemoteJudge (By baoshuo)"]);
	$data = curl_exec($curl);
	return $data;
}
function do_submit_one($remote_site,$username,$sid){
	global $curl;
	$langMap= array(
		0 => 'c/99/gcc', // C
		1 => 'cxx/noi/202107', // C++
		2 => 'pascal/fpc', // Pascal
		3 => 'java/8', // Java
	);
	$problem_id=1000;
	$language=1;
	$source="";
	
	$sql="select * from solution where result=16 and solution_id=?";
 	$data=pdo_query($sql,$sid);	
	if(count($data)>0){
		$row = $data[0];
		$language = $langMap[$row['language']];
		$problem_id = $row['problem_id'];
		$sql = "select remote_oj,remote_id from problem where problem_id=?";
		$data = pdo_query($sql,$problem_id);
		if (count($data)>0) {
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
		'pid' => $problem_id, 
		'lang' => $language,
		'o2' => '1',
		'code' => ($source),
		'trackId' => $sid,
	);
	var_dump($form);
	$data=rmj_lg_curl_post_urlencoded($remote_site."/judge/problem",$form);
	echo ($data), curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if (curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
		$sid=-1;
	}else{
		$data=json_decode($data,true);
		echo ($data);
		$sid=$data['requestId'];
	}
	echo "rid:".($sid);
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
		} elseif ($rid<0) {
			$sql="update solution set remote_oj=?,remote_id=?,result=10 where solution_id=?";
			pdo_query($sql,$remote_oj,$rid,$sid);
		}
		//40s once
		break;
	}
}
function getResult($short){
	//echo "short:$short<br>";
	$map=array(
		// "Accepted" => 4,
		12 => 4,
		// "Runtime Error<br>(ACCESS_VIOLATION)" => 10,
		7 => 10,
		// "Compilation Error" => 11,
		2 => 11,
		// "Wrong Answer" => 6,
		6 => 6,
		14 => 6,
		// "Presentation Error" => 5,
		// (none)
		// "Time Limit Exceeded" => 7,
		5 => 7,
		// "Memory Limit Exceeed" => 8,
		4 => 8,
		// "Output Limit Exceeded" => 9,
		3 => 9,
		// "System Error" => 10,
		// "Validator Error" => 10,
	);
	return $map[$short];
}
function do_result_one($remote_site,$sid,$rid){
	global $curl;
	$html=rmj_lg_curl_get($remote_site."/judge/result?id=".$rid);
	if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 204) return 17; // judging
	if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
		$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
		pdo_query($sql,10,0,0,0,get_domain($remote_site),$sid);
	
		return 10;
	}
	$data=json_decode($html, true);
	//echo $data;
	$reinfo="";
	$ac=0;
	if (!isset($data['data']) || !isset($data['data']['judge']) || !isset($data['data']['judge']['status'])) return 17;
	$result=$data['data']['judge']['status'];
	echo "RawResult:".$result;
	$result=getResult($result);
	$time=$data['data']['judge']['time'];
	$memory=$data['data']['judge']['memory'];
	echo "$sid : $result<br>";
	if($result==11) {
		$reinfo=$data['data']['compile']['message'];
		$sql="insert into compileinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
		pdo_query($sql,$sid,$reinfo,$reinfo);
		$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
		pdo_query($sql,$result,0,$time,$memory,get_domain($remote_site),$sid);
		return $result;	
	}
	if($result==4){
		$pass_rate=1;
	}else $pass_rate=0;
	$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
	pdo_query($sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid);
	// echo $sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid;
	//get user_id
        $data=pdo_query("select user_id from solution where solution_id=?",$sid);
        $user_id=$data[0]['user_id'];
	if($result==4){
		$pid=pdo_query("select problem_id from solution where solution_id=?",$sid)[0][0];
		$sql="update problem set accepted=(select count(1) from solution where result=4 and problem_id=?) where problem_id=?";
		pdo_query($sql,$pid,$pid);
		$sql="UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=? AND `result`=4) WHERE `user_id`=?";
        	pdo_query($sql,$user_id,$user_id);
	}
        //update user
        $sql="UPDATE `users` SET `submit`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=?               ) WHERE `user_id`=?";
        pdo_query($sql,$user_id,$user_id);

	return $result;
}
function do_result($remote_site){
	global $remote_oj;
	$sql="select solution_id,remote_id from solution where remote_oj=? and result=17 order by solution_id ";
	$data=pdo_query($sql,$remote_oj);
	foreach($data as $row){
		$sid=$row['solution_id'];
		$rid=$row['remote_id'];
	//	echo "$sid=>$rid";
		do_result_one($remote_site,$sid,$rid);
	}
}
//---------------------账号配置---------------------------
$remote_oj="luogu";
$remote_site="https://open-v1.lgapi.cn";
$remote_user='baoshuo'; // 请修改为你在洛谷开放平台获取的账号
$remote_pass='passw0rd'; // 请修改为你在洛谷开放平台获取的密码
$remote_cookie=$OJ_DATA.'/'.get_domain($remote_site).'.cookie';
$remote_delay=5;
//--------------------------------------------------------
if(isset($_POST[$OJ_NAME.'_refer'])){
	header("location:".$_SESSION[$OJ_NAME.'_refer']);
	unset($_SESSION[$OJ_NAME.'_refer']);
}else{
	if(time()-fileatime($remote_cookie.".sub")>$remote_delay){
		touch($remote_cookie.".sub");
		do_submit($remote_site,$remote_user);	
	}
 
	//echo (htmlentities(curl_get($remote_site."/login0.php")));
	if (!is_login($remote_site)){
		var_dump(do_login($remote_site,$remote_user,$remote_pass));
	}else{
		echo "logined...";
	}
	if(isset($_SESSION[$OJ_NAME.'_refer'])){
		header("location:".$_SESSION[$OJ_NAME.'_refer']);
		unset($_SESSION[$OJ_NAME.'_refer']);
	}
}
if(time()-fileatime(__FILE__)>$remote_delay){
	touch(__FILE__);
	do_result($remote_site);
}
if(isset($_GET['check'])){
	// $remote_delay*=2;
	echo "<meta http-equiv='refresh' content='$remote_delay'>";
	echo "$remote_oj<br>";
}
chmod($remote_cookie,0600);
