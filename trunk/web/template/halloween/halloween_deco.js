/* halloween 模板 · 装饰层动画：飘落卡通幽灵 / 蝙蝠 / 南瓜灯
 * 挂在 #halloween-decor 上；尊重 prefers-reduced-motion 自动关闭。 */
(function () {
  var deco = document.getElementById('halloween-decor');
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
