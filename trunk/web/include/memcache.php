<?php
        
    require_once(dirname(__FILE__)."/db_info.inc.php");
    # Connect to memcache:
    global $memcache;
    if ($OJ_MEMCACHE){
	$memcache = new Memcache;
	if($OJ_SAE){
				$memcache=memcache_init();
	}else{
				$memcache->connect($OJ_MEMSERVER,  $OJ_MEMPORT);
	}
    }

    //下面两个函数首先都会判断是否有使用memcache，如果有使用，就会调用memcached的set/get命令来保存和获取数据
    //否则简单地返回false
    # Gets key / value pair into memcache … called by mysql_query_cache()
    function getCache($key) {
        global $memcache;
//	if ($memcache->get($key)) echo "true";
        return ($memcache) ? $memcache->get($key) : false;
    }

    # Puts key / value pair into memcache … called by mysql_query_cache()
    function setCache($key, $object, $timeout = 60) {
        global $memcache;
        return ($memcache) ? $memcache->set($key,$object,MEMCACHE_COMPRESSED,$timeout) : false;
    }

    # Caching version of pdo_query()
    function mysql_query_cache($sql){ 
	global $OJ_NAME,$OJ_MEMCACHE;	
    $linkIdentifier = false;
    $timeout = 60;
//首先调用上面的getCache函数，如果返回值不为false的话，就说明是从memcached服务器获取的数据
//如果返回false，此时就需要直接从数据库中获取数据了。
//需要注意的是这里使用操作的命令加上sql语句的md5码作为一个特定的key，可能大家觉得使用数据项的
//名称作为key会比较自然一点。运行memcached加上"-vv"参数，并且不作为daemon运行的话，可以看见
//memcached处理时输出的相关信息
    $num_args = func_num_args();
    $args = func_get_args();       //获得传入的所有参数的数组
    $args = array_slice($args,1,--$num_args);
    $key=md5($OJ_NAME.$_SERVER['HTTP_HOST']."mysql_query" . $sql.implode(" ",$args));
        if (!($cache = getCache($key))) {
            $cache = false;
            $cache =pdo_query($sql,$args);

    //将数据放入memcached服务器中，如果memcached服务器没有开的话，此语句什么也不会做
    //如果开启了服务器的话，数据将会被缓存到memcached服务器中
                if (!setCache($key, $cache, $timeout)) {
                    # If we get here, there isn’t a memcache daemon running or responding
		 if($OJ_MEMCACHE) echo "You can run these command to get faster speed:<br>sudo apt-get install memcached<br>sudo apt-get install php5-memcache<br>sudo apt-get install php-memcache";
                }

        }
	
        return $cache;
    }
?>
