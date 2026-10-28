<?php
require_once ("admin-header.php");
require_once("../include/check_post_key.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_problem_importer'])  )) {
  exit(1);
}

if (isset($OJ_LANG)) {
  require_once("../lang/$OJ_LANG.php");
}

require_once ("../include/const.inc.php");
require_once ("../include/problem.php");

// DOMjudge 题目包（problem-package-format 的 DOMjudge 分支）导入器。
// 和 problem_import_kattis.php 的差别集中在三处：
//   1) 判题方式写在 problem.yaml 的 type:（pass-fail / multi-pass / interactive / interactive multi-pass），
//      不是 Kattis 的 validation:；
//   2) 正式数据放在 data/secret/secret.zip 里（嵌套包），样例放在 data/sample/，题面在 statement/；
//   3) 校验器目录是单数的 output_validator/，源码名字随便作者起。
// multi-pass 导入后 spj=4，由 judge_client 的 communication_case 逐轮判题：DOMjudge 的
// validate.h 里 OutputValidator::init 的调用形式就是 ./validator input judgeanswer feedbackdir < teamoutput，
// 退出码 AC=42/WA=43，下一轮输入写 feedbackdir/nextpass.in，和我们的约定完全一致。
//
// 安全前提：
//   1) 外层包只按条目读内容，不把条目解压到磁盘，所以不存在 zip-slip；
//      嵌套的 secret.zip 必须先落成临时文件才能被 ZipArchive 打开（PHP 没有从内存开 zip 的接口），
//      落成的是这一个整体 blob、权限 0600、导入结束立刻 unlink，包里的路径依然不决定任何落盘路径；
//   2) 落盘的文件名一律由本程序生成（N.in / N.ans / validator.src/<角色>/<单元>/<白名单 basename>）；
//   3) 不在 web 端起进程编译作者提供的 C++（绝大多数主机的 PHP 禁用了 exec/shell_exec，而且 web 用户
//      也不该去编译作者代码）；导入器把数据、答案、校验器源码一次摆好，编译交给判题机上原本就用来
//      编 spj.cc/tpj.cc 的 makeout.sh，导入页把它打印成一条可直接粘贴的命令。
//      数据齐全（需要校验器的，校验器二进制也已在位）的题导入完自动置 defunct='N'。

$DOM_YAML_MAX = 65536;        // problem.yaml
$DOM_TEXT_MAX = 262144;       // 题面 / 校验器源码 / 参考程序
$DOM_DATA_MAX = 67108864;     // 单组测试数据 64M
$DOM_NESTED_MAX = 268435456;  // 嵌套的 data/secret/*.zip 解压后 256M
$DOM_DESC_MAX = 60000;        // problem.description 是 TEXT，留点余量

function dom_notice($msg) {
  echo "<br><span style='color:#a00'>" . $msg . "</span><br>";
}

// 作者提供的文件名只允许这种形状：纯 ASCII、无路径分隔、无 ..、不以点开头
function dom_safe_basename($name) {
  return strlen($name) <= 128 && preg_match('/^[A-Za-z0-9][A-Za-z0-9._\-]*$/', $name) && strpos($name, '..') === false;
}

function dom_zip_index($zip) {
  $map = array();
  for ($index = 0; $index < $zip->numFiles; $index++) {
    $raw = $zip->getNameIndex($index);
    if ($raw === false) continue;
    $name = str_replace('\\', '/', $raw);
    if (strpos($name, "./") === 0) $name = substr($name, 2);
    if ($name === '' || $name[0] === '/' || substr($name, -1) === '/') continue; // 绝对路径与目录项
    $bad = false;
    foreach (explode('/', $name) as $seg) {
      if ($seg === '..') { $bad = true; break; }
    }
    if ($bad) continue;
    $stat = $zip->statIndex($index);
    if ($stat === false) continue;
    $map[$name] = array('i' => $index, 'size' => (int)$stat['size']);
  }
  return $map;
}

// 返回条目内容；null 表示包里没有这个文件，false 表示有但读不出或超过大小上限
function dom_read($arch, $name, $maxsize) {
  $map = $arch['map'];
  if (!isset($map[$name])) return null;
  if ($map[$name]['size'] > $maxsize) {
    dom_notice(htmlentities("[跳过] $name 解压后 " . $map[$name]['size'] . " 字节，超过 " . $maxsize . " 上限"));
    return false;
  }
  $content = $arch['zip']->getFromIndex($map[$name]['i']);
  if ($content === false) {
    dom_notice(htmlentities("[读取失败] $name"));
    return false;
  }
  return $content;
}

// 打开嵌套的 data/secret/xxx.zip。$arch 是外层包，$name 是外层包里的条目名。
// 失败返回 null；成功返回 array('zip'=>..., 'map'=>..., 'tmp'=>临时文件路径)
function dom_open_nested($arch, $name) {
  global $DOM_NESTED_MAX;
  if (!isset($arch['map'][$name]) || $arch['map'][$name]['size'] > $DOM_NESTED_MAX) {
    if (isset($arch['map'][$name]))
      dom_notice(htmlentities("[跳过] $name 解压后 " . $arch['map'][$name]['size'] . " 字节，超过嵌套包上限"));
    return null;
  }
  $blob = $arch['zip']->getFromIndex($arch['map'][$name]['i']);
  if ($blob === false) {
    dom_notice(htmlentities("[读取失败] $name"));
    return null;
  }
  $tmp = tempnam(sys_get_temp_dir(), 'domzip');
  if ($tmp === false || file_put_contents($tmp, $blob) === false) {
    dom_notice(htmlentities("无法为嵌套包 $name 建立临时文件。"));
    if ($tmp !== false) @unlink($tmp);
    return null;
  }
  $nested = new ZipArchive();
  if ($nested->open($tmp) !== true) {
    dom_notice(htmlentities("[打开失败] 嵌套包 $name 不是一个合法的 zip。"));
    @unlink($tmp);
    return null;
  }
  return array('zip' => $nested, 'map' => dom_zip_index($nested), 'tmp' => $tmp);
}

