<?php
	//header.php
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
	$MSG_COOKIE_ERROR="Cookie失效或错误";
	$MSG_LOST_PASSWORD="忘记密码";
	$MSG_REGISTER="注册";
	$MSG_ADMIN="管理";
	$MSG_SYSTEM="系统";
	$MSG_SERVICE_DISABLED="接口未启用";
	$MSG_SETTINGS="系统设置";
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
	$MSG_LAST_MONTH="上月排行";
	$MSG_MONTH="本月排行";
	$MSG_YEAR="年排行";
        $MSG_ABSENT="缺席";

	//registerpage.php
	$MSG_USER_ID="用户名（学号）";
	$MSG_PASSWORD="密码";
	$MSG_REPEAT_PASSWORD="重复密码";
	$MSG_SCHOOL="学校";
        $MSG_GROUP_NAME="班级/小组";
        $MSG_PARENT_PHONE="家长手机";
	$MSG_EMAIL="电子邮件";
	$MSG_CANNOT_EMPTY="不能为空";
	$MSG_WRONG_FORMAT="格式错误";
	$MSG_REG_INFO="设置注册信息";
	$MSG_VCODE="验证码";
        $MSG_ACTIVE_YOUR_ACCOUNT="激活账号";
        $MSG_CLICK_COPY="点击或者复制访问链接";
        $MSG_CHECK="检查";
        $MSG_OLD="旧";
        $MSG_DIFFERENT="不一致";
        $MSG_WRONG="错误";
        $MSG_TOO_LONG="太长";
        $MSG_TOO_SHORT="太短";
	$MSG_TOO_SIMPLE="太简单";
        $MSG_TOO_BAD="请文明上网";

	//problem.php
	$MSG_NO_SUCH_PROBLEM="题目当前不可用!<br>它可能被用于未来的比赛、过去的私有比赛，或者管理员由于尚未测试通过等其他原因暂时停止了该题目用于练习。";
	$MSG_Description="题目描述"  ;
	$MSG_Input="输入"  ;
	$MSG_Output= "输出" ;
	$MSG_Sample_Input= "样例输入" ;
	$MSG_Sample_Output= "样例输出" ;
	$MSG_Sample_Zip= "大样例" ;
	$MSG_Test_Input= "测试输入" ;
	$MSG_Test_Output= "测试输出" ;
	$MSG_NJ= "普通裁判" ;
	$MSG_SPJ= "特殊裁判(spj.cc/upj.cc/tpj.cc)" ;
	$MSG_RTJ= "文本裁判" ;
	$MSG_INTERACT= "交互裁判(interactor.cc)" ;
	$MSG_HINT= "提示" ;
	$MSG_Source= "来源" ;
	$MSG_Time_Limit="时间限制";
	$MSG_Memory_Limit="内存限制";
	$MSG_EDIT="编辑";
	$MSG_TESTDATA="测试数据";
	$MSG_CLICK_VIEW_HINT="点击查看剧透级题解";

	//admin menu
    $MSG_VIEW_DISABLED_USER="查看已禁用或待审核用户";
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
        $MSG_EXPIRY_DATE="有效期";
        $MSG_CLICK_TO_DELETE="点击删除";
        $MSG_CLICK_TO_RECOVER="点击恢复";

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
	$MSG_VIEW_ALL_CONTESTS="所有作业比赛";
	$MSG_VIEW_MY_CONTESTS="我的作业比赛";
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
  $MSG_REGISTERABLE="可注册";
  $MSG_CONTEST_REGISTERED="已注册";
  $MSG_REGISTERABLE_ONLY="该比赛不支持自主注册。";
  $MSG_REGISTER_CLOSED="比赛开始后停止注册。";
  $MSG_CONTEST_ID_INVALID="比赛编号无效。";
  $MSG_CONTEST_NOT_FOUND="比赛不存在。";
  $MSG_REGISTER_REQUIRED="需注册";
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
  $MSG_TABLE_TRANSPOSE="行列转换";

  $MSG_HELP_SETTINGS="在网页上修改系统配置参数（布尔开关、文本、数字），敏感项受保护";
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
  $MSG_ROLLING_RANK="滚榜";
  $MSG_SIMILARITY="相似度";
  $MSG_NO_SUBMISSIONS="暂无选手提交";
  $MSG_NO_CONTACTS="暂无联系人";
  $MSG_SELECT_CONTACT="请选择一个人聊天";
	$MSG_IP_EMAIL_REG_OVERLIMIT="您的IP地址或Email已经注册过{$OJ_REG_SPEED}个账户，请稍后再试。";

  $MSG_CONTEST_CLOSED="比赛已经关闭";
  $MSG_FILE_SUBMIT="文件提交";
  $MSG_NO_FREOPEN="无需freopen";
  $MSG_OI_CONTEST_VIEWING="OI比赛进行中，暂时无法查看";
  $MSG_ADMIN_NO_EMAIL_CONFIG="管理员没有配置邮件发送账户，请联系系统管理员或者老师来手工重置密码。";
  $MSG_PLEASE_CONFIG_EMAIL="请配置db_info.inc.php中的 \$SMTP_USER 等参数，激活邮件密码找回功能。";
  $MSG_STARRED_OF_MONTH="本月之星";
  $MSG_CORRECT_SUBMISSIONS="当前系统正确提交数量";
  $MSG_TOP_OF_PAGE="顶页";
  
  //email
  $MSG_SYS_WARN="系统警告！";
  $MSG_IS_ROBOT="疑似机器人，注意封禁！";
  $MSG_FORBIDDEN="禁止";
  $MSG_OTHERS="查看其他人";
  $MSG_SUBNET="子网";
  $MSG_ONLY_LAST_SUBMISSION="仅以最后一次提交记分";
  $MSG_AI_HELP="人工智能(AI)错误解析";
  $MSG_SHOW_DIFF="显示对比输出";
  $MSG_UPSOLVING="补题";
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

  //aiapi/common.php
  $MSG_AI_CODE_SUGGESTION_SOURCE_BROWSER="分析我可能薄弱的知识点，问我一个提示性的相关问题。";
  $MSG_AI_CODE_SUGGESTION="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析我可能薄弱的知识点，问我一个提示性的相关问题，最后说一句鼓励或安慰的话，卖个萌。";
  $MSG_AI_PROMPT_SYS="你是一个经验丰富的信息学奥赛编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。%s 请尽量言简意赅，节省token消耗。";
  $MSG_AI_INVALID_PARAM="非法参数";
  $MSG_AI_PROMPT_USER_TITLE="题目是:";
  $MSG_AI_PROMPT_USER_SOURCE="源代码是:";
  $MSG_AI_PROMPT_USER_ERROR="报错信息:";
  $MSG_COIN="积分";  //可以改成硬币或者🪙
  $MSG_EARNED="获得";
  $MSG_BONUS="奖励";
  $MSG_SPENT="消耗";
  $MSG_COIN_EARNED="做题获得$MSG_COIN";
  $MSG_COIN_BONUS="老师奖励$MSG_COIN";
  $MSG_COIN_SPENT="已消耗$MSG_COIN";
  $MSG_COIN_BALANCE="当前{$MSG_COIN}余额";
  $MSG_NO_COIN="没有足够的$MSG_COIN";
  $MSG_PAID="已支付$MSG_COIN";
  $MSG_PAY="支付";
  $MSG_USE="使用";
  $MSG_NUM="序号";

