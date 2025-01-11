# 集成 Moodle

### 基础要求

由于 Moodle 需要新版的 PHP 和 MySQL，所以需要安装在 Ubuntu18.04 以上的机器上。

### 预备工作

首先用脚本安装HUSTOJ，然后在 `/etc/nginx/sites-enbaled/default` 文件中 `location / { ` 这一行之前添加下面的内容：

```conf
if (!-e $request_filename) {
   rewrite "^(.*\.php)(/)(.*)$" $1?file=/$3 last;
}
```

查阅 `judge.conf` 获得数据库账户

```
cat /home/judge/etc/judge.conf
```


重新加载 `nginx` 配置文件

```
sudo service nginx reload
```

### 安装moodle

从 [这里](https://download.moodle.org/releases/latest/) 下载最新的moodle源码文件。

把下载到的 `moodle` 安装文件解压到 `/home/judge/src/web` 目录下，得到 `/home/judge/src/web/moodle` 目录

修改所有目录的属主为 `www-data`

```
chown -R www-data /home/judge/src/
```

刷新 OJ 页，得到右上角的 Moodle 入口，点击开始安装流程。

**moodle 的数据库默认 `moodle` ，表前缀 `mdl_` ，请不要修改**


### 关联两个系统

让学生在两个系统中用相同的用户名注册。



在HUSTOJ中添加一个比赛，获得比赛编号1000


在moodle中添加一个文本作业，标题命名为[OJ]-C1000，其中的1000表示OJ中对应的比赛编号。

### 添加触发器

``` 
DELIMITER $$
DROP trigger IF EXISTS `jol`.`tri_moodle` $$
create trigger tri_moodle
after update on solution
for each row
begin
   declare mark int;
   declare total int;
  
   select count(1) into total from contest_problem where contest_id=new.contest_id;
   if total>0 then
       select sum(ac)/total*100 into mark
          from (select max(pass_rate) ac from solution where user_id=new.user_id and contest_id=new.contest_id and problem_id>0 group by problem_id) s;
     
       call update_moodle(new.contest_id,new.user_id,mark);
       
   end if;
end $$
DELIMITER ;
```

### 添加存储过程

```
DELIMITER $$
CREATE PROCEDURE `update_moodle`(IN `cid` INT, IN `user_id` VARCHAR(20), IN `mark` INT)
top:BEGIN
   declare as_id int;
  declare u_id int;
  declare nowtime int;
  declare oldid int;
  set nowtime=UNIX_TIMESTAMP(now());
  set as_id=0;
  select m.id into as_id from
        moodle.mdl_assign m
         where m.name = concat('[OJ]-C',cid);
  if as_id=0 then
    leave top;
  end if;
  set u_id =-1;
  select m.id into u_id from moodle.mdl_user m where username=user_id;
  select mag.grade into oldid from moodle.mdl_assign_grades mag
      where assignment=as_id and userid=u_id;

  set oldid=-1;

  select id into oldid from moodle.mdl_assign_submission m
      where assignment=as_id and userid=u_id;
    if oldid =-1 then
 
       insert into moodle.mdl_assign_submission
             (assignment,userid,timecreated,timemodified,status,attemptnumber)
       values( as_id    ,u_id  ,nowtime    ,nowtime     ,'new' ,0);
       insert into  moodle.mdl_assign_grades(assignment,userid,timecreated,timemodified,grader,grade,attemptnumber)
       select ma.id,mas.userid,UNIX_TIMESTAMP( NOW( ) ),UNIX_TIMESTAMP( NOW( ) ),2,mark,0
                from  moodle.mdl_assign ma
                inner join  moodle.mdl_assign_submission mas on
                mas.assignment=ma.id and mas.status='new' 
                where concat(ma.id,',',mas.userid) not in (select  concat(assignment,',',userid)  from moodle.mdl_assign_grades);

   else

   
    update moodle.mdl_assign_grades
       set grade=mark,timemodified=nowtime where
        assignment=as_id and userid=u_id;

  end if;

  
END$$
DELIMITER ;
```

### 预期效果

当学生在hustoj中提交代码，moodle将在作业里同步显示他们的最新成绩。

![](images/moddle.png)


另外有知乎贴：[打通hustoj和moodle，构建多功能信息学竞赛系统](https://zhuanlan.zhihu.com/p/626006464) [相关代码](https://github.com/zhblue/hustoj/blob/master/docs/moodle-hustoj.zip)
