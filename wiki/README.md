#summary 手动安装说明 installation manual
#labels Featured
HUST JOL安装说明 
	by zhblue(newsclan@gmail.com)
	
普通用户推荐用首页的安装脚本安装，本页只提供给高级用户做参考。

对Linux不熟悉的用户推荐使用[HUSTOJ_LiveCD](https://github.com/zhblue/hustoj/blob/master/wiki/HUSTOJ_LiveCD.md)安装，并浏览[FAQ](https://github.com/zhblue/hustoj/blob/master/wiki/FAQ.md)。
[HUSTOJ_LiveCD](https://github.com/zhblue/hustoj/blob/master/wiki/HUSTOJ_LiveCD.md) and [FAQ](https://github.com/zhblue/hustoj/blob/master/wiki/FAQ.md) is recommended

下面两个方法二选一

1、下载安装程序

debian/ubuntu用户:
```
sudo apt-get update&& sudo apt-get install subversion 
```
redhat/centos用户:
```
sudo yum update&& sudo yum install subversion.i386
```

```
svn checkout https://github.com/zhblue/hustoj/trunk/trunk/install  hustoj
cd hustoj
sudo bash install-interactive.sh

```

安装过程首先会询问您数据库的账号和密码，如果您提前安装了数据库，或使用其他服务提供的数据库服务，您应该已经获得了数据库的账号密码，那么请您确保输入正确。

如果您没有预先mysql服务器，安装安装过程中会自动安装，并触发root账户密码设置操作。这种情况下，第一次询问您数据库账号，请输入root,然后会有三次询问数据库密码的提示，请确保输入完全相同的三次密码，并自行记录下来，以做将来备份迁移时使用。


2、手动安装顺序如下[已经淘汰，仅供参考]：

下载源码
```
	http://code.google.com/p/hustoj/
	svn checkout https://github.com/zhblue/hustoj/trunk/trunk hustoj-read-only
```
创建数据库
```
	mysql
	set names utf8mb4;
	create database jol;
	use jol;
	source db.sql
```
配置Web界面
        cp -R web /home/judge/src/web
注册用户
	http://127.0.0.1/registerpage.php
	注册一个普通帐号zhblue
创建管理员
    insert into privilege(user_id,rightstr) values('zhblue','administrator');
    zhblue 为需要加管理员权限的帐号
管理员登录
          普通登录后访问http://127.0.0.1/admin
##########添加用户
useradd --uid 1536 judge
judge_client.cc:424  
        setuid(1536);       ==>       //设置判题用户//
编译判题服务器、客户端

```
          需要make g++ libmysql++-dev 
	(yum install gcc-c++  mysql-devel  / apt-get install g++ libmysql++-dev)
    mkdir /home/judge/
    mkdir /home/judge/etc
    mkdir /home/judge/data
    mkdir /home/judge/log
    mkdir /home/judge/run0

cd hustoj-read-only/core/
sudo ./make.sh
```

          判题配置文件 
```
########################/home/judge/etc/judge.conf###########################
    	OJ_HOST_NAME=localhost    #数据库地址
	OJ_USER_NAME=jol #数据库用户名
	OJ_PASSWORD=# 数据库密码
	OJ_DB_NAME=jol #数据库名
	OJ_PORT_NUMBER=3306 #数据库端口
	OJ_RUNNING=1 #可以同时运行几个进程
	OJ_SLEEP_TIME=1 #如果有空闲 要休眠多久
	OJ_TOTAL=1 #总共有多少台机器负责判题
	OJ_MOD=0 #当前机器评判取模为多少的提交
########################/home/judge/etc/judge.conf###########################
```
设置启动脚本
```
   with root or sudo
   echo "LANG=C /usr/bin/judged" > /etc/init.d/judged
   chmod +x  /etc/init.d/judged
   ln -s /etc/init.d/judged /etc/rc2.d/S93judged
   ln -s /etc/init.d/judged /etc/rc3.d/S93judged
```

您需要修改系统php.ini,给予php操作数据目录的权限。 以下是推荐修改的设置
```
       sudo gedit /etc/php5/apache2/php.ini 
       open_basedir =/home/judge/data:/var/www/JudgeOnline:/tmp  
       max_execution_time = 300     ; Maximum execution time of each script, in seconds
       max_input_time = 600 
       memory_limit = 256M      ; Maximum amount of memory a script may consume (16MB)
       post_max_size = 64M
       upload_tmp_dir =/tmp
       upload_max_filesize = 64M
      
```
    修改php.ini后apache需重启 

CentOS用户请看 [CentOS](https://github.com/zhblue/hustoj/blob/master/wiki/CentOS.md)
	[x64](https://github.com/zhblue/hustoj/blob/master/wiki/CentOSx86_64.md)
