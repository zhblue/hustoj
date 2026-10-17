/*
  JSLinux-deobfuscated - An annotated version of the original JSLinux.

  Original is Copyright (c) 2011-2012 Fabrice Bellard
  Redistribution or commercial use is prohibited without the author's permission.

  8259A PIC (Programmable Interrupt Controller) Emulation Code

  The 8259 combines multiple interrupt input sources into a single
  interrupt output to the host microprocessor, extending the interrupt
  levels available in a system beyond the one or two levels found on the
  processor chip.

  There are three registers, an Interrupt Mask Register (IMR), an
  Interrupt Request Register (IRR), and an In-Service Register
  (ISR):
  IRR - a mask of the current interrupts that are pending acknowledgement
  ISR - a mask of the interrupts that are pending an EOI
  IMR - a mask of interrupts that should not be sent an acknowledgement

  End Of Interrupt (EOI) operations support specific EOI, non-specific
  EOI, and auto-EOI. A specific EOI specifies the IRQ level it is
  acknowledging in the ISR. A non-specific EOI resets the IRQ level in
  the ISR. Auto-EOI resets the IRQ level in the ISR immediately after
  the interrupt is acknowledged.

  After the IBM XT, it was decided that 8 IRQs was not enough.
  The backwards-compatible solution was simply to chain two 8259As together,
  the master and slave PIC.

  Useful References
  -----------------
  https://en.wikipedia.org/wiki/Programmable_Interrupt_Controller
  https://en.wikipedia.org/wiki/Intel_8259
  http://www.thesatya.com/8259.html
*/

/*
  Common PC arrangements of IRQ lines:
  ------------------------------------

  PC/AT and later systems had two 8259 controllers, master and
  slave. IRQ0 through IRQ7 are the master 8259's interrupt lines, while
  IRQ8 through IRQ15 are the slave 8259's interrupt lines. The labels on
  the pins on an 8259 are IR0 through IR7. IRQ0 through IRQ15 are the
  names of the ISA bus's lines to which the 8259s are attached.

  Master 8259
  IRQ0 – Intel 8253 or Intel 8254 Programmable Interval Timer, aka the system timer
  IRQ1 – Intel 8042 keyboard controller
  IRQ2 – not assigned in PC/XT; cascaded to slave 8259 INT line in PC/AT
  IRQ3 – 8250 UART serial ports 2 and 4
  IRQ4 – 8250 UART serial ports 1 and 3
  IRQ5 – hard disk controller in PC/XT; Intel 8255 parallel ports 2 and 3 in PC/AT
  IRQ6 – Intel 82072A floppy disk controller
  IRQ7 – Intel 8255 parallel port 1 / spurious interrupt

  Slave 8259 (PC/AT and later only)
  IRQ8 – real-time clock (RTC)
  IRQ9 – no common assignment, but 8-bit cards' IRQ2 line is routed to this interrupt.
  IRQ10 – no common assignment
  IRQ11 – no common assignment
  IRQ12 – Intel 8042 PS/2 mouse controller
  IRQ13 – math coprocessor
  IRQ14 – hard disk controller 1
  IRQ15 – hard disk controller 2
*/

