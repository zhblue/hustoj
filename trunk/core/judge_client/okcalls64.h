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
//c & c++
int LANG_CV[256] = {0,1,2,3,4,5,8,9,11,12,13,20,21,59,63,89,158,231,240,272,511, SYS_time, SYS_read, SYS_uname, SYS_write, SYS_open,
		SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect,
		SYS_mmap, SYS_fstat, SYS_set_thread_area, 252, SYS_arch_prctl, 0 };
//pascal
int LANG_PV[256] = {0,1,2,3,4,9,11,13,16,59,89,97,201,231,511,SYS_open, SYS_set_thread_area, SYS_brk, SYS_read,
		SYS_uname, SYS_write, SYS_execve, SYS_ioctl, SYS_readlink, SYS_mmap,
		SYS_rt_sigaction, SYS_getrlimit, 252, 191, 158, 231, SYS_close,
		SYS_exit_group, SYS_munmap, SYS_time, 4, 0 };
//java
int LANG_JV[256] = { 0,39,157,257,302,2,3,4,5,9,10,11,12,13,14,21,56,59,89,97,104,158,202,218,231,273,257, 
		61, 22, 6, 33, 8, 13, 16, 111, 110, 39, 79, SYS_fcntl,
		SYS_getdents64, SYS_getrlimit, SYS_rt_sigprocmask, SYS_futex, SYS_read,
		SYS_mmap, SYS_stat, SYS_open, SYS_close, SYS_execve, SYS_access,
		SYS_brk, SYS_readlink, SYS_munmap, SYS_close, SYS_uname, SYS_clone,
		SYS_uname, SYS_mprotect, SYS_rt_sigaction, SYS_getrlimit, SYS_fstat,
		SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_set_thread_area,
		SYS_set_tid_address, SYS_set_robust_list, SYS_exit_group, 158, 0 };
//ruby
int LANG_RV[256] = { 0,1,2,3,4,5,9,10,12,13,14,16,21,22,56,59,72,97,98,107,108,131,158,202,218,231,273
		,96, 340, 4, 126, SYS_access, SYS_arch_prctl, SYS_brk,
		SYS_close, SYS_execve, SYS_exit_group, SYS_fstat, SYS_futex,
		SYS_getegid, SYS_geteuid, SYS_getgid, SYS_getuid, SYS_getrlimit,
		SYS_mmap, SYS_mprotect, SYS_munmap, SYS_open, SYS_read,
		SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_set_robust_list,
		SYS_set_tid_address, SYS_write, 0 };
//bash
int LANG_BV[256] = { 0,1,3,4,5,8,9,10,12,13,14,16,21,33,39,59,63,72,79,99,102,104,107,108,110,111,158,231,257,302,1,2,3,4,5,8,9,10,12,13,14,16,21,33,39,59,63,72,79,97,99,102,104,107,108,110,111,158,231,
		96, 22, 61, 56, 42, 41, 79, 158, 117, 60, 39, 102, 191,
		183, SYS_access, SYS_arch_prctl, SYS_brk, SYS_close, SYS_dup2,
		SYS_execve, SYS_exit_group, SYS_fcntl, SYS_fstat, SYS_getegid,
		SYS_geteuid, SYS_getgid, SYS_getpgrp, SYS_getpid, SYS_getppid,
		SYS_getrlimit, SYS_getuid, SYS_ioctl, SYS_lseek, SYS_mmap, SYS_mprotect,
		SYS_munmap, SYS_open, SYS_read, SYS_rt_sigaction, SYS_rt_sigprocmask,
		SYS_stat, SYS_uname, SYS_write, 14, 0 };
//python
int LANG_YV[256] = {0,39,99,302,99,32,72,131,1,2,3,4,5,6,8,9,10,11,12,13,14,16,21,59,78,79,89,97,102,104,107,108,158,202,218,231,257,273
			, 41, 42, 146, SYS_mremap, 158, 117, 60, 39, 102, 191,
		SYS_access, SYS_arch_prctl, SYS_brk, SYS_close, SYS_execve,
		SYS_exit_group, SYS_fcntl, SYS_fstat, SYS_futex, SYS_getcwd,
		SYS_getdents, SYS_getegid, SYS_geteuid, SYS_getgid, SYS_getrlimit,
		SYS_getuid, SYS_ioctl, SYS_lseek, SYS_lstat, SYS_mmap, SYS_mprotect,
		SYS_munmap, SYS_open, SYS_read, SYS_readlink, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_set_robust_list, SYS_set_tid_address, SYS_stat,
		SYS_write, 0 };
//php
int LANG_PHV[256] = { 0,1,2,3,4,5,6,8,9,10,11,12,13,14,16,21,59,79,97,158,202,218,231,257,273,
		257, 20, 146, 78, 158, 117, 60, 39, 102, 191, SYS_access,
		SYS_brk, SYS_clone, SYS_close, SYS_execve, SYS_exit_group, SYS_fcntl,
		SYS_fstat, SYS_futex, SYS_getcwd, SYS_getdents64, SYS_getrlimit,
		SYS_gettimeofday, SYS_ioctl, SYS_lseek, SYS_lstat, SYS_mmap,
		SYS_mprotect, SYS_munmap, SYS_open, SYS_read, SYS_readlink,
		SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_set_robust_list,
		SYS_set_thread_area, SYS_set_tid_address, SYS_stat, SYS_time, SYS_uname,
		SYS_write, 0 };