// admin/problem_list.php 批量删除
$MSG_BATCH_DELETE_EMPTY="请先选择要删除的题目";
$MSG_BATCH_DELETE_CONFIRM="确定删除选中的 {count} 道题目？";
$MSG_BATCH_DELETE_IDS="题目编号：";
$MSG_BATCH_DELETE_WARNING="题面和测试数据将被永久删除。";
$MSG_BATCH_DELETE_FORBIDDEN="仅管理员可以批量删除题目";
$MSG_BATCH_DELETE_METHOD="请求方法不允许";
$MSG_BATCH_DELETE_RESULT="已删除 {count} 道题目。";
$MSG_BATCH_DELETE_BACK="返回题目列表";
<?php
	//header.php
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
	$MSG_COOKIE_ERROR="Cookie失效或错误";
	$MSG_LOST_PASSWORD="忘记密码";
	$MSG_REGISTER="注册";
	$MSG_ADMIN="管理";
	$MSG_SYSTEM="系统";
	$MSG_SERVICE_DISABLED="接口未启用";
	$MSG_SETTINGS="系统设置";
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
	$MSG_LAST_MONTH="上月排行";
	$MSG_MONTH="本月排行";
	$MSG_YEAR="年排行";
        $MSG_ABSENT="缺席";

	//registerpage.php
	$MSG_USER_ID="用户名（学号）";
	$MSG_PASSWORD="密码";
	$MSG_REPEAT_PASSWORD="重复密码";
	$MSG_SCHOOL="学校";
        $MSG_GROUP_NAME="班级/小组";
        $MSG_PARENT_PHONE="家长手机";
	$MSG_EMAIL="电子邮件";
	$MSG_CANNOT_EMPTY="不能为空";
	$MSG_WRONG_FORMAT="格式错误";
	$MSG_REG_INFO="设置注册信息";
	$MSG_VCODE="验证码";
        $MSG_ACTIVE_YOUR_ACCOUNT="激活账号";
        $MSG_CLICK_COPY="点击或者复制访问链接";
        $MSG_CHECK="检查";
        $MSG_OLD="旧";
        $MSG_DIFFERENT="不一致";
        $MSG_WRONG="错误";
        $MSG_TOO_LONG="太长";
        $MSG_TOO_SHORT="太短";
	$MSG_TOO_SIMPLE="太简单";
        $MSG_TOO_BAD="请文明上网";

	//problem.php
	$MSG_NO_SUCH_PROBLEM="题目当前不可用!<br>它可能被用于未来的比赛、过去的私有比赛，或者管理员由于尚未测试通过等其他原因暂时停止了该题目用于练习。";
	$MSG_Description="题目描述"  ;
	$MSG_Input="输入"  ;
	$MSG_Output= "输出" ;
	$MSG_Sample_Input= "样例输入" ;
	$MSG_Sample_Output= "样例输出" ;
	$MSG_Sample_Zip= "大样例" ;
	$MSG_Test_Input= "测试输入" ;
	$MSG_Test_Output= "测试输出" ;
	$MSG_NJ= "普通裁判" ;
	$MSG_SPJ= "特殊裁判(spj.cc/upj.cc/tpj.cc)" ;
	$MSG_RTJ= "文本裁判" ;
	$MSG_INTERACT= "交互裁判(interactor.cc)" ;
	$MSG_HINT= "提示" ;
	$MSG_Source= "来源" ;
	$MSG_Time_Limit="时间限制";
	$MSG_Memory_Limit="内存限制";
	$MSG_EDIT="编辑";
	$MSG_TESTDATA="测试数据";
	$MSG_CLICK_VIEW_HINT="点击查看剧透级题解";

	//admin menu
    $MSG_VIEW_DISABLED_USER="查看已禁用或待审核用户";
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
        $MSG_EXPIRY_DATE="有效期";
        $MSG_CLICK_TO_DELETE="点击删除";
        $MSG_CLICK_TO_RECOVER="点击恢复";

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
	$MSG_VIEW_ALL_CONTESTS="所有作业比赛";
	$MSG_VIEW_MY_CONTESTS="我的作业比赛";
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
  $MSG_REGISTERABLE="可注册";
  $MSG_CONTEST_REGISTERED="已注册";
  $MSG_REGISTERABLE_ONLY="该比赛不支持自主注册。";
  $MSG_REGISTER_CLOSED="比赛开始后停止注册。";
  $MSG_CONTEST_ID_INVALID="比赛编号无效。";
  $MSG_CONTEST_NOT_FOUND="比赛不存在。";
  $MSG_REGISTER_REQUIRED="需注册";
  $MSG_REGISTER_COUNT="已报名人数";
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
  $MSG_TABLE_TRANSPOSE="行列转换";

  $MSG_HELP_SETTINGS="在网页上修改系统配置参数（布尔开关、文本、数字），敏感项受保护";
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
  $MSG_ROLLING_RANK="滚榜";
  $MSG_SIMILARITY="相似度";
  $MSG_NO_SUBMISSIONS="暂无选手提交";
  $MSG_NO_CONTACTS="暂无联系人";
  $MSG_SELECT_CONTACT="请选择一个人聊天";
	$MSG_IP_EMAIL_REG_OVERLIMIT="您的IP地址或Email已经注册过{$OJ_REG_SPEED}个账户，请稍后再试。";

  $MSG_CONTEST_CLOSED="比赛已经关闭";
  $MSG_FILE_SUBMIT="文件提交";
  $MSG_NO_FREOPEN="无需freopen";
  $MSG_OI_CONTEST_VIEWING="OI比赛进行中，暂时无法查看";
  $MSG_ADMIN_NO_EMAIL_CONFIG="管理员没有配置邮件发送账户，请联系系统管理员或者老师来手工重置密码。";
  $MSG_PLEASE_CONFIG_EMAIL="请配置db_info.inc.php中的 \$SMTP_USER 等参数，激活邮件密码找回功能。";
  $MSG_STARRED_OF_MONTH="本月之星";
  $MSG_CORRECT_SUBMISSIONS="当前系统正确提交数量";
  $MSG_TOP_OF_PAGE="顶页";
  
  //email
  $MSG_SYS_WARN="系统警告！";
  $MSG_IS_ROBOT="疑似机器人，注意封禁！";
  $MSG_FORBIDDEN="禁止";
  $MSG_OTHERS="查看其他人";
  $MSG_SUBNET="子网";
  $MSG_ONLY_LAST_SUBMISSION="仅以最后一次提交记分";
  $MSG_AI_HELP="人工智能(AI)错误解析";
  $MSG_SHOW_DIFF="显示对比输出";
  $MSG_UPSOLVING="补题";
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

  //aiapi/common.php
  $MSG_AI_CODE_SUGGESTION_SOURCE_BROWSER="分析我可能薄弱的知识点，问我一个提示性的相关问题。";
  $MSG_AI_CODE_SUGGESTION="不要直接给出完整代码,只给出问题原因,让我自己学习修改。分析我可能薄弱的知识点，问我一个提示性的相关问题，最后说一句鼓励或安慰的话，卖个萌。";
  $MSG_AI_PROMPT_SYS="你是一个经验丰富的信息学奥赛编程高手，能帮我用简单清晰的中文，解释我看不懂的报错信息。如果对比中用户的输出为空，可能是没有考虑到多组输入的情况，应该使用循环处理。%s 请尽量言简意赅，节省token消耗。";
  $MSG_AI_INVALID_PARAM="非法参数";
  $MSG_AI_PROMPT_USER_TITLE="题目是:";
  $MSG_AI_PROMPT_USER_SOURCE="源代码是:";
  $MSG_AI_PROMPT_USER_ERROR="报错信息:";
  $MSG_COIN="积分";  //可以改成硬币或者🪙
  $MSG_EARNED="获得";
  $MSG_BONUS="奖励";
  $MSG_SPENT="消耗";
  $MSG_COIN_EARNED="做题获得$MSG_COIN";
  $MSG_COIN_BONUS="老师奖励$MSG_COIN";
  $MSG_COIN_SPENT="已消耗$MSG_COIN";
  $MSG_COIN_BALANCE="当前{$MSG_COIN}余额";
  $MSG_NO_COIN="没有足够的$MSG_COIN";
  $MSG_PAID="已支付$MSG_COIN";
  $MSG_PAY="支付";
  $MSG_USE="使用";
  $MSG_NUM="序号";

