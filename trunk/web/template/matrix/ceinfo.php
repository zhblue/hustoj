<?php $show_title=$id." - $MSG_COMPILE_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<script src="<?php echo $OJ_CDN_URL?>template/<?php echo $OJ_TEMPLATE?>/js/textFit.min.js"></script>
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
// 继续添加更多gcc/g++编译错误模式
pats[i]=/error: expected initializer before/;
exps[i++]="<?php echo $MSG_EXPECTED_INITIALIZER; ?>";
pats[i]=/error: '.*' does not name a type/;
exps[i++]="<?php echo $MSG_DOES_NOT_NAME_A_TYPE; ?>";
pats[i]=/error: expected constructor, destructor, or type conversion before/;
exps[i++]="<?php echo $MSG_EXPECTED_CONSTRUCTOR_DESTRUCTOR; ?>";
pats[i]=/error: '.*' was not declared in this scope/;
exps[i++]="<?php echo $MSG_VARIABLE_NOT_DECLARED_IN_SCOPE; ?>";
pats[i]=/error: invalid types/;
exps[i++]="<?php echo $MSG_INVALID_TYPES; ?>";
pats[i]=/error: too few arguments to function/;
exps[i++]="<?php echo $MSG_TOO_FEW_ARGUMENTS; ?>";
pats[i]=/error: too many arguments to function/;
exps[i++]="<?php echo $MSG_TOO_MANY_ARGUMENTS; ?>";
pats[i]=/error: return-statement with no value/;
exps[i++]="<?php echo $MSG_RETURN_NO_VALUE; ?>";
pats[i]=/error: void value not ignored as it ought to be/;
exps[i++]="<?php echo $MSG_VOID_VALUE_NOT_IGNORED; ?>";
pats[i]=/error: lvalue required as left operand of assignment/;
exps[i++]="<?php echo $MSG_LVALUE_REQUIRED; ?>";
pats[i]=/error: invalid operands to binary/;
exps[i++]="<?php echo $MSG_INVALID_OPERANDS; ?>";
pats[i]=/error: request for member '.*' in '.*', which is of non-class type/;
exps[i++]="<?php echo $MSG_REQUEST_FOR_MEMBER; ?>";
pats[i]=/error: '.*' cannot be used as a function/;
exps[i++]="<?php echo $MSG_CANNOT_USE_AS_FUNCTION; ?>";
pats[i]=/error: new types may not be defined in a return type/;
exps[i++]="<?php echo $MSG_NEW_TYPES_IN_RETURN; ?>";
pats[i]=/error: two or more data types in declaration of/;
exps[i++]="<?php echo $MSG_TWO_OR_MORE_DATA_TYPES; ?>";
pats[i]=/error: expected ';' after struct definition/;
exps[i++]="<?php echo $MSG_EXPECTED_SEMICOLON_AFTER_STRUCT; ?>";
pats[i]=/error: array bound is not an integer constant/;
exps[i++]="<?php echo $MSG_ARRAY_BOUND_NOT_CONSTANT; ?>";
pats[i]=/error: storage size of '.*' isn't known/;
exps[i++]="<?php echo $MSG_STORAGE_SIZE_UNKNOWN; ?>";
pats[i]=/error: '.*' has incomplete type/;
exps[i++]="<?php echo $MSG_INCOMPLETE_TYPE; ?>";
pats[i]=/error: '.*' undeclared \(first use in this function\)/;
exps[i++]="<?php echo $MSG_UNDECLARED_FIRST_USE; ?>";
pats[i]=/error: jump to case label/;
exps[i++]="<?php echo $MSG_JUMP_TO_CASE_LABEL; ?>";
pats[i]=/error: crosses initialization of/;
exps[i++]="<?php echo $MSG_CROSSES_INITIALIZATION; ?>";
pats[i]=/error: invalid conversion from '.*' to '.*'/;
exps[i++]="<?php echo $MSG_INVALID_CONVERSION_TYPES; ?>";
pats[i]=/error: cannot convert '.*' to '.*' in assignment/;
exps[i++]="<?php echo $MSG_CANNOT_CONVERT_IN_ASSIGNMENT; ?>";
pats[i]=/error: no match for 'operator.*'/;
exps[i++]="<?php echo $MSG_NO_MATCH_FOR_OPERATOR; ?>";
pats[i]=/error: template instantiation depth exceeds maximum of/;
exps[i++]="<?php echo $MSG_TEMPLATE_INSTANTIATION_DEPTH; ?>";
pats[i]=/error: expected primary-expression before/;
exps[i++]="<?php echo $MSG_EXPECTED_PRIMARY_EXPRESSION; ?>";
pats[i]=/error: expected ',' or ';' before/;
exps[i++]="<?php echo $MSG_EXPECTED_COMMA_OR_SEMICOLON; ?>";

