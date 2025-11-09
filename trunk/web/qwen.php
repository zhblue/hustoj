<?php
// 这个文件用于对接阿里千问，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $QWEN_API_KEY;  
// 登录阿里云，打开 https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
// 注意这个功能可能会导致阿里云付费账单，
// 访问类似 https://bailian.console.aliyun.com/?tab=model#/model-market/detail/qwen3-coder-480b-a35b-instruct
// 关注所用模型的剩余免费额度
require_once("include/db_info.inc.php");
$sid=intval($_GET['sid']);
$user_id=pdo_query("select user_id from solution where solution_id=?",$sid)[0][0];
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
$http_referer = $_SERVER['HTTP_REFERER'];
if(str_starts_with( basename($http_referer),"reinfo"))
	$table="runtimeinfo";
else       
	$table="compileinfo";

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

// 设置请求的URL
$url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
// 若没有配置环境变量，请用百炼API Key将下行替换为：$apiKey = "sk-xxx";
$apiKey = $QWEN_API_KEY;
// 设置请求头
$headers = [
    'Authorization: Bearer '.$apiKey,
    'Content-Type: application/json'
];
if(isset($_SESSION[$OJ_NAME."_source_browser"])){
	$code_suggestion="分析我可能薄弱的知识点，问我一个提示性的相关问题。";
}else{
	$code_suggestion="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析我可能薄弱的知识点，问我一个提示性的相关问题。";
}
$models=array("qwen-turbo","qwen3-coder-480b-a35b-instruct","qwen3-max","qwen3-coder-30b-a3b-instruct");
$model = $models[array_rand($models)];
// 设置请求体
$data = [
    // 此处以qwen-plus为例，可按需更换模型名称。模型列表：https://help.aliyun.com/zh/model-studio/getting-started/models
    "model" => "$model",
    "messages" => [
        [
            "role" => "system",
            "content" => "你是一个编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。$code_suggestion 请尽量言简意赅，节省token消耗。"
        ],
        [
            "role" => "user",
            "content" => "源代码是:".$source."\n报错信息是:".$ceinfo
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
// 执行cURL会话
$response = curl_exec($ch);
// 检查是否有错误发生
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}
// 关闭cURL资源
curl_close($ch);
// 输出响应结果
$data=json_decode($response);

$answer=$data->choices[0]->message->content."<br> --- $model";

echo htmlentities($answer);
$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
pdo_query($sql,$sid,$answer);

?>
