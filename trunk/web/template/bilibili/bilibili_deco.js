/* bilibili 模板 · 装饰层动画：飘落小电视 / 弹幕气泡 / 粉色爱心
 * 挂在 #bilibili-decor 上；尊重 prefers-reduced-motion 自动关闭。 */
(function () {
  var deco = document.getElementById('bilibili-decor');
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
