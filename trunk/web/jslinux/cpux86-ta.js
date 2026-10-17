/*
JSLinux-deobfuscated - An annotated version of the original JSLinux.

Original is Copyright (c) 2011-2012 Fabrice Bellard
Redistribution or commercial use is prohibited without the author's permission.

A x86 CPU (circa 486 sans FPU) Emulator
======================================================================

Useful references:
======================================================================

http://pdos.csail.mit.edu/6.828/2005/readings/i386/   <-- super useful

http://ref.x86asm.net/coder32.html#xC4
http://en.wikibooks.org/wiki/X86_Assembly/X86_Architecture
http://en.wikipedia.org/wiki/X86
http://en.wikipedia.org/wiki/Control_register
http://en.wikipedia.org/wiki/X86_assembly_language
http://en.wikipedia.org/wiki/Translation_lookaside_buffer

http://bellard.org/jslinux/tech.html :
The exact restrictions of the emulated CPU are:
- No FPU/MMX/SSE
- No segment limit and right checks when accessing memory
- No single-stepping


Memory Modes
=====================================================================

The x86 transforms logical addresses (i.e., addresses as viewed by
programmers) into physical address (i.e., actual addresses in physical
memory) in two steps:

- Segment translation, in which a logical address (consisting of a
segment selector and segment offset) are converted to a linear
address.

- Page translation, in which a linear address is converted to
a physical address. This step is optional, at the discretion of
systems-software designers.

Paged Memory
--------------
A page table is simply an array of 32-bit page specifiers. A page
table is itself a page, and therefore contains 4 Kilobytes of memory
or at most 1K 32-bit entries.  Two levels of tables are used to
address a page of memory. At the higher level is a page directory. The
page directory addresses up to 1K page tables of the second level. A
page table of the second level addresses up to 1K pages. All the
tables addressed by one page directory, therefore, can address 1M
pages (2^(20)). Because each page contains 4K bytes 2^(12) bytes), the
tables of one page directory can span the entire physical address
space of the 80386 (2^(20) times 2^(12) = 2^(32)).


Hints for Bit Twiddling
=========================================================
X & (2^N-1) = mask for lower N bits of X
X & -2^N    = mask for upper N bits of X  (for two's complement)

X & 3       = mask for lower 2 bits for X
X & 7       = mask for lower 7 bits for X
X & -4096   = mask for upper 20 bits for X

((x << 16) >> 16)  = clears top 16bits, enforces word-size data
((x << 24) >> 24)  = clears top 24bits, enforces byte-size data

(1<<0 | 1<<4 | 1<<7)  = sets bits 0,4,7 to 1, rest to 0

*/

/* Parity Check by LUT:
   static const bool ParityTable256[256] = {
   #   define P2(n) n, n^1, n^1, n
   #   define P4(n) P2(n), P2(n^1), P2(n^1), P2(n)
   #   define P6(n) P4(n), P4(n^1), P4(n^1), P4(n)
   P6(0), P6(1), P6(1), P6(0) };
   unsigned char b;  // byte value to compute the parity of
   bool parity = ParityTable256[b];
   // OR, for 32-bit words:    unsigned int v; v ^= v >> 16; v ^= v >> 8; bool parity = ParityTable256[v & 0xff];
   // Variation:               unsigned char * p = (unsigned char *) &v; parity = ParityTable256[p[0] ^ p[1] ^ p[2] ^ p[3]];
*/
var parity_LUT = [1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1, 0, 0, 1];

var shift16_LUT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
var shift8_LUT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 0, 1, 2, 3, 4, 5, 6, 7, 8, 0, 1, 2, 3, 4, 5, 6, 7, 8, 0, 1, 2, 3, 4];

function CPU_X86() {
    var i, tlb_size;
    /*
      AX/EAX/RAX: Accumulator
      BX/EBX/RBX: Base index (for use with arrays)
      CX/ECX/RCX: Counter
      DX/EDX/RDX: Data/general
      SI/ESI/RSI: Source index for string operations.
      DI/EDI/RDI: Destination index for string operations.
      SP/ESP/RSP: Stack pointer for top address of the stack.
      BP/EBP/RBP: Stack base pointer for holding the address of the current stack frame.
    */
    this.regs = new Array(); // EAX, EBX, ECX, EDX, ESI, EDI, ESP, EBP  32bit registers
    for (i = 0; i < 8; i++) {
        this.regs[i] = 0;
    }

    /* IP/EIP/RIP: Instruction pointer. Holds the program counter, the current instruction address. */
    this.eip         = 0; //instruction pointer

    this.cc_op       = 0; // current op
    this.cc_dst      = 0; // current dest
    this.cc_src      = 0; // current src
    this.cc_op2      = 0; // current op, byte2
    this.cc_dst2     = 0; // current dest, byte2

    this.df          = 1; // Direction Flag

    /*
      0.    CF : Carry Flag. Set if the last arithmetic operation carried (addition) or borrowed (subtraction) a
                 bit beyond the size of the register. This is then checked when the operation is followed with
                 an add-with-carry or subtract-with-borrow to deal with values too large for just one register to contain.
      2.    PF : Parity Flag. Set if the number of set bits in the least significant byte is a multiple of 2.
      4.    AF : Adjust Flag. Carry of Binary Code Decimal (BCD) numbers arithmetic operations.
      6.    ZF : Zero Flag. Set if the result of an operation is Zero (0).
      7.    SF : Sign Flag. Set if the result of an operation is negative.
      8.    TF : Trap Flag. Set if step by step debugging.
      9.    IF : Interruption Flag. Set if interrupts are enabled.
      10.   DF : Direction Flag. Stream direction. If set, string operations will decrement their pointer rather
                 than incrementing it, reading memory backwards.
      11.   OF : Overflow Flag. Set if signed arithmetic operations result in a value too large for the register to contain.
      12-13. IOPL : I/O Privilege Level field (2 bits). I/O Privilege Level of the current process.
      14.   NT : Nested Task flag. Controls chaining of interrupts. Set if the current process is linked to the next process.
      16.   RF : Resume Flag. Response to debug exceptions.
      17.   VM : Virtual-8086 Mode. Set if in 8086 compatibility mode.
      18.   AC : Alignment Check. Set if alignment checking of memory references is done.
      19.   VIF : Virtual Interrupt Flag. Virtual image of IF.
      20.   VIP : Virtual Interrupt Pending flag. Set if an interrupt is pending.
      21.   ID : Identification Flag. Support for CPUID instruction if can be set.
    */
    this.eflags      = 0x2; // EFLAG register

    this.cycle_count = 0;
    this.hard_irq    = 0;
    this.hard_intno  = -1;
    this.cpl         = 0; //current privilege level

    /*
       Control Registers
       ==========================================================================================
    */
    /* CR0
       ---
       31    PG  Paging             If 1, enable paging and use the CR3 register, else disable paging
       30    CD  Cache disable      Globally enables/disable the memory cache
       29    NW  Not-write through  Globally enables/disable write-back caching
       18    AM  Alignment mask     Alignment check enabled if AM set, AC flag (in EFLAGS register) set, and privilege level is 3
       16    WP  Write protect      Determines whether the CPU can write to pages marked read-only
       5     NE  Numeric error      Enable internal x87 floating point error reporting when set, else enables PC style x87 error detection
       4     ET  Extension type     On the 386, it allowed to specify whether the external math coprocessor was an 80287 or 80387
       3     TS  Task switched      Allows saving x87 task context only after x87 instruction used after task switch
       2     EM  Emulation          If set, no x87 floating point unit present, if clear, x87 FPU present
       1     MP  Monitor co-processor   Controls interaction of WAIT/FWAIT instructions with TS flag in CR0
       0     PE  Protected Mode Enable  If 1, system is in protected mode, else system is in real mode
    */
    this.cr0 = (1 << 0); //PE-mode ON

    /* CR2
       ---
       Page Fault Linear Address (PFLA) When a page fault occurs,
       the address the program attempted to access is stored in the
       CR2 register. */
    this.cr2 = 0;

    /* CR3
       ---
       Used when virtual addressing is enabled, hence when the PG
       bit is set in CR0.  CR3 enables the processor to translate
       virtual addresses into physical addresses by locating the page
       directory and page tables for the current task.

       Typically, the upper 20 bits of CR3 become the page directory
       base register (PDBR), which stores the physical address of the
       first page directory entry.  */
    this.cr3 = 0;

    /* CR4
       ---
       Used in protected mode to control operations such as virtual-8086 support, enabling I/O breakpoints,
       page size extension and machine check exceptions.
       Bit  Name    Full Name   Description
       18   OSXSAVE XSAVE and Processor Extended States Enable
       17   PCIDE   PCID Enable If set, enables process-context identifiers (PCIDs).
       14   SMXE    SMX Enable
       13   VMXE    VMX Enable
       10   OSXMMEXCPT  Operating System Support for Unmasked SIMD Floating-Point Exceptions    If set, enables unmasked SSE exceptions.
       9    OSFXSR  Operating system support for FXSAVE and FXSTOR instructions If set, enables SSE instructions and fast FPU save & restore
       8    PCE Performance-Monitoring Counter enable
              If set, RDPMC can be executed at any privilege level, else RDPMC can only be used in ring 0.
       7    PGE Page Global Enabled If set, address translations (PDE or PTE records) may be shared between address spaces.
       6    MCE Machine Check Exception If set, enables machine check interrupts to occur.
       5    PAE Physical Address Extension
              If set, changes page table layout to translate 32-bit virtual addresses into extended 36-bit physical addresses.
       4    PSE Page Size Extensions    If unset, page size is 4 KB, else page size is increased to 4 MB (ignored with PAE set).
       3    DE  Debugging Extensions
       2    TSD Time Stamp Disable
              If set, RDTSC instruction can only be executed when in ring 0, otherwise RDTSC can be used at any privilege level.
       1    PVI Protected-mode Virtual Interrupts   If set, enables support for the virtual interrupt flag (VIF) in protected mode.
       0    VME Virtual 8086 Mode Extensions    If set, enables support for the virtual interrupt flag (VIF) in virtual-8086 mode.
     */
    this.cr4 = 0;

    /*
      Segment registers:
      --------------------
      ES: Extra
      CS: Code
      SS: Stack
      DS: Data
      FS: Extra
      GS: Extra

      In memory addressing for Intel x86 computer architectures,
      segment descriptors are a part of the segmentation unit, used for
      translating a logical address to a linear address. Segment descriptors
      describe the memory segment referred to in the logical address.

      The segment descriptor (8 bytes long in 80286) contains the following
      fields:

      - A segment base address
      - The segment limit which specifies the segment limit
      - Access rights byte containing the protection mechanism information
      - Control bits

    */
    /* NOTE: Only segs 0->5 appear to be used in the code, so only ES->GS */
    this.segs = new Array();   //   [" ES", " CS", " SS", " DS", " FS", " GS", "LDT", " TR"]
    for (i = 0; i < 7; i++) {
        this.segs[i] = {selector: 0, base: 0, limit: 0, flags: 0};
    }
    this.segs[2].flags = (1 << 22); // SS
    this.segs[1].flags = (1 << 22); // CS

    /* Interrupt Descriptor Table
       ---------------------------
       The interrupt descriptor table (IDT) associates each interrupt
       or exception identifier with a descriptor for the instructions
       that service the associated event. Like the GDT and LDTs, the
       IDT is an array of 8-byte descriptors. Unlike the GDT and LDTs,
       the first entry of the IDT may contain a descriptor.

       To form an index into the IDT, the processor multiplies the
       interrupt or exception identifier by eight. Because there are
       only 256 identifiers, the IDT need not contain more than 256
       descriptors. It can contain fewer than 256 entries; entries are
       required only for interrupt identifiers that are actually used. */
    this.idt         = {base: 0, limit: 0};

    // The Global Descriptor Table
    this.gdt         = {base: 0, limit: 0};

    // The Local Descriptor Table
    this.ldt = {selector: 0, base: 0, limit: 0, flags: 0};

    /* Task Register
       --------------
       The task register (TR) identifies the currently executing task
       by pointing to the TSS.

       The task register has both a "visible" portion (i.e., can be
       read and changed by instructions) and an "invisible" portion
       (maintained by the processor to correspond to the visible
       portion; cannot be read by any instruction). The selector in
       the visible portion selects a TSS descriptor in the GDT. The
       processor uses the invisible portion to cache the base and
       limit values from the TSS descriptor. Holding the base and
       limit in a register makes execution of the task more efficient,
       because the processor does not need to repeatedly fetch these
       values from memory when it references the TSS of the current
       task.

       The instructions LTR and STR are used to modify and read the
       visible portion of the task register. Both instructions take
       one operand, a 16-bit selector located in memory or in a
       general register.

       LTR (Load task register) loads the visible portion of the task
       register with the selector operand, which must select a TSS
       descriptor in the GDT. LTR also loads the invisible portion
       with information from the TSS descriptor selected by the
       operand. LTR is a privileged instruction; it may be executed
       only when CPL is zero. LTR is generally used during system
       initialization to give an initial value to the task register;
       thereafter, the contents of TR are changed by task switch
       operations.

       STR (Store task register) stores the visible portion of the task
       register in a general register or memory word. STR is not privileged.

      All the information the processor needs in order to manage a
      task is stored in a special type of segment, a task state
      segment (TSS). The fields of a TSS belong to two classes:

	  1. A dynamic set that the processor updates with each switch from the
	  task. This set includes the fields that store:

      - The general registers (EAX, ECX, EDX, EBX, ESP, EBP, ESI, EDI).
      - The segment registers (ES, CS, SS, DS, FS, GS).
      - The flags register (EFLAGS).
      - The instruction pointer (EIP).
      - The selector of the TSS of the previously executing task (updated only when a return is expected).

      2. A static set that the processor reads but does not change. This
      set includes the fields that store:

      - The selector of the task's LDT.
      - The register (PDBR) that contains the base address of the task's
        page directory (read only when paging is enabled).
      - Pointers to the stacks for privilege levels 0-2.
      - The T-bit (debug trap bit) which causes the processor to raise a
        debug exception when a task switch occurs.
      - The I/O map base
    */
    this.tr  = {selector: 0, base: 0, limit: 0, flags: 0};

    this.halted = 0;

    this.phys_mem = null;  //pointer to raw memory buffer allocated by browser

    /*
       A translation lookaside buffer (TLB) is a CPU cache that memory
       management hardware uses to improve virtual address translation
       speed.

       A TLB has a fixed number of slots that contain page table
       entries, which map virtual addresses to physical addresses. The
       virtual memory is the space seen from a process. This space is
       segmented in pages of a prefixed size. The page table
       (generally loaded in memory) keeps track of where the virtual
       pages are loaded in the physical memory. The TLB is a cache of
       the page table; that is, only a subset of its content are
       stored.
     */

    tlb_size = 0x100000; //2^20=1048576 * 4096 ~= 4GB total memory possible
    this.tlb_read_kernel  = new Int32Array(tlb_size);
    this.tlb_write_kernel = new Int32Array(tlb_size);
    this.tlb_read_user    = new Int32Array(tlb_size);
    this.tlb_write_user   = new Int32Array(tlb_size);
    for (i = 0; i < tlb_size; i++) {
        this.tlb_read_kernel[i]  = -1;
        this.tlb_write_kernel[i] = -1;
        this.tlb_read_user[i]    = -1;
        this.tlb_write_user[i]   = -1;
    }
    this.tlb_pages = new Int32Array(2048);
    this.tlb_pages_count = 0;
}

/* Allocates a memory chunnk new_mem_size bytes long and makes 8,16,32 bit array references into it */
CPU_X86.prototype.phys_mem_resize = function(new_mem_size) {
    this.mem_size = new_mem_size;
    new_mem_size += ((15 + 3) & ~3);
    this.phys_mem   = new ArrayBuffer(new_mem_size);
    this.phys_mem8  = new Uint8Array(this.phys_mem, 0, new_mem_size);
    this.phys_mem16 = new Uint16Array(this.phys_mem, 0, new_mem_size / 2);
    this.phys_mem32 = new Int32Array(this.phys_mem, 0, new_mem_size / 4);
};

/* Raw, low level memory access routines to alter the host-stored memory, these are called by the higher-level
   memory access emulation routines */
CPU_X86.prototype.ld8_phys  = function(mem8_loc)    {  return this.phys_mem8[mem8_loc]; };
CPU_X86.prototype.st8_phys  = function(mem8_loc, x) {         this.phys_mem8[mem8_loc] = x; };
CPU_X86.prototype.ld32_phys = function(mem8_loc)    {  return this.phys_mem32[mem8_loc >> 2]; };
CPU_X86.prototype.st32_phys = function(mem8_loc, x) {         this.phys_mem32[mem8_loc >> 2] = x; };

/*
   TLB Routines
   ==========================================================================================
*/
CPU_X86.prototype.tlb_set_page = function(mem8_loc, page_val, set_write_tlb, set_user_tlb) {
    var i, x, j;
    page_val &= -4096; // only top 20bits matter
    mem8_loc &= -4096; // only top 20bits matter
    x = mem8_loc ^ page_val; // XOR used to simulate hashing
    i = mem8_loc >>> 12; // top 20bits point to TLB
    if (this.tlb_read_kernel[i] == -1) {
        if (this.tlb_pages_count >= 2048) {
            this.tlb_flush_all1((i - 1) & 0xfffff);
        }
        this.tlb_pages[this.tlb_pages_count++] = i;
    }
    this.tlb_read_kernel[i] = x;
    if (set_write_tlb) {
        this.tlb_write_kernel[i] = x;
    } else {
        this.tlb_write_kernel[i] = -1;
    }
    if (set_user_tlb) {
        this.tlb_read_user[i] = x;
        if (set_write_tlb) {
            this.tlb_write_user[i] = x;
        } else {
            this.tlb_write_user[i] = -1;
        }
    } else {
        this.tlb_read_user[i] = -1;
        this.tlb_write_user[i] = -1;
    }
};

CPU_X86.prototype.tlb_flush_page = function(mem8_loc) {
    var i;
    i = mem8_loc >>> 12;
    this.tlb_read_kernel[i] = -1;
    this.tlb_write_kernel[i] = -1;
    this.tlb_read_user[i] = -1;
    this.tlb_write_user[i] = -1;
};

CPU_X86.prototype.tlb_flush_all = function() {
    var i, j, n, tlb_pages;
    tlb_pages = this.tlb_pages;
    n = this.tlb_pages_count;
    for (j = 0; j < n; j++) {
        i = tlb_pages[j];
        this.tlb_read_kernel[i] = -1;
        this.tlb_write_kernel[i] = -1;
        this.tlb_read_user[i] = -1;
        this.tlb_write_user[i] = -1;
    }
    this.tlb_pages_count = 0;
};

CPU_X86.prototype.tlb_flush_all1 = function(la) {
    var i, j, n, tlb_pages, new_n;
    tlb_pages = this.tlb_pages;
    n = this.tlb_pages_count;
    new_n = 0;
    for (j = 0; j < n; j++) {
        i = tlb_pages[j];
        if (i == la) {
            tlb_pages[new_n++] = i;
        } else {
            this.tlb_read_kernel[i] = -1;
            this.tlb_write_kernel[i] = -1;
            this.tlb_read_user[i] = -1;
            this.tlb_write_user[i] = -1;
        }
    }
    this.tlb_pages_count = new_n;
};


/*
   String / Logging Routines
   ==========================================================================================
*/

/* writes ASCII string in na into memory location mem8_loc */
CPU_X86.prototype.write_string = function(mem8_loc, str) {
    var i;
    for (i = 0; i < str.length; i++) {
        this.st8_phys(mem8_loc++, str.charCodeAt(i) & 0xff);
    }
    this.st8_phys(mem8_loc, 0);
};

/* Represents numeric value ga as n-digit HEX */
function hex_rep(x, n) {
    var i, s;
    var h = "0123456789ABCDEF";
    s = "";
    for (i = n - 1; i >= 0; i--) {
        s = s + h[(x >>> (i * 4)) & 15];
    }
    return s;
}
function _4_bytes_(n) { return hex_rep(n, 8);} // Represents 8-hex bytes of n
function _2_bytes_(n) { return hex_rep(n, 2);} // Represents 4-hex bytes of n
function _1_byte_(n) { return hex_rep(n, 4);}  // Represents 2-hex bytes of n

CPU_X86.prototype.dump_short = function() {
    console.log(" EIP=" + _4_bytes_(this.eip)    + " EAX=" + _4_bytes_(this.regs[0])
                + " ECX=" + _4_bytes_(this.regs[1]) + " EDX=" + _4_bytes_(this.regs[2]) + " EBX=" + _4_bytes_(this.regs[3]));
    console.log(" EFL=" + _4_bytes_(this.eflags) + " ESP=" + _4_bytes_(this.regs[4])
                + " EBP=" + _4_bytes_(this.regs[5]) + " ESI=" + _4_bytes_(this.regs[6]) + " EDI=" + _4_bytes_(this.regs[7]));
};

CPU_X86.prototype.dump = function() {
    var i, descriptor_table, str;
    var ta = [" ES", " CS", " SS", " DS", " FS", " GS", "LDT", " TR"];
    this.dump_short();
    console.log("TSC=" + _4_bytes_(this.cycle_count) + " OP=" + _2_bytes_(this.cc_op)
                + " SRC=" + _4_bytes_(this.cc_src) + " DST=" + _4_bytes_(this.cc_dst)
                + " OP2=" + _2_bytes_(this.cc_op2) + " DST2=" + _4_bytes_(this.cc_dst2));
    console.log("CPL=" + this.cpl + " CR0=" + _4_bytes_(this.cr0)
                + " CR2=" + _4_bytes_(this.cr2) + " CR3=" + _4_bytes_(this.cr3) + " CR4=" + _4_bytes_(this.cr4));
    str = "";
    for (i = 0; i < 8; i++) {
        if (i == 6)
            descriptor_table = this.ldt;
        else if (i == 7)
            descriptor_table = this.tr;
        else
            descriptor_table = this.segs[i];
        str += ta[i] + "=" + _1_byte_(descriptor_table.selector) + " " + _4_bytes_(descriptor_table.base) + " "
            + _4_bytes_(descriptor_table.limit) + " " + _1_byte_((descriptor_table.flags >> 8) & 0xf0ff);
        if (i & 1) {
            console.log(str);
            str = "";
        } else {
            str += " ";
        }
    }
    descriptor_table = this.gdt;
    str = "GDT=     " + _4_bytes_(descriptor_table.base) + " " + _4_bytes_(descriptor_table.limit) + "      ";
    descriptor_table = this.idt;
    str += "IDT=     " + _4_bytes_(descriptor_table.base) + " " + _4_bytes_(descriptor_table.limit);
    console.log(str);
};



/*
  The Beast
  ==========================================================================================
*/

