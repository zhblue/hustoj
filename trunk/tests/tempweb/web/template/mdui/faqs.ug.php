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
  <font size="+3"> بۇ سىستېما ھەققىدە</font>
</center>
<hr>
<a href="<?php echo $path_fix?>./template/jiugui/download/index.html"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> <?php echo $MSG_EXPORT?></a>
<p align="right"><b>بۇ كود تەكشۈرۈش سۇپىسى قانداق تەرجىمىتېر ۋە تەرجىمە ئۇسلۇبلىرىنى قوللىنىدۇ ؟:<font color=green>Q</font><br>
  مەشغۇلات سىستىمىسىدا يۈگۈرەيدۇ<a href="http://www.ubuntu.com">Ubuntu</a>Linux بۇ سۇپا:<font color=red >A</font> <br>
 دەل بۇسۇپىنىڭ س\س پلۇس-پلۇس تىل پروگرامما تەرجىمە مۇھىتى <a href="http://gcc.gnu.org/">            GNU GCC/G++</a><br>
 تىلىنىڭ تەرجىمە مۇھىتىpascal<a href="http://www.freepascal.org">            Free Pascal</a><br>
 تىلىنىڭ تەرجىمە مۇھىتى ھېسابلىنىدۇ.Java<a href="http://openjdk.java.net/" align="right">       openjdk-7</a><br>
</p>
<table border="1">
  <tr>
    <td>C:</td>
    <td><font color=blue>gcc Main.c -o Main  -fno-asm -Wall -lm --static -std=c99 -DONLINE_JUDGE</font>
        <pre>#pragma GCC optimize ("O2")</pre> بۇ ئارقىلىق قولدا كرگۈزۈش چىقىرىش ئەلالاشتۇرۇشقىمۇ بولىدۇ
      </td>
  </tr>
  <tr>
    <td>C++:</td>
    <td><font color=blue>g++ -fno-asm -Wall -lm --static -std=c++11 -DONLINE_JUDGE -o Main Main.cc</font></td>
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
<p align="right"><b> :سىستىما تىل تەرجىمىتېر نەشىر نۇسخا پايدىلىنىش كۆرسەتكۈچى<br>
  <font color=blue>gcc version 4.8.4 (Ubuntu 4.8.4-2ubuntu1~14.04.3)</font><br>
  <font color=blue>glibc 2.19</font><br>
<font color=blue>Free Pascal Compiler version 2.6.2<br>
openjdk 1.7.0_151<br>
</font></p>
<hr>
<p align="right"><b> پروگىرامما قانداق يېزىش چىقىرىش ئىلىپ بارىدۇ ؟ قانداق پىرىنسىپقا بويسىنىدۇ ؟:<font color=green>Q</font><br>
  ئۆلچەملىك كىرگۈزۈش ئارقىلىق ئۇچۇرنى ئوقۇيدۇ stdin('Standard Input') :<font color=red>A</font><br>
ئۆلچەملىك يېزىش ئارقىلىق ئۇچۇرنى چىقىرىدۇ. stdout('Standard Output') <br>
 نى ئىشلەتسىڭىز بولىدۇ.'scanf' تىلىدا C,نى ئىشلەتسىڭىز 'cin' تىلىدا C++<br>
 نى ئىشلەتسىڭىز بولىدۇ.'printf' تىلىدا C,نى ئىشلەتسىڭىز 'cout' تىلىدا C++<br>
باشقا تىللاردا شۇ تىلنىڭ ئۆلچەملىك ئوقۇش-يېزىشنى ئىشلەتسىڭىز بولىدۇ
</p>
<p align="right"><b>ئەگەر ئۆلچەملىك ئوقۇش-يېزىشنى ئىشلەتمەي،ھۆججەت ئوقۇش-يېزىشنى ئىشلەتسىڭىز بۇ سۇپا يۈرۈش خاتا دەپ نەتىجە چىقىرىپ بىرىدۇ <br>
<font color=green>Runtime Error</font>
  <br>
تۆۋەندە 1000 سوئالنىڭ ئۆلچەملىك كودى بىرىلىدۇ</p>
<p> C++:<br>
</p>
<pre><font color="blue">
#include &lt;iostream&gt;
using namespace std;
int main(){
    int a,b;
    cin >> a >> b;
    cout << a+b << endl;
    return 0;
}
</font></pre>
C:<br>
<pre><font color="blue">
#include &lt;stdio.h&gt;
int main(){
    int a,b;
    scanf("%d %d",&amp;a, &amp;b);
    printf("%d\n",a+b);
    return 0;
}
</font></pre>

<p><b>تۆۋەندىكىلىرى  كۆپ كىرگۈزۈش ئەھۋاللىرىنىڭ كودى،1000 سوئالنىڭ كودى ئەمەس <b> </p>
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