function dom_close_nested($nested) {
  if (is_array($nested)) {
    $nested['zip']->close();
    if (isset($nested['tmp'])) @unlink($nested['tmp']);
  }
}

function dom_yaml($text) {
  if ($text === null || $text === false) return null;
  if (function_exists('yaml_parse')) {
    $parsed = @yaml_parse($text);
    if (is_array($parsed)) return $parsed;
  }
  return null;
}

function dom_yget($y, $path) {
  if (!is_array($y)) return null;
  $cur = $y;
  foreach (explode('.', $path) as $key) {
    if (!is_array($cur) || !isset($cur[$key])) return null;
    $cur = $cur[$key];
  }
  return is_scalar($cur) ? $cur : null;
}

// 没装 php-yaml 时的文本兜底。DOMjudge 的 limits 常写成行内 flow 风格
// （limits: { validation_passes: 3, time_limit: 3.0 }），所以这里不能锚定行首。
function dom_rget($text, $regex) {
  if ($text === null || $text === false) return null;
  if (preg_match($regex, $text, $m)) return trim($m[1], " \t\"'");
  return null;
}

function dom_get($y, $text, $path, $regex) {
  $v = dom_yget($y, $path);
  if ($v === null) $v = dom_rget($text, $regex);
  return $v;
}

function dom_title($y, $text, $fallback) {
  if (is_array($y) && isset($y['name'])) {
    if (is_scalar($y['name'])) return (string)$y['name'];
    if (is_array($y['name'])) {
      foreach (array('en', 'zh') as $lang) {
        if (isset($y['name'][$lang]) && is_scalar($y['name'][$lang])) return (string)$y['name'][$lang];
      }
      foreach ($y['name'] as $v) {
        if (is_scalar($v)) return (string)$v;
      }
    }
  }
  $t = dom_rget($text, '/^\s*(?:en|zh)\s*:\s*(.+)$/m');
  if ($t === null) $t = dom_rget($text, '/^name\s*:\s*(.+)$/m');
  if ($t !== null && $t !== '') return $t;
  return $fallback;
}

// 与 problem_import_hydro.php 同一手法：数学式子里的 < 会被浏览器当标签，换成 \lt
function dom_replaceLT($string) {
  $pattern = '/(\$.*?)(<)(.*?\$)/';
  $replacement = '$1 \\lt $3';
  $ret = preg_replace($pattern, $replacement, $string);
  if (strlen($string) == strlen($ret)) return $ret;
  return preg_replace($pattern, $replacement, $ret);
}

