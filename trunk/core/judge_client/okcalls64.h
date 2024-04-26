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
int LANG_CV[CALL_ARRAY_SIZE] = {0,1,2,3,5,8,9,10,11,12,13,14,16,20,21,39,56,59,63,89,99,158,186,218,231,234,257,262,267,268,275,292,302,318,334,511,        
	SYS_write, SYS_mprotect, SYS_munmap, SYS_brk, SYS_arch_prctl, SYS_pread64, SYS_open, SYS_writev,
        SYS_time, SYS_futex, SYS_set_thread_area, SYS_access, SYS_clock_gettime, SYS_exit_group, SYS_mq_open,
        SYS_ioprio_get, SYS_unshare, SYS_set_robust_list, SYS_splice, SYS_close, SYS_stat, SYS_fstat, SYS_execve,
        SYS_uname, SYS_lseek, SYS_readlink, SYS_mmap, SYS_sysinfo, 0 };
//pascal
int LANG_PV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_munmap, SYS_brk, SYS_rt_sigaction, SYS_arch_prctl, SYS_ioctl,
        SYS_pread64, SYS_getxattr, SYS_open, SYS_time, SYS_set_thread_area, SYS_exit_group, SYS_ioprio_get, SYS_close,
        SYS_stat, SYS_execve, SYS_uname, SYS_readlink, SYS_mmap, SYS_getrlimit, 0 };
//java
int LANG_JV[CALL_ARRAY_SIZE] = {
        0, 262,318,334,435, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_getppid, SYS_getpgrp,
        SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_prctl, SYS_arch_prctl, SYS_ioctl, SYS_pread64, SYS_open,
        SYS_futex, SYS_set_thread_area, SYS_access, SYS_getdents64, SYS_set_tid_address, SYS_pipe, SYS_exit_group,
        SYS_openat, SYS_set_robust_list, SYS_close, SYS_prlimit64, SYS_dup2, SYS_getpid, SYS_stat, SYS_fstat, SYS_clone,
        SYS_execve, SYS_lstat, SYS_wait4, SYS_uname, SYS_fcntl, SYS_getcwd, SYS_lseek, SYS_readlink, SYS_mmap,
        SYS_getrlimit, 0 };
//ruby
int LANG_RV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_brk,
        SYS_capset, SYS_rt_sigaction, SYS_sigaltstack, SYS_rt_sigprocmask, SYS_arch_prctl, SYS_ioctl, SYS_pread64,
        SYS_open, SYS_futex, SYS_access, SYS_set_tid_address, SYS_pipe, SYS_exit_group, SYS_set_robust_list, SYS_close,
        SYS_stat, SYS_fstat, SYS_clone, SYS_execve, SYS_fcntl, SYS_mmap, SYS_gettimeofday, SYS_getrlimit,
        SYS_getrusage, 0 };
//bash
int LANG_BV[CALL_ARRAY_SIZE] = {
        0, 202,218,262,273,318,334,SYS_write, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_getppid,
        SYS_getpgrp, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_arch_prctl, SYS_ioctl, SYS_pread64,
        SYS_afs_syscall, SYS_getxattr, SYS_open, SYS_access, SYS_pipe, SYS_clock_nanosleep, SYS_exit_group, SYS_openat,
        SYS_close, SYS_prlimit64, SYS_dup2, SYS_getpid, SYS_stat, SYS_socket, SYS_connect, SYS_fstat, SYS_clone,
        SYS_execve, SYS_exit, SYS_wait4, SYS_uname, SYS_fcntl, SYS_getcwd, SYS_lseek, SYS_mmap, SYS_gettimeofday,
        SYS_getrlimit, SYS_sysinfo, 0 };
