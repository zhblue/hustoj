<?php $show_title="$MSG_RECENT_CONTEST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>


    <div class="padding">

    <table class="ui very basic center aligned table">
      <thead>
        <tr>
        <th>OJ</th>
        <th><?php echo $MSG_CONTEST_NAME ?></th>
        <th><?php echo $MSG_START_TIME ?></th>
        <th>星期</th>
        <th><?php echo $MSG_CONTEST_OPEN ?></th>
        </tr>
      </thead>
        <tbody id="contest-list">


        </tbody>
    </table>
    <div>数据来源：<a href="https://algcontest.rainng.com/contests.json" target="_blank">https://algcontest.rainng.com/contests.json</a>&nbsp;&nbsp;&nbsp;&nbsp;作者：<a href="https://www.rainng.com/"  target="_blank" >雨凝</a></div>
    </div>
        <script>
                var contestList = $("#contest-list");
                $.get("https://algcontest.rainng.com/contests.json",function(response){
                        response.map(function(val){
                                var item = "<tr><td class='column-1'>"+val.oj+"</td>"+
                                        "<td class='column-2'><a target='_blank' href='"+val.link+"'>"+val.name+"</a></td>"+
                                        "<td class='column-3'>"+val.start_time+"</td>"+
                                        "<td class='column-4'>"+val.week+"</td>"+
                                        "<td class='column-5'>"+val.access+"</td></tr>"
                                contestList.append(item);
                        });
                });
        </script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
