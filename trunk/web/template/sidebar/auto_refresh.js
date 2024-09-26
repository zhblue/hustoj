var i = 0;
var interval = 800;

function auto_refresh() {
        interval *=2 ;
        var tb = window.document.getElementById('result-tab');
        var rows = tb.rows;
        var i=rows.length-1;
        for (; i>0; i--) {
                var result = $(rows[i].cells[4].children[0]).attr("result");
                result=$(rows[i].cells[4]).find("span").attr("result");
                rows[i].cells[4].className = "td_result";
                var sid = $(rows[i].cells[0]).text();

                if (result<4||result>14) {
                        window.setTimeout("fresh_result("+sid+")",interval);
                        console.log("auto_refresh "+sid+" actived!");
                        break;
                }
        }
        if(i==0){
                console.log("no more");
                $("iframe").remove();   // 
        }

}

function findRow(solution_id) {
        var tb = window.document.getElementById('result-tab');
        var rows = tb.rows;
        for (var i=1; i<rows.length; i++) {
                var cell = rows[i].cells[0];
                if ($(cell).text()==solution_id)
                        return rows[i];
        }
}
function fancy(td){
        console.log(user_id+":"+fancy_mp3);
        $(td).html("<div id='bannerFancy' style='position:absolute;top:0px;left:0px;width:100%' class='ui main container'></div><audio autoplay=\"autoplay\" preload=\"auto\" src='"+fancy_mp3+"'></audio>");
        window.setTimeout("$(\"#bannerFancy\").html(\"<iframe border=0 src='fancy.php' width='100%' height='800px'></iframe>\");",500);
        window.setTimeout("$(\"#bannerFancy\").remove();",5000);
}
function fresh_result(solution_id) {
        var xmlhttp;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        var tb = window.document.getElementById('result-tab');
                        var row = findRow(solution_id);
                        //alert(row);
                        var r = xmlhttp.responseText;
                        var ra = r.split(",");
                        ra[0] = parseInt(ra[0]);
                        // alert(r);
                        // alert(judge_result[r]);
                        var loader = "<img width=18 src=image/loader.gif>";
                        row.cells[5].innerHTML = ra[1];
                        row.cells[6].innerHTML = ra[2];

                         if(ra[3] != undefined && row.cells[10] != undefined)
                                row.cells[10].innerHTML = ra[3];

                        if (ra[0]<4) {
                                //console.log(loader);
                                if (-1==row.cells[4].innerHTML.indexOf("loader")) {
                                        //console.log(row.cells[3].innerHTML);
                                        row.cells[4].innerHTML += loader;
                                }
                                interval *= 1.5;
                                window.setTimeout("fresh_result("+solution_id+")",interval);
                        }
                        else {
                                //console.log(ra[0]);
                                switch (ra[0]) {
                                        case  4:
                                        case 14:
                                                if(user_id==ra[5]){
                                                        fancy(row.cells[4]);
                                                        row.cells[4].innerHTML += "<a href=reinfo.php?sid="+solution_id+" class='"+judge_color[ra[0]]+"'>"+judge_result[ra[0]]+"</a>";
                                                }else{
                                                        row.cells[4].innerHTML = "<a href=reinfo.php?sid="+solution_id+" class='"+judge_color[ra[0]]+"'>"+judge_result[ra[0]]+"</a>";
                                                }
                                                break;
                                        case 5:
                                        case 6:
                                  case 7:
                                  case 8:
                                  case 9:
                                  case 10:
                                                row.cells[4].innerHTML = "<a href=reinfo.php?sid="+solution_id+" class='"+judge_color[ra[0]]+"'>"+judge_result[ra[0]]+" :"+(oj_mark=='percent'?100-ra[4].trim():ra[4].trim())+"%</a>";
                                                break;
                                        case 11:
                                                row.cells[4].innerHTML = "<a href=ceinfo.php?sid="+solution_id+" class='"+judge_color[ra[0]]+"'>"+judge_result[ra[0]]+"</a>";
                                                break;
                                  default:
//                                              row.cells[4].innerHTML = "<span class='"+judge_color[ra[0]]+"'>"+judge_result[ra[0]]+" AC:"+ra[4].trim()+"%</span>";
                                }
                                $(row.cells[4].children[0]).attr("result",ra[0]);
                                auto_refresh();
                        }
                }
        }
        xmlhttp.open("GET","status-ajax.php?solution_id="+solution_id,true);
        xmlhttp.send();
}

var hj_ss = "<select class='http_judge form-control' length='2' name='result'>";

for (var i=0; i<10; i++) {
  if(i==3 || i==4 ) continue;
  hj_ss += "    <option value='"+i+"'>"+judge_result[i]+" </option>";
}

hj_ss += "</select>";
hj_ss += "<input name='manual' type='hidden'>";
hj_ss += "<input class='http_judge form-control' size=5 title='Explaination' name='explain' type='text'>";
hj_ss += "<input type='button' class='http_judge' name='manual' value='Submit' onclick='http_judge(this)' >";

$(".http_judge_form").append(hj_ss);

window.setTimeout("auto_refresh();",1000);

$(".td_result").mouseover(function () {
  //$(this).children(".label").hide(300);
  $(this).find("form").show(600);
  var sid = $(this).find("span[class=original]").attr("sid");
  $(this).find("span[class=original]").load("status-ajax.php?q=user_id&solution_id="+sid);
});

$(".http_judge_form").hide();

function http_judge(btn) {
  var sid = $(btn).parent()[0].children[0].value;
  $.post("admin/problem_judge.php",$(btn).parent().serialize(),function(data,textStatus) {
    if(textStatus=="success")window.setTimeout("fresh_result("+sid+")",1000);
        })
  return false;
}
