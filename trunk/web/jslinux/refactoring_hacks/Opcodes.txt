package scale.backend.x86;

/**
 * The Intel X86 opcodes.
 * <p>
 * $Id$
 * <p>
 * Copyright 2008 by James H. Burrill<br>
 * All Rights Reserved.<br>
 */

public final class Opcodes
{
  // The opcode value has several fields:
  //  +------------------+----+----+----------+
  //  |      F           | M  | S  |     O    |
  //  +------------------+----+----+----------+
  //
  //  F: form flags indicating legal forms
  //  M: Scaling factor used in some addressing forms
  //  S: Operand size
  //  O: instruction opcode

  /**
   * Mask for the instruction opcode.
   */
  public static final int O_MASK   = 0x000003ff;
  /**
   * Mask for the instruction opcode.
   */
  public static final int O_SHIFT  = 0;
  /**
   * Mask for the operand size.
   */
  public static final int S_MASK   = 0x00000c00;
  /**
   * Shift for the operand size.
   */
  public static final int S_SHIFT  = 10;
  /**
   * Operand size - byte.
   */
  public static final int S_BYTE   = 0x00000000;
  /**
   * Operand size - short.
   */
  public static final int S_SHORT  = 0x00000400;
  /**
   * Operand size - int.
   */
  public static final int S_INT    = 0x00000800;
  /**
   * Operand size - LONG.
   */
  public static final int S_LONG   = 0x00000c00;
  /**
   * Mask for the scaling factor used in some addressing modes..
   */
  public static final int M_MASK   = 0x00003000;
  /**
   * Shift for the scaling factor used in some addressing modes..
   */
  public static final int M_SHIFT  = 12;
  /**
   * Scaling factor - 1.
   */
  public static final int M_ONE    = 0x00000000;
  /**
   * Scaling factor - 2.
   */
  public static final int M_TWO    = 0x00001000;
  /**
   * Scaling factor - 4.
   */
  public static final int M_FOUR   = 0x00002000;
  /**
   * Scaling factor - 8.
   */
  public static final int M_EIGHT  = 0x00003000;
  /**
   * Valid form has no operands.
   */
  public static final int F_NONE   = 0x00004000;
  /**
   * Valid form has one register operand.
   */
  public static final int F_R      = 0x00008000;
  /**
   * Valid form has two register operands.
   */
  public static final int F_RR     = 0x00010000;
  /**
   * Valid form has three register operands.
   */
  public static final int F_RRR    = 0x00020000;
  /**
   * Valid form has a descriptor operand.
   */
  public static final int F_D      = 0x00040000;
  /**
   * Valid form has a register operand and a descriptor operand.
   */
  public static final int F_RD     = 0x00080000;
  /**
   * Valid form has two register operands and a descriptor operand.
   */
  public static final int F_RRD    = 0x00100000;
  /**
   * Valid form has three register operands and a descriptor operand.
   */
  public static final int F_RRRD   = 0x00200000;
  /**
   * Branch instruction flag.
   */
  public static final int F_BRANCH = 0x00400000;
  /**
   * Operands are reversed flag.
   */
  public static final int F_REV    = 0x00800000;

  public static final int F_SHFT = F_RD + F_RR;
  public static final int F_ARTH = F_RR + F_RD + F_RRR + F_RRD + F_RRRD; // ADD, ADC, AND, XOR, OR, SBB, SUB, CMP
  public static final int F_CALL = F_D + F_R + F_RD + F_RR + F_RRD;

