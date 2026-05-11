<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo $OJ_NAME?></title>  
    <?php include("template/$OJ_TEMPLATE/css.php");?>	    


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
    <?php include("template/$OJ_TEMPLATE/nav.php");?>	    
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
<hr>
<center>
  <font size="+3"><?php echo $OJ_NAME?> Online Judge FAQ</font>
</center>
	      
	      <hr>
	      
<p><font color=green>Q</font>:gets函数没有了吗?<br>
   <font color=red>A</font>:gets函数因为不能限制输入的长度，造成了历史上大量的缓冲区溢出漏洞，因此在最新版本中被彻底删除了，请使用fgets这个函数取代。
   或者使用下面的宏定义来取代：<br>
   <pre>#define gets(S) fgets(S,sizeof(S),stdin)</pre><br>
</p>	      
	      
<hr>
<p><font color=green>Q</font>:这个在线裁判系统使用什么样的编译器和编译选项?<br>
  <font color=red>A</font>:系统运行于<a href="http://www.debian.org/">Debian</a>/<a href="http://www.ubuntu.com">Ubuntu</a>
	Linux. 使用<a href="http://gcc.gnu.org/">GNU GCC/G++</a> 作为C/C++编译器,
	<a href="http://www.freepascal.org">Free Pascal</a> 作为pascal 编译器 ，用
	<a href="http://openjdk.java.net/">openjdk-17.0.4</a> 编译 Java. 对应的编译选项如下:<br>
</p>
<table border="1">
  <tr>
    <td>C:</td>
    <td><font color=blue>gcc Main.c -o Main  -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE</font>
	    <pre>#pragma GCC optimize ("O2")</pre> 可以手工开启O2优化
	  </td>
  </tr>
  <tr>
    <td>C++:</td>
    <td><font color=blue>g++ -fno-asm -Wall -lm --static -std=c++14 -DONLINE_JUDGE -o Main Main.cc</font></td>
  </tr>
  <tr>
    <td>Pascal:</td>
    <td><font color=blue>fpc Main.pas -oMain -O1 -Co -Cr -Ct -Ci </font></td>
  </tr>
  <tr>
    <td>Java:</td>
    <td><font color="blue">javac -J-Xms32m -J-Xmx256m Main.java</font>
    <br>
    <font size="-1" color="red">*Java has 2 more seconds and 512M more memory when running and judging.</font>
    </td>
  </tr>
</table>
<p>  编译器版本为（系统可能升级编译器版本，这里仅供参考）:<br>
	
<font color=blue>Gcc version 9.4.0 (Ubuntu 9.4.0-1ubuntu1~20.04.1)</font><br>
<font color=blue>Glibc 2.31-0ubuntu9.2</font><br>
<font color=blue>Free Pascal Compiler version 3.0.4+dfsg-23 [2019/11/25] for x86_64</font><br>
<font color=blue>Openjdk 17</font><br>
<font color=blue>Python 3.8.5</font><br>
	      </p>
	      
<hr>
<p><font color=green>Q</font>:程序怎样取得输入、进行输出?<br>
  <font color=red>A</font>:你的程序应该从标准输入 stdin('Standard Input')获取输入，并将结果输出到标准输出 stdout('Standard Output').例如,在C语言可以使用 'scanf' ，在C++可以使用'cin' 进行输入；在C使用 'printf' ，在C++使用'cout'进行输出.</p>
<p>用户程序不允许直接读写文件, 如果这样做可能会判为运行时错误 "<font color=green>Runtime Error</font>"。<br>
  <br>
下面是 1000题的参考答案</p>
<p> C++:<br>
</p>
<pre><font color="blue">
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
</font></pre>
C:<br>
<pre><font color="blue">
#include &lt;stdio.h&gt;
int main(){
    int a,b;
    while(scanf("%d %d",&amp;a, &amp;b) != EOF)
        printf("%d\n",a+b);
    return 0;
}
</font></pre>
 PASCAL:<br>
<pre><font color="blue">
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
</font></pre>
<br><br>

