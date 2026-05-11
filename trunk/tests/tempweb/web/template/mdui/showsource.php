<!DOCTYPE html>
<html lang="en">

<head>
    <?php $page_title = "代码查看"; ?>
    <?php include('_includes/head.php') ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <h1>代码</h1>
        <div class="jumbotron">
            <?php
                if ($ok==true){
                if($view_user_id!=$_SESSION[$OJ_NAME.'_'.'user_id'])
                echo "<a href='mail.php?to_user=".htmlentities($view_user_id,ENT_QUOTES,"UTF-8")."&title=$MSG_SUBMIT $id'>Mail the author</a>";
                $brush=strtolower($language_name[$slanguage]);
                if ($brush=='pascal') $brush='delphi';
                if ($brush=='obj-c') $brush='c';
                if ($brush=='c++') $brush='cpp';
                if ($brush=='freebasic') $brush='vb';
                if ($brush=='fortran') $brush='vb';
                if ($brush=='swift') $brush='csharp';
                echo "<pre><code class=\"language-".$brush.";\">";
                ob_start();
                echo "/**************************************************************\n";
                echo "\tProblem: $sproblem_id\n\tUser: $suser_id\n";
                echo "\tLanguage: ".$language_name[$slanguage]."\n\tResult: ".$judge_result[$sresult]."\n";
                if ($sresult==4){
                echo "\tTime:".$stime." ms\n";
                echo "\tMemory:".$smemory." kb\n";
                }
                echo "****************************************************************/\n\n";
                $auth=ob_get_contents();
                ob_end_clean();
                echo htmlentities(str_replace("\n\r","\n",$view_source),ENT_QUOTES,"utf-8")."\n".$auth."</code></pre>";
                } else {
                    echo $MSG_WARNING_ACCESS_DENIED ;
                }
            ?>
        </div>

    </div>
</body>

</html>