  // Integer Instructions 
  //   DATA TRANSFER INSTRUCTIONS 
  public static final int MOV       =  0X000 + F_NONE; // Move 
  public static final int CMOVE     =  0X001 + F_NONE; // Conditional move if equal
  public static final int CMOVZ     =  0X002 + F_NONE; // Conditional move if zero 
  public static final int CMOVNE    =  0X003 + F_NONE; // Conditional move if not equal
  public static final int CMOVNZ    =  0X004 + F_NONE; // Conditional move if not zero 
  public static final int CMOVA     =  0X005 + F_NONE; // Conditional move if above
  public static final int CMOVNBE   =  0X006 + F_NONE; // Conditional move if not below or equal 
  public static final int CMOVAE    =  0X007 + F_NONE; // Conditional move if above or equal
  public static final int CMOVNB    =  0X008 + F_NONE; // Conditional move if not below 
  public static final int CMOVB     =  0X009 + F_NONE; // Conditional move if below
  public static final int CMOVNAE   =  0X00A + F_NONE; // Conditional move if not above or equal 
  public static final int CMOVBE    =  0X00B + F_NONE; // Conditional move if below or equal
  public static final int CMOVNA    =  0X00C + F_NONE; // Conditional move if not above 
  public static final int CMOVG     =  0X00D + F_NONE; // Conditional move if greater
  public static final int CMOVNLE   =  0X00E + F_NONE; // Conditional move if not less or equal 
  public static final int CMOVGE    =  0X00F + F_NONE; // Conditional move if greater or equal
  public static final int CMOVNL    =  0X010 + F_NONE; // Conditional move if not less 
  public static final int CMOVL     =  0X011 + F_NONE; // Conditional move if less
  public static final int CMOVNGE   =  0X012 + F_NONE; // Conditional move if not greater or equal 
  public static final int CMOVLE    =  0X013 + F_NONE; // Conditional move if less or equal
  public static final int CMOVNG    =  0X014 + F_NONE; // Conditional move if not greater 
  public static final int CMOVC     =  0X015 + F_NONE; // Conditional move if carry 
  public static final int CMOVNC    =  0X016 + F_NONE; // Conditional move if not carry 
  public static final int CMOVO     =  0X017 + F_NONE; // Conditional move if overflow 
  public static final int CMOVNO    =  0X018 + F_NONE; // Conditional move if not overflow 
  public static final int CMOVS     =  0X019 + F_NONE; // Conditional move if sign (negative) 
  public static final int CMOVNS    =  0X01A + F_NONE; // Conditional move if not sign (non-negative) 
  public static final int CMOVP     =  0X01B + F_NONE; // Conditional move if parity
  public static final int CMOVPE    =  0X01C + F_NONE; // Conditional move if parity even 
  public static final int CMOVNP    =  0X01D + F_NONE; // Conditional move if not parity
  public static final int CMOVPO    =  0X01E + F_NONE; // Conditional move if parity odd 
  public static final int XCHG      =  0X01F + F_NONE; // Exchange
  public static final int BSWAP     =  0X020 + F_NONE; // Byte swap 
  public static final int XADD      =  0X021 + F_NONE; // Exchange and add 
  public static final int CMPXCHG   =  0X022 + F_NONE; // Compare and exchange 
  public static final int CMPXCHG8B =  0X023 + F_NONE; // Compare and exchange 8 bytes 
  public static final int PUSH      =  0X024 + F_NONE; // Push onto stack 
  public static final int POP       =  0X025 + F_NONE; // Pop off of stack 
  public static final int PUSHA     =  0X026 + F_NONE; // Push general-purpose registers onto stack 
  public static final int PUSHAD    =  0X027 + F_NONE; // Push general-purpose registers onto stack 
  public static final int POPA      =  0X028 + F_NONE; // Pop general-purpose registers from stack 
  public static final int POPAD     =  0X029 + F_NONE; // Pop general-purpose registers from stack 
  public static final int IN        =  0X02A + F_NONE; // Read from a port 
  public static final int OUT       =  0X02B + F_NONE; // Write to a port 
  public static final int CWD       =  0X02C + F_NONE; // Convert word to doubleword
  public static final int CDQ       =  0X02D + F_NONE; // Convert doubleword to quadword 
  public static final int CBW       =  0X02E + F_NONE; // Convert byte to word
  public static final int CWDE      =  0X02F + F_NONE; // Convert word to doubleword in EAX register 
  public static final int MOVSX     =  0X030 + F_NONE; // Move and sign extend 
  public static final int MOVZX     =  0X031 + F_NONE; // Move and zero extend 
  //   BINARY ARTHMETIC INSTRUCTIONS 
  public static final int ADD       =  0X032 + F_ARTH; // Integer add 
  public static final int ADC       =  0X033 + F_ARTH; // Add with carry 
  public static final int SUB       =  0X034 + F_ARTH; // Subtract 
  public static final int SBB       =  0X035 + F_ARTH; // Subtract with borrow 
  public static final int IMUL      =  0X036 + F_NONE; // Signed multiply 
  public static final int MUL       =  0X037 + F_NONE; // Unsigned multiply 
  public static final int IDIV      =  0X038 + F_NONE; // Signed divide 
  public static final int DIV       =  0X039 + F_NONE; // Unsigned divide 
  public static final int INC       =  0X03A + F_NONE; // Increment 
  public static final int DEC       =  0X03B + F_NONE; // Decrement 
  public static final int NEG       =  0X03C + F_NONE; // Negate 
  public static final int CMP       =  0X03D + F_ARTH; // Compare 
  //   DECIMAL ARTHMETIC 
  public static final int DAA       =  0X03E + F_NONE; // Decimal adjust after addition 
  public static final int DAS       =  0X03F + F_NONE; // Decimal adjust after subtraction
  public static final int AAA       =  0X040 + F_NONE; // ASCII adjust after addition 
  public static final int AAS       =  0X041 + F_NONE; // ASCII adjust after subtraction 
  public static final int AAM       =  0X042 + F_NONE; // ASCII adjust after multiplication 
  public static final int AAD       =  0X043 + F_NONE; // ASCII adjust before division 
  //   LOGIC INSTRUCTIONS 
  public static final int AND       =  0X044 + F_ARTH; // And 
  public static final int OR        =  0X045 + F_ARTH; // Or 
  public static final int XOR       =  0X046 + F_ARTH; // Exclusive or 
  public static final int NOT       =  0X047 + F_NONE; // Not 
  //   SHIFT AND ROTATE INSTRUCTIONS 
  public static final int SAR       =  0X048 + F_SHFT; // Shift arithmetic right 
  public static final int SHR       =  0X049 + F_SHFT; // Shift logical right 
  public static final int SAL       =  0X04A + F_SHFT; // Shift arithmetic left
  public static final int SHL       =  0X04B + F_SHFT; // Shift arithmetic left
  public static final int SHRD      =  0X04C + F_SHFT; // Shift right double 
  public static final int SHLD      =  0X04D + F_SHFT; // Shift left double 
  public static final int ROR       =  0X04E + F_SHFT; // Rotate right 
  public static final int ROL       =  0X04F + F_SHFT; // Rotate left 
  public static final int RCR       =  0X050 + F_SHFT; // Rotate through carry right 
  public static final int RCL       =  0X051 + F_SHFT; // Rotate through carry left 
  //   BIT AND BYTE INSTRUCTIONS 
  public static final int BT        =  0X052 + F_ARTH; // Bit test 
  public static final int BTS       =  0X053 + F_NONE; // Bit test and set 
  public static final int BTR       =  0X054 + F_NONE; // Bit test and reset 
  public static final int BTC       =  0X055 + F_ARTH; // Bit test and complement 
  public static final int BSF       =  0X056 + F_NONE; // Bit scan forward 
  public static final int BSR       =  0X057 + F_NONE; // Bit scan reverse 
  public static final int SETE      =  0X058 + F_NONE; // Set byte if equal
  public static final int SETZ      =  0X059 + F_NONE; // Set byte if zero 
  public static final int SETNE     =  0X05A + F_NONE; // Set byte if not equal
  public static final int SETNZ     =  0X05B + F_NONE; // Set byte if not zero
  public static final int SETA      =  0X05C + F_NONE; // Set byte if above
  public static final int SETNBE    =  0X05D + F_NONE; // Set byte if not below or equal 
  public static final int SETAE     =  0X05E + F_NONE; // Set byte if above or equal
  public static final int SETNB     =  0X05F + F_NONE; // Set byte if not below
  public static final int SETNC     =  0X060 + F_NONE; // Set byte if not carry 
  public static final int SETB      =  0X061 + F_NONE; // Set byte if below 
  public static final int SETNAE    =  0X062 + F_NONE; // Set byte if not above or equal
  public static final int SETC      =  0X063 + F_NONE; // Set byte if carry 
  public static final int SETBE     =  0X064 + F_NONE; // Set byte if below or equal
  public static final int SETNA     =  0X065 + F_NONE; // Set byte if not above 
  public static final int SETG      =  0X066 + F_NONE; // Set byte if greater
  public static final int SETNLE    =  0X067 + F_NONE; // Set byte if not less or equal 
  public static final int SETGE     =  0X068 + F_NONE; // Set byte if greater or equal
  public static final int SETNL     =  0X069 + F_NONE; // Set byte if not less 
  public static final int SETL      =  0X06A + F_NONE; // Set byte if less
  public static final int SETNGE    =  0X06B + F_NONE; // Set byte if not greater or equal 
  public static final int SETLE     =  0X06C + F_NONE; // Set byte if less or equal
  public static final int SETNG     =  0X06D + F_NONE; // Set byte if not greater 
  public static final int SETS      =  0X06E + F_NONE; // Set byte if sign (negative) 
  public static final int SETNS     =  0X06F + F_NONE; // Set byte if not sign (non-negative) 
  public static final int SETO      =  0X070 + F_NONE; // Set byte if overflow 
  public static final int SETNO     =  0X071 + F_NONE; // Set byte if not overflow 
  public static final int SETPE     =  0X072 + F_NONE; // Set byte if parity even
  public static final int SETP      =  0X073 + F_NONE; // Set byte if parity 
  public static final int SETPO     =  0X074 + F_NONE; // Set byte if parity odd
  public static final int SETNP     =  0X075 + F_NONE; // Set byte if not parity 
  public static final int TEST      =  0X076 + F_NONE; // Logical compare 
  //   CONTROL TRANSFER INSTRUCTIONS 
  public static final int JMP       =  0X077 + F_NONE; // Jump 
  public static final int JE        =  0X078 + F_NONE; // Jump if equal
  public static final int JZ        =  0X079 + F_NONE; // Jump if zero 
  public static final int JNE       =  0X07A + F_NONE; // Jump if not equal
  public static final int JNZ       =  0X07B + F_NONE; // Jump if not zero 
  public static final int JA        =  0X07C + F_NONE; // Jump if above
  public static final int JNBE      =  0X07D + F_NONE; // Jump if not below or equal 
  public static final int JAE       =  0X07E + F_NONE; // Jump if above or equal
  public static final int JNB       =  0X07F + F_NONE; // Jump if not below 
  public static final int JB        =  0X080 + F_NONE; // Jump if below
  public static final int JNAE      =  0X081 + F_NONE; // Jump if not above or equal 
  public static final int JBE       =  0X082 + F_NONE; // Jump if below or equal
  public static final int JNA       =  0X083 + F_NONE; // Jump if not above 
  public static final int JG        =  0X084 + F_NONE; // Jump if greater
  public static final int JNLE      =  0X085 + F_NONE; // Jump if not less or equal 
  public static final int JGE       =  0X086 + F_NONE; // Jump if greater or equal
  public static final int JNL       =  0X087 + F_NONE; // Jump if not less 
  public static final int JL        =  0X088 + F_NONE; // Jump if less
  public static final int JNGE      =  0X089 + F_NONE; // Jump if not greater or equal 
  public static final int JLE       =  0X08A + F_NONE; // Jump if less or equal
  public static final int JNG       =  0X08B + F_NONE; // Jump if not greater 
  public static final int JC        =  0X08C + F_NONE; // Jump if carry 
  public static final int JNC       =  0X08D + F_NONE; // Jump if not carry
  public static final int JO        =  0X08E + F_NONE; // Jump if overflow 
  public static final int JNO       =  0X08F + F_NONE; // Jump if not overflow 
  public static final int JS        =  0X090 + F_NONE; // Jump if sign (negative) 
  public static final int JNS       =  0X091 + F_NONE; // Jump if not sign (non-negative) 
  public static final int JPO       =  0X092 + F_NONE; // Jump if parity odd
  public static final int JNP       =  0X093 + F_NONE; // Jump if not parity 
  public static final int JPE       =  0X094 + F_NONE; // Jump if parity even
  public static final int JP        =  0X095 + F_NONE; // Jump if parity 
  public static final int JCXZ      =  0X096 + F_NONE; // Jump register CX zero
  public static final int JECXZ     =  0X097 + F_NONE; // Jump register ECX zero 
  public static final int LOOP      =  0X098 + F_NONE; // Loop with ECX counter 
  public static final int LOOPZ     =  0X099 + F_NONE; // Loop with ECX and zero
  public static final int LOOPE     =  0X09A + F_NONE; // Loop with ECX and equal 
  public static final int LOOPNZ    =  0X09B + F_NONE; // Loop with ECX and not zero
  public static final int LOOPNE    =  0X09C + F_NONE; // Loop with ECX and not equal 
  public static final int CALL      =  0X09D + F_NONE; // Call procedure 
  public static final int RET       =  0X09E + F_NONE; // Return 
  public static final int IRET      =  0X09F + F_NONE; // Return from interrupt 
  public static final int INT       =  0X0A0 + F_NONE; // Software interrupt 
  public static final int INTO      =  0X0A1 + F_NONE; // Interrupt on overflow 
  public static final int BOUND     =  0X0A2 + F_NONE; // Detect value out of range 
  public static final int ENTER     =  0X0A3 + F_NONE; // High-level procedure entry 
  public static final int LEAVE     =  0X0A4 + F_NONE; // High-level procedure exit 
  //   STRING INSTRUCTIONS 
  public static final int UN00      =  0X0A5 + F_NONE; // Unused
  public static final int MOVSB     =  0X0A6 + F_NONE; // Move byte string 
  public static final int UN01      =  0X0A7 + F_NONE; // Unused
  public static final int MOVSW     =  0X0A8 + F_NONE; // Move word string 
  public static final int UN02      =  0X0A9 + F_NONE; // Unused
  public static final int MOVSD     =  0X0AA + F_NONE; // Move doubleword string 
  public static final int UN03      =  0X0AB + F_NONE; // Unused
  public static final int CMPSB     =  0X0AC + F_NONE; // Compare byte string 
  public static final int UN04      =  0X0AD + F_NONE; // Unused
  public static final int CMPSW     =  0X0AE + F_NONE; // Compare word string 
  public static final int UN05      =  0X0AF + F_NONE; // Unused
  public static final int CMPSD     =  0X0B0 + F_NONE; // Compare doubleword string 
  public static final int UN06      =  0X0B1 + F_NONE; // Unused
  public static final int SCASB     =  0X0B2 + F_NONE; // Scan byte string 
  public static final int UN07      =  0X0B3 + F_NONE; // Unused
  public static final int SCASW     =  0X0B4 + F_NONE; // Scan word string 
  public static final int UN08      =  0X0B5 + F_NONE; // Unused
  public static final int SCASD     =  0X0B6 + F_NONE; // Scan doubleword string 
  public static final int UN09      =  0X0B7 + F_NONE; // Unused
  public static final int LODSB     =  0X0B8 + F_NONE; // Load byte string 
  public static final int UN10      =  0X0B9 + F_NONE; // Unused
  public static final int LODSW     =  0X0BA + F_NONE; // Load word string
  public static final int UN11      =  0X0BB + F_NONE; // Unused
  public static final int LODSD     =  0X0BC + F_NONE; // Load doubleword string 
  public static final int UN12      =  0X0BD + F_NONE; // Unused
  public static final int STOSB     =  0X0BE + F_NONE; // Store byte string 
  public static final int UN13      =  0X0BF + F_NONE; // Unused
  public static final int STOSW     =  0X0C0 + F_NONE; // Store word string 
  public static final int UN14      =  0X0C1 + F_NONE; // KUnusedStore string
  public static final int STOSD     =  0X0C2 + F_NONE; // Store doubleword string 
  public static final int REP       =  0X0C3 + F_NONE; // Repeat while ECX not zero 
  public static final int REPE      =  0X0C4 + F_NONE; // Repeat while equal
  public static final int REPZ      =  0X0C5 + F_NONE; // Repeat while zero 
  public static final int REPNE     =  0X0C6 + F_NONE; // Repeat while not equal
  public static final int REPNZ     =  0X0C7 + F_NONE; // Repeat while not zero 
  public static final int UN15      =  0X0C8 + F_NONE; // Unused
  public static final int INSB      =  0X0C9 + F_NONE; // Input byte string from port 
  public static final int UN16      =  0X0CA + F_NONE; // Unused
  public static final int INSW      =  0X0CB + F_NONE; // Input word string from port 
  public static final int UN17      =  0X0CC + F_NONE; // Unused
  public static final int INSD      =  0X0CD + F_NONE; // Input doubleword string from port 
  public static final int UN18      =  0X0CE + F_NONE; // Unused
  public static final int OUTSB     =  0X0CF + F_NONE; // Output byte string to port 
  public static final int UN19      =  0X0D0 + F_NONE; // Unused
  public static final int OUTSW     =  0X0D1 + F_NONE; // Output word string to port 
  public static final int UN20      =  0X0D2 + F_NONE; // Unused
  public static final int OUTSD     =  0X0D3 + F_NONE; // Output doubleword string to port 
  //  FLAG CONTROL INSTRUCTIONS 
  public static final int STC       =  0X0D4 + F_NONE; // Set carry flag 
  public static final int CLC       =  0X0D5 + F_NONE; // Clear the carry flag 
  public static final int CMC       =  0X0D6 + F_NONE; // Complement the carry flag 
  public static final int CLD       =  0X0D7 + F_NONE; // Clear the direction flag 
  public static final int STD       =  0X0D8 + F_NONE; // Set direction flag 
  public static final int LAHF      =  0X0D9 + F_NONE; // Load flags into AH register 
  public static final int SAHF      =  0X0DA + F_NONE; // Store AH register into flags 
  public static final int PUSHF     =  0X0DB + F_NONE; // Push EFLAGS onto stack 
  public static final int PUSHFD    =  0X0DC + F_NONE; // Push EFLAGS onto stack 
  public static final int POPF      =  0X0DD + F_NONE; // Pop EFLAGS from stack 
  public static final int POPFD     =  0X0DE + F_NONE; // Pop EFLAGS from stack 
  public static final int STI       =  0X0DF + F_NONE; // Set interrupt flag 
  public static final int CLI       =  0X0E0 + F_NONE; // Clear the interrupt flag 
  //   SEGMENT REGISTER INSTRUCTIONS 
  public static final int LDS       =  0X0E1 + F_NONE; // Load far pointer using DS 
  public static final int LES       =  0X0E2 + F_NONE; // Load far pointer using ES 
  public static final int LFS       =  0X0E3 + F_NONE; // Load far pointer using FS
  public static final int LGS       =  0X0E4 + F_NONE; // Load far pointer using GS 
  public static final int LSS       =  0X0E5 + F_NONE; // Load far pointer using SS 
  //   MISCELLANEOUS INSTRUCTIONS 
  public static final int LEA       =  0X0E6 + F_NONE; // Load effective address 
  public static final int NOP       =  0X0E7 + F_NONE; // No operation 
  public static final int UB2       =  0X0E8 + F_NONE; // Undefined instruction 
  public static final int XLAT      =  0X0E9 + F_NONE; // Table lookup translation 
  public static final int XLATB     =  0X0EA + F_NONE; // Table lookup translation 
  public static final int CPUID     =  0X0EB + F_NONE; // Processor Identification 
  //   MMXTM Technology Instructions 
  //   MMXTM DATA TRANSFER INSTRUCTIONS 
  public static final int MOVD      =  0X0EC + F_NONE; // Move doubleword 
  public static final int MOVQ      =  0X0ED + F_NONE; // Move quadword 
  //    MMXTM CONVERSION INSTRUCTIONS 
  public static final int PACKSSWB  =  0X0EE + F_NONE; // Pack words into bytes with signed saturation 
  public static final int PACKSSDW  =  0X0EF + F_NONE; // Pack doublewords into words with signed saturation 
  public static final int PACKUSWB  =  0X0F0 + F_NONE; // Pack words into bytes with unsigned saturation 
  public static final int PUNPCKHBW =  0X0F1 + F_NONE; // Unpack high-order bytes from words 
  public static final int PUNPCKHWD =  0X0F2 + F_NONE; // Unpack high-order words from doublewords 
  public static final int PUNPCKHDQ =  0X0F3 + F_NONE; // Unpack high-order doublewords from quadword 
  public static final int PUNPCKLBW =  0X0F4 + F_NONE; // Unpack low-order bytes from words 
  public static final int PUNPCKLWD =  0X0F5 + F_NONE; // Unpack low-order words from doublewords 
  public static final int PUNPCKLDQ =  0X0F6 + F_NONE; // Unpack low-order doublewords from quadword
  //   MMXTM PACKED ARTHMETIC INSTRUCTIONS 
  public static final int PADDB     =  0X0F7 + F_NONE; // Add packed bytes 
  public static final int PADDW     =  0X0F8 + F_NONE; // Add packed words 
  public static final int PADDD     =  0X0F9 + F_NONE; // Add packed doublewords 
  public static final int PADDSB    =  0X0FA + F_NONE; // Add packed bytes with saturation 
  public static final int PADDSW    =  0X0FB + F_NONE; // Add packed words with saturation 
  public static final int PADDUSB   =  0X0FC + F_NONE; // Add packed unsigned bytes with saturation 
  public static final int PADDUSW   =  0X0FD + F_NONE; // Add packed unsigned words with saturation 
  public static final int PSUBB     =  0X0FE + F_NONE; // Subtract packed bytes 
  public static final int PSUBW     =  0X0FF + F_NONE; // Subtract packed words 
  public static final int PSUBD     =  0X100 + F_NONE; // Subtract packed doublewords 
  public static final int PSUBSB    =  0X101 + F_NONE; // Subtract packed bytes with saturation 
  public static final int PSUBSW    =  0X102 + F_NONE; // Subtract packed words with saturation 
  public static final int PSUBUSB   =  0X103 + F_NONE; // Subtract packed unsigned bytes with saturation 
  public static final int PSUBUSW   =  0X104 + F_NONE; // Subtract packed unsigned words with saturation 
  public static final int PMULHW    =  0X105 + F_NONE; // Multiply packed words and store high result 
  public static final int PMULLW    =  0X106 + F_NONE; // Multiply packed words and store low result 
  public static final int PMADDWD   =  0X107 + F_NONE; // Multiply and add packed words 
  //   MMXTM COMPARISON INSTRUCTIONS 
  public static final int PCMPEQB   =  0X108 + F_NONE; // Compare packed bytes for equal 
  public static final int PCMPEQW   =  0X109 + F_NONE; // Compare packed words for equal 
  public static final int PCMPEQD   =  0X10A + F_NONE; // Compare packed doublewords for equal 
  public static final int PCMPGTB   =  0X10B + F_NONE; // Compare packed bytes for greater than 
  public static final int PCMPGTW   =  0X10C + F_NONE; // Compare packed words for greater than 
  public static final int PCMPGTD   =  0X10D + F_NONE; // Compare packed doublewords for greater than 
  //   MMXTM LOGIC INSTRUCTIONS 
  public static final int PAND      =  0X10E + F_NONE; // Bitwise logical and 
  public static final int PANDN     =  0X10F + F_NONE; // Bitwise logical and not 
  public static final int POR       =  0X110 + F_NONE; // Bitwise logical or 
  public static final int PXOR      =  0X111 + F_NONE; // Bitwise logical exclusive or
  //   MMXTM SHIFT AND ROTATE INSTRUCTIONS 
  public static final int PSLLW     =  0X112 + F_NONE; // Shift packed words left logical 
  public static final int PSLLD     =  0X113 + F_NONE; // Shift packed doublewords left logical 
  public static final int PSLLQ     =  0X114 + F_NONE; // Shift packed quadword left logical 
  public static final int PSRLW     =  0X115 + F_NONE; // Shift packed words right logical 
  public static final int PSRLD     =  0X116 + F_NONE; // Shift packed doublewords right logical 
  public static final int PSRLQ     =  0X117 + F_NONE; // Shift packed quadword right logical 
  public static final int PSRAW     =  0X118 + F_NONE; // Shift packed words right arithmetic 
  public static final int PSRAD     =  0X119 + F_NONE; // Shift packed doublewords right arithmetic 
  //   MMXTM STATE MANAGEMENT 
  public static final int EMMS      =  0X11A + F_NONE; // Empty MMX state 
  //    Floating-Point Instructions 
  //    DATA TRANSFER 
  public static final int FLD       =  0X11B + F_NONE; // Load real 
  public static final int FST       =  0X11C + F_NONE; // Store real 
  public static final int FSTP      =  0X11D + F_NONE; // Store real and pop 
  public static final int FILD      =  0X11E + F_NONE; // Load integer 
  public static final int FIST      =  0X11F + F_NONE; // Store integer 
  public static final int FISTP     =  0X120 + F_NONE; // Store integer and pop 
  public static final int FBLD      =  0X121 + F_NONE; // Load BCD 
  public static final int FBSTP     =  0X122 + F_NONE; // Store BCD and pop 
  public static final int FXCH      =  0X123 + F_NONE; // Exchange registers 
  public static final int FCMOVE    =  0X124 + F_NONE; // Floating-point conditional move if equal 
  public static final int FCMOVNE   =  0X125 + F_NONE; // Floating-point conditional move if not equal 
  public static final int FCMOVB    =  0X126 + F_NONE; // Floating-point conditional move if below
  public static final int FCMOVBE   =  0X127 + F_NONE; // Floating-point conditional move if below or equal 
  public static final int FCMOVNB   =  0X128 + F_NONE; // Floating-point conditional move if not below 
  public static final int FCMOVNBE  =  0X129 + F_NONE; // Floating-point conditional move if not below or equal 
  public static final int FCMOVU    =  0X12A + F_NONE; // Floating-point conditional move if unordered 
  public static final int FCMOVNU   =  0X12B + F_NONE; // Floating-point conditional move if not unordered 
  //   BASIC ARTHMETIC 
  public static final int FADD      =  0X12C + F_NONE; // Add real 
  public static final int FADDP     =  0X12D + F_NONE; // Add real and pop 
  public static final int FIADD     =  0X12E + F_NONE; // Add integer 
  public static final int FSUB      =  0X12F + F_NONE; // Subtract real 
  public static final int FSUBP     =  0X130 + F_NONE; // Subtract real and pop 
  public static final int FISUB     =  0X131 + F_NONE; // Subtract integer 
  public static final int FSUBR     =  0X132 + F_NONE; // Subtract real reverse 
  public static final int FSUBRP    =  0X133 + F_NONE; // Subtract real reverse and pop 
  public static final int FISUBR    =  0X134 + F_NONE; // Subtract integer reverse 
  public static final int FMUL      =  0X135 + F_NONE; // Multiply real 
  public static final int FMULP     =  0X136 + F_NONE; // Multiply real and pop 
  public static final int FIMUL     =  0X137 + F_NONE; // Multiply integer 
  public static final int FDIV      =  0X138 + F_NONE; // Divide real 
  public static final int FDIVP     =  0X139 + F_NONE; // Divide real and pop 
  public static final int FIDIV     =  0X13A + F_NONE; // Divide integer 
  public static final int FDIVR     =  0X13B + F_NONE; // Divide real reverse 
  public static final int FDIVRP    =  0X13C + F_NONE; // Divide real reverse and pop 
  public static final int FIDIVR    =  0X13D + F_NONE; // Divide integer reverse 
  public static final int FPREM     =  0X13E + F_NONE; // Partial remainder 
  public static final int FPREMI    =  0X13F + F_NONE; // IEEE Partial remainder 
  public static final int FABS      =  0X140 + F_NONE; // Absolute value 
  public static final int FCHS      =  0X141 + F_NONE; // Change sign 
  public static final int FRNDINT   =  0X142 + F_NONE; // Round to integer 
  public static final int FSCALE    =  0X143 + F_NONE; // Scale by power of two
  public static final int FSQRT     =  0X144 + F_NONE; // Square root 
  public static final int FXTRACT   =  0X145 + F_NONE; // Extract exponent and significand 
  //   COMPARISON 
  public static final int FCOM      =  0X146 + F_NONE; // Compare real 
  public static final int FCOMP     =  0X147 + F_NONE; // Compare real and pop 
  public static final int FCOMPP    =  0X148 + F_NONE; // Compare real and pop twice 
  public static final int FUCOM     =  0X149 + F_NONE; // Unordered compare real 
  public static final int FUCOMP    =  0X14A + F_NONE; // Unordered compare real and pop 
  public static final int FUCOMPP   =  0X14B + F_NONE; // Unordered compare real and pop twice 
  public static final int FICOM     =  0X14C + F_NONE; // Compare integer 
  public static final int FICOMP    =  0X14D + F_NONE; // Compare integer and pop 
  public static final int FCOMI     =  0X14E + F_NONE; // Compare real and set EFLAGS 
  public static final int FUCOMI    =  0X14F + F_NONE; // Unordered compare real and set EFLAGS 
  public static final int FCOMIP    =  0X150 + F_NONE; // Compare real, set EFLAGS, and pop 
  public static final int FUCOMIP   =  0X151 + F_NONE; // Unordered compare real, set EFLAGS, and pop 
  public static final int FTST      =  0X152 + F_NONE; // Test real 
  public static final int FXAM      =  0X153 + F_NONE; // Examine real 
  //   TRANSCENDENTAL 
  public static final int FSIN      =  0X154 + F_NONE; // Sine 
  public static final int FCOS      =  0X155 + F_NONE; // Cosine 
  public static final int FSINCOS   =  0X156 + F_NONE; // Sine and cosine 
  public static final int FPTAN     =  0X157 + F_NONE; // Partial tangent 
  public static final int FPATAN    =  0X158 + F_NONE; // Partial arctangent 
  public static final int F2XM1     =  0X159 + F_NONE; // 2x - 1 
  public static final int FYL2X     =  0X15A + F_NONE; // y*log2x 
  public static final int FYL2XP1   =  0X15B + F_NONE; // y*log 2(x+1) 
  //   LOAD CONSTANTS 
  public static final int FLD1      =  0X15C + F_NONE; // Load +1.0 
  public static final int FLDZ      =  0X15D + F_NONE; // Load +0.0
  public static final int FLDPI     =  0X15E + F_NONE; // Load Pi
  public static final int FLDL2E    =  0X15F + F_NONE; // Load log2e 
  public static final int FLDLN2    =  0X160 + F_NONE; // Load loge2 
  public static final int FLDL2T    =  0X161 + F_NONE; // Load log210 
  public static final int FLDLG2    =  0X162 + F_NONE; // Load log102 
  //   FPU CONTROL 
  public static final int FINCSTP   =  0X163 + F_NONE; // Increment FPU register stack pointer 
  public static final int FDECSTP   =  0X164 + F_NONE; // Decrement FPU register stack pointer 
  public static final int FFREE     =  0X165 + F_NONE; // Free floating-point register 
  public static final int FINIT     =  0X166 + F_NONE; // Initialize FPU after checking error conditions 
  public static final int FNINIT    =  0X167 + F_NONE; // Initialize FPU without checking error conditions 
  public static final int FCLEX     =  0X168 + F_NONE; // Clear floating-point exception flags after checking for error conditions 
  public static final int FNCLEX    =  0X169 + F_NONE; // Clear floating-point exception flags without checking for error conditions 
  public static final int FSTCW     =  0X16A + F_NONE; // Store FPU control word after checking error conditions 
  public static final int FNSTCW    =  0X16B + F_NONE; // Store FPU control word without checking error conditions 
  public static final int FLDCW     =  0X16C + F_NONE; // Load FPU control word 
  public static final int FSTENV    =  0X16D + F_NONE; // Store FPU environment after checking error conditions 
  public static final int FNSTENV   =  0X16E + F_NONE; // Store FPU environment without checking error conditions 
  public static final int FLDENV    =  0X16F + F_NONE; // Load FPU environment 
  public static final int FSAVE     =  0X170 + F_NONE; // Save FPU state after checking error conditions 
  public static final int FNSAVE    =  0X171 + F_NONE; // Save FPU state without checking error conditions 
  public static final int FRSTOR    =  0X172 + F_NONE; // Restore FPU state 
  public static final int FSTSW     =  0X173 + F_NONE; // Store FPU status word after checking error conditions 
  public static final int FNSTSW    =  0X174 + F_NONE; // Store FPU status word without checking error conditions 
  public static final int WAIT      =  0X175 + F_NONE; // Wait for FPU 
  public static final int FWAIT     =  0X176 + F_NONE; // Wait for FPU 
  public static final int FNOP      =  0X177 + F_NONE; // FPU no operation
  //   System Instructions 
  public static final int LGDT      =  0X178 + F_NONE; // Load global descriptor table (GDT) register 
  public static final int SGDT      =  0X179 + F_NONE; // Store global descriptor table (GDT) register 
  public static final int LLDT      =  0X17A + F_NONE; // Load local descriptor table (LDT) register 
  public static final int SLDT      =  0X17B + F_NONE; // Store local descriptor table (LDT) register 
  public static final int LTR       =  0X17C + F_NONE; // Load task register 
  public static final int STR       =  0X17D + F_NONE; // Store task register 
  public static final int LIDT      =  0X17E + F_NONE; // Load interrupt descriptor table (IDT) register 
  public static final int SIDT      =  0X17F + F_NONE; // Store interrupt descriptor table (IDT) register 
  public static final int LMSW      =  0X180 + F_NONE; // Load machine status word 
  public static final int SMSW      =  0X181 + F_NONE; // Store machine status word 
  public static final int CLTS      =  0X182 + F_NONE; // Clear the task-switched flag 
  public static final int ARPL      =  0X183 + F_NONE; // Adjust requested privilege level 
  public static final int LAR       =  0X184 + F_NONE; // Load access rights 
  public static final int LSL       =  0X185 + F_NONE; // Load segment limit 
  public static final int VERR      =  0X186 + F_NONE; // Verify segment for reading 
  public static final int VERW      =  0X187 + F_NONE; // Verify segment for writing 
  public static final int INVD      =  0X188 + F_NONE; // Invalidate cache, no writeback 
  public static final int WBINVD    =  0X189 + F_NONE; // Invalidate cache, with writeback 
  public static final int INVLPG    =  0X18A + F_NONE; // Invalidate TLB Entry 
  public static final int LOCK      =  0X18B + F_NONE; // (prefix) Lock Bus 
  public static final int HLT       =  0X18C + F_NONE; // Halt processor 
  public static final int RSM       =  0X18D + F_NONE; // Return from system management mode (SSM) 
  public static final int RDMSR     =  0X18E + F_NONE; // Read model-specific register 
  public static final int WRMSR     =  0X18F + F_NONE; // Write model-specific register 
  public static final int RDPMC     =  0X190 + F_NONE; // Read performance monitoring counters 
  public static final int RDTSC     =  0X191 + F_NONE; // Read time stamp counter
  public static final int LDDR      =  0X192 + F_NONE; // Load debug register
  public static final int STDR      =  0X193 + F_NONE; // Store debug register
  public static final int LDCR      =  0X194 + F_NONE; // Load Control Register
  public static final int STCR      =  0X195 + F_NONE; // Store Control Register

