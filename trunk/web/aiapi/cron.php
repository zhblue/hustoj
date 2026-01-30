<?php
ini_set("display_errors", "On");  //set this to "On" for debugging  ,especially when no reason blank shows up.
// 如果不是 CLI 环境，则直接退出
if (php_sapi_name() !== 'cli') {
    // 可以输出错误信息（可选）
    echo "This script can only be run from command line.\n";
    exit(1); // 非零退出码表示错误
}
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
require_once(dirname(dirname(__FILE__))."/".$OJ_AI_API_URL);

function uniqueSource($str) {
    // 用正则分割字符串，支持多个连续空格
    $arr = preg_split('/\s+/', trim($str));

    // 去除数组中的重复项
    $uniqueArr = array_unique($arr);

    // 重新拼接为空格分割的字符串
    $result = implode(' ', $uniqueArr);

    return $result;
}

$did=0;
do{
	$sql="select * from openai_task_queue where status=0 ";
	$tasks=pdo_query($sql);

	// 设置请求头
	$headers = [
	    'Authorization: Bearer '.$apiKey,
	    'Content-Type: application/json'
	];
	$model = $models[array_rand($models)];

	$did=0;
	foreach($tasks as $task){
		$data=$task['request_body'];
		if(pdo_query("update openai_task_queue set status=1 where id=? and status=0 ",$task['id'])){
			// 初始化cURL会话
			$ch = curl_init();
			if(!isset($timeout)) $timeout=60;
			// 设置cURL选项
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_REFERER, $OJ_NAME );
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			// 执行cURL会话
			$response = curl_exec($ch);
			// 检查是否有错误发生
			if (curl_errno($ch)) {
			    echo 'Curl error: ' . curl_error($ch);
				exit();   // 超时等错误发生时，不将结果入库，下次还能重试。
			}
			// 关闭cURL资源
			curl_close($ch);
			// 输出响应结果
			echo ($response);
			echo "\n\n";
			pdo_query("update openai_task_queue set response_body=?,status=2 where id=?",$response,$task['id']);
			$data=json_decode($response);
			if($task['solution_id']>0){ // 异步的错误解析
				$answer=$data->choices[0]->message->content."<br> --- $model  <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐吧 </a> ";
				$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
				pdo_query($sql,$task['solution_id'],$answer);

			}else if ($task['problem_id']>0 && ($task['task_type']=="problem_list.php")){  // 批量生成题目分类
				echo $task['problem_id']. " ".  $task['task_type']." ".$task['solution_id']." ".$task['task_type']."\n";
				$answer=$data->choices[0]->message->content;
				$pid=$task['problem_id'];
				$new_source=$answer;	
				$old_source=pdo_query("select source from problem where problem_id=?",$pid)[0][0];
				echo "old_source:".$old_source."\n";
				$new_source=uniqueSource($new_source." ".$old_source);
				$sql= "update problem set source=? where problem_id=?";		
				echo "new_source:".$new_source."\n";
				echo ($sql."[".$new_source.",".$pid."]");
				pdo_query($sql,$new_source,$pid);
			}
		$did++;
		}
	}
}while($did>0);