// admin/problem_list.php 批量删除
$MSG_BATCH_DELETE_EMPTY="请先选择要删除的题目";
$MSG_BATCH_DELETE_CONFIRM="确定删除选中的 {count} 道题目？";
$MSG_BATCH_DELETE_IDS="题目编号：";
$MSG_BATCH_DELETE_WARNING="题面和测试数据将被永久删除。";
$MSG_BATCH_DELETE_FORBIDDEN="仅管理员可以批量删除题目";
$MSG_BATCH_DELETE_METHOD="请求方法不允许";
$MSG_BATCH_DELETE_RESULT="已删除 {count} 道题目。";
$MSG_BATCH_DELETE_BACK="返回题目列表";


// ======================================================================
// 编译报错解释文案 —— 配合 template/*/ceinfo.php 的正则使用
// 2026-10 依据 ai_kb 真实语料(22067 条学生报错)覆盖率报告补齐
// ======================================================================
$MSG_ABS_NOT_DECLARED="abs 没有声明。数学函数要 #include <cmath>（或 <bits/stdc++.h>），且用 double 接收结果。";
$MSG_ARRAY_BOUND_NOT_CONSTANT="数组长度必须是编译期常量。可以先写 const int N=100; 再用 N 作为长度。";
$MSG_ARRAY_BRACE_INIT="数组初始化必须用大括号，例如 int a[3]={1,2,3};";
$MSG_ARRAY_SIZE_NOT_CONST="数组长度必须是编译期常量，不能用运行时的变量。最简单的办法是把数组开得足够大。";
$MSG_ASSIGN_USED_AS_TRUTH="if 条件里写的是赋值 = 而不是比较 ==，这样条件恒为真，逻辑一定错了。";
$MSG_AUTO_CHANGES_MEANING="auto 的含义在 C++11 发生了变化，本题可能要求 C++98，请改成明确的类型。";
$MSG_CALLED_DIRECTLY="不能直接这样调用，需要先创建对象再通过对象调用。";
$MSG_CANNOT_CONVERT="赋值号两边的类型不一致。常见原因：把数字直接赋给 string 或 char 变量。";
$MSG_CANNOT_CONVERT_IN_ASSIGNMENT="赋值时类型对不上。检查等号左右两边分别是什么类型。";
$MSG_CANNOT_RESOLVE_OVERLOADED="有多个同名函数，编译器不知道该用哪一个。把参数的类型写明确。";
$MSG_CANNOT_USE_AS_FUNCTION="变量不能当函数来调用。检查是不是把变量名和函数名写混了，或者少了括号。";
$MSG_CASE_NOT_INTEGER="case 后面必须是一个整数常量，不能用变量或表达式。";
$MSG_CHINESE_CANNOT_CONVERT="两种类型之间不能直接转换，检查赋值号两边的类型是否一致。";
$MSG_CHINESE_EXPECTED="编译器提示这里缺东西，按提示补上对应的符号（最常见是漏分号）。";
$MSG_CHINESE_SHOULD_HAVE_BEFORE="这里本来应该有内容，检查是否少写了变量名或符号。";
$MSG_CHINESE_UNDEFINED_REFERENCE="引用了一个没有定义的东西，检查变量或函数是否声明、实现。";
$MSG_CIN_NOT_DECLARED="cin 没有声明。检查是否写了 #include <iostream> 和 using namespace std;。";
$MSG_CLOCKS_PER_SEC_NOT_DECLARED="CLOCKS_PER_SEC 没有声明。计时函数需要 #include <ctime> 或 <bits/stdc++.h>。";
$MSG_CLOCK_NOT_DECLARED="clock 没有声明。计时函数需要 #include <ctime> 或 <bits/stdc++.h>。";
$MSG_COLLECT2_LD_ERROR="链接失败，通常是上一条 undefined reference 引起的，回头看链接器的具体报错。";
$MSG_CONFLICTING_DECLARATION="同一个名字被声明成了不同的类型，检查前后两次声明是否一致。";
$MSG_CONSTANT_EXPR_CONVERSION="case 后面必须跟整数常量，不能写字符串或变量，也不能写需要运行时才能算出的值。";
$MSG_CONSTEXPR_NOT_DECLARED="constexpr 是 C++11 才有的写法，本题可能要求 C++98，请换成普通常量。";
$MSG_CONTROL_REACHES_END="非 void 函数可能没有返回值。补上 return，或者检查是不是某个分支漏写了。";
$MSG_CONTROL_REACHES_END_GENERIC="非 void 函数在某条路径上没有返回值，补上 return 语句。";
$MSG_CONVERSION_TO_NON_SCALAR="不能把字符串或数组整体转成数字类型，先取出单个元素再比较。";
$MSG_CORRUPTED_DOUBLE_LINKED_LIST="内存管理出错，通常是数组越界写坏了内存。检查下标范围。";
$MSG_COULD_NOT_CONVERT="类型转换失败。常见于把下标结果、数组或字符串直接赋给数字变量。";
$MSG_COUT_NOT_DECLARED="cout 没有声明。检查是否写了 #include <iostream> 和 using namespace std;。";
$MSG_CROSSES_INITIALIZATION="跳转语句跨越了带初始化的变量声明，把变量先声明在跳转之前。";
$MSG_DOES_NOT_NAME_A_TYPE="编译器不认识这个类型名。检查是否漏写 int/float/double，也检查 #include 是否齐全。";
$MSG_DOUBLE_FREE_OR_CORRUPTION="内存被重复释放或踩越界了。检查 new/delete 是否配对，以及数组下标是否越界。";
$MSG_EMPTY_CHAR_CONSTANT="两个引号中间少了字符，写成 '0' 这样的形式。";
$MSG_ENDL_NOT_DECLARED="endl 没有声明。和 cout 一样，需要 #include <iostream> 和 using namespace std;。";
$MSG_EXPECTED_AT_EOF="代码没有写完，通常是文件末尾少了一个大括号 }。";
$MSG_EXPECTED_CLASS_NAME="继承或模板参数的位置应该写类名，不是关键字。";
$MSG_EXPECTED_CLOSE_PAREN="括号没有闭合，少了一个右括号 )。";
$MSG_EXPECTED_COMMA_OR_ELLIPSIS="模板参数之间要用逗号分隔，不要写成空格。";
$MSG_EXPECTED_COMMA_OR_SEMICOLON="多个变量之间用逗号分隔，一句话写完要加分号。检查 int a=1<<b=2; 这种写法。";
$MSG_EXPECTED_CONSTRUCTOR_DESTRUCTOR="这个位置应该是一个对象名，检查类型名和变量名之间是不是漏了空格或分号。";
$MSG_EXPECTED_DECLARATION_BEFORE="关键字用错了位置（比如把 if 写在了函数体外面），或者上一行没有写完。";
$MSG_EXPECTED_GENERIC="这个位置缺了一个符号。按编译器提示补上：最常见的是漏分号、漏右括号、或者漏写了变量名。";
$MSG_EXPECTED_IDENTIFIER_BEFORE="这里应该是一个变量名。检查是不是把类型名写了两次，或者符号用错了。";
$MSG_EXPECTED_INITIALIZER="定义变量时忘了写类型，或者在类型和变量名之间漏了东西。正确写法：int n=0;";
$MSG_EXPECTED_PRIMARY_EXPRESSION="编译器在这个位置需要一个具体的值（变量、数字或函数调用），检查运算符左边是不是漏写了东西。";
$MSG_EXPECTED_SEMICOLON_AFTER_STRUCT="struct 或 class 定义结束后必须补一个分号。";
$MSG_EXPECTED_UNQUALIFIED_ID_AT_EOF="代码没有写完，文件末尾少了一个大括号 }。";
$MSG_EXPECTED_UNQUALIFIED_ID_BEFORE="这里需要一个名字（变量名或类型名），你写的是关键字或运算符。常见于把 a==b 写成了 a=b。";
$MSG_EXTENDED_INITIALIZER_LISTS="扩展的初始化列表是 C++11 特性，本题可能要求 C++98，请用普通写法初始化。";
$MSG_FILE_NOT_DECLARED="FILE 类型没有声明。文件操作需要 #include <cstdio> 或 <stdio.h>。";
$MSG_FOPEN_NOT_DECLARED="fopen 没有声明。文件操作需要 #include <cstdio> 或 <stdio.h>。";
$MSG_FORBIDS_BRACED_GROUP="C++ 不允许把 { } 当成一个可以取值的表达式来用。";
$MSG_FORBIDS_DECLARATION_NO_TYPE="C++ 不允许省略类型，变量名前必须写 int / float / double / char。";
$MSG_FUNCTION_DEF_NOT_ALLOWED="函数不能定义在另一个函数里面，把它挪到 main 函数外面。";
$MSG_HAS_NOT_BEEN_DECLARED="这个名字编译器没见过。检查是否漏写声明，或者漏了 #include 头文件。";
$MSG_HAS_NO_MEMBER_NAMED="这个对象里没有这个成员。最常见是把 int 当成 vector 或 string 调用了 .size()。";
$MSG_INCLUDE_EXPECTS="#include 后面要写 〈文件名〉 或 <文件名>，检查写法是否完整。";
$MSG_INCOMPLETE_TYPE="类型不完整：一般是你先声明、后定义，顺序反了；或者漏写了 struct 定义。";
$MSG_INITIALIZER_STRING_TOO_LONG="字符数组开得太小，字符串结尾还有一个 \0 占位置，数组要再多留 1。";
$MSG_INVALID_CONVERSION_TYPES="不能直接把这种类型转成那种类型，需要先经过一个中间类型。";
$MSG_INVALID_NEXT_SIZE="堆内存被写坏了，通常是数组越界。检查下标范围和循环次数。";
$MSG_INVALID_OPERANDS="运算符两边的类型不对。注意 char 和数字一起运算时结果仍是 char，先转成 int。";
$MSG_INVALID_OPERANDS_TYPES="运算符两边放的类型它不接受。检查：两个普通 int 不能连乘成 int*，char+int 要先转 int。";
$MSG_INVALID_OPERAND_BINARY="这个二元运算符要求特定的操作数类型，检查运算对象的类型。";
$MSG_INVALID_TYPES="这里的数据类型不对，重点检查数组下标和指针的用法。";
$MSG_INVALID_TYPE_ARG_UNARY="这个运算符要求右边的操作数是指定类型，检查你的运算对象。";
$MSG_INVALID_USE_MEMBER_FUNCTION="成员函数不能用这种方式调用，检查是否缺少对象名或者调用格式不对。";
$MSG_INVALID_USE_OF_VOID="void 类型的函数没有返回值，不能把它的调用结果参与运算。";
$MSG_JUMP_TO_CASE_LABEL="case 里出现了一个带初始化的新变量，switch 会跳过它的初始化。把变量声明提到 switch 外面。";
$MSG_LAMBDA_EXPRESSIONS="Lambda 表达式是 C++11 才支持的写法，本题可能要求 C++98，请换成普通函数。";
$MSG_LD_RETURNED_EXIT_STATUS="链接失败，通常是上一条 undefined reference 引起的，回头看链接器的具体报错。";
$MSG_LVALUE_REQUIRED="赋值号左边必须是一个能修改的变量，不能是数组名、常量或者函数的返回值。";
$MSG_MAIN_MUST_RETURN_INT2="main 函数的返回值类型必须是 int，结尾写 return 0;";
$MSG_MISLEADING_INDENT="缩进让 else 看起来像属于上面的 if，给 if/else 都加上 {} 可以避免这个问题。";
$MSG_MISSING_TEMPLATE_ARGUMENTS="模板参数写漏了，例如 vector<int> 少了尖括号。";
$MSG_MISSING_TERMINATING_APOSTROPHE="单引号没有闭合。单个字符写成 'a'，字符串才用双引号 abc。";
$MSG_MISSING_TERMINATING_GT="尖括号没有闭合，通常是 #include <xxx> 漏写了结尾的 >。";
$MSG_MISSING_TERMINATING_QUOTE="双引号没有闭合，或者单个字符误用了双引号。单个字符要用单引号，例如 'a'。";
$MSG_MULTIPLE_DEFINITION="同一个函数或变量被定义了多次，删掉多余的定义。";
$MSG_NON_STATIC_INITIALIZERS="非静态成员初始化是 C++11 特性，本题可能要求 C++98，请在构造函数里赋值。";
$MSG_NOT_A_MEMBER_OF="这个类里没有你说的成员，检查拼写和大小写；成员函数要用“对象名.成员名”调用。";
$MSG_NOT_A_MEMBER_OF_STD="std 命名空间里没有这个名字，检查拼写，或者该头文件没有 include。";
$MSG_NOT_DECLARED_IN_THIS_SCOPE="变量或函数没有声明就使用了。先声明再使用，同时检查大小写和拼写错误。";
$MSG_NOT_MEMBER_OF_STD="标准库里没有这个名字，检查拼写；也可能是 C++98 环境不支持这个 C++11 写法。";
$MSG_NO_MATCHING_FUNCTION_CALL="调用的函数不存在，或者参数对不上。检查参数的个数、类型和顺序。";
$MSG_NO_MATCH_FOR_OPERATOR="没有找到能处理这两个类型的运算符。如果是自己定义的类型，需要写运算符重载。";
$MSG_NO_SUCH_FILE_EN="找不到头文件。竞赛建议统一写 #include <bits/stdc++.h>，并确认提交语言是 C++ 而不是 C。";
$MSG_NO_SUCH_FILE_EN2="找不到文件，检查头文件名是否拼写错误。";
$MSG_NULLPTR_NOT_DECLARED="nullptr 是 C++11 才有的写法，确认题目允许 C++11 及以上，否则改用 NULL 或 0。";
$MSG_POW_NOT_DECLARED="pow 没有声明。数学函数要 #include <cmath>，参数和返回值都按 double 使用。";
$MSG_PREPROCESSOR_ERROR="代码里的条件编译没有通过，#error 被触发了，检查 #if/#ifdef 的条件。";
$MSG_RANGE_BASED_FOR_NOT_ALLOWED="范围 for 循环是 C++11 特性，本题可能要求 C++98，改成 for(int i=0;i<n;i++)。";
$MSG_REDECLARATION_OF="同一个名字重复定义了。检查是不是粘贴了两次代码，或者内外层用了同名变量。";
$MSG_REFERENCE_AMBIGUOUS="这个名字有歧义（多个头文件里有同名的东西），把命名空间写完整，例如 std::。";
$MSG_REQUEST_FOR_MEMBER="非对象类型上不能取成员，确认变量的类型是不是你想要的容器或结构体。";
$MSG_RETURN_NO_VALUE="非 void 函数不能只写 return;，要返回一个值，例如 return 0;";
$MSG_RETURN_NO_VALUE2="非 void 函数不能只写 return;，要返回一个值，例如 return 0;";
$MSG_RETURN_WITH_VALUE="void 函数不能返回值。直接写 return; 即可。";
$MSG_SHADOWS_PARAMETER="局部变量把函数的参数遮住了，虽然能编译但很容易用错，建议改个名字。";
$MSG_SIGNED_UNSIGNED_COMPARISON="警告：有符号和无符号整数在比较。数据不大时把其中一个转成 int 再比较。";
$MSG_SIGN_COMPARE="警告：有符号和无符号整数在比较。数值不大时，可以把其中一个写成 (int) 强制转换。";
$MSG_SQRT_NOT_DECLARED="sqrt 没有声明。数学函数要 #include <cmath>，且返回的是 double，建议写 sqrt(1.0*n)。";
$MSG_STATEMENT_NO_EFFECT="这句算式没有产生任何效果，计算结果要赋给变量或者输出出来。";
$MSG_STORAGE_SIZE_UNKNOWN="这个类型的大小未知，通常是结构体还没定义，或者漏了 #include。";
$MSG_TEMPLATE_INSTANTIATION_DEPTH="模板嵌套层数过深，简化一下数据结构。";
$MSG_TEMPLATE_KEYWORD_CONTEXT="template 只能用在文件开头，或者紧跟在另一个模板定义的后面。";
$MSG_TOO_FEW_ARGUMENTS="调用函数时给的参数太少了，补齐缺的参数。";
$MSG_TOO_MANY_ARGUMENTS="调用函数时给的参数太多了，删掉多余的参数。";
$MSG_TOO_MANY_TEMPLATE_PARAMS="template 的参数列表写多了，检查 <> 里面的内容。";
$MSG_TO_MATCH_THIS_BRACE="大括号没有配对。数一数 { 和 } 的个数，if/else/for 后面建议统一都加 {}。";
$MSG_TWO_OR_MORE_DATA_TYPES="同一个变量写了两个类型关键字，删掉多余的那个。";
$MSG_UNABLE_FIND_NUMERIC_OPERATOR="自定义类型没有定义这个运算符。如果要用，需要自己写一个对应的重载函数。";
$MSG_UNABLE_FIND_STRING_OPERATOR="这个类型不支持这种拼接。字符串拼接用 + 或 append()，两边类型要一致。";
$MSG_UNDECLARED_FIRST_USE="函数在使用之前必须有声明（函数原型），或者把函数的定义挪到 main 前面。";
$MSG_UNDEFINED_REFERENCE="链接错误：找不到函数或变量的实现。检查函数名拼写、是否漏写实现、是否漏了 main。";
$MSG_UNTERMINATED_IFNDEF="#ifndef 后面缺少 #endif 结束。";
$MSG_UNUSED_PARAMETER="警告：参数没有被使用，不影响程序结果。";
$MSG_WITHOUT_PREVIOUS_DECL="这个变量既没有类型也没有声明。补上类型：int x=0; 别忘了 int / float / double。";

