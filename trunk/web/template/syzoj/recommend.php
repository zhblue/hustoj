<?php
// 题目推荐页视图（syzoj 模板）
// 结构对齐 template/syzoj/skeleton.php：设置 $show_title -> include header.php -> 页面内容 -> include footer.php
// 交互内容（按钮 / 结果区）不放入 skeleton 的 .md 容器，避免被 marked 二次解析；
// 仅对 AI 异步返回的推荐结果单独用 marked 渲染，呈现更友好的排版。
$recent         = isset($view_recent)          ? $view_recent          : array();
$categories     = isset($view_categories)      ? $view_categories      : array();
$problem_count  = isset($view_problem_count)    ? intval($view_problem_count) : 0;

// 用户做过的分类（近期题目 source 标签的并集），用于把可用分类中“已练过”的标记为高亮
$done_categories = array();
foreach ($recent as $p) {
    foreach (preg_split('/\s+/', trim($p['source'] ?? '')) as $t) {
        $t = trim($t);
        if ($t !== '' && !in_array($t, $done_categories, true)) {
            $done_categories[] = $t;
        }
    }
}
$page_title     = isset($view_title)           ? $view_title           : '题目推荐';
$recent_json    = isset($view_recent_json)      ? $view_recent_json     : '[]';
$categories_json= isset($view_categories_json)  ? $view_categories_json : '[]';
$show_title     = $page_title . " - " . $OJ_NAME;
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class='padding'>
    <h1 class="ui header"><?php echo htmlspecialchars($page_title); ?></h1>
    <p style="color:#6b7280;margin-top:-6px">
        <?php echo htmlspecialchars($OJ_NAME); ?> · 基于你的做题记录，由 AI 教练给出下一步练习建议
    </p>

    <div class="ui segment">
        <h3 class="ui header">你最近做过的 <?php echo count($recent); ?> 道题目</h3>
        <?php if (empty($recent)) { ?>
            <p style="color:#6b7280">还没有做题记录，先去题库做几道题，再来获取个性化推荐吧。</p>
        <?php } else { ?>
            <div class="ui relaxed divided list">
            <?php foreach ($recent as $p) { ?>
                <div class="item">
                    <i class="file alternate icon"></i>
                    <div class="content">
                        <div class="header"><?php echo htmlspecialchars($p['title']); ?></div>
                        <div class="description">分类：<?php
                            $tags = preg_split('/\s+/', trim($p['source'] ?? ''));
                            $first = true;
                            foreach ($tags as $t) {
                                $t = trim($t);
                                if ($t === '') continue;
                                if (!$first) echo ' ';
                                $first = false;
                                echo '<a href="problemset.php?search=' . urlencode($t) . '">' . htmlspecialchars($t) . '</a>';
                            }
                        ?></div>
                    </div>
                </div>
            <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="ui segment">
        <h3 class="ui header"  onclick="$('#categories').toggle()" >系统中已有的题目分类</h3>
        <div style='display:none' id='categories'>
            <?php foreach ($categories as $c) {
                $done = in_array($c, $done_categories, true);
                $cls = $done ? 'ui primary label' : 'ui label';
                echo '<a class="' . $cls . '" href="problemset.php?search=' . urlencode($c) . '">' . htmlspecialchars($c) . '</a>';
            } ?>
        </div>
        <p style="color:#6b7280;margin-bottom:0">当前系统共有 <?php echo $problem_count; ?> 道可做的题目。</p>
    </div>

    <div class="ui segment">
        <button id="gen" class="ui primary button"><i class="magic icon"></i> 生成题目推荐</button>
        <span id="status" style="color:#6b7280;margin-left:12px"></span>
        <div id="result" class="md" style="margin-top:1.2em"></div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/css/"?>highlight.css">
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>highlight.min.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>marked.umd.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>marked-highlight.umd.js"></script>
<script>
(function () {
    // 初始化 marked + highlight（与 skeleton.php 同款，仅用于渲染 AI 推荐结果）
    var md = null;
    if (window.marked && window.markedHighlight && window.hljs) {
        var Marked = window.marked.Marked || window.marked;
        var markedHighlight = window.markedHighlight.markedHighlight || window.markedHighlight;
        md = new Marked(markedHighlight({
            emptyLangClass: 'hljs',
            langPrefix: 'hljs language-',
            highlight: function (code, lang) {
                var language = window.hljs.getLanguage(lang) ? lang : 'plaintext';
                return window.hljs.highlight(code, { language: language }).value;
            }
        }));
    }

    var btn = document.getElementById('gen');
    var statusEl = document.getElementById('status');
    var resultEl = document.getElementById('result');
    var timer = null;

    var RECENT = <?php echo $recent_json; ?>;
    var CATEGORIES = <?php echo $categories_json; ?>;
    var PROBLEM_COUNT = <?php echo $problem_count; ?>;

    // 兜底：把 AI 文本中出现的“系统分类名”自动包成可点击链接，
    // 防止 AI 偶尔没按 [分类](problemset.php?search=分类) 格式输出。
    // 先用占位符保护已存在的 Markdown 链接，避免重复包裹。
    function linkifyCategories(text) {
        if (!text) return text;
        var cats = CATEGORIES.slice().sort(function (a, b) { return b.length - a.length; }); // 长优先，避免子串重复
        var protectedLinks = [];
        var s = text.replace(/\[[^\]]*\]\([^)]*\)/g, function (m) {
            protectedLinks.push(m);
            return '@@LNK' + (protectedLinks.length - 1) + '@@';
        });
        cats.forEach(function (cat) {
            if (!cat) return;
            var esc = cat.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var re = new RegExp(esc, 'g');
            s = s.replace(re, function () {
                return '[' + cat + '](problemset.php?search=' + encodeURIComponent(cat) + ')';
            });
        });
        s = s.replace(/@@LNK(\d+)@@/g, function (m, p) { return protectedLinks[+p]; });
        return s;
    }

    function renderResult(text) {
        var safe = linkifyCategories(text);
        if (md) {
            resultEl.innerHTML = md.parse(safe);
            if (window.hljs) {
                resultEl.querySelectorAll('pre code').forEach(function (b) { window.hljs.highlightElement(b); });
            }
        } else {
            resultEl.textContent = safe;
        }
        // 推荐结果里的超链接统一新窗口打开
        resultEl.querySelectorAll('a').forEach(function (a) {
            a.setAttribute('target', '_blank');
            a.setAttribute('rel', 'noopener');
        });
    }

    function poll(id) {
        statusEl.textContent = '任务已提交（#' + id + '），等待 AI 返回…，每 2 秒刷新一次';
        timer = setInterval(function () {
            fetch('aiapi/ajax.php?id=' + id, { credentials: 'same-origin' })
                .then(function (r) { return r.text(); })
                .then(function (text) {
                    var t = (text || '').trim();
                    if (t === 'waiting' || t === '') return; // 尚未完成，继续轮询
                    clearInterval(timer); timer = null;
                    statusEl.textContent = '已完成';
                    renderResult(text);
                    btn.disabled = false;
                    btn.classList.remove('loading');
                })
                .catch(function () { /* 网络抖动，下一轮继续 */ });
        }, 2000);
    }

    btn.addEventListener('click', function () {
        if (timer) { clearInterval(timer); timer = null; }
        btn.disabled = true;
        btn.classList.add('loading');
        resultEl.innerHTML = '';
        statusEl.textContent = '正在创建任务…';

        var fd = new FormData();
        fd.append('recent', JSON.stringify(RECENT));
        fd.append('categories', JSON.stringify(CATEGORIES));
        fd.append('problem_count', PROBLEM_COUNT);

	fetch('<?php echo $OJ_AI_API_URL??'aiapi/demo.php'; ?>', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.text(); })
            .then(function (text) {
                var id = parseInt((text || '').trim(), 10);
                if (!id) {
                    statusEl.textContent = '创建任务失败，请稍后重试。';
                    resultEl.className = 'md';
                    resultEl.textContent = text;
                    btn.disabled = false;
                    btn.classList.remove('loading');
                    return;
                }
                poll(id);
            })
            .catch(function (e) {
                statusEl.textContent = '请求失败';
                resultEl.textContent = String(e);
                btn.disabled = false;
                btn.classList.remove('loading');
            });
    });
    btn.click();
})();
</script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
