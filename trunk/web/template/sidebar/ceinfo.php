<?php $show_title=$id." - $MSG_COMPILE_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<script src="template/<?php echo $OJ_TEMPLATE?>/js/textFit.min.js"></script>
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shCore.css' rel='stylesheet' type='text/css'/>
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shThemeDefault.css' rel='stylesheet' type='text/css'/>
<div class="padding">
    <div style="margin-top: 0px; margin-bottom: 14px; padding-bottom: 0px; " >
        <p class="transition visible">
           <strong ><?php echo $MSG_SOURCE_CODE ?></strong>
        </p>
        <div class="ui existing segment">
          <pre v-if="escape" style="margin-top: 0; margin-bottom: 0; "><code><div class="brush:c" id='source' name="source"></div></code></pre>
        </div>
    </div>
    <div style="margin-top: 0px; margin-bottom: 14px; " >
        <p class="transition visible">
           <strong ><?php echo $MSG_COMPILE_INFO ?></strong>
        </p>
        <div class="ui existing segment">
          <pre v-if="escape" style="margin-top: 0; margin-bottom: 0; "><code><div id='errtxt'><?php echo $view_reinfo?></div></code></pre>
        </div>
    </div>
    <div style="margin-top: 0px; margin-bottom: 14px; " >
        <p v-if="title" class="transition visible">
           <strong v-html="title"><?php echo $MSG_ERROR_EXPLAIN;?></strong>
        </p>
        <div class="ui existing segment">
          <pre v-if="escape" style="margin-top: 0; margin-bottom: 0; "><code><div id='errexp'></div></code></pre>
        </div>
    </div>
    </div>
