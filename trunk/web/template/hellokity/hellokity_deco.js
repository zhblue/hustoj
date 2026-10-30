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
  if (window.OJ_THEME_FX.hellokity) return;
  window.OJ_THEME_FX.hellokity = true;

  var NS = 'hellokity';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "left",
  "glow": "drop-shadow(0 3px 8px rgba(238,51,78,.25))",
  "bubble": "background:#fff;border-color:#F2A9C4;color:#9C2C52;box-shadow:0 6px 18px rgba(238,143,176,.28);",
  "lines": [
    "喵～今天也要 AC 哦",
    "蝴蝶结歪了啦",
    "一起做题喵？",
    "送你一颗小心心",
    "喵呜～"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><g stroke=\"#EE8FB0\" stroke-width=\"1.4\" stroke-linecap=\"round\"><path d=\"M12 2.4v19.2M3.8 7.2l16.4 9.6M3.8 16.8L20.2 7.2\"/></g><g fill=\"#F7B9CB\"><ellipse cx=\"12\" cy=\"14.6\" rx=\"4\" ry=\"3.2\"/><circle cx=\"7.8\" cy=\"10.4\" r=\"1.4\"/><circle cx=\"12\" cy=\"9\" r=\"1.4\"/><circle cx=\"16.2\" cy=\"10.4\" r=\"1.4\"/></g></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 12L4 7v10z\" fill=\"#EE334E\"/><path d=\"M12 12l8-5v10z\" fill=\"#EE334E\"/><circle cx=\"12\" cy=\"12\" r=\"2.6\" fill=\"#F45D7A\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 21S3 14.5 3 8.8A4.8 4.8 0 0 1 12 6a4.8 4.8 0 0 1 9 2.8C21 14.5 12 21 12 21Z\" fill=\"#F7B9CB\" stroke=\"#EE8FB0\" stroke-width=\"1\"/><ellipse cx=\"8.8\" cy=\"9.6\" rx=\"1.6\" ry=\"1\" fill=\"#FFFFFF\" opacity=\"0.8\" transform=\"rotate(-30 8.8 9.6)\"/></svg>"
  ],
  "flakeColors": null,
  "flakeCount": 18,
  "flakeMin": 9,
  "flakeMax": 20,
  "flakeOpacity": [
    0.3,
    0.52
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M16 22L22 8l10 10M48 22L42 8 32 18\" fill=\"#FFFFFF\" stroke=\"#E89AB8\" stroke-width=\"2.4\" stroke-linejoin=\"round\"/><ellipse cx=\"32\" cy=\"36\" rx=\"24\" ry=\"21\" fill=\"#FFFFFF\" stroke=\"#F2A9C4\" stroke-width=\"2.4\"/><g data-eyes><circle cx=\"24\" cy=\"33\" r=\"3\" fill=\"#5A3A47\"/><circle cx=\"40\" cy=\"33\" r=\"3\" fill=\"#5A3A47\"/></g><ellipse cx=\"32\" cy=\"39\" rx=\"3\" ry=\"2.2\" fill=\"#FCD133\"/><path d=\"M26 45q6 4 12 0\" stroke=\"#5A3A47\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\"/><path d=\"M12 18l8-4 4 6-8 4zM12 18l-2-8 8 1z\" fill=\"#EE334E\"/><circle cx=\"12\" cy=\"18\" r=\"2.6\" fill=\"#EE334E\"/><g stroke=\"#F2A9C4\" stroke-width=\"1.2\" stroke-linecap=\"round\"><path d=\"M5 36h9M6 42l9-2M59 36h-9M58 42l-9-2\"/></g></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M16 10L4 4v18z\" fill=\"#EE334E\"/><path d=\"M16 10l12-6v18z\" fill=\"#EE334E\"/><circle cx=\"16\" cy=\"10\" r=\"4\" fill=\"#F45D7A\"/><circle cx=\"14\" cy=\"9\" r=\"0.9\" fill=\"#FFFFFF\"/><circle cx=\"18\" cy=\"9\" r=\"0.9\" fill=\"#FFFFFF\"/></svg>"
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
