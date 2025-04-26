<?php
     require_once(dirname(__FILE__)."/db_info.inc.php");
        //cache head start
        if(!isset($cache_time)) $cache_time=10;
        $sid=$OJ_NAME.$_SERVER["HTTP_HOST"];
        $OJ_CACHE_SHARE=(isset($OJ_CACHE_SHARE)&&$OJ_CACHE_SHARE)&&!isset($_SESSION[$OJ_NAME.'_'.'administrator']);
        if (!$OJ_CACHE_SHARE&&isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
                $ip = ($_SERVER['REMOTE_ADDR']);
                if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
                    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    $tmp_ip=explode(',',$REMOTE_ADDR);
                    $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
                }
                $sid.=session_id().$ip;
        }
        if (isset($_SERVER["REQUEST_URI"])){
                $sid.=$_SERVER["REQUEST_URI"];
        }

        $sid=md5($sid);
        $cache_file = "cache/cache_$sid.html";
        if($OJ_MEMCACHE){
                    $success = false;
                    if(extension_loaded('apcu')&&apcu_enabled()){
                            $content = apcu_fetch($cache_file, $success);
                    }else{
                            $mem = new Memcache;
                            $mem->connect($OJ_MEMSERVER,  $OJ_MEMPORT);
                            $content=$mem->get($cache_file);
                            $success=!empty($content);
                    }
                    if ($success) {
                        echo $content;
                        echo "<!-- cached -->";
                        exit();
                    } else {
                        $use_cache = false;
                        $write_cache = true;
                    }
        }else{

                if (file_exists ( $cache_file ))
                        $last = filemtime ( $cache_file );
                else
                        $last =0;
                $use_cache=(time () - $last < $cache_time);

        }
        if ($use_cache) {
                //header ( "Location: $file" );
                echo file_get_contents($cache_file);
                exit ();
        } else {
                ob_start ();
        }