// 添加更多警告信息
pats[i]=/warning: control reaches end of non-void function/;
exps[i++]="<?php echo $MSG_CONTROL_REACHES_END; ?>";
pats[i]=/warning: comparison between signed and unsigned integer/;
exps[i++]="<?php echo $MSG_SIGNED_UNSIGNED_COMPARISON; ?>";
pats[i]=/warning: deprecated conversion from string constant to/;
exps[i++]="<?php echo $MSG_DEPRECATED_CONVERSION; ?>";
pats[i]=/warning: overflow in implicit constant conversion/;
exps[i++]="<?php echo $MSG_OVERFLOW_IN_CONVERSION; ?>";
pats[i]=/warning: non-static data member initializers only available with/;
exps[i++]="<?php echo $MSG_NON_STATIC_INITIALIZERS; ?>";
pats[i]=/warning: extended initializer lists only available with/;
exps[i++]="<?php echo $MSG_EXTENDED_INITIALIZER_LISTS; ?>";
pats[i]=/warning: 'auto' changes meaning in C\+\+11/;
exps[i++]="<?php echo $MSG_AUTO_CHANGES_MEANING; ?>";
pats[i]=/warning: lambda expressions only available with/;
exps[i++]="<?php echo $MSG_LAMBDA_EXPRESSIONS; ?>";

// 链接器错误
pats[i]=/undefined reference to/;
exps[i++]="<?php echo $MSG_UNDEFINED_REFERENCE; ?>";
pats[i]=/multiple definition of/;
exps[i++]="<?php echo $MSG_MULTIPLE_DEFINITION; ?>";
pats[i]=/ld returned 1 exit status/;
exps[i++]="<?php echo $MSG_LD_RETURNED_EXIT_STATUS; ?>";
pats[i]=/collect2: error: ld returned 1 exit status/;
exps[i++]="<?php echo $MSG_COLLECT2_LD_ERROR; ?>";

// C++特定错误
pats[i]=/error: '.*' is not a member of '.*'/;
exps[i++]="<?php echo $MSG_NOT_A_MEMBER_OF; ?>";
pats[i]=/error: '.*' was not declared in this scope/;
exps[i++]="<?php echo $MSG_NOT_DECLARED_IN_THIS_SCOPE; ?>";
pats[i]=/error: '.*' has not been declared/;
exps[i++]="<?php echo $MSG_HAS_NOT_BEEN_DECLARED; ?>";
pats[i]=/error: expected class-name before '.*' token/;
exps[i++]="<?php echo $MSG_EXPECTED_CLASS_NAME; ?>";
pats[i]=/error: expected '\)' before '.*' token/;
exps[i++]="<?php echo $MSG_EXPECTED_CLOSE_PAREN; ?>";
pats[i]=/error: expected ',' or '\.\.\.' before '.*' token/;
exps[i++]="<?php echo $MSG_EXPECTED_COMMA_OR_ELLIPSIS; ?>";
pats[i]=/error: ISO C\+\+ forbids declaration of '.*' with no type/;
exps[i++]="<?php echo $MSG_FORBIDS_DECLARATION_NO_TYPE; ?>";

// 模板相关错误
pats[i]=/error: missing template arguments before/;
exps[i++]="<?php echo $MSG_MISSING_TEMPLATE_ARGUMENTS; ?>";
pats[i]=/error: 'template' keyword may not be used in this context/;
exps[i++]="<?php echo $MSG_TEMPLATE_KEYWORD_CONTEXT; ?>";
pats[i]=/error: too many template-parameter-lists/;
exps[i++]="<?php echo $MSG_TOO_MANY_TEMPLATE_PARAMS; ?>";

// 预处理错误
pats[i]=/error: #error.*/;
exps[i++]="<?php echo $MSG_PREPROCESSOR_ERROR; ?>";
pats[i]=/error: missing terminating " character/;
exps[i++]="<?php echo $MSG_MISSING_TERMINATING_QUOTE; ?>";
pats[i]=/error: missing terminating ' character/;
exps[i++]="<?php echo $MSG_MISSING_TERMINATING_APOSTROPHE; ?>";
pats[i]=/error: unterminated #ifndef/;
exps[i++]="<?php echo $MSG_UNTERMINATED_IFNDEF; ?>";
pats[i]=/error: #include expects "FILENAME" or <FILENAME>/;
exps[i++]="<?php echo $MSG_INCLUDE_EXPECTS; ?>";

// 内存相关错误
pats[i]=/error: invalid use of void expression/;
exps[i++]="<?php echo $MSG_INVALID_USE_OF_VOID; ?>";
pats[i]=/error: double free or corruption/;
exps[i++]="<?php echo $MSG_DOUBLE_FREE_OR_CORRUPTION; ?>";
pats[i]=/error: invalid next size/;
exps[i++]="<?php echo $MSG_INVALID_NEXT_SIZE; ?>";
pats[i]=/error: corrupted double-linked list/;
exps[i++]="<?php echo $MSG_CORRUPTED_DOUBLE_LINKED_LIST; ?>";