  public static final String[] opnames = {
    /* 000 */ "MOV", "CMOVE", "CMOVZ", "CMOVNE", "CMOVNZ", "CMOVA", "CMOVNBE", "CMOVAE",
    /* 008 */ "CMOVNB", "CMOVB", "CMOVNAE", "CMOVBE", "CMOVNA", "CMOVG", "CMOVNLE", "CMOVGE",
    /* 010 */ "CMOVNL", "CMOVL", "CMOVNGE", "CMOVLE", "CMOVNG", "CMOVC", "CMOVNC", "CMOVO",
    /* 018 */ "CMOVNO", "CMOVS", "CMOVNS", "CMOVP", "CMOVPE", "CMOVNP", "CMOVPO", "XCHG",
    /* 020 */ "BSWAP", "XADD", "CMPXCHG", "CMPXCHG8B", "PUSH", "POP", "PUSHA", "PUSHAD",
    /* 028 */ "POPA", "POPAD", "IN", "OUT", "CWD", "CDQ", "CBW", "CWDE",
    /* 030 */ "MOVSX", "MOVZX", "ADD", "ADC", "SUB", "SBB", "IMUL", "MUL",
    /* 038 */ "IDIV", "DIV", "INC", "DEC", "NEG", "CMP", "DAA", "DAS",
    /* 040 */ "AAA", "AAS", "AAM", "AAD", "AND", "OR", "XOR", "NOT",
    /* 048 */ "SAR", "SHR", "SAL", "SHL", "SHRD", "SHLD", "ROR", "ROL",
    /* 050 */ "RCR", "RCL", "BT", "BTS", "BTR", "BTC", "BSF", "BSR",
    /* 058 */ "SETE", "SETZ", "SETNE", "SETNZ", "SETA", "SETNBE", "SETAE", "SETNB",
    /* 060 */ "SETNC", "SETB", "SETNAE", "SETC", "SETBE", "SETNA", "SETG", "SETNLE",
    /* 068 */ "SETGE", "SETNL", "SETL", "SETNGE", "SETLE", "SETNG", "SETS", "SETNS",
    /* 070 */ "SETO", "SETNO", "SETPE", "SETP", "SETPO", "SETNP", "TEST", "JMP",
    /* 078 */ "JE", "JZ", "JNE", "JNZ", "JA", "JNBE", "JAE", "JNB",
    /* 080 */ "JB", "JNAE", "JBE", "JNA", "JG", "JNLE", "JGE", "JNL",
    /* 088 */ "JL", "JNGE", "JLE", "JNG", "JC", "JNC", "JO", "JNO",
    /* 090 */ "JS", "JNS", "JPO", "JNP", "JPE", "JP", "JCXZ", "JECXZ",
    /* 098 */ "LOOP", "LOOPZ", "LOOPE", "LOOPNZ", "LOOPNE", "CALL", "RET", "IRET",
    /* 0A0 */ "INT", "INTO", "BOUND", "ENTER", "LEAVE", "UN00", "MOVSB", "UN01",
    /* 0A8 */ "MOVSW", "UN02", "MOVSD", "UN03", "CMPSB", "UN04", "CMPSW", "UN05",
    /* 0B0 */ "CMPSD", "UN06", "SCASB", "UN07", "SCASW", "UN08", "SCASD", "UN09",
    /* 0B8 */ "LODSB", "UN10", "LODSW", "UN11", "LODSD", "UN12", "STOSB", "UN13",
    /* 0C0 */ "STOSW", "UN14", "STOSD", "REP", "REPE", "REPZ", "REPNE", "REPNZ",
    /* 0C8 */ "UN15", "INSB", "UN16", "INSW", "UN17", "INSD", "UN18", "OUTSB",
    /* 0D0 */ "UN19", "OUTSW", "UN20", "OUTSD", "STC", "CLC", "CMC", "CLD",
    /* 0D8 */ "STD", "LAHF", "SAHF", "PUSHF", "PUSHFD", "POPF", "POPFD", "STI",
    /* 0E0 */ "CLI", "LDS", "LES", "LFS", "LGS", "LSS", "LEA", "NOP",
    /* 0E8 */ "UB2", "XLAT", "XLATB", "CPUID", "MOVD", "MOVQ", "PACKSSWB", "PACKSSDW",
    /* 0F0 */ "PACKUSWB", "PUNPCKHBW", "PUNPCKHWD", "PUNPCKHDQ", "PUNPCKLBW", "PUNPCKLWD", "PUNPCKLDQ", "PADDB",
    /* 0F8 */ "PADDW", "PADDD", "PADDSB", "PADDSW", "PADDUSB", "PADDUSW", "PSUBB", "PSUBW",
    /* 100 */ "PSUBD", "PSUBSB", "PSUBSW", "PSUBUSB", "PSUBUSW", "PMULHW", "PMULLW", "PMADDWD",
    /* 108 */ "PCMPEQB", "PCMPEQW", "PCMPEQD", "PCMPGTB", "PCMPGTW", "PCMPGTD", "PAND", "PANDN",
    /* 110 */ "POR", "PXOR", "PSLLW", "PSLLD", "PSLLQ", "PSRLW", "PSRLD", "PSRLQ",
    /* 118 */ "PSRAW", "PSRAD", "EMMS", "FLD", "FST", "FSTP", "FILD", "FIST",
    /* 120 */ "FISTP", "FBLD", "FBSTP", "FXCH", "FCMOVE", "FCMOVNE", "FCMOVB", "FCMOVBE",
    /* 128 */ "FCMOVNB", "FCMOVNBE", "FCMOVU", "FCMOVNU", "FADD", "FADDP", "FIADD", "FSUB",
    /* 130 */ "FSUBP", "FISUB", "FSUBR", "FSUBRP", "FISUBR", "FMUL", "FMULP", "FIMUL",
    /* 138 */ "FDIV", "FDIVP", "FIDIV", "FDIVR", "FDIVRP", "FIDIVR", "FPREM", "FPREMI",
    /* 140 */ "FABS", "FCHS", "FRNDINT", "FSCALE", "FSQRT", "FXTRACT", "FCOM", "FCOMP",
    /* 148 */ "FCOMPP", "FUCOM", "FUCOMP", "FUCOMPP", "FICOM", "FICOMP", "FCOMI", "FUCOMI",
    /* 150 */ "FCOMIP", "FUCOMIP", "FTST", "FXAM", "FSIN", "FCOS", "FSINCOS", "FPTAN",
    /* 158 */ "FPATAN", "F2XM1", "FYL2X", "FYL2XP1", "FLD1", "FLDZ", "FLDPI", "FLDL2E",
    /* 160 */ "FLDLN2", "FLDL2T", "FLDLG2", "FINCSTP", "FDECSTP", "FFREE", "FINIT", "FNINIT",
    /* 168 */ "FCLEX", "FNCLEX", "FSTCW", "FNSTCW", "FLDCW", "FSTENV", "FNSTENV", "FLDENV",
    /* 170 */ "FSAVE", "FNSAVE", "FRSTOR", "FSTSW", "FNSTSW", "WAIT", "FWAIT", "FNOP",
    /* 178 */ "LGDT", "SGDT", "LLDT", "SLDT", "LTR", "STR", "LIDT", "SIDT",
    /* 180 */ "LMSW", "SMSW", "CLTS", "ARPL", "LAR", "LSL", "VERR", "VERW", 
    /* 188 */ "INVD", "WBINVD", "INVLPG", "LOCK", "HLT", "RSM", "RDMSR", "WRMSR",
    /* 190 */ "RDPMC", "RDTSC", "LDDR", "STDR", "LDCR", "STCR",
  };