//python
int LANG_YV[CALL_ARRAY_SIZE] = {
        0,1,2,3,4,5,6,8,9,10,11,12,13,14,16,17,21,32,39,41,42,49,59,72,78,79,89,97,99,102,104,106,107,108,131,137,158,186,202,217,218,228,231,257,262,273,302,318,334,
	511,
        SYS_write, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_brk,
        SYS_rt_sigaction, SYS_sigaltstack, SYS_rt_sigprocmask, SYS_sched_get_priority_max, SYS_arch_prctl, SYS_ioctl,
        SYS_pread64, SYS_getxattr, SYS_open, SYS_futex, SYS_access, SYS_getdents64, SYS_set_tid_address, SYS_clock_gettime,
        SYS_exit_group, SYS_mremap, SYS_openat, SYS_unshare, SYS_set_robust_list, SYS_close, SYS_prlimit64,
        SYS_dup, SYS_getpid, SYS_stat, SYS_socket, SYS_connect, SYS_fstat, SYS_execve, SYS_lstat,
        SYS_exit, SYS_fcntl, SYS_getdents, SYS_getcwd, SYS_lseek, SYS_readlink, SYS_mmap, SYS_getrlimit,
        SYS_sysinfo, 0 };
//php
int LANG_PHV[CALL_ARRAY_SIZE] = {
        0,1,3,4,5,6,8,9,10,11,12,13,14,16,17,21,59,79,99,158,202,218,231,257,262,273,302,318,334,
	SYS_write, SYS_mprotect, SYS_munmap, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask,
        SYS_sched_get_priority_max, SYS_arch_prctl, SYS_ioctl, SYS_pread64, SYS_getxattr, SYS_open, SYS_writev,
        SYS_time, SYS_futex, SYS_set_thread_area, SYS_access, SYS_getdents64, SYS_set_tid_address, SYS_exit_group,
        SYS_openat, SYS_set_robust_list, SYS_close, SYS_getpid, SYS_stat, SYS_fstat, SYS_clone, SYS_execve, SYS_lstat,
        SYS_exit, SYS_uname, SYS_fcntl, SYS_getdents, SYS_getcwd, SYS_lseek, SYS_readlink, SYS_mmap, SYS_gettimeofday,
        SYS_getrlimit, 0 };
//perl
int LANG_PLV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_brk,
        SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_arch_prctl, SYS_ioctl, SYS_pread64, SYS_getxattr, SYS_open, SYS_time,
        SYS_futex, SYS_set_thread_area, SYS_access, SYS_set_tid_address, SYS_exit_group, SYS_set_robust_list, SYS_close,
        SYS_getpid, SYS_stat, SYS_fstat, SYS_execve, SYS_exit, SYS_uname, SYS_fcntl, SYS_getdents, SYS_lseek,
        SYS_readlink, SYS_mmap, SYS_gettimeofday, SYS_getrlimit, 0 };
//c-sharp
int LANG_CSV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_getuid, SYS_geteuid, SYS_munmap, SYS_getppid, SYS_brk, SYS_rt_sigaction,
        SYS_sigaltstack, SYS_statfs, SYS_rt_sigprocmask, SYS_setpriority, SYS_sched_getparam, SYS_sched_getscheduler,
        SYS_sched_get_priority_max, SYS_sched_get_priority_min, SYS_prctl, SYS_arch_prctl, SYS_ioctl, SYS_pread64,
        SYS_getxattr, SYS_open, SYS_time, SYS_futex, SYS_sched_getaffinity, SYS_set_thread_area, SYS_access,
        SYS_getdents64, SYS_set_tid_address, SYS_clock_gettime, SYS_clock_getres, SYS_exit_group, SYS_tgkill,
        SYS_sched_yield, SYS_mremap, SYS_openat, SYS_set_robust_list, SYS_close, SYS_prlimit64, SYS_getpid, SYS_stat,
        SYS_socket, SYS_connect, SYS_fstat, SYS_clone, SYS_execve, SYS_lstat, SYS_exit, SYS_uname, SYS_semget,
        SYS_semop, SYS_semctl, SYS_fcntl, SYS_ftruncate, SYS_getdents, SYS_getcwd, SYS_lseek, SYS_mkdir, SYS_unlink,
        SYS_readlink, SYS_mmap, SYS_chmod, SYS_umask, SYS_gettimeofday, SYS_getrlimit, 0 };
