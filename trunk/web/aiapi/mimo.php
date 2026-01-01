<?php
// 这个文件用于对接小米Mimo，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;
// 登录小米开发者平台 https://platform.xiaomimimo.com/ ，打开 https://platform.xiaomimimo.com/#/console/api-keys  创建新的API KEY
// 注意这个功能可能会导致小米云付费账单，
// 访问类似 https://platform.xiaomimimo.com/#/console/usage
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
// 设置请求的URL
$url = 'https://api.xiaomimimo.com/v1/chat/completions';
$apiKey = "sk-申请你自己的apikey";   //https://platform.xiaomimimo.com/#/console/api-keys  创建新的API KEY
$models=array("mimo-v2-flash");

$temperature=0.8;
$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
       if(str_starts_with( basename($http_referer),"phpfm.php")|| str_starts_with( basename($http_referer),"submitpage.php") ){
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
       }else if(basename($http_referer)=="problem_add_page.php"){
	       $title=$_GET['title'];
		    if($title==""){
                       $prompt_sys="请创作一个天马行空、富有诗意或超现实意境的标题，具体要求如下：

1. 核心形式：一个简短的名词短语。
2. 核心手法：将两个看似无关的具象名词（或概念）进行诗意联结。
3. 字数限制：中文10字以内，英文3-5个单词为佳。
4. 效果要求：无需解释，但需激发强烈的好奇心与故事画面感。
5. 从古诗、词、成语、名著、神话、小说、卡通、漫画、修仙、短剧、网络梗中寻找灵感，可以直接用一句古诗、或者替换古诗中的名词为现代词汇
6. 不要说什么抱歉之类的话，我只需要一个简短的标题。
示例参考：鲸鱼背上的古书店、液态时钟、云朵收银机。

现在，请根据以上规则生成一个新的标题。";
                       $prompt_user="今天是".date("Y-m-d H:i:s").",找找最新的热点新闻，最近的节日、历史上的今天，给你一个随机数".rand()."，帮我想一个标题吧，不要多余的解释，就一个标题。";
				       $temperature=1.2;
               }else{

	       $prompt_sys="1. 你是一个经验丰富的ICPC NOIP 出题人
2. 出题的时候不输出‘好的，遵照您的要求’这种开头，直接'#题目背景'开始
3. 以用户给出的题目为题，创作一道小学生级别的NOIP编程题

创作要素要求
1. 逻辑背景设计
生活化场景：将数学/逻辑问题融入日常情境

年龄适配：选择小学生熟悉的场景（学校、游戏、节日等）

问题直观：题目描述能让小学生直接理解要解决的问题

2. 题目结构规范
text
[题目名称]
[题目背景]：约3-5句，建立情景联系

[题目描述]：
- 清晰定义问题
- 说明计算规则
- 用简单例子辅助理解

[输入格式]：
- 明确变量含义
- 说明数据范围
- 格式示例

[输出格式]：
- 明确输出内容
- 格式要求
- 精度/格式说明

[样例]：
输入：
[具体输入]
输出：
[对应输出]
样例解释：[逐步说明]

[数据范围]：
- 分级说明（如30%、60%、100%数据范围）
- 边界值说明
3. 难度控制标准
知识点：仅使用小学1-6年级数学知识

算法：基础循环、条件判断、简单数组

复杂度：O(n)或O(n²)可接受解法

代码量：目标解≈20-50行代码

4. 验证要求
样例能手工验证

边界情况明确

有唯一确定解

符合NOIP格式标准

主题填充示例
节日庆祝（如：元旦、春节、儿童节）

校园生活（如：分物品、排队、比赛计分）

游戏场景（如：棋盘游戏、卡牌游戏、闯关积分）

日常生活（如：购物计算、时间安排、路径选择）

输出格式示例
markdown
# [题目名称]

## 题目背景
[2-3句情景引入]

## 题目描述
[具体问题定义]

## 输入格式
[详细说明]

## 输出格式
[详细说明]

## 样例
输入：
[输入数据]

text
输出：
[输出数据]

text
解释：[步骤说明]

## 数据范围
- 对于30%的数据：[范围1]
- 对于60%的数据：[范围2]
- 对于100%的数据：[范围3]

## 提示
[可选解题思路提示] ";
	$prompt_user="题目是:".htmlentities($title);
			}
       }
}
if( basename($http_referer)=="reinfo.php" ||  basename($http_referer)=="ceinfo.php"){
       	if( basename($http_referer)=="reinfo.php"){
		$table="runtimeinfo";
	}else if( basename($http_referer)=="ceinfo.php"){
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
	"temperature" => "$temperature",
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
