<?php
function get_problem_text($pid){
	global $MSG_Description,$MSG_Input,$MSG_Output,$MSG_Sample_Input,$MSG_Sample_Output,$MSG_HINT;
		$problem=pdo_query("select * from problem where problem_id=?",$pid)[0];
		if (empty($problem)) return "missing problem text";
		$spj=$problem['spj'];
		$problem_text= "<br>\n\n## ".$problem["title"];
		$problem_text.= "<br>\n\n## ".$MSG_Description." <br>\n\n".$problem["description"];
		if(!empty($problem["input"])) $problem_text.="<br>\n\n## ".$MSG_Input."<br>\n\n".$problem["input"]."<br>\n\n";
		if(!empty($problem["output"])) $problem_text.="<br>\n\n## ".$MSG_Output."<br>\n\n".$problem["output"]."<br>\n\n";
		if(!empty($problem["sample_input"])) $problem_text.="<br>\n\n## ".$MSG_Sample_Input."<br>\n\n".$problem["sample_input"]."<br>\n\n";
		if(!empty($problem["sample_output"])) $problem_text.="<br>\n\n## ".$MSG_Sample_Output."<br>\n\n".$problem["sample_output"]."<br>\n\n";
		if(!empty($problem["hint"])) $problem_text.="<br>\n\n## ".$MSG_HINT."<br>\n\n".$problem["hint"]."<br>\n\n";
		return $problem_text;
}
if(basename($_SERVER['PHP_SELF'])!=="cron.php"){

	$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
	if( isset($_SESSION[$OJ_NAME.'_administrator']) &&  basename($http_referer)=="aidba.php"){
		$purpose=$_POST['purpose'];
		$dbSqlPath = '/home/judge/src/install/db.sql';
		$dbSchema = file_exists($dbSqlPath) ? file_get_contents($dbSqlPath) : "无法读取数据库结构文件。";
		$prompt_sys="你是一个 MySQL 专家，请根据以下数据库表结构定义，编写一个标准的 MySQL 查询语句来帮助用户查询数据库，\n\n表结构定义：\n{$dbSchema}\n\n 请直接输出 SQL 语句，不要包含 Markdown 格式，不要显示密码字段，不要包含解释。产生的结果列名尽量用中文别名";
		$prompt_user="任务目标：\"{$purpose}\"。\n\n 列名含义: ".json_encode($hustoj_zh_columns)." \n\n";
	
	}
	if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
			$keyword="Linux运维";
		if( basename($http_referer)=="news_add_page.php"){
				$title=$_GET['title'];
				$prompt_sys=file_get_contents(dirname(__FILE__)."/news.md"); 
				if($title==""){
					$prompt_sys.="你可以想出一些有趣的标题。你言简意赅，只做非常简练的回答，不做任何解释。这个标题不会包含任何的markdown标记,长度不要超过30字符,可以随机的采用某一句诗词或者游戏的名字，或者上市公司的简称，用广为人知的梗替换诗句中的名词。";
					$prompt_user="想一个$keyword的吸引人的标题,随机挑选一个$keyword学习主题，不局限于某种算法，只要一个标题，不要多余的解释，只要标题，不要超过20个字" ;
				}else{
					$prompt_user="写一篇$keyword公众号文，题目是:".$title ."，不要多余的解释,不要'好的，这是你要的....'，我需要直接复制粘贴到公众号后台中使用,所以只需要文章本身，从$title\n--开始";
				}
		}else if(str_starts_with( basename($http_referer),"phpfm.php")|| str_starts_with( basename($http_referer),"submitpage.php") ){
			$table=false;
			$pid=intval($_GET['pid']);
			$gen_name=$_GET['filename'];
			if($gen_name=="Gen.py"){
				$prompt_sys=file_get_contents(dirname(__FILE__)."/genpy.md");
			}else if(str_starts_with($gen_name,"Main.")){
				$lang=pathinfo($gen_name, PATHINFO_EXTENSION);
				$prompt_sys="你是一个${lang}语言高手ACM/ICPC，NOIP金牌选手级别的代码生成器，完胜绝大多数人类选手。严格遵循以下规则：
				1. 只输出源代码本身，不输出任何其他文本,思路，解释，说明，特别是不要输出markdown标记
				2. 不要以```${lang} 或 ```c 或 ``` 开头或结尾
				3. 不要添加任何无法通过编译的解释性文字
				4. 直接以#include或import，或注释开始代码
				5. 确保代码是完整且可执行的
				6. 确保代码在输入结束后退出，不会死循环
				7. 使用循环到文件结束的方式支持多组数据
				8. 确保样例能通过
				9. 别人家的AI都通过了，现在就剩你了，加油啊。
				现在，写一个${lang}程序，解答下面的题目：";
			}else if(str_ends_with($gen_name,".in")){
				$prompt_sys="你是一个测试生成器。严格遵循以下规则：
				1. 只输出测试输入，不输出任何其他文本
				2. 不要以```text或```开头或结尾
				3. 不要添加\"这是一个...\"、\"以下是...\"等解释性文字
				4. 只按照题目要求格式输入
				5. 确保输入的数据符合题目要求";
			}
			$problem_text=get_problem_text($pid);
			$prompt_user="题目是:".$problem_text ;
		}else if(basename($http_referer)=="problem_list.php"){
			if(isset($_GET['pid'])){
				$pid=intval($_GET['pid']);
				$prompt_sys="你是一位经验丰富的信奥教练，帮我给出这个题目的算法分类, 请用空格分割不同的分类名称，给出至少一个分类名，不要输出其他内容，例如:
	高精度 动态规划 背包问题 数论 几何 贪心";
				$problem_text=get_problem_text($pid);
				$prompt_user="题目是:".$problem_text."\n , 请帮我写个极简分类，不要解释，只要分类，数量不超过4个";
			}
	       }else if(basename($http_referer)=="problem_add_page.php"){
		       $title=$_GET['title'];
			    if($title==""){
			       $prompt_sys=file_get_contents(dirname(__FILE__)."/title.md");
			       $prompt_user="今天是".date("Y-m-d H:i:s").",找找最新的热点新闻，最近的节日、历史上的今天，可以参考一些唐诗宋词、股票简称、动漫剧情、网络热梗，给你一个随机数".rand()."，帮我想一个标题吧，不要多余的解释，就一个标题。";
			      if(isset($temperature)) $temperature=1.2;
		       }else{

			       $prompt_sys=file_get_contents(dirname(__FILE__)."/problem.md");
			       $prompt_user="题目信息**只输出一次**，题目是:".htmlentities($title);

		       }
	       }
	}
	if( basename($http_referer)=="reinfo.php" ||  basename($http_referer)=="ceinfo.php"  || basename($http_referer)=="status.php"){
		if( basename($http_referer)=="reinfo.php"){
			$table="runtimeinfo";
		}else if( basename($http_referer)=="ceinfo.php"){
			$table="compileinfo";
		}

		if(isset($_SESSION[$OJ_NAME."_source_browser"])){
			$code_suggestion=$MSG_AI_CODE_SUGGESTION_SOURCE_BROWSER;
		}else{
			$code_suggestion=$MSG_AI_CODE_SUGGESTION;
		}
		$prompt_sys=sprintf($MSG_AI_PROMPT_SYS,$code_suggestion);
		 
		$sid=intval($_GET['sid']);
		$solution=pdo_query("select user_id,problem_id,result from solution where solution_id=?",$sid)[0];
		$user_id=$solution[0];
		$problem_id=abs($solution[1]);
		$result=$solution[2];
		if($result==11){
			$table="compileinfo";
			$http_referer="ceinfo.php";
		}else{
			$table="runtimeinfo";
			$http_referer="reinfo.php";
		}
		$problem=pdo_query("select * from problem where problem_id=?",$problem_id)[0];
		$spj=$problem['spj'];
		$problem_text= "<br>\n\n## ".$problem["title"];
		$problem_text.= "<br>\n\n## ".$MSG_Description." <br>\n\n".$problem["description"];
		if(!empty($problem["input"])) $problem_text.="<br>\n\n## ".$MSG_Input."<br>\n\n".$problem["input"]."<br>\n\n";
		if(!empty($problem["output"])) $problem_text.="<br>\n\n## ".$MSG_Output."<br>\n\n".$problem["output"]."<br>\n\n";
		if(!empty($problem["sample_input"])) $problem_text.="<br>\n\n## ".$MSG_Sample_Input."<br>\n\n".$problem["sample_input"]."<br>\n\n";
		if(!empty($problem["sample_output"])) $problem_text.="<br>\n\n## ".$MSG_Sample_Output."<br>\n\n".$problem["sample_output"]."<br>\n\n";
		if(!empty($problem["hint"])) $problem_text.="<br>\n\n## ".$MSG_HINT."<br>\n\n".$problem["hint"]."<br>\n\n";
		
		if(!(isset($_SESSION[$OJ_NAME."_source_browser"])|| $user_id==$_SESSION[$OJ_NAME."_user_id"] )){
			echo $MSG_AI_INVALID_PARAM;
			exit();
		}
		$sql="SELECT `source` FROM `source_code_user` WHERE `solution_id`=?";
		$result=pdo_query($sql,$sid);
		if(!empty($result)){
			$row=$result[0];
			$source=$row[0];
		}else{
			echo $MSG_AI_INVALID_PARAM;
			exit();
		}
		$sql="SELECT `error` FROM `$table` WHERE `solution_id`=?";
		$result=pdo_query($sql,$sid);
		if(!empty($result)){
			$row=$result[0];
			$ceinfo=$row[0];
			if($spj==2) $ceinfo="前面的选择题，系统批阅解雇如下，每行依次是\n\n 题号 Answer:正确答案[You:错误答案] 扣除分数 \n\n，请帮我解释我错在何处？".$ceinfo;
		}else{
			echo $MSG_AI_INVALID_PARAM;
			exit();
		}
		$sql="select answer from solution_ai_answer where solution_id=? ";
		$answer=pdo_query($sql,$sid);
		if(!empty($answer)){
			echo htmlentities($answer[0][0]);
			exit();
		}


		$prompt_user="$MSG_AI_PROMPT_USER_TITLE<br>\n".$problem_text." <br>\n$MSG_AI_PROMPT_USER_SOURCE\n<pre>\n".htmlentities($source)."\n</pre>\n\n$MSG_AI_PROMPT_USER_ERROR\n<pre>\n".htmlentities($ceinfo)."\n</pre>\n\n";

	}

	// ---------------------------------------------------------------------------
	// 题目推荐（recommend.php）：根据近期做题记录与系统分类，给出练习建议
	// ---------------------------------------------------------------------------
	if (basename($http_referer) == "recommend.php" || isset($_POST['recent'])) {
		if (!isset($_SESSION[$OJ_NAME . '_user_id'])) {
			echo "login required";
			exit();
		}
		$recent     = isset($_POST['recent'])     ? json_decode($_POST['recent'], true)     : array();
		$categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : array();
		$pc         = isset($_POST['problem_count']) ? intval($_POST['problem_count']) : 0;

		$recent_text = "";
		if (!empty($recent) && is_array($recent)) {
			foreach ($recent as $i => $p) {
				$t = isset($p['title'])  ? $p['title']  : '';
				$s = isset($p['source']) ? $p['source'] : '';
				$recent_text .= ($i + 1) . ". 《" . $t . "》 分类：" . $s . "\n";
			}
		}
		$cat_text = !empty($categories) ? implode("、", $categories) : "（暂无分类信息）";

		// 关键约束：只推荐“本系统题库已有的分类/题目”，禁止引用外部 OJ（POJ/HDU/Luogu/CF 等），
		// 并强制用 [分类](problemset.php?search=分类) 的 Markdown 链接输出，使推荐文本中的链接可点击触发搜索。
		$prompt_sys = "你是一位经验丰富的算法竞赛教练，熟悉在线评测系统（OJ）上的编程题目。"
			. "请根据用户最近练习过的题目，分析他的知识掌握情况，并给出针对性的后续练习建议。\n\n"
			. "【硬性约束】\n"
			. "1. 你只能基于下方“系统可用的题目分类”来给出建议，严禁推荐任何外部 OJ（如 POJ、HDU、Luogu/洛谷、Codeforces/CF、LeetCode、牛客 等）的题目编号或链接，也不要出现“如 POJ 1321”“HDU 1241”这类外部引用；所有推荐都必须能在【本系统题库】内找到对应题目。\n"
			. "2. 推荐的练习方向必须是“系统可用的题目分类”中真实存在的分类，不要编造列表外的分类。\n"
			. "3. 对于每个被推荐的【分类】或【题目方向】，必须用 Markdown 链接格式输出，使其能够可点击跳转到系统题库搜索，链接格式固定为：\n"
			. "   [分类名](problemset.php?search=分类名)\n"
			. "   例如推荐“二分查找”时，必须写成：[二分查找](problemset.php?search=二分查找)；推荐“动态规划入门”时，写成：[动态规划入门](problemset.php?search=动态规划入门)。请务必使用这种可点击链接，不要只写纯文本分类名。\n"
			. "4. 使用简体中文，条理清晰，使用 Markdown 列表呈现。\n\n"
			. "【建议结构】\n"
			. "a. 先简要分析用户当前已涉及的知识领域与可能的薄弱环节；\n"
			. "b. 推荐 5-8 个适合他下一步练习的知识点/题目方向，每个方向给出推荐理由；\n"
			. "c. 不要输出与建议无关的内容。";

		$prompt_user = "本系统题库目前共有 " . $pc . " 道可做的题目，【系统可用的题目分类】列表如下（你只能从中选择推荐方向，不要编造列表外的分类，也不要引用外部 OJ）：\n"
			. $cat_text . "\n\n"
			. "该用户最近做过的 " . count($recent) . " 道题目如下（标题 + 分类）：\n" . $recent_text . "\n"
			. "请基于以上信息，为我推荐接下来适合练习的题目方向与学习路径，并按照上面要求的 Markdown 链接格式输出每个方向的分类，确保链接可点击触发题库搜索。";
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
	    ],
		"enable_thinking" => false
	];
	if(isset($temperature)) 
		$data["temperature"] = $temperature;   
	$sql="insert into openai_task_queue (user_id,task_type,solution_id,problem_id,request_body,status,create_date,update_date) values(?,?,?,?,?,0,now(),now())";
	if(!isset($sid)) $sid=0;
	if(!isset($pid)) $pid=0;  // alter table openai_task_queue add column problem_id bigint not null default 0 after solution_id;

	$check_sql="SELECT id FROM openai_task_queue WHERE user_id=? AND task_type=? AND solution_id=? AND problem_id=? AND status IN (0,1) AND update_date > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
	$check_result=pdo_query($check_sql,$_SESSION[$OJ_NAME.'_user_id'],basename($http_referer),$sid,$pid);
	if($check_result[0][0] > 0){
			$insert_id = $check_result[0][0];  // 重复的请求直接返回id
	}else{
			$insert_id = pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id'],basename($http_referer),$sid,$pid,json_encode($data));
	}
	echo $insert_id;
	trigger_judge($insert_id);
}
