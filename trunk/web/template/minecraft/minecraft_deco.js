/* minecraft 模板 · 装饰层：落方块粒子（草/泥土/石头/钻石/金/红石/熔岩）
 * 挂在 #minecraft-decor 容器上，纯 CSS 动画，pointer-events:none 不挡操作。 */
(function () {
  var box = document.getElementById('minecraft-decor');
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
