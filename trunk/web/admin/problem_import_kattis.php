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

// ICPC 题目包（Kattis / problem-package-format）导入器，对应命令行脚本
// kattis_package_to_hustoj_data.py 的网页版：
//   上传 zip -> 建题 -> 数据摊平成 data/<pid>/N.in(+N.ans) -> 校验器源码放进 data/<pid>/validator.src/
// validation: custom multi-pass 的通信题导入后 spj=4，由 judge_client 的 communication_case 逐轮判题。
//
// 安全前提：
//   1) 只按条目从 zip 里读内容，全程不解压到磁盘，所以不存在 zip-slip；
//   2) 落盘的文件名一律由本程序生成（N.in / N.ans / validator.src/<role>/<unit>/<白名单 basename>）；
//   3) 不在 web 端编译作者提供的 C++（那正是刚修掉的提权类问题），编译只在判题机上手工完成。

$KAT_YAML_MAX = 65536;       // problem.yaml
$KAT_TEXT_MAX = 262144;      // 题面 / 校验器源码
$KAT_DATA_MAX = 67108864;    // 单组测试数据 64M
$KAT_DESC_MAX = 60000;       // problem.description 是 TEXT，留点余量

function kat_notice($msg) {
  echo "<br><span style='color:#a00'>" . $msg . "</span><br>";
}

// 作者提供的文件名只允许这种形状：纯 ASCII、无路径分隔、无 ..、不以点开头
function kat_safe_basename($name) {
  return strlen($name) <= 128 && preg_match('/^[A-Za-z0-9][A-Za-z0-9._\-]*$/', $name) && strpos($name, '..') === false;
}

function kat_zip_index($zip) {
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
function kat_read($zip, $map, $name, $maxsize) {
  if (!isset($map[$name])) return null;
  if ($map[$name]['size'] > $maxsize) {
    kat_notice(htmlentities("[跳过] $name 解压后 " . $map[$name]['size'] . " 字节，超过 " . $maxsize . " 上限"));
    return false;
  }
  $content = $zip->getFromIndex($map[$name]['i']);
  if ($content === false) {
    kat_notice(htmlentities("[读取失败] $name"));
    return false;
  }
  return $content;
}

function kat_yaml($text) {
  if ($text === null || $text === false) return null;
  if (function_exists('yaml_parse')) {
    $parsed = @yaml_parse($text);
    if (is_array($parsed)) return $parsed;
  }
  return null;
}

function kat_yget($y, $path) {
  if (!is_array($y)) return null;
  $cur = $y;
  foreach (explode('.', $path) as $key) {
    if (!is_array($cur) || !isset($cur[$key])) return null;
    $cur = $cur[$key];
  }
  return is_scalar($cur) ? $cur : null;
}

// 没装 php-yaml 时从原文里抠出用到的那几个键，只认题目包实际用到的平面写法
function kat_rget($text, $regex) {
  if ($text === null || $text === false) return null;
  if (preg_match($regex, $text, $m)) return trim($m[1], " \t\"'");
  return null;
}

function kat_title($y, $text, $fallback) {
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
  $t = kat_rget($text, '/^\s*(?:en|zh)\s*:\s*(.+)$/m');
  if ($t === null) $t = kat_rget($text, '/^name\s*:\s*(.+)$/m');
  if ($t !== null && $t !== '') return $t;
  return $fallback;
}

// 与 problem_import_hydro.php 同一手法：数学式子里的 < 会被浏览器当标签，换成 \lt
function kat_replaceLT($string) {
  $pattern = '/(\$.*?)(<)(.*?\$)/';
  $replacement = '$1 \\lt $3';
  $ret = preg_replace($pattern, $replacement, $string);
  if (strlen($string) == strlen($ret)) return $ret;
  return preg_replace($pattern, $replacement, $ret);
}

// LaTeX -> HTML：覆盖题目包格式用到的宏，属于尽力而为的转换，导入后请人工核对题面。
// 先整体转义再做替换，插进去的标签才是真的标签，正文里的 < 才不会变成标签。
function kat_tex_to_html($tex) {
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
  foreach (array('SampleInput', 'SampleOutput', 'Interaction', 'SampleInteraction', 'InteractionOutput') as $env) {
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
                 'SampleExplanation' => 'Sample explanation') as $env => $label) {
    $tex = preg_replace('/\\\\begin\{' . $env . '\}/', "\n<h3>" . $label . "</h3>\n", $tex);
    $tex = preg_replace('/\\\\end\{' . $env . '\}/', "\n", $tex);
  }
  $tex = preg_replace('/\\\\(begin|end)\{(itemize|enumerate|description|center|flushright)\}/', '', $tex);
  $tex = str_replace('\item', '&bull; ', $tex);
  $tex = preg_replace('/\\\\(pagebreak|clearpage|newpage)\b/', "\n<hr>\n", $tex);
  $tex = preg_replace('/\\\\problemname\s*\{[^{}]*\}/s', '', $tex);
  $tex = preg_replace('/\\\\label\s*\{[^{}]*\}/', '', $tex);
  $tex = preg_replace('/\\\\(usepackage|documentclass|input|include|graphicspath|bibliographystyle)\b[^\n]*\n?/', '', $tex);
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
    kat_notice("题面里还有没转换掉的 LaTeX 环境，请在后台核对 description。");
  }
  return '<p>' . $tex . '</p>';
}

