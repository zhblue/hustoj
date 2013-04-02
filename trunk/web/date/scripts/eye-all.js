
/********************************************************************************
* 命名空间：Common        创 建 人：ivanshao          创建时间： 2008-11-12
********************************************************************************/

//==========================================命名空间===============================================//

//声明 Eye 命名空间
eye = {
    namespace: function() {
        var a = arguments, o = null, i, j, d, rt;
        for (i = 0; i < a.length; ++i) {
            d = a[i].split(".");
            rt = d[0];
            eval('if (typeof ' + rt + ' == "undefined"){' + rt + ' = {};} o = ' + rt + ';');
            for (j = 1; j < d.length; ++j) {
                o[d[j]] = o[d[j]] || {};
                o = o[d[j]];
            }
        }
    }
};

eye.namespace("eye", "eye.common", "eye.datePicker", "eye.selectCity", "eye.tab");

eye.zIndex = 10000;

var userAgent = navigator.userAgent.toLowerCase();
eye.browser = {
    version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
    safari: /webkit/.test(userAgent),
    opera: /opera/.test(userAgent),
    chrome: /chrome/.test(userAgent),
    msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
    mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent)
};

// Common 类
eye.common = {

    //获取页面宽度、页面高度、窗口宽度、窗口高度
    getPageSize: function() {
        var scrW, scrH;
        if (window.innerHeight && window.scrollMaxY) {
            // Mozilla
            scrW = window.innerWidth + window.scrollMaxX;
            scrH = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight) {
            // all but IE Mac    
            scrW = document.body.scrollWidth;
            scrH = document.body.scrollHeight;
        } else if (document.body) {
            // IE Mac
            scrW = document.body.offsetWidth;
            scrH = document.body.offsetHeight;
        }

        var winW, winH;
        if (window.innerHeight) {
            // all except IE
            winW = window.innerWidth;
            winH = window.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) {
            // IE 6 Strict Mode
            winW = document.documentElement.clientWidth;
            winH = document.documentElement.clientHeight;
        } else if (document.body) {
            // other    
            winW = document.body.clientWidth;
            winH = document.body.clientHeight;
        }

        // for small pages with total size less then the viewport  
        var pageW = (scrW < winW) ? winW : scrW;
        var pageH = (scrH < winH) ? winH : scrH;

        return { PageW: pageW, PageH: pageH, WinW: winW, WinH: winH };
    },

    //获取滚动条水平位置和滚动条垂直位置
    getPageScroll: function() {
        var x, y;
        if (window.pageYOffset) {
            // all except IE
            y = window.pageYOffset;
            x = window.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {
            // IE 6 Strict
            y = document.documentElement.scrollTop;
            x = document.documentElement.scrollLeft;
        } else if (document.body) {
            // all other IE
            y = document.body.scrollTop;
            x = document.body.scrollLeft;
        }

        return { X: x, Y: y };
    },

    //获取鼠标水平和垂直位置
    pointer: function(event) {
        return {
            X: event.pageX || (event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft)),
            Y: event.pageY || (event.clientY + (document.documentElement.scrollTop || document.body.scrollTop))
        };
    },

    //获取控件左上坐标
    getPositionLT: function(o) {
        var x = 0, y = 0;
        var parent = o;
        while (parent.offsetParent) {
            x += parent.offsetLeft;
            y += parent.offsetTop;
            parent = parent.offsetParent;
        }

        return { X: x, Y: y };
    },

    //获取控件左下坐标
    getPositionLB: function(o) {
        var position = this.getPositionLT(o);
        return { X: position.X, Y: position.Y + o.offsetHeight };
    },

    //获取控件右上坐标
    getPositionRT: function(o) {
        var position = this.getPositionLT(o);
        return { X: position.X + o.offsetWidth, Y: position.Y };
    },

    //获取控件右坐标
    getPositionRB: function(o) {
        var position = this.getPositionLT(o);
        return { X: position.X + o.offsetWidth, Y: position.Y };
    },

    //获取窗口显示位置
    getDisplayPosition: function(width, height) {
        var pageSize = this.getPageSize();
        var pageScroll = this.getPageScroll();
        var x, y;
        if (width != undefined && width != 0) {
            x = 0;
        }
        if (height != undefined && height != 0) {
            y = 0;
        }

        return { X: x, Y: y };
    },

    //获取窗口居中显示位置
    getCenterPosition: function(width, height) {
        var pageSize = this.getPageSize();
        var pageScroll = this.getPageScroll();
        var x = 0, y = 0;

        if (height != undefined) {
            x = (pageSize.WinW - width) / 2 + pageScroll.X;
        }

        if (width != undefined) {
            y = (pageSize.WinH - height) / 2 + pageScroll.Y;
        }

        return { X: x, Y: y };
    }
}

/********************************************************************************
* 命名空间：DatePicker       创 建 人：ivanshao          创建时间： 2008-11-12
********************************************************************************/

