<?php
date_default_timezone_set("PRC");
ini_set("display_errors", "Off"); 
require_once("../include/db_info.inc.php");
require_once("../include/const.inc.php");

// 权限校验
if(!(isset($_SESSION[$OJ_NAME.'_administrator'])||isset($_SESSION[$OJ_NAME.'_problem_editor'])||isset($_SESSION[$OJ_NAME.'_contest_creator'])||isset($_SESSION[$OJ_NAME.'_tag_adder']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}
if(isset($_GET['id'])){
    $id=intval($_GET['id']);
	if(isset($_SESSION[$OJ_NAME.'_user_id'])){
		$user_id=$_SESSION[$OJ_NAME.'_user_id'];

		$sql="select * from openai_task_queue where id=?";
		$tasks=pdo_query($sql,$id);

		if(!empty($tasks)){
			$task=$tasks[0];
			if($user_id==$task['user_id']){
				if($task['status']==2){
					$response=$task['response_body'];
					$data=json_decode($response);
					if(isset($data->choices[0]->message->content)){
						$sql= ($data->choices[0]->message->content);	
					}else{
						echo $response;
					}
				}
			}
		}
	}
    if(isset($sql))
    	$rows = rdo_query($sql);
	// 如果结果集为空，直接提示
    if (empty($rows)) {
        echo "<p>查询成功，但未找到匹配的数据。</p>";
        //exit;
    }else if (!is_array($rows)){
    	echo $rows;
    }else{
	// var_dump($rows);
	    // 4. 动态渲染为 HTML 二维表格
	    echo '<div><table id="result" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; text-align: left;">';

	    // 表头：动态获取第一行的所有键名（列名）
	    echo '<tr style="background-color: #f2f2f2;">';
	    foreach (array_keys($rows[0]) as $columnName) {
		// echo '<th>' . htmlspecialchars($columnName) . '</th>';
		$displayName = isset($columnsMap[$columnName]) ? $columnsMap[$columnName] : $columnName;
		echo '<th>' . htmlspecialchars($displayName) . '</th>';
	    }
	    echo '</tr>';

	    // 表体：循环输出每一行数据
	    foreach ($rows as $row) {
		echo '<tr>';
		foreach (array_keys($row) as $columnName) {
		    // 处理 null 值，并防止 XSS 注入
		    $value=$row[$columnName];
		    if($columnName=='problem_id' || $columnName == "题目ID"  || $columnName == '题号' || $columnName == '题目编号' ){
			echo '<td><a target="_blank" href="../problemt.php?id=' . htmlentities($value??'') . '">'.htmlentities($value??'').'</a> </td>';
		    }else if($columnName=='contest_id' || $columnName == "比赛ID" || $columnName == '比赛编号' ){
			echo '<td><a target="_blank" href="../contest.php?cid=' . htmlentities($value??'') . '">'.htmlentities($value??'').'</a> </td>';
		    }else if($columnName=='user_id' || $columnName == "用户名" ){
			echo '<td><a target="_blank" href="user_list.php?keyword=' . htmlentities($value??'') . '">'.htmlentities($value??'').'</a> </td>';
		    }else{
			echo '<td>' . ($value !== null ? htmlspecialchars($value) : '<i>NULL</i>') . '</td>';
		    }
		}
		echo '</tr>';
	    }
	    echo '</table></div>';
    }
    exit();
}
// 5. 解析并打印结果

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>AI智能查询</title>
    <script src="../template/syzoj/jquery.min.js"></script>
    <script src="../template/syzoj/js/marked.min.js"></script>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; line-height: 1.5; color: #333; padding: 20px; background: #f9f9f9; }
        h2 { color: #2c3e50; border-left: 5px solid #007bff; padding-left: 15px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; table-layout: fixed; border: 1px solid #dee2e6; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: left; vertical-align: top; }
        th { background-color: #f8f9fa; font-weight: 600; }
        .markdown-body { max-height: 350px; overflow-y: auto; font-size: 0.92em; padding: 8px; background: #fafafa; border-radius: 4px; }
        .referer-text { font-size: 0.8em; color: #6c757d; word-break: break-all; }
        .pagination { margin-top: 25px; display: flex; align-items: center; gap: 10px; }
        .pagination a { padding: 6px 14px; border: 1px solid #007bff; text-decoration: none; color: #007bff; border-radius: 3px; transition: 0.2s; }
        .pagination a:hover { background: #007bff; color: white; }
        .timestamp { font-size: 0.85em; color: #495057; white-space: nowrap; }
        /* 代码块简单样式微调 */
        pre { background: #f1f1f1; padding: 10px; border-radius: 3px; overflow-x: auto; display:none }
        code { font-family: Consolas, Monaco, monospace; color: #d63384; }
    </style>
<link rel="stylesheet" href="<?php echo "../template/$OJ_TEMPLATE"?>/css/katex.min.css">
</head>
<body ondblclick='$("pre").toggle()'>
请描述您的数据库需求
    <h3>AI报表: </h3>
<script>

function ai_gen(purpose){
	    let oldval=$('#ai_bt').val();
	    $('#ai_bt').val('AI思考中...请稍候...');
	    $('#ai_bt').prop('disabled', true);
	    $.ajax({
		url: '<?php echo "../".$OJ_AI_API_URL?>', 
			type: 'POST',
			data: { purpose: purpose },
			success: function(data) {
				if(parseInt(data)>0)
					window.setTimeout('pull_result('+data+')',1000);
				else{
					fill_data(data);		
				}
			},
			error: function() {
			    $('#ai_bt').val('获取数据失败');
				$('#ai_bt').prop('disabled', false);
			}
	    });
}
function fill_data(data,id){
	$("pre").text(data);
	$("#output").load("aidba.php?id="+id);
        $('#ai_bt').prop('disabled', false);
        $('#ai_bt').val("再来一次");
}
function pull_result(id){
	console.log(id);
    $.ajax({
	url: '../aiapi/ajax.php', 
	type: 'GET',
	data: { id: id },
	success: function(data) {
		if(data=='waiting'){
			window.setTimeout('pull_result('+id+')',1000);
		}else{
		    fill_data(data,id);
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
<form action="<?php echo basename(__FILE__)?>"  method='post' >
	<textarea id=purpose name='purpose' cols=120 rows=5 >查询一个月内登录最频繁的10个用户</textarea>
    	<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc;'></pre>
   <div>
	<input id='ai_bt' type='button' value='求助AI-DBA' onclick='ai_gen($("#purpose").val())' >
	<input type='button' onclick='exportTableToCSV()' value='<?php echo $MSG_EXPORT ?>'></div>

</form>

 <br>
<div id="output" style="display: flex; align-items: center; gap: 20px;">
</div>
<?php
?>
<script src="../template/syzoj/jquery.min.js"></script>
<script>
function exportTableToCSV() {
    // 1. 获取目标表格
    const $table = $('#result');
    if ($table.length === 0) {
        alert('未找到 id 为 result 的表格');
        return;
    }

    // 2. 遍历每一行，提取 th/td 的文本内容
    const rows = [];
    $table.find('tr').each(function () {
        const cells = [];
        $(this).find('th, td').each(function () {
            // 获取文本并去除首尾空白
            let text = $(this).text().trim();
            // 如果文本包含逗号、双引号或换行，需要用双引号包裹，并转义内部双引号
            if (text.includes(',') || text.includes('"') || text.includes('\n')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            cells.push(text);
        });
        // 跳过完全空白的行（例如只有空白单元格的行）
        if (cells.length > 0) {
            rows.push(cells.join(','));
        }
    });

    // 如果没有任何数据
    if (rows.length === 0) {
        alert('表格中没有可导出的数据');
        return;
    }

    // 3. 拼接为 CSV 字符串
    const csvString = rows.join('\n');

    // 4. 创建 Blob 并触发下载
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'table_data.csv';          // 可自定义下载文件名
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // 5. 释放内存中的 URL 对象
    URL.revokeObjectURL(url);
}

</script>
