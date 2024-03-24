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
int LANG_CV[CALL_ARRAY_SIZE] = {3,4,8,10,11,33,45,85,122,125,140,146,191,192,197,240,243,252,258,272,311,355,383,386,SYS_time, SYS_read, SYS_uname, SYS_write, SYS_open,
		SYS_close, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect,
		SYS_mmap2, SYS_fstat64, SYS_set_thread_area, 463, 0 };
//pascal
int LANG_PV[CALL_ARRAY_SIZE] = { 0,9, 59, 97, 13, 16, 89, 140, 91, 175, 195, 13, SYS_open, SYS_set_thread_area,
		SYS_brk, SYS_read, SYS_uname, SYS_write, SYS_execve, SYS_ioctl,
		SYS_readlink, SYS_mmap, SYS_rt_sigaction, SYS_getrlimit, 252, 191, 0 };
//java
int LANG_JV[CALL_ARRAY_SIZE] = { 3,6,11,20,33,45,85,91,120,122,125,172,174,175,191,192,195,197,240,243,252,258,295,311
		, SYS_fcntl64, SYS_getdents64, SYS_ugetrlimit,
		SYS_rt_sigprocmask, SYS_futex, SYS_read, SYS_mmap2, SYS_stat64,
		SYS_open, SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_readlink,
		SYS_munmap, SYS_close, SYS_uname, SYS_clone, SYS_uname, SYS_mprotect,
		SYS_rt_sigaction, SYS_sigprocmask, SYS_getrlimit, SYS_fstat64,
		SYS_getuid32, SYS_getgid32, SYS_geteuid32, SYS_getegid32,
		SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list,
		SYS_exit_group, 0 };
//ruby
int LANG_RV[CALL_ARRAY_SIZE] = { 3,4,5,6,11,33,42,45,54,77,78,91,120,122,125,174,175,183,186,191,192,
		    195,196,197,199,200,201,202,221,240,243,252,258,311,
		340, 126, SYS_access, SYS_brk, SYS_close, SYS_execve,
		SYS_exit_group, SYS_fstat64, SYS_futex, SYS_getegid32, SYS_geteuid32,
		SYS_getgid32, SYS_getrlimit, SYS_gettimeofday, SYS_getuid32, SYS_mmap2,
		SYS_mprotect, SYS_munmap, SYS_open, SYS_read, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_set_robust_list, SYS_set_thread_area,
		SYS_set_tid_address, SYS_uname, SYS_write, 191, 195, 120, 7, 0 };
//bash
int LANG_BV[CALL_ARRAY_SIZE] = { 3,4,5,6,11,20,33,45,54,63,64,65,78,116,122,125,140,174,175,183,191,192,195,197,199,200,201,202,221,243,252,
		7, 120, 42, 158, 117, 60, 39, 102, 191, 183, SYS_access,
		SYS_brk, SYS_close, SYS_dup2, SYS_execve, SYS_exit_group, SYS_fcntl64,
		SYS_fstat64, SYS_getegid32, SYS_geteuid32, SYS_getgid32, SYS_getpgrp,
		SYS_getpid, SYS_getppid, SYS_getrlimit, SYS_gettimeofday, SYS_getuid32,
		SYS_ioctl, SYS__llseek, SYS_mmap2, SYS_mprotect, SYS_munmap, SYS_open,
		SYS_read, SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_set_thread_area,
		SYS_stat64, SYS_time, SYS_uname, SYS_write,0 };
//python

int LANG_YV[CALL_ARRAY_SIZE]={3,4,5,6,11,20,22,33,41,45,54,85,91,116,122,125,140,174,175,183,
		191,192,195,196,197,199,200,201,202,220,221,243,252,258,265,295,
		311,13,41,91,102,186,221,240,295,355,0};
//php
int LANG_PHV[CALL_ARRAY_SIZE] = {3,4,5,6,11,13,33,45,54,78,91,122,125,140,174,175,183,191,192,195,
		     196,197,240,243,252,258,295,311,146, 158, 117, 60, 39, 102, SYS_access, SYS_brk,
		SYS_clone, SYS_close, SYS_execve, SYS_exit_group, SYS_fcntl64,
		SYS_fstat64, SYS_futex, SYS_getcwd, SYS_getdents64, SYS_getrlimit,
		SYS_gettimeofday, SYS_ioctl, SYS__llseek, SYS_lstat64, SYS_mmap2,
		SYS_mprotect, SYS_munmap, SYS_open, SYS_read, SYS_readlink,
		SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_set_robust_list,
		SYS_set_thread_area, SYS_set_tid_address, SYS_stat64, SYS_time,
		SYS_uname, SYS_write, 0 };
//perl
int LANG_PLV[CALL_ARRAY_SIZE] = { 78, 158, 117, 60, 39, 102, 191, SYS_access, SYS_brk,
		SYS_close, SYS_execve, SYS_exit_group, SYS_fcntl64, SYS_fstat64,
		SYS_futex, SYS_getegid32, SYS_geteuid32, SYS_getgid32, SYS_getrlimit,
		SYS_getuid32, SYS_ioctl, SYS__llseek, SYS_mmap2, SYS_mprotect,
		SYS_munmap, SYS_open, SYS_read, SYS_readlink, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_set_robust_list, SYS_set_thread_area,
		SYS_set_tid_address, SYS_stat64, SYS_time, SYS_uname, SYS_write, 0 };
