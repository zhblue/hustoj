<?php $show_title="高考志愿 - ".htmlentities($special);
	$OJ_BG="";
	$_GET['spa']=1;
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<?php include(dirname(__FILE__)."/css.php");?>
<style>
        td {
            font-size: 20px; /* 默认大小 */
            transition: font-size 0.3s; /* 添加过渡效果，使变化更平滑 */
        }
        td:hover {
            font-size: 20px; /* 悬停时的大小 */
        }
    /* 全局样式 */
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    /* 表格样式 */
    #dataTable {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 20px;
    }

    #dataTable th, #dataTable td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }

    #dataTable th {
      background-color: #f2f2f2;
      position: sticky;
      top: 0;
    }

    #dataTable tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    #dataTable tr:hover {
      background-color: #fffff1;
    }

    .details-btn {
      padding: 5px 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .details-btn:hover {
      background-color: #45a049;
    }

    /* 对话框样式 */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.5);
      display: none;
      z-index: 1000;
    }

    .modal-dialog {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      z-index: 1001;
      width: 80%;
      max-width: 800px;
      max-height: 80vh;
      overflow-y: auto;
      font-size: 18px;
      line-height:24px;
    }

    .modal-header {
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }

    .modal-header h3 {
      margin-top: 0;
      color: #333;
    }

    .modal-body {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .info-group {
      margin-bottom: 10px;
    }

    .info-group strong {
      display: inline-block;
      min-width: 100px;
      color: #555;
    }

    .close-btn {
      padding: 8px 16px;
      background-color: #f44336;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      float: right;
      margin-top: 15px;
    }

    .close-btn:hover {
      background-color: #d32f2f;
    }

    /* 响应式调整 */
    @media (max-width: 768px) {
      .modal-body {
        grid-template-columns: 1fr;
      }

      .modal-dialog {
        width: 95%;
      }
    }
    </style>
<?php

function printTable($first_table){
	echo "<table id='dataTable' class='table striped'  style='overflow:auto' >";
	echo "<tr><th>序号<th>学校代码<th>学校名称<th>专业代码<th>专业名称<th>23计划数<th>24计划数<th>25计划数<th>23分数线<th>24分数线<th>25分数线<th>23位次<th>24位次<th>25位次(0表示未招满)<th>办学性质<th>本专科<th>专业限制<th>25选科<th>学费<th>课程<th>就业<th>城市<th>图示</th><th>必应搜索";
	$c=0;
	foreach( $first_table as $row){
		echo "<tr>";
		$c++;
		$i=0;
		foreach($row as $col){
			$i++;
			if($i%2) continue;
			echo "<td";
			if (mb_strlen($col)>20) echo " title='". htmlentities($col)."'";
			echo ">";
			$span='';
			if($i==1) $col=$c;
			if($i==11){
				if($last>0){
					if($last>$col){
						echo "<span class='green'>";
						$span='<i class="arrow down icon" /></span>';
					}else if($last<$col){
						echo "<span class='red'>";
						$span='<i class="arrow up icon" /></span>';
					}
				}
			}else if($i==8||$i==10||$i==13||$i==14){
				if($last>0&&$col>0){
					if($last>$col){
						echo "<span class='red'>";
						$span='<i class="arrow up icon" /></span>';
					}else if($last<$col){
						echo "<span class='green'>";
						$span='<i class="arrow down icon" /></span>';
					}
				}
				if($last>0&&$col==0){
						echo "<span class='green' title='未招满'>";
						$span='<i class="arrow down icon" /></span>';
				
				}
			}
			if($i==3) echo "<a href='zj26.php?school=".htmlentities($col)."'>";
			if($i==5) echo "<a href='zj26.php?special=".htmlentities($col)."'>";
			if (mb_strlen($col)>25 && !isset($_GET['down'])) $col=mb_substr($col,0,1)."...";
			echo htmlentities($col);
			if($i==3||$i==5) echo "</a>";
			if($i==5&&!isset($_GET['down'])) echo "&nbsp;<button class='details-btn' >详细</button>";
			if(!empty($span)&&($i==12||$i==12)&&$col>0){
				echo "&nbsp;[".(intval(($last-$col)/$last*10000)/100)."%]";
				
			}
			echo "$span";
			echo "</td>";
			$last=$col;
		}
		echo "<td class='canvas-cell'></td><td><a class='ui green button' href='https://cn.bing.com/search?q=".htmlentities( $row['院校名称']." ".$row["专业名称"].'专业介绍' )."' target='_blank'>搜索</td>";
		echo "</tr>";
	}
	echo "</table>";
}
$choices=array("不限"
,"物理&化学"
,"物理"
,"历史"
,"政治"
,"地理"
,"化学"
,"生物"
,"物理&生物"
,"政治&历史"
,"物理&化学&生物"
,"物理&地理"
,"生物&地理"
,"政治&历史&地理"
,"化学&生物"
,"物理&政治"
,"化学&地理"
,"历史&地理"
,"物理&地理&技术"
,"物理&生物&技术"
,"物理&生物&地理"
,"政治&历史&技术"
,"物理&政治&技术"
,"物理&化学&技术"
,"物理&技术"
,"物理&生物&政治"
,"生物&政治&历史"
);
?>
<div class="padding">
<?php if(!isset($_GET['down'])){?>
    <h1 style="float:left:width=100%">2025年浙江高考普通类分数线与位次速查  ……  本页数据来自互联网，仅供参考
	
  	<form id=simform class="ui form" action="zj26.php" method="get"  style="float:left">
	学校<input class="ui input" name="school" style='width:250px;' value='<?php echo htmlentities($school)?>'  placeholder='学校关键词或全称'>
<br>
		专业<input class="ui input" name="special" style='width:450px;' value='<?php echo htmlentities($special)?>'  placeholder='专业关键词或全称,可空格分隔多个'> 
<br>
		分数
		<input class="ui input" name="min_mark" style='width:250px;' value='<?php echo intval($min_mark)>0? intval($min_mark):"" ?>'  placeholder='最低分数'>
		~
		<input class="ui input" name="max_mark" style='width:250px;'  value='<?php echo intval($max_mark)>0? intval($max_mark):""  ?>'  placeholder='最高分数'>
<br>
	        位次	
		<input class="ui input" name="min_rank" style='width:250px;'  value='<?php echo intval($min_rank)>0?intval($min_rank):"" ?>'  placeholder='最前位次'>
		~
		<input class="ui input" name="max_rank" style='width:250px;'  value='<?php echo intval($max_rank)>0?intval($max_rank):"" ?>'  placeholder='最后位次'>
	<br>	
<div class="inline fields">
	性质<select class="ui input" name="school_type" style="width:150px">
			<option value="">全部</option>
			<option value="公办" <?php if($_GET['school_type']=="公办") echo "selected"?>>公办</option>
			<option value="民办" <?php if($_GET['school_type']=="民办") echo "selected"?>>民办</option>
		</select>
	本专科<select class="ui input" name="special_type" style="width:150px">
			<option value="">全部</option>
			<option value="本科" <?php if($_GET['special_type']=="本科") echo "selected"?>>本科</option>
			<option value="专科" <?php if($_GET['special_type']=="专科") echo "selected"?>>专科</option>
		</select>
	选科<select class="ui input" name="choose25" style="width:150px">
			<option value="">全部</option>
			<?php foreach($choices as $choice){ ?>
				<option value="<?php echo $choice?>"  <?php if($_GET['choose25']==$choice) echo "selected"?>><?php echo $choice ?></option>
			<?php } ?>
		</select>
</div>
		<input class="ui primary button" onclick="$('form').submit()" type="submit" value='提交查询' >
		<a class="ui green button " href="<?php echo htmlentities($_SERVER["REQUEST_URI"]."&down&spa" ) ?>" >下载Excel文件</a>
	</form>
	<img width="300" style="float:right" src=http://zjicm.hustoj.com/upload/zjicm.hustoj.com/image/20250306/20250306095438_40564.jpg >
	<h3 style="float:right"> <span class=red >截屏扫码，志愿不求人！</span><br> 本页地址 zj.hustoj.com</h3></h1>
	<div style="float:right" ><img src="qrcode.php"><br>扫描二维码在手机上查看结果, 拍照截图分享给朋友。<h2 class='blue' style='float:right' > <br>[<span id="c"><?php echo $count ?></span> 人查过]……………………</h2>
</div>
<?php } ?>
    <div class="ui existing segment" style="width:100%;overflow:auto;">
	<div id="content" class="font-content">
	<?php
		printTable($first_table);
	?>
	</div>
    </div>
</div>
<!-- 对话框结构 -->
<div class="modal-overlay">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 id="dialog-title">详细信息</h3>
    </div>
    <div class="modal-body" id="dialog-content">
      <!-- 内容将通过jQuery动态填充 -->
    </div>
    <button class="close-btn">关闭</button>
  </div>
</div>

<script>
$(function() {
  // 获取表头信息
  var headers = [];
  $('#dataTable th').each(function() {
    // 获取th的文本和title属性
    var headerText = $(this).text().trim();
    var headerTitle = $(this).attr('title') || '';

    // 存储header信息
    headers.push({
      text: headerText,
      title: headerTitle
    });
  });
  
  // 为每个详情按钮绑定点击事件
  $(".details-btn").click(function() {
    // 获取当前行
    var $row = $(this).closest("tr");

    // 存储行数据和所有title提示
    var rowData = [];
    var allTitles = [];
    var count=0;
    // 遍历行中的每个单元格
    $($row).find("td").each(function(index) {
      // 获取单元格文本和title属性
      var cellText = $(this).text().trim();
      var cellTitle = $(this).attr('title');
      count++;
      if(count>6) cellText = $(this).html();
      // 存储数据
      if (index < headers.length - 1) { // 排除操作列
        rowData.push(cellText);

        // 如果有title，记录下来
        if (cellTitle) {
          allTitles.push({
            header: headers[index].text,
            title: cellTitle
          });
        }
      }
    });

    // 设置对话框标题
    var schoolName = rowData[2]; // 学校名称索引
    var majorName = rowData[4];  // 专业名称索引
    $("#dialog-title").text(schoolName + " - " + majorName);

    // 构建对话框内容 - 主要信息
    var content = '';
    for (var i = 0; i < rowData.length; i++) {
      // 跳过空列
      if (!headers[i] || !rowData[i]) continue;

      content += `
        <div class="info-group">
          <strong>${headers[i].text}：</strong>
          <span>${rowData[i] || '无'}</span>
          ${headers[i].title ? `<br><small style="color:#666;">${headers[i].title}</small>` : ''}
        </div>
      `;
    }

    // 添加所有title提示信息
    if (allTitles.length > 0) {
      content += `<div class="info-group" style="grid-column: 1 / -1; border-top: 1px solid #eee; padding-top: 10px;">
        <strong>附加说明：</strong><ul style="margin:5px 0 0 0; padding-left:20px;">`;

      allTitles.forEach(function(item) {
        content += `<li><strong>${item.header}：</strong>${item.title}</li>`;
      });

      content += `</ul></div>`;
    }

    // 填充对话框内容
    $("#dialog-content").html(content);

    // 显示对话框
    $(".modal-overlay").fadeIn();
  });

  // 关闭对话框
  $(".close-btn, .modal-overlay").click(function(e) {
    if ($(e.target).hasClass('modal-overlay') || $(e.target).hasClass('close-btn')) {
      $(".modal-overlay").fadeOut();
    }
  });

  // 阻止点击对话框内容时关闭
  $(".modal-dialog").click(function(e) {
    e.stopPropagation();
  });
});
</script>
 <script>
        $(document).ready(function() {
            // 遍历表格的每一行
	    window.setInterval("$('#c').load('zj26.php?c')",1000);
            $('#dataTable tbody tr').each(function() {
                // 获取第6列和第7列的数据
                let col5 = parseFloat($(this).find('td:eq(11)').text().replace(/[^\d.]/g, ''));
                let col6 = parseFloat($(this).find('td:eq(12)').text().replace(/\[.*\]/g, ''));
                let col7 = parseFloat($(this).find('td:eq(13)').text().replace(/\[.*\]/g, ''));
               	if (col6==0 || col7==0) return;
                // 创建canvas元素
                const canvas = document.createElement('canvas');
                canvas.width = 100;
                canvas.height = 50;
                
                // 获取canvas上下文
                const ctx = canvas.getContext('2d');
                
                // 确定最大值用于缩放
                const maxVal = Math.max(col5,col6, col7) * 1.1;
                
                // 计算Y坐标（canvas的Y轴是从上往下的）
                let y0 =  (col5 / maxVal) * canvas.height;
                let y1 =  (col6 / maxVal) * canvas.height;
                let y2 =  (col7 / maxVal) * canvas.height;
                
                // 设置线条样式（根据增长或下降选择不同颜色）
                if (col7 >= col6) {
                    ctx.strokeStyle = '#4CAF50'; // 增长为绿色
                    ctx.fillStyle = '#4CAF50';
                } else {
                    ctx.strokeStyle = '#F44336'; // 下降为红色
                    ctx.fillStyle = '#F44336';
                }
                
                ctx.lineWidth = 2;
                
                // 绘制折线
                ctx.beginPath();
                ctx.moveTo(0, y0);
                ctx.lineTo(canvas.width/2, y1);
                ctx.lineTo(canvas.width, y2);
                ctx.stroke();
                
                // 绘制数据点
                ctx.beginPath();
                ctx.arc(0, y0, 3, 0, Math.PI * 2);
                ctx.arc(canvas.width/2, y1, 3, 0, Math.PI * 2);
                ctx.arc(canvas.width, y2, 3, 0, Math.PI * 2);
                //ctx.fill();
                
                // 添加数值标签
                ctx.font = '10px Arial';
                ctx.fillStyle = '#333';
                ctx.fillText((col5), 5, y0 - 5);
                ctx.fillText((col6), canvas.width/2 - 10, y1 - 5);
                ctx.fillText((col7), canvas.width - 30, y2 - 5);
                
                // 将canvas添加到第7列后的单元格
                $(this).find('.canvas-cell').html(canvas);
            });
        });
    </script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
