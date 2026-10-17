De-obfuscated JSLinux
=========================================================

This repo corresponds to a very old version of jslinux, Mr. Bellard 
currently uses an emscripten converted emulator for the current jslinux.

There are better emulator codebases out there for pedagogical use!

I highly recommend checking out another open-source x86 emulation
project that includes vga support, "v86" ([demo][6] / [source][7]).
There's yet another open-source 386-style emulator in javascript
called [jslm32][3].

For a simpler RISC architecture, take a look at the linux on
[jor1k][5] emulator project.

Finally, the [Angel][8] emulator ([source][9]) shows off the elegant
open-ISA 64bit [RISC-V][10] architecture that is being brought to
silicon by the [lowrisc][11] team.  This is by far the cleanest
architecture for studying general low-level hardware and operating
system implementation details.

### History
I wanted to understand how the amazing [JsLinux][1] worked, so in a
fit of mania I hand de-obfuscated the codebase while studying it over
a few days' time.  In the off-chance someone else might be interested
in this code as a basis for further weird in-browser x86 hacking I
posted this redacted version of the code here, with permission of
Mr. Bellard.

### Status

The current codebase should run on most modern versions of Chrome,
Safari, and Firefox.  If you're running it locally, you will need to
load it via a local server to allow the XHR requests to load the
binaries.

jslinux-deobfuscated is still a dense, messy code base from any
pedagogic point of view.  However for those interested in
Mr. Bellard's code, this version is nowhere near so incomprehensible
as the original.  Nearly all of the global variables and function
names have been named somewhat sensibly.  Many pointers to references
have been added to the source.

The core opcode execution loop has been commented to indicate what
instruction the opcode refers to.

### Unresolved

One mystery is, why does CPUID(1) return 8 << 8 in EBX? EBX[15:8] is
now used to indicate CLFLUSH line size, but that field must have been
used for something else in the past.

The CALL/RET/INT/IRET routines are still quite confused and haven't
yet been rewritten.  The code dealing with segmentation, and some of
the code for real-mode remains relatively messy.

Any recommendations / clarifications are welcome!

### ETC

I highly recommend, by the way, the excellent [JSShaper][2] library
for transforming large javascript code bases.  The hacks I made from
it are in this repo: a little symbol-name-transformer node.js script
and an emacs function for doing this in live buffers.

### License

This is a pedagogical/aesthetic derivative of the original JSLinux
code Copyright (c) 2011-2014 Fabrice Bellard.  It is posted here with
permission of the original author subject to his original
constraints : Redistribution or commercial use is prohibited without
the (original) author's permission.

### References
Some other helpful references for understanding what's going on:

#### x86
- http://pdos.csail.mit.edu/6.828/2010/readings/i386/toc.htm
- http://pdos.csail.mit.edu/6.828/2010/readings/i386.pdf (PDF of above)
- http://ref.x86asm.net/coder32.html
- http://www.sandpile.org/
- http://en.wikibooks.org/wiki/X86_Assembly/X86_Architecture
- http://en.wikipedia.org/wiki/X86
- http://en.wikipedia.org/wiki/Control_register
- http://en.wikipedia.org/wiki/X86_assembly_language
- http://en.wikipedia.org/wiki/Translation_lookaside_buffer

#### Bit Hacking
- http://graphics.stanford.edu/~seander/bithacks.html

#### Other devices
- http://en.wikibooks.org/wiki/Serial_Programming/8250_UART_Programming

[1]: http://bellard.org/jslinux/tech.html
[2]: http://jsshaper.org
[3]: https://github.com/ubercomp/jslm32
[4]: https://bugs.webkit.org/show_bug.cgi?id=72154
[5]: https://github.com/s-macke/jor1k
[6]: http://copy.sh/v86/
[7]: https://github.com/copy/v86
[8]: http://riscv.org/angel/
[9]: https://github.com/ucb-bar/riscv-angel
[10]: http://riscv.org/
[11]: http://www.lowrisc.org/