//c-sharp
int LANG_CSV[CALL_ARRAY_SIZE] = {3,4,5,6,11,13,33,45,54,78,85,91,99,102,120,122,125,140,141,158,174,175,
			183,186,191,192,195,197,199,221,240,242,243,252,258,265,266,270,295,
		311, 11,33,45,192,141, 158, 117, 60, 39, 102, 191, SYS_access, SYS_brk,
		SYS_chmod, SYS_clock_getres, SYS_clock_gettime, SYS_clone, SYS_close,
		SYS_execve, SYS_exit_group, SYS_fcntl64, SYS_fstat64, SYS_ftruncate64,
		SYS_futex, SYS_getcwd, SYS_getdents64, SYS_geteuid32, SYS_getpid,
		SYS_getppid, SYS_getrlimit, SYS_gettimeofday, SYS_getuid32, SYS_ioctl,
		SYS__llseek, SYS_lstat64, SYS_mmap2, SYS_mprotect, SYS_mremap,
		SYS_munmap, SYS_open, SYS_read, SYS_readlink, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_sched_getaffinity, SYS_sched_getparam,
		SYS_sched_get_priority_max, SYS_sched_get_priority_min,
		SYS_sched_getscheduler, SYS_set_robust_list, SYS_set_thread_area,
		SYS_set_tid_address, SYS_sigaltstack, SYS_stat64, SYS_statfs,
		SYS_tgkill, SYS_time, SYS_uname, SYS_unlink, SYS_write, 0 };
//objective-c
int LANG_OV[CALL_ARRAY_SIZE] = {3,221,102, 191, SYS_access, SYS_brk, SYS_close, SYS_execve,
		SYS_exit_group, SYS_fstat64, SYS_futex, SYS_getcwd, SYS_geteuid32,
		SYS_getrlimit, SYS_gettimeofday, SYS_getuid32, SYS__llseek, SYS_lstat64,
		SYS_mmap2, SYS_mprotect, SYS_munmap, SYS_open, SYS_read, SYS_readlink,
		SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_set_robust_list,
		SYS_set_thread_area, SYS_set_tid_address, SYS_stat64, SYS_uname,
		SYS_write, 0 };
//freebasic
int LANG_BASICV[CALL_ARRAY_SIZE] = {3,4,5,6,11,33,45,54,91,101,122,125,140,174,175,191,192,195,197,240,243,252,258,311,330
		, SYS_access, SYS_brk, SYS_close, SYS_execve,
		SYS_exit_group, SYS_fstat64, SYS_futex, SYS_getrlimit, SYS_ioctl,
		SYS_ioperm, SYS_mmap2, SYS_open, SYS_read, SYS_rt_sigaction,
		SYS_rt_sigprocmask, SYS_set_robust_list, SYS_set_thread_area,
		SYS_set_tid_address, SYS_stat64, SYS_uname, SYS_write, 0 };
//scheme
int LANG_SV[CALL_ARRAY_SIZE] = { 100, 295, SYS_fcntl64, SYS_getdents64, SYS_ugetrlimit,
		SYS_rt_sigprocmask, SYS_futex, SYS_read, SYS_mmap2, SYS_stat64,
		SYS_open, SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_readlink,
		SYS_munmap, SYS_close, SYS_uname, SYS_clone, SYS_uname, SYS_mprotect,
		SYS_rt_sigaction, SYS_sigprocmask, SYS_getrlimit, SYS_fstat64,
		SYS_getuid32, SYS_getgid32, SYS_geteuid32, SYS_getegid32,
		SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list,
		SYS_exit_group, 0 };
//lua
int LANG_LUAV[CALL_ARRAY_SIZE]={3,4,5,6,11,13,33,45,91,125,174,192,195,197,243,252,330,0};
//nodejs
int LANG_JSV[CALL_ARRAY_SIZE]={3,4,5,6,11,33,45,54,75,78,85,91,120,122,125,168,174,175,183,191,192,195,196,197,199,200,201,202,224,240,243,252,255,256,258,265,266,311,328,329,331,0};
//go-lang
int LANG_GOV[CALL_ARRAY_SIZE]={3,4,11,120,123,174,175,186,192,240,242,252,265,0};

//sqlite3
int LANG_SQLV[CALL_ARRAY_SIZE]={3,4,5,6,11,33,45,54,102,122,125,140,174,175,183,191,192,195,197,199,221,240,243,252,258,311,0};
//fortran
int LANG_FV[CALL_ARRAY_SIZE]={3,4,11,33,45,85,122,174,197,243,252,0};
//matlab
int LANG_MV[CALL_ARRAY_SIZE]={3,4,6,11,20,33,45,54,78,85,91,102,122,125,140,146,168,174,175,183,191,192,195,196,197,199,220,221,240,242,243,252,258,295,311,355,0};
//Cobol
int LANG_CBV[CALL_ARRAY_SIZE]={3,4,6,11,20,33,45,54,78,85,91,102,122,125,140,146,168,174,175,183,191,192,195,196,197,199,220,221,240,242,243,252,258,295,311,355,0};
//R
int LANG_RLV[CALL_ARRAY_SIZE]={0,11,0};
