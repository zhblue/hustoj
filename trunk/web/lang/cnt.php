<?php
 //header.php
 $MSG_FAQ ="常見問答";
 $MSG_BBS ="討論版";
 $MSG_HOME ="首頁";
 $MSG_PROBLEMS ="題目";
 $MSG_STATUS ="狀態";
 $MSG_RANKLIST ="排名";
 $MSG_CONTEST ="比賽&作業";
 $MSG_RECENT_CONTEST ="名校聯賽";
 $MSG_LOGOUT ="登出";
 $MSG_LOGIN ="登入";
 $MSG_LOST_PASSWORD ="忘記密碼";
 $MSG_REGISTER ="註冊";
 $MSG_ADMIN ="管理";
 $MSG_SYSTEM ="系統";
 $MSG_STANDING ="名次";
 $MSG_STATISTICS ="統計";
 $MSG_USERINFO ="使用者資訊";
 $MSG_MAIL ="短訊息";
 $MSG_TODO="待辦事項";
 //status.php
 $MSG_Pending ="等待";
 $MSG_Pending_Rejudging ="等待重判";
 $MSG_Compiling ="編譯中";
 $MSG_Running_Judging ="執行並評判";
 $MSG_Accepted ="正確";
 $MSG_Presentation_Error ="格式錯誤";
 $MSG_Wrong_Answer ="答案錯誤";
 $MSG_Time_Limit_Exceed ="時間超限";
 $MSG_Memory_Limit_Exceed ="記憶體超限";
 $MSG_Output_Limit_Exceed ="輸出超限";
 $MSG_Runtime_Error ="執行錯誤";
 $MSG_Compile_Error ="編譯錯誤";
 $MSG_Runtime_Click ="執行錯誤(點擊看詳細) ";
 $MSG_Compile_Click ="編譯錯誤(點擊看詳細) ";
 $MSG_Compile_OK ="編譯成功";
 $MSG_MANUAL_CONFIRMATION ="自動評測通過，等待人工確認";
 $MSG_Click_Detail ="點擊看詳細";
 $MSG_Manual ="人工判題";
 $MSG_OK ="確定";
 $MSG_Explain ="輸入判定原因與提示";
 $MSG_SUBMITTING="提交中";
 $MSG_REMOTE_PENDING="遠端等待";
 $MSG_REMOTE_JUDGING="遠端判題";	
 $MSG_RP="遠端等待";

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
 
 $MSG_TEST_RUN ="執行完成";
 
 $MSG_RUNID ="提交編號";
 $MSG_USER ="使用者";
 $MSG_PROBLEM ="題目";
 $MSG_RESULT ="結果";
 $MSG_MEMORY ="記憶體";
 //$MSG_TIME="耗時"; // overided by line 236
 $MSG_LANG ="語言";
 $MSG_CODE_LENGTH ="程式碼長度";
 $MSG_SUBMIT_TIME ="提交時間";
 
 //problemstatistics.php
 $MSG_PD ="等待";
 $MSG_PR ="等待重判";
 $MSG_CI ="編譯中";
 $MSG_RJ ="執行並評判";
 $MSG_AC ="正確";
 $MSG_PE ="格式錯誤";
 $MSG_WA ="答案錯誤";
 $MSG_TLE ="時間超限";
 $MSG_MLE ="記憶體超限";
 $MSG_OLE ="輸出超限";
 $MSG_RE ="執行錯誤";
 $MSG_CE ="編譯錯誤";
 $MSG_CO ="編譯成功";
 $MSG_TR ="測試執行";
 $MSG_MC ="待裁判確認";
 $MSG_RESET ="重設";
 
 //problemset.php
 $MSG_SEARCH ="搜尋";
 $MSG_PROBLEM_ID ="題目編號";
 $MSG_TITLE ="標題";
 $MSG_SOURCE ="來源/分類";
 $MSG_SUBMIT_NUM ="提交量";
 $MSG_SUBMIT ="提交";
 $MSG_SHOW_OFF ="露一手! ";
 
 //submit.php
 $MSG_VCODE_WRONG ="驗證碼錯誤！ ";
 $MSG_LINK_ERROR ="在哪裡可以找到此連結？ 沒有這個問題。 ";
 $MSG_PROBLEM_RESERVED ="題目已停用。 ";
 $MSG_NOT_IN_CONTEST ="您不能立即提交，因為您沒有被比賽邀請或比賽沒有進行！ ";
 $MSG_NOT_INVITED ="您不被邀請！ ";
 $MSG_NO_PROBLEM ="沒有這樣的問題！ ";
 $MSG_NO_PLS ="使用未知的程式語言！ ";
 $MSG_TOO_SHORT ="程式碼太短！ ";
 $MSG_TOO_LONG ="程式碼太長！ ";
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
 $MSG_ABSENT="缺席";
 //registerpage.php
 $MSG_USER_ID ="使用者名稱（學號） ";
 $MSG_PASSWORD ="密碼";
 $MSG_REPEAT_PASSWORD ="重複密碼";
 $MSG_SCHOOL ="學校";
 $MSG_GROUP_NAME="班級/小組";
 $MSG_EMAIL ="電子郵件";
 $MSG_REG_INFO ="設定註冊資訊";
 $MSG_VCODE ="驗證碼";
 $MSG_ACTIVE_YOUR_ACCOUNT="啟用您的帳號 ";
 $MSG_CLICK_COPY="點擊或複製連結在瀏覽器中開啟 ";
 $MSG_CHECK="查看 ";
 $MSG_OLD="舊";
 $MSG_DIFFERENT="不一致";
 $MSG_WRONG="錯誤";
 $MSG_TOO_LONG="太長";
 $MSG_TOO_SHORT="太短";
 $MSG_TOO_SIMPLE="太簡單";
 $MSG_TOO_BAD="請文明上網";
 //problem.php
 $MSG_NO_SUCH_PROBLEM ="題目目前無法使用!<br>它可能被用於未來的比賽、過去的私有比賽，或者管理員由於尚未測試通過等其他原因暫時停止了該題目用於練習。 ";
 $MSG_Description ="題目描述" ;
 $MSG_Input ="輸入" ;
 $MSG_Output = "輸出" ;
 $MSG_Sample_Input = "範例輸入" ;
 $MSG_Sample_Output = "範例輸出" ;
 $MSG_Test_Input = "測試輸入" ;
 $MSG_Test_Output = "測試輸出" ;
 $MSG_NJ = "普通裁判" ;
 $MSG_SPJ = "特殊裁判" ;
 $MSG_RTJ = "文字裁判" ;
 $MSG_HINT = "提示" ;
 $MSG_Source = "來源" ;
 $MSG_Time_Limit ="時間限制";
 $MSG_Memory_Limit ="記憶體限制";
 $MSG_EDIT ="編輯";
 $MSG_TESTDATA ="測試資料";
 $MSG_CLICK_VIEW_HINT ="點擊查看劇透級題解";
 
 //admin menu
 $MSG_VIEW_DISABLED_USER="檢視已停用或待審核使用者";
 $MSG_SEEOJ ="檢視前台";
 $MSG_ADD ="新增";
 $MSG_MENU ="選單";
 $MSG_EXPLANATION ="內容描述";
 $MSG_LIST ="清單";
 $MSG_NEWS ="公告";
 $MSG_CONTENTS ="內容";
 $MSG_SAVE ="儲存";
 $MSG_DELETED ="已刪除";
 $MSG_NORMAL ="正常";
 
 $MSG_TEAMGENERATOR ="比賽隊帳號產生器";
 $MSG_SETMESSAGE ="設定公告";
 $MSG_SETPASSWORD ="修改密碼";
 $MSG_REJUDGE ="重判題目";
 $MSG_PRIVILEGE ="權限";
 $MSG_GIVESOURCE ="轉移原始碼";
 $MSG_IMPORT ="匯入";
 $MSG_EXPORT ="匯出";
 $MSG_UPDATE_DATABASE ="更新資料庫";
 $MSG_BACKUP_DATABASE ="備份資料庫";
 $MSG_ONLINE ="線上";
 $MSG_SET_LOGIN_IP ="指定登入IP ";
 $MSG_PRIVILEGE_TYPE ="權限類型";
 $MSG_NEWS_MENU ="是否展示到選單";
 $MSG_LAST_LOGIN="最後登入";
 $MSG_OFFLINE_ZIP_IMPORT="匯入zip檔案，遵循下面的目錄結構:";
 $MSG_OFFLINE="離線";
 $MSG_EXPIRY_DATE="有效期限";
 $MSG_CLICK_TO_DELETE="點擊刪除";
 $MSG_CLICK_TO_RECOVER="點擊恢復";
 //contest.php
 $MSG_PRIVATE_WARNING ="比賽尚未開始或私有，不能檢視題目。 ";
 $MSG_PRIVATE_USERS_ADD =" *可以將學生學號從Excel整列複製過來，然後要求他們用學號做UserID註冊,就能進入Private的比賽作為作業和測驗。 ";
 $MSG_PLS_ADD =" *請選擇所有可以透過Ctrl +點擊提交的語言。 ";
 $MSG_TIME_WARNING ="比賽開始前。 ";
 $MSG_WATCH_RANK ="點擊這裡檢視做題排名。 ";
 $MSG_NOIP_WARNING = $OJ_NOIP_KEYWORD ."比賽進行中，結束後才能檢視結果。 ";
 $MSG_NOIP_NOHINT = $OJ_NOIP_KEYWORD ."比賽,不顯示提示資訊。 ";
 $MSG_SERVER_TIME ="伺服器時間";
 $MSG_START_TIME ="開始時間";
 $MSG_END_TIME ="結束時間";
 $MSG_VIEW_ALL_CONTESTS ="顯示所有作業比賽";
 $MSG_VIEW_MY_CONTESTS="我的作業比賽";
 $MSG_CONTEST_ID ="作業比賽編號";
 $MSG_CONTEST_NAME ="作業比賽名稱";
 $MSG_CONTEST_STATUS ="作業比賽狀態";
 $MSG_CONTEST_OPEN ="開放";
 $MSG_CONTEST_CREATOR ="建立人";
 $MSG_CONTEST_PENALTY ="累計時間";
 $MSG_IP_VERIFICATION =" IP驗證";
 $MSG_LOG="日誌";
 $MSG_SUSPECT="審計";
 $MSG_CONTEST_SUSPECT1 ="具有多個ID的IP位址。如果在競賽/考試期間在同一台電腦上存取多個ID，則會記錄該ID。 ";
 $MSG_CONTEST_SUSPECT2 ="具有多個IP位址的ID。 如果在競賽/考試期間切換到另一台電腦，它將記錄下來。 ";
 
 $MSG_SECONDS ="秒";
 $MSG_MINUTES ="分";
 $MSG_HOURS ="小時";
 $MSG_DAYS ="天";
 $MSG_MONTHS ="月份";
 $MSG_YEARS ="年份";
 
 $MSG_Public ="公開";
 $MSG_Private ="私有";
 $MSG_Running ="執行中";
 $MSG_Start ="開始於";
 $MSG_End ="結束於";
 $MSG_TotalTime ="總賽時";
 $MSG_LeftTime ="剩餘";
 $MSG_Ended ="已結束";
 $MSG_Login ="請登入後繼續操作";
 $MSG_JUDGER ="判題機";
 $MSG_DOWNLOAD="下載";
 $MSG_SHOW="顯示";
 $MSG_HIDE="隱藏";

 $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM ="考試期間，不能查閱以前提交的程式碼。 ";
 $MSG_BBS_NOT_ALLOWED_FOR_EXAM ="考試期間,討論版禁用。 ";
 $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM ="考試期間,禁止修改帳號資訊。 ";
 $MSG_MAIL_NOT_ALLOWED_FOR_EXAM ="考試期間,內郵禁用。 ";
 $MSG_LOAD_TEMPLATE_CONFIRM ="是否載入預設樣板? \\ n 如果選擇是，目前程式碼將被覆蓋! ";
 $MSG_NO_MAIL_HERE ="本OJ不支援內部郵件哦~ ";
 
 $MSG_BLOCKLY_OPEN ="視覺化";
 $MSG_BLOCKLY_TEST ="翻譯執行";
 $MSG_MY_SUBMISSIONS ="我的提交";
 $MSG_MY_CONTESTS ="我的$MSG_CONTEST ";
 $MSG_Creator ="命題人";
 $MSG_IMPORTED ="外部匯入";
 $MSG_PRINTER ="列印";
 $MSG_PRINT_DONE ="列印完成";
 $MSG_PRINT_PENDING ="提交成功,待列印";
 $MSG_PRINT_WAITING ="請耐心等候，不要重複提交相同的列印任務";
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
 $MSG_TABLE_TRANSPOSE="行列轉換";

 $MSG_HELP_SEEOJ ="跳轉回到前台";
 $MSG_HELP_ADD_NEWS ="新增首頁顯示的新聞";
 $MSG_HELP_NEWS_LIST ="管理已經發佈的新聞";
 $MSG_HELP_USER_LIST ="對註冊使用者停用、啟用帳號";
 $MSG_HELP_USER_ADD ="新增使用者";
 $MSG_HELP_ADD_PROBLEM ="手動新增新的題目，多組測試資料在新增後從題目清單TestData按鈕進入上傳，新建題目<b>預設隱藏</b>，需在問題清單中點擊紅色<font color='red'> $MSG_RESERVED </font>切換為綠色<font color='green'>Available</font>啟用。。 ";
 $MSG_HELP_PROBLEM_LIST ="管理已有的題目和資料，上傳資料可以用zip壓縮不含目錄的資料。 ";
 $MSG_HELP_ADD_CONTEST ="規劃新的比賽，用逗號分隔題號。可以設定私有比賽，用密碼或名單限制參與者。 ";
 $MSG_HELP_CONTEST_LIST ="已有的比賽清單，修改時間和公開/私有，盡量不要在開賽後調整題目清單。 ";
 $MSG_HELP_SET_LOGIN_IP ="記錄考試期間的電腦(登入IP)更改。 ";
 $MSG_HELP_TEAMGENERATOR ="批次產生大量比賽帳號、密碼，用於來自不同學校的參賽者。小系統不要隨便使用，可能產生垃圾帳號，無法刪除。 ";
 $MSG_HELP_SETMESSAGE ="設定捲動公告內容";
 $MSG_HELP_SETPASSWORD ="重設指定使用者的密碼，對於管理員帳號需要先降級為普通使用者才能修改。 ";
 $MSG_HELP_REJUDGE ="重判指定的題目、提交或比賽。 ";
 $MSG_HELP_ADD_PRIVILEGE ="給指定使用者增加權限，包括管理員、題目新增者、比賽組織者、比賽參加者、程式碼檢視者、手動判題、遠端判題、列印員、氣球發放員等權限。 ";
 $MSG_HELP_ADD_CONTEST_USER ="給使用者新增單個比賽權限。 ";
 $MSG_HELP_ADD_SOLUTION_VIEW ="給使用者新增單個題目的答案檢視權限。 ";
 $MSG_HELP_PRIVILEGE_LIST ="檢視已有的特殊權限清單、進行刪除操作。 ";
 $MSG_HELP_GIVESOURCE ="將匯入系統的標程贈與指定帳號，用於訓練後輔助未通過的人學習參考。 ";
 $MSG_HELP_EXPORT_PROBLEM ="將系統中的題目以fps.xml檔案的形式匯出。 ";
 $MSG_HELP_IMPORT_PROBLEM ="匯入從官方群共享或tk.hustoj.com下載到的fps.xml檔案。 ";
 $MSG_HELP_UPDATE_DATABASE ="更新資料庫結構，在每次升級（sudo update-hustoj）之後或者匯入老系統資料庫備份，應至少操作一次。 ";
 $MSG_HELP_ONLINE ="檢視線上使用者";
 $MSG_HELP_AC ="答案正確，請再接再厲。 ";
 $MSG_HELP_PE ="答案基本正確，但是格式不對。 ";
 $MSG_HELP_WA ="答案不對，僅僅透過範例資料的測試並不一定是正確答案，一定還有你沒想到的地方，點擊檢視系統可能提供的對比資訊。 ";
 $MSG_HELP_TLE ="執行超出時間限制，檢查下是否有死迴圈，或者應該有更快的計算方法";
 $MSG_HELP_MLE ="超出記憶體限制，資料可能需要壓縮，檢查記憶體是否有洩漏";
 $MSG_HELP_OLE ="輸出超過限制，你的輸出比正確答案長了兩倍，一定是哪裡弄錯了";
 $MSG_HELP_RE ="執行時錯誤，非法的記憶體存取，陣列越界，指標漂移，呼叫禁用的系統函數。請點擊後獲得詳細輸出";
 $MSG_HELP_CE ="編譯錯誤，請點擊後獲得編譯器的詳細輸出";
 
 $MSG_HELP_MORE_TESTDATA_LATER ="更多組測試資料，請在題目新增完成後補充";
 $MSG_HELP_ADD_FAQS ="管理員可以新增一條新聞，命名為\" faqs. $OJ_LANG \"來取代<a href=../faqs.php> $MSG_FAQ </a>的內容。 ";
 $MSG_HELP_HUSTOJ =" <sub><a target='_blank' href='https://github.com/zhblue/hustoj'><span class='glyphicon glyphicon-heart' aria-hidden='true'></span> 請到HUSTOJ 來，給我們加個<span class='glyphicon glyphicon-star' aria-hidden='true'></span>Star!</a></sub> ";
 $MSG_HELP_SPJ ="特殊裁判的使用，請參考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜尋hustoj special judge</a> ";
 $MSG_HELP_BALLOON_SCHOOL ="列印，氣球帳號的School欄位用於過濾任務清單，例如填zjicm則只顯示帳號為zjicm開頭的任務";
 $MSG_HRLP_BACKUP_DATABASE ="備份資料庫,測試資料和圖片到0題目錄";
 $MSG_HELP_LEFT_EMPTY="無需修改密碼，請勿填寫此項";
 $MSG_HELP_LOCAL_EMPTY="本地題請留空";
 $MSG_WARNING_LOGIN_FROM_DIFF_IP ="從不同的ip位址登入";
 $MSG_WARNING_DURING_EXAM_NOT_ALLOWED ="在考試期間不被允許";
 $MSG_WARNING_ACCESS_DENIED ="抱歉，您無法檢視此訊息！因為它不屬於您，或者管理員設定系統狀態為不顯示此類資訊。 ";
 
 $MSG_WARNING_USER_ID_SHORT ="使用者名稱至少3位字元! ";
 $MSG_WARNING_PASSWORD_SHORT ="密碼至少6位! ";
 $MSG_WARNING_REPEAT_PASSWORD_DIFF ="兩次輸入的密碼不一致! ";
 
 
 $MSG_LOSTPASSWORD_MAILBOX ="請到您郵箱的垃圾郵件檔案夾尋找驗證碼，並填寫到這裡";
 $MSG_LOSTPASSWORD_WILLBENEW ="如果填寫正確，透過下一步驗證，這個驗證碼就自動成為您的新密碼！ ";
 

  //discuss.php
  $MSG_LAST_REPLY="最新回覆";
  $MSG_REPLY_COUNTS="回覆總數";
  $MSG_REPLY_NUMBER="回覆計數";
  $MSG_QUESTION="帖子";
  $MSG_NO_QUESTIONS="沒有帖子";
  $MSG_REGISTER_QUESTION="發佈帖子";
  $MSG_WRITE_QUESTION="發帖";
  $MSG_REGISTERED="已發佈";
  $MSG_BLOCKED="已屏蔽";
  $MSG_REPLY="回覆";
  $MSG_REGISTER_REPLY="發佈回覆";
  $MSG_DISABLE="停用";
  $MSG_LOCK="鎖定";
  $MSG_RESUME="恢復";
  $MSG_DISCUSS_DELETE="刪除";
  $MSG_DISCUSS_NOTICE="提示";
  $MSG_DISCUSS_NOTE="筆記";
  $MSG_DISCUSS_NORMAL="普通";

 
 // template/../reinfo.php
 $MSG_A_NOT_ALLOWED_SYSTEM_CALL ="使用了系統禁止的作業系統呼叫，看看是否越權存取了檔案或行程等資源,如果你是系統管理員，而且確認提交的答案沒有問題，測試資料沒有問題，可以發送'RE'到微信公眾號onlinejudge，檢視解決方案。 ";
 $MSG_SEGMETATION_FAULT ="段錯誤，檢查是否有陣列越界，指標異常，存取到不應該存取的記憶體區域";
 $MSG_FLOATING_POINT_EXCEPTION ="浮點錯誤，檢查是否有除以零的情況";
 $MSG_BUFFER_OVERFLOW_DETECTED ="緩衝區溢位，檢查是否有字串長度超出陣列的情況";
 $MSG_PROCESS_KILLED ="行程因為記憶體或時間原因被殺死，檢查是否有死迴圈";
 $MSG_ALARM_CLOCK ="行程因為時間原因被殺死，檢查是否有死迴圈，本錯誤等價於超時TLE ";
 $MSG_CALLID_20 ="可能存在陣列越界，檢查題目描述的資料量與所申請陣列大小關係";
 $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION ="檢查陣列越界的情況";
 $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION ="字串的字元下標越界，檢查subString,charAt等方法的參數";
 $MSG_WRONG_OUTPUT_TYPE_EXCEPTION="二進位輸出錯誤，檢查是否誤將數值類型作為字元輸出，或者輸出了不列印字元的情況。";
 $MSG_NON_ZERO_RETURN="Main函數不能返回非零的值，否則視同程式出錯。";
  $MSG_EXPECTED="期望值";
  $MSG_YOURS="你的程式輸出";
  $MSG_FILENAME="檔案名稱";
  $MSG_SIZE="大小";

 // template/../ceinfo.php
 $MSG_ERROR_EXPLAIN ="輔助解釋";
 $MSG_SYSTEM_OUT_PRINT =" Java中System.out.print用法跟C語言printf不同，請試用System.out.format ";
 $MSG_NO_SUCH_FILE_OR_DIRECTORY ="伺服器為Linux系統，不能使用Windows下特有的非標準標頭檔案。 ";
 $MSG_NOT_A_STATEMENT ="檢查大括號{}匹配情況，eclipse整理程式碼快捷鍵Ctrl+Shift+F ";
 $MSG_EXPECTED_CLASS_INTERFACE_ENUM ="請不要將Java函數（方法）放置在類別宣告外部，注意大括號的結束位置} ";
 $MSG_SUBMIT_JAVA_AS_C_LANG ="請不要將Java程式提交為C語言";
 $MSG_DOES_NOT_EXIST_PACKAGE ="檢測拼寫，如：系統物件System為大寫S開頭";
 $MSG_POSSIBLE_LOSS_OF_PRECISION ="賦值將會失去精度，檢測資料類型，如確定無誤可以使用強制類型轉換";
 $MSG_INCOMPATIBLE_TYPES =" Java中不同類型的資料不能互相賦值，整數不能用作布林值";
 $MSG_ILLEGAL_START_OF_EXPRESSION ="字串應用英文雙引號( \\\" )引起";
 $MSG_CANNOT_FIND_SYMBOL ="拼寫錯誤或者缺少呼叫函數所需的物件如println()需對System.out呼叫";
 $MSG_EXPECTED_SEMICOLON ="缺少分號。 ";
 $MSG_DECLARED_JAVA_FILE_NAMED =" Java必須使用public class Main。 ";
 $MSG_EXPECTED_WILDCARD_CHARACTER_AT_END_OF_INPUT ="程式碼沒有結束，缺少匹配的括號或分號，檢查複製時是否選中了全部程式碼。 ";
 $MSG_INVALID_CONVERSION ="隱含的類型轉換無效，嘗試用顯示的強制類型轉換如(int *)malloc(....) ";
 $MSG_NO_RETURN_TYPE_IN_MAIN =" C++標準中，main函數必須有返回值";
 $MSG_NOT_DECLARED_IN_SCOPE ="變數沒有宣告過，檢查下是否拼寫錯誤！ ";
 $MSG_MAIN_MUST_RETURN_INT ="在標準C語言中，main函數返回值類型必須是int，教材和VC中使用void是非標準的用法";
 $MSG_PRINTF_NOT_DECLARED_IN_SCOPE =" printf函數沒有宣告過就進行呼叫，檢查下是否匯入了stdio.h或cstdio標頭檔案";
 $MSG_IGNOREING_RETURN_VALUE ="警告：忽略了函數的返回值，可能是函數用錯或者沒有考慮到返回值異常的情況";
 $MSG_NOT_DECLARED_INT64 =" __int64沒有宣告，在標準C/C++中不支援微軟VC中的__int64,請使用long long來宣告64位變數";
 $MSG_EXPECTED_SEMICOLON_BEFORE ="前一行缺少分號";
 $MSG_UNDECLARED_NAME ="變數使用前必須先進行宣告，也有可能是拼寫錯誤，注意大小寫區分。 ";
 $MSG_SCANF_NOT_DECLARED_IN_SCOPE =" scanf函數沒有宣告過就進行呼叫，檢查下是否匯入了stdio.h或cstdio標頭檔案";
 $MSG_MEMSET_NOT_DECLARED_IN_SCOPE =" memset函數沒有宣告過就進行呼叫，檢查下是否匯入了stdlib.h或cstdlib標頭檔案";
 $MSG_MALLOC_NOT_DECLARED_IN_SCOPE =" malloc函數沒有宣告過就進行呼叫，檢查下是否匯入了stdlib.h或cstdlib標頭檔案";
 $MSG_PUTS_NOT_DECLARED_IN_SCOPE =" puts函數沒有宣告過就進行呼叫，檢查下是否匯入了stdio.h或cstdio標頭檔案";
 $MSG_GETS_NOT_DECLARED_IN_SCOPE =" gets函數沒有宣告過就進行呼叫，檢查下是否匯入了stdio.h或cstdio標頭檔案";
 $MSG_STRING_NOT_DECLARED_IN_SCOPE =" string類函數沒有宣告過就進行呼叫，檢查下是否匯入了string.h或cstring標頭檔案";
 $MSG_NO_TYPE_IMPORT_IN_C_CPP ="不要將Java語言程式提交為C/C++,提交前注意選擇語言類型。 ";
 $MSG_ASM_UNDECLARED ="不允許在C/C++中嵌入組合語言程式碼。 ";
 $MSG_REDEFINITION_OF ="函數或變數重複定義，看看是否多次貼上程式碼。 ";
 $MSG_EXPECTED_DECLARATION_OR_STATEMENT ="程式好像沒寫完，看看是否複製貼上時漏掉程式碼。 ";
 $MSG_UNUSED_VARIABLE ="警告：變數宣告後沒有使用，檢查下是否拼寫錯誤，誤用了名稱相似的變數。 ";
 $MSG_IMPLICIT_DECLARTION_OF_FUNCTION ="函數隱性宣告，檢查下是否匯入了正確的標頭檔案。或者缺少了題目要求的指定名稱的函數。 ";
 $MSG_ARGUMENTS_ERROR_IN_FUNCTION ="函數呼叫時提供的參數數量不對，檢查下是否用錯參數。 ";
 $MSG_EXPECTED_BEFORE_NAMESPACE ="不要將C++語言程式提交為C,提交前注意選擇語言類型。 ";
 $MSG_STRAY_PROGRAM ="中文空格、標點等不能出現在程式中註釋和字串以外的部分。編寫程式時請關閉中文輸入法。請不要使用網上複製來的程式碼。 ";
 $MSG_DIVISION_BY_ZERO ="除以零將導致浮點溢位。 ";
 $MSG_CANNOT_BE_USED_AS_A_FUNCTION ="變數不能當成函數用，檢查變數名和函數名重複的情況，也可能是拼寫錯誤。 ";
 $MSG_CANNOT_FIND_TYPE =" scanf/printf的格式描述和後面的參數表不一致，檢查是否多了或少了取址符\"&\"，也可能是拼寫錯誤。 ";
 $MSG_JAVA_CLASS_ERROR =" Java語言提交只能有一個public類別，並且類別名稱必須是Main，其他類別請不要用public關鍵詞";
 $MSG_EXPECTED_BRACKETS_TOKEN ="缺少右括號";
 $MSG_NOT_FOUND_SYMBOL ="使用了未定義的函數或變數，檢出拼寫是否有誤，不要使用不存在的函數，Java呼叫方法通常需要給出物件名稱如list1.add(...)。Java方法呼叫時對參數類型敏感，如:不能將整數(int)傳送給接受字串物件(String)的方法";
 $MSG_NEED_CLASS_INTERFACE_ENUM ="缺少關鍵字，應當宣告為class、interface 或enum ";
 $MSG_CLASS_SYMBOL_ERROR ="使用教材上的例子，必須將相關類別的程式碼一併提交，同時去掉其中的public關鍵詞";
 $MSG_INVALID_METHOD_DECLARATION ="只有跟類別名稱相同的方法為建構函數，不寫返回值類型。如果將類別名稱修改為Main,請同時修改建構函數名稱。 ";
 $MSG_EXPECTED_AMPERSAND_TOKEN ="不要將C++語言程式提交為C,提交前注意選擇語言類型。 ";
 $MSG_DECLARED_FUNCTION_ORDER ="請注意函數、方法的宣告前後順序，不能在一個方法內出現另一個方法的宣告。 ";
 $MSG_NEED_SEMICOLON ="上面標註的這一行，最後缺少分號。 ";
 $MSG_EXTRA_TOKEN_AT_END_OF_INCLUDE =" include語句必須獨立一行，不能與後面的語句放在同一行";
 $MSG_INT_HAS_NEXT =" hasNext() 應該改為nextInt() ";
 $MSG_UNTERMINATED_COMMENT ="註釋沒有結束，請檢查\"/*\"對應的結束符\"*/\"是否正確";
 $MSG_EXPECTED_BRACES_TOKEN ="函數宣告缺少小括號()，如int main()寫成了int main ";
 $MSG_REACHED_END_OF_FILE_1 ="檢查提交的原始碼是否沒有複製完整，或者缺少了結束的大括號";
 $MSG_SUBSCRIPT_ERROR ="不能對非陣列或指標的變數進行下標存取";
 $MSG_EXPECTED_PERCENT_TOKEN =" scanf的格式部分需要用雙引號引起";
 $MSG_EXPECTED_EXPRESSION_TOKEN ="參數或表達式沒寫完";
 $MSG_EXPECTED_BUT ="錯誤的標點或符號";
 $MSG_REDEFINITION_MAIN ="這道題目可能是附加程式碼題，請重新審題，看清題意，不要提交系統已經定義的main函數，而應提交指定格式的某個函數。 ";
 $MSG_IOSTREAM_ERROR ="請不要將C++程式提交為C ";
 $MSG_EXPECTED_UNQUALIFIED_ID_TOKEN ="留意陣列宣告後是否少了分號";
 $MSG_REACHED_END_OF_FILE_2 ="程式末尾缺少大括號";
 $MSG_INVALID_SYMBOL ="檢查是否使用了中文標點或空格";
 $MSG_DECLARED_FILE_NAMED =" OJ中public類別只能是Main ";
 $MSG_EXPECTED_IDENTIFIER ="宣告變數時，可能沒有宣告變數名或缺少括號。 ";
 $MSG_VARIABLY_MODIFIED ="陣列大小不能用變數，C 語言中不能使用變數作為全域陣列的維度大小，包括const 變數";
 $MSG_FUNCTION_GETS_REMOVIED =" std::gets 於C++11 被棄用，並於C++14 移除。可使用std::fgets 替代。或者增加巨集定義#define gets(S) fgets(S,sizeof(S),stdin) ";
 $MSG_PROBLEM_USED_IN ="題目已經用於私有比賽";
 $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT ="內郵僅限學生老師互相發送，不允許同學間發送！ ";
 $MSG_COPY_USER_LIST_FROM_CONTEST="選擇一個比賽複製學生名單...";
 $MSG_REFRESH_PRIVILEGE ="重新整理權限";
 
 $MSG_SAVED_DATE ="儲存時間";
 $MSG_PROBLEM_STATUS ="目前狀態";
 
 $MSG_NEW_CONTEST ="建立新比賽";
 $MSG_AVAILABLE ="啟用";
 $MSG_RESERVED ="未啟用";
 $MSG_NEW_PROBLEM_LIST ="建立新題單";
 $MSG_DELETE ="刪除";
 $MSG_EDIT ="編輯";
 $MSG_TEST_DATA ="管理測試資料";
 $MSG_CHECK_TO ="批次選擇操作";
 
 //bbcode.php
 $MSG_TOTAL ="共";
 $MSG_NUMBER_OF_PROBLEMS ="題";
   $MSG_GLOBAL="全域";
   $MSG_THIS_CONTEST="本次比賽";
 $MSG_SUBMIT_RECORD ="提交記錄";
 $MSG_RETURN_CONTEST ="返回比賽";
 $MSG_COPY ="複製";
 $MSG_SUCCESS ="成功";
 $MSG_FAIL ="失敗";
 $MSG_TEXT_COMPARE ="文字比較";
 $MSG_JUDGE_STYLE ="評測方式";
 // reinfo.php
 $MSG_ERROR_INFO ="錯誤資訊";
 $MSG_INFO_EXPLAINATION ="輔助解釋";
 // ceinfo.php
 $MSG_COMPILE_INFO ="編譯資訊";
 $MSG_SOURCE_CODE ="原始碼";
 //contest.php
 $MSG_Contest_Pending ="未開始";
 $MSG_Server_Time ="目前時間";
 $MSG_Contest_Infomation ="資訊與公告";
 // sourcecompare.php
 $MSG_Source_Compare ="原始碼對比";
 $MSG_BACK ="返回上一頁";
  $MSG_NEXT_PAGE="下一頁";
  $MSG_PREV_PAGE="上一頁";
 //email
  $MSG_SYS_WARN="系統警告！";
  $MSG_IS_ROBOT="疑似機器人，注意封禁！";
   $MSG_FORBIDDEN="禁止";
  $MSG_OTHERS="其他人";
  $MSG_SUBNET="子網";
  $MSG_ONLY_LAST_SUBMISSION="僅以最後一次提交記分";
  $MSG_AI_HELP="人工智慧(AI)錯誤解析";
  $MSG_SHOW_DIFF="顯示對比輸出";
  $MSG_UPSOLVING="補題";
  //SaaS friendly
  $MSG_TEMPLATE="樣板";
  $MSG_FRIENDLY_LEVEL="友善級別";
  $MSG_FRIENDLY_L0="不友善";
  $MSG_FRIENDLY_L1="中國時區";
  $MSG_FRIENDLY_L2="強制中文";
  $MSG_FRIENDLY_L3="顯示對比,關閉驗證碼";
  $MSG_FRIENDLY_L4="開啟內郵,程式碼自動分享";
  $MSG_FRIENDLY_L5="開啟測試執行";
  $MSG_FRIENDLY_L6="保持登入狀態";
  $MSG_FRIENDLY_L7="開啟討論版";
  $MSG_FRIENDLY_L8="可以下載測試資料";
  $MSG_FRIENDLY_L9="允許訪客提交";
