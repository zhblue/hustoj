/* 
   Linux launcher

   Copyright (c) 2011 Fabrice Bellard

   Redistribution or commercial use is prohibited without the author's
   permission.
*/
"use strict";

var term, pc, boot_start_time;

function term_start()
{
    term = new Term(80, 30, term_handler);

    term.open();
}

/* send chars to the serial port */
function term_handler(str)
{
    pc.serial.send_chars(str);
}

function clipboard_set(val)
{
    var el;
    el = document.getElementById("text_clipboard");
    el.value = val;
}

function clipboard_get()
{
    var el;
    el = document.getElementById("text_clipboard");
    return el.value;
}

function clear_clipboard()
{
    var el;
    el = document.getElementById("text_clipboard");
    el.value = "";
}

/* just used to display the boot time in the VM */
function get_boot_time()
{
    return (+new Date()) - boot_start_time;
}

/* global to hold binary data from async XHR requests */
var binaries = [false,false,false];

function loadbinary(url,slot) {
    var req, binary_array, len, typed_arrays_exist;

    req = new XMLHttpRequest();

    req.open('GET', url, true);

    typed_arrays_exist = ('ArrayBuffer' in window && 'Uint8Array' in window);
    if (typed_arrays_exist && 'mozResponseType' in req) {
        req.mozResponseType = 'arraybuffer';
    } else if (typed_arrays_exist && 'responseType' in req) {
        req.responseType = 'arraybuffer';
    } else {
        req.overrideMimeType('text/plain; charset=x-user-defined');
        typed_arrays_exist = false;
    }

    req.onerror = function(e) {
      throw "Error while loading " + req.statusText;
    };

    req.onload = function (e) {
      console.log('onload triggered');
      if (req.readyState === 4) {
        if (req.status === 200) {
          if (typed_arrays_exist && 'mozResponse' in req) {
            binaries[slot] = req.mozResponse;
          } else if (typed_arrays_exist && req.mozResponseArrayBuffer) {
            binaries[slot] = req.mozResponseArrayBuffer;
          } else if ('responseType' in req) {
            binaries[slot] = req.response;
          } else {
            binaries[slot] = req.responseText;
          }
          //cb_f()
        } else {
          throw "Error while loading " + url;
        }
      }
    }

    req.send(null);
};

function checkbinaries() {
    //console.log("checkbinaries: ",(binaries[0]!=false),(binaries[1]!=false),(binaries[2]!=false));
    if((binaries[0] != false) && (binaries[1] != false) && (binaries[2] != false)){
        console.log("...binaries done loading, calling start()")
        start();
    } else {
         setTimeout(checkbinaries, 500);
    }
};

function load_binaries() {
    console.log("requesting binaries");
    loadbinary("vmlinux-2.6.20.bin", 0);
    loadbinary("root.bin", 1);
    loadbinary("linuxstart.bin", 2);

    console.log("waiting for binaries to finish loading...");
    checkbinaries();
}

function start()
{
    var start_addr, initrd_size, params, cmdline_addr;
    
    params = new Object();

    /* serial output chars */
    params.serial_write = term.write.bind(term);

    /* memory size (in bytes) */
    params.mem_size = 16 * 1024 * 1024;

    /* clipboard I/O */
    params.clipboard_get = clipboard_get;
    params.clipboard_set = clipboard_set;

    params.get_boot_time = get_boot_time;

    pc = new PCEmulator(params);

    pc.load_binary(binaries[0], 0x00100000);

    initrd_size = pc.load_binary(binaries[1], 0x00400000);

    start_addr = 0x10000;
    pc.load_binary(binaries[2], start_addr);

    /* set the Linux kernel command line */
    /* Note: we don't use initramfs because it is not possible to
       disable gzip decompression in this case, which would be too
       slow. */
    cmdline_addr = 0xf800;
    pc.cpu.write_string(cmdline_addr, "console=ttyS0 root=/dev/ram0 rw init=/sbin/init notsc=1");

    pc.cpu.eip = start_addr;
    pc.cpu.regs[0] = params.mem_size; /* eax */
    pc.cpu.regs[3] = initrd_size; /* ebx */
    pc.cpu.regs[1] = cmdline_addr; /* ecx */

    boot_start_time = (+new Date());

    pc.start();
}

term_start();
