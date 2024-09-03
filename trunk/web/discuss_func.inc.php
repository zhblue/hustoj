<?php
require_once("include/db_info.inc.php");
require_once("include/const.inc.php");
require_once("include/memcache.php");
if(isset($OJ_BBS)&&!$OJ_BBS){
	 $view_errors= "$MSG_BBS_NOT_ALLOWED_FOR_EXAM  || $MSG_BBS is not available.";
     require("template/".$OJ_TEMPLATE."/error.php");
     exit(0);
}
ob_start ();
function problem_exist($pid,$cid){
	if ($pid=='') $pid=0;
	if ($cid!='')
		$cid=intval($cid);
	else
		$cid='NULL';
	if($pid!=0)
		if($cid!='NULL')
			$sql="SELECT 1 FROM `contest_problem` WHERE `contest_id` = $cid AND `problem_id` = '".intval($pid)."'";
		else
			$sql="SELECT 1 FROM `problem` WHERE `problem_id` = ".intval($pid)."";
	else if($cid!='NULL')
		$sql="SELECT 1 FROM `contest` WHERE `contest_id` = $cid";
	else
		return true;
	$sql.=" LIMIT 1";
	//echo $sql;
	$result=pdo_query($sql);
	return count($result)>0;
}
function err_msg($msg){
        $view_errors= "$msg";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
}

$parm = "";
if(isset($_GET['pid'])){
  $pid = intval($_GET['pid']);
  $parm = "pid=".$pid;
}else{
  $pid = 0;
}
if(isset($_GET['cid'])){
    $cid = intval($_GET['cid']);
}else{
    $cid = 0;
}
$parm .= "&cid=".$cid;
