<?php $show_title="$MSG_FAQ - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
    <h1 class="ui center aligned header">帮助</h1>
    <div style="font-content">
        <h2 class="ui header">评测</h2>
        <p>
            <br> C 使用 <code>gcc 11.4.0</code> 编译，命令为
            &nbsp;<code>gcc Main.c -o Main -fno-asm -Wall -lm --static -O2 -std=c99 -DONLINE_JUDGE</code>
            <br> C++ 使用 <code>g++ 11.4.0</code> 编译，命令为
            &nbsp;<code>g++ -fno-asm -Wall -lm --static -O2 -std=c++17 -DONLINE_JUDGE -o Main Main.cc</code>；
            <br> 您可以使用 <code>#pragma GCC optimize ("O0")</code> 手工关闭 O2 优化；
            <br> Pascal 使用 <code>fpc 3.2.2</code> 编译，命令为
            &nbsp;<code>fpc Main.pas -oMain -O1 -Co -Cr -Ct -Ci</code>。
            <br> Java 使用 <code>OpenJDK 17.0.4</code> 编译，命令为
            <code>	javac -J-Xms64m -J-Xmx128m Main.java</code>，如果您的代码中没有 <code>public class</code>，请将入口类命名为 <code>Main</code>，在评测时提供额外 2 秒的运行时间和 64MB 的运行内存。
            <br>
            这里给出的编译器版本仅供参考，请以实际编译器版本为准。
        </p>
        <p>请使用<strong>标准输入输出</strong>。</p>
<h2 class="ui header">Q: cin/cout为什么会超时（TLE）?</h2>
<p>A: cin/cout因为默认同步stdin/stdout而变慢，并产生更多的系统调用而受到性能影响，可以在main函数开头加入下面代码加速：
       <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">ios::sync_with_stdio(false);
cin.tie(0);</pre>
        </div>

        * 另外，请使用'\n'而不是 endl ，因为endl默认会增加刷新操作，而导致输出缓冲失效，降低效率。
    </p>

<h2 class="ui header">Q: gets函数没有了吗?</h2>
<p>A: gets函数因为不能限制输入的长度，造成了历史上大量的缓冲区溢出漏洞，因此在最新版本中被彻底删除了，请使用fgets这个函数取代。 或者使用下面的宏定义来取代：
    <div class="ques-view">   #define gets(S) fgets(S,sizeof(S),stdin)  </div>
</p>
<h2 class="ui header">Q: 为什么我的代码在本地正常，提交以后被判错? </h2>
<p>A: 不要使用rewind来清空输入缓冲，OJ的输入本质是文件，与键盘输入逻辑不一样。 如果你发现别人可以正确完成问题，那么有可能是你的代码没有考虑到所有的情况。
    并不是通过样例数据就代表一定是正确答案。如果你发现所有人都不能正确提交该题，那么有可能是测试数据有问题，请向管理员<?php echo $OJ_ADMIN ?>反馈。
    
</p>
        
<h2 class="ui header">个人资料<br></h2>
        <p>本站不提供头像存储服务，而是使用 QQ 头像显示。请使用QQ邮箱注册 ，系统自动取用您在QQ的头像。</p>
        <h2 class="ui header">返回结果说明<br></h2>
        <div class="ques-view">
            <p>试题的解答提交后由评分系统评出即时得分，每一次提交会判决结果会及时通知；系统可能的反馈信息包括：</p>
            <li>等待评测：评测系统还没有评测到这个提交，请稍候</li>
            <li>正在评测：评测系统正在评测，稍候会有结果</li>
            <li>编译错误：您提交的代码无法完成编译，点击“编译错误”可以看到编译器输出的错误信息</li>
            <li>答案正确：恭喜！您通过了这道题</li>
            <li>格式错误：您的程序输出的格式不符合要求（比如空格和换行与要求不一致）</li>
            <li>答案错误：您的程序未能对评测系统的数据返回正确的结果</li>
            <li>运行超时：您的程序未能在规定时间内运行结束</li>
            <li>内存超限：您的程序使用了超过限制的内存</li>
            <li>运行错误：您的程序在运行过程中崩溃了，发生了如段错误、浮点错误等</li>
            <li>输出超限：您的程序输出了过多内容，一般可能是无限循环输出导致的结果</li>
        </div>


        <h2>程序样例</h2>
        <p>以下样例程序可用于解决这道简单的题目：<strong>读入2个整数A和B，然后输出它们的和。</strong></p>
        <p><strong>gcc (.c)</strong></p>
        <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">
<code class="lang-c">#include &lt;stdio.h&gt;
int main(){
    int a, b;
    while(scanf("%d %d",&amp;a, &amp;b) != EOF){
        printf("%d\n", a + b);
    }
    return 0;
}</code></pre>
        </div>
        <p><strong>g++ (.cpp)</strong></p>
        <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">
<code class="lang-c++">#include &lt;iostream&gt;
using namespace std;
int main(){
    // io speed up
    const char endl = '\n';
    std::ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int a, b;
    while (cin &gt;&gt; a &gt;&gt; b){
        cout &lt;&lt; a+b &lt;&lt; endl;
    }
    return 0;
}</code></pre>
        </div>
        <p><strong>fpc (.pas)</strong></p>
        <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">
<code class="lang-pascal">var
a, b: integer;
begin
    while not eof(input) do begin
        readln(a, b);
        writeln(a + b);
    end;
end.</code></pre>
        </div>
        <p><strong>javac (.java)</strong></p>
        <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">
<code class="lang-java">import java.util.Scanner;	
public class Main {
    public static void main(String[] args) {
        Scanner in = new Scanner(System.in);
        while (in.hasNextInt()) {
            int a = in.nextInt();
            int b = in.nextInt();
            System.out.println(a + b);
        }
    }
}</code></pre>
        </div>
        <p><strong>python3 (.py)</strong></p>
        <div class="ui existing segment">
            <pre style="margin-top: 0; margin-bottom: 0; ">
<code class="lang-c">import io
import sys
sys.stdout = io.TextIOWrapper(sys.stdout.buffer,encoding='utf8')
for line in sys.stdin:
    a = line.split()
    print(int(a[0]) + int(a[1]))</code></pre>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
