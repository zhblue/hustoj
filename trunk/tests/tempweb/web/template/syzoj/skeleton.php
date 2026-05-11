<?php $show_title="$view_title - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class='padding'>
<h1><?php echo  $view_title ?></h1>

<span class="md">
	<?php echo $view_content; ?> 
</span>

 
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/css/"?>highlight.css">
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>highlight.min.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>marked.umd.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?>marked-highlight.umd.js"></script>
<script> 
 const { Marked } = globalThis.marked;
 const { markedHighlight } = globalThis.markedHighlight;
const marked = new Marked(
  markedHighlight({
	emptyLangClass: 'hljs',
    langPrefix: 'hljs language-',
    highlight(code, lang, info) {
      const language = hljs.getLanguage(lang) ? lang : 'plaintext';
      return hljs.highlight(code, { language }).value;
    }
  })
);


console.log(marked.parse(`
\`\`\`javascript
const highlight = "code";
\`\`\`
`));
         $(document).ready(function(){
		$(".md").each(function(){
			$(this).html(marked.parse($(this).html()));             // html() make > to &gt;   text() keep >
		});
	  	// adding note for ```input1  ```output1 in description
	        for(let i=1;i<10;i++){
                        $(".language-input"+i).parent().before("<div><?php echo $MSG_Sample_Input?>"+i+":</div>");
                        $(".language-output"+i).parent().before("<div><?php echo $MSG_Sample_Output?>"+i+":</div>");
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
  });
</script>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