// LaTeX -> HTML：属于尽力而为的转换，导入后请人工核对题面。
// 先整体转义再做替换，插进去的标签才是真的标签，正文里的 < 才不会变成标签。
function dom_tex_to_html($tex) {
  $tex = str_replace("\r\n", "\n", $tex);
  // LaTeX 的引号写法在转义前替换，转义后就找不到 `` 和 '' 了
  $tex = str_replace(array('``', "''"), array('"', '"'), $tex);
  $lines = preg_split("/\n/", $tex);
  foreach ($lines as $k => $line) {
    $lines[$k] = preg_replace('/(?<!\\\\)%.*$/', '', $line); // % 注释（\% 不是）
  }
  $tex = htmlentities(implode("\n", $lines), ENT_QUOTES, 'UTF-8');

  // \newcommand{\x}{body}（不带参数的）先记下、删掉定义行，最后再代入
  $macros = array();
  if (preg_match_all('/\\\\newcommand\s*\{\s*\\\\([A-Za-z]+)\s*\}\s*\{([^{}]+)\}/', $tex, $mm, PREG_SET_ORDER)) {
    foreach ($mm as $m) $macros[$m[1]] = trim($m[2]);
  }
  $tex = preg_replace('/\\\\newcommand\s*\{\s*\\\\[A-Za-z]+\s*\}\s*\{[^{}]*\}[^\n]*\n?/', '', $tex);

  // 原样保留的块：样例输入输出、交互记录
  foreach (array('SampleInput', 'SampleOutput', 'SampleInteraction', 'Interaction', 'InteractionOutput') as $env) {
    $tex = preg_replace_callback(
        '/\\\\begin\{' . $env . '\}(.*?)\\\\end\{' . $env . '\}/s',
        function ($m) use ($env) {
          $head = ($env === 'SampleInput' || $env === 'SampleOutput') ? '' : '<h4>' . $env . '</h4>';
          return "\n" . $head . '<pre>' . trim($m[1]) . '</pre>' . "\n";
        },
        $tex
    );
  }

  foreach (array('Input' => 'Input', 'Output' => 'Output', 'Description' => 'Description',
                 'SampleExplanation' => 'Sample explanation', 'ScoringSubtask' => 'Scoring subtask') as $env => $label) {
    $tex = preg_replace('/\\\\begin\{' . $env . '\}/', "\n<h3>" . $label . "</h3>\n", $tex);
    $tex = preg_replace('/\\\\end\{' . $env . '\}/', "\n", $tex);
  }
  $tex = preg_replace('/\\\\(begin|end)\{(itemize|enumerate|description|center|flushright|subtask)\}/', '', $tex);
  $tex = str_replace('\item', '&bull; ', $tex);
  $tex = preg_replace('/\\\\(pagebreak|clearpage|newpage)\b/', "\n<hr>\n", $tex);
  $tex = preg_replace('/\\\\problemname\s*\{[^{}]*\}/s', '', $tex);
  $tex = preg_replace('/\\\\label\s*\{[^{}]*\}/', '', $tex);
  $tex = preg_replace('/\\\\(usepackage|documentclass|input|include|graphicspath|bibliographystyle|renewcommand|setlength|definecolor|colorlet)\b[^\n]*\n?/', '', $tex);
  $tex = preg_replace('/\\\\(?:sub)*section\*?\s*\{([^{}]*)\}/', "\n<h4>$1</h4>\n", $tex);

  $tex = preg_replace_callback('/\\\\href\s*\{([^{}]*)\}\s*\{([^{}]*)\}/s',
      function ($m) {
        // 到这里 $m[1] 已经是转义过的，直接拼进 href 属性；只放行 http/https
        $url = trim($m[1]);
        return preg_match('/^https?:\/\/[^\s<>"\']+$/i', $url)
            ? '<a href="' . $url . '" target="_blank">' . $m[2] . '</a>'
            : $m[2] . ' (' . $m[1] . ')';
      }, $tex);
  $tex = preg_replace_callback('/\\\\footnote\s*\{(.*?)\}/s',
      function ($m) { return ' <small>(note: ' . trim($m[1]) . ')</small>'; }, $tex);

  $inline = array(
      '/\\\\texttt\s*\{([^{}]*)\}/s'          => '<code>$1</code>',
      '/\\\\verb\|([^|]*)\|/'                 => '<code>$1</code>',
      '/\\\\(?:emph|textit|textsl)\s*\{([^{}]*)\}/s' => '<em>$1</em>',
      '/\\\\(?:textbf|bfseries|strong)\s*\{([^{}]*)\}/s' => '<b>$1</b>',
      '/\\\\(?:text|textnormal|textrm|textsf)\s*\{([^{}]*)\}/s' => '$1',
      '/\\\\(?:hfill|quad|qquad|,|;)/'        => ' ',
  );
  foreach ($inline as $pattern => $replacement) $tex = preg_replace($pattern, $replacement, $tex);

  // 宏代入用 callback，正文里的 $ 和 \ 不会被当成反向引用
  for ($round = 0; $round < 3 && count($macros) > 0; $round++) {
    $hit = false;
    foreach ($macros as $name => $body) {
      $before = $tex;
      $tex = preg_replace_callback('/\\\\' . $name . '\b/', function ($m) use ($body) { return $body; }, $tex);
      if ($tex !== $before) $hit = true;
    }
    if (!$hit) break;
  }

  // 插图：图片文件不进题库，保留出处文字比留个破图好；放到最后做，题注里的 \href 已经变成链接
  $tex = preg_replace_callback('/\\\\illustration\s*\{[^{}]*\}\s*\{([^{}]*)\}\s*\{((?:[^{}]|\{[^{}]*\})*)\}/',
      function ($m) {
        return '<p><em>[figure: ' . trim($m[1]) . ' &mdash; ' . trim(preg_replace('/\s+/', ' ', $m[2])) . ']</em></p>';
      }, $tex);
  $tex = preg_replace_callback('/\\\\includegraphics\s*(?:\[[^\]]*\])?\s*\{([^{}]*)\}/',
      function ($m) {
        return '<p><em>[figure: ' . trim(basename($m[1])) . ']</em></p>';
      }, $tex);

  // <pre> 里的换行不能再插 <br>，先藏起来
  $blocks = array();
  $tex = preg_replace_callback('/<pre>.*?<\/pre>/s', function ($m) use (&$blocks) {
    $blocks[] = $m[0];
    return "\x01" . (count($blocks) - 1) . "\x01";
  }, $tex);

  $tex = preg_replace('/\n{2,}/', "</p><p>\n", $tex);
  $tex = str_replace("\n", "<br>\n", $tex);
  foreach ($blocks as $i => $block) $tex = str_replace("\x01" . $i . "\x01", $block, $tex);
  $tex = preg_replace('/<\/p><p>(\s*(?:<pre>|<h3>|<h4>|<hr>|<br>))/', '$1', $tex);
  $tex = preg_replace('/(<\/pre>|<\/h3>|<\/h4>|<hr>)\s*<\/p><p>/', '$1', $tex);
  $tex = preg_replace('/(<\/h[34]>|<hr>|<\/pre>|<\/p>)(?:\s*<br>)+/', '$1', $tex);
  $tex = preg_replace('/(?:<br>\s*)+(<h[34]>|<hr>|<pre>|<p>)/', '$1', $tex);
  $tex = preg_replace('/^(?:\s|<br>|<p>)+|(?:\s|<br>)+$/', '', $tex);

  if (strpos($tex, '\\begin{') !== false || strpos($tex, '\\end{') !== false) {
    dom_notice("题面里还有没转换掉的 LaTeX 环境，请在后台核对 description。");
  }
  return '<p>' . $tex . '</p>';
}

