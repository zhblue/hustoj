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
$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
    $role="带出过多名信奥赛金牌选手的资深教练";
	$keyword="信奥教学";
	if( basename($http_referer)=="news_add_page.php"){
		$title=$_GET['title'];
			$prompt_sys="角色设定 (Persona)

你是一位拥有10年教学经验、$role。你擅长将复杂的算法思想，用生动、易懂的“教练语言”和现实类比讲清楚。你的文风亲切、专业且富有感染力，善于激发学生的挑战欲和好奇心。
核心指令 (Instruction)
请以“信奥教练”的身份，策划一篇用于微信公众号发布的短文。短文的核心目标是：向初中或高中阶段的信奥初学者及其家长，阐述“某个算法 并不像传说中那么难”，并激发他们学习与挑战的兴趣。
背景与约束 (Context)
场景：这是一个系列教学推文的第二篇，第一篇介绍了“什么是算法思维”。本文需要承上启下。
读者：
学生：有一定编程基础（如已掌握循环、数组），但对算法有畏难情绪，觉得某种算法“高深莫测”。
家长：可能不懂技术，但关心学习价值和孩子的心态。
目标：
认知层面：破除“某种算法=天才专属”的迷思，建立“某种算法是可学、可练的思维框架”的认知。
情感层面：减轻畏惧感，用成就感和趣味性替代焦虑。
行动层面：吸引读者留言互动、期待下一篇具体例题讲解。
平台：微信公众号文章。需考虑手机阅读体验，段落精短，可读性强。
输入材料与参考 (Input)
text
【核心类比（供你参考与发挥）】：
1.  某种算法 本质不是“灵光一闪”，而是“谨慎的指挥官”。就像打《星际争霸》或下棋，不是莽撞冲锋，而是基于当前情报（子问题），为最终胜利（原问题）做最优的局部决策（状态转移）。
2.  某种算法 的“记忆化”或“填表”，像玩“迷宫探宝”游戏时画地图。避免重复走冤枉路（重复计算），把走过的路（子问题解）记下来，整个地图（问题结构）就清晰了。
【必须包含的要点】：
*   点明某种算法的核心思想：“把大问题分解成小问题，记住小问题的答案，避免重复劳动。”
*   至少用一个极简的生活化例子（如爬楼梯、零钱兑换）瞬间说明白思想，不展开公式。
*   联系信奥赛实战：说明掌握某种算法对解决哪类经典赛题至关重要（如背包问题、最长公共子序列）。
*   给出一个极简的、可立即上手的“第一步”行动建议（例如：“今天，请只理解一句话：某种算法就是‘用空间换时间’的聪明记账法。”）。
【风格与禁忌】：
*   风格：口语化、对话感，多使用设问、比喻。可以适当使用表情符号（如😉、🚀、💡）和加粗强调重点。
*   禁忌：避免堆砌专业术语（如“状态转移方程”、“最优子结构”），必须用时要立刻用白话解释。绝对不要出现大段代码。
（以上内容是你的思考素材和约束边界，请充分消化后用于创作。）
输出要求 (Output)
结构：请按以下模块输出，并用---分隔：
【标题】：提供2-3个备选，要求吸引眼球、引发好奇。
【导语】：1-2句话，快速切入痛点，吸引读者继续阅读。
【正文】：涵盖上述要点，逻辑流畅。段落不超过3句话，适当使用小标题（如“一、卸下包袱：某种算法不是魔法”、“二、一个例子，看透本质”）。
【互动结尾】：设计1-2个引导性问题，鼓励读者在评论区留言。
【编者按/下期预告】：以教练口吻，用一两句话总结，并预告下一篇内容（例如：“下一篇，我们将用一道经典例题，手把手带你‘填’出第一个某种算法表格。”）。
长度：正文部分约2600-3800字。
语气：像一个充满热情、循循善诱的导师在和学生聊天。
思维链引导 (Chain of Thought - See)
在生成最终答案前，请先在你的思维中走通以下步骤：
破题：初学者听到“某个算法”时，最大的情绪障碍是什么？（畏难、觉得抽象）
定调：我这篇文章最想传递的核心情绪是什么？（轻松、自信、“原来如此”）
勾画路径：如何从“他们的恐惧”走到“我的目标”？顺序大概是：共情 -> 颠覆认知（用比喻） -> 实证（简单例子） -> 升华价值（与竞赛联系） -> 给出第一步。
设计亮点：哪个比喻或例子会成为本文的“记忆点”，让读者读完能复述给他人？
检查：我的每一部分内容，是否都服务于“破除畏惧、激发兴趣”这个唯一核心目标？有没有跑偏到纯技术讲解？
输出护栏与校验 (KERNEL原则)

Keep it Simple：核心目标唯一，就是“破除畏惧，激发兴趣”，而非“教会某种算法”。
Easy to Verify：文章完成后，可以明显判断是否避免了术语堆砌，是否读起来轻松有趣。
Reproducible：这个提示词框架可用于本系列任何一篇“破畏难”主题的短文。
Narrow：严格限定话题在“某种算法的初印象”，不涉及具体算法细节和代码实现。
Explicit Constraints：已明确禁止大段代码和滥用术语，明确了结构和长度。
Logical Structure：本提示词自身从角色到输出要求，结构清晰，便于AI解析。";
		if($title==""){
		    $prompt_user="帮我想一个$keyword的吸引人的标题,随机挑选一个$keyword学习主题，不局限于某种算法，只要标题，不要多余的解释，只要标题，不要超过20个字" ;	
		}else{
		    $prompt_user="帮我写一篇$keyword公众号文，题目是:".$title ."，不要多余的解释,不要'好的，这是你要的....'，我需要直接复制粘贴到公众号后台中使用,所以只需要文章本身，从$title\n--开始";
		}
	}else if(str_starts_with( basename($http_referer),"phpfm.php")|| str_starts_with( basename($http_referer),"submitpage.php") ){
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
		6. 严格按照题目要求的范围、难度比例来生成数据
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
2. 出题的时候不输出‘好的，遵照您的要求’这种开头，直接'#题目背景'开始, 注意不要重复输出同一个内容
3. 以用户给出的题目为题，创作一道小学生级别的NOIP编程题
4. 逻辑背景设计
生活化场景：将数学/逻辑问题融入日常情境
年龄适配：选择小学生熟悉的场景（学校、游戏、节日等）
问题直观：题目描述能让小学生直接理解要解决的问题

5. 难度控制标准
知识点：仅使用小学1-6年级数学知识
算法：基础循环、条件判断、简单数组
复杂度：O(n)或O(n²)可接受解法
代码量：目标解≈20-50行代码

6. 验证要求
样例能手工验证
边界情况明确
有唯一确定解
符合NOIP格式标准
主题填充示例
节日庆祝（如：元旦、春节、儿童节）
校园生活（如：分物品、排队、比赛计分）
游戏场景（如：棋盘游戏、卡牌游戏、闯关积分）
日常生活（如：购物计算、时间安排、路径选择）
经典故事（如：格林童话、四大名著、科幻电影）
7、必须有不同输入产生不同输出的情况，不能只有唯一输出可能。
8、题目每个部分信息只输出一次，不要重复输出、不要重复输出、不要重复输出

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
```[输入数据]
```
text
输出：
```[输出数据]
```
text
解释：[步骤说明 小于50字]

## 数据范围
- 对于30%的数据：[范围1]
- 对于60%的数据：[范围2]
- 对于100%的数据：[范围3]

## 提示
[可选解题思路提示 小于100字]

9. 提示是最后一项内容，之后不要再重复输出题目
";
        $prompt_user="题目信息**只输出一次**，题目是:".htmlentities($title);

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
