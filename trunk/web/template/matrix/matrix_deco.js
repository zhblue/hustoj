/* matrix 模板 · 装饰层（黑客帝国 · 绿色字符雨）
 * 在页面最底层插入一个全屏 canvas，绘制经典 Matrix 数字雨；
 * 顶层 #matrix-decor 负责 CRT 扫描线与暗角（纯 CSS）。
 * 尊重 prefers-reduced-motion：开启时完全不生成雨。
 */
(function () {
  // CRT 扫描线/暗角是纯 CSS 装饰，依赖 #matrix-decor；模板没有挂载点时补建
  if (!document.getElementById('matrix-decor') && document.body) {
    var deco = document.createElement('div');
    deco.id = 'matrix-decor';
    deco.setAttribute('aria-hidden', 'true');
    document.body.appendChild(deco);
  }
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (document.getElementById('matrix-rain')) return;
  if (!document.body) return;

  var canvas = document.createElement('canvas');
  canvas.id = 'matrix-rain';
  canvas.setAttribute('aria-hidden', 'true');
  document.body.insertBefore(canvas, document.body.firstChild);

  var ctx = canvas.getContext && canvas.getContext('2d');
  if (!ctx) return;

  // 数字 + 半角片假名，经典 Matrix 字符集
  var POOL = '01ﾊﾐﾋｰｳｼﾅﾓﾆｻﾜﾂｵﾘｱﾎﾃﾏｹﾒｴｶｷﾑﾕﾗｾﾈｽﾀﾇﾍ0123456789'.split('');
  var FONT = 15;      // 字号（CSS px）
  var BASE_SPEED = 0.42;
  var FPS = 22;       // 目标帧率（省电）

  var dpr = Math.min(window.devicePixelRatio || 1, 2);
  var W = 0, H = 0, cell = 0, cols = 0, drops = [], speeds = [], snow = [];
  var last = 0, raf = null, rt = null;

  function pick() { return POOL[(Math.random() * POOL.length) | 0]; }

  function setup() {
    W = canvas.width = Math.floor(window.innerWidth * dpr);
    H = canvas.height = Math.floor(window.innerHeight * dpr);
    cell = Math.round(FONT * dpr);
    cols = Math.ceil(W / cell);
    drops = [];
    speeds = [];
    for (var i = 0; i < cols; i++) {
      drops[i] = Math.random() * (-H / cell);
      speeds[i] = BASE_SPEED * (0.55 + Math.random() * 0.95);
    }
    ctx.font = cell + 'px Consolas, "DejaVu Sans Mono", Menlo, monospace';
    ctx.textBaseline = 'top';
    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, W, H);

    // 数码雪花：比字符雨更慢的像素光点，背景氛围用
    snow = [];
    var snowCount = Math.max(24, Math.min(64, Math.round(window.innerWidth / 28)));
    for (var s = 0; s < snowCount; s++) {
      snow.push({
        x: Math.random() * W,
        y: Math.random() * H,
        r: (1 + Math.random() * 2.2) * dpr,
        v: (0.12 + Math.random() * 0.38) * dpr,
        drift: (0.15 + Math.random() * 0.45) * dpr,
        phase: Math.random() * Math.PI * 2,
        a: 0.1 + Math.random() * 0.32
      });
    }
  }

  function frame() {
    // 半透明黑覆盖：让旧字符自然衰减，形成绿色拖尾
    ctx.fillStyle = 'rgba(0, 4, 1, 0.085)';
    ctx.fillRect(0, 0, W, H);

    for (var i = 0; i < cols; i++) {
      var x = i * cell;
      var y = drops[i] * cell;
      if (y > -cell && y < H + cell) {
        // 头部：近白亮绿
        ctx.fillStyle = '#E6FFF0';
        ctx.fillText(pick(), x, y);
        // 次行：亮绿，制造发光层次
        ctx.fillStyle = 'rgba(0, 255, 65, 0.72)';
        ctx.fillText(pick(), x, y - cell);
      }
      drops[i] += speeds[i];
      if (y > H && Math.random() > 0.972) drops[i] = Math.random() * -20;
    }

    // 数码雪花：缓慢下落 + 左右轻摆，落在卡片后方
    var now = performance.now ? performance.now() : Date.now();
    for (var k = 0; k < snow.length; k++) {
      var f = snow[k];
      f.y += f.v;
      f.x += Math.sin(now / 2600 + f.phase) * f.drift;
      if (f.y > H + 6) { f.y = -6; f.x = Math.random() * W; }
      if (f.x < -6) f.x = W + 6; else if (f.x > W + 6) f.x = -6;
      ctx.fillStyle = 'rgba(214, 255, 228, ' + f.a.toFixed(2) + ')';
      ctx.fillRect(f.x, f.y, f.r, f.r);
    }
  }

  function loop(ts) {
    raf = requestAnimationFrame(loop);
    if (ts - last < 1000 / FPS) return;
    last = ts;
    frame();
  }

  function start() { if (!raf) { last = 0; raf = requestAnimationFrame(loop); } }
  function stop() { if (raf) { cancelAnimationFrame(raf); raf = null; } }

  window.addEventListener('resize', function () {
    clearTimeout(rt);
    rt = setTimeout(function () { setup(); start(); }, 220);
  });
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) stop(); else start();
  });

  setup();
  start();
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
  if (window.OJ_THEME_FX.matrix) return;
  window.OJ_THEME_FX.matrix = true;

  var NS = 'matrix';
  var SNOW_ID = NS + '-snow';
  var PET_ID = NS + '-pet';
  var BURST_ID = NS + '-burst';
  var CFG = {
  "side": "right",
  "glow": "drop-shadow(0 0 6px rgba(0,255,65,.55))",
  "bubble": "background:#000;border-color:#00FF41;color:#C4FFD2;box-shadow:0 0 14px rgba(0,255,65,.35);font-family:Consolas,'DejaVu Sans Mono',monospace;",
  "lines": [
    "There is no spoon.",
    "跟随白兔。",
    "红色药丸，还是蓝色？",
    "Wake up, OJ user…",
    "Knock, knock."
  ],
  "flakes": [
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><text x=\"12\" y=\"18\" text-anchor=\"middle\" font-family=\"Consolas,monospace\" font-size=\"16\" fill=\"#00FF41\">0</text></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><text x=\"12\" y=\"18\" text-anchor=\"middle\" font-family=\"Consolas,monospace\" font-size=\"16\" fill=\"#C4FFD2\">1</text></svg>",
    "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M12 3v18M3 12h18\" stroke=\"#00FF41\" stroke-width=\"1.6\" stroke-linecap=\"round\"/></svg>"
  ],
  "flakeColors": [
    "#00FF41",
    "#C4FFD2",
    "#39FF14"
  ],
  "flakeCount": 0,
  "flakeMin": 10,
  "flakeMax": 18,
  "flakeOpacity": [
    0.35,
    0.6
  ],
  "pet": "<svg viewBox=\"0 0 64 64\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M14 26L18 10l10 10h8l10-10 4 16v14c0 8-8 12-20 12S14 48 14 40z\" fill=\"rgba(0,40,10,0.55)\" stroke=\"#00FF41\" stroke-width=\"2.4\" stroke-linejoin=\"round\"/><g data-eyes><path d=\"M23 33h6M35 33h6\" stroke=\"#C4FFD2\" stroke-width=\"3\" stroke-linecap=\"round\"/></g><path d=\"M30 41h4M27 45q5 4 10 0\" stroke=\"#C4FFD2\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\"/><path d=\"M32 10v5M12 26H8M52 26h4M20 50l-3 6M44 50l3 6\" stroke=\"#00C13A\" stroke-width=\"1.6\" stroke-linecap=\"round\"/></svg>",
  "buddy": "<svg viewBox=\"0 0 32 32\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M11 14C8 6 10 2 12 2s4 6 4 12M21 14c3-8 1-12-1-12s-4 6-4 12\" fill=\"#E8FFF0\" stroke=\"#00FF41\" stroke-width=\"1.2\"/><ellipse cx=\"16\" cy=\"21\" rx=\"8\" ry=\"8\" fill=\"#E8FFF0\" stroke=\"#00FF41\" stroke-width=\"1.2\"/><circle cx=\"13\" cy=\"20\" r=\"1.2\" fill=\"#003B00\"/><circle cx=\"19\" cy=\"20\" r=\"1.2\" fill=\"#003B00\"/><path d=\"M15 23h2\" stroke=\"#003B00\" stroke-width=\"1\"/></svg>"
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
