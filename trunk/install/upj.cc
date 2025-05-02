// 包含必要的系统头文件
#include <iostream>          // 标准输入输出流
#include <unistd.h>          // POSIX系统调用（fork, pipe, dup2等）
#include <sys/wait.h>        // 进程等待相关函数
#include <sys/poll.h>        // I/O多路复用poll系统调用
#include <fcntl.h>           // 文件控制（fcntl）
#include <signal.h>          // 信号处理
#include <string>            // 字符串操作
#include <cstring>           // C风格字符串操作
#include <sys/socket.h>      // socket相关操作（shutdown）
#include <chrono>            // 时间处理（超时机制）

// 将文件描述符设置为非阻塞模式
void set_nonblocking(int fd) {
    // 获取当前文件状态标志
    int flags = fcntl(fd, F_GETFL, 0);
    if (flags == -1) {
        perror("fcntl F_GETFL");
        exit(EXIT_FAILURE);  // 获取失败则终止程序
    }
    // 添加非阻塞标志并设置
    if (fcntl(fd, F_SETFL, flags | O_NONBLOCK) == -1) {
        perror("fcntl F_SETFL");
        exit(EXIT_FAILURE);  // 设置失败则终止程序
    }
}

// 启动子程序并建立管道通信
// 参数：程序路径，输入/输出文件描述符引用，进程ID引用
void start_process(const std::string& program, int& in_fd, int& out_fd, pid_t& pid) {
    int stdin_pipe[2];   // 子进程标准输入管道 [0]读端 [1]写端
    int stdout_pipe[2];  // 子进程标准输出管道 [0]读端 [1]写端

    // 创建标准输入管道
    if (pipe(stdin_pipe)) {
        perror("pipe stdin");
        exit(EXIT_FAILURE);
    }
    // 创建标准输出管道
    if (pipe(stdout_pipe)) {
        perror("pipe stdout");
        exit(EXIT_FAILURE);
    }

    // 创建子进程
    pid = fork();
    if (pid < 0) {
        perror("fork");
        exit(EXIT_FAILURE);
    }

    if (pid == 0) { // 子进程代码块
        // 关闭父进程使用的管道端
        close(stdin_pipe[1]);   // 关闭输入管道的写端（父进程使用）
        close(stdout_pipe[0]);  // 关闭输出管道的读端（父进程使用）

        // 重定向标准输入输出到管道
        dup2(stdin_pipe[0], STDIN_FILENO);   // 标准输入重定向到输入管道读端
        dup2(stdout_pipe[1], STDOUT_FILENO); // 标准输出重定向到输出管道写端

        // 关闭原始管道描述符（已完成重定向）
        close(stdin_pipe[0]);
        close(stdout_pipe[1]);

        // 执行目标程序
        execl(program.c_str(), program.c_str(), (char*)NULL);
        
        // 如果执行失败（不会返回）
        perror("execl");
        perror(program.c_str()); // 打印具体程序路径的错误信息
        exit(EXIT_FAILURE);       // 终止子进程
    } else { // 父进程代码块
        // 关闭子进程使用的管道端
        close(stdin_pipe[0]);   // 关闭输入管道读端（子进程使用）
        close(stdout_pipe[1]);  // 关闭输出管道写端（子进程使用）

        // 保存管道描述符供父进程使用
        in_fd = stdin_pipe[1];  // 父进程通过此描述符向子进程写入数据
        out_fd = stdout_pipe[0];// 父进程通过此描述符读取子进程输出

        // 设置非阻塞模式
        set_nonblocking(in_fd);  // 子进程的输入管道（父进程写入端）
        set_nonblocking(out_fd); // 子进程的输出管道（父进程读取端）
    }
}

