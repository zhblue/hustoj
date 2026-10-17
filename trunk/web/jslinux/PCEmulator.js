/*
JSLinux-deobfuscated - An annotated version of the original JSLinux.

Original is Copyright (c) 2011-2012 Fabrice Bellard
Redistribution or commercial use is prohibited without the author's permission.

Main PC Emulator Routine
*/

// used as callback wrappers for emulated PIT and PIC chips
function set_hard_irq_wrapper(irq) { this.hard_irq = irq;}
function return_cycle_count() { return this.cycle_count; }

function PCEmulator(params) {
    var cpu;
    cpu = new CPU_X86();
    this.cpu = cpu;
    cpu.phys_mem_resize(params.mem_size);
    this.init_ioports();
    this.register_ioport_write(0x80, 1, 1, this.ioport80_write);
    this.pic    = new PIC_Controller(this, 0x20, 0xa0, set_hard_irq_wrapper.bind(cpu));
    this.pit    = new PIT(this, this.pic.set_irq.bind(this.pic, 0),  return_cycle_count.bind(cpu));
    this.cmos   = new CMOS(this);
    this.serial = new Serial(this, 0x3f8, this.pic.set_irq.bind(this.pic, 4), params.serial_write);
    this.kbd    = new KBD(this, this.reset.bind(this));
    this.reset_request = 0;
    if (params.clipboard_get && params.clipboard_set) {
        this.jsclipboard = new clipboard_device(this, 0x3c0, params.clipboard_get, params.clipboard_set, params.get_boot_time);
    }
    cpu.ld8_port       = this.ld8_port.bind(this);
    cpu.ld16_port      = this.ld16_port.bind(this);
    cpu.ld32_port      = this.ld32_port.bind(this);
    cpu.st8_port       = this.st8_port.bind(this);
    cpu.st16_port      = this.st16_port.bind(this);
    cpu.st32_port      = this.st32_port.bind(this);
    cpu.get_hard_intno = this.pic.get_hard_intno.bind(this.pic);
}

PCEmulator.prototype.load_binary = function(binary_array, mem8_loc) { return this.cpu.load_binary(binary_array, mem8_loc); };

PCEmulator.prototype.start = function() { setTimeout(this.timer_func.bind(this), 10); };

PCEmulator.prototype.timer_func = function() {
    var exit_status, Ncycles, do_reset, err_on_exit, PC, cpu;
    PC = this;
    cpu = PC.cpu;
    Ncycles = cpu.cycle_count + 100000;

    do_reset = false;
    err_on_exit = false;

    exec_loop: while (cpu.cycle_count < Ncycles) {
        PC.pit.update_irq();
        exit_status = cpu.exec(Ncycles - cpu.cycle_count);
        if (exit_status == 256) {
            if (PC.reset_request) {
                do_reset = true;
                break;
            }
        } else if (exit_status == 257) {
            err_on_exit = true;
            break;
        } else {
            do_reset = true;
            break;
        }
    }
    if (!do_reset) {
        if (err_on_exit) {
            setTimeout(this.timer_func.bind(this), 10);
        } else {
            setTimeout(this.timer_func.bind(this), 0);
        }
    }
};

PCEmulator.prototype.init_ioports = function() {
    var i, readw, writew;
    this.ioport_readb_table = new Array();
    this.ioport_writeb_table = new Array();
    this.ioport_readw_table = new Array();
    this.ioport_writew_table = new Array();
    this.ioport_readl_table = new Array();
    this.ioport_writel_table = new Array();
    readw = this.default_ioport_readw.bind(this);
    writew = this.default_ioport_writew.bind(this);
    for (i = 0; i < 1024; i++) {
        this.ioport_readb_table[i] = this.default_ioport_readb;
        this.ioport_writeb_table[i] = this.default_ioport_writeb;
        this.ioport_readw_table[i] = readw;
        this.ioport_writew_table[i] = writew;
        this.ioport_readl_table[i] = this.default_ioport_readl;
        this.ioport_writel_table[i] = this.default_ioport_writel;
    }
};

PCEmulator.prototype.default_ioport_readb = function(port_num) {
    var x;
    x = 0xff;
    return x;
};

PCEmulator.prototype.default_ioport_readw = function(port_num) {
    var x;
    x = this.ioport_readb_table[port_num](port_num);
    port_num = (port_num + 1) & (1024 - 1);
    x |= this.ioport_readb_table[port_num](port_num) << 8;
    return x;
};

PCEmulator.prototype.default_ioport_readl = function(port_num) {
    var x;
    x = -1;
    return x;
};

PCEmulator.prototype.default_ioport_writeb = function(port_num, x) {};

PCEmulator.prototype.default_ioport_writew = function(port_num, x) {
    this.ioport_writeb_table[port_num](port_num, x & 0xff);
    port_num = (port_num + 1) & (1024 - 1);
    this.ioport_writeb_table[port_num](port_num, (x >> 8) & 0xff);
};

PCEmulator.prototype.default_ioport_writel = function(port_num, x) {};

PCEmulator.prototype.ld8_port = function(port_num) {
    var x;
    x = this.ioport_readb_table[port_num & (1024 - 1)](port_num);
    return x;
};

PCEmulator.prototype.ld16_port = function(port_num) {
    var x;
    x = this.ioport_readw_table[port_num & (1024 - 1)](port_num);
    return x;
};

PCEmulator.prototype.ld32_port = function(port_num) {
    var x;
    x = this.ioport_readl_table[port_num & (1024 - 1)](port_num);
    return x;
};

PCEmulator.prototype.st8_port  = function(port_num, x) { this.ioport_writeb_table[port_num & (1024 - 1)](port_num, x); };
PCEmulator.prototype.st16_port = function(port_num, x) { this.ioport_writew_table[port_num & (1024 - 1)](port_num, x); };
PCEmulator.prototype.st32_port = function(port_num, x) { this.ioport_writel_table[port_num & (1024 - 1)](port_num, x); };

PCEmulator.prototype.register_ioport_read = function(start, len, iotype, io_callback) {
    var i;
    switch (iotype) {
        case 1:
            for (i = start; i < start + len; i++) {
                this.ioport_readb_table[i] = io_callback;
            }
            break;
        case 2:
            for (i = start; i < start + len; i += 2) {
                this.ioport_readw_table[i] = io_callback;
            }
            break;
        case 4:
            for (i = start; i < start + len; i += 4) {
                this.ioport_readl_table[i] = io_callback;
            }
            break;
    }
};

PCEmulator.prototype.register_ioport_write = function(start, len, iotype, io_callback) {
    var i;
    switch (iotype) {
        case 1:
            for (i = start; i < start + len; i++) {
                this.ioport_writeb_table[i] = io_callback;
            }
            break;
        case 2:
            for (i = start; i < start + len; i += 2) {
                this.ioport_writew_table[i] = io_callback;
            }
            break;
        case 4:
            for (i = start; i < start + len; i += 4) {
                this.ioport_writel_table[i] = io_callback;
            }
            break;
    }
};

PCEmulator.prototype.ioport80_write = function(mem8_loc, data) {}; //POST codes! Seem to be ignored?
PCEmulator.prototype.reset = function() { this.request_request = 1; };















