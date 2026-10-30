/* bilibili 模板 · 装饰层动画：飘落小电视 / 弹幕气泡 / 粉色爱心
 * 挂在 #bilibili-decor 上；尊重 prefers-reduced-motion 自动关闭。 */
(function () {
  var deco = document.getElementById('bilibili-decor');
  if (!deco && document.body) {
    deco = document.createElement('div');
    deco.id = 'bilibili-decor';
    deco.setAttribute('aria-hidden', 'true');
    document.body.appendChild(deco);
  }
  if (!deco) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  // 小电视（bilibili logo）
  var tv =
    '<svg viewBox="0 0 40 32" width="36" height="29" aria-hidden="true">' +
    '<path d="M9.4 3.2 L14.2 8.4 M30.6 3.2 L25.8 8.4" stroke="#FB7299" stroke-width="2.6" stroke-linecap="round" fill="none"/>' +
    '<rect x="3.4" y="7.6" width="33.2" height="23.2" rx="6" fill="#FB7299"/>' +
    '<rect x="6.6" y="10.8" width="26.8" height="16.8" rx="4" fill="#FFFFFF"/>' +
    '<path d="M13.4 17.4 q1.6 -2 3.2 0 M23.4 17.4 q1.6 -2 3.2 0" stroke="#FB7299" stroke-width="2" stroke-linecap="round" fill="none"/>' +
    '<path d="M17.2 22.4 q2.8 2 5.6 0" stroke="#FB7299" stroke-width="1.8" stroke-linecap="round" fill="none"/>' +
    '</svg>';

  // 弹幕气泡
  function danmaku(text, color) {
    return '<svg viewBox="0 0 96 30" width="76" height="24" aria-hidden="true">' +
      '<rect x="1.5" y="1.5" width="93" height="23" rx="11.5" fill="#FFFFFF" stroke="' + color + '" stroke-width="2"/>' +
      '<text x="48" y="17" text-anchor="middle" font-size="12" font-family="PingFang SC, Microsoft YaHei, sans-serif" fill="' + color + '">' + text + '</text>' +
      '</svg>';
  }

  // 粉色爱心（点赞）
  var heart =
    '<svg viewBox="0 0 32 30" width="26" height="24" aria-hidden="true">' +
    '<path d="M16 27 C4 18.5 2 12.5 2 9 A7 7 0 0 1 16 6 A7 7 0 0 1 30 9 c0 3.5 -2 9.5 -14 18 z" fill="#FB7299"/>' +
    '<ellipse cx="10.5" cy="9.5" rx="2.4" ry="1.6" fill="#FF9DB7" opacity="0.85" transform="rotate(-30 10.5 9.5)"/>' +
    '</svg>';

  var bubbles = [
    danmaku('666666', '#FB7299'),
    danmaku('前方高能', '#FF9DB7'),
    danmaku('ACCEPTED!', '#00AEEC'),
    danmaku('一键三连', '#FB7299'),
    danmaku('awsl', '#40C5F1')
  ];

  var kinds = [tv, heart].concat(bubbles);

  function spawn() {
    var el = document.createElement('div');
    el.className = 'bili-sprite';
    el.innerHTML = kinds[Math.floor(Math.random() * kinds.length)];
    var svg = el.firstChild;
    var size = 0.7 + Math.random() * 0.75;
    el.style.left = (Math.random() * 94) + 'vw';
    var dur = 11 + Math.random() * 8;
    var delay = Math.random() * 4;
    el.style.opacity = '0';
    svg.style.width = (34 * size) + 'px';
    svg.style.height = 'auto';
    el.style.animation =
      'bili-wobble ' + (2.6 + Math.random() * 2.2) + 's ease-in-out infinite, ' +
      'bili-fall ' + dur + 's linear ' + delay + 's forwards';
    deco.appendChild(el);
    setTimeout(function () {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, (dur + delay) * 1000 + 600);
  }

  for (var i = 0; i < 12; i++) {
    (function (d) { setTimeout(spawn, d * 760); })(i);
  }
  setInterval(spawn, 1500);
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
  if (window.OJ_THEME_FX.bilibili) return;
  window.OJ_THEME_FX.bilibili = true;

  var NS = 'bilibili';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "right",
  "glow": "drop-shadow(0 3px 8px rgba(251,114,153,.35))",
  "bubble": "background:#fff;border-color:#FB7299;color:#E85A85;box-shadow:0 6px 18px rgba(251,114,153,.25);",
  "lines": [
    "一键三连～",
    "前方高能！",
    "AC 达成，awsl",
    "弹幕护体！",
    "这次一定！"
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><g stroke=\"#FB7299\" stroke-width=\"1.5\" stroke-linecap=\"round\"><path d=\"M12 2.4v19.2M3.8 7.2l16.4 9.6M3.8 16.8L20.2 7.2\"/></g><circle cx=\"12\" cy=\"12\" r=\"2.3\" fill=\"#FFFFFF\" stroke=\"#00AEEC\" stroke-width=\"1.3\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 5l2.5 3M17 5l-2.5 3\" stroke=\"#FB7299\" stroke-width=\"1.6\" stroke-linecap=\"round\" fill=\"none\"/><rect x=\"3\" y=\"7\" width=\"18\" height=\"13\" rx=\"3\" fill=\"#FB7299\"/><rect x=\"5.2\" y=\"9.2\" width=\"13.6\" height=\"8.6\" rx=\"2\" fill=\"#FFFFFF\"/><circle cx=\"9.6\" cy=\"13.2\" r=\"1\" fill=\"#FB7299\"/><circle cx=\"14.4\" cy=\"13.2\" r=\"1\" fill=\"#FB7299\"/><path d=\"M10.4 15.6q1.6 1.2 3.2 0\" stroke=\"#FB7299\" stroke-width=\"0.9\" fill=\"none\" stroke-linecap=\"round\"/></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 21S3 14.5 3 8.8A4.8 4.8 0 0 1 12 6a4.8 4.8 0 0 1 9 2.8C21 14.5 12 21 12 21Z\" fill=\"#FF9DB7\" stroke=\"#FB7299\" stroke-width=\"1.1\"/><ellipse cx=\"8.8\" cy=\"9.6\" rx=\"1.6\" ry=\"1\" fill=\"#FFFFFF\" opacity=\"0.75\" transform=\"rotate(-30 8.8 9.6)\"/></svg>"
  ],
  "flakeColors": null,
  "flakeCount": 16,
  "flakeMin": 10,
  "flakeMax": 22,
  "flakeOpacity": [
    0.32,
    0.58
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M18 12L25 20M46 12L39 20\" stroke=\"#FB7299\" stroke-width=\"3\" stroke-linecap=\"round\" fill=\"none\"/><rect x=\"7\" y=\"19\" width=\"50\" height=\"36\" rx=\"9\" fill=\"#FB7299\"/><rect x=\"12\" y=\"24\" width=\"40\" height=\"26\" rx=\"6\" fill=\"#FFFFFF\"/><circle cx=\"25\" cy=\"34\" r=\"2.6\" fill=\"#FB7299\" data-gaze=\"1\"/><circle cx=\"39\" cy=\"34\" r=\"2.6\" fill=\"#FB7299\" data-gaze=\"1\"/><path d=\"M28 42q4 3 8 0\" stroke=\"#FB7299\" stroke-width=\"2.4\" fill=\"none\" stroke-linecap=\"round\"/><circle cx=\"20\" cy=\"41\" r=\"2.4\" fill=\"#FFB3C7\"/><circle cx=\"44\" cy=\"41\" r=\"2.4\" fill=\"#FFB3C7\"/><rect x=\"20\" y=\"53\" width=\"7\" height=\"3\" rx=\"1.5\" fill=\"#E85A85\"/><rect x=\"37\" y=\"53\" width=\"7\" height=\"3\" rx=\"1.5\" fill=\"#E85A85\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M16 28S3 19 3 11.5A6.5 6.5 0 0 1 16 8a6.5 6.5 0 0 1 13 3.5C29 19 16 28 16 28Z\" fill=\"#FB7299\"/><circle cx=\"12\" cy=\"14\" r=\"1.4\" fill=\"#FFFFFF\" data-gaze=\"1\"/><circle cx=\"20\" cy=\"14\" r=\"1.4\" fill=\"#FFFFFF\" data-gaze=\"1\"/><path d=\"M14 18q2 1.6 4 0\" stroke=\"#FFFFFF\" stroke-width=\"1.2\" fill=\"none\" stroke-linecap=\"round\"/></svg>"
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
