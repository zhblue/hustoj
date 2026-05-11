<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>faqs - <?php echo $OJ_NAME;?></title>
        <?php require("./template/bshark/header-files.php");?>
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
        <?php require("./template/bshark/nav.php");?>
        <div class="card" style="margin: 3% 8% 5% 8%">
  <div class="card-body">
    <h4>FAQS</h4>
    <div class="faqs-card">
    <p>Q:What is the compiler the judge is using and what are the compiler options?<br>
  A:The online judge system is running on <a href="http://www.debian.org/">Debian Linux</a>. We are using <a href="http://gcc.gnu.org/">GNU GCC/G++</a> for C/C++ compile,
				<a href="http://www.freepascal.org">Free Pascal</a> for pascal compile and
				<a href="http://openjdk.java.net">openjdk-7-jdk</a> for Java. The compile options are:<br>
</p>
<table class="table table-hover">
  <tr>
    <td>C:</td>
    <td>gcc Main.c -o Main  -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE
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
    <td><font color="blue">javac -J-Xms32m -J-Xmx256m Main.java
    <br>
    <font size="-1" color="red">*Java has 2 more seconds and 512M more memory when running and judging.
    </td>
  </tr>
</table>
<p>Our compiler software version:<br>
<table class="table table-hover">
  <tr>
    <td>gcc</td>
    <td>gcc version 4.8.4 (Ubuntu 4.8.4-2ubuntu1~14.04.3)
	  </td>
  </tr>
  <tr>
    <td>glibc</td>
    <td>glibc 2.19</td>
  </tr>
  <tr>
    <td>FPC</td>
    <td>Free Pascal Compiler version 2.6.2</td>
  </tr>
  <tr>
    <td>openjdk</td>
    <td>openjdk 1.7.0_151
    </td>
  </tr>
</table>
</p>
</div>
<div class="faqs-card">
<p>Q:Where is the input and the output?<br>
  A:Your program shall read input from stdin('Standard Input') and write output to stdout('Standard Output').For example,you can use 'scanf' in C or 'cin' in C++ to read from stdin,and use 'printf' in C or 'cout' in C++ to write to stdout.<br> User programs are not allowed to open and read from/write to files, you will get a "
			Runtime Error" if you try to do so.<br>
  <br>
Here is a sample solution for problem 1000</p>
<p> C++:<br>
</p>
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
}</code></pre></div>
<div class="faqs-card">
Q:Why did I get a Compile Error? It's well done!<br>
A:There are some differences between GNU and MS-VC++, such as:<br>
					<pre><code class="cpp">main must be declared as int,
void main will end up with a Compile Error.
i is out of definition after block "for(int i=0...){...}"
itoa is not an ANSI function.
__int64 of VC is not ANSI, but you can use long long for 64-bit integer.
try use #define __int64 long long when submit codes from MSVC6.0</code></pre>
</div><div class="faqs-card">
Q:What is the meaning of the judge's reply XXXXX?<br>
A:Here is a list of the judge's replies and their meaning:<br>
<table class="table table-hover">
<tr>
<td><?php echo $MSG_Pending;?></td>
<td>The judge is so busy that it can't judge your submit at the moment, usually you just need to wait a minute and your submit will be judged.</td>
</tr>
<tr>
<td><?php echo $MSG_Pending_Rejudging;?></td>
<td>The test datas has been updated, and the submit will be judged again and all of these submission was waiting for the Rejudge.</td>
</tr>
<tr>
<td><?php echo $MSG_Compiling;?></td>
<td>The judge is compiling your source code.</td>
</tr>
<tr>
<td><?php echo $MSG_Running_Judging;?></td>
<td>Your code is running and being judging by our Online Judge.</td>
</tr>
<tr>
<td><?php echo $MSG_Accepted;?></td>
<td>OK! Your program is correct!.</td>
</tr>
<tr>
<td><?php echo $MSG_Presentation_Error;?></td>
<td>Your output format is not exactly the same as the judge's output, although your answer to the problem is correct. Check your output for spaces, blank lines,etc against the problem output specification. </td>
</tr>
<tr>
<td><?php echo $MSG_Wrong_Answer;?></td>
<td>Correct solution not reached for the inputs. The inputs and outputs that we use to test the programs are not public (it is recomendable to get accustomed to a true contest dynamic ;-).</td>
</tr>
<tr>
<td><?php echo $MSG_Time_Limit_Exceed;?></td>
<td>Your program tried to run during too much time.</td>
</tr>
<tr>
<td><?php echo $MSG_Memory_Limit_Exceed;?></td>
<td>Your program tried to use more memory than the judge default settings.</td>
</tr>
<tr>
<td><?php echo $MSG_Output_Limit_Exceed;?></td>
<td>Your program tried to write too much information. This usually occurs if it goes into a infinite loop. Currently the output limit is 1M bytes.</td>
</tr>
<tr>
<td><?php echo $MSG_Runtime_Error;?></td>
<td>All the other Error on the running phrase will get Runtime Error, such as 'segmentation fault','floating point exception','used forbidden functions', 'tried to access forbidden memories' and so on.</td>
</tr>
<tr>
<td><?php echo $MSG_Compile_Error;?></td>
<td>The compiler (gcc/g++/gpc) could not compile your ANSI program. Of course, warning messages are not error messages. Click the link at the judge reply to see the actual error message.
</td>
</tr>
</table>
</div><div class="faqs-card">
Q:How to attend Online Contests?<br>
A:Can you submit programs for any practice problems on this Online Judge? If you can, then that is the account you use in an online contest. If you can't, then please <a href=registerpage.php>register</a> an id with password first.<br>
</div>
this page can be replaced by add a news which titled "<?php echo htmlentities($faqs_name,ENT_QUOTES,"UTF-8")?>";
    </div>
</div>
<?php require("./template/bshark/footer.php");?>
<?php require("./template/bshark/footer-files.php");?>
    </body>
</html>
