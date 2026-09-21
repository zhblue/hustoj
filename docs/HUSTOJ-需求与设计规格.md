# HUSTOJ 净室工程需求与设计规格说明书

> **Cleanroom Engineering Specification for HUSTOJ**
> 版本：1.0　基准日期：2026-09-21　文档状态：基线（Baseline）

---

## 0. 概述与文档约定

### 0.1 文档目的

本文档以**净室工程（Cleanroom Engineering）**方法，对开源在线判题系统 **HUSTOJ** 做**行为级逆向规格化（behavioral spec extraction）**。

其目标是：任何实现者在不阅读原实现源码的前提下，仅凭本文档即可用**任意编程语言、任意操作系统、任意关系型数据库**，构建出与原系统在**外部可观察行为上 1:1 一致**的新系统。

**"1:1 复刻"的判定边界**

| 类别 | 是否必须一致 | 说明 |
|---|---|---|
| 业务语义与流程分支 | 必须 | 例如"比赛中不暴露全局题号""AC 后提交不计罚时" |
| 数据契约 | 必须 | 表名、字段名、字段类型、默认值、触发器副作用 |
| 算法与数值结果 | 必须 | 罚时公式、排名排序、判题结果码、时间/内存换算 |
| 常量与默认值 | 必须 | 编译命令、超时值、路径、掩码、阈值 |
| 文件与目录布局（判题侧） | 必须 | `$OJ_DATA/<pid>/` 下的文件命名与语义 |
| 用户可见文案 | 建议 | 语言包 `$MSG_*` 键集合应保持一致 |
| 编程语言 / 框架 / 模板 | 不要求 | PHP 可用 Go/Java/Python 替代 |
| HTML/CSS 外观 | 不要求 | 六套模板（bs3/mdui/sweet/syzoj/sidebar/bshark）皆为可替换视图层 |
| 内部函数划分 | 不要求 | 本文档给出的是行为，不是实现 |

### 0.2 参考实现基线（Baseline）

| 项 | 值 |
|---|---|
| 源码树 | `hustoj-new/src/`（`web/` + `core/` + `install/`） |
| Web 层 | PHP 7/8 + PDO，页面约 530 个，模板目录 `web/template/<模板名>/` |
| 判题调度 | `core/judged/judged.cc`（C++，直连 MySQL 或走 HTTP） |
| 判题执行 | `core/judge_client/judge_client.cc`（C++，Linux 沙箱） |
| 数据库 | MySQL/MariaDB，库名 `jol`，默认引擎 MyISAM，`online` 表为 MEMORY |
| 数据库定义 | `install/db.sql`（332 行，含 2 个触发器 + 1 个存储过程） |
| 判题配置 | `/home/judge/etc/judge.conf`（`install/judge.conf` 为模板） |
| Web 配置 | `web/include/db_info.inc.php`（`static $OJ_*` 变量） |
| 判题根 | `oj_home = /home/judge`（`judged` 以 `argv[1]` 覆盖） |
| 测试数据根 | `$OJ_DATA = /home/judge/data` |
| 默认模板 | `syzoj` |

### 0.3 范围

**包含**：用户体系、权限模型、题目管理、提交与判题（含判题器沙箱与结果判定）、比赛（ACM/OI 排名、封榜、气球、统计）、状态页、题目列表、讨论区、公告、站内信、打印、代码分享、AI 辅助、远端 OJ、SaaS 多租户、后台管理、配置项全集、部署拓扑。

**不包含**（明确排除，复刻时可自由设计）：
- 前端视觉设计与 CSS 主题；
- 安装脚本本身（属部署工具，非系统行为）；
- `phpfm.php` 中第三方文件管理器的内部交互细节（仅规定其能力边界与安全风险）；
- 移动端 APK（`hustoj.apk`）。

### 0.4 术语表

| 术语 | 定义 |
|---|---|
| **OJ** | Online Judge，在线判题系统 |
| **题号 / problem_id** | 题目全局唯一 ID，`problem.problem_id`，自增起点 1000 |
| **场内序号 / num** | 题目在某场比赛内的 0-based 序号，对应字母编号 A/B/C…（`$PID[num]`） |
| **solution** | 一次提交记录，`solution` 表一行；`solution_id` 自增起点 1001 |
| **判题机 / judger** | 执行判题的进程或主机，由 `judged` 调度 |
| **测试数据** | 一组 `*.in` / `*.out` 文件，位于 `$OJ_DATA/<problem_id>/` |
| **spj** | Special Judge，特判程序；`problem.spj`：0=普通，1=特判，2=文本/填空判，3=交互判 |
| **langmask** | 语言掩码，**位为 1 表示该语言被禁用** |
| **封榜（lock/freeze）** | 比赛末段隐藏榜单真实结果 |
| **一血（first blood）** | 某题在本场比赛中第一个 AC 的提交 |
| **Upsolving** | 比赛结束后以练习方式继续提交该题 |
| **NOIP 模式** | 比赛标题含关键词（默认 `noip`）：赛中对学生隐藏结果与源码 |
| **投毒（poison）** | 对疑似机器人账号返回随机判题结果 |
| **oj_home** | 判题机根目录，默认 `/home/judge` |

### 0.5 需求编号约定

- `FR-<模块>-<序号>`：功能需求（Functional Requirement）
- `BR-<模块>-<序号>`：业务规则（Business Rule，含算法与常量）
- `NFR-<序号>`：非功能需求
- `IF-<序号>`：外部接口（Interface）

模块代码：`USR`(用户) `PRB`(题目) `SUB`(提交) `JDG`(判题) `CST`(比赛) `STT`(状态/列表) `SOC`(内容社交) `ADM`(后台) `CFG`(配置) `OPS`(部署)

### 0.6 行为描述模板

每个功能需求按下表四段式描述；涉及算法时给出伪代码或精确公式。

```
前置条件（Precondition）
输入（Input）
处理（Processing）—— 含分支、SQL、文件操作
输出/副作用（Output / Side-effect）
```

### 0.7 精度约定

- **时间**：数据库 `datetime` 均为**服务器本地时区**；PHP 侧在 `$OJ_FRIENDLY_LEVEL>=1` 时设 `date_default_timezone_set("Asia/Shanghai")` 并对连接执行 `SET time_zone='+8:00'`。判题侧 `judgetime` 由 SQL `NOW()` 生成。
- **耗时**：`solution.time` 单位为**毫秒**；`problem.time_limit` 单位为**秒**（`DECIMAL(10,3)`）。
- **内存**：`solution.memory` 与 `problem.memory_limit` 单位均为 **KB**。
- **通过率**：`solution.pass_rate` 为 `DECIMAL(4,3)`，取值 `0.000~1.000`。
- **字符串**：所有表默认 `utf8mb4`；提交源码存 `TEXT`（上限 64KB，见 `BR-SUB-05`）。
- **IP**：`varchar(46)`，兼容 IPv6。

### 0.8 文档的验证方式

第 10 章给出**验收测试清单**（Acceptance Checklist），每一条均为可在运行中直接观测的行为断言，建议作为复刻完成后的回归测试用例。

---

## 目录

