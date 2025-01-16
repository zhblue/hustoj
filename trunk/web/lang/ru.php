<?php
 //oj-header.php
 $MSG_FAQ = " Часто задаваемые вопросы ";
 $MSG_BBS = " доска обсуждений ";
 $MSG_HOME = " дом ";
 $MSG_PROBLEMS =" проблемы ";
 $MSG_STATUS = " статус ";
 $MSG_RANKLIST = " ранг ";
 $MSG_CONTEST = " Конкурсы и задания ";
 $MSG_RECENT_CONTEST = " Престижная лига ";
 $MSG_LOGOUT = " выйти из системы ";
 $MSG_LOGIN =" логин ";
 $MSG_LOST_PASSWORD =" Забыли пароль ";
 $MSG_REGISTER = " зарегистрироваться ";
 $MSG_ADMIN = " админ ";
 $MSG_SYSTEM =" система ";
 $MSG_STANDING = " Рейтинг ";
 $MSG_STATISTICS = " статистика ";
 $MSG_USERINFO = " Информация о пользователе ";
 $MSG_MAIL = " Короткое сообщение ";
 $MSG_TODO="ToDo";
 //status.php
 $MSG_Pending = " ожидание ";
 $MSG_Pending_Rejudging =" Ожидание повторного рассмотрения ";
 $MSG_Compiling = " Компиляция ";
 $MSG_Running_Judging =" Бег и оценка ";
 $MSG_Accepted = " правильно ";
 $MSG_Presentation_Error = " Ошибка формата ";
 $MSG_Wrong_Answer = " Неверный ответ ";
 $MSG_Time_Limit_Exceed = " Превышен лимит времени ";
 $MSG_Memory_Limit_Exceed = " Память превышена ";
 $MSG_Output_Limit_Exceed =" Превышен предел вывода ";
 $MSG_Runtime_Error = " Ошибка выполнения ";
 $MSG_Compile_Error = " Ошибка компиляции ";
 $MSG_Runtime_Click = " Ошибка выполнения (нажмите, чтобы увидеть подробности) ";
 $MSG_Compile_Click = " Ошибка компиляции (нажмите, чтобы увидеть подробности) ";
 $MSG_Compile_OK = " Компилируется успешно ";
 $MSG_MANUAL_CONFIRMATION = " Автоматическая оценка пройдена, ожидается ручное подтверждение ";
 $MSG_Click_Detail = " Нажмите, чтобы увидеть подробности ";
 $MSG_Manual = " оценка вручную ";
 $MSG_OK = " ОК ";
 $MSG_Explain = " Введите причину суждения и подсказку ";
 
 //день дурака
 если (дата( 'm' )== 4 &&дата( 'd' )== 1 &&rand( 0 , 100 )< 5 ){
 $MSG_Accepted =" <span title=Happy April Fools>Кажется, правильно</span> ";
 //$MSG_Presentation_Error="Вопрос персонажа - С Днем дурака";
 //$MSG_Wrong_Answer="Вопрос персонажа - С Днем дурака";
 //$MSG_Time_Limit_Exceed="Вопрос персонажа - С Днем дурака";
 //$MSG_Memory_Limit_Exceed="Вопрос персонажа - С Днем дурака";
 //$MSG_Output_Limit_Exceed="Вопрос персонажа - С Днем дурака";
 //$MSG_Runtime_Error="Вопрос персонажа - С Днем дурака";
 //$MSG_Compile_Error="Вопрос персонажа - С Днем дурака";
 //$MSG_Compile_OK="Вопрос персонажа - С Днем дурака";
}
 
 $MSG_TEST_RUN = " Выполнение завершено ";
 
 $MSG_RUNID = " номер фиксации ";
 $MSG_USER =" пользователь ";
 $MSG_PROBLEM =" проблема ";
 $MSG_RESULT =" результат ";
 $MSG_MEMORY =" память ";
 //$MSG_TIME="timed"; // перекрывается строкой 236
 $MSG_LANG = " язык ";
 $MSG_CODE_LENGTH =" длина кода ";
 $MSG_SUBMIT_TIME = " Время отправки ";
 
 //проблемная статистика.php
 $MSG_PD =" ожидание ";
 $MSG_PR =" Ожидание повторной попытки ";
 $MSG_CI = " Компиляция ";
 $MSG_RJ = " беги и суди ";
 $MSG_AC = " правильно ";
 $MSG_PE =" Ошибка формата ";
 $MSG_WA =" Неверный ответ ";
 $MSG_TLE =" Превышен лимит времени ";
 $MSG_MLE = " Переполнение памяти ";
 $MSG_OLE =" Переполнение вывода ";
 $MSG_RE = " Ошибка выполнения ";
 $MSG_CE = " ошибка компиляции ";
 $MSG_CO =" успешно скомпилировано ";
 $MSG_TR =" тестовый запуск ";
 $MSG_MC = " Подлежит подтверждению судьей ";
 $MSG_RESET =" сброс ";
 
 //проблемсет.php
 $MSG_SEARCH = " Поиск ";
 $MSG_PROBLEM_ID = " Идентификатор проблемы ";
 $MSG_TITLE = " название ";
 $MSG_SOURCE = " источник/категория ";
 $MSG_SUBMIT_NUM = " Отправить сумму ";
 $MSG_SUBMIT = " Отправить ";
 $MSG_SHOW_OFF = " Покажите свои руки ! ";
 
 //отправить.php
 $MSG_VCODE_WRONG =" Ошибка кода подтверждения! ";
 $MSG_LINK_ERROR =" Где найти эту ссылку? С этим проблем нет. ";
 $MSG_PROBLEM_RESERVED =" Проблема отключена. ";
 $MSG_NOT_IN_CONTEST =" Вы не можете отправить заявку сейчас, потому что вы не приглашены на конкурс или конкурс не проводится! ";
 $MSG_NOT_INVITED =" Вы не приглашены! ";
 $MSG_NO_PROBLEM =" Такой проблемы нет! ";
 $MSG_NO_PLS =" Использование неизвестного языка программирования! ";
 $MSG_TOO_SHORT =" Код слишком короткий! ";
 $MSG_TOO_LONG =" Код слишком длинный! ";
 $MSG_BREAK_TIME =" Вы не должны отправлять более 1 заявки за 10 секунд..... ";
 
 //ranklist.php
 $MSG_Number = " Ранг ";
 $MSG_NICK =" псевдоним ";
 $MSG_SOVLED = " решить ";
 $MSG_RATIO =" коэффициент ";
 $MSG_DAY = " Рейтинг дня ";
 $MSG_WEEK =" Рейтинг недели ";
 $MSG_MONTH = " Рейтинг месяца ";
 $MSG_YEAR =" Рейтинг года ";
 $MSG_ABSENT="Absent";
 //registerpage.php
 $MSG_USER_ID =" имя пользователя (идентификатор учащегося) ";
 $MSG_PASSWORD =" пароль ";
 $MSG_REPEAT_PASSWORD = " Повторить пароль ";
 $MSG_SCHOOL = " школа ";
 $MSG_GROUP_NAME="Group";
 $MSG_EMAIL = " электронная почта ";
 $MSG_REG_INFO =" Установить регистрационную информацию ";
 $MSG_VCODE = " Проверочный код ";
 $MSG_ACTIVE_YOUR_ACCOUNT="Active Your Account ";
 $MSG_CLICK_COPY="Click or Copy the LINK to open in browser ";
 $MSG_CHECK="Check out ";
	$MSG_OLD="Old";
	$MSG_DIFFERENT="Different";
	$MSG_WRONG="Wrong";
	$MSG_TOO_LONG="Too long";
	$MSG_TOO_SHORT="Too short";
	$MSG_TOO_SIMPLE="Too simple";
	$MSG_TOO_BAD="Please surf the internet in a civilized manner";

 //проблема.php
 $MSG_NO_SUCH_PROBLEM =" Проблема в настоящее время недоступна!<br>Она может быть использована для будущих соревнований, прошлых частных соревнований, или администратор временно остановил вопрос для практики по другим причинам, например, из-за того, что тест еще не пройден. ";
 $MSG_Description = " описание темы ";
 $MSG_Input =" ввод ";
 $MSG_Output = " Вывод ";
 $MSG_Sample_Input = " Образец ввода ";
 $MSG_Sample_Output = " Вывод образца ";
 $MSG_Test_Input = " Проверка ввода ";
 $MSG_Test_Output = " Вывод теста ";
 $MSG_NJ =" Обычный судья ";
 $MSG_SPJ =" Специальный судья ";
 $MSG_RTJ = " Судья по тексту ";
 $MSG_HINT = " Подсказка ";
 $MSG_Source = " Источник ";
 $MSG_Time_Limit = " лимит времени ";
 $MSG_Memory_Limit = " лимит памяти ";
 $MSG_EDIT = " редактировать ";
 $MSG_TESTDATA =" Тестовые данные ";
 $MSG_CLICK_VIEW_HINT =" Щелкните, чтобы просмотреть решение на уровне спойлера ";
 
 //меню администратора
 $MSG_SEEOJ = " просмотреть передний план ";
 $MSG_ADD = " добавить ";
 $MSG_MENU =" меню ";
 $MSG_EXPLANATION =" Описание содержимого ";
 $MSG_LIST =" список ";
 $MSG_NEWS = " Объявления ";
 $MSG_CONTENTS = " контент ";
 $MSG_SAVE =" сохранить ";
 $MSG_DELETED =" Удалено ";
 $MSG_NORMAL = " нормальный ";
 
 $MSG_TEAMGENERATOR =" Генератор командных учетных записей ";
 $MSG_SETMESSAGE =" установка объявления ";
 $MSG_SETPASSWORD = " изменить пароль ";
 $MSG_REJUDGE =" Повторный вопрос ";
 $MSG_PRIVILEGE = " Разрешения ";
 $MSG_GIVESOURCE = " источник передачи ";
 $MSG_IMPORT =" импорт ";
 $MSG_EXPORT =" экспорт ";
 $MSG_UPDATE_DATABASE =" обновить базу данных ";
 $MSG_BACKUP_DATABASE =" резервная копия базы данных ";
 $MSG_ONLINE = " Онлайн ";
 $MSG_SET_LOGIN_IP =" указать IP для входа ";
 $MSG_PRIVILEGE_TYPE =" тип привилегии ";
 $MSG_NEWS_MENU = " Показывать ли в меню ";
 $MSG_LAST_LOGIN="Last Login";
 $MSG_OFFLINE_ZIP_IMPORT="Import a offline contest ZIP file, which using the following structure: ";
 $MSG_OFFLINE="Offline";
 $MSG_EXPIRY_DATE="Expiry Date";
 $MSG_CLICK_TO_DELETE="Click to delete";
 $MSG_CLICK_TO_RECOVER="Click to recover";


 //конкурс.php
 $MSG_PRIVATE_WARNING =" Конкурс еще не начался или является закрытым, поэтому вопросы не могут быть просмотрены. ";
 $MSG_PRIVATE_USERS_ADD = " *Вы можете скопировать идентификатор учащегося из столбца Excel, а затем попросить их использовать идентификатор учащегося для регистрации в качестве идентификатора пользователя, а затем они могут принять участие в частном конкурсе в качестве домашнего задания и викторины. ";
 $MSG_PLS_ADD =" *Пожалуйста, выберите все языки, которые можно отправить с помощью Ctrl+Click. ";
 $MSG_TIME_WARNING =" Перед началом игры. ";
 $MSG_WATCH_RANK =" Нажмите здесь, чтобы просмотреть рейтинг вопросов. ";
 $MSG_NOIP_WARNING = $OJ_NOIP_KEYWORD " Игра идет, вы можете просмотреть результаты после окончания игры. ";
 $MSG_NOIP_NOHINT = $OJ_NOIP_KEYWORD ." Соревнование, не отображается подсказка. ";
 $MSG_SERVER_TIME = " серверное время ";
 $MSG_START_TIME =" время начала ";
 $MSG_END_TIME =" время окончания ";
 $MSG_VIEW_ALL_CONTESTS =" Показать все конкурсы домашних заданий ";
 $MSG_CONTEST_ID =" номер конкурса домашних заданий ";
 $MSG_CONTEST_NAME =" название конкурса домашних заданий ";
 $MSG_CONTEST_STATUS = " Статус конкурса вакансий ";
 $MSG_CONTEST_OPEN = " открыть ";
 $MSG_CONTEST_CREATOR = " Создатель ";
 $MSG_CONTEST_PENALTY =" совокупное время ";
 $MSG_LOG="Log";
 $MSG_SUSPECT="Audit";
 $MSG_IP_VERIFICATION = " Проверка IP ";
 $MSG_CONTEST_SUSPECT1 =" IP-адрес с несколькими идентификаторами. Если во время конкурса/экзамена на одном компьютере осуществляется доступ к нескольким идентификаторам, идентификатор будет зарегистрирован. ";
 $MSG_CONTEST_SUSPECT2 =" Идентификатор с несколькими IP-адресами. Если вы переключитесь на другой компьютер во время конкурса/экзамена, это будет зарегистрировано. ";
 
 $MSG_SECONDS = " секунды ";
 $MSG_MINUTES = " минуты ";
 $MSG_HOURS = " часы ";
 $MSG_DAYS = " дней ";
 $MSG_MONTHS = " месяцев ";
 $MSG_YEARS = " Годы ";
 
 $MSG_Public = " общедоступный ";
 $MSG_Private = " частный ";
 $MSG_Running =" Выполняется ";
 $MSG_Start =" начинается с ";
 $MSG_End = " оканчивается на ";
 $MSG_TotalTime = " Общее время ";
 $MSG_LeftTime = " Влево ";
 $MSG_Ended = " Завершено ";
 $MSG_Login =" Пожалуйста, войдите, чтобы продолжить ";
 $MSG_JUDGER = " Судебная машина ";
	$MSG_DOWNLOAD="Download";
	$MSG_SHOW="Show";
	$MSG_HIDE="Hide";

 $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM =" Во время экзамена вы не можете просматривать ранее отправленные коды. ";
 $MSG_BBS_NOT_ALLOWED_FOR_EXAM =" Во время экзамена дискуссионная доска отключена. ";
 $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM =" Во время экзамена запрещено изменять данные учетной записи. ";
 $MSG_MAIL_NOT_ALLOWED_FOR_EXAM =" Во время экзамена внутренняя почта отключена. ";
 $MSG_LOAD_TEMPLATE_CONFIRM =" Загрузить шаблон по умолчанию? \\ n Если да, текущий код будет перезаписан! ";
 $MSG_NO_MAIL_HERE = " Этот OJ не поддерживает внутреннюю почту~ ";
 
 $MSG_BLOCKLY_OPEN =" визуализация ";
 $MSG_BLOCKLY_TEST =" Запуск перевода ";
 $MSG_MY_SUBMISSIONS = " Мои материалы ";
 $MSG_MY_CONTESTS = " мой $MSG_CONTEST ";
 $MSG_Creator =" Предложение человека ";
 $MSG_IMPORTED = " Внешний импорт ";
 $MSG_PRINTER =" печать ";
 $MSG_PRINT_DONE = " Печать выполнена ";
 $MSG_PRINT_PENDING = " Отправлено успешно, для печати ";
 $MSG_PRINT_WAITING =" Пожалуйста, будьте терпеливы и не отправляйте одно и то же задание на печать повторно ";
 $MSG_COLOR =" цвет ";
 $MSG_BALLOON = " Воздушный шар ";
 $MSG_BALLOON_DONE =" Воздушные шары выпущены ";
 $MSG_BALLOON_PENDING =" Ожидание всплывающей подсказки ";
 
 $MSG_DATE =" дата ";
 $MSG_TIME =" время ";
 $MSG_SIGN =" персонализированная подпись ";
 $MSG_RECENT_PROBLEM = " Последнее обновление ";
 $MSG_RECENT_CONTEST = " Последние игры ";
 $MSG_PASS_RATE = " Проходной балл ";
 $MSG_SHOW_TAGS =" Показать теги категорий ";
 $MSG_SHOW_ALL_TAGS = " все теги ";
 $MSG_RESERVED = " Не включено ";
 $MSG_TABLE_TRANSPOSE="Table Transpose";

 $MSG_HELP_SEEOJ =" Вернуться на передний план ";
 $MSG_HELP_ADD_NEWS = " Добавить новость на домашнюю страницу ";
 $MSG_HELP_NEWS_LIST =" Управление опубликованными новостями ";
 $MSG_HELP_USER_LIST =" Деактивировать, активировать учетную запись для зарегистрированных пользователей ";
 $MSG_HELP_USER_ADD = " Добавить пользователя ";
 $MSG_HELP_ADD_PROBLEM = " Добавляйте новые вопросы вручную. После добавления нескольких наборов тестовых данных загрузите их с помощью кнопки TestData в списке вопросов. Новые вопросы <b>по умолчанию скрыты</b>, и вам нужно нажать красную кнопку < font color='red в списке вопросов. '> $MSG_RESERVED </font> переключиться на зеленый <font color='green'>Доступно</font> включено.. ";
 $MSG_HELP_PROBLEM_LIST =" Управление существующими вопросами и данными, загружаемые данные могут быть сжаты с помощью zip данных без каталога. ";
 $MSG_HELP_ADD_CONTEST =" Запланируйте новый конкурс, разделяйте номера вопросов запятыми. Вы можете создавать частные конкурсы и ограничивать участников паролями или списками. ";
 $MSG_HELP_CONTEST_LIST =" Существующий список конкурсов, время модификации и публичный/приватный, старайтесь не корректировать список тем после начала конкурса. ";
 $MSG_HELP_SET_LOGIN_IP =" Записывает изменения компьютера (IP-адрес входа) во время экзамена. ";
 $MSG_HELP_TEAMGENERATOR =" Пакетное создание большого количества конкурсных учетных записей и паролей для участников из разных школ. Не используйте маленькую систему случайно, она может создавать нежелательные учетные записи и не может быть удалена. ";
 $MSG_HELP_SETMESSAGE =" Установить прокручиваемое содержимое объявления ";
 $MSG_HELP_SETPASSWORD = " Сбросить пароль указанного пользователя. Для учетной записи администратора перед изменением его необходимо понизить до обычного пользователя. ";
 $MSG_HELP_REJUDGE =" Переоценка указанного вопроса, заявки или конкурса. ";
 $MSG_HELP_ADD_PRIVILEGE = " Добавить разрешения указанным пользователям, включая администраторов, авторов тем, организаторов конкурса, участников конкурса, наблюдателей кода, судей по ручным вопросам, удаленных судей по вопросам, принтеров, раздатчиков воздушных шаров и т. д. ";
 $MSG_HELP_ADD_CONTEST_USER =" Добавляет пользователю право на одно соревнование. ";
 $MSG_HELP_ADD_SOLUTION_VIEW =" Предоставляет пользователям доступ к просмотру ответов на один вопрос. ";
 $MSG_HELP_PRIVILEGE_LIST =" Просмотреть список существующих специальных привилегий и удалить их. ";
 $MSG_HELP_GIVESOURCE = " Пожертвовать стандартный курс, импортированный в систему, на указанную учетную запись, которая будет использоваться для помощи тем, кто не сможет выучить для справки после обучения. ";
 $MSG_HELP_EXPORT_PROBLEM =" Экспортировать заголовок в систему в виде файла fps.xml. ";
 $MSG_HELP_IMPORT_PROBLEM =" Импортируйте файл fps.xml, загруженный с официального ресурса группы или tk.hustoj.com. ";
 $MSG_HELP_UPDATE_DATABASE =" Обновление структуры базы данных после каждого обновления (sudo update-hustoj) или импорта старой резервной копии системной базы данных должно выполняться хотя бы один раз. ";
 $MSG_HELP_ONLINE =" Просмотр онлайн-пользователей ";
 $MSG_HELP_AC =" Правильный ответ, попробуйте еще раз. ";
 $MSG_HELP_PE =" Ответ в основном правильный, но формат неверный. ";
 $MSG_HELP_WA = " Ответ неверный. Простое прохождение тестовых данных не обязательно является правильным ответом. Должны быть места, которых вы не ожидали. Нажмите, чтобы просмотреть сравнительную информацию, которую может предоставить система. ";
 $MSG_HELP_TLE =" Выполнение ограничения по времени, проверьте, есть ли бесконечный цикл, или должен быть более быстрый метод вычисления ";
 $MSG_HELP_MLE =" Превышен лимит памяти, возможно, данные необходимо сжать, проверьте наличие утечек памяти ";
 $MSG_HELP_OLE =" Вывод превышает лимит, ваш вывод в два раза длиннее правильного ответа, должно быть что-то не так ";
 $MSG_HELP_RE =" Ошибка выполнения, неправильный доступ к памяти, выход за пределы массива, дрейф указателя, вызов отключенной системной функции. Нажмите для подробного вывода ";
 $MSG_HELP_CE =" Ошибка компиляции, нажмите, чтобы получить подробный вывод компилятора ";
 
 $MSG_HELP_MORE_TESTDATA_LATER =" Дополнительные группы тестовых данных, добавьте их после добавления вопросов ";
 $MSG_HELP_ADD_FAQS =" Администраторы могут добавить новость с именем \" faqs. $OJ_LANG \" , чтобы заменить содержимое <a href=../faqs.php> $MSG_FAQ </a>. ";
 $MSG_HELP_HUSTOJ =" <sub><a target='_blank' href='https://github.com/zhblue/hustoj'><span class='glyphicon glyphicon-heart' aria-hidden='true'></ span> Пожалуйста, зайдите в HUSTOJ и добавьте нам <span class='glyphicon glyphicon-star' aria-hidden='true'></span>звезду!</a></sub> ";
 $MSG_HELP_SPJ =" Использование специальных судей, см. <a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>поиск специального судьи hustoj</ а> ";
 $MSG_HELP_BALLOON_SCHOOL = " Печать, поле School учетной записи балуна используется для фильтрации списка задач. Например, заполните zjicm, чтобы отобразить только задачи, номер учетной записи которых начинается с zjicm ";
 $MSG_HRLP_BACKUP_DATABASE =" Резервное копирование базы данных, тестовых данных и изображений в каталог с 0 вопросами ";
 $MSG_HELP_LEFT_EMPTY="If you don't want to modify, please left this empty.";
 $MSG_HELP_LOCAL_EMPTY="Left empty for local problem.";
 
 $MSG_WARNING_LOGIN_FROM_DIFF_IP =" войти с другого IP-адреса ";
 $MSG_WARNING_DURING_EXAM_NOT_ALLOWED =" Запрещено во время экзамена ";
 $MSG_WARNING_ACCESS_DENIED =" Извините, вы не можете просмотреть это сообщение! Поскольку оно не принадлежит вам или администратор установил состояние системы, чтобы это сообщение не отображалось. ";
 
 $MSG_WARNING_USER_ID_SHORT =" Имя пользователя не менее 3 символов! ";
 $MSG_WARNING_PASSWORD_SHORT =" Пароль не менее 6 символов! ";
 $MSG_WARNING_REPEAT_PASSWORD_DIFF =" Введенные дважды пароли не совпадают! ";
 
 
 $MSG_LOSTPASSWORD_MAILBOX =" Пожалуйста, перейдите в папку со спамом вашего почтового ящика, чтобы найти код подтверждения и введите его здесь ";
 $MSG_LOSTPASSWORD_WILLBENEW = " Если вы заполните его правильно, пройдите следующую проверку, этот код подтверждения автоматически станет вашим новым паролем! ";
 
 
  //discuss.php
  $MSG_LAST_REPLY="Last";
  $MSG_REPLY_COUNTS="Counts";
  $MSG_REPLY_NUMBER="Number"; 
  $MSG_QUESTION="Question";
  $MSG_NO_QUESTIONS="No questions";
  $MSG_REGISTER_QUESTION="Register";  
  $MSG_WRITE_QUESTION="Question"; 
  $MSG_REGISTERED="Registered";
  $MSG_BLOCKED="Blocked";
  $MSG_REPLY="Reply"; 
  $MSG_REGISTER_REPLY="Reply";
  $MSG_DISABLE="Disable";   
  $MSG_LOCK="Lock";
  $MSG_RESUME="Resume";
  $MSG_DISCUSS_DELETE="Delete"; 
  $MSG_DISCUSS_NOTICE="Notice";   
  $MSG_DISCUSS_NOTE="Note"; 
  $MSG_DISCUSS_NORMAL="Normal";


 // шаблон/../reinfo.php
 $MSG_A_NOT_ALLOWED_SYSTEM_CALL = " Используйте вызов операционной системы, запрещенный системой, чтобы узнать, есть ли у вас несанкционированный доступ к ресурсам, таким как файлы или процессы. Если вы являетесь системным администратором и подтверждаете, что отправленный ответ правильный и тестовые данные верны, вы можете отправить 'RE' Перейти к онлайн-судье общедоступной учетной записи WeChat, чтобы просмотреть решение. ";
 $MSG_SEGMETATION_FAULT =" Ошибка сегментации, проверка на выход за границы массива, исключение указателя, доступ к области памяти, к которой нельзя обращаться ";
 $MSG_FLOATING_POINT_EXCEPTION =" Ошибка с плавающей запятой, проверьте деление на ноль ";
 $MSG_BUFFER_OVERFLOW_DETECTED =" Переполнение буфера, проверьте, есть ли длина строки за пределами массива ";
 $MSG_PROCESS_KILLED =" Процесс был остановлен из-за проблем с памятью или временем, проверьте, нет ли бесконечного цикла ";
 $MSG_ALARM_CLOCK = " Процесс был убит из-за времени, проверьте, нет ли бесконечного цикла, эта ошибка эквивалентна тайм-ауту TLE ";
 $MSG_CALLID_20 =" Возможно, массив выходит за границы, проверьте связь между количеством данных, описанных в заголовке, и размером применяемого массива ";
 $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION =" Проверить, что массив выходит за границы ";
 $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION =" Нижний индекс строки выходит за границы, проверьте параметры таких методов, как subString, charAt ";
 $MSG_WRONG_OUTPUT_TYPE_EXCEPTION="Are you using an number as a char? Did you print out any Non-printable characters?";
 $MSG_NON_ZERO_RETURN="Do NOT return non-zero value in your main() function , or system will regard it as an error. ";
  $MSG_EXPECTED="Expected Output";
  $MSG_YOURS="Your Output";
  $MSG_FILENAME="Filename";
  $MSG_SIZE="Size";

 // шаблон/../ceinfo.php
 $MSG_ERROR_EXPLAIN =" Вспомогательное пояснение ";
 $MSG_SYSTEM_OUT_PRINT =" Использование System.out.print в Java отличается от использования printf языка C, попробуйте System.out.format ";
 $MSG_NO_SUCH_FILE_OR_DIRECTORY = " Сервер представляет собой систему Linux и не может использовать нестандартные заголовочные файлы, специфичные для Windows. ";
 $MSG_NOT_A_STATEMENT =" Проверить соответствие фигурных скобок {}, сочетание клавиш завершения кода eclipse Ctrl+Shift+F ";
 $MSG_EXPECTED_CLASS_INTERFACE_ENUM =" Пожалуйста, не размещайте Java-функции (методы) за пределами объявления класса, обратите внимание на закрывающую позицию фигурных скобок} ";
 $MSG_SUBMIT_JAVA_AS_C_LANG =" Пожалуйста, не отправляйте программы Java как язык C ";
 $MSG_DOES_NOT_EXIST_PACKAGE = " Определять орфографию, например: системный объект System начинается с заглавной S ";
 $MSG_POSSIBLE_LOSS_OF_PRECISION =" Присвоение потеряет точность, проверьте тип данных, если он правильный, вы можете использовать преобразование типа приведения ";
 $MSG_INCOMPATIBLE_TYPES =" Данные разных типов не могут быть присвоены друг другу в Java, а целые числа не могут использоваться как логические значения ";
 $MSG_ILLEGAL_START_OF_EXPRESSION =" Строка должна быть заключена в двойные кавычки ( \\\" ) ";
 $MSG_CANNOT_FIND_SYMBOL =" Орфографическая ошибка или отсутствующие объекты, необходимые для вызова таких функций, как println(), необходимо вызвать System.out ";
 $MSG_EXPECTED_SEMICOLON =" Пропущена точка с запятой. ";
 $MSG_DECLARED_JAVA_FILE_NAMED =" Java должен использовать общедоступный класс Main. ";
 $MSG_EXPECTED_WILDCARD_CHARACTER_AT_END_OF_INPUT =" Нет конца кода, отсутствуют совпадающие скобки или точки с запятой, проверьте, все ли коды выбраны при копировании. ";
 $MSG_INVALID_CONVERSION =" Недопустимое неявное приведение, попробуйте явное приведение типа (int *)malloc(....) ";
 $MSG_NO_RETURN_TYPE_IN_MAIN =" В стандарте C++ функция main должна иметь возвращаемое значение ";
 $MSG_NOT_DECLARED_IN_SCOPE = " Переменная не объявлена, проверьте наличие орфографических ошибок! ";
 $MSG_MAIN_MUST_RETURN_INT = " В стандартном языке C тип возвращаемого значения функции main должен быть int, а использование void в учебниках и VC является нестандартным использованием ";
 $MSG_PRINTF_NOT_DECLARED_IN_SCOPE = "Функция printf вызывается без объявления, проверьте, импортирован ли заголовочный файл stdio.h или cstdio ";
 $MSG_IGNOREING_RETURN_VALUE = " Предупреждение: Возвращаемое значение функции игнорируется, функция может быть использована неправильно или аномальное возвращаемое значение не учитывается ";
 $MSG_NOT_DECLARED_INT64 =" __int64 не объявлен, __int64 в Microsoft VC не поддерживается в стандартном C/C++, пожалуйста, используйте long long для объявления 64-битных переменных ";
 $MSG_EXPECTED_SEMICOLON_BEFORE =" В предыдущей строке отсутствует точка с запятой ";
 $MSG_UNDECLARED_NAME =" Переменные должны быть объявлены перед использованием, возможны орфографические ошибки, обратите внимание на регистр символов. ";
 $MSG_SCANF_NOT_DECLARED_IN_SCOPE = " функция scanf вызывается без объявления, проверьте, импортирован ли заголовочный файл stdio.h или cstdio ";
 $MSG_MEMSET_NOT_DECLARED_IN_SCOPE = "Функция memset вызывается без объявления, проверьте, импортирован ли заголовочный файл stdlib.h или cstdlib ";
 $MSG_MALLOC_NOT_DECLARED_IN_SCOPE ="Функция malloc вызывается без объявления, проверьте, импортирован ли заголовочный файл stdlib.h или cstdlib ";
 $MSG_PUTS_NOT_DECLARED_IN_SCOPE = "Функция puts вызывается без объявления, проверьте импортируется ли заголовочный файл stdio.h или cstdio ";
 $MSG_GETS_NOT_DECLARED_IN_SCOPE = " Функция Gets вызывается без объявления, проверьте, импортирован ли заголовочный файл stdio.h или cstdio ";
 $MSG_STRING_NOT_DECLARED_IN_SCOPE = "Функция класса string вызывается без объявления, проверьте, импортирован ли заголовочный файл string.h или cstring ";
 $MSG_NO_TYPE_IMPORT_IN_C_CPP = " Не отправляйте программы на языке Java как C/C++, обратите внимание на выбор типа языка перед отправкой. ";
 $MSG_ASM_UNDECLARED =" Встраивание кода на ассемблере в C/C++ запрещено. ";
 $MSG_REDEFINITION_OF =" Функция или переменная определяется повторно, посмотрите, не вставляется ли код несколько раз. ";
 $MSG_EXPECTED_DECLARATION_OR_STATEMENT =" Кажется, программа не завершена, проверьте, не пропустили ли вы код при копировании и вставке. ";
 $MSG_UNUSED_VARIABLE = " Предупреждение: переменная объявлена ​​неиспользуемой, проверьте наличие орфографических ошибок и неправильного использования переменных с похожими именами. ";
 $MSG_IMPLICIT_DECLARTION_OF_FUNCTION =" Функция неявно объявлена,проверьте импортирован ли правильный заголовочный файл.Или отсутствует функция с указанным именем требуемым заголовком. ";
 $MSG_ARGUMENTS_ERROR_IN_FUNCTION =" Количество параметров, предоставленных при вызове функции, неверно, проверьте, используются ли неправильные параметры. ";
 $MSG_EXPECTED_BEFORE_NAMESPACE = " Не отправляйте программы на языке C++ как C, выберите тип языка перед отправкой. ";
 $MSG_STRAY_PROGRAM =" Китайские пробелы, знаки препинания и т.д. не могут использоваться в программе, кроме комментариев и строк. Пожалуйста, отключите китайский метод ввода при написании программы. Пожалуйста, не используйте код, скопированный из Интернета. ";
 $MSG_DIVISION_BY_ZERO =" Деление на ноль вызовет переполнение с плавающей запятой. ";
 $MSG_CANNOT_BE_USED_AS_A_FUNCTION =" Переменные нельзя использовать в качестве функций. Проверьте, не повторяются ли имя переменной и имя функции, иначе оно может быть написано неправильно. ";
 $MSG_CANNOT_FIND_TYPE = " Описание формата scanf/printf не соответствует следующей таблице параметров, проверьте, больше или меньше адресного символа "&", это может быть орфографической ошибкой. ";
 $MSG_JAVA_CLASS_ERROR =" Представление языка Java может иметь только один общедоступный класс, а имя класса должно быть Main, пожалуйста, не используйте ключевое слово public для других классов ";
 $MSG_EXPECTED_BRACKETS_TOKEN =" Отсутствует закрывающая скобка ";
 $MSG_NOT_FOUND_SYMBOL =" Используйте неопределенную функцию или переменную, проверьте правильность написания, не используйте несуществующую функцию, вызовы Java обычно требуют указания имени объекта, такого как list1.add(...). Когда вызов метода Java Чувствителен к типу параметра, например: нельзя передать целое число (int) методу, который принимает строковый объект (String) ";
 $MSG_NEED_CLASS_INTERFACE_ENUM =" Ключевое слово отсутствует, должно быть объявлено как класс, интерфейс или перечисление ";
 $MSG_CLASS_SYMBOL_ERROR = " Используя пример из учебника, вы должны отправить код соответствующего класса вместе и удалить ключевое слово public ";
 $MSG_INVALID_METHOD_DECLARATION =" Только метод с тем же именем класса является методом конструктора, и тип возвращаемого значения не записывается. Если имя класса изменено на Main, одновременно измените имя конструктора. ";
 $MSG_EXPECTED_AMPERSAND_TOKEN = " Не отправляйте программы на языке C++ как C, выберите тип языка перед отправкой. ";
 $MSG_DECLARED_FUNCTION_ORDER =" Обратите внимание на порядок объявления функций и методов. Объявление другого метода не может находиться в одном методе. ";
 $MSG_NEED_SEMICOLON = " В этой строке, отмеченной выше, отсутствует точка с запятой в конце. ";
 $MSG_EXTRA_TOKEN_AT_END_OF_INCLUDE =" Инструкция включения должна находиться на отдельной строке и не может быть размещена на той же строке, что и следующая инструкция ";
 $MSG_INT_HAS_NEXT = " hasNext() следует заменить на nextInt() ";
 $MSG_UNTERMINATED_COMMENT =" Комментарий не закончен, пожалуйста, проверьте правильность терминатора \"*/\", соответствующего \"/*\" ";
 $MSG_EXPECTED_BRACES_TOKEN ="В объявлении функции отсутствуют круглые скобки (), например, int main() записывается как int main ";
 $MSG_REACHED_END_OF_FILE_1 = " Проверить, не скопирован ли исходный код полностью или отсутствует закрывающая фигурная скобка ";
 $MSG_SUBSCRIPT_ERROR =" Невозможно индексировать доступ к переменным, которые не являются массивами или указателями ";
 $MSG_EXPECTED_PERCENT_TOKEN =" Форматная часть scanf должна быть заключена в двойные кавычки ";
 $MSG_EXPECTED_EXPRESSION_TOKEN =" параметр или выражение не завершены ";
 $MSG_EXPECTED_BUT =" Неправильная пунктуация или символ ";
 $MSG_REDEFINITION_MAIN ="Этот вопрос может быть дополнительным вопросом кода. Пожалуйста, изучите вопрос еще раз, чтобы понять смысл вопроса. Не отправляйте основную функцию, определенную системой, а функцию в указанном формате. ";
 $MSG_IOSTREAM_ERROR =" Пожалуйста, не отправляйте программы C++ как C ";
 $MSG_EXPECTED_UNQUALIFIED_ID_TOKEN = " Обратите внимание, отсутствует ли точка с запятой после объявления массива ";
 $MSG_REACHED_END_OF_FILE_2 =" Отсутствуют фигурные скобки в конце программы ";
 $MSG_INVALID_SYMBOL =" Проверить, используются ли китайские знаки препинания или пробелы ";
 $MSG_DECLARED_FILE_NAMED = " Открытый класс в OJ может быть только основным ";
 $MSG_EXPECTED_IDENTIFIER =" При объявлении переменной имя переменной может быть не объявлено или могут отсутствовать круглые скобки. ";
 $MSG_VARIABLY_MODIFIED =" Размер массива не может использовать переменные, переменные не могут использоваться в качестве размера измерения глобальных массивов в языке C, включая константные переменные ";
 $MSG_FUNCTION_GETS_REMOVIED = " std::gets устарел в C++11 и удален в C++14. Вместо этого используйте std::fgets. Или добавьте определение макроса #define gets(S) fgets(S,sizeof( S), стандартный ввод) ";
 $MSG_PROBLEM_USED_IN =" Задача уже используется для частного конкурса ";
 $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT =" Внутренняя почта может отправляться только между учениками и учителями, но не между учениками! ";
 $MSG_COPY_USER_LIST_FROM_CONTEST="Copy user list from a history contest... ";
 $MSG_REFRESH_PRIVILEGE =" Обновить привилегии ";
 
 $MSG_SAVED_DATE =" сэкономить время ";
 $MSG_PROBLEM_STATUS = " текущий статус ";
 
 $MSG_NEW_CONTEST = " Создать новый конкурс ";
 $MSG_AVAILABLE = " включить ";
 $MSG_RESERVED = " Не включено ";
 $MSG_NEW_PROBLEM_LIST =" Создать новый список элементов ";
 $MSG_DELETE =" удалить ";
 $MSG_EDIT = " редактировать ";
 $MSG_TEST_DATA = " Управление тестовыми данными ";
 $MSG_CHECK_TO =" Операция пакетного выбора ";
 
 //bbcode.php
 $MSG_TOTAL = " Всего ";
 $MSG_NUMBER_OF_PROBLEMS = " проблема ";
                                  $MSG_GLOBAL="Global ";
                                  $MSG_THIS_CONTEST="This Contest's ";
 $MSG_SUBMIT_RECORD = " Отправить запись ";
 $MSG_RETURN_CONTEST = " Вернуться к конкурсу ";
 $MSG_COPY =" копировать ";
 $MSG_SUCCESS = " успех ";
 $MSG_FAIL = " НЕУДАЧА ";
 $MSG_TEXT_COMPARE =" сравнение текста ";
 $MSG_JUDGE_STYLE = " Метод оценки ";
 // reinfo.php
 $MSG_ERROR_INFO =" Информация об ошибке ";
 $MSG_INFO_EXPLAINATION =" Вспомогательное пояснение ";
 // ceinfo.php
 $MSG_COMPILE_INFO =" информация о компиляции ";
 $MSG_SOURCE_CODE = " исходный код ";
 //конкурс.php
 $MSG_Contest_Pending =" Не запущено ";
 $MSG_Server_Time =" текущее время ";
 $MSG_Contest_Infomation = " Информация и объявление ";
 //sourcecompare.php
 $MSG_Source_Compare = " сравнение исходного кода ";
 $MSG_BACK = " Вернуться на предыдущую страницу ";
	$MSG_NEXT_PAGE="Next Page";
	$MSG_PREV_PAGE="Prev Page";
 	//email
	$MSG_SYS_WARN="System Warning!";
	$MSG_IS_ROBOT="could be a robot , verify and disable it !";
       $MSG_FORBIDDEN="forbidden ";
       $MSG_OTHERS="other's ";
       $MSG_SUBNET="subnet ";
//SaaS friendly
 $MSG_TEMPLATE="Template";
  $MSG_FRIENDLY_LEVEL="Friendly Level";
  $MSG_FRIENDLY_L0="Not friendly at all";
  $MSG_FRIENDLY_L1="Using UTC+8 TimeZone";
  $MSG_FRIENDLY_L2="Using Chinese UI";
  $MSG_FRIENDLY_L3="Show differ,No Verify Code";
  $MSG_FRIENDLY_L4="Using mail,Code auto share";
  $MSG_FRIENDLY_L5="Allow test running";
  $MSG_FRIENDLY_L6="Keep long login";
  $MSG_FRIENDLY_L7="Enable discus";
  $MSG_FRIENDLY_L8="Allow download test data";
  $MSG_FRIENDLY_L9="Allow guest to submit";