//objective-c
int LANG_OV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_getuid, SYS_munmap, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask,
        SYS_arch_prctl, SYS_pread64, SYS_getxattr, SYS_open, SYS_futex, SYS_set_thread_area, SYS_access,
        SYS_set_tid_address, SYS_exit_group, SYS_set_robust_list, SYS_close, SYS_stat, SYS_fstat, SYS_execve, SYS_uname,
        SYS_getcwd, SYS_readlink, SYS_mmap, SYS_gettimeofday, SYS_getrlimit, 0 };
//freebasic
int LANG_BASICV[CALL_ARRAY_SIZE] = {
       0,1,3,4,5,9,10,12,13,14,16,17,21,59,158,173,218,231,257,273,302, SYS_write, SYS_mprotect, SYS_ptrace, SYS_brk, SYS_setfsuid, SYS_capget, SYS_rt_sigaction,
        SYS_rt_sigprocmask, SYS_sched_get_priority_max, SYS_arch_prctl, SYS_ioctl, SYS_pread64, SYS_ioperm,
        SYS_create_module, SYS_init_module, SYS_getxattr, SYS_lgetxattr, SYS_llistxattr, SYS_removexattr, SYS_open,
        SYS_futex, SYS_set_thread_area, SYS_access, SYS_set_tid_address, SYS_exit_group, SYS_mq_open,
        SYS_mq_timedreceive, SYS_ioprio_get, SYS_mkdirat, SYS_set_robust_list, SYS_close, SYS_process_vm_writev,
        SYS_dup2, SYS_stat, SYS_recvfrom, SYS_fstat, SYS_setsockopt, SYS_execve, SYS_lstat, SYS_uname, SYS_mmap,
        SYS_getrlimit, 0 };
//scheme
int LANG_SV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_times, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap,
        SYS_getppid, SYS_getpgrp, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_arch_prctl, SYS_ioctl, SYS_open,
        SYS_futex, SYS_set_thread_area, SYS_access, SYS_getdents64, SYS_set_tid_address, SYS_pipe, SYS_select,
        SYS_exit_group, SYS_set_robust_list, SYS_close, SYS_dup2, SYS_getpid, SYS_stat, SYS_fstat, SYS_clone,
        SYS_execve, SYS_lstat, SYS_wait4, SYS_uname, SYS_fcntl, SYS_getcwd, SYS_lseek, SYS_readlink, SYS_mmap,
        SYS_getrlimit, 0 };
//lua
int LANG_LUAV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_munmap, SYS_brk, SYS_rt_sigaction, SYS_arch_prctl, SYS_pread64, SYS_open,
        SYS_access, SYS_exit_group, SYS_dup3, SYS_close, SYS_stat, SYS_fstat, SYS_execve, SYS_mmap, 0 };
//nodejs javascript
int LANG_JSV[CALL_ARRAY_SIZE] = {
	0,1,3,4,5,9,10,11,12,13,14,16,17,21,28,39,56,59,79,89,102,104,107,108,158,202,218,229,231,232,233,257,273,290,291,293,302,318,332
        , SYS_write, SYS_mprotect, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_munmap, SYS_brk,
        SYS_rt_sigaction, SYS_rt_sigprocmask, SYS_arch_prctl, SYS_ioctl, SYS_setrlimit, SYS_pread64, SYS_gettid,
        SYS_open, SYS_writev, SYS_futex, SYS_access, SYS_set_tid_address, SYS_clock_gettime, SYS_clock_getres,
        SYS_exit_group, SYS_epoll_wait, SYS_epoll_ctl, SYS_set_robust_list, SYS_eventfd2, SYS_epoll_create1, SYS_pipe2,
        SYS_close, SYS_stat, SYS_fstat, SYS_clone, SYS_execve, SYS_lstat, SYS_poll, SYS_getcwd, SYS_readlink, SYS_mmap,
        SYS_gettimeofday, SYS_getrlimit, 0 };
