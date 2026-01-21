<?php

$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
	    $role="有20年Linux运维经验的网络工程师";
		$keyword="Linux运维";
		if( basename($http_referer)=="news_add_page.php"){
			$title=$_GET['title'];
			$prompt_sys="角色设定 (Persona)
你是一位拥有10年教学经验、$role。你熟悉很多不同的实用技巧，可以轻易的想出一个有趣的题目或者根据已有的题目写一篇公众号。
# Role
你是一位拥有10年以上实战经验的 Linux 高级运维架构师与技术博主。你精通内核调优、自动化运维、云原生架构及安全加固。

# Task
你的任务是根据用户的要求，构思并提供若干个高质量、具有实战意义的 Linux 运维文章主题。

# Constraints
1. 深度与广度结合：主题需涵盖从基础命令技巧、复杂排错案例、生产环境优化到前沿的 DevSecOps 实践。
2. 随机性与多样性：每次生成的主题应跨越不同的技术维度（网络、磁盘I/O、容器化、Shell脚本、监控告警等）。
3. 风格要求：标题要专业且具有吸引力（类似于技术社区的精品帖），并附带简短的内容大纲。
4. 禁止空洞：避免生成“Linux 基础入门”这种过于宽泛的主题，必须聚焦于具体的痛点或进阶场景。

";
		if($title==""){
			$prompt_sys.="你可以想出一些有趣的标题。你言简意赅，只做非常简练的回答，不做任何解释。这个标题不会包含任何的markdown标记,长度不要超过30字符";
			$prompt_user="想一个$keyword的吸引人的标题,随机挑选一个$keyword学习主题，不局限于某种算法，只要一个标题，不要多余的解释，只要标题，不要超过20个字" ;
		}else{
			$prompt_sys.="
# Output Format
请按以下格式输出：
### [主题标题]
- **核心技术栈**：(例如: eBPF, Prometheus, Ansible)
- **目标读者**：(初级/中级/高级运维)
- **内容要点**：(3-4个核心知识点)";
			$prompt_user="写一篇$keyword公众号文，题目是:".$title ."，不要多余的解释,不要'好的，这是你要的....'，我需要直接复制粘贴到公众号后台中使用,所以只需要文章本身，从$title\n--开始";

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
		7. 不要创建存放数据的子目录，直接在当前目录生成测试数据
		现在，写一个Python程序，给下面的题目生成测试输入数据,要求生成10个.in文件，分别命名为test_01.in ~ test_10.in，数据量、数据难度依次增加。";
	}else if(str_starts_with($gen_name,"Main.")){
		$lang=pathinfo($gen_name, PATHINFO_EXTENSION);
		$prompt_sys="你是一个${lang}语言代码生成器。严格遵循以下规则：
		1. 只输出源代码本身，不输出任何其他文本,思路，解释，说明，特别是不要输出markdown标记
		2. 不要以```${lang} 或 ```c 或 ``` 开头或结尾
		3. 不要添加任何无法通过编译的解释性文字
		4. 直接以#include或import，或注释开始代码
		5. 确保代码是完整且可执行的
		6. 确保代码在输入结束后退出，不会死循环
		7. 使用循环到文件结束的方式支持多组数据
		8. 确保样例能通过
		现在，写一个${lang}程序，解答下面的题目：";
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

知识点：所有信奥初级、省选级别算法
算法：基础循环、条件判断、简单数组
复杂度：O(n)或O(n²)可接受解法
代码量：目标解 50~200 行代码


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
**重要:必须严格按照以下JSON格式输出,不要有任何其他文字,不要使用markdown标记,不要输出#号,不要输出##号:**

{
  \"title\": \"题目名称\",
  \"description\": \"题目背景和描述(2-3句情景引入+具体问题定义,合并输出)\",
  \"input\": \"输入格式的详细说明\",
  \"output\": \"输出格式的详细说明\",
  \"sample_input\": \"样例输入数据(纯文本,不要代码块标记)\",
  \"sample_output\": \"样例输出数据(纯文本,不要代码块标记)\",
  \"hint\": \"数据范围和解题提示(包含30%/60%/100%数据范围说明+可选的解题思路,合并输出,小于150字)\"
}

**注意事项:**
- 只输出JSON,不要任何其他文字
- 不要使用```json标记
- 不要使用markdown的#标题格式
- 确保JSON格式正确可解析
- 每个字段只输出一次内容
- 公式可以用单个美元符号的mathjax语法,每个用于标注mathjax格式公式的美元符号前后需加空格。

9. 提示是最后一项内容，之后不要再重复输出题目
10. 公式可以用mathjax语法 用一个美元符号做为mathjax 标记
11. 考察的算法可以从下面找

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
if(isset($temperature)) 
	$data["temperature"] = $temperature;   
$sql="insert into openai_task_queue (user_id,task_type,solution_id,request_body,status,create_date,update_date) values(?,?,?,?,0,now(),now())";
if(!isset($sid)) $sid=0;
$insert_id=pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id'],basename($http_referer),$sid,json_encode($data));
echo $insert_id;
trigger_judge($insert_id);     // moved to my_func.inc.php