| 章节 | 内容 |
|---|---|
| [0. 概述与文档约定](#0-概述与文档约定) | 目的、1:1 判定边界、参考基线、术语、编号约定 |
| [1. 系统架构与运行总览](#1-系统架构与运行总览) | 部署拓扑、请求生命周期、端到端链路、状态机、缓存、SaaS |
| [2. 数据模型](#2-数据模型) | 全表字段、索引、触发器、存储过程、完整性约束 |
| [3. 常量与编码](#3-常量与编码) | 语言表、结果码、langmask、contest_type 位、PID、气球、资源限制 |
| [4. 用户、会话与权限](#4-用户会话与权限) | 注册/登录/密码算法/记住我/第三方登录/找回密码/在线统计/排名 |
| [5. 题目子系统](#5-题目子系统) | 浏览可见性、数据目录布局、增删改、FPS 导入导出、积分 |
| [6. 提交与判题](#6-提交与判题) | submit 全链路、judged 调度、judge_client 编译/运行/比对/特判/回写、查重 |
| [7. 比赛子系统](#7-比赛子系统) | 准入、期间约束、ACM/OI 排名算法、封榜、榜单变体、统计、气球、打印 |
| [8. 状态页、列表、社区与后台](#8-状态页题目列表内容社区与系统功能) | status/题目列表/讨论/站内信/源码查看/AI/远端/后台权限矩阵 |
| [9. 配置项全集](#9-配置项全集) | Web `$OJ_*` 全表、friendly level、judge.conf 全表 |
| [10. 非功能、部署与验收](#10-非功能需求部署与验收) | NFR、部署运维、移植约束、陷阱清单、验收 Checklist、接口与页面清单 |

---


## 1. 系统架构与运行总览

### 1.1 系统组成

HUSTOJ 由四个可独立部署的部分组成：

```
┌──────────────────────────── Web 层（PHP） ────────────────────────────┐
│  页面脚本（problem.php / submit.php / status.php / contest.php …）     │
│  业务层 include/（db_info / my_func / const / problem / contest_…）     │
│  视图层 template/<模板>/（bs3 / bshark / mdui / sidebar / sweet / syzoj）│
│  后台 admin/（题目·比赛·用户·系统设置）                                  │
└────────────────────────────────┬──────────────────────────────────────┘
                                 │ PDO / SQL
                          ┌──────▼───────┐
                          │  数据库 jol   │  ← 唯一权威状态存储
                          └──────┬───────┘
                                 │ 轮询 result<2
┌────────────────────────────────┼──────────────────────────────────────┐
│  judged（调度守护进程，每判题机一个）                                     │
│    └─ fork → exec judge_client <sid> <runner_id> <oj_home>             │
│         └─ judge_client：chroot + setuid + setrlimit + ptrace          │
│              → 编译 → 逐测试点运行 → 比对 → 回写 solution                │
└───────────────────────────────────────────────────────────────────────┘
                                 ▲
                                 │ UDP 唤醒（默认 127.0.0.1:1536）
                           Web 层 submit.php
```

### 1.2 部署拓扑（FR-OPS-01）

**单机默认布局**

| 路径 | 用途 | 属主/权限要求 |
|---|---|---|
| `/home/judge/` | `oj_home` 判题根 | root |
| `/home/judge/etc/judge.conf` | 判题配置 | 600 |
| `/home/judge/etc/judge.pid` | judged 单实例锁 | fcntl `F_WRLCK` |
| `/home/judge/etc/java0.policy` | Java 安全策略模板 | 644 |
| `/home/judge/data/<problem_id>/` | 题目测试数据（`$OJ_DATA`） | www-data 可写（771，组 www-data） |
| `/home/judge/run<0..N-1>/` | 判题工作目录（`N = OJ_RUNNING`） | 700，属主 judge |
| `/home/judge/log/client.log` | 判题日志 | 追加 |
| `/home/judge/src/web/` | Web 根（nginx `root`） | www-data |
| `/home/judge/src/install/` | 安装/维护脚本 | — |
| `/home/judge/rsync.sh` | 可选：判题机间数据同步 | — |

**系统账户**

| 账户 | uid | 用途 |
|---|---|---|
| `judge` | 1536（缺失时兜底值） | 运行用户程序（降权目标） |
| `www-data` | 33 | Web 与 `cron.php` 运行身份（`OJ_WWW_UID`） |

**多机判题**：多台判题机各自运行 `judged`，通过 `OJ_TOTAL` / `OJ_MOD` 按 `solution_id % OJ_TOTAL = OJ_MOD` 分片取任务（见 `BR-JDG-03`）。HTTP 判题机（`OJ_HTTP_JUDGE=1`）不直连数据库，改由 `admin/problem_judge.php` 提供接口。

### 1.3 请求生命周期（Web 层，FR-OPS-02）

每个前台页面遵循固定骨架，复刻时必须保持该顺序（顺序影响缓存、语言、IP 判定结果）：

```
1. require include/db_info.inc.php
     ├─ 定义全部 static $OJ_* / $DB_* 配置
     ├─ 若存在上级目录 global.php 则 include（SaaS 覆盖）
     ├─ require include/pdo.php     （建立 PDO 连接）
     └─ require include/init.php
           ├─ 发送安全响应头
           ├─ session_set_cookie_params（httponly=true, samesite=Strict, path=/）
           ├─ session_start()
           ├─ 语言解析：session[OJ_LANG] > cookie[lang] > GET[lang] > Accept-Language
           │           允许值 {cn, ug, en, fa, ko, th}
           ├─ require lang/<lang>.php
           ├─ SaaS 子域配置载入（$OJ_SaaS_ENABLE 时按 HTTP_HOST 载入 SaaS/<host>.php）
           ├─ UA 判定：含 "w3m" 或 "MSIE" → 强制模板 bs3
           ├─ 客户端 IP 解析：HTTP_CLIENT_IP > REMOTE_ADDR，
           │     再被 HTTP_X_FORWARDED_FOR（取逗号首段）或 HTTP_X_REAL_IP 覆盖；
           │     filter_var(FILTER_VALIDATE_IP) 失败 → "0.0.0.0"
           ├─ 单 IP 登录限制（$OJ_LIMIT_TO_1_IP，见 FR-USR-09）
           ├─ 日志器初始化（$OJ_LOG_ENABLED）
           ├─ switch($OJ_FRIENDLY_LEVEL) 逐级穿透覆盖配置项（见 BR-CFG-01）
           └─ 计算 $coin = coin_earned + coin_bonus - coin_spent
2. 业务查询（pdo_query / mysql_query_cache）
3. require template/<$OJ_TEMPLATE>/<同名文件>.php   （渲染）
4. 可选 require include/cache_end.php               （写缓存）
```

**安全响应头（FR-OPS-03）**，每个响应必须包含：

```
X-XSS-Protection: 1; mode=block; sameorigin
X-Download-Options: noopen
Referrer-Policy: same-origin
```

仅当 `$OJ_CDN_URL` 非空时追加 `Access-Control-Allow-Origin: <OJ_CDN_URL>`。

### 1.4 提交—判题全链路（端到端，FR-OPS-04）

| # | 步骤 | 落库/落盘 | 说明 |
|---|---|---|---|
| 1 | 用户在 `submit.php` 提交 | — | 校验登录、验证码、语言掩码、冷却时间 |
| 2 | 插入 `solution` | `result=14` | 14 为占位态，防止判题机在源码写入前抢单 |
| 3 | 插入 `source_code_user`（用户原始代码）与 `source_code`（拼装后代码：prepend+source+append） | — | 两表内容不同，见 BR-SUB-03 |
| 4 | 测试运行时额外插入 `custominput` | — | 仅 `test_run` 为真 |
| 5 | 非测试运行：`problem.submit+1`；比赛中 `contest_problem.c_submit+1` | — | |
| 6 | 远端题：`result=16`；投毒账号：`result=rand(5,11)` 且 `judger='poisoner'` | — | |
| 7 | 最终 `UPDATE solution SET result=0`（或 16 / 随机值） | `result=0` 待判 | |
| 8 | Redis 队列（`$OJ_REDIS`）`LPUSH` solution_id | — | 可选 |
| 9 | UDP 唤醒 judged（`trigger_judge`） | — | 报文内容为 `$OJ_JUDGE_HUB_PATH` 或 solution_id |
| 10 | judged 轮询/被唤醒 → 领取任务 | `result=2`（CI） | 原子 UPDATE，见 BR-JDG-04 |
| 11 | fork+exec `judge_client <sid> <runner_id> <oj_home>` | — | |
| 12 | judge_client 编译 | 失败写 `compileinfo`，`result=11` | |
| 13 | 逐测试点运行 + 比对 | 运行信息写 `runtimeinfo` | |
| 14 | 回写 `result/time/memory/pass_rate/judger/judgetime` | — | |
| 15 | 更新 `users.solved/submit`、`problem.accepted`、`contest_problem.c_accepted` | — | |
| 16 | 触发器 `firstAC` 置 `first_time=1` 并累加积分 | `users.coin_earned += problem.coin` | 见 BR-PRB-07 |
| 17 | 可选查重（`OJ_SIM_ENABLE`） | 写 `sim` 表 | |
| 18 | 浏览器 `status.php` 轮询展示 | — | 间隔见 FR-STT-05 |

### 1.5 提交状态机（BR-SUB-01）

```sql
-- solution.result 全部取值
0  PD  Pending             排队
1  PR  Pending Rejudging   重判排队
2  CI  Compiling           编译中（已被判题机领取）
3  RJ  Running & Judging   运行中
4  AC  Accepted            通过
5  PE  Presentation Error  格式错误
6  WA  Wrong Answer        答案错误
7  TLE Time Limit Exceed   超时
8  MLE Memory Limit Exceed 超内存
9  OLE Output Limit Exceed 输出超限
10 RE  Runtime Error       运行错误
11 CE  Compile Error       编译错误
12 CO  Compile OK          编译完成
13 TR  Test Run            测试运行完成
14 MC  Manual Confirmation 手工确认/占位（新提交落库初始值）
15     Submitting          提交中（远端）
16 RP  Remote Pending      远端排队
17 RJ  Remote Judging      远端判题中
```

状态迁移：

```
(新建) ──► 14 ──► 0 ──► 2 ──► 3 ──► {4..11}
                  ▲                    │
                  └─── 重判 result=1 ──┘
远端题：0 ──► 16 ──► 17 ──► {4..11}
测试运行：0 ──► 13
人工判题（problem_judge.php manual）：任意 ──► 4/其他
```

> **注意**：`judged` 只领取 `result<2` 的记录（即 0 与 1）。14、16、17 不会被本地判题机领取。

### 1.6 缓存机制（FR-OPS-05）

页面级输出缓存由 `include/cache_start.php` / `cache_end.php` 包裹：

- 页面声明 `$cache_time`（秒）与 `$OJ_CACHE_SHARE`（是否跨用户共享）。
- 缓存键：`md5(session_id() . REMOTE_ADDR . REQUEST_URI)`，落文件 `cache/cache_<md5>.html`；`$OJ_MEMCACHE` 为真时存 memcached（`127.0.0.1:11211`）。
- 常见取值：`problem.php` 10s（`$OJ_CACHE_SHARE=false`）；`status.php` 2s；`ranklist.php` 30s；`recent-contest.php` 30s（共享）；带 `POST['keyword']` 的搜索页降为 1s。
- `submit.php` 成功后主动删除 `status.php` 的缓存文件/memcache 条目。

查询级缓存：`mysql_query_cache($sql, ...params)`，签名即 SQL+参数，TTL 同上。

### 1.7 多租户（SaaS）模型（FR-OPS-06）

- 开关 `$OJ_SaaS_ENABLE`。启用时 `$DOMAIN` 固定为主域名（如 `my.hustoj.com`），`$domain = basename(HTTP_HOST)`。
- 每个租户一个子域 `<user>.<DOMAIN>`；请求到达时按 `HTTP_HOST` 载入 `web/SaaS/<host>.php` 覆盖 `$DB_NAME`、`$OJ_DATA`、`$OJ_NAME` 等。
- 开通流程（`create_subdomain`）会：建库 `jol_<user>`、建账号 `hustoj_<user>`、生成 SaaS 配置文件、生成 `/home/saas/<user>/etc/judge.conf` 与 `java0.policy`、建 `run0/data/etc/log` 目录、为新库注入 `administrator` 与 `source_browser` 权限。
- 判题侧通过 `judgehub` 监听 UDP，按报文中的子路径（`$OJ_JUDGE_HUB_PATH`）拉起对应租户的 `judged` 实例。

---

## 2. 数据模型（Data Model）

> 数据库名 `jol`；除特别说明外，引擎 MyISAM，字符集 `utf8mb4`。
> 复刻时**字段名、类型、默认值必须逐项一致**；引擎可替换，但**触发器语义必须等价实现**（见 2.14）。

### 2.1 实体关系总览

```
users ──1:N──► solution ──1:1──► source_code / source_code_user
  │                 ├──1:0..1──► compileinfo
  │                 ├──1:0..1──► runtimeinfo
  │                 ├──1:0..1──► custominput
  │                 ├──1:0..1──► sim
  │                 └──1:0..1──► solution_ai_answer
  └──1:N──► privilege（权限行）
  └──1:N──► loginlog
  └──1:N──► mail（收/发）
  └──1:N──► topic ──1:N──► reply
  └──1:N──► printer / balloon / share_code

problem ──1:N──► solution
  └──N:M──► contest（经 contest_problem）

contest ──1:N──► contest_problem（num 为场内序号）
  └──1:N──► solution（contest_id + num）
  └──1:N──► topic（cid）

独立表：news（公告）、online（在线会话，MEMORY）、openai_task_queue（AI 任务）
```

### 2.2 `users` 用户表

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `user_id` | varchar(48) **PK** | '' | 用户名，登录唯一标识 |
| `email` | varchar(100) NULL | NULL | 邮箱 |
| `submit` | int | 0 | 提交总数（冗余统计） |
| `solved` | int | 0 | 通过总数（去重题数，冗余统计） |
| `defunct` | char(1) | 'N' | 'Y'=封禁 |
| `ip` | varchar(46) | '' | 注册 IP（也用于"最后登录 IP"展示） |
| `accesstime` | datetime NULL | NULL | 最后访问时间 |
| `volume` | int | 1 | 默认题册（= 上次浏览的题目列表页码） |
| `language` | int | 1 | 默认语言 |
| `password` | varchar(32) NULL | NULL | 密码散列（见 BR-USR-03；注意长度 32 但存 base64 串，实际依赖 MySQL 宽松行为） |
| `reg_time` | datetime NULL | NULL | 注册时间 |
| `expiry_date` | date | '2099-01-01' | 账号到期日；登录校验 `expiry_date >= CURDATE()` |
| `nick` | varchar(20) | '' | 昵称 |
| `school` | varchar(20) | '' | 学校 |
| `parent_phone` | varchar(20) | '' | 家长手机（少儿场景） |
| `group_name` | varchar(16) | '' | 班级/分组，用于组权限继承与统计 |
| `activecode` | varchar(16) | '' | 激活码（18 位 token 经 `active.php` 激活） |
| `starred` | int | 0 | 是否 star 了官方仓库（缓存位） |
| `coin_earned` | int | 0 | 做题获得积分 |
| `coin_bonus` | int | 0 | 教师奖励积分 |
| `coin_spent` | int | 0 | 已消耗积分 |

**派生量**：`$coin = coin_earned + coin_bonus - coin_spent`（`init.php` 每次请求计算，供视图展示）。

### 2.3 `problem` 题目表

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `problem_id` | int **PK AUTO_INCREMENT** | 起点 1000 | 题号 |
| `title` | varchar(200) | '' | 标题 |
| `description` | mediumtext NULL | — | 题面描述（HTML） |
| `input` | mediumtext NULL | — | 输入说明 |
| `output` | mediumtext NULL | — | 输出说明 |
| `sample_input` | text NULL | — | 样例输入 |
| `sample_output` | text NULL | — | 样例输出 |
| `spj` | char(1) | '0' | 0=普通 1=特判 2=文本/填空判 3=交互判 |
| `hint` | mediumtext NULL | — | 提示 |
| `source` | varchar(100) NULL | — | 来源；同时被当作**分类标签**（空格分隔多标签） |
| `in_date` | datetime NULL | — | 添加时间 |
| `time_limit` | DECIMAL(10,3) | 0 | 时间限制（**秒**） |
| `memory_limit` | int | 0 | 内存限制（**KB**） |
| `defunct` | char(1) | 'N' | 'Y'=隐藏/保留（新题默认 'Y'） |
| `accepted` | int | 0 | 通过次数（冗余统计） |
| `submit` | int | 0 | 提交次数（冗余统计） |
| `solved` | int | 0 | 通过人数（冗余） |
| `coin` | int | 1 | AC 本题可得积分 |
| `remote_oj` | varchar(16) NULL | — | 远端 OJ 名（非空表示远端题） |
| `remote_id` | varchar(32) NULL | — | 远端原题号 |

### 2.4 `solution` 提交表（系统核心表）

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `solution_id` | int unsigned **PK AI** | 起点 1001 | 提交 ID |
| `problem_id` | int | 0 | 题号（测试运行时为 0） |
| `user_id` | char(48) | — | 提交者 |
| `nick` | char(20) | '' | 提交者昵称**快照**（改名不影响历史） |
| `time` | int | 0 | 耗时（**毫秒**） |
| `memory` | int | 0 | 峰值内存（**KB**） |
| `in_date` | datetime | '2016-05-13 19:24:00' | 提交时间 |
| `result` | smallint | 0 | 判题结果码（见 BR-SUB-01） |
| `language` | int unsigned | 0 | 语言编号（见 3.1） |
| `ip` | char(46) | — | 提交 IP |
| `contest_id` | int | 0 | 所属比赛，0=练习提交 |
| `valid` | tinyint | 1 | 有效标记 |
| `num` | tinyint | -1 | 场内序号（比赛提交）；-1=非比赛 |
| `code_length` | int | 0 | 代码字节长度 |
| `judgetime` | timestamp NULL | CURRENT_TIMESTAMP | 判题完成时间 |
| `pass_rate` | DECIMAL(4,3) unsigned | 0 | 通过率 0.000~1.000 |
| `first_time` | tinyint(1) | 0 | 一血/首次 AC 标记（触发器置位） |
| `lint_error` | int unsigned | 0 | 静态检查错误数 |
| `judger` | char(16) | 'LOCAL' | 判题机标识；投毒时 'poisoner' |
| `remote_oj` | char(16) | '' | 远端 OJ |
| `remote_id` | char(32) | '' | 远端提交 ID |

**索引**（性能与排名正确性依赖部分索引）：

```
KEY uid(user_id)                         KEY pid(problem_id)
KEY res(result)                          KEY cid(contest_id)
KEY idx_uid_pid(user_id,problem_id)      KEY idx_uid_pid_res(user_id,problem_id,result)
KEY idx_contest_result(contest_id,result)
KEY idx_contest_num(contest_id,num,result)
KEY idx_contest_user_id(contest_id,user_id,solution_id)
KEY idx_cid_result_num_sid(contest_id,result,num,solution_id)
KEY fst(first_time)
KEY idx_solution_in_date(in_date)
```

### 2.5 `source_code` / `source_code_user`

两表结构相同：`solution_id int PK` + `source text NOT NULL`。

- `source_code_user`：用户**原始**提交代码（原样保存，用于"我的代码"展示）。
- `source_code`：判题机使用的**拼装后**代码（`prepend` + 源码 + `append`，且 Python 会追加编码声明）。判题与"他人代码展示"读此表。

> `source_code_user` 由 `CREATE TABLE ... LIKE source_code` 生成。

### 2.6 `contest` 比赛表

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `contest_id` | int **PK AI** | 起点 1000 | |
| `title` | varchar(255) NULL | — | 标题 |
| `start_time` | datetime NULL | — | |
| `end_time` | datetime NULL | — | |
| `defunct` | char(1) | 'N' | 'Y'=停用/删除 |
| `description` | text NULL | — | 说明 |
| `private` | tinyint | 0 | 0=公开 1=私有 |
| `langmask` | int | 0 | 语言掩码，**位 1 = 禁用** |
| `password` | char(16) | '' | 进入密码（明文存储，长度上限 16） |
| `contest_type` | smallint unsigned | 0 | 位标志，见 3.4 |
| `subnet` | varchar(255) | '' | 允许参赛的 IP 段，逗号分隔多段 CIDR |
| `user_id` | varchar(48) | 'admin' | 创建者 |

### 2.7 `contest_problem` 比赛题目关联

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `problem_id` | int | 0 | |
| `contest_id` | int NULL | — | |
| `title` | char(200) | '' | 比赛内显示标题（可覆盖） |
| `num` | int | 0 | 场内序号（0-based → A/B/C…） |
| `c_accepted` | int | 0 | 比赛内通过次数 |
| `c_submit` | int | 0 | 比赛内提交次数 |

`KEY Index_contest_id(contest_id)`（无主键）。

### 2.8 `privilege` 权限表

| 字段 | 类型 | 默认 | 说明 |
|---|---|---|---|
| `user_id` | char(48) | '' | |
| `rightstr` | char(30) | '' | 权限标识 |
| `valuestr` | char(11) | 'true' | 权限值 |
| `defunct` | char(1) | 'N' | |

`KEY user_id_index(user_id)`（无主键，可重复行）。

**`rightstr` 取值全集**

| rightstr | 含义 |
|---|---|
| `administrator` | 超级管理员 |
| `problem_editor` | 题目编辑 |
| `problem_importer` | 题目导入 |
| `problem_verifiter` | 题目审核（原文拼写如此，须保持一致） |
| `tag_adder` | 题目标签 |
| `source_browser` | 查看任意源码 |
| `contest_creator` | 创建比赛 |
| `user_adder` | 批量添加用户 |
| `http_judge` | HTTP 判题机 / 人工判题 |
| `password_setter` | 修改他人密码 |
| `printer` | 打印 |
| `balloon` | 气球 |
| `vip` | 会员（自动获得所有 `[VIP]` 比赛准入） |
| `problem_start` / `problem_end` | 判题题号范围（valuestr 为数字） |
| `service_port` | 服务端口 |
| `m<cid>` | 比赛 `<cid>` 的管理权 |
| `c<cid>` | 比赛 `<cid>` 的参赛权（私有赛） |
| `p<pid>` | 题目 `<pid>` 的编辑权 |
| `s<pid>` | 题目 `<pid>` 的全量源码查看权 |

### 2.9 结果/日志类表

| 表 | 字段 | 说明 |
|---|---|---|
| `compileinfo` | `solution_id int PK`, `error text` | 编译错误信息，写入时截断 **16384 字节** |
| `runtimeinfo` | `solution_id int PK`, `error text` | 运行时错误 / 差异信息，同样截断 16384 |
| `custominput` | `solution_id int PK`, `input_text text` | 测试运行的自定义输入 |
| `sim` | `s_id int PK`, `sim_s_id int NULL`, `sim int NULL` | 查重结果，相似度 0-100 |
| `solution_ai_answer` | `solution_id int PK`, `answer mediumtext` | AI 生成的题解 |
| `loginlog` | `log_id int PK AI`, `user_id varchar(48)`, `password varchar(40)`, `ip varchar(46)`, `time datetime` | 登录日志。**注意 `password` 字段存的是明文尝试密码或标记串**（`'login ok'` / `'login fail'` / `'user added'` / `'c<cid>'`） |

### 2.10 内容类表

`news`（公告）：`news_id PK AI(起点1004)`, `user_id`, `title varchar(200)`, `content mediumtext`, `time datetime`, `importance tinyint`(0), `menu int`(0，1=显示于菜单), `defunct char(1)`('N')

`topic`（讨论主题）：`tid PK AI`, `title varbinary(60)`, `status int`(0=可见/1=锁定/2=删除), `top_level int`(0=普通/2=笔记/3=公告), `cid int NULL`, `pid int`, `author_id varchar(48)`；`KEY(cid,pid)`

`reply`（回复）：`rid PK AI`, `author_id`, `time datetime`, `content text`, `topic_id int`, `status int`(0), `ip varchar(46)`；`KEY(author_id)`

`mail`（站内信）：`mail_id PK AI`, `to_user`, `from_user`, `title varchar(200)`, `content text`, `new_mail tinyint`(1=未读), `reply tinyint`(0), `in_date datetime`, `defunct char(1)`('N')；`KEY uid(to_user)`

`share_code`：`share_id PK AI(1000)`, `user_id`, `title varchar(32)`, `share_code text`, `language varchar(32)`, `share_time datetime`

### 2.11 服务类表

`printer`：`printer_id PK AI`, `user_id char(48)`, `in_date datetime`, `status smallint`(0=待打印/1=已打印), `worktime timestamp`, `printer char(16)`('LOCAL'), `content text`

`balloon`：`balloon_id PK AI`, `user_id char(48)`, `sid int`, `cid int`, `pid int`, `status smallint`(0=待发/1=已发)

`online`（**MEMORY 引擎**）：`hash varchar(32) PK`（= `md5(session_id().ip)`）, `ip varchar(46)`, `ua varchar(255)`, `refer varchar(4096)`, `lastmove int`(Unix 秒), `firsttime int`, `uri varchar(255)`。存活窗口 `ONLINE_DURATION = 600` 秒。

`openai_task_queue`：`id bigint unsigned PK AI`, `user_id varchar(40)`, `task_type varchar(24)`, `solution_id bigint`(0), `problem_id bigint`(0), `request_body mediumtext`, `status tinyint`(0待处理/1处理中/2完成/3失败), `response_body mediumtext`, `error_message text`, `create_date datetime`, `update_date datetime`；`KEY idx_status_create(status,create_date)`, `KEY idx_user_status(user_id,status)`

### 2.12 初始数据（BR-CFG-02）

`install/db.sql` 预置两条 `news`：
1. `news_id` 起于 1004，标题 `HelloWorld!`，作者 `zhblue`，时间 `2009-06-13 18:00:00`，`importance=0, menu=0`，内容为系统说明（`$OJ_INDEX_NEWS_TITLE` 默认引用此标题）。
2. 标题 `题单模板`，时间 `2024-08-06 06:54:43`，`menu=1`，内含 `[plist=1001,...]小节名[/plist]` 题单语法示例。

安装脚本随后导入 `install/A+B.xml`（FPS 样例题）。

### 2.13 存储过程 `DEFAULT_ADMINISTRATOR(user_name)`（BR-USR-01）

```
privileged_count = SELECT COUNT(1) FROM privilege;
IF privileged_count == 0 THEN
    INSERT INTO privilege VALUES(user_name, 'administrator', 'true', 'N');
END IF
```

即：**第一个注册/初始化的用户自动成为管理员**（这是 HUSTOJ "注册 admin 即得管理员" 的实现原理）。

### 2.14 触发器（必须等价实现）

**`firstAC`（BEFORE UPDATE ON solution，逐行）**

```
IF NEW.result == 4 THEN
    acTimes = SELECT COUNT(1) FROM solution
              WHERE problem_id=NEW.problem_id AND result=4
                AND first_time=1 AND user_id=NEW.user_id;
    acCoin  = SELECT IFNULL(coin,1) FROM problem WHERE problem_id=NEW.problem_id;
    IF acTimes == 0 THEN
        SET NEW.first_time = 1;
        IF OLD.first_time == 0 THEN
            UPDATE users SET coin_earned = coin_earned + acCoin
                     WHERE user_id = NEW.user_id;
        END IF;
    END IF;
END IF;
```

语义：某用户某题**首次** AC 时置 `first_time=1`，并给该用户加 `problem.coin` 积分（缺省 1）。

初始化时还有一次性回填：

```sql
UPDATE solution s JOIN (
  SELECT user_id,problem_id,MIN(solution_id) AS first_solution_id
  FROM solution WHERE result=4 GROUP BY user_id,problem_id
) t ON s.solution_id=t.first_solution_id SET s.first_time=1;
```

**`simfilter`（BEFORE INSERT ON sim，逐行）**

```
new_user_id = SELECT user_id FROM solution WHERE solution_id=NEW.s_id;
old_user_id = SELECT user_id FROM solution WHERE solution_id=NEW.sim_s_id;
IF old_user_id == new_user_id THEN SET NEW.s_id = 0; END IF;   -- 自己抄自己不计
```

### 2.15 数据完整性约束（显式规则，非外键）

MyISAM 无外键，以下约束由应用代码保证，复刻时必须显式实现：

| 规则 | 行为 |
|---|---|
| 删除题目 `problem_del.php` | 删 `problem` 行；删 `privilege WHERE rightstr='p<pid>'`；`UPDATE solution SET problem_id=0, result=13 WHERE problem_id=<pid>`；删除 `$OJ_DATA/<pid>` 目录；重设自增 `max(problem_id)`（<1000 取 1000） |
| 删除比赛题目 | 先删 `contest_problem`，再重建 |
| 用户封禁 | `users.defunct='Y'`，登录被拒（`WHERE defunct='N'`），榜单中排除 |
| 比赛停用 | `contest.defunct='Y'` |

---

## 3. 常量与编码表（Constants & Encodings）

> 本章所有表均为**数组下标 = 存储值**的映射表，复刻时必须保持下标与值的对应关系。

### 3.1 语言编号表（BR-PRB-01）

`include/const.inc.php` 中 `$language_name`（26 项）与 `$language_ext`（25 项）：

| 编号 | 名称 | 扩展名 | 主文件名 | 备注 |
|---|---|---|---|---|
| 0 | C | `c` | `Main.c` | |
| 1 | C++ | `cc` | `Main.cc` | |
| 2 | Pascal | `pas` | `Main.pas` | |
| 3 | Java | `java` | `Main.java` | |
| 4 | Ruby | `rb` | `Main.rb` | |
| 5 | Bash | `sh` | `Main.sh` | |
| 6 | Python | `py` | `Main.py` | 按内容判定 python2/python3 |
| 7 | PHP | `php` | `Main.php` | |
| 8 | Perl | `pl` | `Main.pl` | |
| 9 | C# | `cs` | `Main.cs` | mono |
| 10 | Obj-C | `m` | `Main.m` | GNUstep |
| 11 | FreeBasic | `bas` | `Main.bas` | |
| 12 | Scheme | `scm` | `Main.scm` | guile |
| 13 | Clang | `c` | `Main.c` | 与 0 同扩展名 |
| 14 | Clang++ | `cc` | `Main.cc` | 与 1 同扩展名 |
| 15 | Lua | `lua` | `Main.lua` | |
| 16 | JavaScript | `js` | `Main.js` | |
| 17 | Go | `go` | `Main.go` | |
| 18 | SQL | `sql` | `Main.sql` | sqlite3 |
| 19 | Fortran | `f95` | `Main.f95` | |
| 20 | Matlab | `m` | `Main.m` | octave；**与 Obj-C(10) 扩展名冲突** |
| 21 | Cobol | `cob` | `Main.cob` | |
| 22 | R | `R` | `Main.R` | |
| 23 | Scratch3 | `sb3` | `Main.sb3` | 走文件上传分支 |
| 24 | Cangjie | `cj` | `Main.cj` | 仓颉 |
| 25 | UnknownLanguage | （无） | — | `$language_ext` 只有 25 项，代码 25 越界 |

> **复刻注意**：`$language_ext` 数组长度 25（下标 0..24），而 `$language_name` 长度 26。涉及 `1<<count($language_ext)` 的掩码计算按 **25** 位。

### 3.2 判题结果码（BR-SUB-01，完整）

| 值 | 短码 | 展示文案键 | CSS 类（`$judge_color`） |
|---|---|---|---|
| 0 | PD | Pending | `label gray` |
| 1 | PR | Pending Rejudging | `label label-info` |
| 2 | CI | Compiling | `label label-warning` |
| 3 | RJ | Running & Judging | `label label-warning` |
| 4 | AC | Accepted | `label label-success` |
| 5 | PE | Presentation Error | `label label-danger` |
| 6 | WA | Wrong Answer | `label label-danger` |
| 7 | TLE | Time Limit Exceeded | `label label-warning` |
| 8 | MLE | Memory Limit Exceeded | `label label-warning` |
| 9 | OLE | Output Limit Exceeded | `label label-warning` |
| 10 | RE | Runtime Error | `label label-warning` |
| 11 | CE | Compile Error | `label label-warning` |
| 12 | CO | Compile OK | `label label-info` |
| 13 | TR | Test Run | `label label-success` |
| 14 | MC | Manual Confirmation | `label lable-gray`（原文拼写） |
| 15 | — | Submitting | `label label-info` |
| 16 | RP | Remote Pending | `label label-info` |
| 17 | RJ | Remote Judging | `label label-warning` |

判题机侧 C 常量：`OJ_WT0=0 OJ_WT1=1 OJ_CI=2 OJ_RI=3 OJ_AC=4 OJ_PE=5 OJ_WA=6 OJ_TL=7 OJ_ML=8 OJ_OL=9 OJ_RE=10 OJ_CE=11 OJ_CO=12 OJ_TR=13 OJ_MC=14`。

### 3.3 语言掩码 `langmask`（BR-CFG-03）

**语义：第 n 位为 1 ⇒ 语言 n 被禁用。**

后台创建比赛时的计算（精确）：

```php
$langmask = 0;
foreach ($_POST['lang'] as $t)      // 表单勾选的是"允许"的语言编号
    $langmask += 1 << $t;
$langmask = ((1 << count($language_ext)) - 1) & (~$langmask);
```

校验（submit.php）：`if ($langmask & (1 << $language)) → 拒绝提交`。

**系统默认 `$OJ_LANGMASK = 33554356`（0x1FFFFB4）**，其效果（位 1 = 禁用）：

| 允许 | 0 C、1 C++、3 Java、6 Python、25 UnknownLanguage |
|---|---|
| 禁用 | 2 Pascal、4 Ruby、5 Bash、7 PHP、8 Perl、9 C#、10 Obj-C、11 FreeBasic、12 Scheme、13 Clang、14 Clang++、15 Lua、16 JavaScript、17 Go、18 SQL、19 Fortran、20 Matlab、21 Cobol、22 R、23 Scratch3、24 Cangjie |

SaaS 子站默认 `OJ_LANGMASK=2097084`（允许 C/C++/Python/Cobol/R/Scratch3/Cangjie/Unknown）。

### 3.4 比赛类型位标志 `contest_type`（BR-CST-01）

`contest_type` 为位或（多选）。生效条件：`contest_locked()` 判定 `contest_type & level > 0`（**或**标题包含 `$OJ_NOIP_KEYWORD`，默认 `noip`）**且** `start_time < now < end_time`。

| 位 | 值 | 含义（`$contest_locks` 顺序） |
|---|---|---|
| bit0 | 1 | 考试中不允许查看源码 |
| bit1 | 2 | 禁止下载 |
| bit2 | 4 | 禁止查看榜单 |
| bit3 | 8 | 禁止查看他人比赛状态（status 只看自己） |
| bit4 | 16 | NOIP 警告模式（对学生隐藏结果/源码/榜单） |
| bit5 | 32 | 禁止显示 WA 差异对比 |
| bit6 | 64 | 禁止赛后补题（Upsolving） |
| bit7 | 128 | 仅保留最后一次提交（OI 单解模式） |
| bit8 | 256 | 禁止 AI 求助 |

判定函数（精确 SQL）：

```sql
-- contest_locked($contest_id, $level)
SELECT c.contest_id FROM contest c
 WHERE contest_id=? AND (c.contest_type & ? > 0 OR c.title LIKE ?)
   AND start_time < ? AND end_time > ?;

-- problem_locked($problem_id, $level)：同上，但经 contest_problem 关联
SELECT c.contest_id FROM contest c
 INNER JOIN contest_problem cp ON c.contest_id=cp.contest_id AND cp.problem_id=?
 WHERE (c.contest_type & ? > 0 OR c.title LIKE ?) AND start_time < ? AND end_time > ?;
```

> `problem.php` 使用 `problem_locked($id, 28)`（= 4|8|16）来隐藏 `accepted/submit` 与 `hint`。

### 3.5 比赛内题号字母（BR-CST-02）

`$PID` 为预生成数组：`0→A, 1→B, …, 25→Z, 26→AA, 27→AB, …, 51→AZ, 52→BA …`（共约 350 项）。
等价于：`n < 26 ? chr(65+n) : chr(65 + (n-26)/26) . chr(65 + (n-26)%26)`。

气球模块使用 `chr(ord('A') + pid)`（仅支持 26 题以内）。

### 3.6 气球颜色/名称（BR-CST-03）

```php
$ball_color = ['#66cccc','red','green','pink','yellow','violet','magenta','maroon','olive','chocolate'];
$ball_name  = ['蒂芙妮蓝','红','green','pink','yellow','violet','magenta','maroon','olive','chocolate'];
```

按 `pid`（场内序号）取模循环（10 色）。

### 3.7 敏感词表（BR-SOC-01）

`$bad_words`（38 条，新华社禁用语）：
`装逼, 草泥马, 特么的, 撕逼, 玛拉戈壁, 爆菊, JB, 呆逼, 本屌, 齐B短裙, 法克鱿, 丢你老母, 达菲鸡, 装13, 逼格, 蛋疼, 傻逼, 绿茶婊, 你妈的, 表砸, 屌爆了, 买了个婊, 已撸, 吉跋猫, 妈蛋, 逗比, 我靠, 碧莲, 碧池, 然并卵, 日了狗, 吃翔, XX狗, 淫家, 你妹, 浮尸国, 滚粗`

用于用户名、讨论内容过滤（`has_bad_words()`，大小写不敏感 `stristr`）。

### 3.8 判题侧 OJ_* 常量与默认值（BR-JDG-01）

取自 `judge_client.cc` / `judged.cc` 的硬编码默认（配置文件未指定时的回退值）：

| 常量 | 默认 | 含义 |
|---|---|---|
| `max_running` | 3（judged）/ 3（client） | 并发判题槽数 |
| `sleep_time` | 1（judged）/ 3（client） | 轮询间隔秒 |
| `oj_tot` / `oj_mod` | 1 / 0 | 多机分片 |
| `oj_lang_set` | `"0,1,3,6"`（代码内回退）；发行版 judge.conf 为 `0..20` | 可判语言集合 |
| `java_xms` / `java_xmx` | `-Xms32m` / `-Xmx256m`（代码）；judge.conf 为 `-Xms64M`/`-Xmx128M` | JVM 堆 |
| `cc_std` / `cpp_std` | `-std=c99`（gcc>9.3 时 `c17`）/ `-std=c++11`（gcc>9.3 时 `c++14`） | 编译标准 |
| `cc_opt` | `-O2` | 优化级别 |
| `judge_uid/gid` | `getpwnam("judge")`，缺失兜底 **1536** | 降权用户 |
| `www_uid` | 33 | www-data |
| `OJ_JAVA_TIME_BONUS` | 2 | Java 时间加成倍数 |
| `OJ_JAVA_MEMORY_BONUS` | 64 (MB) | Java 内存加成 |
| `OJ_CPU_COMPENSATION` | 1.0 | CPU 快慢补偿系数 |
| `OJ_IGNORE_ESOL` | 1 | 忽略行末空白 |
| `OJ_FULL_DIFF` | 1 | WA 对比详情级别 0-3 |
| `OJ_SHM_RUN` | 0 | 用 /dev/shm 作工作目录 |
| `OJ_USE_MAX_TIME` | 0 | 取最大单点耗时（否则累计） |
| `OJ_TIME_LIMIT_TO_TOTAL` | 1（judge.conf） | 按总时间判 TLE |
| `OJ_COMPILE_CHROOT` | 1 | 编译时 chroot |
| `OJ_COPY_DATA` | 0 | NOIP 文件输入输出 |
| `OJ_PYTHON_FREE` | 0 | Python 不做 chroot |
| `OJ_SIM_ENABLE` | 0 | 查重开关 |
| `OJ_INTERNAL_MARK` | 0 | 单点部分分 |
| `OJ_RAW_TEXT_DIFF` | 1 | 填空题显示正确答案 |

### 3.9 资源限制常量（BR-JDG-02，判题机侧精确值）

| 项 | 值 | 说明 |
|---|---|---|
| `STD_MB` | 1 MB = 1048576 | 基准 |
| 编译 CPU 上限 | `RLIMIT_CPU = 50`（Java 60）秒 + `alarm(cpu)` | |
| 编译输出上限 | `RLIMIT_FSIZE = 500 MB` | |
| 编译地址空间 | Pascal/Java/Go/R/SB3：按架构 `<<12`（x86_64 为 4GB）；否则 `<<11`（2GB）；Java 不设 | |
| 运行 CPU 上限 | `ceil(time_limit / cpu_compensation) + 1` 秒；`RLIMIT_CPU.max = cur+1`；并 `alarm(cur)` | **每个测试点加 1 秒** |
| 运行文件大小 | `RLIMIT_FSIZE.cur = 512MB`，`max = 513MB` | 超限触发 SIGXFSZ → OLE |
| 运行栈 | `RLIMIT_STACK = 256MB` | |
| 运行地址空间 | `cur = STD_MB * mem_limit / 2 * 3`，`max = STD_MB * mem_limit * 2`；仅 `lang < 3(Java)` 或 ObjC/Clang/Clang++/Go | |
| 进程数 `RLIMIT_NPROC` | Go/C#/Java/R/Scratch3 = 880；Ruby/Python/Scheme/JS/Cangjie/Matlab = 200；Bash = 3；其他 = 1 | |
| 全局钳制 | `time_limit > 300 或 < 0 ⇒ 1`；`mem_limit > 2048 或 < 1 ⇒ 2048` | |
| judged 子进程 | `RLIMIT_CPU=800`、`RLIMIT_FSIZE=1GB`、`RLIMIT_AS`（x86_64/aarch64 32GB、i386/arm 2GB、mips 4GB）、`RLIMIT_NPROC=800*max_running` | |

---

## 4. 用户、会话与权限子系统

### 4.1 会话模型（FR-USR-01）

**会话键命名规则（强制）**：所有会话键前缀为 `$OJ_NAME . '_'`。例如 `$OJ_NAME='HUSTOJ'` 时键为 `HUSTOJ_user_id`。这是多实例同域共存的基础。

**Cookie 参数**（`session_set_cookie_params`）：`lifetime=0, path='/', httponly=true, samesite='Strict'`（未启用 `secure` 与 `domain`）。

**会话键全集**

| 键（省略前缀） | 类型 | 来源 |
|---|---|---|
| `user_id` | string | 登录成功 |
| `nick` | string | 登录成功 |
| `group_name` | string | 登录成功 |
| `administrator` / `source_browser` / `contest_creator` / `problem_editor` / `problem_importer` / `problem_verifiter` / `tag_adder` / `user_adder` / `password_setter` / `http_judge` / `printer` / `balloon` / `vip` | bool 或 valuestr | `privilege` 表逐行写入 |
| `m<cid>` / `c<cid>` / `p<pid>` / `s<pid>` | bool | 同上 |
| `OJ_LANG` | string | 语言切换 |
| `vcode` / `vfail` | string/bool | 验证码 |
| `activecode` | string | 注册激活 |
| `lost_user_id` / `lost_key` | string | 找回密码流程 |
| `postkey` / `getkey` / `csrf_keys` | string/array | CSRF |
| `refer` | string | — |

**权限载入 SQL（登录与 `refresh-privilege.php` 共用）**

```sql
SELECT * FROM privilege
 WHERE user_id = ?
    OR (user_id = ? AND rightstr LIKE 'c%');   -- 第二个 ? 为该用户的 group_name（组权限继承）
```

对每一行：`$_SESSION[$OJ_NAME.'_'.$row['rightstr']] = ($row['valuestr'] !== '' ? $row['valuestr'] : true)`。
若会话含 `vip`，额外把所有标题含 `[VIP]` 的比赛的 `c<contest_id>` 置真。
登录后执行 `session_regenerate_id()`。

### 4.2 注册（FR-USR-02）

**前置**：`$OJ_REGISTER=true` 且 `$OJ_LOGIN_MOD='hustoj'`。

**输入字段**：`user_id`, `nick`, `school`, `email`, `password`, `rptpassword`。

**校验**
1. `user_id` 通过 `is_valid_user_name()`：逐字符检查，仅允许 `[A-Za-z0-9_-]`，且**首字符额外允许 `*`**；长度 3~48；不含 `$bad_words`。
2. `nick` 长度 ≤ 20；为空时取 `user_id`。`school` ≤ 20。
3. `email` 非空、`filter_var(FILTER_VALIDATE_EMAIL)`、长度 ≤ 100。
4. `password` 长度 ≥ 6，且 `password === rptpassword`。
5. 重名：`users.user_id` 已存在则拒绝；SaaS 主域下 `$OJ_NAME == user_id` 亦拒绝。
6. 频率：`$OJ_REG_SPEED=60` —— 同一 `ip` 或同一 `email` 在 1 小时内注册数 > 60 则拒绝，并向 `$OJ_ADMIN` 发警告邮件。

**处理**

```sql
INSERT INTO users(user_id,email,ip,accesstime,password,reg_time,nick,school,group_name,defunct,activecode)
VALUES(?,?,?,NOW(),?,NOW(),?,?,?,?,?)
```

- `password = pwGen($password)`（见 BR-USR-03）
- `group_name = getMappedSpecial($user_id)`：取 `substr(user_id,2,4)` 查表 `{'0701':'人智','0708':'网工','5702':'电科','5701':'软工','0207':'数技'}`，命中则返回 `substr(user_id,0,2).映射值`，否则 ''。
- `defunct = $OJ_REG_NEED_CONFIRM ? 'Y' : 'N'`
- `activecode = $OJ_EMAIL_CONFIRM ? getToken(18) : ''`

**输出**：未开启审核/邮件确认时直接写入会话并跳转 `index.php`（SaaS 主域跳 `modifypage.php#MyOJ`）；否则提示待激活。

**激活（FR-USR-03）**：`active.php`，`strlen(code)==18` 时

```sql
UPDATE users SET defunct='N', activecode='' WHERE activecode=? AND defunct='Y'
```

### 4.3 密码算法（BR-USR-03，必须逐字节等价）

```php
function pwGen($password, $md5ed = false) {
    if (!$md5ed) $password = md5($password);          // 明文 → 32位小写hex md5
    $salt = substr(sha1(rand()), 0, 4);               // 4 字节随机 salt（十六进制字符）
    return base64_encode(sha1($password . $salt, true) . $salt);  // 20字节二进制 + salt → base64
}

function pwCheck($password, $saved) {
    if (isOldPW($saved)) {                            // 旧式：32 位十六进制
        $mpw = isOldPW($password) ? $password : md5($password);
        return hash_equals($mpw, $saved);
    }
    $svd  = base64_decode($saved);
    $salt = substr($svd, 20);                         // 末 4 字节为 salt
    $p    = isOldPW($password) ? $password : md5($password);
    return hash_equals(base64_encode(sha1($p . $salt, true) . $salt), $saved);
}

function isOldPW($s) {                                // 长度32且全为 [0-9a-fA-F]
    if (strlen($s) != 32) return false;
    for ($i = strlen($s) - 1; $i >= 0; $i--) {
        $c = $s[$i];
        if (('0'<=$c && $c<='9') || ('a'<=$c && $c<='f') || ('A'<=$c && $c<='F')) continue;
        return false;
    }
    return true;
}
```

**密文格式**：`base64( sha1_raw( md5_hex(明文) . salt4 ) . salt4 )`，共 `base64(24 字节) = 32 字符`，恰好匹配 `varchar(32)`。
**兼容**：旧账户为纯 `md5(明文)` 32 位 hex，必须支持登录。`update_pw.php`（运行一次后自删）用 `pwGen($oldpw, true)` 批量升级。

**复杂度校验 `too_simple()`**：满足以下 5 条中 **≥2** 条才算通过：长度≥8、含数字、含大写、含小写、含特殊字符 `[!@#$%^&*(),.?":{}|<>]`。

### 4.4 登录（FR-USR-04）

**输入**：`user_id`, `password`, `vcode`（当 `$OJ_VCODE` 为真时）。

**校验顺序**
1. **"记住我"自动登录**（`$OJ_LONG_LOGIN`）：读 Cookie `$OJ_NAME.'_user'` 与 `$OJ_NAME.'_check'`，按 BR-USR-05 校验。
2. 验证码：`$_POST['vcode'] === $_SESSION[$OJ_NAME.'_vcode']`，否则置 `vfail=true` 并回退。
3. **失败次数限制**（`$OJ_LOGIN_FAIL_LIMIT=5`）：统计 `loginlog` 中 `password='login fail'` 且 5 分钟内的记录；`user_fail > 5` 或 `ip_fail > 20` 拒绝。
4. 查库：

```sql
SELECT user_id, password FROM users
 WHERE user_id=? AND defunct='N' AND expiry_date >= CURDATE()
```

（`expiry_date` 列缺失时自动 `ALTER TABLE` 补齐。）用 `pwCheck()` 比对；成功后 `UPDATE users SET accesstime=NOW(), ip=?`。

5. 无论成败写 `loginlog(user_id, password, ip, time)`，`password` 字段值：成功 `'login ok'`，失败 `'login fail'`（**明文尝试密码也会被记录，用于审计**）。

**输出**：管理员→`admin/`；`contest_creator`→`contest.php?my`；`$OJ_NEED_LOGIN`→`index.php`；否则 `history.go(-2)`。

### 4.5 记住我（BR-USR-05，精确算法）

```
C_user  = cookie[$OJ_NAME.'_user']
C_check = cookie[$OJ_NAME.'_check']
取该用户 password(pw) 与 accesstime(acc)
C_res = ''
for i in 0 .. strlen(pw)-1:
    C_res .= chr( 39 + ( ord(pw[i])*ord(pw[i]) + ord(acc[i % strlen(acc)])*ord(pw[i]) ) % 88 )
C_res = sha1(C_res)
合法条件：substr(C_check, -1) === strval( (strlen(C_res) * strlen(C_res)) % 7 )
合法则 $login = C_user
```

### 4.6 单 IP 登录限制（FR-USR-09）

`$OJ_LIMIT_TO_1_IP=true` 且已登录时，取该用户 `loginlog` 最后一条 IP；若与当前 IP 不同，则强制登出（清 session、清 cookie、`session_destroy()`）并提示。
`$OJ_LIP_URL` 存在且 Cookie `lip` 有值时，IP 取 `long2ip(intval($_COOKIE['lip']))`（内网穿透场景）。

### 4.7 第三方登录（FR-USR-06，`$OJ_LOGIN_MOD`）

| 模式 | 机制 | 用户名/密码处理 |
|---|---|---|
| `hustoj` | 本地 | — |
| `qq` | OAuth2，回调校验 `state` | 用户名 `qq_<openid>`，密码固定 `$OJ_OPENID_PWD='8a367fe87b1e406ea8e94d7d508dcf01'`，首次自动建号 |
| `weibo` / `renren` | OAuth2 | 同 QQ 模式 |
| `ldap` | `ldap://127.0.0.1:389`，`dn="uid=<user_id>,ou=people,dc=example,dc=com"` | `ldap_bind` 成功即登录 |
| `discuz` | 连 discuz 库 `uc_members` | 校验 `md5(md5($password).$salt)` |
| `moodle` | 连 moodle 库 `mdl_user` | 校验 `md5($password . '<moodle_salt>')` |

非 `hustoj` 模式时禁止注册。

### 4.8 找回密码（FR-USR-07）

1. `lostpassword.php`：校验 `user_id` + `email` 匹配且含 `@`；`lost_key = getToken(16)` 存入 `$_SESSION[$OJ_NAME.'_lost_key']` 与 `_lost_user_id`；发送含该 key 的邮件。
2. 邮件配置未生效判定：`$SMTP_USER === 'mailer@qq.com'`（哨兵值）时跳过真实发信。
3. `lostpassword2.php`：比对 session 中的 `lost_user_id/lost_key`；成功后

```sql
UPDATE users SET password = pwGen(<lost_key>) WHERE user_id=?
```

> **注意**：`lost_key` 即成为新密码。

### 4.9 资料修改（FR-USR-08，`modify.php`）

可改字段：`email`, `school`, `nick`, `password`。
- 修改需验证旧密码 `pwCheck($_POST['opassword'], $row['password'])`。
- 新密码非空时须通过 `too_simple()` 且两次输入一致。
- `$OJ_NICK_IMMUTABLE=false` 时允许改 `nick`，并同步 `UPDATE solution SET nick=? WHERE user_id=?`。
- 所有文本经 `htmlentities($s, ENT_QUOTES, 'UTF-8')` 转义。

### 4.10 登出与刷新权限

- `logout.php`：清 `user_id` session、清两个 cookie、`session_destroy()`，跳 `index.php`。
- `refresh-privilege.php`：重跑权限查询覆写 session；未登录提示并跳登录页。

### 4.11 验证码（FR-USR-10，`vcode.php`）

- 生成 `get_rand_string($len, $type)`，写入 `$_SESSION[$OJ_NAME.'_vcode']`。
- 默认 4 位数字（`type='n'`）；当 `$_SESSION[$OJ_NAME.'_vfail']` 为真时改为 **8 位字母**（`type='c'`）。
- 图像输出为 **GIF**（`imagegif`），随机背景色与干扰点，字体 `include/Vera.ttf`（`imagettftext`）。
- `submit.php` 额外规则：当 `SELECT count(1) FROM solution WHERE result<4` 的结果 **> 50** 时，强制开启验证码（队列拥堵保护）。

### 4.12 在线统计（FR-USR-11）

`$OJ_ONLINE=true` 时，每次请求 upsert `online` 表：`hash = md5(session_id() . ip)`，字段 `ip/ua/refer/lastmove/firsttime/uri`；并清理 `lastmove < time()-600` 的过期行。

### 4.13 用户主页与排名（FR-USR-12）

`userinfo.php` 展示：去重 AC 题数、去重提交题数、实时排名、积分、结果分布饼图、按天提交/AC 折线；管理员可见近 50 条 `loginlog`。处于 NOIP/封榜比赛的题目不计入统计。

`ranklist.php`：
- 排序 `ORDER BY solved DESC, submit, reg_time`，每页 50。
- 排除 `$OJ_RANK_HIDDEN`（默认 `"'admin','zhblue'"`，直接拼进 SQL `IN (...)`）。
- 时间范围 `scope`：`d`=当日、`w`=本周一、`m`=本月 1 日、`y`（默认）=本年 1 月 1 日；通过 `solution_id` 切分时间窗，只统计 `result=4 AND first_time=1` 的去重题数。
- 缓存 30 秒。

### 4.14 权限判定方式（BR-USR-06）

系统**没有**统一的 `hasPrivilege()` 函数，全部采用 `isset($_SESSION[$OJ_NAME.'_'.$rightstr])` 直接判定。复刻时应提供等价的 `hasPriv(name)`，语义为"会话中存在该键"。

典型判定组合：

| 场景 | 判定 |
|---|---|
| 后台入口 | `administrator \|\| contest_creator \|\| user_adder \|\| problem_editor \|\| password_setter` |
| 编辑某题 | `administrator \|\| problem_editor \|\| p<pid>` |
| 管理某比赛 | `administrator \|\| contest_creator \|\| m<cid>` |
| 进入某私有赛 | `c<cid> \|\| m<cid> \|\| administrator` |
| 查看任意源码 | `administrator \|\| source_browser` |

---

## 5. 题目子系统

### 5.1 题目浏览（FR-PRB-01，`problem.php`）

**两种访问模式**

| 模式 | URL | 说明 |
|---|---|---|
| 练习模式 | `problem.php?id=<problem_id>` | `$pr_flag = true` |
| 比赛模式 | `problem.php?cid=<cid>&pid=<num>` | `$co_flag = true`，先 `require contest-check.php` |

**练习模式可见性（三分支）**

```
if (administrator || problem_verifiter || contest_creator || problem_editor):
    SELECT * FROM problem WHERE problem_id=?
elif ($OJ_FREE_PRACTICE):
    SELECT * FROM problem WHERE defunct='N' AND problem_id=?
else:
    SELECT * FROM problem WHERE problem_id=? AND defunct='N'
      AND NOT EXISTS (
        SELECT 1 FROM contest_problem cp
          INNER JOIN contest c ON cp.contest_id=c.contest_id
         WHERE cp.problem_id=? AND (c.end_time>NOW() AND c.defunct='N' OR c.private='1')
      )
```

> 设计意图（原文注释）：防止学生赛前通过改 URL 偷看赛题、防止赛外试错规避罚时。

**"题目被比赛占用"提示**：当查询无结果但该题确实存在于未结束/私有比赛中，列出这些比赛（`$MSG_PROBLEM_USED_IN`），除非设置了 `$OJ_EXAM_CONTEST_ID` 或 `$OJ_ON_SITE_CONTEST_ID`。

**比赛模式**：`contest-check.php` 通过后，

```sql
SELECT * FROM problem WHERE problem_id = (
  SELECT problem_id FROM contest_problem WHERE contest_id=? AND num=?
)
```

**NOIP/锁定处理（FR-PRB-02）**

```
flag = 该题当前处于「标题含 $OJ_NOIP_KEYWORD 的比赛」中（start<now<end）
if (flag || problem_locked($id, 28)):      # 28 = 4|8|16
    accepted = '<font color="red"> ? </font>'
    submit   = '<font color="red"> ? </font>'
    if (!$OJ_NOIP_HINT && !(administrator || contest_creator)):
        hint = $MSG_NOIP_NOHINT
```

**输出**：`require template/<模板>/problem.php`；页面缓存 10 秒（`$OJ_CACHE_SHARE=false`）。

### 5.2 测试数据目录布局（BR-PRB-02，判题契约核心）

目录：`$OJ_DATA/<problem_id>/`（默认 `/home/judge/data/<problem_id>/`）

| 文件 | 语义 |
|---|---|
| `sample.in` / `sample.out` | 样例（题面展示） |
| `test<N>.in` / `test<N>.out` | 后台添加的测试数据 |
| `1.in` / `1.out`、`2.in` … | 通用测试数据；**判题机按 `*.in` 的字典序枚举** |
| `spj` / `spj.c` / `spj.cc` | 特判程序（源码或已编译） |
| `tpj` / `tpj.c` / `tpj.cc` | testlib 风格特判 |
| `upj` | 用户自定义判题（优先于 tpj/spj） |
| `interactor` / `interactor.cc` | 交互题交互器（spj=3） |
| `*.dic` | 词典（随题拷贝进工作目录） |
| `input.name` / `output.name` | NOIP 风格：内容为一个文件名，程序从该文件读输入/写输出 |
| `solution.name` | 内容为一个文件名；提交时上传文件名必须与之相同（否则源码被替换为错误提示） |
| `prepend.<ext>` / `append.<ext>` / `template.<ext>` | 代码拼装模板，`<ext>` 为该语言扩展名 |
| `data.in` / `data.out` | 填空/选择题（spj=2）数据 |
| `sb3/<solution_id>.sb3` | Scratch3 工程文件 |
| `ac/<sid>.<ext>` | 该题 AC 源码库（查重用，保留 90 天） |
| `judge.conf` | 题目级判题配置，覆盖系统配置 |

**测试数据就绪判定**：判题机对目录执行 `scandir` 过滤 `*.in`；目录不存在 → 记日志 `No such dir` 并退出。运行某测试点前若 `spj==0 && 对应的 .out 不存在` → 记 `missing out file` 并按 **RE** 处理。

### 5.3 新建题目（FR-PRB-03，`admin/problem_add.php`）

**表单字段**：`title, time_limit, memory_limit, description, input, output, sample_input, test_input, sample_output, test_output, hint, source, spj, coin, contest_id, remote_oj, remote_id`

**处理**
1. 文本中的半角逗号 `,` 替换为 `&#44;`（防 CSV 注入）；`sample_*`/`test_*` 经 `normalizeSpaces()` 规整（Unicode 空白→普通空格，删除零宽字符与 BOM）。
2. `addproblem(...)`：

```sql
INSERT INTO problem(title,time_limit,memory_limit,description,input,output,
                    sample_input,sample_output,hint,source,spj,in_date,defunct)
VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW(),'Y')
```

**新题默认 `defunct='Y'`（隐藏）**，需管理员启用后才可见。
3. 若 `contest_id > 0`：

```sql
INSERT INTO contest_problem(problem_id,contest_id,num) VALUES(?,?,?)
```
`num` = 该比赛已有题目数（`SELECT count(*) FROM contest_problem WHERE contest_id=?`）。
4. 写测试数据目录：`mkdir("$OJ_DATA/$pid")` 后 `mkdata()` 写 `sample.in/sample.out/test.in/test.out`，内容为 `preg_replace("\r\n" → "\n")`。
5. `UPDATE problem SET coin=? WHERE problem_id=?`（默认 1）。
6. 授权：`INSERT INTO privilege(user_id,rightstr) VALUES(?,'p<pid>')`，并写 `$_SESSION['p<pid>']=true`。
7. 跳转文件管理器（`phpfm`）。

### 5.4 编辑题目（FR-PRB-04）

权限：显示需 `administrator || problem_editor`；保存需 `administrator || problem_editor || p<pid>`。

```sql
UPDATE problem SET title=?,time_limit=?,memory_limit=?,description=?,input=?,output=?,
  sample_input=?,sample_output=?,hint=?,source=?,spj=?,remote_oj=?,remote_id=?,
  in_date=NOW(),coin=? WHERE problem_id=?
```

编辑页**只改写 `sample.in/sample.out`**（当二者均非空且文件已存在时）；`test.in/test.out` 仅在添加页处理。

### 5.5 代码拼装（BR-PRB-03，`submit.php` 侧）

```
source_user = 用户提交的原始代码
prepend_file = $OJ_DATA/<pid>/prepend.<ext>
append_file  = $OJ_DATA/<pid>/append.<ext>

if ($OJ_APPENDCODE && prepend_file 存在):  source = prepend内容 . "\n" . source
if ($OJ_APPENDCODE && append_file 存在):   source = source . "\n" . append内容
if (language == 6 && spj != 2):            source = "# coding=utf-8\n" . source
```

`source_user` 存 `source_code_user`，拼装结果存 `source_code`。

### 5.6 FPS XML 交换格式（BR-PRB-04）

**导出**（`problem_export_xml.php`）：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE fps PUBLIC "-//freeproblemset//..." "http://hustoj.com/fps.current.dtd" >
<fps version="1.6" url="https://github.com/zhblue/freeproblemset/">
  <generator name="HUSTOJ" url="https://github.com/zhblue/hustoj/" />
  <item>
    <title><![CDATA[…]]></title>
    <url><![CDATA[http://HOST/problem.php?id=PID]]></url>
    <time_limit unit="s"><![CDATA[1]]></time_limit>
    <memory_limit unit="mb"><![CDATA[128]]></memory_limit>
    <description>…</description>
    <input>…</input>
    <output>…</output>
    <sample_input>…</sample_input>
    <sample_output>…</sample_output>
    <test_input name="1"><![CDATA[…]]></test_input>
    <test_output name="1"><![CDATA[…]]></test_output>
    <hint>…</hint>
    <source>…</source>
    <remote_oj>…</remote_oj>
    <remote_id>…</remote_id>
    <solution language="C++">…</solution>
    <prepend language="C++">…</prepend>
    <template language="C++">…</template>
    <append language="C++">…</append>
    <spj language="C++">…</spj>
    <tpj language="C++">…</tpj>
    <interactor language="C++">…</interactor>
    <img><src><![CDATA[URL]]></src><base64><![CDATA[…]]></base64></img>
  </item>
</fps>
```

- 测试数据：遍历 `$OJ_DATA/<pid>/` 下 `*.in`（排除 `sample.in`），配对 `<test_input>/<test_output>` 并带 `name` 属性。
- `fixcdata()`：删除 `\x1a`，并把 `]]>` 转义为 `]]]]><![CDATA[>`。
- 图片可内联 base64；导出时可打包为 `export_<user>_<name>_<date>.zip`。

**导入**（`problem_import_xml.php`，支持 `.xml` 与 `.zip`；CLI：`php problem_import_xml.php a.xml`）

| 规则 | 行为 |
|---|---|
| `time_limit unit="ms"` | 值 `/1000` |
| `memory_limit unit="kb"` | 值 `/1024` |
| `<spj>` 或 `<tpj>` 存在 | `spj=1` |
| `<interactor>` 存在 | `spj=3` |
| `language="Text"` | `spj=2` |
| 有 `remote_oj/remote_id` 且库内已存在 | `UPDATE` 而不是新建 |
| 标题重复 | 追加后缀 `_<tail>` 直到唯一 |
| `<test_input name="X">` | 写 `X.in`；无 name 时写 `test<N>.in` |
| `<img>` | base64 写入 `upload/<domain>/Ymd/<pid>_<n>_<file>`，并 `UPDATE problem SET description=replace(...)` |
| `<spj language="C++">` | 写 `spj.cc`；`C` → `spj.c`；`<tpj>` → `tpj.cc`；`<interactor>` → `interactor.cc`（**不自动编译**） |
| `<solution>` | 插入 `solution` + `source_code` + `source_code_user`，`result=14` |
| `<prepend/template/append>` | 写 `prepend.<ext>` 等 |
| ZIP | 防目录穿越：拒绝绝对路径、`..`、以及 `[^A-Za-z0-9._+-]` 之外的字符 |

### 5.7 其他导入格式（FR-PRB-05）

均需 `administrator || problem_importer`，且 `require check_post_key.php`，上传字段名 `fps`。

| 导入器 | 源 | 要点 |
|---|---|---|
| `problem_import_hoj.php` | HOJ zip（`<id>.json` + 数据目录） | `timeLimit` ms→s；`memoryLimit` M；`description` 包在 `[md]…[/md]`；`examples` 拆成样例；用 `system("mv")` 搬数据 |
| `problem_import_hydro.php` | HydroOJ zip（`problem.yaml`/`problem.md`/`config.yaml`/`testdata/`） | 需 php-yaml；`type=objective`→`spj=2` 并生成 `data.in/data.out/template.c`；`type=interactive`→`spj=3` |
| `problem_import_md.php` | Markdown zip | 首行去 `#` 作标题；包 `<span class="md">`；`.cpp`→`<pid>-std.cpp`；内嵌 zip 解压到数据目录 |
| `problem_import_qduoj.php` | QDUOJ zip（`problem.json`） | 字段映射同 HOJ |
| `problem_import_syzoj.php` / `tyvj.php` / `unkownoj.php` | 各自 JSON/YAML | 兜底探测 |
| `offline_import.php` | 离线比赛 zip | `data/problemN/*.in|out` 建题；`source/studentX/problemN/*.cpp` 建比赛与提交（语言固定 1=C++，GBK→UTF-8） |

**从远程 OJ 抓取**（`problem_copy.php` → `problem_add_page_<oj>.php`）：支持 UOJ / HUSTOJ / 洛谷 / LOJ / Waterloo / POJ / HDU / ZJU；字段统一为 `url`；抓取后带 `remote_oj`、`remote_id` 入库。

### 5.8 题目启停与维护（FR-PRB-06）

| 操作 | SQL / 行为 | 权限 |
|---|---|---|
| 单题启停 | `UPDATE problem SET defunct='N'/'Y' WHERE problem_id=?` | `administrator \|\| p<pid>` |
| 批量启停 | `UPDATE problem SET defunct=? WHERE problem_id IN (<plist>)` | `administrator \|\| problem_editor` |
| 改题号 | `rename($OJ_DATA/<from>, $OJ_DATA/<to>)` + 更新 `problem/contest_problem/solution/topic` 的 `problem_id` + 重设自增 | 仅 administrator |
| 复制题 | 从远程 OJ 抓取 | 仅 administrator |
| 删除题 | 见 2.15 | `administrator \|\| p<pid>` |
| 重判 | `UPDATE solution SET result=1, pass_rate=0 WHERE problem_id=?`；`problem SET accepted=0`；删 `sim` 相关行 | 仅 administrator |

### 5.9 积分与一血（BR-PRB-07）

- 题目分值 `problem.coin`（默认 1）。
- 用户某题**首次 AC** 时由触发器 `firstAC` 累加 `users.coin_earned`。
- `first_time=1` 同时被排名页用于"一血"与统计页去重。

### 5.10 题目统计（FR-PRB-07，`problemstatus.php`）

- 总提交数回写 `problem.submit`；AC 用户数回写 `problem.accepted`。
- 结果分布：`SELECT result,count(1) FROM solution WHERE problem_id=? AND result>=4 GROUP BY result`。
- **AC 解法榜**：每页 20 条，每人取最优一次，综合排序键

```
score = 1e19 + time*1e11 + memory*1e5 + code_length
按 score 升序（即：time 越小越好 → memory 越小越好 → code_length 越小越好）
```

- 题目处于进行中的比赛时，时间/内存/代码长度显示为 `------`。

---

## 6. 提交与判题子系统

### 6.1 提交接口（FR-SUB-01，`submit.php`）

**输入（POST）**

| 字段 | 说明 |
|---|---|
| `id` | 练习模式题号（`id<=0` 表示自测运行） |
| `cid` + `pid` | 比赛模式（`cid<0` 表示自测运行，`pid` 为场内序号） |
| `language` | 语言编号 |
| `source` | 源码；当 `encoded_submit` 为真时先 `decode64()` |
| `input_text` | 自测运行的自定义输入 |
| `vcode` | 验证码 |
| `answer`（FILE） | 文件上传（语言 23 Scratch3 必用；其他语言可选） |
| `ajax` / `spa` | 异步提交标记 |

**处理顺序（严格按序，顺序错会导致行为差异）**

1. **登录检查**：未登录 → 渲染登录提示并退出。
2. **CSRF**：`$OJ_CSRF && $OJ_TEMPLATE=='bs3' && !http_judge` 时执行 `csrf_check`。
3. **验证码**：
   - 先查 `SELECT count(1) cnt FROM solution WHERE result<4`；`cnt > 50` 时强制 `$OJ_VCODE=true`。
   - `$OJ_VCODE` 为真时校验 `vcode`，失败置 `vfail=true` 并退出。
   - `$OJ_BENCHMARK_MODE` 为真时跳过全部验证码逻辑。
4. **题目解析**：
   - 有 `cid`：取绝对值，`require contest-check.php`，再 `SELECT problem_id,'N' defunct FROM contest_problem WHERE num=? AND contest_id=?`。
   - 无 `cid`：`SELECT problem_id,defunct FROM problem WHERE problem_id=?`（非自测且非特权时追加 `and defunct='N'`）。
5. **语言检查**：`language > count($language_name) || language < 0` → 置 0；`langmask & (1<<language)` → 拒绝（`$MSG_NO_PLS`）。
6. **文件上传**：非 Scratch3 时读取上传文件内容，大小 > 65536 字节拒绝；用 `mb_detect_encoding(['UTF-8','GBK','GB2312','BIG5','CP936'])` 判定编码（失败按 GBK），非 UTF-8 时转码。Scratch3 时 `source = "Main.sb3"`，`len` 取文件大小。
7. **文件名校验**：若 `$OJ_DATA/<id>/solution.name` 存在且上传文件名与之不同 → 源码被替换为错误提示串（`uploaded file name [...] is not [...]`），从而必然不通过。
8. **代码拼装**：见 BR-PRB-03。
9. **长度校验**：`len < 2` → `$MSG_TOO_SHORT`；`len > 65536` → `$MSG_TOO_LONG`。
10. **冷却时间（BR-SUB-02）**：

```sql
SELECT in_date, solution_id FROM solution
 WHERE user_id=? AND in_date > DATE_SUB(NOW(), INTERVAL <OJ_SUBMIT_COOLDOWN_TIME> SECOND)
 ORDER BY in_date DESC LIMIT 1
```

存在记录则**不插入新提交**，直接重定向到 `status.php?user_id=...`（AJAX 时回 `-1`）。默认冷却 **10 秒**（未设置时兜底 3 秒；`$OJ_BENCHMARK_MODE` 时跳过）。

11. **插入提交**：

```sql
-- 练习模式
INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,result)
VALUES(?,?,?,NOW(),?,?,?,14)

-- 比赛模式
INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,contest_id,num,result)
VALUES(?,?,?,NOW(),?,?,?,?,?,14)
```

`nick` 取 `users.nick`，空则取 `user_id`，查不到取 `"Guest"`。
若为 **OI 单解模式**（`contest_locked($cid,128)` 或标题含 NOIP 关键词且 `$OJ_OI_1_SOLUTION_ONLY` 且非自测）：先把该用户该场该题旧提交置 `result=13, pass_rate=0.0`，并校正 `problem.accepted/submit`。

12. **Scratch3 特例**：`mkdir($OJ_DATA/<id>/sb3)` 并 `copy(上传文件, $OJ_DATA/<id>/sb3/<sid>.sb3)`。
13. **源码入库**：

```sql
INSERT INTO source_code_user(solution_id,source) VALUES(?,?)   -- 用户原始代码
INSERT INTO source_code(solution_id,source)     VALUES(?,?)    -- 拼装后代码
```

14. **自测运行**：额外 `INSERT INTO custominput(solution_id,input_text)`；**不**累加 `problem.submit`。
15. **非自测**：`UPDATE problem SET submit=submit+1`；比赛中 `UPDATE contest_problem SET c_submit=c_submit+1 WHERE contest_id=? AND num=?`。
16. **远端题**：`problem.remote_oj != ''` → `result=16`（`UPDATE solution SET result=16, remote_oj=?`）。
17. **投毒（BR-SUB-04）**：当 `$OJ_POISON_BOT_COUNT > 0` 且用户非管理员/源码审查/赛建者/题编且 `id != 0`：
    - 若该用户该题 AC 次数 `>= $OJ_POISON_BOT_COUNT`，或 UA 含 `91.0.4472.77`：
      `result = rand(5,11)`，`memory = rand(100,2000)`，`time = rand(100,2000)`，`judger='poisoner'`；若 `$OJ_ADMIN != 'root@localhost'` 则给管理员发邮件告警。
18. **最终置位**：`UPDATE solution SET result=? WHERE solution_id=?`（$result 为 0 / 16 / 投毒随机值）。
19. **入队与唤醒**：`$OJ_REDIS && result==0` → `LPUSH <OJ_REDISQNAME> <sid>`；`$OJ_UDP && result==0` → `trigger_judge($sid)`。
20. **清缓存 + 跳转**：删除 `status.php` 的缓存；非自测重定向 `status.php?user_id=<me>[&cid=<cid>&top=<sid>&fixed=]`；自测输出 `<script>window.parent.setTimeout("fresh_result('<sid>')",1000)</script>`。

### 6.2 UDP 唤醒（BR-SUB-05）

```php
function trigger_judge($solution_id) {
    $servers = explode(",", $OJ_UDPSERVER);        // 支持逗号分隔多判题机
    $host = $servers[$solution_id % count($servers)];
    if (strstr($host, ":")) { [$host, $port] = explode(":", $host); }
    // 报文内容：若设置了 $OJ_JUDGE_HUB_PATH 则发送该路径字符串，否则发送 solution_id
    send_udp_message($host, $port, $OJ_JUDGE_HUB_PATH ?? $solution_id);
}
```

### 6.3 自测运行（test_run，FR-SUB-02）

- 触发：`id<=0`（练习）或 `cid<0`（比赛）。
- `problem_id` 落库为 **0**（`if ($test_run) $id = 0;`）。
- 输入存入 `custominput`。
- 判题机用该输入跑一次并回写 `result=13`（TR），不参与统计与排名。

### 6.4 judged 调度守护进程（FR-JDG-01）

**启动**：`chdir(oj_home)` → 读 `./etc/judge.conf` → `mysql_real_connect`（超时 30s）→ `SET NAMES utf8mb4` → `daemon_init()`（fork+setsid+chdir+umask(0)+重定向 0/1/2 到 /dev/null）→ 用 `$oj_home/etc/judge.conf` 旁的 `judge.pid` 加 `fcntl(F_WRLCK)` 排他锁保证单实例。

**取任务 SQL（BR-JDG-03）**

```sql
-- 单机（oj_tot == 1）
SELECT solution_id FROM solution
 WHERE language IN (<oj_lang_set>) AND result < 2
 ORDER BY result, solution_id
 LIMIT <prefetch * max_running>          -- 默认 80*3

-- 多机（oj_tot > 1）
SELECT solution_id FROM solution
 WHERE language IN (<oj_lang_set>) AND result < 2 AND MOD(solution_id, <oj_tot>) = <oj_mod>
 ORDER BY result, solution_id ASC
 LIMIT <prefetch * max_running>
```

**原子领取（BR-JDG-04）**

```sql
UPDATE solution SET result=2, time=0, memory=0, judgetime=NOW()
 WHERE solution_id=? AND result<2 LIMIT 1
```

`affected_rows() > 0` 才视为领取成功（**并发唯一性完全依赖此条件**）。多机分片（`oj_tot>1`）或启用 Redis 时跳过该检查直接返回成功。

**调度循环**

```
while (!stop):
    ids = 取任务()
    for runid in ids:
        if (runid % oj_tot != oj_mod) continue
        while (workcnt >= max_running) waitpid(-1, ...)      # 回收一个
        id = 找空闲槽位 ID[0..max_running-1]
        if (check_out(runid, OJ_CI)):
            fork()
              child: run_client(runid, id)
    if (UDP 开启且无任务) recvfrom(阻塞 sleep_time 秒) 并触发 php cron
    else sleep(sleep_time)
```

**拉起判题机 `run_client`**

- 设 `RLIMIT_CPU=800`、`RLIMIT_FSIZE=1GB`、架构相关 `RLIMIT_AS`、`RLIMIT_NPROC=800*max_running`。
- 非 Docker：`execl("/usr/bin/judge_client", "judge_client", <runid>, <clientid>, <oj_home>)`
- Docker：`docker container run --security-opt=no-new-privileges:true --pids-limit 100 --rm --cap-add SYS_PTRACE --net=host -v <oj_home>/log:/home/judge/log -v <oj_home>/etc:/home/judge/etc -v <oj_home>/src/core:/home/judge/src/core -v <data>:/home/judge/data hustoj <judge_client> <runid> <clientid>`

**信号**：`SIGQUIT/SIGINT/SIGTERM` 仅在 DEBUG 编译时置停止标志；**正常运行拒绝退出，需 `kill -9`**。

**远端/HTTP 模式**（`OJ_HTTP_JUDGE=1`）：全部数据库操作替换为对 `OJ_HTTP_BASEURL + OJ_HTTP_APIPATH`（默认 `http://127.0.0.1/admin/problem_judge.php`）的 `wget --post-data`，POST 动作键见 9.7。

### 6.5 judge_client 判题流程（FR-JDG-02）

**调用形式**：`judge_client <solution_id> <runner_id> <oj_home> [debug_level] [record_call]`

- 工作目录 `$oj_home/run<runner_id>/`（`runner_id ∈ [0, max_running)`），锁文件 `$oj_home/client<runner_id>.pid`。
- `umask(0077)`；开始时清理上次残留（移到 `log/`）。
- 配置优先级：内置默认 < `$oj_home/etc/judge.conf` < `$oj_home/data/<pid>/judge.conf`。

#### 6.5.1 编译（BR-JDG-05）

- Python(6) / JavaScript(16) **不编译**，直接返回 0。
- 其余 fork 子进程编译，`RLIMIT_CPU=50`（Java 60）+ `alarm()`，`RLIMIT_FSIZE=500MB`。
- 输出捕获：非 Pascal/FreeBasic 重定向 **stderr** 到 `ce.txt`；Pascal(2)/FreeBasic(11) 重定向 **stdout** 到 `ce.txt`。
- **失败判定**：`if (lang > 3 && lang < 7) status = get_file_size("ce.txt");` —— Ruby(4)/Bash(5)/Python(6) 下 **ce.txt 非空即视为编译错误**；其他语言看退出码。
- Python 的 `python3 -c "import py_compile; py_compile.compile(r'Main.py')"` 属语法检查。

**编译命令全表（原样）**

| lang | 命令 |
|---|---|
| 0 C | `gcc -fno-asm <cc_opt> <fmax_errors> <cc_std> -Wall --static -DONLINE_JUDGE -o Main Main.c -lm` |
| 1 C++ | `g++ -fno-asm <cc_opt> <fmax_errors> <cpp_std> -Wall -lm --static -DONLINE_JUDGE -o Main Main.cc` |
| 2 Pascal | `fpc Main.pas -Cs32000000 -Sh -O2 -Co -Ct -Ci` |
| 3 Java | `javac -J<Xms> -J<Xmx> -encoding UTF-8 Main.java` |
| 4 Ruby | `ruby -c Main.rb` |
| 5 Bash | `chmod +rx Main.sh` |
| 6 Python | `python3 -c "import py_compile; py_compile.compile(r'Main.py')"` |
| 7 PHP | `php -l Main.php` |
| 8 Perl | `perl -c Main.pl` |
| 9 C# | `mcs -codepage:utf8 -warn:0 Main.cs` |
| 10 ObjC | `gcc -o Main Main.m -fconstant-string-class=NSConstantString -I/usr/include/GNUstep/ -L/usr/lib/GNUstep/Libraries/ -lobjc -lgnustep-base` |
| 11 FreeBasic | `fbc -lang qb Main.bas` |
| 13 Clang | `clang Main.c -o Main -ferror-limit=10 -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE` |
| 14 Clang++ | `clang++ Main.cc -o Main -ferror-limit=10 -fno-asm -Wall -lm --static -std=c++0x -DONLINE_JUDGE` |
| 15 Lua | `luac -o Main Main.lua` |
| 17 Go | `go build -o Main Main.go`（env 含 `GOCACHE=/tmp`） |
| 19 Fortran | `f95 -static -o Main Main.f95` |
| 21 Cobol | `cobc -x -static -o Main Main.cob` |
| 23 Scratch3 | `scratch-run --check Main.sb3` |
| 24 Cangjie | `/opt/cangjie/bin/cjc --static-std --static-libs --diagnostic-format noColor -o Main Main.cj` |

`<fmax_errors>`：gcc > 4.8 时为 `-fmax-errors=10`，否则 `-Wformat`。
`OJ_COMPILE_CHROOT=1`（默认）时，C/C++/Clang/Clang++/Fortran/Cobol/ObjC/Lua/Go/SQL 在 bind 挂载 `/usr`、`/proc`、`/dev` 后 `chroot` 编译；Java/C#/Python/FreeBasic/Bash/R 不 chroot。

**编译失败**：`ce.txt` 截断 **16384 字节**写入 `compileinfo`（先 DELETE 再 INSERT），`result=11`，结束。

#### 6.5.2 运行（BR-JDG-06）

**沙箱建立顺序**
1. `chroot(work_dir)`（非 Docker 且语言不属于 Java/PHP/Bash/Cobol/Matlab/C#/R，且 Python 未开启 `OJ_PYTHON_FREE`）。
2. `setgid(judge_gid)` → `setuid(judge_uid)` → `setresuid(...)`（失败则循环 sleep 1 秒重试）。
3. `unshare(CLONE_NEWNET)`（断网）。
4. `ptrace(PTRACE_TRACEME)`（`use_ptrace && spj != 3`）。
5. `setrlimit`（值见 3.9）。
6. 输入重定向：`p_id==0` 或无 `input.name` 时 `stdin = <测试点>.in`（`OJ_COPY_DATA` 时用 `data.in`）；SQL(18) 先执行 `sqlite3 data.db < data.in` 再 `stdin = Main.sql`。输出：`stdout = user.out`，`stderr = error.out`（追加）。

**执行目标**

| lang | 执行 |
|---|---|
| 0/1/2/10/11/13/14/17/19/21/24 | `./Main` |
| 3 Java | `/usr/bin/java -Xmx<mem>M Main` |
| 4 Ruby | `ruby Main.rb` |
| 5 Bash | `bash Main.sh` |
| 6 Python | `python2|python3 Main.py`（依内容是否含 `python2` 判定） |
| 7 PHP | `php Main.php` |
| 8 Perl | `perl Main.pl` |
| 9 C# | `mono --debug Main.exe` |
| 12 Scheme | `guile Main.scm` |
| 15 Lua | `lua Main.lua` |
| 16 JavaScript | `node Main.js` |
| 18 SQL | `sqlite3 data.db` |
| 20 Matlab | `octave-cli -W -q -H Main.m` |
| 22 R | `Rscript Main.R` |
| 23 Scratch3 | `scratch-run Main.sb3` |

**环境变量**：`PYTHONIOENCODING=utf-8`、`LANG/LANGUAGE=zh_CN.UTF-8`、`LC_ALL=zh_CN.utf-8`、`PATH=/bin:/usr/bin:/opt/cangjie/bin`、`LD_LIBRARY_PATH=/opt/cangjie/runtime/lib/linux_x86_64_llvm:/opt/cangjie/tools/lib`、`USER=judge`。

**系统调用白名单（BR-JDG-07）**

- 依据 `okcalls64.h` / `okcalls32.h` / `okcalls_aarch64.h` 中的 `LANG_<X>V[]` 数组，把允许的系统调用号在 `call_counter[512]` 置为 `-1`（表示不限次）。
- 特殊：`call_counter[SYS_execve % 512] = 1`（R/Python 为 8）；aarch64 下 Java 为 100 否则 1。
- 父进程 `watch_solution` 用 `wait4(-1, &status, __WALL, &ruse)` 捕获每次 `PTRACE_SYSCALL` 陷入，取寄存器 `REG_SYSCALL`（x86_64=`orig_rax`、i386=`orig_eax`、arm=`r7`、aarch64=`regs[18]`、mips=`REG_V0`、loongarch=`regs[17]`），`% 512` 索引。
- 计数为 0 → 判 **RE**，写 `runtimeinfo`："Forbidden system call:%u"，`PTRACE_KILL`。
- `spj==3`（交互题）或 `!use_ptrace` 时不做白名单检查。

#### 6.5.3 逐测试点循环（BR-JDG-08）

```
枚举 $OJ_DATA/<pid>/ 下所有 *.in（alphasort 字典序）
for i in 0..n-1:
    if (!(oi_mode || ACflg==OJ_AC || ACflg==OJ_PE)) break   # 非 OI 模式：首个非 AC 即停
    prepare_files(): 拷贝 *.dic / interactor；touch 并 chmod 0760 user.out
    fork 子进程 run_solution（spj==3 时跑 interactor）
    父进程 watch_solution 看护，超时后 kill(pid, 9)
    比对 user.out 与 <name>.out（或调 spj）
```

#### 6.5.4 结果判定（BR-JDG-09）

| 结果 | 触发条件 |
|---|---|
| AC(4) | 所有测试点通过 |
| PE(5) | 仅空白/换行差异（见 6.5.5） |
| WA(6) | 内容不符 / 用户输出缺失 / spj 返回 1 |
| TLE(7) | 被 `SIGXCPU` / `SIGKILL` / `SIGALRM` / `SIGCHLD` 终止；且 `usedtime < time_limit*1000` 时把 `usedtime` 顶到 `time_limit*1000` |
| MLE(8) | `topmemory > mem_limit * 1MB`；或 Java `OutOfMemoryError`；或（回写时）`result==TLE && memory==0` |
| OLE(9) | `SIGXFSZ` 终止；或 `user.out` 大小 > `outFileSize*2 + 1024` |
| RE(10) | 非零退出码；越权系统调用；`error.out` 非空（非 OI 且非脚本语言）；Java `Exception`/`Could not create`；缺 `*.out`；缺用户输出 |
| CE(11) | 编译失败 |

回写前的两次改写：
```
if (result == OJ_TL && memory == 0) result = OJ_ML;
if (result == OJ_AC) result = auto_result;   // 默认 OJ_AC
```

#### 6.5.5 输出比对算法（BR-JDG-10，必须 1:1 复刻）

采用流式 `compare_zoj`（`ZOJ_COM` 默认开）：

```
逐字符比较 user.out 与 标准输出：
  find_next_nonspace() 跳过两侧的空格与换行（空行整体忽略）
  · 若两侧当前非空白字符相同 → 继续
  · 若不同 → WA（立即结束）
  · 若差异仅出现在空白：
      OJ_IGNORE_ESOL=1（默认）：尝试把 "\n" 与空格互相吞掉对齐；
                              仍无法对齐 → PE
      OJ_IGNORE_ESOL=0      ：仅处理 "\r\n" 与 "\n" 的差异；其余空白差异 → PE
· 大小写敏感（不做 case folding）
· 末尾多余换行被"空行跳过"逻辑吸收，不判 PE
```

`OJ_FULL_DIFF`：1=生成简化 markdown 差异（截断 50 字符前缀、取前 5 行）；2=并排 diff；3=`head -100` + `diff -y`。
`OJ_INTERNAL_MARK` 时部分分：`*spj_mark = (user_now - 1) / out_size`。

#### 6.5.6 特判（BR-JDG-11）

- 查找顺序：`upj` → `tpj` → `spj`（`X_OK` 检测）。
- 调用：`execl(<spj程序>, <infile>, <outfile>, <userfile>)`，即 `argv[1]=输入, argv[2]=标准答案, argv[3]=用户输出`。
- 退出码映射：**0→AC，1→WA，2→PE，3→RE，其他→WA**。
- 若返回值 7 且非 testlib（`spj != 2`）：解析 `diff.out` 末行 `points %lf` 作为部分分。
- `spj==3`（交互题）：通过命名管道 `p_interactor` 读取交互器退出码。
- 编译：`g++ --static -o spj spj.cc || g++ -o spj spj.cc`（`makeout.sh`）。导入 FPS 时**不自动编译**。

#### 6.5.7 结果回写（BR-JDG-12）

```sql
-- OI 模式
UPDATE solution SET result=?, time=?, memory=?, pass_rate=?, judger=?, judgetime=NOW()
 WHERE solution_id=?
-- 非 OI 模式：去掉 pass_rate
```

- `time`：`OJ_USE_MAX_TIME=1` 取单点最大值，否则取累计值；再乘 `OJ_CPU_COMPENSATION`。
- `memory` = `topmemory >> 10`（KB）；内存测量源：非 VM 语言读 `/proc/<pid>/status` 的 `VmPeak`，VM 语言用 `ru_minflt * pagesize`。
- `judger` 经转义后写入（HTTP 模式写 `http_username`）。
- `compileinfo` / `runtimeinfo` / `diff` 信息均截断 **16384 字节**，先 DELETE 再 INSERT。

**统计刷新**

```
users.solved / users.submit             -- 去重题数
problem.accepted                        -- AC 次数
contest_problem.c_accepted / c_submit   -- 比赛内次数（排除 contest_type & 16 的隐藏赛）
```

#### 6.5.8 查重 sim（BR-JDG-13）

- 触发：`OJ_SIM_ENABLE=1` 且最终 `ACflg==OJ_AC` 且（非 OI 或 非 AC）。
- 执行：`/usr/bin/sim.sh Main.<ext> <pid>` —— 按扩展名选 `/usr/bin/sim_<ext>`（缺失用 `text`），对 `$OJ_DATA/<pid>/ac/` 下近 90 天（`-mtime -90`）的 AC 源码逐一比对，取相似度。
- 阈值：**> 80** 即记录（硬编码）。
- 落库：`sim(s_id, sim_s_id, sim)`，`ON DUPLICATE KEY UPDATE`；`if (solution_id <= sim_s_id) sim = 0`。
- 无抄袭时把源码复制/硬链到 `$OJ_DATA/<pid>/ac/<sid>.<ext>`（C/C++ 互为软链）。
- 自己抄自己由触发器 `simfilter` 过滤（`s_id` 置 0）。

### 6.6 人工/HTTP 判题（FR-JDG-03）

`admin/problem_judge.php`，仅 `http_judge` 权限，`Content-Type: text/plain`，全部由 POST 字段驱动：

| 动作键 | 行为 |
|---|---|
| `checklogin` | 心跳，返回 `1` |
| `getpending` | 从 Redis `rpop` 或 SQL `WHERE result<2 OR (result<4 AND NOW()-judgetime>60)`，每行输出一个 solution_id |
| `checkout` | `UPDATE solution SET result=? ... WHERE solution_id=? AND result<2` |
| `update_solution` | `UPDATE solution SET result,time,memory,judgetime=NOW(),pass_rate,judger`；`sim` 写 `sim` 表 |
| `addceinfo` / `addreinfo` | 写 `compileinfo` / `runtimeinfo`（经 `ce.post`/`re.post`/`diff.post` 文件） |
| `getsolution` | 返回 `source_code.source` |
| `getcustominput` | 返回 `custominput.input_text` |
| `getsolutioninfo` / `getprobleminfo` | 返回提交 / 题目元信息 |
| `gettestdatalist` / `gettestdata` | 列出 / 下载测试数据文件（含 mtime 增量比对） |
| `manual` | 人工判分：`UPDATE solution SET result=?, pass_rate=1.0/0, judger=?` |
| `updateuser` / `updateproblem` | 刷新统计 |

### 6.7 重判（FR-JDG-04，`admin/rejudge.php`，仅管理员）

| 参数 | 行为 |
|---|---|
| `rjpid` | `UPDATE solution SET result=1, pass_rate=0 WHERE problem_id=?`；`problem SET accepted=0`；删 `sim` 相关行 |
| `rjsid` | 按 solution_id 重判，跳 `status.php?top=` |
| `rjcid` | `UPDATE solution SET result=1 WHERE contest_id=?`（可带 `pid`/`num` 限定某题） |
| `result`→`to` | 批量 `UPDATE solution SET result=<to> WHERE result=<from>` |

结束后：`$OJ_REDIS` 时把 `result=1` 的 id 全部 `lpush`；`$OJ_UDP` 时 `trigger_judge()`。

---

## 7. 比赛子系统

### 7.1 比赛列表与详情（FR-CST-01，`contest.php`）

- 无 `cid`：渲染比赛列表，每页 **25** 条，`ORDER BY contest_id DESC`，最多 1000 条；支持 `keyword` 模糊搜索 title/description。
- 有 `cid`：渲染单场比赛（题目列表 + 说明）。

**题目列表 SQL**

```sql
SELECT p.title, p.problem_id, p.source, cp.num AS pnum, p.coin coin,
       cp.c_accepted accepted, cp.c_submit submit
  FROM problem p
 INNER JOIN contest_problem cp ON p.problem_id=cp.problem_id AND cp.contest_id=?
 ORDER BY cp.num
```

**全局题号隐藏（BR-CST-04）**：比赛进行中（`now < end_time`）题目链接为 `problem.php?cid=<cid>&pid=<num>`（**不暴露 problem_id**）；比赛结束后，若该题未被其它未结束的私有比赛占用，链接改为 `problem.php?id=<problem_id>`。若被占用且用户非 `m<cid>`，标题显示 `--using in another private contest--`，`accepted/submit` 显示 `?`。

**个人 AC 标记** `check_ac($cid,$num,$noip)`：
- NOIP 模式：有提交 → 灰色 `?`；无提交 → 空。
- 否则：有 AC → 绿色 `Y`；有非 AC 提交 → 红色 `N`；无 → 空。

### 7.2 准入校验（FR-CST-02，`contest-check.php`）

顺序如下，任一步失败即 `contest_ok=false`：

1. 查 `SELECT * FROM contest WHERE contest_id=?`。
2. **IP 段**：`in_subnet_of_contest($ip, $cid)` —— `subnet` 为空放行；否则 `explode(",", $subnet)` 逐段用 `is_ip_in_subnet($ip, $net)` 判定，**任一命中即放行**。
   `is_ip_in_subnet`：`mask_int = ip2long("255.255.255.255") << (32 - $mask)`；判定 `($ip & $mask_int) == ($subnet_ip & $mask_int)`。
3. **关闭**：`defunct='Y'` 且非管理员 → 拒绝。
4. **私有**：`private=1` 时，若 `$_POST['password'] === contest.password` → 置 `$_SESSION['c<cid>']=true`；否则查 `privilege WHERE user_id=? AND rightstr='c<cid>'`，命中则放行，否则拒绝。
5. **特权**：`administrator` / `contest_creator` 强制 `contest_ok=true`。
6. **未开始**：非特权且 `now < start_time` → 显示 `$MSG_TIME_WARNING` 并 `exit`。
7. **已结束**：`now > end_time` → `contest_is_over = true`（进入练习/补题模式）。
8. **个人限时赛**（`$OJ_CONTEST_LIMIT_KEYWORD`，默认 `限时`）：描述中含"限时 N 分钟"时，首次访问写 `loginlog(user_id, password='c<cid>', ip, time)`；超时且非 `m<cid>`/管理员 → 拒绝；否则在描述中追加倒计时。

### 7.3 比赛期间约束（FR-CST-03）

| 行为 | 规则 |
|---|---|
| 提交 | `submit.php` 带 `cid`，`num = pid`（场内序号）；需通过 7.2 准入 |
| 查看源码 | `contest_type & 1`（考试中禁源码）/ `& 16`（NOIP）时，赛中对学生隐藏 |
| 下载 | `& 2` 禁止 |
| 榜单 | `& 4` 禁止查看；`& 16` NOIP 时学生不可见 |
| 他人状态 | `& 8` 时非管理员只能看自己 |
| WA 差异 | `& 32` 禁止显示 |
| 补题 | `& 64` 禁止赛后补题 |
| 仅最后提交 | `& 128` 时新提交会把旧的置 `result=13`（需同时 `$OJ_OI_1_SOLUTION_ONLY=true` 或 NOIP 关键词） |
| AI | `& 256` 禁止 |

### 7.4 ACM 排名算法（BR-CST-05，复刻最易错）

**数据取样（必须按此顺序）**

```sql
SELECT user_id, nick, solution.result, solution.num, solution.in_date, solution.pass_rate
  FROM solution
 WHERE solution.contest_id = ?
   AND num >= 0 AND problem_id > 0
   AND in_date >= <start_time> AND in_date < <end_time>
 ORDER BY user_id, solution_id;
```

> **必须按 `user_id, solution_id` 顺序逐条累加**；乱序会改变罚时与一血判定。

**累加器（类 `TM::Add($pid, $sec, $res)`）**

```
sec = 该提交时刻 - 比赛开始时刻（秒）

if (sec < 0) return;                      // 比赛重启，忽略旧提交
if (isset(p_ac_sec[pid])) return;         // ★ 已 AC：之后所有提交一律忽略

if (res != 4):                            // 未通过
    if (defined('OJ_CE_PENALTY') && OJ_CE_PENALTY === false && res == 11) return;  // CE 不罚
    p_wa_num[pid] = (p_wa_num[pid] ?? 0) + 1;
else:                                     // res == 4 通过
    p_ac_sec[pid] = sec;
    solved++;
    time += sec + p_wa_num[pid] * 1200;   // ★ 每次错误 1200 秒 = 20 分钟
```

**关键结论**
1. 罚时单位**秒**，每次错误提交 **+1200 秒（20 分钟）**。
2. **AC 之后的提交完全忽略**（不增罚时、不改 AC 时刻）。
3. **编译错误默认计入罚时**；仅当 `$OJ_CE_PENALTY === false`（默认值为 false，**注意 PHP 中 `defined` 为 true 且值为 false**）时 CE 完全不计。
   > 由于 `db_info.inc.php` 中 `$OJ_CE_PENALTY=false` 已定义，故默认行为是 **CE 不计罚时**。
4. **排序仅两个关键字**（无末次提交时间 tie-break）：

```
s_cmp(A, B):
    if (A.solved != B.solved) return A.solved > B.solved;   // 解题数降序
    return A.time < B.time;                                  // 罚时升序
```

**一血**

```sql
SELECT s.num, s.user_id FROM solution s,
  (SELECT num, MIN(solution_id) minId FROM solution
    WHERE contest_id=? AND result=4 GROUP BY num) c
 WHERE s.solution_id = c.minId;
```

> 一血 = 该题 `result=4` 中 **`solution_id` 最小**者，而非时间最早者。

**封榜（Freeze，BR-CST-06）**

```
lock_time = end_time - (end_time - start_time) * OJ_RANK_LOCK_PERCENT
冻结窗口：now < end_time + OJ_RANK_LOCK_DELAY  AND  lock_time < 该提交 in_date
窗口内的提交以 result=0 注入累加器（≠4 且 ≠11）→ 记一次错误，永不置 AC
窗口结束后以真实 result 重算
```

- ACM 默认 `$OJ_RANK_LOCK_PERCENT=0`（不封榜）、`$OJ_RANK_LOCK_DELAY=3600` 秒。
- OI 默认 `percent=1`（整场冻结）。
- **副作用**：AC 发生在冻结期时，解冻后会多记一次错误罚时（已知行为，须保留）。

**缺席名单**：`privilege WHERE rightstr='c<cid>' AND user_id NOT IN (已提交用户)` → 补 0 分 `TM` 列于榜尾（`nick` 以 `*` 开头者不参与排名编号）。

**单元格渲染**

| 状态 | 底色 | 内容 |
|---|---|---|
| 已 AC | 绿，按错误数渐变（`0x33 + wa*32`）；一血为 `aaaaff` 蓝 | `HH:MM:SS` 后接 `(-wa)` |
| 未 AC 有尝试 | 红渐变（`0xaa - wa*10`） | `(-wa)` |
| 未尝试 | `#eeeeee` | 空 |

榜单每 **60 秒**自动整页刷新。

### 7.5 OI 排名算法（BR-CST-07）

累加器增加 `p_pass_rate[]` 与 `total`：

```
若已 AC 则 return
if (result != 4):
    p_pass_rate[pid] = max(旧值, res)          // 保留最佳部分分
    total += res*100 - 旧值*100
    p_wa_num++
else:  // result == 4
    p_ac_sec[pid] = sec; solved++
    total -= 旧 p_pass_rate[pid]*100
    total += res*100                            // res = 1.0 → 100 分
    time += sec + p_wa_num*1200
```

- 分数规范化：`result != 4 && pass_rate >= 0.95 → 0.95`；`result == 4 && !$OJ_CONTEST_TOTAL_100 → pass_rate = 1.0`。补题榜（rank5）阈值 `>= 0.99 → 0`。
- **排序**：`total` 降序 → `solved` 降序 → `time` 升序。
- 展示每题得分、总分、罚时。

### 7.6 榜单变体（FR-CST-04）

| 页面 | 模式 | 差异 |
|---|---|---|
| `contestrank.php` | ACM | 标准榜；含缺席名单；封榜按配置；60s 刷新 |
| `contestrank2.php` | ACM | 滚榜大屏；`$OJ_CACHE_SHARE=true`；整段 `json_encode` 供前端滚动；无缺席逻辑 |
| `contestrank3.php` | ACM | 滚榜+奖牌；需 `administrator/contest_creator/problem_editor/password_setter`；需比赛已结束；支持 `?lock_percent=` 覆盖封榜比例（存会话）；奖牌比例 **金 5% / 银 15% / 铜 20%**；提供 `?type=json&list=submit\|team` 接口 |
| `contestrank4.php` | ACM 补题 | 脱离比赛时间窗：`in_date >= start_time AND problem_id IN (比赛题集合)`，排除 `$OJ_RANK_HIDDEN` |
| `contestrank5.php` | OI 补题 | 同 4，但按部分分计，`pass_rate>=0.99→0` |
| `contestrank-oi.php` | OI 正赛 | 见 7.5 |
| `contestrank.xls.php` | 赋分榜 | 按名次正态分布赋分：`mark_start=60`、`mark_end=100`、`sigma=5`；单题时 `mark_base=100`，否则 `mark_per_problem=(100-mark_base)/(pid_cnt-1)`，`mark_per_punish=mark_per_problem/5`，惩罚上限 `mark_per_problem*0.8`；导出 `.xls` |

### 7.7 比赛统计（FR-CST-05，`conteststatistics.php`）

- 取数：`SELECT result,num,language FROM solution WHERE contest_id=? AND num>=0`。
- 对每题 `num`（末行 `num = pid_cnt` 为全场汇总）累加三个维度：
  - 结果分布：`R[num][res-4 < 0 ? 8 : res-4]`
  - 语言分布：`R[num][language + 11]`
  - 提交总数：`R[num][10]`
- 趋势图：`chart_data_all` = 按 `DATE(in_date)` 统计 `result>=4 AND result<13` 的提交数（上限 1000）；`chart_data_ac` = 同日 `result=4` 数。
- 封榜提示：`now ∈ (lock_time, end_time + DELAY)` 时显示 `The board has been locked.`

### 7.8 气球（FR-CST-06）

- 权限 `$_SESSION['balloon']`；按当前用户的 `school` 过滤（`user_id LIKE '<school>%'`）。
- **生成时机：查询时实时生成**（非 AC 时落库）：

```
1. 若 GET['id']   → UPDATE balloon SET status=1 WHERE balloon_id=?      （标记已发）
2. 若 POST['clean'] → DELETE FROM balloon WHERE cid=? AND user_id LIKE '<school>%'
3. 查 solution WHERE result=4 AND contest_id=? AND user_id LIKE '<school>%'
     AND solution_id NOT IN (SELECT sid FROM balloon WHERE cid=?)
   对每条 AC：若 (user_id, cid, pid) 不存在 → INSERT INTO balloon(user_id,sid,cid,pid,status=0)
4. 计算 first_blood（同 7.4）
```

- 列表：`SELECT * FROM balloon LEFT JOIN users ... WHERE cid=? AND user_id LIKE '<school>%' ORDER BY status, balloon_id DESC LIMIT 50`；题号 `chr(ord('A')+pid)`；颜色/名称取 `$ball_color[pid]`/`$ball_name[pid]`（10 色循环）；一血追加 `First Blood!`。

### 7.9 打印（FR-CST-07）

- 开关 `$OJ_PRINTER`；权限 `printer`；写操作需 CSRF。
- 提交：`INSERT INTO printer(user_id,in_date,status,content) VALUES(?,NOW(),0,?)`
- 列表：按 `user_id LIKE '<school>%'`，`ORDER BY status, printer_id DESC LIMIT 50`；`?id` → `status=1`；`?clean` → 删除本校任务。

### 7.10 比赛管理（FR-CST-08）

**创建**（`contest_add.php`）

字段：`startdate, shour, sminute, enddate, ehour, eminute, title, private, password, description, lang[], subnet, contest_type[], cproblem（逗号分隔题号）, ulist（换行用户名）`

```sql
INSERT INTO contest(title,start_time,end_time,private,langmask,description,password,subnet,contest_type,user_id)
VALUES(?,?,?,?,?,?,?,?,?,?)
```

- 时间拼装：`"$startdate $shour:$sminute:00"`。
- `langmask` 计算见 3.3。
- `contest_type`：`foreach ($_POST['contest_type'] as $t) $type += 1 << $t;`
- 题目批量：按 `cproblem` 拆分、去重、`intval`，仅存在的题才 `INSERT contest_problem(contest_id,problem_id,num)`（`num` 自增），并 `UPDATE problem SET defunct='N'`。
- 授权：清 `privilege WHERE rightstr='c<cid>'`；`INSERT privilege(user_id,'m<cid>')`；`ulist` 每行 `INSERT privilege(user_id,'c<cid>')`；写 `$_SESSION['m<cid>']`。
- `?cid=` 时为"复制比赛"（标题加 `-Copy`，带原题号与用户列表）。

**编辑**：与创建对称，`UPDATE contest SET ...`；先删 `contest_problem` 再重建，并更新 `c_accepted/c_submit` 与 `solution.num`。

**列表**：每页 25，支持关键词搜索；操作：`pr_change`（公开/私有切换）、`df_change`（启用/停用）、编辑、复制、导出 XML、导出代码、作弊嫌疑列表。

**队伍生成**（`team_generate.php`）：字段 `prefix, teamnumber, ulist`；账号 `prefix + (i<10 ? '0'.i : i)`；初始密码 `strtoupper(substr(md5($user_id.rand(0,9999999)),0,10))`，经 `pwGen()` 入库；`admin` 前缀跳过；`ON DUPLICATE KEY UPDATE` 覆盖昵称与密码。

---

## 8. 状态页、题目列表、内容社区与系统功能

### 8.1 状态页（FR-STT-01，`status.php`）

**基础条件**：`WHERE problem_id > 0`。

**按 cid 分支**

| 场景 | 附加条件 |
|---|---|
| 指定 `cid` | `AND contest_id=<cid> AND num>=0`；同时取 `contest_type`，若 `& 8` 则非管理员只能看自己 |
| 管理员 / `source_browser` | 可见所有（含比赛提交） |
| 普通登录用户 | `AND (contest_id=0 OR contest_id IS NULL)`（仅练习提交） |
| 游客 | `AND user_id NOT IN (<OJ_RANK_HIDDEN>) AND problem_id>0 AND (contest_id=0 OR contest_id IS NULL)` |

**过滤参数**：`top`（`solution_id <= top`）、`problem_id`（cid 下转 `num`）、`user_id`、`language`（0..24）、`jresult`（**封榜期不生效**）；可按 `users.school` / `group_name` 过滤。

**执行结构（双层）**

```sql
SELECT <fields> FROM
  (SELECT * FROM solution <where> ORDER BY solution_id DESC LIMIT 50) solution
INNER JOIN users ON ... AND users.defunct='N'
<附加过滤> ORDER BY solution_id DESC LIMIT 50
```

`$fields` 含 `solution.*, users.nick, users.group_name, users.starred`；`$OJ_SIM` 开启时附加 `sim.*`。

**可见性规则（FR-STT-02）**

| 内容 | 可见条件 |
|---|---|
| 内存/耗时/语言/代码长度 | `!is_running(contest_id) \|\| source_browser \|\| administrator \|\| 本人`，否则 `----` |
| 源码链接（`showsource.php`） | 赛后 / 本人 / `source_browser` |
| 结果详情 | 非封榜 或 本人 或 `lock_time > in_date`；否则他人显示 `----` |
| `ceinfo.php`（编译错误） | 本人 或 `source_browser` |
| `reinfo.php`（运行错误/差异） | 本人 或 `source_browser` 或 `$OJ_SHOW_DIFF` |
| 相似度 | `sim > 80` 标记 `*`（`$OJ_SIM` 开启时） |

`is_running($cid)`：`SELECT count(*) FROM contest WHERE contest_id=? AND end_time > NOW()`。

### 8.2 状态 AJAX（FR-STT-03，`status-ajax.php`）

| 参数 | 行为 |
|---|---|
| `solution_id` | 查该提交；`result>=16 && $OJ_REMOTE_JUDGE` 时触发重判 |
| `tr=1` | 仅本人可见 CE/RE 详情；SPJ 且 `$OJ_HIDE_RIGHT_ANSWER` 时拒绝 |
| `q=user_id` | 返回 `user_id[nick]`（悬停显示） |
| 默认 | AC 时 `pass_rate=1`；`?t=json` 返回整行 JSON，否则输出 `result, memory KB, time ms, judger, pass_rate*100, user_id`（非管理员 `judger` 输出 `none`） |

**刷新间隔（FR-STT-05）**：`$defaultInterval = avg_delay > 1 ? avg_delay*1000 : 800`（毫秒）。

### 8.3 题目列表（FR-STT-04，`problemset.php`）

- 每页 **50** 题；参数 `page`、`search`、`list`、`order`（默认 `problem_id`）。
- 搜索：`(title LIKE ? OR source LIKE ?)`，两端加 `%`。
- `list`：`FIELD(problem_id, ...)` 排序且**不分页**。
- 权限过滤：管理员看全部；`$OJ_FREE_PRACTICE` 时仅 `defunct='N'`；否则额外排除"正在被未结束比赛使用"的题目。
- 状态标记：登录用户取 `SELECT problem_id, MIN(result) FROM solution WHERE user_id=? AND result>=4 GROUP BY problem_id` → 4 为 `Y`（绿），否则 `N`（红）。
- **分类标签**：`source` 按空格拆分，URL 型取域名；用 `md5(cat)` 前 7 位取模选 `$color_theme`（default/primary/success/info/warning/danger），链接回 `problemset.php?search=<cat>`。
- 页码记忆：登录用户无 `search` 时把当前页写入 `users.volume`（"题册"概念）。

### 8.4 分类云（FR-STT-06，`category.php`）

`SELECT DISTINCT source FROM problem WHERE defunct='N' ORDER BY source LIMIT 5000`；拆分/去重/排序后同法着色渲染。

### 8.5 讨论区（FR-SOC-01）

**状态机**

| 表 | 字段 | 取值 |
|---|---|---|
| `topic` | `status` | 0=正常 1=锁定 2=删除 |
| `topic` | `top_level` | 0=普通 2=笔记 3=公告（全站置顶） |
| `reply` | `status` | 0/1/2 同上 |

**排序**：`WHERE status != 2`，`ORDER BY top_level DESC, MAX(reply.time) DESC`；`cid=0 OR top_level=3` 全站置顶。
**关联**：`cid`（比赛）、`pid`（题目）。

**BBCode（`bbcode_to_html()`）**：支持 `b, u, sup, sub, blockquote, ol, ul, li, table, tr, td, th`；别名 `url→a, code→pre, quote→blockquote, *→li, list→ul`；新闻另有题单语法 `[plist=1001,1002]标题[/plist]`。其余 HTML 全部实体化（防 XSS）。

**权限**（`threadadmin.php`）
- 回复：`disable→status=1`、`resume→0`、`delete→2`；非管理员只能删自己的回复。
- 主题：`sticky&level=0~3`、`lock→1`、`resume→0`、`delete→2`（级联 `reply.status=2`）；非管理员只能删自己的主题。

**长度限制**：内容 > 5000、标题 > 60 拒绝；过滤 `$bad_words`。
`$OJ_BBS`：`false` 关闭；`discuss3` 内置；`discuss` 旧版；`bbs` phpBB3 桥接。

### 8.6 站内信（FR-SOC-02）

`$OJ_MAIL=true` 时可用。发信前校验：**收发双方至少一方**拥有 `source_browser` 或 `administrator`，否则报 `MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT`（防学生互扰）。列表查 `mail WHERE to_user=? OR from_user=?`；查看时置 `new_mail=0`。

**SMTP**（`email.class.php`）：`SMTP_SERVER=smtp.qq.com`、`SMTP_PORT=587`、`SMTP_USER`、`SMTP_PASS`；加密 `ENCRYPTION_SMTPS`。**哨兵**：`$SMTP_USER === 'mailer@qq.com'` 视为未配置，跳过发信。

### 8.7 代码分享与查看（FR-SOC-03）

`showsource.php` / `showsource2.php` 读 `source_code_user`；`getsource.php` / `comparesource.php` 读 `source_code`。

**可见条件（满足其一）**
1. 提交者本人；
2. `source_browser` 或 `administrator`；
3. `$OJ_AUTO_SHARE` 且当前用户该题已 AC；
4. 会话/权限含 `s<pid>`。

且须通过 `problem_locked()` / 比赛进行中封榜检查（`MSG_SOURCE_NOT_ALLOWED_FOR_EXAM`）。

- `export_ac_code.php`：导出本人全部 AC 源码，**排除**处于进行中比赛的题。
- `export_contest_code.php`：需 `m<cid>` / `administrator` / `contest_creator`。
- 代码高亮：语言名映射为 Ace editor brush（`pascal → delphi` 等）。

### 8.8 公告（FR-SOC-04）

`news_*` 后台管理；字段 `user_id,title,content,time,importance,menu,defunct`。`menu=1` 显示在菜单。`$OJ_INDEX_NEWS_TITLE` 指定首页展示的公告标题（默认 `HelloWorld!`）。`setmsg.php` 写 `msg/<domain>.txt` 作为站点消息（经 `RemoveXSS`）。

### 8.9 AI 辅助（FR-SOC-05）

- 开关 `$OJ_AI_API_URL`（默认 `aiapi/demo.php`，可选 `qwen.php`/`hy.php`/`deepseek.php`）。
- 异步任务表 `openai_task_queue`：状态 0 待处理 → 1 处理中 → 2 完成 / 3 失败；结果写 `solution_ai_answer` 或回写 `problem.source` 分类。
- `cron.php`（CLI）：消费队列；随后若 `$OJ_REMOTE_JUDGE` 触发远端同步。
- `aidba.php`：管理员用自然语言生成 SQL 并执行（**高危**，需 `administrator/problem_editor/contest_creator/tag_adder`）。
- `$OJ_AI_HTML`：在页面注入 AI 求助链接。

### 8.10 远端 OJ（FR-SOC-06）

`$OJ_REMOTE_JUDGE=true` 时启用；`remote.php` 配合 `remote_{bas,hdu,luogu,pku}.php`。提交状态机：`result=16`（远端排队）→ `17`（远端判题中）→ 写回真实结果；`remote_oj/remote_id` 记录来源。

### 8.11 定时任务（FR-SOC-07，`cron.php`）

由 judged 的 UDP 唤醒机制以 `www-data`（uid 33）身份、`RLIMIT_CPU=120` 等限制下执行 `php cron.php`。职责：消费 AI 任务队列 → 触发远端 OJ 同步。

### 8.12 后台管理（FR-ADM-01）

**入口门禁**（`admin-header.php`）
1. Referer 白名单校验（host 不在 `$allowed_hosts` → 403）。
2. 权限：`administrator || contest_creator || user_adder || problem_editor || password_setter`，否则提示登录并退出。
3. 页面加载时通过 `csrf.php` 注入一次性 CSRF 隐藏域。

**CSRF 三套机制**

| 机制 | 生成 | 校验 | 特点 |
|---|---|---|---|
| `set_post_key.php` / `check_post_key.php` | `$_SESSION['postkey'] = strtoupper(substr(md5(user_id.rand(0,9999999)),0,10))` | POST `postkey` 与 session 相等 | 用完即焚（校验后 `unset`） |
| `set_get_key.php` / `check_get_key.php` | `$_SESSION['getkey']` | GET `getkey` | 用于链接型切换操作 |
| `csrf.php` / `csrf_check.php` | `$_SESSION['csrf_keys'][]` 数组 | POST `csrf` 在数组中 | 校验后 `array_splice` 移除，失败 403 |

**各页面权限矩阵（摘要）**

| 页面 | 权限 |
|---|---|
| `problem_add.php` | administrator / contest_creator / problem_editor |
| `problem_edit.php` | 显示：administrator / problem_editor；保存：+ `p<pid>` |
| `problem_del.php` | administrator / `p<pid>` |
| `problem_changeid.php` / `problem_copy.php` | 仅 administrator |
| `problem_export_xml.php` / `problem_import*.php` | administrator / problem_importer |
| `contest_add/edit/list.php` | administrator / contest_creator |
| `contest_*_change.php` | `m<cid>` / administrator / contest_creator |
| `user_add.php` / `user_import.php` | administrator / user_adder |
| `changepass.php` / `update_pw.php` | administrator / password_setter（禁止改 administrator 的密码；`update_pw.php` 运行后自删） |
| `privilege_add/delete/list.php` | 仅 administrator（禁止删自己的 administrator） |
| `settings.php` | administrator 且 `$domain == $DOMAIN` |
| `backup.php` / `update_db.php` / `rejudge.php` / `setmsg.php` / `news_add.php` | 仅 administrator |
| `problem_judge.php` | 仅 http_judge |
| `phpfm.php` | administrator / problem_editor |
| `suspect_list.php` | **无显式权限校验**（已知风险） |

**系统设置（FR-ADM-02，`settings.php`）**：写入目标是 `web/include/db_info.inc.php` 文件（**不是数据库**）。表单字段前缀 `cfg_`；仅白名单内的变量可写；`true/false`、数值直接写，其余 `var_export` 加引号；未勾选的 checkbox 写 `false`。**受保护不可改**：`$DB_HOST/$DB_NAME/$DB_USER/$DB_PASS/$OJ_DATA/$SMTP_PASS/$OJ_ADMIN/$OJ_RANK_HIDDEN/$OJ_LOGIN_MOD/$OJ_*_ASEC/$OJ_BG/$OJ_CDN_URL/$OJ_AI_HTML/$OJ_FANCY_MP3/$OJ_UDPSERVER/$OJ_JUDGE_HUB_PATH`。

**数据库升级（FR-ADM-03，`update_db.php`）**：内置探测+`ALTER`/`CREATE` 语句数组（约 65 条），循环执行以把旧库补齐到当前结构。

**备份（FR-ADM-04，`backup.php`）**：`mysqldump` 风格导出到 `$OJ_DATA/0/<DB>_<time>.sql`，再用 `ZipArchive` 打包 `data/` 与 `upload/`（跳过 `data/0`）为 `backup_<time>.zip`。

**监控（FR-ADM-05，`watch.php`）**：读 `/proc/meminfo`、`/proc/net/sockstat`、磁盘、`top -bn1`；历史落 `/dev/shm/watchlog`（JSON，保留 1 周）；`?json` 返回 `[cpu,mem,swap,tcp]`。

### 8.13 高风险功能（复刻须保留门禁或加固）

| 功能 | 风险 | 现状 |
|---|---|---|
| `phpfm.php` 文件管理器 | 文件读写/重命名/删除/上传/解压/执行 PHP 与 shell 命令 | 仅 `administrator/problem_editor`，**无 CSRF、无路径白名单** |
| `aidba.php` | AI 生成 SQL 直接执行 | 权限门禁保护 |
| `problem_judge.php?gettestdata` | 任意路径读取 `$OJ_DATA` 下文件 | 仅 `http_judge` |
| `suspect_list.php` | 展示同 IP 多用户/同用户多 IP | 无权限校验 |
| `loginlog.password` | 明文记录登录尝试密码 | 设计如此，用于审计 |

---

## 9. 配置项全集

> 复刻时，这些开关共同决定了系统的全部可变行为。**默认值必须与下表一致**，否则行为不可比对。

### 9.1 Web 配置（`web/include/db_info.inc.php`，`static $OJ_*`）

#### 9.1.1 数据库与标识

| 变量 | 默认 | 说明 |
|---|---|---|
| `$DB_HOST` | `localhost` | |
| `$DB_NAME` | `jol` | |
| `$DB_USER` / `$DB_PASS` | `root` / `root` | |
| `$DB_RO_USER` / `$DB_RO_PASS` | `root` / `root` | 只读账户（用于 `rdo_query`） |
| `$OJ_NAME` | `HUSTOJ` | 系统名；**同时是全部 session 键前缀** |
| `$OJ_HOME` | `./` | |
| `$OJ_ADMIN` | `root@localhost` | 管理员邮箱；等于此哨兵值时不发告警邮件 |
| `$OJ_DATA` | `/home/judge/data` | 测试数据根 |
| `$OJ_TEMPLATE` | `syzoj` | 模板目录名 |
| `$OJ_CSS` | `white.css` | |
| `$OJ_BG` | `/image/background.jpg` | |
| `$OJ_BEIAN` | `false` | 备案号 |
| `$OJ_CDN_URL` | `""` | 非空时输出 CORS 头 |
| `$OJ_INDEX_NEWS_TITLE` | `HelloWorld!` | 首页展示的公告标题 |
| `$OJ_MENU_NEWS` / `$OJ_MENU_DROPDOWN` | `true` / `false` | |

#### 9.1.2 语言与显示

| 变量 | 默认 | 说明 |
|---|---|---|
| `$OJ_LANG` | `cn` | 可选 `cn/ug/en/fa/ko/th` |
| `$OJ_LANGMASK` | `33554356` | 见 3.3 |
| `$OJ_ACE_EDITOR` | `true` | 代码高亮编辑器 |
| `$OJ_MATHJAX` | `true` | |
| `$OJ_MARKDOWN` | `marked.js` | `marked.js` / `markdown-it` |
| `$OJ_MARK` | `mark` | `mark`=显示得分，`percent`=显示错误率 |
| `$OJ_INDENT` | `true` | 缩进规范检测 |
| `$OJ_DIV_FILTER` | `false` | 过滤题面 div |
| `$OJ_BBCODE_IN_PROBLEM` | `false` | |
| `$OJ_AUTO_SHOW_OFF` | `false` | 默认展开编辑器 |

#### 9.1.3 邮箱

| 变量 | 默认 |
|---|---|
| `$SMTP_SERVER` | `smtp.qq.com` |
| `$SMTP_PORT` | `587` |
| `$SMTP_USER` | `mailer@qq.com`（哨兵：等于此值视为未配置） |
| `$SMTP_PASS` | `your_smpt_auth_password` |
| `$OJ_MAIL` | `false` |

#### 9.1.4 注册与登录

| 变量 | 默认 | 说明 |
|---|---|---|
| `$OJ_REGISTER` | `true` | |
| `$OJ_LOGIN_MOD` | `hustoj` | 见 4.7；非 hustoj 时禁止注册 |
| `$OJ_REG_NEED_CONFIRM` | `false` | 新用户需审核 |
| `$OJ_EMAIL_CONFIRM` | `false` | 邮件激活 |
| `$OJ_EXPIRY_DAYS` | `365` | 手工建号的默认有效期 |
| `$OJ_NEED_LOGIN` | `false` | 全站需登录 |
| `$OJ_LOGIN_FAIL_LIMIT` | `5` | 5 分钟内最大失败次数（IP 阈值为其 4 倍=20） |
| `$OJ_LONG_LOGIN` | `false` | 记住我 |
| `$OJ_KEEP_TIME` | `"30"` | Cookie 有效天数 |
| `$OJ_REG_SPEED` | `60` | 同 IP/邮箱每小时注册上限，0=不限 |
| `$OJ_VCODE` | `false` | 验证码（队列 >50 时会被强制开启） |
| `$OJ_LIMIT_TO_1_IP` | `true` | 单 IP 登录 |
| `$OJ_NICK_IMMUTABLE` | `false` | 昵称不可改 |
| `$OJ_OPENID_PWD` | `8a367fe87b1e406ea8e94d7d508dcf01` | 第三方登录用户的固定密码 |

#### 9.1.5 比赛与排名

| 变量 | 默认 | 说明 |
|---|---|---|
| `$OJ_RANK_HIDDEN` | `"'admin','zhblue'"` | 带单引号的 SQL IN 列表 |
| `$OJ_RANK_LOCK_PERCENT` | `0` | 封榜比例 |
| `$OJ_RANK_LOCK_DELAY` | `3600` | 赛后封榜持续秒数 |
| `$OJ_SHOW_METAL` | `true` | 奖牌 |
| `$OJ_SHOW_DIFF` | `true` | WA 差异对比 |
| `$OJ_HIDE_RIGHT_ANSWER` | `true` | 隐藏填空正确答案 |
| `$OJ_NOIP_KEYWORD` | `noip` | 标题关键词触发 NOIP 模式 |
| `$OJ_NOIP_HINT` | `false` | NOIP 赛中是否显示提示 |
| `$OJ_CONTEST_LIMIT_KEYWORD` | `限时` | 个人限时赛关键词 |
| `$OJ_OI_MODE` | `false` | OI 模式（禁用排名/状态/统计/内邮/论坛等） |
| `$OJ_OI_1_SOLUTION_ONLY` | `false` | 仅保留最后一次提交 |
| `$OJ_CONTEST_TOTAL_100` | `false` | 比赛按 100 分计 |
| `$OJ_CONTEST_RANK_FIX_HEADER` | `false` | |
| `$OJ_FREE_PRACTICE` | `false` | 自由练习（不受比赛占用限制） |
| `$OJ_NO_CONTEST_WATCHER` | `false` | 禁止观战私有赛 |
| `$OJ_ON_SITE_TEAM_TOTAL` | `0` | 奖牌计算队伍基数，0=按榜单实际队伍数 |
| `$OJ_ON_SITE_CONTEST_ID` / `$OJ_EXAM_CONTEST_ID` | 未定义（注释中） | 现场赛/考试模式，启用后强制 `$OJ_FREE_PRACTICE=false` |

#### 9.1.6 判题与队列

| 变量 | 默认 | 说明 |
|---|---|---|
| `$OJ_UDP` | `true` | UDP 唤醒判题机 |
| `$OJ_UDPSERVER` | `127.0.0.1` | 逗号分隔多机，可带 `:port` |
| `$OJ_UDPPORT` | `1536` | |
| `$OJ_JUDGE_HUB_PATH` | `../judge` | UDP 报文内容（JudgeHub 子路径） |
| `$OJ_REDIS` / `$OJ_REDISSERVER` / `$OJ_REDISPORT` / `$OJ_REDISQNAME` | `false` / `127.0.0.1` / `6379` / `hustoj` | Redis 队列 |
| `$OJ_MEMCACHE` / `$OJ_MEMSERVER` / `$OJ_MEMPORT` | `false` / `127.0.0.1` / `11211` | |
| `$OJ_SUBMIT_COOLDOWN_TIME` | `10` | 提交冷却秒数（未设置兜底 3） |
| `$OJ_APPENDCODE` | `true` | prepend/append 代码拼装 |
| `$OJ_CE_PENALTY` | `false` | **false 表示 CE 不计罚时** |
| `$OJ_TEST_RUN` | `false` | 允许测试运行 |
| `$OJ_ENCODE_SUBMIT` | `false` | base64 编码提交（绕 WAF） |
| `$OJ_AUTO_SHARE` | `false` | AC 后可看他人代码 |
| `$OJ_SIM` | `false` | 显示相似度（检测开关在 judge.conf） |
| `$OJ_DICT` | `false` | 在线翻译 |
| `$OJ_DOWNLOAD` / `$OJ_DL_1ST_WA_ONLY` | `false` / `false` | 下载 WA 数据 |
| `$OJ_PRINTER` | `false` | 打印服务 |
| `$OJ_BLOCKLY` | `false` | |
| `$OJ_FANCY_RESULT` / `$OJ_FANCY_MP3` | `false` / `http://cdn.hustoj.com/mp3.php` | AC 动画/音效 |
| `$OJ_PUBLIC_STATUS` | `true` | 公开所有人判题结果 |
| `$OJ_POISON_BOT_COUNT` | `10` | 机器人投毒起始 AC 数 |
| `$OJ_BENCHMARK_MODE` | `false` | 压测模式（跳过验证码与冷却） |
| `$OJ_OLD_FASHINED` | `false` | 保留旧版交互习惯 |
| `$OJ_SHARE_CODE` | `false` | 代码分享 |
| `$OJ_RECENT_CONTEST` | `false` | 近期比赛组件 |
| `$OJ_REMOTE_JUDGE` | `false` | 远端 OJ |
| `$OJ_AI_HTML` / `$OJ_AINO` / `$OJ_AI_API_URL` | `false` / `false` / `aiapi/demo.php` | AI |
| `$OJ_BBS` | `false` | `false`/`discuss3`/`discuss`/`bbs` |
| `$OJ_ONLINE` | `false` | 在线统计 |
| `$OJ_OFFLINE_ZIP_CCF_DIRNAME` | `true` | 离线导入按 CCF 目录名校验 |

#### 9.1.7 社交登录与日志

`$OJ_WEIBO_AUTH/$OJ_WEIBO_AKEY/$OJ_WEIBO_ASEC/$OJ_WEIBO_CBURL`、`$OJ_RR_*`、`$OJ_QQ_*` 默认均为 `false`/占位值。

日志：`$OJ_LOG_ENABLED=false`，以及 `$OJ_LOG_DATETIME_FORMAT="Y-m-d H:i:s"`、`$OJ_LOG_PID_ENABLED`、`$OJ_LOG_USER_ENABLED`、`$OJ_LOG_URL_ENABLED`、`$OJ_LOG_URL_HOST_ENABLED`、`$OJ_LOG_URL_PARAM_ENABLED`、`$OJ_LOG_TRACE_ENABLED`（均 false）。日志文件 `/var/log/hustoj/<OJ_NAME>.log`。

#### 9.1.8 友好级别穿透（BR-CFG-01，必须逐级复现）

```php
switch ($OJ_FRIENDLY_LEVEL) {          // 默认 1
  case 9:  $OJ_GUEST = true;
  case 8:  $OJ_DOWNLOAD = true;
  case 7:  $OJ_BBS = "discuss3"; $OJ_FREE_PRACTICE = true;
  case 6:  $OJ_LONG_LOGIN = true;
  case 5:  $OJ_TEST_RUN = true;
  case 4:  $OJ_MAIL = true; $OJ_AUTO_SHARE = true;
  case 3:  $OJ_SHOW_DIFF = true; $OJ_VCODE = false;
  case 2:  $OJ_LANG = "cn";
  case 1:  date_default_timezone_set("Asia/Shanghai");
           pdo_query("SET time_zone ='+8:00'");
  case 0:  break;
  case -1: $OJ_NEED_LOGIN = true; $OJ_REGISTER = false;
}
```

> PHP `switch` **穿透**语义：设为 N 时会依次执行 N、N-1、…、0 的全部赋值。级别越高越"傻瓜"，安全性越低。

### 9.2 判题配置（`/home/judge/etc/judge.conf`）

| 键 | 发行版默认 | 说明 |
|---|---|---|
| `OJ_HOST_NAME` / `OJ_USER_NAME` / `OJ_PASSWORD` / `OJ_DB_NAME` / `OJ_PORT_NUMBER` | `127.0.0.1` / `root` / `root` / `jol` / `3306` | 数据库 |
| `OJ_RUNNING` | `1` | 并发判题槽数 |
| `OJ_SLEEP_TIME` | `1` | 轮询/UDP 超时秒 |
| `OJ_TOTAL` / `OJ_MOD` | `1` / `0` | 多机分片（按 solution_id 取模） |
| `OJ_JAVA_TIME_BONUS` / `OJ_JAVA_MEMORY_BONUS` | `2` / `64` | |
| `OJ_JAVA_XMS` / `OJ_JAVA_XMX` | `-Xms64M` / `-Xmx128M` | |
| `OJ_IGNORE_ESOL` | `1` | 忽略行末空白 |
| `OJ_INTERNAL_MARK` | `0` | 单点部分分 |
| `OJ_SIM_ENABLE` | `0` | 查重 |
| `OJ_RAW_TEXT_DIFF` | `1` | 填空题显示正确答案 |
| `OJ_FULL_DIFF` | `1` | WA 对比级别 0-3 |
| `OJ_HTTP_JUDGE` | `0` | HTTP 判题机模式 |
| `OJ_HTTP_BASEURL` / `APIPATH` / `LOGINPATH` / `USERNAME` / `PASSWORD` | `http://127.0.0.1/` / `/admin/problem_judge.php` / `/login.php` / `admin` / `admin` | |
| `OJ_HTTP_DOWNLOAD` | `1` | 0=不下载 1=wget 2=rsync.sh |
| `OJ_OI_MODE` | `1` | 判完所有测试点（不遇错即停） |
| `OJ_SHM_RUN` | `0` | 用 /dev/shm |
| `OJ_USE_MAX_TIME` | `0` | 取最大单点耗时 |
| `OJ_TIME_LIMIT_TO_TOTAL` | `1` | 按总时间判 TLE |
| `OJ_LANG_SET` | `0,1,2,…,20` | 可判语言集合（代码内回退 `0,1,3,6`） |
| `OJ_COMPILE_CHROOT` | `1` | 编译 chroot |
| `OJ_TURBO_MODE` | `0` | 跳过中间状态更新 |
| `OJ_CPU_COMPENSATION` | `1.0` | CPU 补偿（>1 放宽时间） |
| `OJ_UDP_ENABLE` / `OJ_UDP_SERVER` / `OJ_UDP_PORT` | `1` / `127.0.0.1` / `1536` | |
| `OJ_PYTHON_FREE` | `0` | Python 不 chroot |
| `OJ_COPY_DATA` | `0` | NOIP 文件输入输出 |
| `OJ_USE_DOCKER` / `OJ_DOCKER_PATH` / `OJ_INTERNAL_CLIENT` | `0` / `/usr/bin/docker` / `1` | 容器化判题 |
| `OJ_DEDICATED` | `1` | 专机判题 |
| `OJ_REDISENABLE` / `SERVER` / `PORT` / `AUTH` / `QNAME` | Redis 队列（可选） | |
| `OJ_CC_STD` / `OJ_CPP_STD` / `OJ_CC_OPT` | 注释掉（默认 c99 / c++11 / -O2） | |
| `OJ_PHP_PATH` / `OJ_WWW_UID` | `src/web` / `33` | cron 执行身份 |

**题目级覆盖**：`$OJ_DATA/<problem_id>/judge.conf` 可覆盖上述配置，仅对该题生效。

---

## 10. 非功能需求、部署与验收

### 10.1 非功能需求

| 编号 | 类别 | 需求 |
|---|---|---|
| NFR-01 | 性能 | 提交落库必须在冷却/验证码校验后一次性完成；判题机并发槽由 `OJ_RUNNING` 控制（默认 3），单槽对应一个 `run<N>` 工作目录 |
| NFR-02 | 性能 | `status.php` 内层先 `LIMIT 50` 再 JOIN，避免全表扫描；榜单/列表走页面缓存（30s/10s/2s） |
| NFR-03 | 并发 | 多判题机领取任务的唯一性依赖 `UPDATE ... WHERE result<2 LIMIT 1` 的行级原子性，禁止改成"先 SELECT 后 UPDATE" |
| NFR-04 | 安全 | 用户程序运行于 `chroot` + `setuid(judge)` + `setrlimit` + `unshare(CLONE_NEWNET)` + ptrace 系统调用白名单的多层沙箱 |
| NFR-05 | 安全 | 所有写操作需 CSRF token（postkey/getkey/csrf 三选一）；后台另有 Referer 白名单 |
| NFR-06 | 安全 | 输出统一 `htmlentities(..., ENT_QUOTES, 'UTF-8')`；富文本经 `RemoveXSS()`；讨论区仅允许白名单 BBCode |
| NFR-07 | 安全 | 密码不得明文存储（见 BR-USR-03）；登录失败日志虽记录明文尝试密码，但仅管理员可查 |
| NFR-08 | 兼容 | 必须支持旧式 32 位 md5 密码登录并支持升级 |
| NFR-09 | 国际化 | 语言包 `lang/<lang>.php`，键为 `$MSG_*`；支持 `cn/ug/en/fa/ko/th`；解析顺序见 1.3 |
| NFR-10 | 可运维 | 判题日志 `$oj_home/log/client.log`（追加）；Web 日志 `/var/log/hustoj/<OJ_NAME>.log`（可开关，可含 PID/用户/URL/参数/调用栈） |
| NFR-11 | 可用性 | 队列拥堵（`result<4` 记录 > 50）时自动开启提交验证码 |
| NFR-12 | 可伸缩 | 支持多判题机（UDP 唤醒 + `OJ_TOTAL/OJ_MOD` 分片 + Redis 队列 + HTTP 判题机 + Docker 容器化） |
| NFR-13 | 数据保护 | 每日 `bak.sh` 备份（cron `1 0 * * *`）；每小时 `oomsaver.sh`（防 OOM）；`backup.php` 导出 SQL + data/upload 压缩包 |

### 10.2 部署与运维（FR-OPS-07）

- Web 服务：nginx，`root /home/judge/src/web`；PHP-FPM 以 `www-data` 运行。
- 判题服务：`/etc/init.d/hustoj` 启停 `judged`（停止需 `kill -9`）。
- 目录权限：`chgrp www-data /home/judge`、`chmod -R 771 $OJ_DATA`（否则 `mkdata()` 写文件失败并提示）。
- 依赖的外部命令（判题侧）：`gcc/g++/fpc/javac/ruby/python3/php/perl/mcs/fbc/clang/clang++/luac/go/f95/cobc/octave/Rscript/node/sqlite3/cjc/scratch-run`、`wget`、`docker`（可选）、`sim_*`（查重，来自 `similarity-tester`）。
- 定时任务：`0 1 * * * bak.sh`、`0 * * * * oomsaver.sh`。

### 10.3 平台依赖与移植约束（NFR-14，1:1 复刻的最大障碍）

| Linux 机制 | 用途 | 非 Linux 替代思路 |
|---|---|---|
| `fork` / `waitpid` / `wait4(__WALL)` + `rusage` | 进程看护、CPU/内存度量 | 需等价进程模型；`rusage` 是 TLE/MLE 判定的数据来源 |
| `ptrace(TRACEME/SYSCALL/GETREGS/KILL/SETOPTIONS)` | 系统调用白名单 | Windows/macOS 无；可用作业对象/沙箱框架 + 系统调用审计替代，但**白名单表须按架构移植** |
| `setrlimit(CPU/AS/FSIZE/NPROC/STACK)` | 资源限制 | Windows：Job Object；macOS：`setrlimit` 部分可用 |
| `chroot` + bind mount | 文件沙箱 | macOS 无等价；建议用容器 |
| `unshare(CLONE_NEWNET)` | 网络隔离 | 需容器或网络命名空间 |
| `setuid/setgid` + `getpwnam("judge")` | 降权到 uid 1536 | 需预建同名账户 |
| `/proc/<pid>/status`（VmPeak/VmRSS） | 内存峰值测量 | 需平台 API |
| POSIX 信号 `SIGXCPU/SIGXFSZ/SIGALRM/SIGKILL` | TLE/OLE 判定依据 | 需等价超时与文件尺寸限制机制 |
| `mkfifo` 命名管道 | 交互题（spj=3）退出码传递 | 各平台有等价 IPC |
| `fcntl(F_WRLCK)` 文件锁 | judged/run 目录单实例 | 各平台有等价 |

> **建议**：若目标平台非 Linux，采用 Docker 模式（`OJ_USE_DOCKER=1`，`--cap-add SYS_PTRACE --pids-limit 100`）把沙箱依赖收敛进 Linux 镜像，是最接近 1:1 的路径。

### 10.4 已知行为陷阱（复刻必读）

1. 新提交初始 `result=14`（非 0），源码写完后才置 0 —— 防止判题机抢单。
2. `langmask` 位为 **1 = 禁用**，且按 `count($language_ext)=25` 位计算。
3. ACM 罚时 **+1200 秒/次错误**；**AC 后的提交完全忽略**；排序**无第三关键字**。
4. 一血用 **`MIN(solution_id)`**，不是最早时间。
5. 封榜期提交以 `result=0` 注入（记一次错误），解冻后重算；AC 落在冻结期会多记一次罚时。
6. `$OJ_CE_PENALTY=false` 是"CE 不计罚时"，不要按字面理解为"CE 要罚时"。
7. `contest_type` 位标志**仅在比赛进行中**生效（`start<now<end`），赛后自动失效。
8. `$OJ_FRIENDLY_LEVEL` 的 switch 是**穿透**的，级别 N 会覆盖到 0 级。
9. `judged.conf` 的 `OJ_LANG_SET` 与代码内回退值不同（`0..20` vs `0,1,3,6`）。
10. `spj` 调用参数顺序为 `(输入, 标准答案, 用户输出)`，与 testlib 惯例 `(in, ouf, ans)` 不同。
11. `Obj-C(10)` 与 `Matlab(20)` 扩展名同为 `m`，`prepend/append` 文件会互相覆盖。
12. `suspect_list.php` 无权限校验；`phpfm.php` 可执行命令 —— 复刻时应保留门禁或主动加固。
13. `solution.result=13`（TR）既表示"测试运行完成"，也用于 OI 单解模式作废旧提交。
14. 删除题目时 `solution.problem_id` 被置 0 且 `result=13`，历史提交不删除。

### 10.5 复刻验收清单（Acceptance Checklist）

每条均为可直接观测的行为断言，建议作为回归测试。

**A. 判题正确性**

- [ ] A1 标准输入输出题，答案完全一致 → **AC(4)**，`pass_rate` 为 1（OI 模式）。
- [ ] A2 答案数字正确但行末多空格/末行多换行 → **AC(4)**（`OJ_IGNORE_ESOL=1`）。
- [ ] A3 答案仅在中间空行/空格不同且无法对齐 → **PE(5)**。
- [ ] A4 内容不同 → **WA(6)**，且第一个失败点即停止（非 OI 模式）。
- [ ] A5 死循环 → **TLE(7)**，`time` 被顶到 `time_limit*1000`。
- [ ] A6 大数组开 200MB（限制 128MB）→ **MLE(8)**。
- [ ] A7 无限输出 → **OLE(9)**。
- [ ] A8 段错误/除零 → **RE(10)**，`runtimeinfo` 有内容。
- [ ] A9 语法错误 → **CE(11)**，`compileinfo` 内容 ≤ 16384 字节。
- [ ] A10 使用禁用系统调用（如 `fork` 于 C）→ **RE(10)** 且 `runtimeinfo` 含 `Forbidden system call`。
- [ ] A11 特判题：`spj` 返回 0/1/2/3 → 分别 AC/WA/PE/RE。
- [ ] A12 时间限制 1s 的程序，单测试点实际 CPU 上限为 `ceil(1/1.0)+1 = 2` 秒。

**B. 提交流程**

- [ ] B1 未登录提交 → 提示登录，不落库。
- [ ] B2 10 秒内重复提交 → 不产生新 solution，跳转到 status。
- [ ] B3 提交后 `source_code_user` 为原始代码，`source_code` 为拼装后代码；开启 `prepend` 时二者不同。
- [ ] B4 Python 提交且 `spj != 2` 时，`source_code` 以 `# coding=utf-8\n` 开头。
- [ ] B5 源码长度 < 2 或 > 65536 → 拒绝。
- [ ] B6 比赛提交 `num` 为场内序号，`contest_id` 正确。
- [ ] B7 自测运行 `problem_id=0`，`custominput` 有记录，结果 `13`。
- [ ] B8 队列待判 > 50 时提交页强制出现验证码。

**C. 用户与权限**

- [ ] C1 密码密文形如 `base64(sha1_raw(md5_hex(pw).salt4).salt4)`，长度 32。
- [ ] C2 旧式 32 位十六进制 md5 密文仍可登录。
- [ ] C3 首个用户自动获得 `administrator`（`privilege` 表为空时）。
- [ ] C4 用户名仅允许 `[A-Za-z0-9_-]`，首字符额外允许 `*`。
- [ ] C5 登录失败写 `loginlog`（`password='login fail'`）；成功写 `'login ok'`。
- [ ] C6 5 分钟内同用户失败 > 5 次或同 IP > 20 次 → 拒绝登录。
- [ ] C7 开启单 IP 限制后，换 IP 访问会强制登出。
- [ ] C8 CSRF token 一次性：重复使用第二次失败。

**D. 比赛**

- [ ] D1 比赛进行中，`problem.php?cid&pid` 页面中不出现全局 `problem_id`。
- [ ] D2 ACM 罚时：错 2 次后第 30 分钟 AC → 罚时 = 1800 + 2×1200 = 4200 秒，显示 `01:10:00`。
- [ ] D3 AC 之后再提交 3 次错误 → 罚时不变。
- [ ] D4 一血取 `MIN(solution_id)` 而非最早时间（可用两条乱序数据验证）。
- [ ] D5 排序：同解题数按罚时升序，无第三关键字。
- [ ] D6 OI 榜：部分分取最佳值，`result=4` 时按 100 分计。
- [ ] D7 封榜窗口内提交在榜单上不显示为 AC；`OJ_RANK_LOCK_DELAY` 过后恢复。
- [ ] D8 私有比赛无 `c<cid>` 时被拒绝；密码正确后置位 session。
- [ ] D9 `subnet` 支持逗号分隔多段 CIDR，任一命中放行。
- [ ] D10 OI 单解模式开启后，新提交把旧提交置 `result=13`。
- [ ] D11 气球：AC 记录出现，`status=0`；点击后 `status=1`。

**E. 题目与数据**

- [ ] E1 新建题目默认 `defunct='Y'`，需启用才在列表出现。
- [ ] E2 数据目录生成 `sample.in/sample.out`；导入 FPS 的 `<test_input name="X">` 生成 `X.in`。
- [ ] E3 FPS 中 `time_limit unit="ms"` → 入库值 ÷1000；`memory_limit unit="kb"` → ÷1024。
- [ ] E4 删除题目后相关 `solution.problem_id=0, result=13`。
- [ ] E5 首次 AC 触发积分：`users.coin_earned += problem.coin`，且仅一次。
- [ ] E6 自己抄自己不写入 `sim`（触发器 `simfilter` 生效）。
- [ ] E7 相似度阈值 80。

**F. 展示与配置**

- [ ] F1 `problem.php` 缓存 10s；`status.php` 缓存 2s；`ranklist.php` 缓存 30s。
- [ ] F2 `$OJ_FRIENDLY_LEVEL=9` 时，8/7/6/5/4/3/2/1 级的效果全部生效。
- [ ] F3 `status.php` 中他人比赛进行中的提交不显示耗时/内存。
- [ ] F4 响应头含 `X-XSS-Protection`、`X-Download-Options`、`Referrer-Policy`。
- [ ] F5 所有 session 键前缀为 `$OJ_NAME.'_'`。

### 10.6 附录 A：Web 页面清单（URL 表面）

| 路径 | 用途 |
|---|---|
| `index.php` / `portal.php` | 首页 |
| `problemset.php` / `category.php` / `problemstatus.php` | 题目列表 / 分类云 / 题目统计 |
| `problem.php` | 题面（练习 `?id=` / 比赛 `?cid=&pid=`） |
| `submit.php` / `submitpage.php` | 提交（处理 / 页面） |
| `status.php` / `status-ajax.php` | 状态页与轮询 |
| `showsource.php` / `showsource2.php` / `getsource.php` / `comparesource.php` | 源码查看与对比 |
| `ceinfo.php` / `reinfo.php` | 编译错误 / 运行错误与差异 |
| `contest.php` / `contest-header.php` / `contest-check.php` | 比赛 |
| `contestrank.php` / `-oi.php` / `2~5.php` / `.xls.php` | 各类榜单 |
| `conteststatistics.php` | 比赛统计 |
| `balloon.php` / `balloon_view.php` | 气球 |
| `printer.php` / `printer_view.php` | 打印 |
| `login.php` / `loginpage.php` / `logout.php` / `register.php` / `registerpage.php` | 认证 |
| `lostpassword.php` / `lostpassword2.php` / `active.php` | 找回密码 / 激活 |
| `modify.php` / `modifypage.php` / `userinfo.php` / `user_set_ip.php` | 用户中心 |
| `ranklist.php` / `online.php` / `refresh-privilege.php` / `setlang.php` | 排名 / 在线 / 刷新权限 / 语言 |
| `vcode.php` / `session.php` | 验证码 / 会话检查 |
| `discuss.php` / `newpost.php` / `post.php` / `thread.php` / `superthread.php` / `threadadmin.php` / `bbs.php` | 讨论区 |
| `mail.php` / `viewnews.php` | 站内信 / 公告 |
| `sharecodepage.php` / `sharecodelist.php` | 代码分享 |
| `download.php` / `export_ac_code.php` / `export_contest_code.php` | 下载与导出 |
| `group_statistics.php` / `group_total.php` | 班级统计 |
| `faqs.php` / `faqs.cn.php` / `service.php` / `service.html` | 帮助 |
| `remote.php` / `cron.php` / `saasinit.php` / `install.php` | 远端/定时/SaaS/安装 |
| `suspect_list.php` / `problem-ajax.php` / `recent-contest.php` / `fancy.php` / `xiaoke.php` / `lip.php` | 杂项 |
| `admin/*` | 后台（见 8.12） |

### 10.7 附录 B：外部接口清单

| 接口 | 协议 | 说明 |
|---|---|---|
| `admin/problem_judge.php` | HTTP POST（`text/plain`） | 判题机与主站交互（见 6.6），仅 `http_judge` 权限 |
| UDP `<OJ_UDPSERVER>:<OJ_UDPPORT>` | UDP | 唤醒判题；报文为 `$OJ_JUDGE_HUB_PATH` 或 `solution_id` |
| `status-ajax.php` | HTTP GET | 前端轮询判题结果 |
| `aiapi/*.php` | HTTP | LLM 适配层（qwen/hy/deepseek/demo/proxy） |
| `remote_*.php` | HTTP | 远端 OJ 抓取与提交 |
| MySQL `jol` | SQL | 判题机直连（默认模式） |
| `judgehub`（UDP 1536） | UDP | 多租户按路径分发到各租户 judged |

### 10.8 附录 C：语言包键约定

所有面向用户的文案取自 `lang/<lang>.php` 中的 `$MSG_*` 变量。复刻时应至少实现以下键（否则页面会出现空串）：

`$MSG_Pending, $MSG_Pending_Rejudging, $MSG_Compiling, $MSG_Running_Judging, $MSG_Accepted, $MSG_Presentation_Error, $MSG_Wrong_Answer, $MSG_Time_Limit_Exceed, $MSG_Memory_Limit_Exceed, $MSG_Output_Limit_Exceed, $MSG_Runtime_Error, $MSG_Compile_Error, $MSG_Compile_OK, $MSG_TEST_RUN, $MSG_MANUAL_CONFIRMATION, $MSG_SUBMITTING, $MSG_REMOTE_PENDING, $MSG_REMOTE_JUDGING, $MSG_PD, $MSG_PR, $MSG_CI, $MSG_RJ, $MSG_AC, $MSG_PE, $MSG_WA, $MSG_TLE, $MSG_MLE, $MSG_OLE, $MSG_RE, $MSG_CE, $MSG_CO, $MSG_TR, $MSG_MC, $MSG_Login, $MSG_Register, $MSG_ADMIN, $MSG_PROBLEM, $MSG_CONTEST, $MSG_NO_SUCH_PROBLEM, $MSG_PROBLEM_RESERVED, $MSG_NO_PLS, $MSG_VCODE_WRONG, $MSG_TOO_SHORT, $MSG_TOO_LONG, $MSG_LINK_ERROR, $MSG_NOT_IN_CONTEST, $MSG_NOT_INVITED, $MSG_NO_PROBLEM, $MSG_TIME_WARNING, $MSG_PRIVATE_WARNING, $MSG_SUBNET, $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM, $MSG_FORBIDDEN, $MSG_DOWNLOAD, $MSG_RANKLIST, $MSG_OTHERS, $MSG_CONTEST_STATUS, $MSG_NOIP_WARNING, $MSG_SHOW_DIFF, $MSG_UPSOLVING, $MSG_ONLY_LAST_SUBMISSION, $MSG_AI_HELP, $MSG_PROBLEM_USED_IN, $MSG_NOIP_NOHINT, $MSG_BALLOON_DONE, $MSG_BALLOON_PENDING, $MSG_SECONDS, $MSG_MINUTES, $MSG_HOURS, $MSG_DAYS, $MSG_SYS_WARN, $MSG_USER, $MSG_IS_ROBOT, $MSG_IMPORTED, $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT, $MSG_REG_INFO`

---

**文档结束。** 复刻过程中若发现本文档未覆盖的行为，应以"参考实现基线"（0.2 节）中的源码为最终裁决依据，并将结论回补到本文档对应章节。
