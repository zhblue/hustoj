<?php
/* bilibili 模板 · 图标助手（高仿 bilibili.com）
 * 用法：在 header.php 里把 <i class="home icon"></i> 替换为 <?php echo xicon('home'); ?>
 * 每个 key 返回一段 inline SVG（class="bilibili-nav-icon"，贴 currentColor）。
 * 没有匹配的 key 返回空字符串（不报错）。
 * 色板：B站粉 #FB7299 / 浅粉 #FF9DB7 / B站蓝 #00AEEC / 浅蓝 #40C5F1 / 线灰 #C9CCD0 / 深灰 #61666D
 */
function xicon($key) {
    static $map = array(
        // ===== bilibili 风导航图标集 =====

        // 品牌：小电视（bilibili logo）
        'brand'   => '<svg class="bilibili-brand" viewBox="0 0 40 32" aria-hidden="true"><path d="M9.4 3.2 L14.2 8.4 M30.6 3.2 L25.8 8.4" stroke="#FB7299" stroke-width="2.6" stroke-linecap="round" fill="none"/><rect x="3.4" y="7.6" width="33.2" height="23.2" rx="6" fill="#FB7299"/><rect x="6.6" y="10.8" width="26.8" height="16.8" rx="4" fill="#FFFFFF"/><path d="M13.4 17.4 q1.6 -2 3.2 0 M23.4 17.4 q1.6 -2 3.2 0" stroke="#FB7299" stroke-width="2" stroke-linecap="round" fill="none"/><path d="M17.2 22.4 q2.8 2 5.6 0" stroke="#FB7299" stroke-width="1.8" stroke-linecap="round" fill="none"/></svg>',

        // 首页
        'home'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.6 11 L12 4 L20.4 11" fill="none" stroke="#FB7299" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/><path d="M5.8 10 V20 H18.2 V10" fill="#FFFFFF" stroke="#FB7299" stroke-width="1.8" stroke-linejoin="round"/><rect x="9.6" y="14" width="4.8" height="6" rx="1" fill="#FB7299"/><circle cx="8.2" cy="12.6" r="0.9" fill="#FF9DB7"/><circle cx="15.8" cy="12.6" r="0.9" fill="#FF9DB7"/></svg>',

        // 小组：两个头像（UP 主与粉丝）
        'group'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="8.4" cy="8.4" r="4" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.7"/><path d="M2.6 20.4 a5.8 5.8 0 0 1 11.6 0 z" fill="#00AEEC"/><circle cx="16.6" cy="9.6" r="3.2" fill="#FFFFFF" stroke="#FB7299" stroke-width="1.6"/><path d="M12.6 20.4 a4.8 4.8 0 0 1 8.8 -2.6" fill="none" stroke="#FB7299" stroke-width="1.7" stroke-linecap="round"/></svg>',

        // 书：稿件 / 专栏
        'book'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4.8 h8.4 a2 2 0 0 1 2 2 v12.6 a2 2 0 0 0 -2 -2 H5 z" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><path d="M19 4.8 h-8.4 a2 2 0 0 0 -2 2 v12.6 a2 2 0 0 1 2 -2 H19 z" fill="#E8F7FD" stroke="#00AEEC" stroke-width="1.6"/><path d="M7.4 8.6 h5.2 M7.4 11.8 h5.2 M7.4 15 h3" stroke="#00AEEC" stroke-width="1.3" stroke-linecap="round"/><path d="M14.6 8.6 h2.4 M14.6 11.8 h2.4" stroke="#FB7299" stroke-width="1.3" stroke-linecap="round"/></svg>',

        // 列表：分区列表
        'list'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3.8" y="4.6" width="16.4" height="14.8" rx="4" fill="#FFFFFF" stroke="#C9CCD0" stroke-width="1.7"/><circle cx="8.2" cy="9.4" r="1.4" fill="#FB7299"/><circle cx="8.2" cy="14.6" r="1.4" fill="#00AEEC"/><path d="M11.6 9.4 h5.8 M11.6 14.6 h5.8" stroke="#61666D" stroke-width="1.4" stroke-linecap="round"/></svg>',

        // 地球：全站 / 分区导航
        'globe'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.2" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><path d="M12 3.8 a8.2 8.2 0 0 1 0 16.4 M12 3.8 a4.2 4.2 0 0 0 0 16.4" fill="none" stroke="#00AEEC" stroke-width="1.2"/><path d="M4 12 h16" stroke="#00AEEC" stroke-width="1.2"/><path d="M9.4 6.4 l-1.2 11.2 M14.6 6.4 l-1.2 11.2" stroke="#40C5F1" stroke-width="1" opacity="0.7"/></svg>',

        // 奖杯：排行榜
        'trophy'  => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3.8 h8 v4 a4 4 0 0 1 -8 0 z" fill="#FFD875" stroke="#E8A93B" stroke-width="1.2"/><path d="M8 5 H5 v1.8 a3 3 0 0 0 3 3 M16 5 h3 v1.8 a3 3 0 0 1 -3 3" stroke="#E8A93B" stroke-width="1.4" fill="none"/><rect x="10.6" y="11.8" width="2.8" height="3" fill="#E8A93B"/><rect x="7" y="14.8" width="10" height="2.8" rx="1.2" fill="#FB7299"/><path d="M12 3.2 l0.9 1.6 1.8 0.2 -1.3 1.2 0.4 1.8 -1.8 -1 -1.8 1 0.4 -1.8 -1.3 -1.2 1.8 -0.2 z" fill="#FFFFFF"/></svg>',

        // 任务：投稿管理
        'tasks'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3.8" y="4.4" width="16.4" height="15.2" rx="4" fill="#FFFFFF" stroke="#C9CCD0" stroke-width="1.7"/><rect x="6" y="6.6" width="12" height="6.6" rx="2" fill="#FFF0F5"/><path d="M10.2 9 l1.6 1.6 3 -3.2" stroke="#FB7299" stroke-width="1.7" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15 h4.4 M7 17.8 h4.4" stroke="#C9CCD0" stroke-width="1.3" stroke-linecap="round"/><circle cx="16.6" cy="16.4" r="1.6" fill="#00AEEC"/></svg>',

        // 信号：直播中
        'signal'  => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.6 12.6 a10 10 0 0 1 14.8 0" stroke="#FB7299" stroke-width="1.9" fill="none" stroke-linecap="round"/><path d="M7.8 15.4 a6 6 0 0 1 8.4 0" stroke="#FF9DB7" stroke-width="1.8" fill="none" stroke-linecap="round"/><circle cx="12" cy="18.6" r="1.8" fill="#FB7299"/><circle cx="18.4" cy="5.6" r="2.2" fill="#F85A5A"/><path d="M18.4 4.2 v2.8" stroke="#FFFFFF" stroke-width="1.2" stroke-linecap="round"/></svg>',

        // 喇叭：动态 / 公告
        'bullhorn'=> '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10 h3.6 l8.4 -4.2 v12.4 l-8.4 -4.2 H4 z" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.5"/><path d="M16 7.8 a3.2 3.2 0 0 1 0 8.4" stroke="#FB7299" stroke-width="1.6" fill="none" stroke-linecap="round"/><path d="M18.6 6 a5 5 0 0 1 0 12" stroke="#FF9DB7" stroke-width="1.2" fill="none" stroke-linecap="round"/><rect x="2.4" y="10" width="1.6" height="4" rx="0.6" fill="#00AEEC"/></svg>',

        // 帮助：问号（客服小电视）
        'help-circle' => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.6 a8.4 8.4 0 1 1 0 16.8 a8.4 8.4 0 0 1 0 -16.8 z" fill="#FFFFFF" stroke="#FB7299" stroke-width="1.7"/><path d="M9.4 9.6 a2.7 2.7 0 1 1 3.4 2.6 v1.4" stroke="#FB7299" stroke-width="1.8" fill="none" stroke-linecap="round"/><circle cx="12.8" cy="15.6" r="1" fill="#FB7299"/></svg>',

        // 剪贴板：稿件箱
        'clipboard' => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5.6" y="5" width="12.8" height="15" rx="3" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><rect x="9" y="3" width="6" height="3.4" rx="1.4" fill="#FB7299"/><path d="M8.4 11.4 h7.2 M8.4 14.6 h7.2 M8.4 17.8 h4.6" stroke="#61666D" stroke-width="1.3" stroke-linecap="round"/></svg>',

        // 左箭头：返回上一页
        'arrow-left' => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14.5 6 L8.5 12 L14.5 18" stroke="#FB7299" stroke-width="2.1" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 12 H19.5" stroke="#00AEEC" stroke-width="2.1" stroke-linecap="round"/></svg>',

        // 编号列表：排行榜
        'numbered-list' => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4.6" width="16" height="14.8" rx="4" fill="#FFFFFF" stroke="#C9CCD0" stroke-width="1.6"/><circle cx="7.8" cy="9" r="1.6" fill="#FB7299"/><circle cx="7.8" cy="15" r="1.6" fill="#00AEEC"/><path d="M11.4 9 h6.6 M11.4 15 h6.6" stroke="#61666D" stroke-width="1.3" stroke-linecap="round"/></svg>',

        // 儿童：大会员 / 用户
        'child'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.4" r="3.4" fill="#FFFFFF" stroke="#FB7299" stroke-width="1.6"/><path d="M6.6 20 v-4 a5.4 5.4 0 0 1 10.8 0 v4 z" fill="#FFF0F5" stroke="#FB7299" stroke-width="1.5"/><circle cx="10.4" cy="7.2" r="0.8" fill="#FB7299"/><circle cx="13.6" cy="7.2" r="0.8" fill="#FB7299"/><path d="M10.6 9.2 q1.4 1.2 2.8 0" stroke="#FB7299" stroke-width="0.9" fill="none" stroke-linecap="round"/></svg>',

        // 眼：播放量 / 观看
        'eye'     => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.4 12 q8.6 -7.4 17.2 0 q-8.6 7.4 -17.2 0 z" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><circle cx="12" cy="12" r="3.4" fill="#00AEEC"/><circle cx="12" cy="12" r="1.4" fill="#FFFFFF"/><circle cx="10.6" cy="10.6" r="0.7" fill="#FFFFFF"/></svg>',

        // 下拉：更多
        'dropdown'=> '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.4 9.4 L12 15.2 L17.6 9.4" stroke="#61666D" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        // 星：收藏 / 推荐
        'star'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.4 l2.5 5.1 5.6 0.7 -4.1 3.9 1.1 5.6 L12 15.9 6.9 18.7 l1.1 -5.6 L3.9 9.2 9.5 8.5 z" fill="#FFD875" stroke="#E8A93B" stroke-width="1.1"/></svg>',

        // 编辑：写专栏
        'edit'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 16.4 l1.2 -4.4 L15.4 2.2 l3.2 3.2 L8.4 15.2 z" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.4"/><path d="M4 16.4 l-0.6 4.4 l4.4 -0.6" stroke="#FB7299" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M14.8 2.8 l3.2 3.2" stroke="#FB7299" stroke-width="1.5"/></svg>',

        // 设置：创作中心
        'settings'=> '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.2" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.7"/><path d="M12 3.4 v2.4 M12 18.2 v2.4 M3.4 12 h2.4 M18.2 12 h2.4 M5.9 5.9 l1.7 1.7 M16.4 16.4 l1.7 1.7 M18.1 5.9 l-1.7 1.7 M7.6 16.4 l-1.7 1.7" stroke="#FB7299" stroke-width="1.7" stroke-linecap="round"/></svg>',

        // 气球：活动 / 抽奖
        'golf-ball' => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="10" rx="6.4" ry="7" fill="#FB7299"/><ellipse cx="9.8" cy="7.6" rx="2" ry="2.4" fill="#FF9DB7" opacity="0.8"/><path d="M12 3 q1.4 -2 2.8 -2.2" stroke="#FB7299" stroke-width="1.3" fill="none" stroke-linecap="round"/><path d="M12 17 q-1.4 3 0.6 5 q-0.6 -2 0.8 -3.4" stroke="#00AEEC" stroke-width="1.3" fill="none" stroke-linecap="round"/></svg>',

        // 邮件：私信
        'mail'    => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="3" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><path d="M3.8 7 L12 13.4 L20.2 7" stroke="#FB7299" stroke-width="1.5" fill="none" stroke-linejoin="round"/></svg>',

        // 打印
        'print'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="8" width="14" height="8.4" rx="2" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.4"/><rect x="8" y="3.8" width="8" height="5" rx="1.4" fill="#FFF0F5" stroke="#00AEEC" stroke-width="1.3"/><rect x="8" y="13.6" width="8" height="5.4" rx="1.4" fill="#FFFFFF" stroke="#C9CCD0" stroke-width="1.1"/><circle cx="15.6" cy="11.6" r="1" fill="#FB7299"/></svg>',

        // 开关：退出登录
        'power'   => '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.6 v7.2" stroke="#FB7299" stroke-width="2.3" stroke-linecap="round"/><path d="M7.4 7.8 a7 7 0 1 0 9.2 0" stroke="#00AEEC" stroke-width="2" fill="none" stroke-linecap="round"/></svg>',

        // 评论：弹幕 / 评论
        'comments'=> '<svg class="bilibili-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.4 5 h15.2 a1.8 1.8 0 0 1 1.8 1.8 v7.6 a1.8 1.8 0 0 1 -1.8 1.8 H9.6 l-4.2 3.2 v-3.2 H4.4 A1.8 1.8 0 0 1 2.6 14.2 V6.8 A1.8 1.8 0 0 1 4.4 5 z" fill="#FFFFFF" stroke="#00AEEC" stroke-width="1.6"/><path d="M7.4 9.4 h9.2 M7.4 12.4 h6" stroke="#FB7299" stroke-width="1.4" stroke-linecap="round"/></svg>',
    );
    return isset($map[$key]) ? $map[$key] : '';
}
