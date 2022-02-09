<?php

require_once('./include/db_info.inc.php');

require_once('./include/memcache.php');

require_once('./include/my_func.inc.php');

require_once('./include/const.inc.php');

require_once('./include/cache_start.php');

if (isset($_POST['ids'])) {

	$ids=trim($_POST['ids']);

	 $sql = "SELECT * FROM problem where  problem_id in ".'('.$ids.')';

        $subList = pdo_query($sql, $cid);

       foreach($subList as $key=>$val){

      	 $sql = "SELECT * FROM `solution` WHERE `user_id` = '".$_SESSION[$OJ_NAME.'_'.'user_id']."' and `result` = 4 and problem_id = ".$val['problem_id'];

	$result = pdo_query($sql,$cid);
	$sql = "SELECT * FROM `solution` WHERE `user_id` = '".$_SESSION[$OJ_NAME.'_'.'user_id']."' and problem_id = ".$val['problem_id'];

	$result1 = pdo_query($sql,$cid);
	if(count($result) > 0){
	  $str = '<div class="label label-success">Y</div>';
	}elseif(count($result1) >0){
	  $str = '<div class="label label-danger">N</div>';
	}else{
	  $str = '';
	}
	$sql = "SELECT * FROM `solution`where  `result` = 4 and problem_id = ".$val['problem_id'];

	$result = pdo_query($sql,$cid);

       $data .='<tr><td>'.$str.'</td><td class="hidden-xs">'.$val['problem_id'].'</td><td><a href="/problem.php?id='.$val['problem_id'].'" >'.$val['title'].'</a></td><td>'.count($result).'</td></tr>';

       }

	echo json_encode($data);

}



?>