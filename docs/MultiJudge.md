# 建立分布式判题系统

HUSTOJ 支持一台数据库服务器，多台 `web` 服务器和多台判题服务器，以承担较高的访问负荷。

[最新参考](https://mp.weixin.qq.com/s?__biz=MzI1MTAwMTI2NA==&mid=2656403413&idx=1&sn=9101091ce4c8eaeadaf5c26e09a896e6&chksm=f2587f0ac52ff61cd1fb86600e25f168973a7b4d75010a3bfd38d95c8c11d2f89eef085915bb&token=1679186159&lang=zh_CN#rd)

### 创建用于从远程连接数据库的帐号

```sql
CREATE USER 'judge'@'%' identified by 'judge_pass';
GRANT ALL PRIVILEGES ON jol.* TO ‘judge’@’%’ WITHOUT GRANT OPTION;
flush privileges;
```
其中 `jol` 为数据库， `judge` 为帐号，`judge_pass` 为密码。

* 注意检查 `/etc/mysql/mysql.conf.d/mysqld.cnf` 确保在mysqld段落里存在设定 `bind-address = 0.0.0.0` 。

高负载的情况下最好设置更多的连接数：`max_connections = 512` 。

### 配置各 web 程序连接到数据库

修改 `include/db_info.inc.php` ：
```php
static $DB_HOST="数据库服务器ip";
static $DB_NAME="jol";
static $DB_USER="judge";
static $DB_PASS="judge_pass";
```

### 配置各判题程序连接到数据库，分配任务

```conf
OJ_HOST_NAME=数据库服务器ip
OJ_USER_NAME=judge
OJ_PASSWORD=judge_pass
OJ_DB_NAME=jol
```

```conf
OJ_TOTAL=判题机总数
OJ_MOD=本机编号，从0 开始
```

> 自R784 版本开始，不再需要分别配置 `OJ_TOTAL` 和 `OJ_MOD` ,全部设为 `1` 和 `0` 即可，所有正常工作的 `judge` 节点将自动分配当前任务。

### 复制测试数据目录到各判题机

> 从r1520 开始，使用 `HTTP_JUDGE` 方式不必单独复制数据，数据将从 `web` 服务器按需下载。

先要准备好远程访问,在主服务器执行：

```bash
sudo apt-get install openssh-server
```

!> 下列两种方法只需选择一种即可。

#### 主机分发

从主机向判题机复制：

```bash
scp -r /home/judge/data root@判题机ip:/home/judge/
```

或用同步命令：

```bash
rsync -vzrtopg –delete /home/judge/data root@判题机ip:/home/judge/
```

#### 判题机获取

判题机从主机复制：

```bash
scp -r root@主机ip:/home/judge/data /home/judge/
```

或用同步命令：

```bash
rsync -vzrtopg --progress --delete root@主机ip:/home/judge/data /home/judge/
```

### 在各判题机重启判题程序

```bash
sudo pkill judged && sudo judged
```

