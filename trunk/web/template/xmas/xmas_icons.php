<?php
/* ============================================================
   HUSTOJ · xmas 模板 · 圣诞主题图标库
   以 inline SVG 返回节日图标，fill:currentColor 让其继承导航金色。
   仅用于视觉替换语义 UI 图标，不改变任何业务功能。
   用法: <?php echo xicon('gift'); ?>
   ============================================================ */
if (!function_exists('xicon')) {
    function xicon($name) {
        static $map = array(
            /* 首页：小木屋 + 屋顶积雪 */
            'home' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-4v-6h-8v6H4a1 1 0 0 1-1-1z" fill="currentColor"/><path d="M2.5 11.2 12 3.4l9.5 7.8-1.2 1.5L12 5.6 3.7 12.7z" fill="#fff" opacity=".9"/></svg>',
            /* 题目：礼物盒 */
            'gift' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="9" width="16" height="11" rx="1.5" fill="currentColor"/><rect x="3" y="6" width="18" height="4" rx="1" fill="currentColor"/><rect x="10.5" y="6" width="3" height="14" fill="#fff" opacity=".85"/><path d="M12 6c-2-3-5-2-4 0M12 6c2-3 5-2 4 0" stroke="#fff" stroke-width="1.6" fill="none"/></svg>',
            /* 分类：雪景球 */
            'globe' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="10" r="7" fill="currentColor"/><path d="M5 10h14M12 3v14M7 6.5l10 7M17 6.5l-10 7" stroke="#fff" stroke-width="1.1" opacity=".75"/><path d="M9 17h6l-1 3.5h-4z" fill="currentColor"/></svg>',
            /* 比赛：铃铛 */
            'bell' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a6 6 0 0 0-6 6c0 4-1.5 5.5-2 6.5h16c-.5-1-2-2.5-2-6.5a6 6 0 0 0-6-6z" fill="currentColor"/><path d="M10 19a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.6" fill="none"/><circle cx="12" cy="20.5" r="1.4" fill="#fff"/></svg>',
            /* 状态：小圣诞树 */
            'tree' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 7 11h3l-4 6h12l-4-6h3z" fill="currentColor"/><rect x="11" y="17" width="2" height="3.5" fill="currentColor"/><circle cx="12" cy="3" r="1.6" fill="#fff"/></svg>',
            /* 排名：金星 */
            'star' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l2.5 5.5 6 .6-4.5 4 1.3 5.9L12 16.8 6.7 19l1.3-5.9-4.5-4 6-.6z" fill="currentColor"/></svg>',
            /* 近期比赛：糖果棒 */
            'candy' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 21 17 3" stroke="currentColor" stroke-width="4" stroke-linecap="round"/><path d="M9 19 15 5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity=".9"/></svg>',
            /* 帮助：雪花 */
            'snowflake' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 2v20M2 12h20M5 5l14 14M19 5 5 19"/></g><g fill="currentColor"><circle cx="12" cy="2" r="1.6"/><circle cx="12" cy="22" r="1.6"/><circle cx="2" cy="12" r="1.6"/><circle cx="22" cy="12" r="1.6"/></g></svg>',
            /* 讨论：圣诞袜 */
            'stocking' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h4v9l6 2a3 3 0 0 1-2 5H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" fill="currentColor"/><path d="M20 13a3 3 0 0 0-2 5" stroke="#fff" stroke-width="1.4" fill="none"/><rect x="8" y="3" width="4" height="2" fill="#fff"/></svg>',
            /* 返回：左箭头 */
            'arrow' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5 7 12l7 7" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 12h12" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',
            /* 列表/清单：卷轴清单 */
            'list' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="16" rx="2" fill="currentColor"/><path d="M8 8h8M8 12h8M8 16h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg>',
            /* 任务：对勾清单 */
            'tasks' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="16" rx="2" fill="currentColor"/><path d="M8 9l1.6 1.6L12 8M8 15l1.6 1.6L12 14" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            /* 编号列表 */
            'numbered' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="16" rx="2" fill="currentColor"/><path d="M9 9h6M9 13h6M9 17h4" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg>',
            /* 统计：放大镜 */
            'eye' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6" fill="currentColor"/><path d="M15 15l5 5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="10.5" cy="10.5" r="2.4" fill="#fff"/></svg>',
            /* 编辑：铅笔 */
            'edit' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1-4L16 5l3 3L8 19z" fill="currentColor"/><path d="M15 6l3 3" stroke="#fff" stroke-width="1.4"/></svg>',
            /* 设置：齿轮 */
            'settings' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm9 4-2-1 1-2-2-1-1 2-2-.5L13 6h-2l-.5 2-2-.5-1-2-2 1 1 2-2 1v2l2 1-1 2 2 1 1-2 2 .5L11 18h2l.5-2 2 .5 1 2 2-1-1-2 2-1z" fill="currentColor" opacity=".9"/></svg>',
            /* 邮件：信封 */
            'mail' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2" fill="currentColor"/><path d="M4 7l8 6 8-6" stroke="#fff" stroke-width="1.4" fill="none"/></svg>',
            /* 打印 */
            'print' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="9" width="12" height="9" rx="1" fill="currentColor"/><path d="M8 9V5h8v4" stroke="currentColor" stroke-width="2" fill="none"/><rect x="9" y="12" width="6" height="4" fill="#fff"/></svg>',
            /* 退出：电源 */
            'power' => '<svg class="xmas-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v8" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><path d="M7 7a7 7 0 1 0 10 0" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round"/></svg>',
        );
        return isset($map[$name]) ? $map[$name] : '';
    }
}
