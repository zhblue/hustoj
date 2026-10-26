/* backrooms 模板 · 装饰脚本
 * 氛围装饰（不飘落，贴合后室）：
 *   ① 荧光灯闪烁（顶部叠加层，偶尔明灭，模拟坏灯管）
 *   ② 飘浮尘埃 / 光斑（z-index:-1，透过玻璃卡可见）
 *   ③ 偶发墙纸 glitch 错位（no-clip 失真感，z-index:-1）
 *   ④ 右下角荧光灯板挂件（轻微晃动）
 * 光敏性硬合规：prefers-reduced-motion 下完全不生成任何动态装饰。
 */
(function () {
  'use strict';

  // 光敏性用户：完全关闭所有动态装饰（仅静态墙纸 + CSS 暗角保留）
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  function rnd(a, b) { return a + Math.random() * (b - a); }

  // 容器兜底：任何绕过 header.php 的页面也不会静默失效
  function ensureDeco() {
    if (!document.body) return null;
    var deco = document.getElementById('backrooms-decor');
    if (!deco) {
      deco = document.createElement('div');
      deco.id = 'backrooms-decor';
      deco.setAttribute('aria-hidden', 'true');
      document.body.insertBefore(deco, document.body.firstChild);
    }
    return deco;
  }

  function spawn() {
    var deco = ensureDeco();
    if (!deco) return;

    // ② 飘浮尘埃 / 光斑：18 个，全程负延迟（加载即均匀分布于上升途中）
    var DUST = 18;
    for (var i = 0; i < DUST; i++) {
      var d = document.createElement('div');
      d.className = 'br-dust';
      var size = rnd(4, 11);
      d.style.left = rnd(0, 100).toFixed(2) + 'vw';
      d.style.width = size.toFixed(1) + 'px';
      d.style.height = size.toFixed(1) + 'px';
      d.style.setProperty('--br-op', rnd(0.45, 0.8).toFixed(2));
      d.style.setProperty('--br-dx', rnd(-22, 22).toFixed(0) + 'px');
      var dur = rnd(15, 27);
      d.style.animation = 'br-dust ' + dur.toFixed(1) + 's linear ' + (-rnd(0.4, dur)).toFixed(1) + 's infinite';
      deco.appendChild(d);
    }

    // ③ 偶发 glitch 错位：5 块，全程负延迟
    var GLITCH = 5;
    for (var j = 0; j < GLITCH; j++) {
      var g = document.createElement('div');
      g.className = 'br-glitch';
      g.style.left = rnd(2, 88).toFixed(2) + 'vw';
      g.style.top = rnd(4, 88).toFixed(2) + 'vh';
      g.style.width = rnd(28, 64).toFixed(0) + 'px';
      g.style.height = rnd(10, 26).toFixed(0) + 'px';
      var gdur = rnd(7, 13);
      g.style.animation = 'br-glitch ' + gdur.toFixed(1) + 's steps(1, end) ' + (-rnd(0.4, gdur)).toFixed(1) + 's infinite';
      deco.appendChild(g);
    }
  }

  // ① 荧光灯闪烁叠加层（内容之上才可见）
  function mountFlicker() {
    if (!document.body) return;
    if (document.getElementById('backrooms-flicker')) return;
    var f = document.createElement('div');
    f.id = 'backrooms-flicker';
    f.setAttribute('aria-hidden', 'true');
    document.body.appendChild(f);
    function burst() {
      f.classList.add('br-flick');
      window.setTimeout(function () { f.classList.remove('br-flick'); }, 460);
    }
    f.addEventListener('animationend', function () { f.classList.remove('br-flick'); });
    function schedule() { window.setTimeout(function () { burst(); schedule(); }, rnd(3500, 9000)); }
    schedule();
  }

  // ④ 右下角荧光灯板挂件
  function mountBadge() {
    if (!document.body) return;
    if (document.getElementById('backrooms-badge')) return;
    var b = document.createElement('div');
    b.id = 'backrooms-badge';
    b.setAttribute('aria-hidden', 'true');
    b.innerHTML = '<svg viewBox="0 0 56 56" width="56" height="56">' +
      '<rect x="8" y="10" width="40" height="14" rx="3" fill="#FFFDF2" stroke="#2B2620" stroke-width="2"/>' +
      '<line x1="14" y1="14" x2="42" y2="14" stroke="#F5D76E" stroke-width="3" stroke-linecap="round"/>' +
      '<line x1="14" y1="20" x2="42" y2="20" stroke="#F5D76E" stroke-width="3" stroke-linecap="round"/>' +
      '<rect x="20" y="24" width="16" height="6" rx="1" fill="#C2A05A" stroke="#2B2620" stroke-width="1.5"/>' +
      '<line x1="28" y1="30" x2="28" y2="44" stroke="#2B2620" stroke-width="2"/>' +
      '<circle cx="28" cy="46" r="3" fill="#C0392B"/>' +
      '</svg>';
    document.body.appendChild(b);
  }

  function init() {
    spawn();
    mountFlicker();
    mountBadge();
  }

  // 切后台暂停：用 visibilitychange 控制动画开关
  var running = true;
  function onVis() {
    var deco = document.getElementById('backrooms-decor');
    if (!deco) return;
    var nodes = deco.querySelectorAll('.br-dust, .br-glitch');
    if (document.hidden) {
      if (running) { for (var i = 0; i < nodes.length; i++) nodes[i].style.animationPlayState = 'paused'; running = false; }
    } else {
      if (!running) { for (var k = 0; k < nodes.length; k++) nodes[k].style.animationPlayState = 'running'; running = true; }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  document.addEventListener('visibilitychange', onVis);
})();
