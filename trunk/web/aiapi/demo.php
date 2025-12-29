<?php
// 这个文件用于对接阿里千问，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件; 
// 登录阿里云，打开 https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
// 注意这个功能可能会导致阿里云付费账单，
// 访问类似 https://bailian.console.aliyun.com/?tab=model#/model-market/detail/qwen3-coder-480b-a35b-instruct
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
// 设置请求的URL
$url = 'http://demo.hustoj.com/aiapi/proxy.php';
$apiKey = "设置为阿里云的API-KEY";   //https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
$models=array("qwen-turbo","qwen3-coder-480b-a35b-instruct","qwen3-max","qwen3-coder-30b-a3b-instruct");

$http_referer =parse_url( $_SERVER['HTTP_REFERER'])['path'];
if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) && str_starts_with( basename($http_referer),"phpfm.php")){
	$table=false;
	$pid=$_GET['pid'];
	$gen_name=$_GET['filename'];
	if($gen_name=="Gen.py"){
		$prompt_sys="你是一个Python代码生成器。严格遵循以下规则：
		1. 只输出Python代码，不输出任何其他文本，特别是不要输出markdown标记
		2. 不要以```python或```开头或结尾
		3. 不要添加\"这是一个...\"、\"以下是...\"等解释性文字
		4. 直接以import、def、class或注释开始代码
		5. 确保代码是完整且可执行的
		现在，写一个Python程序，给下面的题目生成测试输入数据,要求生成10个.in文件，分别命名为test_01.in ~ test_10.in，数据量、数据难度依次增加。";
	}else if($gen_name=="Main.c" || $gen_name=="Main.cc"){
		$prompt_sys="你是一个C语言代码生成器。严格遵循以下规则：
		1. 只输出C代码，不输出任何其他文本，特别是不要输出markdown标记
		2. 不要以```C 或 ```c 或 ``` 开头或结尾
		3. 不要添加\"这是一个...\"、\"以下是...\"等解释性文字
		4. 直接以#include或注释开始代码
		5. 确保代码是完整且可执行的
		6. 确保代码在输入结束后退出，不会死循环
		7. 使用类似while(EOF!=scanf(...))的方式支持多组数据
		现在，写一个C程序，解答下面的题目：";
	}else if(str_ends_with($gen_name,".in")){
		$prompt_sys="你是一个测试生成器。严格遵循以下规则：
		1. 只输出测试输入，不输出任何其他文本
		2. 不要以```text或```开头或结尾
		3. 不要添加\"这是一个...\"、\"以下是...\"等解释性文字
		4. 只按照题目要求格式输入
		5. 确保输入的数据符合题目要求";
	}
	$problem=pdo_query("select concat(description,'输入:',input,'输出:',output,'样例输入:',sample_input,'样例输出:',sample_output,'提示:',hint) from problem where problem_id=?",$pid)[0][0];
	$prompt_user="题目是:".$problem ;
}else{
       	if(str_starts_with( basename($http_referer),"reinfo.php")){
		$table="runtimeinfo";
	}else if(str_starts_with( basename($http_referer),"ceinfo.php")){
		$table="compileinfo";
	}

	if(isset($_SESSION[$OJ_NAME."_source_browser"])){
		$code_suggestion="分析我可能薄弱的知识点，问我一个提示性的相关问题。";
	}else{
		$code_suggestion="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析我可能薄弱的知识点，问我一个提示性的相关问题，最后说一句鼓励或安慰的话，卖个萌。";
	}
	$prompt_sys="你是一个编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。$code_suggestion 请尽量言简意赅，节省token消耗。";
	 
	$sid=intval($_GET['sid']);
	$solution=pdo_query("select user_id,problem_id from solution where solution_id=?",$sid)[0];
	$user_id=$solution[0];
	$problem_id=$solution[1];

	if(!(isset($_SESSION[$OJ_NAME."_source_browser"])|| $user_id==$_SESSION[$OJ_NAME."_user_id"] )){
		echo "非法参数";
		exit();
	}
	$sql="SELECT `source` FROM `source_code_user` WHERE `solution_id`=?";
	$result=pdo_query($sql,$sid);
	if(!empty($result)){
		$row=$result[0];
		$source=$row[0];
	}else{
		echo "非法参数";
		exit();
	}
	$sql="SELECT `error` FROM `$table` WHERE `solution_id`=?";
	$result=pdo_query($sql,$sid);
	if(!empty($result)){
		$row=$result[0];
		$ceinfo=$row[0];
	}else{
		echo "非法参数";
		exit();
	}
	$sql="select answer from solution_ai_answer where solution_id=? ";
	$answer=pdo_query($sql,$sid);
	if(!empty($answer)){
		echo htmlentities($answer[0][0]);
		echo "<!-- cached answer -->";
		exit();
	}
	$problem=pdo_query("select concat(description,'输入:',input,'输出:',output,'样例输入:',sample_input,'样例输出:',sample_output,'提示:',hint) from problem where problem_id=?",$problem_id)[0][0];
	$prompt_user="题目是:".$problem."\n 源代码是:".$source."\n报错信息是:".$ceinfo;

}

// 设置请求头
$headers = [
    'Authorization: Bearer '.$apiKey,
    'Content-Type: application/json'
];
$model = $models[array_rand($models)];
// 设置请求体
$data = [
    // 此处以qwen-plus为例，可按需更换模型名称。模型列表：https://help.aliyun.com/zh/model-studio/getting-started/models
    "model" => "$model",
    "messages" => [
        [
            "role" => "system",
	    "content" => $prompt_sys
 	],
        [
            "role" => "user",
	    "content" => $prompt_user 
	]
    ]
];
// 初始化cURL会话
$ch = curl_init();
// 设置cURL选项
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_REFERER, $domain );
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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
$data=json_decode($response);
if($table){
	$answer=$data->choices[0]->message->content."<br> --- $model  <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐吧 </a> ";
	echo htmlentities($answer);
	$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
	pdo_query($sql,$sid,$answer);
}else{
	echo ($data->choices[0]->message->content);	

}
?>
