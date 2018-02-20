<?php
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
	$MSG_STANDING="名次";
	$MSG_STATISTICS="统计";
	$MSG_USERINFO="修改帐号";
	$MSG_MAIL="短消息";
	
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
  $MSG_Click_Detail="点击看详细";
   $MSG_Manual="人工判题";
   $MSG_OK="确定";
  $MSG_Explain="输入判定原因与提示";
  
	//fool's day
if(date('m')==4&&date('d')==1&&rand(0,100)<10){
        $MSG_Accepted="人品问题-愚人节快乐";
$MSG_Presentation_Error="人品问题-愚人节快乐";
$MSG_Wrong_Answer="人品问题-愚人节快乐";
$MSG_Time_Limit_Exceed="人品问题-愚人节快乐";
$MSG_Memory_Limit_Exceed="人品问题-愚人节快乐";
$MSG_Output_Limit_Exceed="人品问题-愚人节快乐";
$MSG_Runtime_Error="人品问题-愚人节快乐";
$MSG_Compile_Error="人品问题-愚人节快乐";
$MSG_Compile_OK="人品问题-愚人节快乐";
}
    $MSG_TEST_RUN="运行完成";
	$MSG_TR="测试运行";

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
	
	$MSG_RUNID="提交编号";
	$MSG_USER="用户";
	$MSG_PROBLEM="问题";
	$MSG_RESULT="结果";
	$MSG_MEMORY="内存";
	$MSG_TIME="耗时";
	$MSG_LANG="语言";
	$MSG_CODE_LENGTH="代码长度";
	$MSG_SUBMIT_TIME="提交时间";
	
	$MSG_SEARCH="查找";
	$MSG_PROBLEM_ID="题目编号";
	$MSG_TITLE="标题";
	$MSG_SOURCE="来源/分类";
	$MSG_SUBMIT="提交";
	$MSG_ORDER_NUM="序号";
	
	//ranklist.php
	$MSG_Number="名次";
	$MSG_NICK="昵称";
	$MSG_SOVLED="解决";
	$MSG_RATIO="比率";
	
	//registerpage.php
	$MSG_USER_ID="用户名（学号）";
	$MSG_PASSWORD="密码";
	$MSG_REPEAT_PASSWORD="重复密码";
	$MSG_SCHOOL="学校";
	$MSG_EMAIL="电子邮件";
	$MSG_REG_INFO="注册信息";
	$MSG_VCODE="验证码";

		//problem.php
	$MSG_NO_SUCH_PROBLEM="题目不可用!";
	$MSG_Description="题目描述"  ;
	$MSG_Input="输入"  ;
	$MSG_Output= "输出" ;
	$MSG_Sample_Input= "样例输入" ;
	$MSG_Sample_Output= "样例输出" ;
	$MSG_Test_Input= "测试输入" ;
	$MSG_Test_Output= "测试输出" ;
	$MSG_SPJ= "特殊裁判" ;
	$MSG_HINT= "提示" ;
	$MSG_Source= "来源" ;
	$MSG_Time_Limit="时间限制";
	$MSG_Memory_Limit="内存限制";
	//admin menu
	$MSG_SEEOJ="查看前台";
	$MSG_ADD="添加";
	$MSG_LIST="列表";
	$MSG_NEWS="新闻";
	$MSG_TEAMGENERATOR="比赛队帐号生成器";
	$MSG_SETMESSAGE="设置公告";
	$MSG_SETPASSWORD="修改密码";
	$MSG_REJUDGE="重判题目";
	$MSG_PRIVILEGE="权限";
	$MSG_GIVESOURCE="转移源码";
	$MSG_IMPORT="导入";
	$MSG_EXPORT="导出";
	$MSG_UPDATE_DATABASE="更新数据库";
	$MSG_ONLINE="在线";
  //contest
  $MSG_PRIVATE_WARNING="比赛尚未开始或私有，不能查看题目。";
  $MSG_WATCH_RANK="点击这里查看做题排名。";
  $MSG_Public="公开";
  $MSG_Private="私有";
  $MSG_Running="运行中";
  $MSG_Start="开始于";
  $MSG_TotalTime="总赛时";
  $MSG_LeftTime="剩余";
  $MSG_Ended="已结束";
  $MSG_Login="请登录后继续操作";
  $MSG_JUDGER="判题机";
  $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM="考试期间，不能查阅以前提交的代码。";
  $MSG_BBS_NOT_ALLOWED_FOR_EXAM="考试期间,讨论版禁用。";
  $MSG_MAIL_NOT_ALLOWED_FOR_EXAM="考试期间,内邮禁用。";
  $MSG_LOAD_TEMPLATE_CONFIRM="是否加载默认模板?\\n 如果选择是，当前代码将被覆盖!";
  
  $MSG_BLOCKLY_OPEN="可视化"; 
  $MSG_BLOCKLY_TEST="翻译运行"; 
  $MSG_MY_SUBMISSIONS="我的提交"; 
  $MSG_MY_CONTESTS="我的$MSG_CONTEST"; 
  $MSG_Creator="命题人";
  $MSG_IMPORTED="外部导入";
  
  $MSG_USER="用户";
   
  $MSG_HELP_SEEOJ="跳转回到前台";
  $MSG_HELP_ADD_NEWS="添加首页显示的新闻";
  $MSG_HELP_NEWS_LIST="管理已经发布的新闻";
  $MSG_HELP_USER_LIST="对注册用户停用、启用帐号";
  $MSG_HELP_ADD_PROBLEM="手动添加新的题目，多组测试数据在添加后从题目列表TestData按钮进入上传，新建题目<b>默认隐藏</b>，需在问题列表中点击红色<font color='red'>Reserved</font>切换为绿色<font color='green'>Available</font>启用。。";
  $MSG_HELP_PROBLEM_LIST="管理已有的题目和数据，上传数据可以用zip压缩不含目录的数据。";
  $MSG_HELP_ADD_CONTEST="规划新的比赛，用逗号分隔题号。可以设定私有比赛，用密码或名单限制参与者。";
  $MSG_HELP_CONTEST_LIST="已有的比赛列表，修改时间和公开/私有，尽量不要在开赛后调整题目列表。";
  $MSG_HELP_TEAMGENERATOR="批量生成大量比赛帐号、密码，用于来自不同学校的参赛者。小系统不要随便使用，可能产生垃圾帐号，无法删除。";
  $MSG_HELP_SETMESSAGE="设置滚动公告内容";
  $MSG_HELP_SETPASSWORD="重设指定用户的密码，对于管理员帐号需要先降级为普通用户才能修改。";
  $MSG_HELP_REJUDGE="重判指定的题目、提交或比赛。";
  $MSG_HELP_ADD_PRIVILEGE="给指定用户增加权限，包括管理员、题目添加者、比赛组织者、比赛参加者、代码查看者、手动判题、远程判题等权限。";
  $MSG_HELP_PRIVILEGE_LIST="查看已有的特殊权限列表、进行删除操作。";
  $MSG_HELP_GIVESOURCE="将导入系统的标程赠与指定帐号，用于训练后辅助未通过的人学习参考。";
  $MSG_HELP_EXPORT_PROBLEM="将系统中的题目以fps.xml文件的形式导出。";
  $MSG_HELP_IMPORT_PROBLEM="导入从官方群共享或tk.hustoj.com下载到的fps.xml文件。";
  $MSG_HELP_UPDATE_DATABASE="更新数据库结构，在每次升级（sudo update-hustoj）之后或者导入老系统数据库备份，应至少操作一次。";
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
  $MSG_HELP_SPJ="特殊裁判的使用，请参考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜索hustoj special judge</a>"; 
  
?>
