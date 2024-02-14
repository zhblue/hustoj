<?php
 //oj-header.php
 $MSG_FAQ ="常見問答";
 $MSG_BBS ="討論版";
 $MSG_HOME ="主頁";
 $MSG_PROBLEMS ="問題";
 $MSG_STATUS ="狀態";
 $MSG_RANKLIST ="排名";
 $MSG_CONTEST ="競賽&作業";
 $MSG_RECENT_CONTEST ="名校聯賽";
 $MSG_LOGOUT ="註銷";
 $MSG_LOGIN ="登錄";
 $MSG_LOST_PASSWORD ="忘記密碼";
 $MSG_REGISTER ="註冊";
 $MSG_ADMIN ="管理";
 $MSG_SYSTEM ="系統";
 $MSG_STANDING ="名次";
 $MSG_STATISTICS ="統計";
 $MSG_USERINFO ="用戶信息";
 $MSG_MAIL ="短消息";
 
 //status.php
 $MSG_Pending ="等待";
 $MSG_Pending_Rejudging ="等待重判";
 $MSG_Compiling ="編譯中";
 $MSG_Running_Judging ="運行並評判";
 $MSG_Accepted ="正確";
 $MSG_Presentation_Error ="格式錯誤";
 $MSG_Wrong_Answer ="答案錯誤";
 $MSG_Time_Limit_Exceed ="時間超限";
 $MSG_Memory_Limit_Exceed ="內存超限";
 $MSG_Output_Limit_Exceed ="輸出超限";
 $MSG_Runtime_Error ="運行錯誤";
 $MSG_Compile_Error ="編譯錯誤";
 $MSG_Runtime_Click ="運行錯誤(點擊看詳細) ";
 $MSG_Compile_Click ="編譯錯誤(點擊看詳細) ";
 $MSG_Compile_OK ="編譯成功";
 $MSG_MANUAL_CONFIRMATION ="自動評測通過，等待人工確認";
 $MSG_Click_Detail ="點擊看詳細";
 $MSG_Manual ="人工判題";
 $MSG_OK ="確定";
 $MSG_Explain ="輸入判定原因與提示";
 
 //fool's day
 if (date( 'm' )== 4 &&date( 'd' )== 1 &&rand( 0 , 100 )< 5 ){
 $MSG_Accepted =" <span title=愚人節快樂>似乎好像是正確</span> ";
 //$MSG_Presentation_Error="人品問題-愚人節快樂";
 //$MSG_Wrong_Answer="人品問題-愚人節快樂";
 //$MSG_Time_Limit_Exceed="人品問題-愚人節快樂";
 //$MSG_Memory_Limit_Exceed="人品問題-愚人節快樂";
 //$MSG_Output_Limit_Exceed="人品問題-愚人節快樂";
 //$MSG_Runtime_Error="人品問題-愚人節快樂";
 //$MSG_Compile_Error="人品問題-愚人節快樂";
 //$MSG_Compile_OK="人品問題-愚人節快樂";
}
 
 $MSG_TEST_RUN ="運行完成";
 
 $MSG_RUNID ="提交編號";
 $MSG_USER ="用戶";
 $MSG_PROBLEM ="問題";
 $MSG_RESULT ="結果";
 $MSG_MEMORY ="內存";
 //$MSG_TIME="耗時"; // overided by line 236
 $MSG_LANG ="語言";
 $MSG_CODE_LENGTH ="代碼長度";
 $MSG_SUBMIT_TIME ="提交時間";
 
 //problemstatistics.php
 $MSG_PD ="等待";
 $MSG_PR ="等待重判";
 $MSG_CI ="編譯中";
 $MSG_RJ ="運行並評判";
 $MSG_AC ="正確";
 $MSG_PE ="格式錯誤";
 $MSG_WA ="答案錯誤";
 $MSG_TLE ="時間超限";
 $MSG_MLE ="內存超限";
 $MSG_OLE ="輸出超限";
 $MSG_RE ="運行錯誤";
 $MSG_CE ="編譯錯誤";
 $MSG_CO ="編譯成功";
 $MSG_TR ="測試運行";
 $MSG_MC ="待裁判確認";
 $MSG_RESET ="重置";
 
 //problemset.php
 $MSG_SEARCH ="查找";
 $MSG_PROBLEM_ID ="題目編號";
 $MSG_TITLE ="標題";
 $MSG_SOURCE ="來源/分類";
 $MSG_SUBMIT_NUM ="提交量";
 $MSG_SUBMIT ="提交";
 $MSG_SHOW_OFF ="露一手! ";
 
 //submit.php
 $MSG_VCODE_WRONG ="驗證碼錯誤！ ";
 $MSG_LINK_ERROR ="在哪裡可以找到此鏈接？ 沒有這個問題。 ";
 $MSG_PROBLEM_RESERVED ="問題已禁用。 ";
 $MSG_NOT_IN_CONTEST ="您不能立即提交，因為您沒有被比賽邀請或比賽沒有進行！ ";
 $MSG_NOT_INVITED ="您不被邀請！ ";
 $MSG_NO_PROBLEM ="沒有這樣的問題！ ";
 $MSG_NO_PLS ="使用未知的編程語言！ ";
 $MSG_TOO_SHORT ="代碼太短！ ";
 $MSG_TOO_LONG ="代碼太長！ ";
 $MSG_BREAK_TIME ="您不應在10秒鐘內提交超過1次的申請..... ";
 
 //ranklist.php
 $MSG_Number ="名次";
 $MSG_NICK ="暱稱";
 $MSG_SOVLED ="解決";
 $MSG_RATIO ="比率";
 $MSG_DAY ="日排行";
 $MSG_WEEK ="週排行";
 $MSG_MONTH ="月排行";
 $MSG_YEAR ="年排行";
 //registerpage.php
 $MSG_USER_ID ="用戶名（學號） ";
 $MSG_PASSWORD ="密碼";
 $MSG_REPEAT_PASSWORD ="重複密碼";
 $MSG_SCHOOL ="學校";
 $MSG_GROUP_NAME="班級/小組";
 $MSG_EMAIL ="電子郵件";
 $MSG_REG_INFO ="設置註冊信息";
 $MSG_VCODE ="驗證碼";
 $MSG_ACTIVE_YOUR_ACCOUNT="Active Your Account ";
 $MSG_CLICK_COPY="Click or Copy the LINK to open in browser ";
 $MSG_CHECK="Check out ";

 //problem.php
 $MSG_NO_SUCH_PROBLEM ="題目當前不可用!<br>它可能被用於未來的比賽、過去的私有比賽，或者管理員由於尚未測試通過等其他原因暫時停止了該題目用於練習。 ";
 $MSG_Description ="題目描述" ;
 $MSG_Input ="輸入" ;
 $MSG_Output = "輸出" ;
 $MSG_Sample_Input = "樣例輸入" ;
 $MSG_Sample_Output = "樣例輸出" ;
 $MSG_Test_Input = "測試輸入" ;
 $MSG_Test_Output = "測試輸出" ;
 $MSG_NJ = "普通裁判" ;
 $MSG_SPJ = "特殊裁判" ;
 $MSG_RTJ = "文本裁判" ;
 $MSG_HINT = "提示" ;
 $MSG_Source = "來源" ;
 $MSG_Time_Limit ="時間限制";
 $MSG_Memory_Limit ="內存限制";
 $MSG_EDIT ="編輯";
 $MSG_TESTDATA ="測試數據";
 $MSG_CLICK_VIEW_HINT ="點擊查看劇透級題解";
 
 //admin menu
 $MSG_SEEOJ ="查看前台";
 $MSG_ADD ="添加";
 $MSG_MENU ="菜單";
 $MSG_EXPLANATION ="內容描述";
 $MSG_LIST ="列表";
 $MSG_NEWS ="公告";
 $MSG_CONTENTS ="內容";
 $MSG_SAVE ="保存";
 $MSG_DELETED ="已刪除";
 $MSG_NORMAL ="正常";
 
 $MSG_TEAMGENERATOR ="比賽隊帳號生成器";
 $MSG_SETMESSAGE ="設置公告";
 $MSG_SETPASSWORD ="修改密碼";
 $MSG_REJUDGE ="重判題目";
 $MSG_PRIVILEGE ="權限";
 $MSG_GIVESOURCE ="轉移源碼";
 $MSG_IMPORT ="導入";
 $MSG_EXPORT ="導出";
 $MSG_UPDATE_DATABASE ="更新數據庫";
 $MSG_BACKUP_DATABASE ="備份數據庫";
 $MSG_ONLINE ="在線";
 $MSG_SET_LOGIN_IP ="指定登錄IP ";
 $MSG_PRIVILEGE_TYPE ="權限類型";
 $MSG_NEWS_MENU ="是否展示到菜單";
 $MSG_LAST_LOGIN="最後登錄";

 //contest.php
 $MSG_PRIVATE_WARNING ="比賽尚未開始或私有，不能查看題目。 ";
 $MSG_PRIVATE_USERS_ADD =" *可以將學生學號從Excel整列複製過來，然後要求他們用學號做UserID註冊,就能進入Private的比賽作為作業和測驗。 ";
 $MSG_PLS_ADD =" *請選擇所有可以通過Ctrl +單擊提交的語言。 ";
 $MSG_TIME_WARNING ="比賽開始前。 ";
 $MSG_WATCH_RANK ="點擊這裡查看做題排名。 ";
 $MSG_NOIP_WARNING = $OJ_NOIP_KEYWORD ."比賽進行中，結束後才能查看結果。 ";
 $MSG_NOIP_NOHINT = $OJ_NOIP_KEYWORD ."比賽,不顯示提示信息。 ";
 $MSG_SERVER_TIME ="服務器時間";
 $MSG_START_TIME ="開始時間";
 $MSG_END_TIME ="結束時間";
 $MSG_VIEW_ALL_CONTESTS ="顯示所有作業比賽";
 $MSG_CONTEST_ID ="作業比賽編號";
 $MSG_CONTEST_NAME ="作業比賽名稱";
 $MSG_CONTEST_STATUS ="作業比賽狀態";
 $MSG_CONTEST_OPEN ="開放";
 $MSG_CONTEST_CREATOR ="創建人";
 $MSG_CONTEST_PENALTY ="累計時間";
 $MSG_IP_VERIFICATION =" IP驗證";
 $MSG_LOG="日誌";
 $MSG_SUSPECT="審計";
 $MSG_CONTEST_SUSPECT1 ="具有多個ID的IP地址。如果在競賽/考試期間在同一台計算機上訪問了多個ID，則會記錄該ID。 ";
 $MSG_CONTEST_SUSPECT2 ="具有多個IP地址的ID。 如果在競賽/考試期間切換到另一台計算機，它將記錄下來。 ";
 
 $MSG_SECONDS ="秒";
 $MSG_MINUTES ="分";
 $MSG_HOURS ="小時";
 $MSG_DAYS ="天";
 $MSG_MONTHS ="月份";
 $MSG_YEARS ="年份";
 
 $MSG_Public ="公開";
 $MSG_Private ="私有";
 $MSG_Running ="運行中";
 $MSG_Start ="開始於";
 $MSG_End ="結束於";
 $MSG_TotalTime ="總賽時";
 $MSG_LeftTime ="剩餘";
 $MSG_Ended ="已結束";
 $MSG_Login ="請登錄後繼續操作";
 $MSG_JUDGER ="判題機";
 
 $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM ="考試期間，不能查閱以前提交的代碼。 ";
 $MSG_BBS_NOT_ALLOWED_FOR_EXAM ="考試期間,討論版禁用。 ";
 $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM ="考試期間,禁止修改帳號信息。 ";
 $MSG_MAIL_NOT_ALLOWED_FOR_EXAM ="考試期間,內郵禁用。 ";
 $MSG_LOAD_TEMPLATE_CONFIRM ="是否加載默認模板? \\ n 如果選擇是，當前代碼將被覆蓋! ";
 $MSG_NO_MAIL_HERE ="本OJ不支持內部郵件哦~ ";
 
 $MSG_BLOCKLY_OPEN ="可視化";
 $MSG_BLOCKLY_TEST ="翻譯運行";
 $MSG_MY_SUBMISSIONS ="我的提交";
 $MSG_MY_CONTESTS ="我的$MSG_CONTEST ";
 $MSG_Creator ="命題人";
 $MSG_IMPORTED ="外部導入";
 $MSG_PRINTER ="打印";
 $MSG_PRINT_DONE ="打印完成";
 $MSG_PRINT_PENDING ="提交成功,待打印";
 $MSG_PRINT_WAITING ="請耐心等候，不要重複提交相同的打印任務";
 $MSG_COLOR ="顏色";
 $MSG_BALLOON ="氣球";
 $MSG_BALLOON_DONE ="氣球已發放";
 $MSG_BALLOON_PENDING ="氣球待發放";
 
 $MSG_DATE ="日期";
 $MSG_TIME ="時間";
 $MSG_SIGN ="個性簽名";
 $MSG_RECENT_PROBLEM ="最近更新";
 $MSG_RECENT_CONTEST ="近期比賽";
 $MSG_PASS_RATE ="通過率";
 $MSG_SHOW_TAGS ="顯示分類標籤";
 $MSG_SHOW_ALL_TAGS ="所有標籤";
 $MSG_RESERVED ="未啟用";
 
 $MSG_HELP_SEEOJ ="跳轉回到前台";
 $MSG_HELP_ADD_NEWS ="添加首頁顯示的新聞";
 $MSG_HELP_NEWS_LIST ="管理已經發布的新聞";
 $MSG_HELP_USER_LIST ="對註冊用戶停用、啟用帳號";
 $MSG_HELP_USER_ADD ="添加用戶";
 $MSG_HELP_ADD_PROBLEM ="手動添加新的題目，多組測試數據在添加後從題目列表TestData按鈕進入上傳，新建題目<b>默認隱藏</b>，需在問題列表中點擊紅色<font color='red'> $MSG_RESERVED </font>切換為綠色<font color='green'>Available</font>啟用。。 ";
 $MSG_HELP_PROBLEM_LIST ="管理已有的題目和數據，上傳數據可以用zip壓縮不含目錄的數據。 ";
 $MSG_HELP_ADD_CONTEST ="規劃新的比賽，用逗號分隔題號。可以設定私有比賽，用密碼或名單限制參與者。 ";
 $MSG_HELP_CONTEST_LIST ="已有的比賽列表，修改時間和公開/私有，盡量不要在開賽后調整題目列表。 ";
 $MSG_HELP_SET_LOGIN_IP ="記錄考試期間的計算機(登錄IP)更改。 ";
 $MSG_HELP_TEAMGENERATOR ="批量生成大量比賽帳號、密碼，用於來自不同學校的參賽者。小系統不要隨便使用，可能產生垃圾帳號，無法刪除。 ";
 $MSG_HELP_SETMESSAGE ="設置滾動公告內容";
 $MSG_HELP_SETPASSWORD ="重設指定用戶的密碼，對於管理員帳號需要先降級為普通用戶才能修改。 ";
 $MSG_HELP_REJUDGE ="重判指定的題目、提交或比賽。 ";
 $MSG_HELP_ADD_PRIVILEGE ="給指定用戶增加權限，包括管理員、題目添加者、比賽組織者、比賽參加者、代碼查看者、手動判題、遠程判題、打印員、氣球發放員等權限。 ";
 $MSG_HELP_ADD_CONTEST_USER ="給用戶添加單個比賽權限。 ";
 $MSG_HELP_ADD_SOLUTION_VIEW ="給用戶添加單個題目的答案查看權限。 ";
 $MSG_HELP_PRIVILEGE_LIST ="查看已有的特殊權限列表、進行刪除操作。 ";
 $MSG_HELP_GIVESOURCE ="將導入系統的標程贈與指定帳號，用於訓練後輔助未通過的人學習參考。 ";
 $MSG_HELP_EXPORT_PROBLEM ="將系統中的題目以fps.xml档案的形式導出。 ";
 $MSG_HELP_IMPORT_PROBLEM ="導入從官方群共享或tk.hustoj.com下載到的fps.xml档案。 ";
 $MSG_HELP_UPDATE_DATABASE ="更新數據庫結構，在每次升級（sudo update-hustoj）之後或者導入老系統數據庫備份，應至少操作一次。 ";
 $MSG_HELP_ONLINE ="查看在線用戶";
 $MSG_HELP_AC ="答案正確，請再接再厲。 ";
 $MSG_HELP_PE ="答案基本正確，但是格式不對。 ";
 $MSG_HELP_WA ="答案不對，僅僅通過樣例數據的測試並不一定是正確答案，一定還有你沒想到的地方，點擊查看系統可能提供的對比信息。 ";
 $MSG_HELP_TLE ="運行超出時間限制，檢查下是否有死循環，或者應該有更快的計算方法";
 $MSG_HELP_MLE ="超出內存限制，數據可能需要壓縮，檢查內存是否有洩露";
 $MSG_HELP_OLE ="輸出超過限制，你的輸出比正確答案長了兩倍，一定是哪裡弄錯了";
 $MSG_HELP_RE ="運行時錯誤，非法的內存訪問，數組越界，指針漂移，調用禁用的系統函數。請點擊後獲得詳細輸出";
 $MSG_HELP_CE ="編譯錯誤，請點擊後獲得編譯器的詳細輸出";
 
 $MSG_HELP_MORE_TESTDATA_LATER ="更多組測試數據，請在題目添加完成後補充";
 $MSG_HELP_ADD_FAQS ="管理員可以添加一條新聞，命名為\" faqs. $OJ_LANG \"來取代<a href=../faqs.php> $MSG_FAQ </a>的內容。 ";
 $MSG_HELP_HUSTOJ =" <sub><a target='_blank' href='https://github.com/zhblue/hustoj'><span class='glyphicon glyphicon-heart' aria-hidden='true'></span> 請到HUSTOJ 來，給我們加個<span class='glyphicon glyphicon-star' aria-hidden='true'></span>Star!</a></sub> ";
 $MSG_HELP_SPJ ="特殊裁判的使用，請參考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜索hustoj special judge</a> ";
 $MSG_HELP_BALLOON_SCHOOL ="打印，氣球帳號的School字段用於過濾任務列表，例如填zjicm則只顯示帳號為zjicm開頭的任務";
 $MSG_HRLP_BACKUP_DATABASE ="備份數據庫,測試數據和圖片到0題目錄";
 
 $MSG_WARNING_LOGIN_FROM_DIFF_IP ="從不同的ip地址登錄";
 $MSG_WARNING_DURING_EXAM_NOT_ALLOWED ="在考試期間不被允許";
 $MSG_WARNING_ACCESS_DENIED ="抱歉，您無法查看此消息！因為它不屬於您，或者管理員設定係統狀態為不顯示此類信息。 ";
 
 $MSG_WARNING_USER_ID_SHORT ="用戶名至少3位字符! ";
 $MSG_WARNING_PASSWORD_SHORT ="密碼至少6位! ";
 $MSG_WARNING_REPEAT_PASSWORD_DIFF ="兩次輸入的密碼不一致! ";
 
 
 $MSG_LOSTPASSWORD_MAILBOX ="請到您郵箱的垃圾郵件档案夾尋找驗證碼，並填寫到這裡";
 $MSG_LOSTPASSWORD_WILLBENEW ="如果填寫正確，通過下一步驗證，這個驗證碼就自動成為您的新密碼！ ";
 
 
 // template/../reinfo.php
 $MSG_A_NOT_ALLOWED_SYSTEM_CALL ="使用了系統禁止的操作系統調用，看看是否越權訪問了档案或進程等資源,如果你是系統管理員，而且確認提交的答案沒有問題，測試數據沒有問題，可以發送'RE'到微信公眾號onlinejudge，查看解決方案。 ";
 $MSG_SEGMETATION_FAULT ="段錯誤，檢查是否有數組越界，指針異常，訪問到不應該訪問的內存區域";
 $MSG_FLOATING_POINT_EXCEPTION ="浮點錯誤，檢查是否有除以零的情況";
 $MSG_BUFFER_OVERFLOW_DETECTED ="緩衝區溢出，檢查是否有字符串長度超出數組的情況";
 $MSG_PROCESS_KILLED ="進程因為內存或時間原因被殺死，檢查是否有死循環";
 $MSG_ALARM_CLOCK ="進程因為時間原因被殺死，檢查是否有死循環，本錯誤等價於超時TLE ";
 $MSG_CALLID_20 ="可能存在數組越界，檢查題目描述的數據量與所申請數組大小關係";
 $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION ="檢查數組越界的情況";
 $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION ="字符串的字符下標越界，檢查subString,charAt等方法的參數";
 $MSG_WRONG_OUTPUT_TYPE_EXCEPTION="二進製輸出錯誤，檢查是否誤將數值類型作為字符輸出，或者輸出了不打印字符的情況。";
 $MSG_NON_ZERO_RETURN="Main函數不能返回非零的值，否則視同程式出錯。";
	
 // template/../ceinfo.php
 $MSG_ERROR_EXPLAIN ="輔助解釋";
 $MSG_SYSTEM_OUT_PRINT =" Java中System.out.print用法跟C語言printf不同，請試用System.out.format ";
 $MSG_NO_SUCH_FILE_OR_DIRECTORY ="服務器為Linux系統，不能使用windows下特有的非標準頭档案。 ";
 $MSG_NOT_A_STATEMENT ="檢查大括號{}匹配情況，eclipse整理代碼快捷鍵Ctrl+Shift+F ";
 $MSG_EXPECTED_CLASS_INTERFACE_ENUM ="請不要將java函數（方法）放置在類聲明外部，注意大括號的結束位置} ";
 $MSG_SUBMIT_JAVA_AS_C_LANG ="請不要將java程式提交為C語言";
 $MSG_DOES_NOT_EXIST_PACKAGE ="檢測拼寫，如：系統對象System為大寫S開頭";
 $MSG_POSSIBLE_LOSS_OF_PRECISION ="賦值將會失去精度，檢測數據類型，如確定無誤可以使用強制類型轉換";
 $MSG_INCOMPATIBLE_TYPES =" Java中不同類型的數據不能互相賦值，整數不能用作布爾值";
 $MSG_ILLEGAL_START_OF_EXPRESSION ="字符串應用英文雙引號( \\\" )引起";
 $MSG_CANNOT_FIND_SYMBOL ="拼寫錯誤或者缺少調用函數所需的對像如println()需對System.out調用";
 $MSG_EXPECTED_SEMICOLON ="缺少分號。 ";
 $MSG_DECLARED_JAVA_FILE_NAMED =" Java必須使用public class Main。 ";
 $MSG_EXPECTED_WILDCARD_CHARACTER_AT_END_OF_INPUT ="代碼沒有結束，缺少匹配的括號或分號，檢查復制時是否選中了全部代碼。 ";
 $MSG_INVALID_CONVERSION ="隱含的類型轉換無效，嘗試用顯示的強制類型轉換如(int *)malloc(....) ";
 $MSG_NO_RETURN_TYPE_IN_MAIN =" C++標準中，main函數必須有返回值";
 $MSG_NOT_DECLARED_IN_SCOPE ="變量沒有聲明過，檢查下是否拼寫錯誤！ ";
 $MSG_MAIN_MUST_RETURN_INT ="在標準C語言中，main函數返回值類型必須是int，教材和VC中使用void是非標準的用法";
 $MSG_PRINTF_NOT_DECLARED_IN_SCOPE =" printf函數沒有聲明過就進行調用，檢查下是否導入了stdio.h或cstdio頭档案";
 $MSG_IGNOREING_RETURN_VALUE ="警告：忽略了函數的返回值，可能是函數用錯或者沒有考慮到返回值異常的情況";
 $MSG_NOT_DECLARED_INT64 =" __int64沒有聲明，在標準C/C++中不支持微軟VC中的__int64,請使用long long來聲明64位變量";
 $MSG_EXPECTED_SEMICOLON_BEFORE ="前一行缺少分號";
 $MSG_UNDECLARED_NAME ="變量使用前必須先進行聲明，也有可能是拼寫錯誤，注意大小寫區分。 ";
 $MSG_SCANF_NOT_DECLARED_IN_SCOPE =" scanf函數沒有聲明過就進行調用，檢查下是否導入了stdio.h或cstdio頭档案";
 $MSG_MEMSET_NOT_DECLARED_IN_SCOPE =" memset函數沒有聲明過就進行調用，檢查下是否導入了stdlib.h或cstdlib頭档案";
 $MSG_MALLOC_NOT_DECLARED_IN_SCOPE =" malloc函數沒有聲明過就進行調用，檢查下是否導入了stdlib.h或cstdlib頭档案";
 $MSG_PUTS_NOT_DECLARED_IN_SCOPE =" puts函數沒有聲明過就進行調用，檢查下是否導入了stdio.h或cstdio頭档案";
 $MSG_GETS_NOT_DECLARED_IN_SCOPE =" gets函數沒有聲明過就進行調用，檢查下是否導入了stdio.h或cstdio頭档案";
 $MSG_STRING_NOT_DECLARED_IN_SCOPE =" string類函數沒有聲明過就進行調用，檢查下是否導入了string.h或cstring頭档案";
 $MSG_NO_TYPE_IMPORT_IN_C_CPP ="不要將Java語言程式提交為C/C++,提交前註意選擇語言類型。 ";
 $MSG_ASM_UNDECLARED ="不允許在C/C++中嵌入彙編語言代碼。 ";
 $MSG_REDEFINITION_OF ="函數或變量重複定義，看看是否多次粘貼代碼。 ";
 $MSG_EXPECTED_DECLARATION_OR_STATEMENT ="程式好像沒寫完，看看是否複製粘貼時漏掉代碼。 ";
 $MSG_UNUSED_VARIABLE ="警告：變量聲明後沒有使用，檢查下是否拼寫錯誤，誤用了名稱相似的變量。 ";
 $MSG_IMPLICIT_DECLARTION_OF_FUNCTION ="函數隱性聲明，檢查下是否導入了正確的頭档案。或者缺少了題目要求的指定名稱的函數。 ";
 $MSG_ARGUMENTS_ERROR_IN_FUNCTION ="函數調用時提供的參數數量不對，檢查下是否用錯參數。 ";
 $MSG_EXPECTED_BEFORE_NAMESPACE ="不要將C++語言程式提交為C,提交前註意選擇語言類型。 ";
 $MSG_STRAY_PROGRAM ="中文空格、標點等不能出現在程式中註釋和字符串以外的部分。編寫程式時請關閉中文輸入法。請不要使用網上複製來的代碼。 ";
 $MSG_DIVISION_BY_ZERO ="除以零將導致浮點溢出。 ";
 $MSG_CANNOT_BE_USED_AS_A_FUNCTION ="變量不能當成函數用，檢查變量名和函數名重複的情況，也可能是拼寫錯誤。 ";
 $MSG_CANNOT_FIND_TYPE =" scanf/printf的格式描述和後面的參數表不一致，檢查是否多了或少了取址符“&”，也可能是拼寫錯誤。 ";
 $MSG_JAVA_CLASS_ERROR =" Java語言提交只能有一個public類，並且類名必須是Main，其他類請不要用public關鍵詞";
 $MSG_EXPECTED_BRACKETS_TOKEN ="缺少右括號";
 $MSG_NOT_FOUND_SYMBOL ="使用了未定義的函數或變量，檢出拼寫是否有誤，不要使用不存在的函數，Java調用方法通常需要給出對象名稱如list1.add(...)。Java方法調用時對參數類型敏感，如:不能將整數(int)傳送給接受字符串對象(String)的方法";
 $MSG_NEED_CLASS_INTERFACE_ENUM ="缺少關鍵字，應當聲明為class、interface 或enum ";
 $MSG_CLASS_SYMBOL_ERROR ="使用教材上的例子，必須將相關類的代碼一併提交，同時去掉其中的public關鍵詞";
 $MSG_INVALID_METHOD_DECLARATION ="只有跟類名相同的方法為構造方法，不寫返回值類型。如果將類名修改為Main,請同時修改構造方法名稱。 ";
 $MSG_EXPECTED_AMPERSAND_TOKEN ="不要將C++語言程式提交為C,提交前註意選擇語言類型。 ";
 $MSG_DECLARED_FUNCTION_ORDER ="請注意函數、方法的聲明前後順序，不能在一個方法內出現另一個方法的聲明。 ";
 $MSG_NEED_SEMICOLON ="上面標註的這一行，最後缺少分號。 ";
 $MSG_EXTRA_TOKEN_AT_END_OF_INCLUDE =" include語句必須獨立一行，不能與後面的語句放在同一行";
 $MSG_INT_HAS_NEXT =" hasNext() 應該改為nextInt() ";
 $MSG_UNTERMINATED_COMMENT ="註釋沒有結束，請檢查“/*”對應的結束符“*/”是否正確";
 $MSG_EXPECTED_BRACES_TOKEN ="函數聲明缺少小括號()，如int main()寫成了int main ";
 $MSG_REACHED_END_OF_FILE_1 ="檢查提交的源碼是否沒有復製完整，或者缺少了結束的大括號";
 $MSG_SUBSCRIPT_ERROR ="不能對非數組或指針的變量進行下標訪問";
 $MSG_EXPECTED_PERCENT_TOKEN =" scanf的格式部分需要用雙引號引起";
 $MSG_EXPECTED_EXPRESSION_TOKEN ="參數或表達式沒寫完";
 $MSG_EXPECTED_BUT ="錯誤的標點或符號";
 $MSG_REDEFINITION_MAIN ="這道題目可能是附加代碼題，請重新審題，看清題意，不要提交系統已經定義的main函數，而應提交指定格式的某個函數。 ";
 $MSG_IOSTREAM_ERROR ="請不要將C++程式提交為C ";
 $MSG_EXPECTED_UNQUALIFIED_ID_TOKEN ="留意數組聲明後是否少了分號";
 $MSG_REACHED_END_OF_FILE_2 ="程式末尾缺少大括號";
 $MSG_INVALID_SYMBOL ="檢查是否使用了中文標點或空格";
 $MSG_DECLARED_FILE_NAMED =" OJ中public類只能是Main ";
 $MSG_EXPECTED_IDENTIFIER ="聲明變量時，可能沒有聲明變量名或缺少括號。 ";
 $MSG_VARIABLY_MODIFIED ="數組大小不能用變量，C 語言中不能使用變量作為全局數組的維度大小，包括const 變量";
 $MSG_FUNCTION_GETS_REMOVIED =" std::gets 於C++11 被棄用，並於C++14 移除。可使用std::fgets 替代。或者增加宏定義#define gets(S) fgets(S,sizeof(S),stdin) ";
 $MSG_PROBLEM_USED_IN ="題目已經用於私有比賽";
 $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT ="內郵僅限學生老師互相發送，不允許同學間發送！ ";
 $MSG_COPY_USER_LIST_FROM_CONTEST="選擇一個比賽復製學生名單...";
 $MSG_REFRESH_PRIVILEGE ="刷新權限";
 
 $MSG_SAVED_DATE ="保存時間";
 $MSG_PROBLEM_STATUS ="當前狀態";
 
 $MSG_NEW_CONTEST ="創建新比賽";
 $MSG_AVAILABLE ="啟用";
 $MSG_RESERVED ="未啟用";
 $MSG_NEW_PROBLEM_LIST ="創建新題單";
 $MSG_DELETE ="刪除";
 $MSG_EDIT ="編輯";
 $MSG_TEST_DATA ="管理測試數據";
 $MSG_CHECK_TO ="批量選擇操作";
 
 //bbcode.php
 $MSG_TOTAL ="共";
 $MSG_NUMBER_OF_PROBLEMS ="題";
 
 $MSG_SUBMIT_RECORD ="提交記錄";
 $MSG_RETURN_CONTEST ="返回比賽";
 $MSG_COPY ="複製";
 $MSG_SUCCESS ="成功";
 $MSG_FAIL ="失敗";
 $MSG_TEXT_COMPARE ="文本比較";
 $MSG_JUDGE_STYLE ="評測方式";
 // reinfo.php
 $MSG_ERROR_INFO ="錯誤信息";
 $MSG_INFO_EXPLAINATION ="輔助解釋";
 // ceinfo.php
 $MSG_COMPILE_INFO ="編譯信息";
 $MSG_SOURCE_CODE ="源代碼";
 //contest.php
 $MSG_Contest_Pending ="未開始";
 $MSG_Server_Time ="當前時間";
 $MSG_Contest_Infomation ="信息與公告";
 // sourcecompare.php
 $MSG_Source_Compare ="源代碼對比";
 $MSG_BACK ="返回上一頁";
 //email
  $MSG_SYS_WARN="系統警告！";
  $MSG_IS_ROBOT="疑似機器人，註意封禁！";
