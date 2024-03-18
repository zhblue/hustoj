<?php
          if($pr_flag){
            $show_title="P$id - ".$row['title']." - $OJ_NAME";
          }else{
            $id=$row['problem_id'];
            $show_title="$MSG_PROBLEM ".$PID[$pid].": ".$row['title']." - $OJ_NAME";
          }
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<style>
.ace_cursor {
  border-left-width: 1px !important;
  color: #000 !important;
}

#languages-menu::-webkit-scrollbar, #testcase-menu::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}

div[class*=ace_br] {
    border-radius: 0 !important;
}
.copy {
    font-size: 12px;
    color: #4d4d4d;
    background-color: white;
    padding: 2px 8px;
    margin: 8px;
    border-radius: 4px;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.05);
}
</style>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>clipboard.min.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>marked.min.js"></script>
<div class="padding ">
<div class="ui center aligned grid">
    <div class="row">
      <h1 class="ui header">
        <?php
          if($pr_flag){
            echo "$id: ".$row['title'];
            // <%= problem.title %><% if (problem.allowedEdit && !problem.is_public) { %><span class="ui tiny red label">未公开</span><% } %>";
            //echo "<title>$MSG_PROBLEM".$row['problem_id']."--". $row['title']."</title>";
            //echo "<center><h2><strong>$id: ".$row['title']."</strong></h2>";
          }else{
            $id=$row['problem_id'];
            //echo "<title>$MSG_PROBLEM ".$PID[$pid].": ".$row['title']." </title>";
            echo "$MSG_PROBLEM ".$PID[$pid].": ".$row['title'];
          }
          if($row['defunct']=="Y")
          echo "<span class=\"p-label ui tiny red label\">$MSG_RESERVED</span>";
        ?>
      </h1>
    </div>
      <div class="row" style="margin-top: -15px">
          <span class="ui label"><?php echo $MSG_Memory_Limit ?>：<?php echo $row['memory_limit']; ?> MB</span>
          <span class="ui label"><?php echo $MSG_Time_Limit ?>：<?php echo $row['time_limit']; ?> S</span>
         <!-- <span class="ui label">标准输入输出</span> -->
      </div>
      <div class="row" style="margin-top: -23px">
        <!--   <span class="ui label">题目类型：传统</span> -->
          <span class="ui label"><?php echo $MSG_JUDGE_STYLE ?>：<?php if($row['spj']) echo "$MSG_SPJ"; else echo "$MSG_TEXT_COMPARE" ; ?></span>
          <span class="ui label"><?php echo $MSG_Creator ?>：<span id='creator'></span></span>
      </div>
      <div class="row" style="margin-top: -23px">
          <span class="ui label"><?php echo $MSG_SUBMIT ?>：<?php echo $row['submit']; ?></span>
          <span class="ui label"><?php echo $MSG_SOVLED ?>：<?php echo $row['accepted']; ?></span>
      </div>
