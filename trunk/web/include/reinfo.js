function reinfo(){
	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;")
			.replace(/'/g, "&#39;");
	}
	var pats=new Array();
	var exps=new Array();
	pats[0]=/A Not allowed system call.* /;
	exps[0]="使用了系统禁止的操作系统调用，看看是否越权访问了文件或进程等资源。<br>如果你是管理员，确认答案无误，或者是在增加新的语言支持<a href='https://zhuanlan.zhihu.com/p/24498599'>点击这里。</a>";
	pats[1]=/Segmentation fault/;
	exps[1]="段错误，检查是否有数组越界，指针异常，访问到不应该访问的内存区域";
	pats[2]=/Floating point exception/;
	exps[2]="浮点错误，检查是否有除以零的情况";
	pats[3]=/buffer overflow detected/;
	exps[3]="缓冲区溢出，检查是否有字符串长度超出数组的情况";
	pats[4]=/Killed/;
	exps[4]="进程因为内存或时间原因被杀死，检查是否有死循环";
	pats[5]=/Alarm clock/;
	exps[5]="进程因为时间原因被杀死，检查是否有死循环，本错误等价于超时TLE";
	pats[6]=/CALLID:20/;
	exps[6]="可能存在数组越界，检查题目描述的数据量与所申请数组大小关系";
	pats[7]=/NoSuchElementException/;
	exps[7]="可能对输入数据的格式理解有误，输入的数据类型和数量与预期不符";
	pats[8]=/ArrayIndexOutOfBoundsException/;
        exps[8]="数组下标越界，请检查循环变量的上下界范围是否合适，对于特殊值可能需要特殊处理";
	pats[9]=/NoClassDefFoundError: Main/;
        exps[9]="Java语言的提交，主类public class必须是Main";

	//alert("asdf");
	var errmsg=$("#errtxt").text();
	var expmsg="辅助解释：<br><hr>";
	for(var i=0;i<pats.length;i++){
	var pat=pats[i];
	var exp=exps[i];
	var ret=pat.exec(errmsg);
	if(ret){
	expmsg+=escapeHtml(ret)+":"+exp+"<br><hr />";
	}
	}
	document.getElementById("errexp").innerHTML=expmsg;
	//alert(expmsg);


}
