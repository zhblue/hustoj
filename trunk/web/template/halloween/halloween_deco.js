/* halloween 模板 · 装饰层动画：飘落卡通幽灵 / 蝙蝠 / 南瓜灯
 * 挂在 #halloween-decor 上；尊重 prefers-reduced-motion 自动关闭。 */
(function () {
  var deco = document.getElementById('halloween-decor');
  if (!deco && document.body) {
    deco = document.createElement('div');
    deco.id = 'halloween-decor';
    deco.setAttribute('aria-hidden', 'true');
    document.body.appendChild(deco);
  }
  if (!deco) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var ghost =
    '<svg viewBox="0 0 40 48" width="34" height="40" aria-hidden="true">' +
    '<path d="M20 4 a16 16 0 0 1 16 16 v18 l-4 -4 -4 4 -4 -4 -4 4 -4 -4 -4 4 V20 a16 16 0 0 1 16 -16 z" fill="#fff3e0"/>' +
    '<circle cx="14" cy="20" r="2.6" fill="#561d9e"/>' +
    '<circle cx="26" cy="20" r="2.6" fill="#561d9e"/>' +
    '<path d="M15 28 q5 4 10 0" stroke="#561d9e" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';

  var bat =
    '<svg viewBox="0 0 44 28" width="34" height="22" aria-hidden="true">' +
    '<path d="M22 6 q-4 -6 -10 -4 q4 -2 7 1 q-6 -1 -9 4 q5 0 6 3 q-3 1 -1 4 q3 -2 4 -1 q-1 3 1 4 q2 -3 4 -1 q2 -2 4 1 q2 -3 1 -4 q3 -1 4 -4 q1 -3 -9 -4 q3 -3 7 -1 q-6 -2 -10 4 z" fill="#7b3ff2"/>' +
    '<circle cx="20" cy="10" r="1.4" fill="#fff3e0"/>' +
    '<circle cx="24" cy="10" r="1.4" fill="#fff3e0"/></svg>';

  var pumpkin =
    '<svg viewBox="0 0 36 34" width="30" height="28" aria-hidden="true">' +
    '<ellipse cx="18" cy="20" rx="14" ry="11" fill="#ff8c1a"/>' +
    '<path d="M18 20 v-9 M10 20 a8 8 0 0 0 0 -6 M26 20 a8 8 0 0 1 0 -6" stroke="#e8590c" stroke-width="1.4" fill="none"/>' +
    '<rect x="16.5" y="6" width="3" height="5" rx="1.5" fill="#39c94a"/>' +
    '<path d="M12 21 l2.5 -2 1.8 1.6 -1.4 1.6 z" fill="#fff3e0"/>' +
    '<path d="M24 21 l-2.5 -2 -1.8 1.6 1.4 1.6 z" fill="#fff3e0"/></svg>';

  var kinds = [ghost, bat, pumpkin];

  function spawn() {
    var el = document.createElement('div');
    el.className = 'hw-sprite';
    el.innerHTML = kinds[Math.floor(Math.random() * kinds.length)];
    var svg = el.firstChild;
    var size = 0.7 + Math.random() * 0.9;
    el.style.left = (Math.random() * 100) + 'vw';
    var dur = 9 + Math.random() * 8;
    var delay = Math.random() * 4;
    el.style.opacity = '0';
    svg.style.width = (30 * size) + 'px';
    svg.style.height = 'auto';
    el.style.animation =
      'hw-wobble ' + (2 + Math.random() * 2) + 's ease-in-out infinite, ' +
      'hw-fall ' + dur + 's linear ' + delay + 's forwards';
    deco.appendChild(el);
    setTimeout(function () {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, (dur + delay) * 1000 + 600);
  }

  for (var i = 0; i < 14; i++) {
    (function (d) { setTimeout(spawn, d * 700); })(i);
  }
  setInterval(spawn, 1400);
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
  if (window.OJ_THEME_FX.halloween) return;
  window.OJ_THEME_FX.halloween = true;

  var NS = 'halloween';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "right",
  "glow": "drop-shadow(0 0 7px rgba(255,140,26,.45))",
  "bubble": "background:#2a1450;border-color:#ff8c1a;color:#fff3e0;box-shadow:0 6px 20px rgba(0,0,0,.45);",
  "lines": [
    "不给糖就捣蛋！",
    "Trick or Treat～",
    "南瓜灯已点亮",
    "今晚也要 AC 哦",
    "女巫的扫帚借你"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><g stroke=\"#ff8c1a\" stroke-width=\"1.5\" stroke-linecap=\"round\"><path d=\"M12 2.4v19.2M3.8 7.2l16.4 9.6M3.8 16.8L20.2 7.2\"/></g><circle cx=\"12\" cy=\"12\" r=\"2.4\" fill=\"#7b3ff2\"/><circle cx=\"12\" cy=\"12\" r=\"1\" fill=\"#fff3e0\"/></svg>",
    "<svg viewBox=\"0 0 32 22\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M16 5q-3-4-7-3 3-1 5 1-4 0-6 3 3 0 4 2-2 1-1 3 2-1 3-1-1 2 1 3 2-2 3-1 2 2 3 1 2-1 1-3 1 0 3 1 1-2-1-3 1-2 4-2-2-3-6-3 2-2 5-1-4-1-7 3z\" fill=\"#7b3ff2\"/><circle cx=\"13.5\" cy=\"9\" r=\"1\" fill=\"#fff3e0\"/><circle cx=\"18.5\" cy=\"9\" r=\"1\" fill=\"#fff3e0\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 3l7 18H5z\" fill=\"#ff8c1a\"/><path d=\"M12 3l3.5 9h-7z\" fill=\"#fff3e0\"/><path d=\"M8.5 12l-3.5 9h14l-3.5-9z\" fill=\"#ffcf71\"/></svg>"
  ],
  "flakeColors": null,
  "flakeCount": 16,
  "flakeMin": 10,
  "flakeMax": 20,
  "flakeOpacity": [
    0.35,
    0.62
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M32 6a22 22 0 0 1 22 22v26l-5.5-5-5.5 5-5.5-5-5.5 5-5.5-5-5.5 5V28A22 22 0 0 1 32 6z\" fill=\"#fff3e0\" stroke=\"#561d9e\" stroke-width=\"2\"/><g data-eyes><circle cx=\"24\" cy=\"28\" r=\"3.4\" fill=\"#561d9e\"/><circle cx=\"40\" cy=\"28\" r=\"3.4\" fill=\"#561d9e\"/></g><path d=\"M26 38q6 4 12 0\" stroke=\"#561d9e\" stroke-width=\"2.4\" fill=\"none\" stroke-linecap=\"round\"/><circle cx=\"18\" cy=\"35\" r=\"3\" fill=\"#ffb3c7\" opacity=\"0.85\"/><circle cx=\"46\" cy=\"35\" r=\"3\" fill=\"#ffb3c7\" opacity=\"0.85\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M16 7q-4-6-9-4 4-2 7 1-6-1-9 4 5 0 6 3-3 1-1 4 3-2 4-1-1 3 1 4 2-3 4-1 2-2 4 1 2-3 1-4 3-1 4-4-1-3-9-4 3-3 7-1-6-2-10 4z\" fill=\"#7b3ff2\" transform=\"translate(0 2)\"/><circle cx=\"13\" cy=\"13\" r=\"1.3\" fill=\"#fff3e0\"/><circle cx=\"19\" cy=\"13\" r=\"1.3\" fill=\"#fff3e0\"/></svg>"
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
