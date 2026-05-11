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
			<p>
				<font color=green>Q</font>:What is the compiler the judge is using and what are the compiler options?<br>
				<font color=red>A</font>:The online judge system is running on <a href="http://www.debian.org/">Debian Linux</a>. We are using <a href="http://gcc.gnu.org/">GNU GCC/G++</a> for C/C++ compile,
				<a href="http://www.freepascal.org">Free Pascal</a> for pascal compile and
				<a href="http://openjdk.java.net">openjdk-7-jdk</a> for Java. The compile options are:<br>
			</p>
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
						<font size="-1" color="red">*Java has 2 more seconds and 512M more memory when running and judging.</font>
					</td>
				</tr>
			</table>
			<p> Our compiler software version:<br>
				<font color=blue>Gcc version 9.4.0 (Ubuntu 9.4.0-1ubuntu1~20.04.1)</font><br>
				<font color=blue>Glibc 2.31-0ubuntu9.2</font><br>
				<font color=blue>Free Pascal Compiler version 3.0.4+dfsg-23 [2019/11/25] for x86_64</font><br>
				<font color=blue>Openjdk "17.0.4+8-1~20.04"</font><br>
				<font color=blue>Python 3.8.5</font><br>
			</p>
			<hr>
			<font color=green>Q</font>:Where is the input and the output?<br>
			<font color=red>A</font>:Your program shall read input from stdin('Standard Input') and write output to stdout('Standard Output').For example,you can use 'scanf' in C or 'cin' in C++ to read from stdin,and use 'printf' in C or 'cout' in C++ to write to stdout.<br> User programs are not allowed to open and read from/write to files, you will get a "
			<font color=green>Runtime Error</font>" if you try to do so.<br><br> Here is a sample solution for problem 1000 using C++:<br>
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
</font></pre> Here is a sample solution for problem 1000 using C:<br>
			<pre><font color="blue">
#include &lt;stdio.h&gt;
int main(){
    int a,b;
    while(scanf("%d %d",&amp;a, &amp;b) != EOF)
        printf("%d\n",a+b);
    return 0;
}
</font></pre> Here is a sample solution for problem 1000 using PASCAL:<br>
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
			<br><br> Here is a sample solution for problem 1000 using Java:<br>
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
			<font color=green>Q</font>:Why did I get a Compile Error? It's well done!<br>
			<font color=red>A</font>:There are some differences between GNU and MS-VC++, such as:<br>
			<ul>
				<li>
					<font color=blue>main</font> must be declared as
					<font color=blue>int</font>,
					<font color=blue>void main</font> will end up with a Compile Error.<br>
					<li>
						<font color=green>i</font> is out of definition after block "
						<font color=blue>for</font>(
						<font color=blue>int</font>
						<font color=green>i</font>=0...){...}"<br>
						<li>
							<font color=green>itoa</font> is not an ANSI function.<br>
							<li>
								<font color=green>__int64</font> of VC is not ANSI, but you can use
								<font color=blue>long long</font> for 64-bit integer.<br>try use #define __int64 long long when submit codes from MSVC6.0
			</ul>
			<hr>
			<font color=green>Q</font>:What is the meaning of the judge's reply XXXXX?<br>
			<font color=red>A</font>:Here is a list of the judge's replies and their meaning:<br>
			<p>
				<font color=blue>Pending</font> : The judge is so busy that it can't judge your submit at the moment, usually you just need to wait a minute and your submit will be judged. </p>
			<p>
				<font color=blue>Pending Rejudge</font>: The test datas has been updated, and the submit will be judged again and all of these submission was waiting for the Rejudge.</p>
			<p>
				<font color=blue>Compiling</font> : The judge is compiling your source code.<br>
			</p>
			<p>
				<font color="blue">Running &amp; Judging</font>: Your code is running and being judging by our Online Judge.<br>
			</p>
			<p>
				<font color=blue>Accepted</font> : OK! Your program is correct!.<br>
				<br>
				<font color=blue>Presentation Error</font> : Your output format is not exactly the same as the judge's output, although your answer to the problem is correct. Check your output for spaces, blank lines,etc against the problem output specification.<br>
				<br>
				<font color=blue>Wrong Answer</font> : Correct solution not reached for the inputs. The inputs and outputs that we use to test the programs are not public (it is recomendable to get accustomed to a true contest dynamic ;-).<br>
				<br>
				<font color=blue>Time Limit Exceeded</font> : Your program tried to run during too much time.<br>
				<br>
				<font color=blue>Memory Limit Exceeded</font> : Your program tried to use more memory than the judge default settings. <br>
				<br>
				<font color=blue>Output Limit Exceeded</font>: Your program tried to write too much information. This usually occurs if it goes into a infinite loop. Currently the output limit is 1M bytes.<br>
				<br>
				<font color=blue>Runtime Error</font> : All the other Error on the running phrase will get Runtime Error, such as 'segmentation fault','floating point exception','used forbidden functions', 'tried to access forbidden memories' and so on.<br>
			</p>
			<p>
				<font color=blue>Compile Error</font> : The compiler (gcc/g++/gpc) could not compile your ANSI program. Of course, warning messages are not error messages. Click the link at the judge reply to see the actual error message.<br>
				<br>
			</p>
			<hr>
			<font color=green>Q</font>:How to attend Online Contests?<br>
			<font color=red>A</font>:Can you submit programs for any practice problems on this Online Judge? If you can, then that is the account you use in an online contest. If you can't, then please <a href=registerpage.php>register</a> an id with password first.<br>
			<br>
			<hr>
			<center>
				<font color=green size="+2">Any questions/suggestions please post to <a href="bbs.php"><?php echo $MSG_BBS?></a>
				</font>
			</center>
			<hr>
			this page can be replaced by add a news which titled "<?php echo htmlentities($faqs_name,ENT_QUOTES,"UTF-8")?>";
			<hr>
			
			<center>
				<table width=100% border=0>
					<tr>
						<td align=right width=65%>
							<a href="index.php">
								<font color=red>HUSTOJ</font>
							</a>
							<a href="https://github.com/zhblue/hustoj">
								<font color=red>2020.01.22</font>
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