// 检查进程状态
// 参数：进程ID，退出状态引用，是否阻塞等待
// 返回：true表示进程已退出，false表示仍在运行
bool check_process_exit(pid_t pid, int& exit_status, bool wait) {
    int status;  // 保存子进程状态
    // 调用waitpid，根据wait参数决定是否阻塞
    pid_t ret = waitpid(pid, &status, wait ? 0 : WNOHANG);
    
    if (ret == -1) {  // 错误处理
        perror("waitpid");
        return true;  // 认为进程已终止
    }
    if (ret == pid) {  // 成功获取到进程状态
        if (WIFEXITED(status)) {  // 正常退出
            exit_status = WEXITSTATUS(status);  // 获取退出状态码
        } else if (WIFSIGNALED(status)) {  // 被信号终止
            exit_status = 128 + WTERMSIG(status); // 按照bash惯例返回128+信号值
        } else {  // 其他情况
            exit_status = -1;  // 标记为异常状态
        }
        return true;  // 进程已终止
    }
    return false;  // 进程仍在运行
}

int main() {
    int main_in, main_out;     // Main进程的输入/输出管道描述符
    int checker_in, checker_out; // Checker进程的输入/输出管道描述符
    pid_t main_pid = -1, checker_pid = -1;  // 进程ID

    // 设置可执行权限并列出目录（调试用，生产环境应移除）
    system("chmod +x *");  // 为当前目录所有文件添加执行权限
    system("ls -l");       // 列出目录信息（调试文件权限）

    // 注意：此处启动顺序是先Checker后Main，可能与通信流程相关
    start_process("./checker", checker_in, checker_out, checker_pid);
    start_process("./Main", main_in, main_out, main_pid);

    std::cout << "Started Main (PID: " << main_pid 
              << ") and Checker (PID: " << checker_pid << ")\n";

    // poll结构体数组（监控两个输出管道）
    struct pollfd fds[2];  
    bool main_active = true;      // Main进程活动状态
    bool checker_active = true;   // Checker进程活动状态
    int main_exit_status = -1;    // Main退出状态
    int checker_exit_status = -1; // Checker退出状态
    char buffer[4096];            // 数据缓冲区

    // 超时控制相关
    auto start_time = std::chrono::steady_clock::now(); // 记录启动时间
    constexpr int TIMEOUT_SECONDS = 5;  // 超时时间设为5秒

    // 初始同步延迟（等待进程初始化）
    usleep(20000);  // 20毫秒，等待进程启动完成

    // 通信状态跟踪
    bool checker_sent_first_output = false; // Checker是否已发送初始输出
    bool main_replied = false;             // Main是否已回复

    // 主事件循环
    while (main_active || checker_active) {
        // 检查是否超时
        if (std::chrono::duration_cast<std::chrono::seconds>(
            std::chrono::steady_clock::now() - start_time).count() > TIMEOUT_SECONDS) {
            std::cerr << "Timeout reached\n";
            break;  // 跳出主循环
        }

        /* 进程状态检查 */
        // 检查Main进程状态
        if (main_active && check_process_exit(main_pid, main_exit_status, false)) {
            // 检测到Main提前退出（在Checker发送输出或Main回复之前）
            if (!checker_sent_first_output || !main_replied) {
                std::cerr << "Main exited too early!\n";
            }
            std::cerr << "Main process exited with status " << main_exit_status << "\n";
            main_active = false;
            // 关闭Main的输出管道（不再读取）
            close(main_out);
            // 通知Checker输入结束（关闭写端）
            shutdown(checker_in, SHUT_WR);
            close(checker_in);
        }
        
        // 检查Checker进程状态
        if (checker_active && check_process_exit(checker_pid, checker_exit_status, false)) {
            std::cerr << "Checker process exited with status " << checker_exit_status << "\n";
            checker_active = false;
            close(checker_out);  // 关闭Checker的输出管道
        }

        /* 设置poll监控 */
        int nfds = 0;  // 有效文件描述符计数
        if (main_active) {
            fds[nfds].fd = main_out;    // 监控Main的输出
            fds[nfds].events = POLLIN;  // 关注可读事件
            fds[nfds].revents = 0;      // 清空返回事件
            nfds++;
        }
        if (checker_active) {
            fds[nfds].fd = checker_out; // 监控Checker的输出
            fds[nfds].events = POLLIN;
            fds[nfds].revents = 0;
            nfds++;
        }

        if (nfds == 0) break;  // 没有需要监控的描述符，退出循环

        // 调用poll等待事件，100ms超时
        int ret = poll(fds, nfds, 100);
        if (ret == -1) {
            perror("poll");
            break;  // poll错误时退出循环
        }
        if (ret == 0) continue;  // 没有事件，继续循环

        /* 处理Checker的输出（优先处理） */
        for (int i = 0; i < nfds; i++) {
            if (fds[i].fd == checker_out && (fds[i].revents & POLLIN)) {
                // 读取Checker的输出数据
                ssize_t n = read(checker_out, buffer, sizeof(buffer)-1);
                if (n > 0) {  // 成功读取数据
                    buffer[n] = '\0';  // 添加字符串终止符
                    std::cout << "Checker:" << buffer;
                    checker_sent_first_output = true;  // 标记已发送初始输出

                    // 将数据转发给Main的输入（如果Main仍在运行）
                    if (main_active) {
                        write(main_in, buffer, n);  // 写入Main的输入管道
                        fsync(main_in);  // 强制刷新，确保数据写入
                    }
                } else if (n == 0) {  // EOF（管道关闭）
                    close(checker_out);
                    checker_active = false;
                }
                // n < 0且errno为EAGAIN/EWOULDBLOCK时忽略（非阻塞模式正常情况）
            }
        }

        /* 处理Main的输出 */
        for (int i = 0; i < nfds; i++) {
            if (fds[i].fd == main_out && (fds[i].revents & POLLIN)) {
                ssize_t n = read(main_out, buffer, sizeof(buffer)-1);
                if (n > 0) {  // 成功读取数据
                    buffer[n] = '\0';
                    std::cout << "Main:" << buffer;
                    main_replied = true;  // 标记Main已回复

                    // 将数据转发给Checker的输入（如果Checker仍在运行）
                    if (checker_active) {
                        write(checker_in, buffer, n);
                        fsync(checker_in);  // 强制刷新
                    }
                } else if (n == 0) {  // EOF（管道关闭）
                    close(main_out);
                    main_active = false;
                    // 通知Checker输入结束
                    shutdown(checker_in, SHUT_WR);  // 关闭写方向
                    close(checker_in);  // 关闭文件描述符
                }
            }
        }
    } // end while

    /* 后处理阶段 */
    // 检查是否出现Checker发送但Main未回复的情况
    if (checker_sent_first_output && !main_replied) {
        std::cerr << "Warning: Checker sent output but Main didn't reply!\n";
        usleep(50000);  // 额外等待50ms给Main响应机会
    }

    /* 清理仍在运行的进程 */
    // 处理Main进程
    if (main_active) {
        std::cerr << "Terminating Main process\n";
        kill(main_pid, SIGTERM);  // 先尝试正常终止
        usleep(10000);  // 等待10ms
        if (!check_process_exit(main_pid, main_exit_status, false)) {
            kill(main_pid, SIGKILL);  // 强制终止
            check_process_exit(main_pid, main_exit_status, true);
        }
        close(main_in);   // 关闭输入管道
        close(main_out);  // 关闭输出管道
    }

    // 处理Checker进程
    if (checker_active) {
        std::cerr << "Terminating Checker process\n";
        kill(checker_pid, SIGTERM);
        usleep(10000);
        if (!check_process_exit(checker_pid, checker_exit_status, false)) {
            kill(checker_pid, SIGKILL);
            check_process_exit(checker_pid, checker_exit_status, true);
        }
        close(checker_in);
        close(checker_out);
    }

    // 回收所有僵尸进程（非阻塞方式）
    while (waitpid(-1, NULL, WNOHANG) > 0);

    // 输出最终状态
    std::cout << "Final status - Main: " << main_exit_status 
              << ", Checker: " << checker_exit_status << "\n";

    return checker_exit_status;  // 返回Checker的退出状态作为本程序退出码
}
