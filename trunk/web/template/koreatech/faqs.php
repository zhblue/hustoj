<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php
  $navigation_tab = "faqs";
  require_once("oj-header.php");
?>
<div class="container">
<p>
  <font color=green>Q</font> 온라인 채점의 실행 환경, 컴파일러와 옵션은 무엇인가요?<br>
  <font color=red>A</font> 온라인 채점은 <a href="http://www.ubuntu.com/">Ubuntu Linux</a> 에서 돌고 있습니다. C/C++로는 <a href="http://gcc.gnu.org/">GNU GCC/G++</a> 를 사용하고 있고, 자바는 <a href="http://www.oracle.com/technetwork/java/index.html">sun-java-jdk1.8</a> 를 사용합니다. 자세한 컴파일 옵션은 아래와 같습니다.<br>
  <dl class="dl-horizontal">
    <dt>C</dt>
    <dd>gcc Main.c -o Main -fno-asm -O2 -Wall -lm --static -std=c99 -DONLINE_JUDGE</dd>
    <dt>C++</dt>
    <dd>g++ Main.cc -o Main -fno-asm -O2 -Wall -lm --static -std=c++11 -DONLINE_JUDGE</dd>
    <dt>Java</dt>
    <dd>javac -J-Xms32m -J-Xmx256m Main.java<br>
     <small>자바는 JVM구동 시간 때문에 제한시간에 2초가 더 주어지고, 메모리는 512M가 더 주어집니다.</small>
    </dd>
  </dl>
  구체적인 컴파일러 버전<br>
  <ul>
    <li>gcc (Ubuntu 4.8.2-19ubuntu1) 4.8.2</li>
    <li>glibc 2.19</li>
    <li>java version "1.8.0_25"</li>
  </ul>
</p>
<hr>
<p>
<font color=green>Q</font> 입력과 출력은 어떻게 해야 하나요?<br>
<font color=red>A</font> 제출할 프로그램은 표준 입력(<mark>stdin</mark>)으로 읽고, 표준 출력(<mark>stdout</mark>)으로 출력을 해야 합니다.<br>
제출한 코드에서 어떠한 파일도 읽거나 쓰는건 금지되어 있으므로, 시도할 경우 <mark>Runtime Error</mark> 를 받게 됩니다.

<br><br>
1000번 문제를 C++로 푼 예제<br>
<pre><font color="blue">#include &lt;iostream&gt;
using namespace std;
int main(){
    int a,b;
    while(cin >> a >> b)
        cout << a+b << endl;
  return 0;
}
</font></pre>
1000번 문제를 C로 푼 예제<br>
<pre><font color="blue">#include &lt;stdio.h&gt;
int main(){
    int a,b;
    while(scanf("%d %d",&amp;a, &amp;b) != EOF)
        printf("%d\n",a+b);
  return 0;
}
</font></pre>
1000번 문제를 Java로 푼 예제<br>
<pre><font color="blue">import java.util.*;
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
</p>
<hr>
<p>
<font color=green>Q</font> 왜 컴파일 에러가 발생했나요? 전 잘 된단 말이예요!<br>
<font color=red>A</font> GNU C/C++과 Visual Studio C/C++과 몇가지 차이점이 있습니다.<br>
<ul>
  <li><code>main</code> 은 항상 <code>int</code>를 반환해야 합니다. <code>void main</code> 는 컴파일 에러가 발생합니다.</li>
  <li><code>for</code>문 안에 선언된 변수는 <code>for</code> 밖에서는 참조할 수 없습니다.<br>
<pre>for (int i=0...) {
...
}
i = 1 // 안됩니다
</pre>
  </li>
  <li><code>itoa</code> 는 ASNI표준 함수가 아닙니다.</li>
  <li><code>__int64</code> 는 ANSI 표준이 아닙니다. 대신 <code>long long</code>으로 64bit 정수를 표현할 수 있습니다.<br>Visual Studio 6.0에서는 <code>#define __int64 long long</code> 을 선언해서 사용하세요.</li>
</ul>
</p>
<hr>
<p>
<font color=green>Q</font> 채점 시스템의 결과에 대한 설명 부탁드립니다.<br>
<font color=red>A</font> 채점 시스템은 몇가지 결과를 내놓는데, 아래와 같습니다.<br>
<dl class="dl-horizontal">
  <dt><?php echo $MSG_Pending;?></dt>
  <dd>채점 시스템이 바쁩니다. 조금만 기다리시면 채점됩니다.</dd>
  <dt><?php echo $MSG_Pending_Rejudging;?></dt>
  <dd>채점 데이터가 변경되어서 재 채점 대기중 입니다.</dd>
  <dt><?php echo $MSG_Compiling;?></dt>
  <dd>채점 시스템이 컴파일 중 입니다.</dd>
  <dt><?php echo $MSG_Running_Judging;?></dt>
  <dd>제출된 코드가 실행 중이며, 시스템이 채점을 하고 있습니다.</dd>
  <dt><?php echo $MSG_Accepted;?></dt>
  <dd>축하합니다! 정답입니다.</dd>
  <dt><?php echo $MSG_Presentation_Error;?></dt>
  <dd>출력이 채점 데이터와 다른 부분이 있습니다. 대게 공백(space), 개행 등의 출력 조건이 맞지 않는 경우니 다시 점검해 주세요.</dd>
  <dt><?php echo $MSG_Wrong_Answer;?></dt>
  <dd>아쉽네요. 틀렸습니다. (체점 데이터는 공개되지 않습니다, 실제 대회도 이래요)</dd>
  <dt><?php echo $MSG_Time_Limit_Exceed;?></dt>
  <dd>실행하는데 너무 오래 걸립니다. 시간 복잡도를 다시 확인해 보세요.</dd>
  <dt><?php echo $MSG_Memory_Limit_Exceed;?></dt>
  <dd>너무 많은 메모리를 사용했습니다. 문제에 제시된 메모리 제한을 확인해 주세요.</dd>
  <dt><?php echo $MSG_Output_Limit_Exceed;?></dt>
  <dd>너무 많은 내용을 출력했습니다. 현재 출력 제한은 1MB입니다.</dd>
  <dt><?php echo $MSG_Runtime_Error;?></dt>
  <dd>그 외는 모두 <?php echo $MSG_Runtime_Error;?>를 받게 됩니다. 'segmentation fault', 'floating point exception', 'used forbidden functions', 'tried to access forbidden memories' 등에 해당합니다.</dd>
  <dt><?php echo $MSG_Compile_Error;?></dt>
  <dd>컴파일에 실패 했습니다. (물론 경고를 오류로 취급하진 않습니다) 이 메시지는 클릭하면 구체적인 컴파일 에러가 안내됩니다.</bb>
</dl>
</p>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