// 标准库相关错误
pats[i]=/error: '.*' is not a member of 'std'/;
exps[i++]="<?php echo $MSG_NOT_MEMBER_OF_STD; ?>";
pats[i]=/error: 'cout' was not declared in this scope/;
exps[i++]="<?php echo $MSG_COUT_NOT_DECLARED; ?>";
pats[i]=/error: 'cin' was not declared in this scope/;
exps[i++]="<?php echo $MSG_CIN_NOT_DECLARED; ?>";
pats[i]=/error: 'endl' was not declared in this scope/;
exps[i++]="<?php echo $MSG_ENDL_NOT_DECLARED; ?>";

// C++11/14/17特性相关错误
pats[i]=/error: 'nullptr' was not declared in this scope/;
exps[i++]="<?php echo $MSG_NULLPTR_NOT_DECLARED; ?>";
pats[i]=/error: 'constexpr' was not declared in this scope/;
exps[i++]="<?php echo $MSG_CONSTEXPR_NOT_DECLARED; ?>";
pats[i]=/error: range-based 'for' loops are not allowed in C\+\+98 mode/;
exps[i++]="<?php echo $MSG_RANGE_BASED_FOR_NOT_ALLOWED; ?>";

// 添加更多中文错误信息（针对中文版编译器）
pats[i]=/错误：expected/;
exps[i++]="<?php echo $MSG_CHINESE_EXPECTED; ?>";
pats[i]=/错误：在.*之前应有/;
exps[i++]="<?php echo $MSG_CHINESE_SHOULD_HAVE_BEFORE; ?>";
pats[i]=/错误：无法将.*转换为.*/;
exps[i++]="<?php echo $MSG_CHINESE_CANNOT_CONVERT; ?>";
pats[i]=/错误：对.*的引用未定义/;
exps[i++]="<?php echo $MSG_CHINESE_UNDEFINED_REFERENCE; ?>";

// 数学和算法相关错误
pats[i]=/error: 'pow' was not declared in this scope/;
exps[i++]="<?php echo $MSG_POW_NOT_DECLARED; ?>";
pats[i]=/error: 'sqrt' was not declared in this scope/;
exps[i++]="<?php echo $MSG_SQRT_NOT_DECLARED; ?>";
pats[i]=/error: 'abs' was not declared in this scope/;
exps[i++]="<?php echo $MSG_ABS_NOT_DECLARED; ?>";

// 文件操作错误
pats[i]=/error: 'fopen' was not declared in this scope/;
exps[i++]="<?php echo $MSG_FOPEN_NOT_DECLARED; ?>";
pats[i]=/error: 'FILE' was not declared in this scope/;
exps[i++]="<?php echo $MSG_FILE_NOT_DECLARED; ?>";

// 时间函数错误
pats[i]=/error: 'clock' was not declared in this scope/;
exps[i++]="<?php echo $MSG_CLOCK_NOT_DECLARED; ?>";
pats[i]=/error: 'CLOCKS_PER_SEC' was not declared in this scope/;
exps[i++]="<?php echo $MSG_CLOCKS_PER_SEC_NOT_DECLARED; ?>";
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
            var resultVariable = errmsg;
                var errorLines = [];
                var regex = /(\w+\.<?php echo $language_ext[$lang]?>):(\d+):\d+:/g;
                var match;
                while ((match = regex.exec(resultVariable)) !== null) {
                    var lineNumber = parseInt(match[2], 10);
                    errorLines.push(lineNumber);
                }
                errorLines.forEach(function(line) {
                        console.log("line"+line);
                        $("div .number"+line).addClass("highlighted");
                });
         <?php if (isset($OJ_AI_API_URL)&&!empty($OJ_AI_API_URL)){ ?>
                expmsg+="AI 答疑 ...<img src='image/loader.gif'>";
				$("#errexp").html(expmsg);
		    $.ajax({
			url: '<?php echo $OJ_AI_API_URL ?>?sid=<?php echo $id?>', 
			type: 'GET',
			success: function(data) {
				if(parseInt(data)>0)
					window.setTimeout('pull_result('+data+')',2000);
				else{
					fill_data(data);		
				}
			},
			error: function() {
			    console.log('获取数据失败');
			}
		    });
        <?php } ?>

}

function fill_data(data){
    $("#errexp").html(data);    
    $("#errexp").html(marked.parse($("#errexp").text()));    
}
function pull_result(id){
	console.log(id);
    $.ajax({
	url: '../aiapi/ajax.php', 
	type: 'GET',
	data: { id: id },
	success: function(data) {
		if(data=='waiting'){
			window.setTimeout('pull_result('+id+')',2000);
		}else{
			fill_data(data);
		    $('#ai_bt').val('再来一次');
		    $('#ai_bt').prop('disabled', false);
		}
	},
	error: function() {
	    $('#ai_bt').val('获取数据失败');
	    $('#ai_bt').prop('disabled', false);
	}
    });
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
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>marked.min.js"></script>
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
