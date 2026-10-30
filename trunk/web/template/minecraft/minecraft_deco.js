/* minecraft 模板 · 装饰层：落方块粒子（草/泥土/石头/钻石/金/红石/熔岩）
 * 挂在 #minecraft-decor 容器上，纯 CSS 动画，pointer-events:none 不挡操作。 */
(function () {
  var box = document.getElementById('minecraft-decor');
  if (!box && document.body) {
    box = document.createElement('div');
    box.id = 'minecraft-decor';
    box.setAttribute('aria-hidden', 'true');
    document.body.appendChild(box);
  }
  if (!box) return;
  var colors = ['#6abe30', '#7a5230', '#9a9a9a', '#c8c8c8', '#3ad6e0', '#facc2e', '#b02e26', '#f08030'];
  var count = 26;
  for (var i = 0; i < count; i++) {
    var b = document.createElement('div');
    b.className = 'minecraft-block';
    b.style.background = colors[Math.floor(Math.random() * colors.length)];
    b.style.left = (Math.random() * 100) + 'vw';
    var size = 8 + Math.floor(Math.random() * 14);
    b.style.width = size + 'px';
    b.style.height = size + 'px';
    b.style.animationDuration = (6 + Math.random() * 8) + 's';
    b.style.animationDelay = (-Math.random() * 14) + 's';
    box.appendChild(b);
  }
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
  if (window.OJ_THEME_FX.minecraft) return;
  window.OJ_THEME_FX.minecraft = true;

  var NS = 'minecraft';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "right",
  "glow": "drop-shadow(2px 2px 0 rgba(0,0,0,.35))",
  "bubble": "background:#1b1b1b;border-color:#6abe30;color:#e8e8e8;box-shadow:0 4px 0 rgba(0,0,0,.55);font-family:'Courier New',monospace;",
  "lines": [
    "Sss… 要一起挖矿吗？",
    "钻石在脚下！",
    "小心背后！",
    "别炸我，我只是装饰",
    "先撸树，再 AC"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 16 16\" xmlns=\"http://www.w3.org/2000/svg\" shape-rendering=\"crispEdges\"><g fill=\"#e8fbff\"><rect x=\"7\" y=\"0\" width=\"2\" height=\"16\"/><rect x=\"0\" y=\"7\" width=\"16\" height=\"2\"/><rect x=\"2\" y=\"2\" width=\"2\" height=\"2\"/><rect x=\"12\" y=\"2\" width=\"2\" height=\"2\"/><rect x=\"2\" y=\"12\" width=\"2\" height=\"2\"/><rect x=\"12\" y=\"12\" width=\"2\" height=\"2\"/><rect x=\"4\" y=\"4\" width=\"2\" height=\"2\"/><rect x=\"10\" y=\"10\" width=\"2\" height=\"2\"/><rect x=\"10\" y=\"4\" width=\"2\" height=\"2\"/><rect x=\"4\" y=\"10\" width=\"2\" height=\"2\"/></g><rect x=\"7\" y=\"7\" width=\"2\" height=\"2\" fill=\"#3ad6e0\"/></svg>",
    "<svg viewBox=\"0 0 16 16\" xmlns=\"http://www.w3.org/2000/svg\" shape-rendering=\"crispEdges\"><rect x=\"2\" y=\"2\" width=\"12\" height=\"12\" fill=\"#dff0ff\"/><rect x=\"4\" y=\"0\" width=\"8\" height=\"2\" fill=\"#e8fbff\"/><rect x=\"4\" y=\"14\" width=\"8\" height=\"2\" fill=\"#e8fbff\"/><rect x=\"0\" y=\"4\" width=\"2\" height=\"8\" fill=\"#e8fbff\"/><rect x=\"14\" y=\"4\" width=\"2\" height=\"8\" fill=\"#e8fbff\"/><rect x=\"6\" y=\"6\" width=\"4\" height=\"4\" fill=\"#3ad6e0\"/></svg>",
    "<svg viewBox=\"0 0 16 16\" xmlns=\"http://www.w3.org/2000/svg\" shape-rendering=\"crispEdges\"><rect x=\"4\" y=\"4\" width=\"8\" height=\"8\" fill=\"#facc2e\"/><rect x=\"6\" y=\"6\" width=\"4\" height=\"4\" fill=\"#ffe066\"/><rect x=\"0\" y=\"6\" width=\"2\" height=\"4\" fill=\"#e8fbff\"/><rect x=\"14\" y=\"6\" width=\"2\" height=\"4\" fill=\"#e8fbff\"/></svg>"
  ],
  "flakeColors": null,
  "flakeCount": 18,
  "flakeMin": 8,
  "flakeMax": 16,
  "flakeOpacity": [
    0.35,
    0.6
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\" shape-rendering=\"crispEdges\"><rect x=\"8\" y=\"6\" width=\"48\" height=\"52\" rx=\"2\" fill=\"#6abe30\"/><rect x=\"8\" y=\"6\" width=\"48\" height=\"8\" fill=\"#7fcf3e\"/><g fill=\"#0e1a0c\"><rect x=\"18\" y=\"20\" width=\"10\" height=\"10\"/><rect x=\"36\" y=\"20\" width=\"10\" height=\"10\"/><rect x=\"26\" y=\"32\" width=\"4\" height=\"8\"/><rect x=\"36\" y=\"32\" width=\"4\" height=\"8\"/><rect x=\"30\" y=\"38\" width=\"12\" height=\"8\"/><rect x=\"24\" y=\"42\" width=\"8\" height=\"12\"/><rect x=\"36\" y=\"42\" width=\"8\" height=\"12\"/></g><rect x=\"14\" y=\"10\" width=\"6\" height=\"4\" fill=\"#8fe04a\" opacity=\"0.7\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\" shape-rendering=\"crispEdges\"><rect x=\"6\" y=\"9\" width=\"20\" height=\"19\" fill=\"#eef7ff\"/><rect x=\"10\" y=\"4\" width=\"12\" height=\"6\" fill=\"#dff0ff\"/><rect x=\"11\" y=\"15\" width=\"3\" height=\"3\" fill=\"#1b1b1b\"/><rect x=\"18\" y=\"15\" width=\"3\" height=\"3\" fill=\"#1b1b1b\"/><rect x=\"14\" y=\"21\" width=\"4\" height=\"2\" fill=\"#1b1b1b\"/><rect x=\"9\" y=\"12\" width=\"14\" height=\"2\" fill=\"#c8f0ff\"/></svg>"
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
