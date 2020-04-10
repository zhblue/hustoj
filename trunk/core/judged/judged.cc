/*
 * Copyright 2008 sempr <iamsempr@gmail.com>
 *
 * Refacted and modified by zhblue<newsclan@gmail.com> 
 * Bug report email newsclan@gmail.com
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
#include <time.h>
#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>
#include <unistd.h>
#include <syslog.h>
#include <errno.h>
#include <fcntl.h>
#include <stdarg.h>
#include <mysql/mysql.h>
#include <sys/wait.h>
#include <sys/stat.h>
#include <signal.h>
#include <sys/resource.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>


#define BUFFER_SIZE 1024
#define LOCKFILE "/var/run/judged.pid"
#define LOCKMODE (S_IRUSR|S_IWUSR|S_IRGRP|S_IROTH)
#define STD_MB 1048576LL

#define OJ_WT0 0
#define OJ_WT1 1
#define OJ_CI 2
#define OJ_RI 3
#define OJ_AC 4
#define OJ_PE 5
#define OJ_WA 6
#define OJ_TL 7
#define OJ_ML 8
#define OJ_OL 9
#define OJ_RE 10
#define OJ_CE 11
#define OJ_CO 12
static char lock_file[BUFFER_SIZE]=LOCKFILE;
static char host_name[BUFFER_SIZE];
static char user_name[BUFFER_SIZE];
static char password[BUFFER_SIZE];
static char db_name[BUFFER_SIZE];
static char oj_home[BUFFER_SIZE];
static char oj_lang_set[BUFFER_SIZE];
static int port_number;
static int max_running;
static int sleep_time;
static int sleep_tmp;
static int oj_tot;
static int oj_mod;
static int http_judge = 0;
static char http_baseurl[BUFFER_SIZE];
static char http_username[BUFFER_SIZE];
static char http_password[BUFFER_SIZE];

static int  oj_udp = 0;
static char oj_udpserver[BUFFER_SIZE];
static int  oj_udpport=1536;
static int  oj_udp_fd;

static int  oj_redis = 0;
static char oj_redisserver[BUFFER_SIZE];
static int  oj_redisport;
static char oj_redisauth[BUFFER_SIZE];
static char oj_redisqname[BUFFER_SIZE];
static int turbo_mode = 0;


static bool STOP = false;
static int DEBUG = 0;
static int ONCE = 0;
#ifdef _mysql_h
static MYSQL *conn;
static MYSQL_RES *res;
static MYSQL_ROW row;
//static FILE *fp_log;
static char query[BUFFER_SIZE];
#endif
void wait_udp_msg(int fd)
{
    char buf[BUFFER_SIZE];  //......1024..
    socklen_t len;
    int count;
    struct sockaddr_in clent_addr;  //clent_addr............
        memset(buf, 0, BUFFER_SIZE);
        len = sizeof(clent_addr);
        count = recvfrom(fd, buf, BUFFER_SIZE, 0, (struct sockaddr*)&clent_addr, &len);  //recvfrom...............
        if(count == -1)
        {
            printf("recieve data fail!\n");
            return;
        }
        printf("udp client:%s\n",buf);  //..client......
        memset(buf, 0, BUFFER_SIZE);
//        sprintf(buf, "I have recieved %d bytes data!\n", count);  //..client
//        printf("server:%s\n",buf);  //..........
//        sendto(fd, buf, BUFF_LEN, 0, (struct sockaddr*)&clent_addr, len);  //.....client......clent_addr.....

}

void call_for_exit(int s) {
	if(DEBUG){
		STOP = true;
		printf("Stopping judged...\n");
	}else{
		printf("HUSTOJ Refusing to stop...\n Please use kill -9 !\n");
	}
}

void write_log(const char *fmt, ...) {
	va_list ap;
	char buffer[4096];
//	time_t          t = time(NULL);
//	int             l;
	sprintf(buffer, "%s/log/client.log", oj_home);
	FILE *fp = fopen(buffer, "ae+");
	if (fp == NULL) {
		fprintf(stderr, "openfile error!\n");
		system("pwd");
	}
	va_start(ap, fmt);
	vsprintf(buffer, fmt, ap);
	fprintf(fp, "%s\n", buffer);
	if (DEBUG)
		printf("%s\n", buffer);
	va_end(ap);
	fclose(fp);

}

int after_equal(char * c) {
	int i = 0;
	for (; c[i] != '\0' && c[i] != '='; i++)
		;
	return ++i;
}
void trim(char * c) {
	char buf[BUFFER_SIZE];
	char * start, *end;
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
bool read_buf(char * buf, const char * key, char * value) {
	if (strncmp(buf, key, strlen(key)) == 0) {
		strcpy(value, buf + after_equal(buf));
		trim(value);
		if (DEBUG)
			printf("%s\n", value);
		return 1;
	}
	return 0;
}

bool read_env(const char *key, char *value, int len){
	char* env;
	if (env=getenv(key)){
		strncpy(value,env,len);
		trim(value);
		if (DEBUG)
			printf("%s\n", value);
		return true;
	}
	return false;
	
}

void read_int(char * buf, const char * key, int * value) {
	char buf2[BUFFER_SIZE];
	if (read_buf(buf, key, buf2))
		sscanf(buf2, "%d", value);

}
// read the configue file
void init_mysql_conf() {
	FILE *fp = NULL;
	char buf[BUFFER_SIZE];
	host_name[0] = 0;
	user_name[0] = 0;
	password[0] = 0;
	db_name[0] = 0;
	port_number = 3306;
	max_running = 3;
	sleep_time = 1;
	oj_tot = 1;
	oj_mod = 0;
	strcpy(oj_lang_set, "0,1,2,3");
	strcpy(oj_udpserver, "127.0.0.1");
	fp = fopen("./etc/judge.conf", "r");
	if (fp != NULL) {
		while (fgets(buf, BUFFER_SIZE - 1, fp)) {
			read_buf(buf, "OJ_HOST_NAME", host_name);
			read_buf(buf, "OJ_USER_NAME", user_name);
			read_buf(buf, "OJ_PASSWORD", password);
			read_buf(buf, "OJ_DB_NAME", db_name);
			read_int(buf, "OJ_PORT_NUMBER", &port_number);
			read_int(buf, "OJ_RUNNING", &max_running);
			read_int(buf, "OJ_SLEEP_TIME", &sleep_time);
			read_int(buf, "OJ_TOTAL", &oj_tot);

			read_int(buf, "OJ_MOD", &oj_mod);

			read_int(buf, "OJ_HTTP_JUDGE", &http_judge);
			read_buf(buf, "OJ_HTTP_BASEURL", http_baseurl);
			read_buf(buf, "OJ_HTTP_USERNAME", http_username);
			read_buf(buf, "OJ_HTTP_PASSWORD", http_password);
			read_buf(buf, "OJ_LANG_SET", oj_lang_set);
			
			read_int(buf, "OJ_UDP_ENABLE", &oj_udp);
            read_buf(buf, "OJ_UDP_SERVER", oj_udpserver);
            read_int(buf, "OJ_UDP_PORT", &oj_udpport);

			read_int(buf, "OJ_REDISENABLE", &oj_redis);
            read_buf(buf, "OJ_REDISSERVER", oj_redisserver);
            read_int(buf, "OJ_REDISPORT", &oj_redisport);
            read_buf(buf, "OJ_REDISAUTH", oj_redisauth);
            read_buf(buf, "OJ_REDISQNAME", oj_redisqname);
            read_int(buf, "OJ_TURBO_MODE", &turbo_mode);


		}
#ifdef _mysql_h
		sprintf(query,
				"SELECT solution_id FROM solution WHERE language in (%s) and result<2 and MOD(solution_id,%d)=%d ORDER BY result ASC,solution_id ASC limit %d",
				oj_lang_set, oj_tot, oj_mod, 2 *max_running );
#endif
		sleep_tmp = sleep_time;
		//	fclose(fp);
	}
}

void run_client(int runid, int clientid) {
	char buf[BUFFER_SIZE], runidstr[BUFFER_SIZE];
	struct rlimit LIM;
	LIM.rlim_max = 800;
	LIM.rlim_cur = 800;
	setrlimit(RLIMIT_CPU, &LIM);

	LIM.rlim_max = 800 * STD_MB;
	LIM.rlim_cur = 800 * STD_MB;
	setrlimit(RLIMIT_FSIZE, &LIM);
#ifdef __mips__
	LIM.rlim_max = STD_MB << 12;
	LIM.rlim_cur = STD_MB << 12;
#endif
#ifdef __arm__
	LIM.rlim_max = STD_MB << 11;
	LIM.rlim_cur = STD_MB << 11;
#endif
#ifdef __aarch64__
	LIM.rlim_max = STD_MB << 15;
	LIM.rlim_cur = STD_MB << 15;
#endif
#ifdef __i386
	LIM.rlim_max = STD_MB << 11;
	LIM.rlim_cur = STD_MB << 11;
#endif
#ifdef __x86_64__
	LIM.rlim_max = STD_MB << 15;
	LIM.rlim_cur = STD_MB << 15;
#endif
	setrlimit(RLIMIT_AS, &LIM);

	LIM.rlim_cur = LIM.rlim_max = 800* max_running;
	setrlimit(RLIMIT_NPROC, &LIM);

	//buf[0]=clientid+'0'; buf[1]=0;
	sprintf(runidstr, "%d", runid);
	sprintf(buf, "%d", clientid);

	//write_log("sid=%s\tclient=%s\toj_home=%s\n",runidstr,buf,oj_home);
	//sprintf(err,"%s/run%d/error.out",oj_home,clientid);
	//freopen(err,"a+",stderr);

	//if (!DEBUG)
		execl("/usr/bin/judge_client", "/usr/bin/judge_client", runidstr, buf,
				oj_home, (char *) NULL);
	//else
	//	execl("/usr/bin/judge_client", "/usr/bin/judge_client", runidstr, buf,
	//			oj_home, "debug", (char *) NULL);

	//exit(0);
}
#ifdef _mysql_h
int executesql(const char * sql) {

	if (mysql_real_query(conn, sql, strlen(sql))) {
		if (DEBUG)
			write_log("%s", mysql_error(conn));
		sleep(20);
		conn = NULL;
		return 1;
	} else
		return 0;
}
#endif

#ifdef _mysql_h
int init_mysql() {
	if (conn == NULL) {
		conn = mysql_init(NULL);		// init the database connection
		/* connect the database */
		const char timeout = 30;
		mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, &timeout);

		if (!mysql_real_connect(conn, host_name, user_name, password, db_name,
				port_number, 0, 0)) {
			if (DEBUG)
				write_log("%s", mysql_error(conn));
			sleep(2);
			return 1;
		} else {
			return executesql("set names utf8");
		}
	} else {
			return executesql("commit");
	}
}
#endif
FILE * read_cmd_output(const char * fmt, ...) {
	char cmd[BUFFER_SIZE];

	FILE * ret = NULL;
	va_list ap;

	va_start(ap, fmt);
	vsprintf(cmd, fmt, ap);
	va_end(ap);
	//if(DEBUG) printf("%s\n",cmd);
	ret = popen(cmd, "r");

	return ret;
}
int read_int_http(FILE * f) {
	char buf[BUFFER_SIZE];
	fgets(buf, BUFFER_SIZE - 1, f);
	return atoi(buf);
}
bool check_login() {
	const char * cmd =
			"wget --post-data=\"checklogin=1\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/admin/problem_judge.php\"";
	int ret = 0;

	FILE * fjobs = read_cmd_output(cmd, http_baseurl);
	ret = read_int_http(fjobs);
	pclose(fjobs);

	return ret > 0;
}
void login() {
	if (!check_login()) {
		char cmd[BUFFER_SIZE];
		sprintf(cmd,
				"wget --post-data=\"user_id=%s&password=%s\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/login.php\"",
				http_username, http_password, http_baseurl);
		system(cmd);
	}

}
int _get_jobs_http(int * jobs) {
	login();
	int ret = 0;
	int i = 0;
	char buf[BUFFER_SIZE];
	const char * cmd =
			"wget --post-data=\"getpending=1&oj_lang_set=%s&max_running=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/admin/problem_judge.php\"";
	FILE * fjobs = read_cmd_output(cmd, oj_lang_set, max_running, http_baseurl);
	while (fscanf(fjobs, "%s", buf) != EOF) {
		//puts(buf);
		int sid = atoi(buf);
		if (sid > 0)
			jobs[i++] = sid;
		//i++;
	}
	pclose(fjobs);
	ret = i;
	while (i <= max_running * 2)
		jobs[i++] = 0;
	return ret;
}
#ifdef _mysql_h
int _get_jobs_mysql(int * jobs) {
	if (mysql_real_query(conn, query, strlen(query))) {
		if (DEBUG)
			write_log("%s", mysql_error(conn));
		sleep(20);
		return 0;
	}
	res = mysql_store_result(conn);
	int i = 0;
	int ret = 0;
	while (res!=NULL && (row = mysql_fetch_row(res)) != NULL) {
		jobs[i++] = atoi(row[0]);
	}

	if(res!=NULL&&!executesql("commit")){
		mysql_free_result(res);                         // free the memory
		res=NULL;
	}                        
	else i=0;
	ret = i;
	while (i <= max_running * 2)
		jobs[i++] = 0;
	return ret;
}
#endif
int _get_jobs_redis(int * jobs){
        int ret=0;
        const char * cmd="redis-cli -h %s -p %d -a %s --raw rpop %s";
        while(ret<=max_running){
                FILE * fjobs = read_cmd_output(cmd,oj_redisserver,oj_redisport,oj_redisauth,oj_redisqname);
                if(fscanf(fjobs,"%d",&jobs[ret])==1){
                        ret++;
                        pclose(fjobs);
                }else{
                        pclose(fjobs);
                        break;
                }

        }
        int i=ret;
        while (i <= max_running * 2)
                jobs[i++] = 0;
        if(DEBUG) printf("redis return %d jobs",ret);
        return ret;
}

