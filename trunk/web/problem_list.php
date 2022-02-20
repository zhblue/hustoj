<?php
////////////////////////////Common head
        $cache_time=10;
        $OJ_CACHE_SHARE=false;
        require_once('./include/cache_start.php');
    require_once('./include/db_info.inc.php');
        require_once('./include/setlang.php');
        $view_title= "Welcome To Online Judge";

/////////////////////////Template
if(file_exists("template/".$OJ_TEMPLATE."/c++_template.$OJ_LANG.php"))
        require("template/".$OJ_TEMPLATE."/c++_template.$OJ_LANG.php");
else
        require("template/".$OJ_TEMPLATE."/problem_list.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
        require_once('./include/cache_end.php');
?>


