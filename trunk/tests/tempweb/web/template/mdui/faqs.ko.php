<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>
        <?php echo $OJ_NAME?>
    </title>
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
                <font size="+3">
                    <?php echo $OJ_NAME?> FAQ</font>
            </center>
            <hr>

                <font color=green>Q</font>:이 채점시스템에서 사용하는 코드 컴파일 옵션은?<br>
                <font color=red>A</font>:이 채점시스템은 <a href="http://www.debian.org/">Debian Linux</a>에서 운영됩니다. C/C++ 코드는 <a href="http://gcc.gnu.org/">GNU GCC/G++</a> 컴파일러, Pascal 코드는 <a href="http://www.freepascal.org">Free Pascal</a> 컴파일러, Java 코드는 <a href="http://openjdk.java.net">openjdk-7-jdk</a> 컴파일러를 사용합니다. 각 언어별 코드 컴파일 옵션은 다음과 같습니다.:<br>
            <br>

            <table border="1">
                <tr>
                    <td>C:</td>
                    <td>
                        <font color=blue>gcc Main.c -o Main -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE</font>
                    </td>
                </tr>
                <tr>
                    <td>C++:</td>
                    <td>
                        <font color=blue>g++ Main.cc -o Main -fno-asm -Wall -lm --static -std=c++11 -DONLINE_JUDGE</font>
                    </td>
                </tr>
                <tr>
                    <td>Pascal:</td>
                    <td>
                        <font color=blue>fpc Main.pas -oMain -O1 -Co -Cr -Ct -Ci </font>
                    </td>
                </tr>
                <tr>
                    <td>Java:</td>
                    <td>
                        <font color="blue">javac -J-Xms32m -J-Xmx256m Main.java</font>
                        <br>
                        <font size="-1" color="red">*Java 코드를 실행하고 채점하는 경우 +2초, +512MB 가 추가됩니다.</font>
                    </td>
                </tr>
            </table>

            <br>- 컴파일러 버전은 다음과 같습니다.:<br>
                <font color=blue>gcc (Ubuntu/Linaro 4.4.4-14ubuntu5) 4.4.5</font><br>
                <font color=blue>glibc 2.3.6</font><br>
                <font color=blue>Free Pascal Compiler version 2.4.0-2 [2010/03/06] for i386<br> java version "1.7"<br>
                </font>

            <hr>
            <font color=green>Q</font>:코드 작성시 데이터 입출력은 어떻게 하나요?<br>
            <font color=red>A</font>:stdin('표준입력')에서 읽고, stdout('표준출력')으로 출력해야합니다. 예를 들어, C언어에서는 'scanf', C++ 언어에서는 'cin' 을 이용해서 stdin(입력)을 읽어들입니다. 또한, C언어에서는 'printf', C++언어에서는 'cout'을 이용해 stdout(출력)으로 출력할 수 있습니다.<br> 파일 입출력을 사용한 코드를 제출하는 경우에는 "<font color=green>Runtime Error</font>(실행 중 에러)" 메시지를 받게 됩니다.<br><br> C++ 입출력 예시:<br>
            <pre><font color="blue">
#include &lt;iostream&gt;
using namespace std;
int main(){
    int a,b;
    while(cin >> a >> b)
        cout << a+b << endl;
    return 0;
}
</font></pre> C 입출력 예시:<br>
            <pre><font color="blue">
