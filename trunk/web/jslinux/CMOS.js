/*
  JSLinux-deobfuscated - An annotated version of the original JSLinux.

  Original is Copyright (c) 2011-2012 Fabrice Bellard
  Redistribution or commercial use is prohibited without the author's permission.

  CMOS Ram Memory, actually just the RTC Clock Emulator

  Useful references:
  ------------------
  http://www.bioscentral.com/misc/cmosmap.htm
  http://wiki.osdev.org/CMOS
*/

/*
   In this implementation, bytes are stored in the RTC in BCD format
   binary -> bcd: bcd = ((bin / 10) << 4) | (bin % 10)
   bcd -> binary: bin = ((bcd / 16) * 10) + (bcd & 0xf)
*/
function bin_to_bcd(a) { return ((a / 10) << 4) | (a % 10);}

function CMOS(PC) {
    var time_array, d;
    time_array = new Uint8Array(128);
    this.cmos_data = time_array;
    this.cmos_index = 0;
    d = new Date();
    time_array[0] = bin_to_bcd(d.getUTCSeconds());
    time_array[2] = bin_to_bcd(d.getUTCMinutes());
    time_array[4] = bin_to_bcd(d.getUTCHours());
    time_array[6] = bin_to_bcd(d.getUTCDay());
    time_array[7] = bin_to_bcd(d.getUTCDate());
    time_array[8] = bin_to_bcd(d.getUTCMonth() + 1);
    time_array[9] = bin_to_bcd(d.getUTCFullYear() % 100);
    time_array[10] = 0x26;
    time_array[11] = 0x02;
    time_array[12] = 0x00;
    time_array[13] = 0x80;
    time_array[0x14] = 0x02;
    PC.register_ioport_write(0x70, 2, 1, this.ioport_write.bind(this));
    PC.register_ioport_read(0x70, 2, 1, this.ioport_read.bind(this));
}
CMOS.prototype.ioport_write = function(mem8_loc, data) {
    if (mem8_loc == 0x70) {
	    // the high order bit is used to indicate NMI masking
        // low order bits are used to address CMOS
        // the index written here is used on an ioread 0x71
        this.cmos_index = data & 0x7f;
    }
};
CMOS.prototype.ioport_read = function(mem8_loc) {
    var data;
    if (mem8_loc == 0x70) {
        return 0xff;
    } else {
	    // else here => 0x71, i.e., CMOS read
        data = this.cmos_data[this.cmos_index];
        if (this.cmos_index == 10)
	    // flip the UIP (update in progress) bit on a read
            this.cmos_data[10] ^= 0x80;
        else if (this.cmos_index == 12)
	    // Always return interrupt status == 0
            this.cmos_data[12] = 0x00;
        return data;
    }
};