//perl
int LANG_PLV[256] = {0,1,2,3,4,5,8,9,10,12,13,14,16,21,59,72,89,97,102,104,107,108,158,202,218,231,273,
		 96, 78, 158, 117, 60, 39, 102, 191, SYS_access, SYS_brk,
		SYS_close, SYS_execve, SYS_exit_group, SYS_fcntl, SYS_fstat, SYS_futex,
		SYS_getegid, SYS_geteuid, SYS_getgid, SYS_getrlimit, SYS_getuid,
		SYS_ioctl, SYS_lseek, SYS_mmap, SYS_mprotect, SYS_munmap, SYS_open,
		SYS_read, SYS_readlink, SYS_rt_sigaction, SYS_rt_sigprocmask,
		SYS_set_robust_list, SYS_set_thread_area, SYS_set_tid_address, SYS_stat,
		SYS_time, SYS_uname, SYS_write, 0 };
//c-sharp
int LANG_CSV[256] = {0,39,157,302,1,2,3,4,5,8,9,10,11,12,13,14,16,21,24,41,42,56,59,63,72,78,79,89,97
		,102,131,137,158,202,204,218,229,231,234,257,273, 257, 141, 95, 64, 65, 66
		, 83, 24, 42, 41, 158, 117, 60,
		39, 102, 191, SYS_access, SYS_brk, SYS_chmod, SYS_clock_getres,
		SYS_clock_gettime, SYS_clone, SYS_close, SYS_execve, SYS_exit_group,
		SYS_fcntl, SYS_fstat, SYS_ftruncate, SYS_futex, SYS_getcwd,
		SYS_getdents, SYS_geteuid, SYS_getpid, SYS_getppid, SYS_getrlimit,
		SYS_gettimeofday, SYS_getuid, SYS_ioctl, SYS_lseek, SYS_lstat, SYS_mmap,
		SYS_mprotect, SYS_mremap, SYS_munmap, SYS_open, SYS_read, SYS_readlink,
		SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_sched_getaffinity,
		SYS_sched_getparam, SYS_sched_get_priority_max,
		SYS_sched_get_priority_min, SYS_sched_getscheduler, SYS_set_robust_list,
		SYS_set_thread_area, SYS_set_tid_address, SYS_sigaltstack, SYS_stat,
		SYS_statfs, SYS_tgkill, SYS_time, SYS_uname, SYS_unlink, SYS_write, 0 };
//objective-c
int LANG_OV[256] = { 0,1,2,3,4,5,9,10,12,21,59,158,231, 102, 191, SYS_access, SYS_brk, SYS_close,
		SYS_execve, SYS_exit_group, SYS_fstat, SYS_futex, SYS_getcwd,
		SYS_getrlimit, SYS_gettimeofday, SYS_mmap, SYS_mprotect, SYS_munmap,
		SYS_open, SYS_read, SYS_readlink, SYS_rt_sigaction, SYS_rt_sigprocmask,
		SYS_set_robust_list, SYS_set_thread_area, SYS_set_tid_address,
		SYS_uname, SYS_write, 0 };
//freebasic
int LANG_BASICV[256] = { 0,1,2,3,4,5,9,10,12,13,14,16,21,59,97,158,173,202,218,231,273,
		101, 54, 122, 175, 174, 240, 311, 258, 243, 6, 197,
		252, 146, 195, 192, 33, 45, 125, 191, SYS_access, SYS_brk, SYS_close,
		SYS_execve, SYS_exit_group, SYS_fstat, SYS_futex, SYS_getrlimit,
		SYS_ioctl, SYS_ioperm, SYS_mmap, SYS_open, SYS_read, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_set_robust_list, SYS_set_thread_area,
		SYS_set_tid_address, SYS_stat, SYS_uname, SYS_write, 0 };
//scheme
int LANG_SV[256] = { 1, 23, 100, 61, 22, 6, 33, 8, 13, 16, 111, 110, 39, 79,
		SYS_fcntl, SYS_getdents64, SYS_getrlimit, SYS_rt_sigprocmask, SYS_futex,
		SYS_read, SYS_mmap, SYS_stat, SYS_open, SYS_close, SYS_execve,
		SYS_access, SYS_brk, SYS_readlink, SYS_munmap, SYS_close, SYS_uname,
		SYS_clone, SYS_uname, SYS_mprotect, SYS_rt_sigaction, SYS_getrlimit,
		SYS_fstat, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid,
		SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list,
		SYS_exit_group, 158, 0 };
//lua
int LANG_LUAV[256]={0,1,2,3,4,5,9,10,11,12,13,21,59,158,231,292,0};
//nodejs javascript
int LANG_JSV[256]={0,1,2,3,4,5,6,7,9,10,11,12,13,14,16,21,56,59,79,89,96,97,102,104,107,108,158,160,186,202,218,228,229,231,232,233,273,290,291,293,0};
//go-lang
int LANG_GOV[256]={0,1,9,11,13,14,56,59,131,158,202,204,228,231,0};