// data/<group>/<stem>.in 摊平成 data/<group>/<stem>，sample 组排在最前
function kat_collect_cases($map, $root) {
  $prefix = ($root === '' ? '' : $root . '/') . 'data/';
  $cases = array();
  foreach ($map as $name => $info) {
    if (strpos($name, $prefix) !== 0) continue;
    if (substr($name, -3) !== '.in') continue;
    if (basename($name) === '.in') continue;
    $cases[] = 'data/' . substr($name, strlen($prefix));
  }
  usort($cases, function ($a, $b) {
    $sa = (dirname($a) === 'data/sample');
    $sb = (dirname($b) === 'data/sample');
    if ($sa !== $sb) return $sa ? -1 : 1;
    return strnatcmp($a, $b);
  });
  return $cases;
}

// 题面：Markdown 优先于 LaTeX，同语言里优先 .en（题目包按 problem.<lang>.{md,tex} 命名）
function kat_find_statement($map, $prefix) {
  $best = array('md' => '', 'tex' => '');
  foreach ($map as $name => $info) {
    if (strpos($name, $prefix . 'problem_statement/') !== 0) continue;
    foreach (array('md', 'tex') as $ext) {
      if (substr($name, -(strlen($ext) + 1)) !== '.' . $ext) continue;
      $cur = $best[$ext];
      $mine = stripos(basename($name), '.en.') !== false;
      $theirs = $cur !== '' && stripos(basename($cur), '.en.') !== false;
      if ($cur === '' || ($mine && !$theirs)) $best[$ext] = $name;
    }
  }
  if ($best['md'] !== '') return array($best['md'], true);
  if ($best['tex'] !== '') return array($best['tex'], false);
  return array(null, false);
}

// 同一组数据的兄弟文件（.ans / .out / .interaction），返回相对于题目根的路径
function kat_sibling($map, $prefix, $case, $ext) {
  $name = dirname($case) . '/' . basename($case, '.in') . $ext;
  return isset($map[$prefix . $name]) ? $name : null;
}

// 数据目录属主/权限不对时 file_put_contents 只会发 warning，这里统一报出来
function kat_write($path, $content) {
  if (file_put_contents($path, str_replace("\r\n", "\n", $content)) === false) {
    kat_notice("写入失败：" . htmlentities($path, ENT_QUOTES, 'UTF-8') . "，请检查 " . "chmod 775 -R \$OJ_DATA 与属组。");
    return false;
  }
  return true;
}

