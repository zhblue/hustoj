# Stickman OJ · HUSTOJ 主题模板

基于 HUSTOJ 官方 syzoj 模板二次开发的火柴人主题。所有功能原样保留，只替换视觉。

## 安装

```bash
# 1. 把整个 stickman 目录拷到 HUSTOJ 的 web/template/ 下
cp -r web/template/stickman /path/to/your/hustoj/web/template/

# 2. 修改 db_info.inc.php 启用主题
#    把 $OJ_TEMPLATE 的值从 "syzoj" 改为 "stickman"
#    把 $OJ_CSS 的值从 "white.css" 改为 "stickman.css"
#    （脚手架已自动备份为 db_info.inc.php.bak）
```

切回原模板：把上面两行改回 `"syzoj"` 和 `"white.css"`。

## 目录结构（相对 syzoj 的差异）

```
template/stickman/
├── css/
│   ├── style.css          # 已改：@import white.css → stickman.css
│   └── stickman.css       # 新增：笔记本纸 + 马克笔主题
├── stickman_icons.php     # 新增：26 个火柴人风格 inline SVG 图标
├── stickman_deco.js       # 新增：14 个飘动的小火柴人装饰
├── favicon.svg            # 新增：举着奖杯的火柴人
├── header.php             # 改：注入 xicon()、品牌 logo、favicon、装饰容器
├── footer.php             # 改：引入 stickman_deco.js，主题署名
├── index.php              # 改：替换 section header 图标（8 处）
└── …（其余 158 个文件与 syzoj 完全相同，功能零改动）
```

## 视觉规范

- **背景**：`#F7F2E5` 笔记本纸米色 + 40px 一道淡横线
- **主笔触**：`#181818` 墨色
- **强调色**：马克笔红 `#D85A30` / 钢笔蓝 `#185FA5` / 荧光黄 `#EF9F27` / 绿 `#1D9E75`
- **判题状态语义色**：AC 绿 / WA 红 / TLE 黄 / RE 紫 / PE 蓝
- **卡片**：`8px 8px 0` 偏移阴影的"卡纸"质感 + 2px 黑色描边
- **按钮**：实心黑 + 偏移阴影，hover 时变红
- **装饰**：`#stickman-decor` 容器内 14 个随机姿态火柴人缓慢下飘

## 图标覆盖

`xicon('key')` 支持 26 个 key：
`brand home list book globe trophy tasks signal comments bullhorn help-circle clipboard arrow-left numbered-list child eye group star dropdown edit settings print power mail golf-ball search calendar info th rss file file-alternate magic gift money user lock envelope check circle minus-circle plus chevron-left chevron-right fast-backward fast-forward`

未列出的 key 会返回空串（不报错），后续可按需补 SVG。

## 验证

```bash
# 全部 .php 通过语法校验
cd web/template/stickman
php -l css/stickman.css   # CSS 不用 lint，下面只校验 PHP
for f in $(find . -name '*.php'); do php -l "$f"; done
# 期望：所有文件输出 "No syntax errors detected"

# 文件清单与 syzoj 一致 + 4 个新增资源
diff <(cd ../syzoj && find . -type f | sort) <(cd . && find . -type f | sort)
# 期望：仅多出 css/stickman.css, favicon.svg, stickman_deco.js, stickman_icons.php
```

## 已知遗留

- `problem.php / status.php / contest.php / rank.php / discuss.php` 等逻辑页面里的 `<i class="X icon">` 仍由 Semantic UI 渲染（火柴人主题不覆盖）。如要全部替换，可用 `replace_icons.py` 机械替换。
- `userinfo.php` 的金/银/铜星星、推荐、礼物、锁等图标尚未替换，但不影响整体效果。
- 暂未做暗色模式（如需可基于 CSS 变量扩展）。
