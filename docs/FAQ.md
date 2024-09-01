# HUSTOJ FAQ

如何修改默认字体大小
--
修改 [/home/judge/src/web/template/syzoj/css/white.css末尾](https://github.com/zhblue/hustoj/blob/master/trunk/web/template/syzoj/css/white.css#L76)
设置页面内容字体大小。

修改[/home/judge/src/web/template/syzoj/submitpage.php 432行](https://github.com/zhblue/hustoj/blob/master/trunk/web/template/syzoj/submitpage.php#L432)
设置代码输入框的字体大小。

如何给测试数据设定分数
--
测试数据如果命名中包含用英文中括号`[ ]`包裹的数字，则认定为建议分数，如没有建议分数则默认10分。
如:` testA[30].in / testA[30].out` 这组数据给的分数是30分，`testB.in / testB.out `给的分数是10分。
系统将累计得分与题目所有数据的总分，计算通过率，写入数据库pass_rate字段，并在Web端显示。


如何使用HTTP判题模式
--
 1、注册一个新的账户例如judger1，用作判题。
 2、用管理员登陆后台，给这个判题账户增加HTTP_JUDGE权限。
 3、修改判题机judge.conf，设置好相关字段
 ```
    OJ_HTTP_JUDGE=1
    OJ_HTTP_BASEURL=http://OJ系统URL地址/
    OJ_HTTP_USERNAME=judger1
    OJ_HTTP_PASSWORD=judger1password
 ```
 4、修改db_info.inc.php，禁用$OJ_VCODE验证码。
 5、重启判题机
 ```
 	sudo pkill -9 judged
	sudo judged
 ```

另参考 https://github.com/zhblue/hustoj/blob/master/wiki/HTTPJudge.md

是否可以只由管理员来注册账号，自己不能注册
--
可以，设置db_info.inc.php中的选项，
https://github.com/zhblue/hustoj/blob/master/trunk/web/include/db_info.inc.php#L51
```
static $OJ_REGISTER=true; //允许注册新用户
static $OJ_REG_NEED_CONFIRM=false; //新注册用户需要审核
```
关闭注册后，管理员可以在后台“比赛队账户生成器”，生成指定数量的账户用于分配。
http://xxxx.xxxxx/admin/team_generate.php


如何显示MathJax语法的公式？
--
修改[db_info.inc.php](https://github.com/zhblue/hustoj/blob/master/trunk/web/include/db_info.inc.php#L57)设置
```
static  $OJ_MATHJAX=true;  // 激活mathjax
```
需要用户能够正常访问互联网，内网用户需要自行部署mathjax内网镜像，并修改template/bs3/problem.php中相关路径。


如何启用查重机制？
--
修改/home/judge/etc/judge.conf，设置
```
OJ_SIM_ENABLE=1
```
修改/home/judge/src/web/include/db_info.inc.php，设置
```
$OJ_SIM=true;
```
* 抄袭只对不同账号间生效，自己抄袭自己不计。拥有Source_browser权限的账号可以看到具体数值和对比。

不能访问github，国内网，如何通过gitee安装？
--
```
wget https://gitee.com/zhblue/hustoj/raw/master/trunk/install/install-ubuntu18-gitee.sh
sudo bash install-ubuntu18-gitee.sh
```


请问如何重启判题机？
--
```
sudo pkill -9 judged
sudo judged
```

XXXXX 这个文件是在哪的
--
```
   sudo find /home/judge -name "XXXXX"
```


数据库账号密码是什么，如何登陆mysql?
--
数据库账号密码存放在两个配置文件中：
```
/home/judge/etc/judge.conf
/home/judge/src/web/include/db_info.inc.php
```
新版本中，快速登陆mysql的脚本在install目录里，名字为mysql.sh
使用方法
```
sudo bash /home/judge/src/install/mysql.sh
```


后台导入问题失败
--

1、先用谷歌浏览器直接打开xml文件，看是否有语法错误，如果有，用文本编辑器修订提示的行号。

2、如果超过100M,可以先用EasyFPSViewer拆分成多个小文件，然后再导入。

3、对于HUSTOJ，可以先压缩为zip再上传导入

4、修改/etc/php/7.2/fpm/php.ini, 提高post_max_size、upload_max_filesize 、memory_limit、max_execution_time 的值。修改后执行sudo service php7.2-fpm restart生效。



电脑配置太高，造了很多数据还是没法卡住暴力怎么办？
--
修改/home/judge/etc/judge.conf
```
OJ_CPU_COMPENSATION=1.0
```
增加这个值可以降低CPU的评测速度，安装脚本根据CPU的bogomips值来初始化。
最高不超过100，设为100可以将原先1ms的测试数据计成100ms。


为什么题目不见了/如何让比赛里的题目也可以在练习里做？
--
[参考这里](https://github.com/zhblue/hustoj/issues/520)


其他主机怎么连接到oj?
--
这取决于买的阿里云还是校园网服务器，或者虚拟机：
阿里云直接用阿里提供的公网ip访问，也可以添加域名解析后用域名访问。
校园网，用学校提供的内网ip或二级域名访问。
虚拟机，百度“【虚拟机的名字如virtualbox或vmware】+端口映射” ，把80端口转进去，然后用物理机的ip地址访问。


升级后似乎不能提交/判题了？
--
这多半意味着数据库结构与预期不一致，可以通过以下方法解决：
* 管理后台更新数据库
* 参考db.sql中的建表语句，对比修订当前库表结构
* 处理掉从老版本MySQL里带来，在新版MySQL中不再合法的日期数据，如：'0000-00-00'，然后参考前面的方案解决。

比赛后题目看不见了？
--
* 比赛的题目在比赛添加后，直到结束前，是不能在练习中看到和提交的，否则比赛将泄题或罚时被绕过。
* 私有比赛的题目，在比赛结束后，仍然保留，即使比赛被隐藏也是一样，这是为了防止下一届新生提前获知测试内容。
* 如果希望私有比赛后，题目公开可做，请将比赛切换为公开。

关于NOIP赛制
--
* 设置judge.conf中的OJ_OI_MODE=1       //不在单个数据点WA时停止判题，而是继续判题
* 设置db_info.inc.php中的 $OJ_MARK="mark";   // 非AC的提交结果显示得分而非错误比率
* 设置db_info.inc.php中的 $OJ_OI_1_SOLUTION_ONLY=true; //比赛是否采用noip中的仅保留最后一次提交的规则。
* 添加比赛时，比赛标题中包含"NOIP"这个关键词  // 赛后才能看结果
* "NOIP"这个敏感词在db_info.inc.php中可以修改

随机的CE编译错误
--
* 检查OJ_RUNNING的设置与run?目录的对应关系，例如:OJ_RUNNING=2，需要run0 run1两个目录，属主judge，权限700。
* 有的题目CE有的题目AC，适当放宽judge_client.cc中compile函数里的CPU、内存、文件限制。约1234行前后。修改后需在core目录执行sudo bash make.sh

老版本
----

编译报错找不到mysql.h
--

    如果使用debian或centos，可能默认安装的是mariadb不是mysql，这时请自行搜索安装mariadb的头文件。
    
debian里大约是
```
    sudo apt-get install libmariadb-dev
```
centos里大约是
```
    sudo yum install MariaDB-devel
```

Runtime Error:[ERROR] A Not allowed system call: runid:10735 CALLID:20 如何解决？
--

编辑okcalls64.h或okcalls32.h（取决于您使用的Linux版本uname -a出现x64字样则64位，i686字样则32位），在对应的语言数组里增加内容。
如C或C++：

	int LANG_CV[256] = { 85, 8,140, SYS_time, SYS_read, SYS_uname, SYS_write, SYS_open,
		SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect,
		SYS_mmap2, SYS_fstat64, SYS_set_thread_area, 252, 0 };


将上述报错中CALLID:后的数字，增加到数组中非末尾的位置，如果这个数字是0，请加在首位。

	int LANG_CV[256] = { 20, 85, 8,140, SYS_time, SYS_read, SYS_uname, SYS_write, SYS_open,
		SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect,
		SYS_mmap2, SYS_fstat64, SYS_set_thread_area, 252, 0 };

修改完成，重新在core目录执行sudo ./make.sh
然后重新测试，如果发现再次出现类似错误，请留意CALLID数字变化，重复上述步骤直至问题消失。
看不懂请移步[知乎](https://zhuanlan.zhihu.com/p/24498599) 看更详细解释。


如何让判题程序忽略行尾的空白字符
--

	在judge_client.cc头部增加宏定义 IGNORE_ESOL

配置文件里的字段什么含义?
--

    点击[Configuration](https://github.com/zhblue/hustoj/blob/master/wiki/Configuration.md)
	 
多组数据怎么上传？
--
加好题目后在题目列表找TestData，点击上传。
主文件名一样的*.in *.out，如test1.in test1.out

通过.tar.gz源码安装的应该怎么升级？
--

```
sudo su
cd /home/judge/src/install
bash fixing.sh
```


   
为何页面总是需要刷新才能显示？
--
    如果您使用的是ie6浏览器，请禁用服务器上的deflate模块，在ubuntu下的命令是

sudo rm /etc/apache2/mods-enabled/deflate.*
sudo /etc/init.d/apache2 restart

CentOS 用户
--
点击[CentOS](https://github.com/zhblue/hustoj/blob/master/wiki/CentOS.md)

使用HUSTOJ要花多少钱？
--
不要钱，我们是GPL的。
    
管理员如何添加，如何管理？
--

    查看安装说明[README],管理员登录后有Admin菜单。

为什么我提交的答案始终在pending？
--

    判题程序judged需要用root帐号启动，请重启服务器或手动执行sudo judged。如果无效，请检查/home/judge/etc/judge.conf中的数据库账号配置,参考[Configuration]，修正后再次重启服务器或执行sudo pkill -9 judged等待一会儿再执行sudo judged

为什么添加题目时出现warning,题目目录下数据没有自动生成？
--
您需要修改测试数据目录,给予php-fpm操作数据目录的权限。Ubuntu下php-fpm运行的用户身份是www-data
```
chgrp www-data -R /home/judge/data 
chmod g+rw -R /home/judge/data

```
    
    
为什么我添加的题目普通用户看不到？
--

    题目默认为删除状态，只有管理员能访问，当管理员确认题目没有问题后，可以点击ProblemList中红色的Reserved,切换为绿色的Available启用题目。
 
为何我的C/C++都能用，唯独Java总是CE/RE？
--

　　目前只支持sun原版jdk和openjdk，其他jdk暂不能保证支持。如果你用的是64位系统，你可能需要自己调整一下源代码。请联系我。
  
我是管理员，为什么不能查看别人的源码？  
--

    请给自己增加source_browser权限。issue1
    
如何更新到最新版本？
--
运行下面的命令，如果fixing.sh文件不存在，可以从http://dl.hustoj.com/fixing.sh 下载
```
sudo su
cd /home/judge/src/install
bash fixing.sh
```
    
如何从POJ的免费版迁移？
--

    参考[POJ2HUSTOJ]

我有问题怎么办？
--

    到issues去提问，new issue
如何获得管理员帐号？
--
    注册一个叫admin的用户，自动获得权限。
    
#### 改坏了代码怎么办？

自己不小心改坏了 web 代码，可以使用 install 目录中的 fixing.sh 进行系统修复。

#### 如何进入后台？

以管理员身份登录，点击 `Admin/管理` 进入后台。

#### 如何添加题目？

进入后台，点击左侧NewProblem。

#### 如何添加测试数据？

添加题目时，可以在 `test input` `test output` 添加一组测试数据，大规模的数据（10KB+）和更多的数据，可以在添加完题目后，通过 `ftp/sftp`，上传到题目对应目录，通常是 `/home/judge/data/题号` 。命名规则是输入数据以 `.in` 结尾，输出数据以 `.out` 结尾，主文件名相同。

#### 如何编辑题目？

后台中点击 `Problem List` ，找到需要编辑的题目，点击 `Edit` 。编辑时不能修改测试数据，测试数据请使用 `ftp` 工具修改。

#### 如何启用题目？

题目添加后，默认是停用状态，以防比赛提前漏题，后台中点击 `ProblemList` ，找到题目，点击 `Resume` 启用题目，或者组织比赛，比赛中的题目将自动启用。

#### 如何组织比赛？

在题目列表 `ProblemList` 中选择使用的题目， 在 `PID` 一栏打钩， 点击 `CheckToNewContest` 按钮，进入到比赛添加页面，输入比赛名称，设定 `比赛时间`、`语言类型`、`访问权限`，然后提交即可。

也可以使用管理菜单中的 `NewContest` ，需要手动输入题目编号，用英文逗号分隔。

#### 如何修改、删除比赛？

点击比赛列表 ContestList，选择 `Edit` 或 `Delete`。

#### 如何修改公告信息？

点击 `SetMessage`。

#### 如何修改用户密码？

点击 `ChangePassWord`。

#### 如何重新判题？

点击 `Rejudge` ，输入题号或运行编号。

#### 如何增加用户权限？

`Addprivilege` `administrator` 为管理员，`source_browser` 为代码审查，`contest_creator`为比赛组织者。

通常给使用系统的老师分配代码审查和比赛组织者权限即可。

#### 如何导入、导出题目？

使用 ImportProblem ，上传 FPS 文件。

使用 ExportProblem ，输入起始编号，结束编号，或题号列表，如果输入了列表，起始结束将不起作用。

#### 如何更新数据库结构？

系统升级中，有对数据库的修改，这些修改不能通过 SVN 实现自动更新，如果发现升级 `web/core` 代码后系统报错，可以执行  `update database` 操作，进行数据库升级。因为脚本中有测试代码，所以重复执行不会造成影响。

#### 如何下载新题目？

+ 访问 `FreeProblemSet` ，查看 `Downloads` 列表。<https://github.com/zhblue/freeproblemset/>
+ 访问 [TK题库](http://tk.hustoj.com/) 下载题目


#### 如何让判题程序忽略行尾的空白字符？

在 `judge_client.cc` 头部增加宏定义 `IGNORE_ESOL` 。

#### 如何上传多组数据？
 
加好题目后在题目列表找 `TestData` ，点击上传。主文件名一样的 `*.in` `*.out`，如 `test1.in` `test1.out`

#### 无法正常判题，一直 `pending`

可能是 `judged` 服务未能正常启动，执行如下命令即可。
```bash
sudo judged
```

或者检查 `/home/judge/etc/judge.conf` 中的数据库账号配置，修正后再次重启服务器或执行 `sudo pkill -9 judged` 。
等待一会儿再执行 `sudo judged`

#### Runtime error

若出现 `Runtime Error:[ERROR] A Not allowed system call: runid:10735 CALLID:20` 时，编辑 `okcalls64.h` 或 `okcalls32.h`（取决于您使用的 `Linux`  版本 `uname -a` 出现 `x64` 字样则 `64` 位，`i686` 字样则 `32` 位），在对应的语言数组里增加内容。如 `C` 或 `C++` ：

```cpp
int LANG_CV[256] = { 85, 8,140, SYS_time, SYS_read, SYS_uname, SYS_write, SYS_open,
SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect, SYS_mmap2,
SYS_fstat64, SYS_set_thread_area, 252, 0 };
```

将上述报错中 `CALLID:` 后的数字，增加到数组中非末尾的位置，如果这个数字是0，请加在首位。

```cpp
int LANG_CV[256] = { 20, 85, 8,140, SYS_time, SYS_read, SYS_uname, SYS_write,
SYS_open,SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap,
SYS_mprotect,SYS_mmap2, SYS_fstat64, SYS_set_thread_area, 252, 0 };
```

修改完成，重新在 `core` 目录执行 `sudo ./make.sh` 然后重新测试，如果发现再次出现类似错误，请留意 `CALLID` 数字变化，重复上述步骤直至问题消失。

#### Ubuntu 下 `Apache2` 报错（针对旧版本，新版本使用 `nginx`）

Ubuntu 环境，当 `apache2` 重启提示时：

```
* Starting web server apache2

apache2: Could not reliably determine the server's fully qualified domain name,

... waiting apache2: Could not reliably determine the server's fully qualified domain
name, using 127.0.1.1 for ServerName
```

解决的方法是：

```bash
sudo vim /etc/apache2/sites-available/default
```

打开 `default` 文件后，在 `default` 文件顶端加入：`ServerName 127.0.0.1`

重启 `apache2` 就不会提示上述错误了。
```bash
sudo /etc/init.d/apache2 restart
```

#### 页面总是需要刷新才能显示

如果您使用的是 `ie6` 浏览器，请禁用服务器上的 `deflate` 模块。

```bash
sudo rm /etc/apache2/mods-enabled/deflate.* 
sudo /etc/init.d/apache2 restart
```

21．添加题目时出现 `warning/` 题目目录下数据没有自动生成

需要修改系统 `php.ini` ，给予 `php` 操作数据目录的权限。以下是推荐修改的设置。
```
sudo gedit /etc/php5/apache2/php.ini open_basedir=/home/judge/data:/var/www/JudgeOnline:/tmp
```

```ini
max_execution_time = 300    
; Maximum execution time of each script, in seconds
max_input_time = 600 
memory_limit = 128M 
; Maximum amount of memory a script may consume
post_max_size = 64M 
upload_tmp_dir =/tmp 
upload_max_filesize = 64M
```
重启 `Apache` 即可。

#### 添加的题目普通用户看不到

题目默认为删除状态，只有管理员能访问，当管理员确认题目没有问题后，可以点击 `ProblemList` 中的 `Resume` 启用题目。

#### Java 总是CE/RE

目前只支持 `sun` 原版 `jdk` 和 `openjdk`，其他 `jdk` 暂不能保证支持。

#### 管理员不能查看别人的源码

请给自己增加 `source_browser` 权限。

#### fckeditor 上传的图片在题目中无法显示

如果 `web` 安装位置不在 `/JudgeOnline` ，需要手工修改 `/fckeditor/editor/filemanager/connectors/php/config.php` 的第 37 行： `$Config['UserFilesPath'] ='/JudgeOnline/upload/'.date("Ym")."/" ;` 将 `JudgeOnline` 修改为对应的 `OJ web` 路径，如 `oj`。


