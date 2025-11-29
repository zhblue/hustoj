<?php
// 这个文件用于对接huggingface [ https://huggingface.co/]，解析编译报错和运行错误信息。
// 需要在 db_info.inc.php 中配置 $OJ_AI_API_URL 指向本文件; 
// 登录https://huggingface.co/，打开 https://huggingface.co/settings/tokens 创建新的API KEY [create new token ]
// 注意这个功能可能会导致付费账单，
// 访问 https://huggingface.co/settings/billing
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
$apiKey ="hf_api_key";  // 配置你在 https://huggingface.co/settings/tokens 生成的key

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
        $tail= "<br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐ 吧 </a>";
        echo htmlentities($answer[0][0].$tail);
        exit();
}

// 设置请求的URL
$url = "https://router.huggingface.co/v1/chat/completions";

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
$models=array("Qwen/Qwen3-Coder-480B-A35B-Instruct:novita");
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
    ],
    "stream" => false,
    "max_tokens" => 4096
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
//curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:1080");  // 简体中文区可能需要这行，原因你懂的我不能细说
// 执行cURL会话
$response = curl_exec($ch);
// 检查是否有错误发生
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit();
}
// 关闭cURL资源
curl_close($ch);
// 输出响应结果
$data=json_decode($response);

$answer=$data->choices[0]->message->content;

$tail= "<br> --- $model <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐ 吧 </a>";

echo htmlentities($answer.$tail);
$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
pdo_query($sql,$sid,$answer);

?>
