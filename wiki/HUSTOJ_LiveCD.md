#summary 演示和安装一体化的光盘版 a all in one CD is on the way
#labels Featured,LiveCD,Phase-Deploy

Download:
22.04 LTS 版本（zhblue密码freeproblemset）
链接: http://cdn.m.hustoj.com:8090/HUSTOJ24.08.iso 


= Introduction =


为了帮助不了解HUSTOJ的用户快速获得使用体验，发布一张LiveCD.


没有刻录机？请看[http://mp.weixin.qq.com/s/GOH2BQIkvNRhHoexL-3KXQ  如何将LiveCD做成U盘]

In order to help people trying HUSTOJ, here is annoucing a LiveCD

[http://v.youku.com/v_show/id_XMTk5NTAyOTgw.html]

= Details =
* 本光盘是一张LiveCD，即无需安装、无需硬盘，只需要有光驱、内存大于2G的x86-64bit系统即可以光盘启动方式运行。*启动时看到“键盘=人形”提示，按下人任意键进入菜单选

* 虚拟机选第一项-试用，直接进入图形登录界面，以ubuntu/zhblue用户登录，密码freeproblemset，需要root可以用sudo su切换root。

* 物理机选第二项-安装,按照提示输入Alt+F1进入命令行，startx进入图形界面。

* 直接注册admin账号获得管理员权限。

* 启动后系统包含1道题目，可在官方群共享下载fps文件导入。

* 局域网可以访问和使用OJ，网络地址可用ifconfig在 lxterminal中查询。ubuntu用户有sudo权限。

* administrator of OJ is admin, password admin.

* 在图形终端中运行sudo ubiquity可启动图形化安装界面，将LiveCD安装至硬盘系统，适合不了解Linux的用户进行HUSTOJ的初次安装。请参考[InstallGuide]


*使用时如果已经联网，可以用sudo update-hustoj进行程序更新，获得最新版本。提示sudo密码freeproblemset



最低配置实测：
LiveCD System on k6-2 System
*K6-2-450 320M 1G-CF*

![image](https://github.com/zhblue/hustoj/blob/master/wiki/k6-2-system.jpg)

![image](https://github.com/zhblue/hustoj/blob/master/wiki/k6-2-status.jpg)

![image](https://github.com/zhblue/hustoj/blob/master/wiki/k6-2-HustOJ.jpg)