int get_jobs(int * jobs) {
	if (http_judge) {
		return _get_jobs_http(jobs);
	} else {		
		if(oj_redis){
                        return _get_jobs_redis(jobs);
                }else{
#ifdef _mysql_h
                        return _get_jobs_mysql(jobs);
#else
			return 0;
#endif
                }
	}
}

#ifdef _mysql_h
bool _check_out_mysql(int solution_id, int result) {
	char sql[BUFFER_SIZE];
	sprintf(sql,
			"UPDATE solution SET result=%d,time=0,memory=0,judgetime=NOW() WHERE solution_id=%d and result<2 LIMIT 1",
			result, solution_id);
	if (mysql_real_query(conn, sql, strlen(sql))) {
		syslog(LOG_ERR | LOG_DAEMON, "%s", mysql_error(conn));
		return false;
	} else {
		if (conn!=NULL&&mysql_affected_rows(conn) > 0ul)
			return true;
		else
			return false;
	}

}
#endif

bool _check_out_http(int solution_id, int result) {
	login();
	const char * cmd =
			"wget --post-data=\"checkout=1&sid=%d&result=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/admin/problem_judge.php\"";
	int ret = 0;
	FILE * fjobs = read_cmd_output(cmd, solution_id, result, http_baseurl);
	fscanf(fjobs, "%d", &ret);
	pclose(fjobs);

	return ret;
}
bool check_out(int solution_id, int result) {
        if(oj_redis||oj_tot>1) return true;
	if (http_judge) {
		return _check_out_http(solution_id, result);
	} else{
#ifdef _mysql_h
		return _check_out_mysql(solution_id, result);
#else
		return 0;
#endif
	}

}
	static int workcnt = 0;
