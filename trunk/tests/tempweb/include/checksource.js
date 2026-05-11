function checkIsChinese(str){  
	 //如果值为空，通过校验  
	 if   (str   ==   "")  
	         return   false;  
	 var   pattern   =   /([\u4E00-\u9FA5]|[\uFE30-\uFFA0])+/gi;  
	 if   (pattern.test(str))  
	         return   true;  
	 else  
	         return   false;  
}
function checksource(src){
        if (document.getElementById("language").value>"3")
            return true;
		var keys=new Array();
		var errs=new Array();
		var msg="";
		keys[0]="void main";
		errs[0]="main函数返回值不能为void,否则会编译出错,请使用int main()，并在最后return 0。\n虽然VC等windows下的编译器支持,C/C++标准中不允许使用void main()!!!";
   		if (document.getElementById("language").value=="3"){
		     	keys[0]="int main";
	        	errs[0]="java要求有public static void main函数";
      		}
		keys[1]="Please";
		errs[1]="除非题目要求，否则不要使用类似‘Please input’这样的提示";		
		keys[2]="请";
		errs[2]="除非题目要求，否则不要使用类似‘请输入’这样的提示";		
		keys[3]="输入";
		errs[3]="除非题目要求，否则不要使用类似‘请输入’这样的提示";		
		keys[3]="input";
		errs[3]="除非题目要求，否则不要使用类似‘Please input’这样的提示";		
		keys[4]="max=%d";
		errs[4]="除非题目要求，否则不要使用类似‘max=’这样的提示";		
		keys[5]="mian";
		errs[5]="是不是把main打成mian了？";	
		for(var i=0;i<keys.length;i++){
			if(src.indexOf(keys[i])!=-1){
				msg+=errs[i]+"\n";
			}
		}
		if(checkIsChinese(src))
			msg+="程序中有中文字符！注意，一般来说本系统中的题目都不会要求输出提示，特别是中文提示。\n请先使用SampleInput做输入，对比输出和SampleOutput，有任何多余的输出（包括提示、多出的逗号、等号空格等等）都会被判错误！\n如有任何程序内容出现中文的括号、分号、引号、空格都会编译出错。";
		if(msg.length>0)
			return confirm(msg+"\n 代码可能有错误，确定要提交么？\n建议先使用题目的SampleInput做测试，看看你的程序输出是否与SampleOutput完全一致。\n多个空格，标点都会被认为是错误答案（WrongAnswer）。\n如果出现编译错误（CompileError），请点击CompileError字样，查看具体编译报错，以便纠正。");					
		else
			return true;
}