  private static final char[] sizeLabels = {'b', 'w', 'l', 'x'};

  static
  {
    assert opnames.length == 0x193;
  }

  /**
   * Return the symbolic string for the instruction.
   */
  public static String getOp(X86Instruction inst)
  {
    return opnames[inst.getOpcode() & O_MASK];
  }

  /**
   * Return the symbolic string for the instruction.
   */
  public static String getOp(X86Branch inst)
  {
    assert ((inst.getOpcode() & F_BRANCH) != 0);
    return opnames[inst.getOpcode() & O_MASK];
  }

  /**
   * Return the symbolic string for the instruction.
   */
  public static String getOp(int opcode)
  {
    return opnames[opcode & O_MASK];
  }

  /**
   * Return 1, 2, 4, or 8 depending on the scale factor specified for
   * the instruction.
   */
  public static int getScale(int opcode)
  {
    return 1 << ((opcode & M_MASK) >> M_SHIFT);
  }

  /**
   * Set the scale factor specified for the instruction.  The value
   * must be 1, 2, 4, or 8.
   * @return the new opcode
   */
  public static int setScale(int opcode, int scale)
  {
    int sf = 0;
    switch (scale) {
    case 1: sf = M_ONE;   break;
    case 2: sf = M_TWO;   break;
    case 4: sf = M_FOUR;  break;
    case 8: sf = M_EIGHT; break;
    }
    return ((opcode & M_MASK) | sf);
  }

  /**
   * Return 1, 2, 4, or 8 depending on the size of the operand, in
   * bytes, specified for the instruction.
   */
  public static int getOperandSize(int opcode)
  {
    return 1 << ((opcode & S_MASK) >> S_SHIFT);
  }

  /**
   * Return 'b', 'w', 'l', or 'x' depending on the size of the
   * operand specified for the instruction.
   */
  public static char getOperandSizeLabel(int opcode)
  {
    return sizeLabels[(opcode & S_MASK) >> S_SHIFT];
  }

  /**
   * Set the operand size specified for the instruction.  The value
   * must be 1, 2, 4, or 8.
   * @return the new opcode
   */
  public static int setOperandSize(int opcode, int size)
  {
    int sf = 0;
    switch (size) {
    case 1: sf = S_BYTE;  break;
    case 2: sf = S_SHORT; break;
    case 4: sf = S_INT;   break;
    case 8: sf = S_LONG;  break;
    }
    return ((opcode & S_MASK) | sf);
  }
}