int work() {
//      char buf[1024];
	int retcnt = 0;
	int i = 0;
	static pid_t ID[100];
	int runid = 0;
	int jobs[max_running * 2 + 1];
	pid_t tmp_pid = 0;

	//for(i=0;i<max_running;i++){
	//      ID[i]=0;
	//}
	for(i=0;i<max_running *2 +1 ;i++)
		jobs[i]=0;

	//sleep_time=sleep_tmp;
	/* get the database info */
	if (!get_jobs(jobs)){
		return 0;
	}
	/* exec the submit */
	for (int j = 0; jobs[j] > 0; j++) {
		runid = jobs[j];
		if (runid % oj_tot != oj_mod)
			continue;
		if (workcnt >= max_running) {           // if no more client can running
			tmp_pid = waitpid(-1, NULL, WNOHANG);     // wait 4 one child exit
			if (DEBUG) printf("try get one tmp_pid=%d\n",tmp_pid);
			for (i = 0; i < max_running; i++){     // get the client id
				if (ID[i] == tmp_pid){
					workcnt--;
					retcnt++;
					ID[i] = 0;
					break; // got the client id
				}
			}
		} else {                                             // have free client

			for (i = 0; i < max_running; i++)     // find the client id
				if (ID[i] == 0)
					break;    // got the client id
		}
		if(i<max_running){
			if (workcnt < max_running && check_out(runid, OJ_CI)) {
				workcnt++;
				ID[i] = fork();                                   // start to fork
				if (ID[i] == 0) {
					if (DEBUG){
						write_log("Judging solution %d", runid);
						write_log("<<=sid=%d===clientid=%d==>>\n", runid, i);
					}
					run_client(runid, i);    // if the process is the son, run it
					workcnt--;
					exit(0);
				}

			} else {
			//	ID[i] = 0;
				if(DEBUG){
					if(workcnt<max_running)
						printf("check out failure ! runid:%d pid:%d \n",i,ID[i]);
					else
						printf("workcnt:%d max_running:%d ! \n",workcnt,max_running);
						
				}
			}
		}
		if(DEBUG)
			  printf("workcnt:%d max_running:%d ! \n",workcnt,max_running);
	}
	while ((tmp_pid = waitpid(-1, NULL,WNOHANG)) > 0) {
		for (i = 0; i < max_running; i++){     // get the client id
			if (ID[i] == tmp_pid){
			
				workcnt--;
				retcnt++;
				ID[i] = 0;
				break; // got the client id
			}
		}
		printf("tmp_pid = %d\n", tmp_pid);
	}
	if (!http_judge) {
#ifdef _mysql_h
		if(res!=NULL) {
			mysql_free_result(res);                         // free the memory
			res=NULL;
		}
		executesql("commit");
#endif
	}
	if (DEBUG && retcnt)
		write_log("<<%ddone!>>", retcnt);
	//free(ID);
	//free(jobs);
	return retcnt;
}

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
		syslog(LOG_ERR | LOG_DAEMON, "can't open %s: %s", LOCKFILE,
				strerror(errno));
		exit(1);
	}
	if (lockfile(fd) < 0) {
		if (errno == EACCES || errno == EAGAIN) {
			close(fd);
			return 1;
		}
		syslog(LOG_ERR | LOG_DAEMON, "can't lock %s: %s", LOCKFILE,
				strerror(errno));
		exit(1);
	}
	ftruncate(fd, 0);
	sprintf(buf, "%d", getpid());
	write(fd, buf, strlen(buf) + 1);
	return (0);
}
int daemon_init(void)

