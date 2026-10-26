/* ============================================================
   HUSTOJ · stickman 模板 · 装饰动画
   在 #stickman-decor 容器里动态生成多个小火柴人，
   缓慢下沉 + 旋转，模拟"作业飘在风里"的氛围。
   不影响任何业务功能。纯装饰，pointer-events: none。
   ============================================================ */
(function () {
    if (typeof window === 'undefined') return;
    var host = document.getElementById('stickman-decor');
    if (!host) return;

    // 9 个不同姿态的迷你火柴人（旋转+位置不同）
    var poses = [
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="11" x2="6" y2="16"/><line x1="12" y1="11" x2="18" y2="14"/><line x1="12" y1="20" x2="8" y2="28"/><line x1="12" y1="20" x2="16" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="11" x2="18" y2="16"/><line x1="12" y1="11" x2="6" y2="14"/><line x1="12" y1="20" x2="9" y2="28"/><line x1="12" y1="20" x2="15" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><path d="M12 11 Q6 13 4 18"/><path d="M12 11 Q18 13 20 18"/><line x1="12" y1="20" x2="8" y2="28"/><line x1="12" y1="20" x2="16" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="12" x2="20" y2="6"/><line x1="12" y1="12" x2="4" y2="14"/><line x1="12" y1="20" x2="7" y2="28"/><line x1="12" y1="20" x2="17" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="11" x2="8" y2="8"/><line x1="12" y1="11" x2="16" y2="8"/><line x1="12" y1="20" x2="6" y2="28"/><line x1="12" y1="20" x2="18" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="12" x2="6" y2="18"/><line x1="12" y1="12" x2="18" y2="18"/><line x1="12" y1="20" x2="9" y2="30"/><line x1="12" y1="20" x2="15" y2="30"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="11" x2="16" y2="18"/><line x1="12" y1="11" x2="6" y2="12"/><line x1="12" y1="20" x2="10" y2="28"/><line x1="12" y1="20" x2="16" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><line x1="12" y1="11" x2="8" y2="16"/><line x1="12" y1="11" x2="18" y2="10"/><line x1="12" y1="20" x2="8" y2="28"/><line x1="12" y1="20" x2="14" y2="28"/></g></svg>',
        '<svg viewBox="0 0 24 32"><g><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="20"/><path d="M12 11 L8 14"/><path d="M12 11 L16 14"/><line x1="12" y1="20" x2="8" y2="28"/><line x1="12" y1="20" x2="16" y2="28"/></g></svg>'
    ];

    // 调色：和 CSS 主题色一致
    var colors = ['#D85A30', '#185FA5', '#1D9E75', '#EF9F27', '#534AB7', '#181818'];

    var COUNT = 14; // 不要太多，避免视觉噪音
    var w = window.innerWidth;

    for (var i = 0; i < COUNT; i++) {
        var el = document.createElement('div');
        el.className = 'stickman-flake t' + ((i % 3) + 1);
        var pose = poses[i % poses.length];
        var color = colors[i % colors.length];
        el.style.left = (Math.random() * 100) + 'vw';
        el.style.color = color;
        el.style.animationDelay = (-Math.random() * 60) + 's';
        el.style.transform = 'scale(' + (0.6 + Math.random() * 0.6) + ')';
        el.innerHTML = pose;
        host.appendChild(el);
    }
})();