#include &lt;stdio.h&gt;
int main(){
    int a,b;
    while(scanf("%d %d",&amp;a, &amp;b) != EOF)
        printf("%d\n",a+b);
    return 0;
}
</font></pre> Pascal 입출력 예시:<br>
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
</font></pre> Java 입출력 예시:<br>
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

            <hr>
            <font color=green>Q</font>:컴파일 에러는 언제 발생하나요?!<br>
            <font color=red>A</font>:GNU 와 MS-VC++ 는 다음과 같이 다릅니다.:<br>
            <ul>
                <li>
                    <font color=blue>main</font> 함수의 데이터형은 반드시 <font color=blue>int</font> 이어야 합니다. <font color=blue>void main</font> 으로 작성한 경우 Compile Error 메시지를 받게 됩니다.<br>
                    <li>
                        <font color=green>i</font> 변수가 "<font color=blue>for</font>(<font color=blue>int </font><font color=green>i</font>=0...){...}" 와 같이 선언되어있는 상태인데 for 코드블록 밖에서 참조되는 경우 Compile Error 메시지를 받게 됩니다.<br>
                        <li>
                            <font color=green>itoa</font> 함수는 ANSI 표준 함수가 아닙니다.<br>
                            <li>
                                VC 에서 사용되는 <font color=green>__int64</font> 데이터형도 ANSI 표준이 아닙니다. 따라서, 64비트 정수 데이터형을 위해서는
                                <font color=blue>long long</font> 을 사용해야합니다. <br>MSVC6.0 를 위해서 #define __int64 long long 와 같은 전처리 코드를 사용할 수도 있습니다.
            </ul>
            <hr>
            <font color=green>Q</font>:채점 코드 제출 후 받게 되는 메시지들은 어떤 의미인가요?<br>
            <font color=red>A</font>:채점 코드 제출 후 받게 되는 메시지들의 의미는 다음과 같습니다.:<br>
            <br>
                - <font color=blue>채점 대기중</font> : 코드가 제출되고 채점을 기다리고 있는 상태입니다. 대부분의 경우 조금만 기다리면 채점이 진행됩니다.
            <br>
                - <font color=blue>재채점 대기중</font> : 채점 데이터가 갱신되어 재채점을 기다리고 있는 상태입니다.
            <br>
                - <font color=blue>컴파일중</font> : 제출된 코드를 컴파일 중이라는 의미입니다.
            <br>
                - <font color="blue">채점중</font>: 채점이 진행되고 있는 상태라는 의미입니다.
            <br>
                - <font color=blue>모두 맞음</font> : 모든 채점 데이터에 대해서 정확한 답을 출력했다는 의미입니다.
            <br>
                - <font color=blue>출력형식 다름</font> : 출력된 결과가 문제에서 출력해야하는 출력형식과 다르게 출력되었다는 의미입니다. 문제의 출력형식에서 요구하는 형식과 똑같아야 합니다. 답 출력 후 출력형식에는 없는 공백문자나 줄 바꿈이 더 출력되지는 않았는지 확인해 보아야 합니다.
            <br>
                - <font color=blue>틀림</font> : 틀린 답을 출력한 것을 의미합니다. 채점 시스템에 등록하는 채점 데이터들은 외부로 공개하지 않는 것이 일반적입니다. 제출한 코드가 틀린 답을 출력하는 경우가 어떤 경우일지 더 생각해 보아야 합니다.
            <br>
                - <font color=blue>시간제한 초과</font> : 제한시간 이내에 답을 출력하지 못했다는 것을 의미합니다. 좀 더 빠르면서도 정확한 결과를 출력하도록 소스 코드를 수정해야합니다.
            <br>
                - <font color=blue>메모리제한 초과</font> : 제출한 프로그램이 제한된 메모리용량보다 더 많은 기억공간을 사용했다는 것을 의미합니다. 일반적으로는 메모리를 더 적게 사용하는 코드로 수정해야합니다.
            <br>
                - <font color=blue>출력제한 초과</font>: 제출한 프로그램이 제한된 출력량 이상으로 결과를 출력했다는 것을 의미합니다. 대부분의 경우 무한 반복 실행 구조에 의해 발생합니다. 채점 시스템의 출력 제한 바이트 수는 1M bytes 입니다.
            <br>
                - <font color=blue>실행중 에러</font> : 제출한 프로그램이 실행되는 도중에 오류가 발생했다는 것을 의미합니다. 예를 들어, 'segmentation fault(허용되지 않는 메모리 영역에 접근하는 경우: 배열 인덱스 초과 등)','floating point exception(실수 계산 예외: 0 으로 나누는 등)','used forbidden functions(제한된 함수를 사용한 경우: 파일 처리 함수 등이 사용된 경우 등)', 'tried to access forbidden memories(허용되지 않는 시스템 메모리 영역 등에 접근하는 경우 등)' 등에 의해 발생합니다.
            <br>
                - <font color=blue>컴파일 에러</font> : 제출한 소스코드를 ANSI 표준(gcc/g++/gpc) 컴파일러로 컴파일하지 못했다는 것을 의미합니다. 컴파일 오류 메시지가 아닌 오류 경고(warning)는 이 메시지를 출력하지 않습니다. 메시지 부분을 누르면 컴파일 오류 메시지를 확인할 수도 있습니다.<br>

            <hr>
            <font color=green>Q</font>: 온라인 대회(Online Contests)는 어떻게 참가하나요?<br>
            <font color=red>A</font>: 먼저 <a href=registerpage.php>회원가입</a> 을 통해 계정을 만들어야 온라인 대회에 참여할 수 있게 됩니다.<br>
            <br>
            <hr>
            
            <center>
                <font color=green size="+2">궁금한 내용이나 건의사항은 <a href="bbs.php"><?php echo $MSG_BBS?></a>를 사용해 도움 받을 수도 있습니다.
                </font>
            </center>
            <hr>
            <center>
                <table width=100% border=0>
                    <tr>
                        <td align=right width=65%>
                            <a href="index.php">
                                <font color=red><?php echo $OJ_NAME?></font>
                                <font color=red>20.09.15</font>
                            </a>
                        </td>
                    </tr>
                </table>
            </center>
        </div>

    </div>
    <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>

    </script>
</body>
</html>