//DatePicker 类
eye.datePicker = function() {

    var path = 'images/cbox/';
    var style = new Array();
    style[0] = 'width: 1px; height: 1px; background-color: #D9E5E7;';
    style[1] = 'background-color: #99BBE8;';
    style[2] = 'width: 1px; height: 1px; background-color: #99BBE8;';
    style[3] = 'width: 1px; height: 1px; background-color: #B1CBEB;';
    style[4] = 'background-color: #F8FBFC;';
    style[5] = 'width: 1px; height: 1px; background-color: #F8FBFC;';
    style[6] = 'height: 22px; background-image:url(' + path + 'icon-title-bg-22.png);';
    style[7] = 'height: 1px; background-color: #99BBE8;';
    style[8] = 'height: 20px; background-color: #DFE8F6; text-align: center;';
    style[9] = 'width: 15px; height: 15px; margin-top: 2px; margin-left: 2px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-left2.gif); background-position: left;';
    style[10] = 'width: 15px; height: 15px; margin-top: 2px; margin-left: 2px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-left1.gif); background-position: left;';
    style[11] = 'width: 15px; height: 15px; margin-top: 2px; margin-right: 2px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-right1.gif); background-position: left;';
    style[12] = 'width: 15px; height: 15px; margin-top: 2px; margin-right: 2px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-right2.gif); background-position: left;';
    style[13] = 'height: 20px; float: left; text-align: center; font-family: Tahoma; font-size: 12px; font-weight: bold; color: #15428B; line-height: 20px; overflow: hidden; cursor: pointer;';
    style[14] = 'width: 26px; height: 20px; float: left; text-align: center; font-family: Tahoma; font-size: 12px; color: #15428B; line-height: 20px; background-color: #DFE8F6;';
    style[15] = 'width: 24px; height: 18px; float: left; text-align: center; font-family: Tahoma; font-size: 11px; color: #000000; line-height: 18px; background-color: #FFFFFF; border: solid 1px #FFFFFF; cursor: pointer;';
    style[16] = 'width: 43px; height: 20px; float: left; text-align: center; font-family: Tahoma; font-size: 11px; color: #000000; line-height: 20px; background-color: #FFFFFF; border: solid 1px #FFFFFF; cursor: pointer;';
    style[17] = 'width: 15px; height: 15px; margin-top: 3px; margin-bottom: 2px; margin-right: 5px; float: right; font-size: 12px; cursor: pointer; background-image:  url(' + path + 'icon-left1.gif); background-position: left;';
    style[18] = 'width: 15px; height: 15px; margin-top: 3px; margin-bottom: 2px;  margin-left: 5px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-right1.gif); background-position: left;';
    style[19] = 'height: 20px; background-color: #DFE8F6; text-align: center; padding-top: 4px; padding-bottom: 4px;';
    style[20] = 'width: 2px; height: 1px; background-color: #99BBE8;';

    //声明最大年份和最小年份
    var oMaxYear = 9999;
    var oMinYear = 1000;
    var oSrcElem = null;

    scrollMove = function(e) {
        e = window.event || e;
        var srcElement = e.srcElement || e.target;

        e.stopPropagation && (e.preventDefault(), e.stopPropagation()) || (e.cancelBubble = true, e.returnValue = false); //取消默认滚动

        if (srcElement.id == 'eye.datePicker.prevYear' || srcElement.id == "eye.datePicker.nextYear") {
            eye.datePicker.addYear(e.detail > 0 ? 1 : -1);
        } else {
            eye.datePicker.addMonth(e.detail > 0 ? 1 : -1);
        }
    };

    addListener = function(ele, eve, fun) {
        if (ele.addEventListener) {
            ele.addEventListener(eve, fun, false);
        } else {
            ele.attachEvent("on" + eve, fun);
        }
    };

    addListener(document, "click", function(e) {
        e = window.event || e;
        var srcElement = e.srcElement || e.target;
        if (srcElement != oSrcElem && srcElement.id != "eye.datePicker.calendar") {
            var parent = srcElement.parentNode;
            var clendar = document.getElementById("eye.datePicker.calendar");
            while (parent) {
                if (parent == clendar) {
                    return;
                }
                parent = parent.parentNode;
            }

            eye.datePicker.close();
        }
    });

    return {
        show: function(o) {
            this.obj = o;
            oSrcElem = o;

            if (this.obj != null && this.obj != undefined) {
                var date = this.obj.value.trim();

                if (date.length > 0 && date.isDateTime()) {
                    this.oDate = new Date(Date.parse(date.replace(/-/g, "/")));
                } else {
                    this.oDate = new Date();
                }

                this.oGuid = 'eye.datePicker.calendar';
                this.oWidth = 186;
                this.oYear = this.oDate.getFullYear();
                this.oMonth = this.oDate.getMonth();    //一年的第几月（0-11）
                this.oDayOfWeek = this.oDate.getDay();  //一周的第几天（0-6）
                this.oDayOfMonth = this.oDate.getDate(); //一月的第几天（1-31）
                this.oPosition = eye.common.getPositionLB(this.obj);
                this.createPicker(0);
            }
        },
        createPicker: function(type) {
            var html = '';
            switch (type) {
                case 0:  //创建日期选择器
                    html = this.createHeader() + this.createTitle() + this.createDate() + this.createConfirm() + this.createFooter();
                    break;
                case 1:  //创建日期时间选择器
                    html = this.createHeader() + this.createTitle() + this.createSelect() + this.createFooter();
                    break;
            }

            var oldDiv = document.getElementById(this.oGuid);

            if (oldDiv != null && oldDiv != undefined) {
                oldDiv.style.top = this.oPosition.Y + 'px';
                oldDiv.style.left = this.oPosition.X + 'px';
                oldDiv.style.zIndex = (eye.zIndex += 1);
            }
            else {
                var newDiv = document.createElement('div');
                newDiv.id = this.oGuid;
                newDiv.style.top = this.oPosition.Y + 'px';
                newDiv.style.left = this.oPosition.X + 'px';
                newDiv.style.position = 'absolute';
                newDiv.style.zIndex = (eye.zIndex += 1);
                newDiv.innerHTML = html;
                document.body.appendChild(newDiv);
            }
            this.initDate(this.oYear, this.oMonth);
            this.createSelect();
        },
        createHeader: function() {
            return '<table style="width: ' + this.oWidth + 'px;" border="0" cellpadding="0" cellspacing="0">' +
                       '<tr>' +
                           '<td style="' + style[0] + '"></td>' +
                           '<td style="' + style[1] + '" colspan="3"></td>' +
                           '<td style="' + style[0] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[4] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>'
        },
        createTitle: function() {
            return '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[6] + '">' +
                               '<div style="' + style[9] + '" id="eye.datePicker.prevYear" title="上一年" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onmousewheel="eye.datePicker.eWheel(this);" onclick="eye.datePicker.addYear(-1);"></div>' +
                               '<div style="' + style[10] + '" id="eye.datePicker.prevMonth" title="上一月" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onmousewheel="eye.datePicker.eWheel(this);" onclick="eye.datePicker.addMonth(-1);"></div>' +
                               '<div style="width: ' + (this.oWidth - 4 - 10 - 60) + 'px;' + style[13] + '" id="eye.datePicker.date"  title="选择年月" onmouseover="eye.datePicker.sOver(this);" onmouseout="eye.datePicker.sOut(this);" onclick="eye.datePicker.initSelect();"></div>' +
                               '<div style="' + style[11] + '" id="eye.datePicker.nextMonth" title="下一月" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onmousewheel="eye.datePicker.eWheel(this);" onclick="eye.datePicker.addMonth(1);"></div>' +
                               '<div style="' + style[12] + '" id="eye.datePicker.nextYear" title="下一年" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onmousewheel="eye.datePicker.eWheel(this);" onclick="eye.datePicker.addYear(1);"></div>' +
                           '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[7] + '" colspan="5"></td>' +
                       '</tr>' +
                       '<tr style="display: none;">' +
                           '<td colspan="5" id="eye.datePicker.select"></td>' +
                       '</tr>'
        },
        createDate: function() {
            var htm = '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td>' +
                               '<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">' +
                                   '<tr>' +
                                       '<td style="' + style[14] + '">日</td>' +
                                       '<td style="' + style[14] + '">一</td>' +
                                       '<td style="' + style[14] + '">二</td>' +
                                       '<td style="' + style[14] + '">三</td>' +
                                       '<td style="' + style[14] + '">四</td>' +
                                       '<td style="' + style[14] + '">五</td>' +
                                       '<td style="' + style[14] + '">六</td>' +
                                   '</tr>' +
                               '</table>' +
                           '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td>' +
                               '<table id="eye.datePicker.datebody" style="width: 100%;" border="0" cellpadding="0" cellspacing="0">'
            //绘制日期格
            for (var i = 0; i < 6; i++) {
                htm += '<tr>';
                for (var j = 0; j < 7; j++) {
                    htm += '<td style="' + style[15] + '" name="eye.datePicker.datecell" onmouseover="eye.datePicker.mOver(this);" onmouseout="eye.datePicker.mOut(this);" onclick="eye.datePicker.getDate(this);"></td>';
                }
                htm += '</tr>';
            }
            htm += '</table>' +
                           '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>'

            return htm;
        },
        createConfirm: function() {
            return '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[8] + '"><button type="button" style="height: 24px;" onclick="eye.datePicker.today();">今天</button>&nbsp;&nbsp;<button type="button" style="height: 24px;" onclick="eye.datePicker.close();">关闭</button></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>'
        },
        createFooter: function() {
            return '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[4] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[0] + '"></td>' +
                           '<td style="' + style[1] + '" colspan="3"></td>' +
                           '<td style="' + style[0] + '"></td>' +
                       '</tr>' +
                   '</table>'
        },
        createSelect: function() {
            var htm = '';
            htm += '<table style="width: 100%; position: absolute;" border="0" cellpadding="0" cellspacing="0">' +
                       '<tr id="eye.datePicker.select">' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td>' +
                               '<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">' +
                                   '<tr>' +
                                       '<td style="width: 90px;">' +
                                           '<table id="eye.datePicker.yearbody" style="width: 100%;" border="0" cellpadding="0" cellspacing="0">'
            //绘制年份格
            for (var i = 0; i < 5; i++) {
                htm += '<tr>';
                for (var j = 0; j < 2; j++) {
                    htm += '<td style="' + style[16] + '" name="eye.datePicker.yearcell" onmouseover="eye.datePicker.mOver(this);" onmouseout="eye.datePicker.eOut(this, 0);" onclick="eye.datePicker.setYear(this);" ondblclick="eye.datePicker.ok();"></td>';
                }
                htm += '</tr>';
            }
            htm += '<tr>' +
                                                   '<td style="' + style[16] + '">' +
                                                       '<div style="' + style[17] + '" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onclick="eye.datePicker.getYear(-10);"></div>' +
                                                   '</td>' +
                                                   '<td style="' + style[16] + '">' +
                                                       '<div style="' + style[18] + '" onmouseover="eye.datePicker.position(this, \'right\');" onmouseout="eye.datePicker.position(this, \'left\');" onclick="eye.datePicker.getYear(10);"></div>' +
                                                   '</td>' +
                                               '</tr>' +
                                           '</table>' +
                                       '</td>' +
                                       '<td style="' + style[20] + '"></td>' +
                                       '<td style="width: 90px;">' +
                                           '<table id="eye.datePicker.monthbody" style="width: 100%;" border="0" cellpadding="0" cellspacing="0">'
            //绘制月份格
            for (var i = 0; i < 6; i++) {
                htm += '<tr>';
                for (var j = 0; j < 2; j++) {
                    htm += '<td style="' + style[16] + '" name="eye.datePicker.monthcell" onmouseover="eye.datePicker.mOver(this);" onmouseout="eye.datePicker.eOut(this, 1);" onclick="eye.datePicker.setMonth(this);" ondblclick="eye.datePicker.ok();"></td>';
                }
                htm += '</tr>';
            }
            htm += '</table>' +
                                       '</td>' +
                                   '</tr>' +
                               '</table>' +
                           '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[19] + '"><button type="button"  style="height: 24px;" onclick="eye.datePicker.ok();">确定</button>&nbsp;&nbsp;<button type="button"  style="height: 24px;" onclick="eye.datePicker.cancel();">取消</button></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                   '</table>';

            document.getElementById('eye.datePicker.select').innerHTML = htm;
        },
        initDate: function(year, month) {

            //星期中的第几天（0-6）
            var dayOfWeek = new Date(year, month, 1).getDay();

            //每月的天数
            var days = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

            //如果是闰年则2月有29天，否则2月有28天
            days[1] = ((year % 4 == 0) && (year % 100 != 0) || (year % 400 == 0)) ? 29 : 28;

            //显示年月
            document.getElementById("eye.datePicker.date").innerHTML = year + "年" + (month < 9 ? "0" + (month + 1) : (month + 1)) + "月";

            var cells = eye.browser.msie ? document.getElementById('eye.datePicker.datebody').cells : document.getElementsByName('eye.datePicker.datecell');

            //清空日期格
            for (var i = 0; i < cells.length; i++) {
                cells[i].innerHTML = '';
                cells[i].style.color = '#000000';
                cells[i].style.borderColor = "#ffffff";
                cells[i].style.backgroundColor = "#ffffff";
                cells[i].style.fontWeight = "normal";
            }

            //填充本月日期
            for (var i = 0; i < days[month]; i++) {
                cells[i + dayOfWeek].innerHTML = i + 1;
                cells[i + dayOfWeek].setAttribute('mtype', 'curr');

                //设置选中日期样式
                if (this.oDayOfMonth == (i + 1)) {
                    this.mOver(cells[i + dayOfWeek]);
                }

                //设置休息日字体颜色为红色
                if (this.isPlayday(year, month, i + 1)) {
                    cells[i + dayOfWeek].style.color = 'red';
                }
            }

            var preMonth = (month - 1) < 0 ? 11 : (month - 1);

            //填充上月日期
            for (var i = 0; i < dayOfWeek; i++) {
                cells[i].innerHTML = days[preMonth] - dayOfWeek + i + 1;
                cells[i].style.color = '#AAAAAA';
                cells[i].setAttribute('mtype', 'prec');
            }

            var start = days[month] + dayOfWeek;
            var index = 0;
            //填充下月日期
            for (var i = start; i < 42; i++) {
                index += 1;
                cells[i].innerHTML = index;
                cells[i].style.color = '#AAAAAA';
                cells[i].setAttribute('mtype', 'next');
            }
        },
        isPlayday: function(year, month, day) {
            var date = new Date(year, month, day);
            return (date.getDay() == 0 || date.getDay() == 6);
        },
        mOver: function(o) {
            o.style.borderColor = "#99BBE8";
            o.style.backgroundColor = "#DFE8F6";
            o.style.fontWeight = "bold";
        },
        mOut: function(o) {
            if (o.getAttribute('mtype') == 'curr' && o.innerHTML == this.oDayOfMonth) {
                return;
            }
            else {
                o.style.borderColor = "#ffffff";
                o.style.backgroundColor = "#ffffff";
                o.style.fontWeight = "normal";
            }
        },
        eOut: function(o, type) {
            if (type == 0) {
                if (this.cYear != o.innerHTML) {
                    o.style.borderColor = "#ffffff";
                    o.style.backgroundColor = "#ffffff";
                    o.style.fontWeight = "normal";
                }
            } else {
                var month = (this.cMonth + 1) < 10 ? '0' + (this.cMonth + 1) + '月' : (this.cMonth + 1) + '月';
                if (month != o.innerHTML) {
                    o.style.borderColor = "#ffffff";
                    o.style.backgroundColor = "#ffffff";
                    o.style.fontWeight = "normal";
                }
            }
        },
        sOver: function(o) {
            o.style.color = "#000066";
            o.style.textDecoration = 'underline';
        },
        sOut: function(o) {
            o.style.color = "#15428B";
            o.style.textDecoration = '';
        },
        addYear: function(year) {
            this.oYear += parseInt(year, 10);

            if (this.oYear > oMaxYear) {
                this.oYear = oMaxYear;
            }

            if (this.oYear < oMinYear) {
                this.oYear = oMinYear;
            }

            this.initDate(this.oYear, this.oMonth);
        },
        addMonth: function(month) {
            this.oMonth += parseInt(month, 10);

            if (this.oMonth > 11) {
                this.oMonth = 0;
                this.oYear += 1;

                if (this.oYear > oMaxYear) {
                    this.oYear = oMaxYear;
                }
            }

            if (this.oMonth < 0) {
                this.oMonth = 11;
                this.oYear -= 1;
                if (this.oYear < oMinYear) {
                    this.oYear = oMinYear;
                }
            }

            this.initDate(this.oYear, this.oMonth);
        },
        eWheel: function(o) {
            var e = window.event || e;
            document.onmousewheel = function() {
                if (event.srcElement.id == "eye.datePicker.prevYear" || event.srcElement.id == "eye.datePicker.nextYear" || event.srcElement.id == "eye.datePicker.prevMonth" || event.srcElement.id == "eye.datePicker.nextMonth") {
                    return false;
                }
            }

            if (o.id == 'eye.datePicker.prevYear' || o.id == "eye.datePicker.nextYear") {
                this.addYear(e.wheelDelta > 0 ? -1 : 1);
            } else {
                this.addMonth(e.wheelDelta > 0 ? -1 : 1);
            }
        },
        initSelect: function() {
            this.cYear = this.oYear;
            this.cMonth = this.oMonth;
            this.sYear = this.oYear - 4;

            if (this.sYear < oMinYear) {
                this.sYear = oMinYear;
            }
            this.createYear();
            this.createMonth();

            document.getElementById('eye.datePicker.select').parentNode.style.display = '';
        },
        getYear: function(year) {
            this.sYear += parseInt(year, 10);

            if (this.sYear < oMinYear) {
                this.sYear = oMinYear;
            } else if (this.sYear > oMaxYear) {
                this.sYear = oMaxYear - 10;
            }
            this.createYear();
        },
        setYear: function(o) {
            this.cYear = parseInt(o.innerHTML, 10);
            this.clearYear(eye.browser.msie ? document.getElementById('eye.datePicker.yearbody').cells : document.getElementsByName('eye.datePicker.yearcell'));
            this.mOver(o);
        },
        setMonth: function(o) {
            var month = parseInt(o.innerHTML.substring(0, o.innerHTML.length - 1), 10);
            this.cMonth = month - 1;
            this.clearMonth(eye.browser.msie ? document.getElementById('eye.datePicker.monthbody').cells : document.getElementsByName('eye.datePicker.monthcell'));
            this.mOver(o);
        },
        createYear: function() {
            var cells = eye.browser.msie ? document.getElementById('eye.datePicker.yearbody').cells : document.getElementsByName('eye.datePicker.yearcell');

            this.clearYear(cells);

            for (var i = 0; i < 5; i++) {
                cells[i * 2].innerHTML = this.sYear + i;
                cells[i * 2 + 1].innerHTML = this.sYear + i + 5;
            }

            //设置选中年份样式 
            for (var i = 0; i < 10; i++) {
                if (this.cYear == cells[i].innerHTML) {
                    this.mOver(cells[i]);
                }
            }
        },
        createMonth: function() {
            var cells = eye.browser.msie ? document.getElementById('eye.datePicker.monthbody').cells : document.getElementsByName('eye.datePicker.monthcell');

            this.clearMonth(cells);

            for (var i = 0; i < 6; i++) {
                cells[i * 2].innerHTML = (i + 1) < 10 ? '0' + (i + 1) + '月' : (i + 1) + '月';
                cells[i * 2 + 1].innerHTML = (i + 5 + 2) < 10 ? '0' + (i + 5 + 2) + '月' : (i + 5 + 2) + '月';
            }

            //设置选中年份样式 
            for (var i = 0; i < 12; i++) {
                var month = (this.cMonth + 1) < 10 ? '0' + (this.cMonth + 1) + '月' : (this.cMonth + 1) + '月';
                if (month == cells[i].innerHTML) {
                    this.mOver(cells[i]);
                }
            }
        },
        clearYear: function(cells) {
            for (var i = 0; i < 10; i++) {
                cells[i].style.color = '#000000';
                cells[i].style.borderColor = "#ffffff";
                cells[i].style.backgroundColor = "#ffffff";
                cells[i].style.fontWeight = "normal";
            }
        },
        clearMonth: function(cells) {
            for (var i = 0; i < cells.length; i++) {
                cells[i].style.color = '#000000';
                cells[i].style.borderColor = "#ffffff";
                cells[i].style.backgroundColor = "#ffffff";
                cells[i].style.fontWeight = "normal";
            }
        },
        ok: function() {
            this.oYear = this.cYear;
            this.oMonth = this.cMonth;
            this.initDate(this.oYear, this.oMonth);

            document.getElementById('eye.datePicker.select').parentNode.style.display = 'none';
        },
        cancel: function() {
            document.getElementById('eye.datePicker.select').parentNode.style.display = 'none';
        },
        getDate: function(o) {
            switch (o.getAttribute('mtype')) {
                case 'prec':
                    if (this.oMonth == 0) {
                        this.oMonth = 11;
                        this.oYear -= 1;
                    } else {
                        this.oMonth -= 1;
                    }
                    break;
                case 'next':
                    if (this.oMonth == 11) {
                        this.oMonth = 0;
                        this.oYear += 1;
                    } else {
                        this.oMonth += 1;
                    }
                    break;
            }
            this.oDayOfMonth = o.innerHTML;
            this.obj.value = new Date(this.oYear, this.oMonth, this.oDayOfMonth).format("yyyy-MM-dd");
            this.close();
        },
        today: function() {
            this.obj.value = new Date().format("yyyy-MM-dd");
            this.close();
        },
        position: function(o, p) {
            o.style.backgroundPosition = p;

            if (eye.browser.mozilla) {
                o.addEventListener("DOMMouseScroll", scrollMove, false);
            }
        },
        close: function() {
            var elem = document.getElementById(this.oGuid);
            if (elem != null && elem.parentNode != null) {
                elem.parentNode.removeChild(elem);
            }
        }
    }
} ();