<script>
var i=0;
var pats=new Array();
var exps=new Array();
pats[i]=/System\.out\.print.*%.*/;
exps[i++]="<?php echo $MSG_SYSTEM_OUT_PRINT; ?>";
pats[i]=/.*没有那个文件或目录.*/;
exps[i++]="<?php echo $MSG_NO_SUCH_FILE_OR_DIRECTORY; ?>";
pats[i]=/not a statement/;
exps[i++]="<?php echo $MSG_NOT_A_STATEMENT; ?>";
pats[i]=/class, interface, or enum expected/;
exps[i++]="<?php echo $MSG_EXPECTED_CLASS_INTERFACE_ENUM; ?>";
pats[i]=/asm.*java/;
exps[i++]="<?php echo $MSG_SUBMIT_JAVA_AS_C_LANG; ?>";
pats[i]=/package .* does not exist/;
exps[i++]="<?php echo $MSG_DOES_NOT_EXIST_PACKAGE; ?>";
pats[i]=/possible loss of precision/;
exps[i++]="<?php echo $MSG_POSSIBLE_LOSS_OF_PRECISION; ?>";
pats[i]=/incompatible types/;
exps[i++]="<?php echo $MSG_INCOMPATIBLE_TYPES; ?>";
pats[i]=/illegal start of expression/;
exps[i++]="<?php echo $MSG_ILLEGAL_START_OF_EXPRESSION; ?>";
pats[i]=/cannot find symbol/;
exps[i++]="<?php echo $MSG_CANNOT_FIND_SYMBOL; ?>";
pats[i]=/';' expected/;
exps[i++]="<?php echo $MSG_EXPECTED_SEMICOLON; ?>";
pats[i]=/should be declared in a file named/;
exps[i++]="<?php echo $MSG_DECLARED_JAVA_FILE_NAMED; ?>";
pats[i]=/expected ‘.*’ at end of input/;
exps[i++]="<?php echo $MSG_EXPECTED_WILDCARD_CHARACTER_AT_END_OF_INPUT; ?>";
pats[i]=/invalid conversion from ‘.*’ to ‘.*’/;
exps[i++]="<?php echo $MSG_INVALID_CONVERSION; ?>";
pats[i]=/warning.*declaration of 'main' with no type/;
exps[i++]="<?php echo $MSG_NO_RETURN_TYPE_IN_MAIN; ?>";
pats[i]=/'.*' was not declared in this scope/;
exps[i++]="<?php echo $MSG_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/main’ must return ‘int’/;
exps[i++]="<?php echo $MSG_MAIN_MUST_RETURN_INT; ?>";
pats[i]=/expected identifier or '\(' before numeric constant/
exps[i++]="<?php echo $MSG_EXPECTED_IDENTIFIER; ?>";
pats[i]=/printf.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_PRINTF_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/warning: ignoring return value of/;
exps[i++]="<?php echo $MSG_IGNOREING_RETURN_VALUE; ?>";
pats[i]=/:.*__int64’ undeclared/;
exps[i++]="<?php echo $MSG_NOT_DECLARED_INT64; ?>";
pats[i]=/:.*expected ‘;’ before/;
exps[i++]="<?php echo $MSG_EXPECTED_SEMICOLON_BEFORE; ?>";
pats[i]=/ .* undeclared \(first use in this function\)/;
exps[i++]="<?php echo $MSG_UNDECLARED_NAME; ?>";
pats[i]=/scanf.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_SCANF_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/memset.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_MEMSET_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/malloc.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_MALLOC_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/puts.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_PUTS_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/gets.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_GETS_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/str.*was not declared in this scope/;
exps[i++]="<?php echo $MSG_STRING_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/‘import’ does not name a type/;
exps[i++]="<?php echo $MSG_NO_TYPE_IMPORT_IN_C_CPP; ?>";
pats[i]=/asm’ undeclared/;
exps[i++]="<?php echo $MSG_ASM_UNDECLARED; ?>";
pats[i]=/redefinition of/;
exps[i++]="<?php echo $MSG_REDEFINITION_OF; ?>";
pats[i]=/expected declaration or statement at end of input/;
exps[i++]="<?php echo $MSG_EXPECTED_DECLARATION_OR_STATEMENT; ?>";
pats[i]=/warning: unused variable/;
exps[i++]="<?php echo $MSG_UNUSED_VARIABLE; ?>";
pats[i]=/implicit declaration of function/;
exps[i++]="<?php echo $MSG_IMPLICIT_DECLARTION_OF_FUNCTION; ?>";
pats[i]=/too .* arguments to function/;
exps[i++]="<?php echo $MSG_ARGUMENTS_ERROR_IN_FUNCTION; ?>";
pats[i]=/expected ‘=’, ‘,’, ‘;’, ‘asm’ or ‘__attribute__’ before ‘namespace’/;
exps[i++]="<?php echo $MSG_EXPECTED_BEFORE_NAMESPACE; ?>";
pats[i]=/stray ‘\\[0123456789]*’ in program/;
exps[i++]="<?php echo $MSG_STRAY_PROGRAM; ?>";
pats[i]=/division by zero/;
exps[i++]="<?php echo $MSG_DIVISION_BY_ZERO; ?>";
pats[i]=/cannot be used as a function/;
exps[i++]="<?php echo $MSG_CANNOT_BE_USED_AS_A_FUNCTION; ?>";
pats[i]=/format .* expects type .* but argument .* has type .*/;
exps[i++]="<?php echo $MSG_CANNOT_FIND_TYPE; ?>";
pats[i]=/类.*是公共的，应在名为 .*java 的文件中声明/;
exps[i++]="<?php echo $MSG_JAVA_CLASS_ERROR; ?>";
pats[i]=/expected ‘\)’ before ‘.*’ token/;
exps[i++]="<?php echo $MSG_EXPECTED_BRACKETS_TOKEN; ?>";
pats[i]=/找不到符号/;
exps[i++]="<?php echo $MSG_NOT_FOUND_SYMBOL; ?>";
pats[i]=/需要为 class、interface 或 enum/;
exps[i++]="<?php echo $MSG_NEED_CLASS_INTERFACE_ENUM; ?>";
pats[i]=/符号： 类 .*List/;
exps[i++]="<?php echo $MSG_CLASS_SYMBOL_ERROR; ?>";
pats[i]=/方法声明无效；需要返回类型/;
exps[i++]="<?php echo $MSG_INVALID_METHOD_DECLARATION; ?>";
pats[i]=/expected.*before.*&.*token/;
exps[i++]="<?php echo $MSG_EXPECTED_AMPERSAND_TOKEN; ?>";
pats[i]=/非法的表达式开始/;
exps[i++]="<?php echo $MSG_DECLARED_FUNCTION_ORDER; ?>";
pats[i]=/需要 ';'/;
exps[i++]="<?php echo $MSG_NEED_SEMICOLON; ?>";
pats[i]=/extra tokens at end of #include directive/;
exps[i++]="<?php echo $MSG_EXTRA_TOKEN_AT_END_OF_INCLUDE; ?>";
pats[i]=/int.*hasNext/;
exps[i++]="<?php echo $MSG_INT_HAS_NEXT; ?>";
pats[i]=/unterminated comment/;
exps[i++]="<?php echo $MSG_UNTERMINATED_COMMENT; ?>";
pats[i]=/expected '=’, ‘,’, ‘;’, ‘asm’ or ‘__attribute__’ before ‘{’ token/;
exps[i++]="<?php echo $MSG_EXPECTED_BRACES_TOKEN; ?>";
pats[i]=/进行语法解析时已到达文件结尾/;
exps[i++]="<?php echo $MSG_REACHED_END_OF_FILE_1; ?>";
pats[i]=/subscripted value is neither array nor pointer/;
exps[i++]="<?php echo $MSG_SUBSCRIPT_ERROR; ?>";
pats[i]=/expected expression before ‘%’ token/;
exps[i++]="<?php echo $MSG_EXPECTED_PERCENT_TOKEN; ?>";
pats[i]=/expected expression before ‘.*’ token/;
exps[i++]="<?php echo $MSG_EXPECTED_EXPRESSION_TOKEN; ?>";
pats[i]=/expected but/;
exps[i++]="<?php echo $MSG_EXPECTED_BUT; ?>";
pats[i]=/redefinition of ‘main’/;
exps[i++]="<?php echo $MSG_REDEFINITION_MAIN; ?>";
pats[i]=/iostream: No such file or directory/;
exps[i++]="<?php echo $MSG_IOSTREAM_ERROR; ?>";
pats[i]=/expected unqualified-id before ‘\[’ token/;
exps[i++]="<?php echo $MSG_EXPECTED_UNQUALIFIED_ID_TOKEN; ?>";
pats[i]=/解析时已到达文件结尾/;
exps[i++]="<?php echo $MSG_REACHED_END_OF_FILE_2; ?>";
pats[i]=/非法字符/;
exps[i++]="<?php echo $MSG_INVALID_SYMBOL; ?>";
pats[i]=/应在名为.*的文件中声明/;
exps[i++]="<?php echo $MSG_DECLARED_FILE_NAMED; ?>";
pats[i]=/variably modified/;
exps[i++]="<?php echo $MSG_VARIABLY_MODIFIED; ?>";

function explain(){
//alert("asdf");
var errmsg=$("#errtxt").text();
var expmsg="";
for(var i=0;i<pats.length;i++){
var pat=pats[i];
var exp=exps[i];
var ret=pat.exec(errmsg);
if(ret){
expmsg+=ret+":"+exp+"<br>";
}
}
document.getElementById("errexp").innerHTML=expmsg;
//alert(expmsg);
}
</script> 
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shCore.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCpp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCss.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushJava.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushDelphi.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushRuby.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushBash.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPython.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPhp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPerl.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCSharp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushVb.js' type='text/javascript'></script>

<script>
$(document).ready(function(){
	$("#source").load("showsource2.php?id=<?php echo $id?>",function(response,status,xhr){

   	if(status=="success"){
		SyntaxHighlighter.config.bloggerMode = false;
		SyntaxHighlighter.config.clipboardSwf = '<?php echo $OJ_CDN_URL?>highlight/scripts/clipboard.swf';
		SyntaxHighlighter.highlight();
		explain();
   	}

	});

});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
