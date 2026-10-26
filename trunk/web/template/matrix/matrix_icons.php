<?php
/* matrix 模板 · 图标助手（黑客帝国 · 荧光绿终端风）
 * 用法：在 header.php 里把 <i class="home icon"></i> 替换为 <?php echo xicon('home'); ?>
 * 每个 key 返回一段 inline SVG（class="matrix-nav-icon"，贴 currentColor）。
 * 没有匹配的 key 返回空字符串（不报错）。
 * 配色：荧光绿 #00FF41 / 霓虹绿 #39FF14 / 继承色 currentColor（顶栏已为绿）。
 */
function xicon($key) {
    static $map = array(
        // 品牌：数字雨方块（0 1 + 闪烁光标）
        'brand' => '<svg class="matrix-brand" viewBox="0 0 24 24" aria-hidden="true"><rect x="2.4" y="2.4" width="19.2" height="19.2" rx="3.2" fill="#001006" stroke="#00FF41" stroke-width="1.4"/><ellipse cx="8.4" cy="13.2" rx="1.9" ry="3.2" fill="none" stroke="#00FF41" stroke-width="1.5"/><path d="M15.4 10v6.4M13.8 11.8l1.6-1.8" fill="none" stroke="#00FF41" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect class="mx-cursor" x="6.4" y="17.6" width="11.2" height="1.9" rx="0.6" fill="#39FF14"/></svg>',
        // 首页：终端提示符 >_
        'home' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.2 6.6l5.2 5.4-5.2 5.4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.6 17.4h8.2" stroke="#00FF41" stroke-width="2.1" stroke-linecap="round"/></svg>',
        // 小组：网络节点拓扑
        'group' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.8 7.4L6.6 15.8M13.2 7.4l4.2 8.4M7.9 17.4h8.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="5.4" r="2.4" fill="#00FF41"/><circle cx="5.4" cy="18" r="2.4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="18.6" cy="18" r="2.4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>',
        // 题目集：翻开的书本
        'book' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.6c2.6-1.3 5.3-1.3 8 0v13.6c-2.7-1.3-5.4-1.3-8 0z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M20 5.6c-2.6-1.3-5.3-1.3-8 0v13.6c2.7-1.3 5.4-1.3 8 0z" fill="none" stroke="#00FF41" stroke-width="1.9" stroke-linejoin="round"/><path d="M12 5.6v13.6" stroke="currentColor" stroke-width="1.3"/></svg>',
        // 列表：代码行方块
        'list' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3.4" y="5.6" width="2.6" height="2.6" rx="0.6" fill="#00FF41"/><rect x="3.4" y="10.7" width="2.6" height="2.6" rx="0.6" fill="currentColor"/><rect x="3.4" y="15.8" width="2.6" height="2.6" rx="0.6" fill="currentColor"/><path d="M9 6.9h11.6M9 12h11.6M9 17.1h8" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>',
        // 分类 / 来源：经纬地球
        'globe' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.6" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="M3.4 12h17.2" stroke="currentColor" stroke-width="1.5" opacity="0.85"/><ellipse cx="12" cy="12" rx="4" ry="8.6" fill="none" stroke="#00FF41" stroke-width="1.6"/></svg>',
        // 比赛：奖杯
        'trophy' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.2 4.6h9.6v5.2a4.8 4.8 0 0 1-9.6 0z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M7.2 6.6H4.4v1.6a3 3 0 0 0 2.9 3.1M16.8 6.6h2.8v1.6a3 3 0 0 1-2.9 3.1" fill="none" stroke="#00FF41" stroke-width="1.6"/><path d="M12 14.6v2.6M8.4 19.6h7.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M10.4 16.8h3.2l-.6 2.8h-2z" fill="currentColor"/></svg>',
        // 作业 / 任务：清单 + 勾
        'tasks' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4.4" y="4.2" width="15.2" height="16.4" rx="2.4" fill="none" stroke="currentColor" stroke-width="1.9"/><rect x="9" y="2.6" width="6" height="3.2" rx="1" fill="currentColor"/><path d="M7.6 10.4l1.8 1.8 3.2-3.2" fill="none" stroke="#00FF41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.6 16.8l1.8 1.8 3.2-3.2" fill="none" stroke="#00FF41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 11.2h3.2M15 17.6h3.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        // 实时状态：信号发射塔
        'signal' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21V10.4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="9.2" r="2.2" fill="#00FF41"/><path d="M6.8 6.8a7.4 7.4 0 0 1 10.4 0M4.4 4.6a10.8 10.8 0 0 1 15.2 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        // 公告：喇叭
        'bullhorn' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.4v3.2h3.4L15 17.6V6.4L7.4 10.4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M15 6.4v11.2l3.4-2.3V8.7z" fill="#00FF41" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M7.6 14v4.4h2.6v-3.4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
        // 帮助：问号
        'help-circle' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.8" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="M9.6 9.8a2.6 2.6 0 1 1 3.4 2.4c-.7.3-1 .8-1 1.7v.4" fill="none" stroke="#00FF41" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="17" r="1.3" fill="#00FF41"/></svg>',
        // 评测记录：剪贴板
        'clipboard' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4.6" y="4.6" width="14.8" height="16.4" rx="2.4" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="M9 2.8h6v3.2H9z" fill="currentColor"/><path d="M8.4 11.4h7.2M8.4 14.6h7.2M8.4 17.6h4.4" stroke="#00FF41" stroke-width="1.7" stroke-linecap="round"/></svg>',
        // 返回：左箭头
        'arrow-left' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H6.6" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/><path d="M11.4 6.6L6 12l5.4 5.4" fill="none" stroke="#00FF41" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        // 榜单：数字列表
        'numbered-list' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.4 6.6h10M10.4 12h10M10.4 17.4h6.6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M3.6 6.6h2.6l-2.6 3.2h2.6" fill="none" stroke="#00FF41" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.2 12.4h2.4v5h-2.4z" fill="none" stroke="#00FF41" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        // 用户：人形
        'child' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="6.6" r="3.4" fill="none" stroke="#00FF41" stroke-width="1.9"/><circle cx="10.6" cy="6.2" r="0.7" fill="#00FF41"/><circle cx="13.4" cy="6.2" r="0.7" fill="#00FF41"/><path d="M5.6 20.4c0-3.5 2.8-5.8 6.4-5.8s6.4 2.3 6.4 5.8" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>',
        // 查看：眼睛
        'eye' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.8 12S6.2 6.6 12 6.6 21.2 12 21.2 12 17.8 17.4 12 17.4 2.8 12 2.8 12z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><circle cx="12" cy="12" r="3.2" fill="none" stroke="#00FF41" stroke-width="1.9"/><circle cx="12" cy="12" r="1.2" fill="#00FF41"/></svg>',
        // 下拉三角
        'dropdown' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.8 9.6L12 14.8l5.2-5.2" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        // 推荐 / 收藏：星
        'star' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.2l2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.6l6.1-.9z" fill="none" stroke="#00FF41" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        // 编辑：铅笔
        'edit' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4.2L19.4 8.8a2.5 2.5 0 0 0-3.5-3.5L4.6 16.6V20z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M14.4 6.6l3.2 3.2" stroke="#00FF41" stroke-width="1.9" stroke-linecap="round"/></svg>',
        // 设置：齿轮
        'settings' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.2" fill="none" stroke="#00FF41" stroke-width="1.9"/><path d="M12 2.8v2.4M12 18.8v2.4M4.6 12H2.2M21.8 12h-2.4M5.8 5.8L4.1 4.1M19.9 19.9l-1.7-1.7M18.2 5.8l1.7-1.7M4.1 19.9l1.7-1.7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        // 奖励 / 气球：数据核心
        'golf-ball' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4.6" fill="none" stroke="#00FF41" stroke-width="1.6"/><circle cx="12" cy="12" r="1.7" fill="#00FF41"/><path d="M12 3.6v3.1M12 17.3v3.1M3.6 12h3.1M17.3 12h3.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        // 邮件：信封
        'mail' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5.8" width="18" height="13" rx="2.4" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="M3.6 7.2L12 13.4l8.4-6.2" fill="none" stroke="#00FF41" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        // 打印
        'print' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 9V4.6h10V9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><rect x="4" y="9" width="16" height="7" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M7.6 13.6h8.8v5.8H7.6z" fill="none" stroke="#00FF41" stroke-width="1.7"/><circle cx="17" cy="11.4" r="0.9" fill="#00FF41"/></svg>',
        // 登录 / 退出：电源
        'power' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.2v8.4" stroke="#00FF41" stroke-width="2.4" stroke-linecap="round"/><path d="M7 7.2a7 7 0 1 0 10 0" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',
        // 讨论：对话气泡
        'comments' => '<svg class="matrix-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.6h16v9.8H10l-4.4 4v-4H4z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><circle cx="8.6" cy="10.5" r="1.1" fill="#00FF41"/><circle cx="12" cy="10.5" r="1.1" fill="#00FF41"/><circle cx="15.4" cy="10.5" r="1.1" fill="#00FF41"/></svg>',
    );
    return isset($map[$key]) ? $map[$key] : '';
}
?>