</div>  
<div class="ui grid">
  <div class="row" id="submit-buttons"> 
    <div class="column">
      <div class="ui buttons">

          <?php
            if($pr_flag){
              echo "<a id='submit'  class=\"small ui primary button\" href=\"submitpage.php?id=$id\">$MSG_SUBMIT</a>";
              echo "<a class=\"small ui positive button\" href=\"status.php?problem_id=$id\">$MSG_SUBMIT_RECORD</a>";
              echo "<a class=\"small ui orange button\" href=\"problemstatus.php?id=$id\">$MSG_STATISTICS</a>";
	      if($OJ_BBS)echo "<a class=\"small ui red button\" href=\"discuss.php?pid=$id\">$MSG_BBS</a>";
            }else{
              echo "<a href=\"contest.php?cid=$cid\" class=\"ui orange button\">$MSG_RETURN_CONTEST</a>";
              echo "<a id='submit'  class=\"small ui primary button\" href=\"submitpage.php?cid=$cid&pid=$pid&langmask=$langmask\">$MSG_SUBMIT</a>";
              echo "<a class=\"small ui positive button\" href=\"status.php?problem_id=$id\">$MSG_GLOBAL$MSG_SUBMIT_RECORD</a>";
              echo "<a class=\"small ui orange button\" href=\"status.php?problem_id=$PID[$pid]&cid=$cid\">$MSG_THIS_CONTEST$MSG_SUBMIT_RECORD</a>";

            }
	      echo "<a class='small ui primary button' href='#' onclick='transform()' role='button'>$MSG_SHOW_OFF</a>";
          ?>
          
      </div>
     
      <?php
        if ( isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$row['problem_id']])  ) {  //only  the original editor can edit this  problem
        
        require_once("include/set_get_key.php");
      ?>
      
        <div class="ui buttons right floated">
            <a class="small ui button" href="admin/problem_edit.php?id=<?php echo $id?>&getkey=<?php echo $_SESSION[$OJ_NAME.'_'.'getkey']?>"><?php echo $MSG_EDIT.$MSG_PROBLEM?></a>
            <a class="small ui button" href='javascript:phpfm(<?php echo $row['problem_id'];?>)'><?php echo $MSG_TEST_DATA?></a>
        </div>
      <?php }?>
    </div>
  </div>

  <div class="row">
    <div class="column">
      <h4 class="ui top attached block header"><?php echo $MSG_Description?></h4>
      <div class="ui bottom attached segment font-content"><?php echo bbcode_to_html($row['description']); ?></div>
    </div>
  </div>
  <?php if($row['input']){ ?>
    <div class="row">
      <div class="column">
          <h4 class="ui top attached block header"><?php echo $MSG_Input?></h4>
          <div class="ui bottom attached segment font-content"><?php echo bbcode_to_html($row['input']); ?></div>
      </div>
    </div>
  <?php }?>
  <?php if($row['output']){ ?>
    <div class="row">
        <div class="column">
          <h4 class="ui top attached block header"><?php echo $MSG_Output?></h4>
          <div class="ui bottom attached segment font-content"><?php echo bbcode_to_html($row['output']); ?></div>
        </div>
    </div>
  <?php }?>

  <?php
    $sinput=str_replace("<","&lt;",$row['sample_input']);
    $sinput=str_replace(">","&gt;",$sinput);
    $soutput=str_replace("<","&lt;",$row['sample_output']);
    $soutput=str_replace(">","&gt;",$soutput);
  ?>
  <?php if(strlen($sinput)>0 && $sinput!="\n"){ ?>
    <div class="row">
        <div class="column">
          <h4 class="ui top attached block header"><?php echo $MSG_Sample_Input?> 
          <span class="copy" id="copyin" data-clipboard-text="<?php echo htmlentities($sinput, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $MSG_COPY; ?></span>
          </h4>
          <!-- <span class=copy id=\"copyin\" data-clipboard-text=\"".($sinput)."\"><?php echo $MSG_COPY; ?></span> -->
          <div class="ui bottom attached segment font-content">
            <!-- <pre><?php echo ($sinput); ?></pre> -->
            <pre style="margin-top: 0; margin-bottom: 0; "><code class="lang-plain"><?php echo ($sinput); ?></code></pre>
          </div>
        </div>
    </div>
  <?php }?>
  <?php if(strlen($soutput)>0 && $soutput!="\n"){ ?>
    <div class="row">
        <div class="column">
          <h4 class="ui top attached block header"><?php echo $MSG_Sample_Output?>
          <span class="copy" id="copyout" data-clipboard-text="<?php echo htmlentities($soutput, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $MSG_COPY; ?></span>
          </h4>
          <!-- <span class=copy id=\"copyout\" data-clipboard-text=\"".($soutput)."\"><?php echo $MSG_COPY; ?></span> -->
          <div class="ui bottom attached segment font-content">
            <!-- <div class="ui existing segment"> -->
              <pre style="margin-top: 0; margin-bottom: 0; "><code class="lang-plain"><?php echo ($soutput); ?></code></pre>
            <!-- </div> -->
          </div>
        </div>
    </div>
  <?php }?>
  <?php if($row['hint']){ ?>
    <div class="row">
        <div class="column">
          <h4 class="ui top attached block header"><?php echo $MSG_HINT?></h4>
          <div class="ui bottom attached segment font-content hint"><?php echo bbcode_to_html($row['hint']); ?></div>
        </div>
    </div>
  <?php }?>
  <?php
    $color=array("blue","teal","orange","pink","olive","red","violet","yellow","green","purple");
    $tcolor=0;
  ?>
  <?php if($row['source'] && !isset($_GET['cid']) ){
    $cats=explode(" ",$row['source']);
  ?>
    <div class="row">
      <div class="column">
        <h4 class="ui block header top attached" id="show_tag_title_div" style="margin-bottom: 0; margin-left: -1px; margin-right: -1px; ">
        <?php echo $MSG_SOURCE?>
        </h4>
        <div class="ui bottom attached segment" id="show_tag_div">

          <?php foreach($cats as $cat){
            if(trim($cat)=="") continue;
            $label_theme=$color[$tcolor%count($color)];
            $tcolor++;
            ?>
            <a href="<?php
                if(mb_ereg("^http",$cat))    // remote oj pop links
                        echo htmlentities($cat,ENT_QUOTES,'utf-8').'" target="_blank' ;
                else
                        echo "problemset.php?search=".htmlentities($cat,ENT_QUOTES,'utf-8') ;
            ?>" class="ui medium <?php echo $label_theme; ?> label">
              <?php echo htmlentities($cat,ENT_QUOTES,'utf-8'); ?>
            </a>
          <?php } ?>


        </div>
     
      </div>
    </div>
  <?php } ?>
  
     <div class="ui buttons">

          <?php
            if($pr_flag){
              echo "<a id='submit'  class=\"small ui primary button\" href=\"submitpage.php?id=$id\">$MSG_SUBMIT</a>";
              echo "<a class=\"small ui positive button\" href=\"status.php?problem_id=$id\">$MSG_SUBMIT_RECORD</a>";
              echo "<a class=\"small ui orange button\" href=\"problemstatus.php?id=$id\">$MSG_STATISTICS</a>";
	      if($OJ_BBS)echo "<a class=\"small ui red button\" href=\"discuss.php?pid=$id\">$MSG_BBS</a>";
            }else{
              echo "<a href=\"contest.php?cid=$cid\" class=\"ui orange button\">$MSG_RETURN_CONTEST</a>";
              echo "<a id='submit'  class=\"small ui primary button\" href=\"submitpage.php?cid=$cid&pid=$pid&langmask=$langmask\">$MSG_SUBMIT</a>";
              echo "<a class=\"small ui positive button\" href=\"status.php?problem_id=$PID[$pid]&cid=$cid\">$MSG_SUBMIT_RECORD</a>";
            }
	      echo "<a class='small ui primary button' href='#' onclick='transform()' role='button'>$MSG_SHOW_OFF</a>";
          ?>
          
      </div>
