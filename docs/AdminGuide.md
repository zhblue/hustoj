# HUSTOJ 系统管理员指南

> 管理员拥有 `administrator` 权限，可对系统进行全方位配置与维护。本文档涵盖日常运维、用户管理、权限配置、系统设置、安全管理等核心操作。

---

## 1. 管理员账号

### 1.1 获取管理员权限

系统安装后，注册一个名为 **admin** 的用户，该用户会自动获得 `administrator` 权限。

### 1.2 进入管理后台

以管理员身份登录后，点击页面右上角 **Admin → 管理**，即可进入管理后台。

---

## 2. 配置文件

系统有两个核心配置文件：

### 2.1 judge.conf

路径：`/home/judge/etc/judge.conf`（判题机配置文件）

| 参数 | 说明 |
|------|------|
| `OJ_HOST_NAME` | 数据库主机地址 |
| `OJ_USER_NAME` | 数据库用户名 |
| `OJ_PASSWORD` | 数据库密码 |
| `OJ_DB_NAME` | 数据库名（默认 `jol`） |
| `OJ_PORT_NUMBER` | 数据库端口 |
| `OJ_RUNNING` | 最大同时运行的判题线程数 |
| `OJ_SLEEP_TIME` | 判题轮询间隔（秒） |
| `OJ_SIM_ENABLE` | 是否启用相似度检测（0/1） |
| `OJ_HTTP_JUDGE` | 是否启用 HTTP 判题模式（0/1） |
| `OJ_OI_MODE` | 是否启用 OI 模式（0/1） |
| `OJ_SHM_RUN` | 是否使用 /dev/shm 共享内存运行（提速） |
| `OJ_CPU_COMPENSATION` | CPU 速度补偿参数（默认根据 bogomips 自动设置） |
| `OJ_HTTP_BASEURL` | HTTP 判题模式下的 OJ 访问地址 |
| `OJ_HTTP_USERNAME` | HTTP 判题账号（需有 Http_Judge 权限） |

### 2.2 db_info.inc.php

路径：`/home/judge/src/web/include/db_info.inc.php`（Web 端配置文件）

```php
// 数据库配置
static $DB_HOST="localhost";
static $DB_NAME="jol";
static $DB_USER="root";
static $DB_PASS="your_password";

// 系统名称（左上角显示）
static $OJ_NAME="HUSTOJ";

// 测试数据目录
static $OJ_DATA="/home/judge/data";

// 默认语言 (cn/en/ug/fa/ko/th)
static $OJ_LANG="cn";

// 验证码
static $OJ_VCODE=false;  // false=关闭，true=开启

// 相似度显示（实际检测开关在 judge.conf）
static $OJ_SIM=false;

// 默认界面主题
static $OJ_CSS="white.css";  // 可选: white/dark/blue/green/hznu.css 等

// 论坛开关 ("discuss3"/"bbs"/false)
static $OJ_BBS=false;

// 语言掩码（支持的语言种类）
static $OJ_LANGMASK=33554356;

// 代码编辑器高亮
static $OJ_ACE_EDITOR=true;

// 自动分享代码（通过后可见他人代码）
static $OJ_AUTO_SHARE=false;

// 打印服务
static $OJ_PRINTER=false;

// 内邮系统
static $OJ_MAIL=false;
```

> ⚠️ 修改 `db_info.inc.php` 后无需重启服务，直接刷新页面即可生效。请**务必注意语法正确**，分号、引号缺失可导致全站无法访问。

---

## 3. 用户与权限管理

### 3.1 权限系统概述

HUSTOJ 采用数据库权限表（`privilege` 表）管理权限，而非配置文件。常用权限类型：

| 权限标识 | 说明 |
|---------|------|
| `administrator` | 系统管理员（拥有所有权限） |
| `problem_editor` | 题目编辑者 |
| `contest_creator` | 比赛创建者 |
| `source_browser` | 可查看任意用户提交的源代码 |
| `Http_Judge` | 可通过 HTTP 接口提交判题任务 |
| `printer` | 打印服务权限 |
| `balloon` | 气球发放权限 |
| `password_changer` | 可修改他人密码 |

### 3.2 添加/删除权限

进入 **Admin → Privilege → 添加权限**：

```
用户ID：teacher001
权限类型：problem_editor,contest_creator
```

进入 **Admin → Privilege → 列表**，可查看所有已分配的权限并进行删除。

### 3.3 用户列表

进入 **Admin → User → 用户列表**：

- 筛选：按学校、注册时间、账号状态
- 启用/停用账号
- 点击用户名查看详细信息和解题统计

### 3.4 批量导入用户

进入 **Admin → User → Import**，上传 Excel 文件批量创建账号。

### 3.5 批量生成比赛账号

进入 **Admin → Contest → 比赛队账号生成器**：

1. 设置前缀、起始编号、数量
2. 点击生成
3. 导出为 Excel 分发

### 3.6 限制同 IP 注册数

在 `db_info.inc.php` 中设置：

```php
static $OJ_REG_SPEED=60;  // 每小时同一 IP 最多注册 60 个账号，0=不限制
```

---

## 4. 题目与数据管理

### 4.1 题目生命周期

1. **Reserved（停用）**：默认状态，普通用户不可见
2. **Available（启用）**：普通用户可见，可提交
3. **Defunct（已删除）**：逻辑删除，不可恢复

管理员可在题目列表中切换题目状态。

### 4.2 批量导入题目（Offline Import）

