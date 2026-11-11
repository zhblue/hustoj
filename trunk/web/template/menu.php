<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
// 当$OJ_FRIENDLY_LEVEL<0时，关闭所有增加功能。
if($OJ_FRIENDLY_LEVEL>=0){
    if(file_exists("hello")){  // 如果存在hello目录，自动添加链接
      echo '<a class="item" onclick=\'window.open("hello/index.html", "_blank",
        "width=600,height=850,left=" + (window.screen.width-600)  + ",top=0,toolbar=no,menubar=no,location=no,status=no,resizable=yes");\'><i class="book icon"></i><span class="desktop-only">Hello算法</span></a>';
    }

	echo '<a class="item" onclick=\'window.open("https://cpp.hustoj.com/index.html", "_blank",
        "width=1280,height=900,left=" + (window.screen.width-600)  + ",top=0,toolbar=no,menubar=no,location=no,status=no,resizable=yes");\'><i class="columns icon"></i><span class="desktop-only">C++调试播放器</span></a>';

    if(file_exists("blocks")){  // 如果存在blocks目录，自动添加链接
      echo '<a class="item" onclick=\'window.open("blocks/index.html", "_blank",
        "width=1280,height=900,left=" + (window.screen.width-600)  + ",top=0,toolbar=no,menubar=no,location=no,status=no,resizable=yes");\'><i class="object ungroup icon"></i><span class="desktop-only">积木C++</span></a>';
    }

    echo "<a class='item' href='noilinux.html' target='noilinux'><i class='clone icon'></i>NOI Linux 2.0模拟器</a>\n";
	// 获取当前目录下的所有子目录名
	if(isset($OJ_CUSTOM_TEMPLATE) && $OJ_CUSTOM_TEMPLATE){
		$tps = array_map('basename', glob(__DIR__ . '/*', GLOB_ONLYDIR));
	
		// 遍历并输出
		foreach ($tps as $tp) {
		    $safe_tp = htmlspecialchars($tp, ENT_QUOTES, 'UTF-8');
		    echo "<a class='item' href='?tp={$safe_tp}'><i class='star icon'></i>{$safe_tp}</a>\n";
		}
	}
}
