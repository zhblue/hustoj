<?php
if (!function_exists('str_contains')) {
    function str_contains (string $haystack, string $needle)
    {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

function getPartByMark($html,$mark1,$mark2){
   $i=strpos($html,$mark1);
   if($i===false) return $html;	
   $start=$i+strlen($mark1)+1;
   if($i>=0&&$start<=strlen($html)) $j=strpos($html,$mark2,$start);
   else return $html;
   $descriptionHTML=substr($html,$i+ strlen($mark1),$j-($i+ strlen($mark1)));
   return $descriptionHTML;
}

function getPartByMarkMB($html,$mark1,$mark2){
   $i=mb_strpos($html,$mark1);
   $start=$i+mb_strlen($mark1)+1;
   if($i>=0&&$start<=mb_strlen($html)) $j=mb_strpos($html,$mark2,$start);
   else return $html;
   $descriptionHTML=mb_substr($html,$i+ mb_strlen($mark1),$j-($i+ mb_strlen($mark1)));
   return $descriptionHTML;
}
function get_domain($url){
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}
function curl_get($url){
        global $curl,$OJ_DATA,$remote_cookie;
        if(function_exists('curl_init')){
                $curl = curl_init($url);
                //curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=buiebpv91e0cdhpmm6a320j1l7; path=/');
                //curl_setopt($curl, CURLOPT_HEADER, true);
                curl_setopt($curl, CURLOPT_COOKIEFILE, $remote_cookie); // use saved cookies
                curl_setopt($curl, CURLOPT_COOKIEJAR, $remote_cookie);  // save coockies
                curl_setopt($curl, CURLOPT_REFERER, "$url");
                curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36");
                curl_setopt($curl, CURLOPT_TIMEOUT, 5);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                $data = curl_exec($curl);
                return $data;
        }else{
                echo "PHP-curl missing (apt-get install php-curl)";
                return "PHP-curl missing (apt-get install php-curl)";
        }
}

function curl_post_urlencoded($url,$form){
	global $curl,$OJ_DATA,$remote_cookie;
	$curl = curl_init($url);
	//curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=buiebpv91e0cdhpmm6a320j1l7; path=/');
	//// 设置header
       // curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $remote_cookie); // use saved cookies
	curl_setopt($curl, CURLOPT_COOKIEJAR, $remote_cookie);  // save coockies
	curl_setopt($curl, CURLOPT_REFERER, "$url"); 
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"); 
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // 不要打印内容
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	// 设置 post 方式提交
	curl_setopt($curl, CURLOPT_POST, true);
	// 设置 post 数据
	$data="";
	foreach($form as $key => $value){
		$data.="$key=".urlencode($value)."&";
	}
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$data = curl_exec($curl);
	
	return $data;
}
function curl_post($url,$form){
	global $curl,$OJ_DATA,$remote_cookie;
	$curl = curl_init($url);
	//curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=buiebpv91e0cdhpmm6a320j1l7; path=/');
	//// 设置header
        //curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $remote_cookie); // use saved cookies
	curl_setopt($curl, CURLOPT_COOKIEJAR, $remote_cookie);  // save coockies
	curl_setopt($curl, CURLOPT_REFERER, "http://poj.org/"); 
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"); 
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // 不要打印内容
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	// 设置 post 方式提交
	curl_setopt($curl, CURLOPT_POST, true);
	// 设置 post 数据
	curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
	$data = curl_exec($curl);
	
	return $data;
}
