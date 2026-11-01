/* ============================================================
   HUSTOJ · xmas 模板 · 圣诞装饰脚本
   负责生成「飘雪」与顶栏「串灯」动画。纯视觉，不依赖任何业务数据。
   ============================================================ */
(function () {
  "use strict";

  // 容器兜底：个别页面绕过 header.php 时也能出效果
  function ensureLayer(id) {
    var el = document.getElementById(id);
    if (!el && document.body) {
      el = document.createElement("div");
      el.id = id;
      el.setAttribute("aria-hidden", "true");
      document.body.appendChild(el);
    }
    return el;
  }

  // ---------- 飘雪 ----------
  function buildSnow() {
    var layer = ensureLayer("xmas-snow");
    if (!layer) return;
    var glyphs = ["❄", "❅", "❆", "✦"]; // 雪花 / 星
    var count = window.innerWidth < 800 ? 28 : 60;
    for (var i = 0; i < count; i++) {
      var s = document.createElement("span");
      s.className = "xmas-flake";
      s.textContent = glyphs[i % glyphs.length];
      var size = 8 + Math.random() * 16;          // 字号
      s.style.left = Math.random() * 100 + "vw";
      s.style.fontSize = size + "px";
      s.style.opacity = (0.55 + Math.random() * 0.4).toFixed(2);
      var dur = 6 + Math.random() * 9;             // 下落时长
      s.style.animationDuration = dur + "s";
      s.style.animationDelay = (-Math.random() * dur) + "s";
      layer.appendChild(s);
    }
  }

  // ---------- 串灯 ----------
  function buildLights() {
    var bar = ensureLayer("xmas-lights");
    if (!bar) return;
    var colors = ["#ff4d4d", "#ffd34d", "#3ddc84", "#5aa9ff", "#ff8fcf", "#ffffff"];
    var n = Math.max(12, Math.floor(window.innerWidth / 42));
    for (var i = 0; i < n; i++) {
      var b = document.createElement("span");
      b.className = "bulb";
      b.style.color = colors[i % colors.length];
      b.style.background = colors[i % colors.length];
      b.style.animationDelay = (Math.random() * 1.6).toFixed(2) + "s";
      bar.appendChild(b);
    }
  }

  function init() {
    buildSnow();
    buildLights();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
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
  if (window.OJ_THEME_FX.xmasfx) return;
  window.OJ_THEME_FX.xmasfx = true;

  var NS = 'xmasfx';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
    "side": "right",
    "glow": "drop-shadow(0 3px 9px rgba(192,39,31,.30))",
    "bubble": "background:#fff;border-color:#c0271f;color:#8f1a13;box-shadow:0 6px 18px rgba(192,39,31,.22);",
    "lines": [
      "圣诞快乐！",
      "Merry Christmas!",
      "许个愿吧～",
      "姜饼人分你一半",
      "袜子挂好了吗？"
    ],
    "flakes": [
      "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 2.6l2.6 6.4 6.8.5-5.2 4.4 1.7 6.6-5.9-3.6-5.9 3.6 1.7-6.6L2.6 9.5l6.8-.5z\" fill=\"#e3b23c\" stroke=\"#b58a1f\" stroke-width=\"1\"/></svg>",
      "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><rect x=\"3.5\" y=\"9\" width=\"17\" height=\"12.5\" rx=\"1.6\" fill=\"#c0271f\"/><rect x=\"3.5\" y=\"12.8\" width=\"17\" height=\"2.6\" fill=\"#f4d35e\"/><rect x=\"10.8\" y=\"9\" width=\"2.4\" height=\"12.5\" fill=\"#f4d35e\"/><path d=\"M12 9c-2.6 0-5-1.4-5-3.4S10 3 12 9c2-6 5-5.4 5-3.4S14.6 9 12 9z\" fill=\"#f4d35e\" stroke=\"#c9a227\" stroke-width=\".8\"/></svg>",
      "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M9 21V8a3.6 3.6 0 0 1 7.2 0v1.2\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"5\" stroke-linecap=\"round\"/><path d=\"M9 21V8a3.6 3.6 0 0 1 7.2 0v1.2\" fill=\"none\" stroke=\"#c0271f\" stroke-width=\"2.4\" stroke-dasharray=\"2.6 2.6\" stroke-linecap=\"round\"/></svg>"
    ],
    "flakeColors": null,
    "flakeCount": 0,
    "flakeMin": 10,
    "flakeMax": 20,
    "flakeOpacity": [
      0.3,
      0.5
    ],
    "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M14 24C14 13 22 6 32 6s18 7 18 18z\" fill=\"#c0271f\"/><path d=\"M44 9c4 1 6 3 7 6l-7 1z\" fill=\"#c0271f\"/><circle cx=\"53\" cy=\"15\" r=\"5\" fill=\"#ffffff\"/><rect x=\"12\" y=\"21\" width=\"40\" height=\"7\" rx=\"3.5\" fill=\"#ffffff\"/><circle cx=\"32\" cy=\"35\" r=\"13\" fill=\"#f6d3b3\"/><path d=\"M19 33c0 13 6 21 13 21s13-8 13-21c-4 4-9 6-13 6s-9-2-13-6z\" fill=\"#ffffff\"/><g data-eyes><circle cx=\"27\" cy=\"31\" r=\"1.7\" fill=\"#3b2b25\" data-gaze=\"1\"/><circle cx=\"37\" cy=\"31\" r=\"1.7\" fill=\"#3b2b25\" data-gaze=\"1\"/></g><circle cx=\"32\" cy=\"36\" r=\"2.2\" fill=\"#e08a7a\"/><path d=\"M29 41q3 2 6 0\" stroke=\"#8f1a13\" stroke-width=\"1.4\" fill=\"none\" stroke-linecap=\"round\"/><path d=\"M21 45h22l4 13H17z\" fill=\"#c0271f\"/><rect x=\"16\" y=\"54\" width=\"32\" height=\"6\" rx=\"3\" fill=\"#ffffff\"/><circle cx=\"32\" cy=\"50\" r=\"1.6\" fill=\"#ffffff\"/><circle cx=\"27\" cy=\"53\" r=\"1.6\" fill=\"#ffffff\"/><circle cx=\"37\" cy=\"53\" r=\"1.6\" fill=\"#ffffff\"/></svg>",
    "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 28V10a4.5 4.5 0 0 1 9 0v2\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"7\" stroke-linecap=\"round\"/><path d=\"M12 28V10a4.5 4.5 0 0 1 9 0v2\" fill=\"none\" stroke=\"#c0271f\" stroke-width=\"3\" stroke-dasharray=\"3 3.2\" stroke-linecap=\"round\"/><path d=\"M5 8l1.2 3 3 .3-2.3 2 .7 3-2.6-1.7-2.6 1.7.7-3-2.3-2 3-.3z\" fill=\"#f4d35e\" opacity=\".8\"/></svg>"
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

/* ===== 趣味互动 2026-12 =====
 * ① 雪花靠近鼠标自动减速（Web Animations playbackRate，离开后恢复）
 * ② window.xmasSantaGift()：AC 时圣诞老人从天而降撒礼物
 * 均尊重 prefers-reduced-motion。
 */
(function () {
  'use strict';
  if (typeof window === 'undefined' || typeof document === 'undefined') return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (window.OJ_THEME_FX && window.OJ_THEME_FX.xmasfun2) return;
  if (window.OJ_THEME_FX) window.OJ_THEME_FX.xmasfun2 = true;

  // ---------- ① 雪花遇鼠标减速 ----------
  var mouse = { x: -9999, y: -9999, active: false };
  window.addEventListener('pointermove', function (e) {
    mouse.x = e.clientX; mouse.y = e.clientY; mouse.active = true;
  }, { passive: true });
  window.addEventListener('pointerleave', function () { mouse.active = false; }, { passive: true });

  var slowed = {};   // element index -> 播放中的 CSSAnimation
  var flakeSeq = 0;
  function markFlake(el) {
    if (!el.dataset.xmasId) el.dataset.xmasId = 'f' + (++flakeSeq);
    return el.dataset.xmasId;
  }
  function within(el, r) {
    var b = el.getBoundingClientRect();
    var cx = b.left + b.width / 2, cy = b.top + b.height / 2;
    return Math.abs(cx - mouse.x) < r + b.width / 2 && Math.abs(cy - mouse.y) < r + b.height / 2;
  }
  function tuneSnow() {
    if (!document.getAnimations) return;   // 老浏览器直接跳过
    var near = {};
    if (mouse.active) {
      var list = document.querySelectorAll('#xmas-snow .xmas-flake, #xmasfx-snow .xmasfx-snowflake');
      for (var i = 0; i < list.length; i++) {
        if (within(list[i], 130)) near[markFlake(list[i])] = list[i];
      }
    }
    var anims = document.getAnimations();
    for (var j = 0; j < anims.length; j++) {
      var a = anims[j], t = a.effect && a.effect.target;
      if (!t || !t.dataset || !t.dataset.xmasId) continue;
      var id = t.dataset.xmasId;
      if (near[id]) {
        if (!slowed[id]) { slowed[id] = a; try { a.updatePlaybackRate(0.2); } catch (e) {} }
      } else if (slowed[id] === a) {
        try { a.updatePlaybackRate(1); } catch (e) {}
        delete slowed[id];
      }
    }
  }
  setInterval(tuneSnow, 160);

  // ---------- ② AC 圣诞老人发礼物 ----------
  var GIFT_SVG = {
    gift: '<svg viewBox="0 0 24 24"><rect x="3.5" y="9" width="17" height="12.5" rx="1.6" fill="#c0271f"/><rect x="3.5" y="12.8" width="17" height="2.6" fill="#f4d35e"/><rect x="10.8" y="9" width="2.4" height="12.5" fill="#f4d35e"/><path d="M12 9c-2.6 0-5-1.4-5-3.4S10 3 12 9c2-6 5-5.4 5-3.4S14.6 9 12 9z" fill="#f4d35e" stroke="#c9a227" stroke-width=".8"/></svg>',
    sock: '<svg viewBox="0 0 24 24"><path d="M9 3h6v11l4.2 4.2a3 3 0 0 1-4.2 4.2L9 17V3z" fill="#c0271f"/><rect x="8" y="3" width="8" height="3.4" rx="1.4" fill="#fff"/><path d="M9 14l4 4.4" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/></svg>',
    candy: '<svg viewBox="0 0 24 24"><path d="M9 21V8a4.5 4.5 0 0 1 9 0v1.6" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round"/><path d="M9 21V8a4.5 4.5 0 0 1 9 0v1.6" fill="none" stroke="#c0271f" stroke-width="2.6" stroke-dasharray="2.8 2.8" stroke-linecap="round"/></svg>'
  };
  var SANTA_SVG = '<svg viewBox="0 0 64 64"><path d="M14 24C14 13 22 6 32 6s18 7 18 18z" fill="#c0271f"/><path d="M44 9c4 1 6 3 7 6l-7 1z" fill="#c0271f"/><circle cx="53" cy="15" r="5" fill="#fff"/><rect x="12" y="21" width="40" height="7" rx="3.5" fill="#fff"/><circle cx="32" cy="35" r="13" fill="#f6d3b3"/><path d="M19 33c0 13 6 21 13 21s13-8 13-21c-4 4-9 6-13 6s-9-2-13-6z" fill="#fff"/><circle cx="27" cy="31" r="1.7" fill="#3b2b25"/><circle cx="37" cy="31" r="1.7" fill="#3b2b25"/><circle cx="32" cy="36" r="2.2" fill="#e08a7a"/><path d="M29 41q3 2 6 0" stroke="#8f1a13" stroke-width="1.4" fill="none" stroke-linecap="round"/></svg>';

  function injectGiftCss() {
    if (document.getElementById('xmas-gift-css')) return;
    var css = [
      '#xmas-gift-layer{position:fixed;inset:0;z-index:99999;pointer-events:none;overflow:hidden}',
      '.xmas-gift-santa{position:absolute;top:-160px;left:50%;margin-left:-80px;width:160px;height:160px;animation:xmas-gift-drop 4.2s cubic-bezier(.3,.7,.4,1) forwards;filter:drop-shadow(0 6px 16px rgba(192,39,31,.45))}',
      '.xmas-gift-santa svg{width:100%;height:100%}',
      '.xmas-gift-bubble{position:absolute;top:150px;left:50%;transform:translateX(-50%);white-space:nowrap;background:#fff;border:2px solid #c0271f;color:#8f1a13;font-weight:700;font-size:14px;padding:6px 14px;border-radius:14px;box-shadow:0 6px 18px rgba(192,39,31,.25);opacity:0;animation:xmas-gift-bubble 4.2s ease forwards}',
      '@keyframes xmas-gift-drop{0%{top:-160px;transform:rotate(-8deg)}35%{top:22vh;transform:rotate(4deg)}50%{top:26vh;transform:rotate(-2deg)}100%{top:-160px;transform:rotate(6deg);opacity:1}}',
      '@keyframes xmas-gift-bubble{0%,20%{opacity:0}30%,80%{opacity:1}100%{opacity:0}}',
      '.xmas-gift-item{position:absolute;top:-40px;width:26px;height:26px;animation:xmas-gift-fall linear forwards}',
      '.xmas-gift-item svg{width:100%;height:100%}',
      '@keyframes xmas-gift-fall{0%{transform:translateY(0) rotate(0deg);opacity:0}8%{opacity:1}100%{transform:translateY(96vh) rotate(var(--rz,300deg));opacity:.9}}'
    ].join('\n');
    var st = document.createElement('style');
    st.id = 'xmas-gift-css';
    st.textContent = css;
    document.head.appendChild(st);
  }

  window.xmasSantaGift = function () {
    if (document.hidden) return;
    injectGiftCss();
    var layer = document.createElement('div');
    layer.id = 'xmas-gift-layer';
    layer.setAttribute('aria-hidden', 'true');
    var santa = document.createElement('div');
    santa.className = 'xmas-gift-santa';
    santa.innerHTML = SANTA_SVG + '<div class="xmas-gift-bubble">恭喜 AC！圣诞老人发礼物啦～</div>';
    layer.appendChild(santa);
    var keys = Object.keys(GIFT_SVG);
    for (var i = 0; i < 14; i++) {
      var g = document.createElement('span');
      g.className = 'xmas-gift-item';
      g.innerHTML = GIFT_SVG[keys[i % keys.length]];
      g.style.left = (4 + Math.random() * 92).toFixed(1) + 'vw';
      var dur = 1.6 + Math.random() * 1.6;
      g.style.animationDuration = dur + 's';
      g.style.animationDelay = (0.7 + Math.random() * 1.8).toFixed(2) + 's';
      g.style.setProperty('--rz', Math.round(180 + Math.random() * 540) + 'deg');
      var size = 18 + Math.random() * 18;
      g.style.width = size + 'px';
      g.style.height = size + 'px';
      layer.appendChild(g);
    }
    document.body.appendChild(layer);
    setTimeout(function () {
      if (layer.parentNode) layer.parentNode.removeChild(layer);
    }, 4600);
  };
})();