/********************************************************************************
* 命名空间：SelectCity       创 建 人：ivanshao          创建时间： 2008-06-16
********************************************************************************/
eye.selectCity = function() {
    var path = 'images/cbox/';
    var style = new Array();
    style[0] = 'width: 1px; height: 1px; background-color: #D9E5E7;';
    style[1] = 'background-color: #99BBE8;';
    style[2] = 'width: 1px; height: 1px; background-color: #99BBE8;';
    style[3] = 'width: 1px; height: 1px; background-color: #B1CBEB;';
    style[4] = 'background-color: #F8FBFC;';
    style[5] = 'width: 1px; height: 1px; background-color: #F8FBFC;';
    style[6] = 'height: 20px; background-image:url(' + path + 'icon-title-bg.png); padding-left: 4px; padding-right: 2px;';
    style[7] = 'height: 20px; float: left; font-family: Tahoma; font-size: 12px; font-weight: bold; color: #15428B; line-height: 20px; overflow: hidden;';
    style[8] = 'width: 15px; height: 15px; margin-top: 2px; float: left; font-size: 12px; cursor: pointer; background-image: url(' + path + 'icon-close.gif); background-position: left;';
    style[9] = 'height: 1px; background-color: #99BBE8;';
    style[10] = 'background-color: #DFE8F6; font-family: Tahoma; font-size: 12px; color: #000000; word-break: break-all; line-height: 20px;';
    style[11] = 'height: 20px; background-color: #DFE8F6; text-align: center; padding-top: 5px; padding-bottom: 3px;';
    style[12] = 'width: 20px; height: 20px; margin-top: 2px; font-size: 12px; background-image: url(' + path + 'icon-loader.gif);';
    style[13] = 'width: 32px; height: 32px; margin-top: 2px; font-size: 12px; background-image: url(' + path + 'icon-info.gif);';
    style[14] = 'width: 52px; height: 20px; float: left; text-align: center; overflow: hidden;';
    style[15] = 'width: 100%; float: left; margin-top: 5px; border-top: solid 1px #AACBEE;';
    style[16] = 'width: 60px; height: 20px; float: left; text-align: center; overflow: hidden;';

    var province = new Array('北京', '天津', '河北', '山西', '内蒙古', '辽宁', '吉林', '黑龙江', '上海', '江苏', '浙江', '安徽', '福建', '江西', '山东', '河南', '湖北', '湖南', '广东', '广西', '海南', '重庆', '四川', '贵州', '云南', '西藏', '陕西', '甘肃', '青海', '宁夏', '新疆', '香港', '澳门', '台湾');
    var city = new Array();
    city[0] = '北京';
    city[1] = '天津';
    city[2] = '石家庄,唐山,秦皇岛,邯郸,邢台,保定,张家口,承德,沧州,廊坊,衡水';
    city[3] = '太原,大同,阳泉,长治,晋城,朔州,晋中,运城,忻州,临汾,吕梁';
    city[4] = '呼和浩特,包头,乌海,赤峰,通辽,鄂尔多斯,呼伦贝尔,巴彦淖尔,乌兰察布,锡林浩特,乌兰浩特';
    city[5] = '沈阳,大连,鞍山,抚顺,本溪,丹东,锦州,葫芦岛,营口,盘锦,阜新,辽阳,铁岭,朝阳';
    city[6] = '长春,吉林,四平,辽源,通化,白山,松原,白城,延吉';
    city[7] = '哈尔滨,齐齐哈尔,鹤岗,双鸭山,鸡西,大庆,伊春,牡丹江,佳木斯,七台河,黑河,绥化';
    city[8] = '上海';
    city[9] = '南京,无锡,徐州,常州,苏州,南通,连云港,淮安,盐城,扬州,镇江,泰州,宿迁';
    city[10] = '杭州,宁波,温州,嘉兴,湖州,绍兴,金华,衢州,舟山,台州,丽水';
    city[11] = '合肥,芜湖,蚌埠,淮南,马鞍山,淮北,铜陵,安庆,黄山,滁州,阜阳,宿州,巢湖,六安,亳州,池州,宣城';
    city[12] = '福州,厦门,莆田,三明,泉州,漳州,南平,龙岩,宁德';
    city[13] = '南昌,景德镇,萍乡,新余,九江,鹰潭,赣州,吉安,宜春,抚州,上饶';
    city[14] = '济南,青岛,淄博,枣庄,东营,潍坊,烟台,威海,济宁,泰安,日照,莱芜,德州,临沂,聊城,滨州,菏泽';
    city[15] = '郑州,开封,洛阳,平顶山,焦作,鹤壁,新乡,安阳,濮阳,许昌,漯河,三门峡,南阳,商丘,信阳,周口,驻马店,济源';
    city[16] = '武汉,黄石,襄樊,十堰,荆州,宜昌,荆门,鄂州,孝感,黄冈,咸宁,随州,恩施,仙桃,天门,潜江';
    city[17] = '长沙,株洲,湘潭,衡阳,邵阳,岳阳,常德,张家界,益阳,郴州,永州,怀化,娄底,吉首';
    city[18] = '广州,深圳,珠海,汕头,韶关,佛山,江门,湛江,茂名,肇庆,惠州,梅州,汕尾,河源,阳江,清远,东莞,中山,潮州,揭阳,云浮';
    city[19] = '南宁,柳州,桂林,梧州,北海,防城港,钦州,贵港,玉林,百色,贺州,河池,来宾,崇左';
    city[20] = '海口,三亚';
    city[21] = '重庆';
    city[22] = '成都,自贡,攀枝花,泸州,德阳,绵阳,广元,遂宁,内江,乐山,南充,宜宾,广安,达州,眉山,雅安,巴中,资阳,西昌';
    city[23] = '贵阳,六盘水,遵义,安顺,铜仁,毕节,兴义,凯里,都匀';
    city[24] = '昆明,曲靖,玉溪,保山,昭通,丽江,思茅,临沧,景洪,楚雄,大理,潞西';
    city[25] = '拉萨,日喀则';
    city[26] = '西安,铜川,宝鸡,咸阳,渭南,延安,汉中,榆林,安康,商洛';
    city[27] = '兰州,金昌,白银,天水,嘉峪关,武威,张掖,平凉,酒泉,庆阳,定西,陇南,临夏,合作';
    city[28] = '西宁,德令哈,格尔木';
    city[29] = '银川,石嘴山,吴忠,固原,中卫';
    city[30] = '乌鲁木齐,克拉玛依,吐鲁番,哈密,和田,阿克苏,喀什,阿图什,库尔勒,昌吉,博乐,伊宁,塔城,阿勒泰,石河子,阿拉尔,图木舒克,五家渠';
    city[31] = '香港';
    city[32] = '澳门';
    city[33] = '台北,高雄,基隆,台中,台南,新竹市,嘉义';

    var oSelter = null;
    var oGuid = '';

    return {
        show: function(privince, city) {

            if (typeof (privince) == 'object') {
                this.oPriv = privince;
                this.obj = this.oPriv;
                this.oCity = document.getElementById(city);
            } else if (typeof (city) == 'object') {
                this.oCity = city;
                this.obj = this.oCity;
                this.oPriv = document.getElementById(privince);
            }

            this.oGuid = 'eye.selectCity';
            this.oTitle = "省市选择器";
            this.oWidth = 368;
            this.oPosition = eye.common.getPositionLB(this.obj);
            this.createCity();
        },
        createCity: function() {

            var oldDiv = document.getElementById(this.oGuid);

            if (oldDiv != null && oldDiv != undefined) {
                oldDiv.style.top = this.oPosition.Y + 'px';
                oldDiv.style.left = this.oPosition.X + 'px';
                oldDiv.style.zIndex = (eye.zIndex += 1);
            }
            else {
                var newDiv = document.createElement('div');
                newDiv.id = this.oGuid;
                newDiv.style.top = this.oPosition.Y + 'px';
                newDiv.style.left = this.oPosition.X + 'px';
                newDiv.style.position = 'absolute';
                newDiv.style.zIndex = (eye.zIndex += 1);
                document.body.appendChild(newDiv);
            }
            this.renderCity('北京', 0);
        },
        renderCity: function(nowProvince, indexProvince) {
            var htm = '';
            htm += '<table style="width: ' + this.oWidth + 'px;" border="0" cellpadding="0" cellspacing="0">' +
                       '<tr>' +
                           '<td style="' + style[0] + '"></td>' +
                           '<td style="' + style[1] + '" colspan="3"></td>' +
                           '<td style="' + style[0] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[4] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[6] + '">' +
                               '<div style="width: ' + (this.oWidth - 4 - 6 - 15) + 'px;' + style[7] + '">' + this.oTitle + '</div>' +
                               '<div style="' + style[8] + '" onmouseover="eye.selectCity.position(this, \'right\');" onmouseout="eye.selectCity.position(this, \'left\');" onclick="eye.selectCity.close(\'' + this.oGuid + '\', false);"></div>' +
                           '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[9] + '" colspan="5"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[10] + '">' +
                           '<div style="float: left;">';
            for (var i = 0; i < province.length; i++) {
                htm += '<div style="' + style[14] + '"><a href="#" style="white-space: nowrap;" onclick="eye.selectCity.renderCity(\'' + province[i] + '\',' + i + '); return false;">' + province[i] + '</a></div>';
            }
            htm += '</div><div style="' + style[15] + '">';
            var cityList = city[indexProvince].split(',');
            for (i = 0; i < cityList.length; i++) {
                htm += '<div style="' + style[16] + '"><a href="#" style="white-space: nowrap;" onclick="eye.selectCity.getCity(\'' + nowProvince + '\',\'' + cityList[i] + '\'); return false;">' + cityList[i] + '</a></div>';
            }
            htm += '</div>' +
                   '</td>' +
                   '</td>' +
                           '<td style="' + style[5] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[2] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[4] + '"></td>' +
                           '<td style="' + style[3] + '"></td>' +
                           '<td style="' + style[2] + '"></td>' +
                       '</tr>' +
                       '<tr>' +
                           '<td style="' + style[0] + '"></td>' +
                           '<td style="' + style[1] + '" colspan="3"></td>' +
                           '<td style="' + style[0] + '"></td>' +
                       '</tr>' +
                   '</table>'
            document.getElementById(this.oGuid).innerHTML = htm;
            return htm;
        },
        getCity: function(selPriv, selCity) {
            this.oPriv.value = selPriv == null ? '' : selPriv;
            this.oCity.value = selCity == null ? '' : selCity;
            this.close();
        },
        position: function(o, p) {
            o.style.backgroundPosition = p;

            if (eye.browser.mozilla) {
                o.addEventListener("DOMMouseScroll", scrollMove, false);
            }
        },
        close: function() {
            var elem = document.getElementById(this.oGuid);
            if (elem != null && elem.parentNode != null) {
                elem.parentNode.removeChild(elem);
            }
        }
    }
} ();