<hr >
<b>
<p align="right">
<b>
نىمىشقا كومپۇتېرىمدا مۇۋاپپىقىيەتلىك يۈرگەن كود ب سۇپىدا ئۆتەلمەيدۇ ؟<font color=green>Q</font><br>
نىڭ ئۇسلۇبىغا تېخىمۇ بەك ئوخشايدۇc/c++ گە ئوخشىمايدۇ،VC6 نىڭ تەرجىمە ئۇسلۇبى GCC:<font color=red>A</font><br>
</p>
<ul align="right">
  <li>دەپ ئېنىقلىما بىرىلگەن  ئاساس فۇنكىسىيە ئۆتەلمەيدۇ <font color=blue>void main</font>  تىپدا بولىشى زۆرۈر <font color=blue>int</font> فۇنكىسىيە قايىتقان قىممەت چوقۇم <font color=blue>main</font><br> 
  <li >سىرتىغا چىكىتىپ قالغان بولۇشى مۇمكىن "<font color=blue>for</font>(<font color=blue>int</font> <font color=green>i</font>=0...){...}" <font color=green>i</font> ئۆزگەرگۈچى مىقدار<br>
  <li>ئۆلچەملىك  فۇنكىسىيە ئەمەس،خالىغانچە ئىشلەتمەڭ<font color=green>itoa</font><br>
  <li>نى ئىشلىتىشكە بولىدۇ،ياكى تۆۋەندىكىدەك ئىشلىتىشكىمۇ بولىدۇ  <font color=blue>long long</font>دىلا ئىشلىتىشكە بولىدۇ،ئەمماVCئۆلچەملىك ئەمەس،پەقەت <font color=green>__int64</font><br>
#define __int64 long long<br>
 نى ئىشلىتىڭ%lldدە  scanf،printf
</ul>
<hr>
<p align="right">
<b>
سىستىما چىقارغان نەتىجىلەر نىمە مەنىدە؟：<font color=green>Q</font><br>
 تەپسىلانى تۆۋىندىكىچىە ：<font color=red>A</font><br>
</p>
<p align="right"><b>سىستىما ئالدىراش،ئۆچرەتتە كۈتۈۋاتىدۇ :<font color=blue>Pending</font></p>
<p align="right"><b>سان-سىپىر سەۋەپلىك سىستىما قايتا تەكشۈرۈشتە:<font color=blue>Pending Rejudge</font></p>
<p align="right"><b>تەرجىمە قىلىنىۋاتىدۇ:<font color=blue>Compiling</font><br>
</p>
<p align="right"><b>!كود يۈرۈۋاتىدۇ ھەم تەكشۈرۈشتە:<font color="blue">Running &amp; Judging</font><br>
</p>
<p align="right"><b>!مۇبارەك بولسۇن ،كودىڭىز بارلىق سىناق نۇقتىلىرىدىن مۇۋاپپىقىيەتلىك ئۆتتى:<font color=blue>Accepted</font><br>
  <br>
  جاۋاب توغرا ، ئەمما لىكىن فورمات چىقىرىشتا مەسىلە كۆرۈلدى:<font color=blue>Presentation Error</font><br>
  <br>:<font color=blue>Wrong Answer</font><br>
  جاۋابىڭىز خاتا!!! ئۈلگە كىرگۈزۈش چىقىرىشتىنلا ئۆتكەن كود توغرا كود بولۇشى ناتايىن،تېخىمۇ ئىنچىكە ئويلىنىپ بېقىڭ،ئارقا سۇپىدا يەنە باشقا كىرگۈزۈش چىقىرىشلار بار
  <br>
  كودىڭىز بەلگىلەنگەن چەكلىك ۋاقىت ئىچىدە بارلىق سىناق نۇقتىلىرىدىن ئۆتۈپ بولالمىدى:<font color=blue>Time Limit Exceeded</font><br>
  <br>
  كودىڭىز چەكلىمىدىن يۇقرى ئىچكى ساقلىغۇچ سەرىپ قىلدى:<font color=blue>Memory Limit Exceeded</font><br>
  <br>
  كودىڭىز بەك كۆپ ئۇچۇر چىقاردى،تەپسىلى كۆرۈپ بېقىڭ ،ئادەتتە چىقىرىش 1 مىگابايىتتىن ئاشمايدۇ:<font color=blue>Output Limit Exceeded</font><br>
  <br>
  :<font color=blue>Runtime Error</font><br>
يۈرۈش خاتا.گەرچە كودىڭىز توغرا تەرجىمە بولۇنسىمۇ،يۈرۈش جەريانىدا خاتالىق كۆرۈلدى.سەۋەبى بەلكىم تۆۋەندىكىچە:قائىدىگە ئىخلاپ ساقلىغۇچ زىيارىتى ياكى ئىندىكىس،چەكلەنگەن فۇنكىسىيەنى ئىشلىتىش،پويىنتېر ئۇچۇپ يۈرۈش
</p>
<p align="right"><b>  
كودىڭىز تەرجىمىدە مەغلۇپ بولدى.كودىڭىزدا ئېغىر دەرجىدە تىل خاتالىق بار،بۇيەرنى چىكىپ تەپسىلاتىنى كۆرۈڭ:<font color=blue>Compile Error</font><br>
  <br>
</p>
<hr>
<br>
<hr>
<center>
  <font color=green size="+2"><a href="bbs.php"><?php echo $OJ_NAME?>论坛系统</a>باشقا كۆپ تەپسىلاتلارنى بۇ يەردىن كۆرۈڭ</font>
</center>
<hr>
<center>
  <table width=100% border=0>
    <tr>
      <td align=right width=65%>
      <a href = "index.php"><font color=red><?php echo $OJ_NAME?></font></a> 
      <a href = "https://github.com/zhblue/hustoj"><font color=red>17.12.01</font></a></td>
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
