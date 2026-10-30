/* ============================================================
   HUSTOJ · stickman 模板 · 装饰动画
   在 #stickman-decor 容器里动态生成多个小火柴人，
   缓慢下沉 + 旋转，模拟"作业飘在风里"的氛围。
   不影响任何业务功能。纯装饰，pointer-events: none。
   ============================================================ */
(function () {
    if (typeof window === 'undefined') return;
    var host = document.getElementById('stickman-decor');
    if (!host && document.body) {
        host = document.createElement('div');
        host.id = 'stickman-decor';
        host.setAttribute('aria-hidden', 'true');
        document.body.appendChild(host);
    }
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
  if (window.OJ_THEME_FX.stickman) return;
  window.OJ_THEME_FX.stickman = true;

  var NS = 'stickman';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "right",
  "glow": "drop-shadow(0 1px 1px rgba(0,0,0,.15))",
  "bubble": "background:#FFFEFA;border-color:#181818;color:#181818;box-shadow:2px 3px 0 rgba(24,24,24,.18);",
  "lines": [
    "画个 AC！",
    "这题我也不会…",
    "橡皮借我一下",
    "翻页，继续写",
    "老师来了！"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.6\" stroke-linecap=\"round\"><path d=\"M12 3v18M4 7l16 10M20 7L4 17\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.6\" stroke-linecap=\"round\"><path d=\"M12 2l2.6 7.4L22 12l-7.4 2.6L12 22l-2.6-7.4L2 12l7.4-2.6z\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linejoin=\"round\"><path d=\"M4 5h16l-2 5 2 4-8 6-8-6 2-4z\"/><path d=\"M8 8h8\"/></svg>"
  ],
  "flakeColors": [
    "#181818",
    "#D85A30",
    "#185FA5",
    "#1D9E75"
  ],
  "flakeCount": 15,
  "flakeMin": 10,
  "flakeMax": 20,
  "flakeOpacity": [
    0.22,
    0.42
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" stroke=\"#181818\" stroke-width=\"3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"32\" cy=\"14\" r=\"7\" fill=\"#FFFEFA\"/><path d=\"M32 21v20M32 27l-10 7M32 27l10 5M32 41l-8 14M32 41l8 14\"/><g data-eyes><path d=\"M29 14h0.1M35 14h0.1\" stroke-width=\"4\"/></g><path d=\"M28 19q4 3 8 0\" stroke-width=\"2\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><g transform=\"rotate(-35 16 16)\"><rect x=\"10\" y=\"3\" width=\"12\" height=\"20\" fill=\"#EF9F27\" stroke=\"#181818\" stroke-width=\"2\"/><path d=\"M10 23h12l-6 7z\" fill=\"#FAEEDA\" stroke=\"#181818\" stroke-width=\"2\"/><path d=\"M13 27l3 3 3-3\" fill=\"#181818\"/><rect x=\"10\" y=\"3\" width=\"4\" height=\"20\" fill=\"#D85A30\" opacity=\"0.55\"/></g></svg>"
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
