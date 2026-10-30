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


/* ===== 主题互动层：背景飘落物 + 可交互小精灵 =====
 * 背景层用 z-index:-1 配合 body isolation，始终位于正文和卡片后方，不遮挡阅读；
 * 小精灵固定在页面角落留白处，只有图标本身接收点击，不拦截滚动与选字。
 * 尊重 prefers-reduced-motion；切到后台自动暂停。
 */
(function () {
  'use strict';
  if (typeof window === 'undefined' || typeof document === 'undefined') return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  window.OJ_THEME_FX = window.OJ_THEME_FX || {};
  if (window.OJ_THEME_FX.backrooms) return;
  window.OJ_THEME_FX.backrooms = true;

  var NS = 'backrooms';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "left",
  "glow": "drop-shadow(0 2px 5px rgba(43,38,32,.35))",
  "bubble": "background:#F4EAC6;border-color:#2B2620;color:#2B2620;box-shadow:0 6px 16px rgba(43,38,32,.3);",
  "lines": [
    "你 noclip 到这里了…",
    "这里是 Level 0",
    "要喝杏仁水吗？",
    "灯管又闪了…",
    "别走太远，墙纸会变"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 2l3 4 4 1 2 4-2 4 1 4-4 2-4 2-4-2-4-2 1-4-2-4 2-4 4-1z\" fill=\"#EDE0BE\" stroke=\"#C2A05A\" stroke-width=\"1.1\" opacity=\"0.95\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 2v20M2 12h20M5 5l14 14M19 5L5 19\" stroke=\"#F5D76E\" stroke-width=\"1.1\" stroke-linecap=\"round\" opacity=\"0.8\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><rect x=\"3\" y=\"3\" width=\"18\" height=\"18\" rx=\"2\" fill=\"none\" stroke=\"#C2A05A\" stroke-width=\"1.2\" opacity=\"0.8\"/><path d=\"M3 14l6-6 5 5 3-3 4 4\" fill=\"none\" stroke=\"#C2A05A\" stroke-width=\"1.2\"/></svg>"
  ],
  "flakeColors": null,
  "flakeCount": 12,
  "flakeMin": 5,
  "flakeMax": 12,
  "flakeOpacity": [
    0.2,
    0.45
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><defs><radialGradient id=\"br-smiler\" cx=\"50%\" cy=\"45%\" r=\"62%\"><stop offset=\"0\" stop-color=\"#3a332b\"/><stop offset=\"1\" stop-color=\"#100e0c\"/></radialGradient></defs><ellipse cx=\"32\" cy=\"34\" rx=\"23\" ry=\"25\" fill=\"url(#br-smiler)\" stroke=\"#2B2620\" stroke-width=\"2\"/><g fill=\"#FFFDF2\"><circle cx=\"23\" cy=\"28\" r=\"3.2\" data-gaze=\"1\"/><circle cx=\"41\" cy=\"28\" r=\"3.2\" data-gaze=\"1\"/></g><path d=\"M20 38q12 12 24 0\" stroke=\"#FFFDF2\" stroke-width=\"3.2\" fill=\"none\" stroke-linecap=\"round\"/><path d=\"M25 40v6M32 42v6M39 40v6\" stroke=\"#100e0c\" stroke-width=\"1.6\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><circle cx=\"16\" cy=\"18\" r=\"9\" fill=\"#FFFDF2\" opacity=\"0.92\"/><circle cx=\"13\" cy=\"16\" r=\"1.2\" fill=\"#2B2620\"/><circle cx=\"19\" cy=\"16\" r=\"1.2\" fill=\"#2B2620\"/><path d=\"M14 21q2 1.4 4 0\" stroke=\"#2B2620\" stroke-width=\"1\" fill=\"none\"/><path d=\"M16 2v4M12 4l1 3M20 4l-1 3\" stroke=\"#F5D76E\" stroke-width=\"1.2\" stroke-linecap=\"round\"/></svg>"
};

  function rand(a, b) { return a + Math.random() * (b - a); }
  function pick(list) { return list[(Math.random() * list.length) | 0]; }
  function onReady(fn) {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
    else fn();
  }

  function injectCss() {
    if (document.getElementById(NS + '-css')) return;
    var css = [
      'body{isolation:isolate}',
      '#' + SNOW_ID + '{position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:-1}',
      '.' + NS + '-snowflake{position:absolute;top:-10vh;display:block;opacity:0;will-change:transform,opacity;animation:' + NS + '-fall var(--d) linear var(--dl) infinite}',
      '.' + NS + '-snowflake svg{display:block;width:100%;height:100%;overflow:visible}',
      '@keyframes ' + NS + '-fall{0%{transform:translate3d(0,-12vh,0) rotate(0deg);opacity:0}6%{opacity:var(--o)}80%{opacity:var(--o)}100%{transform:translate3d(var(--x),112vh,0) rotate(var(--r));opacity:0}}',
      '#' + PET_ID + '{position:fixed;right:14px;bottom:12px;width:78px;height:78px;z-index:99997;pointer-events:none;user-select:none;-webkit-user-select:none}',
      '#' + PET_ID + '[data-side="left"]{right:auto;left:14px}',
      '.' + NS + '-main{position:absolute;right:2px;bottom:0;width:60px;height:60px;pointer-events:auto;cursor:pointer;transform-origin:50% 100%;animation:' + NS + '-idle 3.8s ease-in-out infinite;filter:' + CFG.glow + '}',
      '#' + PET_ID + '[data-side="left"] .' + NS + '-main{right:auto;left:2px}',
      '.' + NS + '-main:hover{animation:' + NS + '-hop .55s ease}',
      '.' + NS + '-main.is-pop{animation:' + NS + '-pop .8s ease}',
      '.' + NS + '-main svg,.' + NS + '-buddy svg{display:block;width:100%;height:100%;overflow:visible}',
      '.' + NS + '-main [data-gaze],.' + NS + '-buddy [data-gaze]{transition:transform .12s linear;transform-box:fill-box;transform-origin:center}',
      '.' + NS + '-main [data-eyes]{transform-box:fill-box;transform-origin:center;transition:transform .1s}',
      '.' + NS + '-main.is-blink [data-eyes]{transform:scaleY(.12)}',
      '.' + NS + '-buddy{position:absolute;left:-4px;bottom:2px;width:28px;height:28px;pointer-events:auto;cursor:pointer;animation:' + NS + '-bob 2.7s ease-in-out infinite;filter:' + CFG.glow + '}',
      '#' + PET_ID + '[data-side="left"] .' + NS + '-buddy{left:auto;right:-4px}',
      '.' + NS + '-bubble{position:absolute;bottom:70px;right:0;width:max-content;max-width:200px;padding:7px 11px;border:2px solid;border-radius:12px;font-size:12px;font-weight:700;line-height:1.4;text-align:center;white-space:normal;opacity:0;transform:translateY(6px);transition:opacity .18s ease,transform .18s ease;pointer-events:none;z-index:2;' + CFG.bubble + '}',
      '#' + PET_ID + '[data-side="left"] .' + NS + '-bubble{right:auto;left:0}',
      '.' + NS + '-bubble.is-show{opacity:1;transform:translateY(0)}',
      '.' + NS + '-bubble::after{content:"";position:absolute;bottom:-7px;right:22px;border:7px solid transparent;border-bottom:0;border-top-color:inherit}',
      '#' + PET_ID + '[data-side="left"] .' + NS + '-bubble::after{right:auto;left:22px}',
      '#' + BURST_ID + '{position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:99998}',
      '.' + NS + '-bit{position:absolute;width:10px;height:10px;margin:-5px 0 0 -5px;will-change:transform,opacity;animation:' + NS + '-burst .9s cubic-bezier(.15,.65,.4,1) forwards}',
      '.' + NS + '-bit svg{display:block;width:100%;height:100%}',
      '@keyframes ' + NS + '-burst{0%{transform:translate(0,0) rotate(0deg) scale(.5);opacity:1}100%{transform:translate(var(--bx),var(--by)) rotate(var(--rz)) scale(1.05);opacity:0}}',
      '@keyframes ' + NS + '-idle{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-4px) rotate(-1.5deg)}}',
      '@keyframes ' + NS + '-hop{0%,100%{transform:translateY(0) rotate(0deg)}35%{transform:translateY(-11px) rotate(-4deg)}70%{transform:translateY(0) rotate(3deg)}}',
      '@keyframes ' + NS + '-pop{0%{transform:scale(1)}22%{transform:scale(1.16,.9)}48%{transform:translateY(-13px) scale(.96,1.06)}72%{transform:translateY(0) scale(1.06,.95)}100%{transform:scale(1)}}',
      '@keyframes ' + NS + '-bob{0%,100%{transform:translateY(0) rotate(-4deg)}50%{transform:translateY(-5px) rotate(6deg)}}',
      '@media (max-width:1199px){#' + PET_ID + '{width:66px;height:66px}.' + NS + '-main{width:52px;height:52px}.' + NS + '-buddy{width:24px;height:24px}.' + NS + '-bubble{bottom:60px}}',
      '@media (max-width:768px){#' + PET_ID + '{width:60px;height:60px;right:8px;bottom:8px}#' + PET_ID + '[data-side="left"]{left:8px}.' + NS + '-main{width:48px;height:48px}.' + NS + '-buddy{width:22px;height:22px}.' + NS + '-bubble{bottom:56px;max-width:160px;font-size:11px;padding:6px 9px}}',
      '@media print{#' + SNOW_ID + ',#' + PET_ID + ',#' + BURST_ID + '{display:none!important}}',
      '@media (prefers-reduced-motion:reduce){#' + SNOW_ID + ',#' + PET_ID + ',#' + BURST_ID + '{display:none!important}}'
    ].join('\n');
    var style = document.createElement('style');
    style.id = NS + '-css';
    style.textContent = css;
    document.head.appendChild(style);
  }

  function mountSnow() {
    if (!CFG.flakeCount || document.getElementById(SNOW_ID)) return;
    var layer = document.createElement('div');
    layer.id = SNOW_ID;
    layer.setAttribute('aria-hidden', 'true');
    for (var i = 0; i < CFG.flakeCount; i++) {
      var flake = document.createElement('span');
      flake.className = NS + '-snowflake';
      var size = rand(CFG.flakeMin, CFG.flakeMax);
      flake.style.left = rand(0, 100).toFixed(2) + 'vw';
      flake.style.width = size.toFixed(1) + 'px';
      flake.style.height = size.toFixed(1) + 'px';
      flake.style.setProperty('--d', rand(12, 26).toFixed(1) + 's');
      flake.style.setProperty('--dl', (-rand(0, 26)).toFixed(1) + 's');
      flake.style.setProperty('--x', rand(-48, 48).toFixed(0) + 'px');
      flake.style.setProperty('--r', rand(180, 540).toFixed(0) + 'deg');
      flake.style.setProperty('--o', rand(CFG.flakeOpacity[0], CFG.flakeOpacity[1]).toFixed(2));
      if (CFG.flakeColors) flake.style.color = pick(CFG.flakeColors);
      flake.innerHTML = pick(CFG.flakes);
      layer.appendChild(flake);
    }
    document.body.appendChild(layer);
  }

  function mountPet() {
    if (document.getElementById(PET_ID)) return;
    var box = document.createElement('div');
    box.id = PET_ID;
    box.setAttribute('data-side', CFG.side);
    box.setAttribute('aria-hidden', 'true');
    box.innerHTML =
      '<div class="' + NS + '-bubble" id="' + NS + '-bubble"></div>' +
      '<div class="' + NS + '-main" id="' + NS + '-main" title="点我互动">' + CFG.pet + '</div>' +
      '<div class="' + NS + '-buddy" id="' + NS + '-buddy" title="点我互动">' + CFG.buddy + '</div>';
    document.body.appendChild(box);

    var main = document.getElementById(NS + '-main');
    var buddy = document.getElementById(NS + '-buddy');
    var bubble = document.getElementById(NS + '-bubble');
    var sayTimer = null;

    function say() {
      bubble.textContent = pick(CFG.lines);
      bubble.classList.add('is-show');
      clearTimeout(sayTimer);
      sayTimer = setTimeout(function () { bubble.classList.remove('is-show'); }, 2600);
    }

    function burst() {
      var layer = document.getElementById(BURST_ID);
      if (!layer) {
        layer = document.createElement('div');
        layer.id = BURST_ID;
        layer.setAttribute('aria-hidden', 'true');
        document.body.appendChild(layer);
      }
      var rect = main.getBoundingClientRect();
      var cx = rect.left + rect.width / 2;
      var cy = rect.top + rect.height / 2;
      for (var i = 0; i < 10; i++) {
        var bit = document.createElement('span');
        bit.className = NS + '-bit';
        bit.style.left = cx + 'px';
        bit.style.top = cy + 'px';
        var size = rand(7, 14);
        bit.style.width = size + 'px';
        bit.style.height = size + 'px';
        bit.style.setProperty('--bx', rand(-80, 80).toFixed(0) + 'px');
        bit.style.setProperty('--by', rand(-90, -20).toFixed(0) + 'px');
        bit.style.setProperty('--rz', rand(-200, 200).toFixed(0) + 'deg');
        if (CFG.flakeColors) bit.style.color = pick(CFG.flakeColors);
        bit.innerHTML = pick(CFG.flakes);
        layer.appendChild(bit);
        (function (node) {
          setTimeout(function () { if (node.parentNode) node.parentNode.removeChild(node); }, 1000);
        })(bit);
      }
    }

    function react(ev) {
      if (ev) {
        ev.preventDefault();
        ev.stopPropagation();
      }
      main.classList.remove('is-pop');
      void main.offsetWidth;
      main.classList.add('is-pop');
      say();
      burst();
    }

    main.addEventListener('click', react);
    buddy.addEventListener('click', react);
    main.addEventListener('animationend', function (e) {
      if (e.animationName === NS + '-pop') main.classList.remove('is-pop');
    });

    var pending = false, px = 0, py = 0;
    function look(x, y) {
      var rect = main.getBoundingClientRect();
      var dx = Math.max(-2, Math.min(2, (x - rect.left - rect.width / 2) / 45));
      var dy = Math.max(-2, Math.min(2, (y - rect.top - rect.height / 2) / 45));
      var eyes = main.querySelectorAll('[data-gaze]');
      for (var i = 0; i < eyes.length; i++) {
        eyes[i].style.transform = 'translate(' + dx.toFixed(2) + 'px,' + dy.toFixed(2) + 'px)';
      }
    }
    window.addEventListener('pointermove', function (e) {
      px = e.clientX;
      py = e.clientY;
      if (!pending) {
        pending = true;
        var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); };
        raf(function () {
          pending = false;
          look(px, py);
        });
      }
    }, { passive: true });

    function blink() {
      if (!document.hidden) {
        main.classList.add('is-blink');
        setTimeout(function () { main.classList.remove('is-blink'); }, 130);
      }
      setTimeout(blink, rand(2800, 7200));
    }
    setTimeout(blink, rand(2200, 5200));

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        clearTimeout(sayTimer);
        bubble.classList.remove('is-show');
      }
    });
  }

  onReady(function () {
    injectCss();
    mountSnow();
    mountPet();
    document.addEventListener('visibilitychange', function () {
      var layer = document.getElementById(SNOW_ID);
      if (layer) layer.style.display = document.hidden ? 'none' : '';
    });
  });
})();
