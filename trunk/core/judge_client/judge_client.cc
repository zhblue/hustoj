// File:   judge_client.cc
// Author: sempr
// refacted by zhblue
/*
 * Copyright 2008 sempr <iamsempr@gmail.com>
 *
 * Refacted and modified by zhblue<newsclan@gmail.com>
 * Bug report email newsclan@gmail.com
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

#include <stdio.h>
#include <syslog.h>
#include <errno.h>
#include <fcntl.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <unistd.h>
#include <time.h>
#include <stdarg.h>
#include <ctype.h>
#include <sys/wait.h>
#include <sys/ptrace.h>
#include <sys/types.h>
#include <sys/user.h>
#include <sys/syscall.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <sys/signal.h>
//#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#ifdef OJ_USE_MYSQL
	#include <mysql/mysql.h>
#endif
#include <assert.h>
#include "okcalls.h"

#define STD_MB 1048576LL
#define STD_T_LIM 2
#define STD_F_LIM (STD_MB << 9) //default file size limit 512m ,2^9=512
#define STD_M_LIM (STD_MB << 8) //default memory limit 256m ,2^8=256
#define BUFFER_SIZE 4096		//default size of char buffer 5120 bytes
#define LOCKMODE (S_IRUSR|S_IWUSR|S_IRGRP|S_IROTH)

#define OJ_WT0 0     //提交排队
#define OJ_WT1 1     //重判排队
#define OJ_CI 2      //编译中（任务已派发）
#define OJ_RI 3      //运行中
#define OJ_AC 4      //答案正确
#define OJ_PE 5      //格式错误
#define OJ_WA 6      //答案错误
#define OJ_TL 7      //时间超限
#define OJ_ML 8      //内存超限
#define OJ_OL 9      //输出超限
#define OJ_RE 10     //运行错误
#define OJ_CE 11     //编译错误
#define OJ_CO 12     //编译完成
#define OJ_TR 13     //测试运行结束
#define OJ_MC 14     // 等待裁判手工确认

#ifdef __arm__             // arm 的寄存器结构
struct user_regs_struct {
        long uregs[18];
};
#define ARM_r7          uregs[7]
#define ARM_ORIG_r0     uregs[17]

#define REG_SYSCALL ARM_r7
#endif

#ifdef __aarch64__          //arm64的寄存器结构  
#define NT_PRSTATUS	1
#define NT_ARM_SYSTEM_CALL	0x404
#define ARM_cpsr	uregs[16]
#define ARM_pc		uregs[15]
#define ARM_lr		uregs[14]
#define ARM_sp		uregs[13]
#define ARM_ip		uregs[12]
#define ARM_fp		uregs[11]
#define ARM_r10		uregs[10]
#define ARM_r9		uregs[9]
#define ARM_r8		regs[8]
#define ARM_r7		uregs[7]
#define ARM_r6		uregs[6]
#define ARM_r5		uregs[5]
#define ARM_r4		uregs[4]
#define ARM_r3		uregs[3]
#define ARM_r2		uregs[2]
#define ARM_r1		uregs[1]
#define ARM_r0		uregs[0]
#define ARM_ORIG_r0	uregs[17]
#define PTRACE_GETREGS PTRACE_GETREGSET
#define PTRACE_SETREGS PTRACE_SETREGSET
#define REG_SYSCALL regs[18]

#endif 

#ifdef __mips__                 //mips 龙芯的寄存器结构
	typedef unsigned long long uint64_t;
	struct user_regs_struct{
		uint64_t uregs[38];
	};


	#define REG_V0 2
	#define REG_A0 4

	#define mips_REG_V0 uregs[REG_V0]
	#define REG_SYSCALL mips_REG_V0

#endif

#ifdef __i386          //32位x86寄存器
#define REG_SYSCALL orig_eax
#define REG_RET eax
#define REG_ARG0 ebx
#define REG_ARG1 ecx
#endif

#ifdef __x86_64__      //64位x86寄存器
#define REG_SYSCALL orig_rax
#define REG_RET rax
#define REG_ARG0 rdi
#define REG_ARG1 rsi

#endif



static int DEBUG = 0;
static char host_name[BUFFER_SIZE/10];     //数据库服务器地址
static int port_number;                    //端口
static char user_name[BUFFER_SIZE/10];     //用户名
static char password[BUFFER_SIZE/10];      //密码
static char db_name[BUFFER_SIZE/10];       //库名
static char oj_home[BUFFER_SIZE/10];       //判题系统主目录
static char data_list[BUFFER_SIZE][BUFFER_SIZE]; //测试数据列表
static int data_list_len = 0;                    //列表长度
static char lock_file[BUFFER_SIZE]="/home/judge/run0/judge_client.pid";     //工作目录锁定文件


static int max_running;       
static int sleep_time;
static int java_time_bonus = 5;
static int java_memory_bonus = 512;
static char java_xms[BUFFER_SIZE/10];
static char java_xmx[BUFFER_SIZE/10];
static int sim_enable = 0;
static int oi_mode = 0;
static int full_diff = 0;
static int raw_text_diff = 1;
static int use_max_time = 0;
static int time_limit_to_total= 1;
static int total_time= 0;

static int http_judge = 0;
static int copy_data= 0;
static char http_baseurl[BUFFER_SIZE/10];
static char http_apipath[BUFFER_SIZE/10];
static char http_loginpath[BUFFER_SIZE/10];
static char http_username[BUFFER_SIZE/10];
static char http_password[BUFFER_SIZE/10];
static int http_download = 1;
static double cpu_compensation = 1.0;

static int shm_run = 0;

static char record_call = 0;
static int use_ptrace = 1;
static int ignore_esol= 1;
static int compile_chroot = 0;
static int turbo_mode = 0;
static int python_free=0;
static int use_docker=0;
static const char *tbname = "solution";
static char cc_opt[BUFFER_SIZE/10];
static char cc_std[BUFFER_SIZE/10];
static char cpp_std[BUFFER_SIZE/10];
static int auto_result = OJ_AC ;

int num_of_test = 0;
//static int sleep_tmp;
size_t prelen=16;
static int py2=1; // caution: py2=1 means default using py3

#define ZOJ_COM

#ifdef _mysql_h
MYSQL *conn;
#endif
static char jresult[15][4]={"PD","PR","CI","RJ","AC","PE","WA","TLE","MLE","OLE","RE","CE","CO","TR","MC"};
static char lang_ext[24][8] = {"c", "cc", "pas", "java", "rb", "sh", "py",
			       "php", "pl", "cs", "m", "bas", "scm", "c", "cc", "lua", "js", "go","sql","f95","m","cob","R","sb3"};
//static char buf[BUFFER_SIZE];

int lockfile(int fd) {
	struct flock fl;
	fl.l_type = F_WRLCK;
	fl.l_start = 0;
	fl.l_whence = SEEK_SET;
	fl.l_len = 0;
	return (fcntl(fd, F_SETLK, &fl));
}

int already_running() {
	int fd;
	char buf[16];
	fd = open(lock_file, O_RDWR | O_CREAT, LOCKMODE);
	if (fd < 0) {
		if(DEBUG)printf("%s open fail.\n",lock_file);
		exit(1);
	}
	if (lockfile(fd) < 0) {
		if (errno == EACCES || errno == EAGAIN) {
			close(fd);
			return 1;
		}
		
		if(DEBUG)printf("%s lock fail.\n",lock_file);
		exit(1);
	}
	if(ftruncate(fd, 0)) printf("close file fail 0 \n");
	sprintf(buf, "%d", getpid());
	if(write(fd, buf, strlen(buf) + 1)>=BUFFER_SIZE) printf("buffer size overflow!...\n");
	return (0);
}
void print_arm_regs(long long unsigned int *d){
	for(int i=0;i<32;i++){
		printf("[%d]:%lld ",i,d[i]%CALL_ARRAY_SIZE);
	}
	printf("\n");
}
int data_list_has(char *file)
{
	for (int i = 0; i < data_list_len; i++)
	{
		if (strcmp(data_list[i], file) == 0)
			return 1;
	}
	return 0;
}
int data_list_add(char *file)
{
	if (data_list_len < BUFFER_SIZE - 1)
	{
		strcpy(data_list[data_list_len], file);
		data_list_len++;
		return 0;
	}
	else
	{
		return 1;
	}
}
long get_file_size(const char *filename)
{
	struct stat f_stat;

	if (stat(filename, &f_stat) == -1)
	{
		return 0;
	}

	return (long)f_stat.st_size;
}

void write_log(const char *_fmt, ...)
{
	va_list ap;
	char fmt[BUFFER_SIZE];
	strncpy(fmt, _fmt,BUFFER_SIZE);
	char buffer[BUFFER_SIZE];
	//      time_t          t = time(NULL);
	//int l;
	sprintf(buffer, "%s/log/client.log", oj_home);
	FILE *fp = fopen(buffer, "ae+");
	if (fp == NULL)
	{
		fprintf(stderr, "%s/log/client.log openfile error!\n",oj_home);
		exit(-6);
	}
	va_start(ap, _fmt);
	//l =
	vsprintf(buffer, fmt, ap);
	fprintf(fp, "%s\n", buffer);
	if (DEBUG)
		printf("%s\n", buffer);
	va_end(ap);
	fclose(fp);
}
int execute_cmd(const char *fmt, ...)   //执行命令获得返回值
{
	char cmd[BUFFER_SIZE];

	int ret = 0;
	va_list ap;

	va_start(ap, fmt);
	vsprintf(cmd, fmt, ap);
	if (DEBUG)
		printf("%s\n", cmd);
	ret = system(cmd);
	va_end(ap);
	return ret;
}

const int call_array_size = CALL_ARRAY_SIZE;
unsigned int call_id = 0;
int call_counter[call_array_size] = {0};
static char LANG_NAME[BUFFER_SIZE];
void init_syscalls_limits(int lang)      //白名单初始化
{
	int i;
	memset(call_counter, 0, sizeof(call_counter));
	if (DEBUG)
		write_log("init_call_counter:%d", lang);
	if (record_call)
	{ // recording for debuging
		for (i = 0; i < call_array_size; i++)
		{
			call_counter[i] = 0;
		}
	}
	else if (lang <= LANG_CPP || lang == LANG_CLANG || lang == LANG_CLANGPP )
	{ // C & C++
		for (i = 0; i == 0 || LANG_CV[i]; i++)
		{
			call_counter[LANG_CV[i]] = HOJ_MAX_LIMIT;
		}
	}
	else if (lang == LANG_PASCAL )
	{ // Pascal
		for (i = 0; i == 0 || LANG_PV[i]; i++)
			call_counter[LANG_PV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_JAVA)
	{ // Java
		for (i = 0; i == 0 || LANG_JV[i]; i++)
			call_counter[LANG_JV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_RUBY)
	{ // Ruby
		for (i = 0; i == 0 || LANG_RV[i]; i++)
			call_counter[LANG_RV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_BASH)
	{ // Bash
		for (i = 0; i == 0 || LANG_BV[i]; i++)
			call_counter[LANG_BV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_PYTHON)
	{ // Python
		for (i = 0; i == 0 || LANG_YV[i]; i++)
			call_counter[LANG_YV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_PHP)
	{ // php
		for (i = 0; i == 0 || LANG_PHV[i]; i++)
			call_counter[LANG_PHV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_PERL )
	{ // perl
		for (i = 0; i == 0 || LANG_PLV[i]; i++)
			call_counter[LANG_PLV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_CSHARP )
	{ // mono c#
		for (i = 0; i == 0 || LANG_CSV[i]; i++)
			call_counter[LANG_CSV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_OBJC )
	{ //objective c
		for (i = 0; i == 0 || LANG_OV[i]; i++)
			call_counter[LANG_OV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_FREEBASIC)
	{ //free basic
		for (i = 0; i == 0 || LANG_BASICV[i]; i++)
			call_counter[LANG_BASICV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_SCHEME )
	{ //scheme guile
		for (i = 0; i == 0 || LANG_SV[i]; i++)
			call_counter[LANG_SV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_LUA )
	{ //lua
		for (i = 0; i == 0 || LANG_LUAV[i]; i++)
			call_counter[LANG_LUAV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_JS )
	{ //nodejs
		for (i = 0; i == 0 || LANG_JSV[i]; i++)
			call_counter[LANG_JSV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_GO )
	{ //go
		for (i = 0; i == 0 || LANG_GOV[i]; i++)
			call_counter[LANG_GOV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_SQL )
	{ //go
		for (i = 0; i == 0 || LANG_SQLV[i]; i++)
			call_counter[LANG_SQLV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_FORTRAN )
	{ //go
		for (i = 0; i == 0 || LANG_FV[i]; i++)
			call_counter[LANG_FV[i]] = HOJ_MAX_LIMIT;
	}
	else if (lang == LANG_MATLAB )
        { //matlab
                for (i = 0; i == 0 || LANG_MV[i]; i++)
                        call_counter[LANG_MV[i]] = HOJ_MAX_LIMIT;
        }
	else if (lang == LANG_COBOL )
	{ //cobol
		for (i = 0; i == 0 || LANG_CBV[i]; i++)
			call_counter[LANG_CBV[i]] = HOJ_MAX_LIMIT;
	}else if(lang == LANG_R ){
		for (i = 0; i == 0 || LANG_RLV[i]; i++)
			call_counter[LANG_RLV[i]] = HOJ_MAX_LIMIT;
	}else if(lang == LANG_SB3 ){
		for (i = 0; i == 0 || LANG_SB3V[i]; i++)
			call_counter[LANG_SB3V[i]] = HOJ_MAX_LIMIT;
	}
#ifdef __aarch64__
	if (lang==3)call_counter[220]= 100;
	else call_counter[220]= 1;
#else
	call_counter[SYS_execve %  call_array_size ]= 1;
	if(lang == LANG_R ) call_counter[SYS_execve %  call_array_size ]= 8;
#endif
	printf("SYS_execve:%d [%d] \n",SYS_execve  % call_array_size , call_counter[SYS_execve %  call_array_size ]);
}

int after_equal(char *c)
{
	int i = 0;
	for (; c[i] != '\0' && c[i] != '='; i++)
		;
	return ++i;
}
void trim(char *c)
{
	char buf[BUFFER_SIZE];
	char *start, *end;
	strcpy(buf, c);
	start = buf;
	while (isspace(*start))
		start++;
	end = start;
	while (!isspace(*end))
		end++;
	*end = '\0';
	strcpy(c, start);
}
bool read_buf(char *buf, const char *key, char *value)
{
	if (strncmp(buf, key, strlen(key)) == 0)
	{
		strcpy(value, buf + after_equal(buf));
		trim(value);
		if (DEBUG)
			printf("%s=%s\n", key, value);
		return 1;
	}
	return 0;
}
void read_double(char *buf, const char *key, double *value)
{
	char buf2[BUFFER_SIZE];
	if (read_buf(buf, key, buf2))
		if(1!=sscanf(buf2, "%lf", value)) printf("double value read fail\n");
}

void read_int(char *buf, const char *key, int *value)
{
	char buf2[BUFFER_SIZE];
	if (read_buf(buf, key, buf2))
		if(1!=sscanf(buf2, "%d", value)) printf("int value read fail\n");
}

FILE *read_cmd_output(const char *fmt, ...)
{
	char cmd[BUFFER_SIZE];

	FILE *ret = NULL;
	va_list ap;

	va_start(ap, fmt);
	vsprintf(cmd, fmt, ap);
	va_end(ap);
	if (DEBUG)
		printf("%s\n", cmd);
	ret = popen(cmd, "r");

	return ret;
}
// read the configue file
void init_judge_conf()   //读取判题主目录etc中的配置文件judge.conf
{
	FILE *fp = NULL;
	char buf[BUFFER_SIZE];
	host_name[0] = 0;
	user_name[0] = 0;
	password[0] = 0;
	db_name[0] = 0;
	port_number = 3306;
	max_running = 3;
	sleep_time = 3;
	strcpy(java_xms, "-Xms32m");
	strcpy(java_xmx, "-Xmx256m");
	strcpy(cc_std,"-std=c99");
	strcpy(cc_opt,"-O2");
	if(__GNUC__ > 9 || (  __GNUC__ == 9 &&  __GNUC_MINOR__ >= 3 ) ){ 
		// ubuntu20.04 introduce g++9.3
		strcpy(cc_std,"-std=c17");
		strcpy(cpp_std,"-std=c++14");    // CCF NOI change settings for NOIP to C++14 on 2021-9-1
	}else{
		strcpy(cc_std,"-std=c99");
		strcpy(cpp_std,"-std=c++11");
	}
	sprintf(buf, "%s/etc/judge.conf", oj_home);
	fp = fopen("./etc/judge.conf", "re");
	if (fp != NULL)
	{
		while (fgets(buf, BUFFER_SIZE - 1, fp))
		{
			read_buf(buf, "OJ_HOST_NAME", host_name);
			read_buf(buf, "OJ_USER_NAME", user_name);
			read_buf(buf, "OJ_PASSWORD", password);
			read_buf(buf, "OJ_DB_NAME", db_name);
			read_int(buf, "OJ_PORT_NUMBER", &port_number);
			read_int(buf, "OJ_JAVA_TIME_BONUS", &java_time_bonus);
			read_int(buf, "OJ_JAVA_MEMORY_BONUS", &java_memory_bonus);
			read_int(buf, "OJ_SIM_ENABLE", &sim_enable);
			read_buf(buf, "OJ_JAVA_XMS", java_xms);
			read_buf(buf, "OJ_JAVA_XMX", java_xmx);
			read_int(buf, "OJ_HTTP_JUDGE", &http_judge);
			read_buf(buf, "OJ_HTTP_BASEURL", http_baseurl);
			read_buf(buf, "OJ_HTTP_APIPATH", http_apipath);
			read_buf(buf, "OJ_HTTP_LOGINPATH", http_loginpath);
			read_buf(buf, "OJ_HTTP_USERNAME", http_username);
			read_buf(buf, "OJ_HTTP_PASSWORD", http_password);
			read_int(buf, "OJ_HTTP_DOWNLOAD", &http_download);
			read_int(buf, "OJ_OI_MODE", &oi_mode);
			read_int(buf, "OJ_FULL_DIFF", &full_diff);
			read_int(buf, "OJ_RAW_TEXT_DIFF", &raw_text_diff);
			read_int(buf, "OJ_SHM_RUN", &shm_run);
			read_int(buf, "OJ_USE_MAX_TIME", &use_max_time);
			read_int(buf, "OJ_TIME_LIMIT_TO_TOTAL", &time_limit_to_total);
			read_int(buf, "OJ_USE_PTRACE", &use_ptrace);
			read_int(buf, "OJ_IGNORE_ESOL", &ignore_esol);
			read_int(buf, "OJ_COMPILE_CHROOT", &compile_chroot);
			read_int(buf, "OJ_TURBO_MODE", &turbo_mode);
			read_double(buf, "OJ_CPU_COMPENSATION", &cpu_compensation);
			read_int(buf, "OJ_PYTHON_FREE", &python_free);
			read_int(buf, "OJ_COPY_DATA", &copy_data);
			read_int(buf, "OJ_USE_DOCKER",&use_docker);
			read_buf(buf, "OJ_CC_STD", cc_std);
			read_buf(buf, "OJ_CPP_STD", cpp_std);
			read_buf(buf, "OJ_CC_OPT", cc_opt);
			read_int(buf, "OJ_AUTO_RESULT", &auto_result);
		}
		fclose(fp);
	}
//	fclose(fp);
	if(use_docker){
		shm_run=0;
		compile_chroot=0;
	}
 	if(strcmp(http_username,"IP")==0){
                  FILE * fjobs = fopen("/etc/hostname","r");
                  if(1!=fscanf(fjobs, "%s", http_username)) printf("IP/HOSTNAME read fail...\n");
                  fclose(fjobs);
        }
	if(turbo_mode==2) tbname="solution2";
}
int isInFile(const char fname[])
{
	int l = strlen(fname);
	if (l <= 3 || strcmp(fname + l - 3, ".in") != 0)
		return 0;
	else
		return l - 3;
}
int inFile(const struct dirent * dp){   //获得测试数据目录中测试数据列表
	int l = strlen(dp->d_name);
	if(DEBUG) printf("file name:%s\n",dp->d_name);
	if(DEBUG) printf("ext name:%s\n",dp->d_name + l - 3);
	int ret = isInFile(dp->d_name);	
	if(DEBUG) printf("\t:%d\n",ret);
	return ret;
}

void find_next_nonspace(int &c1, int &c2, FILE *&f1, FILE *&f2, int &ret)
{
	// Find the next non-space character or \n.
	while ((isspace(c1)) || (isspace(c2)))
	{
		if (c1 != c2)
		{
			if (c2 == EOF)
			{
				do
				{
					c1 = fgetc(f1);
				} while (isspace(c1));
				continue;
			}
			else if (c1 == EOF)
			{
				do
				{
					c2 = fgetc(f2);
				} while (isspace(c2));
				continue;
			}else if(ignore_esol){			
				if (isspace(c1) && isspace(c2))
				{
					while (c2 == '\n' && isspace(c1) && c1 != '\n')
						c1 = fgetc(f1);
					while (c1 == '\n' && isspace(c2) && c2 != '\n')
						c2 = fgetc(f2);

				}
				else{
					if (DEBUG)
						printf("%d=%c\t%d=%c", c1, c1, c2, c2);
					;
					ret = OJ_PE;
				}
			}else if(!ignore_esol){
				if ((c1 == '\r' && c2 == '\n'))
				{
					c1 = fgetc(f1);
				}
				else if ((c2 == '\r' && c1 == '\n'))
				{
					c2 = fgetc(f2);
				}
				else{
					if (DEBUG)
						printf("%d=%c\t%d=%c", c1, c1, c2, c2);
					;
					ret = OJ_PE;
				}
			}
		}
		if (isspace(c1))
		{
			c1 = fgetc(f1);
		}
		if (isspace(c2))
		{
			c2 = fgetc(f2);
		}
	}
}

/***
 int compare_diff(const char *file1,const char *file2){
 char diff[1024];
 sprintf(diff,"diff -q -B -b -w --strip-trailing-cr %s %s",file1,file2);
 int d=system(diff);
 if (d) return OJ_WA;
 sprintf(diff,"diff -q -B --strip-trailing-cr %s %s",file1,file2);
 int p=system(diff);
 if (p) return OJ_PE;
 else return OJ_AC;
 }
 */
