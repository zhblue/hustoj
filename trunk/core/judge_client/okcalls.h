/*
 * 
 *
 * This file is part of HUSTOJ.
 *
 * HUSTOJ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HUSTOJ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HUSTOJ. if not, see <http://www.gnu.org/licenses/>.
 */
#include <sys/syscall.h>
#define HOJ_MAX_LIMIT -1
#define CALL_ARRAY_SIZE 512
#define LANG_C 0
#define LANG_CPP 1
#define LANG_PASCAL 2
#define LANG_JAVA 3
#define LANG_RUBY 4
#define LANG_BASH 5
#define LANG_PYTHON 6
#define LANG_PHP 7
#define LANG_PERL 8
#define LANG_CSHARP 9
#define LANG_OBJC 10
#define LANG_FREEBASIC 11
#define LANG_SCHEME 12
#define LANG_CLANG 13
#define LANG_CLANGPP 14
#define LANG_LUA 15
#define LANG_JS 16
#define LANG_GO 17
#define LANG_SQL 18
#define LANG_FORTRAN 19
#define LANG_MATLAB 20
#define LANG_COBOL 21
#define LANG_R 22

#ifdef __i386
   #include "okcalls32.h"
#endif
#ifdef __x86_64
   #include "okcalls64.h"
#endif
#ifdef __arm__
   #include "okcalls_arm.h"
#endif
#ifdef __aarch64__
   #include "okcalls_aarch64.h"
#endif
#ifdef __mips__
   #include "okcalls_mips.h"
#endif