//go-lang
int LANG_GOV[CALL_ARRAY_SIZE] = {
        0, 3, 35,511, SYS_write, SYS_mprotect, SYS_munmap, SYS_rt_sigaction, SYS_sigaltstack, SYS_rt_sigprocmask,
        SYS_arch_prctl, SYS_pread64, SYS_gettid, SYS_futex, SYS_sched_getaffinity, SYS_clock_gettime, SYS_exit_group,
        SYS_sched_yield, SYS_openat, SYS_readlinkat, SYS_clone, SYS_execve, SYS_fcntl, SYS_mmap, 0 };
//sqlite3
int LANG_SQLV[CALL_ARRAY_SIZE] = {0,8,11,18,75,87,1,3,4,5,6,9,10,12,13,14,16,17,21,39,41,42,59,72,79,102,107,158,218,231,257,262,273,302,318,334,
        SYS_write, SYS_mprotect, SYS_getuid, SYS_geteuid, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask,
        SYS_arch_prctl, SYS_ioctl, SYS_pread64, SYS_open, SYS_futex, SYS_access, SYS_set_tid_address, SYS_exit_group,
        SYS_set_robust_list, SYS_close, SYS_stat, SYS_socket, SYS_connect, SYS_fstat, SYS_execve, SYS_semget, SYS_fcntl,
        SYS_fsync, SYS_getcwd, SYS_lseek, SYS_unlink, SYS_mmap, SYS_getrlimit, 0 };
//fortran
int LANG_FV[CALL_ARRAY_SIZE] = {
        0, SYS_write, SYS_mprotect, SYS_brk, SYS_rt_sigaction, SYS_arch_prctl, SYS_pread64, SYS_access,
        SYS_exit_group, SYS_fstat, SYS_execve, SYS_uname, SYS_readlink, 0 };
//octave
int LANG_MV[CALL_ARRAY_SIZE] = {
         0,1,3,4,5,6,8,9,10,11,12,13,14,16,17,21,39,41,42,44,47,49,51,54,56,59,63,79,89,102,158,202,204,217,218,231,257,273,302,318,
		  SYS_write, SYS_mprotect, SYS_getuid, SYS_munmap, SYS_brk, SYS_rt_sigaction, SYS_rt_sigprocmask,
        SYS_arch_prctl, SYS_ioctl, SYS_writev, SYS_futex, SYS_sched_getaffinity, SYS_access, SYS_set_tid_address,
        SYS_exit_group, SYS_openat, SYS_set_robust_list, SYS_close, SYS_prlimit64, 318, SYS_getpid, SYS_stat,
        SYS_socket, SYS_connect, SYS_recvfrom, SYS_shutdown, SYS_fstat, SYS_getpeername, SYS_clone, SYS_execve,
        SYS_lstat, SYS_uname, SYS_poll, SYS_fcntl, SYS_getdents, SYS_getcwd, SYS_lseek, SYS_readlink, SYS_mmap,
        SYS_gettimeofday, 0 };
//Cobal
int LANG_CBV[CALL_ARRAY_SIZE]={0,1,3,4,5,8,9,10,11,12,13,14,17,21,41,42,59,89,158,202,218,231,257,273,302,0};

//R Lang
int LANG_RLV[CALL_ARRAY_SIZE]={0,1,3,8,9,10,11,12,13,14,16,17,21,33,39,41,42,56,59,61,63,72,79,83,89,98,99,102,104,107,
                               108,110,111,131,158,202,204,217,218,231,257,262,273,293,302,318,334,435,439,0};

int LANG_SB3V[CALL_ARRAY_SIZE]={0,1,3,5,9,10,11,12,13,14,16,17,21,28,39,56,59,63,72,89,99,102,104,107,108,158,202,218,231,232,233,257,262,273,290,291,293,302,318,332,334,435,0};
