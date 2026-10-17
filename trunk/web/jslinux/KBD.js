/*
JSLinux-deobfuscated - An annotated version of the original JSLinux.

Original is Copyright (c) 2011-2012 Fabrice Bellard
Redistribution or commercial use is prohibited without the author's permission.

Keyboard Device Emulator
*/
function KBD(PC, reset_callback) {
    PC.register_ioport_read(0x64, 1, 1, this.read_status.bind(this));
    PC.register_ioport_write(0x64, 1, 1, this.write_command.bind(this));
    this.reset_request = reset_callback;
}
KBD.prototype.read_status = function(mem8_loc) {
    return 0;
};
KBD.prototype.write_command = function(mem8_loc, x) {
    switch (x) {
        case 0xfe: // Resend command. Other commands are, apparently, ignored.
            this.reset_request();
            break;
        default:
            break;
    }
};