/********************************************************************************
* 命名空间：Tab 类       创 建 人：ivanshao          创建时间： 2009-12-04
********************************************************************************/
eye.tab = function() {

    var color1 = 'background-color: #012F53;';
    var color2 = 'background-color: #32709A;';
    var color3 = 'background-color: #548AAF;';
    var color4 = 'background-color: #467598;';
    var color5 = 'background-color: #0F65A3;';
    var color6 = 'background-color: #548AAF;';
    var color7 = 'background-color: #0F65A3;';
    var color8 = 'background-color: #005CA0;';

    var style0 = 'font-size: 1px; line-height: 18px;';
    var style1 = 'width: 1px; height: 1px;';
    var style2 = 'width: 54px; height: 1px;';
    var style3 = 'width: 1px; height: 18px;';
    var style4 = 'width: 54px; height: 18px;';
    var style5 = 'text-align: center; vertical-align: middle; font-size: 12px; font-family: Arial; font-weight: bold; color: #02E2F4;';

    var width = 44;
    var height = 18;

    return {
        show: function(tab) {
            oTab = tab;
            var wpx = oTab.style.width;
            var hpx = oTab.style.height;
            var w = parseInt(wpx.substr(0, wpx.length - 2));
            var h = parseInt(hpx.substr(0, wpx.length - 2));

            if (width < (w - 6)) {
                width = w - 6;
            }

            if (height < (h - 3)) {
                height = h - 3;
            }

            style2 = 'width: ' + width + 'px; height: 1px;';
            style3 = 'width: 1px; height: ' + height + 'px;';
            style4 = 'width: ' + width + 'px; height: ' + height + 'px;';

            this.renderTab();
        },
        renderTab: function() {
            var htm = '';
            htm += '<table style="' + style0 + '" border="0" cellpadding="0" cellspacing="0">' +
                        '<tr>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color1 + style1 + '"></td>' +
                                        '<td style="' + color2 + style1 + '"></td>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color2 + style1 + '"></td>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                        '<td style="' + color4 + style1 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                        '<td style="' + color4 + style1 + '"></td>' +
                                        '<td style="' + color5 + style1 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color6 + style2 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color7 + style2 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color8 + style2 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                        '<td style="' + color2 + style1 + '"></td>' +
                                        '<td style="' + color1 + style1 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color4 + style1 + '"></td>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                        '<td style="' + color2 + style1 + '"></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="' + color5 + style1 + '"></td>' +
                                        '<td style="' + color4 + style1 + '"></td>' +
                                        '<td style="' + color3 + style1 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                        '</tr>' +
                        '<tr>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color6 + style3 + '"></td>' +
                                        '<td style="' + color7 + style3 + '"></td>' +
                                        '<td style="' + color8 + style3 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table style="' + style5 + '" border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color8 + style4 + '">测试</td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color8 + style3 + '"></td>' +
                                        '<td style="' + color7 + style3 + '"></td>' +
                                        '<td style="' + color6 + style3 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                        '</tr>' +
                        '<tr>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color6 + style1 + '"></td>' +
                                        '<td style="' + color7 + style1 + '"></td>' +
                                        '<td style="' + color7 + style1 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color7 + style2 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                            '<td>' +
                                '<table border="0" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                        '<td style="' + color7 + style1 + '"></td>' +
                                        '<td style="' + color7 + style1 + '"></td>' +
                                        '<td style="' + color6 + style1 + '"></td>' +
                                    '</tr>' +
                                '</table>' +
                            '</td>' +
                        '</tr>' +
                    '</table>';
            alert(htm);
            $($(oTab)).html(htm);
        }
    }
} ();