<?php
// 这个文件用于对接deepseek，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;
// 登录 https://platform.deepseek.com/ ，打开 [API Keys](https://platform.deepseek.com/api_keys)  创建新的API KEY
// 注意这个功能可能会导致付费账单，
// 访问类似 https://platform.deepseek.com/usage
// 关注所用账户的剩余额度
require_once("../include/db_info.inc.php");
// 设置请求的URL
$url = 'https://api.deepseek.com/chat/completions';
$apiKey = "sk-你自己申请的api-key";   // https://platform.deepseek.com/api_keys  创建新的API KEY
$models=array("deepseek-chat");
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
if( !empty($answer)){
        echo htmlentities($answer[0][0]);
        echo "<!-- cached answer -->";
        exit();
}
$problem=pdo_query("select concat(description,'输入:',input,'输出:',output,'样例输入:',sample_input,'样例输出:',sample_output,'提示:',hint) from problem where problem_id=?",$problem_id)[0][0];



// 设置请求头
$headers = [
    'Authorization: Bearer '.$apiKey,
    'Content-Type: application/json'
];
if(isset($_SESSION[$OJ_NAME."_source_browser"])){
        $code_suggestion="分析我可能薄弱的知识点，问我一个提示性的相关问题。";
}else{
        $code_suggestion="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析我可能薄弱的知识点，问我一个提示性的相关问题，最后说一句鼓励或安慰的话，卖个萌。";
}

$model = $models[array_rand($models)];
// 设置请求体
$data = [
    // 此处以deepseek-plus为例，可按需更换模型名称。模型列表：https://help.aliyun.com/zh/model-studio/getting-started/models
    "model" => "$model",
    "messages" => [
        [
            "role" => "system",
            "content" => "你是一个编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。$code_suggestion 请尽量言简意赅，节省token消耗。"
        ],
        [
            "role" => "user",
            "content" => "题目是:".$problem."\n 源代码是:".$source."\n报错信息是:".$ceinfo
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
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
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
//echo $response;
$answer=$data->choices[0]->message->content."<br> --- $model  <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐吧 </a> ";

echo htmlentities($answer);
$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
pdo_query($sql,$sid,$answer);
