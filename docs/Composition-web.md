# Web 解析

`web` 分两大部分，`前端` 和 `admin` 目录下的管理程序。

### 前端

无非是数据库的 CRUD 操作，关键功能是将用户提交的程序源码加入数据库的任务队列（`solution` 表、`souce_code` 表）。

### 管理程序

提供具有 `administrator` 等高级权限的账号管理试题、账号等方面的功能。其中 `FPS` 导入
导出程序主要为 `XML` 格式的数据处理。

特别的， `judged` 可以多重启动， 通过增加基准目录参数指定启动位置（ 默认 `/home/judge`），从而确定 `judge.conf` 的位置，并确定其他参数。因此不但可以一个 `web` 服务器下挂多个判题服务器，也可以一台物理机器上同时启动任意多个相互独立的 OnlineJudge 系统。实际使用中，使用开源的 `ispcp` 虚拟主机管理系统搭建多 `Web` 环境与 `hustoj` 协同工作取
得了良好效果。

### 比赛

1. 比赛根据数据通过率排名，而不只看 `AC` 数量

2. 数据库`solution` 表 `pass_rate` 字段表示该条通过率。

3. 把`contestrank.php` 中的 `solved` 字段变成浮点对待。

4. 修改积分方式，按照希望的方式积分。可能需要给 TM 增加字段 `$p_wa_best_rate` 记录每题最大通过率。

### 配置文件注释

配置文件： `db_info.inc.php` 。


配置                                  |                      注释
------------------------------------  |  ------------------------------------------
`static $DB_HOST="localhost";`        |  数据库的服务器地址。
`static $DB_NAME="jol";`              |  数据库名。
`static $DB_USER="root";`             |  数据库用户名。
`static $DB_PASS="root";`             |  数据库密码。
`static $OJ_NAME="HUSTOJ";`           |  OJ 的名字，将取代页面标题等位置 `HUSTOJ` 字样。
`static $OJ_HOME="./";`               |  OJ 的首页地址。
`static $OJ_ADMIN="root@localhost";`  |  管理员email。
`static $OJ_DATA="/home/judge/data";` |  测试数据所在目录，实际位置。
`static $OJ_BBS="discuss";`           |  论坛的形式，`discuss3` 为自带的简单论坛，`bbs` 为外挂论坛，参考 `bbs.php` 代码。
`static $OJ_ONLINE=false;`            |  是否使用在线监控，需要消耗一定的内存和计算，因此如果并发大建议关闭。
`static $OJ_LANG="en";`               |  默认的语言，中文为 `cn` 。
`static $OJ_SIM=true;`                |  是否显示相似度检测的结果。
`static $OJ_DICT=true;`               |  是否启用在线英字典。
`static $OJ_LANGMASK=1008;`           |  `1mC` `2mCPP` `4mPascal` `8mJava` `16mRuby` `32mBash` 用掩码表示的OJ 接受的提交语言，可以被比赛设定覆盖。1008 为只使用 `C` `CPP` `Pascal` `Java`。
`static $OJ_EDITE_AREA=true;`         |  是否启用高亮语法显示的提交界面，可以在线编程，无须IDE。
`static $OJ_AUTO_SHARE=false;`        |  `true`: 自动分享代码，启用的话，做出一道题就可以在该题的 `Status` 中看其他人的答案。
`static $OJ_CSS="hoj.css";`           |  默认的css,可以选择 `dark.css` 和 `gcode.css` , 具有有限的界面制定效果。
`static $OJ_SAE=false;`               |  是否是在新浪的云平台运行web 部分
`static $OJ_VCODE=true;`              |  是否启用图形登录、注册验证码。
`static $OJ_APPENDCODE=false;`        |  是否启用自动添加代码，启用的话，提交时会参考$OJ_DATA 对应目录里是否有 `append.c` 一类的文件，有的话会把其中代码附加到对应语言的答案之后，巧妙使用可以指定 `main` 函数而要求学生编写main 部分调用的函数。
`static $OJ_MEMCACHE=false;`          |  是否使用 `memcache` 作为页面缓存，如果不启用则用 `/cache` 目录
`static $OJ_MEMSERVER="127.0.0.1";`   |  `memcached` 的服务器地址
`static $OJ_MEMPORT=11211;`           |  `memcached` 的端口
`static $OJ_RANK_LOCK_PERCENT=0;`     |  比赛封榜时间的比率，如 5 小时比赛设为 `0.2` 则最后 1 小时封榜。
`static $OJ_SHOW_DIFF=false;`         |  显示 `WrongAnswer` 时的对比

![image](https://github.com/zhblue/hustoj/assets/3926566/93f61c7a-8b38-4012-b7e5-3d2ba2e54896)