const char *getFileNameFromPath(const char *path)
{
	for (int i = strlen(path); i >= 0; i--)
	{
		if (path[i] == '/')
			return &path[i + 1];
	}
	return path;
}

void make_diff_out_full(FILE *f1, FILE *f2, int c1, int c2, const char *path,const char * infile,const char * userfile)
{
	execute_cmd("echo '========[%s]========='>>diff.out", getFileNameFromPath(path));
	execute_cmd("echo  '\\n------test in top 100 lines------'>>diff.out");
	execute_cmd("head -100 %s >>diff.out",infile);
	execute_cmd("echo  '\\n------test out top 100 lines-----'>>diff.out");
	execute_cmd("head -100 '%s'>>diff.out", path);
	execute_cmd("echo  '\\n------user out top 100 lines-----'>>diff.out");
	execute_cmd("head -100 %s >>diff.out",userfile);
	execute_cmd("echo  '\\n------diff out 200 lines-----'>>diff.out");
	execute_cmd("diff '%s' %s -y|grep \\||head -200>>diff.out", path,userfile);
	execute_cmd("echo  '\\n=============================='>>diff.out");
}
void make_diff_out(FILE *f1, FILE *f2, int c1, int c2, const char *path,const char * userfile )
{
        execute_cmd("echo '%s\n--\n'>>diff.out", getFileNameFromPath(path));
        execute_cmd("echo 'Expected|Yours\n--|--'>>diff.out");
        execute_cmd("diff '%s' %s -y|head -100|tr '>/\\' ' ||' >>diff.out", path,userfile);
        execute_cmd("echo '\n\n'>>diff.out");
}
char * str_replace(char * old, const char * search, const char * replace){
	int p,r;
	char *s;
	char buf[BUFFER_SIZE];
	r=strlen(replace);
	p=strlen(search);
	while (NULL!=(s=strstr(old,search))){
		strcpy(buf,s+p);
		if(strlen(old)-strlen(s)+strlen(buf)>BUFFER_SIZE) break;
		strcpy(s,replace);
		strcpy(s+r-p+1,buf);
	}
	return old;
}
void make_diff_out_simple(FILE *f1, FILE *f2,char * prefix, int c1, int c2, const char *path,const char * userfile )
{
        char buf[BUFFER_SIZE];
        FILE *diff=fopen("diff.out","a+");
        fprintf(diff,"%s\n--\n", getFileNameFromPath(path));
        fprintf(diff,"|Expected|Yours\n|--|--\n");
        int row=0;
        int need=1;
        while(!(feof(f1)&&feof(f2))){
                row++;
                fprintf(diff,"|");
                if(row==1){
                        fprintf(diff,"%s",prefix);
                        if(feof(f2)&&strlen(prefix)>0){
                                fprintf(diff,"|%s\n|",prefix);
                                need=0;
                        }
                        if(isprint(c1))fprintf(diff,"%c",c1);
			//else fprintf(diff,"`Binary:0x%02x`",c1);
                }
                if(!feof(f1)&&fgets(buf,BUFFER_SIZE-1,f1)){
                        if(buf[strlen(buf)-1]=='\n') buf[strlen(buf)-1]='\0';
                        fprintf(diff,"%s",buf);
                }
                fprintf(diff,"|");
                if(row==1){
                        if(need)fprintf(diff,"%s",prefix);
                        if(c2=='|') fprintf(diff,"丨");
                        else if(c2=='[') fprintf(diff,"［");
                        else if(c2==']') fprintf(diff,"］");
                        else if(c2=='(') fprintf(diff,"（");
                        else if(c2==')') fprintf(diff,"）");
                        else if(c2=='\n'){
                                  fprintf(diff,"\n||");
                        }
                        else if(isprint(c2))fprintf(diff,"%c",c2);
                        else fprintf(diff,"`Binary:0x%02x`",c2);
                }
                if(!feof(f2)&&fgets(buf,BUFFER_SIZE-1,f2)){
                        str_replace(buf,"|","丨");
                        str_replace(buf,"[","［");
                        str_replace(buf,"]","］");
                        str_replace(buf,"(","（");
                        str_replace(buf,")","）");
                        if(buf[strlen(buf)-1]=='\n') buf[strlen(buf)-1]='\0';
                        fprintf(diff,"%s",buf);
                }
                fprintf(diff,"\n");
                if(row>=5) break;
        }
        fprintf(diff,"\n\n");
        fclose(diff);
}

/*
 * translated from ZOJ judger r367
 * http://code.google.com/p/zoj/source/browse/trunk/judge_client/client/text_checker.cc#25
 * 参考zoj的文件流式比较器，有更低的内存占用
 */
int compare_zoj(const char *file1, const char *file2,const char * infile,const char * userfile)
{
        int ret = OJ_AC;
        int c1, c2;
        char prefix[BUFFER_SIZE];
        int preK=0;
        FILE *f1, *f2;
        f1 = fopen(file1, "re");
        f2 = fopen(file2, "re");
        if (!f1 || !f2)
        {
                ret = OJ_RE;
        }
        else
                for (;;)
                {
                        // Find the first non-space character at the beginning of line.
                        // Blank lines are skipped.
                        c1 = fgetc(f1);
                        c2 = fgetc(f2);
                        find_next_nonspace(c1, c2, f1, f2, ret);
                        // Compare the current line.
                        for (;;)
                        {
                                // Read until 2 files return a space or 0 together.
                                while ((!isspace(c1) && c1) || (!isspace(c2) && c2))
                                {
                                        if (c1 == EOF && c2 == EOF)
                                        {
                                                goto end;
                                        }
                                        if (c1 == EOF || c2 == EOF)
                                        {
                                                break;
                                        }
                                        if (c1 != c2) {
                                                // Consecutive non-space characters should be all exactly the ifconfig|grep 'inet'|awk -F: '{printf $2}'|awk  '{printf $1}'same
                                                ret = OJ_WA;
                                                goto end;
                                        }else if(preK<BUFFER_SIZE-1){
						prefix[preK++]=c1;
						prefix[preK]='\0';
                                        }else{
						preK=0;
						prefix[preK]='\0';
					}
					c1 = fgetc(f1);
					c2 = fgetc(f2);
				}
				find_next_nonspace(c1, c2, f1, f2, ret);
				preK=0;
	                        prefix[preK]='\0';
				if (c1 == EOF && c2 == EOF)
				{
					goto end;
				}
				if (c1 == EOF || c2 == EOF)
				{
					ret = OJ_WA;
					goto end;
				}

				if ((c1 == '\n' || !c1) && (c2 == '\n' || !c2))
				{
					break;
				}
			}
		}
end:
	if (ret == OJ_WA || ret == OJ_PE)
	{
		if (full_diff)
			make_diff_out_full(f1, f2, c1, c2, file1,infile,userfile);
		else
			make_diff_out_simple(f1, f2, prefix, c1, c2, file1,userfile);
	}
	if (f1)
		fclose(f1);
	if (f2)
		fclose(f2);
	return ret;
}

void delnextline(char s[])
{
	int L;
	L = strlen(s);
	while (L > 0 && (s[L - 1] == '\n' || s[L - 1] == '\r'))
		s[--L] = 0;
}

int compare(const char *file1, const char *file2, const char * infile,const char * userfile)  
{
#ifdef ZOJ_COM
	//compare ported and improved from zoj don't limit file size
	return compare_zoj(file1, file2,infile,userfile);
#endif
#ifndef ZOJ_COM
	//the original compare from the first version of hustoj has file size limit  原始的内存中比较，速度更快但耗用更多内存
	//and waste memory
	FILE *f1, *f2;
	char *s1, *s2, *p1, *p2;
	int PEflg;
	s1 = new char[STD_F_LIM + 512];
	s2 = new char[STD_F_LIM + 512];
	if (!(f1 = fopen(file1, "re")))
		return OJ_AC;
	for (p1 = s1; EOF != fscanf(f1, "%s", p1);)
		while (*p1)
			p1++;
	fclose(f1);
	if (!(f2 = fopen(file2, "re")))
		return OJ_RE;
	for (p2 = s2; EOF != fscanf(f2, "%s", p2);)
		while (*p2)
			p2++;
	fclose(f2);
	if (strcmp(s1, s2) != 0)
	{
		//              printf("A:%s\nB:%s\n",s1,s2);
		delete[] s1;
		delete[] s2;

		return OJ_WA;
	}
	else
	{
		f1 = fopen(file1, "re");
		f2 = fopen(file2, "re");
		PEflg = 0;
		while (PEflg == 0 && fgets(s1, STD_F_LIM, f1) && fgets(s2, STD_F_LIM, f2))
		{
			delnextline(s1);
			delnextline(s2);
			if (strcmp(s1, s2) == 0)
				continue;
			else
				PEflg = 1;
		}
		delete[] s1;
		delete[] s2;
		fclose(f1);
		fclose(f2);
		if (PEflg)
			return OJ_PE;
		else
			return OJ_AC;
	}
#endif
}

bool check_login()   // http模式中检测是否已经登陆
{
	const char *cmd =
		" wget --post-data=\"checklogin=1\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	int ret = 0;
	FILE *fjobs = read_cmd_output(cmd, http_baseurl, http_apipath);
	if(1!=fscanf(fjobs, "%d", &ret)) printf("http login fail..");
	pclose(fjobs);

	return ret;
}
void login()  //http登陆
{
	if (!check_login())
	{
		const char *cmd =
			"wget --post-data=\"user_id=%s&password=%s\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
		FILE *fjobs = read_cmd_output(cmd, http_username, http_password,
									  http_baseurl, http_loginpath);
		//fscanf(fjobs,"%d",&ret);
		pclose(fjobs);
	}
}
#ifdef _mysql_h
/* write result back to database */
void _update_solution_mysql(int solution_id, int result, int time, int memory,
							int sim, int sim_s_id, double pass_rate)
{
	char sql[BUFFER_SIZE];
	char judger[BUFFER_SIZE/10];
	mysql_real_escape_string(conn, judger, http_username, strlen(http_username));
	
	if (oi_mode) {
		sprintf(sql,
				"UPDATE %s SET result=%d,time=%d,memory=%d,pass_rate=%f,judger='%s',judgetime=now() WHERE solution_id=%d ",
					tbname,	    result, time,   memory,   pass_rate,  judger, solution_id);
	} else {
		sprintf(sql,
				"UPDATE %s SET result=%d,time=%d,memory=%d,judger='%s',judgetime=now() WHERE solution_id=%d ",
					tbname,     result, time, memory,judger, solution_id);
	}
	//      printf("sql= %s\n",sql);
	if (mysql_real_query(conn, sql, strlen(sql)))
	{
		//              printf("..update failed! %s\n",mysql_error(conn));
	}
	if (sim)
	{
		sprintf(sql,
				"insert into sim(s_id,sim_s_id,sim) values(%d,%d,%d) on duplicate key update  sim_s_id=%d,sim=%d",
				solution_id, sim_s_id, sim, sim_s_id, sim);
		//      printf("sql= %s\n",sql);
		if (mysql_real_query(conn, sql, strlen(sql)))
		{
			//              printf("..update failed! %s\n",mysql_error(conn));
		}
	}
}
#endif
void _update_solution_http(int solution_id, int result, int time, int memory,
						   int sim, int sim_s_id, double pass_rate)
{
	const char *cmd =
		" wget --post-data=\"update_solution=1&sid=%d&result=%d&time=%d&memory=%d&sim=%d&simid=%d&pass_rate=%f\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *fjobs = read_cmd_output(cmd, solution_id, result, time, memory, sim,
								  sim_s_id, pass_rate, http_baseurl, http_apipath);
	//fscanf(fjobs,"%d",&ret);
	pclose(fjobs);
}
void update_solution(int solution_id, int result, int time, int memory, int sim,
					 int sim_s_id, double pass_rate)
{
	if (result == OJ_TL && memory == 0)
		result = OJ_ML;
	if (result == OJ_AC )
		result=auto_result;
	if (http_judge)
	{
		_update_solution_http(solution_id, result, time, memory, sim, sim_s_id,
							  pass_rate);
	}
	else
	{

#ifdef _mysql_h
		_update_solution_mysql(solution_id, result, time, memory, sim, sim_s_id,
							   pass_rate);
#endif
	}
}
/* write compile error message back to database */
#ifdef _mysql_h
void _addceinfo_mysql(int solution_id)
{
	char sql[(1 << 16)], *end;
	char ceinfo[(1 << 16)], *cend;
	FILE *fp = fopen("ce.txt", "re");
	snprintf(sql, (1 << 16) - 1, "DELETE FROM compileinfo WHERE solution_id=%d",
			 solution_id);
	mysql_real_query(conn, sql, strlen(sql));
	cend = ceinfo;
	while (fgets(cend, 1024, fp))
	{
		cend += strlen(cend);
		if (cend - ceinfo > 16384)
			break;
	}
	*cend = '\0';
	end = sql;
	strcpy(end, "INSERT INTO compileinfo VALUES(");
	end += strlen(sql);
	*end++ = '\'';
	end += sprintf(end, "%d", solution_id);
	*end++ = '\'';
	*end++ = ',';
	*end++ = '\'';
	end += mysql_real_escape_string(conn, end, ceinfo, strlen(ceinfo));
	*end++ = '\'';
	*end++ = ')';
	*end = 0;
	//      printf("%s\n",ceinfo);
	if (mysql_real_query(conn, sql, end - sql))
		printf("%s\n", mysql_error(conn));
	fclose(fp);
}
#endif
// urlencoded function copied from http://www.geekhideout.com/urlcode.shtml
/* Converts a hex character to its integer value */
char from_hex(char ch)
{
	return isdigit(ch) ? ch - '0' : tolower(ch) - 'a' + 10;
}

