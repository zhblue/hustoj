<?php

    echo "<a class='item' href='noilinux.html' target='noilinux'><i class='star icon'></i>NOI Linux 2.0模拟器</a>\n";
// 获取当前目录下的所有子目录名
if($OJ_CUSTOM_TEMPLATE){
	$tps = array_map('basename', glob(__DIR__ . '/*', GLOB_ONLYDIR));

	// 遍历并输出
	foreach ($tps as $tp) {
	    $safe_tp = htmlspecialchars($tp, ENT_QUOTES, 'UTF-8');
	    echo "<a class='item' href='?tp={$safe_tp}'><i class='star icon'></i>{$safe_tp}</a>\n";
	}
}