// 数据目录属主/权限不对时 file_put_contents 只会发 warning，这里统一报出来。
// $mode 非 0 时顺手改权限：裁判答案要收权限，见 dom_write 调用处的注释。
function dom_write($path, $content, $mode = 0) {
  if (file_put_contents($path, str_replace("\r\n", "\n", $content)) === false) {
    dom_notice("写入失败：" . htmlentities($path, ENT_QUOTES, 'UTF-8') . "，请检查 " . "chmod 775 -R \$OJ_DATA 与属组。");
    return false;
  }
  if ($mode !== 0) @chmod($path, $mode); // .in 不动，judge_client 的 fix_mod() 每轮判题前统一收成 0710 www:judge
  return true;
}

// 把 $arch 里 data/<组>/<名字>.in 形式的条目摊平成用例列表。
// 组名排序：sample 在最前，其余按名字自然序；名字相同的用例 .in/.ans/.interaction 靠 base 配对。
function dom_collect_group($arch, $group, $prefix) {
  $cases = array();
  $dir = ($prefix === '' ? '' : $prefix . '/') . 'data/' . $group . '/';
  foreach ($arch['map'] as $name => $info) {
    if (strpos($name, $dir) !== 0) continue;
    if (substr($name, -3) !== '.in') continue;
    $rel = substr($name, strlen($dir));
    if (strpos($rel, '/') !== false) continue; // 组目录里再套一层的不认
    $base = basename($rel, '.in');
    if ($base === '') continue;
    $cases[] = array('name' => $name, 'base' => $base, 'group' => $group);
  }
  usort($cases, function ($a, $b) { return strnatcmp($a['base'], $b['base']); });
  return $cases;
}

// 嵌套包（data/secret/secret.zip）里的用例：DOMjudge 直接在包根放 01.in / 01.ans
function dom_collect_nested($arch) {
  $cases = array();
  foreach ($arch['map'] as $name => $info) {
    if (substr($name, -3) !== '.in') continue;
    if (dirname($name) !== '.') continue;
    $base = basename($name, '.in');
    if ($base === '') continue;
    $cases[] = array('name' => $name, 'base' => $base, 'group' => 'secret');
  }
  usort($cases, function ($a, $b) { return strnatcmp($a['base'], $b['base']); });
  return $cases;
}

function dom_sibling($arch, $case, $ext) {
  $dir = dirname($case['name']);
  if ($dir === '' || $dir === '.') $name = $case['base'] . $ext;
  else $name = $dir . '/' . $case['base'] . $ext;
  return isset($arch['map'][$name]) ? $name : null;
}

