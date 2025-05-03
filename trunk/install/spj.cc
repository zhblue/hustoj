#include <stdio.h>

const double eps = 1e-4; // 定义允许的浮点数误差范围

int main(int argc, char *args[]) // 主函数，接收命令行参数
{
    // 打开三个文件流：
    FILE *f_in = fopen(args[1], "r");   // 测试输入文件（题目给定的输入）
    FILE *f_out = fopen(args[2], "r");  // 标准答案文件（正确输出）
    FILE *f_user = fopen(args[3], "r"); // 用户提交的文件（待验证输出）

    // 文件打开失败检查
    if (f_user == NULL || f_out == NULL || f_in == NULL)
        return 1; // 文件打开失败时返回 1（SPJ 运行错误）

    int ret = 0;       // 初始化返回值为 0（默认答案正确）
    int T;             // 数据组数
    double a, x;       // a 存储正确答案，x 存储用户答案
    fscanf(f_in, "%d", &T); // 从输入文件读取测试数据组数

    // 处理每组测试数据
    while (T--) {
        fscanf(f_out, "%lf", &a);  // 读取标准答案
        fscanf(f_user, "%lf", &x); // 读取用户答案

        // 比较两者差值是否超过允许误差
        if (fabs(a - x) > eps)
            ret = 1; // 若任意一组数据不满足精度要求，标记为错误答案
    }

    // 关闭所有文件流
    fclose(f_in);
    fclose(f_out);
    fclose(f_user);

    return ret; // 返回判题结果：0 表示正确，1 表示错误
}
