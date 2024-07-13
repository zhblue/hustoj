二次开发
----

界面美化
--
web根目录的默认位置/home/judge/src/web
根目录下的文件用于处理逻辑，处理完成后，数据以变量存储，在文件末尾进行跳转或包含操作(require/include)，包含的目标为template/[模板名]下的同名文件。
目前默认的模板是[bs3](http://hmbb.hustoj.com/)，还有[sweet](http://sweet.hustoj.com/)、[ie](http://zjicm.hustoj.com/)可选，其他模板比较老，不推荐。

针对界面进行美化，可以先在template目录下执行
```
sudo cp -a bs3 newgui
```
然后修改db_info.inc.php，设置$OJ_TEMPLATE="newgui";

之后随意修改newgui目录下的文件，来进行美化处理。


利用判题内核/重新开发Web
--
数据库主要表结构
<img src="https://raw.githubusercontent.com/zhblue/hustoj/master/wiki/hustoj-db.png" >

如果希望自己用jsp/j2ee/php/python/nodejs/golang等二次开发，请保留数据库中的以下表和表已有字段：
```
users
solution
source_code
problem
contest_problem
reinfo
ceinfo
```
在solution表中插入新的记录，设置result=14获得自动编号solution_id，将源码关联solution_id插入source_code，再更新solution表中的result为0表示任务就绪，发送UDP消息给judged(默认UDP1536端口)，然后judged将轮询得到任务，调用judge_client去执行，执行时从source_code取出源码，执行完成后会更新ceinfo、solution、reinfo、problem、users、contest_problem等表。

了解以上流程后，您可以根据自己的需要自行编写新的Web或Client/App端。
