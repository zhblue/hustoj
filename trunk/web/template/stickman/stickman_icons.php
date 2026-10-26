<?php
/* ============================================================
   HUSTOJ · stickman 模板 · 火柴人风格图标库
   用 inline SVG 替换 Semantic UI 图标字体，全部为线稿风格
   （圆头+线条 / 马克笔手绘感 / 极简几何），与火柴人主题契合。
   用法: <?php echo xicon('home'); ?>
   类名 .stickman-nav-icon 统一控制尺寸（CSS 已配 1.18em）。
   ============================================================ */
if (!function_exists('xicon')) {
    function xicon($name) {
        static $map = array(
            /* 品牌 logo：举着奖杯的火柴人 */
            'brand' => '<svg class="stickman-nav-icon stickman-brand-logo" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="5" r="2.5"/>
                <line x1="12" y1="7.5" x2="12" y2="17"/>
                <path d="M12 10 L8 7.5 M12 10 L16 8"/>
                <line x1="12" y1="17" x2="9" y2="22"/>
                <line x1="12" y1="17" x2="15" y2="22"/>
                <path d="M9 2.5 L9 4.5 L15 4.5 L15 2.5 Z"/>
                <line x1="12" y1="2.5" x2="12" y2="1"/>
            </svg>',

            /* 首页：火柴人 + 小房子 */
            'home' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="6" r="2.2"/>
                <line x1="12" y1="8.2" x2="12" y2="15"/>
                <line x1="12" y1="10" x2="9" y2="13"/>
                <line x1="12" y1="10" x2="15" y2="12"/>
                <path d="M3 12 L12 4 L21 12 L21 20 L3 20 Z"/>
                <path d="M10 20 L10 14 L14 14 L14 20"/>
            </svg>',

            /* 题目列表：火柴人旁边的清单 */
            'list' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="4" width="14" height="16" rx="1.5"/>
                <line x1="6" y1="9" x2="14" y2="9"/>
                <line x1="6" y1="13" x2="14" y2="13"/>
                <line x1="6" y1="17" x2="11" y2="17"/>
                <circle cx="20.5" cy="6" r="1.5"/>
                <line x1="20.5" y1="7.5" x2="20.5" y2="11"/>
                <line x1="20.5" y1="9" x2="18" y2="10.5"/>
                <line x1="20.5" y1="9" x2="23" y2="10.5"/>
            </svg>',

            /* 书本：Hello 算法入口 */
            'book' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 5 L11 5 L11 20 L3 18 Z"/>
                <path d="M21 5 L13 5 L13 20 L21 18 Z"/>
                <line x1="11" y1="5" x2="13" y2="5"/>
                <line x1="6" y1="9" x2="9" y2="9"/>
                <line x1="6" y1="12" x2="9" y2="12"/>
                <line x1="15" y1="9" x2="18" y2="9"/>
                <line x1="15" y1="12" x2="18" y2="12"/>
            </svg>',

            /* 分类：地球 + 一个站在上面的火柴人 */
            'globe' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="13" r="8"/>
                <line x1="4" y1="13" x2="20" y2="13"/>
                <path d="M12 5 C8 9 8 17 12 21"/>
                <path d="M12 5 C16 9 16 17 12 21"/>
                <circle cx="12" cy="3" r="1.5"/>
                <line x1="12" y1="4.5" x2="12" y2="7"/>
                <line x1="12" y1="5" x2="10" y2="6"/>
                <line x1="12" y1="5" x2="14" y2="6"/>
            </svg>',

            /* 比赛：奖杯 */
            'trophy' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 4 L17 4 L17 9 C17 12 15 14 12 14 C9 14 7 12 7 9 Z"/>
                <path d="M7 6 L4 6 L4 9 C4 11 5 12 7 12"/>
                <path d="M17 6 L20 6 L20 9 C20 11 19 12 17 12"/>
                <line x1="12" y1="14" x2="12" y2="17"/>
                <line x1="9" y1="20" x2="15" y2="20"/>
                <line x1="12" y1="17" x2="12" y2="20"/>
                <path d="M11 1.5 L12 3 L13.5 1.5"/>
            </svg>',

            /* 状态：清单 + 对勾 */
            'tasks' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="3" width="16" height="18" rx="1.5"/>
                <path d="M7 9 L9 11 L12 8"/>
                <line x1="14" y1="9.5" x2="17" y2="9.5"/>
                <path d="M7 15 L9 17 L12 14"/>
                <line x1="14" y1="15.5" x2="17" y2="15.5"/>
            </svg>',

            /* 排名：阶梯 + 三个小点（领奖台） */
            'signal' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="14" width="5" height="6"/>
                <rect x="10" y="10" width="5" height="10"/>
                <rect x="16" y="6" width="5" height="14"/>
                <circle cx="6.5" cy="17" r="0.8" fill="currentColor"/>
                <circle cx="12.5" cy="13" r="0.8" fill="currentColor"/>
                <circle cx="18.5" cy="9" r="0.8" fill="currentColor"/>
            </svg>',

            /* 讨论：两个火柴人 + 对话气泡 */
            'comments' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="7" cy="8" r="2"/>
                <line x1="7" y1="10" x2="7" y2="15"/>
                <line x1="7" y1="12" x2="4" y2="14"/>
                <line x1="7" y1="12" x2="10" y2="14"/>
                <line x1="7" y1="15" x2="5" y2="20"/>
                <line x1="7" y1="15" x2="9" y2="20"/>
                <circle cx="17" cy="13" r="2"/>
                <line x1="17" y1="15" x2="17" y2="20"/>
                <line x1="17" y1="17" x2="14" y2="19"/>
                <line x1="17" y1="17" x2="20" y2="19"/>
                <path d="M14 4 L20 4 L20 9 L16 9 L14 11 Z" stroke-dasharray="2 1.5"/>
            </svg>',

            /* 公告/近期比赛：火柴人 + 喇叭 */
            'bullhorn' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="6" cy="11" r="2"/>
                <line x1="6" y1="13" x2="6" y2="20"/>
                <line x1="6" y1="15" x2="4" y2="20"/>
                <line x1="6" y1="15" x2="8" y2="20"/>
                <path d="M11 7 L20 4 L20 14 L11 11 Z"/>
                <line x1="11" y1="7" x2="11" y2="11"/>
                <line x1="11" y1="9" x2="9" y2="9.5"/>
            </svg>',

            /* 帮助 */
            'help-circle' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M9 9 C9 7 10.5 6 12 6 C13.5 6 15 7 15 9 C15 11 12 11 12 13"/>
                <circle cx="12" cy="17" r="0.5" fill="currentColor"/>
            </svg>',

            /* 讨论板 / 剪贴板 */
            'clipboard' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="5" y="5" width="14" height="16" rx="1.5"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="8" y1="11" x2="16" y2="11"/>
                <line x1="8" y1="15" x2="16" y2="15"/>
                <line x1="8" y1="18" x2="13" y2="18"/>
            </svg>',

            /* 返回箭头 */
            'arrow-left' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <line x1="20" y1="12" x2="4" y2="12"/>
                <polyline points="10 6 4 12 10 18"/>
            </svg>',

            /* 编号列表 */
            'numbered-list' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <text x="3" y="10" font-size="6" font-weight="700" fill="currentColor" stroke="none">1</text>
                <text x="3" y="18" font-size="6" font-weight="700" fill="currentColor" stroke="none">2</text>
                <line x1="9" y1="9" x2="20" y2="9"/>
                <line x1="9" y1="17" x2="20" y2="17"/>
            </svg>',

            /* OI 排名（小火柴人） */
            'child' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="7" r="2.5"/>
                <line x1="12" y1="9.5" x2="12" y2="16"/>
                <line x1="12" y1="11" x2="9" y2="13"/>
                <line x1="12" y1="11" x2="15" y2="13"/>
                <line x1="12" y1="16" x2="9.5" y2="20"/>
                <line x1="12" y1="16" x2="14.5" y2="20"/>
                <circle cx="19" cy="18" r="1" fill="currentColor"/>
            </svg>',

            /* 眼睛 / 统计 */
            'eye' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M2 12 C5 6 9 6 12 6 C15 6 19 6 22 12 C19 18 15 18 12 18 C9 18 5 18 2 12 Z"/>
                <circle cx="12" cy="12" r="3"/>
                <circle cx="12" cy="12" r="1" fill="currentColor"/>
            </svg>',

            /* 群组（Moodle） */
            'group' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="8" cy="8" r="2.5"/>
                <line x1="8" y1="10.5" x2="8" y2="18"/>
                <line x1="8" y1="13" x2="5" y2="15"/>
                <line x1="8" y1="13" x2="11" y2="15"/>
                <line x1="8" y1="18" x2="5" y2="22"/>
                <line x1="8" y1="18" x2="11" y2="22"/>
                <circle cx="17" cy="9" r="2"/>
                <line x1="17" y1="11" x2="17" y2="17"/>
                <line x1="17" y1="13" x2="14.5" y2="15"/>
                <line x1="17" y1="13" x2="19.5" y2="15"/>
            </svg>',

            /* 推荐星标 */
            'star' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3 L14.5 9 L21 9.5 L16 13.5 L17.5 20 L12 16.5 L6.5 20 L8 13.5 L3 9.5 L9.5 9 Z"/>
            </svg>',

            /* 下拉 */
            'dropdown' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"/>
            </svg>',

            /* 编辑（铅笔） */
            'edit' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 20 L5 16 L17 4 L20 7 L8 19 Z"/>
                <line x1="15" y1="6" x2="18" y2="9"/>
            </svg>',

            /* 设置（齿轮简化为四角） */
            'settings' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="3.5"/>
                <line x1="12" y1="2" x2="12" y2="6"/>
                <line x1="12" y1="18" x2="12" y2="22"/>
                <line x1="2" y1="12" x2="6" y2="12"/>
                <line x1="18" y1="12" x2="22" y2="12"/>
                <line x1="5" y1="5" x2="8" y2="8"/>
                <line x1="16" y1="16" x2="19" y2="19"/>
                <line x1="19" y1="5" x2="16" y2="8"/>
                <line x1="8" y1="16" x2="5" y2="19"/>
            </svg>',

            /* 打印 */
            'print' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="7" y="3" width="10" height="5"/>
                <rect x="4" y="8" width="16" height="9" rx="1"/>
                <rect x="7" y="14" width="10" height="7"/>
                <circle cx="17" cy="11" r="0.6" fill="currentColor"/>
            </svg>',

            /* 退出 / 电源 */
            'power' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <line x1="12" y1="3" x2="12" y2="11"/>
                <path d="M7 7 C4 9 4 14 7 17 C10 20 14 20 17 17 C20 14 20 9 17 7"/>
            </svg>',

            /* 邮件 */
            'mail' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="6" width="18" height="13" rx="1.5"/>
                <polyline points="3 8 12 14 21 8"/>
            </svg>',

            /* 气球（OI 比赛气球） */
            'golf-ball' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="9" r="6"/>
                <line x1="12" y1="15" x2="12" y2="20"/>
                <path d="M10 20 L14 20"/>
                <line x1="9" y1="6" x2="15" y2="6" stroke-dasharray="1.5 1.5"/>
            </svg>',

            /* 通用补充：搜索 */
            'search' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="10.5" cy="10.5" r="6"/>
                <line x1="15" y1="15" x2="20" y2="20"/>
            </svg>',

            /* 通用补充：日历 */
            'calendar' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="1.5"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <line x1="8" y1="3" x2="8" y2="7"/>
                <line x1="16" y1="3" x2="16" y2="7"/>
                <circle cx="8" cy="14" r="0.8" fill="currentColor"/>
                <circle cx="12" cy="14" r="0.8" fill="currentColor"/>
                <circle cx="16" cy="14" r="0.8" fill="currentColor"/>
            </svg>',

            /* 通用补充：信息 i */
            'info' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <line x1="12" y1="11" x2="12" y2="17"/>
                <circle cx="12" cy="7.5" r="0.8" fill="currentColor"/>
            </svg>',

            /* 通用补充：网格 th */
            'th' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="1.5"/>
                <line x1="9" y1="3" x2="9" y2="21"/>
                <line x1="15" y1="3" x2="15" y2="21"/>
                <line x1="3" y1="9" x2="21" y2="9"/>
                <line x1="3" y1="15" x2="21" y2="15"/>
            </svg>',

            /* 通用补充：rss */
            'rss' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 11 C10 11 13 14 13 20"/>
                <path d="M4 4 C15 4 20 9 20 20"/>
                <circle cx="5" cy="19" r="1.5" fill="currentColor"/>
            </svg>',

            /* 通用补充：文件 */
            'file' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 3 L15 3 L19 7 L19 21 L6 21 Z"/>
                <path d="M15 3 L15 7 L19 7"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="15" y2="16"/>
            </svg>',
            'file-alternate' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 3 L15 3 L19 7 L19 21 L6 21 Z"/>
                <path d="M15 3 L15 7 L19 7"/>
                <line x1="9" y1="13" x2="15" y2="13"/>
                <line x1="9" y1="17" x2="13" y2="17"/>
            </svg>',

            /* 通用补充：魔杖 */
            'magic' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <line x1="3" y1="21" x2="14" y2="10"/>
                <path d="M14 10 L17 7 L20 10 L17 13 Z"/>
                <line x1="3" y1="3" x2="3" y2="6"/>
                <line x1="1" y1="5" x2="5" y2="5"/>
                <line x1="20" y1="3" x2="20" y2="5"/>
                <line x1="19" y1="4" x2="21" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="22"/>
                <line x1="5" y1="21" x2="7" y2="21"/>
            </svg>',

            /* 通用补充：礼物 */
            'gift' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="9" width="18" height="12" rx="1"/>
                <rect x="2" y="6" width="20" height="4" rx="1"/>
                <line x1="12" y1="6" x2="12" y2="21"/>
                <path d="M12 6 C10 3 6 4 8 6 M12 6 C14 3 18 4 16 6"/>
            </svg>',

            /* 通用补充：钱币 */
            'money' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M9 9 C9 8 10 7 12 7 C14 7 15 8 15 9 C15 11 12 10 12 12 M12 12 C9 13 9 15 12 15 M12 12 C12 12 12 14 12 15 M12 12 C12 12 12 10 12 9"/>
                <line x1="12" y1="6" x2="12" y2="7"/>
                <line x1="12" y1="15" x2="12" y2="18"/>
            </svg>',

            /* 通用补充：用户 */
            'user' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 21 C4 16 8 14 12 14 C16 14 20 16 20 21"/>
            </svg>',

            /* 通用补充：锁 */
            'lock' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="5" y="11" width="14" height="10" rx="1.5"/>
                <path d="M8 11 L8 7 C8 4 10 3 12 3 C14 3 16 4 16 7 L16 11"/>
                <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>',

            /* 通用补充：信封 / 大信封 */
            'envelope' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="6" width="18" height="13" rx="1.5"/>
                <polyline points="3 8 12 14 21 8"/>
            </svg>',

            /* 通用补充：对勾 */
            'check' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="4 12 10 18 20 6"/>
            </svg>',

            /* 通用补充：圆圈 */
            'circle' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="8"/>
            </svg>',

            /* 通用补充：减号圆圈 */
            'minus-circle' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <line x1="7" y1="12" x2="17" y2="12"/>
            </svg>',

            /* 通用补充：加号 */
            'plus' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>',

            /* 通用补充：左右 chevron */
            'chevron-left' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="15 6 9 12 15 18"/>
            </svg>',
            'chevron-right' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="9 6 15 12 9 18"/>
            </svg>',

            /* 通用补充：fast-backward / fast-forward */
            'fast-backward' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polygon points="13 6 5 12 13 18 13 6"/>
                <line x1="19" y1="5" x2="19" y2="19"/>
            </svg>',
            'fast-forward' => '<svg class="stickman-nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polygon points="11 6 19 12 11 18 11 6"/>
                <line x1="5" y1="5" x2="5" y2="19"/>
            </svg>',
        );
        return isset($map[$name]) ? $map[$name] : '';
    }
}
?>