CPU_X86.prototype.exec_internal = function(N_cycles, interrupt) {
    /*
      x,y,z,v are either just general non-local values or their exact specialization is unclear,
      esp. x,y look like they're used for everything

      I don't know what 'v' should be called, it's not clear yet
     */
    var cpu, mem8_loc, regs;
    var _src, _dst, _op, _op2, _dst2;
    var CS_flags, mem8, reg_idx0, OPbyte, reg_idx1, x, y, z, conditional_var, cycles_left, exit_code, v;
    var CS_base, SS_base, SS_mask, FS_usage_flag, init_CS_flags, iopl;//io privilege level
    var phys_mem8, last_tlb_val;
    var phys_mem16, phys_mem32;
    var tlb_read_kernel, tlb_write_kernel, tlb_read_user, tlb_write_user, _tlb_read_, _tlb_write_;


    /*
       Paged Memory Mode Access Routines
       ================================================================================
    */

    /* Storing XOR values as small lookup table is software equivalent of a Translation Lookaside Buffer (TLB) */
    function __ld_8bits_mem8_read() {
        var tlb_lookup;
        do_tlb_set_page(mem8_loc, 0, cpu.cpl == 3);
        tlb_lookup = _tlb_read_[mem8_loc >>> 12] ^ mem8_loc;
        return phys_mem8[tlb_lookup];
    }
    function ld_8bits_mem8_read() {
        var last_tlb_val;
        return (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
    }
    function __ld_16bits_mem8_read() {
        var x;
        x = ld_8bits_mem8_read();
        mem8_loc++;
        x |= ld_8bits_mem8_read() << 8;
        mem8_loc--;
        return x;
    }
    function ld_16bits_mem8_read() {
        var last_tlb_val;
        return (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) | mem8_loc) & 1 ? __ld_16bits_mem8_read() : phys_mem16[(mem8_loc ^ last_tlb_val) >> 1]);
    }
    function __ld_32bits_mem8_read() {
        var x;
        x = ld_8bits_mem8_read();
        mem8_loc++;
        x |= ld_8bits_mem8_read() << 8;
        mem8_loc++;
        x |= ld_8bits_mem8_read() << 16;
        mem8_loc++;
        x |= ld_8bits_mem8_read() << 24;
        mem8_loc -= 3;
        return x;
    }
    function ld_32bits_mem8_read() {
        var last_tlb_val;
        return (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) | mem8_loc) & 3 ? __ld_32bits_mem8_read() : phys_mem32[(mem8_loc ^ last_tlb_val) >> 2]);
    }
    function __ld_8bits_mem8_write() {
        var tlb_lookup;
        do_tlb_set_page(mem8_loc, 1, cpu.cpl == 3);
        tlb_lookup = _tlb_write_[mem8_loc >>> 12] ^ mem8_loc;
        return phys_mem8[tlb_lookup];
    }
    function ld_8bits_mem8_write() {
        var tlb_lookup;
        return ((tlb_lookup = _tlb_write_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_write() : phys_mem8[mem8_loc ^ tlb_lookup];
    }
    function __ld_16bits_mem8_write() {
        var x;
        x = ld_8bits_mem8_write();
        mem8_loc++;
        x |= ld_8bits_mem8_write() << 8;
        mem8_loc--;
        return x;
    }
    function ld_16bits_mem8_write() {
        var tlb_lookup;
        return ((tlb_lookup = _tlb_write_[mem8_loc >>> 12]) | mem8_loc) & 1 ? __ld_16bits_mem8_write() : phys_mem16[(mem8_loc ^ tlb_lookup) >> 1];
    }
    function __ld_32bits_mem8_write() {
        var x;
        x = ld_8bits_mem8_write();
        mem8_loc++;
        x |= ld_8bits_mem8_write() << 8;
        mem8_loc++;
        x |= ld_8bits_mem8_write() << 16;
        mem8_loc++;
        x |= ld_8bits_mem8_write() << 24;
        mem8_loc -= 3;
        return x;
    }
    function ld_32bits_mem8_write() {
        var tlb_lookup;
        return ((tlb_lookup = _tlb_write_[mem8_loc >>> 12]) | mem8_loc) & 3 ? __ld_32bits_mem8_write() : phys_mem32[(mem8_loc ^ tlb_lookup) >> 2];
    }
    function __st8_mem8_write(x) {
        var tlb_lookup;
        do_tlb_set_page(mem8_loc, 1, cpu.cpl == 3);
        tlb_lookup = _tlb_write_[mem8_loc >>> 12] ^ mem8_loc;
        phys_mem8[tlb_lookup] = x;
    }
    function st8_mem8_write(x) {
        var last_tlb_val;
        {
            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
            if (last_tlb_val == -1) {
                __st8_mem8_write(x);
            } else {
                phys_mem8[mem8_loc ^ last_tlb_val] = x;
            }
        }
    }
    function __st16_mem8_write(x) {
        st8_mem8_write(x);
        mem8_loc++;
        st8_mem8_write(x >> 8);
        mem8_loc--;
    }
    function st16_mem8_write(x) {
        var last_tlb_val;
        {
            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
            if ((last_tlb_val | mem8_loc) & 1) {
                __st16_mem8_write(x);
            } else {
                phys_mem16[(mem8_loc ^ last_tlb_val) >> 1] = x;
            }
        }
    }
    function __st32_mem8_write(x) {
        st8_mem8_write(x);
        mem8_loc++;
        st8_mem8_write(x >> 8);
        mem8_loc++;
        st8_mem8_write(x >> 16);
        mem8_loc++;
        st8_mem8_write(x >> 24);
        mem8_loc -= 3;
    }
    function st32_mem8_write(x) {
        var last_tlb_val;
        {
            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
            if ((last_tlb_val | mem8_loc) & 3) {
                __st32_mem8_write(x);
            } else {
                phys_mem32[(mem8_loc ^ last_tlb_val) >> 2] = x;
            }
        }
    }
    function __ld8_mem8_kernel_read() {
        var tlb_lookup;
        do_tlb_set_page(mem8_loc, 0, 0);
        tlb_lookup = tlb_read_kernel[mem8_loc >>> 12] ^ mem8_loc;
        return phys_mem8[tlb_lookup];
    }
    function ld8_mem8_kernel_read() {
        var tlb_lookup;
        return ((tlb_lookup = tlb_read_kernel[mem8_loc >>> 12]) == -1) ? __ld8_mem8_kernel_read() : phys_mem8[mem8_loc ^ tlb_lookup];
    }
    function __ld16_mem8_kernel_read() {
        var x;
        x = ld8_mem8_kernel_read();
        mem8_loc++;
        x |= ld8_mem8_kernel_read() << 8;
        mem8_loc--;
        return x;
    }
    function ld16_mem8_kernel_read() {
        var tlb_lookup;
        return ((tlb_lookup = tlb_read_kernel[mem8_loc >>> 12]) | mem8_loc) & 1 ? __ld16_mem8_kernel_read() : phys_mem16[(mem8_loc ^ tlb_lookup) >> 1];
    }
    function __ld32_mem8_kernel_read() {
        var x;
        x = ld8_mem8_kernel_read();
        mem8_loc++;
        x |= ld8_mem8_kernel_read() << 8;
        mem8_loc++;
        x |= ld8_mem8_kernel_read() << 16;
        mem8_loc++;
        x |= ld8_mem8_kernel_read() << 24;
        mem8_loc -= 3;
        return x;
    }
    function ld32_mem8_kernel_read() {
        var tlb_lookup;
        return ((tlb_lookup = tlb_read_kernel[mem8_loc >>> 12]) | mem8_loc) & 3 ? __ld32_mem8_kernel_read() : phys_mem32[(mem8_loc ^ tlb_lookup) >> 2];
    }
    function __st8_mem8_kernel_write(x) {
        var tlb_lookup;
        do_tlb_set_page(mem8_loc, 1, 0);
        tlb_lookup = tlb_write_kernel[mem8_loc >>> 12] ^ mem8_loc;
        phys_mem8[tlb_lookup] = x;
    }
    function st8_mem8_kernel_write(x) {
        var tlb_lookup;
        tlb_lookup = tlb_write_kernel[mem8_loc >>> 12];
        if (tlb_lookup == -1) {
            __st8_mem8_kernel_write(x);
        } else {
            phys_mem8[mem8_loc ^ tlb_lookup] = x;
        }
    }
    function __st16_mem8_kernel_write(x) {
        st8_mem8_kernel_write(x);
        mem8_loc++;
        st8_mem8_kernel_write(x >> 8);
        mem8_loc--;
    }
    function st16_mem8_kernel_write(x) {
        var tlb_lookup;
        tlb_lookup = tlb_write_kernel[mem8_loc >>> 12];
        if ((tlb_lookup | mem8_loc) & 1) {
            __st16_mem8_kernel_write(x);
        } else {
            phys_mem16[(mem8_loc ^ tlb_lookup) >> 1] = x;
        }
    }
    function __st32_mem8_kernel_write(x) {
        st8_mem8_kernel_write(x);
        mem8_loc++;
        st8_mem8_kernel_write(x >> 8);
        mem8_loc++;
        st8_mem8_kernel_write(x >> 16);
        mem8_loc++;
        st8_mem8_kernel_write(x >> 24);
        mem8_loc -= 3;
    }
    function st32_mem8_kernel_write(x) {
        var tlb_lookup;
        tlb_lookup = tlb_write_kernel[mem8_loc >>> 12];
        if ((tlb_lookup | mem8_loc) & 3) {
            __st32_mem8_kernel_write(x);
        } else {
            phys_mem32[(mem8_loc ^ tlb_lookup) >> 2] = x;
        }
    }

    var eip, physmem8_ptr, eip_tlb_val, initial_mem_ptr, eip_offset;

    function ld16_mem8_direct() {
        var x, y;
        x = phys_mem8[physmem8_ptr++];
        y = phys_mem8[physmem8_ptr++];
        return x | (y << 8);
    }

    /*
       Segmented Memory Mode Routines
       ================================================================================

       Segmented Memory
       -----------------
       x86 memory segmentation refers to the implementation of memory
       segmentation on the x86 architecture. Memory is divided into portions
       that may be addressed by a single index register without changing a
       16-bit segment selector. In real mode or V86 mode, a segment is always
       64 kilobytes in size (using 16-bit offsets). In protected mode, a
       segment can have variable length. Segments can overlap.

       Within the x86 architectures, when operating in the real (compatible)
       mode, physical address is computed as:

       Address = 16*segment + offset

       The 16-bit segment register is shifted
       left by 4 bits and added to a 16-bit offset, resulting in a 20-bit
       address.

       When the 80386 is used to execute software designed for architectures
       that don't have segments, it may be expedient to effectively "turn
       off" the segmentation features of the 80386. The 80386 does not have a
       mode that disables segmentation, but the same effect can be achieved
       by initially loading the segment registers with selectors for
       descriptors that encompass the entire 32-bit linear address
       space. Once loaded, the segment registers don't need to be
       changed. The 32-bit offsets used by 80386 instructions are adequate to
       address the entire linear-address space.

     */
    /*
       segment translation routine (I believe):
       Translates Logical Memory Address to Linear Memory Address
     */
    function segment_translation(mem8) {
        var base, mem8_loc, Qb, Rb, Sb, Tb;
        if (FS_usage_flag && (CS_flags & (0x000f | 0x0080)) == 0) {
            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                case 0x04:
                    Qb = phys_mem8[physmem8_ptr++];
                    base = Qb & 7;
                    if (base == 5) {
                        {
                            mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                    } else {
                        mem8_loc = regs[base];
                    }
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x0c:
                    Qb = phys_mem8[physmem8_ptr++];
                    mem8_loc = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    base = Qb & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x14:
                    Qb = phys_mem8[physmem8_ptr++];
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    base = Qb & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x05:
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    break;
                case 0x00:
                case 0x01:
                case 0x02:
                case 0x03:
                case 0x06:
                case 0x07:
                    base = mem8 & 7;
                    mem8_loc = regs[base];
                    break;
                case 0x08:
                case 0x09:
                case 0x0a:
                case 0x0b:
                case 0x0d:
                case 0x0e:
                case 0x0f:
                    mem8_loc = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    base = mem8 & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    break;
                case 0x10:
                case 0x11:
                case 0x12:
                case 0x13:
                case 0x15:
                case 0x16:
                case 0x17:
                default:
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    base = mem8 & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    break;
            }
            return mem8_loc;
        } else if (CS_flags & 0x0080) {
            if ((mem8 & 0xc7) == 0x06) {
                mem8_loc = ld16_mem8_direct();
                Tb = 3;
            } else {
                switch (mem8 >> 6) {
                    case 0:
                        mem8_loc = 0;
                        break;
                    case 1:
                        mem8_loc = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        break;
                    default:
                        mem8_loc = ld16_mem8_direct();
                        break;
                }
                switch (mem8 & 7) {
                    case 0:
                        mem8_loc = (mem8_loc + regs[3] + regs[6]) & 0xffff;
                        Tb = 3;
                        break;
                    case 1:
                        mem8_loc = (mem8_loc + regs[3] + regs[7]) & 0xffff;
                        Tb = 3;
                        break;
                    case 2:
                        mem8_loc = (mem8_loc + regs[5] + regs[6]) & 0xffff;
                        Tb = 2;
                        break;
                    case 3:
                        mem8_loc = (mem8_loc + regs[5] + regs[7]) & 0xffff;
                        Tb = 2;
                        break;
                    case 4:
                        mem8_loc = (mem8_loc + regs[6]) & 0xffff;
                        Tb = 3;
                        break;
                    case 5:
                        mem8_loc = (mem8_loc + regs[7]) & 0xffff;
                        Tb = 3;
                        break;
                    case 6:
                        mem8_loc = (mem8_loc + regs[5]) & 0xffff;
                        Tb = 2;
                        break;
                    case 7:
                    default:
                        mem8_loc = (mem8_loc + regs[3]) & 0xffff;
                        Tb = 3;
                        break;
                }
            }
            Sb = CS_flags & 0x000f;
            if (Sb == 0) {
                Sb = Tb;
            } else {
                Sb--;
            }
            mem8_loc = (mem8_loc + cpu.segs[Sb].base) >> 0;
            return mem8_loc;
        } else {
            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                case 0x04:
                    Qb = phys_mem8[physmem8_ptr++];
                    base = Qb & 7;
                    if (base == 5) {
                        {
                            mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                        base = 0;
                    } else {
                        mem8_loc = regs[base];
                    }
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x0c:
                    Qb = phys_mem8[physmem8_ptr++];
                    mem8_loc = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    base = Qb & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x14:
                    Qb = phys_mem8[physmem8_ptr++];
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    base = Qb & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    Rb = (Qb >> 3) & 7;
                    if (Rb != 4) {
                        mem8_loc = (mem8_loc + (regs[Rb] << (Qb >> 6))) >> 0;
                    }
                    break;
                case 0x05:
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    base = 0;
                    break;
                case 0x00:
                case 0x01:
                case 0x02:
                case 0x03:
                case 0x06:
                case 0x07:
                    base = mem8 & 7;
                    mem8_loc = regs[base];
                    break;
                case 0x08:
                case 0x09:
                case 0x0a:
                case 0x0b:
                case 0x0d:
                case 0x0e:
                case 0x0f:
                    mem8_loc = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    base = mem8 & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    break;
                case 0x10:
                case 0x11:
                case 0x12:
                case 0x13:
                case 0x15:
                case 0x16:
                case 0x17:
                default:
                    {
                        mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    base = mem8 & 7;
                    mem8_loc = (mem8_loc + regs[base]) >> 0;
                    break;
            }
            Sb = CS_flags & 0x000f;
            if (Sb == 0) {
                if (base == 4 || base == 5)
                    Sb = 2;
                else
                    Sb = 3;
            } else {
                Sb--;
            }
            mem8_loc = (mem8_loc + cpu.segs[Sb].base) >> 0;
            return mem8_loc;
        }
    }
    function segmented_mem8_loc_for_MOV() {
        var mem8_loc, Sb;
        if (CS_flags & 0x0080) {
            mem8_loc = ld16_mem8_direct();
        } else {
            {
                mem8_loc = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                physmem8_ptr += 4;
            }
        }
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        mem8_loc = (mem8_loc + cpu.segs[Sb].base) >> 0;
        return mem8_loc;
    }

    /*
       Register Manipulation
       ==========================================================================================
    */
    function set_word_in_register(reg_idx1, x) {
        /*
           if arg[0] is = 1xx  then set register xx's upper two bytes to two bytes in arg[1]
           if arg[0] is = 0xx  then set register xx's lower two bytes to two bytes in arg[1]
        */
        if (reg_idx1 & 4)
            regs[reg_idx1 & 3] = (regs[reg_idx1 & 3] & -65281) | ((x & 0xff) << 8);
        else
            regs[reg_idx1 & 3] = (regs[reg_idx1 & 3] & -256) | (x & 0xff);
    }

    function set_lower_word_in_register(reg_idx1, x) {
        regs[reg_idx1] = (regs[reg_idx1] & -65536) | (x & 0xffff);
    }

    /*
      Arithmetic Operations
      ==========================================================================================
    */
    function do_32bit_math(conditional_var, Yb, Zb) {
        var ac;
        switch (conditional_var) {
            case 0:
                _src = Zb;
                Yb = (Yb + Zb) >> 0;
                _dst = Yb;
                _op = 2;
                break;
            case 1:
                Yb = Yb | Zb;
                _dst = Yb;
                _op = 14;
                break;
            case 2:
                ac = check_carry();
                _src = Zb;
                Yb = (Yb + Zb + ac) >> 0;
                _dst = Yb;
                _op = ac ? 5 : 2;
                break;
            case 3:
                ac = check_carry();
                _src = Zb;
                Yb = (Yb - Zb - ac) >> 0;
                _dst = Yb;
                _op = ac ? 11 : 8;
                break;
            case 4:
                Yb = Yb & Zb;
                _dst = Yb;
                _op = 14;
                break;
            case 5:
                _src = Zb;
                Yb = (Yb - Zb) >> 0;
                _dst = Yb;
                _op = 8;
                break;
            case 6:
                Yb = Yb ^ Zb;
                _dst = Yb;
                _op = 14;
                break;
            case 7:
                _src = Zb;
                _dst = (Yb - Zb) >> 0;
                _op = 8;
                break;
            default:
                throw "arith" + cc + ": invalid op";
        }
        return Yb;
    }
    function do_16bit_math(conditional_var, Yb, Zb) {
        var ac;
        switch (conditional_var) {
            case 0:
                _src = Zb;
                Yb = (((Yb + Zb) << 16) >> 16);
                _dst = Yb;
                _op = 1;
                break;
            case 1:
                Yb = (((Yb | Zb) << 16) >> 16);
                _dst = Yb;
                _op = 13;
                break;
            case 2:
                ac = check_carry();
                _src = Zb;
                Yb = (((Yb + Zb + ac) << 16) >> 16);
                _dst = Yb;
                _op = ac ? 4 : 1;
                break;
            case 3:
                ac = check_carry();
                _src = Zb;
                Yb = (((Yb - Zb - ac) << 16) >> 16);
                _dst = Yb;
                _op = ac ? 10 : 7;
                break;
            case 4:
                Yb = (((Yb & Zb) << 16) >> 16);
                _dst = Yb;
                _op = 13;
                break;
            case 5:
                _src = Zb;
                Yb = (((Yb - Zb) << 16) >> 16);
                _dst = Yb;
                _op = 7;
                break;
            case 6:
                Yb = (((Yb ^ Zb) << 16) >> 16);
                _dst = Yb;
                _op = 13;
                break;
            case 7:
                _src = Zb;
                _dst = (((Yb - Zb) << 16) >> 16);
                _op = 7;
                break;
            default:
                throw "arith" + cc + ": invalid op";
        }
        return Yb;
    }
    function increment_16bit(x) {
        if (_op < 25) {
            _op2 = _op;
            _dst2 = _dst;
        }
        _dst = (((x + 1) << 16) >> 16);
        _op = 26;
        return _dst;
    }
    function decrement_16bit(x) {
        if (_op < 25) {
            _op2 = _op;
            _dst2 = _dst;
        }
        _dst = (((x - 1) << 16) >> 16);
        _op = 29;
        return _dst;
    }
    function do_8bit_math(conditional_var, Yb, Zb) {
        var ac;
        switch (conditional_var) {
            case 0:
                _src = Zb;
                Yb = (((Yb + Zb) << 24) >> 24);
                _dst = Yb;
                _op = 0;
                break;
            case 1:
                Yb = (((Yb | Zb) << 24) >> 24);
                _dst = Yb;
                _op = 12;
                break;
            case 2:
                ac = check_carry();
                _src = Zb;
                Yb = (((Yb + Zb + ac) << 24) >> 24);
                _dst = Yb;
                _op = ac ? 3 : 0;
                break;
            case 3:
                ac = check_carry();
                _src = Zb;
                Yb = (((Yb - Zb - ac) << 24) >> 24);
                _dst = Yb;
                _op = ac ? 9 : 6;
                break;
            case 4:
                Yb = (((Yb & Zb) << 24) >> 24);
                _dst = Yb;
                _op = 12;
                break;
            case 5:
                _src = Zb;
                Yb = (((Yb - Zb) << 24) >> 24);
                _dst = Yb;
                _op = 6;
                break;
            case 6:
                Yb = (((Yb ^ Zb) << 24) >> 24);
                _dst = Yb;
                _op = 12;
                break;
            case 7:
                _src = Zb;
                _dst = (((Yb - Zb) << 24) >> 24);
                _op = 6;
                break;
            default:
                throw "arith" + cc + ": invalid op";
        }
        return Yb;
    }
    function increment_8bit(x) {
        if (_op < 25) {
            _op2 = _op;
            _dst2 = _dst;
        }
        _dst = (((x + 1) << 24) >> 24);
        _op = 25;
        return _dst;
    }
    function decrement_8bit(x) {
        if (_op < 25) {
            _op2 = _op;
            _dst2 = _dst;
        }
        _dst = (((x - 1) << 24) >> 24);
        _op = 28;
        return _dst;
    }
    function shift8(conditional_var, Yb, Zb) {
        var kc, ac;
        switch (conditional_var) {
            case 0:
                if (Zb & 0x1f) {
                    Zb &= 0x7;
                    Yb &= 0xff;
                    kc = Yb;
                    Yb = (Yb << Zb) | (Yb >>> (8 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (Yb & 0x0001) | (((kc ^ Yb) << 4) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 1:
                if (Zb & 0x1f) {
                    Zb &= 0x7;
                    Yb &= 0xff;
                    kc = Yb;
                    Yb = (Yb >>> Zb) | (Yb << (8 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= ((Yb >> 7) & 0x0001) | (((kc ^ Yb) << 4) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 2:
                Zb = shift8_LUT[Zb & 0x1f];
                if (Zb) {
                    Yb &= 0xff;
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb << Zb) | (ac << (Zb - 1));
                    if (Zb > 1)
                        Yb |= kc >>> (9 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) << 4) & 0x0800) | ((kc >> (8 - Zb)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 3:
                Zb = shift8_LUT[Zb & 0x1f];
                if (Zb) {
                    Yb &= 0xff;
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb >>> Zb) | (ac << (8 - Zb));
                    if (Zb > 1)
                        Yb |= kc << (9 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) << 4) & 0x0800) | ((kc >> (Zb - 1)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 4:
            case 6:
                Zb &= 0x1f;
                if (Zb) {
                    _src = Yb << (Zb - 1);
                    _dst = Yb = (((Yb << Zb) << 24) >> 24);
                    _op = 15;
                }
                break;
            case 5:
                Zb &= 0x1f;
                if (Zb) {
                    Yb &= 0xff;
                    _src = Yb >>> (Zb - 1);
                    _dst = Yb = (((Yb >>> Zb) << 24) >> 24);
                    _op = 18;
                }
                break;
            case 7:
                Zb &= 0x1f;
                if (Zb) {
                    Yb = (Yb << 24) >> 24;
                    _src = Yb >> (Zb - 1);
                    _dst = Yb = (((Yb >> Zb) << 24) >> 24);
                    _op = 18;
                }
                break;
            default:
                throw "unsupported shift8=" + conditional_var;
        }
        return Yb;
    }
    function shift16(conditional_var, Yb, Zb) {
        var kc, ac;
        switch (conditional_var) {
            case 0:
                if (Zb & 0x1f) {
                    Zb &= 0xf;
                    Yb &= 0xffff;
                    kc = Yb;
                    Yb = (Yb << Zb) | (Yb >>> (16 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (Yb & 0x0001) | (((kc ^ Yb) >> 4) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 1:
                if (Zb & 0x1f) {
                    Zb &= 0xf;
                    Yb &= 0xffff;
                    kc = Yb;
                    Yb = (Yb >>> Zb) | (Yb << (16 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= ((Yb >> 15) & 0x0001) | (((kc ^ Yb) >> 4) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 2:
                Zb = shift16_LUT[Zb & 0x1f];
                if (Zb) {
                    Yb &= 0xffff;
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb << Zb) | (ac << (Zb - 1));
                    if (Zb > 1)
                        Yb |= kc >>> (17 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) >> 4) & 0x0800) | ((kc >> (16 - Zb)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 3:
                Zb = shift16_LUT[Zb & 0x1f];
                if (Zb) {
                    Yb &= 0xffff;
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb >>> Zb) | (ac << (16 - Zb));
                    if (Zb > 1)
                        Yb |= kc << (17 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) >> 4) & 0x0800) | ((kc >> (Zb - 1)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 4:
            case 6:
                Zb &= 0x1f;
                if (Zb) {
                    _src = Yb << (Zb - 1);
                    _dst = Yb = (((Yb << Zb) << 16) >> 16);
                    _op = 16;
                }
                break;
            case 5:
                Zb &= 0x1f;
                if (Zb) {
                    Yb &= 0xffff;
                    _src = Yb >>> (Zb - 1);
                    _dst = Yb = (((Yb >>> Zb) << 16) >> 16);
                    _op = 19;
                }
                break;
            case 7:
                Zb &= 0x1f;
                if (Zb) {
                    Yb = (Yb << 16) >> 16;
                    _src = Yb >> (Zb - 1);
                    _dst = Yb = (((Yb >> Zb) << 16) >> 16);
                    _op = 19;
                }
                break;
            default:
                throw "unsupported shift16=" + conditional_var;
        }
        return Yb;
    }
    function shift32(conditional_var, Yb, Zb) {
        var kc, ac;
        switch (conditional_var) {
            case 0:
                Zb &= 0x1f;
                if (Zb) {
                    kc = Yb;
                    Yb = (Yb << Zb) | (Yb >>> (32 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (Yb & 0x0001) | (((kc ^ Yb) >> 20) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 1:
                Zb &= 0x1f;
                if (Zb) {
                    kc = Yb;
                    Yb = (Yb >>> Zb) | (Yb << (32 - Zb));
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= ((Yb >> 31) & 0x0001) | (((kc ^ Yb) >> 20) & 0x0800);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 2:
                Zb &= 0x1f;
                if (Zb) {
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb << Zb) | (ac << (Zb - 1));
                    if (Zb > 1)
                        Yb |= kc >>> (33 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) >> 20) & 0x0800) | ((kc >> (32 - Zb)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 3:
                Zb &= 0x1f;
                if (Zb) {
                    kc = Yb;
                    ac = check_carry();
                    Yb = (Yb >>> Zb) | (ac << (32 - Zb));
                    if (Zb > 1)
                        Yb |= kc << (33 - Zb);
                    _src = conditional_flags_for_rot_shift_ops();
                    _src |= (((kc ^ Yb) >> 20) & 0x0800) | ((kc >> (Zb - 1)) & 0x0001);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                }
                break;
            case 4:
            case 6:
                Zb &= 0x1f;
                if (Zb) {
                    _src = Yb << (Zb - 1);
                    _dst = Yb = Yb << Zb;
                    _op = 17;
                }
                break;
            case 5:
                Zb &= 0x1f;
                if (Zb) {
                    _src = Yb >>> (Zb - 1);
                    _dst = Yb = Yb >>> Zb;
                    _op = 20;
                }
                break;
            case 7:
                Zb &= 0x1f;
                if (Zb) {
                    _src = Yb >> (Zb - 1);
                    _dst = Yb = Yb >> Zb;
                    _op = 20;
                }
                break;
            default:
                throw "unsupported shift32=" + conditional_var;
        }
        return Yb;
    }


    /*
       Bit Twiddling Functions
       ==========================================================================================
    */

    function op_16_SHRD_SHLD(conditional_var, Yb, Zb, pc) {
        var bool;
        pc &= 0x1f;
        if (pc) {
            if (conditional_var == 0) {
                Zb &= 0xffff;
                bool = Zb | (Yb << 16);
                _src = bool >> (32 - pc);
                bool <<= pc;
                if (pc > 16)
                    bool |= Zb << (pc - 16);
                Yb = _dst = bool >> 16;
                _op = 19;
            } else {
                bool = (Yb & 0xffff) | (Zb << 16);
                _src = bool >> (pc - 1);
                bool >>= pc;
                if (pc > 16)
                    bool |= Zb << (32 - pc);
                Yb = _dst = (((bool) << 16) >> 16);
                _op = 19;
            }
        }
        return Yb;
    }
    function op_SHLD(Yb, Zb, pc) {
        pc &= 0x1f;
        if (pc) {
            _src = Yb << (pc - 1);
            _dst = Yb = (Yb << pc) | (Zb >>> (32 - pc));
            _op = 17;
        }
        return Yb;
    }
    function op_SHRD(Yb, Zb, pc) {
        pc &= 0x1f;
        if (pc) {
            _src = Yb >> (pc - 1);
            _dst = Yb = (Yb >>> pc) | (Zb << (32 - pc));
            _op = 20;
        }
        return Yb;
    }
    function op_16_BT(Yb, Zb) {
        Zb &= 0xf;
        _src = Yb >> Zb;
        _op = 19;
    }
    function op_BT(Yb, Zb) {
        Zb &= 0x1f;
        _src = Yb >> Zb;
        _op = 20;
    }
    function op_16_BTS_BTR_BTC(conditional_var, Yb, Zb) {
        var wc;
        Zb &= 0xf;
        _src = Yb >> Zb;
        wc = 1 << Zb;
        switch (conditional_var) {
            case 1:
                Yb |= wc;
                break;
            case 2:
                Yb &= ~wc;
                break;
            case 3:
            default:
                Yb ^= wc;
                break;
        }
        _op = 19;
        return Yb;
    }
    function op_BTS_BTR_BTC(conditional_var, Yb, Zb) {
        var wc;
        Zb &= 0x1f;
        _src = Yb >> Zb;
        wc = 1 << Zb;
        switch (conditional_var) {
            case 1:
                Yb |= wc;
                break;
            case 2:
                Yb &= ~wc;
                break;
            case 3:
            default:
                Yb ^= wc;
                break;
        }
        _op = 20;
        return Yb;
    }
    function op_16_BSF(Yb, Zb) {
        Zb &= 0xffff;
        if (Zb) {
            Yb = 0;
            while ((Zb & 1) == 0) {
                Yb++;
                Zb >>= 1;
            }
            _dst = 1;
        } else {
            _dst = 0;
        }
        _op = 14;
        return Yb;
    }
    function op_BSF(Yb, Zb) {
        if (Zb) {
            Yb = 0;
            while ((Zb & 1) == 0) {
                Yb++;
                Zb >>= 1;
            }
            _dst = 1;
        } else {
            _dst = 0;
        }
        _op = 14;
        return Yb;
    }
    function op_16_BSR(Yb, Zb) {
        Zb &= 0xffff;
        if (Zb) {
            Yb = 15;
            while ((Zb & 0x8000) == 0) {
                Yb--;
                Zb <<= 1;
            }
            _dst = 1;
        } else {
            _dst = 0;
        }
        _op = 14;
        return Yb;
    }
    function op_BSR(Yb, Zb) {
        if (Zb) {
            Yb = 31;
            while (Zb >= 0) {
                Yb--;
                Zb <<= 1;
            }
            _dst = 1;
        } else {
            _dst = 0;
        }
        _op = 14;
        return Yb;
    }

    /*
      Multiply / Divide Functions
      ==========================================================================================
    */

    function op_DIV(OPbyte) {
        var a, q, r;
        a = regs[0] & 0xffff;
        OPbyte &= 0xff;
        if ((a >> 8) >= OPbyte)
            abort(0);
        q = (a / OPbyte) >> 0;
        r = (a % OPbyte);
        set_lower_word_in_register(0, (q & 0xff) | (r << 8));
    }
    function op_IDIV(OPbyte) {
        var a, q, r;
        a = (regs[0] << 16) >> 16;
        OPbyte = (OPbyte << 24) >> 24;
        if (OPbyte == 0)
            abort(0);
        q = (a / OPbyte) >> 0;
        if (((q << 24) >> 24) != q)
            abort(0);
        r = (a % OPbyte);
        set_lower_word_in_register(0, (q & 0xff) | (r << 8));
    }
    function op_16_DIV(OPbyte) {
        var a, q, r;
        a = (regs[2] << 16) | (regs[0] & 0xffff);
        OPbyte &= 0xffff;
        if ((a >>> 16) >= OPbyte)
            abort(0);
        q = (a / OPbyte) >> 0;
        r = (a % OPbyte);
        set_lower_word_in_register(0, q);
        set_lower_word_in_register(2, r);
    }
    function op_16_IDIV(OPbyte) {
        var a, q, r;
        a = (regs[2] << 16) | (regs[0] & 0xffff);
        OPbyte = (OPbyte << 16) >> 16;
        if (OPbyte == 0)
            abort(0);
        q = (a / OPbyte) >> 0;
        if (((q << 16) >> 16) != q)
            abort(0);
        r = (a % OPbyte);
        set_lower_word_in_register(0, q);
        set_lower_word_in_register(2, r);
    }
    function op_DIV32(Ic, Jc, OPbyte) {
        var a, i, Kc;
        Ic = Ic >>> 0;
        Jc = Jc >>> 0;
        OPbyte = OPbyte >>> 0;
        if (Ic >= OPbyte) {
            abort(0);
        }
        if (Ic >= 0 && Ic <= 0x200000) {
            a = Ic * 4294967296 + Jc;
            v = (a % OPbyte) >> 0;
            return (a / OPbyte) >> 0;
        } else {
            for (i = 0; i < 32; i++) {
                Kc = Ic >> 31;
                Ic = ((Ic << 1) | (Jc >>> 31)) >>> 0;
                if (Kc || Ic >= OPbyte) {
                    Ic = Ic - OPbyte;
                    Jc = (Jc << 1) | 1;
                } else {
                    Jc = Jc << 1;
                }
            }
            v = Ic >> 0;
            return Jc;
        }
    }
    function op_IDIV32(Ic, Jc, OPbyte) {
        var Mc, Nc, q;
        if (Ic < 0) {
            Mc = 1;
            Ic = ~Ic;
            Jc = (-Jc) >> 0;
            if (Jc == 0)
                Ic = (Ic + 1) >> 0;
        } else {
            Mc = 0;
        }
        if (OPbyte < 0) {
            OPbyte = (-OPbyte) >> 0;
            Nc = 1;
        } else {
            Nc = 0;
        }
        q = op_DIV32(Ic, Jc, OPbyte);
        Nc ^= Mc;
        if (Nc) {
            if ((q >>> 0) > 0x80000000)
                abort(0);
            q = (-q) >> 0;
        } else {
            if ((q >>> 0) >= 0x80000000)
                abort(0);
        }
        if (Mc) {
            v = (-v) >> 0;
        }
        return q;
    }
    function op_MUL(a, OPbyte) {
        var bool;
        a &= 0xff;
        OPbyte &= 0xff;
        bool = (regs[0] & 0xff) * (OPbyte & 0xff);
        _src = bool >> 8;
        _dst = (((bool) << 24) >> 24);
        _op = 21;
        return bool;
    }
    function op_IMUL(a, OPbyte) {
        var bool;
        a = (((a) << 24) >> 24);
        OPbyte = (((OPbyte) << 24) >> 24);
        bool = (a * OPbyte) >> 0;
        _dst = (((bool) << 24) >> 24);
        _src = (bool != _dst) >> 0;
        _op = 21;
        return bool;
    }
    function op_16_MUL(a, OPbyte) {
        var bool;
        bool = ((a & 0xffff) * (OPbyte & 0xffff)) >> 0;
        _src = bool >>> 16;
        _dst = (((bool) << 16) >> 16);
        _op = 22;
        return bool;
    }
    function op_16_IMUL(a, OPbyte) {
        var bool;
        a = (a << 16) >> 16;
        OPbyte = (OPbyte << 16) >> 16;
        bool = (a * OPbyte) >> 0;
        _dst = (((bool) << 16) >> 16);
        _src = (bool != _dst) >> 0;
        _op = 22;
        return bool;
    }
    function do_multiply32(a, OPbyte) {
        var r, Jc, Ic, Tc, Uc, m;
        a = a >>> 0;
        OPbyte = OPbyte >>> 0;
        r = a * OPbyte;
        if (r <= 0xffffffff) {
            v = 0;
            r &= -1;
        } else {
            Jc = a & 0xffff;
            Ic = a >>> 16;
            Tc = OPbyte & 0xffff;
            Uc = OPbyte >>> 16;
            r = Jc * Tc;
            v = Ic * Uc;
            m = Jc * Uc;
            r += (((m & 0xffff) << 16) >>> 0);
            v += (m >>> 16);
            if (r >= 4294967296) {
                r -= 4294967296;
                v++;
            }
            m = Ic * Tc;
            r += (((m & 0xffff) << 16) >>> 0);
            v += (m >>> 16);
            if (r >= 4294967296) {
                r -= 4294967296;
                v++;
            }
            r &= -1;
            v &= -1;
        }
        return r;
    }
    function op_MUL32(a, OPbyte) {
        _dst = do_multiply32(a, OPbyte);
        _src = v;
        _op = 23;
        return _dst;
    }
    function op_IMUL32(a, OPbyte) {
        var s, r;
        s = 0;
        if (a < 0) {
            a = -a;
            s = 1;
        }
        if (OPbyte < 0) {
            OPbyte = -OPbyte;
            s ^= 1;
        }
        r = do_multiply32(a, OPbyte);
        if (s) {
            v = ~v;
            r = (-r) >> 0;
            if (r == 0) {
                v = (v + 1) >> 0;
            }
        }
        _dst = r;
        _src = (v - (r >> 31)) >> 0;
        _op = 23;
        return r;
    }


    /*
      Status bits and Flags Routines
      ================================================================================
    */

    function check_carry() {
        var Yb, bool, current_op, relevant_dst;
        if (_op >= 25) {
            current_op = _op2;
            relevant_dst = _dst2;
        } else {
            current_op = _op;
            relevant_dst = _dst;
        }
        switch (current_op) {
            case 0:
                bool = (relevant_dst & 0xff) < (_src & 0xff);
                break;
            case 1:
                bool = (relevant_dst & 0xffff) < (_src & 0xffff);
                break;
            case 2:
                bool = (relevant_dst >>> 0) < (_src >>> 0);
                break;
            case 3:
                bool = (relevant_dst & 0xff) <= (_src & 0xff);
                break;
            case 4:
                bool = (relevant_dst & 0xffff) <= (_src & 0xffff);
                break;
            case 5:
                bool = (relevant_dst >>> 0) <= (_src >>> 0);
                break;
            case 6:
                bool = ((relevant_dst + _src) & 0xff) < (_src & 0xff);
                break;
            case 7:
                bool = ((relevant_dst + _src) & 0xffff) < (_src & 0xffff);
                break;
            case 8:
                bool = ((relevant_dst + _src) >>> 0) < (_src >>> 0);
                break;
            case 9:
                Yb = (relevant_dst + _src + 1) & 0xff;
                bool = Yb <= (_src & 0xff);
                break;
            case 10:
                Yb = (relevant_dst + _src + 1) & 0xffff;
                bool = Yb <= (_src & 0xffff);
                break;
            case 11:
                Yb = (relevant_dst + _src + 1) >>> 0;
                bool = Yb <= (_src >>> 0);
                break;
            case 12:
            case 13:
            case 14:
                bool = 0;
                break;
            case 15:
                bool = (_src >> 7) & 1;
                break;
            case 16:
                bool = (_src >> 15) & 1;
                break;
            case 17:
                bool = (_src >> 31) & 1;
                break;
            case 18:
            case 19:
            case 20:
                bool = _src & 1;
                break;
            case 21:
            case 22:
            case 23:
                bool = _src != 0;
                break;
            case 24:
                bool = _src & 1;
                break;
            default:
                throw "GET_CARRY: unsupported cc_op=" + _op;
        }
        return bool;
    }
    function check_overflow() {
        var bool, Yb;
        switch (_op) {
            case 0:
                Yb = (_dst - _src) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 7) & 1;
                break;
            case 1:
                Yb = (_dst - _src) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 15) & 1;
                break;
            case 2:
                Yb = (_dst - _src) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 31) & 1;
                break;
            case 3:
                Yb = (_dst - _src - 1) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 7) & 1;
                break;
            case 4:
                Yb = (_dst - _src - 1) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 15) & 1;
                break;
            case 5:
                Yb = (_dst - _src - 1) >> 0;
                bool = (((Yb ^ _src ^ -1) & (Yb ^ _dst)) >> 31) & 1;
                break;
            case 6:
                Yb = (_dst + _src) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 7) & 1;
                break;
            case 7:
                Yb = (_dst + _src) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 15) & 1;
                break;
            case 8:
                Yb = (_dst + _src) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 31) & 1;
                break;
            case 9:
                Yb = (_dst + _src + 1) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 7) & 1;
                break;
            case 10:
                Yb = (_dst + _src + 1) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 15) & 1;
                break;
            case 11:
                Yb = (_dst + _src + 1) >> 0;
                bool = (((Yb ^ _src) & (Yb ^ _dst)) >> 31) & 1;
                break;
            case 12:
            case 13:
            case 14:
                bool = 0;
                break;
            case 15:
            case 18:
                bool = ((_src ^ _dst) >> 7) & 1;
                break;
            case 16:
            case 19:
                bool = ((_src ^ _dst) >> 15) & 1;
                break;
            case 17:
            case 20:
                bool = ((_src ^ _dst) >> 31) & 1;
                break;
            case 21:
            case 22:
            case 23:
                bool = _src != 0;
                break;
            case 24:
                bool = (_src >> 11) & 1;
                break;
            case 25:
                bool = (_dst & 0xff) == 0x80;
                break;
            case 26:
                bool = (_dst & 0xffff) == 0x8000;
                break;
            case 27:
                bool = (_dst == -2147483648);
                break;
            case 28:
                bool = (_dst & 0xff) == 0x7f;
                break;
            case 29:
                bool = (_dst & 0xffff) == 0x7fff;
                break;
            case 30:
                bool = _dst == 0x7fffffff;
                break;
            default:
                throw "JO: unsupported cc_op=" + _op;
        }
        return bool;
    }
    function check_below_or_equal() {
        var bool;
        switch (_op) {
            case 6:
                bool = ((_dst + _src) & 0xff) <= (_src & 0xff);
                break;
            case 7:
                bool = ((_dst + _src) & 0xffff) <= (_src & 0xffff);
                break;
            case 8:
                bool = ((_dst + _src) >>> 0) <= (_src >>> 0);
                break;
            case 24:
                bool = (_src & (0x0040 | 0x0001)) != 0;
                break;
            default:
                bool = check_carry() | (_dst == 0);
                break;
        }
        return bool;
    }
    function check_parity() {
        if (_op == 24) {
            return (_src >> 2) & 1;
        } else {
            return parity_LUT[_dst & 0xff];
        }
    }
    function check_less_than() {
        var bool;
        switch (_op) {
            case 6:
                bool = ((_dst + _src) << 24) < (_src << 24);
                break;
            case 7:
                bool = ((_dst + _src) << 16) < (_src << 16);
                break;
            case 8:
                bool = ((_dst + _src) >> 0) < _src;
                break;
            case 12:
            case 25:
            case 28:
            case 13:
            case 26:
            case 29:
            case 14:
            case 27:
            case 30:
                bool = _dst < 0;
                break;
            case 24:
                bool = ((_src >> 7) ^ (_src >> 11)) & 1;
                break;
            default:
                bool = (_op == 24 ? ((_src >> 7) & 1) : (_dst < 0)) ^ check_overflow();
                break;
        }
        return bool;
    }
    function check_less_or_equal() {
        var bool;
        switch (_op) {
            case 6:
                bool = ((_dst + _src) << 24) <= (_src << 24);
                break;
            case 7:
                bool = ((_dst + _src) << 16) <= (_src << 16);
                break;
            case 8:
                bool = ((_dst + _src) >> 0) <= _src;
                break;
            case 12:
            case 25:
            case 28:
            case 13:
            case 26:
            case 29:
            case 14:
            case 27:
            case 30:
                bool = _dst <= 0;
                break;
            case 24:
                bool = (((_src >> 7) ^ (_src >> 11)) | (_src >> 6)) & 1;
                break;
            default:
                bool = ((_op == 24 ? ((_src >> 7) & 1) : (_dst < 0)) ^ check_overflow()) | (_dst == 0);
                break;
        }
        return bool;
    }
    function check_adjust_flag() {
        var Yb, bool;
        switch (_op) {
            case 0:
            case 1:
            case 2:
                Yb = (_dst - _src) >> 0;
                bool = (_dst ^ Yb ^ _src) & 0x10;
                break;
            case 3:
            case 4:
            case 5:
                Yb = (_dst - _src - 1) >> 0;
                bool = (_dst ^ Yb ^ _src) & 0x10;
                break;
            case 6:
            case 7:
            case 8:
                Yb = (_dst + _src) >> 0;
                bool = (_dst ^ Yb ^ _src) & 0x10;
                break;
            case 9:
            case 10:
            case 11:
                Yb = (_dst + _src + 1) >> 0;
                bool = (_dst ^ Yb ^ _src) & 0x10;
                break;
            case 12:
            case 13:
            case 14:
                bool = 0;
                break;
            case 15:
            case 18:
            case 16:
            case 19:
            case 17:
            case 20:
            case 21:
            case 22:
            case 23:
                bool = 0;
                break;
            case 24:
                bool = _src & 0x10;
                break;
            case 25:
            case 26:
            case 27:
                bool = (_dst ^ (_dst - 1)) & 0x10;
                break;
            case 28:
            case 29:
            case 30:
                bool = (_dst ^ (_dst + 1)) & 0x10;
                break;
            default:
                throw "AF: unsupported cc_op=" + _op;
        }
        return bool;
    }
    function check_status_bits_for_jump(gd) {
        var bool;
        switch (gd >> 1) {
            case 0:
                bool = check_overflow();
                break;
            case 1:
                bool = check_carry();
                break;
            case 2:
                bool = (_dst == 0);
                break;
            case 3:
                bool = check_below_or_equal();
                break;
            case 4:
                bool = (_op == 24 ? ((_src >> 7) & 1) : (_dst < 0));
                break;
            case 5:
                bool = check_parity();
                break;
            case 6:
                bool = check_less_than();
                break;
            case 7:
                bool = check_less_or_equal();
                break;
            default:
                throw "unsupported cond: " + gd;
        }
        return bool ^ (gd & 1);
    }
    function conditional_flags_for_rot_shift_ops() {
        return (check_parity() << 2) | ((_dst == 0) << 6) | ((_op == 24 ? ((_src >> 7) & 1) : (_dst < 0)) << 7) | check_adjust_flag();
    }
    function get_conditional_flags() {
        return (check_carry() << 0) | (check_parity() << 2) | ((_dst == 0) << 6) | ((_op == 24 ? ((_src >> 7) & 1) : (_dst < 0)) << 7) | (check_overflow() << 11) | check_adjust_flag();
    }
    /* The below two functions set the global condition flags */
    function get_FLAGS() {
        var flag_bits;
        flag_bits = get_conditional_flags();
        flag_bits |= cpu.df & 0x00000400; //direction flag
        flag_bits |= cpu.eflags; //get extended flags
        return flag_bits;
    }
    function set_FLAGS(flag_bits, ld) {
        _src = flag_bits & (0x0800 | 0x0080 | 0x0040 | 0x0010 | 0x0004 | 0x0001);
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
        cpu.df = 1 - (2 * ((flag_bits >> 10) & 1));
        cpu.eflags = (cpu.eflags & ~ld) | (flag_bits & ld);
    }


    /*
      Basic Debug Routines
      ================================================================================
    */

    function current_cycle_count() {
        return cpu.cycle_count + (N_cycles - cycles_left);
    }
    function cpu_abort(str) {
        throw "CPU abort: " + str;
    }
    function cpu_dump() {
        cpu.eip = eip;
        cpu.cc_src = _src;
        cpu.cc_dst = _dst;
        cpu.cc_op = _op;
        cpu.cc_op2 = _op2;
        cpu.cc_dst2 = _dst2;
        cpu.dump();
    }
    function cpu_dump_short() {
        cpu.eip = eip;
        cpu.cc_src = _src;
        cpu.cc_dst = _dst;
        cpu.cc_op = _op;
        cpu.cc_op2 = _op2;
        cpu.cc_dst2 = _dst2;
        cpu.dump_short();
    }

    /* Oh No You Didn't!
       Identifier   Description
       0            Divide error
       1            Debug exceptions
       2            Nonmaskable interrupt
       3            Breakpoint (one-byte INT 3 instruction)
       4            Overflow (INTO instruction)
       5            Bounds check (BOUND instruction)
       6            Invalid opcode
       7            Coprocessor not available
       8            Double fault
       9            (reserved)
       10           Invalid TSS
       11           Segment not present
       12           Stack exception
       13           General protection
       14           Page fault
       15           (reserved)
       16           Coprecessor error
       17-31        (reserved)
       32-255       Available for external interrupts via INTR pin

       The identifiers of the maskable interrupts are determined by external
       interrupt controllers (such as Intel's 8259A Programmable Interrupt
       Controller) and communicated to the processor during the processor's
       interrupt-acknowledge sequence. The numbers assigned by an 8259A PIC
       can be specified by software. Any numbers in the range 32 through 255
       can be used. Table 9-1 shows the assignment of interrupt and exception
       identifiers.
     */
    function abort_with_error_code(intno, error_code) { //used only for errors 10,11,12,13,14
        cpu.cycle_count += (N_cycles - cycles_left);
        cpu.eip = eip;
        cpu.cc_src = _src;
        cpu.cc_dst = _dst;
        cpu.cc_op = _op;
        cpu.cc_op2 = _op2;
        cpu.cc_dst2 = _dst2;
        throw {intno: intno, error_code: error_code};
    }
    function abort(intno) { //used only for errors 0, 5, 6, 7, 13
        abort_with_error_code(intno, 0);
    }

    function change_permission_level(sd) {
        cpu.cpl = sd;
        if (cpu.cpl == 3) {
            _tlb_read_ = tlb_read_user;
            _tlb_write_ = tlb_write_user;
        } else {
            _tlb_read_ = tlb_read_kernel;
            _tlb_write_ = tlb_write_kernel;
        }
    }
    function do_tlb_lookup(mem8_loc, ud) {
        var tlb_lookup;
        if (ud) {
            tlb_lookup = _tlb_write_[mem8_loc >>> 12];
        } else {
            tlb_lookup = _tlb_read_[mem8_loc >>> 12];
        }
        if (tlb_lookup == -1) {
            do_tlb_set_page(mem8_loc, ud, cpu.cpl == 3);
            if (ud) {
                tlb_lookup = _tlb_write_[mem8_loc >>> 12];
            } else {
                tlb_lookup = _tlb_read_[mem8_loc >>> 12];
            }
        }
        return tlb_lookup ^ mem8_loc;
    }

    /*
      Stack Operations
      ==========================================================================================
    */

    function push_word_to_stack(x) {
        var wd;
        wd = regs[4] - 2;
        mem8_loc = ((wd & SS_mask) + SS_base) >> 0;
        st16_mem8_write(x);
        regs[4] = (regs[4] & ~SS_mask) | ((wd) & SS_mask);
    }
    function push_dword_to_stack(x) {
        var wd;
        wd = regs[4] - 4;
        mem8_loc = ((wd & SS_mask) + SS_base) >> 0;
        st32_mem8_write(x);
        regs[4] = (regs[4] & ~SS_mask) | ((wd) & SS_mask);
    }
    function pop_word_from_stack_read() {
        mem8_loc = ((regs[4] & SS_mask) + SS_base) >> 0;
        return ld_16bits_mem8_read();
    }
    function pop_word_from_stack_incr_ptr() {
        regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 2) & SS_mask);
    }
    function pop_dword_from_stack_read() {
        mem8_loc = ((regs[4] & SS_mask) + SS_base) >> 0;
        return ld_32bits_mem8_read();
    }
    function pop_dword_from_stack_incr_ptr() {
        regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 4) & SS_mask);
    }


    /*
      This next function is a bit mysterious to me still, it's used when the next instruction crosses a page boundary, I think.
      It seems to determine the total size of the next operator (opcodes plus operands) up on deck.
     */
    function operation_size_function(eip_offset, OPbyte) {
        var n, CS_flags, l, mem8, local_OPbyte_var, base, conditional_var, stride;
        n = 1;
        CS_flags = init_CS_flags;
        if (CS_flags & 0x0100)//are we in 16bit compatibility mode?
            stride = 2;
        else
            stride = 4;
        EXEC_LOOP: for (; ; ) {
            switch (OPbyte) {
                case 0x66://   Operand-size override prefix
                    if (init_CS_flags & 0x0100) {
                        stride = 4;
                        CS_flags &= ~0x0100;
                    } else {
                        stride = 2;
                        CS_flags |= 0x0100;
                    }
                case 0xf0://LOCK   Assert LOCK# Signal Prefix
                case 0xf2://REPNZ  eCX Repeat String Operation Prefix
                case 0xf3://REPZ  eCX Repeat String Operation Prefix
                case 0x26://ES ES  ES segment override prefix
                case 0x2e://CS CS  CS segment override prefix
                case 0x36://SS SS  SS segment override prefix
                case 0x3e://DS DS  DS segment override prefix
                case 0x64://FS FS  FS segment override prefix
                case 0x65://GS GS  GS segment override prefix
                    {
                        if ((n + 1) > 15)
                            abort(6);
                        mem8_loc = (eip_offset + (n++)) >> 0;
                        OPbyte = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                    }
                    break;
                case 0x67://   Address-size override prefix
                    if (init_CS_flags & 0x0080) {
                        CS_flags &= ~0x0080;
                    } else {
                        CS_flags |= 0x0080;
                    }
                    {
                        if ((n + 1) > 15)
                            abort(6);
                        mem8_loc = (eip_offset + (n++)) >> 0;
                        OPbyte = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                    }
                    break;
                case 0x91:
                case 0x92:
                case 0x93:
                case 0x94:
                case 0x95:
                case 0x96:
                case 0x97:
                case 0x40://INC  Zv Increment by 1
                case 0x41://REX.B   Extension of r/m field, base field, or opcode reg field
                case 0x42://REX.X   Extension of SIB index field
                case 0x43://REX.XB   REX.X and REX.B combination
                case 0x44://REX.R   Extension of ModR/M reg field
                case 0x45://REX.RB   REX.R and REX.B combination
                case 0x46://REX.RX   REX.R and REX.X combination
                case 0x47://REX.RXB   REX.R, REX.X and REX.B combination
                case 0x48://DEC  Zv Decrement by 1
                case 0x49://REX.WB   REX.W and REX.B combination
                case 0x4a://REX.WX   REX.W and REX.X combination
                case 0x4b://REX.WXB   REX.W, REX.X and REX.B combination
                case 0x4c://REX.WR   REX.W and REX.R combination
                case 0x4d://REX.WRB   REX.W, REX.R and REX.B combination
                case 0x4e://REX.WRX   REX.W, REX.R and REX.X combination
                case 0x4f://REX.WRXB   REX.W, REX.R, REX.X and REX.B combination
                case 0x50://PUSH Zv SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x51:
                case 0x52:
                case 0x53:
                case 0x54:
                case 0x55:
                case 0x56:
                case 0x57:
                case 0x58://POP SS:[rSP] Zv Pop a Value from the Stack
                case 0x59:
                case 0x5a:
                case 0x5b:
                case 0x5c:
                case 0x5d:
                case 0x5e:
                case 0x5f:
                case 0x98://CBW AL AX Convert Byte to Word
                case 0x99://CWD AX DX Convert Word to Doubleword
                case 0xc9://LEAVE SS:[rSP] eBP High Level Procedure Exit
                case 0x9c://PUSHF Flags SS:[rSP] Push FLAGS Register onto the Stack
                case 0x9d://POPF SS:[rSP] Flags Pop Stack into FLAGS Register
                case 0x06://PUSH ES SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x0e://PUSH CS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x16://PUSH SS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x1e://PUSH DS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x07://POP SS:[rSP] ES Pop a Value from the Stack
                case 0x17://POP SS:[rSP] SS Pop a Value from the Stack
                case 0x1f://POP SS:[rSP] DS Pop a Value from the Stack
                case 0xc3://RETN SS:[rSP]  Return from procedure
                case 0xcb://RETF SS:[rSP]  Return from procedure
                case 0x90://XCHG  Zvqp Exchange Register/Memory with Register
                case 0xcc://INT 3 SS:[rSP] Call to Interrupt Procedure
                case 0xce://INTO eFlags SS:[rSP] Call to Interrupt Procedure
                case 0xcf://IRET SS:[rSP] Flags Interrupt Return
                case 0xf5://CMC   Complement Carry Flag
                case 0xf8://CLC   Clear Carry Flag
                case 0xf9://STC   Set Carry Flag
                case 0xfc://CLD   Clear Direction Flag
                case 0xfd://STD   Set Direction Flag
                case 0xfa://CLI   Clear Interrupt Flag
                case 0xfb://STI   Set Interrupt Flag
                case 0x9e://SAHF AH  Store AH into Flags
                case 0x9f://LAHF  AH Load Status Flags into AH Register
                case 0xf4://HLT   Halt
                case 0xa4://MOVS (DS:)[rSI] (ES:)[rDI] Move Data from String to String
                case 0xa5://MOVS DS:[SI] ES:[DI] Move Data from String to String
                case 0xaa://STOS AL (ES:)[rDI] Store String
                case 0xab://STOS AX ES:[DI] Store String
                case 0xa6://CMPS (ES:)[rDI]  Compare String Operands
                case 0xa7://CMPS ES:[DI]  Compare String Operands
                case 0xac://LODS (DS:)[rSI] AL Load String
                case 0xad://LODS DS:[SI] AX Load String
                case 0xae://SCAS (ES:)[rDI]  Scan String
                case 0xaf://SCAS ES:[DI]  Scan String
                case 0x9b://FWAIT   Check pending unmasked floating-point exceptions
                case 0xec://IN DX AL Input from Port
                case 0xed://IN DX eAX Input from Port
                case 0xee://OUT AL DX Output to Port
                case 0xef://OUT eAX DX Output to Port
                case 0xd7://XLAT (DS:)[rBX+AL] AL Table Look-up Translation
                case 0x27://DAA  AL Decimal Adjust AL after Addition
                case 0x2f://DAS  AL Decimal Adjust AL after Subtraction
                case 0x37://AAA  AL ASCII Adjust After Addition
                case 0x3f://AAS  AL ASCII Adjust AL After Subtraction
                case 0x60://PUSHA AX SS:[rSP] Push All General-Purpose Registers
                case 0x61://POPA SS:[rSP] DI Pop All General-Purpose Registers
                case 0x6c://INS DX (ES:)[rDI] Input from Port to String
                case 0x6d://INS DX ES:[DI] Input from Port to String
                case 0x6e://OUTS (DS):[rSI] DX Output String to Port
                case 0x6f://OUTS DS:[SI] DX Output String to Port
                    break EXEC_LOOP;
                case 0xb0://MOV Ib Zb Move
                case 0xb1:
                case 0xb2:
                case 0xb3:
                case 0xb4:
                case 0xb5:
                case 0xb6:
                case 0xb7:
                case 0x04://ADD Ib AL Add
                case 0x0c://OR Ib AL Logical Inclusive OR
                case 0x14://ADC Ib AL Add with Carry
                case 0x1c://SBB Ib AL Integer Subtraction with Borrow
                case 0x24://AND Ib AL Logical AND
                case 0x2c://SUB Ib AL Subtract
                case 0x34://XOR Ib AL Logical Exclusive OR
                case 0x3c://CMP AL  Compare Two Operands
                case 0xa8://TEST AL  Logical Compare
                case 0x6a://PUSH Ibss SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0xeb://JMP Jbs  Jump
                case 0x70://JO Jbs  Jump short if overflow (OF=1)
                case 0x71://JNO Jbs  Jump short if not overflow (OF=0)
                case 0x72://JB Jbs  Jump short if below/not above or equal/carry (CF=1)
                case 0x73://JNB Jbs  Jump short if not below/above or equal/not carry (CF=0)
                case 0x76://JBE Jbs  Jump short if below or equal/not above (CF=1 AND ZF=1)
                case 0x77://JNBE Jbs  Jump short if not below or equal/above (CF=0 AND ZF=0)
                case 0x78://JS Jbs  Jump short if sign (SF=1)
                case 0x79://JNS Jbs  Jump short if not sign (SF=0)
                case 0x7a://JP Jbs  Jump short if parity/parity even (PF=1)
                case 0x7b://JNP Jbs  Jump short if not parity/parity odd
                case 0x7c://JL Jbs  Jump short if less/not greater (SF!=OF)
                case 0x7d://JNL Jbs  Jump short if not less/greater or equal (SF=OF)
                case 0x7e://JLE Jbs  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                case 0x7f://JNLE Jbs  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                case 0x74://JZ Jbs  Jump short if zero/equal (ZF=0)
                case 0x75://JNZ Jbs  Jump short if not zero/not equal (ZF=1)
                case 0xe0://LOOPNZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=0
                case 0xe1://LOOPZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=1
                case 0xe2://LOOP Jbs eCX Decrement count; Jump short if count!=0
                case 0xe3://JCXZ Jbs  Jump short if eCX register is 0
                case 0xcd://INT Ib SS:[rSP] Call to Interrupt Procedure
                case 0xe4://IN Ib AL Input from Port
                case 0xe5://IN Ib eAX Input from Port
                case 0xe6://OUT AL Ib Output to Port
                case 0xe7://OUT eAX Ib Output to Port
                case 0xd4://AAM  AL ASCII Adjust AX After Multiply
                case 0xd5://AAD  AL ASCII Adjust AX Before Division
                    n++;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xb8://MOV Ivqp Zvqp Move
                case 0xb9:
                case 0xba:
                case 0xbb:
                case 0xbc:
                case 0xbd:
                case 0xbe:
                case 0xbf:
                case 0x05://ADD Ivds rAX Add
                case 0x0d://OR Ivds rAX Logical Inclusive OR
                case 0x15://ADC Ivds rAX Add with Carry
                case 0x1d://SBB Ivds rAX Integer Subtraction with Borrow
                case 0x25://AND Ivds rAX Logical AND
                case 0x2d://SUB Ivds rAX Subtract
                case 0x35://XOR Ivds rAX Logical Exclusive OR
                case 0x3d://CMP rAX  Compare Two Operands
                case 0xa9://TEST rAX  Logical Compare
                case 0x68://PUSH Ivs SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0xe9://JMP Jvds  Jump
                case 0xe8://CALL Jvds SS:[rSP] Call Procedure
                    n += stride;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0x88://MOV Gb Eb Move
                case 0x89://MOV Gvqp Evqp Move
                case 0x8a://MOV Eb Gb Move
                case 0x8b://MOV Evqp Gvqp Move
                case 0x86://XCHG  Gb Exchange Register/Memory with Register
                case 0x87://XCHG  Gvqp Exchange Register/Memory with Register
                case 0x8e://MOV Ew Sw Move
                case 0x8c://MOV Sw Mw Move
                case 0xc4://LES Mp ES Load Far Pointer
                case 0xc5://LDS Mp DS Load Far Pointer
                case 0x00://ADD Gb Eb Add
                case 0x08://OR Gb Eb Logical Inclusive OR
                case 0x10://ADC Gb Eb Add with Carry
                case 0x18://SBB Gb Eb Integer Subtraction with Borrow
                case 0x20://AND Gb Eb Logical AND
                case 0x28://SUB Gb Eb Subtract
                case 0x30://XOR Gb Eb Logical Exclusive OR
                case 0x38://CMP Eb  Compare Two Operands
                case 0x01://ADD Gvqp Evqp Add
                case 0x09://OR Gvqp Evqp Logical Inclusive OR
                case 0x11://ADC Gvqp Evqp Add with Carry
                case 0x19://SBB Gvqp Evqp Integer Subtraction with Borrow
                case 0x21://AND Gvqp Evqp Logical AND
                case 0x29://SUB Gvqp Evqp Subtract
                case 0x31://XOR Gvqp Evqp Logical Exclusive OR
                case 0x39://CMP Evqp  Compare Two Operands
                case 0x02://ADD Eb Gb Add
                case 0x0a://OR Eb Gb Logical Inclusive OR
                case 0x12://ADC Eb Gb Add with Carry
                case 0x1a://SBB Eb Gb Integer Subtraction with Borrow
                case 0x22://AND Eb Gb Logical AND
                case 0x2a://SUB Eb Gb Subtract
                case 0x32://XOR Eb Gb Logical Exclusive OR
                case 0x3a://CMP Gb  Compare Two Operands
                case 0x03://ADD Evqp Gvqp Add
                case 0x0b://OR Evqp Gvqp Logical Inclusive OR
                case 0x13://ADC Evqp Gvqp Add with Carry
                case 0x1b://SBB Evqp Gvqp Integer Subtraction with Borrow
                case 0x23://AND Evqp Gvqp Logical AND
                case 0x2b://SUB Evqp Gvqp Subtract
                case 0x33://XOR Evqp Gvqp Logical Exclusive OR
                case 0x3b://CMP Gvqp  Compare Two Operands
                case 0x84://TEST Eb  Logical Compare
                case 0x85://TEST Evqp  Logical Compare
                case 0xd0://ROL 1 Eb Rotate
                case 0xd1://ROL 1 Evqp Rotate
                case 0xd2://ROL CL Eb Rotate
                case 0xd3://ROL CL Evqp Rotate
                case 0x8f://POP SS:[rSP] Ev Pop a Value from the Stack
                case 0x8d://LEA M Gvqp Load Effective Address
                case 0xfe://INC  Eb Increment by 1
                case 0xff://INC  Evqp Increment by 1
                case 0xd8://FADD Msr ST Add
                case 0xd9://FLD ESsr ST Load Floating Point Value
                case 0xda://FIADD Mdi ST Add
                case 0xdb://FILD Mdi ST Load Integer
                case 0xdc://FADD Mdr ST Add
                case 0xdd://FLD Mdr ST Load Floating Point Value
                case 0xde://FIADD Mwi ST Add
                case 0xdf://FILD Mwi ST Load Integer
                case 0x62://BOUND Gv SS:[rSP] Check Array Index Against Bounds
                case 0x63://ARPL Ew  Adjust RPL Field of Segment Selector
                    {
                        {
                            if ((n + 1) > 15)
                                abort(6);
                            mem8_loc = (eip_offset + (n++)) >> 0;
                            mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                        }
                        if (CS_flags & 0x0080) {
                            switch (mem8 >> 6) {
                                case 0:
                                    if ((mem8 & 7) == 6)
                                        n += 2;
                                    break;
                                case 1:
                                    n++;
                                    break;
                                default:
                                    n += 2;
                                    break;
                            }
                        } else {
                            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                case 0x04:
                                    {
                                        if ((n + 1) > 15)
                                            abort(6);
                                        mem8_loc = (eip_offset + (n++)) >> 0;
                                        local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                    }
                                    if ((local_OPbyte_var & 7) == 5) {
                                        n += 4;
                                    }
                                    break;
                                case 0x0c:
                                    n += 2;
                                    break;
                                case 0x14:
                                    n += 5;
                                    break;
                                case 0x05:
                                    n += 4;
                                    break;
                                case 0x00:
                                case 0x01:
                                case 0x02:
                                case 0x03:
                                case 0x06:
                                case 0x07:
                                    break;
                                case 0x08:
                                case 0x09:
                                case 0x0a:
                                case 0x0b:
                                case 0x0d:
                                case 0x0e:
                                case 0x0f:
                                    n++;
                                    break;
                                case 0x10:
                                case 0x11:
                                case 0x12:
                                case 0x13:
                                case 0x15:
                                case 0x16:
                                case 0x17:
                                    n += 4;
                                    break;
                            }
                        }
                        if (n > 15)
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xa0://MOV Ob AL Move
                case 0xa1://MOV Ovqp rAX Move
                case 0xa2://MOV AL Ob Move
                case 0xa3://MOV rAX Ovqp Move
                    if (CS_flags & 0x0100)
                        n += 2;
                    else
                        n += 4;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xc6://MOV Ib Eb Move
                case 0x80://ADD Ib Eb Add
                case 0x82://ADD Ib Eb Add
                case 0x83://ADD Ibs Evqp Add
                case 0x6b://IMUL Evqp Gvqp Signed Multiply
                case 0xc0://ROL Ib Eb Rotate
                case 0xc1://ROL Ib Evqp Rotate
                    {
                        {
                            if ((n + 1) > 15)
                                abort(6);
                            mem8_loc = (eip_offset + (n++)) >> 0;
                            mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                        }
                        if (CS_flags & 0x0080) {
                            switch (mem8 >> 6) {
                                case 0:
                                    if ((mem8 & 7) == 6)
                                        n += 2;
                                    break;
                                case 1:
                                    n++;
                                    break;
                                default:
                                    n += 2;
                                    break;
                            }
                        } else {
                            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                case 0x04:
                                    {
                                        if ((n + 1) > 15)
                                            abort(6);
                                        mem8_loc = (eip_offset + (n++)) >> 0;
                                        local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                    }
                                    if ((local_OPbyte_var & 7) == 5) {
                                        n += 4;
                                    }
                                    break;
                                case 0x0c:
                                    n += 2;
                                    break;
                                case 0x14:
                                    n += 5;
                                    break;
                                case 0x05:
                                    n += 4;
                                    break;
                                case 0x00:
                                case 0x01:
                                case 0x02:
                                case 0x03:
                                case 0x06:
                                case 0x07:
                                    break;
                                case 0x08:
                                case 0x09:
                                case 0x0a:
                                case 0x0b:
                                case 0x0d:
                                case 0x0e:
                                case 0x0f:
                                    n++;
                                    break;
                                case 0x10:
                                case 0x11:
                                case 0x12:
                                case 0x13:
                                case 0x15:
                                case 0x16:
                                case 0x17:
                                    n += 4;
                                    break;
                            }
                        }
                        if (n > 15)
                            abort(6);
                    }
                    n++;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xc7://MOV Ivds Evqp Move
                case 0x81://ADD Ivds Evqp Add
                case 0x69://IMUL Evqp Gvqp Signed Multiply
                    {
                        {
                            if ((n + 1) > 15)
                                abort(6);
                            mem8_loc = (eip_offset + (n++)) >> 0;
                            mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                        }
                        if (CS_flags & 0x0080) {
                            switch (mem8 >> 6) {
                                case 0:
                                    if ((mem8 & 7) == 6)
                                        n += 2;
                                    break;
                                case 1:
                                    n++;
                                    break;
                                default:
                                    n += 2;
                                    break;
                            }
                        } else {
                            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                case 0x04:
                                    {
                                        if ((n + 1) > 15)
                                            abort(6);
                                        mem8_loc = (eip_offset + (n++)) >> 0;
                                        local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                    }
                                    if ((local_OPbyte_var & 7) == 5) {
                                        n += 4;
                                    }
                                    break;
                                case 0x0c:
                                    n += 2;
                                    break;
                                case 0x14:
                                    n += 5;
                                    break;
                                case 0x05:
                                    n += 4;
                                    break;
                                case 0x00:
                                case 0x01:
                                case 0x02:
                                case 0x03:
                                case 0x06:
                                case 0x07:
                                    break;
                                case 0x08:
                                case 0x09:
                                case 0x0a:
                                case 0x0b:
                                case 0x0d:
                                case 0x0e:
                                case 0x0f:
                                    n++;
                                    break;
                                case 0x10:
                                case 0x11:
                                case 0x12:
                                case 0x13:
                                case 0x15:
                                case 0x16:
                                case 0x17:
                                    n += 4;
                                    break;
                            }
                        }
                        if (n > 15)
                            abort(6);
                    }
                    n += stride;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xf6://TEST Eb  Logical Compare
                    {
                        {
                            if ((n + 1) > 15)
                                abort(6);
                            mem8_loc = (eip_offset + (n++)) >> 0;
                            mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                        }
                        if (CS_flags & 0x0080) {
                            switch (mem8 >> 6) {
                                case 0:
                                    if ((mem8 & 7) == 6)
                                        n += 2;
                                    break;
                                case 1:
                                    n++;
                                    break;
                                default:
                                    n += 2;
                                    break;
                            }
                        } else {
                            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                case 0x04:
                                    {
                                        if ((n + 1) > 15)
                                            abort(6);
                                        mem8_loc = (eip_offset + (n++)) >> 0;
                                        local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                    }
                                    if ((local_OPbyte_var & 7) == 5) {
                                        n += 4;
                                    }
                                    break;
                                case 0x0c:
                                    n += 2;
                                    break;
                                case 0x14:
                                    n += 5;
                                    break;
                                case 0x05:
                                    n += 4;
                                    break;
                                case 0x00:
                                case 0x01:
                                case 0x02:
                                case 0x03:
                                case 0x06:
                                case 0x07:
                                    break;
                                case 0x08:
                                case 0x09:
                                case 0x0a:
                                case 0x0b:
                                case 0x0d:
                                case 0x0e:
                                case 0x0f:
                                    n++;
                                    break;
                                case 0x10:
                                case 0x11:
                                case 0x12:
                                case 0x13:
                                case 0x15:
                                case 0x16:
                                case 0x17:
                                    n += 4;
                                    break;
                            }
                        }
                        if (n > 15)
                            abort(6);
                    }
                    conditional_var = (mem8 >> 3) & 7;
                    if (conditional_var == 0) {
                        n++;
                        if (n > 15)
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xf7://TEST Evqp  Logical Compare
                    {
                        {
                            if ((n + 1) > 15)
                                abort(6);
                            mem8_loc = (eip_offset + (n++)) >> 0;
                            mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                        }
                        if (CS_flags & 0x0080) {
                            switch (mem8 >> 6) {
                                case 0:
                                    if ((mem8 & 7) == 6)
                                        n += 2;
                                    break;
                                case 1:
                                    n++;
                                    break;
                                default:
                                    n += 2;
                                    break;
                            }
                        } else {
                            switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                case 0x04:
                                    {
                                        if ((n + 1) > 15)
                                            abort(6);
                                        mem8_loc = (eip_offset + (n++)) >> 0;
                                        local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                    }
                                    if ((local_OPbyte_var & 7) == 5) {
                                        n += 4;
                                    }
                                    break;
                                case 0x0c:
                                    n += 2;
                                    break;
                                case 0x14:
                                    n += 5;
                                    break;
                                case 0x05:
                                    n += 4;
                                    break;
                                case 0x00:
                                case 0x01:
                                case 0x02:
                                case 0x03:
                                case 0x06:
                                case 0x07:
                                    break;
                                case 0x08:
                                case 0x09:
                                case 0x0a:
                                case 0x0b:
                                case 0x0d:
                                case 0x0e:
                                case 0x0f:
                                    n++;
                                    break;
                                case 0x10:
                                case 0x11:
                                case 0x12:
                                case 0x13:
                                case 0x15:
                                case 0x16:
                                case 0x17:
                                    n += 4;
                                    break;
                            }
                        }
                        if (n > 15)
                            abort(6);
                    }
                    conditional_var = (mem8 >> 3) & 7;
                    if (conditional_var == 0) {
                        n += stride;
                        if (n > 15)
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xea://JMPF Ap  Jump
                case 0x9a://CALLF Ap SS:[rSP] Call Procedure
                    n += 2 + stride;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xc2://RETN SS:[rSP]  Return from procedure
                case 0xca://RETF Iw  Return from procedure
                    n += 2;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xc8://ENTER Iw SS:[rSP] Make Stack Frame for Procedure Parameters
                    n += 3;
                    if (n > 15)
                        abort(6);
                    break EXEC_LOOP;
                case 0xd6://SALC   Undefined and Reserved; Does not Generate #UD
                case 0xf1://INT1   Undefined and Reserved; Does not Generate #UD
                default:
                    abort(6);
                case 0x0f://two-op instruction prefix
                    {
                        if ((n + 1) > 15)
                            abort(6);
                        mem8_loc = (eip_offset + (n++)) >> 0;
                        OPbyte = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                    }
                    switch (OPbyte) {
                        case 0x06://CLTS  CR0 Clear Task-Switched Flag in CR0
                        case 0xa2://CPUID  IA32_BIOS_SIGN_ID CPU Identification
                        case 0x31://RDTSC IA32_TIME_STAMP_COUNTER EAX Read Time-Stamp Counter
                        case 0xa0://PUSH FS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0xa8://PUSH GS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0xa1://POP SS:[rSP] FS Pop a Value from the Stack
                        case 0xa9://POP SS:[rSP] GS Pop a Value from the Stack
                        case 0xc8://BSWAP  Zvqp Byte Swap
                        case 0xc9:
                        case 0xca:
                        case 0xcb:
                        case 0xcc:
                        case 0xcd:
                        case 0xce:
                        case 0xcf:
                            break EXEC_LOOP;
                        case 0x80://JO Jvds  Jump short if overflow (OF=1)
                        case 0x81://JNO Jvds  Jump short if not overflow (OF=0)
                        case 0x82://JB Jvds  Jump short if below/not above or equal/carry (CF=1)
                        case 0x83://JNB Jvds  Jump short if not below/above or equal/not carry (CF=0)
                        case 0x84://JZ Jvds  Jump short if zero/equal (ZF=0)
                        case 0x85://JNZ Jvds  Jump short if not zero/not equal (ZF=1)
                        case 0x86://JBE Jvds  Jump short if below or equal/not above (CF=1 AND ZF=1)
                        case 0x87://JNBE Jvds  Jump short if not below or equal/above (CF=0 AND ZF=0)
                        case 0x88://JS Jvds  Jump short if sign (SF=1)
                        case 0x89://JNS Jvds  Jump short if not sign (SF=0)
                        case 0x8a://JP Jvds  Jump short if parity/parity even (PF=1)
                        case 0x8b://JNP Jvds  Jump short if not parity/parity odd
                        case 0x8c://JL Jvds  Jump short if less/not greater (SF!=OF)
                        case 0x8d://JNL Jvds  Jump short if not less/greater or equal (SF=OF)
                        case 0x8e://JLE Jvds  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x8f://JNLE Jvds  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                            n += stride;
                            if (n > 15)
                                abort(6);
                            break EXEC_LOOP;
                        case 0x90://SETO  Eb Set Byte on Condition - overflow (OF=1)
                        case 0x91://SETNO  Eb Set Byte on Condition - not overflow (OF=0)
                        case 0x92://SETB  Eb Set Byte on Condition - below/not above or equal/carry (CF=1)
                        case 0x93://SETNB  Eb Set Byte on Condition - not below/above or equal/not carry (CF=0)
                        case 0x94://SETZ  Eb Set Byte on Condition - zero/equal (ZF=0)
                        case 0x95://SETNZ  Eb Set Byte on Condition - not zero/not equal (ZF=1)
                        case 0x96://SETBE  Eb Set Byte on Condition - below or equal/not above (CF=1 AND ZF=1)
                        case 0x97://SETNBE  Eb Set Byte on Condition - not below or equal/above (CF=0 AND ZF=0)
                        case 0x98://SETS  Eb Set Byte on Condition - sign (SF=1)
                        case 0x99://SETNS  Eb Set Byte on Condition - not sign (SF=0)
                        case 0x9a://SETP  Eb Set Byte on Condition - parity/parity even (PF=1)
                        case 0x9b://SETNP  Eb Set Byte on Condition - not parity/parity odd
                        case 0x9c://SETL  Eb Set Byte on Condition - less/not greater (SF!=OF)
                        case 0x9d://SETNL  Eb Set Byte on Condition - not less/greater or equal (SF=OF)
                        case 0x9e://SETLE  Eb Set Byte on Condition - less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x9f://SETNLE  Eb Set Byte on Condition - not less nor equal/greater ((ZF=0) AND (SF=OF))
                        case 0x40://CMOVO Evqp Gvqp Conditional Move - overflow (OF=1)
                        case 0x41://CMOVNO Evqp Gvqp Conditional Move - not overflow (OF=0)
                        case 0x42://CMOVB Evqp Gvqp Conditional Move - below/not above or equal/carry (CF=1)
                        case 0x43://CMOVNB Evqp Gvqp Conditional Move - not below/above or equal/not carry (CF=0)
                        case 0x44://CMOVZ Evqp Gvqp Conditional Move - zero/equal (ZF=0)
                        case 0x45://CMOVNZ Evqp Gvqp Conditional Move - not zero/not equal (ZF=1)
                        case 0x46://CMOVBE Evqp Gvqp Conditional Move - below or equal/not above (CF=1 AND ZF=1)
                        case 0x47://CMOVNBE Evqp Gvqp Conditional Move - not below or equal/above (CF=0 AND ZF=0)
                        case 0x48://CMOVS Evqp Gvqp Conditional Move - sign (SF=1)
                        case 0x49://CMOVNS Evqp Gvqp Conditional Move - not sign (SF=0)
                        case 0x4a://CMOVP Evqp Gvqp Conditional Move - parity/parity even (PF=1)
                        case 0x4b://CMOVNP Evqp Gvqp Conditional Move - not parity/parity odd
                        case 0x4c://CMOVL Evqp Gvqp Conditional Move - less/not greater (SF!=OF)
                        case 0x4d://CMOVNL Evqp Gvqp Conditional Move - not less/greater or equal (SF=OF)
                        case 0x4e://CMOVLE Evqp Gvqp Conditional Move - less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x4f://CMOVNLE Evqp Gvqp Conditional Move - not less nor equal/greater ((ZF=0) AND (SF=OF))
                        case 0xb6://MOVZX Eb Gvqp Move with Zero-Extend
                        case 0xb7://MOVZX Ew Gvqp Move with Zero-Extend
                        case 0xbe://MOVSX Eb Gvqp Move with Sign-Extension
                        case 0xbf://MOVSX Ew Gvqp Move with Sign-Extension
                        case 0x00://SLDT LDTR Mw Store Local Descriptor Table Register
                        case 0x01://SGDT GDTR Ms Store Global Descriptor Table Register
                        case 0x02://LAR Mw Gvqp Load Access Rights Byte
                        case 0x03://LSL Mw Gvqp Load Segment Limit
                        case 0x20://MOV Cd Rd Move to/from Control Registers
                        case 0x22://MOV Rd Cd Move to/from Control Registers
                        case 0x23://MOV Rd Dd Move to/from Debug Registers
                        case 0xb2://LSS Mptp SS Load Far Pointer
                        case 0xb4://LFS Mptp FS Load Far Pointer
                        case 0xb5://LGS Mptp GS Load Far Pointer
                        case 0xa5://SHLD Gvqp Evqp Double Precision Shift Left
                        case 0xad://SHRD Gvqp Evqp Double Precision Shift Right
                        case 0xa3://BT Evqp  Bit Test
                        case 0xab://BTS Gvqp Evqp Bit Test and Set
                        case 0xb3://BTR Gvqp Evqp Bit Test and Reset
                        case 0xbb://BTC Gvqp Evqp Bit Test and Complement
                        case 0xbc://BSF Evqp Gvqp Bit Scan Forward
                        case 0xbd://BSR Evqp Gvqp Bit Scan Reverse
                        case 0xaf://IMUL Evqp Gvqp Signed Multiply
                        case 0xc0://XADD  Eb Exchange and Add
                        case 0xc1://XADD  Evqp Exchange and Add
                        case 0xb0://CMPXCHG Gb Eb Compare and Exchange
                        case 0xb1://CMPXCHG Gvqp Evqp Compare and Exchange
                            {
                                {
                                    if ((n + 1) > 15)
                                        abort(6);
                                    mem8_loc = (eip_offset + (n++)) >> 0;
                                    mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                }
                                if (CS_flags & 0x0080) {
                                    switch (mem8 >> 6) {
                                        case 0:
                                            if ((mem8 & 7) == 6)
                                                n += 2;
                                            break;
                                        case 1:
                                            n++;
                                            break;
                                        default:
                                            n += 2;
                                            break;
                                    }
                                } else {
                                    switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                        case 0x04:
                                            {
                                                if ((n + 1) > 15)
                                                    abort(6);
                                                mem8_loc = (eip_offset + (n++)) >> 0;
                                                local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                            }
                                            if ((local_OPbyte_var & 7) == 5) {
                                                n += 4;
                                            }
                                            break;
                                        case 0x0c:
                                            n += 2;
                                            break;
                                        case 0x14:
                                            n += 5;
                                            break;
                                        case 0x05:
                                            n += 4;
                                            break;
                                        case 0x00:
                                        case 0x01:
                                        case 0x02:
                                        case 0x03:
                                        case 0x06:
                                        case 0x07:
                                            break;
                                        case 0x08:
                                        case 0x09:
                                        case 0x0a:
                                        case 0x0b:
                                        case 0x0d:
                                        case 0x0e:
                                        case 0x0f:
                                            n++;
                                            break;
                                        case 0x10:
                                        case 0x11:
                                        case 0x12:
                                        case 0x13:
                                        case 0x15:
                                        case 0x16:
                                        case 0x17:
                                            n += 4;
                                            break;
                                    }
                                }
                                if (n > 15)
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0xa4://SHLD Gvqp Evqp Double Precision Shift Left
                        case 0xac://SHRD Gvqp Evqp Double Precision Shift Right
                        case 0xba://BT Evqp  Bit Test
                            {
                                {
                                    if ((n + 1) > 15)
                                        abort(6);
                                    mem8_loc = (eip_offset + (n++)) >> 0;
                                    mem8 = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                }
                                if (CS_flags & 0x0080) {
                                    switch (mem8 >> 6) {
                                        case 0:
                                            if ((mem8 & 7) == 6)
                                                n += 2;
                                            break;
                                        case 1:
                                            n++;
                                            break;
                                        default:
                                            n += 2;
                                            break;
                                    }
                                } else {
                                    switch ((mem8 & 7) | ((mem8 >> 3) & 0x18)) {
                                        case 0x04:
                                            {
                                                if ((n + 1) > 15)
                                                    abort(6);
                                                mem8_loc = (eip_offset + (n++)) >> 0;
                                                local_OPbyte_var = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                                            }
                                            if ((local_OPbyte_var & 7) == 5) {
                                                n += 4;
                                            }
                                            break;
                                        case 0x0c:
                                            n += 2;
                                            break;
                                        case 0x14:
                                            n += 5;
                                            break;
                                        case 0x05:
                                            n += 4;
                                            break;
                                        case 0x00:
                                        case 0x01:
                                        case 0x02:
                                        case 0x03:
                                        case 0x06:
                                        case 0x07:
                                            break;
                                        case 0x08:
                                        case 0x09:
                                        case 0x0a:
                                        case 0x0b:
                                        case 0x0d:
                                        case 0x0e:
                                        case 0x0f:
                                            n++;
                                            break;
                                        case 0x10:
                                        case 0x11:
                                        case 0x12:
                                        case 0x13:
                                        case 0x15:
                                        case 0x16:
                                        case 0x17:
                                            n += 4;
                                            break;
                                    }
                                }
                                if (n > 15)
                                    abort(6);
                            }
                            n++;
                            if (n > 15)
                                abort(6);
                            break EXEC_LOOP;
                        case 0x04:
                        case 0x05://LOADALL  AX Load All of the CPU Registers
                        case 0x07://LOADALL  EAX Load All of the CPU Registers
                        case 0x08://INVD   Invalidate Internal Caches
                        case 0x09://WBINVD   Write Back and Invalidate Cache
                        case 0x0a:
                        case 0x0b://UD2   Undefined Instruction
                        case 0x0c:
                        case 0x0d://NOP Ev  No Operation
                        case 0x0e:
                        case 0x0f:
                        case 0x10://MOVUPS Wps Vps Move Unaligned Packed Single-FP Values
                        case 0x11://MOVUPS Vps Wps Move Unaligned Packed Single-FP Values
                        case 0x12://MOVHLPS Uq Vq Move Packed Single-FP Values High to Low
                        case 0x13://MOVLPS Vq Mq Move Low Packed Single-FP Values
                        case 0x14://UNPCKLPS Wq Vps Unpack and Interleave Low Packed Single-FP Values
                        case 0x15://UNPCKHPS Wq Vps Unpack and Interleave High Packed Single-FP Values
                        case 0x16://MOVLHPS Uq Vq Move Packed Single-FP Values Low to High
                        case 0x17://MOVHPS Vq Mq Move High Packed Single-FP Values
                        case 0x18://HINT_NOP Ev  Hintable NOP
                        case 0x19://HINT_NOP Ev  Hintable NOP
                        case 0x1a://HINT_NOP Ev  Hintable NOP
                        case 0x1b://HINT_NOP Ev  Hintable NOP
                        case 0x1c://HINT_NOP Ev  Hintable NOP
                        case 0x1d://HINT_NOP Ev  Hintable NOP
                        case 0x1e://HINT_NOP Ev  Hintable NOP
                        case 0x1f://HINT_NOP Ev  Hintable NOP
                        case 0x21://MOV Dd Rd Move to/from Debug Registers
                        case 0x24://MOV Td Rd Move to/from Test Registers
                        case 0x25:
                        case 0x26://MOV Rd Td Move to/from Test Registers
                        case 0x27:
                        case 0x28://MOVAPS Wps Vps Move Aligned Packed Single-FP Values
                        case 0x29://MOVAPS Vps Wps Move Aligned Packed Single-FP Values
                        case 0x2a://CVTPI2PS Qpi Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                        case 0x2b://MOVNTPS Vps Mps Store Packed Single-FP Values Using Non-Temporal Hint
                        case 0x2c://CVTTPS2PI Wpsq Ppi Convert with Trunc. Packed Single-FP Values to1.11 PackedDW Integers
                        case 0x2d://CVTPS2PI Wpsq Ppi Convert Packed Single-FP Values to1.11 PackedDW Integers
                        case 0x2e://UCOMISS Vss  Unordered Compare Scalar Single-FP Values and Set EFLAGS
                        case 0x2f://COMISS Vss  Compare Scalar Ordered Single-FP Values and Set EFLAGS
                        case 0x30://WRMSR rCX MSR Write to Model Specific Register
                        case 0x32://RDMSR rCX rAX Read from Model Specific Register
                        case 0x33://RDPMC PMC EAX Read Performance-Monitoring Counters
                        case 0x34://SYSENTER IA32_SYSENTER_CS SS Fast System Call
                        case 0x35://SYSEXIT IA32_SYSENTER_CS SS Fast Return from Fast System Call
                        case 0x36:
                        case 0x37://GETSEC EAX  GETSEC Leaf Functions
                        case 0x38://PSHUFB Qq Pq Packed Shuffle Bytes
                        case 0x39:
                        case 0x3a://ROUNDPS Wps Vps Round Packed Single-FP Values
                        case 0x3b:
                        case 0x3c:
                        case 0x3d:
                        case 0x3e:
                        case 0x3f:
                        case 0x50://MOVMSKPS Ups Gdqp Extract Packed Single-FP Sign Mask
                        case 0x51://SQRTPS Wps Vps Compute Square Roots of Packed Single-FP Values
                        case 0x52://RSQRTPS Wps Vps Compute Recipr. of Square Roots of Packed Single-FP Values
                        case 0x53://RCPPS Wps Vps Compute Reciprocals of Packed Single-FP Values
                        case 0x54://ANDPS Wps Vps Bitwise Logical AND of Packed Single-FP Values
                        case 0x55://ANDNPS Wps Vps Bitwise Logical AND NOT of Packed Single-FP Values
                        case 0x56://ORPS Wps Vps Bitwise Logical OR of Single-FP Values
                        case 0x57://XORPS Wps Vps Bitwise Logical XOR for Single-FP Values
                        case 0x58://ADDPS Wps Vps Add Packed Single-FP Values
                        case 0x59://MULPS Wps Vps Multiply Packed Single-FP Values
                        case 0x5a://CVTPS2PD Wps Vpd Convert Packed Single-FP Values to1.11 PackedDouble-FP Values
                        case 0x5b://CVTDQ2PS Wdq Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                        case 0x5c://SUBPS Wps Vps Subtract Packed Single-FP Values
                        case 0x5d://MINPS Wps Vps Return Minimum Packed Single-FP Values
                        case 0x5e://DIVPS Wps Vps Divide Packed Single-FP Values
                        case 0x5f://MAXPS Wps Vps Return Maximum Packed Single-FP Values
                        case 0x60://PUNPCKLBW Qd Pq Unpack Low Data
                        case 0x61://PUNPCKLWD Qd Pq Unpack Low Data
                        case 0x62://PUNPCKLDQ Qd Pq Unpack Low Data
                        case 0x63://PACKSSWB Qd Pq Pack with Signed Saturation
                        case 0x64://PCMPGTB Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x65://PCMPGTW Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x66://PCMPGTD Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x67://PACKUSWB Qq Pq Pack with Unsigned Saturation
                        case 0x68://PUNPCKHBW Qq Pq Unpack High Data
                        case 0x69://PUNPCKHWD Qq Pq Unpack High Data
                        case 0x6a://PUNPCKHDQ Qq Pq Unpack High Data
                        case 0x6b://PACKSSDW Qq Pq Pack with Signed Saturation
                        case 0x6c://PUNPCKLQDQ Wdq Vdq Unpack Low Data
                        case 0x6d://PUNPCKHQDQ Wdq Vdq Unpack High Data
                        case 0x6e://MOVD Ed Pq Move Doubleword
                        case 0x6f://MOVQ Qq Pq Move Quadword
                        case 0x70://PSHUFW Qq Pq Shuffle Packed Words
                        case 0x71://PSRLW Ib Nq Shift Packed Data Right Logical
                        case 0x72://PSRLD Ib Nq Shift Double Quadword Right Logical
                        case 0x73://PSRLQ Ib Nq Shift Packed Data Right Logical
                        case 0x74://PCMPEQB Qq Pq Compare Packed Data for Equal
                        case 0x75://PCMPEQW Qq Pq Compare Packed Data for Equal
                        case 0x76://PCMPEQD Qq Pq Compare Packed Data for Equal
                        case 0x77://EMMS   Empty MMX Technology State
                        case 0x78://VMREAD Gd Ed Read Field from Virtual-Machine Control Structure
                        case 0x79://VMWRITE Gd  Write Field to Virtual-Machine Control Structure
                        case 0x7a:
                        case 0x7b:
                        case 0x7c://HADDPD Wpd Vpd Packed Double-FP Horizontal Add
                        case 0x7d://HSUBPD Wpd Vpd Packed Double-FP Horizontal Subtract
                        case 0x7e://MOVD Pq Ed Move Doubleword
                        case 0x7f://MOVQ Pq Qq Move Quadword
                        case 0xa6:
                        case 0xa7:
                        case 0xaa://RSM  Flags Resume from System Management Mode
                        case 0xae://FXSAVE ST Mstx Save x87 FPU, MMX, XMM, and MXCSR State
                        case 0xb8://JMPE   Jump to IA-64 Instruction Set
                        case 0xb9://UD G  Undefined Instruction
                        case 0xc2://CMPPS Wps Vps Compare Packed Single-FP Values
                        case 0xc3://MOVNTI Gdqp Mdqp Store Doubleword Using Non-Temporal Hint
                        case 0xc4://PINSRW Rdqp Pq Insert Word
                        case 0xc5://PEXTRW Nq Gdqp Extract Word
                        case 0xc6://SHUFPS Wps Vps Shuffle Packed Single-FP Values
                        case 0xc7://CMPXCHG8B EBX Mq Compare and Exchange Bytes
                        default:
                            abort(6);
                    }
                    break;
            }
        }
        return n;
    }


    /*
       Typically, the upper 20 bits of CR3 become the page directory base register (PDBR),
       which stores the physical address of the first page directory entry.
    */
    function do_tlb_set_page(Gd, Hd, ja) {
        var Id, Jd, error_code, Kd, Ld, Md, Nd, ud, Od;
        if (!(cpu.cr0 & (1 << 31))) { //CR0: bit31 PG Paging If 1, enable paging and use the CR3 register, else disable paging
            cpu.tlb_set_page(Gd & -4096, Gd & -4096, 1);
        } else {
            Id = (cpu.cr3 & -4096) + ((Gd >> 20) & 0xffc);
            Jd = cpu.ld32_phys(Id);
            if (!(Jd & 0x00000001)) {
                error_code = 0;
            } else {
                if (!(Jd & 0x00000020)) {
                    Jd |= 0x00000020;
                    cpu.st32_phys(Id, Jd);
                }
                Kd = (Jd & -4096) + ((Gd >> 10) & 0xffc);
                Ld = cpu.ld32_phys(Kd);
                if (!(Ld & 0x00000001)) {
                    error_code = 0;
                } else {
                    Md = Ld & Jd;
                    if (ja && !(Md & 0x00000004)) {
                        error_code = 0x01;
                    } else if (Hd && !(Md & 0x00000002)) {
                        error_code = 0x01;
                    } else {
                        Nd = (Hd && !(Ld & 0x00000040));
                        if (!(Ld & 0x00000020) || Nd) {
                            Ld |= 0x00000020;
                            if (Nd)
                                Ld |= 0x00000040;
                            cpu.st32_phys(Kd, Ld);
                        }
                        ud = 0;
                        if ((Ld & 0x00000040) && (Md & 0x00000002))
                            ud = 1;
                        Od = 0;
                        if (Md & 0x00000004)
                            Od = 1;
                        cpu.tlb_set_page(Gd & -4096, Ld & -4096, ud, Od);
                        return;
                    }
                }
            }
            error_code |= Hd << 1;
            if (ja)
                error_code |= 0x04;
            cpu.cr2 = Gd;
            abort_with_error_code(14, error_code);
        }
    }
    function set_CR0(Qd) {
        if (!(Qd & (1 << 0)))  //0th bit protected or real, real not supported!
            cpu_abort("real mode not supported");
        //if changing flags 31, 16, or 0 must flush tlb
        if ((Qd & ((1 << 31) | (1 << 16) | (1 << 0))) != (cpu.cr0 & ((1 << 31) | (1 << 16) | (1 << 0)))) {
            cpu.tlb_flush_all();
        }
        cpu.cr0 = Qd | (1 << 4); //keep bit 4 set to 1
    }
    function set_CR3(new_pdb) { // page directory base register PDBR
        cpu.cr3 = new_pdb;
        if (cpu.cr0 & (1 << 31)) { //if in paging mode must reset tables
            cpu.tlb_flush_all();
        }
    }
    function set_CR4(newval) {
        cpu.cr4 = newval;
    }

    /*
      Segment / Descriptor Handling Functions
      -----------------------------------------
    */

    function SS_mask_from_flags(descriptor_high4bytes) {
        if (descriptor_high4bytes & (1 << 22))
            return -1;
        else
            return 0xffff;
    }
    function load_from_descriptor_table(selector) {
        var descriptor_table, Rb, descriptor_low4bytes, descriptor_high4bytes;
        if (selector & 0x4)
            descriptor_table = cpu.ldt;
        else
            descriptor_table = cpu.gdt;
        Rb = selector & ~7;
        if ((Rb + 7) > descriptor_table.limit)
            return null;
        mem8_loc = descriptor_table.base + Rb;
        descriptor_low4bytes = ld32_mem8_kernel_read();
        mem8_loc += 4;
        descriptor_high4bytes = ld32_mem8_kernel_read();
        return [descriptor_low4bytes, descriptor_high4bytes];
    }
    function calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes) {
        var limit;
        limit = (descriptor_low4bytes & 0xffff) | (descriptor_high4bytes & 0x000f0000);
        if (descriptor_high4bytes & (1 << 23))
            limit = (limit << 12) | 0xfff;
        return limit;
    }
    function calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes) {
        return (((descriptor_low4bytes >>> 16) | ((descriptor_high4bytes & 0xff) << 16) | (descriptor_high4bytes & 0xff000000))) & -1;
    }
    /* Used to set TR and LDTR */
    function set_descriptor_register(descriptor_table, descriptor_low4bytes, descriptor_high4bytes) {
        descriptor_table.base = calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes);
        descriptor_table.limit = calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes);
        descriptor_table.flags = descriptor_high4bytes;
    }
    function init_segment_local_vars() {
        CS_base = cpu.segs[1].base;//CS
        SS_base = cpu.segs[2].base;//SS
        if (cpu.segs[2].flags & (1 << 22))
            SS_mask = -1;
        else
            SS_mask = 0xffff;
        FS_usage_flag = (((CS_base | SS_base | cpu.segs[3].base | cpu.segs[0].base) == 0) && SS_mask == -1);
        if (cpu.segs[1].flags & (1 << 22))
            init_CS_flags = 0;
        else
            init_CS_flags = 0x0100 | 0x0080;
    }
    function set_segment_vars(ee, selector, base, limit, flags) {
        cpu.segs[ee] = {selector: selector,base: base,limit: limit,flags: flags};
        init_segment_local_vars();
    }
    function init_segment_vars_with_selector(Sb, selector) {
        set_segment_vars(Sb, selector, (selector << 4), 0xffff, (1 << 15) | (3 << 13) | (1 << 12) | (1 << 8) | (1 << 12) | (1 << 9));
    }


    /*
      Flow Control Functions
      -----------------------------------------------------
      These are some of the most complicated subroutines here as they involve storing / retrieving the TSS and other
      key data structures when transferring control to other locations.
    */


    /* used only in CALLF, and InterruptF in paged mode */
    function load_from_TR(he) {
        var tr_type, Rb, is_32_bit, ke, le;
        if (!(cpu.tr.flags & (1 << 15)))
            cpu_abort("invalid tss");  //task state segment
        tr_type = (cpu.tr.flags >> 8) & 0xf;
        if ((tr_type & 7) != 1)
            cpu_abort("invalid tss type");
        is_32_bit = tr_type >> 3;
        Rb = (he * 4 + 2) << is_32_bit;
        if (Rb + (4 << is_32_bit) - 1 > cpu.tr.limit)
            abort_with_error_code(10, cpu.tr.selector & 0xfffc);
        mem8_loc = (cpu.tr.base + Rb) & -1;
        if (is_32_bit == 0) {
            le = ld16_mem8_kernel_read();
            mem8_loc += 2;
        } else {
            le = ld32_mem8_kernel_read();
            mem8_loc += 4;
        }
        ke = ld16_mem8_kernel_read();
        return [ke, le];
    }
    function do_interrupt_protected_mode(intno, ne, error_code, oe, pe) {
        var descriptor_table, qe, descriptor_type, he, selector, re, cpl_var;
        var te, ue, is_32_bit;
        var e, descriptor_low4bytes, descriptor_high4bytes, ve, ke, le, we, xe;
        var ye, SS_mask;
        te = 0;
        if (!ne && !pe) {
            switch (intno) {
                case 8:
                case 10:
                case 11:
                case 12:
                case 13:
                case 14:
                case 17:
                    te = 1;
                    break;
            }
        }
        if (ne)
            ye = oe;
        else
            ye = eip;
        descriptor_table = cpu.idt;
        if (intno * 8 + 7 > descriptor_table.limit)
            abort_with_error_code(13, intno * 8 + 2);
        mem8_loc = (descriptor_table.base + intno * 8) & -1;
        descriptor_low4bytes = ld32_mem8_kernel_read();
        mem8_loc += 4;
        descriptor_high4bytes = ld32_mem8_kernel_read();
        descriptor_type = (descriptor_high4bytes >> 8) & 0x1f;
        switch (descriptor_type) {
            case 5:
            case 7:
            case 6:
                throw "unsupported task gate";
            case 14:
            case 15:
                break;
            default:
                abort_with_error_code(13, intno * 8 + 2);
                break;
        }
        dpl = (descriptor_high4bytes >> 13) & 3;
        cpl_var = cpu.cpl;
        if (ne && dpl < cpl_var)
            abort_with_error_code(13, intno * 8 + 2);
        if (!(descriptor_high4bytes & (1 << 15)))
            abort_with_error_code(11, intno * 8 + 2);
        selector = descriptor_low4bytes >> 16;
        ve = (descriptor_high4bytes & -65536) | (descriptor_low4bytes & 0x0000ffff);
        if ((selector & 0xfffc) == 0)
            abort_with_error_code(13, 0);
        e = load_from_descriptor_table(selector);
        if (!e)
            abort_with_error_code(13, selector & 0xfffc);
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        if (!(descriptor_high4bytes & (1 << 12)) || !(descriptor_high4bytes & ((1 << 11))))
            abort_with_error_code(13, selector & 0xfffc);
        dpl = (descriptor_high4bytes >> 13) & 3;
        if (dpl > cpl_var)
            abort_with_error_code(13, selector & 0xfffc);
        if (!(descriptor_high4bytes & (1 << 15)))
            abort_with_error_code(11, selector & 0xfffc);
        if (!(descriptor_high4bytes & (1 << 10)) && dpl < cpl_var) {
            e = load_from_TR(dpl);
            ke = e[0];
            le = e[1];
            if ((ke & 0xfffc) == 0)
                abort_with_error_code(10, ke & 0xfffc);
            if ((ke & 3) != dpl)
                abort_with_error_code(10, ke & 0xfffc);
            e = load_from_descriptor_table(ke);
            if (!e)
                abort_with_error_code(10, ke & 0xfffc);
            we = e[0];
            xe = e[1];
            re = (xe >> 13) & 3;
            if (re != dpl)
                abort_with_error_code(10, ke & 0xfffc);
            if (!(xe & (1 << 12)) || (xe & (1 << 11)) || !(xe & (1 << 9)))
                abort_with_error_code(10, ke & 0xfffc);
            if (!(xe & (1 << 15)))
                abort_with_error_code(10, ke & 0xfffc);
            ue = 1;
            SS_mask = SS_mask_from_flags(xe);
            qe = calculate_descriptor_base(we, xe);
        } else if ((descriptor_high4bytes & (1 << 10)) || dpl == cpl_var) {
            if (cpu.eflags & 0x00020000)
                abort_with_error_code(13, selector & 0xfffc);
            ue = 0;
            SS_mask = SS_mask_from_flags(cpu.segs[2].flags);
            qe = cpu.segs[2].base;
            le = regs[4];
            dpl = cpl_var;
        } else {
            abort_with_error_code(13, selector & 0xfffc);
            ue = 0;
            SS_mask = 0;
            qe = 0;
            le = 0;
        }
        is_32_bit = descriptor_type >> 3;
        if (is_32_bit == 1) {
            if (ue) {
                if (cpu.eflags & 0x00020000) {
                    {
                        le = (le - 4) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[5].selector);
                    }
                    {
                        le = (le - 4) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[4].selector);
                    }
                    {
                        le = (le - 4) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[3].selector);
                    }
                    {
                        le = (le - 4) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[0].selector);
                    }
                }
                {
                    le = (le - 4) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st32_mem8_kernel_write(cpu.segs[2].selector);
                }
                {
                    le = (le - 4) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st32_mem8_kernel_write(regs[4]);
                }
            }
            {
                le = (le - 4) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st32_mem8_kernel_write(get_FLAGS());
            }
            {
                le = (le - 4) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st32_mem8_kernel_write(cpu.segs[1].selector);
            }
            {
                le = (le - 4) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st32_mem8_kernel_write(ye);
            }
            if (te) {
                {
                    le = (le - 4) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st32_mem8_kernel_write(error_code);
                }
            }
        } else {
            if (ue) {
                if (cpu.eflags & 0x00020000) {
                    {
                        le = (le - 2) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[5].selector);
                    }
                    {
                        le = (le - 2) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[4].selector);
                    }
                    {
                        le = (le - 2) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[3].selector);
                    }
                    {
                        le = (le - 2) & -1;
                        mem8_loc = (qe + (le & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[0].selector);
                    }
                }
                {
                    le = (le - 2) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st16_mem8_kernel_write(cpu.segs[2].selector);
                }
                {
                    le = (le - 2) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st16_mem8_kernel_write(regs[4]);
                }
            }
            {
                le = (le - 2) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st16_mem8_kernel_write(get_FLAGS());
            }
            {
                le = (le - 2) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st16_mem8_kernel_write(cpu.segs[1].selector);
            }
            {
                le = (le - 2) & -1;
                mem8_loc = (qe + (le & SS_mask)) & -1;
                st16_mem8_kernel_write(ye);
            }
            if (te) {
                {
                    le = (le - 2) & -1;
                    mem8_loc = (qe + (le & SS_mask)) & -1;
                    st16_mem8_kernel_write(error_code);
                }
            }
        }
        if (ue) {
            if (cpu.eflags & 0x00020000) {
                set_segment_vars(0, 0, 0, 0, 0);
                set_segment_vars(3, 0, 0, 0, 0);
                set_segment_vars(4, 0, 0, 0, 0);
                set_segment_vars(5, 0, 0, 0, 0);
            }
            ke = (ke & ~3) | dpl;
            set_segment_vars(2, ke, qe, calculate_descriptor_limit(we, xe), xe);
        }
        regs[4] = (regs[4] & ~SS_mask) | ((le) & SS_mask);
        selector = (selector & ~3) | dpl;
        set_segment_vars(1, selector, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes), descriptor_high4bytes);
        change_permission_level(dpl);
        eip = ve, physmem8_ptr = initial_mem_ptr = 0;
        if ((descriptor_type & 1) == 0) {
            cpu.eflags &= ~0x00000200;
        }
        cpu.eflags &= ~(0x00000100 | 0x00020000 | 0x00010000 | 0x00004000);
    }
    function do_interrupt_not_protected_mode(intno, ne, error_code, oe, pe) {
        var descriptor_table, qe, selector, ve, le, ye;
        descriptor_table = cpu.idt;
        if (intno * 4 + 3 > descriptor_table.limit)
            abort_with_error_code(13, intno * 8 + 2);
        mem8_loc = (descriptor_table.base + (intno << 2)) >> 0;
        ve = ld16_mem8_kernel_read();
        mem8_loc = (mem8_loc + 2) >> 0;
        selector = ld16_mem8_kernel_read();
        le = regs[4];
        if (ne)
            ye = oe;
        else
            ye = eip;
        {
            le = (le - 2) >> 0;
            mem8_loc = ((le & SS_mask) + SS_base) >> 0;
            st16_mem8_write(get_FLAGS());
        }
        {
            le = (le - 2) >> 0;
            mem8_loc = ((le & SS_mask) + SS_base) >> 0;
            st16_mem8_write(cpu.segs[1].selector);
        }
        {
            le = (le - 2) >> 0;
            mem8_loc = ((le & SS_mask) + SS_base) >> 0;
            st16_mem8_write(ye);
        }
        regs[4] = (regs[4] & ~SS_mask) | ((le) & SS_mask);
        eip = ve, physmem8_ptr = initial_mem_ptr = 0;
        cpu.segs[1].selector = selector;
        cpu.segs[1].base = (selector << 4);
        cpu.eflags &= ~(0x00000200 | 0x00000100 | 0x00040000 | 0x00010000);
    }
    function do_interrupt(intno, ne, error_code, oe, pe) {
        if (intno == 0x06) {
            var eip_tmp = eip;
            var eip_offset;
            str = "do_interrupt: intno=" + _2_bytes_(intno) + " error_code=" + _4_bytes_(error_code)
                + " EIP=" + _4_bytes_(eip_tmp) + " ESP=" + _4_bytes_(regs[4]) + " EAX=" + _4_bytes_(regs[0])
                + " EBX=" + _4_bytes_(regs[3]) + " ECX=" + _4_bytes_(regs[1]);
            if (intno == 0x0e) {
                str += " CR2=" + _4_bytes_(cpu.cr2);
            }
            console.log(str);
            if (intno == 0x06) {
                var str, i, n;
                str = "Code:";
                eip_offset = (eip_tmp + CS_base) >> 0;
                n = 4096 - (eip_offset & 0xfff);
                if (n > 15)
                    n = 15;
                for (i = 0; i < n; i++) {
                    mem8_loc = (eip_offset + i) & -1;
                    str += " " + _2_bytes_(ld_8bits_mem8_read());
                }
                console.log(str);
            }
        }
        if (cpu.cr0 & (1 << 0)) {
            do_interrupt_protected_mode(intno, ne, error_code, oe, pe);
        } else {
            do_interrupt_not_protected_mode(intno, ne, error_code, oe, pe);
        }
    }
    //SLDT routines
    function op_LDTR(selector) {
        var descriptor_table, descriptor_low4bytes, descriptor_high4bytes, Rb, De;
        selector &= 0xffff;
        if ((selector & 0xfffc) == 0) {
            cpu.ldt.base = 0;
            cpu.ldt.limit = 0;
        } else {
            if (selector & 0x4)
                abort_with_error_code(13, selector & 0xfffc);
            descriptor_table = cpu.gdt;
            Rb = selector & ~7;
            De = 7;
            if ((Rb + De) > descriptor_table.limit)
                abort_with_error_code(13, selector & 0xfffc);
            mem8_loc = (descriptor_table.base + Rb) & -1;
            descriptor_low4bytes = ld32_mem8_kernel_read();
            mem8_loc += 4;
            descriptor_high4bytes = ld32_mem8_kernel_read();
            if ((descriptor_high4bytes & (1 << 12)) || ((descriptor_high4bytes >> 8) & 0xf) != 2)
                abort_with_error_code(13, selector & 0xfffc);
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            set_descriptor_register(cpu.ldt, descriptor_low4bytes, descriptor_high4bytes);
        }
        cpu.ldt.selector = selector;
    }
    function op_LTR(selector) {
        var descriptor_table, descriptor_low4bytes, descriptor_high4bytes, Rb, descriptor_type, De;
        selector &= 0xffff;
        if ((selector & 0xfffc) == 0) {
            cpu.tr.base = 0;
            cpu.tr.limit = 0;
            cpu.tr.flags = 0;
        } else {
            if (selector & 0x4)
                abort_with_error_code(13, selector & 0xfffc);
            descriptor_table = cpu.gdt;
            Rb = selector & ~7;
            De = 7;
            if ((Rb + De) > descriptor_table.limit)
                abort_with_error_code(13, selector & 0xfffc);
            mem8_loc = (descriptor_table.base + Rb) & -1;
            descriptor_low4bytes = ld32_mem8_kernel_read();
            mem8_loc += 4;
            descriptor_high4bytes = ld32_mem8_kernel_read();
            descriptor_type = (descriptor_high4bytes >> 8) & 0xf;
            if ((descriptor_high4bytes & (1 << 12)) || (descriptor_type != 1 && descriptor_type != 9))
                abort_with_error_code(13, selector & 0xfffc);
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            set_descriptor_register(cpu.tr, descriptor_low4bytes, descriptor_high4bytes);
            descriptor_high4bytes |= (1 << 9);
            st32_mem8_kernel_write(descriptor_high4bytes);
        }
        cpu.tr.selector = selector;
    }
    function set_protected_mode_segment_register(register, selector) {
        var descriptor_low4bytes, descriptor_high4bytes, cpl_var, dpl, rpl, descriptor_table, selector_index;
        cpl_var = cpu.cpl;
        if ((selector & 0xfffc) == 0) {                        //null selector
            if (register == 2)                                 //(SS == null) => #GP(0)
                abort_with_error_code(13, 0);
            set_segment_vars(register, selector, 0, 0, 0);
        } else {
            if (selector & 0x4)
                descriptor_table = cpu.ldt;
            else
                descriptor_table = cpu.gdt;
            selector_index = selector & ~7;
            if ((selector_index + 7) > descriptor_table.limit)
                abort_with_error_code(13, selector & 0xfffc);
            mem8_loc = (descriptor_table.base + selector_index) & -1;
            descriptor_low4bytes = ld32_mem8_kernel_read();
            mem8_loc += 4;
            descriptor_high4bytes = ld32_mem8_kernel_read();
            if (!(descriptor_high4bytes & (1 << 12)))
                abort_with_error_code(13, selector & 0xfffc);
            rpl = selector & 3;
            dpl = (descriptor_high4bytes >> 13) & 3;
            if (register == 2) {
                if ((descriptor_high4bytes & (1 << 11)) || !(descriptor_high4bytes & (1 << 9)))
                    abort_with_error_code(13, selector & 0xfffc);
                if (rpl != cpl_var || dpl != cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
            } else {
                if ((descriptor_high4bytes & ((1 << 11) | (1 << 9))) == (1 << 11))
                    abort_with_error_code(13, selector & 0xfffc);
                if (!(descriptor_high4bytes & (1 << 11)) || !(descriptor_high4bytes & (1 << 10))) {
                    if (dpl < cpl_var || dpl < rpl)
                        abort_with_error_code(13, selector & 0xfffc);
                }
            }
            if (!(descriptor_high4bytes & (1 << 15))) {
                if (register == 2)
                    abort_with_error_code(12, selector & 0xfffc);
                else
                    abort_with_error_code(11, selector & 0xfffc);
            }
            if (!(descriptor_high4bytes & (1 << 8))) {
                descriptor_high4bytes |= (1 << 8);
                st32_mem8_kernel_write(descriptor_high4bytes);
            }
            set_segment_vars(register, selector, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes), descriptor_high4bytes);
        }
    }
    function set_segment_register(register, selector) {
        var descriptor_table;
        selector &= 0xffff;
        if (!(cpu.cr0 & (1 << 0))) {          //CR0.PE (0 == real mode)
            descriptor_table = cpu.segs[register];
            descriptor_table.selector = selector;
            descriptor_table.base = selector << 4;
        } else if (cpu.eflags & 0x00020000) { //EFLAGS.VM (1 == v86 mode)
            init_segment_vars_with_selector(register, selector);
        } else {                              //protected mode
            set_protected_mode_segment_register(register, selector);
         }
    }
    function do_JMPF_virtual_mode(selector, Le) {
        eip = Le, physmem8_ptr = initial_mem_ptr = 0;
        cpu.segs[1].selector = selector;
        cpu.segs[1].base = (selector << 4);
        init_segment_local_vars();
    }
    function do_JMPF(selector, Le) {
        var Ne, ie, descriptor_low4bytes, descriptor_high4bytes, cpl_var, dpl, rpl, limit, e;
        if ((selector & 0xfffc) == 0)
            abort_with_error_code(13, 0);
        e = load_from_descriptor_table(selector);
        if (!e)
            abort_with_error_code(13, selector & 0xfffc);
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        cpl_var = cpu.cpl;
        if (descriptor_high4bytes & (1 << 12)) {
            if (!(descriptor_high4bytes & (1 << 11)))
                abort_with_error_code(13, selector & 0xfffc);
            dpl = (descriptor_high4bytes >> 13) & 3;
            if (descriptor_high4bytes & (1 << 10)) {
                if (dpl > cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
            } else {
                rpl = selector & 3;
                if (rpl > cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
                if (dpl != cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
            }
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            limit = calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes);
            if ((Le >>> 0) > (limit >>> 0))
                abort_with_error_code(13, selector & 0xfffc);
            set_segment_vars(1, (selector & 0xfffc) | cpl_var, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), limit, descriptor_high4bytes);
            eip = Le, physmem8_ptr = initial_mem_ptr = 0;
        } else {
            cpu_abort("unsupported jump to call or task gate");
        }
    }
    function op_JMPF(selector, Le) {
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000)) {
            do_JMPF_virtual_mode(selector, Le);
        } else {
            do_JMPF(selector, Le);
        }
    }

    /* used only in do_return_protected_mode */
    function Pe(register, cpl_var) {
        var dpl, descriptor_high4bytes;
        if ((register == 4 || register == 5) && (cpu.segs[register].selector & 0xfffc) == 0)
            return;
        descriptor_high4bytes = cpu.segs[register].flags;
        dpl = (descriptor_high4bytes >> 13) & 3;
        if (!(descriptor_high4bytes & (1 << 11)) || !(descriptor_high4bytes & (1 << 10))) {
            if (dpl < cpl_var) {
                set_segment_vars(register, 0, 0, 0, 0);
            }
        }
    }

    function op_CALLF_not_protected_mode(is_32_bit, selector, Le, oe) {
        var le;
        le = regs[4];
        if (is_32_bit) {
            {
                le = (le - 4) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st32_mem8_write(cpu.segs[1].selector);
            }
            {
                le = (le - 4) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st32_mem8_write(oe);
            }
        } else {
            {
                le = (le - 2) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st16_mem8_write(cpu.segs[1].selector);
            }
            {
                le = (le - 2) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st16_mem8_write(oe);
            }
        }
        regs[4] = (regs[4] & ~SS_mask) | ((le) & SS_mask);
        eip = Le, physmem8_ptr = initial_mem_ptr = 0;
        cpu.segs[1].selector = selector;
        cpu.segs[1].base = (selector << 4);
        init_segment_local_vars();
    }
    function op_CALLF_protected_mode(is_32_bit, selector, Le, oe) {
        var ue, i, e;
        var descriptor_low4bytes, descriptor_high4bytes, cpl_var, dpl, rpl, selector, ve, Se;
        var ke, we, xe, esp, descriptor_type, re, SS_mask;
        var x, limit, Ue;
        var qe, Ve, We;
        if ((selector & 0xfffc) == 0)
            abort_with_error_code(13, 0);
        e = load_from_descriptor_table(selector);
        if (!e)
            abort_with_error_code(13, selector & 0xfffc);
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        cpl_var = cpu.cpl;
        We = regs[4];
        if (descriptor_high4bytes & (1 << 12)) {
            if (!(descriptor_high4bytes & (1 << 11)))
                abort_with_error_code(13, selector & 0xfffc);
            dpl = (descriptor_high4bytes >> 13) & 3;
            if (descriptor_high4bytes & (1 << 10)) {
                if (dpl > cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
            } else {
                rpl = selector & 3;
                if (rpl > cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
                if (dpl != cpl_var)
                    abort_with_error_code(13, selector & 0xfffc);
            }
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            {
                esp = We;
                SS_mask = SS_mask_from_flags(cpu.segs[2].flags);
                qe = cpu.segs[2].base;
                if (is_32_bit) {
                    {
                        esp = (esp - 4) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[1].selector);
                    }
                    {
                        esp = (esp - 4) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st32_mem8_kernel_write(oe);
                    }
                } else {
                    {
                        esp = (esp - 2) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[1].selector);
                    }
                    {
                        esp = (esp - 2) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st16_mem8_kernel_write(oe);
                    }
                }
                limit = calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes);
                if (Le > limit)
                    abort_with_error_code(13, selector & 0xfffc);
                regs[4] = (regs[4] & ~SS_mask) | ((esp) & SS_mask);
                set_segment_vars(1, (selector & 0xfffc) | cpl_var, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), limit, descriptor_high4bytes);
                eip = Le, physmem8_ptr = initial_mem_ptr = 0;
            }
        } else {
            descriptor_type = (descriptor_high4bytes >> 8) & 0x1f;
            dpl = (descriptor_high4bytes >> 13) & 3;
            rpl = selector & 3;
            switch (descriptor_type) {
                case 1:
                case 9:
                case 5:
                    throw "unsupported task gate";
                    return;
                case 4:
                case 12:
                    break;
                default:
                    abort_with_error_code(13, selector & 0xfffc);
                    break;
            }
            is_32_bit = descriptor_type >> 3;
            if (dpl < cpl_var || dpl < rpl)
                abort_with_error_code(13, selector & 0xfffc);
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            selector = descriptor_low4bytes >> 16;
            ve = (descriptor_high4bytes & 0xffff0000) | (descriptor_low4bytes & 0x0000ffff);
            Se = descriptor_high4bytes & 0x1f;
            if ((selector & 0xfffc) == 0)
                abort_with_error_code(13, 0);
            e = load_from_descriptor_table(selector);
            if (!e)
                abort_with_error_code(13, selector & 0xfffc);
            descriptor_low4bytes = e[0];
            descriptor_high4bytes = e[1];
            if (!(descriptor_high4bytes & (1 << 12)) || !(descriptor_high4bytes & ((1 << 11))))
                abort_with_error_code(13, selector & 0xfffc);
            dpl = (descriptor_high4bytes >> 13) & 3;
            if (dpl > cpl_var)
                abort_with_error_code(13, selector & 0xfffc);
            if (!(descriptor_high4bytes & (1 << 15)))
                abort_with_error_code(11, selector & 0xfffc);
            if (!(descriptor_high4bytes & (1 << 10)) && dpl < cpl_var) {
                e = load_from_TR(dpl);
                ke = e[0];
                esp = e[1];
                if ((ke & 0xfffc) == 0)
                    abort_with_error_code(10, ke & 0xfffc);
                if ((ke & 3) != dpl)
                    abort_with_error_code(10, ke & 0xfffc);
                e = load_from_descriptor_table(ke);
                if (!e)
                    abort_with_error_code(10, ke & 0xfffc);
                we = e[0];
                xe = e[1];
                re = (xe >> 13) & 3;
                if (re != dpl)
                    abort_with_error_code(10, ke & 0xfffc);
                if (!(xe & (1 << 12)) || (xe & (1 << 11)) || !(xe & (1 << 9)))
                    abort_with_error_code(10, ke & 0xfffc);
                if (!(xe & (1 << 15)))
                    abort_with_error_code(10, ke & 0xfffc);
                Ue = SS_mask_from_flags(cpu.segs[2].flags);
                Ve = cpu.segs[2].base;
                SS_mask = SS_mask_from_flags(xe);
                qe = calculate_descriptor_base(we, xe);
                if (is_32_bit) {
                    {
                        esp = (esp - 4) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st32_mem8_kernel_write(cpu.segs[2].selector);
                    }
                    {
                        esp = (esp - 4) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st32_mem8_kernel_write(We);
                    }
                    for (i = Se - 1; i >= 0; i--) {
                        x = Xe(Ve + ((We + i * 4) & Ue));
                        {
                            esp = (esp - 4) & -1;
                            mem8_loc = (qe + (esp & SS_mask)) & -1;
                            st32_mem8_kernel_write(x);
                        }
                    }
                } else {
                    {
                        esp = (esp - 2) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st16_mem8_kernel_write(cpu.segs[2].selector);
                    }
                    {
                        esp = (esp - 2) & -1;
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        st16_mem8_kernel_write(We);
                    }
                    for (i = Se - 1; i >= 0; i--) {
                        x = Ye(Ve + ((We + i * 2) & Ue));
                        {
                            esp = (esp - 2) & -1;
                            mem8_loc = (qe + (esp & SS_mask)) & -1;
                            st16_mem8_kernel_write(x);
                        }
                    }
                }
                ue = 1;
            } else {
                esp = We;
                SS_mask = SS_mask_from_flags(cpu.segs[2].flags);
                qe = cpu.segs[2].base;
                ue = 0;
            }
            if (is_32_bit) {
                {
                    esp = (esp - 4) & -1;
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    st32_mem8_kernel_write(cpu.segs[1].selector);
                }
                {
                    esp = (esp - 4) & -1;
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    st32_mem8_kernel_write(oe);
                }
            } else {
                {
                    esp = (esp - 2) & -1;
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    st16_mem8_kernel_write(cpu.segs[1].selector);
                }
                {
                    esp = (esp - 2) & -1;
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    st16_mem8_kernel_write(oe);
                }
            }
            if (ue) {
                ke = (ke & ~3) | dpl;
                set_segment_vars(2, ke, qe, calculate_descriptor_limit(we, xe), xe);
            }
            selector = (selector & ~3) | dpl;
            set_segment_vars(1, selector, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes), descriptor_high4bytes);
            change_permission_level(dpl);
            regs[4] = (regs[4] & ~SS_mask) | ((esp) & SS_mask);
            eip = ve, physmem8_ptr = initial_mem_ptr = 0;
        }
    }
    function op_CALLF(is_32_bit, selector, Le, oe) {
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000)) {
            op_CALLF_not_protected_mode(is_32_bit, selector, Le, oe);
        } else {
            op_CALLF_protected_mode(is_32_bit, selector, Le, oe);
        }
    }
    function do_return_not_protected_mode(is_32_bit, is_iret, imm16) {
        var esp, selector, stack_eip, stack_eflags, SS_mask, qe, ef;
        SS_mask = 0xffff;
        esp = regs[4];
        qe = cpu.segs[2].base;
        if (is_32_bit == 1) {
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eip = ld32_mem8_kernel_read();
                esp = (esp + 4) & -1;
            }
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                selector = ld32_mem8_kernel_read();
                esp = (esp + 4) & -1;
            }
            selector &= 0xffff;
            if (is_iret) {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eflags = ld32_mem8_kernel_read();
                esp = (esp + 4) & -1;
            }
        } else {
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eip = ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                selector = ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
            if (is_iret) {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eflags = ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
        }
        regs[4] = (regs[4] & ~SS_mask) | ((esp + imm16) & SS_mask);
        cpu.segs[1].selector = selector;
        cpu.segs[1].base = (selector << 4);
        eip = stack_eip, physmem8_ptr = initial_mem_ptr = 0;
        if (is_iret) {
            if (cpu.eflags & 0x00020000)
                ef = 0x00000100 | 0x00040000 | 0x00200000 | 0x00000200 | 0x00010000 | 0x00004000;
            else
                ef = 0x00000100 | 0x00040000 | 0x00200000 | 0x00000200 | 0x00003000 | 0x00010000 | 0x00004000;
            if (is_32_bit == 0)
                ef &= 0xffff;
            set_FLAGS(stack_eflags, ef);
        }
        init_segment_local_vars();
    }
    function do_return_protected_mode(is_32_bit, is_iret, imm16) {
        var selector, stack_eflags, gf;
        var hf, jf, kf, lf;
        var e, descriptor_low4bytes, descriptor_high4bytes, we, xe;
        var cpl_var, dpl, rpl, ef, iopl;
        var qe, esp, stack_eip, wd, SS_mask;
        SS_mask = SS_mask_from_flags(cpu.segs[2].flags);
        esp = regs[4];
        qe = cpu.segs[2].base;
        stack_eflags = 0;
        if (is_32_bit == 1) {
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eip = ld32_mem8_kernel_read();
                esp = (esp + 4) & -1;
            }
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                selector = ld32_mem8_kernel_read();                //CS selector
                esp = (esp + 4) & -1;
            }
            selector &= 0xffff;
            if (is_iret) {
                {
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    stack_eflags = ld32_mem8_kernel_read();
                    esp = (esp + 4) & -1;
                }
                if (stack_eflags & 0x00020000) {     //eflags.VM (return to v86 mode)
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        wd = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    //pop segment selectors from stack
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        gf = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        hf = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        jf = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        kf = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    {
                        mem8_loc = (qe + (esp & SS_mask)) & -1;
                        lf = ld32_mem8_kernel_read();
                        esp = (esp + 4) & -1;
                    }
                    set_FLAGS(stack_eflags, 0x00000100 | 0x00040000 | 0x00200000 | 0x00000200 | 0x00003000 | 0x00020000 | 0x00004000 | 0x00080000 | 0x00100000);
                    init_segment_vars_with_selector(1, selector & 0xffff);
                    change_permission_level(3);
                    init_segment_vars_with_selector(2, gf & 0xffff);
                    init_segment_vars_with_selector(0, hf & 0xffff);
                    init_segment_vars_with_selector(3, jf & 0xffff);
                    init_segment_vars_with_selector(4, kf & 0xffff);
                    init_segment_vars_with_selector(5, lf & 0xffff);
                    eip = stack_eip & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                    regs[4] = (regs[4] & ~SS_mask) | ((wd) & SS_mask);
                    return;
                }
            }
        } else {
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eip= ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
            {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                selector = ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
            if (is_iret) {
                mem8_loc = (qe + (esp & SS_mask)) & -1;
                stack_eflags = ld16_mem8_kernel_read();
                esp = (esp + 2) & -1;
            }
        }
        if ((selector & 0xfffc) == 0)
            abort_with_error_code(13, selector & 0xfffc);
        e = load_from_descriptor_table(selector);
        if (!e)
            abort_with_error_code(13, selector & 0xfffc);
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        if (!(descriptor_high4bytes & (1 << 12)) || !(descriptor_high4bytes & (1 << 11)))
            abort_with_error_code(13, selector & 0xfffc);
        cpl_var = cpu.cpl;
        rpl = selector & 3;
        if (rpl < cpl_var)
            abort_with_error_code(13, selector & 0xfffc);
        dpl = (descriptor_high4bytes >> 13) & 3;
        if (descriptor_high4bytes & (1 << 10)) {
            if (dpl > rpl)
                abort_with_error_code(13, selector & 0xfffc);
        } else {
            if (dpl != rpl)
                abort_with_error_code(13, selector & 0xfffc);
        }
        if (!(descriptor_high4bytes & (1 << 15)))
            abort_with_error_code(11, selector & 0xfffc);
        esp = (esp + imm16) & -1;
        if (rpl == cpl_var) {
            set_segment_vars(1, selector, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes), descriptor_high4bytes);
        } else {
            if (is_32_bit == 1) {
                {
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    wd = ld32_mem8_kernel_read();
                    esp = (esp + 4) & -1;
                }
                {
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    gf = ld32_mem8_kernel_read();
                    esp = (esp + 4) & -1;
                }
                gf &= 0xffff;
            } else {
                {
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    wd = ld16_mem8_kernel_read();
                    esp = (esp + 2) & -1;
                }
                {
                    mem8_loc = (qe + (esp & SS_mask)) & -1;
                    gf = ld16_mem8_kernel_read();
                    esp = (esp + 2) & -1;
                }
            }
            if ((gf & 0xfffc) == 0) {
                abort_with_error_code(13, 0);
            } else {
                if ((gf & 3) != rpl)
                    abort_with_error_code(13, gf & 0xfffc);
                e = load_from_descriptor_table(gf);
                if (!e)
                    abort_with_error_code(13, gf & 0xfffc);
                we = e[0];
                xe = e[1];
                if (!(xe & (1 << 12)) || (xe & (1 << 11)) || !(xe & (1 << 9)))
                    abort_with_error_code(13, gf & 0xfffc);
                dpl = (xe >> 13) & 3;
                if (dpl != rpl)
                    abort_with_error_code(13, gf & 0xfffc);
                if (!(xe & (1 << 15)))
                    abort_with_error_code(11, gf & 0xfffc);
                set_segment_vars(2, gf, calculate_descriptor_base(we, xe), calculate_descriptor_limit(we, xe), xe);
            }
            set_segment_vars(1, selector, calculate_descriptor_base(descriptor_low4bytes, descriptor_high4bytes), calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes), descriptor_high4bytes);
            change_permission_level(rpl);
            esp = wd;
            SS_mask = SS_mask_from_flags(xe);
            Pe(0, rpl);
            Pe(3, rpl);
            Pe(4, rpl);
            Pe(5, rpl);
            esp = (esp + imm16) & -1;
        }
        regs[4] = (regs[4] & ~SS_mask) | ((esp) & SS_mask);
        eip = stack_eip, physmem8_ptr = initial_mem_ptr = 0;
        if (is_iret) {
            ef = 0x00000100 | 0x00040000 | 0x00200000 | 0x00010000 | 0x00004000;
            if (cpl_var == 0)
                ef |= 0x00003000;
            iopl = (cpu.eflags >> 12) & 3;
            if (cpl_var <= iopl)
                ef |= 0x00000200;
            if (is_32_bit == 0)
                ef &= 0xffff;
            set_FLAGS(stack_eflags, ef);
        }
    }
    function op_IRET(is_32_bit) {
        var iopl;
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000)) {
            if (cpu.eflags & 0x00020000) {
                iopl = (cpu.eflags >> 12) & 3;
                if (iopl != 3)
                    abort(13);
            }
            do_return_not_protected_mode(is_32_bit, 1, 0);
        } else {
            if (cpu.eflags & 0x00004000) {
                throw "unsupported task gate";
            } else {
                do_return_protected_mode(is_32_bit, 1, 0);
            }
        }
    }
    function op_RETF(is_32_bit, imm16) {
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000)) {
            do_return_not_protected_mode(is_32_bit, 0, imm16);
        } else {
            do_return_protected_mode(is_32_bit, 0, imm16);
        }
    }

    //utility function for op_LAR_LSL
    function of(selector, is_lsl) {
        var e, descriptor_low4bytes, descriptor_high4bytes, rpl, dpl, cpl_var, descriptor_type;
        if ((selector & 0xfffc) == 0)
            return null;
        e = load_from_descriptor_table(selector);
        if (!e)
            return null;
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        rpl = selector & 3;
        dpl = (descriptor_high4bytes >> 13) & 3;
        cpl_var = cpu.cpl;
        if (descriptor_high4bytes & (1 << 12)) {
            if ((descriptor_high4bytes & (1 << 11)) && (descriptor_high4bytes & (1 << 10))) {
            } else {
                if (dpl < cpl_var || dpl < rpl)
                    return null;
            }
        } else {
            descriptor_type = (descriptor_high4bytes >> 8) & 0xf;
						//Valid descriptors fall through this switch. Invalid descriptors return null
            switch (descriptor_type) {
                case 1:
                case 2:
                case 3:
                case 9:
                case 11:
                    break;
                case 4:
                case 5:
                case 12:
                    if (is_lsl)
                        return null;
                    break;
                default:
                    return null;
            }
            if (dpl < cpl_var || dpl < rpl)
                return null;
        }
        if (is_lsl) {
            return calculate_descriptor_limit(descriptor_low4bytes, descriptor_high4bytes);
        } else {
            return descriptor_high4bytes & 0x00f0ff00;
        }
    }
    function op_LAR_LSL(is_32_bit, is_lsl) {
        var x, mem8, reg_idx1, selector;
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000))
            abort(6);
        mem8 = phys_mem8[physmem8_ptr++];
        reg_idx1 = (mem8 >> 3) & 7;
        if ((mem8 >> 6) == 3) {
            selector = regs[mem8 & 7] & 0xffff;
        } else {
            mem8_loc = segment_translation(mem8);
            selector = ld_16bits_mem8_read();
        }
        x = of(selector, is_lsl);
        _src = get_conditional_flags();
        if (x === null) {
            _src &= ~0x0040;
        } else {
            _src |= 0x0040;
            if (is_32_bit)
                regs[reg_idx1] = x;
            else
                set_lower_word_in_register(reg_idx1, x);
        }
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }

    //utility function for op_VERR_VERW
    function segment_isnt_accessible(selector, is_verw) {
        var e, descriptor_low4bytes, descriptor_high4bytes, rpl, dpl, cpl_var;
        if ((selector & 0xfffc) == 0)
            return 0;
        e = load_from_descriptor_table(selector);
        if (!e)
            return 0;
        descriptor_low4bytes = e[0];
        descriptor_high4bytes = e[1];
        if (!(descriptor_high4bytes & (1 << 12)))   // s bit (system == 0)
            return 0;
        rpl = selector & 3;
        dpl = (descriptor_high4bytes >> 13) & 3;
        cpl_var = cpu.cpl;
        if (descriptor_high4bytes & (1 << 11)) {   // code == 1, data == 0
            if (is_verw) {                         // code segments are never writable
                return 0;
            } else {
                if (!(descriptor_high4bytes & (1 << 9)))
                    return 1;
                if (!(descriptor_high4bytes & (1 << 10))) {
                    if (dpl < cpl_var || dpl < rpl)
                        return 0;
                }
            }
        } else {
            if (dpl < cpl_var || dpl < rpl)        // data segments are always readable, if privilege is sufficient
                return 0;
            if (is_verw && !(descriptor_high4bytes & (1 << 9)))  //writable data segment
                return 0;
        }
        return 1;
    }
    function op_VERR_VERW(selector, is_verw) {
        var z;
        z = segment_isnt_accessible(selector, is_verw);
        _src = get_conditional_flags();

        // clear eflags.zf if selector is accessible and (readable (for VERR) or writable (for VERW))
        if (z)
            _src |= 0x0040;
        else
            _src &= ~0x0040;
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function op_ARPL() {
        var mem8, x, y, reg_idx0;
        if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000))
            abort(6);
        mem8 = phys_mem8[physmem8_ptr++];
        if ((mem8 >> 6) == 3) {
            reg_idx0 = mem8 & 7;
            x = regs[reg_idx0] & 0xffff;
        } else {
            mem8_loc = segment_translation(mem8);
            x = ld_16bits_mem8_write();
        }
        y = regs[(mem8 >> 3) & 7];
        _src = get_conditional_flags();
        if ((x & 3) < (y & 3)) {
            x = (x & ~3) | (y & 3);
            if ((mem8 >> 6) == 3) {
                set_lower_word_in_register(reg_idx0, x);
            } else {
                st16_mem8_write(x);
            }
            _src |= 0x0040;
        } else {
            _src &= ~0x0040;
        }
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function op_CPUID() {
        var eax;
        eax = regs[0];
        switch (eax) {
            case 0:  // eax == 0: vendor ID
                regs[0] = 1;
                regs[3] = 0x756e6547 & -1;
                regs[2] = 0x49656e69 & -1;
                regs[1] = 0x6c65746e & -1;
                break;
            case 1:  // eax == 1: processor info and feature flags
            default:
                regs[0] = (5 << 8) | (4 << 4) | 3; // family | model | stepping
                regs[3] = 8 << 8;                  // danluu: This is a mystery to me. This bit now indicates clflush line size, but must have meant something else in the past.
                regs[1] = 0;
                regs[2] = (1 << 4);                // rdtsc support
                break;
        }
    }
    function op_AAM(base) {
        var wf, xf;
        if (base == 0)
            abort(0);
        wf = regs[0] & 0xff;
        xf = (wf / base) & -1;
        wf = (wf % base);
        regs[0] = (regs[0] & ~0xffff) | wf | (xf << 8);
        _dst = (((wf) << 24) >> 24);
        _op = 12;
    }
    function op_AAD(base) {
        var wf, xf;
        wf = regs[0] & 0xff;
        xf = (regs[0] >> 8) & 0xff;
        wf = (xf * base + wf) & 0xff;
        regs[0] = (regs[0] & ~0xffff) | wf;
        _dst = (((wf) << 24) >> 24);
        _op = 12;
    }
    function op_AAA() {
        var Af, wf, xf, Bf, flag_bits;
        flag_bits = get_conditional_flags();
        Bf = flag_bits & 0x0010;
        wf = regs[0] & 0xff;
        xf = (regs[0] >> 8) & 0xff;
        Af = (wf > 0xf9);
        if (((wf & 0x0f) > 9) || Bf) {
            wf = (wf + 6) & 0x0f;
            xf = (xf + 1 + Af) & 0xff;
            flag_bits |= 0x0001 | 0x0010;
        } else {
            flag_bits &= ~(0x0001 | 0x0010);
            wf &= 0x0f;
        }
        regs[0] = (regs[0] & ~0xffff) | wf | (xf << 8);
        _src = flag_bits;
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function op_AAS() {
        var Af, wf, xf, Bf, flag_bits;
        flag_bits = get_conditional_flags();
        Bf = flag_bits & 0x0010;
        wf = regs[0] & 0xff;
        xf = (regs[0] >> 8) & 0xff;
        Af = (wf < 6);
        if (((wf & 0x0f) > 9) || Bf) {
            wf = (wf - 6) & 0x0f;
            xf = (xf - 1 - Af) & 0xff;
            flag_bits |= 0x0001 | 0x0010;
        } else {
            flag_bits &= ~(0x0001 | 0x0010);
            wf &= 0x0f;
        }
        regs[0] = (regs[0] & ~0xffff) | wf | (xf << 8);
        _src = flag_bits;
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function op_DAA() {
        var wf, Bf, Ef, flag_bits;
        flag_bits = get_conditional_flags();
        Ef = flag_bits & 0x0001;
        Bf = flag_bits & 0x0010;
        wf = regs[0] & 0xff;
        flag_bits = 0;
        if (((wf & 0x0f) > 9) || Bf) {
            wf = (wf + 6) & 0xff;
            flag_bits |= 0x0010;
        }
        if ((wf > 0x9f) || Ef) {
            wf = (wf + 0x60) & 0xff;
            flag_bits |= 0x0001;
        }
        regs[0] = (regs[0] & ~0xff) | wf;
        flag_bits |= (wf == 0) << 6;
        flag_bits |= parity_LUT[wf] << 2;
        flag_bits |= (wf & 0x80);
        _src = flag_bits;
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function op_DAS() {
        var wf, Gf, Bf, Ef, flag_bits;
        flag_bits = get_conditional_flags();
        Ef = flag_bits & 0x0001;
        Bf = flag_bits & 0x0010;
        wf = regs[0] & 0xff;
        flag_bits = 0;
        Gf = wf;
        if (((wf & 0x0f) > 9) || Bf) {
            flag_bits |= 0x0010;
            if (wf < 6 || Ef)
                flag_bits |= 0x0001;
            wf = (wf - 6) & 0xff;
        }
        if ((Gf > 0x99) || Ef) {
            wf = (wf - 0x60) & 0xff;
            flag_bits |= 0x0001;
        }
        regs[0] = (regs[0] & ~0xff) | wf;
        flag_bits |= (wf == 0) << 6;
        flag_bits |= parity_LUT[wf] << 2;
        flag_bits |= (wf & 0x80);
        _src = flag_bits;
        _dst = ((_src >> 6) & 1) ^ 1;
        _op = 24;
    }
    function checkOp_BOUND() {
        var mem8, x, y, z;
        mem8 = phys_mem8[physmem8_ptr++];
        if ((mem8 >> 3) == 3)
            abort(6);
        mem8_loc = segment_translation(mem8);
        x = ld_32bits_mem8_read();
        mem8_loc = (mem8_loc + 4) & -1;
        y = ld_32bits_mem8_read();
        reg_idx1 = (mem8 >> 3) & 7;
        z = regs[reg_idx1];
        if (z < x || z > y)
            abort(5);
    }
    function op_16_BOUND() {
        var mem8, x, y, z;
        mem8 = phys_mem8[physmem8_ptr++];
        if ((mem8 >> 3) == 3)
            abort(6);
        mem8_loc = segment_translation(mem8);
        x = (ld_16bits_mem8_read() << 16) >> 16;
        mem8_loc = (mem8_loc + 2) & -1;
        y = (ld_16bits_mem8_read() << 16) >> 16;
        reg_idx1 = (mem8 >> 3) & 7;
        z = (regs[reg_idx1] << 16) >> 16;
        if (z < x || z > y)
            abort(5);
    }
    function op_16_PUSHA() {
        var x, y, reg_idx1;
        y = (regs[4] - 16) >> 0;
        mem8_loc = ((y & SS_mask) + SS_base) >> 0;
        for (reg_idx1 = 7; reg_idx1 >= 0; reg_idx1--) {
            x = regs[reg_idx1];
            st16_mem8_write(x);
            mem8_loc = (mem8_loc + 2) >> 0;
        }
        regs[4] = (regs[4] & ~SS_mask) | ((y) & SS_mask);
    }
    function op_PUSHA() {
        var x, y, reg_idx1;
        y = (regs[4] - 32) >> 0;
        mem8_loc = ((y & SS_mask) + SS_base) >> 0;
        for (reg_idx1 = 7; reg_idx1 >= 0; reg_idx1--) {
            x = regs[reg_idx1];
            st32_mem8_write(x);
            mem8_loc = (mem8_loc + 4) >> 0;
        }
        regs[4] = (regs[4] & ~SS_mask) | ((y) & SS_mask);
    }
    function op_16_POPA() {
        var reg_idx1;
        mem8_loc = ((regs[4] & SS_mask) + SS_base) >> 0;
        for (reg_idx1 = 7; reg_idx1 >= 0; reg_idx1--) {
            if (reg_idx1 != 4) {
                set_lower_word_in_register(reg_idx1, ld_16bits_mem8_read());
            }
            mem8_loc = (mem8_loc + 2) >> 0;
        }
        regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 16) & SS_mask);
    }
    function op_POPA() {
        var reg_idx1;
        mem8_loc = ((regs[4] & SS_mask) + SS_base) >> 0;
        for (reg_idx1 = 7; reg_idx1 >= 0; reg_idx1--) {
            if (reg_idx1 != 4) {
                regs[reg_idx1] = ld_32bits_mem8_read();
            }
            mem8_loc = (mem8_loc + 4) >> 0;
        }
        regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 32) & SS_mask);
    }
    function op_16_LEAVE() {
        var x, y;
        y = regs[5];
        mem8_loc = ((y & SS_mask) + SS_base) >> 0;
        x = ld_16bits_mem8_read();
        set_lower_word_in_register(5, x);
        regs[4] = (regs[4] & ~SS_mask) | ((y + 2) & SS_mask);
    }
    function op_LEAVE() {
        var x, y;
        y = regs[5];
        mem8_loc = ((y & SS_mask) + SS_base) >> 0;
        x = ld_32bits_mem8_read();
        regs[5] = x;
        regs[4] = (regs[4] & ~SS_mask) | ((y + 4) & SS_mask);
    }
    function op_16_ENTER() {
        var cf, Qf, le, Rf, x, Sf;
        cf = ld16_mem8_direct();
        Qf = phys_mem8[physmem8_ptr++];
        Qf &= 0x1f;
        le = regs[4];
        Rf = regs[5];
        {
            le = (le - 2) >> 0;
            mem8_loc = ((le & SS_mask) + SS_base) >> 0;
            st16_mem8_write(Rf);
        }
        Sf = le;
        if (Qf != 0) {
            while (Qf > 1) {
                Rf = (Rf - 2) >> 0;
                mem8_loc = ((Rf & SS_mask) + SS_base) >> 0;
                x = ld_16bits_mem8_read();
                {
                    le = (le - 2) >> 0;
                    mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                    st16_mem8_write(x);
                }
                Qf--;
            }
            {
                le = (le - 2) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st16_mem8_write(Sf);
            }
        }
        le = (le - cf) >> 0;
        mem8_loc = ((le & SS_mask) + SS_base) >> 0;
        ld_16bits_mem8_write();
        regs[5] = (regs[5] & ~SS_mask) | (Sf & SS_mask);
        regs[4] = le;
    }
    function op_ENTER() {
        var cf, Qf, le, Rf, x, Sf;
        cf = ld16_mem8_direct();
        Qf = phys_mem8[physmem8_ptr++];
        Qf &= 0x1f;
        le = regs[4];
        Rf = regs[5];
        {
            le = (le - 4) >> 0;
            mem8_loc = ((le & SS_mask) + SS_base) >> 0;
            st32_mem8_write(Rf);
        }
        Sf = le;
        if (Qf != 0) {
            while (Qf > 1) {
                Rf = (Rf - 4) >> 0;
                mem8_loc = ((Rf & SS_mask) + SS_base) >> 0;
                x = ld_32bits_mem8_read();
                {
                    le = (le - 4) >> 0;
                    mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                    st32_mem8_write(x);
                }
                Qf--;
            }
            {
                le = (le - 4) >> 0;
                mem8_loc = ((le & SS_mask) + SS_base) >> 0;
                st32_mem8_write(Sf);
            }
        }
        le = (le - cf) >> 0;
        mem8_loc = ((le & SS_mask) + SS_base) >> 0;
        ld_32bits_mem8_write();
        regs[5] = (regs[5] & ~SS_mask) | (Sf & SS_mask);
        regs[4] = (regs[4] & ~SS_mask) | ((le) & SS_mask);
    }
    function op_16_load_far_pointer32(Sb) {
        var x, y, mem8;
        mem8 = phys_mem8[physmem8_ptr++];
        if ((mem8 >> 3) == 3)
            abort(6);
        mem8_loc = segment_translation(mem8);
        x = ld_32bits_mem8_read();
        mem8_loc += 4;
        y = ld_16bits_mem8_read();
        set_segment_register(Sb, y);
        regs[(mem8 >> 3) & 7] = x;
    }
    function op_16_load_far_pointer16(Sb) {
        var x, y, mem8;
        mem8 = phys_mem8[physmem8_ptr++];
        if ((mem8 >> 3) == 3)
            abort(6);
        mem8_loc = segment_translation(mem8);
        x = ld_16bits_mem8_read();
        mem8_loc += 2;
        y = ld_16bits_mem8_read();
        set_segment_register(Sb, y);
        set_lower_word_in_register((mem8 >> 3) & 7, x);
    }
    function stringOp_INSB() {
        var Xf, Yf, Zf, ag, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = cpu.ld8_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st8_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = cpu.ld8_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st8_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_OUTSB() {
        var Xf, cg, Sb, ag, Zf, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_8bits_mem8_read();
            cpu.st8_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_8bits_mem8_read();
            cpu.st8_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_MOVSB() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            {
                x = ld_8bits_mem8_read();
                mem8_loc = eg;
                st8_mem8_write(x);
                regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            x = ld_8bits_mem8_read();
            mem8_loc = eg;
            st8_mem8_write(x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_STOSB() {
        var Xf, Yf, ag;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            {
                st8_mem8_write(regs[0]);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            st8_mem8_write(regs[0]);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_CMPSB() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_8bits_mem8_read();
            mem8_loc = eg;
            y = ld_8bits_mem8_read();
            do_8bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_8bits_mem8_read();
            mem8_loc = eg;
            y = ld_8bits_mem8_read();
            do_8bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_LODSB() {
        var Xf, cg, Sb, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_8bits_mem8_read();
            regs[0] = (regs[0] & -256) | x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_8bits_mem8_read();
            regs[0] = (regs[0] & -256) | x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 0)) & Xf);
        }
    }
    function stringOp_SCASB() {
        var Xf, Yf, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_8bits_mem8_read();
            do_8bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_8bits_mem8_read();
            do_8bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 0)) & Xf);
        }
    }
    function op_16_INS() {
        var Xf, Yf, Zf, ag, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = cpu.ld16_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st16_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = cpu.ld16_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st16_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_OUTS() {
        var Xf, cg, Sb, ag, Zf, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_16bits_mem8_read();
            cpu.st16_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_16bits_mem8_read();
            cpu.st16_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_MOVS() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            {
                x = ld_16bits_mem8_read();
                mem8_loc = eg;
                st16_mem8_write(x);
                regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            x = ld_16bits_mem8_read();
            mem8_loc = eg;
            st16_mem8_write(x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_STOS() {
        var Xf, Yf, ag;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            {
                st16_mem8_write(regs[0]);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            st16_mem8_write(regs[0]);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_CMPS() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_16bits_mem8_read();
            mem8_loc = eg;
            y = ld_16bits_mem8_read();
            do_16bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_16bits_mem8_read();
            mem8_loc = eg;
            y = ld_16bits_mem8_read();
            do_16bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_LODS() {
        var Xf, cg, Sb, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_16bits_mem8_read();
            regs[0] = (regs[0] & -65536) | x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_16bits_mem8_read();
            regs[0] = (regs[0] & -65536) | x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 1)) & Xf);
        }
    }
    function op_16_SCAS() {
        var Xf, Yf, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_16bits_mem8_read();
            do_16bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_16bits_mem8_read();
            do_16bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 1)) & Xf);
        }
    }
    function stringOp_INSD() {
        var Xf, Yf, Zf, ag, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = cpu.ld32_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st32_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = cpu.ld32_port(Zf);
            mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
            st32_mem8_write(x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_OUTSD() {
        var Xf, cg, Sb, ag, Zf, iopl, x;
        iopl = (cpu.eflags >> 12) & 3;
        if (cpu.cpl > iopl)
            abort(13);
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Zf = regs[2] & 0xffff;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_32bits_mem8_read();
            cpu.st32_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
            x = ld_32bits_mem8_read();
            cpu.st32_port(Zf, x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_MOVSD() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            if (Xf == -1 && cpu.df == 1 && ((mem8_loc | eg) & 3) == 0) {
                var len, l, ug, vg, i, wg;
                len = ag >>> 0;
                l = (4096 - (mem8_loc & 0xfff)) >> 2;
                if (len > l)
                    len = l;
                l = (4096 - (eg & 0xfff)) >> 2;
                if (len > l)
                    len = l;
                ug = do_tlb_lookup(mem8_loc, 0);
                vg = do_tlb_lookup(eg, 1);
                wg = len << 2;
                vg >>= 2;
                ug >>= 2;
                for (i = 0; i < len; i++)
                    phys_mem32[vg + i] = phys_mem32[ug + i];
                regs[6] = (cg + wg) >> 0;
                regs[7] = (Yf + wg) >> 0;
                regs[1] = ag = (ag - len) >> 0;
                if (ag)
                    physmem8_ptr = initial_mem_ptr;
            } else {
                x = ld_32bits_mem8_read();
                mem8_loc = eg;
                st32_mem8_write(x);
                regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            x = ld_32bits_mem8_read();
            mem8_loc = eg;
            st32_mem8_write(x);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_STOSD() {
        var Xf, Yf, ag;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            if (Xf == -1 && cpu.df == 1 && (mem8_loc & 3) == 0) {
                var len, l, vg, i, wg, x;
                len = ag >>> 0;
                l = (4096 - (mem8_loc & 0xfff)) >> 2;
                if (len > l)
                    len = l;
                vg = do_tlb_lookup(regs[7], 1);
                x = regs[0];
                vg >>= 2;
                for (i = 0; i < len; i++)
                    phys_mem32[vg + i] = x;
                wg = len << 2;
                regs[7] = (Yf + wg) >> 0;
                regs[1] = ag = (ag - len) >> 0;
                if (ag)
                    physmem8_ptr = initial_mem_ptr;
            } else {
                st32_mem8_write(regs[0]);
                regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
                regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
                if (ag & Xf)
                    physmem8_ptr = initial_mem_ptr;
            }
        } else {
            st32_mem8_write(regs[0]);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_CMPSD() {
        var Xf, Yf, cg, ag, Sb, eg;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        Yf = regs[7];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        eg = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_32bits_mem8_read();
            mem8_loc = eg;
            y = ld_32bits_mem8_read();
            do_32bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_32bits_mem8_read();
            mem8_loc = eg;
            y = ld_32bits_mem8_read();
            do_32bit_math(7, x, y);
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_LODSD() {
        var Xf, cg, Sb, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Sb = CS_flags & 0x000f;
        if (Sb == 0)
            Sb = 3;
        else
            Sb--;
        cg = regs[6];
        mem8_loc = ((cg & Xf) + cpu.segs[Sb].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_32bits_mem8_read();
            regs[0] = x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_32bits_mem8_read();
            regs[0] = x;
            regs[6] = (cg & ~Xf) | ((cg + (cpu.df << 2)) & Xf);
        }
    }
    function stringOp_SCASD() {
        var Xf, Yf, ag, x;
        if (CS_flags & 0x0080)
            Xf = 0xffff;
        else
            Xf = -1;
        Yf = regs[7];
        mem8_loc = ((Yf & Xf) + cpu.segs[0].base) >> 0;
        if (CS_flags & (0x0010 | 0x0020)) {
            ag = regs[1];
            if ((ag & Xf) == 0)
                return;
            x = ld_32bits_mem8_read();
            do_32bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
            regs[1] = ag = (ag & ~Xf) | ((ag - 1) & Xf);
            if (CS_flags & 0x0010) {
                if (!(_dst == 0))
                    return;
            } else {
                if ((_dst == 0))
                    return;
            }
            if (ag & Xf)
                physmem8_ptr = initial_mem_ptr;
        } else {
            x = ld_32bits_mem8_read();
            do_32bit_math(7, regs[0], x);
            regs[7] = (Yf & ~Xf) | ((Yf + (cpu.df << 2)) & Xf);
        }
    }

    cpu = this;
    phys_mem8        = this.phys_mem8;
    phys_mem16       = this.phys_mem16;
    phys_mem32       = this.phys_mem32;
    tlb_read_user    = this.tlb_read_user;
    tlb_write_user   = this.tlb_write_user;
    tlb_read_kernel  = this.tlb_read_kernel;
    tlb_write_kernel = this.tlb_write_kernel;

    if (cpu.cpl == 3) {  //current privilege level
        _tlb_read_ = tlb_read_user;
        _tlb_write_ = tlb_write_user;
    } else {
        _tlb_read_ = tlb_read_kernel;
        _tlb_write_ = tlb_write_kernel;
    }

    if (cpu.halted) {
        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200)) {
            cpu.halted = 0;
        } else {
            return 257;
        }
    }

    regs = this.regs;
    _src = this.cc_src;
    _dst = this.cc_dst;
    _op = this.cc_op;
    _op2 = this.cc_op2;
    _dst2 = this.cc_dst2;

    eip = this.eip;
    init_segment_local_vars();
    exit_code = 256;
    cycles_left = N_cycles;

    if (interrupt) {
        do_interrupt(interrupt.intno, 0, interrupt.error_code, 0, 0);
    }
    if (cpu.hard_intno >= 0) {
        do_interrupt(cpu.hard_intno, 0, 0, 0, 1);
        cpu.hard_intno = -1;
    }
    if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200)) {
        cpu.hard_intno = cpu.get_hard_intno();
        do_interrupt(cpu.hard_intno, 0, 0, 0, 1);
        cpu.hard_intno = -1;
    }

    physmem8_ptr = 0;
    initial_mem_ptr = 0;

    OUTER_LOOP: do {
        /*
           All the below is solely to determine what the next instruction is before re-entering the main EXEC_LOOP
           ------------------------------------------------------------------------------------------------------
         */
        eip = (eip + physmem8_ptr - initial_mem_ptr) >> 0;
        eip_offset = (eip + CS_base) >> 0;
        eip_tlb_val = _tlb_read_[eip_offset >>> 12];
        if (((eip_tlb_val | eip_offset) & 0xfff) >= (4096 - 15 + 1)) { //what does this condition mean? operation straddling page boundary?
            var Cg;
            if (eip_tlb_val == -1)
                do_tlb_set_page(eip_offset, 0, cpu.cpl == 3);
            eip_tlb_val = _tlb_read_[eip_offset >>> 12];
            initial_mem_ptr = physmem8_ptr = eip_offset ^ eip_tlb_val;
            OPbyte = phys_mem8[physmem8_ptr++];
            Cg = eip_offset & 0xfff;
            if (Cg >= (4096 - 15 + 1)) { //again, WTF does this do?
                x = operation_size_function(eip_offset, OPbyte);
                if ((Cg + x) > 4096) {
                    initial_mem_ptr = physmem8_ptr = this.mem_size;
                    for (y = 0; y < x; y++) {
                        mem8_loc = (eip_offset + y) >> 0;
                        phys_mem8[physmem8_ptr + y] = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                    }
                    physmem8_ptr++;
                }
            }
        } else {
            initial_mem_ptr = physmem8_ptr = eip_offset ^ eip_tlb_val;
            OPbyte = phys_mem8[physmem8_ptr++];
        }

        OPbyte |= (CS_flags = init_CS_flags) & 0x0100; //Are we running in 16bit compatibility mode? if so, ops look like 0x1XX instead of 0xXX

        EXEC_LOOP: for (; ; ) {
            switch (OPbyte) {
                case 0x66://   Operand-size override prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    if (init_CS_flags & 0x0100)
                        CS_flags &= ~0x0100;
                    else
                        CS_flags |= 0x0100;
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0x67://   Address-size override prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    if (init_CS_flags & 0x0080)
                        CS_flags &= ~0x0080;
                    else
                        CS_flags |= 0x0080;
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0xf0://LOCK   Assert LOCK# Signal Prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    CS_flags |= 0x0040;
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0xf2://REPNZ  eCX Repeat String Operation Prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    CS_flags |= 0x0020;
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0xf3://REPZ  eCX Repeat String Operation Prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    CS_flags |= 0x0010;
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0x26://ES ES  ES segment override prefix
                case 0x2e://CS CS  CS segment override prefix
                case 0x36://SS SS  SS segment override prefix
                case 0x3e://DS DS  DS segment override prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    CS_flags = (CS_flags & ~0x000f) | (((OPbyte >> 3) & 3) + 1);
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0x64://FS FS  FS segment override prefix
                case 0x65://GS GS  GS segment override prefix
                    if (CS_flags == init_CS_flags)
                        operation_size_function(eip_offset, OPbyte);
                    CS_flags = (CS_flags & ~0x000f) | ((OPbyte & 7) + 1);
                    OPbyte = phys_mem8[physmem8_ptr++];
                    OPbyte |= (CS_flags & 0x0100);
                    break;
                case 0xb0://MOV Ib Zb Move
                case 0xb1:
                case 0xb2:
                case 0xb3:
                case 0xb4:
                case 0xb5:
                case 0xb6:
                case 0xb7:
                    x = phys_mem8[physmem8_ptr++]; //r8
                    OPbyte &= 7; //last bits
                    last_tlb_val = (OPbyte & 4) << 1;
                    regs[OPbyte & 3] = (regs[OPbyte & 3] & ~(0xff << last_tlb_val)) | (((x) & 0xff) << last_tlb_val);
                    break EXEC_LOOP;
                case 0xb8://MOV Ivqp Zvqp Move
                case 0xb9:
                case 0xba:
                case 0xbb:
                case 0xbc:
                case 0xbd:
                case 0xbe:
                case 0xbf:
                    {
                        x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    regs[OPbyte & 7] = x;
                    break EXEC_LOOP;
                case 0x88://MOV Gb Eb Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    x = (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1));
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        last_tlb_val = (reg_idx0 & 4) << 1;
                        regs[reg_idx0 & 3] = (regs[reg_idx0 & 3] & ~(0xff << last_tlb_val)) | (((x) & 0xff) << last_tlb_val);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        {
                            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
                            if (last_tlb_val == -1) {
                                __st8_mem8_write(x);
                            } else {
                                phys_mem8[mem8_loc ^ last_tlb_val] = x;
                            }
                        }
                    }
                    break EXEC_LOOP;
                case 0x89://MOV Gvqp Evqp Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    x = regs[(mem8 >> 3) & 7];
                    if ((mem8 >> 6) == 3) {
                        regs[mem8 & 7] = x;
                    } else {
                        mem8_loc = segment_translation(mem8);
                        {
                            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
                            if ((last_tlb_val | mem8_loc) & 3) {
                                __st32_mem8_write(x);
                            } else {
                                phys_mem32[(mem8_loc ^ last_tlb_val) >> 2] = x;
                            }
                        }
                    }
                    break EXEC_LOOP;
                case 0x8a://MOV Eb Gb Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                    }
                    reg_idx1 = (mem8 >> 3) & 7;
                    last_tlb_val = (reg_idx1 & 4) << 1;
                    regs[reg_idx1 & 3] = (regs[reg_idx1 & 3] & ~(0xff << last_tlb_val)) | (((x) & 0xff) << last_tlb_val);
                    break EXEC_LOOP;
                case 0x8b://MOV Evqp Gvqp Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        x = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) | mem8_loc) & 3 ? __ld_32bits_mem8_read() : phys_mem32[(mem8_loc ^ last_tlb_val) >> 2]);
                    }
                    regs[(mem8 >> 3) & 7] = x;
                    break EXEC_LOOP;
                case 0xa0://MOV Ob AL Move byte at (seg:offset) to AL
                    mem8_loc = segmented_mem8_loc_for_MOV();
                    x = ld_8bits_mem8_read();
                    regs[0] = (regs[0] & -256) | x;
                    break EXEC_LOOP;
                case 0xa1://MOV Ovqp rAX Move dword at (seg:offset) to EAX
                    mem8_loc = segmented_mem8_loc_for_MOV();
                    x = ld_32bits_mem8_read();
                    regs[0] = x;
                    break EXEC_LOOP;
                case 0xa2://MOV AL Ob Move AL to (seg:offset)
                    mem8_loc = segmented_mem8_loc_for_MOV();
                    st8_mem8_write(regs[0]);
                    break EXEC_LOOP;
                case 0xa3://MOV rAX Ovqp Move EAX to (seg:offset)
                    mem8_loc = segmented_mem8_loc_for_MOV();
                    st32_mem8_write(regs[0]);
                    break EXEC_LOOP;
                case 0xd7://XLAT (DS:)[rBX+AL] AL Table Look-up Translation
                    mem8_loc = (regs[3] + (regs[0] & 0xff)) >> 0;
                    if (CS_flags & 0x0080)
                        mem8_loc &= 0xffff;
                    reg_idx1 = CS_flags & 0x000f;
                    if (reg_idx1 == 0)
                        reg_idx1 = 3;
                    else
                        reg_idx1--;
                    mem8_loc = (mem8_loc + cpu.segs[reg_idx1].base) >> 0;
                    x = ld_8bits_mem8_read();
                    set_word_in_register(0, x);
                    break EXEC_LOOP;
                case 0xc6://MOV Ib Eb Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        x = phys_mem8[physmem8_ptr++];
                        set_word_in_register(mem8 & 7, x);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = phys_mem8[physmem8_ptr++];
                        st8_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xc7://MOV Ivds Evqp Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        {
                            x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                        regs[mem8 & 7] = x;
                    } else {
                        mem8_loc = segment_translation(mem8);
                        {
                            x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0x91://(90+r)  XCHG  r16/32  eAX     Exchange Register/Memory with Register
                case 0x92:
                case 0x93:
                case 0x94:
                case 0x95:
                case 0x96:
                case 0x97:
                    reg_idx1 = OPbyte & 7;
                    x = regs[0];
                    regs[0] = regs[reg_idx1];
                    regs[reg_idx1] = x;
                    break EXEC_LOOP;
                case 0x86://XCHG  Gb Exchange Register/Memory with Register
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                        set_word_in_register(reg_idx0, (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_8bits_mem8_write();
                        st8_mem8_write((regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                    }
                    set_word_in_register(reg_idx1, x);
                    break EXEC_LOOP;
                case 0x87://XCHG  Gvqp Exchange Register/Memory with Register
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        x = regs[reg_idx0];
                        regs[reg_idx0] = regs[reg_idx1];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_write();
                        st32_mem8_write(regs[reg_idx1]);
                    }
                    regs[reg_idx1] = x;
                    break EXEC_LOOP;
                case 0x8e://MOV Ew Sw Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if (reg_idx1 >= 6 || reg_idx1 == 1)
                        abort(6);
                    if ((mem8 >> 6) == 3) {
                        x = regs[mem8 & 7] & 0xffff;
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_16bits_mem8_read();
                    }
                    set_segment_register(reg_idx1, x);
                    break EXEC_LOOP;
                case 0x8c://MOV Sw Mw Move
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if (reg_idx1 >= 6)
                        abort(6);
                    x = cpu.segs[reg_idx1].selector;
                    if ((mem8 >> 6) == 3) {
                        if ((((CS_flags >> 8) & 1) ^ 1)) {
                            regs[mem8 & 7] = x;
                        } else {
                            set_lower_word_in_register(mem8 & 7, x);
                        }
                    } else {
                        mem8_loc = segment_translation(mem8);
                        st16_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xc4://LES Mp ES Load Far Pointer
                    op_16_load_far_pointer32(0);
                    break EXEC_LOOP;
                case 0xc5://LDS Mp DS Load Far Pointer
                    op_16_load_far_pointer32(3);
                    break EXEC_LOOP;
                case 0x00://ADD Gb Eb Add
                case 0x08://OR Gb Eb Logical Inclusive OR
                case 0x10://ADC Gb Eb Add with Carry
                case 0x18://SBB Gb Eb Integer Subtraction with Borrow
                case 0x20://AND Gb Eb Logical AND
                case 0x28://SUB Gb Eb Subtract
                case 0x30://XOR Gb Eb Logical Exclusive OR
                case 0x38://CMP Eb  Compare Two Operands
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    reg_idx1 = (mem8 >> 3) & 7;
                    y = (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1));
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        set_word_in_register(reg_idx0, do_8bit_math(conditional_var, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)), y));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        if (conditional_var != 7) {
                            x = ld_8bits_mem8_write();
                            x = do_8bit_math(conditional_var, x, y);
                            st8_mem8_write(x);
                        } else {
                            x = ld_8bits_mem8_read();
                            do_8bit_math(7, x, y);
                        }
                    }
                    break EXEC_LOOP;
                case 0x01://ADD Gvqp Evqp Add
                    mem8 = phys_mem8[physmem8_ptr++];
                    y = regs[(mem8 >> 3) & 7];
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        {
                            _src = y;
                            _dst = regs[reg_idx0] = (regs[reg_idx0] + _src) >> 0;
                            _op = 2;
                        }
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_write();
                        {
                            _src = y;
                            _dst = x = (x + _src) >> 0;
                            _op = 2;
                        }
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0x09://OR Gvqp Evqp Logical Inclusive OR
                case 0x11://ADC Gvqp Evqp Add with Carry
                case 0x19://SBB Gvqp Evqp Integer Subtraction with Borrow
                case 0x21://AND Gvqp Evqp Logical AND
                case 0x29://SUB Gvqp Evqp Subtract
                case 0x31://XOR Gvqp Evqp Logical Exclusive OR
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    y = regs[(mem8 >> 3) & 7];
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        regs[reg_idx0] = do_32bit_math(conditional_var, regs[reg_idx0], y);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_write();
                        x = do_32bit_math(conditional_var, x, y);
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0x39://CMP Evqp  Compare Two Operands
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    y = regs[(mem8 >> 3) & 7];
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        {
                            _src = y;
                            _dst = (regs[reg_idx0] - _src) >> 0;
                            _op = 8;
                        }
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_read();
                        {
                            _src = y;
                            _dst = (x - _src) >> 0;
                            _op = 8;
                        }
                    }
                    break EXEC_LOOP;
                case 0x02://ADD Eb Gb Add
                case 0x0a://OR Eb Gb Logical Inclusive OR
                case 0x12://ADC Eb Gb Add with Carry
                case 0x1a://SBB Eb Gb Integer Subtraction with Borrow
                case 0x22://AND Eb Gb Logical AND
                case 0x2a://SUB Eb Gb Subtract
                case 0x32://XOR Eb Gb Logical Exclusive OR
                case 0x3a://CMP Gb  Compare Two Operands
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        y = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_8bits_mem8_read();
                    }
                    set_word_in_register(reg_idx1, do_8bit_math(conditional_var, (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)), y));
                    break EXEC_LOOP;
                case 0x03://ADD Evqp Gvqp Add
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_32bits_mem8_read();
                    }
                    {
                        _src = y;
                        _dst = regs[reg_idx1] = (regs[reg_idx1] + _src) >> 0;
                        _op = 2;
                    }
                    break EXEC_LOOP;
                case 0x0b://OR Evqp Gvqp Logical Inclusive OR
                case 0x13://ADC Evqp Gvqp Add with Carry
                case 0x1b://SBB Evqp Gvqp Integer Subtraction with Borrow
                case 0x23://AND Evqp Gvqp Logical AND
                case 0x2b://SUB Evqp Gvqp Subtract
                case 0x33://XOR Evqp Gvqp Logical Exclusive OR
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_32bits_mem8_read();
                    }
                    regs[reg_idx1] = do_32bit_math(conditional_var, regs[reg_idx1], y);
                    break EXEC_LOOP;
                case 0x3b://CMP Gvqp  Compare Two Operands
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_32bits_mem8_read();
                    }
                    {
                        _src = y;
                        _dst = (regs[reg_idx1] - _src) >> 0;
                        _op = 8;
                    }
                    break EXEC_LOOP;
                case 0x04://ADD Ib AL Add
                case 0x0c://OR Ib AL Logical Inclusive OR
                case 0x14://ADC Ib AL Add with Carry
                case 0x1c://SBB Ib AL Integer Subtraction with Borrow
                case 0x24://AND Ib AL Logical AND
                case 0x2c://SUB Ib AL Subtract
                case 0x34://XOR Ib AL Logical Exclusive OR
                case 0x3c://CMP AL  Compare Two Operands
                    y = phys_mem8[physmem8_ptr++];
                    conditional_var = OPbyte >> 3;
                    set_word_in_register(0, do_8bit_math(conditional_var, regs[0] & 0xff, y));
                    break EXEC_LOOP;
                case 0x05://ADD Ivds rAX Add
                    {
                        y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    {
                        _src = y;
                        _dst = regs[0] = (regs[0] + _src) >> 0;
                        _op = 2;
                    }
                    break EXEC_LOOP;
                case 0x0d://OR Ivds rAX Logical Inclusive OR
                case 0x15://ADC Ivds rAX Add with Carry
                case 0x1d://SBB Ivds rAX Integer Subtraction with Borrow
                case 0x25://AND Ivds rAX Logical AND
                case 0x2d://SUB Ivds rAX Subtract
                    {
                        y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    conditional_var = OPbyte >> 3;
                    regs[0] = do_32bit_math(conditional_var, regs[0], y);
                    break EXEC_LOOP;
                case 0x35://XOR Ivds rAX Logical Exclusive OR
                    {
                        y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    {
                        _dst = regs[0] = regs[0] ^ y;
                        _op = 14;
                    }
                    break EXEC_LOOP;
                case 0x3d://CMP rAX  Compare Two Operands
                    {
                        y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    {
                        _src = y;
                        _dst = (regs[0] - _src) >> 0;
                        _op = 8;
                    }
                    break EXEC_LOOP;
                case 0x80://ADD Ib Eb Add
                case 0x82://ADD Ib Eb Add
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        y = phys_mem8[physmem8_ptr++];
                        set_word_in_register(reg_idx0, do_8bit_math(conditional_var, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)), y));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = phys_mem8[physmem8_ptr++];
                        if (conditional_var != 7) {
                            x = ld_8bits_mem8_write();
                            x = do_8bit_math(conditional_var, x, y);
                            st8_mem8_write(x);
                        } else {
                            x = ld_8bits_mem8_read();
                            do_8bit_math(7, x, y);
                        }
                    }
                    break EXEC_LOOP;
                case 0x81://ADD Ivds Evqp Add
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if (conditional_var == 7) {
                        if ((mem8 >> 6) == 3) {
                            x = regs[mem8 & 7];
                        } else {
                            mem8_loc = segment_translation(mem8);
                            x = ld_32bits_mem8_read();
                        }
                        {
                            y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                        {
                            _src = y;
                            _dst = (x - _src) >> 0;
                            _op = 8;
                        }
                    } else {
                        if ((mem8 >> 6) == 3) {
                            reg_idx0 = mem8 & 7;
                            {
                                y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                                physmem8_ptr += 4;
                            }
                            regs[reg_idx0] = do_32bit_math(conditional_var, regs[reg_idx0], y);
                        } else {
                            mem8_loc = segment_translation(mem8);
                            {
                                y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                                physmem8_ptr += 4;
                            }
                            x = ld_32bits_mem8_write();
                            x = do_32bit_math(conditional_var, x, y);
                            st32_mem8_write(x);
                        }
                    }
                    break EXEC_LOOP;
                case 0x83://ADD Ibs Evqp Add
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if (conditional_var == 7) {
                        if ((mem8 >> 6) == 3) {
                            x = regs[mem8 & 7];
                        } else {
                            mem8_loc = segment_translation(mem8);
                            x = ld_32bits_mem8_read();
                        }
                        y = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        {
                            _src = y;
                            _dst = (x - _src) >> 0;
                            _op = 8;
                        }
                    } else {
                        if ((mem8 >> 6) == 3) {
                            reg_idx0 = mem8 & 7;
                            y = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            regs[reg_idx0] = do_32bit_math(conditional_var, regs[reg_idx0], y);
                        } else {
                            mem8_loc = segment_translation(mem8);
                            y = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            x = ld_32bits_mem8_write();
                            x = do_32bit_math(conditional_var, x, y);
                            st32_mem8_write(x);
                        }
                    }
                    break EXEC_LOOP;
                case 0x40://INC  Zv Increment by 1
                case 0x41://REX.B   Extension of r/m field, base field, or opcode reg field
                case 0x42://REX.X   Extension of SIB index field
                case 0x43://REX.XB   REX.X and REX.B combination
                case 0x44://REX.R   Extension of ModR/M reg field
                case 0x45://REX.RB   REX.R and REX.B combination
                case 0x46://REX.RX   REX.R and REX.X combination
                case 0x47://REX.RXB   REX.R, REX.X and REX.B combination
                    reg_idx1 = OPbyte & 7;
                    {
                        if (_op < 25) {
                            _op2 = _op;
                            _dst2 = _dst;
                        }
                        regs[reg_idx1] = _dst = (regs[reg_idx1] + 1) >> 0;
                        _op = 27;
                    }
                    break EXEC_LOOP;
                case 0x48://DEC  Zv Decrement by 1
                case 0x49://REX.WB   REX.W and REX.B combination
                case 0x4a://REX.WX   REX.W and REX.X combination
                case 0x4b://REX.WXB   REX.W, REX.X and REX.B combination
                case 0x4c://REX.WR   REX.W and REX.R combination
                case 0x4d://REX.WRB   REX.W, REX.R and REX.B combination
                case 0x4e://REX.WRX   REX.W, REX.R and REX.X combination
                case 0x4f://REX.WRXB   REX.W, REX.R, REX.X and REX.B combination
                    reg_idx1 = OPbyte & 7;
                    {
                        if (_op < 25) {
                            _op2 = _op;
                            _dst2 = _dst;
                        }
                        regs[reg_idx1] = _dst = (regs[reg_idx1] - 1) >> 0;
                        _op = 30;
                    }
                    break EXEC_LOOP;
                case 0x6b://IMUL Evqp Gvqp Signed Multiply
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_32bits_mem8_read();
                    }
                    z = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    regs[reg_idx1] = op_IMUL32(y, z);
                    break EXEC_LOOP;
                case 0x69://IMUL Evqp Gvqp Signed Multiply
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = ld_32bits_mem8_read();
                    }
                    {
                        z = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    regs[reg_idx1] = op_IMUL32(y, z);
                    break EXEC_LOOP;
                case 0x84://TEST Eb  Logical Compare
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_8bits_mem8_read();
                    }
                    reg_idx1 = (mem8 >> 3) & 7;
                    y = (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1));
                    {
                        _dst = (((x & y) << 24) >> 24);
                        _op = 12;
                    }
                    break EXEC_LOOP;
                case 0x85://TEST Evqp  Logical Compare
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        x = regs[mem8 & 7];
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_read();
                    }
                    y = regs[(mem8 >> 3) & 7];
                    {
                        _dst = x & y;
                        _op = 14;
                    }
                    break EXEC_LOOP;
                case 0xa8://TEST AL  Logical Compare
                    y = phys_mem8[physmem8_ptr++];
                    {
                        _dst = (((regs[0] & y) << 24) >> 24);
                        _op = 12;
                    }
                    break EXEC_LOOP;
                case 0xa9://TEST rAX  Logical Compare
                    {
                        y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    {
                        _dst = regs[0] & y;
                        _op = 14;
                    }
                    break EXEC_LOOP;
                case 0xf6://TEST Eb  Logical Compare
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    switch (conditional_var) {
                        case 0:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_read();
                            }
                            y = phys_mem8[physmem8_ptr++];
                            {
                                _dst = (((x & y) << 24) >> 24);
                                _op = 12;
                            }
                            break;
                        case 2:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_word_in_register(reg_idx0, ~(regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                x = ~x;
                                st8_mem8_write(x);
                            }
                            break;
                        case 3:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_word_in_register(reg_idx0, do_8bit_math(5, 0, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1))));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                x = do_8bit_math(5, 0, x);
                                st8_mem8_write(x);
                            }
                            break;
                        case 4:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_read();
                            }
                            set_lower_word_in_register(0, op_MUL(regs[0], x));
                            break;
                        case 5:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_read();
                            }
                            set_lower_word_in_register(0, op_IMUL(regs[0], x));
                            break;
                        case 6:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_read();
                            }
                            op_DIV(x);
                            break;
                        case 7:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_read();
                            }
                            op_IDIV(x);
                            break;
                        default:
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xf7://TEST Evqp  Logical Compare
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    switch (conditional_var) {
                        case 0:
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            {
                                y = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                                physmem8_ptr += 4;
                            }
                            {
                                _dst = x & y;
                                _op = 14;
                            }
                            break;
                        case 2:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = ~regs[reg_idx0];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                x = ~x;
                                st32_mem8_write(x);
                            }
                            break;
                        case 3:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = do_32bit_math(5, 0, regs[reg_idx0]);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                x = do_32bit_math(5, 0, x);
                                st32_mem8_write(x);
                            }
                            break;
                        case 4:
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            regs[0] = op_MUL32(regs[0], x);
                            regs[2] = v;
                            break;
                        case 5:
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            regs[0] = op_IMUL32(regs[0], x);
                            regs[2] = v;
                            break;
                        case 6:
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            regs[0] = op_DIV32(regs[2], regs[0], x);
                            regs[2] = v;
                            break;
                        case 7:
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            regs[0] = op_IDIV32(regs[2], regs[0], x);
                            regs[2] = v;
                            break;
                        default:
                            abort(6);
                    }
                    break EXEC_LOOP;
                //Rotate and Shift ops ---------------------------------------------------------------
                case 0xc0://ROL Ib Eb Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = phys_mem8[physmem8_ptr++];
                        reg_idx0 = mem8 & 7;
                        set_word_in_register(reg_idx0, shift8(conditional_var, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)), y));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = phys_mem8[physmem8_ptr++];
                        x = ld_8bits_mem8_write();
                        x = shift8(conditional_var, x, y);
                        st8_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xc1://ROL Ib Evqp Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        y = phys_mem8[physmem8_ptr++];
                        reg_idx0 = mem8 & 7;
                        regs[reg_idx0] = shift32(conditional_var, regs[reg_idx0], y);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        y = phys_mem8[physmem8_ptr++];
                        x = ld_32bits_mem8_write();
                        x = shift32(conditional_var, x, y);
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xd0://ROL 1 Eb Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        set_word_in_register(reg_idx0, shift8(conditional_var, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)), 1));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_8bits_mem8_write();
                        x = shift8(conditional_var, x, 1);
                        st8_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xd1://ROL 1 Evqp Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        regs[reg_idx0] = shift32(conditional_var, regs[reg_idx0], 1);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_write();
                        x = shift32(conditional_var, x, 1);
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xd2://ROL CL Eb Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    y = regs[1] & 0xff;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        set_word_in_register(reg_idx0, shift8(conditional_var, (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)), y));
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_8bits_mem8_write();
                        x = shift8(conditional_var, x, y);
                        st8_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0xd3://ROL CL Evqp Rotate
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    y = regs[1] & 0xff;
                    if ((mem8 >> 6) == 3) {
                        reg_idx0 = mem8 & 7;
                        regs[reg_idx0] = shift32(conditional_var, regs[reg_idx0], y);
                    } else {
                        mem8_loc = segment_translation(mem8);
                        x = ld_32bits_mem8_write();
                        x = shift32(conditional_var, x, y);
                        st32_mem8_write(x);
                    }
                    break EXEC_LOOP;
                case 0x98://CBW AL AX Convert Byte to Word
                    regs[0] = (regs[0] << 16) >> 16;
                    break EXEC_LOOP;
                case 0x99://CWD AX DX Convert Word to Doubleword
                    regs[2] = regs[0] >> 31;
                    break EXEC_LOOP;
                case 0x50://PUSH Zv SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x51:
                case 0x52:
                case 0x53:
                case 0x54:
                case 0x55:
                case 0x56:
                case 0x57:
                    x = regs[OPbyte & 7];
                    if (FS_usage_flag) {
                        mem8_loc = (regs[4] - 4) >> 0;
                        {
                            last_tlb_val = _tlb_write_[mem8_loc >>> 12];
                            if ((last_tlb_val | mem8_loc) & 3) {
                                __st32_mem8_write(x);
                            } else {
                                phys_mem32[(mem8_loc ^ last_tlb_val) >> 2] = x;
                            }
                        }
                        regs[4] = mem8_loc;
                    } else {
                        push_dword_to_stack(x);
                    }
                    break EXEC_LOOP;
                case 0x58://POP SS:[rSP] Zv Pop a Value from the Stack
                case 0x59:
                case 0x5a:
                case 0x5b:
                case 0x5c:
                case 0x5d:
                case 0x5e:
                case 0x5f:
                    if (FS_usage_flag) {
                        mem8_loc = regs[4];
                        x = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) | mem8_loc) & 3 ? __ld_32bits_mem8_read() : phys_mem32[(mem8_loc ^ last_tlb_val) >> 2]);
                        regs[4] = (mem8_loc + 4) >> 0;
                    } else {
                        x = pop_dword_from_stack_read();
                        pop_dword_from_stack_incr_ptr();
                    }
                    regs[OPbyte & 7] = x;
                    break EXEC_LOOP;

                case 0x60://PUSHA AX SS:[rSP] Push All General-Purpose Registers
                    op_PUSHA();
                    break EXEC_LOOP;
                case 0x61://POPA SS:[rSP] DI Pop All General-Purpose Registers
                    op_POPA();
                    break EXEC_LOOP;
                case 0x8f://POP SS:[rSP] Ev Pop a Value from the Stack
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3) {
                        x = pop_dword_from_stack_read();
                        pop_dword_from_stack_incr_ptr();
                        regs[mem8 & 7] = x;
                    } else {
                        x = pop_dword_from_stack_read();
                        y = regs[4];
                        pop_dword_from_stack_incr_ptr();
                        z = regs[4];
                        mem8_loc = segment_translation(mem8);
                        regs[4] = y;
                        st32_mem8_write(x);
                        regs[4] = z;
                    }
                    break EXEC_LOOP;
                case 0x68://PUSH Ivs SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                    {
                        x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    if (FS_usage_flag) {
                        mem8_loc = (regs[4] - 4) >> 0;
                        st32_mem8_write(x);
                        regs[4] = mem8_loc;
                    } else {
                        push_dword_to_stack(x);
                    }
                    break EXEC_LOOP;
                case 0x6a://PUSH Ibss SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                    x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    if (FS_usage_flag) {
                        mem8_loc = (regs[4] - 4) >> 0;
                        st32_mem8_write(x);
                        regs[4] = mem8_loc;
                    } else {
                        push_dword_to_stack(x);
                    }
                    break EXEC_LOOP;
                case 0xc8://ENTER Iw SS:[rSP] Make Stack Frame for Procedure Parameters
                    op_ENTER();
                    break EXEC_LOOP;
                case 0xc9://LEAVE SS:[rSP] eBP High Level Procedure Exit
                    if (FS_usage_flag) {
                        mem8_loc = regs[5];
                        x = ld_32bits_mem8_read();
                        regs[5] = x;
                        regs[4] = (mem8_loc + 4) >> 0;
                    } else {
                        op_LEAVE();
                    }
                    break EXEC_LOOP;
                case 0x9c://PUSHF Flags SS:[rSP] Push FLAGS Register onto the Stack
                    iopl = (cpu.eflags >> 12) & 3;
                    if ((cpu.eflags & 0x00020000) && iopl != 3)
                        abort(13);
                    x = get_FLAGS() & ~(0x00020000 | 0x00010000);
                    if ((((CS_flags >> 8) & 1) ^ 1)) {
                        push_dword_to_stack(x);
                    } else {
                        push_word_to_stack(x);
                    }
                    break EXEC_LOOP;
                case 0x9d://POPF SS:[rSP] Flags Pop Stack into FLAGS Register
                    iopl = (cpu.eflags >> 12) & 3;
                    if ((cpu.eflags & 0x00020000) && iopl != 3)
                        abort(13);
                    if ((((CS_flags >> 8) & 1) ^ 1)) {
                        x = pop_dword_from_stack_read();
                        pop_dword_from_stack_incr_ptr();
                        y = -1;
                    } else {
                        x = pop_word_from_stack_read();
                        pop_word_from_stack_incr_ptr();
                        y = 0xffff;
                    }
                    z = (0x00000100 | 0x00040000 | 0x00200000 | 0x00004000);
                    if (cpu.cpl == 0) {
                        z |= 0x00000200 | 0x00003000;
                    } else {
                        if (cpu.cpl <= iopl)
                            z |= 0x00000200;
                    }
                    set_FLAGS(x, z & y);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x06://PUSH ES SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x0e://PUSH CS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x16://PUSH SS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                case 0x1e://PUSH DS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                    push_dword_to_stack(cpu.segs[OPbyte >> 3].selector);
                    break EXEC_LOOP;
                case 0x07://POP SS:[rSP] ES Pop a Value from the Stack
                case 0x17://POP SS:[rSP] SS Pop a Value from the Stack
                case 0x1f://POP SS:[rSP] DS Pop a Value from the Stack
                    set_segment_register(OPbyte >> 3, pop_dword_from_stack_read() & 0xffff);
                    pop_dword_from_stack_incr_ptr();
                    break EXEC_LOOP;
                case 0x8d://LEA M Gvqp Load Effective Address
                    mem8 = phys_mem8[physmem8_ptr++];
                    if ((mem8 >> 6) == 3)
                        abort(6);
                    CS_flags = (CS_flags & ~0x000f) | (6 + 1);
                    regs[(mem8 >> 3) & 7] = segment_translation(mem8);
                    break EXEC_LOOP;
                case 0xfe://INC  Eb Increment by 1
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    switch (conditional_var) {
                        case 0:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_word_in_register(reg_idx0, increment_8bit((regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1))));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                x = increment_8bit(x);
                                st8_mem8_write(x);
                            }
                            break;
                        case 1:
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_word_in_register(reg_idx0, decrement_8bit((regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1))));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                x = decrement_8bit(x);
                                st8_mem8_write(x);
                            }
                            break;
                        default:
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xff://INC DEC CALL CALLF JMP JMPF PUSH
                    mem8 = phys_mem8[physmem8_ptr++];
                    conditional_var = (mem8 >> 3) & 7;
                    switch (conditional_var) {
                        case 0://INC  Evqp Increment by 1
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                {
                                    if (_op < 25) {
                                        _op2 = _op;
                                        _dst2 = _dst;
                                    }
                                    regs[reg_idx0] = _dst = (regs[reg_idx0] + 1) >> 0;
                                    _op = 27;
                                }
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                {
                                    if (_op < 25) {
                                        _op2 = _op;
                                        _dst2 = _dst;
                                    }
                                    x = _dst = (x + 1) >> 0;
                                    _op = 27;
                                }
                                st32_mem8_write(x);
                            }
                            break;
                        case 1://DEC
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                {
                                    if (_op < 25) {
                                        _op2 = _op;
                                        _dst2 = _dst;
                                    }
                                    regs[reg_idx0] = _dst = (regs[reg_idx0] - 1) >> 0;
                                    _op = 30;
                                }
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                {
                                    if (_op < 25) {
                                        _op2 = _op;
                                        _dst2 = _dst;
                                    }
                                    x = _dst = (x - 1) >> 0;
                                    _op = 30;
                                }
                                st32_mem8_write(x);
                            }
                            break;
                        case 2://CALL
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            y = (eip + physmem8_ptr - initial_mem_ptr);
                            if (FS_usage_flag) {
                                mem8_loc = (regs[4] - 4) >> 0;
                                st32_mem8_write(y);
                                regs[4] = mem8_loc;
                            } else {
                                push_dword_to_stack(y);
                            }
                            eip = x, physmem8_ptr = initial_mem_ptr = 0;
                            break;
                        case 4://JMP
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            eip = x, physmem8_ptr = initial_mem_ptr = 0;
                            break;
                        case 6://PUSH
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            if (FS_usage_flag) {
                                mem8_loc = (regs[4] - 4) >> 0;
                                st32_mem8_write(x);
                                regs[4] = mem8_loc;
                            } else {
                                push_dword_to_stack(x);
                            }
                            break;
                        case 3://CALLF
                        case 5://JMPF
                            if ((mem8 >> 6) == 3)
                                abort(6);
                            mem8_loc = segment_translation(mem8);
                            x = ld_32bits_mem8_read();
                            mem8_loc = (mem8_loc + 4) >> 0;
                            y = ld_16bits_mem8_read();
                            if (conditional_var == 3)
                                op_CALLF(1, y, x, (eip + physmem8_ptr - initial_mem_ptr));
                            else
                                op_JMPF(y, x);
                            break;
                        default:
                            abort(6);
                    }
                    break EXEC_LOOP;
                case 0xeb://JMP Jbs  Jump
                    x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    physmem8_ptr = (physmem8_ptr + x) >> 0;
                    break EXEC_LOOP;
                case 0xe9://JMP Jvds  Jump
                    {
                        x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    physmem8_ptr = (physmem8_ptr + x) >> 0;
                    break EXEC_LOOP;
                case 0xea://JMPF Ap  Jump
                    if ((((CS_flags >> 8) & 1) ^ 1)) {
                        {
                            x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                    } else {
                        x = ld16_mem8_direct();
                    }
                    y = ld16_mem8_direct();
                    op_JMPF(y, x);
                    break EXEC_LOOP;
                case 0x70://JO Jbs  Jump short if overflow (OF=1)
                    if (check_overflow()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x71://JNO Jbs  Jump short if not overflow (OF=0)
                    if (!check_overflow()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x72://JB Jbs  Jump short if below/not above or equal/carry (CF=1)
                    if (check_carry()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x73://JNB Jbs  Jump short if not below/above or equal/not carry (CF=0)
                    if (!check_carry()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x74://JZ Jbs  Jump short if zero/equal (ZF=0)
                    if ((_dst == 0)) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x75://JNZ Jbs  Jump short if not zero/not equal (ZF=1)
                    if (!(_dst == 0)) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x76://JBE Jbs  Jump short if below or equal/not above (CF=1 AND ZF=1)
                    if (check_below_or_equal()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x77://JNBE Jbs  Jump short if not below or equal/above (CF=0 AND ZF=0)
                    if (!check_below_or_equal()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x78://JS Jbs  Jump short if sign (SF=1)
                    if ((_op == 24 ? ((_src >> 7) & 1) : (_dst < 0))) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x79://JNS Jbs  Jump short if not sign (SF=0)
                    if (!(_op == 24 ? ((_src >> 7) & 1) : (_dst < 0))) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7a://JP Jbs  Jump short if parity/parity even (PF=1)
                    if (check_parity()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7b://JNP Jbs  Jump short if not parity/parity odd
                    if (!check_parity()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7c://JL Jbs  Jump short if less/not greater (SF!=OF)
                    if (check_less_than()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7d://JNL Jbs  Jump short if not less/greater or equal (SF=OF)
                    if (!check_less_than()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7e://JLE Jbs  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                    if (check_less_or_equal()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0x7f://JNLE Jbs  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                    if (!check_less_or_equal()) {
                        x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                        physmem8_ptr = (physmem8_ptr + x) >> 0;
                    } else {
                        physmem8_ptr = (physmem8_ptr + 1) >> 0;
                    }
                    break EXEC_LOOP;
                case 0xe0://LOOPNZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=0
                case 0xe1://LOOPZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=1
                case 0xe2://LOOP Jbs eCX Decrement count; Jump short if count!=0
                    x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    if (CS_flags & 0x0080)
                        conditional_var = 0xffff;
                    else
                        conditional_var = -1;
                    y = (regs[1] - 1) & conditional_var;
                    regs[1] = (regs[1] & ~conditional_var) | y;
                    OPbyte &= 3;
                    if (OPbyte == 0)
                        z = !(_dst == 0);
                    else if (OPbyte == 1)
                        z = (_dst == 0);
                    else
                        z = 1;
                    if (y && z) {
                        if (CS_flags & 0x0100) {
                            eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                        } else {
                            physmem8_ptr = (physmem8_ptr + x) >> 0;
                        }
                    }
                    break EXEC_LOOP;
                case 0xe3://JCXZ Jbs  Jump short if eCX register is 0
                    x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                    if (CS_flags & 0x0080)
                        conditional_var = 0xffff;
                    else
                        conditional_var = -1;
                    if ((regs[1] & conditional_var) == 0) {
                        if (CS_flags & 0x0100) {
                            eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                        } else {
                            physmem8_ptr = (physmem8_ptr + x) >> 0;
                        }
                    }
                    break EXEC_LOOP;
                case 0xc2://RETN SS:[rSP]  Return from procedure
                    y = (ld16_mem8_direct() << 16) >> 16;
                    x = pop_dword_from_stack_read();
                    regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 4 + y) & SS_mask);
                    eip = x, physmem8_ptr = initial_mem_ptr = 0;
                    break EXEC_LOOP;
                case 0xc3://RETN SS:[rSP]  Return from procedure
                    if (FS_usage_flag) {
                        mem8_loc = regs[4];
                        x = ld_32bits_mem8_read();
                        regs[4] = (regs[4] + 4) >> 0;
                    } else {
                        x = pop_dword_from_stack_read();
                        pop_dword_from_stack_incr_ptr();
                    }
                    eip = x, physmem8_ptr = initial_mem_ptr = 0;
                    break EXEC_LOOP;
                case 0xe8://CALL Jvds SS:[rSP] Call Procedure
                    {
                        x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                        physmem8_ptr += 4;
                    }
                    y = (eip + physmem8_ptr - initial_mem_ptr);
                    if (FS_usage_flag) {
                        mem8_loc = (regs[4] - 4) >> 0;
                        st32_mem8_write(y);
                        regs[4] = mem8_loc;
                    } else {
                        push_dword_to_stack(y);
                    }
                    physmem8_ptr = (physmem8_ptr + x) >> 0;
                    break EXEC_LOOP;
                case 0x9a://CALLF Ap SS:[rSP] Call Procedure
                    z = (((CS_flags >> 8) & 1) ^ 1);
                    if (z) {
                        {
                            x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                            physmem8_ptr += 4;
                        }
                    } else {
                        x = ld16_mem8_direct();
                    }
                    y = ld16_mem8_direct();
                    op_CALLF(z, y, x, (eip + physmem8_ptr - initial_mem_ptr));
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xca://RETF Iw  Return from procedure
                    y = (ld16_mem8_direct() << 16) >> 16;     //16 bit immediate field
                    op_RETF((((CS_flags >> 8) & 1) ^ 1), y);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xcb://RETF SS:[rSP]  Return from procedure
                    op_RETF((((CS_flags >> 8) & 1) ^ 1), 0);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xcf://IRET SS:[rSP] Flags Interrupt Return
                    op_IRET((((CS_flags >> 8) & 1) ^ 1));
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x90://XCHG  Zvqp Exchange Register/Memory with Register
                    break EXEC_LOOP;
                case 0xcc://INT 3 SS:[rSP] Call to Interrupt Procedure
                    y = (eip + physmem8_ptr - initial_mem_ptr);
                    do_interrupt(3, 1, 0, y, 0);
                    break EXEC_LOOP;
                case 0xcd://INT Ib SS:[rSP] Call to Interrupt Procedure
                    x = phys_mem8[physmem8_ptr++];
                    if ((cpu.eflags & 0x00020000) && ((cpu.eflags >> 12) & 3) != 3)
                        abort(13);
                    y = (eip + physmem8_ptr - initial_mem_ptr);
                    do_interrupt(x, 1, 0, y, 0);
                    break EXEC_LOOP;
                case 0xce://INTO eFlags SS:[rSP] Call to Interrupt Procedure
                    if (check_overflow()) {
                        y = (eip + physmem8_ptr - initial_mem_ptr);
                        do_interrupt(4, 1, 0, y, 0);
                    }
                    break EXEC_LOOP;
                case 0x62://BOUND Gv SS:[rSP] Check Array Index Against Bounds
                    checkOp_BOUND();
                    break EXEC_LOOP;
                case 0xf5://CMC   Complement Carry Flag
                    _src = get_conditional_flags() ^ 0x0001;
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                    break EXEC_LOOP;
                case 0xf8://CLC   Clear Carry Flag
                    _src = get_conditional_flags() & ~0x0001;
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                    break EXEC_LOOP;
                case 0xf9://STC   Set Carry Flag
                    _src = get_conditional_flags() | 0x0001;
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                    break EXEC_LOOP;
                case 0xfc://CLD   Clear Direction Flag
                    cpu.df = 1;
                    break EXEC_LOOP;
                case 0xfd://STD   Set Direction Flag
                    cpu.df = -1;
                    break EXEC_LOOP;
                case 0xfa://CLI   Clear Interrupt Flag
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    cpu.eflags &= ~0x00000200;
                    break EXEC_LOOP;
                case 0xfb://STI   Set Interrupt Flag
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    cpu.eflags |= 0x00000200;
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x9e://SAHF AH  Store AH into Flags
                    _src = ((regs[0] >> 8) & (0x0080 | 0x0040 | 0x0010 | 0x0004 | 0x0001)) | (check_overflow() << 11);
                    _dst = ((_src >> 6) & 1) ^ 1;
                    _op = 24;
                    break EXEC_LOOP;
                case 0x9f://LAHF  AH Load Status Flags into AH Register
                    x = get_FLAGS();
                    set_word_in_register(4, x);
                    break EXEC_LOOP;
                case 0xf4://HLT   Halt
                    if (cpu.cpl != 0)
                        abort(13);
                    cpu.halted = 1;
                    exit_code = 257;
                    break OUTER_LOOP;
                case 0xa4://MOVS (DS:)[rSI] (ES:)[rDI] Move Data from String to String
                    stringOp_MOVSB();
                    break EXEC_LOOP;
                case 0xa5://MOVS DS:[SI] ES:[DI] Move Data from String to String
                    stringOp_MOVSD();
                    break EXEC_LOOP;
                case 0xaa://STOS AL (ES:)[rDI] Store String
                    stringOp_STOSB();
                    break EXEC_LOOP;
                case 0xab://STOS AX ES:[DI] Store String
                    stringOp_STOSD();
                    break EXEC_LOOP;
                case 0xa6://CMPS (ES:)[rDI]  Compare String Operands
                    stringOp_CMPSB();
                    break EXEC_LOOP;
                case 0xa7://CMPS ES:[DI]  Compare String Operands
                    stringOp_CMPSD();
                    break EXEC_LOOP;
                case 0xac://LODS (DS:)[rSI] AL Load String
                    stringOp_LODSB();
                    break EXEC_LOOP;
                case 0xad://LODS DS:[SI] AX Load String
                    stringOp_LODSD();
                    break EXEC_LOOP;
                case 0xae://SCAS (ES:)[rDI]  Scan String
                    stringOp_SCASB();
                    break EXEC_LOOP;
                case 0xaf://SCAS ES:[DI]  Scan String
                    stringOp_SCASD();
                    break EXEC_LOOP;
                case 0x6c://INS DX (ES:)[rDI] Input from Port to String
                    stringOp_INSB();
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x6d://INS DX ES:[DI] Input from Port to String
                    stringOp_INSD();
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x6e://OUTS (DS):[rSI] DX Output String to Port
                    stringOp_OUTSB();
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x6f://OUTS DS:[SI] DX Output String to Port
                    stringOp_OUTSD();
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xd8://FADD Msr ST Add
                case 0xd9://FLD ESsr ST Load Floating Point Value
                case 0xda://FIADD Mdi ST Add
                case 0xdb://FILD Mdi ST Load Integer
                case 0xdc://FADD Mdr ST Add
                case 0xdd://FLD Mdr ST Load Floating Point Value
                case 0xde://FIADD Mwi ST Add
                case 0xdf://FILD Mwi ST Load Integer
                    if (cpu.cr0 & ((1 << 2) | (1 << 3))) {
                        abort(7);
                    }
                    mem8 = phys_mem8[physmem8_ptr++];
                    reg_idx1 = (mem8 >> 3) & 7;
                    reg_idx0 = mem8 & 7;
                    conditional_var = ((OPbyte & 7) << 3) | ((mem8 >> 3) & 7);
                    set_lower_word_in_register(0, 0xffff);
                    if ((mem8 >> 6) == 3) {
                    } else {
                        mem8_loc = segment_translation(mem8);
                    }
                    break EXEC_LOOP;
                case 0x9b://FWAIT   Check pending unmasked floating-point exceptions
                    break EXEC_LOOP;
                case 0xe4://IN Ib AL Input from Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    x = phys_mem8[physmem8_ptr++];
                    set_word_in_register(0, cpu.ld8_port(x));
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xe5://IN Ib eAX Input from Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    x = phys_mem8[physmem8_ptr++];
                    regs[0] = cpu.ld32_port(x);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xe6://OUT AL Ib Output to Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    x = phys_mem8[physmem8_ptr++];
                    cpu.st8_port(x, regs[0] & 0xff);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xe7://OUT eAX Ib Output to Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    x = phys_mem8[physmem8_ptr++];
                    cpu.st32_port(x, regs[0]);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xec://IN DX AL Input from Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    set_word_in_register(0, cpu.ld8_port(regs[2] & 0xffff));
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xed://IN DX eAX Input from Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    regs[0] = cpu.ld32_port(regs[2] & 0xffff);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xee://OUT AL DX Output to Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    cpu.st8_port(regs[2] & 0xffff, regs[0] & 0xff);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0xef://OUT eAX DX Output to Port
                    iopl = (cpu.eflags >> 12) & 3;
                    if (cpu.cpl > iopl)
                        abort(13);
                    cpu.st32_port(regs[2] & 0xffff, regs[0]);
                    {
                        if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                            break OUTER_LOOP;
                    }
                    break EXEC_LOOP;
                case 0x27://DAA  AL Decimal Adjust AL after Addition
                    op_DAA();
                    break EXEC_LOOP;
                case 0x2f://DAS  AL Decimal Adjust AL after Subtraction
                    op_DAS();
                    break EXEC_LOOP;
                case 0x37://AAA  AL ASCII Adjust After Addition
                    op_AAA();
                    break EXEC_LOOP;
                case 0x3f://AAS  AL ASCII Adjust AL After Subtraction
                    op_AAS();
                    break EXEC_LOOP;
                case 0xd4://AAM  AL ASCII Adjust AX After Multiply
                    x = phys_mem8[physmem8_ptr++];
                    op_AAM(x);
                    break EXEC_LOOP;
                case 0xd5://AAD  AL ASCII Adjust AX Before Division
                    x = phys_mem8[physmem8_ptr++];
                    op_AAD(x);
                    break EXEC_LOOP;
                case 0x63://ARPL Ew  Adjust RPL Field of Segment Selector
                    op_ARPL();
                    break EXEC_LOOP;
                case 0xd6://SALC   Undefined and Reserved; Does not Generate #UD
                case 0xf1://INT1   Undefined and Reserved; Does not Generate #UD
                    abort(6);
                    break;

                /*
                   TWO BYTE CODE INSTRUCTIONS BEGIN WITH 0F :  0F xx
                   =====================================================================================================
                */
                case 0x0f:
                    OPbyte = phys_mem8[physmem8_ptr++];
                    switch (OPbyte) {
                        case 0x80://JO Jvds  Jump short if overflow (OF=1)
                        case 0x81://JNO Jvds  Jump short if not overflow (OF=0)
                        case 0x82://JB Jvds  Jump short if below/not above or equal/carry (CF=1)
                        case 0x83://JNB Jvds  Jump short if not below/above or equal/not carry (CF=0)
                        case 0x84://JZ Jvds  Jump short if zero/equal (ZF=0)
                        case 0x85://JNZ Jvds  Jump short if not zero/not equal (ZF=1)
                        case 0x86://JBE Jvds  Jump short if below or equal/not above (CF=1 AND ZF=1)
                        case 0x87://JNBE Jvds  Jump short if not below or equal/above (CF=0 AND ZF=0)
                        case 0x88://JS Jvds  Jump short if sign (SF=1)
                        case 0x89://JNS Jvds  Jump short if not sign (SF=0)
                        case 0x8a://JP Jvds  Jump short if parity/parity even (PF=1)
                        case 0x8b://JNP Jvds  Jump short if not parity/parity odd
                        case 0x8c://JL Jvds  Jump short if less/not greater (SF!=OF)
                        case 0x8d://JNL Jvds  Jump short if not less/greater or equal (SF=OF)
                        case 0x8e://JLE Jvds  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x8f://JNLE Jvds  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                            {
                                x = phys_mem8[physmem8_ptr] | (phys_mem8[physmem8_ptr + 1] << 8) | (phys_mem8[physmem8_ptr + 2] << 16) | (phys_mem8[physmem8_ptr + 3] << 24);
                                physmem8_ptr += 4;
                            }
                            if (check_status_bits_for_jump(OPbyte & 0xf))
                                physmem8_ptr = (physmem8_ptr + x) >> 0;
                            break EXEC_LOOP;
                        case 0x90://SETO  Eb Set Byte on Condition - overflow (OF=1)
                        case 0x91://SETNO  Eb Set Byte on Condition - not overflow (OF=0)
                        case 0x92://SETB  Eb Set Byte on Condition - below/not above or equal/carry (CF=1)
                        case 0x93://SETNB  Eb Set Byte on Condition - not below/above or equal/not carry (CF=0)
                        case 0x94://SETZ  Eb Set Byte on Condition - zero/equal (ZF=0)
                        case 0x95://SETNZ  Eb Set Byte on Condition - not zero/not equal (ZF=1)
                        case 0x96://SETBE  Eb Set Byte on Condition - below or equal/not above (CF=1 AND ZF=1)
                        case 0x97://SETNBE  Eb Set Byte on Condition - not below or equal/above (CF=0 AND ZF=0)
                        case 0x98://SETS  Eb Set Byte on Condition - sign (SF=1)
                        case 0x99://SETNS  Eb Set Byte on Condition - not sign (SF=0)
                        case 0x9a://SETP  Eb Set Byte on Condition - parity/parity even (PF=1)
                        case 0x9b://SETNP  Eb Set Byte on Condition - not parity/parity odd
                        case 0x9c://SETL  Eb Set Byte on Condition - less/not greater (SF!=OF)
                        case 0x9d://SETNL  Eb Set Byte on Condition - not less/greater or equal (SF=OF)
                        case 0x9e://SETLE  Eb Set Byte on Condition - less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x9f://SETNLE  Eb Set Byte on Condition - not less nor equal/greater ((ZF=0) AND (SF=OF))
                            mem8 = phys_mem8[physmem8_ptr++];
                            x = check_status_bits_for_jump(OPbyte & 0xf);
                            if ((mem8 >> 6) == 3) {
                                set_word_in_register(mem8 & 7, x);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                st8_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x40://CMOVO Evqp Gvqp Conditional Move - overflow (OF=1)
                        case 0x41://CMOVNO Evqp Gvqp Conditional Move - not overflow (OF=0)
                        case 0x42://CMOVB Evqp Gvqp Conditional Move - below/not above or equal/carry (CF=1)
                        case 0x43://CMOVNB Evqp Gvqp Conditional Move - not below/above or equal/not carry (CF=0)
                        case 0x44://CMOVZ Evqp Gvqp Conditional Move - zero/equal (ZF=0)
                        case 0x45://CMOVNZ Evqp Gvqp Conditional Move - not zero/not equal (ZF=1)
                        case 0x46://CMOVBE Evqp Gvqp Conditional Move - below or equal/not above (CF=1 AND ZF=1)
                        case 0x47://CMOVNBE Evqp Gvqp Conditional Move - not below or equal/above (CF=0 AND ZF=0)
                        case 0x48://CMOVS Evqp Gvqp Conditional Move - sign (SF=1)
                        case 0x49://CMOVNS Evqp Gvqp Conditional Move - not sign (SF=0)
                        case 0x4a://CMOVP Evqp Gvqp Conditional Move - parity/parity even (PF=1)
                        case 0x4b://CMOVNP Evqp Gvqp Conditional Move - not parity/parity odd
                        case 0x4c://CMOVL Evqp Gvqp Conditional Move - less/not greater (SF!=OF)
                        case 0x4d://CMOVNL Evqp Gvqp Conditional Move - not less/greater or equal (SF=OF)
                        case 0x4e://CMOVLE Evqp Gvqp Conditional Move - less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x4f://CMOVNLE Evqp Gvqp Conditional Move - not less nor equal/greater ((ZF=0) AND (SF=OF))
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_read();
                            }
                            if (check_status_bits_for_jump(OPbyte & 0xf))
                                regs[(mem8 >> 3) & 7] = x;
                            break EXEC_LOOP;
                        case 0xb6://MOVZX Eb Gvqp Move with Zero-Extend
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)) & 0xff;
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                            }
                            regs[reg_idx1] = x;
                            break EXEC_LOOP;
                        case 0xb7://MOVZX Ew Gvqp Move with Zero-Extend
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7] & 0xffff;
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_read();
                            }
                            regs[reg_idx1] = x;
                            break EXEC_LOOP;
                        case 0xbe://MOVSX Eb Gvqp Move with Sign-Extension
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = (((last_tlb_val = _tlb_read_[mem8_loc >>> 12]) == -1) ? __ld_8bits_mem8_read() : phys_mem8[mem8_loc ^ last_tlb_val]);
                            }
                            regs[reg_idx1] = (((x) << 24) >> 24);
                            break EXEC_LOOP;
                        case 0xbf://MOVSX Ew Gvqp Move with Sign-Extension
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_read();
                            }
                            regs[reg_idx1] = (((x) << 16) >> 16);
                            break EXEC_LOOP;
                        case 0x00://SLDT
                            if (!(cpu.cr0 & (1 << 0)) || (cpu.eflags & 0x00020000))
                                abort(6);
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            switch (conditional_var) {
                                case 0://SLDT Store Local Descriptor Table Register
                                case 1://STR Store Task Register
                                    if (conditional_var == 0)
                                        x = cpu.ldt.selector;
                                    else
                                        x = cpu.tr.selector;
                                    if ((mem8 >> 6) == 3) {
                                        set_lower_word_in_register(mem8 & 7, x);
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        st16_mem8_write(x);
                                    }
                                    break;
                                case 2://LDTR Load Local Descriptor Table Register
                                case 3://LTR Load Task Register
                                    if (cpu.cpl != 0)
                                        abort(13);
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7] & 0xffff;
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    if (conditional_var == 2)
                                        op_LDTR(x);
                                    else
                                        op_LTR(x);
                                    break;
                                case 4://VERR Verify a Segment for Reading
                                case 5://VERW Verify a Segment for Writing
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7] & 0xffff;
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    op_VERR_VERW(x, conditional_var & 1);
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0x01://SGDT GDTR Ms Store Global Descriptor Table Register
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            switch (conditional_var) {
                                case 2:
                                case 3:
                                    if ((mem8 >> 6) == 3)
                                        abort(6);
                                    if (this.cpl != 0)
                                        abort(13);
                                    mem8_loc = segment_translation(mem8);
                                    x = ld_16bits_mem8_read();
                                    mem8_loc += 2;
                                    y = ld_32bits_mem8_read();
                                    if (conditional_var == 2) {
                                        this.gdt.base = y;
                                        this.gdt.limit = x;
                                    } else {
                                        this.idt.base = y;
                                        this.idt.limit = x;
                                    }
                                    break;
                                case 7:
                                    if (this.cpl != 0)
                                        abort(13);
                                    if ((mem8 >> 6) == 3)
                                        abort(6);
                                    mem8_loc = segment_translation(mem8);
                                    cpu.tlb_flush_page(mem8_loc & -4096);
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0x02://LAR Mw Gvqp Load Access Rights Byte
                        case 0x03://LSL Mw Gvqp Load Segment Limit
                            op_LAR_LSL((((CS_flags >> 8) & 1) ^ 1), OPbyte & 1);
                            break EXEC_LOOP;
                        case 0x20://MOV Cd Rd Move to/from Control Registers
                            if (cpu.cpl != 0)
                                abort(13);
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) != 3)
                                abort(6);
                            reg_idx1 = (mem8 >> 3) & 7;
                            switch (reg_idx1) {
                                case 0:
                                    x = cpu.cr0;
                                    break;
                                case 2:
                                    x = cpu.cr2;
                                    break;
                                case 3:
                                    x = cpu.cr3;
                                    break;
                                case 4:
                                    x = cpu.cr4;
                                    break;
                                default:
                                    abort(6);
                            }
                            regs[mem8 & 7] = x;
                            break EXEC_LOOP;
                        case 0x22://MOV Rd Cd Move to/from Control Registers
                            if (cpu.cpl != 0)
                                abort(13);
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) != 3)
                                abort(6);
                            reg_idx1 = (mem8 >> 3) & 7;
                            x = regs[mem8 & 7];
                            switch (reg_idx1) {
                                case 0:
                                    set_CR0(x);
                                    break;
                                case 2:
                                    cpu.cr2 = x;
                                    break;
                                case 3:
                                    set_CR3(x);
                                    break;
                                case 4:
                                    set_CR4(x);
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0x06://CLTS  CR0 Clear Task-Switched Flag in CR0
                            if (cpu.cpl != 0)
                                abort(13);
                            set_CR0(cpu.cr0 & ~(1 << 3)); //Clear Task-Switched Flag in CR0
                            break EXEC_LOOP;
                        case 0x23://MOV Rd Dd Move to/from Debug Registers
                            if (cpu.cpl != 0)
                                abort(13);
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) != 3)
                                abort(6);
                            reg_idx1 = (mem8 >> 3) & 7;
                            x = regs[mem8 & 7];
                            if (reg_idx1 == 4 || reg_idx1 == 5)
                                abort(6);
                            break EXEC_LOOP;
                        case 0xb2://LSS Mptp SS Load Far Pointer
                        case 0xb4://LFS Mptp FS Load Far Pointer
                        case 0xb5://LGS Mptp GS Load Far Pointer
                            op_16_load_far_pointer32(OPbyte & 7);
                            break EXEC_LOOP;
                        case 0xa2://CPUID  IA32_BIOS_SIGN_ID CPU Identification
                            op_CPUID();
                            break EXEC_LOOP;
                        case 0xa4://SHLD Gvqp Evqp Double Precision Shift Left
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            if ((mem8 >> 6) == 3) {
                                z = phys_mem8[physmem8_ptr++];
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = op_SHLD(regs[reg_idx0], y, z);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                z = phys_mem8[physmem8_ptr++];
                                x = ld_32bits_mem8_write();
                                x = op_SHLD(x, y, z);
                                st32_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0xa5://SHLD Gvqp Evqp Double Precision Shift Left
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            z = regs[1];
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = op_SHLD(regs[reg_idx0], y, z);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                x = op_SHLD(x, y, z);
                                st32_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0xac://SHRD Gvqp Evqp Double Precision Shift Right
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            if ((mem8 >> 6) == 3) {
                                z = phys_mem8[physmem8_ptr++];
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = op_SHRD(regs[reg_idx0], y, z);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                z = phys_mem8[physmem8_ptr++];
                                x = ld_32bits_mem8_write();
                                x = op_SHRD(x, y, z);
                                st32_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0xad://SHRD Gvqp Evqp Double Precision Shift Right
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            z = regs[1];
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = op_SHRD(regs[reg_idx0], y, z);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                x = op_SHRD(x, y, z);
                                st32_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0xba://BT Evqp  Bit Test
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            switch (conditional_var) {
                                case 4://BT Evqp  Bit Test
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                        y = phys_mem8[physmem8_ptr++];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        y = phys_mem8[physmem8_ptr++];
                                        x = ld_32bits_mem8_read();
                                    }
                                    op_BT(x, y);
                                    break;
                                case 5://BTS  Bit Test and Set
                                case 6://BTR  Bit Test and Reset
                                case 7://BTC  Bit Test and Complement
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        y = phys_mem8[physmem8_ptr++];
                                        regs[reg_idx0] = op_BTS_BTR_BTC(conditional_var & 3, regs[reg_idx0], y);
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        y = phys_mem8[physmem8_ptr++];
                                        x = ld_32bits_mem8_write();
                                        x = op_BTS_BTR_BTC(conditional_var & 3, x, y);
                                        st32_mem8_write(x);
                                    }
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0xa3://BT Evqp  Bit Test
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                mem8_loc = (mem8_loc + ((y >> 5) << 2)) >> 0;
                                x = ld_32bits_mem8_read();
                            }
                            op_BT(x, y);
                            break EXEC_LOOP;
                        case 0xab://BTS Gvqp Evqp Bit Test and Set
                        case 0xb3://BTR Gvqp Evqp Bit Test and Reset
                        case 0xbb://BTC Gvqp Evqp Bit Test and Complement
                            mem8 = phys_mem8[physmem8_ptr++];
                            y = regs[(mem8 >> 3) & 7];
                            conditional_var = (OPbyte >> 3) & 3;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                regs[reg_idx0] = op_BTS_BTR_BTC(conditional_var, regs[reg_idx0], y);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                mem8_loc = (mem8_loc + ((y >> 5) << 2)) >> 0;
                                x = ld_32bits_mem8_write();
                                x = op_BTS_BTR_BTC(conditional_var, x, y);
                                st32_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0xbc://BSF Evqp Gvqp Bit Scan Forward
                        case 0xbd://BSR Evqp Gvqp Bit Scan Reverse
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld_32bits_mem8_read();
                            }
                            if (OPbyte & 1)
                                regs[reg_idx1] = op_BSR(regs[reg_idx1], y);
                            else
                                regs[reg_idx1] = op_BSF(regs[reg_idx1], y);
                            break EXEC_LOOP;
                        case 0xaf://IMUL Evqp Gvqp Signed Multiply
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld_32bits_mem8_read();
                            }
                            regs[reg_idx1] = op_IMUL32(regs[reg_idx1], y);
                            break EXEC_LOOP;
                        case 0x31://RDTSC IA32_TIME_STAMP_COUNTER EAX Read Time-Stamp Counter
                            if ((cpu.cr4 & (1 << 2)) && cpu.cpl != 0)
                                abort(13);
                            x = current_cycle_count();
                            regs[0] = x >>> 0;
                            regs[2] = (x / 0x100000000) >>> 0;
                            break EXEC_LOOP;
                        case 0xc0://XADD  Eb Exchange and Add
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                                y = do_8bit_math(0, x, (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                                set_word_in_register(reg_idx1, x);
                                set_word_in_register(reg_idx0, y);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                y = do_8bit_math(0, x, (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                                st8_mem8_write(y);
                                set_word_in_register(reg_idx1, x);
                            }
                            break EXEC_LOOP;
                        case 0xc1://XADD  Evqp Exchange and Add
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = regs[reg_idx0];
                                y = do_32bit_math(0, x, regs[reg_idx1]);
                                regs[reg_idx1] = x;
                                regs[reg_idx0] = y;
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                y = do_32bit_math(0, x, regs[reg_idx1]);
                                st32_mem8_write(y);
                                regs[reg_idx1] = x;
                            }
                            break EXEC_LOOP;
                        case 0xb0://CMPXCHG Gb Eb Compare and Exchange
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                                y = do_8bit_math(5, regs[0], x);
                                if (y == 0) {
                                    set_word_in_register(reg_idx0, (regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                                } else {
                                    set_word_in_register(0, x);
                                }
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_8bits_mem8_write();
                                y = do_8bit_math(5, regs[0], x);
                                if (y == 0) {
                                    st8_mem8_write((regs[reg_idx1 & 3] >> ((reg_idx1 & 4) << 1)));
                                } else {
                                    set_word_in_register(0, x);
                                }
                            }
                            break EXEC_LOOP;
                        case 0xb1://CMPXCHG Gvqp Evqp Compare and Exchange
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = regs[reg_idx0];
                                y = do_32bit_math(5, regs[0], x);
                                if (y == 0) {
                                    regs[reg_idx0] = regs[reg_idx1];
                                } else {
                                    regs[0] = x;
                                }
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_32bits_mem8_write();
                                y = do_32bit_math(5, regs[0], x);
                                if (y == 0) {
                                    st32_mem8_write(regs[reg_idx1]);
                                } else {
                                    regs[0] = x;
                                }
                            }
                            break EXEC_LOOP;
                        case 0xa0://PUSH FS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0xa8://PUSH GS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                            push_dword_to_stack(cpu.segs[(OPbyte >> 3) & 7].selector);
                            break EXEC_LOOP;
                        case 0xa1://POP SS:[rSP] FS Pop a Value from the Stack
                        case 0xa9://POP SS:[rSP] GS Pop a Value from the Stack
                            set_segment_register((OPbyte >> 3) & 7, pop_dword_from_stack_read() & 0xffff);
                            pop_dword_from_stack_incr_ptr();
                            break EXEC_LOOP;
                        case 0xc8://BSWAP  Zvqp Byte Swap
                        case 0xc9:
                        case 0xca:
                        case 0xcb:
                        case 0xcc:
                        case 0xcd:
                        case 0xce:
                        case 0xcf:
                            reg_idx1 = OPbyte & 7;
                            x = regs[reg_idx1];
                            x = (x >>> 24) | ((x >> 8) & 0x0000ff00) | ((x << 8) & 0x00ff0000) | (x << 24);
                            regs[reg_idx1] = x;
                            break EXEC_LOOP;
                        case 0x04:
                        case 0x05://LOADALL  AX Load All of the CPU Registers
                        case 0x07://LOADALL  EAX Load All of the CPU Registers
                        case 0x08://INVD   Invalidate Internal Caches
                        case 0x09://WBINVD   Write Back and Invalidate Cache
                        case 0x0a:
                        case 0x0b://UD2   Undefined Instruction
                        case 0x0c:
                        case 0x0d://NOP Ev  No Operation
                        case 0x0e:
                        case 0x0f:
                        case 0x10://MOVUPS Wps Vps Move Unaligned Packed Single-FP Values
                        case 0x11://MOVUPS Vps Wps Move Unaligned Packed Single-FP Values
                        case 0x12://MOVHLPS Uq Vq Move Packed Single-FP Values High to Low
                        case 0x13://MOVLPS Vq Mq Move Low Packed Single-FP Values
                        case 0x14://UNPCKLPS Wq Vps Unpack and Interleave Low Packed Single-FP Values
                        case 0x15://UNPCKHPS Wq Vps Unpack and Interleave High Packed Single-FP Values
                        case 0x16://MOVLHPS Uq Vq Move Packed Single-FP Values Low to High
                        case 0x17://MOVHPS Vq Mq Move High Packed Single-FP Values
                        case 0x18://HINT_NOP Ev  Hintable NOP
                        case 0x19://HINT_NOP Ev  Hintable NOP
                        case 0x1a://HINT_NOP Ev  Hintable NOP
                        case 0x1b://HINT_NOP Ev  Hintable NOP
                        case 0x1c://HINT_NOP Ev  Hintable NOP
                        case 0x1d://HINT_NOP Ev  Hintable NOP
                        case 0x1e://HINT_NOP Ev  Hintable NOP
                        case 0x1f://HINT_NOP Ev  Hintable NOP
                        case 0x21://MOV Dd Rd Move to/from Debug Registers
                        case 0x24://MOV Td Rd Move to/from Test Registers
                        case 0x25:
                        case 0x26://MOV Rd Td Move to/from Test Registers
                        case 0x27:
                        case 0x28://MOVAPS Wps Vps Move Aligned Packed Single-FP Values
                        case 0x29://MOVAPS Vps Wps Move Aligned Packed Single-FP Values
                        case 0x2a://CVTPI2PS Qpi Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                        case 0x2b://MOVNTPS Vps Mps Store Packed Single-FP Values Using Non-Temporal Hint
                        case 0x2c://CVTTPS2PI Wpsq Ppi Convert with Trunc. Packed Single-FP Values to1.11 PackedDW Integers
                        case 0x2d://CVTPS2PI Wpsq Ppi Convert Packed Single-FP Values to1.11 PackedDW Integers
                        case 0x2e://UCOMISS Vss  Unordered Compare Scalar Single-FP Values and Set EFLAGS
                        case 0x2f://COMISS Vss  Compare Scalar Ordered Single-FP Values and Set EFLAGS
                        case 0x30://WRMSR rCX MSR Write to Model Specific Register
                        case 0x32://RDMSR rCX rAX Read from Model Specific Register
                        case 0x33://RDPMC PMC EAX Read Performance-Monitoring Counters
                        case 0x34://SYSENTER IA32_SYSENTER_CS SS Fast System Call
                        case 0x35://SYSEXIT IA32_SYSENTER_CS SS Fast Return from Fast System Call
                        case 0x36:
                        case 0x37://GETSEC EAX  GETSEC Leaf Functions
                        case 0x38://PSHUFB Qq Pq Packed Shuffle Bytes
                        case 0x39:
                        case 0x3a://ROUNDPS Wps Vps Round Packed Single-FP Values
                        case 0x3b:
                        case 0x3c:
                        case 0x3d:
                        case 0x3e:
                        case 0x3f:
                        case 0x50://MOVMSKPS Ups Gdqp Extract Packed Single-FP Sign Mask
                        case 0x51://SQRTPS Wps Vps Compute Square Roots of Packed Single-FP Values
                        case 0x52://RSQRTPS Wps Vps Compute Recipr. of Square Roots of Packed Single-FP Values
                        case 0x53://RCPPS Wps Vps Compute Reciprocals of Packed Single-FP Values
                        case 0x54://ANDPS Wps Vps Bitwise Logical AND of Packed Single-FP Values
                        case 0x55://ANDNPS Wps Vps Bitwise Logical AND NOT of Packed Single-FP Values
                        case 0x56://ORPS Wps Vps Bitwise Logical OR of Single-FP Values
                        case 0x57://XORPS Wps Vps Bitwise Logical XOR for Single-FP Values
                        case 0x58://ADDPS Wps Vps Add Packed Single-FP Values
                        case 0x59://MULPS Wps Vps Multiply Packed Single-FP Values
                        case 0x5a://CVTPS2PD Wps Vpd Convert Packed Single-FP Values to1.11 PackedDouble-FP Values
                        case 0x5b://CVTDQ2PS Wdq Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                        case 0x5c://SUBPS Wps Vps Subtract Packed Single-FP Values
                        case 0x5d://MINPS Wps Vps Return Minimum Packed Single-FP Values
                        case 0x5e://DIVPS Wps Vps Divide Packed Single-FP Values
                        case 0x5f://MAXPS Wps Vps Return Maximum Packed Single-FP Values
                        case 0x60://PUNPCKLBW Qd Pq Unpack Low Data
                        case 0x61://PUNPCKLWD Qd Pq Unpack Low Data
                        case 0x62://PUNPCKLDQ Qd Pq Unpack Low Data
                        case 0x63://PACKSSWB Qd Pq Pack with Signed Saturation
                        case 0x64://PCMPGTB Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x65://PCMPGTW Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x66://PCMPGTD Qd Pq Compare Packed Signed Integers for Greater Than
                        case 0x67://PACKUSWB Qq Pq Pack with Unsigned Saturation
                        case 0x68://PUNPCKHBW Qq Pq Unpack High Data
                        case 0x69://PUNPCKHWD Qq Pq Unpack High Data
                        case 0x6a://PUNPCKHDQ Qq Pq Unpack High Data
                        case 0x6b://PACKSSDW Qq Pq Pack with Signed Saturation
                        case 0x6c://PUNPCKLQDQ Wdq Vdq Unpack Low Data
                        case 0x6d://PUNPCKHQDQ Wdq Vdq Unpack High Data
                        case 0x6e://MOVD Ed Pq Move Doubleword
                        case 0x6f://MOVQ Qq Pq Move Quadword
                        case 0x70://PSHUFW Qq Pq Shuffle Packed Words
                        case 0x71://PSRLW Ib Nq Shift Packed Data Right Logical
                        case 0x72://PSRLD Ib Nq Shift Double Quadword Right Logical
                        case 0x73://PSRLQ Ib Nq Shift Packed Data Right Logical
                        case 0x74://PCMPEQB Qq Pq Compare Packed Data for Equal
                        case 0x75://PCMPEQW Qq Pq Compare Packed Data for Equal
                        case 0x76://PCMPEQD Qq Pq Compare Packed Data for Equal
                        case 0x77://EMMS   Empty MMX Technology State
                        case 0x78://VMREAD Gd Ed Read Field from Virtual-Machine Control Structure
                        case 0x79://VMWRITE Gd  Write Field to Virtual-Machine Control Structure
                        case 0x7a:
                        case 0x7b:
                        case 0x7c://HADDPD Wpd Vpd Packed Double-FP Horizontal Add
                        case 0x7d://HSUBPD Wpd Vpd Packed Double-FP Horizontal Subtract
                        case 0x7e://MOVD Pq Ed Move Doubleword
                        case 0x7f://MOVQ Pq Qq Move Quadword
                        case 0xa6:
                        case 0xa7:
                        case 0xaa://RSM  Flags Resume from System Management Mode
                        case 0xae://FXSAVE ST Mstx Save x87 FPU, MMX, XMM, and MXCSR State
                        case 0xb8://JMPE   Jump to IA-64 Instruction Set
                        case 0xb9://UD G  Undefined Instruction
                        case 0xc2://CMPPS Wps Vps Compare Packed Single-FP Values
                        case 0xc3://MOVNTI Gdqp Mdqp Store Doubleword Using Non-Temporal Hint
                        case 0xc4://PINSRW Rdqp Pq Insert Word
                        case 0xc5://PEXTRW Nq Gdqp Extract Word
                        case 0xc6://SHUFPS Wps Vps Shuffle Packed Single-FP Values
                        case 0xc7://CMPXCHG8B EBX Mq Compare and Exchange Bytes
                        case 0xd0://ADDSUBPD Wpd Vpd Packed Double-FP Add/Subtract
                        case 0xd1://PSRLW Qq Pq Shift Packed Data Right Logical
                        case 0xd2://PSRLD Qq Pq Shift Packed Data Right Logical
                        case 0xd3://PSRLQ Qq Pq Shift Packed Data Right Logical
                        case 0xd4://PADDQ Qq Pq Add Packed Quadword Integers
                        case 0xd5://PMULLW Qq Pq Multiply Packed Signed Integers and Store Low Result
                        case 0xd6://MOVQ Vq Wq Move Quadword
                        case 0xd7://PMOVMSKB Nq Gdqp Move Byte Mask
                        case 0xd8://PSUBUSB Qq Pq Subtract Packed Unsigned Integers with Unsigned Saturation
                        case 0xd9://PSUBUSW Qq Pq Subtract Packed Unsigned Integers with Unsigned Saturation
                        case 0xda://PMINUB Qq Pq Minimum of Packed Unsigned Byte Integers
                        case 0xdb://PAND Qd Pq Logical AND
                        case 0xdc://PADDUSB Qq Pq Add Packed Unsigned Integers with Unsigned Saturation
                        case 0xdd://PADDUSW Qq Pq Add Packed Unsigned Integers with Unsigned Saturation
                        case 0xde://PMAXUB Qq Pq Maximum of Packed Unsigned Byte Integers
                        case 0xdf://PANDN Qq Pq Logical AND NOT
                        case 0xe0://PAVGB Qq Pq Average Packed Integers
                        case 0xe1://PSRAW Qq Pq Shift Packed Data Right Arithmetic
                        case 0xe2://PSRAD Qq Pq Shift Packed Data Right Arithmetic
                        case 0xe3://PAVGW Qq Pq Average Packed Integers
                        case 0xe4://PMULHUW Qq Pq Multiply Packed Unsigned Integers and Store High Result
                        case 0xe5://PMULHW Qq Pq Multiply Packed Signed Integers and Store High Result
                        case 0xe6://CVTPD2DQ Wpd Vdq Convert Packed Double-FP Values to1.11 PackedDW Integers
                        case 0xe7://MOVNTQ Pq Mq Store of Quadword Using Non-Temporal Hint
                        case 0xe8://PSUBSB Qq Pq Subtract Packed Signed Integers with Signed Saturation
                        case 0xe9://PSUBSW Qq Pq Subtract Packed Signed Integers with Signed Saturation
                        case 0xea://PMINSW Qq Pq Minimum of Packed Signed Word Integers
                        case 0xeb://POR Qq Pq Bitwise Logical OR
                        case 0xec://PADDSB Qq Pq Add Packed Signed Integers with Signed Saturation
                        case 0xed://PADDSW Qq Pq Add Packed Signed Integers with Signed Saturation
                        case 0xee://PMAXSW Qq Pq Maximum of Packed Signed Word Integers
                        case 0xef://PXOR Qq Pq Logical Exclusive OR
                        case 0xf0://LDDQU Mdq Vdq Load Unaligned Integer 128 Bits
                        case 0xf1://PSLLW Qq Pq Shift Packed Data Left Logical
                        case 0xf2://PSLLD Qq Pq Shift Packed Data Left Logical
                        case 0xf3://PSLLQ Qq Pq Shift Packed Data Left Logical
                        case 0xf4://PMULUDQ Qq Pq Multiply Packed Unsigned DW Integers
                        case 0xf5://PMADDWD Qd Pq Multiply and Add Packed Integers
                        case 0xf6://PSADBW Qq Pq Compute Sum of Absolute Differences
                        case 0xf7://MASKMOVQ Nq (DS:)[rDI] Store Selected Bytes of Quadword
                        case 0xf8://PSUBB Qq Pq Subtract Packed Integers
                        case 0xf9://PSUBW Qq Pq Subtract Packed Integers
                        case 0xfa://PSUBD Qq Pq Subtract Packed Integers
                        case 0xfb://PSUBQ Qq Pq Subtract Packed Quadword Integers
                        case 0xfc://PADDB Qq Pq Add Packed Integers
                        case 0xfd://PADDW Qq Pq Add Packed Integers
                        case 0xfe://PADDD Qq Pq Add Packed Integers
                        case 0xff:
                        default:
                            abort(6);
                    }
                    break;

                /*
                  16bit Compatibility Mode Operator Routines
                  ==========================================================================================
                   0x1XX  corresponds to the 16-bit compat operator corresponding to the usual 0xXX
                */
                default:
                    switch (OPbyte) {
                        case 0x189://MOV Gvqp Evqp Move
                            mem8 = phys_mem8[physmem8_ptr++];
                            x = regs[(mem8 >> 3) & 7];
                            if ((mem8 >> 6) == 3) {
                                set_lower_word_in_register(mem8 & 7, x);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                st16_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x18b://MOV Evqp Gvqp Move
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_read();
                            }
                            set_lower_word_in_register((mem8 >> 3) & 7, x);
                            break EXEC_LOOP;
                        case 0x1b8://MOV Ivqp Zvqp Move
                        case 0x1b9:
                        case 0x1ba:
                        case 0x1bb:
                        case 0x1bc:
                        case 0x1bd:
                        case 0x1be:
                        case 0x1bf:
                            set_lower_word_in_register(OPbyte & 7, ld16_mem8_direct());
                            break EXEC_LOOP;
                        case 0x1a1://MOV Ovqp rAX Move
                            mem8_loc = segmented_mem8_loc_for_MOV();
                            x = ld_16bits_mem8_read();
                            set_lower_word_in_register(0, x);
                            break EXEC_LOOP;
                        case 0x1a3://MOV rAX Ovqp Move
                            mem8_loc = segmented_mem8_loc_for_MOV();
                            st16_mem8_write(regs[0]);
                            break EXEC_LOOP;
                        case 0x1c7://MOV Ivds Evqp Move
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3) {
                                x = ld16_mem8_direct();
                                set_lower_word_in_register(mem8 & 7, x);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld16_mem8_direct();
                                st16_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x191:
                        case 0x192:
                        case 0x193:
                        case 0x194:
                        case 0x195:
                        case 0x196:
                        case 0x197:
                            reg_idx1 = OPbyte & 7;
                            x = regs[0];
                            set_lower_word_in_register(0, regs[reg_idx1]);
                            set_lower_word_in_register(reg_idx1, x);
                            break EXEC_LOOP;
                        case 0x187://XCHG  Gvqp Exchange Register/Memory with Register
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                x = regs[reg_idx0];
                                set_lower_word_in_register(reg_idx0, regs[reg_idx1]);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_write();
                                st16_mem8_write(regs[reg_idx1]);
                            }
                            set_lower_word_in_register(reg_idx1, x);
                            break EXEC_LOOP;
                        case 0x1c4://LES Mp ES Load Far Pointer
                            op_16_load_far_pointer16(0);
                            break EXEC_LOOP;
                        case 0x1c5://LDS Mp DS Load Far Pointer
                            op_16_load_far_pointer16(3);
                            break EXEC_LOOP;
                        case 0x101://ADD Gvqp Evqp Add
                        case 0x109://OR Gvqp Evqp Logical Inclusive OR
                        case 0x111://ADC Gvqp Evqp Add with Carry
                        case 0x119://SBB Gvqp Evqp Integer Subtraction with Borrow
                        case 0x121://AND Gvqp Evqp Logical AND
                        case 0x129://SUB Gvqp Evqp Subtract
                        case 0x131://XOR Gvqp Evqp Logical Exclusive OR
                        case 0x139://CMP Evqp  Compare Two Operands
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (OPbyte >> 3) & 7;
                            y = regs[(mem8 >> 3) & 7];
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_lower_word_in_register(reg_idx0, do_16bit_math(conditional_var, regs[reg_idx0], y));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                if (conditional_var != 7) {
                                    x = ld_16bits_mem8_write();
                                    x = do_16bit_math(conditional_var, x, y);
                                    st16_mem8_write(x);
                                } else {
                                    x = ld_16bits_mem8_read();
                                    do_16bit_math(7, x, y);
                                }
                            }
                            break EXEC_LOOP;
                        case 0x103://ADD Evqp Gvqp Add
                        case 0x10b://OR Evqp Gvqp Logical Inclusive OR
                        case 0x113://ADC Evqp Gvqp Add with Carry
                        case 0x11b://SBB Evqp Gvqp Integer Subtraction with Borrow
                        case 0x123://AND Evqp Gvqp Logical AND
                        case 0x12b://SUB Evqp Gvqp Subtract
                        case 0x133://XOR Evqp Gvqp Logical Exclusive OR
                        case 0x13b://CMP Gvqp  Compare Two Operands
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (OPbyte >> 3) & 7;
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld_16bits_mem8_read();
                            }
                            set_lower_word_in_register(reg_idx1, do_16bit_math(conditional_var, regs[reg_idx1], y));
                            break EXEC_LOOP;
                        case 0x105://ADD Ivds rAX Add
                        case 0x10d://OR Ivds rAX Logical Inclusive OR
                        case 0x115://ADC Ivds rAX Add with Carry
                        case 0x11d://SBB Ivds rAX Integer Subtraction with Borrow
                        case 0x125://AND Ivds rAX Logical AND
                        case 0x12d://SUB Ivds rAX Subtract
                        case 0x135://XOR Ivds rAX Logical Exclusive OR
                        case 0x13d://CMP rAX  Compare Two Operands
                            y = ld16_mem8_direct();
                            conditional_var = (OPbyte >> 3) & 7;
                            set_lower_word_in_register(0, do_16bit_math(conditional_var, regs[0], y));
                            break EXEC_LOOP;
                        case 0x181://ADD Ivds Evqp Add
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                y = ld16_mem8_direct();
                                regs[reg_idx0] = do_16bit_math(conditional_var, regs[reg_idx0], y);
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld16_mem8_direct();
                                if (conditional_var != 7) {
                                    x = ld_16bits_mem8_write();
                                    x = do_16bit_math(conditional_var, x, y);
                                    st16_mem8_write(x);
                                } else {
                                    x = ld_16bits_mem8_read();
                                    do_16bit_math(7, x, y);
                                }
                            }
                            break EXEC_LOOP;
                        case 0x183://ADD Ibs Evqp Add
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                y = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                                set_lower_word_in_register(reg_idx0, do_16bit_math(conditional_var, regs[reg_idx0], y));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                                if (conditional_var != 7) {
                                    x = ld_16bits_mem8_write();
                                    x = do_16bit_math(conditional_var, x, y);
                                    st16_mem8_write(x);
                                } else {
                                    x = ld_16bits_mem8_read();
                                    do_16bit_math(7, x, y);
                                }
                            }
                            break EXEC_LOOP;
                        case 0x140://INC  Zv Increment by 1
                        case 0x141://REX.B   Extension of r/m field, base field, or opcode reg field
                        case 0x142://REX.X   Extension of SIB index field
                        case 0x143://REX.XB   REX.X and REX.B combination
                        case 0x144://REX.R   Extension of ModR/M reg field
                        case 0x145://REX.RB   REX.R and REX.B combination
                        case 0x146://REX.RX   REX.R and REX.X combination
                        case 0x147://REX.RXB   REX.R, REX.X and REX.B combination
                            reg_idx1 = OPbyte & 7;
                            set_lower_word_in_register(reg_idx1, increment_16bit(regs[reg_idx1]));
                            break EXEC_LOOP;
                        case 0x148://DEC  Zv Decrement by 1
                        case 0x149://REX.WB   REX.W and REX.B combination
                        case 0x14a://REX.WX   REX.W and REX.X combination
                        case 0x14b://REX.WXB   REX.W, REX.X and REX.B combination
                        case 0x14c://REX.WR   REX.W and REX.R combination
                        case 0x14d://REX.WRB   REX.W, REX.R and REX.B combination
                        case 0x14e://REX.WRX   REX.W, REX.R and REX.X combination
                        case 0x14f://REX.WRXB   REX.W, REX.R, REX.X and REX.B combination
                            reg_idx1 = OPbyte & 7;
                            set_lower_word_in_register(reg_idx1, decrement_16bit(regs[reg_idx1]));
                            break EXEC_LOOP;
                        case 0x16b://IMUL Evqp Gvqp Signed Multiply
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld_16bits_mem8_read();
                            }
                            z = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            set_lower_word_in_register(reg_idx1, op_16_IMUL(y, z));
                            break EXEC_LOOP;
                        case 0x169://IMUL Evqp Gvqp Signed Multiply
                            mem8 = phys_mem8[physmem8_ptr++];
                            reg_idx1 = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = ld_16bits_mem8_read();
                            }
                            z = ld16_mem8_direct();
                            set_lower_word_in_register(reg_idx1, op_16_IMUL(y, z));
                            break EXEC_LOOP;
                        case 0x185://TEST Evqp  Logical Compare
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3) {
                                x = regs[mem8 & 7];
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_read();
                            }
                            y = regs[(mem8 >> 3) & 7];
                            {
                                _dst = (((x & y) << 16) >> 16);
                                _op = 13;
                            }
                            break EXEC_LOOP;
                        case 0x1a9://TEST rAX  Logical Compare
                            y = ld16_mem8_direct();
                            {
                                _dst = (((regs[0] & y) << 16) >> 16);
                                _op = 13;
                            }
                            break EXEC_LOOP;
                        case 0x1f7://TEST Evqp  Logical Compare
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            switch (conditional_var) {
                                case 0:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    y = ld16_mem8_direct();
                                    {
                                        _dst = (((x & y) << 16) >> 16);
                                        _op = 13;
                                    }
                                    break;
                                case 2:
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, ~regs[reg_idx0]);
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        x = ~x;
                                        st16_mem8_write(x);
                                    }
                                    break;
                                case 3:
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, do_16bit_math(5, 0, regs[reg_idx0]));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        x = do_16bit_math(5, 0, x);
                                        st16_mem8_write(x);
                                    }
                                    break;
                                case 4:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    x = op_16_MUL(regs[0], x);
                                    set_lower_word_in_register(0, x);
                                    set_lower_word_in_register(2, x >> 16);
                                    break;
                                case 5:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    x = op_16_IMUL(regs[0], x);
                                    set_lower_word_in_register(0, x);
                                    set_lower_word_in_register(2, x >> 16);
                                    break;
                                case 6:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    op_16_DIV(x);
                                    break;
                                case 7:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    op_16_IDIV(x);
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0x1c1://ROL Ib Evqp Rotate
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                y = phys_mem8[physmem8_ptr++];
                                reg_idx0 = mem8 & 7;
                                set_lower_word_in_register(reg_idx0, shift16(conditional_var, regs[reg_idx0], y));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                y = phys_mem8[physmem8_ptr++];
                                x = ld_16bits_mem8_write();
                                x = shift16(conditional_var, x, y);
                                st16_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x1d1://ROL 1 Evqp Rotate
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_lower_word_in_register(reg_idx0, shift16(conditional_var, regs[reg_idx0], 1));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_write();
                                x = shift16(conditional_var, x, 1);
                                st16_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x1d3://ROL CL Evqp Rotate
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            y = regs[1] & 0xff;
                            if ((mem8 >> 6) == 3) {
                                reg_idx0 = mem8 & 7;
                                set_lower_word_in_register(reg_idx0, shift16(conditional_var, regs[reg_idx0], y));
                            } else {
                                mem8_loc = segment_translation(mem8);
                                x = ld_16bits_mem8_write();
                                x = shift16(conditional_var, x, y);
                                st16_mem8_write(x);
                            }
                            break EXEC_LOOP;
                        case 0x198://CBW AL AX Convert Byte to Word
                            set_lower_word_in_register(0, (regs[0] << 24) >> 24);
                            break EXEC_LOOP;
                        case 0x199://CWD AX DX Convert Word to Doubleword
                            set_lower_word_in_register(2, (regs[0] << 16) >> 31);
                            break EXEC_LOOP;
                        case 0x190://XCHG  Zvqp Exchange Register/Memory with Register
                            break EXEC_LOOP;
                        case 0x150://PUSH Zv SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0x151:
                        case 0x152:
                        case 0x153:
                        case 0x154:
                        case 0x155:
                        case 0x156:
                        case 0x157:
                            push_word_to_stack(regs[OPbyte & 7]);
                            break EXEC_LOOP;
                        case 0x158://POP SS:[rSP] Zv Pop a Value from the Stack
                        case 0x159:
                        case 0x15a:
                        case 0x15b:
                        case 0x15c:
                        case 0x15d:
                        case 0x15e:
                        case 0x15f:
                            x = pop_word_from_stack_read();
                            pop_word_from_stack_incr_ptr();
                            set_lower_word_in_register(OPbyte & 7, x);
                            break EXEC_LOOP;
                        case 0x160://PUSHA AX SS:[rSP] Push All General-Purpose Registers
                            op_16_PUSHA();
                            break EXEC_LOOP;
                        case 0x161://POPA SS:[rSP] DI Pop All General-Purpose Registers
                            op_16_POPA();
                            break EXEC_LOOP;
                        case 0x18f://POP SS:[rSP] Ev Pop a Value from the Stack
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3) {
                                x = pop_word_from_stack_read();
                                pop_word_from_stack_incr_ptr();
                                set_lower_word_in_register(mem8 & 7, x);
                            } else {
                                x = pop_word_from_stack_read();
                                y = regs[4];
                                pop_word_from_stack_incr_ptr();
                                z = regs[4];
                                mem8_loc = segment_translation(mem8);
                                regs[4] = y;
                                st16_mem8_write(x);
                                regs[4] = z;
                            }
                            break EXEC_LOOP;
                        case 0x168://PUSH Ivs SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                            x = ld16_mem8_direct();
                            push_word_to_stack(x);
                            break EXEC_LOOP;
                        case 0x16a://PUSH Ibss SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                            x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            push_word_to_stack(x);
                            break EXEC_LOOP;
                        case 0x1c8://ENTER Iw SS:[rSP] Make Stack Frame for Procedure Parameters
                            op_16_ENTER();
                            break EXEC_LOOP;
                        case 0x1c9://LEAVE SS:[rSP] eBP High Level Procedure Exit
                            op_16_LEAVE();
                            break EXEC_LOOP;
                        case 0x106://PUSH ES SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0x10e://PUSH CS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0x116://PUSH SS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                        case 0x11e://PUSH DS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                            push_word_to_stack(cpu.segs[(OPbyte >> 3) & 3].selector);
                            break EXEC_LOOP;
                        case 0x107://POP SS:[rSP] ES Pop a Value from the Stack
                        case 0x117://POP SS:[rSP] SS Pop a Value from the Stack
                        case 0x11f://POP SS:[rSP] DS Pop a Value from the Stack
                            set_segment_register((OPbyte >> 3) & 3, pop_word_from_stack_read());
                            pop_word_from_stack_incr_ptr();
                            break EXEC_LOOP;
                        case 0x18d://LEA M Gvqp Load Effective Address
                            mem8 = phys_mem8[physmem8_ptr++];
                            if ((mem8 >> 6) == 3)
                                abort(6);
                            CS_flags = (CS_flags & ~0x000f) | (6 + 1);
                            set_lower_word_in_register((mem8 >> 3) & 7, segment_translation(mem8));
                            break EXEC_LOOP;
                        case 0x1ff://INC  Evqp Increment by 1
                            mem8 = phys_mem8[physmem8_ptr++];
                            conditional_var = (mem8 >> 3) & 7;
                            switch (conditional_var) {
                                case 0:
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, increment_16bit(regs[reg_idx0]));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        x = increment_16bit(x);
                                        st16_mem8_write(x);
                                    }
                                    break;
                                case 1:
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, decrement_16bit(regs[reg_idx0]));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        x = decrement_16bit(x);
                                        st16_mem8_write(x);
                                    }
                                    break;
                                case 2:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7] & 0xffff;
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    push_word_to_stack((eip + physmem8_ptr - initial_mem_ptr));
                                    eip = x, physmem8_ptr = initial_mem_ptr = 0;
                                    break;
                                case 4:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7] & 0xffff;
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    eip = x, physmem8_ptr = initial_mem_ptr = 0;
                                    break;
                                case 6:
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    push_word_to_stack(x);
                                    break;
                                case 3:
                                case 5:
                                    if ((mem8 >> 6) == 3)
                                        abort(6);
                                    mem8_loc = segment_translation(mem8);
                                    x = ld_16bits_mem8_read();
                                    mem8_loc = (mem8_loc + 2) >> 0;
                                    y = ld_16bits_mem8_read();
                                    if (conditional_var == 3)
                                        op_CALLF(0, y, x, (eip + physmem8_ptr - initial_mem_ptr));
                                    else
                                        op_JMPF(y, x);
                                    break;
                                default:
                                    abort(6);
                            }
                            break EXEC_LOOP;
                        case 0x1eb://JMP Jbs  Jump
                            x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x1e9://JMP Jvds  Jump
                            x = ld16_mem8_direct();
                            eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x170://JO Jbs  Jump short if overflow (OF=1)
                        case 0x171://JNO Jbs  Jump short if not overflow (OF=0)
                        case 0x172://JB Jbs  Jump short if below/not above or equal/carry (CF=1)
                        case 0x173://JNB Jbs  Jump short if not below/above or equal/not carry (CF=0)
                        case 0x174://JZ Jbs  Jump short if zero/equal (ZF=0)
                        case 0x175://JNZ Jbs  Jump short if not zero/not equal (ZF=1)
                        case 0x176://JBE Jbs  Jump short if below or equal/not above (CF=1 AND ZF=1)
                        case 0x177://JNBE Jbs  Jump short if not below or equal/above (CF=0 AND ZF=0)
                        case 0x178://JS Jbs  Jump short if sign (SF=1)
                        case 0x179://JNS Jbs  Jump short if not sign (SF=0)
                        case 0x17a://JP Jbs  Jump short if parity/parity even (PF=1)
                        case 0x17b://JNP Jbs  Jump short if not parity/parity odd
                        case 0x17c://JL Jbs  Jump short if less/not greater (SF!=OF)
                        case 0x17d://JNL Jbs  Jump short if not less/greater or equal (SF=OF)
                        case 0x17e://JLE Jbs  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                        case 0x17f://JNLE Jbs  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                            x = ((phys_mem8[physmem8_ptr++] << 24) >> 24);
                            y = check_status_bits_for_jump(OPbyte & 0xf);
                            if (y)
                                eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x1c2://RETN SS:[rSP]  Return from procedure
                            y = (ld16_mem8_direct() << 16) >> 16;
                            x = pop_word_from_stack_read();
                            regs[4] = (regs[4] & ~SS_mask) | ((regs[4] + 2 + y) & SS_mask);
                            eip = x, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x1c3://RETN SS:[rSP]  Return from procedure
                            x = pop_word_from_stack_read();
                            pop_word_from_stack_incr_ptr();
                            eip = x, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x1e8://CALL Jvds SS:[rSP] Call Procedure
                            x = ld16_mem8_direct();
                            push_word_to_stack((eip + physmem8_ptr - initial_mem_ptr));
                            eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                            break EXEC_LOOP;
                        case 0x162://BOUND Gv SS:[rSP] Check Array Index Against Bounds
                            op_16_BOUND();
                            break EXEC_LOOP;
                        case 0x1a5://MOVS DS:[SI] ES:[DI] Move Data from String to String
                            op_16_MOVS();
                            break EXEC_LOOP;
                        case 0x1a7://CMPS ES:[DI]  Compare String Operands
                            op_16_CMPS();
                            break EXEC_LOOP;
                        case 0x1ad://LODS DS:[SI] AX Load String
                            op_16_LODS();
                            break EXEC_LOOP;
                        case 0x1af://SCAS ES:[DI]  Scan String
                            op_16_SCAS();
                            break EXEC_LOOP;
                        case 0x1ab://STOS AX ES:[DI] Store String
                            op_16_STOS();
                            break EXEC_LOOP;
                        case 0x16d://INS DX ES:[DI] Input from Port to String
                            op_16_INS();
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x16f://OUTS DS:[SI] DX Output String to Port
                            op_16_OUTS();
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x1e5://IN Ib eAX Input from Port
                            iopl = (cpu.eflags >> 12) & 3;
                            if (cpu.cpl > iopl)
                                abort(13);
                            x = phys_mem8[physmem8_ptr++];
                            set_lower_word_in_register(0, cpu.ld16_port(x));
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x1e7://OUT eAX Ib Output to Port
                            iopl = (cpu.eflags >> 12) & 3;
                            if (cpu.cpl > iopl)
                                abort(13);
                            x = phys_mem8[physmem8_ptr++];
                            cpu.st16_port(x, regs[0] & 0xffff);
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x1ed://IN DX eAX Input from Port
                            iopl = (cpu.eflags >> 12) & 3;
                            if (cpu.cpl > iopl)
                                abort(13);
                            set_lower_word_in_register(0, cpu.ld16_port(regs[2] & 0xffff));
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x1ef://OUT eAX DX Output to Port
                            iopl = (cpu.eflags >> 12) & 3;
                            if (cpu.cpl > iopl)
                                abort(13);
                            cpu.st16_port(regs[2] & 0xffff, regs[0] & 0xffff);
                            {
                                if (cpu.hard_irq != 0 && (cpu.eflags & 0x00000200))
                                    break OUTER_LOOP;
                            }
                            break EXEC_LOOP;
                        case 0x166://   Operand-size override prefix
                        case 0x167://   Address-size override prefix
                        case 0x1f0://LOCK   Assert LOCK# Signal Prefix
                        case 0x1f2://REPNZ  eCX Repeat String Operation Prefix
                        case 0x1f3://REPZ  eCX Repeat String Operation Prefix
                        case 0x126://ES ES  ES segment override prefix
                        case 0x12e://CS CS  CS segment override prefix
                        case 0x136://SS SS  SS segment override prefix
                        case 0x13e://DS DS  DS segment override prefix
                        case 0x164://FS FS  FS segment override prefix
                        case 0x165://GS GS  GS segment override prefix
                        case 0x100://ADD Gb Eb Add
                        case 0x108://OR Gb Eb Logical Inclusive OR
                        case 0x110://ADC Gb Eb Add with Carry
                        case 0x118://SBB Gb Eb Integer Subtraction with Borrow
                        case 0x120://AND Gb Eb Logical AND
                        case 0x128://SUB Gb Eb Subtract
                        case 0x130://XOR Gb Eb Logical Exclusive OR
                        case 0x138://CMP Eb  Compare Two Operands
                        case 0x102://ADD Eb Gb Add
                        case 0x10a://OR Eb Gb Logical Inclusive OR
                        case 0x112://ADC Eb Gb Add with Carry
                        case 0x11a://SBB Eb Gb Integer Subtraction with Borrow
                        case 0x122://AND Eb Gb Logical AND
                        case 0x12a://SUB Eb Gb Subtract
                        case 0x132://XOR Eb Gb Logical Exclusive OR
                        case 0x13a://CMP Gb  Compare Two Operands
                        case 0x104://ADD Ib AL Add
                        case 0x10c://OR Ib AL Logical Inclusive OR
                        case 0x114://ADC Ib AL Add with Carry
                        case 0x11c://SBB Ib AL Integer Subtraction with Borrow
                        case 0x124://AND Ib AL Logical AND
                        case 0x12c://SUB Ib AL Subtract
                        case 0x134://XOR Ib AL Logical Exclusive OR
                        case 0x13c://CMP AL  Compare Two Operands
                        case 0x1a0://MOV Ob AL Move
                        case 0x1a2://MOV AL Ob Move
                        case 0x1d8://FADD Msr ST Add
                        case 0x1d9://FLD ESsr ST Load Floating Point Value
                        case 0x1da://FIADD Mdi ST Add
                        case 0x1db://FILD Mdi ST Load Integer
                        case 0x1dc://FADD Mdr ST Add
                        case 0x1dd://FLD Mdr ST Load Floating Point Value
                        case 0x1de://FIADD Mwi ST Add
                        case 0x1df://FILD Mwi ST Load Integer
                        case 0x184://TEST Eb  Logical Compare
                        case 0x1a8://TEST AL  Logical Compare
                        case 0x1f6://TEST Eb  Logical Compare
                        case 0x1c0://ROL Ib Eb Rotate
                        case 0x1d0://ROL 1 Eb Rotate
                        case 0x1d2://ROL CL Eb Rotate
                        case 0x1fe://INC  Eb Increment by 1
                        case 0x1cd://INT Ib SS:[rSP] Call to Interrupt Procedure
                        case 0x1ce://INTO eFlags SS:[rSP] Call to Interrupt Procedure
                        case 0x1f5://CMC   Complement Carry Flag
                        case 0x1f8://CLC   Clear Carry Flag
                        case 0x1f9://STC   Set Carry Flag
                        case 0x1fc://CLD   Clear Direction Flag
                        case 0x1fd://STD   Set Direction Flag
                        case 0x1fa://CLI   Clear Interrupt Flag
                        case 0x1fb://STI   Set Interrupt Flag
                        case 0x19e://SAHF AH  Store AH into Flags
                        case 0x19f://LAHF  AH Load Status Flags into AH Register
                        case 0x1f4://HLT   Halt
                        case 0x127://DAA  AL Decimal Adjust AL after Addition
                        case 0x12f://DAS  AL Decimal Adjust AL after Subtraction
                        case 0x137://AAA  AL ASCII Adjust After Addition
                        case 0x13f://AAS  AL ASCII Adjust AL After Subtraction
                        case 0x1d4://AAM  AL ASCII Adjust AX After Multiply
                        case 0x1d5://AAD  AL ASCII Adjust AX Before Division
                        case 0x16c://INS DX (ES:)[rDI] Input from Port to String
                        case 0x16e://OUTS (DS):[rSI] DX Output String to Port
                        case 0x1a4://MOVS (DS:)[rSI] (ES:)[rDI] Move Data from String to String
                        case 0x1a6://CMPS (ES:)[rDI]  Compare String Operands
                        case 0x1aa://STOS AL (ES:)[rDI] Store String
                        case 0x1ac://LODS (DS:)[rSI] AL Load String
                        case 0x1ae://SCAS (ES:)[rDI]  Scan String
                        case 0x180://ADD Ib Eb Add
                        case 0x182://ADD Ib Eb Add
                        case 0x186://XCHG  Gb Exchange Register/Memory with Register
                        case 0x188://MOV Gb Eb Move
                        case 0x18a://MOV Eb Gb Move
                        case 0x18c://MOV Sw Mw Move
                        case 0x18e://MOV Ew Sw Move
                        case 0x19b://FWAIT   Check pending unmasked floating-point exceptions
                        case 0x1b0://MOV Ib Zb Move
                        case 0x1b1:
                        case 0x1b2:
                        case 0x1b3:
                        case 0x1b4:
                        case 0x1b5:
                        case 0x1b6:
                        case 0x1b7:
                        case 0x1c6://MOV Ib Eb Move
                        case 0x1cc://INT 3 SS:[rSP] Call to Interrupt Procedure
                        case 0x1d7://XLAT (DS:)[rBX+AL] AL Table Look-up Translation
                        case 0x1e4://IN Ib AL Input from Port
                        case 0x1e6://OUT AL Ib Output to Port
                        case 0x1ec://IN DX AL Input from Port
                        case 0x1ee://OUT AL DX Output to Port
                        case 0x1cf://IRET SS:[rSP] Flags Interrupt Return
                        case 0x1ca://RETF Iw  Return from procedure
                        case 0x1cb://RETF SS:[rSP]  Return from procedure
                        case 0x19a://CALLF Ap SS:[rSP] Call Procedure
                        case 0x19c://PUSHF Flags SS:[rSP] Push FLAGS Register onto the Stack
                        case 0x19d://POPF SS:[rSP] Flags Pop Stack into FLAGS Register
                        case 0x1ea://JMPF Ap  Jump
                        case 0x1e0://LOOPNZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=0
                        case 0x1e1://LOOPZ Jbs eCX Decrement count; Jump short if count!=0 and ZF=1
                        case 0x1e2://LOOP Jbs eCX Decrement count; Jump short if count!=0
                        case 0x1e3://JCXZ Jbs  Jump short if eCX register is 0
                            OPbyte &= 0xff;
                            break;
                        case 0x163://ARPL Ew  Adjust RPL Field of Segment Selector
                        case 0x1d6://SALC   Undefined and Reserved; Does not Generate #UD
                        case 0x1f1://INT1   Undefined and Reserved; Does not Generate #UD
                        default:
                            abort(6);

                        /*
                           two byte instructions
                           ================================================================================
                         */

                        case 0x10f:
                            OPbyte = phys_mem8[physmem8_ptr++];
                            OPbyte |= 0x0100;
                            switch (OPbyte) {
                                case 0x180://JO Jvds  Jump short if overflow (OF=1)
                                case 0x181://JNO Jvds  Jump short if not overflow (OF=0)
                                case 0x182://JB Jvds  Jump short if below/not above or equal/carry (CF=1)
                                case 0x183://JNB Jvds  Jump short if not below/above or equal/not carry (CF=0)
                                case 0x184://JZ Jvds  Jump short if zero/equal (ZF=0)
                                case 0x185://JNZ Jvds  Jump short if not zero/not equal (ZF=1)
                                case 0x186://JBE Jvds  Jump short if below or equal/not above (CF=1 AND ZF=1)
                                case 0x187://JNBE Jvds  Jump short if not below or equal/above (CF=0 AND ZF=0)
                                case 0x188://JS Jvds  Jump short if sign (SF=1)
                                case 0x189://JNS Jvds  Jump short if not sign (SF=0)
                                case 0x18a://JP Jvds  Jump short if parity/parity even (PF=1)
                                case 0x18b://JNP Jvds  Jump short if not parity/parity odd
                                case 0x18c://JL Jvds  Jump short if less/not greater (SF!=OF)
                                case 0x18d://JNL Jvds  Jump short if not less/greater or equal (SF=OF)
                                case 0x18e://JLE Jvds  Jump short if less or equal/not greater ((ZF=1) OR (SF!=OF))
                                case 0x18f://JNLE Jvds  Jump short if not less nor equal/greater ((ZF=0) AND (SF=OF))
                                    x = ld16_mem8_direct();
                                    if (check_status_bits_for_jump(OPbyte & 0xf))
                                        eip = (eip + physmem8_ptr - initial_mem_ptr + x) & 0xffff, physmem8_ptr = initial_mem_ptr = 0;
                                    break EXEC_LOOP;
                                case 0x140://CMOVO Evqp Gvqp Conditional Move - overflow (OF=1)
                                case 0x141://CMOVNO Evqp Gvqp Conditional Move - not overflow (OF=0)
                                case 0x142://CMOVB Evqp Gvqp Conditional Move - below/not above or equal/carry (CF=1)
                                case 0x143://CMOVNB Evqp Gvqp Conditional Move - not below/above or equal/not carry (CF=0)
                                case 0x144://CMOVZ Evqp Gvqp Conditional Move - zero/equal (ZF=0)
                                case 0x145://CMOVNZ Evqp Gvqp Conditional Move - not zero/not equal (ZF=1)
                                case 0x146://CMOVBE Evqp Gvqp Conditional Move - below or equal/not above (CF=1 AND ZF=1)
                                case 0x147://CMOVNBE Evqp Gvqp Conditional Move - not below or equal/above (CF=0 AND ZF=0)
                                case 0x148://CMOVS Evqp Gvqp Conditional Move - sign (SF=1)
                                case 0x149://CMOVNS Evqp Gvqp Conditional Move - not sign (SF=0)
                                case 0x14a://CMOVP Evqp Gvqp Conditional Move - parity/parity even (PF=1)
                                case 0x14b://CMOVNP Evqp Gvqp Conditional Move - not parity/parity odd
                                case 0x14c://CMOVL Evqp Gvqp Conditional Move - less/not greater (SF!=OF)
                                case 0x14d://CMOVNL Evqp Gvqp Conditional Move - not less/greater or equal (SF=OF)
                                case 0x14e://CMOVLE Evqp Gvqp Conditional Move - less or equal/not greater ((ZF=1) OR (SF!=OF))
                                case 0x14f://CMOVNLE Evqp Gvqp Conditional Move - not less nor equal/greater ((ZF=0) AND (SF=OF))
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_read();
                                    }
                                    if (check_status_bits_for_jump(OPbyte & 0xf))
                                        set_lower_word_in_register((mem8 >> 3) & 7, x);
                                    break EXEC_LOOP;
                                case 0x1b6://MOVZX Eb Gvqp Move with Zero-Extend
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1)) & 0xff;
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_8bits_mem8_read();
                                    }
                                    set_lower_word_in_register(reg_idx1, x);
                                    break EXEC_LOOP;
                                case 0x1be://MOVSX Eb Gvqp Move with Sign-Extension
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        x = (regs[reg_idx0 & 3] >> ((reg_idx0 & 4) << 1));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_8bits_mem8_read();
                                    }
                                    set_lower_word_in_register(reg_idx1, (((x) << 24) >> 24));
                                    break EXEC_LOOP;
                                case 0x1af://IMUL Evqp Gvqp Signed Multiply
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        y = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        y = ld_16bits_mem8_read();
                                    }
                                    set_lower_word_in_register(reg_idx1, op_16_IMUL(regs[reg_idx1], y));
                                    break EXEC_LOOP;
                                case 0x1c1://XADD  Evqp Exchange and Add
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        x = regs[reg_idx0];
                                        y = do_16bit_math(0, x, regs[reg_idx1]);
                                        set_lower_word_in_register(reg_idx1, x);
                                        set_lower_word_in_register(reg_idx0, y);
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        y = do_16bit_math(0, x, regs[reg_idx1]);
                                        st16_mem8_write(y);
                                        set_lower_word_in_register(reg_idx1, x);
                                    }
                                    break EXEC_LOOP;
                                case 0x1a0://PUSH FS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                                case 0x1a8://PUSH GS SS:[rSP] Push Word, Doubleword or Quadword Onto the Stack
                                    push_word_to_stack(cpu.segs[(OPbyte >> 3) & 7].selector);
                                    break EXEC_LOOP;
                                case 0x1a1://POP SS:[rSP] FS Pop a Value from the Stack
                                case 0x1a9://POP SS:[rSP] GS Pop a Value from the Stack
                                    set_segment_register((OPbyte >> 3) & 7, pop_word_from_stack_read());
                                    pop_word_from_stack_incr_ptr();
                                    break EXEC_LOOP;
                                case 0x1b2://LSS Mptp SS Load Far Pointer
                                case 0x1b4://LFS Mptp FS Load Far Pointer
                                case 0x1b5://LGS Mptp GS Load Far Pointer
                                    op_16_load_far_pointer16(OPbyte & 7);
                                    break EXEC_LOOP;
                                case 0x1a4://SHLD Gvqp Evqp Double Precision Shift Left
                                case 0x1ac://SHRD Gvqp Evqp Double Precision Shift Right
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    y = regs[(mem8 >> 3) & 7];
                                    conditional_var = (OPbyte >> 3) & 1;
                                    if ((mem8 >> 6) == 3) {
                                        z = phys_mem8[physmem8_ptr++];
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, op_16_SHRD_SHLD(conditional_var, regs[reg_idx0], y, z));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        z = phys_mem8[physmem8_ptr++];
                                        x = ld_16bits_mem8_write();
                                        x = op_16_SHRD_SHLD(conditional_var, x, y, z);
                                        st16_mem8_write(x);
                                    }
                                    break EXEC_LOOP;
                                case 0x1a5://SHLD Gvqp Evqp Double Precision Shift Left
                                case 0x1ad://SHRD Gvqp Evqp Double Precision Shift Right
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    y = regs[(mem8 >> 3) & 7];
                                    z = regs[1];
                                    conditional_var = (OPbyte >> 3) & 1;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, op_16_SHRD_SHLD(conditional_var, regs[reg_idx0], y, z));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        x = op_16_SHRD_SHLD(conditional_var, x, y, z);
                                        st16_mem8_write(x);
                                    }
                                    break EXEC_LOOP;
                                case 0x1ba://BT Evqp  Bit Test
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    conditional_var = (mem8 >> 3) & 7;
                                    switch (conditional_var) {
                                        case 4:
                                            if ((mem8 >> 6) == 3) {
                                                x = regs[mem8 & 7];
                                                y = phys_mem8[physmem8_ptr++];
                                            } else {
                                                mem8_loc = segment_translation(mem8);
                                                y = phys_mem8[physmem8_ptr++];
                                                x = ld_16bits_mem8_read();
                                            }
                                            op_16_BT(x, y);
                                            break;
                                        case 5:
                                        case 6:
                                        case 7:
                                            if ((mem8 >> 6) == 3) {
                                                reg_idx0 = mem8 & 7;
                                                y = phys_mem8[physmem8_ptr++];
                                                regs[reg_idx0] = op_16_BTS_BTR_BTC(conditional_var & 3, regs[reg_idx0], y);
                                            } else {
                                                mem8_loc = segment_translation(mem8);
                                                y = phys_mem8[physmem8_ptr++];
                                                x = ld_16bits_mem8_write();
                                                x = op_16_BTS_BTR_BTC(conditional_var & 3, x, y);
                                                st16_mem8_write(x);
                                            }
                                            break;
                                        default:
                                            abort(6);
                                    }
                                    break EXEC_LOOP;
                                case 0x1a3://BT Evqp  Bit Test
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    y = regs[(mem8 >> 3) & 7];
                                    if ((mem8 >> 6) == 3) {
                                        x = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        mem8_loc = (mem8_loc + (((y & 0xffff) >> 4) << 1)) >> 0;
                                        x = ld_16bits_mem8_read();
                                    }
                                    op_16_BT(x, y);
                                    break EXEC_LOOP;
                                case 0x1ab://BTS Gvqp Evqp Bit Test and Set
                                case 0x1b3://BTR Gvqp Evqp Bit Test and Reset
                                case 0x1bb://BTC Gvqp Evqp Bit Test and Complement
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    y = regs[(mem8 >> 3) & 7];
                                    conditional_var = (OPbyte >> 3) & 3;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        set_lower_word_in_register(reg_idx0, op_16_BTS_BTR_BTC(conditional_var, regs[reg_idx0], y));
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        mem8_loc = (mem8_loc + (((y & 0xffff) >> 4) << 1)) >> 0;
                                        x = ld_16bits_mem8_write();
                                        x = op_16_BTS_BTR_BTC(conditional_var, x, y);
                                        st16_mem8_write(x);
                                    }
                                    break EXEC_LOOP;
                                case 0x1bc://BSF Evqp Gvqp Bit Scan Forward
                                case 0x1bd://BSR Evqp Gvqp Bit Scan Reverse
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        y = regs[mem8 & 7];
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        y = ld_16bits_mem8_read();
                                    }
                                    x = regs[reg_idx1];
                                    if (OPbyte & 1)
                                        x = op_16_BSR(x, y);
                                    else
                                        x = op_16_BSF(x, y);
                                    set_lower_word_in_register(reg_idx1, x);
                                    break EXEC_LOOP;
                                case 0x1b1://CMPXCHG Gvqp Evqp Compare and Exchange
                                    mem8 = phys_mem8[physmem8_ptr++];
                                    reg_idx1 = (mem8 >> 3) & 7;
                                    if ((mem8 >> 6) == 3) {
                                        reg_idx0 = mem8 & 7;
                                        x = regs[reg_idx0];
                                        y = do_16bit_math(5, regs[0], x);
                                        if (y == 0) {
                                            set_lower_word_in_register(reg_idx0, regs[reg_idx1]);
                                        } else {
                                            set_lower_word_in_register(0, x);
                                        }
                                    } else {
                                        mem8_loc = segment_translation(mem8);
                                        x = ld_16bits_mem8_write();
                                        y = do_16bit_math(5, regs[0], x);
                                        if (y == 0) {
                                            st16_mem8_write(regs[reg_idx1]);
                                        } else {
                                            set_lower_word_in_register(0, x);
                                        }
                                    }
                                    break EXEC_LOOP;
                                case 0x100://SLDT LDTR Mw Store Local Descriptor Table Register
                                case 0x101://SGDT GDTR Ms Store Global Descriptor Table Register
                                case 0x102://LAR Mw Gvqp Load Access Rights Byte
                                case 0x103://LSL Mw Gvqp Load Segment Limit
                                case 0x120://MOV Cd Rd Move to/from Control Registers
                                case 0x122://MOV Rd Cd Move to/from Control Registers
                                case 0x106://CLTS  CR0 Clear Task-Switched Flag in CR0
                                case 0x123://MOV Rd Dd Move to/from Debug Registers
                                case 0x1a2://CPUID  IA32_BIOS_SIGN_ID CPU Identification
                                case 0x131://RDTSC IA32_TIME_STAMP_COUNTER EAX Read Time-Stamp Counter
                                case 0x190://SETO  Eb Set Byte on Condition - overflow (OF=1)
                                case 0x191://SETNO  Eb Set Byte on Condition - not overflow (OF=0)
                                case 0x192://SETB  Eb Set Byte on Condition - below/not above or equal/carry (CF=1)
                                case 0x193://SETNB  Eb Set Byte on Condition - not below/above or equal/not carry (CF=0)
                                case 0x194://SETZ  Eb Set Byte on Condition - zero/equal (ZF=0)
                                case 0x195://SETNZ  Eb Set Byte on Condition - not zero/not equal (ZF=1)
                                case 0x196://SETBE  Eb Set Byte on Condition - below or equal/not above (CF=1 AND ZF=1)
                                case 0x197://SETNBE  Eb Set Byte on Condition - not below or equal/above (CF=0 AND ZF=0)
                                case 0x198://SETS  Eb Set Byte on Condition - sign (SF=1)
                                case 0x199://SETNS  Eb Set Byte on Condition - not sign (SF=0)
                                case 0x19a://SETP  Eb Set Byte on Condition - parity/parity even (PF=1)
                                case 0x19b://SETNP  Eb Set Byte on Condition - not parity/parity odd
                                case 0x19c://SETL  Eb Set Byte on Condition - less/not greater (SF!=OF)
                                case 0x19d://SETNL  Eb Set Byte on Condition - not less/greater or equal (SF=OF)
                                case 0x19e://SETLE  Eb Set Byte on Condition - less or equal/not greater ((ZF=1) OR (SF!=OF))
                                case 0x19f://SETNLE  Eb Set Byte on Condition - not less nor equal/greater ((ZF=0) AND (SF=OF))
                                case 0x1b0://CMPXCHG Gb Eb Compare and Exchange
                                    OPbyte = 0x0f;
                                    physmem8_ptr--;
                                    break;
                                case 0x104:
                                case 0x105://LOADALL  AX Load All of the CPU Registers
                                case 0x107://LOADALL  EAX Load All of the CPU Registers
                                case 0x108://INVD   Invalidate Internal Caches
                                case 0x109://WBINVD   Write Back and Invalidate Cache
                                case 0x10a:
                                case 0x10b://UD2   Undefined Instruction
                                case 0x10c:
                                case 0x10d://NOP Ev  No Operation
                                case 0x10e:
                                case 0x10f:
                                case 0x110://MOVUPS Wps Vps Move Unaligned Packed Single-FP Values
                                case 0x111://MOVUPS Vps Wps Move Unaligned Packed Single-FP Values
                                case 0x112://MOVHLPS Uq Vq Move Packed Single-FP Values High to Low
                                case 0x113://MOVLPS Vq Mq Move Low Packed Single-FP Values
                                case 0x114://UNPCKLPS Wq Vps Unpack and Interleave Low Packed Single-FP Values
                                case 0x115://UNPCKHPS Wq Vps Unpack and Interleave High Packed Single-FP Values
                                case 0x116://MOVLHPS Uq Vq Move Packed Single-FP Values Low to High
                                case 0x117://MOVHPS Vq Mq Move High Packed Single-FP Values
                                case 0x118://HINT_NOP Ev  Hintable NOP
                                case 0x119://HINT_NOP Ev  Hintable NOP
                                case 0x11a://HINT_NOP Ev  Hintable NOP
                                case 0x11b://HINT_NOP Ev  Hintable NOP
                                case 0x11c://HINT_NOP Ev  Hintable NOP
                                case 0x11d://HINT_NOP Ev  Hintable NOP
                                case 0x11e://HINT_NOP Ev  Hintable NOP
                                case 0x11f://HINT_NOP Ev  Hintable NOP
                                case 0x121://MOV Dd Rd Move to/from Debug Registers
                                case 0x124://MOV Td Rd Move to/from Test Registers
                                case 0x125:
                                case 0x126://MOV Rd Td Move to/from Test Registers
                                case 0x127:
                                case 0x128://MOVAPS Wps Vps Move Aligned Packed Single-FP Values
                                case 0x129://MOVAPS Vps Wps Move Aligned Packed Single-FP Values
                                case 0x12a://CVTPI2PS Qpi Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                                case 0x12b://MOVNTPS Vps Mps Store Packed Single-FP Values Using Non-Temporal Hint
                                case 0x12c://CVTTPS2PI Wpsq Ppi Convert with Trunc. Packed Single-FP Values to1.11 PackedDW Integers
                                case 0x12d://CVTPS2PI Wpsq Ppi Convert Packed Single-FP Values to1.11 PackedDW Integers
                                case 0x12e://UCOMISS Vss  Unordered Compare Scalar Single-FP Values and Set EFLAGS
                                case 0x12f://COMISS Vss  Compare Scalar Ordered Single-FP Values and Set EFLAGS
                                case 0x130://WRMSR rCX MSR Write to Model Specific Register
                                case 0x132://RDMSR rCX rAX Read from Model Specific Register
                                case 0x133://RDPMC PMC EAX Read Performance-Monitoring Counters
                                case 0x134://SYSENTER IA32_SYSENTER_CS SS Fast System Call
                                case 0x135://SYSEXIT IA32_SYSENTER_CS SS Fast Return from Fast System Call
                                case 0x136:
                                case 0x137://GETSEC EAX  GETSEC Leaf Functions
                                case 0x138://PSHUFB Qq Pq Packed Shuffle Bytes
                                case 0x139:
                                case 0x13a://ROUNDPS Wps Vps Round Packed Single-FP Values
                                case 0x13b:
                                case 0x13c:
                                case 0x13d:
                                case 0x13e:
                                case 0x13f:
                                case 0x150://MOVMSKPS Ups Gdqp Extract Packed Single-FP Sign Mask
                                case 0x151://SQRTPS Wps Vps Compute Square Roots of Packed Single-FP Values
                                case 0x152://RSQRTPS Wps Vps Compute Recipr. of Square Roots of Packed Single-FP Values
                                case 0x153://RCPPS Wps Vps Compute Reciprocals of Packed Single-FP Values
                                case 0x154://ANDPS Wps Vps Bitwise Logical AND of Packed Single-FP Values
                                case 0x155://ANDNPS Wps Vps Bitwise Logical AND NOT of Packed Single-FP Values
                                case 0x156://ORPS Wps Vps Bitwise Logical OR of Single-FP Values
                                case 0x157://XORPS Wps Vps Bitwise Logical XOR for Single-FP Values
                                case 0x158://ADDPS Wps Vps Add Packed Single-FP Values
                                case 0x159://MULPS Wps Vps Multiply Packed Single-FP Values
                                case 0x15a://CVTPS2PD Wps Vpd Convert Packed Single-FP Values to1.11 PackedDouble-FP Values
                                case 0x15b://CVTDQ2PS Wdq Vps Convert Packed DW Integers to1.11 PackedSingle-FP Values
                                case 0x15c://SUBPS Wps Vps Subtract Packed Single-FP Values
                                case 0x15d://MINPS Wps Vps Return Minimum Packed Single-FP Values
                                case 0x15e://DIVPS Wps Vps Divide Packed Single-FP Values
                                case 0x15f://MAXPS Wps Vps Return Maximum Packed Single-FP Values
                                case 0x160://PUNPCKLBW Qd Pq Unpack Low Data
                                case 0x161://PUNPCKLWD Qd Pq Unpack Low Data
                                case 0x162://PUNPCKLDQ Qd Pq Unpack Low Data
                                case 0x163://PACKSSWB Qd Pq Pack with Signed Saturation
                                case 0x164://PCMPGTB Qd Pq Compare Packed Signed Integers for Greater Than
                                case 0x165://PCMPGTW Qd Pq Compare Packed Signed Integers for Greater Than
                                case 0x166://PCMPGTD Qd Pq Compare Packed Signed Integers for Greater Than
                                case 0x167://PACKUSWB Qq Pq Pack with Unsigned Saturation
                                case 0x168://PUNPCKHBW Qq Pq Unpack High Data
                                case 0x169://PUNPCKHWD Qq Pq Unpack High Data
                                case 0x16a://PUNPCKHDQ Qq Pq Unpack High Data
                                case 0x16b://PACKSSDW Qq Pq Pack with Signed Saturation
                                case 0x16c://PUNPCKLQDQ Wdq Vdq Unpack Low Data
                                case 0x16d://PUNPCKHQDQ Wdq Vdq Unpack High Data
                                case 0x16e://MOVD Ed Pq Move Doubleword
                                case 0x16f://MOVQ Qq Pq Move Quadword
                                case 0x170://PSHUFW Qq Pq Shuffle Packed Words
                                case 0x171://PSRLW Ib Nq Shift Packed Data Right Logical
                                case 0x172://PSRLD Ib Nq Shift Double Quadword Right Logical
                                case 0x173://PSRLQ Ib Nq Shift Packed Data Right Logical
                                case 0x174://PCMPEQB Qq Pq Compare Packed Data for Equal
                                case 0x175://PCMPEQW Qq Pq Compare Packed Data for Equal
                                case 0x176://PCMPEQD Qq Pq Compare Packed Data for Equal
                                case 0x177://EMMS   Empty MMX Technology State
                                case 0x178://VMREAD Gd Ed Read Field from Virtual-Machine Control Structure
                                case 0x179://VMWRITE Gd  Write Field to Virtual-Machine Control Structure
                                case 0x17a:
                                case 0x17b:
                                case 0x17c://HADDPD Wpd Vpd Packed Double-FP Horizontal Add
                                case 0x17d://HSUBPD Wpd Vpd Packed Double-FP Horizontal Subtract
                                case 0x17e://MOVD Pq Ed Move Doubleword
                                case 0x17f://MOVQ Pq Qq Move Quadword
                                case 0x1a6:
                                case 0x1a7:
                                case 0x1aa://RSM  Flags Resume from System Management Mode
                                case 0x1ae://FXSAVE ST Mstx Save x87 FPU, MMX, XMM, and MXCSR State
                                case 0x1b7://MOVZX Ew Gvqp Move with Zero-Extend
                                case 0x1b8://JMPE   Jump to IA-64 Instruction Set
                                case 0x1b9://UD G  Undefined Instruction
                                case 0x1bf://MOVSX Ew Gvqp Move with Sign-Extension
                                case 0x1c0://XADD  Eb Exchange and Add
                                default:
                                    abort(6);
                            }
                            break;
                    }
            }
        }
    } while (--cycles_left); //End Giant Core DO WHILE Execution Loop
    this.cycle_count += (N_cycles - cycles_left);
    this.eip           = (eip + physmem8_ptr - initial_mem_ptr);
    this.cc_src        = _src;
    this.cc_dst        = _dst;
    this.cc_op         = _op;
    this.cc_op2        = _op2;
    this.cc_dst2       = _dst2;
    return exit_code;
};


/*
  Execution Wrapper
  ==========================================================================================
  This seems to primarily catch internal interrupts.
*/

CPU_X86.prototype.exec = function(N_cycles) {
    var exit_code, final_cycle_count, interrupt;
    final_cycle_count = this.cycle_count + N_cycles;
    exit_code = 256;
    interrupt = null;
    while (this.cycle_count < final_cycle_count) {
        try {
            exit_code = this.exec_internal(final_cycle_count - this.cycle_count, interrupt);
            if (exit_code != 256)
                break;
            interrupt = null;
        } catch (cpu_exception) {
            if (cpu_exception.hasOwnProperty("intno")) { //an interrupt
                interrupt = cpu_exception;
            } else {
                throw cpu_exception;
            }
        }
    }
    return exit_code;
};


/*
  Binary Loader
  ==========================================================================================
  This routine loads a binary array into memory.
*/

CPU_X86.prototype.load_binary = function(binary_array, mem8_loc) {
  var len, i, typed_binary_array;
  len = binary_array.byteLength;
  typed_binary_array = new Uint8Array(binary_array, 0, len);
  for (i = 0; i < len; i++) {
    this.st8_phys(mem8_loc + i, typed_binary_array[i]);
  }
  return len;
};