/* Converts an integer value to its hex character*/
char to_hex(char code)
{
	static char hex[] = "0123456789abcdef";
	return hex[code & 15];
}

/* Returns a url-encoded version of str */
/* IMPORTANT: be sure to free() the returned string after use */
// 用url编码字符串
char *url_encode(char *str)
{
	char *pstr = str, *buf = (char *)malloc(strlen(str) * 3 + 1), *pbuf = buf;
	while (*pstr)
	{
		if (isalnum(*pstr) || *pstr == '-' || *pstr == '_' || *pstr == '.' || *pstr == '~')
			*pbuf++ = *pstr;
		else if (*pstr == ' ')
			*pbuf++ = '+';
		else
			*pbuf++ = '%', *pbuf++ = to_hex(*pstr >> 4), *pbuf++ = to_hex(*pstr & 15);
		pstr++;
	}
	*pbuf = '\0';
	return buf;
}

void _addceinfo_http(int solution_id)
{

	char ceinfo[(1 << 16)], *cend;
	char *ceinfo_encode;
	FILE *fp = fopen("ce.txt", "re");

	cend = ceinfo;
	while (fgets(cend, 1024, fp))
	{
		cend += strlen(cend);
		if (cend - ceinfo > 16384)
			break;
	}
	*cend='\0';
	fclose(fp);
	ceinfo_encode = url_encode(ceinfo);
	FILE *ce = fopen("ce.post", "we");
	fprintf(ce, "addceinfo=1&sid=%d&ceinfo=%s", solution_id, ceinfo_encode);
	fclose(ce);
	free(ceinfo_encode);

	const char *cmd =
		" wget --post-file=\"ce.post\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *fjobs = read_cmd_output(cmd, http_baseurl, http_apipath);
	//fscanf(fjobs,"%d",&ret);
	pclose(fjobs);
}
void addceinfo(int solution_id)
{
	if (http_judge)
	{
		_addceinfo_http(solution_id);
	}
	else
	{

#ifdef _mysql_h
		_addceinfo_mysql(solution_id);
#endif
	}
}
/* write runtime error message back to database */
#ifdef _mysql_h
void _addreinfo_mysql(int solution_id, const char *filename)
{
	char sql[(1 << 16)], *end;
	char reinfo[(1 << 16)], *rend;
	FILE *fp = fopen(filename, "re");
	snprintf(sql, (1 << 16) - 1, "DELETE FROM runtimeinfo WHERE solution_id=%d",
			 solution_id);
	mysql_real_query(conn, sql, strlen(sql));
	rend = reinfo;
	while (fgets(rend, 1024, fp))
	{
		rend += strlen(rend);
		if (rend - reinfo > 16384)
			break;
	}
	*rend = '\0';
	end = sql;
	strcpy(end, "INSERT INTO runtimeinfo VALUES(");
	end += strlen(sql);
	*end++ = '\'';
	end += sprintf(end, "%d", solution_id);
	*end++ = '\'';
	*end++ = ',';
	*end++ = '\'';
	end += mysql_real_escape_string(conn, end, reinfo, strlen(reinfo));
	*end++ = '\'';
	*end++ = ')';
	*end = 0;
	//      printf("%s\n",ceinfo);
	if (mysql_real_query(conn, sql, end - sql))
		printf("%s\n", mysql_error(conn));
	fclose(fp);
}
#endif
void _addreinfo_http(int solution_id, const char *filename)
{

	char reinfo[(1 << 16)], *rend;
	char *reinfo_encode;
	FILE *fp = fopen(filename, "re");

	rend = reinfo;
	while (fgets(rend, 1024, fp))
	{
		rend += strlen(rend);
		if (rend - reinfo > 16384)
			break;
	}
	*rend = '\0';
	fclose(fp);
	reinfo_encode = url_encode(reinfo);
	FILE *re = fopen("re.post", "we");
	fprintf(re, "addreinfo=1&sid=%d&reinfo=%s", solution_id, reinfo_encode);
	fclose(re);
	free(reinfo_encode);

	const char *cmd =
		" wget --post-file=\"re.post\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *fjobs = read_cmd_output(cmd, http_baseurl, http_apipath);
	//fscanf(fjobs,"%d",&ret);
	pclose(fjobs);
}
void addreinfo(int solution_id)
{
	if (http_judge)
	{
		_addreinfo_http(solution_id, "error.out");
	}
	else
	{
#ifdef _mysql_h
		_addreinfo_mysql(solution_id, "error.out");
#endif
	}
}

void adddiffinfo(int solution_id)
{

	if (http_judge)
	{
		_addreinfo_http(solution_id, "diff.out");
	}
	else
	{
#ifdef _mysql_h
		_addreinfo_mysql(solution_id, "diff.out");
#endif
	}
}
void addcustomout(int solution_id)
{

	if (http_judge)
	{
		_addreinfo_http(solution_id, "user.out");
	}
	else
	{
#ifdef _mysql_h
		_addreinfo_mysql(solution_id, "user.out");
#endif
	}
}
#ifdef _mysql_h

void _update_user_mysql(char *user_id)
{
	char sql[BUFFER_SIZE];
	sprintf(sql,
			"UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=\'%s\' AND `result`=4) WHERE `user_id`=\'%s\'",
			user_id, user_id);
	if (mysql_real_query(conn, sql, strlen(sql)))
		write_log(mysql_error(conn));
	sprintf(sql,
			"UPDATE `users` SET `submit`=(SELECT count(*) FROM `solution` WHERE `user_id`=\'%s\' and problem_id>0) WHERE `user_id`=\'%s\'",
			user_id, user_id);
	if (mysql_real_query(conn, sql, strlen(sql)))
		write_log(mysql_error(conn));
}
#endif
void _update_user_http(char *user_id)
{

	const char *cmd =
		" wget --post-data=\"updateuser=1&user_id=%s\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *fjobs = read_cmd_output(cmd, user_id, http_baseurl, http_apipath);
	//fscanf(fjobs,"%d",&ret);
	pclose(fjobs);
}
void update_user(char *user_id)
{
	if (http_judge)
	{
		_update_user_http(user_id);
	}
	else
	{

#ifdef _mysql_h
		_update_user_mysql(user_id);
#endif
	}
}

void _update_problem_http(int pid,int cid) {
	const char * cmd =
			" wget --post-data=\"updateproblem=1&pid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE * fjobs = read_cmd_output(cmd, pid, http_baseurl, http_apipath);
	//fscanf(fjobs,"%d",&ret);
	pclose(fjobs);
}

#ifdef _mysql_h
void _update_problem_mysql(int p_id,int cid) {
	char sql[BUFFER_SIZE];
	if(cid>0){
		sprintf(sql,
			"UPDATE `contest_problem` SET `c_accepted`=(SELECT count(*) FROM `solution` WHERE `problem_id`=%d AND `result`=4 and contest_id=%d) WHERE `problem_id`=%d and contest_id=%d",
			p_id,cid, p_id,cid);
		printf("sql:[%s]\n",sql);
		if (mysql_real_query(conn, sql, strlen(sql)))
			write_log(mysql_error(conn));

	}

		sprintf(sql,
			"UPDATE `problem` SET `accepted`=(SELECT count(*) FROM `solution` WHERE `problem_id`=%d AND `result`=4) WHERE `problem_id`=%d",
			p_id, p_id);
		printf("sql:[%s]\n",sql);
	if (mysql_real_query(conn, sql, strlen(sql)))
		write_log(mysql_error(conn));
	if(cid>0){
		sprintf(sql,
			"UPDATE `contest_problem` SET `c_submit`=(SELECT count(*) FROM `solution` WHERE `problem_id`=%d AND  contest_id=%d) WHERE `problem_id`=%d and contest_id=%d",
			p_id,cid, p_id,cid);
		if (mysql_real_query(conn, sql, strlen(sql)))
			write_log(mysql_error(conn));
	}
	/*	sprintf(sql,
			"UPDATE `problem` SET `submit`=(SELECT count(*) FROM `solution` WHERE `problem_id`=%d) WHERE `problem_id`=%d",
			p_id, p_id);
	
	if (mysql_real_query(conn, sql, strlen(sql)))
		write_log(mysql_error(conn));
	*/
}
#endif
void update_problem(int pid,int cid) {
	if (http_judge) {
		_update_problem_http(pid,cid);
	} else {
#ifdef _mysql_h
		_update_problem_mysql(pid,cid);
#endif
	}
}
void umount(char *work_dir)  //清理可能存在的热加载目录
{
	if(chdir(work_dir)) exit(-1);
	execute_cmd("/bin/umount -l %s/usr 2>/dev/null", work_dir);
	if(strlen(work_dir)>0){
		execute_cmd("/bin/umount -l %s/proc 2>/dev/null", work_dir);
	}
	execute_cmd("/bin/umount -l %s/dev 2>/dev/null", work_dir);
	execute_cmd("/bin/umount -l %s/usr 2>/dev/null", work_dir);
	execute_cmd("/bin/umount -l usr dev");
	execute_cmd("/bin/umount -l lib lib64");
	execute_cmd("/bin/rmdir %s/* ", work_dir);
	execute_cmd("/bin/rmdir %s/log/* ", work_dir);
}
int compile(int lang, char *work_dir)
{
	if( lang == LANG_PYTHON || lang == LANG_JS ) return 0; // python / js don't compile
	int pid;
        char fmax_errors[BUFFER_SIZE];

        if(__GNUC__ > 4 || (  __GNUC__ == 4 &&  __GNUC_MINOR__ >= 8 ) ){
                sprintf(fmax_errors,"-fmax-errors=10");
        }else{
                sprintf(fmax_errors,"-Wformat");
        }
        const char *CP_C[] = {"gcc", "-fno-asm", cc_opt , fmax_errors , cc_std  ,
                                                   "-Wall", "--static", "-DONLINE_JUDGE", "-o", "Main", "Main.c",  "-lm",  NULL};    // 看起来别扭，但是gcc非要-lm选项在Main.c后面才认
        const char *CP_X[] = {"g++", "-fno-asm", cc_opt , fmax_errors , cpp_std ,
                                                   "-Wall", "-lm", "--static", "-DONLINE_JUDGE", "-o", "Main", "Main.cc", NULL};

	const char *CP_P[] =
		{"fpc", "Main.pas", "-Cs32000000", "-Sh", "-O2", "-Co", "-Ct", "-Ci", NULL};
	//      const char * CP_J[] = { "javac", "-J-Xms32m", "-J-Xmx256m","-encoding","UTF-8", "Main.java",NULL };

	const char *CP_R[] = {"ruby", "-c", "Main.rb", NULL};
	const char *CP_B[] = {"chmod", "+rx", "Main.sh", NULL};
	const char * CP_Y[] = { "python3", "-c",
			"import py_compile; py_compile.compile(r'Main.py')", NULL };
	const char *CP_PH[] = {"php", "-l", "Main.php", NULL};
	const char *CP_PL[] = {"perl", "-c", "Main.pl", NULL};
	const char *CP_CS[] = {"mcs","-codepage:utf8", "-warn:0", "Main.cs", NULL};
	const char *CP_OC[] = {"gcc", "-o", "Main", "Main.m",
						   "-fconstant-string-class=NSConstantString", "-I",
						   "/usr/include/GNUstep/", "-L", "/usr/lib/GNUstep/Libraries/",
						   "-lobjc", "-lgnustep-base", NULL};
	const char *CP_BS[] = {"fbc", "-lang", "qb", "Main.bas", NULL};
	const char *CP_CLANG[] = {"clang", "Main.c", "-o", "Main", "-ferror-limit=10", "-fno-asm", "-Wall",
							  "-lm", "--static", "-std=c99", "-DONLINE_JUDGE", NULL};
	const char *CP_CLANG_CPP[] = {"clang++", "Main.cc", "-o", "Main", "-ferror-limit=10", "-fno-asm", "-Wall",
								  "-lm", "--static", "-std=c++0x", "-DONLINE_JUDGE", NULL};
	const char *CP_LUA[] = {"luac", "-o", "Main", "Main.lua", NULL};
	//const char * CP_JS[] = { "js24","-c", "Main.js", NULL };
	const char *CP_GO[] = {"go", "build", "-o", "Main", "Main.go", NULL};
	const char *CP_FORTRAN[] = {"f95", "-static", "-o", "Main", "Main.f95", NULL};
	const char *CP_COBOL[] = {"cobc","-x", "-static", "-o", "Main", "Main.cob", NULL};
	const char *CP_SB3[] = {"scratch-run","--check", "Main.sb3", NULL};

	char * const envp[]={(char * const )"PYTHONIOENCODING=utf-8",
			     (char * const )"USER=judge",
			     (char * const )"GOCACHE=/tmp",
			     (char * const )"LANG=zh_CN.UTF-8",
			     (char * const )"LANGUAGE=zh_CN.UTF-8",
			     (char * const )"LC_ALL=zh_CN.utf-8",NULL};
	char javac_buf[7][32];
	char *CP_J[7];

	for (int i = 0; i < 7; i++)
		CP_J[i] = javac_buf[i];

	sprintf(CP_J[0], "javac");
	sprintf(CP_J[1], "-J%s", java_xms);
	sprintf(CP_J[2], "-J%s", java_xmx);
	sprintf(CP_J[3], "-encoding");
	sprintf(CP_J[4], "UTF-8");
	sprintf(CP_J[5], "Main.java");
	CP_J[6] = (char *)NULL;

	pid = fork();
	if (pid == 0)
	{
		struct rlimit LIM;
		int cpu = 50;
		if (lang == 3)
			cpu = 60;
		LIM.rlim_max = cpu;
		LIM.rlim_cur = cpu;
		setrlimit(RLIMIT_CPU, &LIM);
		alarm(0);
		if(cpu>0)alarm(cpu);else alarm(1);
		LIM.rlim_max = 500 * STD_MB;
		LIM.rlim_cur = 500 * STD_MB;
		setrlimit(RLIMIT_FSIZE, &LIM);

		if (lang == LANG_PASCAL  || lang == LANG_JAVA || lang == LANG_GO  || lang == LANG_R || lang == LANG_SB3 )
		{
#ifdef __mips__
			LIM.rlim_max = STD_MB << 12;
			LIM.rlim_cur = STD_MB << 12;
#endif
#ifdef __arm__
			LIM.rlim_max = STD_MB << 11;
			LIM.rlim_cur = STD_MB << 11;
#endif
#ifdef __i386__
			LIM.rlim_max = STD_MB << 11;
			LIM.rlim_cur = STD_MB << 11;
#endif
#ifdef __x86_64__
			LIM.rlim_max = STD_MB << 12;
			LIM.rlim_cur = STD_MB << 12;
#endif
		}
		else
		{
			LIM.rlim_max = STD_MB << 11 ;
			LIM.rlim_cur = STD_MB << 11;
		}
		if (lang != 3)
			setrlimit(RLIMIT_AS, &LIM);
		if (lang != 2 && lang != 11)
		{
			stderr=freopen("ce.txt", "w", stderr);
			//freopen("/dev/null", "w", stdout);
		}
		else
		{
			stdout=freopen("ce.txt", "w", stdout);
		}
		execute_cmd("/bin/chown judge %s ", work_dir);
		execute_cmd("/bin/chmod 750 %s ", work_dir);

		if (compile_chroot && lang != LANG_JAVA && lang != LANG_CSHARP && lang != LANG_PYTHON && lang != LANG_FREEBASIC && lang != LANG_BASH && lang != LANG_R )
		{
			 if (access("usr", F_OK ) == -1){
				execute_cmd("mkdir -p root/.cache/go-build usr etc/alternatives proc tmp dev");
				execute_cmd("chown judge -R root tmp ");
				execute_cmd("mount -o bind /usr usr");
				execute_cmd("mount -o remount,ro usr");
				execute_cmd("ln -s usr/bin bin");
				execute_cmd("ln -s usr/lib lib");
				execute_cmd("ln -s usr/lib32 lib32");
				execute_cmd("ln -s usr/libx32 libx32");
#ifndef __i386__
				execute_cmd("ln -s usr/lib64 lib64");
#endif
				execute_cmd("cp /etc/alternatives/* etc/alternatives");
				execute_cmd("cp /etc/fpc* etc/");
				execute_cmd("mount -o bind /proc proc");
				if (lang > 2 && lang != 6 && lang != 10 && lang != 13 && lang != 14 )
				{
					execute_cmd("mount -o bind /dev dev");
					execute_cmd("mount -o remount,ro dev");
				}
				//execute_cmd("mount -o remount,ro proc");
			}
			if(chroot(work_dir)) printf("warning chroot fail!\n");
		}
		while (setgid(1536) != 0)
			sleep(1);
		while (setuid(1536) != 0)
			sleep(1);
		while (setresuid(1536, 1536, 1536) != 0)
			sleep(1);

		switch (lang)
		{
		case LANG_C:
			execvp(CP_C[0], (char *const *)CP_C);
			break;
		case LANG_CPP :
			execvp(CP_X[0], (char *const *)CP_X);
			break;
		case LANG_PASCAL :
			execvp(CP_P[0], (char *const *)CP_P);
			break;
		case LANG_JAVA:
			execvp(CP_J[0], (char *const *)CP_J);
			break;
		case LANG_RUBY :
			execvp(CP_R[0], (char *const *)CP_R);
			break;
		case LANG_BASH :
			execvp(CP_B[0], (char *const *)CP_B);
			break;
		case LANG_PYTHON :
			execvp(CP_Y[0], (char * const *) CP_Y);
			break;
		case LANG_PHP :
			execvp(CP_PH[0], (char *const *)CP_PH);
			break;
		case LANG_PERL :
			execvp(CP_PL[0], (char *const *)CP_PL);
			break;
		case LANG_CSHARP :
			execvp(CP_CS[0], (char *const *)CP_CS);
			break;

		case LANG_OBJC :
			execvp(CP_OC[0], (char *const *)CP_OC);
			break;
		case LANG_FREEBASIC :
			execvp(CP_BS[0], (char *const *)CP_BS);
			break;
		case LANG_CLANG:
			execvp(CP_CLANG[0], (char *const *)CP_CLANG);
			break;
		case LANG_CLANGPP:
			execvp(CP_CLANG_CPP[0], (char *const *)CP_CLANG_CPP);
			break;
		case LANG_LUA :
			execvp(CP_LUA[0], (char *const *)CP_LUA);
			break;
		//case 16:
		//	execvp(CP_JS[0], (char * const *) CP_JS);
		//	break;
		case LANG_GO :
			execvpe(CP_GO[0], (char *const *)CP_GO,envp);
			break;
		case LANG_FORTRAN :
			execvp(CP_FORTRAN[0], (char *const *)CP_FORTRAN);
			break;
		case LANG_COBOL:
			execvp(CP_COBOL[0], (char *const *)CP_COBOL);
			break;
		case LANG_SB3:
			execvp(CP_SB3[0], (char *const *)CP_SB3);
			break;
		default:
			printf("nothing to do!\n");
		}
		if (DEBUG)
			printf("compile end!\n");
		//exit(!system("cat ce.txt"));
		exit(0);
	}
	else
	{
		int status = 0;

		waitpid(pid, &status, 0);
		if (lang > 3 && lang < 7)
			status = get_file_size("ce.txt");
		if (DEBUG)
			printf("status=%d\n", status);
		execute_cmd("/bin/umount -l bin usr lib lib64 etc/alternatives dev 2>/dev/null");
		//execute_cmd("/bin/umount -r %s/* 2>/dev/null", work_dir);
		umount(work_dir);

		return status;
	}
}
/*
 int read_proc_statm(int pid){
 FILE * pf;
 char fn[4096];
 int ret;
 sprintf(fn,"/proc/%d/statm",pid);
 pf=fopen(fn,"r");
 fscanf(pf,"%d",&ret);
 fclose(pf);
 return ret;
 }
 */
