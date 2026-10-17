/*
JSLinux-deobfuscated - An annotated version of the original JSLinux.

Original is Copyright (c) 2011-2012 Fabrice Bellard
Redistribution or commercial use is prohibited without the author's permission.

Clipboard Device
*/
function clipboard_device(Ng, Zf, rh, lh, sh) {
    Ng.register_ioport_read(Zf, 16, 4, this.ioport_readl.bind(this));
    Ng.register_ioport_write(Zf, 16, 4, this.ioport_writel.bind(this));
    Ng.register_ioport_read(Zf + 8, 1, 1, this.ioport_readb.bind(this));
    Ng.register_ioport_write(Zf + 8, 1, 1, this.ioport_writeb.bind(this));
    this.cur_pos = 0;
    this.doc_str = "";
    this.read_func = rh;
    this.write_func = lh;
    this.get_boot_time = sh;
}
clipboard_device.prototype.ioport_writeb = function(mem8_loc, x) {
    this.doc_str += String.fromCharCode(x);
};
clipboard_device.prototype.ioport_readb = function(mem8_loc) {
    var c, na, x;
    na = this.doc_str;
    if (this.cur_pos < na.length) {
        x = na.charCodeAt(this.cur_pos) & 0xff;
    } else {
        x = 0;
    }
    this.cur_pos++;
    return x;
};
clipboard_device.prototype.ioport_writel = function(mem8_loc, x) {
    var na;
    mem8_loc = (mem8_loc >> 2) & 3;
    switch (mem8_loc) {
        case 0:
            this.doc_str = this.doc_str.substr(0, x >>> 0);
            break;
        case 1:
            return this.cur_pos = x >>> 0;
        case 2:
            na = String.fromCharCode(x & 0xff) + String.fromCharCode((x >> 8) & 0xff) + String.fromCharCode((x >> 16) & 0xff) + String.fromCharCode((x >> 24) & 0xff);
            this.doc_str += na;
            break;
        case 3:
            this.write_func(this.doc_str);
    }
};
clipboard_device.prototype.ioport_readl = function(mem8_loc) {
    var x;
    mem8_loc = (mem8_loc >> 2) & 3;
    switch (mem8_loc) {
        case 0:
            this.doc_str = this.read_func();
            return this.doc_str.length >> 0;
        case 1:
            return this.cur_pos >> 0;
        case 2:
            x = this.ioport_readb(0);
            x |= this.ioport_readb(0) << 8;
            x |= this.ioport_readb(0) << 16;
            x |= this.ioport_readb(0) << 24;
            return x;
        case 3:
            if (this.get_boot_time)
                return this.get_boot_time() >> 0;
            else
                return 0;
    }
};
