<?php
	//oj-header.php
	$MSG_FAQ="常见问答";
	$MSG_BBS="讨论版";
	$MSG_HOME="主页";
	$MSG_PROBLEMS="问题";
	$MSG_STATUS="状态";
	$MSG_RANKLIST="排名";
	$MSG_CONTEST="竞赛&作业";
        $MSG_RECENT_CONTEST="名校联赛";
	$MSG_LOGOUT="注销";
	$MSG_LOGIN="登录";
	$MSG_LOST_PASSWORD="忘记密码";
	$MSG_REGISTER="注册";
	$MSG_ADMIN="管理";
	$MSG_SYSTEM="系统";
	$MSG_STANDING="名次";
	$MSG_STATISTICS="统计";
	$MSG_USERINFO="用户信息";
	$MSG_MAIL="短消息";
	$MSG_TODO="待完成的任务";

	//status.php
	$MSG_Pending="等待";
	$MSG_Pending_Rejudging="等待重判";
	$MSG_Compiling="编译中";
	$MSG_Running_Judging="运行并评判";
	$MSG_Accepted="正确";
	$MSG_Presentation_Error="格式错误";
	$MSG_Wrong_Answer="答案错误";
	$MSG_Time_Limit_Exceed="时间超限";
	$MSG_Memory_Limit_Exceed="内存超限";
	$MSG_Output_Limit_Exceed="输出超限";
	$MSG_Runtime_Error="运行错误";
	$MSG_Compile_Error="编译错误";
	$MSG_Runtime_Click="运行错误(点击看详细)";
	$MSG_Compile_Click="编译错误(点击看详细)";
	$MSG_Compile_OK="编译成功";
	$MSG_MANUAL_CONFIRMATION="自动评测通过，等待人工确认";
        $MSG_Click_Detail="点击看详细";
        $MSG_Manual="人工判题";
        $MSG_OK="确定";
        $MSG_Explain="输入判定原因与提示";
        $MSG_MARK="得分";
	$MSG_SUBMITTING="提交中";
	$MSG_REMOTE_PENDING="远程等待";
	$MSG_REMOTE_JUDGING="远程判题";	
	$MSG_RP="远程等待";

	//fool's day
	if(date('m')==4&&date('d')==1&&rand(0,100)<5){
  	        $MSG_Accepted="<span title=愚人节快乐>似乎好像是正确</span>";
		//$MSG_Presentation_Error="人品问题-愚人节快乐";
		//$MSG_Wrong_Answer="人品问题-愚人节快乐";
		//$MSG_Time_Limit_Exceed="人品问题-愚人节快乐";
		//$MSG_Memory_Limit_Exceed="人品问题-愚人节快乐";
		//$MSG_Output_Limit_Exceed="人品问题-愚人节快乐";
		//$MSG_Runtime_Error="人品问题-愚人节快乐";
		//$MSG_Compile_Error="人品问题-愚人节快乐";
		//$MSG_Compile_OK="人品问题-愚人节快乐";
	}
  
        $MSG_TEST_RUN="运行完成";

	$MSG_RUNID="提交编号";
	$MSG_USER="用户";
	$MSG_PROBLEM="问题";
	$MSG_RESULT="结果";
	$MSG_MEMORY="内存";
	//$MSG_TIME="耗时";  // overided by line 236
	$MSG_LANG="语言";
	$MSG_CODE_LENGTH="代码长度";
	$MSG_SUBMIT_TIME="提交时间";

	//problemstatistics.php
	$MSG_PD="等待";
	$MSG_PR="等待重判";
	$MSG_CI="编译中";
	$MSG_RJ="运行并评判";
	$MSG_AC="正确";
	$MSG_PE="格式错误";
	$MSG_WA="答案错误";
	$MSG_TLE="时间超限";
	$MSG_MLE="内存超限";
	$MSG_OLE="输出超限";
	$MSG_RE="运行错误";
	$MSG_CE="编译错误";
	$MSG_CO="编译成功";
	$MSG_TR="测试运行";
	$MSG_MC="待裁判确认";
	$MSG_RESET="重置";	
	
	//problemset.php
	$MSG_SEARCH="查找";
	$MSG_PROBLEM_ID="题目编号";
	$MSG_TITLE="标题";
	$MSG_SOURCE="来源/分类";
        $MSG_REMOTE_OJ="宿主OJ";
	$MSG_SUBMIT_NUM="提交量";
	$MSG_SUBMIT="提交";
	$MSG_SHOW_OFF="露一手!";
	
	//submit.php
	$MSG_VCODE_WRONG="验证码错误！";
	$MSG_LINK_ERROR="在哪里可以找到此链接？ 没有这个问题。";
	$MSG_PROBLEM_RESERVED="问题已禁用。";
	$MSG_NOT_IN_CONTEST="您不能立即提交，因为您没有被比赛邀请或比赛没有进行！";
	$MSG_NOT_INVITED="您不被邀请！";
	$MSG_NO_PROBLEM="没有这样的问题！";
	$MSG_NO_PLS="使用未知的编程语言！";
	$MSG_TOO_SHORT="代码太短！";
	$MSG_TOO_LONG="代码太长！";
	$MSG_BREAK_TIME="您不应在10秒钟内提交超过1次的申请.....";

	//ranklist.php
	$MSG_Number="名次";
	$MSG_NICK="昵称";
	$MSG_SOVLED="解决";
	$MSG_RATIO="比率";
	$MSG_DAY="日排行";
 	$MSG_WEEK="周排行";
	$MSG_MONTH="月排行";
	$MSG_YEAR="年排行";
        $MSG_ABSENT="缺席";
	//registerpage.php
	$MSG_USER_ID="用户名（学号）";
	$MSG_PASSWORD="密码";
	$MSG_REPEAT_PASSWORD="重复密码";
	$MSG_SCHOOL="学校";
        $MSG_GROUP_NAME="班级/小组";
	$MSG_EMAIL="电子邮件";
	$MSG_REG_INFO="设置注册信息";
	$MSG_VCODE="验证码";
        $MSG_ACTIVE_YOUR_ACCOUNT="激活账号";
        $MSG_CLICK_COPY="点击或者复制访问链接";
        $MSG_CHECK="检查";

	//problem.php
	$MSG_NO_SUCH_PROBLEM="题目当前不可用!<br>它可能被用于未来的比赛、过去的私有比赛，或者管理员由于尚未测试通过等其他原因暂时停止了该题目用于练习。";
	$MSG_Description="题目描述"  ;
	$MSG_Input="输入"  ;
	$MSG_Output= "输出" ;
	$MSG_Sample_Input= "样例输入" ;
	$MSG_Sample_Output= "样例输出" ;
	$MSG_Test_Input= "测试输入" ;
	$MSG_Test_Output= "测试输出" ;
	$MSG_NJ= "普通裁判" ;
	$MSG_SPJ= "特殊裁判" ;
	$MSG_RTJ= "文本裁判" ;
	$MSG_HINT= "提示" ;
	$MSG_Source= "来源" ;
	$MSG_Time_Limit="时间限制";
	$MSG_Memory_Limit="内存限制";
	$MSG_EDIT="编辑";
	$MSG_TESTDATA="测试数据";
	$MSG_CLICK_VIEW_HINT="点击查看剧透级题解";

	//admin menu
	$MSG_SEEOJ="查看前台";
	$MSG_ADD="添加";
	$MSG_MENU="菜单";
	$MSG_EXPLANATION="内容描述";
	$MSG_LIST="列表";
	$MSG_NEWS="公告";
	$MSG_CONTENTS="内容";
	$MSG_SAVE="保存";	
        $MSG_DELETED="已删除";	
        $MSG_NORMAL="正常";	
        $MSG_RESERVED="未启用";

	$MSG_TEAMGENERATOR="比赛队帐号生成器";
	$MSG_SETMESSAGE="设置公告";
	$MSG_SETPASSWORD="修改密码";
	$MSG_REJUDGE="重判题目";
	$MSG_PRIVILEGE="权限";
	$MSG_GIVESOURCE="转移源码";
	$MSG_IMPORT="导入";
	$MSG_EXPORT="导出";
	$MSG_UPDATE_DATABASE="更新数据库";
	$MSG_BACKUP_DATABASE="备份数据库";
	$MSG_ONLINE="在线";
	$MSG_SET_LOGIN_IP="指定登录IP";
	$MSG_PRIVILEGE_TYPE="权限 类型";
	$MSG_NEWS_MENU="是否展示到菜单";
        $MSG_LAST_LOGIN="最后登录";
	$MSG_OFFLINE_ZIP_IMPORT="导入zip文件，遵循下面的目录结构:";
	$MSG_OFFLINE="离线";

  //contest.php
  $MSG_PRIVATE_WARNING="比赛尚未开始或私有，不能查看题目。";
  $MSG_PRIVATE_USERS_ADD="*可以将学生学号从Excel整列复制过来，然后要求他们用学号做UserID注册,就能进入Private的比赛作为作业和测验。";
  $MSG_PLS_ADD="*请选择所有可以通过Ctrl +单击提交的语言。";
	$MSG_TIME_WARNING="比赛开始前。";
  $MSG_WATCH_RANK="点击这里查看做题排名。";
  $MSG_NOIP_WARNING=$OJ_NOIP_KEYWORD." 比赛进行中，结束后才能查看结果。";
  $MSG_NOIP_NOHINT=$OJ_NOIP_KEYWORD." 比赛,不显示提示信息。";
	$MSG_SERVER_TIME="服务器时间";
	$MSG_START_TIME="开始时间";
	$MSG_END_TIME="结束时间";
	$MSG_VIEW_ALL_CONTESTS="显示所有作业比赛";
	$MSG_CONTEST_ID="作业比赛编号";
	$MSG_CONTEST_NAME="作业比赛名称";
	$MSG_CONTEST_STATUS="作业比赛状态";
	$MSG_CONTEST_OPEN="开放";
	$MSG_CONTEST_CREATOR="创建人";
	$MSG_CONTEST_PENALTY="累计时间";
	$MSG_IP_VERIFICATION="IP验证";
        $MSG_LOG="日志";
        $MSG_SUSPECT="审计";
	$MSG_CONTEST_SUSPECT1="具有多个ID的IP地址。如果在竞赛/考试期间在同一台计算机上访问了多个ID，则会记录该ID。";
	$MSG_CONTEST_SUSPECT2="具有多个IP地址的ID。 如果在竞赛/考试期间切换到另一台计算机，它将记录下来。";
	$MSG_REVIEW_CONTESTRANK="补题榜"; // 比赛之后在练习中补做比赛原题 
	
	$MSG_SECONDS="秒";
	$MSG_MINUTES="分";
	$MSG_HOURS="小时";
	$MSG_DAYS="天";
	$MSG_MONTHS="月份";
	$MSG_YEARS="年份";

  $MSG_Public="公开";
  $MSG_Private="私有";
  $MSG_Running="运行中";
  $MSG_Start="开始于";
  $MSG_End="结束于";
  $MSG_TotalTime="总赛时";
  $MSG_LeftTime="剩余";
  $MSG_Ended="已结束";
  $MSG_Login="请登录后继续操作";
  $MSG_JUDGER="判题机";
  $MSG_DOWNLOAD="下载";
  $MSG_SHOW="显示";
  $MSG_HIDE="隐藏";
  
  $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM="考试期间，不能查阅以前提交的代码。";
  $MSG_BBS_NOT_ALLOWED_FOR_EXAM="考试期间,讨论版禁用。";
  $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM="考试期间,禁止修改帐号信息。";
  $MSG_MAIL_NOT_ALLOWED_FOR_EXAM="考试期间,内邮禁用。";
  $MSG_LOAD_TEMPLATE_CONFIRM="是否加载默认模板?\\n 如果选择是，当前代码将被覆盖!";
  $MSG_NO_MAIL_HERE="本OJ不支持内部邮件哦~";

  $MSG_BLOCKLY_OPEN="可视化";
  $MSG_BLOCKLY_TEST="翻译运行";
  $MSG_MY_SUBMISSIONS="我的提交"; 
  $MSG_MY_CONTESTS="我的$MSG_CONTEST"; 
  $MSG_Creator="命题人";
  $MSG_IMPORTED="外部导入";
  $MSG_PRINTER="打印";
  $MSG_PRINT_DONE="打印完成";
  $MSG_PRINT_PENDING="提交成功,待打印";
  $MSG_PRINT_WAITING="请耐心等候，不要重复提交相同的打印任务";
  $MSG_COLOR="颜色";
  $MSG_BALLOON="气球";
  $MSG_BALLOON_DONE="气球已发放";
  $MSG_BALLOON_PENDING="气球待发放";

  $MSG_DATE="日期";
  $MSG_TIME="时间";
  $MSG_SIGN="个性签名";
  $MSG_RECENT_PROBLEM="最近追题";
  $MSG_RECENT_CONTEST="近期比赛";
  $MSG_PASS_RATE="通过率";
  $MSG_SHOW_TAGS="显示分类标签";
  $MSG_SHOW_ALL_TAGS="所有标签";  

  $MSG_HELP_SEEOJ="跳转回到前台";
  $MSG_HELP_ADD_NEWS="添加首页显示的新闻";
  $MSG_HELP_NEWS_LIST="管理已经发布的新闻";
  $MSG_HELP_USER_LIST="对注册用户停用、启用帐号";
  $MSG_HELP_USER_ADD="添加用户";
  $MSG_HELP_USER_IMPORT="导入用户";
  $MSG_HELP_ADD_PROBLEM="手动添加新的题目，多组测试数据在添加后从题目列表TestData按钮进入上传，新建题目<b>默认隐藏</b>，需在问题列表中点击红色<font color='red'>$MSG_RESERVED</font>切换为绿色<font color='green'>Available</font>启用。。";
  $MSG_HELP_PROBLEM_LIST="管理已有的题目和数据，上传数据可以用zip压缩不含目录的数据。";
  $MSG_HELP_ADD_CONTEST="规划新的比赛，用逗号分隔题号。可以设定私有比赛，用密码或名单限制参与者。";
  $MSG_HELP_CONTEST_LIST="已有的比赛列表，修改时间和公开/私有，尽量不要在开赛后调整题目列表。";
	$MSG_HELP_SET_LOGIN_IP="记录考试期间的计算机(登录IP)更改。";
  $MSG_HELP_TEAMGENERATOR="批量生成大量比赛帐号、密码，用于来自不同学校的参赛者。小系统不要随便使用，可能产生垃圾帐号，无法删除。";
  $MSG_HELP_SETMESSAGE="设置滚动公告内容";
  $MSG_HELP_SETPASSWORD="重设指定用户的密码，对于管理员帐号需要先降级为普通用户才能修改。";
  $MSG_HELP_REJUDGE="重判指定的题目、提交或比赛。";
  $MSG_HELP_ADD_PRIVILEGE="给指定用户增加权限，包括管理员、题目添加者、比赛组织者、比赛参加者、代码查看者、手动判题、远程判题、打印员、气球发放员等权限。";
	$MSG_HELP_ADD_CONTEST_USER="给用户添加单个比赛权限。";
	$MSG_HELP_ADD_SOLUTION_VIEW="给用户添加单个题目的答案查看权限。";
  $MSG_HELP_PRIVILEGE_LIST="查看已有的特殊权限列表、进行删除操作。";
  $MSG_HELP_GIVESOURCE="将导入系统的标程赠与指定帐号，用于训练后辅助未通过的人学习参考。";
  $MSG_HELP_EXPORT_PROBLEM="将系统中的题目以fps.xml文件的形式导出。";
  $MSG_HELP_IMPORT_PROBLEM="导入从官方群共享或tk.hustoj.com下载到的fps.xml文件。";
  $MSG_HELP_UPDATE_DATABASE="更新数据库结构，在每次升级（sudo bash fixing.sh）之后或者导入老系统数据库备份，应至少操作一次。";
  $MSG_HELP_ONLINE="查看在线用户";
  $MSG_HELP_AC="答案正确，请再接再厉。"; 
  $MSG_HELP_PE="答案基本正确，但是格式不对。"; 
  $MSG_HELP_WA="答案不对，仅仅通过样例数据的测试并不一定是正确答案，一定还有你没想到的地方，点击查看系统可能提供的对比信息。"; 
  $MSG_HELP_TLE="运行超出时间限制，检查下是否有死循环，或者应该有更快的计算方法"; 
  $MSG_HELP_MLE="超出内存限制，数据可能需要压缩，检查内存是否有泄露"; 
  $MSG_HELP_OLE="输出超过限制，你的输出比正确答案长了两倍，一定是哪里弄错了"; 
  $MSG_HELP_RE="运行时错误，非法的内存访问，数组越界，指针漂移，调用禁用的系统函数。请点击后获得详细输出";
  $MSG_HELP_CE="编译错误，请点击后获得编译器的详细输出"; 
  
  $MSG_HELP_MORE_TESTDATA_LATER="更多组测试数据，请在题目添加完成后补充";
  $MSG_HELP_ADD_FAQS="管理员可以添加一条新闻，命名为\"faqs.$OJ_LANG\" 来取代<a href=../faqs.php>$MSG_FAQ</a>的内容。";
  $MSG_HELP_HUSTOJ="<sub><a target='_blank' href='https://github.com/zhblue/hustoj'><span class='glyphicon glyphicon-heart' aria-hidden='true'></span> 请到 HUSTOJ 来，给我们加个<span class='glyphicon glyphicon-star' aria-hidden='true'></span>Star!</a></sub>"; 
  $MSG_HELP_SPJ="特殊裁判的使用，请参考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜索hustoj special judge</a>"; 
  $MSG_HELP_BALLOON_SCHOOL="打印，气球帐号的School字段用于过滤任务列表，例如填[东校区]则只显示帐号为[东校区]开头的任务";
  $MSG_HELP_BACKUP_DATABASE="备份数据库,测试数据和图片到0题目录";
  $MSG_HELP_LEFT_EMPTY="无需修改密码，请勿填写此项";
  $MSG_HELP_LOCAL_EMPTY="本地题请留空";
  $MSG_WARNING_LOGIN_FROM_DIFF_IP="从不同的ip地址登录";
  $MSG_WARNING_DURING_EXAM_NOT_ALLOWED=" 在考试期间不被允许 ";
  $MSG_WARNING_ACCESS_DENIED="抱歉，您无法查看此消息！因为它不属于您，或者管理员设定系统状态为不显示此类信息。<br>如果你是管理员，请给自己增加source_browser权限，然后重新登录。<br>如果希望学生能看出错对比，编辑db_info.inc.php,设置 \$OJ_SHOW_DIFF=true;<br> 更多细节查看hustoj.com。";
  $MSG_WARNING_USER_ID_SHORT="用户名至少3位字符!";
  $MSG_WARNING_PASSWORD_SHORT="密码至少6位!";
  $MSG_WARNING_REPEAT_PASSWORD_DIFF="两次输入的密码不一致!";
 

  $MSG_LOSTPASSWORD_MAILBOX="请到您邮箱的垃圾邮件文件夹寻找验证码，并填写到这里";
  $MSG_LOSTPASSWORD_WILLBENEW="如果填写正确，通过下一步验证，这个验证码就自动成为您的新密码！";


  //discuss.php
  $MSG_LAST_REPLY="最新回复";
  $MSG_REPLY_COUNTS="回复总数";
  $MSG_REPLY_NUMBER="回复计数";
  $MSG_QUESTION="帖子";
  $MSG_NO_QUESTIONS="没有帖子";
  $MSG_REGISTER_QUESTION="发布帖子";
  $MSG_WRITE_QUESTION="发帖";
  $MSG_REGISTERED="已发布";
  $MSG_BLOCKED="已屏蔽";
  $MSG_REPLY="回复";
  $MSG_REGISTER_REPLY="发布回复";
  $MSG_DISABLE="禁用";
  $MSG_LOCK="锁定";
  $MSG_RESUME="恢复";
  $MSG_DISCUSS_DELETE="删除";
  $MSG_DISCUSS_NOTICE="提示";
  $MSG_DISCUSS_NOTE="笔记";
  $MSG_DISCUSS_NORMAL="普通";



  // template/../reinfo.php
  $MSG_A_NOT_ALLOWED_SYSTEM_CALL="使用了系统禁止的操作系统调用，看看是否越权访问了文件或进程等资源,如果你是系统管理员，而且确认提交的答案没有问题，测试数据没有问题，可以发送'RE'到微信公众号onlinejudge，查看解决方案。";
  $MSG_SEGMETATION_FAULT="段错误（a.k.a 数组开小了，亲！）<br> 检查是否有数组越界，指针异常，访问到不应该访问的内存区域，或者全局数组申请过大空间(每个long long占用8字节)。<br>如果您是管理员，正在使用标准答案测试系统，那么本报错可视同MLE内存超限。<br>请增加题目限制的内存空间，或增加judge.conf中的OJ_JAVA_MEMORY_BONUS";
  $MSG_FLOATING_POINT_EXCEPTION="浮点错误，检查是否有除以零的情况";
  $MSG_WRONG_OUTPUT_TYPE_EXCEPTION="二进制输出错误，检查是否误将数值类型作为字符输出，或者输出了不打印字符的情况。";
  $MSG_BUFFER_OVERFLOW_DETECTED="缓冲区溢出，检查是否有字符串长度超出数组的情况";
  $MSG_PROCESS_KILLED="进程因为内存或时间原因被杀死，检查是否有死循环";
  $MSG_ALARM_CLOCK="进程因为时间原因被杀死，检查是否有死循环，本错误等价于超时TLE";
  $MSG_CALLID_20="可能存在数组越界，检查题目描述的数据量与所申请数组大小关系";
  $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION="检查数组越界的情况";
  $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION="字符串的字符下标越界，检查subString,charAt等方法的参数";
  $MSG_WRONG_OUTPUT_TYPE_EXCEPTION="二进制输出错误，检查是否误将数值类型作为字符输出，或者输出了不打印字符的情况。";
  $MSG_NON_ZERO_RETURN="Main函数不能返回非零的值，否则视同程序出错。";
  $MSG_EXPECTED="期望值";
  $MSG_YOURS="你的程序输出";
  $MSG_FILENAME="文件名";
  $MSG_SIZE="大小";
  

  // template/../ceinfo.php
  $MSG_ERROR_EXPLAIN="辅助解释";
  $MSG_SYSTEM_OUT_PRINT="Java中System.out.print用法跟C语言printf不同，请试用System.out.format";
  $MSG_NO_SUCH_FILE_OR_DIRECTORY="服务器为Linux系统，不能使用windows下特有的非标准头文件。";
  $MSG_NOT_A_STATEMENT="检查大括号{}匹配情况，eclipse整理代码快捷键Ctrl+Shift+F";
  $MSG_EXPECTED_CLASS_INTERFACE_ENUM="请不要将java函数（方法）放置在类声明外部，注意大括号的结束位置}";
  $MSG_SUBMIT_JAVA_AS_C_LANG="请不要将java程序提交为C语言";
  $MSG_DOES_NOT_EXIST_PACKAGE="检测拼写，如：系统对象System为大写S开头";
  $MSG_POSSIBLE_LOSS_OF_PRECISION="赋值将会失去精度，检测数据类型，如确定无误可以使用强制类型转换";
  $MSG_INCOMPATIBLE_TYPES="Java中不同类型的数据不能互相赋值，整数不能用作布尔值";
  $MSG_ILLEGAL_START_OF_EXPRESSION="字符串应用英文双引号(\\\")引起";
  $MSG_CANNOT_FIND_SYMBOL="拼写错误或者缺少调用函数所需的对象如println()需对System.out调用";
  $MSG_EXPECTED_SEMICOLON="缺少分号。";
  $MSG_DECLARED_JAVA_FILE_NAMED="Java必须使用public class Main。";
  $MSG_EXPECTED_WILDCARD_CHARACTER_AT_END_OF_INPUT="代码没有结束，缺少匹配的括号或分号，检查复制时是否选中了全部代码。";
  $MSG_INVALID_CONVERSION="隐含的类型转换无效，尝试用显示的强制类型转换如(int *)malloc(....)";
  $MSG_NO_RETURN_TYPE_IN_MAIN="C++标准中，main函数必须有返回值";
  $MSG_NOT_DECLARED_IN_SCOPE="变量没有声明过，检查下是否拼写错误！";
  $MSG_MAIN_MUST_RETURN_INT="在标准C语言中，main函数返回值类型必须是int，教材和VC中使用void是非标准的用法";
  $MSG_PRINTF_NOT_DECLARED_IN_SCOPE="printf函数没有声明过就进行调用，检查下是否导入了stdio.h或cstdio头文件";
  $MSG_IGNOREING_RETURN_VALUE="警告：忽略了函数的返回值，可能是函数用错或者没有考虑到返回值异常的情况";
  $MSG_NOT_DECLARED_INT64="__int64没有声明，在标准C/C++中不支持微软VC中的__int64,请使用long long来声明64位变量";
  $MSG_EXPECTED_SEMICOLON_BEFORE="前一行缺少分号";
  $MSG_UNDECLARED_NAME="变量使用前必须先进行声明，也有可能是拼写错误，注意大小写区分。";
  $MSG_SCANF_NOT_DECLARED_IN_SCOPE="scanf函数没有声明过就进行调用，检查下是否导入了stdio.h或cstdio头文件";
  $MSG_MEMSET_NOT_DECLARED_IN_SCOPE="memset函数没有声明过就进行调用，检查下是否导入了stdlib.h或cstdlib头文件";
  $MSG_MALLOC_NOT_DECLARED_IN_SCOPE="malloc函数没有声明过就进行调用，检查下是否导入了stdlib.h或cstdlib头文件";
  $MSG_PUTS_NOT_DECLARED_IN_SCOPE="puts函数没有声明过就进行调用，检查下是否导入了stdio.h或cstdio头文件";
  $MSG_GETS_NOT_DECLARED_IN_SCOPE="gets函数没有声明过就进行调用，检查下是否导入了stdio.h或cstdio头文件";
  $MSG_STRING_NOT_DECLARED_IN_SCOPE="string类函数没有声明过就进行调用，检查下是否导入了string.h或cstring头文件";
  $MSG_NO_TYPE_IMPORT_IN_C_CPP="不要将Java语言程序提交为C/C++,提交前注意选择语言类型。";
  $MSG_ASM_UNDECLARED="不允许在C/C++中嵌入汇编语言代码。";
  $MSG_REDEFINITION_OF="函数或变量重复定义，看看是否多次粘贴代码。";
  $MSG_EXPECTED_DECLARATION_OR_STATEMENT="程序好像没写完，看看是否复制粘贴时漏掉代码。";
  $MSG_UNUSED_VARIABLE="警告：变量声明后没有使用，检查下是否拼写错误，误用了名称相似的变量。";
  $MSG_IMPLICIT_DECLARTION_OF_FUNCTION="函数隐性声明，检查下是否导入了正确的头文件。或者缺少了题目要求的指定名称的函数。";
  $MSG_ARGUMENTS_ERROR_IN_FUNCTION="函数调用时提供的参数数量不对，检查下是否用错参数。";
  $MSG_EXPECTED_BEFORE_NAMESPACE="不要将C++语言程序提交为C,提交前注意选择语言类型。";
  $MSG_STRAY_PROGRAM="中文空格、标点等不能出现在程序中注释和字符串以外的部分。编写程序时请关闭中文输入法。请不要使用网上复制来的代码。";
  $MSG_DIVISION_BY_ZERO="除以零将导致浮点溢出。";
  $MSG_CANNOT_BE_USED_AS_A_FUNCTION="变量不能当成函数用，检查变量名和函数名重复的情况，也可能是拼写错误。";
  $MSG_CANNOT_FIND_TYPE="scanf/printf的格式描述和后面的参数表不一致，检查是否多了或少了取址符“&”，也可能是拼写错误。";
  $MSG_JAVA_CLASS_ERROR="Java语言提交只能有一个public类，并且类名必须是Main，其他类请不要用public关键词";
  $MSG_EXPECTED_BRACKETS_TOKEN="缺少右括号";
  $MSG_NOT_FOUND_SYMBOL="使用了未定义的函数或变量，检出拼写是否有误，不要使用不存在的函数，Java调用方法通常需要给出对象名称如list1.add(...)。Java方法调用时对参数类型敏感，如:不能将整数(int)传送给接受字符串对象(String)的方法";
  $MSG_NEED_CLASS_INTERFACE_ENUM="缺少关键字，应当声明为class、interface 或 enum";
  $MSG_CLASS_SYMBOL_ERROR="使用教材上的例子，必须将相关类的代码一并提交，同时去掉其中的public关键词";
  $MSG_INVALID_METHOD_DECLARATION="只有跟类名相同的方法为构造方法，不写返回值类型。如果将类名修改为Main,请同时修改构造方法名称。";
  $MSG_EXPECTED_AMPERSAND_TOKEN="不要将C++语言程序提交为C,提交前注意选择语言类型。";
  $MSG_DECLARED_FUNCTION_ORDER="请注意函数、方法的声明前后顺序，不能在一个方法内出现另一个方法的声明。";
  $MSG_NEED_SEMICOLON="上面标注的这一行，最后缺少分号。";
  $MSG_EXTRA_TOKEN_AT_END_OF_INCLUDE="include语句必须独立一行，不能与后面的语句放在同一行";
  $MSG_INT_HAS_NEXT="hasNext() 应该改为nextInt()";
  $MSG_UNTERMINATED_COMMENT="注释没有结束，请检查“/*”对应的结束符“*/”是否正确";
  $MSG_EXPECTED_BRACES_TOKEN="函数声明缺少小括号()，如int main()写成了int main";
  $MSG_REACHED_END_OF_FILE_1="检查提交的源码是否没有复制完整，或者缺少了结束的大括号";
  $MSG_SUBSCRIPT_ERROR="不能对非数组或指针的变量进行下标访问";
  $MSG_EXPECTED_PERCENT_TOKEN="scanf的格式部分需要用双引号引起";
  $MSG_EXPECTED_EXPRESSION_TOKEN="参数或表达式没写完";
  $MSG_EXPECTED_BUT="错误的标点或符号";
  $MSG_REDEFINITION_MAIN="这道题目可能是附加代码题，请重新审题，看清题意，不要提交系统已经定义的main函数，而应提交指定格式的某个函数。";
  $MSG_IOSTREAM_ERROR="请不要将C++程序提交为C";
  $MSG_EXPECTED_UNQUALIFIED_ID_TOKEN="留意数组声明后是否少了分号";
  $MSG_REACHED_END_OF_FILE_2="程序末尾缺少大括号";
  $MSG_INVALID_SYMBOL="检查是否使用了中文标点或空格";
  $MSG_DECLARED_FILE_NAMED="OJ中public类只能是Main";
  $MSG_EXPECTED_IDENTIFIER="声明变量时，可能没有声明变量名或缺少括号。";
  $MSG_VARIABLY_MODIFIED="数组大小不能用变量，C 语言中不能使用变量作为全局数组的维度大小，包括 const 变量";
  $MSG_FUNCTION_GETS_REMOVIED=" std::gets 于 C++11 被弃用，并于 C++14 移除。可使用 std::fgets 替代。或者增加宏定义 #define gets(S) fgets(S,sizeof(S),stdin) ";
  $MSG_NON_ZERO_RETURN="Main函数不能返回非零的值，否则视同程序出错。";
  $MSG_PROBLEM_USED_IN="题目已经用于私有比赛";
  $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT="内邮仅限学生老师互相发送，不允许同学间发送！";
  $MSG_COPY_USER_LIST_FROM_CONTEST="选择一个比赛复制学生名单...";

  $MSG_REFRESH_PRIVILEGE="刷新权限";
  
  $MSG_SAVED_DATE="保存时间";
  $MSG_PROBLEM_STATUS="当前状态";

  $MSG_NEW_CONTEST="创建新比赛";
  $MSG_AVAILABLE="启用";
 
  $MSG_NEW_PROBLEM_LIST="创建新题单";
  $MSG_DELETE="删除";
  $MSG_EDIT="编辑";
  $MSG_TEST_DATA="管理测试数据";
  $MSG_CHECK_TO="批量选择操作";

  //bbcode.php
  $MSG_TOTAL="共";
  $MSG_NUMBER_OF_PROBLEMS="题";

  $MSG_GLOBAL="全局";
  $MSG_THIS_CONTEST="本次比赛";
  $MSG_SUBMIT_RECORD="提交记录";
  $MSG_RETURN_CONTEST="返回比赛";
  $MSG_COPY="复制";
  $MSG_SUCCESS="成功";
  $MSG_FAIL="失败";
  $MSG_TEXT_COMPARE="文本比较";
  $MSG_JUDGE_STYLE="评测方式";
  // reinfo.php 
  $MSG_ERROR_INFO="错误信息";
  $MSG_INFO_EXPLAINATION="辅助解释";
  // ceinfo.php
  $MSG_COMPILE_INFO="编译信息";
  $MSG_SOURCE_CODE="源代码";
  //contest.php
  $MSG_Contest_Pending="未开始";
  $MSG_Server_Time="当前时间";
  $MSG_Contest_Infomation="信息与公告";
  // sourcecompare.php
  $MSG_Source_Compare="源代码对比";
  $MSG_BACK="返回上一页";
  $MSG_NEXT_PAGE="下一页";
  $MSG_PREV_PAGE="上一页";
  
  //email
  $MSG_SYS_WARN="系统警告！";
  $MSG_IS_ROBOT="疑似机器人，注意封禁！";

  //SaaS friendly
  $MSG_TEMPLATE="模板";
  $MSG_FRIENDLY_LEVEL="友善级别";
  $MSG_FRIENDLY_B1="禁止注册，强制登录";
  $MSG_FRIENDLY_L0="开放注册，允许浏览";
  $MSG_FRIENDLY_L1="中国时区";
  $MSG_FRIENDLY_L2="强制中文";
  $MSG_FRIENDLY_L3="显示对比,关闭验证码";
  $MSG_FRIENDLY_L4="开启内邮,代码自动分享";
  $MSG_FRIENDLY_L5="开启测试运行";
  $MSG_FRIENDLY_L6="保持登陆状态";
  $MSG_FRIENDLY_L7="开启讨论版";
  $MSG_FRIENDLY_L8="可以下载测试数据";
  $MSG_FRIENDLY_L9="允许访客提交";