function import_kattis_problem($zip, $map, $root, $OJ_DATA) {
  global $KAT_YAML_MAX, $KAT_TEXT_MAX, $KAT_DATA_MAX, $KAT_DESC_MAX, $OJ_NAME;

  $prefix = ($root === '' ? '' : $root . '/');
  $yaml_text = kat_read($zip, $map, $prefix . 'problem.yaml', $KAT_YAML_MAX);
  $y = kat_yaml($yaml_text);
  if ($y === null && !function_exists('yaml_parse')) {
    kat_notice("未安装 php-yaml 扩展，problem.yaml 按文本解析（只认 name/validation/limits/source/license/author）。");
  }

  $title = kat_title($y, $yaml_text, $root === '' ? 'Problem' : basename($root));
  $validation = kat_yget($y, 'validation');
  if ($validation === null) $validation = kat_rget($yaml_text, '/^validation\s*:\s*(.+)$/m');
  $validation = strtolower(trim((string)$validation));

  $source = kat_yget($y, 'source');
  if ($source === null) $source = kat_rget($yaml_text, '/^source\s*:\s*(.+)$/m');
  $source = trim('Kattis ' . (string)$source);
  $license = kat_yget($y, 'license');
  if ($license === null) $license = kat_rget($yaml_text, '/^license\s*:\s*(.+)$/m');
  $author = trim((string)(kat_yget($y, 'author') === null ? kat_rget($yaml_text, '/^author\s*:\s*(.+)$/m') : kat_yget($y, 'author')));
  $hint = ($author !== '' ? 'Author: ' . $author . ' ' : '') . ($license !== null ? 'License: ' . $license : '');

  // 校验方式 -> hustoj 的 spj
  $spj = 0;
  $multi_pass = ($validation === 'custom multi-pass');
  if ($validation === 'custom multi-pass' || $validation === 'custom') {
    $spj = 4;
  } else if ($validation === 'custom interactive') {
    $spj = 3;
    kat_notice(htmlentities("[$title] validation: custom interactive 走的是交互题（spj=3）路径，需要 $prefix" . "spj/ 下的交互校验程序；题目包一般给的是 output validator，建议按 custom multi-pass 判。"));
  } else if ($validation !== 'default' && $validation !== '') {
    kat_notice(htmlentities("[$title] 不认识的 validation: " . $validation . "，按普通题（spj=0）处理。"));
  }

  // 时限：包根的 .timelimit（秒），没有就 1s；多轮题再乘 time_multiplier（Kattis 的口径）
  $time = 1.0;
  $tl = kat_read($zip, $map, $prefix . '.timelimit', 64);
  if ($tl === null && isset($map['.timelimit'])) $tl = kat_read($zip, $map, '.timelimit', 64);
  if (is_string($tl) && is_numeric(trim($tl))) $time = floatval(trim($tl));
  $mult = kat_yget($y, 'limits.time_multiplier');
  if ($mult === null) $mult = kat_rget($yaml_text, '/^\s*time_multiplier\s*:\s*([0-9.]+)\s*$/m');
  if ($multi_pass && is_numeric($mult) && floatval($mult) > 0) $time = round($time * floatval($mult), 3);

  $mem = kat_yget($y, 'limits.memory');
  if ($mem === null) $mem = kat_yget($y, 'limits.memory_limit');
  if ($mem === null) $mem = kat_rget($yaml_text, '/^\s*memory(?:_limit)?\s*:\s*([0-9]+)\s*$/m');
  $memory = (is_numeric($mem) && floatval($mem) > 0) ? intval(floatval($mem)) : 1024;

  // 题面：Markdown 优先，其次 LaTeX
  list($statement, $is_md) = kat_find_statement($map, $prefix);
  $description = '';
  if ($statement !== null) {
    $text = kat_read($zip, $map, $statement, $KAT_TEXT_MAX);
    if (is_string($text)) {
      if ($is_md) {
        $description = '<span class="md">' . kat_replaceLT($text) . '</span>';
      } else {
        $description = kat_tex_to_html($text)
            . "\n<p><small>（题面由 LaTeX 自动转换，请人工核对。原文：" . htmlentities($statement, ENT_QUOTES, 'UTF-8') . "）</small></p>";
      }
    }
  } else {
    kat_notice(htmlentities("[$title] 没找到 problem_statement 下的题面，description 为空，请手工补上。"));
  }
  if (strlen($description) > $KAT_DESC_MAX) {
    $description = substr($description, 0, $KAT_DESC_MAX) . "<p>……（题面过长已被截断，请在后台编辑补全）</p>";
    kat_notice(htmlentities("[$title] 题面超过 " . $KAT_DESC_MAX . " 字节，已截断。"));
  }

  $cases = kat_collect_cases($map, $root);
  if (count($cases) === 0) {
    kat_notice(htmlentities("[$title] {$prefix}data/ 下没有 .in 文件，只建了题目。"));
  }

  $pid = addproblem($title, $time, $memory, $description, '', '', '', '', $hint, $source, $spj, $OJ_DATA);
  if (!file_exists($OJ_DATA . "/" . $pid)) @mkdir($OJ_DATA . "/" . $pid, 0755, true);

  $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
  pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "p$pid");
  $_SESSION[$OJ_NAME . '_' . "p$pid"] = true;

  $written = 0;
  $sample_case = null;
  foreach ($cases as $i => $case) {
    $n = $i + 1;
    $in = kat_read($zip, $map, $prefix . $case, $KAT_DATA_MAX);
    if (!is_string($in)) continue;

    $ans_name = kat_sibling($map, $prefix, $case, '.ans');
    $out_name = kat_sibling($map, $prefix, $case, '.out');

    if (!kat_write($OJ_DATA . "/$pid/$n.in", $in)) continue;
    if ($spj == 4 || $spj == 3) {
      // communication_case 拿同名的 .ans 当 jury.ans；这类题的 .ans 常常是空文件，缺了就补一个空的
      $src = ($ans_name !== null) ? $ans_name : $out_name;
      $content = ($src === null) ? null : kat_read($zip, $map, $prefix . $src, $KAT_DATA_MAX);
      kat_write($OJ_DATA . "/$pid/$n.ans", is_string($content) ? $content : '');
    } else {
      $src = ($out_name !== null) ? $out_name : $ans_name;
      $content = ($src === null) ? null : kat_read($zip, $map, $prefix . $src, $KAT_DATA_MAX);
      if (!is_string($content)) {
        kat_notice(htmlentities("[$title] 第 $n 组缺标准答案（.out/.ans），判题会一律 WA。"));
        $content = '';
      }
      kat_write($OJ_DATA . "/$pid/$n.out", $content);
    }
    $written++;
    if ($sample_case === null && dirname($case) === 'data/sample') $sample_case = $case;
  }

  // 样例只取 data/sample，secret 组的输入不当样例展示
  if ($sample_case !== null) {
    $in = kat_read($zip, $map, $prefix . $sample_case, $KAT_DATA_MAX);
    $sample_input = is_string($in) ? $in : '';
    $sample_output = '';
    foreach (array('.interaction', '.out', '.ans') as $ext) {
      $sib = kat_sibling($map, $prefix, $sample_case, $ext);
      if ($sib === null) continue;
      $content = kat_read($zip, $map, $prefix . $sib, $KAT_DATA_MAX);
      if (is_string($content)) { $sample_output = $content; break; }
    }
    pdo_query("UPDATE problem SET sample_input=?, sample_output=? WHERE problem_id=?", $sample_input, $sample_output, $pid);
  }

  // 校验器：只搬源码；预编译好的可执行文件一律不装
  $build = array();
  $copied = 0;
  foreach (array('output_validators', 'answer_validators', 'input_validators') as $role) {
    $role_prefix = $prefix . $role . '/';
    foreach ($map as $name => $info) {
      if (strpos($name, $role_prefix) !== 0) continue;
      $unit = dirname(substr($name, strlen($role_prefix)));
      $base = basename($name);
      if ($unit === '.' || !kat_safe_basename($unit) || !kat_safe_basename($base)) continue;
      if (!preg_match('/\.(cpp|cc|h|hpp)$/i', $base)) continue;
      $content = kat_read($zip, $map, $name, $KAT_TEXT_MAX);
      if (!is_string($content)) continue;
      $dir = $OJ_DATA . "/$pid/validator.src/$role/$unit";
      if (!file_exists($dir) && !@mkdir($dir, 0755, true)) continue;
      if (!kat_write($dir . "/" . $base, $content)) continue;
      $copied++;
      if ($role === 'output_validators' && $base === $unit . '.cpp') $build[] = array($dir, $base);
    }
  }

  echo "<hr><b>PID <a href='../problem.php?id=$pid'>" . $pid . "</a></b> " . htmlentities($title, ENT_QUOTES, "UTF-8")
     . " &mdash; spj=$spj" . ($multi_pass ? "（通信题，逐轮判题）" : "")
     . "，$written 组数据，时限 {$time}s，内存 {$memory}M<br>";

  $todo = array();
  if ($spj == 4) {
    if (count($build) > 0) {
      foreach ($build as $b) {
        $todo[] = "cd " . $b[0] . " && g++ -std=c++17 -O2 -static -o " . $OJ_DATA . "/$pid/validator " . $b[1];
      }
    } else {
      $todo[] = "包里没有 output_validators/<名字>/<名字>.cpp；判题机找不到 $OJ_DATA/$pid/validator 会判 RE";
    }
    $passes = kat_yget($y, 'limits.validation_passes');
    if ($passes === null) $passes = kat_rget($yaml_text, '/^\s*validation_passes\s*:\s*([0-9]+)\s*$/m');
    if ($passes !== null) $todo[] = "problem.yaml 要求 validation_passes={$passes}（judge_client 的轮数上限是 COMM_MAX_PASS=100，不用另外设置）";
  }
  if ($copied > 0) $todo[] = "校验器源码在 $OJ_DATA/$pid/validator.src/（$copied 个文件），编译产物放 $OJ_DATA/$pid/validator 并保证可执行";
  if ($spj == 3) $todo[] = "spj=3 是 FIFO 交互题路径，需要 $OJ_DATA/$pid/spj.cc；本题包给的是 output validator";

  if (count($todo) > 0) {
    echo "<pre style='background:#f8f8f8;padding:8px;border:1px solid #ddd'>还需要在判题机上手工完成（web 端不以 root 编译作者代码）：<br>";
    foreach ($todo as $t) echo "&nbsp;&nbsp;" . htmlentities($t, ENT_QUOTES, "UTF-8") . "<br>";
    echo "</pre>";
  }
  return $pid;
}