将题目目录结构整理为以下格式后压缩为 zip：

```
题号/
  in/
    1.in
    2.in
  out/
    1.out
    2.out
```

上传后系统自动识别并入库。

### 4.3 数据库更新

每次代码升级后，执行 **Admin → System → 更新数据库（Update Database）**，系统会自动补充新增的表和字段。重复执行不影响现有数据。

> 💡 也可通过命令行：`sudo bash /home/judge/src/install/fixing.sh`

### 4.4 数据库备份

进入 **Admin → System → 备份数据库**，系统会备份数据库、测试数据和图片到题号 0 目录。

---

## 5. 系统配置（Settings）

进入 **Admin → System → 设置（Settings）**，可直接在 Web 界面修改 `db_info.inc.php` 中的布尔开关、文本和数字参数，敏感配置项受保护不可随意修改。

---

## 6. 判题服务管理

### 6.1 重启判题服务

```bash
sudo pkill -9 judged
sudo judged
```

### 6.2 多判题机部署

judged 支持多实例运行：

```bash
sudo judged  # 第一个实例
sudo judged /home/judge2  # 第二个实例，指定不同 judge.conf 所在目录
```

每个实例读取各自目录下的 `judge.conf`，可对接同一个或不同的数据库，实现一台物理机运行多个独立的 OJ 实例。

### 6.3 HTTP 判题模式

在多台判题机环境下，可启用 HTTP 判题模式：

1. 创建专用判题账号（如 `judger1`）
2. 管理员给该账号添加 `Http_Judge` 权限
3. 编辑判题机 `judge.conf`：

```
OJ_HTTP_JUDGE=1
OJ_HTTP_BASEURL=http://your-oj-domain/
OJ_HTTP_USERNAME=judger1
OJ_HTTP_PASSWORD=yourpassword
```

4. 关闭 Web 端的验证码：`$OJ_VCODE=false`

### 6.4 强制重判

进入 **Admin → System → 重判（Rejudge）**，输入题号或提交编号，系统会清除旧的评测结果并重新评测。

---

## 7. 邮件系统配置

在 `db_info.inc.php` 中配置 SMTP 发件服务：

```php
static $SMTP_SERVER="smtp.qq.com";       // SMTP 服务器
static $SMTP_PORT=587;                    // 端口（QQ 用 587）
static $SMTP_USER="mailer@qq.com";         // 发件邮箱
static $SMTP_PASS="your_auth_password";    // 授权码（非登录密码）
```

配置后系统可发送：
- 用户注册激活邮件
- 密码找回邮件
- 判题结果通知（需开启）

---

## 8. 安全加固

### 8.1 验证码

```php
static $OJ_VCODE=true;  // 开启登录/注册验证码
```

### 8.2 注册审核

```php
static $OJ_REGISTER=true;         // 允许注册
static $OJ_REG_NEED_CONFIRM=false; // 注册后是否需要管理员审核
```

### 8.3 IP 限制登录

设置 `$OJ_LIMIT_TO_1_IP=true` 后，同一账号只能在固定 IP 登录，更换 IP 后自动登出。

### 8.4 考试模式

设置 `$OJ_EXAM_CONTEST_ID` 后，系统自动进入考试模式：
- 关闭自由练习
- 禁用讨论版
- 禁用代码外泄
- 禁用内邮

### 8.5 禁止访问敏感系统调用

编辑 `okcalls64.h` 或 `okcalls32.h`（根据系统架构选择），在对应语言的系统调用白名单数组中添加新的 CALLID 编号，可阻止恶意代码访问敏感 Linux 系统调用。

---

## 9. 系统升级

### 9.1 在线升级

```bash
sudo su
cd /home/judge/src/install
bash fixing.sh
```

### 9.2 版本检查

查看当前版本：`grep 'VERSION' /home/judge/src/web/include/const.inc.php`

---

## 10. 常见问题排查

| 症状 | 可能原因 | 解决方案 |
|------|---------|---------|
| 提交后一直 Pending | judged 未启动或数据库连接失败 | 执行 `sudo judged`，检查 judge.conf 中的数据库配置 |
| 编译错误但代码正确 | 编译器版本不兼容 | 检查 judge_client.cc 中的编译参数 |
| 页面空白 | db_info.inc.php 语法错误 | 用备份恢复或运行 fixing.sh 修复 |
| 无法上传大文件 | php.ini 的 upload_max_filesize 太小 | 修改 php.ini 后重启 php-fpm |
| Java 总是 CE/RE | JDK 版本不兼容 | 确认使用 sun jdk 或 openjdk |
| 相似度检测不生效 | judge.conf 中 OJ_SIM_ENABLE=0 | 改为 1 后重启 judged |

---

## 11. 目录结构参考

```
/home/judge/
├── etc/
│   └── judge.conf          # 判题机配置
├── data/
│   ├── 1000/               # 题号 1000 的测试数据
│   │   ├── 1.in
│   │   ├── 1.out
│   │   └── spj/            # 特殊裁判程序
│   └── 0/                  # 系统级备份目录
├── src/
│   ├── core/               # 判题核心代码（judge_client.cc 等）
│   ├── web/                # Web 前端代码
│   │   ├── include/        # 公共库
│   │   ├── admin/          # 管理后台
│   │   ├── lang/           # 语言包
│   │   └── template/       # 界面模板
│   └── install/            # 安装与修复脚本
└── log/                    # 判题日志
```
