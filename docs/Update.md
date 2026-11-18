# 升级

### 使用脚本安装的用户

可以使用 `install` 目录中的 `update-hustoj` 进行升级。

```bash
sudo bash /home/judge/src/install/update-hustoj
```

升级脚本执行后，可能需要登陆 web 端管理后台，执行一次更新数据库。

#### 手工更新数据库

```bash
sudo su
cd /home/judge/src/install
./mysql.sh
source update.sql
```


### 宝塔安装的系统，因nginx/php的用户不同，可能需要文件属主

```bash
sudo su
cd /home
chown -R www:judge judge
chmod 751 -R judge
chmod 700 /home/judge/etc/judge.conf
chmod 750 /home/judge/src/web/include/db_info.inc.php

```