function kat_extension($file) {
  return strtolower(pathinfo(basename($file), PATHINFO_EXTENSION));
}

if ($_FILES["fps"]["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: " . $_FILES["fps"]["error"] . " File size is too big, change in PHP.ini<br />";
} else if (kat_extension($_FILES["fps"]["name"]) != "zip") {
  echo "&nbsp;&nbsp;- 只接受 ICPC 题目包（Kattis）导出的 .zip<br />";
} else if (!class_exists('ZipArchive')) {
  kat_notice("当前 PHP 环境没有 zip 扩展，无法读取题目包。宝塔：软件商店 -> PHP 设置 -> 安装扩展 -> zip。");
} else {
  @set_time_limit(600);
  $zip = new ZipArchive();
  if ($zip->open($_FILES["fps"]["tmp_name"]) !== true) {
    die("Could not open ZIP archive.");
  }
  $map = kat_zip_index($zip);

  // 哪个目录里有 problem.yaml，那个目录就算一道题：整场比赛打包成的 zip 也能一次导完
  $roots = array();
  foreach ($map as $name => $info) {
    if (basename($name) === 'problem.yaml') {
      $dir = dirname($name);
      $roots[$dir === '.' ? '' : $dir] = true;
    }
  }
  $roots = array_keys($roots);
  sort($roots, SORT_STRING);

  if (count($roots) === 0) {
    kat_notice("这个 zip 里找不到 problem.yaml，不是 ICPC problem package（Kattis 题目包）。");
  } else if (!is_writable($OJ_DATA)) {
    // 数据目录写不进去就先停手：否则建出一堆有题面没数据的空题
    kat_notice("数据目录 <b>" . htmlentities($OJ_DATA, ENT_QUOTES, 'UTF-8') . "</b> 不可写，请执行 "
             . "chmod 775 -R " . htmlentities($OJ_DATA, ENT_QUOTES, 'UTF-8') . " 并把属组改成 www 用户（参见 phpfm 页面的提示）。");
  } else {
    echo "&nbsp;&nbsp;- 找到 " . count($roots) . " 道题："
       . htmlentities(implode(', ', array_map(function ($r) { return $r === '' ? '(包根)' : $r; }, $roots)), ENT_QUOTES, 'UTF-8') . "<br>";
    $pids = array();
    foreach ($roots as $root) $pids[] = import_kattis_problem($zip, $map, $root, $OJ_DATA);
    echo "<hr>已导入 PID：" . implode(', ', $pids) . "<br>通信题（spj=4）按 data/&lt;pid&gt;/N.in 逐轮运行提交程序，每轮把 user.out 交给 "
       . "$OJ_DATA/&lt;pid&gt;/validator 裁决；校验器写出 nextpass.in 就进入下一轮，没写就算这一组过了。";
  }
  $zip->close();
  unlink($_FILES["fps"]["tmp_name"]);
}
