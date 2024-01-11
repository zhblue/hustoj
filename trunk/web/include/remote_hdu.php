<?php
require_once(realpath(dirname(__FILE__)."/..")."/include/db_info.inc.php");
require_once(realpath(dirname(__FILE__)."/..")."/include/init.php");
require_once(dirname(__FILE__)."/curl.php");
function is_login($remote_site){
	$html=curl_get($remote_site.'/control_panel.php');
	//echo $html;
	if (str_contains($html,"Sign Out")) return true; 
	else return false;
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
    		'username' => $username,
    		'userpass' => $password
	);
	//echo "try login...";
	$data=curl_post_urlencoded($remote_site.'/userloginex.php?action=login&cid=0&notice=0',$form);
	//echo htmlentities($remote_site.'/login');
	if(str_contains($data,"No such user or wrong password.")) return false;
	else return true;
}
function do_submit_one($remote_site,$username,$sid){
	
	$langMap= array(
    		0 => 1, //C
    		1 => 0, //C++
		2 => 4, //Pascal
		3 => 5, //Java
	);
	$problem_id=1000;
	$language=1;
	$source="";
	
	$sql="select * from solution where result=16 and solution_id=?";
 	$data=pdo_query($sql,$sid);	
	if(count($data)>0){
		$row=$data[0];
	        $language=$langMap[ $row['language']];
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
	while(strlen($source)<50) $source.="\n          \n";     // hdu要求至少50
	 $form=array(
                'problemid' => $problem_id,
                'language' => $language,
                'usercode' => ($source),
                '_usercode' => base64_encode(rawurlencode($source)),
                'check' => '0'
        );
        //var_dump($form);
        $data=curl_get($remote_site."/status.php?first=&pid=&user=".$username."&lang=0&status=0");
        echo (getPartByMark($data,"<td height=22px>","</td>"));
        $vid=intval(getPartByMark($data,"<td height=22px>","</td>"));
        echo "last id:".$vid;
        $data=curl_post_urlencoded($remote_site."/submit.php?action=submit",$form);
        echo ($data);
        if(str_contains($data,"ERROR")) {
                $sid=0;
        }else{
                $data=curl_get($remote_site."/status.php?first=&pid=&user=".$username."&lang=0&status=0");
                $sid=intval(getPartByMark($data,"<td height=22px>","</td>"));
                if($sid==$vid) $sid=-1;
        }
        echo "rid:".intval($sid);
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
		usleep(150000);
	}

}
function getResult($short){
        //echo "short:$short<br>";
        $map=array(
                "Queuing" => 17,
                "Accepted" => 4,
                "Runtime Error" => 10,
                "Runtime Error<br>(ACCESS_VIOLATION)" => 10,
                "Compilation Error" => 11,
                "Wrong Answer" => 6,
                "Presentation Error" => 5,
                "Time Limit Exceeded" => 7,
                "Memory Limit Exceeded" => 8,
                "Output Limit Exceeded" => 9,
                "System Error" => 10,
                "Validator Error" => 10,
        );
        if(isset($map[$short])){
                return $map[$short];
        }else if(mb_strpos($short,"Error")>0){
                return 10;
        }else{
                return 17;
        }
}

function do_result_one($remote_site,$sid,$rid){
	$html=curl_get($remote_site."/status.php?first=".$rid);
	$data=getPartByMark($html,"</center></form></td></tr>","</tr>");
	//echo $data;
	$reinfo="";
	$ac=0;
	$result=getPartByMark($data,"<font color","/font>");
	$result=getPartByMark($result,">","<");
	echo "RawResult:".$result;
	$result=getResult($result);
	$time=intval(getPartByMark($data,"</a></td><td>","MS"));
	$memory=intval(getPartByMark($data,"MS</td><td>","K"));
	echo "$sid : $result<br>";
	if($result==11) {
			$reinfo=curl_get($remote_site."/viewerror.php?rid=".$rid);
			$reinfo=getPartByMark($reinfo,"<pre>","</pre>");
			$sql="insert into compileinfo(solution_id,error) values(?,?) on duplicate key update error=? ";
			pdo_query($sql,$sid,$reinfo,$reinfo);
			$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
			pdo_query($sql,$result,0,$time,$memory,get_domain($remote_site),$sid);
			return $result;	
	}
	if($result==4){
		$pass_rate=1;
	}else{
		$pass_rate=0;
	}
	
	$sql="update solution set result=?,pass_rate=?,time=?,memory=?,judger=?,judgetime=now()  where solution_id=?";
	pdo_query($sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid);
	echo $sql,$result,$pass_rate,$time,$memory,get_domain($remote_site),$sid;
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
	}
	        //get user_id
        $data=pdo_query("select user_id from solution where solution_id=?",$sid);
        $user_id=$data[0]['user_id'];
        //update user
        $sql="UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=? AND `result`=4) WHERE `user_id`=?";
        pdo_query($sql,$user_id,$user_id);
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
		$ret=do_result_one($remote_site,$sid,$rid);
		if($ret<0) {
			echo "error code:".$ret;
			break;
		}else{
			usleep(150000);
		}
	}

}
//---------------------账号配置---------------------------
$remote_oj="hdu";
$remote_site="https://acm.hdu.edu.cn";
$remote_user='hustoj';         //// 请修改为你在acm.hdu.edu.cn注册的机器人账号
$remote_pass='freeproblemset'; // 请修改为你注册的机器人账号的密码
$remote_cookie=$OJ_DATA.'/'.get_domain($remote_site).'.cookie';
$remote_delay=15;
//--------------------------------------------------------
if(isset($_POST[$OJ_NAME.'_refer'])){
	header("location:".$_SESSION[$OJ_NAME.'_refer']);
	unset($_SESSION[$OJ_NAME.'_refer']);
}else{
	if(time()-fileatime($remote_cookie.".sub")>$remote_delay && is_login($remote_site) ){
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
	$remote_delay*=2;
	echo "<meta http-equiv='refresh' content='$remote_delay'>";
	echo "$remote_oj<br>";
}
chmod($remote_cookie,0600);
