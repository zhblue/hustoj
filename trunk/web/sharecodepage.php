<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once("./include/memcache.php");
 
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$title = "";
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

// 当前用户的user_id
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];

/************************  数据库  *******************************/

/**
 * 存储用户分享的代码
 * @param $title
 * @param $code
 * @param $user_id
 * @param $language
 * @return mixed
 */
function saveShareCode($title,$code,$language,$user_id){
    $sql = "INSERT INTO share_code(`user_id`,`title`,`share_code`,`language`,`share_time`) VALUES (?,?,?,?,NOW());";
    $rows = pdo_query($sql, $user_id, $title, $code, $language);
    return $rows;
}

/**
 * 查询某一sid 的详情
 * @param $OJ_MEMCACHE
 * @param $sid
 * @return mixed
 */
function getShareCodeBySid($OJ_MEMCACHE,$sid){
    if ($OJ_MEMCACHE) {
        $sql = "SELECT * FROM share_code where share_id = '$sid';";
         $share_info = mysql_query_cache($sql);
    } else {
        $sql = "SELECT * FROM share_code where share_id = ?";
        $share_info = pdo_query($sql, $sid);
    }
    return $share_info;
}

/**
 * 验证某个share_id 的 代码 是不是某个用户的
 * @param $sid
 * @param $user_id
 * @return bool
 */
function checkCodeOwner($sid, $user_id){
    $sql = "SELECT share_id FROM share_code WHERE user_id = ? AND share_id = ?";
    $result = pdo_query($sql, $user_id, $sid);
    if(count($result)==0){
        return false;
    }else {
        return true;
    }
}

/**
 * 删除某一个sid的代码
 * @param $sid
 * @param $user_id
 */
function deleteShareCodeBySid($sid, $user_id){
    $sql = "DELETE FROM share_code WHERE user_id = ? AND share_id = ?";
    pdo_query($sql, $user_id, $sid);
}

/**
 * 更新某一sid 的代码
 * @param $sid
 * @param $code
 * @param $language
 * @param $user_id
 * @return mixed
 */
function updateShareCodeBySid($sid, $title,$code, $language, $user_id) {
    $sql = "UPDATE share_code SET title=?,share_code = ?,`language` = ? WHERE share_id = ? AND user_id = ?";
    pdo_query($sql,$title, $code, $language, $sid, $user_id);
    return $sid;
}


/*****************************  路由 *******************************/
// 如果有比赛正在进行，则不可用
if(
    (isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
    (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)||
    (isset($OJ_SHARE_CODE)&&!$OJ_SHARE_CODE)
){
    $view_errors= "代码分享功能当前不可用！";
    require("template/".$OJ_TEMPLATE."/error.php");
    exit (0);
}


// 删除某一个分享的代码
if(isset($_POST['delete'])) {
    $sid = $_POST['delete'];
    header('Content-Type:application/json');
    if (!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
        echo '{
        "status": "error",
        "msg": "请先登录！"
       }';
    }else {
        if(!checkCodeOwner($sid, $user_id)){
            echo '{
            "status": "error",
            "msg": "您没有权限删除！"
           }';
        }else {
            deleteShareCodeBySid($sid, $user_id);
            echo '{
            "status": "success",
            "msg": "删除成功！"
           }';
        }
    }
    exit(0);
}


// 查看分享的代码
if(isset($_GET['sid'])){
    // share_id
    $sid = intval($_GET['sid']);
    $share_info = getShareCodeBySid($OJ_MEMCACHE,$sid);
    // 代码不存在
    if(count($share_info)==0){
        $view_errors= "该代码不存在！";
        require("template/".$OJ_TEMPLATE."/error.php");
    }else {
        //echo array_column($share_info,"user_id")[0] ;
        $sid = array_column($share_info,"share_id")[0];
        $author = array_column($share_info,"user_id")[0];
        $title = array_column($share_info,"title")[0];
        $language = array_column($share_info,"language")[0];
        $share_time = array_column($share_info,"share_time")[0];
        $view_src = array_column($share_info,"share_code")[0];
        if($author==$user_id){
            $readOnly = false;
            $isOwner = true;
        }else {
            $readOnly = true;
        }
        require("template/" . $OJ_TEMPLATE . "/sharecodepage.php");
    }
    exit(0);
}

// 存储提交代码，包括新提交以及修改提交
if(isset($_POST['code'])) {
    if (!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
        $view_errors= "<a href=loginpage.php>$MSG_Login</a>";
        require("template/".$OJ_TEMPLATE."/error.php");
    }else {
        header('Content-Type:application/json');

        $vcode = $_POST['vcode'];
        if($_SESSION[$OJ_NAME.'_'."vcode"]==null||$vcode!= $_SESSION[$OJ_NAME.'_'."vcode"]||$vcode==""||$vcode==null){
            $_SESSION[$OJ_NAME.'_'."vcode"]=null;
            echo '{
            "status": "error",
            "msg": "验证码错误"
           }';
        }else {
            if(isset($_POST['sid'])) {
                $sid = $_POST['sid'];
            }
            $title = $_POST['title'];
            $code = $_POST['code'];
            $language = $_POST['language'];
            try {
                if(isset($_POST['sid'])) {
                    $new_id = updateShareCodeBySid($sid,$title, $code, $language, $user_id);
                }else {
                    $new_id = saveShareCode($title,$code, $language, $user_id);
                }

            }catch (Exception $e) {
                echo '{
                "status": "error",
                "msg": "系统繁忙"
               }';
                exit(0);
            }

            echo '{
            "status": "success",
            "msg": "添加成功！",
            "data": {
              "sid":'.$new_id.'
            }
           }';
        }
    }
    exit(0);
}

// 请求未带参数，直接进入代码提交页面
if (empty($_GET)) {
    if (!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
        $view_errors= "<a href=loginpage.php>$MSG_Login</a>";
        require("template/".$OJ_TEMPLATE."/error.php");
    }else {
        $readOnly = false;
        require("template/" . $OJ_TEMPLATE . "/sharecodepage.php");
    }
    exit(0);
}






// json 请求时
if (isset($_GET['type'])&&$_GET['type']=='json') {
    header('Content-Type:application/json');
    if(isset($_GET['list'])&&$_GET['list']=='submit'){

        $subList = getSubmitByCid($OJ_MEMCACHE,$cid);
        $problemMap = getProblemMapByCid($OJ_MEMCACHE,$cid);

        $arr = array();
        for($i=0;$i<count($subList);$i++){
            $arr[$i] = submit2Array($subList[$i],$problemMap);
        }
        echo json_encode($arr);

    }else if(isset($_GET['list'])&&$_GET['list']=='team'){
        $teamList = getTeamByCid($OJ_MEMCACHE,$cid);
        $arr = array();
        for($i=0;$i<count($teamList);$i++){
            $arr[$i] = team2Array($teamList[$i]);
        }
        echo json_encode($arr);
    }
    exit(0);
}

/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>


