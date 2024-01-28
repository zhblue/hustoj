<?php $show_title="近期比赛 - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>


    <div class="padding">

    <table class="ui very basic center aligned table">
      <thead>
        <tr>
        <th>OJ</th>
        <th>比赛名称</th>
        <th>开始时间</th>
        <th>星期</th>
        <th>Access</th>
        </tr>
      </thead>
        <tbody id="contest-list">


        </tbody>
    </table>
    <div>数据来源：<a href="http://contests.acmicpc.info/contests.json" target="_blank">http://contests.acmicpc.info/contests.json</a>&nbsp;&nbsp;&nbsp;&nbsp;作者：<a href="http://contests.acmicpc.info"  target="_blank" >doraemonok</a></div>
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
