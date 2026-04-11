<?php
if(basename($_SERVER['PHP_SELF'])!=="cron.php"){

	$http_referer =basename(parse_url( $_SERVER['HTTP_REFERER'])['path']);
	if((isset($_SESSION[$OJ_NAME.'_administrator'])|| isset($_SESSION[$OJ_NAME.'_problem_editor']) ) ){
		    $role="有20年Linux运维经验的网络工程师";
			$keyword="Linux运维";
			if( basename($http_referer)=="news_add_page.php"){
				$title=$_GET['title'];
				$prompt_sys=file_get_contents(dirname(__FILE__)."/news.md"); 
			if($title==""){
				$prompt_sys.="你可以想出一些有趣的标题。你言简意赅，只做非常简练的回答，不做任何解释。这个标题不会包含任何的markdown标记,长度不要超过30字符,可以随机的采用某一句诗词或者游戏的名字，或者上市公司的简称，用广为人知的梗替换诗句中的名词。";
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
		$problem=pdo_query("select concat(description,'输入:',input,'输出:',output,'样例输入:',sample_input,'样例输出:',sample_output,'提示:',hint) from problem where problem_id=?",$pid)[0][0];
		$prompt_user="题目是:".$problem ;
		}else if(basename($http_referer)=="problem_list.php"){
			if(isset($_GET['pid'])){
				$pid=intval($_GET['pid']);
				$prompt_sys="你是一位经验丰富的信奥教练，帮我给出这个题目的算法分类, 请用空格分割不同的分类名称，给出至少一个分类名，不要输出其他内容，例如:
	高精度 动态规划 背包问题 数论 几何 贪心";
				$problem=pdo_query("select concat(description,'输入:',input,'输出:',output,'样例输入:',sample_input,'样例输出:',sample_output,'提示:',hint) from problem where problem_id=?",$pid)[0][0];
				$prompt_user="题目是:".$problem."\n , 请帮我写个极简分类，不要解释，只要分类，数量不超过4个";
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
	if( basename($http_referer)=="reinfo.php" ||  basename($http_referer)=="ceinfo.php"){
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
		$solution=pdo_query("select user_id,problem_id from solution where solution_id=?",$sid)[0];
		$user_id=$solution[0];
		$problem_id=$solution[1];

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
		$problem=pdo_query("select concat('<br>\n## $MSG_Description <br>\n\n',description,'<br>\n\n## $MSG_Input<br>\n\n',input,'<br>\n\n## $MSG_Output <br>\n\n',output,'<br>\n\n## $MSG_Sample_Input <br>\n',sample_input,'<br>\n\n## $MSG_Sample_Output <br>\n',sample_output,'<br>\n##  $MSG_HINT <br>\n',hint) from problem where problem_id=?",$problem_id)[0][0];
		$prompt_user="$MSG_AI_PROMPT_USER_TITLE<br>\n".$problem."<br>\n$MSG_AI_PROMPT_USER_SOURCE\n<pre>\n".htmlentities($source)."\n</pre>\n$MSG_AI_PROMPT_USER_ERROR\n<pre>\n".htmlentities($ceinfo)."\n</pre>";

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
