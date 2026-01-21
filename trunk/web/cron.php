<?php
        chdir(dirname(__FILE__).'/aiapi');
        require_once("cron.php");

        //触发Remote judge模块
        chdir(dirname(__FILE__));
        if( isset($OJ_REMOTE_JUDGE)&&$OJ_REMOTE_JUDGE ){
               touch("remote.php");
               require_once("include/remote_bas.php");
        }
