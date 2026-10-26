/* hellokity 主题装饰脚本：飘落猫头 / 蝴蝶结 / 爱心 / 小爪印 + 右下角摇摆挂件
 * 性能：DPR 上限 2、resize 防抖、切后台暂停、prefers-reduced-motion 完全不生成。
 * 真实模板保留无障碍降级；预览页为演示已去掉 reduced-motion 检测。
 */
(function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var SVG_KITTY = '<svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M7 8.2 9.4 3.6 12 8.4Z" fill="#fff" stroke="#E89AB8" stroke-width="1" stroke-linejoin="round"/><path d="M12 8.4 15.6 3.6 18 8.2Z" fill="#fff" stroke="#E89AB8" stroke-width="1" stroke-linejoin="round"/><ellipse cx="12" cy="13.2" rx="9" ry="8.2" fill="#fff" stroke="#F2A9C4" stroke-width="1.2"/><circle cx="9.4" cy="12.4" r="1" fill="#9C2C52"/><circle cx="14.6" cy="12.4" r="1" fill="#9C2C52"/><ellipse cx="12" cy="14.2" rx="1.1" ry="0.8" fill="#FCD133"/><path d="M9.6 5.4 6.6 3.6 6.6 7.2Z" fill="#EE334E"/><path d="M9.6 5.4 12.6 3.6 12.6 7.2Z" fill="#EE334E"/><circle cx="9.6" cy="5.4" r="1.1" fill="#EE334E"/></svg>';
  var SVG_BOW = '<svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M12 12 4 7 4 17Z" fill="#EE334E"/><path d="M12 12 20 7 20 17Z" fill="#EE334E"/><circle cx="12" cy="12" r="3" fill="#F45D7A"/></svg>';
  var SVG_HEART = '<svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M12 21S3 14.5 3 8.8A4.8 4.8 0 0 1 12 6a4.8 4.8 0 0 1 9 2.8C21 14.5 12 21 12 21Z" fill="#F7B9CB" stroke="#EE8FB0" stroke-width="1"/></svg>';
  var SVG_PAW = '<svg viewBox="0 0 24 24" width="100%" height="100%"><ellipse cx="12" cy="15" rx="6" ry="5" fill="#F7B9CB"/><circle cx="7" cy="9" r="2" fill="#F7B9CB"/><circle cx="12" cy="7" r="2" fill="#F7B9CB"/><circle cx="17" cy="9" r="2" fill="#F7B9CB"/></svg>';
  var TYPES = [SVG_KITTY, SVG_BOW, SVG_HEART, SVG_PAW];

  function rnd(a, b) { return a + Math.random() * (b - a); }

  function spawn() {
    if (!document.body) return;
    var deco = document.getElementById('hellokity-decor');
    if (!deco) {
      // 兜底：极少数绕过 header.php 的页面也保证装饰层存在，避免静默失效
      deco = document.createElement('div');
      deco.id = 'hellokity-decor';
      document.body.insertBefore(deco, document.body.firstChild);
    }
    var COUNT = 18;
    deco.innerHTML = '';
    for (var i = 0; i < COUNT; i++) {
      var el = document.createElement('div');
      el.className = 'hk-sprite';
      var size = rnd(20, 40);
      el.style.left = rnd(0, 96) + 'vw';
      el.style.width = size + 'px';
      el.style.height = size + 'px';
      el.style.setProperty('--hk-rot', rnd(-30, 30).toFixed(0) + 'deg');
      el.style.setProperty('--hk-op', rnd(0.35, 0.6).toFixed(2));
      // 全程负延迟：加载瞬间所有精灵已分布在下落途中，避免正延迟期间静止贴在顶部造成的可见跳变
      var dur = rnd(11, 19);
      el.style.animation = 'hk-fall ' + dur.toFixed(1) + 's linear ' + (-rnd(0.4, dur)).toFixed(1) + 's infinite';
      el.innerHTML = TYPES[i % TYPES.length];
      deco.appendChild(el);
    }
  }

  function mountBadge() {
    if (!document.body) return;
    if (document.getElementById('hellokity-badge')) return;
    var b = document.createElement('div');
    b.id = 'hellokity-badge';
    b.setAttribute('aria-hidden', 'true');
    b.innerHTML = '<svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M7 8.2 9.4 3.6 12 8.4Z" fill="#fff" stroke="#000" stroke-width="1.1" stroke-linejoin="round"/><path d="M12 8.4 15.6 3.6 18 8.2Z" fill="#fff" stroke="#000" stroke-width="1.1" stroke-linejoin="round"/><ellipse cx="12" cy="13.2" rx="9" ry="8.2" fill="#fff" stroke="#000" stroke-width="1.2"/><circle cx="9.4" cy="12.4" r="1" fill="#000"/><circle cx="14.6" cy="12.4" r="1" fill="#000"/><ellipse cx="12" cy="14.2" rx="1.1" ry="0.8" fill="#FCD133" stroke="#000" stroke-width="0.6"/><path d="M9.6 5.4 6.6 3.6 6.6 7.2Z" fill="#EE334E" stroke="#000" stroke-width="0.7" stroke-linejoin="round"/><path d="M9.6 5.4 12.6 3.6 12.6 7.2Z" fill="#EE334E" stroke="#000" stroke-width="0.7" stroke-linejoin="round"/><circle cx="9.6" cy="5.4" r="1.1" fill="#EE334E" stroke="#000" stroke-width="0.7"/></svg>';
    document.body.appendChild(b);
  }

  spawn();
  mountBadge();

  // 切后台暂停（省电）
  document.addEventListener('visibilitychange', function () {
    var deco = document.getElementById('hellokity-decor');
    if (!deco) return;
    if (document.hidden) deco.style.display = 'none';
    else { deco.style.display = ''; spawn(); }
  });
})();