function PIC(PC, port_num) {
    PC.register_ioport_write(port_num, 2, 1, this.ioport_write.bind(this));
    PC.register_ioport_read(port_num, 2, 1, this.ioport_read.bind(this));
    this.reset();
}
PIC.prototype.reset = function() {
    this.last_irr = 0;
    this.irr = 0; //Interrupt Request Register
    this.imr = 0; //Interrupt Mask Register
    this.isr = 0; //In-Service Register
    this.priority_add = 0;
    this.irq_base = 0;
    this.read_reg_select = 0;
    this.special_mask = 0;
    this.init_state = 0;
    this.auto_eoi = 0;
    this.rotate_on_autoeoi = 0;
    this.init4 = 0;
    this.elcr = 0; // Edge/Level Control Register
    this.elcr_mask = 0;
};
PIC.prototype.set_irq1 = function(irq, Qf) {
    var ir_register;
    ir_register = 1 << irq;
    if (Qf) {
        if ((this.last_irr & ir_register) == 0)
            this.irr |= ir_register;
        this.last_irr |= ir_register;
    } else {
        this.last_irr &= ~ir_register;
    }
};
/*
  The priority assignments for IRQ0-7 seem to be maintained in a
  cyclic order modulo 8 by the 8259A.  On bootup, it default to:

  Priority: 0 1 2 3 4 5 6 7
  IRQ:      7 6 5 4 3 2 1 0

  but can be rotated automatically or programmatically to a state e.g.:

  Priority: 5 6 7 0 1 2 3 4
  IRQ:      7 6 5 4 3 2 1 0
*/
PIC.prototype.get_priority = function(ir_register) {
    var priority;
    if (ir_register == 0)
        return -1;
    priority = 7;
    while ((ir_register & (1 << ((priority + this.priority_add) & 7))) == 0)
        priority--;
    return priority;
};
PIC.prototype.get_irq = function() {
    var ir_register, in_service_priority, priority;
    ir_register = this.irr & ~this.imr;
    priority = this.get_priority(ir_register);
    if (priority < 0)
        return -1;
    in_service_priority = this.get_priority(this.isr);
    if (priority > in_service_priority) {
        return priority;
    } else {
        return -1;
    }
};
PIC.prototype.intack = function(irq) {
    if (this.auto_eoi) {
        if (this.rotate_on_auto_eoi)
            this.priority_add = (irq + 1) & 7;
    } else {
        this.isr |= (1 << irq);
    }
    if (!(this.elcr & (1 << irq)))
        this.irr &= ~(1 << irq);
};
PIC.prototype.ioport_write = function(mem8_loc, x) {
    var priority;
    mem8_loc &= 1;
    if (mem8_loc == 0) {
        if (x & 0x10) {
	    /*
	      ICW1
	      // 7:5 = address (if MCS-80/85 mode)
	      // 4 == 1
	      // 3: 1 == level triggered, 0 == edge triggered
	      // 2: 1 == call interval 4, 0 == call interval 8
	      // 1: 1 == single PIC, 0 == cascaded PICs
	      // 0: 1 == send ICW4

	     */
            this.reset();
            this.init_state = 1;
            this.init4 = x & 1;
            if (x & 0x02)
                throw "single mode not supported";
            if (x & 0x08)
                throw "level sensitive irq not supported";
        } else if (x & 0x08) {
            if (x & 0x02)
                this.read_reg_select = x & 1;
            if (x & 0x40)
                this.special_mask = (x >> 5) & 1;
        } else {
            switch (x) {
                case 0x00:
                case 0x80:
                    this.rotate_on_autoeoi = x >> 7;
                    break;
                case 0x20:
                case 0xa0:
                    priority = this.get_priority(this.isr);
                    if (priority >= 0) {
                        this.isr &= ~(1 << ((priority + this.priority_add) & 7));
                    }
                    if (x == 0xa0)
                        this.priority_add = (this.priority_add + 1) & 7;
                    break;
                case 0x60:
                case 0x61:
                case 0x62:
                case 0x63:
                case 0x64:
                case 0x65:
                case 0x66:
                case 0x67:
                    priority = x & 7;
                    this.isr &= ~(1 << priority);
                    break;
                case 0xc0:
                case 0xc1:
                case 0xc2:
                case 0xc3:
                case 0xc4:
                case 0xc5:
                case 0xc6:
                case 0xc7:
                    this.priority_add = (x + 1) & 7;
                    break;
                case 0xe0:
                case 0xe1:
                case 0xe2:
                case 0xe3:
                case 0xe4:
                case 0xe5:
                case 0xe6:
                case 0xe7:
                    priority = x & 7;
                    this.isr &= ~(1 << priority);
                    this.priority_add = (priority + 1) & 7;
                    break;
            }
        }
    } else {
        switch (this.init_state) {
            case 0:
                this.imr = x;
                this.update_irq();
                break;
            case 1:
                this.irq_base = x & 0xf8;
                this.init_state = 2;
                break;
            case 2:
                if (this.init4) {
                    this.init_state = 3;
                } else {
                    this.init_state = 0;
                }
                break;
            case 3:
                this.auto_eoi = (x >> 1) & 1;
                this.init_state = 0;
                break;
        }
    }
};
PIC.prototype.ioport_read = function(Ug) {
    var mem8_loc, return_register;
    mem8_loc = Ug & 1;
    if (mem8_loc == 0) {
        if (this.read_reg_select)
            return_register = this.isr;
        else
            return_register = this.irr;
    } else {
        return_register = this.imr;
    }
    return return_register;
};


function PIC_Controller(PC, master_PIC_port, slave_PIC_port, cpu_set_irq_callback) {
    this.pics = new Array();
    this.pics[0] = new PIC(PC, master_PIC_port);
    this.pics[1] = new PIC(PC, slave_PIC_port);
    this.pics[0].elcr_mask = 0xf8;
    this.pics[1].elcr_mask = 0xde;
    this.irq_requested = 0;
    this.cpu_set_irq = cpu_set_irq_callback;
    this.pics[0].update_irq = this.update_irq.bind(this);
    this.pics[1].update_irq = this.update_irq.bind(this);
}
PIC_Controller.prototype.update_irq = function() {
    var slave_irq, irq;
    slave_irq = this.pics[1].get_irq();
    if (slave_irq >= 0) {
        this.pics[0].set_irq1(2, 1);
        this.pics[0].set_irq1(2, 0);
    }
    irq = this.pics[0].get_irq();
    if (irq >= 0) {
        this.cpu_set_irq(1);
    } else {
        this.cpu_set_irq(0);
    }
};
PIC_Controller.prototype.set_irq = function(irq, Qf) {
    this.pics[irq >> 3].set_irq1(irq & 7, Qf);
    this.update_irq();
};
PIC_Controller.prototype.get_hard_intno = function() {
    var irq, slave_irq, intno;
    irq = this.pics[0].get_irq();
    if (irq >= 0) {
        this.pics[0].intack(irq);
        if (irq == 2) { //IRQ 2 cascaded to slave 8259 INT line in PC/AT
            slave_irq = this.pics[1].get_irq();
            if (slave_irq >= 0) {
                this.pics[1].intack(slave_irq);
            } else {
                slave_irq = 7;
            }
            intno = this.pics[1].irq_base + slave_irq;
            irq = slave_irq + 8;
        } else {
            intno = this.pics[0].irq_base + irq;
        }
    } else {
        irq = 7;
        intno = this.pics[0].irq_base + irq;
    }
    this.update_irq();
    return intno;
};


