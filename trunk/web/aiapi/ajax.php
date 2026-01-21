<?php
//ini_set("display_errors", "On");  //set this to "On" for debugging  ,especially when no reason blank shows up.
// 这个文件用于对接阿里千问，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件; 
// 登录阿里云，打开 https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
// 注意这个功能可能会导致阿里云付费账单，
// 访问类似 https://bailian.console.aliyun.com/?tab=model#/model-market/detail/qwen3-coder-480b-a35b-instruct
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$id=intval($_GET['id']);

if(isset($_SESSION[$OJ_NAME.'_user_id'])){
	$user_id=$_SESSION[$OJ_NAME.'_user_id'];

	$sql="select * from openai_task_queue where id=?";
	$tasks=pdo_query($sql,$id);

	if(!empty($tasks)){
		$task=$tasks[0];
		if($user_id==$task['user_id']){
			if($task['status']==2){
				$response=$task['response_body'];
				$data=json_decode($response);
				if(isset($data->choices[0]->message->content))
					echo ($data->choices[0]->message->content);	
				else
					echo $response;
			}else{
				echo "waiting";	
			}
		}else{
			echo "not your ai answer:".$user_id."[".$task['user_id']."]";
		}
	}
}