</div>

  <script type="text/javascript">
  
  function transform(){
        let height=document.body.clientHeight;
<?php if ( $row[ 'spj' ]==2 ) {?>
			let width=parseInt(document.body.clientWidth*0.3);
			let width2=parseInt(document.body.clientWidth*0.7);
<?php }else{ ?>
			let width=parseInt(document.body.clientWidth*0.6);
			let width2=parseInt(document.body.clientWidth*0.4);
<?php } ?>
        let submitURL=$("#submit")[0].href;
        console.log(width);
        let main=$("#main");
        let problem=main.html();
        if (window.screen.width < 500){
        	main.parent().append("<div id='submitPage' class='container' style='opacity:0.95;z-index:1000;top:49px;'></div>");
                $("#submitPage").html("<iframe id='ansFrame' src='"+submitURL+"&spa' width='100%' height='"+window.screen.height+"px' ></iframe>");
                window.setTimeout('$("#ansFrame")[0].scrollIntoView()',1000);
	}else{
        	main.removeClass("container");
		main.css("width",width2);
		main.css("margin-left","10px");
       	 	main.parent().append("<div id='submitPage' class='container' style='opacity:0.95;position:fixed;z-index:1000;top:49px;right:-"+width2+"px'></div>");
		$("#submitPage").html("<iframe src='"+submitURL+"&spa' width='"+width+"px' height='"+height+"px' ></iframe>");
	}
	$("#submit").remove();
	<?php if ($row['spj']>1){ ?>
            window.setTimeout('$("iframe")[0].contentWindow.$("#TestRun").remove();',1000);
        <?php }?>
  }

  function submit_code() {
    if (!$('#submit_code input[name=answer]').val().trim() && !editor.getValue().trim()) return false;
    $('#submit_code input[name=language]').val($('#languages-menu .item.active').data('value'));
    lastSubmitted = editor.getValue();
    $('#submit_code input[name=code]').val(editor.getValue());
    return true;
  }

 // $('#languages-menu')[0].scrollTop = $('#languages-menu .active')[0].offsetTop - $('#languages-menu')[0].firstElementChild.offsetTop;

  $(function () {
    $('#languages-menu .item').click(function() {
      $(this)
        .addClass('active')
        .closest('.ui.menu')
        .find('.item')
          .not($(this))
          .removeClass('active')
      ;
      editor.getSession().setMode("ace/mode/" + $(this).data('mode'));
    });
  });
  </script>

    
