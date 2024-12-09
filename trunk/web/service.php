<?php
header("HTTP/1.0 404 Not Found");
echo "接口未启用";
exit();
// 注释以上代码启用接口，注意这可能是不安全的，仅用于内部调用。
// Press F12 to watch network payload in Devtab,if you don't know what to do, you need to learn more before using this page.
require_once ("include/db_info.inc.php");
require_once ("include/my_func.inc.php");
ini_set("display_errors", "Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
function login($m){
                // 拥有service_port权限的账号才能登陆接口
                global $OJ_NAME,$_SESSION;
                $sql="SELECT `user_id`,`password` FROM `users` WHERE `user_id`=? and defunct='N' and expiry_date >= curdate() ";
                $result=pdo_query($sql,$m->user_id);
                if(is_array($result)&&count($result)==1){
                        $row = $result[0];
                        if( pwCheck($m->password,$row['password'])){
                                $sql="select * from privilege where rightstr=? and user_id=? ";
                                $result=pdo_query($sql,"service_port",$m->user_id);
                                if(is_array($result)&&count($result)==1){
                                        $_SESSION[$OJ_NAME."_service_port"]=true;
                                        return array("login"=>"ok");
                                }
                        }
                }
                return array("login"=>"fail");
}
function submit($m){
        global $ip;
        $problem_id=$m->problem_id;
        $language=$m->language;
        $source=$m->source;
        $len=strlen($source);
        $sql = "INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,result) VALUES(?,?,?,NOW(),?,?,?,14)";
        $insert_id = pdo_query($sql, $problem_id,"guest","test", $language, $ip, $len);
        if($insert_id>0){

                $sql = "INSERT INTO `source_code`(`solution_id`,`source`) VALUES(?,?)";
                pdo_query($sql, $insert_id, $source);

                if ($problem_id==0) {
                    $sql = "INSERT INTO `custominput`(`solution_id`,`input_text`) VALUES(?,?)";
                    pdo_query($sql, $insert_id, $m->input_text);
                }
                $sql = "update solution set result=0 where solution_id=?";
                pdo_query($sql, $insert_id);
                trigger_judge($insert_id);     // moved to my_func.inc.php
                return array("solution_id"=>$insert_id);
        }else{
                return array("error"=>"internal error ","debug"=>$m);
        }
}
function query($m){
        $solution_id=$m->solution_id;
        $sql="select * from solution where solution_id=?";
        $ret=pdo_query($sql,$solution_id);
        if(is_array($ret)&&isset($ret[0])){
                return $ret[0];
        }else{
                if(is_array($ret)&&!isset($ret[0]))
                        return array("error"=>"not found","debug"=>$m);
                else
                        return array("error"=>"internal error ","debug"=>$m);
        }
}
function ce($m){
        $solution_id=$m->solution_id;
        $sql="select * from compileinfo where solution_id=?";
        $ret=pdo_query($sql,$solution_id);
        if(is_array($ret)&&isset($ret[0])){
                return $ret[0];
        }else{
                if(is_array($ret)&&!isset($ret[0]))
                        return array("error"=>"not found","debug"=>$m);
                else
                        return array("error"=>"internal error ","debug"=>$m);
        }
}
function re($m){
        $solution_id=$m->solution_id;
        $sql="select * from runtimeinfo where solution_id=?";
        $ret=pdo_query($sql,$solution_id);
        if(is_array($ret)&&isset($ret[0])){
                return $ret[0];
        }else{
                if(is_array($ret)&&!isset($ret[0]))
                        return array("error"=>"not found","request"=>$m);
                else
                        return array("error"=>"internal error ","request"=>$m);
        }
}
if(isset($_POST['m'])){
        $m=$_POST['m'];
        $m=json_decode($m);
        $ret=array("error"=>"not implemented yet ");
        if($m->action=="login"){
                $ret=login($m);
        }else if (isset($_SESSION[$OJ_NAME."_service_port"])){
                switch($m->action){
                        case "submit":
                                $ret=submit($m);
                                break;
                        case "query":
                                $ret=query($m);
                                break;
                        case "ce":
                                $ret=ce($m);
                                break;
                        case "re":
                                $ret=re($m);
                                break;
                        default :
                                $ret["request"]=$m;
                }
        }
        echo json_encode($ret);
}else if (isset($_SESSION[$OJ_NAME."_service_port"])){
?>
请使用F12调试这个接口的示例，帮助理解如何开发对接。如果看不懂这些东西如何使用，也许您不适合使用这个接口，请删除本文件。
Press F12 to watch network payload in Devtab,if you don't know what to do, you need to learn more before using this page.
        <form action="service.php" method="POST">
                submit a solution to a problem
                <textarea name=m rows=5 cols=80 >
{
   "action":"submit",
   "problem_id":1000,
   "language":1,
   "source":"#include <stdio.h>\nint main(){\n    int a, b;\n    while(scanf(\"%d %d\",&a, &b) != EOF){\n        printf(\"%d\\n\", a + b);\n    }\n    return 0;\n}"
}
                </textarea>
                <input type=submit >
        </form>
        <form action="service.php" method="POST">
                test run
                <textarea name=m rows=5 cols=80 >
{
   "action":"submit",
   "problem_id":0,
   "input_text":"1 2",
   "language":1,
   "source":"#include <stdio.h>\nint main(){\n    int a, b;\n    while(scanf(\"%d %d\",&a, &b) != EOF){\n        printf(\"%d\\n\", a + b);\n    }\n    return 0;\n}"
}
                </textarea>
                <input type=submit >
        </form>
        <form action="service.php" method="POST">
        query result of a solution
                <textarea name=m rows=5 cols=80 >
{
   "action":"query",
   "solution_id":1001
}
                </textarea>
                <input type=submit >
        </form>
        <form action="service.php" method="POST">
        query compile error of a solution
                <textarea name=m rows=5 cols=80 >
{
   "action":"ce",
   "solution_id":1001
}
                </textarea>
                <input type=submit >
        </form>
        <form action="service.php" method="POST">
        query runtime error of a solution
                <textarea name=m rows=5 cols=80 >
{
   "action":"re",
   "solution_id":1001
}
                </textarea>
                <input type=submit >
        </form>
<?php
        }else{
?>
        <form action="service.php" method="POST">
                login the service port
                <textarea name=m rows=5 cols=80 >
{
   "action":"login",
   "user_id":"service",
   "password":"123456"
}
                </textarea>
                <input type=submit >
        </form>


<?php

}
