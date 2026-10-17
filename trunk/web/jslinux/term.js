 /*
   Javascript Terminal

   Copyright (c) 2011 Fabrice Bellard

   Redistribution or commercial use is prohibited without the author's
   permission.
*/
"use strict";
if (!Function.prototype.bind) {
    Function.prototype.bind = function(aa) {
        var ba = [].slice, ca = ba.call(arguments, 1), self = this, da = function() {
        }, ea = function() {
            return self.apply(this instanceof da ? this : (aa || {}), ca.concat(ba.call(arguments)));
        };
        da.prototype = self.prototype;
        ea.prototype = new da();
        return ea;
    };
}
function Term(fa, ga, ha) {
    this.w = fa;
    this.h = ga;
    this.cur_h = ga;
    this.tot_h = 1000;
    this.y_base = 0;
    this.y_disp = 0;
    this.x = 0;
    this.y = 0;
    this.cursorstate = 0;
    this.handler = ha;
    this.convert_lf_to_crlf = false;
    this.state = 0;
    this.output_queue = "";
    this.bg_colors = ["#000000", "#ff0000", "#00ff00", "#ffff00", "#0000ff", "#ff00ff", "#00ffff", "#ffffff"];
    this.fg_colors = ["#000000", "#ff0000", "#00ff00", "#ffff00", "#0000ff", "#ff00ff", "#00ffff", "#ffffff"];
    this.def_attr = (7 << 3) | 0;
    this.cur_attr = this.def_attr;
    this.is_mac = (navigator.userAgent.indexOf("Mac") >= 0) ? true : false;
    this.key_rep_state = 0;
    this.key_rep_str = "";
}
Term.prototype.open = function() {
    var y, ia, i, ja, c;
    this.lines = new Array();
    c = 32 | (this.def_attr << 16);
    for (y = 0; y < this.cur_h; y++) {
        ia = new Array();
        for (i = 0; i < this.w; i++)
            ia[i] = c;
        this.lines[y] = ia;
    }
    document.writeln('<table border="0" cellspacing="0" cellpadding="0">');
    for (y = 0; y < this.h; y++) {
        document.writeln('<tr><td class="term" id="tline' + y + '"></td></tr>');
    }
    document.writeln('</table>');
    this.refresh(0, this.h - 1);
    document.addEventListener("keydown", this.keyDownHandler.bind(this), true);
    document.addEventListener("keypress", this.keyPressHandler.bind(this), true);
    ja = this;
    setInterval(function() {
        ja.cursor_timer_cb();
    }, 1000);
};
Term.prototype.refresh = function(ka, la) {
    var ma, y, ia, na, c, w, i, oa, pa, qa, ra, sa, ta;
    for (y = ka; y <= la; y++) {
        ta = y + this.y_disp;
        if (ta >= this.cur_h)
            ta -= this.cur_h;
        ia = this.lines[ta];
        na = "";
        w = this.w;
        if (y == this.y && this.cursor_state && this.y_disp == this.y_base) {
            oa = this.x;
        } else {
            oa = -1;
        }
        qa = this.def_attr;
        for (i = 0; i < w; i++) {
            c = ia[i];
            pa = c >> 16;
            c &= 0xffff;
            if (i == oa) {
                pa = -1;
            }
            if (pa != qa) {
                if (qa != this.def_attr)
                    na += '</span>';
                if (pa != this.def_attr) {
                    if (pa == -1) {
                        na += '<span class="termReverse">';
                    } else {
                        na += '<span style="';
                        ra = (pa >> 3) & 7;
                        sa = pa & 7;
                        if (ra != 7) {
                            na += 'color:' + this.fg_colors[ra] + ';';
                        }
                        if (sa != 0) {
                            na += 'background-color:' + this.bg_colors[sa] + ';';
                        }
                        na += '">';
                    }
                }
            }
            switch (c) {
                case 32:
                    na += "&nbsp;";
                    break;
                case 38:
                    na += "&amp;";
                    break;
                case 60:
                    na += "&lt;";
                    break;
                case 62:
                    na += "&gt;";
                    break;
                default:
                    if (c < 32) {
                        na += "&nbsp;";
                    } else {
                        na += String.fromCharCode(c);
                    }
                    break;
            }
            qa = pa;
        }
        if (qa != this.def_attr) {
            na += '</span>';
        }
        ma = document.getElementById("tline" + y);
        ma.innerHTML = na;
    }
};
Term.prototype.cursor_timer_cb = function() {
    this.cursor_state ^= 1;
    this.refresh(this.y, this.y);
};
Term.prototype.show_cursor = function() {
    if (!this.cursor_state) {
        this.cursor_state = 1;
        this.refresh(this.y, this.y);
    }
};
Term.prototype.scroll = function() {
    var y, ia, x, c, ta;
    if (this.cur_h < this.tot_h) {
        this.cur_h++;
    }
    if (++this.y_base == this.cur_h)
        this.y_base = 0;
    this.y_disp = this.y_base;
    c = 32 | (this.def_attr << 16);
    ia = new Array();
    for (x = 0; x < this.w; x++)
        ia[x] = c;
    ta = this.y_base + this.h - 1;
    if (ta >= this.cur_h)
        ta -= this.cur_h;
    this.lines[ta] = ia;
};
Term.prototype.scroll_disp = function(n) {
    var i, ta;
    if (n >= 0) {
        for (i = 0; i < n; i++) {
            if (this.y_disp == this.y_base)
                break;
            if (++this.y_disp == this.cur_h)
                this.y_disp = 0;
        }
    } else {
        n = -n;
        ta = this.y_base + this.h;
        if (ta >= this.cur_h)
            ta -= this.cur_h;
        for (i = 0; i < n; i++) {
            if (this.y_disp == ta)
                break;
            if (--this.y_disp < 0)
                this.y_disp = this.cur_h - 1;
        }
    }
    this.refresh(0, this.h - 1);
};
Term.prototype.write = function(char) {
    function va(y) {
        ka = Math.min(ka, y);
        la = Math.max(la, y);
    }
    function wa(s, x, y) {
        var l, i, c, ta;
        ta = s.y_base + y;
        if (ta >= s.cur_h)
            ta -= s.cur_h;
        l = s.lines[ta];
        c = 32 | (s.def_attr << 16);
        for (i = x; i < s.w; i++)
            l[i] = c;
        va(y);
    }
    function xa(s, ya) {
        var j, n;
        if (ya.length == 0) {
            s.cur_attr = s.def_attr;
        } else {
            for (j = 0; j < ya.length; j++) {
                n = ya[j];
                if (n >= 30 && n <= 37) {
                    s.cur_attr = (s.cur_attr & ~(7 << 3)) | ((n - 30) << 3);
                } else if (n >= 40 && n <= 47) {
                    s.cur_attr = (s.cur_attr & ~7) | (n - 40);
                } else if (n == 0) {
                    s.cur_attr = s.def_attr;
                }
            }
        }
    }
    var za = 0;
    var Aa = 1;
    var Ba = 2;
    var i, c, ka, la, l, n, j, ta;
    ka = this.h;
    la = -1;
    va(this.y);
    if (this.y_base != this.y_disp) {
        this.y_disp = this.y_base;
        ka = 0;
        la = this.h - 1;
    }
    for (i = 0; i < char.length; i++) {
        c = char.charCodeAt(i);
        switch (this.state) {
            case za:
                switch (c) {
                    case 10:
                        if (this.convert_lf_to_crlf) {
                            this.x = 0;
                        }
                        this.y++;
                        if (this.y >= this.h) {
                            this.y--;
                            this.scroll();
                            ka = 0;
                            la = this.h - 1;
                        }
                        break;
                    case 13:
                        this.x = 0;
                        break;
                    case 8:
                        if (this.x > 0) {
                            this.x--;
                        }
                        break;
                    case 9:
                        n = (this.x + 8) & ~7;
                        if (n <= this.w) {
                            this.x = n;
                        }
                        break;
                    case 27:
                        this.state = Aa;
                        break;
                    default:
                        if (c >= 32) {
                            if (this.x >= this.w) {
                                this.x = 0;
                                this.y++;
                                if (this.y >= this.h) {
                                    this.y--;
                                    this.scroll();
                                    ka = 0;
                                    la = this.h - 1;
                                }
                            }
                            ta = this.y + this.y_base;
                            if (ta >= this.cur_h)
                                ta -= this.cur_h;
                            this.lines[ta][this.x] = (c & 0xffff) | (this.cur_attr << 16);
                            this.x++;
                            va(this.y);
                        }
                        break;
                }
                break;
            case Aa:
                if (c == 91) {
                    this.esc_params = new Array();
                    this.cur_param = 0;
                    this.state = Ba;
                } else {
                    this.state = za;
                }
                break;
            case Ba:
                if (c >= 48 && c <= 57) {
                    this.cur_param = this.cur_param * 10 + c - 48;
                } else {
                    this.esc_params[this.esc_params.length] = this.cur_param;
                    this.cur_param = 0;
                    if (c == 59)
                        break;
                    this.state = za;
                    switch (c) {
                        case 65:
                            n = this.esc_params[0];
                            if (n < 1)
                                n = 1;
                            this.y -= n;
                            if (this.y < 0)
                                this.y = 0;
                            break;
                        case 66:
                            n = this.esc_params[0];
                            if (n < 1)
                                n = 1;
                            this.y += n;
                            if (this.y >= this.h)
                                this.y = this.h - 1;
                            break;
                        case 67:
                            n = this.esc_params[0];
                            if (n < 1)
                                n = 1;
                            this.x += n;
                            if (this.x >= this.w - 1)
                                this.x = this.w - 1;
                            break;
                        case 68:
                            n = this.esc_params[0];
                            if (n < 1)
                                n = 1;
                            this.x -= n;
                            if (this.x < 0)
                                this.x = 0;
                            break;
                        case 72:
                            {
                                var Ca, ta;
                                ta = this.esc_params[0] - 1;
                                if (this.esc_params.length >= 2)
                                    Ca = this.esc_params[1] - 1;
                                else
                                    Ca = 0;
                                if (ta < 0)
                                    ta = 0;
                                else if (ta >= this.h)
                                    ta = this.h - 1;
                                if (Ca < 0)
                                    Ca = 0;
                                else if (Ca >= this.w)
                                    Ca = this.w - 1;
                                this.x = Ca;
                                this.y = ta;
                            }
                            break;
                        case 74:
                            wa(this, this.x, this.y);
                            for (j = this.y + 1; j < this.h; j++)
                                wa(this, 0, j);
                            break;
                        case 75:
                            wa(this, this.x, this.y);
                            break;
                        case 109:
                            xa(this, this.esc_params);
                            break;
                        case 110:
                            this.queue_chars("\x1b[" + (this.y + 1) + ";" + (this.x + 1) + "R");
                            break;
                        default:
                            break;
                    }
                }
                break;
        }
    }
    va(this.y);
    if (la >= ka)
        this.refresh(ka, la);
};
Term.prototype.writeln = function(char) {
    this.write(char + '\r\n');
};
Term.prototype.keyDownHandler = function(event) {
    var char;
    char = "";
    switch (event.keyCode) {
        case 8:
            char = "";
            break;
        case 9:
            char = "\t";
            break;
        case 13:
            char = "\r";
            break;
        case 27:
            char = "\x1b";
            break;
        case 37:
            char = "\x1b[D";
            break;
        case 39:
            char = "\x1b[C";
            break;
        case 38:
            if (event.ctrlKey) {
                this.scroll_disp(-1);
            } else {
                char = "\x1b[A";
            }
            break;
        case 40:
            if (event.ctrlKey) {
                this.scroll_disp(1);
            } else {
                char = "\x1b[B";
            }
            break;
        case 46:
            char = "\x1b[3~";
            break;
        case 45:
            char = "\x1b[2~";
            break;
        case 36:
            char = "\x1bOH";
            break;
        case 35:
            char = "\x1bOF";
            break;
        case 33:
            if (event.ctrlKey) {
                this.scroll_disp(-(this.h - 1));
            } else {
                char = "\x1b[5~";
            }
            break;
        case 34:
            if (event.ctrlKey) {
                this.scroll_disp(this.h - 1);
            } else {
                char = "\x1b[6~";
            }
            break;
        default:
            if (event.ctrlKey) {
                if (event.keyCode >= 65 && event.keyCode <= 90) {
                    char = String.fromCharCode(event.keyCode - 64);
                } else if (event.keyCode == 32) {
                    char = String.fromCharCode(0);
                }
            } else if ((!this.is_mac && event.altKey) || (this.is_mac && event.metaKey)) {
                if (event.keyCode >= 65 && event.keyCode <= 90) {
                    char = "\x1b" + String.fromCharCode(event.keyCode + 32);
                }
            }
            break;
    }
    if (char) {
        if (event.stopPropagation)
            event.stopPropagation();
        if (event.preventDefault)
            event.preventDefault();
        this.show_cursor();
        this.key_rep_state = 1;
        this.key_rep_str = char;
        this.handler(char);
        return false;
    } else {
        this.key_rep_state = 0;
        return true;
    }
};
Term.prototype.keyPressHandler = function(event) {
    var char, charcode;
    if (event.stopPropagation)
        event.stopPropagation();
    if (event.preventDefault)
        event.preventDefault();
    char = "";
    if (!("charCode" in event)) {
        charcode = event.keyCode;
        if (this.key_rep_state == 1) {
            this.key_rep_state = 2;
            return false;
        } else if (this.key_rep_state == 2) {
            this.show_cursor();
            this.handler(this.key_rep_str);
            return false;
        }
    } else {
        charcode = event.charCode;
    }
    if (charcode != 0) {
        if (!event.ctrlKey && ((!this.is_mac && !event.altKey) || (this.is_mac && !event.metaKey))) {
            char = String.fromCharCode(charcode);
        }
    }
    if (char) {
        this.show_cursor();
        this.handler(char);
        return false;
    } else {
        return true;
    }
};
Term.prototype.queue_chars = function(char) {
    this.output_queue += char;
    if (this.output_queue)
        setTimeout(this.outputHandler.bind(this), 0);
};
Term.prototype.outputHandler = function() {
    if (this.output_queue) {
        this.handler(this.output_queue);
        this.output_queue = "";
    }
};






