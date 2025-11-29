<?php
// 这个文件用于对接智普bigmodel.cn，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件; 
// 注册邀请 智谱 GLM Coding 超值订阅，邀你一起薅羊毛！Claude Code、Cline 等 10+ 大编程工具无缝支持，“码力”全开，越拼越爽！立即开拼，享限时惊喜价！
//
// 链接：https://www.bigmodel.cn/claude-code?ic=YVNXOQOZX5
//
// 登录bigmodel.cn，打开 https://bigmodel.cn/usercenter/proj-mgmt/apikeys 创建新的API KEY
// 注意这个功能可能会导致付费账单，
// 访问类似 https://bigmodel.cn/finance-center/resource-package/package-mgmt
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 请用智普API Key将下行 $QWEN_API_KEY ;
$apiKey = "填写你申请的api-key";

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
$url = 'https://open.bigmodel.cn/api/paas/v4/chat/completions';

// 设置请求头
$headers = [
    'Authorization: Bearer '.$apiKey,
    'Content-Type: application/json'
];
if(isset($_SESSION[$OJ_NAME."_source_browser"])){
        $code_suggestion="分析可能薄弱的知识点，问一个提示性的相关问题。";
}else{
        $code_suggestion="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析可能薄弱的知识点，问一个提示性的相关问题。";
}
$models=array("glm-4.5","glm-4.5-air","glm-4.5-flash","glm-4.5-airx");
$model = $models[array_rand($models)];
// 设置请求体
$data = [
    // 此处以qwen-plus为例，可按需更换模型名称。模型列表：https://help.aliyun.com/zh/model-studio/getting-started/models
    "model" => "$model",
    "messages" => [
        [
            "role" => "system",
            "content" => "你是一个编程高手，能帮你用简单清晰的中文，解释看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。$code_suggestion 请尽量言简意赅，节省token消耗。"
        ],
        [
            "role" => "user",
            "content" => "源代码是:".$source."\n报错信息是:".$ceinfo
        ]
    ],
    "stream" => false,
    "max_tokens" => 10240
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
}
// 关闭cURL资源
curl_close($ch);
// 输出响应结果
$data=json_decode($response);

$answer=$data->choices[0]->message->content."<br> --- $model  <br><a href='https://github.com/zhblue/hustoj/' target=_blank > 如果你觉得这个系统对你有帮助，请到Github来给我们加 个
Star⭐吧 </a> ";

echo htmlentities($answer);
$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
pdo_query($sql,$sid,$answer);

?>