Java:<br>
<pre><font color="blue">
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
}</font></pre>
Python3<br>
<pre>import io
import sys
sys.stdout = io.TextIOWrapper(sys.stdout.buffer,encoding='utf8')
for line in sys.stdin:
    a = line.split()
    print(int(a[0]) + int(a[1]))
</pre>
	    为了方便使用本地文件调试，C/C++也可以用下面的方法来仅在本地运行时进行输入、输出重定向。
<pre>
#ifdnef ONLINE_JUDGE
    freopen("sample.in", "r", stdin);
    freopen("sample.out", "w", stdout);
#endif
</pre>
	
<hr>
<font color=green>Q</font>:为什么我的程序在自己的电脑上正常编译，而系统告诉我编译错误!<br>
<font color=red>A</font>:GCC的编译标准与VC6有些不同，更加符合c/c++标准:<br>
<ul>
  <li><font color=blue>main</font> 函数必须返回<font color=blue>int</font>, <font color=blue>void main</font> 的函数声明会报编译错误。<br> 
  <li><font color=green>i</font> 在循环外失去定义 "<font color=blue>for</font>(<font color=blue>int</font> <font color=green>i</font>=0...){...}"<br>
  <li><font color=green>itoa</font> 不是ansi标准函数.<br>
  <li><font color=green>__int64</font> 不是ANSI标准定义，只能在VC使用, 但是可以使用<font color=blue>long long</font>声明64位整数。<br>如果用了__int64,试试提交前加一句#define __int64 long long, scanf和printf 请使用%lld作为格式
</ul>
<hr>
<font color=green>Q</font>:系统返回信息都是什么意思?<br>
<font color=red>A</font>:详见下述:<br>
<p><font color=blue>Pending</font> : 系统忙，你的答案在排队等待. </p>
<p><font color=blue>Pending Rejudge</font>: 因为数据更新或其他原因，系统将重新判你的答案.</p>
<p><font color=blue>Compiling</font> : 正在编译.<br>
</p>
<p><font color="blue">Running &amp; Judging</font>: 正在运行和判断.<br>
</p>
<p><font color=blue>Accepted</font> : 程序通过!<br>
  <br>
  <font color=blue>Presentation Error</font> : 答案基本正确，但是格式不对。<br>
  <br>
  <font color=blue>Wrong Answer</font> : 答案不对，仅仅通过样例数据的测试并不一定是正确答案，一定还有你没想到的地方.<br>
  <br>
  <font color=blue>Time Limit Exceeded</font> : 运行超出时间限制，检查下是否有死循环，或者应该有更快的计算方法。<br>
  <br>
  <font color=blue>Memory Limit Exceeded</font> : 超出内存限制，数据可能需要压缩，检查内存是否有泄露。<br>
  <br>
  <font color=blue>Output Limit Exceeded</font>: 输出超过限制，你的输出比正确答案长了两倍.<br>
  <br>
  <font color=blue>Runtime Error</font> : 运行时错误，非法的内存访问，数组越界，指针漂移，调用禁用的系统函数。请点击后获得详细输出。<br>
</p>
<p>  <font color=blue>Compile Error</font> : 编译错误，请点击后获得编译器的详细输出。<br>
  <br>
</p>
<hr>
<font color=green>Q</font>:如何参加在线比赛?<br>
<font color=red>A</font>:<a href=registerpage.php>注册</a> 一个帐号，然后就可以练习，点击比赛列表Contests可以看到正在进行的比赛并参加。<br>
<br>
<hr>
<center>
  <font color=green size="+2">其他问题请访问<a href="bbs.php"><?php echo $OJ_NAME?>论坛系统</a></font>
</center>
<hr>
<center>
  <table width=100% border=0>
    <tr>
      <td align=right width=65%>
      <a href = "index.php"><font color=red><?php echo $OJ_NAME?></font></a> 
      <a href = "https://github.com/zhblue/hustoj"><font color=red>2024.8.4</font></a></td>
    </tr>
  </table>
</center>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    

  </body>
</html>
