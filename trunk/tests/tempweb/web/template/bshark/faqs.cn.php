<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>faqs - <?php echo $OJ_NAME; ?></title>
    <?php require("./template/bshark/header-files.php"); ?>
    <style>
        .faqs-card {
            border-radius: 20px;
            border: 1px solid #00000020;
            padding: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <h2 style="ui header">常见问答</h2>
        <div class="card">
            <div class="card-body">
                <font color=green>Q</font>:gets函数没有了吗?<br>
                <font color=red>A</font>:gets函数因为不能限制输入的长度，造成了历史上大量的缓冲区溢出漏洞，因此在最新版本中被彻底删除了，请使用fgets这个函数取代。
                或者使用下面的宏定义来取代：<br>
                <pre><code>#define gets(S) fgets(S,sizeof(S),stdin)</code></pre>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                <p>Q:这个在线裁判系统使用什么样的编译器和编译选项?<br>
                    A:系统运行于<a href="http://www.debian.org/">Debian</a>/<a href="http://www.ubuntu.com">Ubuntu</a>
                    Linux. 使用<a href="http://gcc.gnu.org/">GNU GCC/G++</a> 作为C/C++编译器,
                    <a href="http://www.freepascal.org">Free Pascal</a> 作为pascal 编译器 ，用
                    <a href="http://openjdk.java.net/">openjdk-7</a> 编译 Java. 对应的编译选项如下:<br>
                </p>
                <table class="ui very basic fluid stackable table">
                    <tr>
                        <td>C:</td>
                        <td>gcc Main.c -o Main -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE
                            <pre><code class='cpp'>#pragma GCC optimize ("O2")</code></pre> 可以手工开启O2优化
                        </td>
                    </tr>
                    <tr>
                        <td>C++:</td>
                        <td>g++ -fno-asm -Wall -lm --static -std=c++11 -DONLINE_JUDGE -o Main Main.cc</td>
                    </tr>
                    <tr>
                        <td>Pascal:</td>
                        <td>fpc Main.pas -oMain -O1 -Co -Cr -Ct -Ci </td>
                    </tr>
                    <tr>
                        <td>Java:</td>
                        <td>
                            <font color="blue">javac -J-Xms32m -J-Xmx256m Main.java
                                <br>
                                <font size="-1" color="red">*Java has 2 more seconds and 512M more memory when running and judging.
                        </td>
                    </tr>
                </table>
                <p> 编译器版本为（系统可能升级编译器版本，这里仅供参考）:<br>
                    <font color=blue>Gcc version 9.3.0 (Ubuntu 9.3.0-17ubuntu1~20.04)</font><br>
                    <font color=blue>Glibc 2.31-0ubuntu9.2</font><br>
                    <font color=blue>Free Pascal Compiler version 3.0.4+dfsg-23 [2019/11/25] for x86_64</font><br>
                    <font color=blue>Openjdk "14.0.2"</font><br>
                    <font color=blue>Python 3.8.5</font><br>
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p>Q:程序怎样取得输入、进行输出?<br>
                    A:你的程序应该从标准输入 stdin('Standard Input')获取输入，并将结果输出到标准输出 stdout('Standard Output').例如,在C语言可以使用 'scanf'
                    ，在C++可以使用'cin' 进行输入；在C使用 'printf' ，在C++使用'cout'进行输出.</p>
                <p>用户程序不允许直接读写文件, 如果这样做可能会判为运行时错误 "Runtime Error"。<br>
                    <br>
                    下面是 1000题的参考答案
                </p>
                C++:<br>
                <pre>
<code class="cpp">
#include &lt;iostream&gt;
using namespace std;
int main(){
        // io speed up
        const char endl = '\n';
        std::ios::sync_with_stdio(false);
        cin.tie(nullptr);
        int a,b;
        while(cin >> a >> b)
                cout << a+b << endl;
        return 0;
}
</code>
</pre>
                C:<br>
                <pre>
<code class="c">
#include &lt;stdio.h&gt;
int main(){
        int a,b;
        while(scanf("%d %d",&amp;a, &amp;b) != EOF)
                printf("%d\n",a+b);
        return 0;
}
</code>
</pre>
                PASCAL:<br>
                <pre><code class="delphi">
program p1001(Input,Output); 
var 
    a,b:Integer; 
begin 
     while not eof(Input) do 
         begin 
             Readln(a,b); 
             Writeln(a+b); 
         end; 
end.
</code>
</pre>
                <br>
                Java:<br>
                <pre><code class="java">
import java.util.*;
public class Main{
    public static void main(String args[]){
        Scanner cin = new Scanner(System.in);
        int a, b;
        while (cin.hasNext()){
            a = cin.nextInt(); b = cin.nextInt();
            System.out.println(a + b);
        }
    }
}</code></pre>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                Q:为什么我的程序在自己的电脑上正常编译，而系统告诉我编译错误!<br>
                A:GCC的编译标准与VC6有些不同，更加符合c/c++标准:<br>
                <ul>
                    <li>main 函数必须返回int, void main 的函数声明会报编译错误。<br>
                    <li>i 在循环外失去定义 "for(int i=0...){...}"<br>
                    <li>itoa 不是ansi标准函数.<br>
                    <li>__int64 不是ANSI标准定义，只能在VC使用, 但是可以使用long long声明64位整数。<br>如果用了__int64,试试提交前加一句#define __int64 long long,
                        scanf和printf 请使用%lld作为格式
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                Q:系统返回信息都是什么意思?<br>
                A:详见下述:<br>
                <table class="ui very basic fluid stackable table">
                    <tr>
                        <td>
                            <?php echo $MSG_Pending; ?>
                        </td>
                        <td>系统忙，你的答案在排队等待. </td>
                    </tr>
                    <tr>
                        <td><?php echo $MSG_Pending_Rejudging; ?></td>
                        <td>因为数据更新或其他原因，系统将重新判你的答案.</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Compiling; ?>
                        </td>
                        <td>正在编译.</td>
                    </tr>
                    <tr>
                        <td><?php echo $MSG_Running_Judging; ?></td>
                        <td>正在运行和判断.</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Accepted; ?>
                        </td>
                        <td>程序通过! </td>
                    </tr>
                    <tr>
                        <td><?php echo $MSG_Presentation_Error; ?></td>
                        <td>答案基本正确，但是格式不对。 </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Wrong_Answer; ?>
                        </td>
                        <td>答案不对，仅仅通过样例数据的测试并不一定是正确答案，一定还有你没想到的地方.</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Time_Limit_Exceed; ?>
                        </td>
                        <td>运行超出时间限制，检查下是否有死循环，或者应该有更快的计算方法。</td>
                    </tr>
                    <tr>
                        <td><?php echo $MSG_Memory_Limit_Exceed; ?></td>
                        <td>超出内存限制，数据可能需要压缩，检查内存是否有泄露。</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Output_Limit_Exceed; ?>
                        </td>
                        <td>输出超过限制，你的输出比正确答案长了两倍.</td>
                    </tr>
                    <tr>
                        <td><?php echo $MSG_Runtime_Error; ?></td>
                        <td>运行时错误，非法的内存访问，数组越界，指针漂移，调用禁用的系统函数。请点击后获得详细输出。</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $MSG_Compile_Error; ?>
                        </td>
                        <td>编译错误，请点击后获得编译器的详细输出。</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                Q:如何参加在线比赛?<br>
                A:<a href=registerpage.php>注册</a> 一个帐号，然后就可以练习，点击比赛列表Contests可以看到正在进行的比赛并参加。<br>
                <br>
            </div>
        </div>
    </div>
    </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>