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
$url = 'http://demo.hustoj.com/aiapi/proxy.php';   // 千问是：'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
$apiKey = "设置为阿里云的API-KEY";   //https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
$models=array("qwen-turbo","qwen3-coder-480b-a35b-instruct","qwen3-max","qwen3-coder-30b-a3b-instruct");
$temperature=0.8;
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

			if($task['solution_id']>0){
				$data=json_decode($response);
				$answer=$data->choices[0]->message->content."<br> --- $model  <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐吧 </a> ";
				$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
				pdo_query($sql,$task['solution_id'],$answer);

			}else{
			}
			$did++;
		}
	}
}while($did>0);
