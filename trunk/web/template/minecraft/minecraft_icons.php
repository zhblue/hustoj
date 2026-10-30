<?php
/* minecraft 模板 · 图标助手
 * 用法：在 header.php 里把 <i class="home icon"></i> 替换为 <?php echo xicon('home'); ?>
 * 每个 key 返回一段 inline SVG（class="minecraft-nav-icon"，贴 currentColor；方块类图标用 MC 招牌色直接上色）。
 * 没有匹配的 key 返回空字符串（不报错）。
 */
function xicon($key) {
    static $map = array(
        // 品牌：creeper 脸
        'brand'   => '<svg class="mc-brand" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" fill="#5d9c3f" stroke="#0c0c0c" stroke-width="1"/><rect x="7" y="8" width="3" height="4" fill="#0c0c0c"/><rect x="14" y="8" width="3" height="4" fill="#0c0c0c"/><rect x="7" y="14" width="10" height="3" fill="#0c0c0c"/><rect x="11" y="14" width="2" height="4" fill="#0c0c0c"/></svg>',

        // 首页：草方块
        'home'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="1" fill="#7a5230" stroke="#000" stroke-width="1.5"/><rect x="3" y="3" width="18" height="6" fill="#6abe30"/></svg>',

        // 题目列表：书
        'list'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M4 4h7v16H4zM13 4h7v16h-7z"/></svg>',

        // 状态：地图
        'globe'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1" fill="#cd" stroke="#000" stroke-width="1.5"/><rect x="3" y="5" width="18" height="14" rx="1" fill="#e9e2c4" stroke="#000" stroke-width="1.5"/><path d="M12 9l4 3-4 3-4-3z" fill="#6abe30"/></svg>',

        // 比赛：钻石
        'trophy'  => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l5 8-5 12-5-12z" fill="#3ad6e0" stroke="#1b6b73" stroke-width="1.2"/></svg>',

        // 提交：合成台（2x2）
        'tasks'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="8" height="8"/><rect x="13" y="3" width="8" height="8"/><rect x="3" y="13" width="8" height="8"/><rect x="13" y="13" width="8" height="8"/></svg>',

        // 运行状态：红石火把
        'signal'  => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="11" y="10" width="2" height="11" fill="#7a5230"/><rect x="7" y="4" width="10" height="7" rx="1" fill="#b02e26" stroke="#000" stroke-width="1"/></svg>',

        // 公告：音符块
        'bullhorn'=> '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M9 4v10.5a3 3 0 1 0 2 2.8V8l7-2v7.5a3 3 0 1 0 2 2.8V3z"/></svg>',

        // 帮助：问号方块
        'help-circle' => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="1" fill="#facc2e" stroke="#000" stroke-width="1.5"/><text x="12" y="17" font-size="14" font-family="monospace" font-weight="bold" text-anchor="middle" fill="#000">?</text></svg>',

        // 关于：书与羽毛（剪贴板造型）
        'clipboard'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M6 3h12v18H6zM9 7h6v2H9zM9 11h6v2H9zM9 15h6v2H9z"/></svg>',

        // 返回：箭头
        'arrow-left'  => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M14 6l-6 6 6 6V6z"/></svg>',

        // 排名：橡木牌子
        'numbered-list' => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="10" fill="#a9743b" stroke="#000" stroke-width="1"/><rect x="11" y="14" width="2" height="7" fill="#7a5230"/></svg>',

        // 用户：Steve 头
        'child'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" fill="#caa472" stroke="#000" stroke-width="1.5"/><rect x="8" y="9" width="3" height="3" fill="#000"/><rect x="14" y="9" width="3" height="3" fill="#000"/><rect x="8" y="14" width="9" height="3" fill="#7a4a2a"/></svg>',

        // 以下由 replace_icons.py 在 PHP 字符串字面量内检测到、需人工拼接
        'group'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="8" height="8" fill="#caa472" stroke="#000" stroke-width="1"/><rect x="13" y="7" width="8" height="8" fill="#caa472" stroke="#000" stroke-width="1"/><rect x="5" y="11" width="2" height="2" fill="#000"/><rect x="15" y="11" width="2" height="2" fill="#000"/></svg>',
        'book'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M5 4h6v16H5zM13 4h6v16h-6z"/></svg>',

        // 由 replace_icons.py 在 header 中替换、但脚手架骨架未预置的 key
        'comments' => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M4 4h16v12H8l-4 4z"/></svg>',
        'dropdown' => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>',

        // 我的：末影之眼
        'eye'     => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="12" rx="9" ry="6" fill="#e8e0c0" stroke="#000" stroke-width="1"/><circle cx="12" cy="12" r="3.5" fill="#3ad6e0"/></svg>',

        // 编辑资料：铁砧
        'edit'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M5 7h14l-2 3h-2v3H9v-3H7zM10 15h4v5h-4z"/></svg>',

        // 设置：红石比较器（齿轮）
        'settings'=> '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm-9 4l3-2 1 3-3 2zm18 0l-3-2 1-3 3 2zM12 1l-2 3 3 1 2-3zm0 22l-2-3 3-1 2 3z"/></svg>',

        // 比赛子菜单-登入：末影珍珠
        'golf-ball' => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8" fill="#7d3ad6"/><circle cx="9" cy="9" r="2" fill="#b98af0"/></svg>',

        // 私信：箱子
        'mail'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="8" width="18" height="13" rx="1" fill="#7a5230" stroke="#000" stroke-width="1.5"/><rect x="3" y="8" width="18" height="5" fill="#8a5a34"/><rect x="10" y="12" width="4" height="3" fill="#facc2e" stroke="#000" stroke-width="1"/></svg>',

        // 打印：纸张
        'print'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M7 3h10v14H7zM9 7h6v2H9zM9 11h6v2H9z"/></svg>',

        // 退出：床
        'power'   => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><rect x="3" y="10" width="18" height="9" fill="#caa472"/><rect x="3" y="6" width="8" height="6" fill="#b02e26"/></svg>',

        // 推荐自学方向：下界之星
        'star'    => '<svg class="minecraft-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3 7 7 0-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9l7 0z" fill="#f4f4f4" stroke="#000" stroke-width="1"/></svg>',
    );
    return isset($map[$key]) ? $map[$key] : '';
}
?>
