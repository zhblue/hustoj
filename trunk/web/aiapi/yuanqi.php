<?php
// 这个文件用于对接在腾讯元器自行训练的Agent      
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;  
//  登录元器账号，访问  https://yuanqi.tencent.com/my-creation/agent
// 需要配置助手ID 和 访问Token
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
$url = 'https://yuanqi.tencent.com/openapi/v1/agent/chat/completions';   // 设置请求的URL
$apiKey = "配置你的腾讯元器智能体Token";             //配置你的腾讯元器智能体Token
$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
	    $role="带出过多名信奥赛金牌选手的资深教练";
		$keyword="信奥教学";
		if( basename($http_referer)=="news_add_page.php"){
			$title=$_GET['title'];
			$prompt_sys="角色设定 (Persona)
你是一位拥有10年教学经验、$role。你熟悉很多不同的算法，可以轻易的想出一个有趣的题目或者根据已有的题目写一篇公众号。
可选的某种算法：
第一层：通用基础与核心语法
编程语言基础 (C++ STL, Java, Python)
基础语法与模拟
时间与空间复杂度分析
第二层：基础算法与数据结构 (信奥省选/NOIP & ICPC铜-银牌核心)
枚举、模拟、贪心
排序、二分查找、三分查找
递归、分治
基础数据结构：数组、链表、栈、队列、(有序)集合/映射、优先队列(堆)、并查集、树状数组、线段树、哈希表、字符串（KMP、字典树）
基础动态规划：线性DP、背包DP、区间DP、状态压缩DP
基础图论：图的存储（邻接表、矩阵）、DFS、BFS、拓扑排序、最短路（Dijkstra, Bellman-Ford, SPFA, Floyd）、最小生成树（Prim, Kruskal）、连通分量
基础数学：数论（质数筛法、GCD、同余）、组合数学（排列组合、卡特兰数）、简单博弈论、高精度计算
第三层：进阶算法与技巧 (信奥国赛/NOI & ICPC银-金牌关键)
搜索优化：迭代加深、双向BFS、启发式搜索（A*）、剪枝
数据结构进阶：可持久化数据结构、树链剖分、平衡树（Treap, Splay）、ST表、莫队算法、块状链表、跳表
动态规划进阶：树形DP、数位DP、状压DP优化、斜率优化、四边形不等式优化、概率DP
图论进阶：网络流（最大流、最小割、费用流）、强连通分量、双连通分量、割点与桥、二分图匹配（匈牙利算法、Hopcroft-Karp）、差分约束系统、2-SAT问题、LCA（最近公共祖先）
数学进阶：扩展欧几里得算法、中国剩余定理、莫比乌斯反演、快速傅里叶变换（FFT）、多项式、线性代数（矩阵快速幂、高斯消元）、概率与期望、群论基础（Burnside引理）
计算几何：点、线、多边形的基本运算，凸包，旋转卡壳，扫描线，半平面交
第四层：专题与高级内容 (NOI/ICPC决赛 & 顶级竞赛)
字符串算法：后缀数组、后缀自动机、回文树（Palindromic Tree）
动态规划：插头DP、动态DP
图论：最大团、最小树形图、支配树
数学：生成函数、组合设计、拟阵、线性规划
其他：博弈论（SG函数）、随机化算法、近似算法、启发式算法
";
		if($title==""){
			$prompt_sys.="你是个熟悉各类算法的工程师，可以想出一些有趣的标题。你言简意赅，只做非常简练的回答，不做任何解释。这个标题不会包含任何的markdown标记";
			$prompt_user="帮我想一个$keyword的吸引人的标题,随机挑选一个$keyword学习主题，不局限于某种算法，只要一个标题，不要多余的解释，只要标题，不要超过20个字" ;
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
		      if(isset($temperature)) $temperature=1.2;
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
    "assistant_id" => "元器助手ID",
    "user_id" => "$user_id" ,
    "stream" => false,
    "messages" => [
        [
            "role" => "user",
	    "content" => [
		[
		"type" => "text",
		"text" => "你是一个编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。".$code_suggestion."\n题目是: $problem \n源代码是:".$source."\n报错信息是:".$ceinfo
		]
	    ]
        ]
    ]
];
//echo htmlentities(json_encode($data));
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
//var_dump($data);
$answer=$data->choices[0]->message->content."<br> --- 腾讯元器 ";

echo htmlentities($answer);
$sql="insert into solution_ai_answer (solution_id,answer) values(?,?)";
pdo_query($sql,$sid,$answer);

?>