function import_domjudge_problem($arch, $root, $OJ_DATA) {
  global $DOM_YAML_MAX, $DOM_TEXT_MAX, $DOM_DATA_MAX, $DOM_DESC_MAX, $OJ_NAME, $DB_NAME;
  if (!isset($DB_NAME) || $DB_NAME === '') $DB_NAME = 'jol';

  $root_prefix = ($root === '' ? '' : $root . '/');
  $yaml_text = dom_read($arch, $root_prefix . 'problem.yaml', $DOM_YAML_MAX);
  $y = dom_yaml($yaml_text);
  if ($y === null && !function_exists('yaml_parse')) {
    dom_notice("未安装 php-yaml 扩展，problem.yaml 按文本解析（只认 name/type/limits/source/license/author）。");
  }

  $title = dom_title($y, $yaml_text, $root === '' ? 'Problem' : basename($root));

  // 判题方式 -> hustoj 的 spj
  $type = dom_get($y, $yaml_text, 'type', '/^\s*type\s*:\s*(.+)$/m');
  $type = strtolower(trim((string)$type));
  if ($type === '') $type = 'pass-fail'; // problem.yaml 省略 type 时 DOMjudge 按 pass-fail 处理
  $multi_pass = (strpos($type, 'multi-pass') !== false);
  $interactive = (strpos($type, 'interactive') !== false);
  $has_ov = false;
  foreach (array('output_validator/', 'output_validators/') as $role) {
    foreach ($arch['map'] as $name => $info) {
      if (strpos($name, $root_prefix . $role) === 0) { $has_ov = true; break; }
    }
  }

  $spj = 0;
  $spj_note = '';
  if ($multi_pass) {
    $spj = 4;
  } else if ($has_ov) {
    // DOMjudge 的 output validator 约定（in/ans/feedbackdir + 42/43）和 spj=4 的单轮跑法一致，
    // 比套进 spj=1 的老 checker 接口（spj in out user.out，返回 0/-1）更贴合。
    $spj = 4;
    $spj_note = "type=$type 且带 output validator，按单轮通信题（spj=4）判：校验器不写 nextpass.in 就只跑一轮。";
  } else if ($interactive) {
    $spj = 3;
    $spj_note = "type=$type 走的是交互题（spj=3）路径，需要 data/<pid>/spj.cc；题目包给的一般是 output validator，建议按 multi-pass 判。";
  } else if ($type !== 'pass-fail') {
    dom_notice(htmlentities("[$title] 不认识的 type: " . $type . "，按普通题（spj=0）处理。"));
  }

  $source = dom_get($y, $yaml_text, 'source.name', '/^\s*(?:name|source)\s*:\s*(.+)$/m');
  $source = trim('DOMjudge ' . (string)$source);
  $license = dom_get($y, $yaml_text, 'license', '/^\s*license\s*:\s*(\w+)/m');
  $author = trim((string)dom_get($y, $yaml_text, 'author.name', '/^\s*author\s*:\s*(.+)$/m'));
  $credits = trim((string)dom_get($y, $yaml_text, 'credits', '/^\s*credits\s*:\s*(.+)$/m'));
  $hint = trim(($author !== '' ? 'Author: ' . $author . ' ' : '')
             . ($credits !== '' ? 'Credits: ' . $credits . ' ' : '')
             . ($license !== null ? 'License: ' . $license : ''));

  // 时限：DOMjudge 写在 problem.yaml 的 limits.time_limit（秒，可以是小数）
  $tl = dom_get($y, $yaml_text, 'limits.time_limit', '/\btime_limit\s*:\s*([0-9.]+)/');
  $time = (is_numeric($tl) && floatval($tl) > 0) ? round(floatval($tl), 3) : 1.0;
  $mem = dom_get($y, $yaml_text, 'limits.memory_limit', '/\bmemory_limit\s*:\s*([0-9]+)/');
  $memory = (is_numeric($mem) && floatval($mem) > 0) ? intval(floatval($mem)) : 1024;
  $passes = dom_get($y, $yaml_text, 'limits.validation_passes', '/\bvalidation_passes\s*:\s*([0-9]+)/');

  // 题面：DOMjudge 放在 statement/problem.<lang>.{tex,md}
  $statement = null;
  $is_md = false;
  $best = array('md' => '', 'tex' => '');
  foreach ($arch['map'] as $name => $info) {
    if (strpos($name, $root_prefix . 'statement/') !== 0 && strpos($name, $root_prefix . 'problem_statement/') !== 0) continue;
    foreach (array('md', 'tex') as $ext) {
      if (substr($name, -(strlen($ext) + 1)) !== '.' . $ext) continue;
      $cur = $best[$ext];
      $mine = stripos(basename($name), '.en.') !== false;
      $theirs = $cur !== '' && stripos(basename($cur), '.en.') !== false;
      if ($cur === '' || ($mine && !$theirs)) $best[$ext] = $name;
    }
  }
  if ($best['md'] !== '') { $statement = $best['md']; $is_md = true; }
  else if ($best['tex'] !== '') $statement = $best['tex'];

  $description = '';
  if ($statement !== null) {
    $text = dom_read($arch, $statement, $DOM_TEXT_MAX);
    if (is_string($text)) {
      if ($is_md) {
        $description = '<span class="md">' . dom_replaceLT($text) . '</span>';
      } else {
        $description = dom_tex_to_html($text)
            . "\n<p><small>（题面由 LaTeX 自动转换，请人工核对。原文：" . htmlentities($statement, ENT_QUOTES, 'UTF-8') . "）</small></p>";
      }
    }
  } else {
    dom_notice(htmlentities("[$title] 没找到 statement/ 下的题面，description 为空，请手工补上（题目包通常只给 PDF）。"));
  }
  if (strlen($description) > $DOM_DESC_MAX) {
    $description = substr($description, 0, $DOM_DESC_MAX) . "<p>……（题面过长已被截断，请在后台编辑补全）</p>";
    dom_notice(htmlentities("[$title] 题面超过 " . $DOM_DESC_MAX . " 字节，已截断。"));
  }

  // 用例：sample 摊在外层包里，正式数据可能直接放在 data/secret/ 下，也可能整体塞进 data/secret/*.zip
  $nested_list = array();
  foreach ($arch['map'] as $name => $info) {
    if (strpos($name, $root_prefix . 'data/secret/') !== 0) continue;
    if (substr($name, -4) !== '.zip') continue;
    $nested = dom_open_nested($arch, $name);
    if ($nested !== null) $nested_list[] = array('arch' => $nested, 'name' => $name);
  }

  $groups = array();
  foreach ($arch['map'] as $name => $info) {
    if (strpos($name, $root_prefix . 'data/') !== 0) continue;
    $rel = substr($name, strlen($root_prefix) + 5);
    if (strpos($rel, '/') === false) continue;
    $group = substr($rel, 0, strpos($rel, '/'));
    if ($group === 'secret') continue; // 正式数据单独从嵌套包里取，没有嵌套包时下面兜底
    $groups[$group] = true;
  }
  $cases = array();
  if (isset($groups['sample'])) {
    foreach (dom_collect_group($arch, 'sample', $root) as $c) $cases[] = array('arch' => $arch, 'case' => $c, 'rank' => 0);
  }
  $secret_plain = dom_collect_group($arch, 'secret', $root);
  if (count($secret_plain) > 0) {
    foreach ($secret_plain as $c) $cases[] = array('arch' => $arch, 'case' => $c, 'rank' => 1);
  }
  foreach ($nested_list as $n) {
    foreach (dom_collect_nested($n['arch']) as $c) $cases[] = array('arch' => $n['arch'], 'case' => $c, 'rank' => 1);
  }
  // sample 在前，其余按“组名 + 用例名”自然序（嵌套包里的用例没有组目录，用 base 排）
  usort($cases, function ($a, $b) {
    if ($a['rank'] !== $b['rank']) return $a['rank'] < $b['rank'] ? -1 : 1;
    $ka = ($a['case']['group'] === 'sample' ? '' : 'zz/') . $a['case']['base'];
    $kb = ($b['case']['group'] === 'sample' ? '' : 'zz/') . $b['case']['base'];
    return strnatcmp($ka, $kb);
  });
  if (count($cases) === 0) {
    dom_notice(htmlentities("[$title] {$root_prefix}data/ 下没有 .in 文件（也没能读出 data/secret/*.zip），只建了题目。"));
  }

  $pid = addproblem($title, $time, $memory, $description, '', '', '', '', $hint, $source, $spj, $OJ_DATA);
  if (!file_exists($OJ_DATA . "/" . $pid)) @mkdir($OJ_DATA . "/" . $pid, 0755, true);

  $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
  pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "p$pid");
  $_SESSION[$OJ_NAME . '_' . "p$pid"] = true;

  $written = 0;
  $missing_ans = 0;
  $first_sample = null;
  foreach ($cases as $i => $item) {
    $n = $i + 1;
    $A = $item['arch'];
    $case = $item['case'];
    $in = dom_read($A, $case['name'], $DOM_DATA_MAX);
    if (!is_string($in)) continue;
    if (!dom_write($OJ_DATA . "/$pid/$n.in", $in)) continue;

    $ans_name = dom_sibling($A, $case, '.ans');
    $out_name = dom_sibling($A, $case, '.out');
    $src = ($ans_name !== null) ? $ans_name : $out_name;
    $content = ($src === null) ? null : dom_read($A, $src, $DOM_DATA_MAX);
    if ($spj == 4 || $spj == 3) {
      // communication_case 拿同名的 .ans 当 jury.ans；DOMjudge 的 multi-pass 里 .ans 是每轮的
      // 裁判输入（这题是 adversary 的名字），缺了校验器会 judgeAssert 失败，补个空的至少能看清原因。
      // 权限收成 0600：judged.cc 挂数据目录时没加 :ro（%s:/home/judge/data），容器里的提交程序
      // 以 judge 身份跑，能读走同目录下任何它可读的文件，68 组裁判输入会一次性泄露。
      // 判题不受影响——communication_case 里 copy_data_file(ans_src, ans_file) 和随后的 chown
      // 都发生在父进程（root）里；run<N>/ 里的那份副本归 www 用户，只有同样以 www 身份跑的
      // 校验器读得到，提交程序（judge 用户）读不到。
      if (!is_string($content)) $missing_ans++;
      dom_write($OJ_DATA . "/$pid/$n.ans", is_string($content) ? $content : '', 0600);
    } else {
      // pass-fail：.ans 就是标准输出，判题机找的是 .out
      if (!is_string($content)) $missing_ans++;
      dom_write($OJ_DATA . "/$pid/$n.out", is_string($content) ? $content : '');
    }
    $written++;
    if ($first_sample === null && $case['group'] === 'sample') $first_sample = array('arch' => $A, 'case' => $case);
  }
  if ($missing_ans > 0) {
    dom_notice(htmlentities("[$title] 有 $missing_ans 组没有 .ans/.out，已补空文件。"
      . ($spj == 4 ? "spj=4 看校验器是否要读裁判输入：DOMjudge 的 validate.h 里读 judgeanswer 会 judgeAssert 失败。"
                   : "普通判题会一律 WA。")));
  }

  // addproblem() 建出来的题一律 defunct='Y'，不放开就得再去后台点一次。这里能自动放开的就自动放开：
  // 数据写出来了、且 spj=4 需要的 data/<pid>/validator 已经存在时才公开，
  // 缺校验器可执行文件就保持未公开——否则学生提交拿到的是没有意义的 RE。
  // 编译必须留在判题机上做：导入器不在 web 进程里起编译器（绝大多数主机的 PHP 禁用了 exec/shell_exec，
  // 而且 web 用户也不该去编译作者提供的代码），下面把这一步打成一条可以直接粘贴的命令。
  $validator_ready = ($spj != 4) || file_exists($OJ_DATA . "/$pid/validator");
  $answers_ready = ($spj == 4) || $missing_ans == 0; // 校验器题的答案由 .ans 供，缺不缺由校验器说了算
  $defunct = 'Y';
  if ($written > 0 && $answers_ready && $validator_ready) {
    // pdo_query() 出错时返回 -1（UPDATE 成功但没有行变化是 0，不会为负）
    $r = pdo_query("UPDATE problem SET defunct='N' WHERE problem_id=?", $pid);
    if (is_numeric($r) && $r >= 0) $defunct = 'N';
    else dom_notice(htmlentities("[$title] 更新 defunct 失败，请到题目编辑里手工公开。"));
  } else if ($written == 0) {
    dom_notice(htmlentities("[$title] 没写出任何数据，题目保持未公开。"));
  } else if (!$validator_ready) {
    dom_notice(htmlentities("[$title] 缺 $OJ_DATA/$pid/validator 可执行文件，题目先保持未公开；按下面这条命令编好就自动可用了。"));
  } else {
    dom_notice(htmlentities("[$title] 有缺标准答案的数据，题目保持未公开。"));
  }

  // 样例：多轮题的 .ans 是裁判参数（例如 adversary 名字），给选手看的应该是 .interaction
  if ($first_sample !== null) {
    $A = $first_sample['arch'];
    $case = $first_sample['case'];
    $in = dom_read($A, $case['name'], $DOM_DATA_MAX);
    $sample_input = is_string($in) ? $in : '';
    $sample_output = '';
    $exts = ($spj == 4) ? array('.interaction', '.ans', '.out') : array('.ans', '.out', '.interaction');
    foreach ($exts as $ext) {
      $sib = dom_sibling($A, $case, $ext);
      if ($sib === null) continue;
      $content = dom_read($A, $sib, $DOM_DATA_MAX);
      if (is_string($content)) { $sample_output = $content; break; }
    }
    pdo_query("UPDATE problem SET sample_input=?, sample_output=? WHERE problem_id=?", $sample_input, $sample_output, $pid);
  }

  // 校验器：只搬源码；预编译好的可执行文件一律不装
  $build = array();
  $copied = 0;
  foreach (array('output_validator' => 'output_validator', 'output_validators' => 'output_validators',
                 'input_validators' => 'input_validators', 'answer_validators' => 'answer_validators') as $role => $dir) {
    $role_prefix = $root_prefix . $dir . '/';
    foreach ($arch['map'] as $name => $info) {
      if (strpos($name, $role_prefix) !== 0) continue;
      $rel = substr($name, strlen($role_prefix));
      $base = basename($rel);
      if (!dom_safe_basename($base)) continue;
      if (!preg_match('/\.(cpp|cc|h|hpp|ctd)$/i', $base)) continue;
      $content = dom_read($arch, $name, $DOM_TEXT_MAX);
      if (!is_string($content)) continue;
      // DOMjudge 的复数目录（Kattis 也用它）是 <角色>/<单元>/<单元>.cpp，validate.h 和主源码同目录；
      // 单数的 output_validator/ 里文件直接躺在角色根下，就别再凭空造一层目录，
      // 否则主源码和 validate.h 会被分到两个目录里，编译命令照抄还编不过。
      $unit = dirname($rel);
      if ($unit === '.' || $unit === '') $dst_dir = $OJ_DATA . "/$pid/validator.src/$role";
      else {
        if (!dom_safe_basename($unit)) continue;
        $dst_dir = $OJ_DATA . "/$pid/validator.src/$role/$unit";
      }
      if (!file_exists($dst_dir) && !@mkdir($dst_dir, 0755, true)) continue;
      if (!dom_write($dst_dir . "/" . $base, $content)) continue;
      $copied++;
      if (strpos($role, 'output_validator') === 0 && preg_match('/\.(cpp|cc)$/i', $base))
        $build[] = array($dst_dir, $base);
    }
  }

  // 参考程序：DOMjudge 包里的 submissions/ 对导入后自测很有用，只当源码存着，不参与判题
  $sol_copied = 0;
  $sol_prefix = $root_prefix . 'submissions/';
  foreach ($arch['map'] as $name => $info) {
    if (strpos($name, $sol_prefix) !== 0) continue;
    $rel = substr($name, strlen($sol_prefix));
    $cat = dirname($rel);
    $base = basename($rel);
    if (!in_array($cat, array('accepted', 'wrong_answer', 'time_limit_exceeded', 'timelimit_exceeded', 'rejected'), true)) continue;
    if (!dom_safe_basename($cat) || !dom_safe_basename($base)) continue;
    if (!preg_match('/\.(cpp|cc|py|java|kt|c)$/i', $base)) continue;
    $content = dom_read($arch, $name, $DOM_TEXT_MAX);
    if (!is_string($content)) continue;
    $dst_dir = $OJ_DATA . "/$pid/reference.src/$cat";
    if (!file_exists($dst_dir) && !@mkdir($dst_dir, 0755, true)) continue;
    if (dom_write($dst_dir . "/" . $base, $content)) $sol_copied++;
  }

  echo "<hr><b>PID <a href='../problem.php?id=$pid'>" . $pid . "</a></b> " . htmlentities($title, ENT_QUOTES, "UTF-8")
     . " &mdash; spj=$spj" . ($spj == 4 ? "（" . ($multi_pass ? "通信题，逐轮判题" : "output validator，单轮") . "）" : "")
     . "，$written 组数据（另有 " . count($nested_list) . " 个嵌套数据包），时限 {$time}s，内存 {$memory}M，"
     . ($defunct === 'N' ? "<span style='color:#080'>已公开</span>" : "<span style='color:#a00'>未公开（defunct=Y）</span>") . "<br>";
  if ($spj_note !== '') echo "<span style='color:#a06'>" . htmlentities($spj_note, ENT_QUOTES, 'UTF-8') . "</span><br>";

  $todo = array();
  // defunct 的放开命令有两种给法：跟编译校验器捆成一条（照着粘一次就完事），或者单独提示。
  $bundled_publish = false;
  if ($spj == 4) {
    if (count($build) > 0) {
      // 校验器的编译已经并进 makeout.sh——它原本就负责编 spj.cc/tpj.cc/upj.cc。
      // 在本题数据目录里跑一次，validator.src/ 下的源码就编成同目录的 ./validator。
      // 权限不用手工理：judge_client 每次用 validator 前都把属主收成 www 用户、权限收成 0710，
      // 谁编的都一样。makeout.sh 自己拒绝 root，用平时准备数据的那个用户跑。
      $todo[] = "在判题机上编校验器：cd " . $OJ_DATA . "/$pid && bash /usr/bin/makeout.sh Main"
              . ($defunct === 'Y' ? " && mysql $DB_NAME -e \"UPDATE problem SET defunct='N' WHERE problem_id=$pid\"" : "");
      $bundled_publish = ($defunct === 'Y');
    } else {
      $todo[] = "包里没有 output_validator(s)/*.cpp；判题机找不到 $OJ_DATA/$pid/validator 会判 RE";
    }
    if ($passes !== null) $todo[] = "problem.yaml 要求 validation_passes={$passes}（judge_client 的轮数上限是 COMM_MAX_PASS=100，不用另外设置）";
    $todo[] = "时限 {$time}s 是每轮计的，" . ($multi_pass ? "多轮累加后写进 result.time" : "单轮就是总时限");
  }
  if ($defunct === 'Y' && $written > 0 && !$bundled_publish) $todo[] = "放开题目：后台题目编辑改成公开，或 mysql $DB_NAME -e \"UPDATE problem SET defunct='N' WHERE problem_id=$pid\"";
  if ($copied > 0) $todo[] = "校验器源码在 $OJ_DATA/$pid/validator.src/（$copied 个文件）";
  if ($sol_copied > 0) $todo[] = "参考程序在 $OJ_DATA/$pid/reference.src/（$sol_copied 个文件），自测时编译一份提交即可";
  if ($spj == 3) $todo[] = "spj=3 是 FIFO 交互题路径，需要 $OJ_DATA/$pid/spj.cc；本题包给的是 output validator";
  if (count($nested_list) > 0) $todo[] = "嵌套数据包：" . htmlentities(implode(', ', array_map(function ($n) { return $n['name']; }, $nested_list)), ENT_QUOTES, 'UTF-8');

  if (count($todo) > 0) {
    echo "<pre style='background:#f8f8f8;padding:8px;border:1px solid #ddd'>还需要在判题机上手工完成（导入器不在 web 进程里跑编译器，绝大多数主机的 PHP 禁用了 exec）：<br>";
    foreach ($todo as $t) echo "&nbsp;&nbsp;" . $t . "<br>";
    echo "</pre>";
  }
  foreach ($nested_list as $n) dom_close_nested($n['arch']);
  return $pid;
}