// ---- 覆盖率报告第二轮补齐 ----
$MSG_BREAK_OUTSIDE_LOOP="break/continue 只能写在循环或 switch 里面，检查是不是少写了一层循环，或者花括号没闭合。";
$MSG_DEPRECATED_CONVERSION="警告：这种类型转换写法已经不推荐使用，换成更明确的写法即可，不影响结果。";
$MSG_EXTENDED_CHARACTER="代码里混进了中文全角符号或不间断空格。切到英文输入法，把这一行重新敲一遍。";
$MSG_FORBIDS_PTR_INT_COMPARE="指针和整数不能直接比较大小。如果要比较地址，要先把两个都转成同一种指针类型。";
$MSG_NEW_TYPES_IN_RETURN="返回值位置上不能顺便定义新类型，类型要写在函数头部的返回类型里。";
$MSG_OVERFLOW_IN_CONVERSION="警告：常量转换时发生了溢出，数字超出了该类型的取值范围，检查数字是否太大。";
$MSG_VARIABLE_NOT_DECLARED_IN_SCOPE="变量在使用前必须先声明。正确写法：int n=0;  然后再给 n 赋值。";
$MSG_VOID_VALUE_NOT_IGNORED="void 类型的值不能参与运算，调用无返回值的函数时不要把结果用在表达式里。";
