console.log('%cbshark%cby yemaster',
    'background-color: #2180db;padding: 2px 4px;color: #fff;',
    'background-color: #eee;padding: 2px 4px;color: #222324;')

$('.ui.dropdown')
    .dropdown({
        on: "hover"
    })
    ;

function totop() {
    var scrollToTop = window.setInterval(function () {
        var pos = window.pageYOffset;
        if (pos > 0) {
            window.scrollTo(0, pos - 100); // how far to scroll on each step
        } else {
            window.clearInterval(scrollToTop);
        }
    }, 16);
}