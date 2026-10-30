<?php
/* halloween 模板 · 图标助手
 * 用法：在 header.php 里把 <i class="home icon"></i> 替换为 <?php echo xicon('home'); ?>
 * 每个 key 返回一段 inline SVG（class="halloween-nav-icon"，贴 currentColor）。
 * 没有匹配的 key 返回空字符串（不报错）。
 */
function xicon($key) {
    static $map = array(
        // ===== 卡通万圣节导航图标集 =====
        'brand'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="14" rx="9" ry="7.5" fill="#ff8c1a"/><ellipse cx="12" cy="14" rx="3.4" ry="7.5" fill="#e8590c" opacity="0.5"/><ellipse cx="7" cy="14" rx="2.4" ry="7" fill="#e8590c" opacity="0.4"/><ellipse cx="17" cy="14" rx="2.4" ry="7" fill="#e8590c" opacity="0.4"/><rect x="10.7" y="4" width="2.6" height="4" rx="1.3" fill="#39c94a"/><path d="M8 13 l3 -2.4 2 2 -1.6 2.2 z" fill="#fff3e0"/><path d="M16 13 l-3 -2.4 -2 2 1.6 2.2 z" fill="#fff3e0"/><path d="M8 18 q2 -2.4 4 -2.4 q2 0 4 2.4 q-2 2.4 -4 2.4 q-2 0 -4 -2.4 z" fill="#fff3e0"/><path d="M9.6 18 l1 -1 1 1 1 -1 1 1 1 -1 1 1" fill="none" stroke="#e8590c" stroke-width="0.7"/></svg>',

        'home'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11 L12 4 L20 11" fill="none" stroke="#7b3ff2" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/><path d="M6 10 V19 H18 V10" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.6"/><rect x="10" y="14" width="4" height="5" rx="1" fill="#ff8c1a"/><circle cx="9" cy="13" r="1.3" fill="#ffd27a"/><circle cx="15" cy="13" r="1.3" fill="#ffd27a"/><path d="M12 6 v3" stroke="#ff8c1a" stroke-width="1.4"/></svg>',

        'group'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18 v-6 a3 3 0 0 1 6 0 v6 z" fill="#fff3e0"/><circle cx="8" cy="12" r="0.9" fill="#561d9e"/><circle cx="10" cy="12" r="0.9" fill="#561d9e"/><path d="M13 18 v-5 a2.4 2.4 0 0 1 4.8 0 v5 z" fill="#ff8c1a"/><circle cx="14.6" cy="13" r="0.8" fill="#fff3e0"/><circle cx="16.2" cy="13" r="0.8" fill="#fff3e0"/></svg>',

        'book'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5 h9 a2 2 0 0 1 2 2 v12 a2 2 0 0 0 -2 -2 H5 z" fill="#561d9e"/><path d="M19 5 h-9 a2 2 0 0 0 -2 2 v12 a2 2 0 0 1 2 -2 h9 z" fill="#7b3ff2"/><circle cx="12" cy="11" r="2" fill="#ffd27a"/><path d="M12 9.4 v5.2 M10.4 11 h3.2" stroke="#fff3e0" stroke-width="0.8"/></svg>',

        'list'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="3" fill="none" stroke="#7b3ff2" stroke-width="1.8"/><circle cx="8" cy="9.5" r="1.3" fill="#ff8c1a"/><circle cx="8" cy="14.5" r="1.3" fill="#7cff6b"/><path d="M11 9.5 h7 M11 14.5 h7" stroke="#fff3e0" stroke-width="1.4" stroke-linecap="round"/></svg>',

        'globe'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8" fill="#561d9e"/><path d="M12 4 a8 8 0 0 1 0 16 a4 4 0 0 1 0 -8 a4 4 0 0 0 0 -8 z" fill="#7b3ff2"/><path d="M9 12 q3 -2 6 0" stroke="#ff8c1a" stroke-width="1.2" fill="none"/><circle cx="9.5" cy="10" r="1" fill="#fff3e0"/><circle cx="14.5" cy="10" r="1" fill="#fff3e0"/></svg>',

        'trophy'  => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4 h8 v4 a4 4 0 0 1 -8 0 z" fill="#ffd27a"/><path d="M8 5 H5 v2 a3 3 0 0 0 3 3 M16 5 h3 v2 a3 3 0 0 1 -3 3" stroke="#ff8c1a" stroke-width="1.4" fill="none"/><rect x="10.5" y="12" width="3" height="3" fill="#ff8c1a"/><rect x="8" y="15" width="8" height="2.6" rx="1" fill="#e8590c"/><path d="M12 6 l1 1.6 -1 1 -1 -1 z" fill="#ff8c1a"/></svg>',

        'tasks'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3" fill="none" stroke="#7b3ff2" stroke-width="1.8"/><path d="M7 8 l1.5 1.5 L11 7" stroke="#7cff6b" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 14 l1.5 1.5 L11 13" stroke="#ff8c1a" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 8 h5 M13 14 h5" stroke="#fff3e0" stroke-width="1.4" stroke-linecap="round"/></svg>',

        'signal'  => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12 a10 10 0 0 1 14 0" stroke="#7b3ff2" stroke-width="1.8" fill="none" stroke-linecap="round"/><path d="M8 15 a6 6 0 0 1 8 0" stroke="#ff8c1a" stroke-width="1.8" fill="none" stroke-linecap="round"/><circle cx="12" cy="18.5" r="1.6" fill="#7cff6b"/></svg>',

        'bullhorn'=> '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10 h4 l8 -4 v12 l-8 -4 H4 z" fill="#7b3ff2"/><path d="M16 8 a3 3 0 0 1 0 8" stroke="#ff8c1a" stroke-width="1.5" fill="none"/><rect x="2.5" y="10" width="1.6" height="4" rx="0.8" fill="#ffd27a"/></svg>',

        'help-circle' => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4 a8 8 0 0 1 8 8 v3 l-2 1 v-1 a6 6 0 0 0 -12 0 v1 l-2 -1 v-3 a8 8 0 0 1 8 -8 z" fill="#fff3e0"/><circle cx="9.5" cy="11" r="1" fill="#561d9e"/><circle cx="14.5" cy="11" r="1" fill="#561d9e"/><path d="M11 13 a1.6 1.6 0 1 1 2 1.5 q-1 0.4 -1 1.1" stroke="#561d9e" stroke-width="1.1" fill="none" stroke-linecap="round"/><circle cx="12" cy="16.4" r="0.7" fill="#561d9e"/></svg>',

        'clipboard' => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="5" width="12" height="15" rx="2.5" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.6"/><rect x="9" y="3.5" width="6" height="3" rx="1.5" fill="#ff8c1a"/><path d="M8.5 11 h7 M8.5 14 h7 M8.5 17 h4" stroke="#fff3e0" stroke-width="1.3" stroke-linecap="round"/></svg>',

        'arrow-left' => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 6 L8 12 L14 18" stroke="#ff8c1a" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12 H19" stroke="#7b3ff2" stroke-width="2" stroke-linecap="round"/></svg>',

        'numbered-list' => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3" fill="none" stroke="#7b3ff2" stroke-width="1.6"/><circle cx="7.5" cy="9" r="1.4" fill="#ff8c1a"/><circle cx="7.5" cy="15" r="1.4" fill="#7cff6b"/><path d="M11 9 h7 M11 15 h7" stroke="#fff3e0" stroke-width="1.3" stroke-linecap="round"/></svg>',

        'child'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="3" fill="#fff3e0"/><path d="M7 19 v-4 a5 5 0 0 1 10 0 v4 z" fill="#7b3ff2"/><circle cx="10.5" cy="7" r="0.7" fill="#561d9e"/><circle cx="13.5" cy="7" r="0.7" fill="#561d9e"/><path d="M10.5 9 q1.5 1.2 3 0" stroke="#561d9e" stroke-width="0.8" fill="none"/></svg>',

        'eye'     => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12 q8 -7 16 0 q-8 7 -16 0 z" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.4"/><circle cx="12" cy="12" r="3.2" fill="#ff8c1a"/><circle cx="12" cy="12" r="1.3" fill="#fff3e0"/></svg>',

        'dropdown'=> '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9 L12 15 L18 9" stroke="#ff8c1a" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'star'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 l2.4 5 5.4 0.6 -4 3.8 1.1 5.4 -4.9 -2.7 -4.9 2.7 1.1 -5.4 -4 -3.8 5.4 -0.6 z" fill="#ffd27a" stroke="#ff8c1a" stroke-width="1"/></svg>',

        'edit'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 16 l1 -4 L15 2 l3 3 L8 15 z" fill="#7b3ff2" stroke="#561d9e" stroke-width="1"/><path d="M4 16 l-0.5 4 4 -0.5" stroke="#ff8c1a" stroke-width="1.6" fill="none" stroke-linecap="round"/><rect x="14.5" y="2.5" width="3" height="3" rx="1" transform="rotate(45 16 4)" fill="#ff8c1a"/></svg>',

        'settings'=> '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.2" fill="#7b3ff2"/><path d="M12 3 v3 M12 18 v3 M3 12 h3 M18 12 h3 M5.5 5.5 l2 2 M16.5 16.5 l2 2 M18.5 5.5 l-2 2 M7.5 16.5 l-2 2" stroke="#ff8c1a" stroke-width="1.6" stroke-linecap="round"/></svg>',

        'golf-ball' => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="14" rx="6" ry="7" fill="#ff8c1a"/><ellipse cx="10" cy="12" rx="2" ry="2.4" fill="#ffd27a" opacity="0.7"/><path d="M12 7 q1 -3 3 -3" stroke="#7cff6b" stroke-width="1.2" fill="none" stroke-linecap="round"/><path d="M12 21 v2" stroke="#7b3ff2" stroke-width="1.4"/></svg>',

        'mail'    => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2.5" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.4"/><path d="M4 7 L12 13 L20 7" stroke="#ff8c1a" stroke-width="1.4" fill="none"/><path d="M16 9 q2 -1.5 3 0 q-0.6 1.2 -1.5 1 q-0.6 0.8 -1.5 0.2 z" fill="#561d9e"/></svg>',

        'print'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="8" width="14" height="9" rx="2" fill="#7b3ff2"/><rect x="8" y="4" width="8" height="5" rx="1.5" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.2"/><rect x="8" y="14" width="8" height="5" rx="1" fill="#fff3e0"/><circle cx="15.5" cy="12.5" r="1" fill="#7cff6b"/></svg>',

        'power'   => '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4 v7" stroke="#ff8c1a" stroke-width="2.2" stroke-linecap="round"/><path d="M7.5 8 a7 7 0 1 0 9 0" stroke="#7b3ff2" stroke-width="2" fill="none" stroke-linecap="round"/></svg>',

        'comments'=> '<svg class="halloween-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5 h16 a2 2 0 0 1 2 2 v8 a2 2 0 0 1 -2 2 H9 l-4 3 v-3 H4 a2 2 0 0 1 -2 -2 V7 a2 2 0 0 1 2 -2 z" fill="#3a1f6e" stroke="#7b3ff2" stroke-width="1.4"/><circle cx="9" cy="11" r="1.1" fill="#ff8c1a"/><circle cx="12.5" cy="11" r="1.1" fill="#7cff6b"/><circle cx="16" cy="11" r="1.1" fill="#ffd27a"/></svg>',
    );
    return isset($map[$key]) ? $map[$key] : '';
}