int get_proc_status(int pid, const char *mark)
{
	FILE *pf;
	char fn[BUFFER_SIZE], buf[BUFFER_SIZE];
	int ret = 0;
	sprintf(fn, "/proc/%d/status", pid);
	pf = fopen(fn, "re");
	int m = strlen(mark);
	while (pf && fgets(buf, BUFFER_SIZE - 1, pf))
	{

		buf[strlen(buf) - 1] = 0;
		if (strncmp(buf, mark, m) == 0)
		{
			if(1!=sscanf(buf + m + 1, "%d", &ret)) printf("proc read fail\n");
		}
	}
	if (pf)
		fclose(pf);
	return ret;
}

#ifdef _mysql_h
int init_mysql_conn()   //连接数据库
{

	conn = mysql_init(NULL);
	//mysql_real_connect(conn,host_name,user_name,password,db_name,port_number,0,0);
	const char timeout = 30;
	mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, &timeout);

	if (!mysql_real_connect(conn, host_name, user_name, password, db_name,
							port_number, 0, 0))
	{
		write_log("%s", mysql_error(conn));
		return 0;
	}
	const char *utf8sql = "set names utf8";
	if (mysql_real_query(conn, utf8sql, strlen(utf8sql)))
	{
		write_log("%s", mysql_error(conn));
		return 0;
	}
	return 1;
}
int check_mysql_conn(){
        MYSQL_RES *res;
        const char * sql="select 1";
        while(mysql_real_query(conn,sql,strlen(sql))){
                if(DEBUG){
                        printf("..update failed! %s\n",mysql_error(conn));
                        write_log("\n.......Mysql time out !---- retry............\n");
                }
                mysql_close(conn);
		sleep(1);
                init_mysql_conn();
                
        }
        res = mysql_store_result(conn);
        mysql_free_result(res);
        return 0;
}

#endif

#ifdef _mysql_h
void _get_solution_mysql(int solution_id, char *work_dir, int lang)
{
	char sql[BUFFER_SIZE], src_pth[BUFFER_SIZE];
	// get the source code
	MYSQL_RES *res;
	MYSQL_ROW row;
	sprintf(sql, "SELECT source FROM source_code WHERE solution_id=%d",
			solution_id);
	mysql_real_query(conn, sql, strlen(sql));
	res = mysql_store_result(conn);

	// create the src file
	
	if (res != NULL)
	{
		row = mysql_fetch_row(res);
		if(row != NULL) {
			sprintf(src_pth, "Main.%s", lang_ext[lang]);
			if (DEBUG)
				printf("Main=%s", src_pth);
			FILE *fp_src = fopen(src_pth, "we");
			fprintf(fp_src, "%s", row[0]);
			mysql_free_result(res); // free the memory
			res = NULL;
			row = NULL;
			fclose(fp_src);
		}
	}
}
#endif
void _get_solution_http(int solution_id, char *work_dir, int lang)
{
	char src_pth[BUFFER_SIZE];

	// create the src file
	sprintf(src_pth, "Main.%s", lang_ext[lang]);
	if (DEBUG)
		printf("Main=%s", src_pth);

	//login();

	const char *cmd2 =
		"wget --post-data=\"getsolution=1&sid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O %s \"%s%s\"";
	FILE *pout = read_cmd_output(cmd2, solution_id, src_pth, http_baseurl, http_apipath);

	pclose(pout);
}
void get_solution(int solution_id, char *work_dir, int lang,int p_id)
{
	char src_pth[BUFFER_SIZE];
	sprintf(src_pth, "Main.%s", lang_ext[lang]);
	if (http_judge)
	{
		_get_solution_http(solution_id, work_dir, lang);
	}
	else
	{

#ifdef _mysql_h
		_get_solution_mysql(solution_id, work_dir, lang);
#endif
	}
	if(lang == LANG_PYTHON ){    // 从源码中搜索python2字样，失败的结果非零默认python3,成功的结果为0是python2
		py2 = execute_cmd("/bin/grep 'python2' %s/Main.py > /dev/null", work_dir);
                execute_cmd("sed -i 's/import.*os//g' %s/%s", work_dir, src_pth);
	}
	if(lang == LANG_SB3 ){
                execute_cmd("cp %s/../data/%d/sb3/%d.sb3 %s", work_dir,p_id,solution_id, src_pth);
	}
	execute_cmd("chown judge %s/%s", work_dir, src_pth);
}

#ifdef _mysql_h
void _get_custominput_mysql(int solution_id, char *work_dir)
{
	char sql[BUFFER_SIZE], src_pth[BUFFER_SIZE];
	// get the source code
	printf("geting custom input\n");
	MYSQL_RES *res;
	MYSQL_ROW row;
	sprintf(sql, "SELECT input_text FROM custominput WHERE solution_id=%d",
			solution_id);
	mysql_real_query(conn, sql, strlen(sql));
	res = mysql_store_result(conn);
	row = mysql_fetch_row(res);
	if (row != NULL)
	{

		// create the src file
		sprintf(src_pth, "data.in");
		FILE *fp_src = fopen(src_pth, "w");
		fprintf(fp_src, "%s", row[0]);
		fclose(fp_src);
		sprintf(sql, "DELETE FROM custominput WHERE solution_id=%d", solution_id);
		mysql_real_query(conn, sql, strlen(sql));
	}
	if (res != NULL)
	{
		printf("no custom input\n");
		mysql_free_result(res); // free the memory
		res = NULL;
	}
}
#endif
void _get_custominput_http(int solution_id, char *work_dir)
{
	char src_pth[BUFFER_SIZE];

	// create the src file
	sprintf(src_pth, "data.in");

	//login();

	const char *cmd2 =
		"wget --post-data=\"getcustominput=1&sid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O %s \"%s%s\"";
	FILE *pout = read_cmd_output(cmd2, solution_id, src_pth, http_baseurl, http_apipath);

	pclose(pout);
}
void get_custominput(int solution_id, char *work_dir)
{
	if (http_judge)
	{
		_get_custominput_http(solution_id, work_dir);
	}
	else
	{
#ifdef _mysql_h
		_get_custominput_mysql(solution_id, work_dir);
#endif
	}
}

#ifdef _mysql_h
void _get_solution_info_mysql(int solution_id, int & p_id, char * user_id,
		int & lang,int &cid) {

	MYSQL_RES *res;
	MYSQL_ROW row;

	char sql[BUFFER_SIZE];
	// get the problem id and user id from Table:solution
	if (turbo_mode == 2)
	{
		sprintf(sql,
				"insert into solution2 select *  FROM solution where solution_id=%d",
				solution_id);
		//printf("%s\n",sql);
		mysql_real_query(conn, sql, strlen(sql));
		sprintf(sql,
				"SELECT problem_id, user_id, language,contest_id FROM solution2 where solution_id=%d",
				solution_id);
		//printf("%s\n",sql);
	}
	else
	{
		sprintf(sql,
				"SELECT problem_id, user_id, language,contest_id FROM solution where solution_id=%d",
				solution_id);
	}
	//printf("%s\n",sql);
	mysql_real_query(conn, sql, strlen(sql));
	res = mysql_store_result(conn);
	row = mysql_fetch_row(res);
	p_id = atoi(row[0]);
	strcpy(user_id, row[1]);
	lang = atoi(row[2]);
	if(row[3]==NULL) cid=0;
	else cid = atoi(row[3]);
	printf("cid:%d\n",cid);
	if(res!=NULL) {
		mysql_free_result(res);                         // free the memory
		res=NULL;
	}
}
#endif
void _get_solution_info_http(int solution_id, int & p_id, char * user_id,
		int & lang,int & cid) {

	login();

	const char *cmd =
		"wget --post-data=\"getsolutioninfo=1&sid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *pout = read_cmd_output(cmd, solution_id, http_baseurl, http_apipath);
	if(1!=fscanf(pout, "%d", &p_id))  printf("http problem_id read fail...\n");
	if(1!=fscanf(pout, "%s", user_id)) printf("http user_id read fail ... \n") ;
	if(1!=fscanf(pout, "%d", &lang))   printf("http language read fail ... \n") ;
	if(1!=fscanf(pout, "%d", &cid))    printf("http contest_id read fail ... \n") ;
	pclose(pout);
}
void get_solution_info(int solution_id, int & p_id, char * user_id,
		int & lang,int & cid) {

	if (http_judge) {
		_get_solution_info_http(solution_id, p_id, user_id, lang,cid);
	} else {
#ifdef _mysql_h
		_get_solution_info_mysql(solution_id, p_id, user_id, lang,cid);
#endif
	}
}

#ifdef _mysql_h
void _get_problem_info_mysql(int p_id, double &time_lmt, int &mem_lmt,
							 int &spj)
{
	// get the problem info from Table:problem
	char sql[BUFFER_SIZE];
	MYSQL_RES *res;
	MYSQL_ROW row;
	sprintf(sql,
			"SELECT time_limit,memory_limit,spj FROM problem where problem_id=%d",
			p_id);
	mysql_real_query(conn, sql, strlen(sql));
	res = mysql_store_result(conn);
	row = mysql_fetch_row(res);
	time_lmt = atof(row[0]);
	mem_lmt = atoi(row[1]);
	spj = atoi(row[2]);
	if (res != NULL)
	{
		mysql_free_result(res); // free the memory
		res = NULL;
	}
}
#endif
void _get_problem_info_http(int p_id, double &time_lmt, int &mem_lmt,
							int &spj)
{
	//login();

	const char *cmd =
		"wget --post-data=\"getprobleminfo=1&pid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *pout = read_cmd_output(cmd, p_id, http_baseurl, http_apipath);
	if(1!=fscanf(pout, "%lf", &time_lmt)) printf("http read time_limit fail...\n");
	if(1!=fscanf(pout, "%d", &mem_lmt)  ) printf("http read memory_limit fail...\n");
	if(1!=fscanf(pout, "%d", &spj)    ) printf("http read special judge fail...\n");
	pclose(pout);
	if(DEBUG) printf("time_lmt:%g\n",time_lmt);
}

void get_problem_info(int p_id, double &time_lmt, int &mem_lmt, int &spj)
{
	if (http_judge)
	{
		_get_problem_info_http(p_id, time_lmt, mem_lmt, spj);
	}
	else
	{
#ifdef _mysql_h
		_get_problem_info_mysql(p_id, time_lmt, mem_lmt, spj);
#endif
	}
	if (time_lmt <= 0)
		time_lmt = 1;
}
char *escape(char s[], char t[])
{
	int i, j;
	for (i = j = 0; t[i] != '\0'; ++i)
	{
		if (t[i] == '\'')
		{
			s[j++] = '\'';
			s[j++] = '\\';
			s[j++] = '\'';
			s[j++] = '\'';
			continue;
		}
		else
		{
			s[j++] = t[i];
		}
	}
	s[j] = '\0';
	return s;
}

