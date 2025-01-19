<?php
ini_set("display_errors", "Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
require_once("../include/db_info.inc.php");
if(!(isset($_SESSION[$OJ_NAME.'_administrator'])||isset($_SESSION[$OJ_NAME.'_problem_editor'])||isset($_SESSION[$OJ_NAME.'_contest_creator'])||isset($_SESSION[$OJ_NAME.'_tag_adder']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);  
}
function try_ajax($tb,$fd,$pr){
	global $OJ_NAME,$_SESSION,$_POST;
	$m=$_POST["m"];	
	if($m==$tb."_update_".$fd  && ( isset($_SESSION[$OJ_NAME.'_'.$pr]) )){
                $data_id=$_POST[$tb.'_id'];
                $new_value=$_POST[$fd];
		if($tb=="user") $tb_name="users";
		else $tb_name=$tb;
                $sql="update ".$tb_name." set `".$fd."`=? where ".$tb."_id=?";
                echo pdo_query($sql,$new_value,$data_id);
        }
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	$m=$_POST["m"];	
	if($m=="problem_add_source" && ( isset($_SESSION[$OJ_NAME.'_administrator']) || isset($_SESSION[$OJ_NAME.'_problem_editor']) || isset($_SESSION[$OJ_NAME.'_tag_adder']) ) ){
		$pid=intval($_POST['pid']);
		$new_source=($_POST['ns']);	
		$sql= "update problem set source=concat(source,' ',?) where problem_id=?";		
		echo pdo_query($sql,$new_source,$pid);
	}
	if($m=="problem_update_time" && ( isset($_SESSION[$OJ_NAME.'_administrator']) || isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
		$pid=intval($_POST['pid']);
		$time=intval($_POST['t']);	
		$sql= "update problem set time_limit=? where problem_id=?";		
		echo pdo_query($sql,$time,$pid);
	}
	if($m=="problem_get_title"  && ( isset($_SESSION[$OJ_NAME.'_administrator']) || isset($_SESSION[$OJ_NAME.'_problem_editor']) )){
                $pid=intval($_POST['pid']);
                $sql= "select title,source from problem where problem_id=?";
                $row=mysql_query_cache($sql,$pid)[0];
                echo $row['title']."&nbsp;&nbsp;<span class='label label-success'>".$row['source']."</span>";
	}
	
        if($m=="user_update_nick"  && ( isset($_SESSION[$OJ_NAME.'_administrator']) )){
                $user_id=$_POST['user_id'];
                $nick=$_POST['nick'];
                $sql= "update users set nick=? where user_id=?";
                echo pdo_query($sql,$nick,$user_id);
		$sql= "update solution set nick=? where user_id=?";
                pdo_query($sql,$nick,$user_id);
        }
	/*
        if($m=="user_update_expiry_date"  && ( isset($_SESSION[$OJ_NAME.'_administrator']) )){
                $user_id=$_POST['user_id'];
                $expiry_date=$_POST['expiry_date'];
                $sql= "update users set expiry_date=? where user_id=?";
                echo pdo_query($sql,$expiry_date,$user_id);
        }
	if($m=="user_update_school"  && ( isset($_SESSION[$OJ_NAME.'_administrator']) )){
                $user_id=$_POST['user_id'];
                $school=$_POST['school'];
                $sql= "update users set school=? where user_id=?";
                echo pdo_query($sql,$school,$user_id);
        }
	if($m=="user_update_group_name"  && ( isset($_SESSION[$OJ_NAME.'_administrator']) )){
                $user_id=$_POST['user_id'];
                $group_name=$_POST['group_name'];
                $sql="update users set group_name=? where user_id=?";
                echo pdo_query($sql,$group_name,$user_id);
	}
	 */
	// try_ajax("user","nick","administrator");
	try_ajax("user","expiry_date","administrator");
	try_ajax("user","school","administrator");
	try_ajax("user","group_name","administrator");
	try_ajax("news","importance","administrator");
	try_ajax("problem","time_limit","administrator");
        try_ajax("problem","memory_limit","administrator");

	if($m=="get_user_list_of_contest"  && ( isset($_SESSION[$OJ_NAME.'_administrator'])||isset($_SESSION[$OJ_NAME.'_contest_creator']) )){
			$contest_id=$_POST['contest_id'];
			$sql= "select distinct user_id from privilege where rightstr=? ";
			$users=pdo_query($sql,"c".$contest_id);
			foreach($users as $user){
					echo $user['user_id']."\r\n";
			}
	}

}