<?php include("template/$OJ_TEMPLATE/footer.php");?>

  <script>
function phpfm(pid){
    //alert(pid);
    $.post("admin/phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
      if(status=="success"){
        document.location.href="admin/phpfm.php?frame=3&pid="+pid;
      }
    });
}
function selectOne( num, answer){
          let editor = $("iframe")[0].contentWindow.$("#source");
          let old=editor.text();
          let key= num+".*";
          console.log(key);
          let rep=old.replace(new RegExp(key),num+" "+answer);
          editor.text(rep);
}
function selectMulti( num, answer){
  let editor = $("iframe")[0].contentWindow.$("#source");
  let old=editor.text();
  let key= num+".*";
  console.log(key);
  let rep=old.replace(new RegExp(key),num+" "+answer);
  editor.text(rep);
}

  $(document).ready(function(){
    	$("#creator").load("problem-ajax.php?pid=<?php echo $id?>");
	<?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
		marked.use({
                  // 开启异步渲染
                  async: true,
                  pedantic: false,
                  gfm: true,
                  mangle: false,
                  headerIds: false
                });

		$("div.md").each(function(){
			$(this).html(marked.parse($(this).html()));
		});
	  	// adding note for ```input1  ```output1 in description
	        for(let i=1;i<10;i++){
                        $(".language-input"+i).parent().before("<div><?php echo $MSG_Input?>"+i+":</div>");
                        $(".language-output"+i).parent().before("<div><?php echo $MSG_Output?>"+i+":</div>");
                }

	       
        $(".md table tr td").css({
            "border": "1px solid grey",
            "text-align": "center",
            "width": "200px",
            "height": "30px"
        });

        $(".md table th").css({
            "border": "1px solid grey",
            "width": "200px",
            "height": "30px",
            "background-color": "#9e9e9ea1",
            "text-align": "center"
        });
	        
	<?php } ?>
	        //单纯文本1. A. B. C. D. 自动变控件
        $('span[class=auto_select]').each(function(){
                let i=1;
                let start=0;
                let next=0;
                let raw=$(this).html();
                let options=['A','B','C','D'];
                while(start>=0){
                        start=raw.indexOf("\n"+i+".",start);
                        if(start<0) break;
                        let end=start;
                        let type="radio"
                        for(let j=0;j<4;j++){
                                let option=options[j];
                                end=raw.indexOf(option+".",start);
                                next=raw.indexOf("\n"+(i+1)+".",start);
                                if ( end<0 || ( end > next && next > 0 )) {
                                        console.log("i:"+i+" j:"+option+" end:"+end+" next:"+next);
                                        end=start;
                                        break;
                                }
                                if(j==0&&raw.substring(start,end).indexOf("多选")>0) type="checkbox";
                                let disp="<input type=\""+type+"\" name=\""+i+"\" value=\""+option+"\" />"+option+".";
                                //console.log(disp);
                                raw= raw.substring(0,end-1)+disp+raw.substring(end+2);
                                start+=disp.length;
                        }
                        start=end+1;
                        i++;
                }
                //console.log(raw);
                $(this).html(raw);
        });




        $('input[type="radio"]').click(function(){
                if ($(this).is(':checked'))
                   selectOne($(this).attr("name"),$(this).val());
        });
	$('input[type="checkbox"]').click(function(){
                let num=$(this).attr("name");
                let answer="";
                $("input[type=checkbox][name="+num+"]").each(function(){
                        if ($(this).is(':checked'))
                                answer+=$(this).val();
                });
                selectMulti(num,answer);
        });
	<?php if ($row['spj']>1){ ?>
	    transform();
	<?php }?>

  });
  </script>   


  <script>
	  if($('#copyin')[0]!= undefined ){

		    var clipboardin=new Clipboard($('#copyin')[0]);
		    clipboardin.on('success', function(e){
		      $("#copyin").text("<?php echo $MSG_COPY.$MSG_SUCCESS; ?>!"); 
		          setTimeout(function () {$("#copyin").text("<?php echo $MSG_COPY; ?>"); }, 1500);    
		      console.log(e);
		    });
		    clipboardin.on('error', function(e){
		      $("#copyin").text("<?php echo $MSG_COPY.$MSG_FAIL; ?>!"); 
		          setTimeout(function () {$("#copyin").text("<?php echo $MSG_COPY; ?>"); }, 1500);
		      console.log(e);
		    });
	  }
	  if($('#copyout')[0]!= undefined ){

		    var clipboardout=new Clipboard($('#copyout')[0]);
		    clipboardout.on('success', function(e){
		      $("#copyout").text("<?php echo $MSG_COPY.$MSG_SUCCESS; ?>!"); 
		          setTimeout(function () {$("#copyout").text("<?php echo $MSG_COPY; ?>"); }, 1500);    
		      console.log(e);
		    });
		    clipboardout.on('error', function(e){
		      $("#copyout").text("<?php echo $MSG_COPY.$MSG_FAIL; ?>!"); 
		          setTimeout(function () {$("#copyout").text("<?php echo $MSG_COPY; ?>"); }, 1500);
		      console.log(e);
		    });
	  }

  </script>