function dom_extension($file) {
  return strtolower(pathinfo(basename($file), PATHINFO_EXTENSION));
}

if ($_FILES["fps"]["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: " . $_FILES["fps"]["error"] . " File size is too big, change in PHP.ini<br />";
} else if (dom_extension($_FILES["fps"]["name"]) != "zip") {
  echo "&nbsp;&nbsp;- 只接受 DOMjudge 题目包导出的 .zip<br />";
} else if (!class_exists('ZipArchive')) {
  dom_notice("当前 PHP 环境没有 zip 扩展，无法读取题目包。宝塔：软件商店 -> PHP 设置 -> 安装扩展 -> zip。");
} else {
  @set_time_limit(600);
  $zip = new ZipArchive();
  if ($zip->open($_FILES["fps"]["tmp_name"]) !== true) {
    die("Could not open ZIP archive.");
  }
  $outer = array('zip' => $zip, 'map' => dom_zip_index($zip), 'tmp' => null);

  // 哪个目录里有 problem.yaml，那个目录就算一道题：整场比赛打包成的 zip 也能一次导完
  $roots = array();
  foreach ($outer['map'] as $name => $info) {
    if (basename($name) === 'problem.yaml') {
      $dir = dirname($name);
      $roots[$dir === '.' ? '' : $dir] = true;
    }
  }
  $roots = array_keys($roots);
  sort($roots, SORT_STRING);

  if (count($roots) === 0) {
    dom_notice("这个 zip 里找不到 problem.yaml，不是 DOMjudge 题目包。");
  } else if (!is_writable($OJ_DATA)) {
    // 数据目录写不进去就先停手：否则建出一堆有题面没数据的空题
    dom_notice("数据目录 <b>" . htmlentities($OJ_DATA, ENT_QUOTES, 'UTF-8') . "</b> 不可写，请执行 "
             . "chmod 775 -R " . htmlentities($OJ_DATA, ENT_QUOTES, 'UTF-8') . " 并把属组改成 www 用户（参见 phpfm 页面的提示）。");
  } else {
    echo "&nbsp;&nbsp;- 找到 " . count($roots) . " 道题："
       . htmlentities(implode(', ', array_map(function ($r) { return $r === '' ? '(包根)' : $r; }, $roots)), ENT_QUOTES, 'UTF-8') . "<br>";
    $pids = array();
    foreach ($roots as $root) $pids[] = import_domjudge_problem($outer, $root, $OJ_DATA);
    echo "<hr>已导入 PID：" . implode(', ', $pids) . "<br>通信题（spj=4）按 data/&lt;pid&gt;/N.in 逐轮运行提交程序，每轮把 user.out 交给 "
       . "$OJ_DATA/&lt;pid&gt;/validator 裁决；校验器写出 nextpass.in 就进入下一轮，没写就算这一组过了。"
       . "<br>DOMjudge 的 validate.h 会把轮次计数写在反馈目录的 .pass、把跨轮状态写在 .state0/.state1，判题机每组数据开始前都会清掉这些点文件。";
  }
  $zip->close();
  unlink($_FILES["fps"]["tmp_name"]);
}
