<?php $show_title=isset($MSG_FAQ) ? "$MSG_FAQ - $OJ_NAME" : "FAQ - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-question-circle"></i> <?php echo isset($MSG_FAQ) ? $MSG_FAQ : 'Help'?></h4>
    </div>
    <div class="card-body">

        <h5 class="mt-2"><i class="bi bi-gear"></i> 评测环境</h5>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
                <thead class="table-dark"><tr><th>语言</th><th>编译器</th><th>编译命令</th></tr></thead>
                <tbody>
                    <tr><td>C</td><td>gcc 11.4.0</td><td><code>gcc Main.c -o Main -fno-asm -Wall -lm --static -O2 -std=c99 -DONLINE_JUDGE</code></td></tr>
                    <tr><td>C++</td><td>g++ 11.4.0</td><td><code>g++ -fno-asm -Wall -lm --static -O2 -std=c++17 -DONLINE_JUDGE -o Main Main.cc</code></td></tr>
                    <tr><td>Pascal</td><td>fpc 3.2.2</td><td><code>fpc Main.pas -oMain -O1 -Co -Cr -Ct -Ci</code></td></tr>
                    <tr><td>Java</td><td>OpenJDK 17.0.4</td><td><code>javac -J-Xms64m -J-Xmx128m Main.java</code></td></tr>
                    <tr><td>Python3</td><td>Python 3.10</td><td><code>python3 Main.py</code></td></tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-secondary small">
            <i class="bi bi-info-circle"></i> 编译器版本仅供参考，请以实际编译器版本为准。<br>
            请使用<strong>标准输入输出</strong>。
        </div>

        <hr>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Q: cin/cout 为什么会超时（TLE）？
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>A: cin/cout 因为默认同步 stdin/stdout 而变慢，可以在 <code>main</code> 函数开头加入：</p>
                        <pre class="bg-dark text-light p-2 rounded">ios::sync_with_stdio(false);
cin.tie(0);</pre>
                        <p class="text-body-secondary small mb-0">另外，请使用 <code>'\n'</code> 而不是 <code>endl</code>，因为 endl 默认会增加刷新操作，降低效率。</p>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Q: gets 函数没有了吗？
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>A: <code>gets</code> 函数因存在缓冲区溢出漏洞已被删除，请使用 <code>fgets</code> 取代：</p>
                        <pre class="bg-dark text-light p-2 rounded">#define gets(S) fgets(S, sizeof(S), stdin)</pre>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Q: 为什么我的代码在本地正常，提交后被判错？
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>A: 不要使用 <code>rewind</code> 来清空输入缓冲。OJ 的输入本质是文件，与键盘输入逻辑不一样。如果发现所有人都不能正确提交该题，请联系管理员 <?php echo isset($OJ_ADMIN) ? $OJ_ADMIN : ''?> 反馈。
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h5><i class="bi bi-person"></i> 个人资料</h5>
        <p>本站不提供头像存储服务，而是使用 QQ 头像显示。请使用 <strong>QQ 邮箱</strong>注册，系统自动取用您在 QQ 的头像。</p>

        <hr>

        <h5><i class="bi bi-check-circle"></i> 返回结果说明</h5>
        <div class="list-group mb-3">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>等待评测</span><span class="badge bg-secondary">Pending</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>正在评测</span><span class="badge bg-info">Judging</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>编译错误</span><span class="badge bg-secondary">CE</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>答案正确</span><span class="badge bg-success">AC</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>格式错误</span><span class="badge bg-warning text-dark">PE</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>答案错误</span><span class="badge bg-danger">WA</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>运行超时</span><span class="badge bg-danger">TLE</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>内存超限</span><span class="badge bg-danger">MLE</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>运行错误</span><span class="badge bg-danger">RE</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>输出超限</span><span class="badge bg-danger">OLE</span>
            </div>
        </div>

        <hr>

        <h5><i class="bi bi-code-square"></i> 程序样例（A+B）</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card bg-dark text-light">
                    <div class="card-header py-1"><small>C (gcc)</small></div>
                    <div class="card-body p-2">
                        <pre class="mb-0 small"><code>#include &lt;stdio.h&gt;
int main(){
    int a, b;
    while(scanf("%d %d",&amp;a,&amp;b)!=EOF)
        printf("%d\n",a+b);
    return 0;
}</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark text-light">
                    <div class="card-header py-1"><small>C++ (g++)</small></div>
                    <div class="card-body p-2">
                        <pre class="mb-0 small"><code>#include &lt;iostream&gt;
using namespace std;
int main(){
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int a,b;
    while(cin&gt;&gt;a&gt;&gt;b)
        cout&lt;&lt;a+b&lt;&lt;'\n';
    return 0;
}</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark text-light">
                    <div class="card-header py-1"><small>Python3</small></div>
                    <div class="card-body p-2">
                        <pre class="mb-0 small"><code>import sys
for line in sys.stdin:
    a,b=map(int,line.split())
    print(a+b)</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark text-light">
                    <div class="card-header py-1"><small>Java</small></div>
                    <div class="card-body p-2">
                        <pre class="mb-0 small"><code>import java.util.*;
public class Main{
    public static void main(String[] args){
        Scanner sc=new Scanner(System.in);
        while(sc.hasNextInt()){
            int a=sc.nextInt(),b=sc.nextInt();
            System.out.println(a+b);
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