void prepare_files(char *filename, int namelen, char *infile, int &p_id,
				   char *work_dir, char *outfile, char *userfile, int runner_id,int lang )
{
	//              printf("ACflg=%d %d check a file!\n",ACflg,solution_id);

	char fname0[BUFFER_SIZE];
	char fname[BUFFER_SIZE];
	strncpy(fname0, filename, namelen);
	fname0[namelen] = 0;
	escape(fname, fname0);
	//printf("%s\n%s\n",fname0,fname);
	sprintf(infile, "%s/data/%d/%s.in", oj_home, p_id, fname);
	char noip_file_name[BUFFER_SIZE];
	sprintf(noip_file_name,"%s/data/%d/input.name",oj_home,p_id);
	if(DEBUG) printf("NOIP filename:%s\n",noip_file_name);
 	if (access(noip_file_name, R_OK ) != -1){
		if(DEBUG) printf("NOIP filename:%s\n",noip_file_name);
		FILE * fpname=fopen(noip_file_name,"r");
		if(fscanf(fpname,"%s",noip_file_name)){
		    execute_cmd("/bin/cp '%s' %s/%s", infile, work_dir,basename(noip_file_name));   // 如果存在input.name则复制测试数据
		     execute_cmd("/usr/bin/chown judge %s/%s", work_dir,basename(noip_file_name));   // 修改属主
		    if(DEBUG) printf("NOIP filename:%s\n",noip_file_name);
		}
		fclose(fpname);
	}else{
		if(copy_data||lang == LANG_R) execute_cmd("/bin/cp '%s' %s/data.in", infile, work_dir);   // 如果开启了COPY_DATA则复制测试数据
	}
	execute_cmd("/bin/cp %s/data/%d/*.dic %s/ 2>/dev/null", oj_home, p_id, work_dir);
 	execute_cmd("chown judge %s/*.dic ", work_dir);
	sprintf(outfile, "%s/data/%d/%s.out", oj_home, p_id, fname0);

	sprintf(noip_file_name,"%s/data/%d/output.name",oj_home,p_id);
	if(DEBUG) printf("NOIP filename:%s\n",noip_file_name);
 	if (access(noip_file_name, R_OK ) != -1){	
		FILE * fpname=fopen(noip_file_name,"r");
		if(fscanf(fpname,"%s",noip_file_name)){
		    if(DEBUG) printf("NOIP filename:%s\n",noip_file_name);
		    if(!strstr("noip_file_name","//")){
                            sprintf(userfile, "%s/run%d/%s", oj_home, runner_id,basename(noip_file_name));
                            execute_cmd("rm %s",userfile);
                    }
		}
		fclose(fpname);
	}else{
		sprintf(userfile, "%s/run%d/user.out", oj_home, runner_id);
		execute_cmd("rm %s",userfile);
	}
}
// 以下 copy_开头的函数均为准备相应语言的chroot环境，复制动态链接库等，如果使用的系统不是Ubuntu则路径有所区别，可以用ldd/find查看实际位置。
void copy_shell_runtime(char *work_dir)
{

	execute_cmd("/bin/mkdir %s/lib", work_dir);
	execute_cmd("/bin/mkdir %s/lib64", work_dir);
	execute_cmd("/bin/mkdir %s/bin", work_dir);
#ifdef __mips__
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/  %s/lib/mips64el-linux-gnuabi64",work_dir);
	execute_cmd("mkdir -p %s/lib/mips64el-linux-gnuabi64/",work_dir);
	execute_cmd("/bin/cp -a /lib64/ld.so.1  %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libdl.so.2  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libutil.so.1  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libz.so.1  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libm.so.6  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libc.so.6  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libtinfo.so.5  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/ld-2.24.so  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib/mips64el-linux-gnuabi64/libc-2.24.so  %s/lib/mips64el-linux-gnuabi64", work_dir);
	execute_cmd("/bin/cp -a /lib64/libc.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libtinfo.so.6  %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/ld-2.27.so  %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libc-2.27.so %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libdl-2.27.so %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libtinfo.so.6.1 %s/lib64/", work_dir);
	execute_cmd("cp  /lib/mips64el-linux-gnuabi64/libpthread.so.0 %s/lib/mips64el-linux-gnuabi64/",work_dir);
	execute_cmd("/bin/cp -a /bin/bash %s/bin/", work_dir);

#endif

#ifdef __i386
	execute_cmd("/bin/cp /lib/ld-linux* %s/lib/", work_dir);
	execute_cmd("/bin/cp -a /lib/i386-linux-gnu/  %s/lib/", work_dir);
//	execute_cmd("/bin/cp -a /usr/lib/i386-linux-gnu %s/lib/", work_dir);
#endif

#ifdef __x86_64__
	execute_cmd("mount -o bind /lib %s/lib", work_dir);
	execute_cmd("mount -o bind /lib64 %s/lib64", work_dir);
#endif
	//	execute_cmd("/bin/cp /lib32 %s/", work_dir);
	execute_cmd("/bin/cp /bin/busybox %s/bin/", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/sh", work_dir);
	execute_cmd("/bin/cp /bin/bash %s/bin/bash", work_dir);
}
void copy_objc_runtime(char *work_dir)
{
	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/proc", work_dir);
	execute_cmd("/bin/mount -o bind /proc %s/proc", work_dir);
	execute_cmd("/bin/mkdir -p %s/lib/", work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/libdbus-1.so.3                          %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/libgcc_s.so.1                           %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/libgcrypt.so.11                         %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/libgpg-error.so.0                       %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/libz.so.1                               %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/libc.so.6                 %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/libdl.so.2                %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/libm.so.6                 %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/libnsl.so.1               %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/libpthread.so.0           %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /lib/tls/i686/cmov/librt.so.1                %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libavahi-client.so.3                %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libavahi-common.so.3                %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libdns_sd.so.1                      %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libffi.so.5                         %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libgnustep-base.so.1.19             %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libgnutls.so.26                     %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libobjc.so.2                        %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libtasn1.so.3                       %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libxml2.so.2                        %s/lib/ ",
		work_dir);
	execute_cmd(
		"/bin/cp -aL /usr/lib/libxslt.so.1                        %s/lib/ ",
		work_dir);
}
void copy_bash_runtime(char *work_dir)
{
	//char cmd[BUFFER_SIZE];
	//const char * ruby_run="/usr/bin/ruby";
	copy_shell_runtime(work_dir);
	execute_cmd("/bin/cp `which bc`  %s/bin/", work_dir);
	execute_cmd("busybox dos2unix Main.sh", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/grep", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/awk", work_dir);
	execute_cmd("/bin/cp /bin/sed %s/bin/sed", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/cut", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/sort", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/join", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/wc", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/tr", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/dc", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/dd", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/cat", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/tail", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/head", work_dir);
	execute_cmd("/bin/ln -s /bin/busybox %s/bin/xargs", work_dir);
	execute_cmd("chmod +rx %s/Main.sh", work_dir);
}
void copy_ruby_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("mkdir -p %s/usr/bin", work_dir);
	execute_cmd("mkdir -p %s/usr/lib", work_dir);
	execute_cmd("mkdir -p %s/usr/lib64", work_dir);
	execute_cmd("cp -a /usr/lib/libruby* %s/usr/lib/", work_dir);
	execute_cmd("cp -a /usr/lib/ruby* %s/usr/lib/", work_dir);
	execute_cmd("cp -a /usr/lib64/ruby* %s/usr/lib64/", work_dir);
	execute_cmd("cp -a /usr/lib64/libruby* %s/usr/lib64/", work_dir);
	execute_cmd("cp -a /usr/bin/ruby* %s/usr/bin", work_dir);
#ifdef __x86_64__
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libruby* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libgmp* %s/usr/lib/", work_dir);
#endif
}

void copy_guile_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/proc", work_dir);
	execute_cmd("/bin/mount -o bind /proc %s/proc", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/lib", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/share", work_dir);
	execute_cmd("/bin/cp -a /usr/share/guile %s/usr/share/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libguile* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libgc* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libffi* %s/usr/lib/",
				work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libunistring* %s/usr/lib/",
				work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libgmp* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libgmp* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libltdl* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libltdl* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/bin/guile* %s/usr/bin", work_dir);
#ifdef __x86_64__
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libguile* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libgc* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libffi* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp -a /usr/lib/x86_64-linux-gnu/libunistring* %s/usr/lib/", work_dir);
#endif
}

void copy_python_runtime(char *work_dir)
{
	copy_shell_runtime(work_dir);
	execute_cmd("mkdir -p %s/usr/include", work_dir);
	execute_cmd("mkdir -p %s/dev", work_dir);
	
	execute_cmd("mkdir -p %s/usr/bin", work_dir);
	execute_cmd("mkdir -p %s/usr/lib", work_dir);
	execute_cmd("mkdir -p %s/usr/lib64", work_dir);
	execute_cmd("mkdir -p %s/usr/local/lib", work_dir);
	execute_cmd("mkdir -p %s/lib/x86_64-linux-gnu", work_dir);

	// /etc/abrt/plugins/python.conf for Centos7
	execute_cmd("mkdir -p %s/etc/abrt", work_dir);
	execute_cmd("mkdir -p %s/etc/abrt/plugins", work_dir);
	execute_cmd("cp -a /etc/abrt/plugins/python.conf %s/etc/abrt/plugins/python.conf", work_dir);
	
	// /usr/share/abrt/conf.d/plugins/python.conf for Centos7
	execute_cmd("mkdir -p %s/usr/share", work_dir);
	execute_cmd("mkdir -p %s/usr/share/abrt/", work_dir);
	execute_cmd("mkdir -p %s/usr/share/abrt/conf.d", work_dir);
	execute_cmd("mkdir -p %s/usr/share/abrt/conf.d/plugins", work_dir);
	execute_cmd("cp -a /usr/share/abrt/conf.d/plugins/python.conf %s/usr/share/abrt/conf.d/plugins/python.conf", work_dir);
	if(!py2){	
		execute_cmd("cp /usr/bin/python2* %s/usr/bin", work_dir);
		execute_cmd("cp -a /usr/lib/python2* %s/usr/lib/", work_dir);
		execute_cmd("cp -a /usr/lib64/python2.7  %s/usr/lib64/", work_dir);
#if (defined __mips__)
		execute_cmd("cp -a /usr/lib64/python2* %s/usr/lib64/", work_dir);
		execute_cmd("mkdir -p  %s/usr/local/lib/", work_dir);
		execute_cmd("cp -a /usr/local/lib/python2* %s/usr/local/lib/", work_dir);
#endif
	}else{
		execute_cmd("cp /usr/bin/python3* %s/usr/bin", work_dir);
		execute_cmd("cp -a /usr/lib/python3* %s/usr/lib/", work_dir);
		execute_cmd("cp -a /usr/lib64/python3.6  %s/usr/lib64/", work_dir);
#if (defined __mips__)
		execute_cmd("cp -a /usr/lib64/python3* %s/usr/lib64/", work_dir);
		execute_cmd("mkdir -p  %s/usr/local/lib/", work_dir);
		execute_cmd("cp -a /usr/local/lib/python3* %s/usr/local/lib/", work_dir);
#endif
	}
	execute_cmd("cp /usr/lib/lapack/* %s/usr/lib/liblapack.so.3", work_dir);
	execute_cmd("cp /usr/lib/libblas/* %s/usr/lib/libblas.so.3", work_dir);
	execute_cmd("cp /usr/lib/x86_64-linux-gnu/libgfortran.so.3 %s/usr/lib/", work_dir);
	execute_cmd("cp /usr/lib/x86_64-linux-gnu/libquadmath.so.0 %s/usr/lib", work_dir);
	execute_cmd("cp /usr/lib/x86_64-linux-gnu/blas/* %s/usr/lib", work_dir);
	execute_cmd("cp /usr/lib/x86_64-linux-gnu/liblapack.so* %s/usr/lib", work_dir);
	execute_cmd("cp /usr/lib/x86_64-linux-gnu/libgfortran.so.4 %s/usr/lib", work_dir);
#ifdef __mips__
	execute_cmd("/bin/cp -a /lib64/libpthread.so.0 %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libutil.so.1 %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libm.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libc.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libpthread-2.27.so %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libutil-2.27.so %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libc-2.27.so %s/lib64/", work_dir);
	execute_cmd("/bin/cp -a /lib64/libm-2.27.so %s/lib64/", work_dir);


#endif

	/*execute_cmd("/bin/mkdir -p %s/lib/x86_64-linux-gnu", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libpthread* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libdl.so.2 %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libutil.so.1 %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libexpat.so.1 %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libz.so.1 %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/libm.so.6 %s/lib/x86_64-linux-gnu/", work_dir);
	*/
	//execute_cmd("/bin/cp -a /lib/x86_64-linux-gnu/ %s/lib/x86_64-linux-gnu/", work_dir);

	execute_cmd("cp -a /usr/lib64/libpython* %s/usr/lib64/", work_dir);
	execute_cmd("cp -a /usr/local/lib/python* %s/usr/local/lib/", work_dir);
	execute_cmd("cp -a /usr/include/python* %s/usr/include/", work_dir);
	execute_cmd("cp -a /usr/lib/libpython* %s/usr/lib/", work_dir);
	execute_cmd("/bin/mkdir -p %s/home/judge", work_dir);
	execute_cmd("/bin/chown judge %s", work_dir);
	execute_cmd("/bin/mkdir -p %s/etc", work_dir);
	execute_cmd("/bin/grep judge /etc/passwd>%s/etc/passwd", work_dir);
	execute_cmd("/bin/mount -o bind /dev %s/dev", work_dir);
	execute_cmd("/bin/mount -o remount,ro %s/dev", work_dir);
}
void copy_php_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/mkdir %s/usr/lib", work_dir);
	execute_cmd("/bin/cp /usr/lib/libedit* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libdb* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libgssapi_krb5* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libkrb5* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libk5crypto* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libedit* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libdb* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libgssapi_krb5* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libkrb5* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/*/libk5crypto* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libxml2* %s/usr/lib/", work_dir);
