# Core 与 Web 的连接方式解析

### 简化 ER 图

![](images/c2web.jpg)

### 数据库连接（默认）

1. `Web` 插入 `Solution` 表，`source_code`表。
2. `Web` 发送UDP通知给 `judged` 或 `judgehub`( 可选，不通知就是定时轮询 )。
3. `judged` 收到UDP通知，或者UDP接受超时后轮询 `solution` 表，发现新记录。 若是judgehub收到UDP通知，则启动judged做任务查询。
4. `judged` 启动 judge_client 完成判题。
5. `judge_client` 更新 `solution` 表 `result` 等字段
6. `Web` 端轮询 `soltuion` 显示 `result` 等字段。

### HTTP 方式连接

1. `Web` 插入 `Solution` 表
2. `Web` 发送UDP通知给 `judged` 或 `judgehub`( 可选，不通知就是定时轮询 )。
3. `judged` 访问 `Web` 端 `admin/problem_judge.php` ，发现新纪录，启动judge_client。
4. `judge_client` 根据配置judge.conf决定是否下载测试数据，通过admin/problem_judge.php下载到所需数据，完成判题。
5. `judge_client` 向 `Web` 端 `admin/problem_judge.php` 提交结果，`problem_judge.php` 更新 `solution` 表 `result` 等字段。
6. `Web` 端轮询 `soltuion` 显示 `result` 等字段。