{
	pid_t pid;

	if ((pid = fork()) < 0)
		return (-1);

	else if (pid != 0)
		exit(0); /* parent exit */

	/* child continues */

	setsid(); /* become session leader */

	chdir(oj_home); /* change working directory */

	umask(0); /* clear file mode creation mask */

	close(0); /* close stdin */
	close(1); /* close stdout */
	
	close(2); /* close stderr */
	
	int fd = open( "/dev/null", O_RDWR );
	dup2( fd, 0 );
	dup2( fd, 1 );
	dup2( fd, 2 );
	if ( fd > 2 ){
		close( fd );
	}

	return (0);
}
void turbo_mode2(){
#ifdef _mysql_h
	if(turbo_mode==2){
			char sql[BUFFER_SIZE];
			sprintf(sql," CALL `sync_result`();");
			if (mysql_real_query(conn, sql, strlen(sql)));
	}
#endif

}
int main(int argc, char** argv) {
	int oj_udp_ret=0;
	DEBUG = (argc > 2);
	ONCE = (argc > 3);
	if (argc > 1)
		strcpy(oj_home, argv[1]);
	else
		strcpy(oj_home, "/home/judge");
	chdir(oj_home);    // change the dir

	sprintf(lock_file,"%s/etc/judge.pid",oj_home);
	if (!DEBUG)
		daemon_init();
	if ( already_running()) {
		syslog(LOG_ERR | LOG_DAEMON,
				"This daemon program is already running!\n");
		printf("%s already has one judged on it!\n",oj_home);
		return 1;
	}
	if(!DEBUG)
		system("/sbin/iptables -A OUTPUT -m owner --uid-owner judge -j DROP");
//	struct timespec final_sleep;
//	final_sleep.tv_sec=0;
//	final_sleep.tv_nsec=500000000;
#ifdef _mysql_h
	init_mysql_conf();	// set the database info
#endif
	if(oj_udp){
		oj_udp_fd = socket(AF_INET, SOCK_DGRAM, 0);
		if(oj_udp_fd<0) 
			printf("udp fd open failed! \n");		
		struct sockaddr_in ser_addr;
		memset(&ser_addr, 0, sizeof(ser_addr));
		ser_addr.sin_family = AF_INET;
		ser_addr.sin_addr.s_addr = inet_addr(oj_udpserver);
		ser_addr.sin_port = htons(oj_udpport);
		struct timeval timeOut;
		timeOut.tv_sec = sleep_time;                 //..5s..
		timeOut.tv_usec = 0;
		if (setsockopt(oj_udp_fd, SOL_SOCKET, SO_RCVTIMEO, &timeOut, sizeof(timeOut)) < 0)
		{
			printf("time out setting failed\n");
		}
		oj_udp_ret=bind(oj_udp_fd, (struct sockaddr*)&ser_addr, sizeof(ser_addr));
		if(oj_udp_ret<0) 
			printf("udp fd open failed! \n");
	}
	signal(SIGQUIT, call_for_exit);
	signal(SIGINT, call_for_exit);
	signal(SIGTERM, call_for_exit);
	int j = 1;
	int n = 0;
	while (!STOP) {			// start to run until call for exit
		n=0;
		while (j && (http_judge
#ifdef _mysql_h
			 || !init_mysql()
#endif
		)) {

			j = work();
			n+=j;
			if(turbo_mode==2&&(n>max_running*10||j<max_running)){
				turbo_mode2();
				n=0;
			}
	

			if(ONCE) break;
		}
		turbo_mode2();
		if(ONCE) break;
                if(n==0){
			printf("workcnt:%d\n",workcnt);
			if(oj_udp&&oj_udp_ret==0){
				if(STOP) return 1;
				wait_udp_msg(oj_udp_fd);
                        	if(DEBUG) printf("udp job ... \n");

			}else{
                        	sleep(sleep_time);
                        	if(DEBUG) printf("sleeping ... %ds \n",sleep_time);
			}
                }
		j = 1;
	}
	return 0;
}
