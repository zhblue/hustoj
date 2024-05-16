# 安装

### 注意事项

根据你选择的发行版不同，从下面三个脚本里选一个来用。

**不要相信百度来的长篇大论的所谓教程，那些都是好几年前的老皇历了，会导致不判题，不显示，不好升级等等问题。**
	
尤其**别装 `Apache`** ，如果已经安装，请先停用或卸载，以免 80 端口冲突。

**不要**使用 `LNMP` `LAMP` `Cpanel` 等其他面板程序提供的 `MySQL` `Nginx` `Apache` `PHP` 环境，安装脚本已经包含所有必须环境的安装。

若已经安装`宝塔`,推荐参考[宝塔环境安装hustoj说明](https://github.com/zhblue/hustoj/raw/master/docs/%E5%AE%9D%E5%A1%94%E7%B3%BB%E7%BB%9F%E5%AE%89%E8%A3%85HUSTOJ%E6%8C%87%E5%8D%97v0.2.docx)

**腾讯云用户请[换软件源](https://developer.aliyun.com/mirror/ubuntu)，增加 `multiverse` 。**

阿里云用户请百度 `阿里云 80端口`。

!> **安装完成，用admin作为用户名注册一个用户，自动成为管理员。**

### 自动识别系统安装脚本

```bash
wget https://github.com/zhblue/hustoj/raw/master/trunk/install/install+.sh -O install+.sh
sudo bash install+.sh
```

### 国内版自动识别ubuntu版本

```bash
wget http://dl.hustoj.com/install.sh -O install.sh
sudo bash install.sh
```

### 基于 Ubuntu 24.04 安装

```bash
wget http://dl.hustoj.com/install-ubuntu24.04.sh
sudo bash install-ubuntu24.04.sh
```

### 基于 Ubuntu 22.04 安装

```bash
wget http://dl.hustoj.com/install-ubuntu22.04.sh
sudo bash install-ubuntu22.04.sh
```


### 基于 Ubuntu 20.04 安装

```bash
wget http://dl.hustoj.com/install-ubuntu20.04.sh
sudo bash install-ubuntu20.04.sh
```

### 基于 Ubuntu 18.04 安装

**腾讯云用户请[换软件源](https://developer.aliyun.com/mirror/ubuntu)**

```bash
wget https://github.com/zhblue/hustoj/raw/master/trunk/install/install-ubuntu18.04.sh -O install-ubuntu18.04.sh
sudo bash install-ubuntu18.04.sh
```  

#### 演示视频（基于 Ubuntu Server 18.04 LTS）

Bilibili: [av967577292](https://www.bilibili.com/video/BV1Mp4y1C7Xx)

### 基于 Deepin15+ 安装

国内桌面用户 Deepin15.9+ （内置QQ微信WPS方便出题人本地测试，最新15.11测试通过）

```bash
wget https://github.com/zhblue/hustoj/raw/master/trunk/install/install-deepin15.9.sh -O install-deepin15.9.sh
sudo bash install-deepin15.9.sh
```
    
### 基于 CentOS 安装

假如你不得已非要用 CentOS7 （有的语言可能不支持，但是某些机架式服务器的 Raid 卡 Ubuntu 不认只能装 CentOS ），可以用下面脚本快速安装 OJ ：  

```bash
wget https://raw.githubusercontent.com/zhblue/hustoj/master/trunk/install/install-centos7.sh -O install-centos7.sh
sudo bash install-centos7.sh
```


### 基于 Docker 安装

Docker 安装，可用于快速体验 HUSTOJ 的全部功能，**可能存在未知的魔法问题，请慎重考虑用于生产环境！！！**
使用构建好的 Docker 镜像（GitLab CI/CD系统自动构建）

```shell
docker run -d           \
    --name hustoj       \
    -p 8080:80          \
    -v ~/volume:/volume \
    registry.gitlab.com/mgdream/hustoj
```

由于 Web 端、数据库、判题机全部被打包在同一个镜像，无法扩展，不推荐使用此镜像做分布式判题，另外请**不要**在 Docker 中使用 SHM 文件系统，会由于内存空间不足无法挂载沙箱环境而导致莫名其妙的运行错误。

部署后使用浏览器访问 [http://localhost:8080](http://localhost:8080) 。

### 基于其他发行版安装

其他的发行版，如树莓派的 `raspbian 8/9` `Ubuntu 14.04` 的安装脚本在 `install` 目录可以找到，但是不完善，安装后需要部分手工修复调整。

[脚本列表传送门](https://github.com/zhblue/hustoj/tree/master/trunk/install)

参考视频：<https://www.youtube.com/watch?v=hRap7ettUWc>

### LiveCD 下载安装

Linux不熟悉的用户推荐使用: `HUSTOJ_LiveCD` （发送 `livecd` 到微信公众号 `onlinejudge` ，获得百度云下载链接）

`HUSTOJ_Windows` （仅支持 XP ， QQ 群 `23361372` 共享文件）进行安装。

使用说明见 `iso` 中 `README` ，也可以参考 [LiveCD简介](/LiveCD)  

