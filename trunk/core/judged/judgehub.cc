/*
 * Copyright 2021 zhblue <newsclan@gmail.com>
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
#include <sys/wait.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <signal.h>
#include <sys/resource.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define BUFFER_SIZE 1024

#define ONE_YEAR_SECONDS  (31536000L)

static char oj_udpserver[BUFFER_SIZE]="127.0.0.1";
static int  oj_udpport=2008;
static int  oj_udp_fd;
static char oj_hub_base[BUFFER_SIZE]="/home/saas";

static bool STOP = false;
static int DEBUG = 0;
void wait_udp_msg(int fd)
{
    char buf[BUFFER_SIZE];  //......1024..
    char dir[BUFFER_SIZE];  //......1024..
    char cmd[BUFFER_SIZE<<2];  //......1024..
    char oj_home[BUFFER_SIZE<<2];  //......1024..
    char cnf[BUFFER_SIZE<<2];  //......1024..
    socklen_t len;
    int count;
    long diff;
    struct sockaddr_in clent_addr;  //clent_addr............
        memset(buf, 0, BUFFER_SIZE);
        len = sizeof(clent_addr);
        count = recvfrom(fd, buf, BUFFER_SIZE, 0, (struct sockaddr*)&clent_addr, &len);  //recvfrom.......
        if(count == -1)
        {
            printf("recieve data fail!\n");
            return;
        }
        printf("task msg:%s\n",buf);  //..client......
        sscanf(buf,"%s\n",dir);
        sprintf(oj_home,"%s/%s/",oj_hub_base,dir);  //..client......
        sprintf(cnf,"%s/%s/etc/judge.conf",oj_hub_base,dir);
        struct stat cnfstat;
        if(stat(cnf,&cnfstat)){
                printf("%s read fail...\n",cnf);
        }else{
                struct timeval now;
                gettimeofday(&now,NULL);
                printf(" now_time:%ld\n",now.tv_sec);
                printf("cnf_mtime:%ld\n",cnfstat.st_mtime);
                diff = now.tv_sec - cnfstat.st_mtime;
//                if(labs(diff) > ONE_YEAR_SECONDS ) return;
                if(fork()==0){
                        printf("Running judged on : %s\n",oj_home);
                        execl("/usr/bin/judged", "/usr/bin/judged", oj_home,"debug","4", (char *) NULL);
                        // 如果执行到这里，说明 execl 失败了
                        perror("execl failed");
                        _exit(EXIT_FAILURE); // 必须立即退出，防止污染父进程逻辑
                        //exit(system(cmd));
                }else{
                        waitpid(-1, NULL, WNOHANG);
                }
        }
        memset(buf, 0, BUFFER_SIZE);
//        sprintf(buf, "I have recieved %d bytes data!\n", count);  //..client
//        printf("server:%s\n",buf);  //..........
//        sendto(fd, buf, BUFF_LEN, 0, (struct sockaddr*)&clent_addr, len);  //.....client......clent_addr.....

}

void call_for_exit(int s) {
                STOP = true;
                printf("Stopping judge hub ...\n");
}

void write_log(const char *fmt, ...) {
        va_list ap;
        char buffer[4096];
//      time_t          t = time(NULL);
//      int             l;
        sprintf(buffer, "/var/log/judgehub.log");
        FILE *fp = fopen(buffer, "ae+");
        if (fp == NULL) {
                fprintf(stderr, "openfile error!\n");
        }
        va_start(ap, fmt);
        vsprintf(buffer, fmt, ap);
        fprintf(fp, "%s\n", buffer);
        if (DEBUG)
                printf("%s\n", buffer);
        va_end(ap);
        fclose(fp);

}

int lockfile(int fd) {
        struct flock fl;
        fl.l_type = F_WRLCK;
        fl.l_start = 0;
        fl.l_whence = SEEK_SET;
        fl.l_len = 0;
        return (fcntl(fd, F_SETLK, &fl));
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
        DEBUG = argc>3 ;
        if(argc >1){
                strcpy(oj_hub_base,argv[1]);
        }else{
                printf("check out https://github.com/zhblue/hustoj/blob/master/wiki/JudgeHub.md \n");
        }
        if(argc >2){
                sscanf(argv[2],"%d",&oj_udpport);
        }
        if (!DEBUG)
                daemon_init();
        oj_udp_fd = socket(AF_INET, SOCK_DGRAM, 0);
        if(oj_udp_fd<0){
                printf("udp fd open failed! \n");
                exit(-1);
        }
        struct sockaddr_in ser_addr;
        memset(&ser_addr, 0, sizeof(ser_addr));
        ser_addr.sin_family = AF_INET;
        ser_addr.sin_addr.s_addr = inet_addr(oj_udpserver);
        ser_addr.sin_port = htons(oj_udpport);
        struct timeval timeOut;
        timeOut.tv_sec = 60 ;                 //..5s..
        timeOut.tv_usec = 0;
        if (setsockopt(oj_udp_fd, SOL_SOCKET, SO_RCVTIMEO, &timeOut, sizeof(timeOut)) < 0)
        {
                printf("time out setting failed\n");
        }
        oj_udp_ret=bind(oj_udp_fd, (struct sockaddr*)&ser_addr, sizeof(ser_addr));
        if(oj_udp_ret<0)
                printf("udp fd open failed! \n");
        //signal(SIGQUIT, call_for_exit);
        //signal(SIGINT, call_for_exit);
        //signal(SIGTERM, call_for_exit);
        while (!STOP) {                 // start to run until call for exit
                printf("waiting task:%d\n",oj_udpport);
                if(STOP) return 100;
                wait_udp_msg(oj_udp_fd);
                if(DEBUG) printf("udp job ... \n");
                waitpid(-1, NULL, WNOHANG);
        }
        return 0;
}