#ifdef __x86_64__
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libxml2.so* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libicuuc.so* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libicudata.so* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libstdc++.so* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libssl* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libcrypto* %s/usr/lib/", work_dir);
#endif
	execute_cmd("/bin/cp /usr/bin/php* %s/usr/bin", work_dir);
	execute_cmd("chmod +rx %s/Main.php", work_dir);
}
void copy_perl_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/mkdir %s/usr/lib", work_dir);
	execute_cmd("/bin/cp /usr/lib/libperl* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/bin/perl* %s/usr/bin", work_dir);
}
void copy_freebasic_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/local/lib", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/local/bin", work_dir);
	execute_cmd("/bin/cp /usr/local/lib/freebasic %s/usr/local/lib/", work_dir);
	execute_cmd("/bin/cp /usr/local/bin/fbc %s/", work_dir);
	execute_cmd("/bin/cp -a /lib32/* %s/lib/", work_dir);
}
void copy_mono_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/mkdir %s/proc", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/lib/mono/2.0", work_dir);
	execute_cmd("/bin/cp -a /usr/lib/mono %s/usr/lib/", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/lib64/mono/2.0", work_dir);
	execute_cmd("/bin/cp -a /usr/lib64/mono %s/usr/lib64/", work_dir);

	execute_cmd("/bin/cp /usr/lib/libgthread* %s/usr/lib/", work_dir);

	execute_cmd("/bin/mount -o bind /proc %s/proc", work_dir);
	execute_cmd("/bin/cp /usr/bin/mono* %s/usr", work_dir);

	execute_cmd("/bin/cp /usr/lib/libgthread* %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /lib/libglib* %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/tls/i686/cmov/lib* %s/lib/tls/i686/cmov/",
				work_dir);
	execute_cmd("/bin/cp /lib/libpcre* %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/ld-linux* %s/lib/", work_dir);
#ifdef __x86_64__
	execute_cmd("/bin/cp /lib64/ld-linux* %s/lib64/", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/lib/x86_64-linux-gnu", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libm.so.6 %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/librt.so.1 %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libpthread.so.0 %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libgcc_s.so.1 %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libc.so.6 %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib64/ld-linux-x86-64.so.2 %s/lib64", work_dir);
#endif
	execute_cmd("/bin/mkdir -p %s/home/judge", work_dir);
	execute_cmd("/bin/chown judge %s/home/judge", work_dir);
	execute_cmd("/bin/mkdir -p %s/etc", work_dir);
	execute_cmd("/bin/grep judge /etc/passwd>%s/etc/passwd", work_dir);
}
void copy_lua_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/local/lib", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/local/bin", work_dir);
	execute_cmd("/bin/cp /usr/bin/lua %s/usr/bin", work_dir);
}
void copy_sql_runtime(char *work_dir)
{

	copy_shell_runtime(work_dir);
	execute_cmd("mkdir -p %s/usr/bin", work_dir);
	execute_cmd("/bin/cp /usr/bin/sqlite3 %s/usr/bin", work_dir);
#ifdef __mips__
	execute_cmd("/bin/cp /lib64/libedit.so.0 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libm.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libdl.so.2 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libz.so.1 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libpthread.so.0 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libc.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib64/libtinfo.so.6 %s/lib64/", work_dir);
#endif
#ifdef __i386__
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libsqlite3.so.0*   %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libreadline.so.6*   %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libc.so.6*  %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libpthread.so.0 %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libdl.so.2* %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libtinfo.so.5* %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libedit.so.0 %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libm.so.6* %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libz.so.1 %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libtinfo.so.6* %s/lib/", work_dir);
#endif
#ifdef __x86_64__
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libsqlite3.so.0   %s/lib/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libreadline.so.6   %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libc.so.6  %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libpthread.so.0 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libdl.so.2 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libtinfo.so.5 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libedit.so.0 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libm.so.6 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libz.so.1 %s/lib64/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libtinfo.so.6 %s/lib64/", work_dir);
#endif
}
void copy_js_runtime(char *work_dir)
{

	//copy_shell_runtime(work_dir);
	execute_cmd("mkdir -p %s/usr/bin", work_dir);
	execute_cmd("mkdir -p %s/dev", work_dir);
	execute_cmd("/bin/mount -o bind /dev %s/dev", work_dir);
	execute_cmd("/bin/mount -o remount,ro %s/dev", work_dir);
	execute_cmd("/bin/mkdir -p %s/usr/lib %s/lib/i386-linux-gnu/ %s/lib64/", work_dir, work_dir, work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libz.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libuv.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libicui18n.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libicuuc.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libicudata.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libtinfo.so.*  %s/lib/i386-linux-gnu/", work_dir);

	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libcares.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libv8.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libssl.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libcrypto.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libdl.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/librt.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/i386-linux-gnu/libstdc++.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libpthread.so.*  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libc.so.6  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libm.so.6  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/i386-linux-gnu/libgcc_s.so.1  %s/lib/i386-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/ld-linux.so.*  %s/lib/", work_dir);

#ifdef __x86_64__
	execute_cmd("/bin/mkdir -p %s/usr/lib/x86_64-linux-gnu/ %s/lib/x86_64-linux-gnu/", work_dir, work_dir);

	//execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/  %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/libv8.so.*  %s/usr/lib/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libcares.so.* %s/usr/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libz.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libuv.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/librt.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libpthread.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libdl.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libssl.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libcrypto.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libicui18n.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libicuuc.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libstdc++.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libm.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libgcc_s.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib/x86_64-linux-gnu/libc.so.* %s/lib/x86_64-linux-gnu/", work_dir);
	execute_cmd("/bin/cp /lib64/ld-linux-x86-64.so.* %s/lib64/", work_dir);
	execute_cmd("/bin/cp /usr/lib/x86_64-linux-gnu/libicudata.so.* %s/lib/x86_64-linux-gnu/", work_dir);
#endif
	execute_cmd("/bin/cp /usr/bin/node %s/usr/bin", work_dir);
}
void run_solution(int &lang, char *work_dir, double &time_lmt, int &usedtime,
				  int &mem_lmt,char * data_file_path,int p_id)   // 为每个测试数据运行一次提交的答案
{
	//准备环境变量处理中文，如果希望使用非中文的语言环境，可能需要修改这些环境变量
	char * const envp[]={(char * const )"PYTHONIOENCODING=utf-8",
			     (char * const )"LANG=zh_CN.UTF-8",
			     (char * const )"LANGUAGE=zh_CN.UTF-8",
			     (char * const )"LC_ALL=zh_CN.utf-8",NULL};
	if(nice(19)!=19) printf("......................renice fail... \n");
	// now the user is "judger"
	if(chdir(work_dir)){
		write_log("Working directory :%s switch fail...",work_dir);		
		exit(-4);
	}
	// open the files
	if(lang==18){ 
		execute_cmd("/usr/bin/sqlite3 %s/data.db < %s", work_dir,data_file_path);
		execute_cmd("/bin/chown judge %s/data.db", work_dir);
		stdin=freopen("Main.sql", "r", stdin);
	}else{
		char noip_file_name[BUFFER_SIZE];
		sprintf(noip_file_name,"%s/data/%d/input.name",oj_home,p_id);
		if(DEBUG) printf("---------NOIP filename:%s\n",noip_file_name);
		if (access(noip_file_name, R_OK ) == -1){   //不存在指定文件名，使用标准输入
			if(copy_data){
				stdin=freopen("data.in", "r", stdin);
			}else{
				printf("infile: [%s]\n",data_file_path);
				stdin=freopen(data_file_path,"r",stdin);
			}
		}
	}
	execute_cmd("touch %s/user.out", work_dir);
	
	if (copy_data){
		execute_cmd("chgrp judge %s/user.out %s/data.in", work_dir,work_dir);
		execute_cmd("chmod 740 %s/data.in", work_dir);
	}
	execute_cmd("chmod 760 %s/user.out", work_dir);
	if (   
		(!use_docker) && lang != 3 && lang != 5 && lang != 20 && lang != 9 && !(lang ==6 && python_free )
	   ){
		
		if(DEBUG)printf("chroot...............................................\n");
	
	}else{
		if(DEBUG)printf("Skiping chroot........................................\n");
	
	}
	stdout=freopen("user.out", "w", stdout);
	stderr=freopen("error.out", "a+", stderr);
	// trace me
	ptrace(PTRACE_TRACEME, 0, NULL, NULL);
	// run me
	if (   
		(!use_docker) 
			&& lang != LANG_JAVA
			&& lang != LANG_PHP
			&& lang != LANG_BASH
			&& lang != LANG_COBOL 
			&& lang != LANG_MATLAB 
			&& lang != LANG_CSHARP
			&& lang != LANG_R
			&& !(lang == LANG_PYTHON && python_free )
	   ){
		
		if(chroot(work_dir));
	}else{
		// vm script language don't chroot within docker
	}
	while (setgid(1536) != 0)
		sleep(1);
	while (setuid(1536) != 0)
		sleep(1);
	while (setresuid(1536, 1536, 1536) != 0)
		sleep(1);

	//      char java_p1[BUFFER_SIZE], java_p2[BUFFER_SIZE];
	// child
	// set the limit
	struct rlimit LIM; // time limit, file limit& memory limit
	// time limit
	if (time_limit_to_total)
		LIM.rlim_cur = (time_lmt / cpu_compensation - usedtime / 1000.0f) + 1;
	else
		LIM.rlim_cur = time_lmt / cpu_compensation + 1 ;
	LIM.rlim_max = LIM.rlim_cur + 1 ;
	//if(DEBUG) printf("LIM_CPU=%d",(int)(LIM.rlim_cur));
	setrlimit(RLIMIT_CPU, &LIM);
	alarm(0);
//	if ( num_of_test >0 ){
//		if(num_of_test * time_lmt / cpu_compensation>1)
//			alarm( num_of_test * time_lmt / cpu_compensation);
//		else
//			alarm(1);
//	}else{
		if(time_lmt / cpu_compensation>1)
			alarm( time_lmt / cpu_compensation);
		else
			alarm(1);
//	}
	// file limit
	LIM.rlim_max = STD_F_LIM + STD_MB;
	LIM.rlim_cur = STD_F_LIM;
	setrlimit(RLIMIT_FSIZE, &LIM);
	// proc limit
	switch (lang)
	{
	case LANG_GO:
	case LANG_CSHARP: //C#
	case LANG_JAVA: //java
	case LANG_R:
	case LANG_SB3:
		LIM.rlim_cur = LIM.rlim_max = 880;
		break;
	case LANG_RUBY: //ruby
	case LANG_PYTHON:  //python
	case LANG_SCHEME:
	case LANG_JS:
	case LANG_MATLAB:
		LIM.rlim_cur = LIM.rlim_max = 200;
		break;
	case LANG_BASH: //bash
		LIM.rlim_cur = LIM.rlim_max = 3;
		break;
	default:
		LIM.rlim_cur = LIM.rlim_max = 1;
	}

	setrlimit(RLIMIT_NPROC, &LIM);

	// set the stack
	LIM.rlim_cur = STD_MB << 8;
	LIM.rlim_max = STD_MB << 8;
	setrlimit(RLIMIT_STACK, &LIM);
	// set the memory
	LIM.rlim_cur = STD_MB * mem_lmt / 2 * 3;
	LIM.rlim_max = STD_MB * mem_lmt * 2;
	if (lang < LANG_JAVA || lang == LANG_OBJC || lang == LANG_CLANG || lang == LANG_CLANGPP || lang == LANG_GO)
		setrlimit(RLIMIT_AS, &LIM);

	switch (lang)
	{
	case LANG_C:
	case LANG_CPP:
	case LANG_PASCAL:
	case LANG_OBJC:
	case LANG_FREEBASIC:
	case LANG_CLANG:
	case LANG_CLANGPP:
	case LANG_GO:
	case LANG_FORTRAN:
	case LANG_COBOL:
		execle("./Main", "./Main", (char *)NULL,envp);
		break;
	case LANG_JAVA:
		sprintf(java_xmx, "-Xmx%dM", mem_lmt);
		//sprintf(java_xmx, "-XX:MaxPermSize=%dM", mem_lmt);

		execle("/usr/bin/java", "/usr/bin/java",java_xmx , // the security manager has been removed in later java version
		       "Main", (char *) NULL,envp);
		break;
	case LANG_RUBY:
		//system("/ruby Main.rb<data.in");
		execle("/usr/bin/ruby", "/usr/bin/ruby", "Main.rb", (char *)NULL,envp);
		break;
	case LANG_BASH: //bash
		execle("/bin/bash", "/bin/bash", "Main.sh", (char *)NULL,envp);
		break;
	case LANG_PYTHON: //Python
		if (!py2)
		{     
			execle("/usr/bin/python2", "/usr/bin/python2", "Main.py", (char *)NULL,envp);	
		}
		else
		{      
			execle("/usr/bin/python3", "/usr/bin/python3", "Main.py", (char *)NULL,envp);
		}
		break;
	case LANG_PHP: //php
		execle("/usr/bin/php", "/usr/bin/php", "Main.php", (char *)NULL,  envp);
		break;
	case LANG_PERL: //perl
		execle("/usr/bin/perl", "/usr/bin/perl", "Main.pl", (char *)NULL,  envp);
		break;
	case LANG_CSHARP: //Mono C#
		execle("/usr/bin/mono", "/usr/bin/mono","--debug",  "Main.exe", (char *)NULL,envp);
		break;
	case LANG_SCHEME: //guile
		execle("/usr/bin/guile", "/usr/bin/guile", "Main.scm", (char *)NULL,envp);
		break;
	case LANG_LUA: //lua
		execle("/usr/bin/lua", "/usr/bin/lua", "Main.lua", (char *)NULL,envp);
		break;
	case LANG_JS: //Node.js
		execle("/usr/bin/node", "/usr/bin/node", "Main.js", (char *)NULL,envp);
		break;
	case LANG_SQL: //sqlite3
		execle("/usr/bin/sqlite3", "/usr/bin/sqlite3", "data.db", (char *)NULL,envp);
		break;
	case LANG_MATLAB: //octave
		execl("/usr/bin/octave-cli", "/usr/bin/octave-cli",  "-W", "-q", "-H", "Main.m", (char *)NULL); //"--no-init-file", "--no-init-path", "--no-line-editing", "--no-site-file"
		break;
	case LANG_R:
		execle("/usr/bin/Rscript", "/usr/bin/Rscript", "Main.R", (char *)NULL,envp);
		break;
	case LANG_SB3:
		execle("/usr/bin/scratch-run", "/usr/bin/scratch-run", "Main.sb3", (char *)NULL,envp);
		break;

	}
	//sleep(1);
	printf("Execution error, USE_DOCKER:%d !\nYou need to install compiler VM or runtime for your language.",use_docker);
	fflush(stderr);
	exit(0);
}
int fix_python_mis_judge(char *work_dir, int &ACflg, int &topmemory,
						 int mem_lmt)
{
	int comp_res = OJ_AC;

	comp_res = execute_cmd(
		"/bin/grep 'MemoryError'  %s/error.out", work_dir);

	if (!comp_res)
	{
		printf("Python need more Memory!");
		ACflg = OJ_ML;
		topmemory = mem_lmt * STD_MB;
	}

	return comp_res;
}

int fix_java_mis_judge(char *work_dir, int &ACflg, int &topmemory,
					   int mem_lmt)
{
	int comp_res = OJ_AC;
	execute_cmd("chmod 700 %s/error.out", work_dir);
	if (DEBUG)
		execute_cmd("cat %s/error.out", work_dir);
	comp_res = execute_cmd("/bin/grep 'Exception'  %s/error.out", work_dir);
	if (!comp_res)
	{
		printf("Exception reported\n");
		ACflg = OJ_RE;
	}
	execute_cmd("cat %s/error.out", work_dir);

	comp_res = execute_cmd(
		"/bin/grep 'java.lang.OutOfMemoryError'  %s/error.out", work_dir);

	if (!comp_res)
	{
		printf("JVM need more Memory!");
		ACflg = OJ_ML;
		topmemory = mem_lmt * STD_MB;
	}

	if (!comp_res)
	{
		printf("JVM need more Memory or Threads!");
		ACflg = OJ_ML;
		topmemory = mem_lmt * STD_MB;
	}
	comp_res = execute_cmd("/bin/grep 'Could not create'  %s/error.out",
						   work_dir);
	if (!comp_res)
	{
		printf("jvm need more resource,tweak -Xmx(OJ_JAVA_BONUS) Settings");
		ACflg = OJ_RE;
		//topmemory=0;
	}
	return comp_res;
}
float raw_text_judge( char *infile, char *outfile, char *userfile, float *total_mark){
        float mark=0;
        int total=0;
        FILE *in=fopen(infile,"r");
        if(fscanf(in,"%d",&total)!=1) return -1;
        fclose(in);
        FILE *out=fopen(outfile,"r");
        int num=0;
        char * user_answer=NULL;
        size_t user_length;
        size_t buf_length;
        size_t ans_length;
        float m[total+1];
        char * ans[total+1];
        *total_mark=0;
        for(int i=1;i<=total;i++){
                ans[i]=NULL;
                buf_length=0;
                if(fscanf(out,"%d",&num)!=1) return -2;
                if(i==num){
                        if(fscanf(out,"%*[^\[][%f]",&m[num])!=1) return -3;
                        *total_mark+=m[num];
                        ans_length=getline(&ans[i],&buf_length,out);
                        for(int j=ans_length-1;'\n'==ans[i][j]||'\r'==ans[i][j];j--){
                                ans[i][j]='\0';
                        }
                        trim(ans[i]);
                }else{
                }
        }
        fclose(out);
        FILE *user=fopen(userfile,"r");
        FILE *df=fopen("diff.out","a");
        for(int i=1;i<=total;i++){
                user_answer=NULL;
                buf_length=0;
                if(fscanf(user,"%d",&num)==EOF) continue;
                user_length=getline(&user_answer,&buf_length,user);
                int j=0;
                for(j=user_length-1;'\n'==user_answer[j]||'\r'==user_answer[j];j--){
                        user_answer[j]='\0';
                }
                trim(user_answer);
                if(num>0&&num<=total){
                        if(strcasecmp(ans[num],user_answer)==0 || strcasecmp(ans[num],"*")==0 || strcasecmp(ans[num]," *")==0){
                                mark+=m[num];
                        }else{
                              if(raw_text_diff) fprintf(df,"%d Answer:%s[You:%s] -%.1f\n",i,ans[i],user_answer,m[i]);
                        }
                        m[num]=0;
                }else{
                        break;
                }
        }
        for(int i=1;i<=total;i++){
                free(ans[i]);
        }
        free(user_answer);
        fclose(user);
        fclose(df);
        return mark;

}
int special_judge(char *oj_home, int problem_id, char *infile, char *outfile,
				  char *userfile,double* pass_rate,int spj)
{

	pid_t pid;
	char spjpath[BUFFER_SIZE/2];
	char tpjpath[BUFFER_SIZE/2];
	if (DEBUG) printf("pid=%d\n", problem_id);
	// prevent privileges settings caused spj fail in [issues686]
	execute_cmd("chgrp judge %s/data/%d/spj %s %s %s", oj_home, problem_id,infile, outfile, userfile);
	execute_cmd("chmod 751 %s/data/%d/spj %s %s %s", oj_home, problem_id,infile, outfile, userfile);
	
	pid = fork();
	int ret = 0;
	if (pid == 0)
	{

		while (setgid(1536) != 0)
			sleep(1);
		while (setuid(1536) != 0)
			sleep(1);
		while (setresuid(1536, 1536, 1536) != 0)
			sleep(1);

		struct rlimit LIM; // time limit, file limit& memory limit

		LIM.rlim_cur = 15;
		LIM.rlim_max = LIM.rlim_cur;
		setrlimit(RLIMIT_CPU, &LIM);
		alarm(0);
		alarm(10);

		// file limit
		LIM.rlim_max = STD_F_LIM + STD_MB;
		LIM.rlim_cur = STD_F_LIM;
		setrlimit(RLIMIT_FSIZE, &LIM);
		sprintf(spjpath,"%s/data/%d/spj", oj_home, problem_id);
		sprintf(tpjpath,"%s/data/%d/tpj", oj_home, problem_id);

		if( access( tpjpath , X_OK ) == 0 ){
			ret = execute_cmd("%s/data/%d/tpj %s %s %s 2>> diff.out ", oj_home, problem_id, infile, userfile, outfile);    // testlib style
			if (DEBUG) printf("testlib spj return: %d\n", ret);
		}else if (access( spjpath , X_OK ) == 0 ) {	
			ret = execute_cmd("%s/data/%d/spj %s %s %s", oj_home, problem_id, infile, outfile, userfile);    // hustoj style
			if (DEBUG) printf("hustoj spj return: %d\n", ret);
		}else if(spj == 2){

		}else{
			printf("spj tpj not found problem: %d\n", problem_id);		
			ret=1;
		}
		if (ret)
			exit(1);
		else
			exit(0);
	}
	else
	{
		int status;

		waitpid(pid, &status, 0);
		ret = WEXITSTATUS(status);
		if (DEBUG)
			printf("recorded spj: %d\n", ret);
	}
	return ret;
}
void judge_solution(int &ACflg, int &usedtime, double time_lmt, int spj,
					int p_id, char *infile, char *outfile, char *userfile, int &PEflg,
					int lang, char *work_dir, int &topmemory, int mem_lmt,
					int solution_id, int num_of_test,double * pass_rate)
{
	//usedtime-=1000;
	int comp_res;
	if (num_of_test == 0 )
		num_of_test = 1.0;
	
	if (ACflg == OJ_AC){
		int real_limit=1000;
		if(time_limit_to_total){                 // 如果限制总时间
			real_limit=time_lmt*1000;
			if(total_time>real_limit) ACflg = OJ_TL;   // 总时间超过
			if(usedtime> real_limit) ACflg = OJ_TL;    // 单点超过
		}else if(num_of_test>0){                        // 如果数据点不为0，且限制单点时间
			real_limit=num_of_test*time_lmt*1000;
			//if(total_time>real_limit) ACflg = OJ_TL;  //总时间超过测试点数*限制
			if(usedtime> time_lmt*1000) ACflg = OJ_TL; // 单点超过限制
		}else{                                            //测试数为0 ，这种情况不应该出现，但给出，作为保险。
			real_limit=time_lmt*1000; // fallback
			if(usedtime > real_limit) ACflg = OJ_TL;
			if(total_time>real_limit) ACflg = OJ_TL;
		}
	}
	if (topmemory > mem_lmt * STD_MB)
		ACflg = OJ_ML; //issues79
	// compare
	if (ACflg == OJ_AC)
	{
		if (spj)
		{
			comp_res = special_judge(oj_home, p_id, infile, outfile, userfile,pass_rate,spj);

			if (comp_res == 0)
				comp_res = OJ_AC;
			else
			{
				if (DEBUG)
					printf("fail test %s\n", infile);
				comp_res = OJ_WA;
			}
		}
		else
		{
			comp_res = compare(outfile, userfile,infile,userfile);
		}
		if (comp_res == OJ_WA)
		{
			ACflg = OJ_WA;
			if (DEBUG)
				printf("fail test %s\n", infile);
		}
		else if (comp_res == OJ_PE)
			PEflg = OJ_PE;
		ACflg = comp_res;
	}
	//jvm popup messages, if don't consider them will get miss-WrongAnswer
	if (lang == LANG_JAVA )
	{
		comp_res = fix_java_mis_judge(work_dir, ACflg, topmemory, mem_lmt);
	}
	if (lang == LANG_PYTHON )
	{
		comp_res = fix_python_mis_judge(work_dir, ACflg, topmemory, mem_lmt);
	}
}

int get_page_fault_mem(struct rusage &ruse, pid_t &pidApp)
{
	//java use pagefault
	int m_vmpeak, m_vmdata, m_minflt;
	m_minflt = ruse.ru_minflt * getpagesize();
	if (0 && DEBUG)
	{
		m_vmpeak = get_proc_status(pidApp, "VmPeak:");
		m_vmdata = get_proc_status(pidApp, "VmData:");
		printf("VmPeak:%d KB VmData:%d KB minflt:%d KB\n", m_vmpeak, m_vmdata,
			   m_minflt >> 10);
	}
	return m_minflt;
}
void print_runtimeerror(char* infile,char *err)
{
	FILE *ferr = fopen("error.out", "a+");
	fprintf(ferr, "%s:%s\n",infile, err);
	fclose(ferr);
}
void clean_session(pid_t p)
{
	//char cmd[BUFFER_SIZE];
	const char *pre = "ps awx -o \"\%p \%P\"|grep -w ";
	const char *post = " | awk \'{ print $1  }\'|xargs kill -9";
	execute_cmd("%s %d %s", pre, p, post);
	execute_cmd("ps aux |grep \\^judge|awk '{print $2}'|xargs kill");
}

void watch_solution(pid_t pidApp, char *infile, int &ACflg, int spj,
					char *userfile, char *outfile, int solution_id, int lang,
					int &topmemory, int mem_lmt, int &usedtime, double time_lmt, int &p_id,
					int &PEflg, char *work_dir)
{
	// 父进程中的保姆程序
	int tempmemory = 0;

	if (DEBUG)
		printf("pid=%d judging %s\n", pidApp, infile);

	int status, sig, exitcode;
	char white_code[256]={0};
        white_code[0]=1;  // add more if new signal complain
        white_code[5]=1;
        white_code[17]=1;
        white_code[23]=1;
        white_code[133]=1;
        char buf[BUFFER_SIZE];

	struct user_regs_struct reg;
	struct rusage ruse;
	int first = true;
	int tick=0;
	long outFileSize=get_file_size(outfile);
	while (1)
	{
		tick++;
		// check the usage

		wait4(pidApp, &status, __WALL, &ruse);     //等待子进程切换内核态（调用系统API或者运行状态变化）
		if (first)
		{ //
			ptrace(PTRACE_SETOPTIONS, pidApp, NULL, PTRACE_O_TRACESYSGOOD | PTRACE_O_TRACEEXIT
				   //	|PTRACE_O_EXITKILL
				   //	|PTRACE_O_TRACECLONE
				   //	|PTRACE_O_TRACEFORK
				   //	|PTRACE_O_TRACEVFORK
			           //   有的发行版带的PTRACE不识别以上宏，因此注释掉
			);
		}

		//jvm gc ask VM before need,so used kernel page fault times and page size
		if (lang == LANG_JAVA || 
		    lang == LANG_PHP || 
		    lang == LANG_CSHARP || 
		    lang == LANG_CLANG || lang == LANG_CLANGPP || 
		    lang == LANG_JS || 
		    lang == LANG_GO || 
		    lang == LANG_MATLAB||
		    lang == LANG_COBOL||
		    lang == LANG_SB3
		    )
		{
			tempmemory = get_page_fault_mem(ruse, pidApp);
		}
		else
		{ //other use VmPeak
			tempmemory = get_proc_status(pidApp, "VmPeak:") << 10;
		}
		if (tempmemory > topmemory)
			topmemory = tempmemory;
		if (topmemory > mem_lmt * STD_MB)
		{
			if (DEBUG)
				printf("out of memory %d\n", topmemory);
			if (ACflg == OJ_AC)
				ACflg = OJ_ML;
			ptrace(PTRACE_KILL, pidApp, NULL, NULL);
			break;
		}
		//sig = status >> 8;/*status >> 8 EXITCODE*/

		if (WIFEXITED(status)) { // 子进程已经退出 ，返回值不为0则判RE
                        exitcode = WEXITSTATUS(status);
			char error[BUFFER_SIZE];
                        if(exitcode){
				ACflg=OJ_RE;
                        	sprintf(error, "\t    non-zero return = %d \n", exitcode);
                        	print_runtimeerror(infile+prelen,error);
			}
                        break;
                }
		if ((lang < LANG_RUBY ||lang == LANG_BASH || lang == LANG_CSHARP ) && get_file_size("error.out") && !oi_mode)
		{
			ACflg = OJ_RE;
			//addreinfo(solution_id);
			ptrace(PTRACE_KILL, pidApp, NULL, NULL);
			break;
		}

		if (((tick & 0xff)==0x00) &&(!spj) && get_file_size(userfile) >outFileSize * 2 + 1024)
		{
			ACflg = OJ_OL;
			ptrace(PTRACE_KILL, pidApp, NULL, NULL);
			break;
		}

                exitcode = WEXITSTATUS(status) % 256 ;
                /*exitcode == 5 waiting for next CPU allocation          * ruby using system to run,exit 17 ok
                 *  Runtime Error:Unknown signal xxx need be added here
                 */
                if (white_code[exitcode]
                        // || ( (exitcode == 17||exitcode == 23) && (lang >= LANG_JAVA && lang!= LANG_OBJC && lang != LANG_CLANG && lang != LANG_CLANGPP) )
                   ){  // 进程休眠或等待IO
                        //go on and on
                        ;
                }else{


			if (DEBUG)
			{
				printf("status>>8=%d\n", exitcode);
			}
			//psignal(exitcode, NULL);

			if (ACflg == OJ_AC)
			{
				switch (exitcode)                  // 根据退出的原因给出判题结果
				{
				case SIGCHLD:
				case SIGALRM:
					alarm(0);
					if (DEBUG)
						printf("alarm:%g\n", time_lmt);
				case SIGKILL:
				case SIGXCPU:
					ACflg = OJ_TL;
					usedtime = time_lmt * 1000;
					if (DEBUG)
						printf("TLE:%d\n", usedtime);
					break;
				case SIGXFSZ:
					ACflg = OJ_OL;
					break;
				default:
					ACflg = OJ_RE;
				}
				print_runtimeerror(infile+prelen,strsignal(exitcode));
				sprintf(buf,"if you can confirm the code is right and your system is somehow different from others , try adding: ' white_code[%d]=1; ' after judge_client:2888 for it, but don't panic just for a Segmentfault. ",exitcode);
                                print_runtimeerror(buf,strsignal(exitcode));

			}
			ptrace(PTRACE_KILL, pidApp, NULL, NULL);    // 杀死出问题的进程

			break;
		}
		if (WIFSIGNALED(status))
		{
			/*  WIFSIGNALED: if the process is terminated by signal
			 *  由外部信号触发的进程终止
			 *  psignal(int sig, char *s)，like perror(char *s)，print out s, with error msg from system of sig  
			 * sig = 5 means Trace/breakpoint trap
			 * sig = 11 means Segmentation fault
			 * sig = 25 means File size limit exceeded
			 */
			sig = WTERMSIG(status);

			if (DEBUG)
			{
				printf("WTERMSIG=%d\n", sig);
				psignal(sig, NULL);
			}
			if (ACflg == OJ_AC)
			{
				switch (sig)      //根据原因给出结论
				{
				case SIGCHLD:
				case SIGALRM:
					alarm(0);
				case SIGKILL:
				case SIGXCPU:
					ACflg = OJ_TL;
					break;
				case SIGXFSZ:
					ACflg = OJ_OL;
					break;

				default:
					ACflg = OJ_RE;
				}
				print_runtimeerror(infile+prelen,strsignal(sig));
			}
			break;
		}
		/*     comment from http://www.felix021.com/blog/read.php?1662
		 WIFSTOPPED: return true if the process is paused or stopped while ptrace is watching on it
		 WSTOPSIG: get the signal if it was stopped by signal
		 */

		// check the system calls
	if (!use_ptrace){
		ptrace(PTRACE_SYSCALL, pidApp, NULL, NULL);
		continue;
	}
#ifdef __mips__
//		if(exitcode!=5&&exitcode!=133){
	//https://github.com/strace/strace/blob/master/linux/mips/syscallent-n32.h#L344
		ptrace(PTRACE_GETREGS, pidApp, NULL, &reg);
		call_id=(unsigned int)reg.REG_SYSCALL;
		if( (call_id > 1000 && call_id <5000 )|| (lang == LANG_PYTHON && call_id < 5500)  || call_id> 6500){
		    // not a valid syscall
			ptrace(PTRACE_SYSCALL, pidApp, NULL, NULL);
			continue;
		}else{
#endif
#ifdef __arm__
		call_id=ptrace(PTRACE_GETREGS, pidApp, NULL, &reg);
		call_id = ((unsigned int)reg.REG_SYSCALL) % call_array_size;
#endif
#ifdef __aarch64__
		call_id=ptrace(PTRACE_GETREGS, pidApp, (void *)NT_ARM_SYSTEM_CALL, &reg);
		print_arm_regs(reg.regs);
		printf("return call_id:%d\n",call_id);
		call_id = ((unsigned int)reg.REG_SYSCALL) % call_array_size;
		printf("regist call_id:%d\n",call_id);
#endif
#ifdef __i386__
		call_id=ptrace(PTRACE_GETREGS, pidApp, NULL, &reg);
			call_id = ((unsigned int)reg.REG_SYSCALL) % call_array_size;
#endif 
#ifdef __x86_64__
		call_id=ptrace(PTRACE_GETREGS, pidApp, NULL, &reg);
			call_id = ((unsigned int)reg.REG_SYSCALL) % call_array_size;
#endif 
//			printf("call_id:%x %d\n",call_id,call_counter[call_id]);
			call_id = call_id % call_array_size;
//			printf("call_id:%d %d\n",call_id,call_counter[call_id]);
			

			if (record_call)
			{
				printf("new call id:%d\n",call_id);
				call_counter[call_id]++;
				printf("call %d: %d\n",call_id,call_counter[call_id]);
			}else if (call_counter[call_id])
			{
				call_counter[call_id]--;
			}
			else
			{ //do not limit JVM syscall for using different JVM 对于非法的系统调用，给出具体编号给管理员参考
				ACflg = OJ_RE;
				char error[BUFFER_SIZE];
				sprintf(error,
						"[ERROR] solution_id:%d called a Forbidden system call:%u [%u]\n"
						" TO FIX THIS , ask admin to add the CALLID into corresponding LANG_XXV[] located at okcalls32/64.h ,\n"
						"and recompile judge_client. \n"
						"if you are admin and you don't know what to do ,\n"
						"中文解释查看知乎 https://zhuanlan.zhihu.com/p/24498599\n",
						solution_id, call_id,(unsigned int)reg.REG_SYSCALL);

				write_log(error);
				print_runtimeerror(infile+prelen,error);
				//ptrace(PTRACE_SYSCALL, pidApp, NULL, NULL);
				//continue;
				ptrace(PTRACE_KILL, pidApp, NULL, NULL);
		
			}
			call_id=0;
#ifdef __mips__
//		   }
		}
#endif
		ptrace(PTRACE_SYSCALL, pidApp, NULL, NULL);    // 继续等待下一次的系统调用或其他中断
		first = false;
		//usleep(1);
	}
	
	ptrace(PTRACE_KILL, pidApp, NULL, NULL);    // 杀死出问题的进程
        execute_cmd("kill -9 %d",pidApp);          // 抗拒的补刀
	usedtime += (ruse.ru_utime.tv_sec * 1000 + ruse.ru_utime.tv_usec / 1000) * cpu_compensation; // 统计用户态耗时，在更快速的CPU上加以cpu_compensation倍数放大
	usedtime += (ruse.ru_stime.tv_sec * 1000 + ruse.ru_stime.tv_usec / 1000) * cpu_compensation; // 统计内核态耗时，在更快速的CPU上加以cpu_compensation倍数放大

	//clean_session(pidApp);
}

void clean_workdir(char *work_dir)
{
	umount(work_dir);
	if (DEBUG)
	{
		execute_cmd("/bin/rmdir %s/log/* 2>/dev/null", work_dir);
		execute_cmd("/bin/rm -rf %s/log/* 2>/dev/null", work_dir);
		execute_cmd("mkdir %s/log/ 2>/dev/null", work_dir);
		execute_cmd("/bin/mv %s/* %s/log/ 2>/dev/null", work_dir, work_dir);
	}
	else
	{
		execute_cmd("mkdir %s/log/ 2>/dev/null", work_dir);
		execute_cmd("/bin/mv %s/* %s/log/ 2>/dev/null", work_dir, work_dir);
		execute_cmd("/bin/rmdir %s/log/* 2>/dev/null", work_dir);
		execute_cmd("/bin/rm -rf %s/log/* 2>/dev/null", work_dir);
	}
}

void init_parameters(int argc, char **argv, int &solution_id,
					 int &runner_id)
{
	if (argc < 3)
	{
		fprintf(stderr,"HUSTOJ judge_client ver 20240407\n\n");
		fprintf(stderr, "Normal Usage:\n\t%s <solution_id> <runner_id>\n\n", argv[0]);
		fprintf(stderr, "Multi OJ with Specific home :\n\t%s <solution_id> <runner_id> [judge_base_path].\n\n",
				argv[0]);
		fprintf(stderr,
				"Debug with Specific home:\n\t%s <solution_id> <runner_id> [judge_base_path] [debug].\n\n",
				argv[0]);
		fprintf(stderr,"\n\n");
		fprintf(stderr,"Example:\n\tsudo %s 1001 0 /home/judge/ debug  \n\n",argv[0]);
		exit(1);
	}
	DEBUG = (argc > 4);
	record_call = (argc > 5);
	if (argc > 5)
	{
		strcpy(LANG_NAME, argv[5]);
	}
	if (argc > 3)
		strcpy(oj_home, argv[3]);
	else
		strcpy(oj_home, "/home/judge");

	if(chdir(oj_home)) exit(-2); // change the dir// init our work

	solution_id = atoi(argv[1]);
	runner_id = atoi(argv[2]);
}
int get_sim(int solution_id, int lang, int pid, int &sim_s_id)
{
        char src_pth[BUFFER_SIZE];
        //char cmd[BUFFER_SIZE];
        sprintf(src_pth, "Main.%s", lang_ext[lang]);
        int sim = execute_cmd("/usr/bin/sim.sh %s %d", src_pth, pid);
        FILE *pf;
        pf = fopen("sim", "r");
        if (!sim){
                execute_cmd("/bin/mkdir ../data/%d/ac/ 2>/dev/null", pid);
                execute_cmd("/bin/chown www-data ../data/%d/ac/ 2>/dev/null", pid);
                execute_cmd("/bin/cp %s ../data/%d/ac/%d.%s 2>/dev/null", src_pth, pid, solution_id,lang_ext[lang]);
                execute_cmd("/bin/chown www-data ../data/%d/ac/%d.%s 2>/dev/null", pid, solution_id,lang_ext[lang]);
 		 //c cpp will
                if (lang == 0)
                        execute_cmd("/bin/ln ../data/%d/ac/%d.%s ../data/%d/ac/%d.%s 2>/dev/null", pid,
                                                solution_id, lang_ext[lang], pid, solution_id,
                                                lang_ext[lang + 1]);
                if (lang == 1)
                        execute_cmd("/bin/ln ../data/%d/ac/%d.%s ../data/%d/ac/%d.%s 2>/dev/null", pid,
                                                solution_id, lang_ext[lang], pid, solution_id,
                                                lang_ext[lang - 1]);
        }else{
                if (pf){
                        if(2==fscanf(pf, "%d%d", &sim, &sim_s_id));
                }
        }
        if(pf) fclose(pf);
        if (solution_id <= sim_s_id)
                sim = 0;
        return sim;
}

void mk_shm_workdir(char *work_dir)
{
	char shm_path[BUFFER_SIZE];
	sprintf(shm_path, "/dev/shm/hustoj/%s", work_dir);
	execute_cmd("/bin/mkdir -p %s  2>/dev/null", shm_path);
	execute_cmd("/bin/ln -s %s %s/  2>/dev/null", shm_path, oj_home);
	execute_cmd("/bin/chown judge %s  2>/dev/null", shm_path);
	execute_cmd("chmod 755 %s  2>/dev/null", shm_path);
	//sim need a soft link in shm_dir to work correctly
	sprintf(shm_path, "/dev/shm/hustoj/%s/", oj_home);
	execute_cmd("/bin/ln -s %s/data %s  2>/dev/null", oj_home, shm_path);
}
int count_in_files(char *dirpath)
{
	const char *cmd = "ls -l %s/*.in|wc -l";
	int ret = 0;
	FILE *fjobs = read_cmd_output(cmd, dirpath);
	if(1!=fscanf(fjobs, "%d", &ret)) printf("warning count files fail");;
	pclose(fjobs);

	return ret;
}

int get_test_file(char *work_dir, int p_id)
{
	char filename[BUFFER_SIZE/2];
	char localfile[BUFFER_SIZE];
	time_t remote_date, local_date;
	int ret = 0;
	const char *cmd =
		" wget --post-data=\"gettestdatalist=1&time=1&pid=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s%s\"";
	FILE *fjobs = read_cmd_output(cmd, p_id, http_baseurl, http_apipath);
	while (fgets(filename, sizeof(filename) - 1, fjobs) != NULL)
	{

		if(1!=sscanf(filename, "%ld", &remote_date)) printf("http remote time stamp read fail\n");
		if (fgets(filename, sizeof(filename) - 1, fjobs) == NULL)
			break;
		if(1!=sscanf(filename, "%s", filename)) printf("http filename read fail\n");
		if (http_judge && (!data_list_has(filename)))
			data_list_add(filename);
		sprintf(localfile, "%s/data/%d/%s", oj_home, p_id, filename);
		if (DEBUG)
			printf("localfile[%s]\n", localfile);

		struct stat fst;
		stat(localfile, &fst);
		local_date = fst.st_mtime;

		if (access(localfile, F_OK ) == -1 || local_date < remote_date)
		{

			if (strcmp(filename, "spj") == 0)
				continue;
			execute_cmd("/bin/mkdir -p %s/data/%d", oj_home, p_id);
			const char *cmd2 =
				" wget --post-data=\"gettestdata=1&filename=%d/%s\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O \"%s\"  \"%s%s\"";
			execute_cmd(cmd2, p_id, filename, localfile, http_baseurl, http_apipath);
			ret++;

			if (strcmp(filename, "spj.c") == 0)
			{
				//   sprintf(localfile,"%s/data/%d/spj.c",oj_home,p_id);
				if (access(localfile, F_OK ) == 0)
				{
					const char *cmd3 = "gcc -o %s/data/%d/spj %s/data/%d/spj.c";
					execute_cmd(cmd3, oj_home, p_id, oj_home, p_id);
				}
			}
			if (strcmp(filename, "spj.cc") == 0)
			{
				//     sprintf(localfile,"%s/data/%d/spj.cc",oj_home,p_id);
				if (access(localfile, F_OK ) == 0)
				{
					const char *cmd4 =
						"g++ -o %s/data/%d/spj %s/data/%d/spj.cc";
					execute_cmd(cmd4, oj_home, p_id, oj_home, p_id);
				}
			}
		}
	}
	pclose(fjobs);

	return ret;
}
void print_call_array()
{
	printf("int LANG_%sV[CALL_ARRAY_SIZE]={", LANG_NAME);
	int i = 0;
	for (i = 0; i < call_array_size; i++)
	{
		if (call_counter[i]>0)
		{
			printf("%d,", i);
		}
	}
	printf("0};\n");

	printf("int LANG_%sC[CALL_ARRAY_SIZE]={", LANG_NAME);
	for (i = 0; i < call_array_size; i++)
	{
		if (call_counter[i])
		{
			printf("%d,",call_counter[i]);
		}
	}
	printf("0};\n");
}
int has_mark_in_name=0;
int mark_of_name(const char * name){
	int mark;
	printf("reading mark from %s \n",name);
	if(sscanf(name,"%*[^\[][%d]",&mark)==1){
		printf("reading mark %d \n",mark);
		has_mark_in_name=1;
		return mark;
	}else{
		return 10;
	}
}

int same_subtask(char * last,char * cur){
    int i=0;
    for(i=0;last[i]!='.' && cur[i]!='.';i++){
    	if (last[i]!=cur[i]){
		break;
	}
    }	
    return last[i]==cur[i];
}
int main(int argc, char **argv)
{

	char work_dir[BUFFER_SIZE];
	//char cmd[BUFFER_SIZE];
	char user_id[BUFFER_SIZE];
	int solution_id = 1000;
	int runner_id = 0;
	int p_id,  mem_lmt, lang, spj, sim, sim_s_id, max_case_time = 0,cid=0;
	double time_lmt;
	char time_space_table[BUFFER_SIZE*100];
	int time_space_index=0;
        umask(0077);
	init_parameters(argc, argv, solution_id, runner_id);

	init_judge_conf();

#ifdef _mysql_h
	if (!http_judge && !init_mysql_conn())
	{
		exit(0); //exit if mysql is down
	}
#endif
	//set work directory to start running & judging
	sprintf(work_dir, "%s/run%s/", oj_home, argv[2]);
	sprintf(lock_file,"%s/client%s.pid",oj_home,argv[2]);

	while ( already_running()) {
		syslog(LOG_ERR | LOG_DAEMON,
				"This working directory is occupied !\n");
		printf("%s already has one judge_client in it!\n",work_dir);
		sleep(5);
	}

	if (shm_run){
		mk_shm_workdir(work_dir);
	}else{
		execute_cmd("mkdir %s",work_dir);
	}
	
	clean_workdir(work_dir);
	
	if(chdir(work_dir)) exit(-3);

	if (http_judge)
		if(!system("/bin/ln -s ../cookie ./")) printf("cookie link fail \n");
	get_solution_info(solution_id, p_id, user_id, lang,cid);
	//get the limit

	if (p_id == 0)
	{
		time_lmt = 5;
		mem_lmt = 128;
		spj = 0;
	}
	else
	{
		get_problem_info(p_id, time_lmt, mem_lmt, spj);
	}
	//copy source file

	get_solution(solution_id, work_dir, lang,p_id);

	//java and other VM language are lucky to have the global bonus in judge.conf
	if (lang >= LANG_JAVA && lang != LANG_OBJC && lang != LANG_CLANG && lang != LANG_CLANGPP  && lang != LANG_GO )
	{ //ObjectivC Clang Clang++ Go not VM or Script
		// the limit for java
		time_lmt = time_lmt + java_time_bonus;
		mem_lmt = mem_lmt + java_memory_bonus;
		// copy java.policy
		if (lang == 3)
		{
			execute_cmd("/bin/cp %s/etc/java0.policy %s/java.policy", oj_home, work_dir);
			execute_cmd("chmod 755 %s/java.policy", work_dir);
			execute_cmd("chown judge %s/java.policy", work_dir);
		}
	}

	//never bigger than judged set value;
	if (time_lmt > 300 || time_lmt < 0)
		time_lmt = 1;
	if (mem_lmt > 1024 || mem_lmt < 1)
		mem_lmt = 1024;

	if (DEBUG)
		printf("time: %g mem: %d\n", time_lmt, mem_lmt);

	// compile
	//      printf("%s\n",cmd);
	// set the result to compiling
	int Compile_OK = 0 ;
        if(spj!=2){
                Compile_OK = compile(lang, work_dir);
        }
	if ( Compile_OK != 0 && spj!=2 )
	{
		addceinfo(solution_id);
		update_solution(solution_id, OJ_CE, 0, 0, 0, 0, 0.0);
		if(!turbo_mode)update_user(user_id);
		if(!turbo_mode)update_problem(p_id,cid);
#ifdef _mysql_h
		if (!http_judge)
			mysql_close(conn);
#endif
		clean_workdir(work_dir);
		write_log("compile error");
		exit(0);
	}
	else
	{
		if (!turbo_mode)
			update_solution(solution_id, OJ_RI, 0, 0, 0, 0, 0.0);
		umount(work_dir);
	}
	//exit(0);
	// run
	char fullpath[BUFFER_SIZE];
	char infile[BUFFER_SIZE/10];
	char outfile[BUFFER_SIZE/10];
	char userfile[BUFFER_SIZE/10];
	sprintf(fullpath, "%s/data/%d", oj_home, p_id); // the fullpath of data dir

	// open DIRs
	//DIR *dp;
	dirent *dirp;
	// using http to get remote test data files
	if (p_id > 0 && http_judge && http_download == 1)
		get_test_file(work_dir, p_id);
	// using rsync to get remote test data files, using id_rsa to login ,change src/install/rsync.sh if need
        if (p_id > 0 && http_download == 2 )
		execute_cmd("%s/rsync.sh %d",oj_home,p_id);
	
	struct dirent **namelist;
        int namelist_len;
	namelist_len = scandir(fullpath,&namelist,inFile,alphasort);
	if(p_id > 0 && namelist_len == -1 ){
		
		write_log("No such dir:%s!\n", fullpath);
#ifdef _mysql_h
		if (!http_judge)
			mysql_close(conn);
#endif
		exit(-1);
	
	}else{
		if(DEBUG){
			printf("total test case:%d \n", namelist_len);
			for(int i=0;i<namelist_len;i++){
				printf("test file %d : %s\n",i+1,namelist[i]->d_name);
			}
		}
		
	}
/*
	if (p_id > 0 && (dp = opendir(fullpath)) == NULL)
	{
		write_log("No such dir:%s!\n", fullpath);
#ifdef _mysql_h
		if (!http_judge)
			mysql_close(conn);
#endif
		exit(-1);
	}
	
	
*/

	int ACflg, PEflg;
	ACflg = PEflg = OJ_AC;
	int namelen;
	int usedtime = 0, topmemory = 0;
	if (lang == LANG_BASH){
			execute_cmd("busybox dos2unix Main.sh", work_dir);
	}
	if(!use_docker){
		//create chroot for ruby bash python
		if (lang == LANG_RUBY)
			copy_ruby_runtime(work_dir);
		if (lang == LANG_BASH){
			copy_bash_runtime(work_dir);
		}
		if (lang == LANG_PYTHON && !python_free)
			copy_python_runtime(work_dir);
		if (lang == LANG_PHP)
			copy_php_runtime(work_dir);
		if (lang == LANG_PERL)
			copy_perl_runtime(work_dir);
	//	if (lang == LANG_CSHARP)
	//		copy_mono_runtime(work_dir);
		if (lang == LANG_OBJC)
			copy_objc_runtime(work_dir);
		if (lang == LANG_FREEBASIC)
			copy_freebasic_runtime(work_dir);
		if (lang == LANG_SCHEME)
			copy_guile_runtime(work_dir);
		if (lang == LANG_LUA)
			copy_lua_runtime(work_dir);
		if (lang == LANG_JS)
			copy_js_runtime(work_dir);
		if (lang == LANG_SQL)
			copy_sql_runtime(work_dir);
	}
	
	// read files and run
	double pass_rate = 0.0;
	float mark=0;
	float total_mark=0,get_mark=0;
	int finalACflg = ACflg;
	if (p_id == 0)
	{ //custom input running
		printf("running a custom input...\n");
		get_custominput(solution_id, work_dir);
		init_syscalls_limits(lang);
		pid_t pidApp = fork();

		if (pidApp == 0)
		{
			run_solution(lang, work_dir, time_lmt, usedtime, mem_lmt,(char *)"data.in",p_id);
		}
		else
		{
			watch_solution(pidApp, infile, ACflg, spj, userfile, outfile,
						   solution_id, lang, topmemory, mem_lmt, usedtime, time_lmt,
						   p_id, PEflg, work_dir);
		}
		if(DEBUG) printf("custom running result:%d PEflg:%d\n",ACflg,PEflg);
		if (ACflg == OJ_RE||get_file_size("error.out")>0)
		{
			if (DEBUG)
				printf("add RE info of %d..... \n", solution_id);
			addreinfo(solution_id);
		}
		else
		{
			addcustomout(solution_id);
		}
		update_solution(solution_id, OJ_TR, usedtime, topmemory >> 10, 0, 0, 0);
		clean_workdir(work_dir);
		exit(0);
	}
/*	
	for (;(dirp = readdir(dp)) != NULL;)
	{
		int namelen = isInFile(dirp->d_name); // check if the file is *.in or not
		if (namelen == 0)
			continue;
		num_of_test++;
	}
	rewinddir(dp);
*/	
	num_of_test=namelist_len;

	if( num_of_test == 0 ){
		print_runtimeerror((char *)"no test data ",(char *)" no *.in file found");
		ACflg = OJ_RE;
		finalACflg = OJ_RE;
		
	}
	char last_name[BUFFER_SIZE];
	int minus_mark=0;
	char path_buf[BUFFER_SIZE];
	sprintf(path_buf,"%s/data/%d/",oj_home,p_id);
	prelen=strlen(path_buf);
	if (prelen<strlen(oj_home)+6) prelen=strlen(oj_home)+6;

	for (int i=0 ; (oi_mode || ACflg == OJ_AC || ACflg == OJ_PE) && i < namelist_len ;i++)
	{
		dirp=namelist[i];

		namelen = isInFile(dirp->d_name); // check if the file is *.in or not
		if (namelen == 0)
			continue;
		mark=mark_of_name(dirp->d_name);
		total_mark+=mark;
		if (http_judge && ( http_download==1 ) && (!data_list_has(dirp->d_name)))
			continue;

		prepare_files(dirp->d_name, namelen, infile, p_id, work_dir, outfile,
					  userfile, runner_id, lang );
		if (access(outfile, R_OK ) == -1)
		{
			//out file does not exist
			char error[BUFFER_SIZE];
			sprintf(error, "missing out file %s, report to system administrator!\n", basename(outfile));
			print_runtimeerror(infile+prelen,error);
			ACflg = OJ_RE;
		}

		init_syscalls_limits(lang);

		pid_t pidApp = fork();                  //创建子进程，这里程序将自身复制一份，两份同时运行，进程根据返回值确定自己的身份

		if (pidApp == 0)                        //返回值是0，我就是子进程 
		{
			if(spj==2){
			       	exit(0);
			}
			run_solution(lang, work_dir, time_lmt, usedtime, mem_lmt,infile,p_id);

		}
		else
		{                                       //返回值非0 ，我是父进程，返回值就是上面那个子进程的pid

			//num_of_test++;
                        //看护子进程，不让他做奇怪的事
			if(spj!=2)watch_solution(pidApp, infile, ACflg, spj, userfile, outfile,
						   solution_id, lang, topmemory, mem_lmt, usedtime, time_lmt,
						   p_id, PEflg, work_dir);
			kill(pidApp,9);
			printf("%s: mem=%d time=%d\n",infile+prelen,topmemory,usedtime);	
			total_time+=usedtime;
			printf("time:%d/%d\n",usedtime,total_time);
			//判断用户程序输出是否正确，给出结果
			judge_solution(ACflg, usedtime, time_lmt, spj, p_id, infile,
						   outfile, userfile, PEflg, lang, work_dir, topmemory,
						   mem_lmt, solution_id, num_of_test,&pass_rate);
			/*
   			if(usedtime > time_lmt * 1000) {          // 如果觉得的显示超时结果的计时过长，可以覆盖数据。
					usedtime = time_lmt * 1000;
			}
			*/
			
			time_space_index+=sprintf(time_space_table+time_space_index,"%s|%ld|%s|%dk|%dms\n",infile+prelen,get_file_size(infile),jresult[ACflg],topmemory/1024,usedtime);
			/*   // full diff code backup
			 if( ACflg != OJ_AC ){
                                FILE *DF=fopen("diff.out","a");
                                fprintf(DF,"%s:%s mem=%dk time=%dms\n",infile+strlen(oj_home)+5,jresult[ACflg],topmemory/1024,usedtime);
                                fprintf(DF,"=============================================================\n");
                                fclose(DF);
                        }			
			*/
			
			
			if (use_max_time)
			{
				max_case_time =
					usedtime > max_case_time ? usedtime : max_case_time;
			}
				usedtime = 0;
			//clean_session(pidApp);
		}
		if (oi_mode)
		{
			if (ACflg == OJ_AC)
			{
				++pass_rate;
				get_mark+=mark;
				if(same_subtask(last_name,dirp->d_name)){ //相同子任务
					if(minus_mark >=0 ){              //本子任务未曾失败
						minus_mark+=mark;         //累计任务内积分
					}else{
						get_mark-=mark;           //任务已经失败，扣除本次得分
					}
				}else{
					minus_mark=mark;                 //跨越任务，积分重新累计
				}
			}else{
				if(same_subtask(last_name,dirp->d_name)){ //相同子任务，初次失败
					if(minus_mark>=0) get_mark-=minus_mark;  //扣除任务内积分
				}
				minus_mark= -1 ;                          //当前任务失败，标记
			}
			if (finalACflg < ACflg)
			{
				finalACflg = ACflg;
			}
			if ( time_limit_to_total && (ACflg==OJ_TL) ) break;     // limit all TLE submit to protect the system from D.o.S flooding
			ACflg = OJ_AC;
		}
#ifdef _mysql_h
		if (!http_judge)
                         check_mysql_conn();
#endif
		strcpy(last_name,dirp->d_name);

	}
	if (ACflg == OJ_AC && PEflg == OJ_PE)
		ACflg = OJ_PE;
	if (sim_enable && ACflg == OJ_AC && (!oi_mode || finalACflg == OJ_AC))
	{ //bash don't supported
		sim = get_sim(solution_id, lang, p_id, sim_s_id);
	}
	else
	{
		sim = 0;
	}
	//if(ACflg == OJ_RE)addreinfo(solution_id);

	if ((oi_mode && finalACflg == OJ_RE) || ACflg == OJ_RE)
	{
		if (DEBUG)
			printf("add RE info of %d..... \n", solution_id);
		FILE *df=fopen("error.out","a");
                fprintf(df,"filename|size|result|memory|time\n--|--|--|--|--\n%s\n",time_space_table);
                fclose(df);
		addreinfo(solution_id);
	}
	if (use_max_time)
	{
		if(DEBUG) printf("use max case time:%d\n",max_case_time);
		usedtime = max_case_time;
	}else{
		if(DEBUG) printf("use total time:%d\n",total_time);
		usedtime = total_time;
	}
	

	if(spj!=2){
		if (oi_mode)
		{
			if (num_of_test > 0){
				pass_rate /= num_of_test;
			}
			if (total_mark > 0 && has_mark_in_name ){
				pass_rate =get_mark;
				 if(total_mark>100) pass_rate /= total_mark;
                                else pass_rate /= 100.0;
			}
			update_solution(solution_id, finalACflg, usedtime, topmemory >> 10, sim,
							sim_s_id, pass_rate);
		}
		else
		{
			if(ACflg==OJ_AC) pass_rate=1.0;
			else pass_rate=0.0;
			update_solution(solution_id, ACflg, usedtime, topmemory >> 10, sim,
							sim_s_id, pass_rate);
		}
	}else{
			char src_pth[BUFFER_SIZE];
			printf("raw text judge %d \n",p_id);
        		sprintf(src_pth, "Main.%s", lang_ext[lang]);
			mark=raw_text_judge(infile, outfile, src_pth,&total_mark);
			printf("raw_text_mark:%.1f\n",mark);
			if(mark>=0 && mark<=total_mark) pass_rate=mark;
			pass_rate/=100.0;
			if(mark==total_mark) finalACflg=ACflg=OJ_AC;else finalACflg=ACflg=OJ_WA;
			update_solution(solution_id, finalACflg,total_mark,mark*10,sim,sim_s_id, pass_rate);

	}
	FILE *df=fopen("diff.out","a");
	fprintf(df,"filename|size|result|memory|time\n--|--|--|--|--\n%s\n",time_space_table);
	fclose(df);
	if(DEBUG) printf("ACflg:%d\n",ACflg);
	printf("final result:%d\n",finalACflg);
	if(ACflg != OJ_RE && finalACflg!= OJ_RE ) adddiffinfo(solution_id);
	if(!turbo_mode)update_user(user_id);
	if(!turbo_mode)update_problem(p_id,cid);
	clean_workdir(work_dir);

	if (DEBUG)
		write_log("result=%d", oi_mode ? finalACflg : ACflg);
#ifdef _mysql_h
	if (!http_judge)
		mysql_close(conn);
#endif
	if (record_call)
	{
		print_call_array();
	}
	//closedir(dp);
	free(namelist);
	return 0;
}
