/* matrix 模板 · 装饰层（黑客帝国 · 绿色字符雨）
 * 在页面最底层插入一个全屏 canvas，绘制经典 Matrix 数字雨；
 * 顶层 #matrix-decor 负责 CRT 扫描线与暗角（纯 CSS）。
 * 尊重 prefers-reduced-motion：开启时完全不生成雨。
 */
(function () {
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
  var W = 0, H = 0, cell = 0, cols = 0, drops = [], speeds = [];
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