<style>
   
   /* 高亮文本的样式 */
   .highlighted {
     transition: background-color 0.5s; /* 添加过渡效果 */
   }

   /* 颜色选择框的样式 */
   .color-picker {
     position: absolute; /* 设置绝对定位 */
     display: none; /* 设置默认不显示 */
     border: 1px solid black; /* 设置边框 */
     padding: 5px; /* 设置内边距 */
     background-color: white; /* 设置背景颜色 */
   }

   /* 颜色选择框中的颜色按钮的样式 */
   .color-picker button {
     width: 20px; /* 设置宽度 */
     height: 20px; /* 设置高度 */
     border: none; /* 去掉边框 */
     margin: 2px; /* 设置外边距 */
     cursor: pointer; /* 设置鼠标样式为手指 */
   }
 </style>
 

<script>
      // 创建一个颜色选择框元素
   var colorPicker = document.createElement('div');
   colorPicker.className = 'color-picker'; // 设置类名
   colorPicker.innerHTML = '<button style="background-color: yellow; border-radius: 50%;"></button><button style="background-color: rgb(255, 79, 79); border-radius: 50%;"></button><button style="background-color: rgb(71, 255, 111); border-radius: 50%;"></button><button style="background-color: rgb(255, 255, 255); border-radius: 50%; border: 1px solid black;"></button>';

   document.body.appendChild(colorPicker); // 将颜色选择框添加到文档中

   // 获取颜色选择框中的所有颜色按钮
   var colorButtons = colorPicker.getElementsByTagName('button');

  // 给每个颜色按钮添加点击事件
for (var i = 0; i < colorButtons.length; i++) {
 colorButtons[i].addEventListener('click', function () {
   var color = this.style.backgroundColor; // 获取点击的颜色按钮的背景颜色
   var selection = window.getSelection();
   if (selection.rangeCount > 0) {
     var range = selection.getRangeAt(0); // 获取选中的文本范围
     var selectedText = range.extractContents(); // 提取选中的内容

     // Create a new span element
     var span = document.createElement('span');
     span.className = 'highlighted';
     span.style.backgroundColor = color; // 设置选中文本的背景颜色

     // Wrap the extracted content in a new text node
     var textNode = document.createTextNode(selectedText.textContent);
     span.appendChild(textNode); // Append the text node to the span element

     // Insert the modified content back into the range
     range.insertNode(span);

     window.getSelection().removeAllRanges(); // 清空选中文本
     colorPicker.style.display = 'none'; // 隐藏颜色选择框
   }
 });
}

   // 给文档添加鼠标抬起事件
   document.addEventListener('mouseup', function (event) {
     var selection = window.getSelection().toString().trim();
     if (selection !== '') {
       colorPicker.style.display = 'block'; // 显示颜色选择框
       colorPicker.style.left = event.pageX + 'px'; // 设置颜色选择框的水平位置
       colorPicker.style.top = event.pageY -110 + 'px'; // 设置颜色选择框的垂直位置
     } else {
       colorPicker.style.display = 'none'; // 隐藏颜色选择框
     }
   });
   
</script>
<?php if (isset($OJ_MATHJAX)&&$OJ_MATHJAX){?>
    <!--以下为了加载公式的使用而既加入-->
<script>
  MathJax = {
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]}
  };
</script>

<script id="MathJax-script" async src="template/bs3/tex-chtml.js"></script>
<style>
.jumbotron1{
  font-size: 18px;
}
</style>

<?php } ?>
