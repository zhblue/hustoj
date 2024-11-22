<?php
//a:9:{s:4:"lang";s:2:"en";s:9:"auth_pass";s:32:"d41d8cd98f00b204e9800998ecf8427e";s:8:"quota_mb";i:0;s:17:"upload_ext_filter";a:0:{}s:19:"download_ext_filter";a:0:{}s:15:"error_reporting";i:1;s:7:"fm_root";s:0:"";s:17:"cookie_cache_time";i:2592000;s:7:"version";s:5:"0.9.8";}
require_once("../include/db_info.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
      ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
     )){
	echo $_SESSION[$OJ_NAME.'_'.'administrator'];
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
// this is not a webshell , and it need administrator / problem editor  membership to use, 
// if aliyun warn you about this file , don't panic   
// 这不是后门(Webshell)文件，不要理会阿里云的误报。
    $charset = "UTF-8";
    //@setlocale(LC_CTYPE, 'C');
    putenv("LC_ALL=en_US.UTF-8");  //可能解决某些文件名乱码问题
    header("Pragma: no-cache");
    header("Cache-Control: no-store");
	header("Content-Type: text/html; charset=".$charset);
	//@ini_set('default_charset', $charset);
    if (false) {   // php8.0 removed get_magic_quotes_gpc
        function stripslashes_deep($value){
            return is_array($value)? array_map('stripslashes_deep', $value):$value;
        }
        $_POST = array_map('stripslashes_deep', $_POST);
        $_GET = array_map('stripslashes_deep', $_GET);
        $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    }
    //php ver < 8.0 has no endsWith function
    if (!function_exists('str_ends_with')) {
        function str_ends_with( $haystack, $needle ) {
            $length = strlen( $needle );
            if( !$length ) {
                return true;
            }
            return substr( $haystack, -$length ) === $needle;
        }
    }
    // Server Vars
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED'])) $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
		// proxy transparente não esconde o IP local, colocando ele após o IP da rede, separado por vírgula
		if (strpos($ipaddress, ',') !== false) {
		    $ips = explode(',', $ipaddress);
		    $ipaddress = trim($ips[0]);
		}
		if ($ipaddress == '::1') $ipaddress = '';
        return $ipaddress;
    }		
    $ip = get_client_ip();
    $islinux = !(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    function getServerURL() {
        $url = (isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"] == "on")?"https://":"http://";
        $url .= $_SERVER["SERVER_NAME"]; // $_SERVER["HTTP_HOST"] is equivalent
        if ($_SERVER["SERVER_PORT"] != "80") $url .= ":".$_SERVER["SERVER_PORT"];
        return $url;
    }
    function reSortFiles($directory) {
	// 列出目录中的所有文件
	$files = scandir($directory);
	// 遍历所有文件
	foreach ($files as $file) {
	// 检查文件是否是.ans文件
		if (str_ends_with($file, '.ans') === false ) {
		    continue;
		}
		$main=basename($file,".ans");
		$filePath = $directory . '/' . $main.".ans";
		$newFilePath = $directory . '/' . $main.".out";
		if (rename($filePath, $newFilePath)) {
		//              echo "File renamed: $filePath -> $newFilePath<br>";
		} else {
		echo "Error renaming file: $file<br>";
		}
	}
	foreach ($files as $file) {
		// 检查文件是否是.in文件
		if (str_ends_with($file, '.in') === false ) {
		    continue;
		}
		//echo "$file:<br>";
		$main=basename($file,".in");
		// 检查文件名是否以单个数字结尾
		if (preg_match('/^[^0-9]*([0-9])$/', $main)) {
		    // 获取文件名中的数字
		  //  echo "...";
		    $number = substr($main, -1);
		    // 获取文件名中的非数字部分
		    $nonNumberPart = preg_replace('/[0-9]$/', '', $main);
		    // 创建新的文件名
		    $newFileName = $nonNumberPart . '0' . $number;
		    // 获取文件路径
		    $filePath = $directory . '/' . $main.".in";
		    $newFilePath = $directory . '/' . $newFileName.".in";
		    // 尝试重命名文件
		    if (rename($filePath, $newFilePath)) {
		    } else {
			echo "Error renaming file: $file<br>";
		    }
		    $filePath = $directory . '/' . $main.".out";
		    $newFilePath = $directory . '/' . $newFileName.".out";
		    // 尝试重命名文件
		    if (rename($filePath, $newFilePath)) {
		    } else {
			echo "Error renaming file: $file<br>";
		    }
		}
	}
	
    }
    function getCompleteURL() {
        return getServerURL().$_SERVER["REQUEST_URI"];
    }

    $url = getCompleteURL();
    $url_info = parse_url($url);
	if( !isset($_SERVER['DOCUMENT_ROOT']) ) {
		if ( isset($_SERVER['SCRIPT_FILENAME']) ) $path = $_SERVER['SCRIPT_FILENAME'];
		elseif ( isset($_SERVER['PATH_TRANSLATED']) ) $path = str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']);
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($path, 0, 0-strlen($_SERVER['PHP_SELF'])));
	}
	$doc_root = str_replace('//','/',str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER["DOCUMENT_ROOT"]));
    $fm_self = $doc_root.$_SERVER["PHP_SELF"];
    $path_info = pathinfo($fm_self);
	// Register Globals
	$blockKeys = array('_SERVER','_SESSION','_GET','_POST','_COOKIE','charset','ip','islinux','url','url_info','doc_root','fm_self','path_info');
    foreach ($_GET as $key => $val) if (array_search($key,$blockKeys) === false) $$key=$val;
    foreach ($_POST as $key => $val) if (array_search($key,$blockKeys) === false) $$key=$val;
    foreach ($_COOKIE as $key => $val) if (array_search($key,$blockKeys) === false) $$key=$val;
// +--------------------------------------------------
// | Config
// +--------------------------------------------------
    $cfg = new config();
    $cfg->load();
    switch ($error_reporting){
        case 0: error_reporting(0); @ini_set("display_errors",0); break;
        case 1: error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR); @ini_set("display_errors",1); break;
        case 2: error_reporting(E_ALL); @ini_set("display_errors",1); break;
    }

    $pid=0;
    if(isset($_GET['pid'])){
        $pid=intval($_GET['pid']);
    }else{
        $pid=intval(basename($current_dir));
        if($pid==0) $pid=intval(basename($dir_dest));
    }
    $current_dir="$OJ_DATA/$pid/";
    if(! (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$pid])) ){
        echo "No Privilege.<br>你不是管理员，也不是这个题的原创作者，因此不能管理这个题的数据。";
        exit(0)    ;
    }

    $dir_dest=$current_dir;
    if (!isset($current_dir)){
        $current_dir = $path_info["dirname"]."/";
        if (!$islinux) $current_dir = ucfirst($current_dir);
        //@chmod($current_dir,0711);
    } else $current_dir = format_path($current_dir);
    // Auto Expand Local Path
    if (!isset($expanded_dir_list)){
        $expanded_dir_list = "";
        $mat = explode("/",$path_info["dirname"]);
        for ($x=0;$x<count($mat);$x++) $expanded_dir_list .= ":".$mat[$x];
        setcookie("expanded_dir_list", $expanded_dir_list, 0, "/");
    }

    if (!isset($fm_current_root)){
        if (strlen($fm_root)) $fm_current_root = $fm_root;
        else {
            if (!$islinux) $fm_current_root = ucfirst($path_info["dirname"]."/");
            else $fm_current_root = $doc_root."/";
        }
        setcookie("fm_current_root", $fm_current_root, 0, "/");
    } elseif (isset($set_fm_current_root)) {
        if (!$islinux) $fm_current_root = ucfirst($set_fm_current_root);
        setcookie("fm_current_root", $fm_current_root, 0, "/");
    }
    $fm_current_root=$OJ_DATA;
    if (!isset($resolveIDs)){
        setcookie("resolveIDs", 0, time()+$cookie_cache_time, "/");
    } elseif (isset($set_resolveIDs)){
        $resolveIDs=($resolveIDs)?0:1;
        setcookie("resolveIDs", $resolveIDs, time()+$cookie_cache_time, "/");
    }
    if(isset($_GET['generate'])){
            //echo "Generate out in $current_dir......";
            chdir($current_dir);
            if(file_exists($current_dir."/Main.c")){
                if(!$OJ_SaaS_ENABLE)system("/home/judge/src/install/gcc.sh $current_dir");
                if(!system("/home/judge/src/install/makeout.sh Main"))
                        echo "makeout fail:<br>chgrp -R www-data /home/judge";
            }else if(file_exists($current_dir."/Main.cc")){
                if(!$OJ_SaaS_ENABLE)system("/home/judge/src/install/g++.sh $current_dir");
                if(!system("/home/judge/src/install/makeout.sh Main"))
                        echo "makeout fail:<br>chgrp -R www-data to /home/judge";
            }else{
                echo "未找到Main.c或Main.cc,自动生成9组空文件。";
                for($i=1;$i<10;$i++){
                        touch("test_$i.in");
                        touch("test_$i.out");
                }
            }
    }
    if(isset($_GET['ans2out'])){
	    //echo "Generate out in $current_dir......";
	    chdir($current_dir);
	    //system("/home/judge/src/install/ans2out $current_dir");
	    //using php to finish the work without system function
	    reSortFiles($current_dir);
    }
    if ($resolveIDs){               // php8 default disabled exec()
        $mat_passwd=explode("\n",file_get_contents("/etc/passwd"));
        $mat_group=explode("\n",file_get_contents("/etc/group"));
    }
    $fm_color['Bg'] = "EEEEEE";
    $fm_color['Text'] = "000000";
    $fm_color['Link'] = "0A77F7";
    $fm_color['Entry'] = "FFFFFF";
    $fm_color['Over'] = "C0EBFD";
    $fm_color['Mark'] = "A7D2E4";
    foreach($fm_color as $tag=>$color){
        $fm_color[$tag]=strtolower($color);
    }
// +--------------------------------------------------
// | File Manager Actions
// +--------------------------------------------------
if ($loggedon==$auth_pass){
    switch ($frame){
        case 1: break; // Empty Frame
        case 2: frame2(); break;
        case 3: frame3(); break;
        default:
            switch($action){
                case 1: logout(); break;
                case 2: config_form(); break;
                case 3: download(); break;
                case 4: view(); break;
                case 5: server_info(); break;
                //case 6: execute_cmd(); break;
                case 7: edit_file_form(); break;
                //case 8: chmod_form(); break;
                //case 9: shell_form(); break;
                case 10: upload_form(); break;
                //case 11: execute_file(); break;
                default: frameset();
            }
    }
} else {
    if (isset($_SESSION[$OJ_NAME.'_administrator'])||isset($_SESSION[$OJ_NAME.'_problem_editor'])) login();
    else login_form();
}
// +--------------------------------------------------
// | Config Class
// +--------------------------------------------------
class config {
    var $data;
    var $filename;
    function __construct(){
        global $fm_self;
	global $OJ_LANG;
        $this->data = array(
            'lang'=>'en',
            'auth_pass'=>md5(''),
            'quota_mb'=>0,
            'upload_ext_filter'=>array(),
            'download_ext_filter'=>array(),
            'error_reporting'=>1,
            'fm_root'=>'',
            'cookie_cache_time'=>60*60*24*30, // 30 Days
            'version'=>'0.9.8'
            );
        $data = false;
        $this->filename = $fm_self;
        if (file_exists($this->filename)){
            $mat = file($this->filename);
            $objdata = trim(substr($mat[1],2));
            if (strlen($objdata)) $data = unserialize($objdata);
        }
        if (is_array($data)&&count($data)==count($this->data)) $this->data = $data;
        else $this->save();
	if($OJ_LANG == "cn" ) $this->data['lang']="cn";
    }
    function save(){
        $objdata = "<?php".chr(13).chr(10)."//".serialize($this->data).chr(13).chr(10);
        if (strlen($objdata)){
            if (file_exists($this->filename)){
                $mat = file($this->filename);
                if ($fh = @fopen($this->filename, "w")){
                    @fputs($fh,$objdata,strlen($objdata));
                    for ($x=2;$x<count($mat);$x++) @fputs($fh,$mat[$x],strlen($mat[$x]));
                    @fclose($fh);
                }
            }
        }
    }
    function load(){
        foreach ($this->data as $key => $val) $GLOBALS[$key] = $val;
    }
}
// +--------------------------------------------------
// | Internationalization
// +--------------------------------------------------
function et($tag){
    global $lang;

    // English - by Fabricio Seger Kolling
    $en['Version'] = 'Version';
    $en['DocRoot'] = 'Document Root';
    $en['FLRoot'] = 'File Manager Root';
    $en['Name'] = 'Name';
    $en['And'] = 'and';
    $en['Enter'] = 'Enter';
    $en['Send'] = 'Send';
    $en['Refresh'] = 'Refresh';
    $en['SaveConfig'] = 'Save Configurations';
    $en['SavePass'] = 'Save Password';
    $en['SaveFile'] = 'Save File';
    $en['Save'] = 'Save';
    $en['Leave'] = 'Leave';
    $en['Edit'] = 'Edit';
    $en['View'] = 'View';
    $en['Config'] = 'Config';
    $en['Ren'] = 'Rename';
    $en['Rem'] = 'Delete';
    $en['Compress'] = 'Compress';
    $en['Decompress'] = 'Decompress';
    $en['ResolveIDs'] = 'Resolve IDs';
    $en['Move'] = 'Move';
    $en['Copy'] = 'Copy';
    $en['ServerInfo'] = 'Server Info';
    $en['CreateDir'] = 'Create Directory';
    $en['CreateArq'] = 'Create File';
    $en['ExecCmd'] = 'Execute Command';
    $en['Upload'] = 'Upload';
    $en['UploadEnd'] = 'Upload Finished';
    $en['Perm'] = 'Perm';
    $en['Perms'] = 'Permissions';
    $en['Owner'] = 'Owner';
    $en['Group'] = 'Group';
    $en['Other'] = 'Other';
    $en['Size'] = 'Size';
    $en['Date'] = 'Date';
    $en['Type'] = 'Type';
    $en['Free'] = 'free';
    $en['Shell'] = 'Shell';
    $en['Read'] = 'Read';
    $en['Write'] = 'Write';
    $en['Exec'] = 'Execute';
    $en['Apply'] = 'Apply';
    $en['StickyBit'] = 'Sticky Bit';
    $en['Pass'] = 'Password';
    $en['Lang'] = 'Language';
    $en['File'] = 'File';
    $en['File_s'] = 'file(s)';
    $en['Dir_s'] = 'directory(s)';
    $en['To'] = 'to';
    $en['Destination'] = 'Destination';
    $en['Configurations'] = 'Configurations';
    $en['JSError'] = 'JavaScript Error';
    $en['NoSel'] = 'There are no selected itens';
    $en['SelDir'] = 'Select the destination directory on the left tree';
    $en['TypeDir'] = 'Enter the directory name';
    $en['TypeArq'] = 'Enter the file name';
    $en['TypeCmd'] = 'Enter the command';
    $en['TypeArqComp'] = 'Enter the file name.\\nThe extension will define the compression type.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $en['RemSel'] = 'DELETE selected itens';
    $en['NoDestDir'] = 'There is no selected destination directory';
    $en['DestEqOrig'] = 'Origin and destination directories are equal';
    $en['InvalidDest'] = 'Destination directory is invalid';
    $en['NoNewPerm'] = 'New permission not set';
    $en['CopyTo'] = 'COPY to';
    $en['MoveTo'] = 'MOVE to';
    $en['AlterPermTo'] = 'CHANGE PERMISSIONS to';
    $en['ConfExec'] = 'Confirm EXECUTE';
    $en['ConfRem'] = 'Confirm DELETE';
    $en['EmptyDir'] = 'Empty directory';
    $en['IOError'] = 'I/O Error';
    $en['FileMan'] = 'PHP File Manager';
    $en['TypePass'] = 'Enter the password';
    $en['InvPass'] = 'Invalid Password';
    $en['ReadDenied'] = 'Read Access Denied';
    $en['FileNotFound'] = 'File not found';
    $en['AutoClose'] = 'Close on Complete';
    $en['OutDocRoot'] = 'File beyond DOCUMENT_ROOT';
    $en['NoCmd'] = 'Error: Command not informed';
    $en['ConfTrySave'] = 'File without write permisson.\\nTry to save anyway';
    $en['ConfSaved'] = 'Configurations saved';
    $en['PassSaved'] = 'Password saved';
    $en['NoPhpinfo'] = 'Function phpinfo disabled';
    $en['NoReturn'] = 'no return';
    $en['FileSent'] = 'File sent';
    $en['SpaceLimReached'] = 'Space limit reached';
    $en['InvExt'] = 'Invalid extension';
    $en['FileNoOverw'] = 'File could not be overwritten';
    $en['FileOverw'] = 'File overwritten';
    $en['FileIgnored'] = 'File ignored';
    $en['ChkVer'] = 'Check for new version';
    $en['ChkVerAvailable'] = 'New version, click here to begin download!!';
    $en['ChkVerNotAvailable'] = 'No new version available. :(';
    $en['ChkVerError'] = 'Connection Error.';
    $en['Website'] = 'Website';
    $en['SendingForm'] = 'Sending files, please wait';
    $en['NoFileSel'] = 'No file selected';
    $en['SelAll'] = 'All';
    $en['SelNone'] = 'None';
    $en['SelInverse'] = 'Inverse';
    $en['Selected_s'] = 'selected';
    $en['Total'] = 'total';
    $en['Partition'] = 'Partition';
    $en['RenderTime'] = 'Time to render this page';
    $en['Seconds'] = 'sec';
    $en['ErrorReport'] = 'Error Reporting';
    $en['Random-data'] = 'Random-data generator';
    $en['GenerateOut'] = 'Generate Out files';
    $en['Ans2out'] = '*.ans -> *.out';
    $en['IOFilename'] = 'Specify IOFilename';
    $en['SolutionFilename'] = 'Specify Solution filename';
    // chinese
	$cn['Version'] = '版本';
    $cn['DocRoot'] = '根目录';
    $cn['FLRoot'] = '文件管理器根目录';
    $cn['Name'] = '名字';
    $cn['And'] = '和';
    $cn['Enter'] = '进入';
    $cn['Send'] = '发送';
    $cn['Refresh'] = '刷新';
    $cn['SaveConfig'] = '保存配置';
    $cn['SavePass'] = '保存密码';
    $cn['SaveFile'] = '保存文件';
    $cn['Save'] = '保存';
    $cn['Leave'] = '退出';
    $cn['Edit'] = '编辑';
    $cn['View'] = '查看';
    $cn['Config'] = '配置';
    $cn['Ren'] = '重命名';
    $cn['Rem'] = '删除';
    $cn['Compress'] = '压缩';
    $cn['Decompress'] = '解压';
    $cn['ResolveIDs'] = '刷新/解析ID';
    $cn['Move'] = '移动';
    $cn['Copy'] = '复制';
    $cn['ServerInfo'] = '服务器信息';
    $cn['CreateDir'] = '创建目录';
    $cn['CreateArq'] = '创建文件';
    $cn['ExecCmd'] = '执行命令';
    $cn['Upload'] = '上传';
    $cn['UploadEnd'] = '上传完成';
    $cn['Perms'] = '权限';
    $cn['Owner'] = '所有者';
    $cn['Group'] = '组群';
    $cn['Other'] = '其他';
    $cn['Size'] = '大小';
    $cn['Date'] = '日期';
    $cn['Type'] = '类型';
    $cn['Free'] = '空闲';
    $cn['Shell'] = '控制台';
    $cn['Read'] = '读';
    $cn['Write'] = '写';
    $cn['Exec'] = '执行';
    $cn['Apply'] = '应用';
    $cn['StickyBit'] = 'Sticky Bit';
    $cn['Pass'] = '密码';
    $cn['Lang'] = '语言';
    $cn['File'] = '文件';
    $cn['File_s'] = '文件';
    $cn['Dir_s'] = '目录';
    $cn['To'] = '到';
    $cn['Destination'] = '目标';
    $cn['Configurations'] = '配置';
    $cn['JSError'] = 'JS脚本错误';
    $cn['NoSel'] = '未选择文件或目录';
    $cn['SelDir'] = '在左边的目录树中选择目标目录';
    $cn['TypeDir'] = '请输入目录名';
    $cn['TypeArq'] = '请输入文件名';
    $cn['TypeCmd'] = '请输入命令';
    $cn['TypeArqComp'] = '请输入文件名.扩展名将确定其压缩格式.\\n如:file.zip, file.tar, file.bzip, file.gzip';
    $cn['RemSel'] = '删除选中项';
    $cn['NoDestDir'] = '未选择目标目录';
    $cn['DestEqOrig'] = '目标目录和原始目录相同';
    $cn['InvalidDest'] = '目标目录错误';
    $cn['NoNewPerm'] = '新权限未设置';
    $cn['CopyTo'] = '复制到';
    $cn['MoveTo'] = '移动到';
    $cn['AlterPermTo'] = '更改权限为';
    $cn['ConfExec'] = '确认执行';
    $cn['ConfRem'] = '确认删除';
    $cn['EmptyDir'] = '空目录';
    $cn['IOError'] = 'I/O 错误';
    $cn['FileMan'] = 'PHP 文件管理器';
    $cn['TypePass'] = '请输入密码';
    $cn['InvPass'] = '密码错误';
    $cn['ReadDenied'] = '访问被拒绝';
    $cn['FileNotFound'] = '文件不存在';
    $cn['AutoClose'] = '完成后关闭';
    $cn['OutDocRoot'] = '文件不在根目录中';
    $cn['NoCmd'] = '错误: 命令不正确';
    $cn['ConfTrySave'] = '文件不可写.\\n请尝试保存在其他地方';
    $cn['ConfSaved'] = '配置已保存';
    $cn['PassSaved'] = '密码已保存';
    $cn['FileDirExists'] = '文件或目录已存在';
    $cn['NoPhpinfo'] = '函数 phpinfo 已禁用';
    $cn['NoReturn'] = '无返回';
    $cn['FileSent'] = '发送文件';
    $cn['SpaceLimReached'] = '空间限制';
    $cn['InvExt'] = '非法扩展名';
    $cn['FileNoOverw'] = '文件无法覆盖';
    $cn['FileOverw'] = '覆盖';
    $cn['FileIgnored'] = '忽略';
    $cn['ChkVer'] = '从 sf.net 查询新版本';
    $cn['ChkVerAvailable'] = '发现新版本, 请点击这里下载!!';
    $cn['ChkVerNotAvailable'] = '当前无新版本可用. :(';
    $cn['ChkVerError'] = '连接错误.';
    $cn['Website'] = '站点';
    $cn['SendingForm'] = '正在发送文件,请稍后';
    $cn['NoFileSel'] = '未选择文件';
    $cn['SelAll'] = '全选';
    $cn['SelNone'] = '不选';
    $cn['SelInverse'] = '反选';
    $cn['Selected_s'] = '已选择';
    $cn['Total'] = '总数';
    $cn['Partition'] = '磁盘分区';
    $cn['RenderTime'] = '页面执行时间';
    $cn['Seconds'] = '秒';
    $cn['ErrorReport'] = '错误报告';
    $cn['Random-data'] = '随机测试数据生成器';
    $cn['GenerateOut'] = '用标程Main.c/Main.cc覆盖生成Out文件';
    $cn['Ans2out'] = '自动修订文件名';
    $cn['IOFilename'] = '指定输入输出文件名';
    $cn['SolutionFilename'] = '指定NOIP提交代码文件名';

    // Portuguese by - Fabricio Seger Kolling
    $pt['Version'] = 'Versão';
    $pt['DocRoot'] = 'Document Root';
    $pt['FLRoot'] = 'File Manager Root';
    $pt['Name'] = 'Nome';
    $pt['And'] = 'e';
    $pt['Enter'] = 'Entrar';
    $pt['Send'] = 'Enviar';
    $pt['Refresh'] = 'Atualizar';
    $pt['SaveConfig'] = 'Salvar Configurações';
    $pt['SavePass'] = 'Salvar Senha';
    $pt['SaveFile'] = 'Salvar Arquivo';
    $pt['Save'] = 'Salvar';
    $pt['Leave'] = 'Sair';
    $pt['Edit'] = 'Editar';
    $pt['View'] = 'Visualizar';
    $pt['Config'] = 'Config';
    $pt['Ren'] = 'Renomear';
    $pt['Rem'] = 'Apagar';
    $pt['Compress'] = 'Compactar';
    $pt['Decompress'] = 'Descompactar';
    $pt['ResolveIDs'] = 'Resolver IDs';
    $pt['Move'] = 'Mover';
    $pt['Copy'] = 'Copiar';
    $pt['ServerInfo'] = 'Server Info';
    $pt['CreateDir'] = 'Criar Diretório';
    $pt['CreateArq'] = 'Criar Arquivo';
    $pt['ExecCmd'] = 'Executar Comando';
    $pt['Upload'] = 'Upload';
    $pt['UploadEnd'] = 'Upload Terminado';
    $pt['Perm'] = 'Perm';
    $pt['Perms'] = 'Permissões';
    $pt['Owner'] = 'Dono';
    $pt['Group'] = 'Grupo';
    $pt['Other'] = 'Outros';
    $pt['Size'] = 'Tamanho';
    $pt['Date'] = 'Data';
    $pt['Type'] = 'Tipo';
    $pt['Free'] = 'livre';
    $pt['Shell'] = 'Shell';
    $pt['Read'] = 'Ler';
    $pt['Write'] = 'Escrever';
    $pt['Exec'] = 'Executar';
    $pt['Apply'] = 'Aplicar';
    $pt['StickyBit'] = 'Sticky Bit';
    $pt['Pass'] = 'Senha';
    $pt['Lang'] = 'Idioma';
    $pt['File'] = 'Arquivo';
    $pt['File_s'] = 'arquivo(s)';
    $pt['Dir_s'] = 'diretorio(s)';
    $pt['To'] = 'para';
    $pt['Destination'] = 'Destino';
    $pt['Configurations'] = 'Configurações';
    $pt['JSError'] = 'Erro de JavaScript';
    $pt['NoSel'] = 'Não há itens selecionados';
    $pt['SelDir'] = 'Selecione o diretório de destino na árvore a esquerda';
    $pt['TypeDir'] = 'Digite o nome do diretório';
    $pt['TypeArq'] = 'Digite o nome do arquivo';
    $pt['TypeCmd'] = 'Digite o commando';
    $pt['TypeArqComp'] = 'Digite o nome do arquivo.\\nA extensão determina o tipo de compactação.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $pt['RemSel'] = 'APAGAR itens selecionados';
    $pt['NoDestDir'] = 'Não há um diretório de destino selecionado';
    $pt['DestEqOrig'] = 'Diretório de origem e destino iguais';
    $pt['InvalidDest'] = 'Diretório de destino inválido';
    $pt['NoNewPerm'] = 'Nova permissão não foi setada';
    $pt['CopyTo'] = 'COPIAR para';
    $pt['MoveTo'] = 'MOVER para';
    $pt['AlterPermTo'] = 'ALTERAR PERMISSÕES para';
    $pt['ConfExec'] = 'Confirma EXECUTAR';
    $pt['ConfRem'] = 'Confirma APAGAR';
    $pt['EmptyDir'] = 'Diretório vazio';
    $pt['IOError'] = 'Erro de E/S';
    $pt['FileMan'] = 'PHP File Manager';
    $pt['TypePass'] = 'Digite a senha';
    $pt['InvPass'] = 'Senha Inválida';
    $pt['ReadDenied'] = 'Acesso de leitura negado';
    $pt['FileNotFound'] = 'Arquivo não encontrado';
    $pt['AutoClose'] = 'Fechar Automaticamente';
    $pt['OutDocRoot'] = 'Arquivo fora do DOCUMENT_ROOT';
    $pt['NoCmd'] = 'Erro: Comando não informado';
    $pt['ConfTrySave'] = 'Arquivo sem permissão de escrita.\\nTentar salvar assim mesmo';
    $pt['ConfSaved'] = 'Configurações salvas';
    $pt['PassSaved'] = 'Senha salva';
    $pt['FileDirExists'] = 'Arquivo ou diretório já existe';
    $pt['NoPhpinfo'] = 'Função phpinfo desabilitada';
    $pt['NoReturn'] = 'sem retorno';
    $pt['FileSent'] = 'Arquivo enviado';
    $pt['SpaceLimReached'] = 'Limite de espaço alcançado';
    $pt['InvExt'] = 'Extensão inválida';
    $pt['FileNoOverw'] = 'Arquivo não pode ser sobreescrito';
    $pt['FileOverw'] = 'Arquivo sobreescrito';
    $pt['FileIgnored'] = 'Arquivo omitido';
    $pt['ChkVer'] = 'Verificar por nova versão';
    $pt['ChkVerAvailable'] = 'Nova versão, clique aqui para iniciar download!!';
    $pt['ChkVerNotAvailable'] = 'Não há nova versão disponível. :(';
    $pt['ChkVerError'] = 'Erro de conexão.';
    $pt['Website'] = 'Website';
    $pt['SendingForm'] = 'Enviando arquivos, aguarde';
    $pt['NoFileSel'] = 'Nenhum arquivo selecionado';
    $pt['SelAll'] = 'Tudo';
    $pt['SelNone'] = 'Nada';
    $pt['SelInverse'] = 'Inverso';
    $pt['Selected_s'] = 'selecionado(s)';
    $pt['Total'] = 'total';
    $pt['Partition'] = 'Partição';
    $pt['RenderTime'] = 'Tempo para gerar esta página';
    $pt['Seconds'] = 'seg';
    $pt['ErrorReport'] = 'Error Reporting';
   $pt['IOFilename'] = 'Specify IOFilename';
    $pt['SolutionFilename'] = 'Specify Solution filename';
 
	// Spanish - by Sh Studios
    $es['Version'] = 'Versión';
    $es['DocRoot'] = 'Raiz del programa';
    $es['FLRoot'] = 'Raiz del administrador de archivos';
    $es['Name'] = 'Nombre';
    $es['And'] = 'y';
    $es['Enter'] = 'Enter';
    $es['Send'] = 'Enviar';
    $es['Refresh'] = 'Refrescar';
    $es['SaveConfig'] = 'Guardar configuraciones';
    $es['SavePass'] = 'Cuardar Contraseña';
    $es['SaveFile'] = 'Guardar Archivo';
    $es['Save'] = 'Guardar';
    $es['Leave'] = 'Salir';
    $es['Edit'] = 'Editar';
    $es['View'] = 'Mirar';
    $es['Config'] = 'Config.';
    $es['Ren'] = 'Renombrar';
    $es['Rem'] = 'Borrar';
    $es['Compress'] = 'Comprimir';
    $es['Decompress'] = 'Decomprimir';
    $es['ResolveIDs'] = 'Resolver IDs';
    $es['Move'] = 'Mover';
    $es['Copy'] = 'Copiar';
    $es['ServerInfo'] = 'Info del Server';
    $es['CreateDir'] = 'Crear Directorio';
    $es['CreateArq'] = 'Crear Archivo';
    $es['ExecCmd'] = 'Ejecutar Comando';
    $es['Upload'] = 'Subir';
    $es['UploadEnd'] = 'Subida exitosa';
    $es['Perm'] = 'Perm';
    $es['Perms'] = 'Permisiones';
    $es['Owner'] = 'Propietario';
    $es['Group'] = 'Grupo';
    $es['Other'] = 'Otro';
    $es['Size'] = 'Tamaño';
    $es['Date'] = 'Fecha';
    $es['Type'] = 'Tipo';
    $es['Free'] = 'libre';
    $es['Shell'] = 'Ejecutar';
    $es['Read'] = 'Leer';
    $es['Write'] = 'Escribir';
    $es['Exec'] = 'Ejecutar';
    $es['Apply'] = 'Aplicar';
    $es['StickyBit'] = 'Sticky Bit';
    $es['Pass'] = 'Contraseña';
    $es['Lang'] = 'Lenguage';
    $es['File'] = 'Archivos';
    $es['File_s'] = 'archivo(s)';
    $es['Dir_s'] = 'directorio(s)';
    $es['To'] = 'a';
    $es['Destination'] = 'Destino';
    $es['Configurations'] = 'Configuracion';
    $es['JSError'] = 'Error de JavaScript';
    $es['NoSel'] = 'No hay items seleccionados';
    $es['SelDir'] = 'Seleccione el directorio de destino en el arbol derecho';
    $es['TypeDir'] = 'Escriba el nombre del directorio';
    $es['TypeArq'] = 'Escriba el nombre del archivo';
    $es['TypeCmd'] = 'Escriba el comando';
    $es['TypeArqComp'] = 'Escriba el nombre del directorio.\\nLa extension definira el tipo de compresion.\\nEj:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $es['RemSel'] = 'BORRAR items seleccionados';
    $es['NoDestDir'] = 'No se ha seleccionado el directorio de destino';
    $es['DestEqOrig'] = 'El origen y el destino son iguales';
    $es['InvalidDest'] = 'El destino del directorio es invalido';
    $es['NoNewPerm'] = 'Las permisiones no se pudieron establecer';
    $es['CopyTo'] = 'COPIAR a';
    $es['MoveTo'] = 'MOVER a';
    $es['AlterPermTo'] = 'CAMBIAR PERMISIONES a';
    $es['ConfExec'] = 'Confirmar EJECUCION';
    $es['ConfRem'] = 'Confirmar BORRADO';
    $es['EmptyDir'] = 'Directorio Vacio';
    $es['IOError'] = 'Error I/O';
    $es['FileMan'] = 'PHP File Manager';
    $es['TypePass'] = 'Escriba la contraseña';
    $es['InvPass'] = 'Contraseña invalida';
    $es['ReadDenied'] = 'Acceso de lectura denegado';
    $es['FileNotFound'] = 'Archivo no encontrado';
    $es['AutoClose'] = 'Cerrar al completar';
    $es['OutDocRoot'] = 'Archivo antes de DOCUMENT_ROOT';
    $es['NoCmd'] = 'Error: No se ha escrito ningun comando';
    $es['ConfTrySave'] = 'Archivo sin permisos de escritura.\\nIntente guardar en otro lugar';
    $es['ConfSaved'] = 'Configuracion Guardada';
    $es['PassSaved'] = 'Contraseña guardada';
    $es['FileDirExists'] = 'Archivo o directorio ya existente';
    $es['NoPhpinfo'] = 'Funcion phpinfo() inhabilitada';
    $es['NoReturn'] = 'sin retorno';
    $es['FileSent'] = 'Archivo enviado';
    $es['SpaceLimReached'] = 'Limite de espacio en disco alcanzado';
    $es['InvExt'] = 'Extension inalida';
    $es['FileNoOverw'] = 'El archivo no pudo ser sobreescrito';
    $es['FileOverw'] = 'Archivo sobreescrito';
    $es['FileIgnored'] = 'Archivo ignorado';
    $es['ChkVer'] = 'Chequear las actualizaciones';
    $es['ChkVerAvailable'] = 'Nueva version, haga click aqui para descargar!!';
    $es['ChkVerNotAvailable'] = 'Su version es la mas reciente.';
    $es['ChkVerError'] = 'Error de coneccion.';
    $es['Website'] = 'Sitio Web';
    $es['SendingForm'] = 'Enviando archivos, espere!';
    $es['NoFileSel'] = 'Ningun archivo seleccionado';
    $es['SelAll'] = 'Todos';
    $es['SelNone'] = 'Ninguno';
    $es['SelInverse'] = 'Inverso';
    $es['Selected_s'] = 'seleccionado';
    $es['Total'] = 'total';
    $es['Partition'] = 'Particion';
    $es['RenderTime'] = 'Generado en';
    $es['Seconds'] = 'seg';
    $es['ErrorReport'] = 'Reporte de error';
   $es['IOFilename'] = 'Specify IOFilename';
    $es['SolutionFilename'] = 'Specify Solution filename';
 
	// Korean - by Airplanez
    $kr['Version'] = '버전';
    $kr['DocRoot'] = '웹서버 루트';
    $kr['FLRoot'] = '파일 매니저 루트';
    $kr['Name'] = '이름';
    $kr['Enter'] = '입력';
    $kr['Send'] = '전송';
    $kr['Refresh'] = '새로고침';
    $kr['SaveConfig'] = '환경 저장';
    $kr['SavePass'] = '비밀번호 저장';
    $kr['SaveFile'] = '파일 저장';
    $kr['Save'] = '저장';
    $kr['Leave'] = '나가기';
    $kr['Edit'] = '수정';
    $kr['View'] = '보기';
    $kr['Config'] = '환경';
    $kr['Ren'] = '이름바꾸기';
    $kr['Rem'] = '삭제';
    $kr['Compress'] = '압축하기';
    $kr['Decompress'] = '압축풀기';
    $kr['ResolveIDs'] = '소유자';
    $kr['Move'] = '이동';
    $kr['Copy'] = '복사';
    $kr['ServerInfo'] = '서버 정보';
    $kr['CreateDir'] = '디렉토리 생성';
    $kr['CreateArq'] = '파일 생성';
    $kr['ExecCmd'] = '명령 실행';
    $kr['Upload'] = '업로드';
    $kr['UploadEnd'] = '업로드가 완료되었습니다.';
    $kr['Perm'] = '권한';
    $kr['Perms'] = '권한';
    $kr['Owner'] = '소유자';
    $kr['Group'] = '그룹';
    $kr['Other'] = '모든사용자';
    $kr['Size'] = '크기';
    $kr['Date'] = '날짜';
    $kr['Type'] = '종류';
    $kr['Free'] = '여유';
    $kr['Shell'] = '쉘';
    $kr['Read'] = '읽기';
    $kr['Write'] = '쓰기';
    $kr['Exec'] = '실행';
    $kr['Apply'] = '적용';
    $kr['StickyBit'] = '스티키 비트';
    $kr['Pass'] = '비밀번호';
    $kr['Lang'] = '언어';
    $kr['File'] = '파일';
    $kr['File_s'] = '파일';
    $kr['To'] = '으로';
    $kr['Destination'] = '대상';
    $kr['Configurations'] = '환경';
    $kr['JSError'] = '자바스크립트 오류';
    $kr['NoSel'] = '선택된 것이 없습니다';
    $kr['SelDir'] = '왼쪽리스트에서 대상 디렉토리를 선택하세요';
    $kr['TypeDir'] = '디렉토리명을 입력하세요';
    $kr['TypeArq'] = '파일명을 입력하세요';
    $kr['TypeCmd'] = '명령을 입력하세요';
    $kr['TypeArqComp'] = '파일명을 입력하세요.\\n확장자에 따라 압축형식이 정해집니다.\\n예:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $kr['RemSel'] = '선택된 것을 삭제했습니다';
    $kr['NoDestDir'] = '선택된 대상 디렉토리가 없습니다.';
    $kr['DestEqOrig'] = '원래 디렉토리와 대상 디렉토리가 같습니다';
    $kr['NoNewPerm'] = '새로운 권한이 설정되지 않았습니다';
    $kr['CopyTo'] = '여기에 복사';
    $kr['MoveTo'] = '여기로 이동';
    $kr['AlterPermTo'] = '으로 권한변경';
    $kr['ConfExec'] = '실행 확인';
    $kr['ConfRem'] = '삭제 확인';
    $kr['EmptyDir'] = '빈 디렉토리';
    $kr['IOError'] = '입/출력 오류';
    $kr['FileMan'] = 'PHP 파일 매니저';
    $kr['TypePass'] = '비밀번호를 입력하세요';
    $kr['InvPass'] = '비밀번호가 틀립니다';
    $kr['ReadDenied'] = '읽기가 거부되었습니다';
    $kr['FileNotFound'] = '파일이 없습니다';
    $kr['AutoClose'] = '완료후 닫기';
    $kr['OutDocRoot'] = 'DOCUMENT_ROOT 이내의 파일이 아닙니다';
    $kr['NoCmd'] = '오류: 명령이 실행되지 않았습니다';
    $kr['ConfTrySave'] = '파일에 쓰기 권한이 없습니다.\\n그래도 저장하시겠습니까';
    $kr['ConfSaved'] = '환경이 저장되었습니다';
    $kr['PassSaved'] = '비밀번호 저장';
    $kr['FileDirExists'] = '파일 또는 디렉토리가 이미 존재합니다';
    $kr['NoPhpinfo'] = 'PHPINFO()를 사용할수 없습니다';
    $kr['NoReturn'] = '반환값 없음';
    $kr['FileSent'] = '파일 전송';
    $kr['SpaceLimReached'] = '저장공가 여유가 없습니다';
    $kr['InvExt'] = '유효하지 않은 확장자';
    $kr['FileNoOverw'] = '파일을 덮어 쓸수 없습니다';
    $kr['FileOverw'] = '파일을 덮어 썼습니다';
    $kr['FileIgnored'] = '파일이 무시되었습니다';
    $kr['ChkVer'] = '에서 새버전 확인';
    $kr['ChkVerAvailable'] = '새로운 버전이 있습니다. 다운받으려면 클릭하세요!!';
    $kr['ChkVerNotAvailable'] = '새로운 버전이 없습니다. :(';
    $kr['ChkVerError'] = '연결 오류';
    $kr['Website'] = '웹사이트';
    $kr['SendingForm'] = '파일을 전송중입니다. 기다리세요';
    $kr['NoFileSel'] = '파일이 선택되지 않았습니다';
    $kr['SelAll'] = '모든';
    $kr['SelNone'] = '제로';
    $kr['SelInverse'] = '역';
    $kr['IOFilename'] = 'Specify IOFilename';
    $kr['SolutionFilename'] = 'Specify Solution filename';
 
    // German - by Guido Ogrzal
    $de1['Version'] = 'Version';
    $de1['DocRoot'] = 'Dokument Wurzelverzeichnis';
    $de1['FLRoot'] = 'Dateimanager Wurzelverzeichnis';
    $de1['Name'] = 'Name';
    $de1['And'] = 'und';
    $de1['Enter'] = 'Eintreten';
    $de1['Send'] = 'Senden';
    $de1['Refresh'] = 'Aktualisieren';
    $de1['SaveConfig'] = 'Konfiguration speichern';
    $de1['SavePass'] = 'Passwort speichern';
    $de1['SaveFile'] = 'Datei speichern';
    $de1['Save'] = 'Speichern';
    $de1['Leave'] = 'Verlassen';
    $de1['Edit'] = 'Bearbeiten';
    $de1['View'] = 'Ansehen';
    $de1['Config'] = 'Konfigurieren';
    $de1['Ren'] = 'Umbenennen';
    $de1['Rem'] = 'Löschen';
    $de1['Compress'] = 'Komprimieren';
    $de1['Decompress'] = 'Dekomprimieren';
    $de1['ResolveIDs'] = 'Resolve IDs';
    $de1['Move'] = 'Verschieben';
    $de1['Copy'] = 'Kopieren';
    $de1['ServerInfo'] = 'Server-Info';
    $de1['CreateDir'] = 'Neues Verzeichnis';
    $de1['CreateArq'] = 'Neue Datei';
    $de1['ExecCmd'] = 'Kommando';
    $de1['Upload'] = 'Datei hochladen';
    $de1['UploadEnd'] = 'Datei hochladen beendet';
    $de1['Perm'] = 'Erlaubnis';
    $de1['Perms'] = 'Erlaubnis';
    $de1['Owner'] = 'Besitzer';
    $de1['Group'] = 'Gruppe';
    $de1['Other'] = 'Andere';
    $de1['Size'] = 'Größe';
    $de1['Date'] = 'Datum';
    $de1['Type'] = 'Typ';
    $de1['Free'] = 'frei';
    $de1['Shell'] = 'Shell';
    $de1['Read'] = 'Lesen';
    $de1['Write'] = 'Schreiben';
    $de1['Exec'] = 'Ausführen';
    $de1['Apply'] = 'Bestätigen';
    $de1['StickyBit'] = 'Sticky Bit';
    $de1['Pass'] = 'Passwort';
    $de1['Lang'] = 'Sprache';
    $de1['File'] = 'Datei';
    $de1['File_s'] = 'Datei(en)';
    $de1['Dir_s'] = 'Verzeichniss(e)';
    $de1['To'] = '-&gt;';
    $de1['Destination'] = 'Ziel';
    $de1['Configurations'] = 'Konfiguration';
    $de1['JSError'] = 'JavaScript Fehler';
    $de1['NoSel'] = 'Es gibt keine selektierten Objekte';
    $de1['SelDir'] = 'Selektiere das Zielverzeichnis im linken Verzeichnisbaum';
    $de1['TypeDir'] = 'Trage den Verzeichnisnamen ein';
    $de1['TypeArq'] = 'Trage den Dateinamen ein';
    $de1['TypeCmd'] = 'Gib das Kommando ein';
    $de1['TypeArqComp'] = 'Trage den Dateinamen ein.\\nDie Dateierweiterung wird den Kompressiontyp bestimmen.\\nBsp.:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $de1['RemSel'] = 'LÖSCHE die selektierten Objekte';
    $de1['NoDestDir'] = 'Das selektierte Zielverzeichnis existiert nicht';
    $de1['DestEqOrig'] = 'Quell- und Zielverzeichnis stimmen überein';
    $de1['InvalidDest'] = 'Zielverzeichnis ist ungültig';
    $de1['NoNewPerm'] = 'Neue Zugriffserlaubnis konnte nicht gesetzt werden';
    $de1['CopyTo'] = 'KOPIERE nach';
    $de1['MoveTo'] = 'VERSCHIEBE nach';
    $de1['AlterPermTo'] = 'ÄNDERE ZUGRIFFSERLAUBSNIS in';
    $de1['ConfExec'] = 'Bestätige AUSFÜHRUNG';
    $de1['ConfRem'] = 'Bestätige LÖSCHEN';
    $de1['EmptyDir'] = 'Leeres Verzeichnis';
    $de1['IOError'] = 'Eingabe/Ausgabe-Fehler';
    $de1['FileMan'] = 'PHP File Manager';
    $de1['TypePass'] = 'Trage das Passwort ein';
    $de1['InvPass'] = 'Ungültiges Passwort';
    $de1['ReadDenied'] = 'Lesezugriff verweigert';
    $de1['FileNotFound'] = 'Datei nicht gefunden';
    $de1['AutoClose'] = 'Schließen, wenn fertig';
    $de1['OutDocRoot'] = 'Datei außerhalb von DOCUMENT_ROOT';
    $de1['NoCmd'] = 'Fehler: Es wurde kein Kommando eingetragen';
    $de1['ConfTrySave'] = 'Keine Schreibberechtigung für die Datei.\\nVersuche trotzdem zu speichern';
    $de1['ConfSaved'] = 'Konfiguration gespeichert';
    $de1['PassSaved'] = 'Passwort gespeichert';
    $de1['FileDirExists'] = 'Datei oder Verzeichnis existiert schon';
    $de1['NoPhpinfo'] = 'Funktion phpinfo ist inaktiv';
    $de1['NoReturn'] = 'keine Rückgabe';
    $de1['FileSent'] = 'Datei wurde gesendet';
    $de1['SpaceLimReached'] = 'Verfügbares Speicherlimit wurde erreicht';
    $de1['InvExt'] = 'Ungültige Dateiendung';
    $de1['FileNoOverw'] = 'Datei kann nicht überschrieben werden';
    $de1['FileOverw'] = 'Datei überschrieben';
    $de1['FileIgnored'] = 'Datei ignoriert';
    $de1['ChkVer'] = 'Prüfe auf neue Version';
    $de1['ChkVerAvailable'] = 'Neue Version verfügbar; klicke hier, um den Download zu starten!!';
    $de1['ChkVerNotAvailable'] = 'Keine neue Version gefunden. :(';
    $de1['ChkVerError'] = 'Verbindungsfehler.';
    $de1['Website'] = 'Webseite';
    $de1['SendingForm'] = 'Sende Dateien... Bitte warten.';
    $de1['NoFileSel'] = 'Keine Datei selektiert';
    $de1['SelAll'] = 'Alle';
    $de1['SelNone'] = 'Keine';
    $de1['SelInverse'] = 'Invertieren';
    $de1['Selected_s'] = 'selektiert';
    $de1['Total'] = 'Gesamt';
    $de1['Partition'] = 'Partition';
    $de1['RenderTime'] = 'Zeit, um die Seite anzuzeigen';
    $de1['Seconds'] = 's';
    $de1['ErrorReport'] = 'Fehlerreport';
    $de1['IOFilename'] = 'Specify IOFilename';
    $de1['SolutionFilename'] = 'Specify Solution filename';
 
    // German - by AXL
    $de2['Version'] = 'Version';
    $de2['DocRoot'] = 'Document Stammverzeichnis';
    $de2['FLRoot'] = 'Datei Manager Stammverzeichnis';
    $de2['Name'] = 'Name';
    $de2['And'] = 'und';
    $de2['Enter'] = 'Enter';
    $de2['Send'] = 'Senden';
    $de2['Refresh'] = 'Aktualisieren';
    $de2['SaveConfig'] = 'Konfiguration speichern';
    $de2['SavePass'] = 'Passwort speichern';
    $de2['SaveFile'] = 'Datei speichern';
    $de2['Save'] = 'Speichern';
    $de2['Leave'] = 'Verlassen';
    $de2['Edit'] = 'Bearb.';
    $de2['View'] = 'Anzeigen';
    $de2['Config'] = 'Konfigurieren';
    $de2['Ren'] = 'Umb.';
    $de2['Rem'] = 'Löschen';
    $de2['Compress'] = 'Komprimieren';
    $de2['Decompress'] = 'De-Komprimieren';
    $de2['ResolveIDs'] = 'IDs auflösen';
    $de2['Move'] = 'Versch.';
    $de2['Copy'] = 'Kopie';
    $de2['ServerInfo'] = 'Server Info';
    $de2['CreateDir'] = 'Verzeichnis erstellen';
    $de2['CreateArq'] = 'Datei erstellen';
    $de2['ExecCmd'] = 'Befehl ausführen';
    $de2['Upload'] = 'Upload';
    $de2['UploadEnd'] = 'Upload abgeschlossen';
    $de2['Perm'] = 'Rechte';
    $de2['Perms'] = 'Rechte';
    $de2['Owner'] = 'Besitzer';
    $de2['Group'] = 'Gruppe';
    $de2['Other'] = 'Andere';
    $de2['Size'] = 'Größe';
    $de2['Date'] = 'Datum';
    $de2['Type'] = 'Typ';
    $de2['Free'] = 'frei';
    $de2['Shell'] = 'Shell';
    $de2['Read'] = 'Read';
    $de2['Write'] = 'Write';
    $de2['Exec'] = 'Execute';
    $de2['Apply'] = 'Anwenden';
    $de2['StickyBit'] = 'Sticky Bit';
    $de2['Pass'] = 'Passwort';
    $de2['Lang'] = 'Sprache';
    $de2['File'] = 'Datei';
    $de2['File_s'] = 'Datei(en)';
    $de2['Dir_s'] = 'Verzeichnis(se)';
    $de2['To'] = 'an';
    $de2['Destination'] = 'Ziel';
    $de2['Configurations'] = 'Konfigurationen';
    $de2['JSError'] = 'JavaScript Fehler';
    $de2['NoSel'] = 'Keine Einträge ausgewählt';
    $de2['SelDir'] = 'Wählen Sie das Zeilverzeichnis im Verzeichnis links';
    $de2['TypeDir'] = 'Geben Sie den Verzeichnisnamen ein';
    $de2['TypeArq'] = 'Geben Sie den Dateinamen ein';
    $de2['TypeCmd'] = 'Geben Sie den Befehl ein';
    $de2['TypeArqComp'] = 'Geben Sie den Dateinamen ein.\\nDie Datei-Extension legt den Kopressionstyp fest.\\nBeispiel:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $de2['RemSel'] = 'Ausgewählte Dateien LÖSCHEN';
    $de2['NoDestDir'] = 'Es wurde kein Zielverzeichnis angegeben';
    $de2['DestEqOrig'] = 'Quell- und Zielverzeichnis sind identisch';
    $de2['InvalidDest'] = 'Zielverzeichnis ungültig';
    $de2['NoNewPerm'] = 'Unzureichende Rechte';
    $de2['CopyTo'] = 'KOPIEREN nach';
    $de2['MoveTo'] = 'VERSCHIEBEN nach';
    $de2['AlterPermTo'] = 'RECHTE ÄNDERN in';
    $de2['ConfExec'] = 'Bestätigung AUSFÜHREN';
    $de2['ConfRem'] = 'Bestätigung LÖSCHEN';
    $de2['EmptyDir'] = 'Leeres Verzeichnis';
    $de2['IOError'] = 'Ein-/Ausgabe-Fehler';
    $de2['FileMan'] = 'PHP File Manager';
    $de2['TypePass'] = 'Bitte geben Sie das Passwort ein';
    $de2['InvPass'] = 'Ungültiges Passwort';
    $de2['ReadDenied'] = 'Leasezugriff verweigert';
    $de2['FileNotFound'] = 'Datei nicht gefunden';
    $de2['AutoClose'] = 'Schliessen nach Beenden';
    $de2['OutDocRoot'] = 'Datei oberhalb DOCUMENT_ROOT';
    $de2['NoCmd'] = 'Fehler: Befehl nicht informed';
    $de2['ConfTrySave'] = 'Datei ohne Schreibberechtigung.\\nTrotzdem versuchen zu speichern';
    $de2['ConfSaved'] = 'Konfigurationen gespeichert';
    $de2['PassSaved'] = 'Passwort gespeichert';
    $de2['FileDirExists'] = 'Datei oder Verzeichnis existiert bereits';
    $de2['NoPhpinfo'] = 'Funktion phpinfo ausgeschaltet';
    $de2['NoReturn'] = 'keine Rückgabe';
    $de2['FileSent'] = 'Datei versandt';
    $de2['SpaceLimReached'] = 'Plattenplatz erschöpft';
    $de2['InvExt'] = 'Ungültige datei-Extension';
    $de2['FileNoOverw'] = 'Datei kann nicht überschrieben werden';
    $de2['FileOverw'] = 'Datei überschrieben';
    $de2['FileIgnored'] = 'Datei ignoriert';
    $de2['ChkVer'] = 'Überprüfe neuer Version';
    $de2['ChkVerAvailable'] = 'Neue Version. Hier klicken für Download!!';
    $de2['ChkVerNotAvailable'] = 'Keine neue Version verfügbar. :(';
    $de2['ChkVerError'] = 'Verbindungsfehler.';
    $de2['Website'] = 'Webseite';
    $de2['SendingForm'] = 'Sende Dateien, bitte warten';
    $de2['NoFileSel'] = 'Keine Dateien ausgewählt';
    $de2['SelAll'] = 'Alle';
    $de2['SelNone'] = 'Keine';
    $de2['SelInverse'] = 'Invers';
    $de2['Selected_s'] = 'ausgewählt';
    $de2['Total'] = 'Total';
    $de2['Partition'] = 'Partition';
    $de2['RenderTime'] = 'Zeit zum Erzeugen der Seite';
    $de2['Seconds'] = 'Sekunden';
    $de2['ErrorReport'] = 'Fehler berichten';
   $de2['IOFilename'] = 'Specify IOFilename';
    $de2['SolutionFilename'] = 'Specify Solution filename';
 
	// German - by Mathias Rothe
    $de3['Version'] = 'Version';
    $de3['DocRoot'] = 'Dokumenten Root';
    $de3['FLRoot'] = 'Datei Manager Root';
    $de3['Name'] = 'Name';
    $de3['And'] = 'und';
    $de3['Enter'] = 'Enter';
    $de3['Send'] = 'Senden';
    $de3['Refresh'] = 'Refresh';
    $de3['SaveConfig'] = 'Konfiguration speichern';
    $de3['SavePass'] = 'Passwort speichern';
    $de3['SaveFile'] = 'Datei speichern';
    $de3['Save'] = 'Speichern';
    $de3['Leave'] = 'Abbrechen';
    $de3['Edit'] = 'Bearbeiten';
    $de3['View'] = 'Anzeigen';
    $de3['Config'] = 'Konfiguration';
    $de3['Ren'] = 'Umbenennen';
    $de3['Rem'] = 'Entfernen';
    $de3['Compress'] = 'Packen';
    $de3['Decompress'] = 'Entpacken';
    $de3['ResolveIDs'] = 'IDs aufloesen';
    $de3['Move'] = 'Verschieben';
    $de3['Copy'] = 'Kopie';
    $de3['ServerInfo'] = 'Server Info';
    $de3['CreateDir'] = 'Neuer Ordner';
    $de3['CreateArq'] = 'Neue Datei';
    $de3['ExecCmd'] = 'Befehl ausfuehren';
    $de3['Upload'] = 'Upload';
    $de3['UploadEnd'] = 'Upload beendet';
    $de3['Perm'] = 'Rechte';
    $de3['Perms'] = 'Rechte';
    $de3['Owner'] = 'Eigent';
    $de3['Group'] = 'Gruppe';
    $de3['Other'] = 'Andere';
    $de3['Size'] = 'Groesse';
    $de3['Date'] = 'Datum';
    $de3['Type'] = 'Typ';
    $de3['Free'] = 'frei';
    $de3['Shell'] = 'Shell';
    $de3['Read'] = 'Lesen';
    $de3['Write'] = 'Schreiben';
    $de3['Exec'] = 'Ausfuehren';
    $de3['Apply'] = 'Bestaetigen';
    $de3['StickyBit'] = 'Sticky Bit';
    $de3['Pass'] = 'Passwort';
    $de3['Lang'] = 'Sprache';
    $de3['File'] = 'Datei';
    $de3['File_s'] = 'Datei(en)';
    $de3['Dir_s'] = 'Ordner';
	$de3['To'] = 'nach';
    $de3['Destination'] = 'Ziel';
    $de3['Configurations'] = 'Konfiguration';
    $de3['JSError'] = 'JavaScript Error';
    $de3['NoSel'] = 'Keine Objekte ausgewaehlt';
    $de3['SelDir'] = 'Waehlen Sie links das Zielverzeichnis aus';
    $de3['TypeDir'] = 'Verzeichnisname eingeben';
    $de3['TypeArq'] = 'Dateiname eingeben';
    $de3['TypeCmd'] = 'Befehl eingeben';
    $de3['TypeArqComp'] = 'Dateinamen eingeben.\\nDie Erweiterung definiert den Archiv-Typ.\\nEx:\\nname.zip\\nname.tar\\nname.bzip\\nname.gzip';
    $de3['RemSel'] = 'Entferne ausgewaehlte Objekte';
    $de3['NoDestDir'] = 'Kein Zielverzeichnis ausgewaehlt';
    $de3['DestEqOrig'] = 'Quelle und Zielverzeichnis sind gleich';
    $de3['InvalidDest'] = 'Zielverzeichnis ungueltig';
    $de3['NoNewPerm'] = 'Neue Rechte nicht gesetzt';
    $de3['CopyTo'] = 'Kopiere nach';
    $de3['MoveTo'] = 'Verschiebe nach';
    $de3['AlterPermTo'] = 'Aendere Rechte zu';
    $de3['ConfExec'] = 'Ausfuehren bestaetigen';
    $de3['ConfRem'] = 'Entfernen bestaetigen';
    $de3['EmptyDir'] = 'Leerer Ordner';
    $de3['IOError'] = 'I/O Fehler';
    $de3['FileMan'] = 'PHP Datei Manager';
    $de3['TypePass'] = 'Bitte Passwort eingeben';
    $de3['InvPass'] = 'Falsches Passwort';
    $de3['ReadDenied'] = 'Kein Lesezugriff';
    $de3['FileNotFound'] = 'Datei nicht gefunden';
    $de3['AutoClose'] = 'Beenden bei Fertigstellung';
    $de3['OutDocRoot'] = 'Datei ausserhalb des DOCUMENT_ROOT';
    $de3['NoCmd'] = 'Fehler: unbekannter Befehl';
    $de3['ConfTrySave'] = 'Datei ohne Schreibrecht.\\nVersuche dennoch zu speichern';
    $de3['ConfSaved'] = 'Konfiguration gespeichert';
    $de3['PassSaved'] = 'Passwort gespeichert';
    $de3['FileDirExists'] = 'Datei oder Verzeichnis existiert bereits';
    $de3['NoPhpinfo'] = 'Funktion phpinfo gesperrt';
    $de3['NoReturn'] = 'kein zurueck';
    $de3['FileSent'] = 'Datei gesendet';
    $de3['SpaceLimReached'] = 'Speicherplatz Grenze erreicht';
    $de3['InvExt'] = 'Ungueltige Erweiterung';
    $de3['FileNoOverw'] = 'Datei konnte nicht ueberschrieben werden';
    $de3['FileOverw'] = 'Datei ueberschrieben';
    $de3['FileIgnored'] = 'Datei ignoriert';
    $de3['ChkVer'] = 'Puefe eine neuere Version';
    $de3['ChkVerAvailable'] = 'Neue Version, hier klicken zum Download!!';
    $de3['ChkVerNotAvailable'] = 'Keine neuere Version vorhanden. :(';
    $de3['ChkVerError'] = 'Verbindungsfehler.';
    $de3['Website'] = 'Website';
    $de3['SendingForm'] = 'Dateien werden gesendet, bitte warten';
    $de3['NoFileSel'] = 'Keine Datei ausgewaehlt';
    $de3['SelAll'] = 'Alle';
    $de3['SelNone'] = 'Keine';
    $de3['SelInverse'] = 'Invertiere';
    $de3['Selected_s'] = 'ausgewaehlt';
    $de3['Total'] = 'gesamt';
    $de3['Partition'] = 'Partition';
    $de3['RenderTime'] = 'Zeit zur Erzeugung dieser Seite';
    $de3['Seconds'] = 'sec';
    $de3['ErrorReport'] = 'Fehlermeldungen';
   $de3['IOFilename'] = 'Specify IOFilename';
    $de3['SolutionFilename'] = 'Specify Solution filename';
 
    // French - by Jean Bilwes
    $fr1['Version'] = 'Version';
    $fr1['DocRoot'] = 'Racine des documents';
    $fr1['FLRoot'] = 'Racine du gestionnaire de fichers';
    $fr1['Name'] = 'Nom';
    $fr1['And'] = 'et';
    $fr1['Enter'] = 'Enter';
    $fr1['Send'] = 'Envoyer';
    $fr1['Refresh'] = 'Rafraichir';
    $fr1['SaveConfig'] = 'Enregistrer la Configuration';
    $fr1['SavePass'] = 'Enregistrer le mot de passe';
    $fr1['SaveFile'] = 'Enregistrer le fichier';
    $fr1['Save'] = 'Enregistrer';
    $fr1['Leave'] = 'Quitter';
    $fr1['Edit'] = 'Modifier';
    $fr1['View'] = 'Voir';
    $fr1['Config'] = 'Config';
    $fr1['Ren'] = 'Renommer';
    $fr1['Rem'] = 'Detruire';
    $fr1['Compress'] = 'Compresser';
    $fr1['Decompress'] = 'Decompresser';
    $fr1['ResolveIDs'] = 'Resoudre les IDs';
    $fr1['Move'] = 'Déplacer';
    $fr1['Copy'] = 'Copier';
    $fr1['ServerInfo'] = 'info du sreveur';
    $fr1['CreateDir'] = 'Créer un répertoire';
    $fr1['CreateArq'] = 'Créer un fichier';
    $fr1['ExecCmd'] = 'Executer une Commande';
    $fr1['Upload'] = 'Téléversement(upload)';
    $fr1['UploadEnd'] = 'Téléversement Fini';
    $fr1['Perm'] = 'Perm';
    $fr1['Perms'] = 'Permissions';
    $fr1['Owner'] = 'Propriétaire';
    $fr1['Group'] = 'Groupe';
    $fr1['Other'] = 'Autre';
    $fr1['Size'] = 'Taille';
    $fr1['Date'] = 'Date';
    $fr1['Type'] = 'Type';
    $fr1['Free'] = 'libre';
    $fr1['Shell'] = 'Shell';
    $fr1['Read'] = 'Lecture';
    $fr1['Write'] = 'Ecriture';
    $fr1['Exec'] = 'Executer';
    $fr1['Apply'] = 'Appliquer';
    $fr1['StickyBit'] = 'Sticky Bit';
    $fr1['Pass'] = 'Mot de passe';
    $fr1['Lang'] = 'Langage';
    $fr1['File'] = 'Fichier';
    $fr1['File_s'] = 'fichier(s)';
    $fr1['Dir_s'] = 'répertoire(s)';
    $fr1['To'] = 'à';
    $fr1['Destination'] = 'Destination';
    $fr1['Configurations'] = 'Configurations';
    $fr1['JSError'] = 'Erreur JavaScript';
    $fr1['NoSel'] = 'Rien n\'est sélectionné';
    $fr1['SelDir'] = 'Selectionnez le répertoire de destination dans le panneau gauche';
    $fr1['TypeDir'] = 'Entrer le nom du répertoire';
    $fr1['TypeArq'] = 'Entrer le nom du fichier';
    $fr1['TypeCmd'] = 'Entrer la commande';
    $fr1['TypeArqComp'] = 'Entrer le nom du fichier.\\nL\'extension définira le type de compression.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $fr1['RemSel'] = 'EFFACER les objets sélectionnés';
    $fr1['NoDestDir'] = 'Aucun répertoire de destination n\'est sélectionné';
    $fr1['DestEqOrig'] = 'Les répertoires source et destination sont identiques';
    $fr1['InvalidDest'] = 'Le répertoire de destination est invalide';
    $fr1['NoNewPerm'] = 'Nouvelle permission non établie';
    $fr1['CopyTo'] = 'COPIER vers';
    $fr1['MoveTo'] = 'DEPLACER vers';
    $fr1['AlterPermTo'] = 'CHANGER LES PERMISSIONS';
    $fr1['ConfExec'] = 'Confirmer l\'EXECUTION';
    $fr1['ConfRem'] = 'Confirmer la DESTRUCTION';
    $fr1['EmptyDir'] = 'Répertoire vide';
    $fr1['IOError'] = 'I/O Error';
    $fr1['FileMan'] = 'PHP File Manager';
    $fr1['TypePass'] = 'Entrer le mot de passe';
    $fr1['InvPass'] = 'Mot de passe invalide';
    $fr1['ReadDenied'] = 'Droit de lecture refusé';
    $fr1['FileNotFound'] = 'Fichier introuvable';
    $fr1['AutoClose'] = 'Fermer sur fin';
    $fr1['OutDocRoot'] = 'Fichier au delà de DOCUMENT_ROOT';
    $fr1['NoCmd'] = 'Erreur: Commande non renseignée';
    $fr1['ConfTrySave'] = 'Fichier sans permission d\'écriture.\\nJ\'essaie de l\'enregister';
    $fr1['ConfSaved'] = 'Configurations enreristrée';
    $fr1['PassSaved'] = 'Mot de passe enreristré';
    $fr1['FileDirExists'] = 'Le fichier ou le répertoire existe déjà';
    $fr1['NoPhpinfo'] = 'Function phpinfo désactivée';
    $fr1['NoReturn'] = 'pas de retour';
    $fr1['FileSent'] = 'Fichier envoyé';
    $fr1['SpaceLimReached'] = 'Espace maxi atteint';
    $fr1['InvExt'] = 'Extension invalide';
    $fr1['FileNoOverw'] = 'Le fichier ne peut pas etre écrasé';
    $fr1['FileOverw'] = 'Fichier écrasé';
    $fr1['FileIgnored'] = 'Fichier ignoré';
    $fr1['ChkVer'] = 'Verifier nouvelle version';
    $fr1['ChkVerAvailable'] = 'Nouvelle version, cliquer ici pour la téléchager!!';
    $fr1['ChkVerNotAvailable'] = 'Aucune mise a jour de disponible. :(';
    $fr1['ChkVerError'] = 'Erreur de connection.';
    $fr1['Website'] = 'siteweb';
    $fr1['SendingForm'] = 'Envoi des fichiers en cours, Patienter';
    $fr1['NoFileSel'] = 'Aucun fichier sélectionné';
    $fr1['SelAll'] = 'Tous';
    $fr1['SelNone'] = 'Aucun';
    $fr1['SelInverse'] = 'Inverser';
    $fr1['Selected_s'] = 'selectioné';
    $fr1['Total'] = 'total';
    $fr1['Partition'] = 'Partition';
    $fr1['RenderTime'] = 'Temps pour afficher cette page';
    $fr1['Seconds'] = 'sec';
    $fr1['ErrorReport'] = 'Rapport d\'erreur';
    $fr1['IOFilename'] = 'Specify IOFilename';
    $fr1['SolutionFilename'] = 'Specify Solution filename';
 
	// French - by Sharky
    $fr2['Version'] = 'Version';
    $fr2['DocRoot'] = 'Racine document';
    $fr2['FLRoot'] = 'Gestion des fichiers racine';
    $fr2['Name'] = 'Nom';
    $fr2['And'] = 'et';
    $fr2['Enter'] = 'Entrer';
    $fr2['Send'] = 'Envoi';
    $fr2['Refresh'] = 'Rafraîchir';
    $fr2['SaveConfig'] = 'Sauver configurations';
    $fr2['SavePass'] = 'Sauver mot de passe';
    $fr2['SaveFile'] = 'Sauver fichier';
    $fr2['Save'] = 'Sauver';
    $fr2['Leave'] = 'Permission';
    $fr2['Edit'] = 'Éditer';
    $fr2['View'] = 'Afficher';
    $fr2['Config'] = 'config';
    $fr2['Ren'] = 'Renommer';
    $fr2['Rem'] = 'Effacer';
    $fr2['Compress'] = 'Compresser';
    $fr2['Decompress'] = 'Décompresser';
    $fr2['ResolveIDs'] = 'Résoudre ID';
    $fr2['Move'] = 'Déplacer';
    $fr2['Copy'] = 'Copier';
    $fr2['ServerInfo'] = 'Information Serveur';
    $fr2['CreateDir'] = 'Créer un répertoire';
    $fr2['CreateArq'] = 'Créer un fichier';
    $fr2['ExecCmd'] = 'Executé une commande';
    $fr2['Upload'] = 'Transférer';
    $fr2['UploadEnd'] = 'Transfert terminé';
    $fr2['Perm'] = 'Perm';
    $fr2['Perms'] = 'Permissions';
    $fr2['Owner'] = 'Propriétaire';
    $fr2['Group'] = 'Groupe';
    $fr2['Other'] = 'Autre';
    $fr2['Size'] = 'Taille';
    $fr2['Date'] = 'date';
    $fr2['Type'] = 'Type';
    $fr2['Free'] = 'Libre';
    $fr2['Shell'] = 'Shell';
    $fr2['Read'] = 'lecture';
    $fr2['Write'] = 'écriture';
    $fr2['Exec'] = 'Execute';
    $fr2['Apply'] = 'Appliquer';
    $fr2['StickyBit'] = 'Bit figer';
    $fr2['Pass'] = 'mot de passe';
    $fr2['Lang'] = 'Language';
    $fr2['File'] = 'Fichier';
    $fr2['File_s'] = 'fichier(s)';
    $fr2['Dir_s'] = 'répertoire(s)';
    $fr2['To'] = 'à';
    $fr2['Destination'] = 'Destination';
    $fr2['Configurations'] = 'Configurations';
    $fr2['JSError'] = 'Erreur JavaScript';
    $fr2['NoSel'] = 'Il n\'y a pas d\'objets sélectionnés';
    $fr2['SelDir'] = 'Sélectionnez le répertoire de destination sur l\'arborescence de gauche';
    $fr2['TypeDir'] = 'Entrez le nom du répertoire';
    $fr2['TypeArq'] = 'Entrez le nom du fichier';
    $fr2['TypeCmd'] = 'Entrez la commande';
    $fr2['TypeArqComp'] = 'Entrez le fichier.\\nL\'extension définira le type de compression.\\nEx:\\nnom.zip\\nnom.tar\\nnom.bzip\\nnom.gzip';
    $fr2['RemSel'] = 'EFFACEZ l\'objet sélectionné';
    $fr2['NoDestDir'] = 'Il n\'y a aucun répertoire de destination sélectionné';
    $fr2['DestEqOrig'] = 'Origine et répertoires de destination sont identique';
    $fr2['InvalidDest'] = 'Répertoire de destination est invalide';
    $fr2['NoNewPerm'] = 'Nouvelle autorisation n\'a pas été configuré';
    $fr2['CopyTo'] = 'COPIE dans';
    $fr2['MoveTo'] = 'DÉPLACER dans';
    $fr2['AlterPermTo'] = 'CHANGER PERMISSIONS dans';
    $fr2['ConfExec'] = 'Confirmer EXECUTE';
    $fr2['ConfRem'] = 'Confirmer EFFACER';
    $fr2['EmptyDir'] = 'Répertoire vide';
    $fr2['IOError'] = 'I/O Erreur';
    $fr2['FileMan'] = 'Gestion de fichiers PHP';
    $fr2['TypePass'] = 'Entrer le mot de passe';
    $fr2['InvPass'] = 'Mot de passe invalide';
    $fr2['ReadDenied'] = 'Accès en lecture refuser';
    $fr2['FileNotFound'] = 'Fichier non-trouvé';
    $fr2['AutoClose'] = 'Fermez a la fin';
    $fr2['OutDocRoot'] = 'Fichier au-delà DOCUMENT_ROOT';
    $fr2['NoCmd'] = 'Erreur: Commande inconnue';
    $fr2['ConfTrySave'] = 'Fichier sans permission d\'écriture.\\nEssayez de sauver';
    $fr2['ConfSaved'] = 'Configurations sauvée';
    $fr2['PassSaved'] = 'Mot de passe sauvé';
    $fr2['FileDirExists'] = 'Fichier ou répertoire déjà existant';
    $fr2['NoPhpinfo'] = 'Function phpinfo désactivé';
    $fr2['NoReturn'] = 'sans retour possible';
    $fr2['FileSent'] = 'Fichier envoyé';
    $fr2['SpaceLimReached'] = 'Limite de d\'espace atteint';
    $fr2['InvExt'] = 'Extension invalide';
    $fr2['FileNoOverw'] = 'Fichier ne peut pas être écrasé';
    $fr2['FileOverw'] = 'Fichier écrasé';
    $fr2['FileIgnored'] = 'Fichier ignoré';
    $fr2['ChkVer'] = 'Check nouvelle version';
    $fr2['ChkVerAvailable'] = 'Nouvelle version, cliquez ici pour commencer le téléchargement!!';
    $fr2['ChkVerNotAvailable'] = 'Aucune nouvelle version disponible. :(';
    $fr2['ChkVerError'] = 'Erreur de connection.';
    $fr2['Website'] = 'Site Web';
    $fr2['SendingForm'] = 'Envoye de fichier, s\'il vous plaît patientez';
    $fr2['NoFileSel'] = 'Aucun fichier sélectionné';
    $fr2['SelAll'] = 'Tout';
    $fr2['SelNone'] = 'Aucuns';
    $fr2['SelInverse'] = 'Inverser';
    $fr2['Selected_s'] = 'sélectionné';
    $fr2['Total'] = 'total';
    $fr2['Partition'] = 'Partition';
    $fr2['RenderTime'] = 'Temps pour afficher la page';
    $fr2['Seconds'] = 'sec';
    $fr2['ErrorReport'] = 'Liste des erreurs';

    // French - by Michel Lainey
    $fr3['Version'] = 'Version';
    $fr3['DocRoot'] = 'Racine Document';
    $fr3['FLRoot'] = 'Racine File Manager';
    $fr3['Name'] = 'Nom';
    $fr3['And'] = 'et';
    $fr3['Enter'] = 'Valider';
    $fr3['Send'] = 'Envoyer';
    $fr3['Refresh'] = 'Raffraichir';
    $fr3['SaveConfig'] = 'Sauvegarder Config';
    $fr3['SavePass'] = 'Sauvegarder Password';
    $fr3['SaveFile'] = 'Sauvegarder Fichier';
    $fr3['Save'] = 'Sauvegarder';
    $fr3['Leave'] = 'Quitter';
    $fr3['Edit'] = 'Editer';
    $fr3['View'] = 'Visualiser';
    $fr3['Config'] = 'Config';
    $fr3['Ren'] = 'Renommer';
    $fr3['Rem'] = 'Supprimer';
    $fr3['Compress'] = 'Compresser';
    $fr3['Decompress'] = 'Décompresser';
    $fr3['ResolveIDs'] = 'Resoudre IDs';
    $fr3['Move'] = 'Déplacer';
    $fr3['Copy'] = 'Copier';
    $fr3['ServerInfo'] = 'Server Info';
    $fr3['CreateDir'] = 'Créer Répertoire';
    $fr3['CreateArq'] = 'Créer Fichier';
    $fr3['ExecCmd'] = 'Executer Commande';
    $fr3['Upload'] = 'Upload';
    $fr3['UploadEnd'] = 'Upload Fini';
    $fr3['Perm'] = 'Perm';
    $fr3['Perms'] = 'Permissions';
    $fr3['Owner'] = 'Propriétaire';
    $fr3['Group'] = 'Groupe';
    $fr3['Other'] = 'Autres';
    $fr3['Size'] = 'Taille';
    $fr3['Date'] = 'Date';
    $fr3['Type'] = 'Type';
    $fr3['Free'] = 'libre';
    $fr3['Shell'] = 'Shell';
    $fr3['Read'] = 'Lecture';
    $fr3['Write'] = 'Ecriture';
    $fr3['Exec'] = 'Execute';
    $fr3['Apply'] = 'Application';
    $fr3['StickyBit'] = 'Sticky Bit';
    $fr3['Pass'] = 'Password';
    $fr3['Lang'] = 'Language';
    $fr3['File'] = 'Fichier';
    $fr3['File_s'] = 'fichier(s)';
    $fr3['Dir_s'] = 'répertoire(s)';
    $fr3['To'] = 'à';
    $fr3['Destination'] = 'Destination';
    $fr3['Configurations'] = 'Configurations';
    $fr3['JSError'] = 'Erreur JavaScript';
    $fr3['NoSel'] = 'Aucun élément sélectionné';
    $fr3['SelDir'] = "Sélectionner le répertoire de destination dans l'arboresence de gauchethe destination directory on the left tree";
    $fr3['TypeDir'] = 'Indiquer le nom du répertoire';
    $fr3['TypeArq'] = 'Indiquer le nom du fichier';
    $fr3['TypeCmd'] = 'Entrer une commande';
    $fr3['TypeArqComp'] = "Indiquer le nom du fichier.\\nL'extension définira le type de compression.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip";
    $fr3['RemSel'] = "SUPPRIMER l'élément sélectionné";
    $fr3['NoDestDir'] = "Il n'y a pas de répertoire destination sélectionné";
    $fr3['DestEqOrig'] = 'Répertoire Origine et Destination sont identiques';
    $fr3['InvalidDest'] = 'Le répertoire de destination est invalide';
    $fr3['NoNewPerm'] = 'Nouvelle permission non appliquée';
    $fr3['CopyTo'] = 'COPIER vers';
    $fr3['MoveTo'] = 'DEPLACER vers';
    $fr3['AlterPermTo'] = 'CHANGER LES PERMISSIONS vers';
    $fr3['ConfExec'] = 'Confirmer EXECUTION';
    $fr3['ConfRem'] = 'Confirmer SUPPRESSION';
    $fr3['EmptyDir'] = 'Répertoire vide';
    $fr3['IOError'] = 'Erreur entrée/sortie';
    $fr3['FileMan'] = 'PHP File Manager';
    $fr3['TypePass'] = 'Saisir le mot de passe';
    $fr3['InvPass'] = 'Mot de passe invalide';
    $fr3['ReadDenied'] = 'Accès en lecture refusé';
    $fr3['FileNotFound'] = 'Fichier non trouvé';
    $fr3['AutoClose'] = 'Fermeture en fin de traitement';
    $fr3['OutDocRoot'] = 'Fichier en dessous de DOCUMENT_ROOT';
    $fr3['NoCmd'] = 'Erreur : Commande non renseignée';
    $fr3['ConfTrySave'] = "Fichier sans permission d'écriture.\\nTenter de sauver malgré tout";
    $fr3['ConfSaved'] = 'Configurations sauvegardée';
    $fr3['PassSaved'] = 'Password sauvegardé';
    $fr3['FileDirExists'] = 'Fichier ou répertoire déjà existant';
    $fr3['NoPhpinfo'] = 'Fonction phpinfo disactivée';
    $fr3['NoReturn'] = 'pas de retour';
    $fr3['FileSent'] = 'Fichier envoyé';
    $fr3['SpaceLimReached'] = 'Capacité maximale atteinte';
    $fr3['InvExt'] = 'Extension invalide';
    $fr3['FileNoOverw'] = 'Fichier ne pouvant être remplacé';
    $fr3['FileOverw'] = 'Fichier remplacé';
    $fr3['FileIgnored'] = 'Fichier ignoré';
    $fr3['ChkVer'] = 'Vérifier nouvelle version';
    $fr3['ChkVerAvailable'] = 'Nouvelle version, cliquer ici pour commencer le téléchargement !';
    $fr3['ChkVerNotAvailable'] = 'Pas de nouvelle version disponible. :(';
    $fr3['ChkVerError'] = 'Erreur de connection.';
    $fr3['Website'] = 'Site Web';
    $fr3['SendingForm'] = "Fichiers en cours d'envoi, merci de patienter";
    $fr3['NoFileSel'] = 'Pas de fichier sélectionné';
    $fr3['SelAll'] = 'Tous';
    $fr3['SelNone'] = 'Aucun';
    $fr3['SelInverse'] = 'Inverser';
    $fr3['Selected_s'] = 'sélectionné';
    $fr3['Total'] = 'total';
    $fr3['Partition'] = 'Partition';
    $fr3['RenderTime'] = 'Temps nécessaire pour obtenir cette page';
    $fr3['Seconds'] = 'sec';
    $fr3['ErrorReport'] = 'Erreur de compte rendu';

	// Dutch - by Leon Buijs
	$nl['Version'] = 'Versie';
	$nl['DocRoot'] = 'Document Root';
	$nl['FLRoot'] = 'File Manager Root';
	$nl['Name'] = 'Naam';
	$nl['And'] = 'en';
	$nl['Enter'] = 'Enter';
	$nl['Send'] = 'Verzend';
	$nl['Refresh'] = 'Vernieuw';
	$nl['SaveConfig'] = 'Configuratie opslaan';
	$nl['SavePass'] = 'Wachtwoord opslaan';
	$nl['SaveFile'] = 'Bestand opslaan';
	$nl['Save'] = 'Opslaan';
	$nl['Leave'] = 'Verlaten';
	$nl['Edit'] = 'Wijzigen';
	$nl['View'] = 'Toon';
	$nl['Config'] = 'Configuratie';
	$nl['Ren'] = 'Naam wijzigen';
	$nl['Rem'] = 'Verwijderen';
	$nl['Compress'] = 'Comprimeren';
	$nl['Decompress'] = 'Decomprimeren';
	$nl['ResolveIDs'] = 'Resolve IDs';
	$nl['Move'] = 'Verplaats';
	$nl['Copy'] = 'Kopieer';
	$nl['ServerInfo'] = 'Serverinformatie';
	$nl['CreateDir'] = 'Nieuwe map';
	$nl['CreateArq'] = 'Nieuw bestand';
	$nl['ExecCmd'] = 'Commando uitvoeren';
	$nl['Upload'] = 'Upload';
	$nl['UploadEnd'] = 'Upload voltooid';
    $nl['Perm'] = 'Rechten';
	$nl['Perms'] = 'Rechten';
	$nl['Owner'] = 'Eigenaar';
	$nl['Group'] = 'Groep';
	$nl['Other'] = 'Anderen';
	$nl['Size'] = 'Grootte';
	$nl['Date'] = 'Datum';
	$nl['Type'] = 'Type';
	$nl['Free'] = 'free';
	$nl['Shell'] = 'Shell';
	$nl['Read'] = 'Lezen';
	$nl['Write'] = 'Schrijven';
	$nl['Exec'] = 'Uitvoeren';
	$nl['Apply'] = 'Toepassen';
	$nl['StickyBit'] = 'Sticky Bit';
	$nl['Pass'] = 'Wachtwoord';
	$nl['Lang'] = 'Taal';
	$nl['File'] = 'Bestand';
	$nl['File_s'] = 'bestand(en)';
	$nl['Dir_s'] = 'map(pen)';
	$nl['To'] = 'naar';
	$nl['Destination'] = 'Bestemming';
	$nl['Configurations'] = 'Instellingen';
	$nl['JSError'] = 'Javascriptfout';
	$nl['NoSel'] = 'Er zijn geen bestanden geselecteerd';
	$nl['SelDir'] = 'Kies de bestemming in de boom aan de linker kant';
	$nl['TypeDir'] = 'Voer de mapnaam in';
	$nl['TypeArq'] = 'Voer de bestandsnaam in';
	$nl['TypeCmd'] = 'Voer het commando in';
	$nl['TypeArqComp'] = 'Voer de bestandsnaam in.\\nDe extensie zal het compressietype bepalen.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
	$nl['RemSel'] = 'VERWIJDER geselecteerde itens';
	$nl['NoDestDir'] = 'Er is geen doelmap geselecteerd';
	$nl['DestEqOrig'] = 'Bron- en doelmap zijn hetzelfde';
	$nl['InvalidDest'] = 'Doelmap is ongeldig';
	$nl['NoNewPerm'] = 'Nieuwe rechten niet geset';
	$nl['CopyTo'] = 'KOPIEER naar';
	$nl['MoveTo'] = 'VERPLAATS naar';
	$nl['AlterPermTo'] = 'VERANDER RECHTEN in';
	$nl['ConfExec'] = 'Bevestig UITVOEREN';
	$nl['ConfRem'] = 'Bevestig VERWIJDEREN';
	$nl['EmptyDir'] = 'Lege map';
	$nl['IOError'] = 'I/O Error';
	$nl['FileMan'] = 'PHP File Manager';
	$nl['TypePass'] = 'Voer het wachtwoord in';
	$nl['InvPass'] = 'Ongeldig wachtwoord';
	$nl['ReadDenied'] = 'Leestoegang ontzegd';
	$nl['FileNotFound'] = 'Bestand niet gevonden';
	$nl['AutoClose'] = 'Sluit na voltooien';
	$nl['OutDocRoot'] = 'Bestand buiten DOCUMENT_ROOT';
	$nl['NoCmd'] = 'Error: Command not informed';
	$nl['ConfTrySave'] = 'Bestand zonder schrijfrechten.\\nProbeer een andere manier';
	$nl['ConfSaved'] = 'Instellingen opgeslagen';
	$nl['PassSaved'] = 'Wachtwoord opgeslagen';
	$nl['FileDirExists'] = 'Bestand of map bestaat al';
	$nl['NoPhpinfo'] = 'Functie \'phpinfo\' is uitgeschakeld';
	$nl['NoReturn'] = 'no return';
	$nl['FileSent'] = 'Bestand verzonden';
	$nl['SpaceLimReached'] = 'Opslagruimtelimiet bereikt';
	$nl['InvExt'] = 'Ongeldige extensie';
	$nl['FileNoOverw'] = 'Bestand kan niet worden overgeschreven';
	$nl['FileOverw'] = 'Bestand overgeschreven';
	$nl['FileIgnored'] = 'Bestand genegeerd';
	$nl['ChkVer'] = 'Controleer nieuwe versie';
	$nl['ChkVerAvailable'] = 'Nieuwe versie, klik hier om de download te starten';
	$nl['ChkVerNotAvailable'] = 'Geen nieuwe versie beschikbaar';
	$nl['ChkVerError'] = 'Verbindingsfout.';
	$nl['Website'] = 'Website';
	$nl['SendingForm'] = 'Bestanden worden verzonden. Even geduld...';
	$nl['NoFileSel'] = 'Geen bestanden geselecteerd';
	$nl['SelAll'] = 'Alles';
	$nl['SelNone'] = 'Geen';
	$nl['SelInverse'] = 'Keer om';
	$nl['Selected_s'] = 'geselecteerd';
	$nl['Total'] = 'totaal';
	$nl['Partition'] = 'Partitie';
	$nl['RenderTime'] = 'Tijd voor maken van deze pagina';
	$nl['Seconds'] = 'sec';
	$nl['ErrorReport'] = 'Foutenrapport';

    // Italian - by Valerio Capello
    $it1['Version'] = 'Versione';
    $it1['DocRoot'] = 'Document Root';
    $it1['FLRoot'] = 'File Manager Root';
    $it1['Name'] = 'Nome';
    $it1['And'] = 'e';
    $it1['Enter'] = 'Immetti';
    $it1['Send'] = 'Invia';
    $it1['Refresh'] = 'Aggiorna';
    $it1['SaveConfig'] = 'Salva la Configurazione';
    $it1['SavePass'] = 'Salva la Password';
    $it1['SaveFile'] = 'Salva il File';
    $it1['Save'] = 'Salva';
    $it1['Leave'] = 'Abbandona';
    $it1['Edit'] = 'Modifica';
    $it1['View'] = 'Guarda';
    $it1['Config'] = 'Configurazione';
    $it1['Ren'] = 'Rinomina';
    $it1['Rem'] = 'Elimina';
    $it1['Compress'] = 'Comprimi';
    $it1['Decompress'] = 'Decomprimi';
    $it1['ResolveIDs'] = 'Risolvi IDs';
    $it1['Move'] = 'Sposta';
    $it1['Copy'] = 'Copia';
    $it1['ServerInfo'] = 'Informazioni sul Server';
    $it1['CreateDir'] = 'Crea Directory';
    $it1['CreateArq'] = 'Crea File';
    $it1['ExecCmd'] = 'Esegui Comando';
    $it1['Upload'] = 'Carica';
    $it1['UploadEnd'] = 'Caricamento terminato';
    $it1['Perm'] = 'Perm';
    $it1['Perms'] = 'Permessi';
    $it1['Owner'] = 'Proprietario';
    $it1['Group'] = 'Gruppo';
    $it1['Other'] = 'Altri';
    $it1['Size'] = 'Dimensioni';
    $it1['Date'] = 'Data';
    $it1['Type'] = 'Tipo';
    $it1['Free'] = 'liberi';
    $it1['Shell'] = 'Shell';
    $it1['Read'] = 'Lettura';
    $it1['Write'] = 'Scrittura';
    $it1['Exec'] = 'Esecuzione';
    $it1['Apply'] = 'Applica';
    $it1['StickyBit'] = 'Sticky Bit';
    $it1['Pass'] = 'Password';
    $it1['Lang'] = 'Lingua';
    $it1['File'] = 'File';
    $it1['File_s'] = 'file';
    $it1['Dir_s'] = 'directory';
    $it1['To'] = 'a';
    $it1['Destination'] = 'Destinazione';
    $it1['Configurations'] = 'Configurazione';
    $it1['JSError'] = 'Errore JavaScript';
    $it1['NoSel'] = 'Non ci sono elementi selezionati';
    $it1['SelDir'] = 'Scegli la directory di destinazione';
    $it1['TypeDir'] = 'Inserisci il nome della directory';
    $it1['TypeArq'] = 'Inserisci il nome del file';
    $it1['TypeCmd'] = 'Inserisci il comando';
    $it1['TypeArqComp'] = 'Inserisci il nome del file.\\nLa estensione definirà il tipo di compressione.\\nEsempio:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $it1['RemSel'] = 'ELIMINA gli elementi selezionati';
    $it1['NoDestDir'] = 'LA directory di destinazione non è stata selezionata';
    $it1['DestEqOrig'] = 'La directory di origine e di destinazione sono la stessa';
    $it1['InvalidDest'] = 'La directory di destinazione non è valida';
    $it1['NoNewPerm'] = 'Nuovi permessi non attivati';
    $it1['CopyTo'] = 'COPIA in';
    $it1['MoveTo'] = 'SPOSTA in';
    $it1['AlterPermTo'] = 'CAMBIA I PERMESSI: ';
    $it1['ConfExec'] = 'Conferma ESECUZIONE';
    $it1['ConfRem'] = 'Conferma ELIMINAZIONE';
    $it1['EmptyDir'] = 'Directory vuota';
    $it1['IOError'] = 'Errore di I/O';
    $it1['FileMan'] = 'PHP File Manager';
    $it1['TypePass'] = 'Immetti la password';
    $it1['InvPass'] = 'Password non valida';
    $it1['ReadDenied'] = 'Permesso di lettura negato';
    $it1['FileNotFound'] = 'File non trovato';
    $it1['AutoClose'] = 'Chiudi la finestra al termine';
    $it1['OutDocRoot'] = 'File oltre DOCUMENT_ROOT';
    $it1['NoCmd'] = 'Errore: Comando non informato';
    $it1['ConfTrySave'] = 'File senza permesso di scrittura.\\nProvo a salvare comunque';
    $it1['ConfSaved'] = 'Configurazione salvata';
    $it1['PassSaved'] = 'Password salvata';
    $it1['FileDirExists'] = 'Il file o la directory esiste già';
    $it1['NoPhpinfo'] = 'La funzione phpinfo è disabilitata';
    $it1['NoReturn'] = 'senza Return';
    $it1['FileSent'] = 'File inviato';
    $it1['SpaceLimReached'] = 'è stato raggiunto il limite di spazio disponibile';
    $it1['InvExt'] = 'Estensione non valida';
    $it1['FileNoOverw'] = 'Il file non può essere sovrascritto';
    $it1['FileOverw'] = 'File sovrascritto';
    $it1['FileIgnored'] = 'File ignorato';
    $it1['ChkVer'] = 'Controlla se è disponibile una nuova versione';
    $it1['ChkVerAvailable'] = 'è disponibile una nuova versione: premi qui per scaricarla.';
    $it1['ChkVerNotAvailable'] = 'Non è disponibile nessuna nuova versione. :(';
    $it1['ChkVerError'] = 'Errore di connessione.';
    $it1['Website'] = 'Sito Web';
    $it1['SendingForm'] = 'Invio file, attendere prego';
    $it1['NoFileSel'] = 'Nessun file selezionato';
    $it1['SelAll'] = 'Tutti';
    $it1['SelNone'] = 'Nessuno';
    $it1['SelInverse'] = 'Inverti';
    $it1['Selected_s'] = 'selezionato';
    $it1['Total'] = 'totali';
    $it1['Partition'] = 'Partizione';
    $it1['RenderTime'] = 'Tempo per elaborare questa pagina';
    $it1['Seconds'] = 'sec';
    $it1['ErrorReport'] = 'Error Reporting';

    // Italian - by Federico Corrà
    $it2['Version'] = 'Versione';
    $it2['DocRoot'] = 'Root Documenti';
    $it2['FLRoot'] = 'Root del File Manager';
    $it2['Name'] = 'Nome';
    $it2['And'] = 'e';
    $it2['Enter'] = 'Invio';
    $it2['Send'] = 'Spedisci';
    $it2['Refresh'] = 'Aggiorna';
    $it2['SaveConfig'] = 'Salva configurazioni';
    $it2['SavePass'] = 'Salva password';
    $it2['SaveFile'] = 'Salva file';
    $it2['Save'] = 'Salva';
    $it2['Leave'] = 'Esci';
    $it2['Edit'] = 'Modifica';
    $it2['View'] = 'Visualizza';
    $it2['Config'] = 'Configura';
    $it2['Ren'] = 'Rinomina';
    $it2['Rem'] = 'Cancella';
    $it2['Compress'] = 'Comprimi';
    $it2['Decompress'] = 'Decomprimi';
    $it2['ResolveIDs'] = 'Risolvi ID';
    $it2['Move'] = 'Muovi';
    $it2['Copy'] = 'Copia';
    $it2['ServerInfo'] = 'Server info';
    $it2['CreateDir'] = 'Crea cartella';
    $it2['CreateArq'] = 'Crea file';
    $it2['ExecCmd'] = 'Esegui comando';
    $it2['Upload'] = 'Upload';
    $it2['UploadEnd'] = 'Upload terminato';
    $it2['Perm'] = 'Perm';
    $it2['Perms'] = 'Permessi';
    $it2['Owner'] = 'Owner';
    $it2['Group'] = 'Grouppo';
    $it2['Other'] = 'Altro';
    $it2['Size'] = 'Dimensione';
    $it2['Date'] = 'Data';
    $it2['Type'] = 'Tipo';
    $it2['Free'] = 'liberi';
    $it2['Shell'] = 'Shell';
    $it2['Read'] = 'Lettura';
    $it2['Write'] = 'Scrittura';
    $it2['Exec'] = 'Esecuzione';
    $it2['Apply'] = 'Applica';
    $it2['StickyBit'] = 'Sticky Bit';
    $it2['Pass'] = 'Password';
    $it2['Lang'] = 'Lingua';
    $it2['File'] = 'File';
    $it2['File_s'] = 'file';
    $it2['Dir_s'] = 'cartella';
    $it2['To'] = 'a';
    $it2['Destination'] = 'Destinazione';
    $it2['Configurations'] = 'Configurazioni';
    $it2['JSError'] = 'Errore JavaScript';
    $it2['NoSel'] = 'Nessun item selezionato';
    $it2['SelDir'] = 'Scegli la cartella di destinazione sull\'albero a sinistra';
    $it2['TypeDir'] = 'Inserisci il nome della cartella';
    $it2['TypeArq'] = 'Inserisci il nome del file';
    $it2['TypeCmd'] = 'Inserisci il comando';
    $it2['TypeArqComp'] = 'Inserisci il nome del file.\\nL\'estensione definirà le modalità di compressione.\\nEx:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $it2['RemSel'] = 'ELIMINA gli item selezionati';
    $it2['NoDestDir'] = 'Non è stata selezionata la cartella di destinazione';
    $it2['DestEqOrig'] = 'La cartella di origine e di destinazione coincidono';
    $it2['InvalidDest'] = 'La cartella di destinazione non è valida';
    $it2['NoNewPerm'] = 'Nuovo permesso non definito';
    $it2['CopyTo'] = 'COPIA in';
    $it2['MoveTo'] = 'MUOVI in';
    $it2['AlterPermTo'] = 'CAMBIA PERMESSI in';
    $it2['ConfExec'] = 'Conferma ESECUZIONE';
    $it2['ConfRem'] = 'Conferma CANCELLA';
    $it2['EmptyDir'] = 'Cartella Vuota';
    $it2['IOError'] = 'Errore I/O';
    $it2['FileMan'] = 'PHP File Manager';
    $it2['TypePass'] = 'Inserisci la password';
    $it2['InvPass'] = 'Password non valida';
    $it2['ReadDenied'] = 'Accesso in lettura non consentito';
    $it2['FileNotFound'] = 'File non trovato';
    $it2['AutoClose'] = 'Chiudi dopo aver completato';
    $it2['OutDocRoot'] = 'File oltre DOCUMENT_ROOT';
    $it2['NoCmd'] = 'Errore: comando non informato';
    $it2['ConfTrySave'] = 'Accesso in scrittura non consentito.\\nProva a salvare comunque';
    $it2['ConfSaved'] = 'Configurazioni salvate';
    $it2['PassSaved'] = 'Password salvate';
    $it2['FileDirExists'] = 'Il file o la cartella esiste già';
    $it2['NoPhpinfo'] = 'Funzione phpinfo disabilitata';
    $it2['NoReturn'] = 'Nessun ritorno';
    $it2['FileSent'] = 'File spedito';
    $it2['SpaceLimReached'] = 'Limite di spazio raggiunto';
    $it2['InvExt'] = 'Estensione non valida';
    $it2['FileNoOverw'] = 'Il file non potrebbe essere sovrascritto';
    $it2['FileOverw'] = 'File sovrascritto';
    $it2['FileIgnored'] = 'File ignorato';
    $it2['ChkVer'] = 'Check nuova versione';
    $it2['ChkVerAvailable'] = 'Nuova versione, clicca qui per iniziare il download!!';
    $it2['ChkVerNotAvailable'] = 'Nessuna nuova versione disponibile. :(';
    $it2['ChkVerError'] = 'Errore di connessione.';
    $it2['Website'] = 'Sito Web';
    $it2['SendingForm'] = 'Invio file, prego attendi';
    $it2['NoFileSel'] = 'Nessun file selezionato';
    $it2['SelAll'] = 'Tutti';
    $it2['SelNone'] = 'Nessuno';
    $it2['SelInverse'] = 'Inverti';
    $it2['Selected_s'] = 'selezionati';
    $it2['Total'] = 'totale';
    $it2['Partition'] = 'Partizione';
    $it2['RenderTime'] = 'Tempo per renderizzare questa pagina';
    $it2['Seconds'] = 'sec';
    $it2['ErrorReport'] = 'Report errori';

    // Italian - by Luca Zorzi
    $it3['Version'] = 'Versione';
    $it3['DocRoot'] = 'Document Root';
    $it3['FLRoot'] = 'Root del File Manager';
    $it3['Name'] = 'Nome';
    $it3['And'] = 'e';
    $it3['Enter'] = 'Invio';
    $it3['Send'] = 'Invia';
    $it3['Refresh'] = 'Aggiorna';
    $it3['SaveConfig'] = 'Salva le impostazioni';
    $it3['SavePass'] = 'Salva la Password';
    $it3['SaveFile'] = 'Salva il File';
    $it3['Save'] = 'Salva';
    $it3['Leave'] = 'Annulla';
    $it3['Edit'] = 'Modifica';
    $it3['View'] = 'Guarda';
    $it3['Config'] = 'Impostazioni';
    $it3['Ren'] = 'Rinomina';
    $it3['Rem'] = 'Elimina';
    $it3['Compress'] = 'Comprimi';
    $it3['Decompress'] = 'Decomprimi';
    $it3['ResolveIDs'] = 'Risolvi ID';
    $it3['Move'] = 'Sposta';
    $it3['Copy'] = 'Copia';
    $it3['ServerInfo'] = 'Server Info';
    $it3['CreateDir'] = 'Crea Cartella';
    $it3['CreateArq'] = 'Crea File';
    $it3['ExecCmd'] = 'Esegui Comando';
    $it3['Upload'] = 'Upload';
    $it3['UploadEnd'] = 'Upload completato';
    $it3['Perm'] = 'Perm';
    $it3['Perms'] = 'Permessi';
    $it3['Owner'] = 'Proprietario';
    $it3['Group'] = 'Gruppo';
    $it3['Other'] = 'Altri';
    $it3['Size'] = 'Dimensione';
    $it3['Date'] = 'Data';
    $it3['Type'] = 'Tipo';
    $it3['Free'] = 'libero';
    $it3['Shell'] = 'Shell';
    $it3['Read'] = 'Lettura';
    $it3['Write'] = 'Scruttura';
    $it3['Exec'] = 'Esecuzione';
    $it3['Apply'] = 'Applica';
    $it3['StickyBit'] = 'Bit Sticky';
    $it3['Pass'] = 'Password';
    $it3['Lang'] = 'Lingua';
    $it3['File'] = 'File';
    $it3['File_s'] = 'file';
    $it3['Dir_s'] = 'cartella/e';
    $it3['To'] = 'a';
    $it3['Destination'] = 'Destinazione';
    $it3['Configurations'] = 'Configurazioni';
    $it3['JSError'] = 'Errore JavaScript';
    $it3['NoSel'] = 'Non ci sono elementi selezioneti';
    $it3['SelDir'] = 'Scegli la cartella di destinazione nell\'elenco a sinistra';
    $it3['TypeDir'] = 'Inserisci il nome della cartella';
    $it3['TypeArq'] = 'Inserisci il nome del file';
    $it3['TypeCmd'] = 'Inserisci il comando';
    $it3['TypeArqComp'] = 'Inserisci il nome del file.\\nIl nome definir &agrave; il tipo della compressione .\\nEs:\\nnome.zip\\nnome.tar\\nnome.bzip\\nnome.gzip';
    $it3['RemSel'] = 'ELIMINA gli elementi selezionati';
    $it3['NoDestDir'] = 'Non hai selezionato la cartella di destinazione';
    $it3['DestEqOrig'] = 'La cartella di origine e destinazione &egrave; la stessa';
    $it3['InvalidDest'] = 'La cartella di destinazione non &egrave; valida';
    $it3['NoNewPerm'] = 'Nuovi permessi non impostati';
    $it3['CopyTo'] = 'COPIA in';
    $it3['MoveTo'] = 'SPOSTA in';
    $it3['AlterPermTo'] = 'CAMBIA I PERMESSI a';
    $it3['ConfExec'] = 'Conferma ESECUZIONE';
    $it3['ConfRem'] = 'Conferma ELIMINAZIONE';
    $it3['EmptyDir'] = 'CArtella vuota';
    $it3['IOError'] = 'Errore di I/O';
    $it3['FileMan'] = 'PHP File Manager';
    $it3['TypePass'] = 'Inserisci la password';
    $it3['InvPass'] = 'Password errata';
    $it3['ReadDenied'] = 'Accesso in lettura negato';
    $it3['FileNotFound'] = 'File non trovato';
    $it3['AutoClose'] = 'Chiudi alla fine';
    $it3['OutDocRoot'] = 'File fuori dalla DOCUMENT_ROOT';
    $it3['NoCmd'] = 'Errore: Comando non informato';
    $it3['ConfTrySave'] = 'File senza il permesso di scrittura.\\nProvare a salvarlo comunque';
    $it3['ConfSaved'] = 'Configurazione salvata';
    $it3['PassSaved'] = 'Password salvata';
    $it3['FileDirExists'] = 'Il file o la cartella esiste gi&agrave;';
    $it3['NoPhpinfo'] = 'Funzione phpinfo disabilitata';
    $it3['NoReturn'] = 'no return';
    $it3['FileSent'] = 'File inviato';
    $it3['SpaceLimReached'] = 'Limite di spazio raggiunto';
    $it3['InvExt'] = 'Estensione non valida';
    $it3['FileNoOverw'] = 'Il file non pu&ograve; essere sovrascritto';
    $it3['FileOverw'] = 'File sovrascritto';
    $it3['FileIgnored'] = 'File ignorato';
    $it3['ChkVer'] = 'Controlla la presnza di una nuova versione';
    $it3['ChkVerAvailable'] = 'Nuova versione, clicca qui per avviare il download!!';
    $it3['ChkVerNotAvailable'] = 'Nessuna nuova versione disponibile. :(';
    $it3['ChkVerError'] = 'Errore di connessione.';
    $it3['Website'] = 'Sito';
    $it3['SendingForm'] = 'Invio dei file, attendi';
    $it3['NoFileSel'] = 'Nessun file selezionato';
    $it3['SelAll'] = 'Tutti';
    $it3['SelNone'] = 'Nessuno';
    $it3['SelInverse'] = 'Inverti selezione';
    $it3['Selected_s'] = 'selezionato';
    $it3['Total'] = 'totale';
    $it3['Partition'] = 'Partizione';
    $it3['RenderTime'] = 'Tempo di generazione';
    $it3['Seconds'] = 'sec';
    $it3['ErrorReport'] = 'Error Reporting';

	// Italian - by Gianni
    $it4['Version'] = 'Versione';
    $it4['DocRoot'] = 'Root documenti';
    $it4['FLRoot'] = 'Root file manager';
    $it4['Name'] = 'Nome';
    $it4['And'] = 'e';
    $it4['Enter'] = 'Entra';
    $it4['Send'] = 'Invia';
    $it4['Refresh'] = 'Aggiorna';
    $it4['SaveConfig'] = 'Salva configurazioni';
    $it4['SavePass'] = 'Salva password';
    $it4['SaveFile'] = 'Salva file';
    $it4['Save'] = 'Salva';
    $it4['Leave'] = 'Esci';
    $it4['Edit'] = 'Modifica';
    $it4['View'] = 'Vedi';
    $it4['Config'] = 'Preferenze';
    $it4['Ren'] = 'Rinomina';
    $it4['Rem'] = 'Cancella';
    $it4['Compress'] = 'Comprimi';
    $it4['Decompress'] = 'Decomprimi';
    $it4['ResolveIDs'] = 'Risolvi IDs';
    $it4['Move'] = 'Sposta';
    $it4['Copy'] = 'Copia';
    $it4['ServerInfo'] = 'Versione PHP';
    $it4['CreateDir'] = 'Crea directory';
    $it4['CreateArq'] = 'Crea file';
    $it4['ExecCmd'] = 'Esegui comando';
    $it4['Upload'] = 'Upload';
    $it4['UploadEnd'] = 'Upload terminato';
    $it4['Perm'] = 'Perm';
    $it4['Perms'] = 'Permessi';
    $it4['Owner'] = 'Proprietario';
    $it4['Group'] = 'Gruppo';
    $it4['Other'] = 'Altro';
    $it4['Size'] = 'Dimensione';
    $it4['Date'] = 'Data';
    $it4['Type'] = 'Tipo';
    $it4['Free'] = 'liberi';
    $it4['Shell'] = 'Shell';
    $it4['Read'] = 'Lettura';
    $it4['Write'] = 'Scrittura';
    $it4['Exec'] = 'Esecuzione';
    $it4['Apply'] = 'Applica';
    $it4['StickyBit'] = 'Sticky Bit';
    $it4['Pass'] = 'Password';
    $it4['Lang'] = 'Lingua';
    $it4['File'] = 'File';
    $it4['File_s'] = 'files';
    $it4['Dir_s'] = 'directory';
    $it4['To'] = 'in';
    $it4['Destination'] = 'Destinazione';
    $it4['Configurations'] = 'Preferenze';
    $it4['JSError'] = 'Errore JavaScript';
    $it4['NoSel'] = 'Non ci sono elementi selezionati';
    $it4['SelDir'] = 'Seleziona una directory di destinazione a sinistra';
    $it4['TypeDir'] = 'Inserisci il nome della directory';
    $it4['TypeArq'] = 'Inserisci il nome del file';
    $it4['TypeCmd'] = 'Inserisci il comando';
    $it4['TypeArqComp'] = 'Inserisci il nome del file e tipo di compressione\\n(.Zip .Tar .Bzip .Gzip)';
    $it4['RemSel'] = 'Cancella gli elementi selezionati';
    $it4['NoDestDir'] = 'Seleziona una directory di destinazione';
    $it4['DestEqOrig'] = 'Origine e destinazione sono uguali';
    $it4['InvalidDest'] = 'Directory di destinazione non valida';
    $it4['NoNewPerm'] = 'Nuovi permessi non impostati';
    $it4['CopyTo'] = 'Copia in';
    $it4['MoveTo'] = 'Sposta in';
    $it4['AlterPermTo'] = 'Cambia permessi in';
    $it4['ConfExec'] = 'Conferma esecuzione';
    $it4['ConfRem'] = 'Conferma eliminazione';
    $it4['EmptyDir'] = 'Directory Vuota';
    $it4['IOError'] = 'Errore I/O';
    $it4['FileMan'] = 'PHP File Manager';
    $it4['TypePass'] = 'Inserisci la password';
    $it4['InvPass'] = 'Password non valida';
    $it4['ReadDenied'] = 'Accesso in lettura negato';
    $it4['FileNotFound'] = 'File non trovato';
    $it4['AutoClose'] = 'Chiudi al termine';
    $it4['OutDocRoot'] = 'File fuori dalla Root documenti';
    $it4['NoCmd'] = 'Errore: comando non informato';
    $it4['ConfTrySave'] = 'File senza permessi di scrittura.\\nRiprova a salvare';
    $it4['ConfSaved'] = 'Preferenze salvate';
    $it4['PassSaved'] = 'Password salvata';
    $it4['FileDirExists'] = 'Il file o la directory esistono già';
    $it4['NoPhpinfo'] = 'Funzione phpinfo disabilitata';
    $it4['NoReturn'] = 'Nessun ritorno';
    $it4['FileSent'] = 'File inviato';
    $it4['SpaceLimReached'] = 'Raggiunto spazio limite';
    $it4['InvExt'] = 'Estensione non valida';
    $it4['FileNoOverw'] = 'Il file non può essere sovrascritto';
    $it4['FileOverw'] = 'File sovrascritto';
    $it4['FileIgnored'] = 'File ignorato';
    $it4['ChkVer'] = 'Controlla aggiornamenti';
    $it4['ChkVerAvailable'] = 'Nuova versione, click qui per effettuare il download!';
    $it4['ChkVerNotAvailable'] = 'Nessuna nuova versione';
    $it4['ChkVerError'] = 'Errore di connessione';
    $it4['Website'] = 'Sito';
    $it4['SendingForm'] = 'Invio files, attendere...';
    $it4['NoFileSel'] = 'Nessun file selezionato';
    $it4['SelAll'] = 'Tutti';
    $it4['SelNone'] = 'Nessuno';
    $it4['SelInverse'] = 'Inverti';
    $it4['Selected_s'] = 'selezionati';
    $it4['Total'] = 'totale';
    $it4['Partition'] = 'Partizione';
    $it4['RenderTime'] = 'Tempo per il render di questa pagina';
    $it4['Seconds'] = 'sec';
    $it4['ErrorReport'] = 'Report errori';

    // Turkish - by Necdet Yazilimlari
    $tr['Version'] = 'Versiyon';
    $tr['DocRoot'] = 'Kok dosya';
    $tr['FLRoot'] = 'Kok dosya yoneticisi';
    $tr['Name'] = 'Isim';
    $tr['And'] = 've';
    $tr['Enter'] = 'Giris';
    $tr['Send'] = 'Yolla';
    $tr['Refresh'] = 'Yenile';
    $tr['SaveConfig'] = 'Ayarlari kaydet';
    $tr['SavePass'] = 'Parolayi kaydet';
    $tr['SaveFile'] = 'Dosyayi kaydet';
    $tr['Save'] = 'Kaydet';
    $tr['Leave'] = 'Ayril';
    $tr['Edit'] = 'Duzenle';
    $tr['View'] = 'Goster';
    $tr['Config'] = 'Yapilandirma';
    $tr['Ren'] = 'Yeniden adlandir';
    $tr['Rem'] = 'Sil';
    $tr['Compress'] = '.Zip';
    $tr['Decompress'] = '.ZipCoz';
    $tr['ResolveIDs'] = 'Kimlikleri coz';
    $tr['Move'] = 'Tasi';
    $tr['Copy'] = 'Kopyala';
    $tr['ServerInfo'] = 'Sunucu Bilgisi';
    $tr['CreateDir'] = 'Dizin olustur';
    $tr['CreateArq'] = 'Dosya olusutur';
    $tr['ExecCmd'] = 'Komut calistir';
    $tr['Upload'] = 'Dosya yukle';
    $tr['UploadEnd'] = 'Yukleme tamamlandi';
    $tr['Perm'] = 'Izinler';
    $tr['Perms'] = 'Izinler';
    $tr['Owner'] = 'Sahip';
    $tr['Group'] = 'Grup';
    $tr['Other'] = 'Diger';
    $tr['Size'] = 'Boyut';
    $tr['Date'] = 'Tarih';
    $tr['Type'] = 'Tip';
    $tr['Free'] = 'Bos';
    $tr['Shell'] = 'Kabuk';
    $tr['Read'] = 'Oku';
    $tr['Write'] = 'Yaz';
    $tr['Exec'] = 'Calistir';
    $tr['Apply'] = 'Uygula';
    $tr['StickyBit'] = 'Sabit bit';
    $tr['Pass'] = 'Parola';
    $tr['Lang'] = 'Dil';
    $tr['File'] = 'Dosya';
    $tr['File_s'] = 'Dosya(lar)';
    $tr['Dir_s'] = 'Dizin(ler)';
    $tr['To'] = 'icin';
    $tr['Destination'] = 'Hedef';
    $tr['Configurations'] = 'Yapilandirmalar';
    $tr['JSError'] = 'JavaScript hatasi';
    $tr['NoSel'] = 'Secilen oge yok';
    $tr['SelDir'] = 'Soldaki hedef dizin agaci secin';
    $tr['TypeDir'] = 'Dizin adini girin';
    $tr['TypeArq'] = 'Dosya adini girin';
    $tr['TypeCmd'] = 'Komut girin';
    $tr['TypeArqComp'] = 'Dosya ismini yazdiktan sonra sonuna .zip ekleyin';
    $tr['RemSel'] = 'Secili ogeleri sil';
    $tr['NoDestDir'] = 'Secili dizin yok';
    $tr['DestEqOrig'] = 'Kokenli ve esit gidis rehberi';
    $tr['InvalidDest'] = 'Hedef dizin gecersiz';
    $tr['NoNewPerm'] = 'Izinler uygun degil';
    $tr['CopyTo'] = 'Kopya icin';
    $tr['MoveTo'] = 'Tasi icin';
    $tr['AlterPermTo'] = 'Permission secin';
    $tr['ConfExec'] = 'Yapilandirmayi onayla';
    $tr['ConfRem'] = 'Simeyi onayla';
    $tr['EmptyDir'] = 'Dizin bos';
    $tr['IOError'] = 'Hata';
    $tr['FileMan'] = 'Necdet_Yazilimlari';
    $tr['TypePass'] = 'Parolayi girin';
    $tr['InvPass'] = 'Gecersiz parola';
    $tr['ReadDenied'] = 'Okumaya erisim engellendi';
    $tr['FileNotFound'] = 'Dosya bulunamadi';
    $tr['AutoClose'] = 'Otomatik kapat';
    $tr['OutDocRoot'] = 'Kok klasor disindaki dosya';
    $tr['NoCmd'] = 'Hata: Komut haberdar degil';
    $tr['ConfTrySave'] = 'Dosya yazma izniniz yok. Yine de kaydetmeyi deneyebilirsiniz.';
    $tr['ConfSaved'] = 'Ayarlar kaydedildi';
    $tr['PassSaved'] = 'Parola kaydedildi';
    $tr['FileDirExists'] = 'Dosya veya dizin zaten var';
    $tr['NoPhpinfo'] = 'Php fonksiyon bilgisi devre disi';
    $tr['NoReturn'] = 'Deger dondurmuyor';
    $tr['FileSent'] = 'Dosya gonderildi';
    $tr['SpaceLimReached'] = 'Disk limitine ulasildi';
    $tr['InvExt'] = 'Gecersiz uzanti';
    $tr['FileNoOverw'] = 'Dosya degistirilemiyor';
    $tr['FileOverw'] = 'Dosya degistiribiliyor';
    $tr['FileIgnored'] = 'Dosya kabul edildi';
    $tr['ChkVer'] = 'Yeni versiyonu kontrol et';
    $tr['ChkVerAvailable'] = 'Yeni surum bulundu. Indirmek icin buraya tiklayin.';
    $tr['ChkVerNotAvailable'] = 'Yeni surum bulunamadi.';
    $tr['ChkVerError'] = 'Baglanti hatasi';
    $tr['Website'] = 'Website';
    $tr['SendingForm'] = 'Dosyalar gonderiliyor, lutfen bekleyin';
    $tr['NoFileSel'] = 'Secili dosya yok';
    $tr['SelAll'] = 'Hepsi';
    $tr['SelNone'] = 'Hicbiri';
    $tr['SelInverse'] = 'Ters';
    $tr['Selected_s'] = 'Secili oge(ler)';
    $tr['Total'] = 'Toplam';
    $tr['Partition'] = 'Bolme';
    $tr['RenderTime'] = 'Olusturuluyor';
    $tr['Seconds'] = 'Saniye';
    $tr['ErrorReport'] = 'Hata raporu';

	// Россия - Евгений Рашев
	$ru['Version']='Версия';
	$ru['DocRoot']='Документ Root ';
	$ru['FLRoot']='Файловый менеджер';
	$ru['Name']='Имя';
	$ru['And']='и';
	$ru['Enter']='Enter';
	$ru['Send']='Отправить';
	$ru['Refresh']='Обновить';
	$ru['SaveConfig']='Сохранить конфигурацию';
	$ru['SavePass']='Сохранить пароль';
	$ru['SaveFile']='Сохранить файл ';
	$ru['Save']='Сохранить';
	$ru['Leave']='Оставь';
	$ru['Edit']='Изменить';
	$ru['View']='Просмотр';
	$ru['Config']='Настройки';
	$ru['Ren']='Переименовать';
	$ru['Rem']='Удалить';
	$ru['Compress']='Сжать';
	$ru['Decompress']='Распаковать';
	$ru['ResolveIDs']='Определять id';
	$ru['Move']='Переместить';
	$ru['Copy']='Копировать';
	$ru['ServerInfo']='Инфо о сервере';
	$ru['CreateDir']='Создать папку';
	$ru['CreateArq']='Создайте файл ';
	$ru['ExecCmd']='Выполнить';
	$ru['Upload']='Загрузить';
	$ru['UploadEnd']='Загружено';
	$ru['Perm']='Права';
	$ru['Perms']='Разрешения';
	$ru['Owner']='Владелец';
	$ru['Group']='Группа';
	$ru['Other']='Другие';
	$ru['Size']='Размер';
	$ru['Date']='Дата';
	$ru['Type']='Тип';
	$ru['Free']='Свободно';
	$ru['Shell']='Shell';
	$ru['Read']='Читать';
	$ru['Write']='Писать';
	$ru['Exec']='Выполнять';
	$ru['Apply']='Применить';
	$ru['StickyBit']='StickyBit';
	$ru['Pass']='Пароль';
	$ru['Lang']='Язык';
	$ru['File']='Файл';
	$ru['File_s']='Файл..';
	$ru['Dir_s']='Пап..';
	$ru['To']='в';
	$ru['Destination']='Назначение';
	$ru['Configurations']='Конфигурация';
	$ru['JSError']='Ошибка JavaScript';
	$ru['NoSel']='нет выбранных элементов';
	$ru['SelDir']='Выберите папку назначения на левом дереве ';
	$ru['TypeDir']='Введите имя каталога ';
	$ru['TypeArq']='Введите имя файла';
	$ru['TypeCmd']='Введите команду ';
	$ru['TypeArqComp']='Введите имя файла ,расширение\\n это позволит определить тип сжатия \\n Пример:.. \\n nome.zip \\n nome.tar \\n nome.bzip \\n nome.gzip ';
	$ru['RemSel']='Удалить выбранные элементы';
	$ru['NoDestDir']='нет выбранного каталога назначения';
	$ru['DestEqOrig']='Происхождение и назначение каталогов равны ';
	$ru['InvalidDest']='Назначение каталога недействительно';
	$ru['NoNewPerm']='Новые разрешения не установлены';
	$ru['CopyTo']='Копировать в ';
	$ru['MoveTo']='Переместить в';
	$ru['AlterPermTo']='Изменение разрешений в ';
	$ru['ConfExec']='Подтвердить ВЫПОЛНИТЬ ';
	$ru['ConfRem']='Подтвердить УДАЛЕНИЕ';
	$ru['EmptyDir']='Пустой каталог ';
	$ru['IOError']='I/O Error';
	$ru['FileMan']='PHP Файловый менеджер ';
	$ru['TypePass']='Введите пароль';
	$ru['InvPass']='Неверный пароль';
	$ru['ReadDenied']='Доступ запрещен ';
	$ru['FileNotFound']='Файл не найден';
	$ru['AutoClose']='Закрыть полностью ';
	$ru['OutDocRoot']='Файлы за пределами DOCUMENT_ROOT';
	$ru['NoCmd']='Ошибка: Не поддерживаемая команда';
	$ru['ConfTrySave']='Файл без прав на запись. \\n Сохранить в любом случае. ';
	$ru['ConfSaved']='Конфигурация сохранена';
	$ru['PassSaved']='Пароль сохранен';
	$ru['FileDirExists']='Файл или каталог уже существует';
	$ru['NoPhpinfo']='Функция PHPInfo отключена';
	$ru['NoReturn']='Нет возврата';
	$ru['FileSent']='Файл отправлен';
	$ru['SpaceLimReached']='Достигнут предел Пространства';
	$ru['InvExt']='Неверное расширение';
	$ru['FileNoOverw']='Файл не может быть перезаписан ';
	$ru['FileOverw']='Файл перезаписывается';
	$ru['FileIgnored']='Файл игнорируется';
	$ru['ChkVer']='Проверить обновление';
	$ru['ChkVerAvailable']=' Доступна новая версия, нажмите здесь, чтобы начать загрузку! ';
	$ru['ChkVerNotAvailable']='Нет новой версии. :(';
	$ru['ChkVerError']='Ошибка подключения. ';
	$ru['Website']='Сайт';
	$ru['SendingForm']='Отправка файлов, пожалуйста, подождите ';
	$ru['NoFileSel']='Нет выбранных файлов';
	$ru['SelAll']='Выделить все';
	$ru['SelNone']='Отмена';
	$ru['SelInverse']='Обратить';
	$ru['Selected_s']='Выбран';
	$ru['Total']='Всего';
	$ru['Partition']='Раздел';
	$ru['RenderTime']='Скрипт выполнен за';
	$ru['Seconds']='Секунд';
	$ru['ErrorReport']='Отчет об ошибках';

	// Catalan - by Pere Borràs AKA @Norl
    $cat['Version'] = 'Versió';
    $cat['DocRoot'] = 'Arrel del programa';
    $cat['FLRoot'] = 'Arrel de l`administrador d`arxius';
    $cat['Name'] = 'Nom';
    $cat['And'] = 'i';
    $cat['Enter'] = 'Entrar';
    $cat['Send'] = 'Enviar';
    $cat['Refresh'] = 'Refrescar';
    $cat['SaveConfig'] = 'Desar configuracions';
    $cat['SavePass'] = 'Desar clau';
    $cat['SaveFile'] = 'Desar Arxiu';
    $cat['Save'] = 'Desar';
    $cat['Leave'] = 'Sortir';
    $cat['Edit'] = 'Editar';
    $cat['View'] = 'Mirar';
    $cat['Config'] = 'Config.';
    $cat['Ren'] = 'Canviar nom';
    $cat['Rem'] = 'Esborrar';
    $cat['Compress'] = 'Comprimir';
    $cat['Decompress'] = 'Descomprimir';
    $cat['ResolveIDs'] = 'Resoldre IDs';
    $cat['Move'] = 'Moure';
    $cat['Copy'] = 'Copiar';
    $cat['ServerInfo'] = 'Info del Server';
    $cat['CreateDir'] = 'Crear Directori';
    $cat['CreateArq'] = 'Crear Arxiu';
    $cat['ExecCmd'] = 'Executar Comandament';
    $cat['Upload'] = 'Pujar';
    $cat['UploadEnd'] = 'Pujat amb èxit';
    $cat['Perm'] = 'Perm';
    $cat['Perms'] = 'Permisos';
    $cat['Owner'] = 'Propietari';
    $cat['Group'] = 'Grup';
    $cat['Other'] = 'Altre';
    $cat['Size'] = 'Tamany';
    $cat['Date'] = 'Data';
    $cat['Type'] = 'Tipus';
    $cat['Free'] = 'lliure';
    $cat['Shell'] = 'Executar';
    $cat['Read'] = 'Llegir';
    $cat['Write'] = 'Escriure';
    $cat['Exec'] = 'Executar';
    $cat['Apply'] = 'Aplicar';
    $cat['StickyBit'] = 'Sticky Bit';
    $cat['Pass'] = 'Clau';
    $cat['Lang'] = 'Llenguatje';
    $cat['File'] = 'Arxius';
    $cat['File_s'] = 'arxiu(s)';
    $cat['Dir_s'] = 'directori(s)';
    $cat['To'] = 'a';
    $cat['Destination'] = 'Destí';
    $cat['Configurations'] = 'Configuracions';
    $cat['JSError'] = 'Error de JavaScript';
    $cat['NoSel'] = 'No hi ha items seleccionats';
    $cat['SelDir'] = 'Seleccioneu el directori de destí a l`arbre de la dreta';
    $cat['TypeDir'] = 'Escrigui el nom del directori';
    $cat['TypeArq'] = 'Escrigui el nom de l`arxiu';
    $cat['TypeCmd'] = 'Escrigui el comandament';
    $cat['TypeArqComp'] = 'Escrigui el nombre del directorio.\\nL`extensió definirà el tipus de compressió.\\nEx:\\nnom.zip\\nnom.tar\\nnom.bzip\\nnom.gzip';
    $cat['RemSel'] = 'ESBORRAR items seleccionats';
    $cat['NoDestDir'] = 'No s`ha seleccionat el directori de destí';
    $cat['DestEqOrig'] = 'L`origen i el destí són iguals';
    $cat['InvalidDest'] = 'El destí del directori és invàlid';
    $cat['NoNewPerm'] = 'Els permisos no s`han pogut establir';
    $cat['CopyTo'] = 'COPIAR a';
    $cat['MoveTo'] = 'MOURE a';
    $cat['AlterPermTo'] = 'CAMBIAR PERMISOS a';
    $cat['ConfExec'] = 'Confirmar EXECUCIÓ';
    $cat['ConfRem'] = 'Confirmar ESBORRAT';
    $cat['EmptyDir'] = 'Directori buit';
    $cat['IOError'] = 'Error I/O';
    $cat['FileMan'] = 'PHP File Manager';
    $cat['TypePass'] = 'Escrigui la clau';
    $cat['InvPass'] = 'Clau invàlida';
    $cat['ReadDenied'] = 'Accés de lectura denegat';
    $cat['FileNotFound'] = 'Arxiu no trobat';
    $cat['AutoClose'] = 'Tancar al completar';
    $cat['OutDocRoot'] = 'Arxiu abans de DOCUMENT_ROOT';
    $cat['NoCmd'] = 'Error: No s`ha escrit cap comandament';
    $cat['ConfTrySave'] = 'Arxiu sense permisos d`escriptura.\\nIntenteu desar a un altre lloc';
    $cat['ConfSaved'] = 'Configuració Desada';
    $cat['PassSaved'] = 'Clau desada';
    $cat['FileDirExists'] = 'Arxiu o directori ja existent';
    $cat['NoPhpinfo'] = 'Funció phpinfo() no habilitada';
    $cat['NoReturn'] = 'sense retorn';
    $cat['FileSent'] = 'Arxiu enviat';
    $cat['SpaceLimReached'] = 'Límit d`espaci al disc assolit';
    $cat['InvExt'] = 'Extensió no vàlida';
    $cat['FileNoOverw'] = 'L`arxiu no ha pogut ser sobreescrit';
    $cat['FileOverw'] = 'Arxiu sobreescrit';
    $cat['FileIgnored'] = 'Arxiu ignorat';
    $cat['ChkVer'] = 'Revisar les actualitzacions';
    $cat['ChkVerAvailable'] = 'Nova versió, feu clic aquí per descarregar';
    $cat['ChkVerNotAvailable'] = 'La vostra versió és la més recent.';
    $cat['ChkVerError'] = 'Error de connexió.';
    $cat['Website'] = 'Lloc Web';
    $cat['SendingForm'] = 'Enviant arxius, esperi';
    $cat['NoFileSel'] = 'Cap arxiu seleccionat';
    $cat['SelAll'] = 'Tots';
    $cat['SelNone'] = 'Cap';
    $cat['SelInverse'] = 'Invers';
    $cat['Selected_s'] = 'seleccionat';
    $cat['Total'] = 'total';
    $cat['Partition'] = 'Partició';
    $cat['RenderTime'] = 'Generat en';
    $cat['Seconds'] = 'seg';
    $cat['ErrorReport'] = 'Informe d`error';

    $lang_ = $$lang;
    if (isset($lang_[$tag])) return html_encode($lang_[$tag]);
    //else return "[$tag]"; // So we can know what is missing
    return $en[$tag];
}
// +--------------------------------------------------
// | File System
// +--------------------------------------------------
function total_size($arg) {
    $total = 0;
    if (file_exists($arg)) {
        if (is_dir($arg)) {
            $handle = opendir($arg);
            while($aux = readdir($handle)) {
                if ($aux != "." && $aux != "..") $total += total_size($arg."/".$aux);
            }
            @closedir($handle);
        } else $total = filesize($arg);
    }
    return $total;
}
function total_delete($arg) {
    if (file_exists($arg)) {
        @chmod($arg,0711);
        if (is_dir($arg)) {
            $handle = opendir($arg);
            while($aux = readdir($handle)) {
                if ($aux != "." && $aux != "..") total_delete($arg."/".$aux);
            }
            @closedir($handle);
            rmdir($arg);
        } else unlink($arg);
    }
}
function total_copy($orig,$dest) {
    $ok = true;
    if (file_exists($orig)) {
        if (is_dir($orig)) {
            mkdir($dest,0711);
            $handle = opendir($orig);
            while(($aux = readdir($handle))&&($ok)) {
                if ($aux != "." && $aux != "..") $ok = total_copy($orig."/".$aux,$dest."/".$aux);
            }
            @closedir($handle);
        } else $ok = copy((string)$orig,(string)$dest);
    }
    return $ok;
}
function total_move($orig,$dest) {
    // Just why doesn't it has a MOVE alias?!
    return rename((string)$orig,(string)$dest);
}
function download(){
    global $current_dir,$filename;
    $filename = remove_special_chars($filename);
    $file = $current_dir.$filename;
    if(file_exists($file)){
        $is_denied = false;
        foreach($download_ext_filter as $key=>$ext){
            if (preg_match($ext,$filename)){
                $is_denied = true;
                break;
            }
        }
        if (!$is_denied){
            $size = filesize($file);
            header("Content-Type: application/save");
            header("Content-Length: $size");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Transfer-Encoding: binary");
            if ($fh = fopen("$file", "rb")){
                fpassthru($fh);
                fclose($fh);
            } else alert(et('ReadDenied').": ".$file);
        } else alert(et('ReadDenied').": ".$file);
    } else alert(et('FileNotFound').": ".$file);
}
function save_upload($temp_file,$filename,$dir_dest) {
    global $upload_ext_filter;
    $filename = remove_special_chars($filename);
    $file = $dir_dest.$filename;
    $filesize = filesize($temp_file);
    $is_denied = false;
    foreach($upload_ext_filter as $key=>$ext){
        if (preg_match($ext,$filename)){
            $is_denied = true;
            break;
        }
    }
    if (!$is_denied){
        if (!check_limit($filesize)){
	//echo "file:$file";
            if (file_exists($file)){
                if (unlink($file)){
                    if (copy($temp_file,$file)){
                        @chmod($file,0744);
                        $out = 6;
                    } else $out = 2;
                } else $out = 5;
            } else {
                if (copy($temp_file,$file)){
                    @chmod($file,0744);
                    $out = 1;
                } else $out = 2;
            }
	if(file_exists("/usr/bin/dos2unix")&&function_exists("system")) system("/usr/bin/dos2unix ".$file);
        } else $out = 3;
    } else $out = 4;
    return $out;
}
function zip_extract(){
  global $cmd_arg,$current_dir,$islinux;
  $zip = zip_open($current_dir.$cmd_arg);
  if ($zip) {
    while ($zip_entry = zip_read($zip)) {
        if (zip_entry_filesize($zip_entry)) {
            $complete_path = $path.dirname(zip_entry_name($zip_entry));
            $complete_name = $path.zip_entry_name($zip_entry);
	    $complete_path=remove_special_chars($complete_path);
            if(!file_exists($complete_path)) {
                $tmp = '';
                foreach(explode('/',$complete_path) AS $k) {
                    $tmp .= $k.'/';
                    if(!file_exists($tmp)) {
                        @mkdir($current_dir.$tmp, 0711);
                    }
                }
            }
            if (zip_entry_open($zip, $zip_entry, "r")) {
		$complete_name=remove_special_chars($complete_name);
                if ($fd = fopen($current_dir.$complete_name, 'w')){
                    fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                    fclose($fd);
                } else echo "fopen($current_dir.$complete_name) error<br>";
                zip_entry_close($zip_entry);
            } else echo "zip_entry_open($zip,$zip_entry) error<br>";
        }
    }
    zip_close($zip);
  }
}
// +--------------------------------------------------
// | Data Formating
// +--------------------------------------------------
function html_encode($str){
	global $charSet;
	$str = preg_replace(array('/&/', '/</', '/>/', '/"/'), array('&amp;', '&lt;', '&gt;', '&quot;'), $str);  // Bypass PHP to allow any charset!!
    $str = htmlentities($str, ENT_QUOTES, $charSet, false);
	return $str;
}
function rep($x,$y){
  if ($x) {
    $aux = "";
    for ($a=1;$a<=$x;$a++) $aux .= $y;
    return $aux;
  } else return "";
}
function str_zero($arg1,$arg2){
    if (strstr($arg1,"-") == false){
        $aux = intval($arg2) - strlen($arg1);
        if ($aux) return rep($aux,"0").$arg1;
        else return $arg1;
    } else {
        return "[$arg1]";
    }
}
function replace_double($sub,$str){
    $out=str_replace($sub.$sub,$sub,$str);
    while ( strlen($out) != strlen($str) ){
        $str=$out;
        $out=str_replace($sub.$sub,$sub,$str);
    }
    return $out;
}
function remove_special_chars($str){
   // $str = trim($str);
   // $str = strtr($str,"¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ!@#%&*()[]{}+=?/\\",
   //                   "YuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy_________________");
    $str = mb_ereg_replace ("\\.\\.","",$str);
    $str = mb_ereg_replace("\\\\","",$str);
    $str = mb_ereg_replace ("/","",$str);
    $str = mb_ereg_replace ("\\$","",$str);
    return $str;
}
function format_path($str){
    global $islinux;
    $str = trim($str);
    $str = str_replace("..","",str_replace("\\","/",str_replace("\$","",$str)));
    $done = false;
    while (!$done) {
        $str2 = str_replace("//","/",$str);
        if (strlen($str) == strlen($str2)) $done = true;
        else $str = $str2;
    }
    $tam = strlen($str);
    if ($tam){
        $last_char = $tam - 1;
        if ($str[$last_char] != "/") $str .= "/";
        if (!$islinux) $str = ucfirst($str);
    }
    return $str;
}
function array_csort() {
  $args = func_get_args();
  $marray = array_shift($args);
  $msortline = "return(array_multisort(";
   foreach ($args as $arg) {
       $i++;
       if (is_string($arg)) {
          foreach ($marray as $row) {
               $sortarr[$i][] = $row[$arg];
           }
       } else {
          $sortarr[$i] = $arg;
       }
       $msortline .= "\$sortarr[".$i."],";
   }
   $msortline .= "\$marray));";
   eval($msortline);
   return $marray;
}
function show_perms( $P ) {
   $sP = "<b>";
   if($P & 0x1000) $sP .= 'p';            // FIFO pipe
   elseif($P & 0x2000) $sP .= 'c';        // Character special
   elseif($P & 0x4000) $sP .= 'd';        // Directory
   elseif($P & 0x6000) $sP .= 'b';        // Block special
   elseif($P & 0x8000) $sP .= '&minus;';  // Regular
   elseif($P & 0xA000) $sP .= 'l';        // Symbolic Link
   elseif($P & 0xC000) $sP .= 's';        // Socket
   else $sP .= 'u';                       // UNKNOWN
   $sP .= "</b>";
   // owner - group - others
   $sP .= (($P & 0x0100) ? 'r' : '&minus;') . (($P & 0x0080) ? 'w' : '&minus;') . (($P & 0x0040) ? (($P & 0x0800) ? 's' : 'x' ) : (($P & 0x0800) ? 'S' : '&minus;'));
   $sP .= (($P & 0x0020) ? 'r' : '&minus;') . (($P & 0x0010) ? 'w' : '&minus;') . (($P & 0x0008) ? (($P & 0x0400) ? 's' : 'x' ) : (($P & 0x0400) ? 'S' : '&minus;'));
   $sP .= (($P & 0x0004) ? 'r' : '&minus;') . (($P & 0x0002) ? 'w' : '&minus;') . (($P & 0x0001) ? (($P & 0x0200) ? 't' : 'x' ) : (($P & 0x0200) ? 'T' : '&minus;'));
   return $sP;
}
function format_size($arg) {
    if ($arg>0){
        $j = 0;
        $ext = array(" bytes"," Kb"," Mb"," Gb"," Tb");
        while ($arg >= pow(1024,$j)) ++$j;
        return round($arg / pow(1024,$j-1) * 100) / 100 . $ext[$j-1];
    } else return "0 bytes";
}
function get_size($file) {
    return format_size(filesize($file));
}
function check_limit($new_filesize=0) {
    global $fm_current_root;
    global $quota_mb;
    if($quota_mb){
        $total = total_size($fm_current_root);
        if (floor(($total+$new_filesize)/(1024*1024)) > $quota_mb) return true;
    }
    return false;
}
function get_user($arg) {
    global $mat_passwd;
    $aux = "x:".trim($arg).":";
    for($x=0;$x<count($mat_passwd);$x++){
        if (strstr($mat_passwd[$x],$aux)){
         $mat = explode(":",$mat_passwd[$x]);
         return $mat[0];
        }
    }
    return $arg;
}
function get_group($arg) {
    global $mat_group;
    $aux = "x:".trim($arg).":";
    for($x=0;$x<count($mat_group);$x++){
        if (strstr($mat_group[$x],$aux)){
         $mat = explode(":",$mat_group[$x]);
         return $mat[0];
        }
    }
    return $arg;
}
function uppercase($str){
	global $charset;
    return mb_strtoupper($str, $charset);
}
function lowercase($str){
	global $charset;
    return mb_strtolower($str, $charset);
}
// +--------------------------------------------------
// | Interface
// +--------------------------------------------------
function html_header($header=""){
    global $charset,$fm_color;
    echo "
	<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>    
    <meta http-equiv=\"content-type\" content=\"text/html; charset=".$charset."\" />	
	<title>...:::: ".et('FileMan')."</title>
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        function Is(){
            this.appname = navigator.appName;
            this.appversion = navigator.appVersion;
            this.platform = navigator.platform;
            this.useragent = navigator.userAgent.toLowerCase();
            this.ie = ( this.appname == 'Microsoft Internet Explorer' );
            if (( this.useragent.indexOf( 'mac' ) != -1 ) || ( this.platform.indexOf( 'mac' ) != -1 )){
                this.sisop = 'mac';
            } else if (( this.useragent.indexOf( 'windows' ) != -1 ) || ( this.platform.indexOf( 'win32' ) != -1 )){
                this.sisop = 'windows';
            } else if (( this.useragent.indexOf( 'inux' ) != -1 ) || ( this.platform.indexOf( 'linux' ) != -1 )){
                this.sisop = 'linux';
            }
        }
        var is = new Is();
        function enterSubmit(keypressEvent,submitFunc){
            var kCode = (is.ie) ? keypressEvent.keyCode : keypressEvent.which
            if( kCode == 13) eval(submitFunc);
        }
        function getCookieVal (offset) {
            var endstr = document.cookie.indexOf (';', offset);
            if (endstr == -1) endstr = document.cookie.length;
            return unescape(document.cookie.substring(offset, endstr));
        }
        function getCookie (name) {
            var arg = name + '=';
            var alen = arg.length;
            var clen = document.cookie.length;
            var i = 0;
            while (i < clen) {
                var j = i + alen;
                if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
                i = document.cookie.indexOf(' ', i) + 1;
                if (i == 0) break;
            }
            return null;
        }
        function setCookie (name, value, expires) {
            var argv = setCookie.arguments;
            var argc = setCookie.arguments.length;
            var expires = (argc > 2) ? argv[2] : null;
            var path = (argc > 3) ? argv[3] : null;
            var domain = (argc > 4) ? argv[4] : null;
            var secure = (argc > 5) ? argv[5] : false;
            document.cookie = name + '=' + escape (value) +
            ((expires == null) ? '' : ('; expires=' + expires.toGMTString())) +
            ((path == null) ? '' : ('; path=' + path)) +
            ((domain == null) ? '' : ('; domain=' + domain)) +
            ((secure == true) ? '; secure' : '');
        }
        function delCookie (name) {
            var exp = new Date();
            exp.setTime (exp.getTime() - 1);
            var cval = getCookie (name);
            document.cookie = name + '=' + cval + '; expires=' + exp.toGMTString();
        }
        var frameWidth, frameHeight;
        function getFrameSize(){
            if (self.innerWidth){
                frameWidth = self.innerWidth;
                frameHeight = self.innerHeight;
            }else if (document.documentElement && document.documentElement.clientWidth){
                frameWidth = document.documentElement.clientWidth;
                frameHeight = document.documentElement.clientHeight;
            }else if (document.body){
                frameWidth = document.body.clientWidth;
                frameHeight = document.body.clientHeight;
            }else return false;
            return true;
        }
	var flag = ".(($setflag)?"true":"false")."
        function set_flag(arg) {
            flag = arg;
        }
        getFrameSize();
    //-->
    </script>
    $header
    </head>
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        var W = screen.width;
        var H = screen.height;
        var FONTSIZE = 0;
        switch (W){
            case 640:
                FONTSIZE = 8;
            break;
            case 800:
                FONTSIZE = 10;
            break;
            case 1024:
                FONTSIZE = 12;
            break;
            default:
                FONTSIZE = 14;
            break;
        }
    ";
    echo replace_double(" ",str_replace(chr(13),"",str_replace(chr(10),"","
        document.writeln('
        <style type=\"text/css\">
        body {
            font-family : Arial;
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #".$fm_color['Text'].";
            background-color: #".$fm_color['Bg'].";
        }
        table {
            font-family : Arial;
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #".$fm_color['Text'].";
            cursor: default;
        }
        input {
            font-family : Arial;
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #".$fm_color['Text'].";
        }
        textarea {
            font-family : Courier;
            font-size: 12px;
            font-weight : normal;
            color: #".$fm_color['Text'].";
        }
        a {
            font-family : Arial;
            font-size : '+FONTSIZE+'px;
            font-weight : bold;
            text-decoration: none;
            color: #".$fm_color['Text'].";
        }
        a:link {
            color: #".$fm_color['Text'].";
        }
        a:visited {
            color: #".$fm_color['Text'].";
        }
        a:hover {
            color: #".$fm_color['Link'].";
        }
        a:active {
            color: #".$fm_color['Text'].";
        }
        tr.entryUnselected {
            background-color: #".$fm_color['Entry'].";
        }
        tr.entryUnselected:hover {
            background-color: #".$fm_color['Over'].";
        }
        tr.entrySelected {
            background-color: #".$fm_color['Mark'].";
        }
        </style>
        ');
    ")));
    echo "
    //-->
    </script>
    ";
}
function reloadframe($ref,$frame_number,$Plus=""){
    global $current_dir,$path_info;
    echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        ".$ref.".frame".$frame_number.".location.href='".$path_info["basename"]."?frame=".$frame_number."&current_dir=".$current_dir.$Plus."';
    //-->
    </script>
    ";
}
function alert($arg){
    echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        alert('$arg');
    //-->
    </script>
    ";
}
function tree($dir_before,$dir_current,$indice){
    global $fm_current_root, $current_dir, $islinux;
    global $expanded_dir_list;
    $indice++;
    $num_dir = 0;
    $dir_name = str_replace($dir_before,"",$dir_current);
    $dir_before = str_replace("//","/",$dir_before);
    $dir_current = str_replace("//","/",$dir_current);
    $is_denied = false;
    if ($islinux) {
        $denied_list = "/proc#/dev";
        $mat = explode("#",$denied_list);
        foreach($mat as $key => $val){
            if ($dir_current == $val){
                $is_denied = true;
                break;
            }
        }
        unset($mat);
    }
    if (!$is_denied){
        if ($handle = @opendir($dir_current)){
            // Permitido
            while ($file = readdir($handle)){
                if ($file != "." && $file != ".." && is_dir("$dir_current/$file"))
                    $mat_dir[] = $file;
            }
            @closedir($handle);
            if (count($mat_dir)){
                sort($mat_dir,SORT_STRING);
                // with Sub-dir
                if ($indice != 0){
                    for ($aux=1;$aux<$indice;$aux++) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                if ($dir_before != $dir_current){
                    if (strstr($expanded_dir_list,":$dir_current/$dir_name")) $op_str = "[–]";
                    else $op_str = "[+]";
                    echo "<nobr><a href=\"JavaScript:go_dir('$dir_current/$dir_name')\">$op_str</a> <a href=\"JavaScript:go('$dir_current')\">$dir_name</a></nobr><br>\n";
                } else {
                    echo "<nobr><a href=\"JavaScript:go('$dir_current')\">$fm_current_root</a></nobr><br>\n";
                }
                for ($x=0;$x<count($mat_dir);$x++){
                    if (($dir_before == $dir_current)||(strstr($expanded_dir_list,":$dir_current/$dir_name"))){
                       // tree($dir_current."/",$dir_current."/".$mat_dir[$x],$indice);
                    } else flush();
                }
            } else {
              // no Sub-dir
              if ($dir_before != $dir_current){
                for ($aux=1;$aux<$indice;$aux++) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "<b>[&nbsp;&nbsp;]</b>";
                echo "<nobr><a href=\"JavaScript:go('$dir_current')\"> $dir_name</a></nobr><br>\n";
              } else {
                echo "<nobr><a href=\"JavaScript:go('$dir_current')\"> $fm_current_root</a></nobr><br>\n";
              }
            }
        } else {
            // denied
            if ($dir_before != $dir_current){
                for ($aux=1;$aux<$indice;$aux++) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "<b>[&nbsp;&nbsp;]</b>";
                echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $dir_name</font></a></nobr><br>\n";
            } else {
                echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $fm_current_root</font></a></nobr><br>\n";
            }

        }
    } else {
        // denied
        if ($dir_before != $dir_current){
            for ($aux=1;$aux<$indice;$aux++) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<b>[&nbsp;&nbsp;]</b>";
            echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $dir_name</font></a></nobr><br>\n";
        } else {
            echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $fm_current_root</font></a></nobr><br>\n";
        }
    }
}
function show_tree(){
    global $fm_current_root,$path_info,$setflag,$islinux,$cookie_cache_time;
    html_header("
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        function saveFrameSize(){
            if (getFrameSize()){
                var exp = new Date();
                exp.setTime(exp.getTime()+$cookie_cache_time);
                setCookie('leftFrameWidth',frameWidth,exp);
            }
        }
        window.onresize = saveFrameSize;
    //-->
    </script>");
    echo "<body marginwidth=\"0\" marginheight=\"0\">\n";
    echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        // Disable text selection, binding the onmousedown, but not for some elements, it must work.
        function disableTextSelection(e){
			var type = String(e.target.type);
			return (type.indexOf('select') != -1 || type.indexOf('button') != -1 || type.indexOf('input') != -1 || type.indexOf('radio') != -1);
		}
        function enableTextSelection(){return true}
        if (is.ie) document.onselectstart=new Function('return false')
        else {
            document.body.onmousedown=disableTextSelection
            document.body.onclick=enableTextSelection
        }
        var flag = ".(($setflag)?"true":"false")."
        function set_flag(arg) {
            flag = arg;
        }
        function go_dir(arg) {
            var setflag;
            setflag = (flag)?1:0;
            document.location.href='".addslashes($path_info["basename"])."?frame=2&setflag='+setflag+'&current_dir=".addslashes($current_dir)."&ec_dir='+arg;
        }
        function go(arg) {
            if (flag) {
                parent.frame3.set_dir_dest(arg+'/');
                flag = false;
            } else {
                parent.frame3.location.href='".addslashes($path_info["basename"])."?frame=3&current_dir='+arg+'/';
            }
        }
        function set_fm_current_root(arg){
            document.location.href='".addslashes($path_info["basename"])."?frame=2&set_fm_current_root='+escape(arg);
        }
        function atualizar(){
            document.location.href='".addslashes($path_info["basename"])."?frame=2';
        }
    //-->
    </script>
    ";
    echo "<table width=\"100%\" height=\"100%\" border=0 cellspacing=0 cellpadding=5>\n";
    echo "<form><tr valign=top height=10><td>";
    if (!$islinux){
        echo "<select name=drive onchange=\"set_fm_current_root(this.value)\">";
        $aux="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for($x=0;$x<strlen($aux);$x++){
			if ($handle = opendir($aux[$x].":/")){
    			@closedir($handle);
	            if (strstr(uppercase($fm_current_root),$aux[$x].":/")) $is_sel="selected";
	            else $is_sel="";
	            echo "<option $is_sel value=\"".$aux[$x].":/\">".$aux[$x].":/";
			}
        }
        echo "</select> ";
    }
    echo "<input type=button value=".et('Refresh')." onclick=\"atualizar()\"></tr></form>";
    echo "<tr valign=top><td>";
            clearstatcache();
            tree($fm_current_root,$fm_current_root,-1,0);
    echo "</td></tr>";
    echo "
        <form name=\"login_form\" action=\"".$path_info["basename"]."\" method=\"post\" target=\"_parent\">
        <input type=hidden name=action value=1>
        <tr>
        <td height=10 colspan=2><input type=submit value=\"".et('Leave')."\">
        </tr>
        </form>
    ";
    echo "</table>\n";
    echo "</body>\n</html>";
}
function getmicrotime(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
function dir_list_form() {
    global $fm_current_root,$current_dir,$quota_mb,$resolveIDs,$order_dir_list_by,$islinux,$cmd_name,$ip,$path_info,$fm_color;
    $ti = getmicrotime();
    clearstatcache();
    $out = "<table border=0 cellspacing=1 cellpadding=4 width=\"100%\" bgcolor=\"#eeeeee\">\n";
    if ($opdir = @opendir($current_dir)) {
        $has_files = false;
        $entry_count = 0;
        $total_size = 0;
        $entry_list = array();
        while ($file = readdir($opdir)) {
          if (($file != ".")&&($file != "..")){
			$entry_list[$entry_count]["size"] = 0;
			$entry_list[$entry_count]["sizet"] = 0;
			$entry_list[$entry_count]["type"] = "none";
            if (is_file($current_dir.$file)){
                $ext = lowercase(strrchr($file,"."));
                $entry_list[$entry_count]["type"] = "file";
                // Função filetype() returns only "file"...
                $entry_list[$entry_count]["size"] = filesize($current_dir.$file);
                $entry_list[$entry_count]["sizet"] = format_size($entry_list[$entry_count]["size"]);
                if (strstr($ext,".")){
                    $entry_list[$entry_count]["ext"] = $ext;
                    $entry_list[$entry_count]["extt"] = $ext;
                } else {
                    $entry_list[$entry_count]["ext"] = "";
                    $entry_list[$entry_count]["extt"] = "&nbsp;";
                }
                $has_files = true;
            } elseif (is_dir($current_dir.$file)) {
                // Recursive directory size disabled
                // $entry_list[$entry_count]["size"] = total_size($current_dir.$file);
		continue; // don't display subdir
                $entry_list[$entry_count]["size"] = 0;
                $entry_list[$entry_count]["sizet"] = "&nbsp;";
                $entry_list[$entry_count]["type"] = "dir";
            }
            $entry_list[$entry_count]["name"] = $file;
            $entry_list[$entry_count]["date"] = date("Ymd", filemtime($current_dir.$file));
            $entry_list[$entry_count]["time"] = date("his", filemtime($current_dir.$file));
            $entry_list[$entry_count]["datet"] = date("d/m/y h:i", filemtime($current_dir.$file));
            if ($islinux && $resolveIDs){
                $entry_list[$entry_count]["p"] = show_perms(fileperms($current_dir.$file));
                $entry_list[$entry_count]["u"] = get_user(fileowner($current_dir.$file));
                $entry_list[$entry_count]["g"] = get_group(filegroup($current_dir.$file));
            } else {
                $entry_list[$entry_count]["p"] = base_convert(fileperms($current_dir.$file),10,8);
                $entry_list[$entry_count]["p"] = substr($entry_list[$entry_count]["p"],strlen($entry_list[$entry_count]["p"])-3);
                $entry_list[$entry_count]["u"] = fileowner($current_dir.$file);
                $entry_list[$entry_count]["g"] = filegroup($current_dir.$file);
            }
            $total_size += $entry_list[$entry_count]["size"];
            $entry_count++;
          }
        }
        @closedir($opdir);

        if($entry_count){
            $or1="1A";
            $or2="2D";
            $or3="3A";
            $or4="4A";
            $or5="5A";
            $or6="6D";
            $or7="7D";
            switch($order_dir_list_by){
                case "1A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"name",SORT_STRING,SORT_ASC); $or1="1D"; break;
                case "1D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"name",SORT_STRING,SORT_DESC); $or1="1A"; break;
                case "2A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"p",SORT_STRING,SORT_ASC,"g",SORT_STRING,SORT_ASC,"u",SORT_STRING,SORT_ASC); $or2="2D"; break;
                case "2D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"p",SORT_STRING,SORT_DESC,"g",SORT_STRING,SORT_ASC,"u",SORT_STRING,SORT_ASC); $or2="2A"; break;
                case "3A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"u",SORT_STRING,SORT_ASC,"g",SORT_STRING,SORT_ASC); $or3="3D"; break;
                case "3D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"u",SORT_STRING,SORT_DESC,"g",SORT_STRING,SORT_ASC); $or3="3A"; break;
                case "4A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"g",SORT_STRING,SORT_ASC,"u",SORT_STRING,SORT_DESC); $or4="4D"; break;
                case "4D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"g",SORT_STRING,SORT_DESC,"u",SORT_STRING,SORT_DESC); $or4="4A"; break;
                case "5A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"size",SORT_NUMERIC,SORT_ASC); $or5="5D"; break;
                case "5D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"size",SORT_NUMERIC,SORT_DESC); $or5="5A"; break;
                case "6A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"date",SORT_STRING,SORT_ASC,"time",SORT_STRING,SORT_ASC,"name",SORT_STRING,SORT_ASC); $or6="6D"; break;
                case "6D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"date",SORT_STRING,SORT_DESC,"time",SORT_STRING,SORT_DESC,"name",SORT_STRING,SORT_ASC); $or6="6A"; break;
                case "7A": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"ext",SORT_STRING,SORT_ASC,"name",SORT_STRING,SORT_ASC); $or7="7D"; break;
                case "7D": $entry_list = array_csort ($entry_list,"type",SORT_STRING,SORT_ASC,"ext",SORT_STRING,SORT_DESC,"name",SORT_STRING,SORT_ASC); $or7="7A"; break;
            }
        }
        $out .= "
        <script language=\"Javascript\" type=\"text/javascript\">
        <!--
        function go(arg) {
            document.location.href='".addslashes($path_info["basename"])."?frame=3&pid='+arg;
        }
        function ans2out() {
	    if(confirm('可能覆盖已有.out文件，请三思而行！\\n Are you sure about overwrite all .out files ?')){
            	document.location.href='".addslashes($path_info["basename"])."?frame=3&ans2out=1&current_dir=".addslashes($current_dir)."';
            }
        }
        function generate() {
	    if(confirm('将覆盖所有.out文件，请三思而行！\\n Are you sure about overwrite all .out files ?')){
            	document.location.href='".addslashes($path_info["basename"])."?frame=3&generate=1&current_dir=".addslashes($current_dir)."';
            }
        }
        function resolveIDs() {
            document.location.href='".addslashes($path_info["basename"])."?frame=3&set_resolveIDs=1&current_dir=".addslashes($current_dir)."';
        }
        var entry_list = new Array();
        // Custom object constructor
        function entry(name, type, size, selected){
            this.name = name;
            this.type = type;
            this.size = size;
            this.selected = false;
        }
        // Declare entry_list for selection procedures";
        foreach ($entry_list as $i=>$data){
            $out .= "\nentry_list['entry$i'] = new entry('".addslashes($data["name"])."', '".$data["type"]."', ".$data["size"].", false);";
        }
        $out .= "
        // Select/Unselect Rows OnClick/OnMouseOver
        var lastRows = new Array(null,null);
        function selectEntry(Row, Action){
            if (multipleSelection){
                // Avoid repeated onmouseover events from same Row ( cell transition )
                if (Row != lastRows[0]){
                    if (Action == 'over') {
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) {
                                Row.className = 'entryUnselected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (unselect(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entryUnselected';
                                    }
                                }
                            }
                        } else {
                            if (select(entry_list[Row.id])){
                                Row.className = 'entrySelected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (select(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entrySelected';
                                    }
                                }
                            }
                        }
                        lastRows[1] = lastRows[0];
                        lastRows[0] = Row;
                    }
                }
            } else {
                if (Action == 'click') {
                    var newClassName = null;
                    if (entry_list[Row.id].selected){
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    } else {
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    }
                    if (newClassName) {
                        lastRows[0] = lastRows[1] = Row;
                        Row.className = newClassName;
                    }
                }
            }
            return true;
        }
        // Disable text selection and bind multiple selection flag
        var multipleSelection = false;
        if (is.ie) {
            document.onselectstart=new Function('return false');
            document.onmousedown=switch_flag_on;
            document.onmouseup=switch_flag_off;
            // Event mouseup is not generated over scrollbar.. curiously, mousedown is.. go figure.
            window.onscroll=new Function('multipleSelection=false');
            window.onresize=new Function('multipleSelection=false');
        } else {
            if (document.layers) window.captureEvents(Event.MOUSEDOWN);
            if (document.layers) window.captureEvents(Event.MOUSEUP);
            window.onmousedown=switch_flag_on;
            window.onmouseup=switch_flag_off;
        }
        // Using same function and a ternary operator couses bug on double click
        function switch_flag_on(e) {
            if (is.ie){
                multipleSelection = (event.button == 1);
            } else {
                multipleSelection = (e.which == 1);
            }
			var type = String(e.target.type);
			return (type.indexOf('select') != -1 || type.indexOf('button') != -1 || type.indexOf('input') != -1 || type.indexOf('radio') != -1);
        }
        function switch_flag_off(e) {
            if (is.ie){
                multipleSelection = (event.button != 1);
            } else {
                multipleSelection = (e.which != 1);
            }
            lastRows[0] = lastRows[1] = null;
            update_sel_status();
            return false;
        }
        var total_dirs_selected = 0;
        var total_files_selected = 0;
        function unselect(Entry){
            if (!Entry.selected) return false;
            Entry.selected = false;
            sel_totalsize -= Entry.size;
            if (Entry.type == 'dir') total_dirs_selected--;
            else total_files_selected--;
            return true;
        }
        function select(Entry){
            if(Entry.selected) return false;
            Entry.selected = true;
            sel_totalsize += Entry.size;
            if(Entry.type == 'dir') total_dirs_selected++;
            else total_files_selected++;
            return true;
        }
        function is_anything_selected(){
            var selected_dir_list = new Array();
            var selected_file_list = new Array();
            for(var x=0;x<".(integer)count($entry_list).";x++){
                if(entry_list['entry'+x].selected){
                    if(entry_list['entry'+x].type == 'dir') selected_dir_list.push(entry_list['entry'+x].name);
                    else selected_file_list.push(entry_list['entry'+x].name);
                }
            }
            document.form_action.selected_dir_list.value = selected_dir_list.join('<|*|>');
            document.form_action.selected_file_list.value = selected_file_list.join('<|*|>');
            return (total_dirs_selected>0 || total_files_selected>0);
        }
        function format_size (arg) {
            var resul = '';
            if (arg>0){
                var j = 0;
                var ext = new Array(' bytes',' Kb',' Mb',' Gb',' Tb');
                while (arg >= Math.pow(1024,j)) ++j;
                resul = (Math.round(arg/Math.pow(1024,j-1)*100)/100) + ext[j-1];
            } else resul = 0;
            return resul;
        }
        var sel_totalsize = 0;
        function update_sel_status(){
            var t = total_dirs_selected+' ".et('Dir_s')." ".et('And')." '+total_files_selected+' ".et('File_s')." ".et('Selected_s')." = '+format_size(sel_totalsize);
            //document.getElementById(\"sel_status\").innerHTML = t;
            window.status = t;
        }
        // Select all/none/inverse
        function selectANI(Butt){
        	cancel_copy_move();
            for(var x=0;x<". (integer)count($entry_list).";x++){
                var Row = document.getElementById('entry'+x);
                var newClassName = null;
                switch (Butt.value){
                    case '".et('SelAll')."':
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    break;
                    case '".et('SelNone')."':
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    break;
                    case '".et('SelInverse')."':
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                        } else {
                            if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                        }
                    break;
                }
                if (newClassName) {
                    Row.className = newClassName;
                }
            }
            if (Butt.value == '".et('SelAll')."'){
                for(var i=0;i<2;i++){
                    document.getElementById('ANI'+i).value='".et('SelNone')."';
                }
            } else if (Butt.value == '".et('SelNone')."'){
                for(var i=0;i<2;i++){
                    document.getElementById('ANI'+i).value='".et('SelAll')."';
                }
            }
            update_sel_status();
            return true;
        }
        function download(arg){
            location.href='".addslashes($path_info["basename"])."?action=3&current_dir=".addslashes($current_dir)."&filename='+escape(arg);
        }
        function upload(){
            var w = 600;
            var h = 320;
            window.open('".addslashes($path_info["basename"])."?action=10&current_dir=".addslashes($current_dir)."', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function execute_cmd(){
            var arg = prompt('".et('TypeCmd').".');
            if(arg.length>0){
                if(confirm('".et('ConfExec')." \\' '+arg+' \\' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('".addslashes($path_info["basename"])."?action=6&current_dir=".addslashes($current_dir)."&cmd='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function decompress(arg){
            if(confirm('".uppercase(et('Decompress'))." \\' '+arg+' \\' ?')) {
                document.form_action.action.value = 72;
                document.form_action.cmd_arg.value = arg;
                document.form_action.submit();
            }
        }
        function execute_file(arg){
            if(arg.length>0){
                if(confirm('".et('ConfExec')." \\' '+arg+' \\' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('".addslashes($path_info["basename"])."?action=11&current_dir=".addslashes($current_dir)."&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function edit_file(arg){
            var w = 1150;
            var h = 768;
            // if(confirm('".uppercase(et('Edit'))." \\' '+arg+' \\' ?'))
            window.open('".addslashes($path_info["basename"])."?action=7&current_dir=".addslashes($current_dir)."&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function config(){
            var w = 650;
            var h = 400;
            window.open('".addslashes($path_info["basename"])."?action=2', 'win_config', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function server_info(arg){
            var w = 800;
            var h = 600;
            window.open('".addslashes($path_info["basename"])."?action=5', 'win_serverinfo', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function shell(){
            var w = 800;
            var h = 600;
            window.open('".addslashes($path_info["basename"])."?action=9', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function view(arg){
            var w = 800;
            var h = 600;
            if(confirm('".uppercase(et('View'))." \\' '+arg+' \\' ?')) window.open('".addslashes($path_info["basename"])."?action=4&current_dir=".addslashes($current_dir)."&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=yes,toolbar=no,menubar=no,location=yes');
        }
        function rename(arg){
            var nome = '';
            if (nome = prompt('".uppercase(et('Ren'))." \\' '+arg+' \\' ".et('To')." ...')) document.location.href='".addslashes($path_info["basename"])."?frame=3&action=3&current_dir=".addslashes($current_dir)."&old_name='+escape(arg)+'&new_name='+escape(nome);
        }
        function set_dir_dest(arg){
            document.form_action.dir_dest.value=arg;
            if (document.form_action.action.value.length>0) test(document.form_action.action.value);
            else alert('".et('JSError').".');
        }
        function sel_dir(arg){
            document.form_action.action.value = arg;
            document.form_action.dir_dest.value='';
            if (!is_anything_selected()) alert('".et('NoSel').".');
            else {
                if (!getCookie('sel_dir_warn')) {
                    //alert('".et('SelDir').".');
                    document.cookie='sel_dir_warn'+'='+escape('true')+';';
                }
                set_sel_dir_warn(true);
                set_flag(true);
            }
        }
		function set_sel_dir_warn(b){
		   if(document.getElementById(\"sel_dir_warn\")!=null)
        	document.getElementById(\"sel_dir_warn\").style.display=(b?'':'none');
		}
		function cancel_copy_move(){
           	set_sel_dir_warn(false);
           	set_flag(false);
		}
        function chmod_form(){
            cancel_copy_move();
            document.form_action.dir_dest.value='';
            document.form_action.chmod_arg.value='';
            if (!is_anything_selected()) alert('".et('NoSel').".');
            else {
                var w = 280;
                var h = 180;
                window.open('".addslashes($path_info["basename"])."?action=8', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
            }
        }
        function set_chmod_arg(arg){
            cancel_copy_move();
            if (!is_anything_selected()) alert('".et('NoSel').".');
            else {
            	document.form_action.dir_dest.value='';
            	document.form_action.chmod_arg.value=arg;
            	test(9);
			}
        }
        function test_action(){
            if (document.form_action.action.value != 0) return true;
            else return false;
        }
        function test_prompt(arg){
        	cancel_copy_move();
			var erro='';
            var conf='';
            if (arg == 1){
                document.form_action.cmd_arg.value = prompt('".et('TypeDir').".');
            } else if (arg == 2){
                document.form_action.cmd_arg.value = prompt('".et('TypeArq').".');
            } else if (arg == 21){
                document.form_action.cmd_arg.value = prompt('".et('IOFilename').".');
            } else if (arg == 22){
                document.form_action.cmd_arg.value = prompt('".et('SolutionFilename').".');
            } else if (arg == 71){
                if (!is_anything_selected()) erro = '".et('NoSel').".';
                else document.form_action.cmd_arg.value = prompt('".et('TypeArqComp')."');
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(document.form_action.cmd_arg.value.length>0) {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
        function strstr(haystack,needle){
            var index = haystack.indexOf(needle);
            return (index==-1)?false:index;
        }
        function valid_dest(dest,orig){
            return (strstr(dest,orig)==false)?true:false;
        }
        // ArrayAlert - Selection debug only
        function aa(){
            var str = 'selected_dir_list:\\n';
            for (x=0;x<selected_dir_list.length;x++){
                str += selected_dir_list[x]+'\\n';
            }
            str += '\\nselected_file_list:\\n';
            for (x=0;x<selected_file_list.length;x++){
                str += selected_file_list[x]+'\\n';
            }
            alert(str);
        }
        function test(arg){
        	cancel_copy_move();
            var erro='';
            var conf='';
            if (arg == 4){
                if (!is_anything_selected()) erro = '".et('NoSel').".\\n';
                conf = '".et('RemSel')." ?\\n';
            } else if (arg == 5){
                if (!is_anything_selected()) erro = '".et('NoSel').".\\n';
                else if(document.form_action.dir_dest.value.length == 0) erro = '".et('NoDestDir').".';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = '".et('DestEqOrig').".';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = '".et('InvalidDest').".';
                conf = '".et('CopyTo')." \\' '+document.form_action.dir_dest.value+' \\' ?\\n';
            } else if (arg == 6){
                if (!is_anything_selected()) erro = '".et('NoSel').".';
                else if(document.form_action.dir_dest.value.length == 0) erro = '".et('NoDestDir').".';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = '".et('DestEqOrig').".';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = '".et('InvalidDest').".';
                conf = '".et('MoveTo')." \\' '+document.form_action.dir_dest.value+' \\' ?\\n';
            } else if (arg == 9){
                if (!is_anything_selected()) erro = '".et('NoSel').".';
                else if(document.form_action.chmod_arg.value.length == 0) erro = '".et('NoNewPerm').".';
                //conf = '".et('AlterPermTo')." \\' '+document.form_action.chmod_arg.value+' \\' ?\\n';
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(conf!='') {
                if(confirm(conf)) {
                    document.form_action.action.value = arg;
                    document.form_action.submit();
                } else {
                    set_sel_dir_warn(false);
				}
            } else {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
        //-->
        </script>";
        $out .= "
        <form name=\"form_action\" action=\"".$path_info["basename"]."\" method=\"post\" onsubmit=\"return test_action();\">
            <input type=hidden name=\"frame\" value=3>
            <input type=hidden name=\"action\" value=0>
            <input type=hidden name=\"dir_dest\" value=\"\">
            <input type=hidden name=\"chmod_arg\" value=\"\">
            <input type=hidden name=\"cmd_arg\" value=\"\">
            <input type=hidden name=\"current_dir\" value=\"$current_dir\">
            <input type=hidden name=\"dir_before\" value=\"$dir_before\">
            <input type=hidden name=\"selected_dir_list\" value=\"\">
            <input type=hidden name=\"selected_file_list\" value=\"\">";
        $out .= "
            <tr>
            <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
            <input type=button onclick=\"test_prompt(2)\" value=\"".et('CreateArq')."\">
	    <input type=button onclick=\"upload()\" value=\"".et('Upload')."\">";
	if(!$OJ_SaaS_ENABLE)$out.="<input type=button onclick=\"generate()\" value=\"".et('GenerateOut')."\">";
	$out.="<input type=button onclick=\"ans2out()\" value=\"".et('Ans2out')."\">";
	$out.="<input type=button onclick=\"test_prompt(21)\" value=\"".et('IOFilename')."\">";
	$out.="<input type=button onclick=\"test_prompt(22)\" value=\"".et('SolutionFilename')."\">";
	if(isset($_GET['pid'])){
                $pid=intval($_GET['pid']);
                $_SESSION[$OJ_NAME."_PID"]=$pid;
        }else{
                $pid=$_SESSION[$OJ_NAME."_PID"];
        }
        $title=pdo_query('select title from problem where problem_id=?',$pid)[0][0];
        $out.="<b></b>
            <a class='btn' href='https://muzea-demo.github.io/random-data/' target='_blank'>".et('Random-data')."</a>
            <b><a href='../problem.php?id=$pid' target='_self'><font color=blue>$title </font></a></b>
            </nobr>";
        $uplink = "";

        if ($current_dir != $fm_current_root){
            $mat = explode("/",$current_dir);
            $dir_before = "";
            for($x=0;$x<(count($mat)-2);$x++) $dir_before .= $mat[$x]."/";
            $uplink = "<a href=\"".$path_info["basename"]."?frame=3&current_dir=$dir_before\"><<</a> ";
        }
        if($entry_count){
            $out .= "
                <tr bgcolor=\"#DDDDDD\">
                <tr>
                <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
                    <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" id=\"ANI0\" value=\"".et('SelAll')."\">
                    <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" value=\"".et('SelInverse')."\">
                    <input type=\"button\" style=\"width:80\" onclick=\"test(4)\" value=\"".et('Rem')."\">
                    <input type=\"button\" style=\"width:100\" onclick=\"test_prompt(71)\" value=\"".et('Compress')."\">";
            if ($islinux) $out .= "
                    <input type=\"button\" style=\"width:100\" onclick=\"resolveIDs()\" value=\"".et('ResolveIDs')."\">";
            $out .= "<span title='一般推荐用英文命名，相同文件名的.in .out文件为一组。
不支持.ans的扩展名，请在上传前用Windows的命令行统一修改ren *.ans *.out。
支持在文件名中使用方括号[]来标注分数。如 test01[20].in / test01[20].out将视为分数是20分,未标注的文件按10分计分，
系统最终根据所有文件的总分和运行得分，记录提交的通过率pass_rate放入solution表，用于前台显示。
对于所有文件都没有标注的题目，按每组文件相同权重对待。
评测的时候根据所有.in文件的字典序来评测, 因此test10先于test2评测, 后于test02评测。
subtask的命名规则，可以在分数之前，再增加一个英文句号. 系统认为这个句号之前的内容为subtask的名字，相同subtask名字的测试数据为一组，互相绑定。
例如: subtask1.1[10].in/out 和 subtask1.2[20].in/out 为一组subtask,必须同时通过才能得分共30分。
subtask不限定必须是数字，如 TaskA.a[20].in/out 和 TaskA.b[20].in/out也可以是一组，共计40分。
subtask的题目中也可以有不跟其他数据绑定的，认为是自己一组，如big[30].in/out可以跟前面两个例子同时放在一个题目中，共计100分。' >
               命名规则: .in/.out 对应，[]标记分数，.标记subtask ， 如 TaskA.a[20].in/out 和 TaskA.b[20].in/out 是一组subtask，共计40分。</span>
                </nobr></td>
                </tr>
				<tr>
                <td bgcolor=\"#DDDDDD\" colspan=50 id=\"sel_dir_warn\" style=\"display:none\"><nobr><font color=\"red\">".et('SelDir')."...</font></nobr></td>
                </tr>";
            $file_count = 0;
            $dir_count = 0;
            $dir_out = array();
            $file_out = array();
            $max_opt = 0;
            foreach ($entry_list as $ind=>$dir_entry) {
                $file = $dir_entry["name"];
                if ($dir_entry["type"]=="dir"){
                    $dir_out[$dir_count] = array();
                    $dir_out[$dir_count][] = "
                        <tr ID=\"entry$ind\" class=\"entryUnselected\" onmouseover=\"selectEntry(this, 'over');\" onmousedown=\"selectEntry(this, 'click');\">
                        <td><nobr><a href=\"JavaScript:go('".addslashes($file)."')\">$file</a></nobr></td>";
                    $dir_out[$dir_count][] = "<td>".$dir_entry["p"]."</td>";
                    if ($islinux) {
                        $dir_out[$dir_count][] = "<td><nobr>".$dir_entry["u"]."</nobr></td>";
                        $dir_out[$dir_count][] = "<td><nobr>".$dir_entry["g"]."</nobr></td>";
                    }
                    $dir_out[$dir_count][] = "<td><nobr>".$dir_entry["sizet"]."</nobr></td>";
                    $dir_out[$dir_count][] = "<td><nobr>".$dir_entry["datet"]."</nobr></td>";
                    if ($has_files) $dir_out[$dir_count][] = "<td>&nbsp;</td>";
                    // Opções de diretório
                    if ( is_writable($current_dir.$file) ) $dir_out[$dir_count][] = "
                        <td align=center><a href=\"JavaScript:if(confirm('".et('ConfRem')." \\'".addslashes($file)."\\' ?')) document.location.href='".addslashes($path_info["basename"])."?frame=3&action=8&cmd_arg=".addslashes($file)."&current_dir=".addslashes($current_dir)."'\">".et('Rem')."</a>";
                    if ( is_writable($current_dir.$file) ) $dir_out[$dir_count][] = "
                        <td align=center><a href=\"JavaScript:rename('".addslashes($file)."')\">".et('Ren')."</a>";
                    if (count($dir_out[$dir_count])>$max_opt){
                        $max_opt = count($dir_out[$dir_count]);
                    }
                    $dir_count++;
                } else {
                    $file_out[$file_count] = array();
                    $file_out[$file_count][] = "
                        <tr ID=\"entry$ind\" class=\"entryUnselected\" onmouseover=\"selectEntry(this, 'over');\" onmousedown=\"selectEntry(this, 'click');\">
                        <td><nobr><a href=\"JavaScript:download('".addslashes($file)."')\">$file</a></nobr></td>";
                    $file_out[$file_count][] = "<td>".$dir_entry["p"]."</td>";
                    if ($islinux) {
                        $file_out[$file_count][] = "<td><nobr>".$dir_entry["u"]."</nobr></td>";
                        $file_out[$file_count][] = "<td><nobr>".$dir_entry["g"]."</nobr></td>";
                    }
                    $file_out[$file_count][] = "<td><nobr>".$dir_entry["sizet"]."</nobr></td>";
                    $file_out[$file_count][] = "<td><nobr>".$dir_entry["datet"]."</nobr></td>";
                    $file_out[$file_count][] = "<td>".$dir_entry["extt"]."</td>";
                    // Opções de arquivo
                    if ( is_writable($current_dir.$file) ) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:if(confirm('".uppercase(et('Rem'))." \\'".addslashes($file)."\\' ?')) document.location.href='".addslashes($path_info["basename"])."?frame=3&action=8&cmd_arg=".addslashes($file)."&current_dir=".addslashes($current_dir)."'\">".et('Rem')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if ( is_writable($current_dir.$file) ) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:rename('".addslashes($file)."')\">".et('Ren')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if ( is_readable($current_dir.$file) && (strpos(".wav#.mp3#.mid#.avi#.mov#.mpeg#.mpg#.rm#.iso#.bin#.img#.dll#.psd#.fla#.swf#.class#.ppt#.tif#.tiff#.pcx#.jpg#.gif#.png#.wmf#.eps#.bmp#.msi#.exe#.com#.rar#.tar#.zip#.bz2#.tbz2#.bz#.tbz#.bzip#.gzip#.gz#.tgz#", $dir_entry["ext"]."#" ) === false)) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:edit_file('".addslashes($file)."')\">".et('Edit')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if ( is_readable($current_dir.$file) && (strpos(".txt#.sys#.bat#.ini#.conf#.swf#.php#.php3#.asp#.html#.htm#.jpg#.gif#.png#.bmp#", $dir_entry["ext"]."#" ) !== false)) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:view('".addslashes($file)."');\">".et('View')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if ( is_readable($current_dir.$file) && strlen($dir_entry["ext"]) && (strpos(".tar#.zip#.bz2#.tbz2#.bz#.tbz#.bzip#.gzip#.gz#.tgz#", $dir_entry["ext"]."#" ) !== false)) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:decompress('".addslashes($file)."')\">".et('Decompress')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if ( is_readable($current_dir.$file) && strlen($dir_entry["ext"]) && (strpos(".exe#.com#.sh#.bat#", $dir_entry["ext"]."#" ) !== false)) $file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:execute_file('".addslashes($file)."')\">".et('Exec')."</a>";
                    else $file_out[$file_count][] = "<td>&nbsp;</td>";
                    if (count($file_out[$file_count])>$max_opt){
                        $max_opt = count($file_out[$file_count]);
                    }
                    $file_count++;
                }
            }
            if ($dir_count){
                $out .= "
                <tr>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or1&current_dir=$current_dir\">".et('Name')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or2&current_dir=$current_dir\">".et('Perm')."</a></nobr></td>";
                if ($islinux) $out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or3&current_dir=$current_dir\">".et('Owner')."</a></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or4&current_dir=$current_dir\">".et('Group')."</a></nobr></td>";
                $out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or5&current_dir=$current_dir\">".et('Size')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or6&current_dir=$current_dir\">".et('Date')."</a></nobr></td>";
                if ($file_count) $out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or7&current_dir=$current_dir\">".et('Type')."</a></nobr></td>";
                $out .= "
                      <td bgcolor=\"#DDDDDD\" colspan=50>&nbsp;</td>
                </tr>";

            }
            foreach($dir_out as $k=>$v){
                while (count($dir_out[$k])<$max_opt) {
                    $dir_out[$k][] = "<td>&nbsp;</td>";
                }
                $out .= implode($dir_out[$k]);
                $out .= "</tr>";
            }
            if ($file_count){
                $out .= "
                <tr>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or1&current_dir=$current_dir\">".et('Name')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or2&current_dir=$current_dir\">".et('Perm')."</a></nobr></td>";
                if ($islinux) $out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or3&current_dir=$current_dir\">".et('Owner')."</a></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or4&current_dir=$current_dir\">".et('Group')."</a></nobr></td>";
                $out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or5&current_dir=$current_dir\">".et('Size')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or6&current_dir=$current_dir\">".et('Date')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"".$path_info["basename"]."?frame=3&or_by=$or7&current_dir=$current_dir\">".et('Type')."</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\" colspan=50>&nbsp;</td>
                </tr>";

            }
            foreach($file_out as $k=>$v){
                while (count($file_out[$k])<$max_opt) {
                    $file_out[$k][] = "<td>&nbsp;</td>";
                }
                $out .= implode($file_out[$k]);
                $out .= "</tr>";
            }
            $out .= "
                <tr>
                <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
                      <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" id=\"ANI1\" value=\"".et('SelAll')."\">
                      <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" value=\"".et('SelInverse')."\">
                      <input type=\"button\" style=\"width:80\" onclick=\"test(4)\" value=\"".et('Rem')."\">
                      <input type=\"button\" style=\"width:100\" onclick=\"test_prompt(71)\" value=\"".et('Compress')."\">";
            if ($islinux) $out .= "
                      <input type=\"button\" style=\"width:100\" onclick=\"resolveIDs()\" value=\"".et('ResolveIDs')."\">";
            $out .= "
                </nobr></td>
                </tr>";
            $out .= "
            </form>";
            $out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>$dir_count ".et('Dir_s')." ".et('And')." $file_count ".et('File_s')." = ".format_size($total_size)."</td></tr>";
            if ($quota_mb) {
                $out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>".et('Partition').": ".format_size(($quota_mb*1024*1024))." ".et('Total')." - ".format_size(($quota_mb*1024*1024)-total_size($fm_current_root))." ".et('Free')."</td></tr>";
            } else {
                $out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>".et('Partition').": ".format_size(disk_total_space($current_dir))." ".et('Total')." - ".format_size(disk_free_space($current_dir))." ".et('Free')."</td></tr>";
            }
            $tf = getmicrotime();
            $tt = ($tf - $ti);
            $out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>".et('RenderTime').": ".substr($tt,0,strrpos($tt,".")+5)." ".et('Seconds')."</td></tr>";
            $out .= "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                update_sel_status();
            //-->
            </script>";
        } else {
            $out .= "
            <tr>
            <td bgcolor=\"#DDDDDD\" width=\"1%\">$uplink<td bgcolor=\"#DDDDDD\" colspan=50><nobr><a href=\"".$path_info["basename"]."?frame=3&current_dir=$current_dir\">$current_dir</a></nobr>
            <tr><td bgcolor=\"#DDDDDD\" colspan=50>".et('EmptyDir').".</tr>";
        }
    } else $out .= "<tr><td><font color=red>".et('IOError').".<br>$current_dir</font>";
    $out .= "</table>";
    echo $out;
}
function upload_form(){
    global $_FILES,$current_dir,$dir_dest,$fechar,$quota_mb,$path_info;
    $num_uploads = 5;
    html_header();
    echo "<body marginwidth=\"0\" marginheight=\"0\">";
    if (count($_FILES)==0){
        echo "
        <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
        <form name=\"upload_form\" action=\"".$path_info["basename"]."\" method=\"post\" ENCTYPE=\"multipart/form-data\">
        <input type=hidden name=dir_dest value=\"".basename($current_dir)."\">
        <input type=hidden name=action value=10>
        <tr><th colspan=2>".et('Upload')."</th></tr>
        <tr><td align=right><b>".et('Destination').":<td><b><nobr>".basename($current_dir)."</nobr>";
        for ($x=0;$x<$num_uploads;$x++){
            echo "<tr><td align=right><b>".et('File').":<td><nobr><input type=\"file\" name=\"file$x\"></nobr>";
            $test_js .= "(document.upload_form.file$x.value.length>0)||";
        }
        echo "
        <input type=button value=\"".et('Send')."\" onclick=\"test_upload_form()\"></nobr>
        <tr><td> <td><input type=checkbox name=fechar value=\"1\" checked> <a href=\"JavaScript:troca();\">".et('AutoClose')."</a>
        <tr><td colspan=2>zip file can be decompressed on the server later. 
			  just don't add dirs,please<br>
			  可以上传zip文件，之后点击decompress解压缩，但是请不要在zip文件中包含子目录。
	</td></tr>
        </form>
        </table>
        <script language=\"Javascript\" type=\"text/javascript\">
        <!--
            function troca(){
                if(document.upload_form.fechar.checked){document.upload_form.fechar.checked=false;}else{document.upload_form.fechar.checked=true;}
            }
            foi = false;
            function test_upload_form(){
                if(".substr($test_js,0,strlen($test_js)-2)."){
                    if (foi) alert('".et('SendingForm')."...');
                    else {
                        foi = true;
                        document.upload_form.submit();
                    }
                } else alert('".et('NoFileSel').".');
            }
            window.moveTo((window.screen.width-400)/2,((window.screen.height-200)/2)-20);
        //-->
        </script>";
    } else {
        $out = "<tr><th colspan=2>".et('UploadEnd')."</th></tr>
                <tr><th colspan=2><nobr>".et('Destination').": ".basename($dir_dest)."</nobr>";
        for ($x=0;$x<$num_uploads;$x++){
            $temp_file = $_FILES["file".$x]["tmp_name"];
            $filename = $_FILES["file".$x]["name"];
            if (strlen($filename)) $resul = save_upload($temp_file,$filename,$dir_dest);
            else $resul = 7;
            switch($resul){
                case 1:
                $out .= "<tr><td><b>".str_zero($x+1,3).".<font color=green><b> ".et('FileSent').":</font><td>".$filename."</td></tr>\n";
                break;
                case 2:
                $out .= "<tr><td colspan=2><font color=red><b>".et('IOError')."</font></td></tr>\n";
                $x = $upload_num;
                break;
                case 3:
                $out .= "<tr><td colspan=2><font color=red><b>".et('SpaceLimReached')." ($quota_mb Mb)</font></td></tr>\n";
                $x = $upload_num;
                break;
                case 4:
                $out .= "<tr><td><b>".str_zero($x+1,3).".<font color=red><b> ".et('InvExt').":</font><td>".$filename."</td></tr>\n";
                break;
                case 5:
                $out .= "<tr><td><b>".str_zero($x+1,3).".<font color=red><b> ".et('FileNoOverw')."</font><td>".$filename."</td></tr>\n";
                break;
                case 6:
                $out .= "<tr><td><b>".str_zero($x+1,3).".<font color=green><b> ".et('FileOverw').":</font><td>".$filename."</td></tr>\n";
                break;
                case 7:
                $out .= "<tr><td colspan=2><b>".str_zero($x+1,3).".<font color=red><b> ".et('FileIgnored')."</font></td></tr>\n";
            }
        }
        if ($fechar) {
            echo "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
		window.opener.location.reload();
                window.close();
            //-->
            </script>
            ";
        } else {
            echo "
            <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
            $out
            <tr><td colspan=2> </td></tr>
            </table>
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.focus();
            //-->
            </script>
            ";
        }
    }
    echo "</body>\n</html>";
}
function chmod_form(){
    html_header("
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
    function octalchange()
    {
        var val = document.chmod_form.t_total.value;
        var stickybin = parseInt(val.charAt(0)).toString(2);
        var ownerbin = parseInt(val.charAt(1)).toString(2);
        while (ownerbin.length<3) { ownerbin=\"0\"+ownerbin; };
        var groupbin = parseInt(val.charAt(2)).toString(2);
        while (groupbin.length<3) { groupbin=\"0\"+groupbin; };
        var otherbin = parseInt(val.charAt(3)).toString(2);
        while (otherbin.length<3) { otherbin=\"0\"+otherbin; };
        document.chmod_form.sticky.checked = parseInt(stickybin.charAt(0));
        document.chmod_form.owner4.checked = parseInt(ownerbin.charAt(0));
        document.chmod_form.owner2.checked = parseInt(ownerbin.charAt(1));
        document.chmod_form.owner1.checked = parseInt(ownerbin.charAt(2));
        document.chmod_form.group4.checked = parseInt(groupbin.charAt(0));
        document.chmod_form.group2.checked = parseInt(groupbin.charAt(1));
        document.chmod_form.group1.checked = parseInt(groupbin.charAt(2));
        document.chmod_form.other4.checked = parseInt(otherbin.charAt(0));
        document.chmod_form.other2.checked = parseInt(otherbin.charAt(1));
        document.chmod_form.other1.checked = parseInt(otherbin.charAt(2));
        calc_chmod(1);
    };

    function calc_chmod(nototals)
    {
      var users = new Array(\"owner\", \"group\", \"other\");
      var totals = new Array(\"\",\"\",\"\");
      var syms = new Array(\"\",\"\",\"\");

        for (var i=0; i<users.length; i++)
        {
            var user=users[i];
            var field4 = user + \"4\";
            var field2 = user + \"2\";
            var field1 = user + \"1\";
            var symbolic = \"sym_\" + user;
            var number = 0;
            var sym_string = \"\";
            var sticky = \"0\";
            var sticky_sym = \" \";
            if (document.chmod_form.sticky.checked){
                sticky = \"1\";
                sticky_sym = \"t\";
            }
            if (document.chmod_form[field4].checked == true) { number += 4; }
            if (document.chmod_form[field2].checked == true) { number += 2; }
            if (document.chmod_form[field1].checked == true) { number += 1; }

            if (document.chmod_form[field4].checked == true) {
                sym_string += \"r\";
            } else {
                sym_string += \"-\";
            }
            if (document.chmod_form[field2].checked == true) {
                sym_string += \"w\";
            } else {
                sym_string += \"-\";
            }
            if (document.chmod_form[field1].checked == true) {
                sym_string += \"x\";
            } else {
                sym_string += \"-\";
            }

            totals[i] = totals[i]+number;
            syms[i] =  syms[i]+sym_string;

      };
        if (!nototals) document.chmod_form.t_total.value = sticky + totals[0] + totals[1] + totals[2];
        document.chmod_form.sym_total.value = syms[0] + syms[1] + syms[2] + sticky_sym;
    }
    function sticky_change(){
        document.chmod_form.sticky.checked = !(document.chmod_form.sticky.checked);
    }
	function apply_chmod(){
        if (confirm('".et('AlterPermTo')." \\' '+document.chmod_form.t_total.value+' \\' ?\\n')){
            window.opener.set_chmod_arg(document.chmod_form.t_total.value);
			window.close();
		}
	}

    window.onload=octalchange
    window.moveTo((window.screen.width-400)/2,((window.screen.height-200)/2)-20);
    //-->
    </script>");
    echo "<body marginwidth=\"0\" marginheight=\"0\">
    <form name=\"chmod_form\">
    <TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\" ALIGN=CENTER>
    <tr><th colspan=4>".et('Perms')."</th></tr>
    <TR ALIGN=\"LEFT\" VALIGN=\"MIDDLE\">
    <TD><input type=\"text\" name=\"t_total\" value=\"0711\" size=\"4\" onKeyUp=\"octalchange()\"> </TD>
    <TD><input type=\"text\" name=\"sym_total\" value=\"\" size=\"12\" READONLY=\"1\"></TD>
    </TR>
    </TABLE>
    <table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" ALIGN=CENTER>
    <tr bgcolor=\"#333333\">
    <td WIDTH=\"60\" align=\"left\"> </td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>".et('Owner')."
    </b></td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>".et('Group')."
    </b></td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>".et('Other')."
    <b></td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">".et('Read')."</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">".et('Write')."</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner2\" value=\"2\" onclick=\"calc_chmod()\"></td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group2\" value=\"2\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other2\" value=\"2\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">".et('Exec')."</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    </TABLE>
    <TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\" ALIGN=CENTER>
    <tr><td colspan=2><input type=checkbox name=sticky value=\"1\" onclick=\"calc_chmod()\"> <a href=\"JavaScript:sticky_change();\">".et('StickyBit')."</a><td colspan=2 align=right><input type=button value=\"".et('Apply')."\" onClick=\"apply_chmod()\"></tr>
    </table>
    </form>
    </body>\n</html>";
}
function get_mime_type($ext = ''){
    $mimes = array(
      'hqx'   =>  'application/mac-binhex40',
      'cpt'   =>  'application/mac-compactpro',
      'doc'   =>  'application/msword',
      'bin'   =>  'application/macbinary',
      'dms'   =>  'application/octet-stream',
      'lha'   =>  'application/octet-stream',
      'lzh'   =>  'application/octet-stream',
      'exe'   =>  'application/octet-stream',
      'class' =>  'application/octet-stream',
      'psd'   =>  'application/octet-stream',
      'so'    =>  'application/octet-stream',
      'sea'   =>  'application/octet-stream',
      'dll'   =>  'application/octet-stream',
      'oda'   =>  'application/oda',
      'pdf'   =>  'application/pdf',
      'ai'    =>  'application/postscript',
      'eps'   =>  'application/postscript',
      'ps'    =>  'application/postscript',
      'smi'   =>  'application/smil',
      'smil'  =>  'application/smil',
      'mif'   =>  'application/vnd.mif',
      'xls'   =>  'application/vnd.ms-excel',
      'ppt'   =>  'application/vnd.ms-powerpoint',
      'pptx'  =>  'application/vnd.ms-powerpoint',
      'wbxml' =>  'application/vnd.wap.wbxml',
      'wmlc'  =>  'application/vnd.wap.wmlc',
      'dcr'   =>  'application/x-director',
      'dir'   =>  'application/x-director',
      'dxr'   =>  'application/x-director',
      'dvi'   =>  'application/x-dvi',
      'gtar'  =>  'application/x-gtar',
      'php'   =>  'application/x-httpd-php',
      'php4'  =>  'application/x-httpd-php',
      'php3'  =>  'application/x-httpd-php',
      'phtml' =>  'application/x-httpd-php',
      'phps'  =>  'application/x-httpd-php-source',
      'js'    =>  'application/x-javascript',
      'swf'   =>  'application/x-shockwave-flash',
      'sit'   =>  'application/x-stuffit',
      'tar'   =>  'application/x-tar',
      'tgz'   =>  'application/x-tar',
      'xhtml' =>  'application/xhtml+xml',
      'xht'   =>  'application/xhtml+xml',
      'zip'   =>  'application/zip',
      'mid'   =>  'audio/midi',
      'midi'  =>  'audio/midi',
      'mpga'  =>  'audio/mpeg',
      'mp2'   =>  'audio/mpeg',
      'mp3'   =>  'audio/mpeg',
      'aif'   =>  'audio/x-aiff',
      'aiff'  =>  'audio/x-aiff',
      'aifc'  =>  'audio/x-aiff',
      'ram'   =>  'audio/x-pn-realaudio',
      'rm'    =>  'audio/x-pn-realaudio',
      'rpm'   =>  'audio/x-pn-realaudio-plugin',
      'ra'    =>  'audio/x-realaudio',
      'rv'    =>  'video/vnd.rn-realvideo',
      'wav'   =>  'audio/x-wav',
      'bmp'   =>  'image/bmp',
      'gif'   =>  'image/gif',
      'jpeg'  =>  'image/jpeg',
      'jpg'   =>  'image/jpeg',
      'jpe'   =>  'image/jpeg',
      'png'   =>  'image/png',
      'tiff'  =>  'image/tiff',
      'tif'   =>  'image/tiff',
      'css'   =>  'text/css',
      'html'  =>  'text/html',
      'htm'   =>  'text/html',
      'shtml' =>  'text/html',
      'txt'   =>  'text/plain',
      'text'  =>  'text/plain',
      'log'   =>  'text/plain',
      'rtx'   =>  'text/richtext',
      'rtf'   =>  'text/rtf',
      'xml'   =>  'text/xml',
      'xsl'   =>  'text/xml',
      'mpeg'  =>  'video/mpeg',
      'mpg'   =>  'video/mpeg',
      'mpe'   =>  'video/mpeg',
      'qt'    =>  'video/quicktime',
      'mov'   =>  'video/quicktime',
      'avi'   =>  'video/x-msvideo',
      'movie' =>  'video/x-sgi-movie',
      'doc'   =>  'application/msword',
      'docx'  =>  'application/msword',
      'word'  =>  'application/msword',
      'xl'    =>  'application/excel',
      'xls'   =>  'application/excel',
      'xlsx'  =>  'application/excel',
      'eml'   =>  'message/rfc822'
    );
    return (!isset($mimes[lowercase($ext)])) ? 'application/octet-stream' : $mimes[lowercase($ext)];
}
function view(){
    global $doc_root,$path_info,$url_info,$current_dir,$islinux,$filename,$passthru;
	if (intval($passthru)){
    $filename = remove_special_chars($filename);
	    $file = $current_dir.$filename;
	    if(file_exists($file)){
	        $is_denied = false;
	        foreach($download_ext_filter as $key=>$ext){
	            if (preg_match($ext,$filename)){
	                $is_denied = true;
	                break;
	            }
	        }
	        if (!$is_denied){
                if ($fh = fopen("$file", "rb")){
	                fclose($fh);
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					$ctype = get_mime_type($ext);
					if ($ctype == "application/octet-stream") $ctype = "text/plain";
					header("Pragma: public");
					header("Expires: 0");
					header("Connection: close");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: public");
					header("Content-Description: File Transfer");
					header("Content-Type: ".$ctype);
				    header("Content-Disposition: inline; filename=\"".pathinfo($file, PATHINFO_BASENAME)."\";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize($file));
					@readfile($file);
					exit();
	            } else alert(et('ReadDenied').": ".$file);
	        } else alert(et('ReadDenied').": ".$file);
	    } else alert(et('FileNotFound').": ".$file);
        echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
	        window.close();
	    //-->
	    </script>";
	} else {
	    html_header();
	    echo "<body marginwidth=\"0\" marginheight=\"0\">";
	    $is_reachable_thru_webserver = (stristr($current_dir,$doc_root)!==false);
	    if ($is_reachable_thru_webserver){
	        $url = $url_info["scheme"]."://".$url_info["host"];
	        if (strlen($url_info["port"])) $url .= ":".$url_info["port"];
	        // Malditas variaveis de sistema!! No windows doc_root é sempre em lowercase... cadê o str_ireplace() ??
	        $url .= str_replace($doc_root,"","/".$current_dir).$filename;
	    } else {
			$url = addslashes($path_info["basename"])."?action=4&current_dir=".addslashes($current_dir)."&filename=".addslashes($filename)."&passthru=1";
	    }
        echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
        	window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
	        document.location.href='$url';
	    //-->
	    </script>
    	</body>\n</html>";
	}
}
function edit_file_form(){
    global $current_dir,$filename,$file_data,$save_file,$path_info;
    $filename=remove_special_chars($filename);
   // echo "[$filename]";
    $file = $current_dir.$filename;
    if ($save_file){
        $fh=fopen($file,"w");
	$file_data=preg_replace("(\r\n)","\n",$file_data);
        fputs($fh,$file_data,strlen($file_data));
        fclose($fh);
    }
    $file_data=file_get_contents($file);
    html_header();
    echo "<body marginwidth=\"0\" marginheight=\"0\">
    <table border=0 cellspacing=0 cellpadding=5 align=center>
    <form name=\"edit_form\" action=\"".$path_info["basename"]."\" method=\"post\">
    <input type=hidden name=action value=\"7\">
    <input type=hidden name=save_file value=\"1\">
    <input type=hidden name=current_dir value=\"$current_dir\">
    <input type=hidden name=filename value=\"$filename\">
    <tr><th colspan=2>".$filename."</th></tr>
    <tr><td colspan=2><textarea name=file_data style='width:1000px;height:500px;'>".html_encode($file_data)."</textarea></td></tr>
    <tr><td><input type=button value=\"".et('Refresh')."\" onclick=\"document.edit_form_refresh.submit()\"></td><td align=right><input type=button value=\"".et('SaveFile')."\" onclick=\"go_save()\"></td></tr>
    </form>
    <form name=\"edit_form_refresh\" action=\"".$path_info["basename"]."\" method=\"post\">
    <input type=hidden name=action value=\"7\">
    <input type=hidden name=current_dir value=\"$current_dir\">
    <input type=hidden name=filename value=\"$filename\">
    </form>
    </table>
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        window.moveTo((window.screen.width-1024)/2,((window.screen.height-728)/2)-20);
        function go_save(){";
    if (is_writable($file)) {
        echo "
        document.edit_form.submit();";
    } else {
        echo "
        if(confirm('".et('ConfTrySave')." ?')) document.edit_form.submit();";
    }
    echo "
        }
    //-->
    </script>
    </body>\n</html>";
}
function config_form(){
    global $cfg;
    global $current_dir,$fm_self,$doc_root,$path_info,$fm_current_root,$lang,$error_reporting,$version;
    global $config_action,$newpass,$newlang,$newerror,$newfm_root;
    $Warning = "";
    switch ($config_action){
        case 1:
            if ($fh = fopen("http://phpfm.sf.net/latest.php","r")){
                $data = "";
                while (!feof($fh)) $data .= fread($fh,1024);
                fclose($fh);
                $data = unserialize($data);
                $ChkVerWarning = "<tr><td align=right> ";
                if (is_array($data)&&count($data)){
                    $ChkVerWarning .= "<a href=\"JavaScript:open_win('http://sourceforge.net')\">
                    <img src=\"http://sourceforge.net/sflogo.php?group_id=114392&type=1\" width=\"88\" height=\"31\" style=\"border: 1px solid #AAAAAA\" alt=\"SourceForge.net Logo\" />
					</a>";
                    if (str_replace(".","",$data['version'])>str_replace(".","",$cfg->data['version'])) $ChkVerWarning .= "<td><a href=\"JavaScript:open_win('http://prdownloads.sourceforge.net/phpfm/phpFileManager-".$data['version'].".zip?download')\"><font color=green>".et('ChkVerAvailable')."</font></a>";
                    else $ChkVerWarning .= "<td><font color=red>".et('ChkVerNotAvailable')."</font>";
                } else $ChkVerWarning .= "<td><font color=red>".et('ChkVerError')."</font>";
            } else $ChkVerWarning .= "<td><font color=red>".et('ChkVerError')."</font>";
        break;
        case 2:
            $reload = false;
            if ($cfg->data['lang'] != $newlang){
                $cfg->data['lang'] = $newlang;
                $lang = $newlang;
                $reload = true;
            }
            if ($cfg->data['error_reporting'] != $newerror){
                $cfg->data['error_reporting'] = $newerror;
                $error_reporting = $newerror;
                $reload = true;
            }
            $newfm_root = format_path($newfm_root);
            if ($cfg->data['fm_root'] != $newfm_root){
                $cfg->data['fm_root'] = $newfm_root;
                if (strlen($newfm_root)) $current_dir = $newfm_root;
                else $current_dir = $path_info["dirname"]."/";
                setcookie("fm_current_root", $newfm_root , 0 , "/");
                $reload = true;
            }
            $cfg->save();
            if ($reload){
                reloadframe("window.opener.parent",2);
                reloadframe("window.opener.parent",3);
            }
            $Warning1 = et('ConfSaved')."...";
        break;
        case 3:
            if ($cfg->data['auth_pass'] != md5($newpass)){
                $cfg->data['auth_pass'] = md5($newpass);
                setcookie("loggedon", md5($newpass) , 0 , "/");
            }
            $cfg->save();
            $Warning2 = et('PassSaved')."...";
        break;
    }
    html_header();
    echo "<body marginwidth=\"0\" marginheight=\"0\">\n";
    echo "
    <table border=0 cellspacing=0 cellpadding=5 align=center width=\"100%\">
    <tr><td colspan=2 align=center><b>".uppercase(et('Configurations'))."</b></td></tr>
    </table>
    <table border=0 cellspacing=0 cellpadding=5 align=center width=\"100%\">
	<form>
    <tr><td align=right width=\"1%\">".et('Version').":<td>$version (".get_size($fm_self).")</td></tr>
    <tr><td align=right>".et('Website').":<td><a href=\"JavaScript:open_win('http://phpfm.sf.net')\">http://phpfm.sf.net</a>&nbsp;&nbsp;&nbsp;<input type=button value=\"".et('ChkVer')."\" onclick=\"test_config_form(1)\"></td></tr>
	</form>";
    if (strlen($ChkVerWarning)) echo $ChkVerWarning.$data['warnings'];
    echo "
 	<style type=\"text/css\">
		.buymeabeer {
		    background: url('http://phpfm.sf.net/img/buymeabeer.png') 0 0 no-repeat;
		    text-indent: -9999px;
		    width: 128px;
		    height: 31px;
            border: none;
   			cursor: hand;
   			cursor: pointer;
		}
		.buymeabeer:hover {
		    background: url('http://phpfm.sf.net/img/buymeabeer.png') 0 -31px no-repeat;
		}
	</style>
	<tr><td align=right>Like this project?</td><td>
	<form name=\"buymeabeer_form\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
		<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
		<input type=\"hidden\" name=\"business\" value=\"dulldusk@gmail.com\">
		<input type=\"hidden\" name=\"lc\" value=\"BR\">
		<input type=\"hidden\" name=\"item_name\" value=\"A Beer\">
		<input type=\"hidden\" name=\"button_subtype\" value=\"services\">
		<input type=\"hidden\" name=\"currency_code\" value=\"USD\">
		<input type=\"hidden\" name=\"tax_rate\" value=\"0.000\">
		<input type=\"hidden\" name=\"shipping\" value=\"0.00\">
		<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest\">
        <input type=\"submit\" class=\"buymeabeer\" value=\"buy me a beer\">
	        <input type=\"hidden\" name=\"buyer_credit_promo_code\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_product_category\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_shipping_method\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_user_address_change\" value=\"\">
	        <input type=\"hidden\" name=\"tax\" value=\"0\">
			<input type=\"hidden\" name=\"no_shipping\" value=\"1\">
	        <input type=\"hidden\" name=\"return\" value=\"http://phpfm.sf.net\">
	        <input type=\"hidden\" name=\"cancel_return\" value=\"http://phpfm.sf.net\">
	</form>
	</td></tr>
    <form name=\"config_form\" action=\"".$path_info["basename"]."\" method=\"post\">
    <input type=hidden name=action value=2>
    <input type=hidden name=config_action value=0>
    <tr><td align=right width=1><nobr>".et('DocRoot').":</nobr><td>".$doc_root."</td></tr>
    <tr><td align=right><nobr>".et('FLRoot').":</nobr><td><input type=text size=60 name=newfm_root value=\"".$cfg->data['fm_root']."\" onkeypress=\"enterSubmit(event,'test_config_form(2)')\"></td></tr>
    <tr><td align=right>".et('Lang').":<td>
	<select name=newlang>
    	<option value=cat>Catalan - by Pere Borràs AKA @Norl
        <option value=nl>Dutch - by Leon Buijs
		<option value=en>English - by Fabricio Seger Kolling
		<option value=fr1>French - by Jean Bilwes
        <option value=fr2>French - by Sharky
        <option value=fr3>French - by Michel Lainey
		<option value=de1>German - by Guido Ogrzal
        <option value=de2>German - by AXL
        <option value=de3>German - by Mathias Rothe
        <option value=it1>Italian - by Valerio Capello
        <option value=it2>Italian - by Federico Corrà
        <option value=it3>Italian - by Luca Zorzi
        <option value=it4>Italian - by Gianni
		<option value=kr>Korean - by Airplanez	
		<option value=pt>Portuguese - by Fabricio Seger Kolling
		<option value=es>Spanish - by Sh Studios
        <option value=ru>Russian - by Евгений Рашев
        <option value=tr>Turkish - by Necdet Yazilimlari
	</select></td></tr>
    <tr><td align=right>".et('ErrorReport').":<td><select name=newerror>
	<option value=\"0\">Disabled
	<option value=\"1\">Show Errors
	<option value=\"2\">Show Errors, Warnings and Notices
	</select></td></tr>
    <tr><td> <td><input type=button value=\"".et('SaveConfig')."\" onclick=\"test_config_form(2)\">";
    if (strlen($Warning1)) echo " <font color=red>$Warning1</font>";
    echo "
    <tr><td align=right>".et('Pass').":<td><input type=text size=30 name=newpass value=\"\" onkeypress=\"enterSubmit(event,'test_config_form(3)')\"></td></tr>
    <tr><td> <td><input type=button value=\"".et('SavePass')."\" onclick=\"test_config_form(3)\">";
    if (strlen($Warning2)) echo " <font color=red>$Warning2</font>";
    echo "</td></tr>";
    echo "
    </form>
    </table>
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        function set_select(sel,val){
            for(var x=0;x<sel.length;x++){
                if(sel.options[x].value==val){
                    sel.options[x].selected=true;
                    break;
                }
            }
        }
        set_select(document.config_form.newlang,'".$cfg->data['lang']."');
        set_select(document.config_form.newerror,'".$cfg->data['error_reporting']."');
        function test_config_form(arg){
            document.config_form.config_action.value = arg;
            document.config_form.submit();
        }
        function open_win(url){
            var w = 800;
            var h = 600;
            window.open(url, '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=yes,toolbar=yes,menubar=yes,location=yes');
        }
        window.moveTo((window.screen.width-600)/2,((window.screen.height-400)/2)-20);
        window.focus();
    //-->
    </script>
    ";
    echo "</body>\n</html>";
}
function server_info(){
/*
    if (!@phpinfo()) echo et('NoPhpinfo')."...";
    echo "<br><br>";
	    $a=ini_get_all();
	    $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
	    $output.="<tr><th colspan=2>ini_get_all()</td></tr>";
	    while(list($key, $value)=each($a)) {
	        list($k, $v)= each($a[$key]);
	        $output.="<tr><td align=right>$key</td><td>$v</td></tr>";
	    }
	    $output.="</table>";
	echo $output;
    echo "<br><br>";
	    $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
	    $output.="<tr><th colspan=2>\$_SERVER</td></tr>";
	    foreach ($_SERVER as $k=>$v) {
	        $output.="<tr><td align=right>$k</td><td>$v</td></tr>";
	    }
	    $output.="</table>";
	echo $output;
    echo "<br><br>";
    echo "<table border=1 cellspacing=0 cellpadding=4 align=center>";
    $safe_mode=trim(ini_get("safe_mode"));
    if ((strlen($safe_mode)==0)||($safe_mode==0)) $safe_mode=false;
    else $safe_mode=true;
    $is_windows_server = (uppercase(substr(PHP_OS, 0, 3)) === 'WIN');
    echo "<tr><td colspan=2>".php_uname();
    echo "<tr><td>safe_mode<td>".($safe_mode?"on":"off");
    if ($is_windows_server) echo "<tr><td>sisop<td>Windows<br>";
    else echo "<tr><td>sisop<td>Linux<br>";
    echo "</table><br><br><table border=1 cellspacing=0 cellpadding=4 align=center>";
    $display_errors=ini_get("display_errors");
    $ignore_user_abort = ignore_user_abort();
    $max_execution_time = ini_get("max_execution_time");
    $upload_max_filesize = ini_get("upload_max_filesize");
    $memory_limit=ini_get("memory_limit");
    $output_buffering=ini_get("output_buffering");
    $default_socket_timeout=ini_get("default_socket_timeout");
    $allow_url_fopen = ini_get("allow_url_fopen");
    $magic_quotes_gpc = ini_get("magic_quotes_gpc");
    ignore_user_abort(true);
    ini_set("display_errors",0);
    ini_set("max_execution_time",0);
    ini_set("upload_max_filesize","10M");
    ini_set("memory_limit","20M");
    ini_set("output_buffering",0);
    ini_set("default_socket_timeout",30);
    ini_set("allow_url_fopen",1);
    ini_set("magic_quotes_gpc",0);
    echo "<tr><td> <td>Get<td>Set<td>Get";
    echo "<tr><td>display_errors<td>$display_errors<td>0<td>".ini_get("display_errors");
    echo "<tr><td>ignore_user_abort<td>".($ignore_user_abort?"on":"off")."<td>on<td>".(ignore_user_abort()?"on":"off");
    echo "<tr><td>max_execution_time<td>$max_execution_time<td>0<td>".ini_get("max_execution_time");
    echo "<tr><td>upload_max_filesize<td>$upload_max_filesize<td>10M<td>".ini_get("upload_max_filesize");
    echo "<tr><td>memory_limit<td>$memory_limit<td>20M<td>".ini_get("memory_limit");
    echo "<tr><td>output_buffering<td>$output_buffering<td>0<td>".ini_get("output_buffering");
    echo "<tr><td>default_socket_timeout<td>$default_socket_timeout<td>30<td>".ini_get("default_socket_timeout");
    echo "<tr><td>allow_url_fopen<td>$allow_url_fopen<td>1<td>".ini_get("allow_url_fopen");
    echo "<tr><td>magic_quotes_gpc<td>$magic_quotes_gpc<td>0<td>".ini_get("magic_quotes_gpc");
    echo "</table><br><br>";
    echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
        window.focus();
    //-->
    </script>";
    echo "</body>\n</html>";
	*/
}
// +--------------------------------------------------
// | Session
// +--------------------------------------------------
function logout(){
    setcookie("loggedon",0,0,"/");
    login_form();
}
function login(){
    global $pass,$auth_pass,$path_info;
    if (md5(trim($pass)) == $auth_pass){
        setcookie("loggedon",$auth_pass,0,"/");
        header ("Location: ".$path_info["basename"]."");
    } else header ("Location: ".$path_info["basename"]."?erro=1");
}
function login_form(){
    global $erro,$auth_pass,$path_info;
    html_header();
    echo "<body onLoad=\"if(parent.location.href != self.location.href){ parent.location.href = self.location.href } return true;\">\n";
    if ($auth_pass != md5("")){
        echo "
        <table border=0 cellspacing=0 cellpadding=5>
            <form name=\"login_form\" action=\"".$path_info["basename"]."\" method=\"post\">
            <tr>
            <td><b>".et('FileMan')."</b>
            </tr>
            <tr>
            <td align=left><font size=4>".et('TypePass').".</font>
            </tr>
            <tr>
            <td><input name=pass type=password size=10> <input type=submit value=\"".et('Send')."\">
            </tr>
        ";
        if (strlen($erro)) echo "
            <tr>
            <td align=left><font color=red size=4>".et('InvPass').".</font>
            </tr>
        ";
        echo "
            </form>
        </table>
             <script language=\"Javascript\" type=\"text/javascript\">
             <!--
             document.login_form.pass.focus();
             //-->
             </script>
        ";
    } else {
        echo "
        <table border=0 cellspacing=0 cellpadding=5>
            <form name=\"login_form\" action=\"".$path_info["basename"]."\" method=\"post\">
            <input type=hidden name=frame value=3>
            <input type=hidden name=pass value=\"\">
            <tr>
            <td><b>".et('FileMan')."</b>
            </tr>
            <tr>
            <td><input type=submit value=\"".et('Enter')."\">
            </tr>
            </form>
        </table>
        ";
    }
    echo "</body>\n</html>";
}
function frame3(){
    global $islinux,$cmd_arg,$chmod_arg,$zip_dir,$fm_current_root,$cookie_cache_time;
    global $dir_dest,$current_dir,$dir_before;
    global $selected_file_list,$selected_dir_list,$old_name,$new_name;
    global $action,$or_by,$order_dir_list_by;
    if (!isset($order_dir_list_by)){
        $order_dir_list_by = "1A";
        setcookie("order_dir_list_by", $order_dir_list_by , time()+$cookie_cache_time , "/");
    } elseif (strlen($or_by)){
        $order_dir_list_by = $or_by;
        setcookie("order_dir_list_by", $or_by , time()+$cookie_cache_time , "/");
    }
    html_header();
    echo "<body>\n";
    if ($action){
        switch ($action){
            case 1: // create dir
            if (strlen($cmd_arg)){
                $cmd_arg = format_path($current_dir.$cmd_arg);
                if (!file_exists($cmd_arg)){
                  //  @mkdir($cmd_arg,0711);
                    @chmod($cmd_arg,0711);
                    reloadframe("parent",2,"&ec_dir=".$cmd_arg);
                } else alert(et('FileDirExists').".");
            }
            break;
            case 2: // create arq
            if (strlen($cmd_arg)){
                $cmd_arg = $current_dir.$cmd_arg;
                if (!file_exists($cmd_arg)){
                    if ($fh = @fopen($cmd_arg, "w")){
                        @fclose($fh);
                    }
                    @chmod($cmd_arg,0644);
                } else alert(et('FileDirExists').".");
            }
            break;
            case 21: // create IOFilename
            if (strlen($cmd_arg)){
                $filename = $current_dir."input.name";
		if ($fh = @fopen($filename, "w")){
		        fprintf($fh,"%s.in\n",$cmd_arg);	
                        @fclose($fh);
                }
                @chmod($filename,0644);
                $filename = $current_dir."output.name";
		if ($fh = @fopen($filename, "w")){
		        fprintf($fh,"%s.out\n",$cmd_arg);	
                        @fclose($fh);
                }
                @chmod($filename,0644);
            }
	    case 22: // create SolutionFilename
            if (strlen($cmd_arg)){
                $filename = $current_dir."solution.name";
		if ($fh = @fopen($filename, "w")){
		        fprintf($fh,"%s\n",$cmd_arg);	// maybe   "%s.cpp\n" ?
                        @fclose($fh);
                }
                @chmod($filename,0644);
            }
            break;
            case 3: // rename arq ou dir
            if ((strlen($old_name))&&(strlen($new_name))){
                rename($current_dir.$old_name,$current_dir.$new_name);
                if (is_dir($current_dir.$new_name)) reloadframe("parent",2);
            }
            break;
            case 4: // delete sel
            if(strstr($current_dir,$fm_current_root)){
                if (strlen($selected_file_list)){
                    $selected_file_list = explode("<|*|>",$selected_file_list);
                    if (count($selected_file_list)) {
                        for($x=0;$x<count($selected_file_list);$x++) {
                            $selected_file_list[$x] = trim($selected_file_list[$x]);
                            if (strlen($selected_file_list[$x])) total_delete($current_dir.$selected_file_list[$x],$dir_dest.$selected_file_list[$x]);
                        }
                    }
                }
                if (strlen($selected_dir_list)){
                    $selected_dir_list = explode("<|*|>",$selected_dir_list);
                    if (count($selected_dir_list)) {
                        for($x=0;$x<count($selected_dir_list);$x++) {
                            $selected_dir_list[$x] = trim($selected_dir_list[$x]);
                            if (strlen($selected_dir_list[$x])) total_delete($current_dir.$selected_dir_list[$x],$dir_dest.$selected_dir_list[$x]);
                        }
                        reloadframe("parent",2);
                    }
                }
            }
            break;
            case 5: // copy sel
            if (strlen($dir_dest)){
                if(uppercase($dir_dest) != uppercase($current_dir)){
                    if (strlen($selected_file_list)){
                        $selected_file_list = explode("<|*|>",$selected_file_list);
                        if (count($selected_file_list)) {
                            for($x=0;$x<count($selected_file_list);$x++) {
                                $selected_file_list[$x] = trim($selected_file_list[$x]);
                                if (strlen($selected_file_list[$x])) total_copy($current_dir.$selected_file_list[$x],$dir_dest.$selected_file_list[$x]);
                            }
                        }
                    }
                    if (strlen($selected_dir_list)){
                        $selected_dir_list = explode("<|*|>",$selected_dir_list);
                        if (count($selected_dir_list)) {
                            for($x=0;$x<count($selected_dir_list);$x++) {
                                $selected_dir_list[$x] = trim($selected_dir_list[$x]);
                                if (strlen($selected_dir_list[$x])) total_copy($current_dir.$selected_dir_list[$x],$dir_dest.$selected_dir_list[$x]);
                            }
                            reloadframe("parent",2);
                        }
                    }
                    $current_dir = $dir_dest;
                }
            }
            break;
            case 6: // move sel
            if (strlen($dir_dest)){
                if(uppercase($dir_dest) != uppercase($current_dir)){
                    if (strlen($selected_file_list)){
                        $selected_file_list = explode("<|*|>",$selected_file_list);
                        if (count($selected_file_list)) {
                            for($x=0;$x<count($selected_file_list);$x++) {
                                $selected_file_list[$x] = trim($selected_file_list[$x]);
                                if (strlen($selected_file_list[$x])) total_move($current_dir.$selected_file_list[$x],$dir_dest.$selected_file_list[$x]);
                            }
                        }
                    }
                    if (strlen($selected_dir_list)){
                        $selected_dir_list = explode("<|*|>",$selected_dir_list);
                        if (count($selected_dir_list)) {
                            for($x=0;$x<count($selected_dir_list);$x++) {
                                $selected_dir_list[$x] = trim($selected_dir_list[$x]);
                                if (strlen($selected_dir_list[$x])) total_move($current_dir.$selected_dir_list[$x],$dir_dest.$selected_dir_list[$x]);
                            }
                            reloadframe("parent",2);
                        }
                    }
                    $current_dir = $dir_dest;
                }
            }
            break;
            case 71: // compress sel
            if (strlen($cmd_arg)){
                ignore_user_abort(true);
                ini_set("display_errors",0);
                ini_set("max_execution_time",0);
                $zipfile=false;
                if (strstr($cmd_arg,".tar")) $zipfile = new tar_file($cmd_arg);
                elseif (strstr($cmd_arg,".zip")) $zipfile = new zip_file($cmd_arg);
                elseif (strstr($cmd_arg,".bzip")) $zipfile = new bzip_file($cmd_arg);
                elseif (strstr($cmd_arg,".gzip")) $zipfile = new gzip_file($cmd_arg);
                if ($zipfile){
                    $zipfile->set_options(array('basedir'=>$current_dir,'overwrite'=>1,'level'=>3));
                    if (strlen($selected_file_list)){
                        $selected_file_list = explode("<|*|>",$selected_file_list);
                        if (count($selected_file_list)) {
                            for($x=0;$x<count($selected_file_list);$x++) {
                                $selected_file_list[$x] = trim($selected_file_list[$x]);
                                if (strlen($selected_file_list[$x])) $zipfile->add_files($selected_file_list[$x]);
                            }
                        }
                    }
                    if (strlen($selected_dir_list)){
                        $selected_dir_list = explode("<|*|>",$selected_dir_list);
                        if (count($selected_dir_list)) {
                            for($x=0;$x<count($selected_dir_list);$x++) {
                                $selected_dir_list[$x] = trim($selected_dir_list[$x]);
                                if (strlen($selected_dir_list[$x])) $zipfile->add_files($selected_dir_list[$x]);
                            }
                        }
                    }
                    $zipfile->create_archive();
                }
                unset($zipfile);
            }
            break;
            case 72: // decompress arq
            if (strlen($cmd_arg)){
                if (file_exists($current_dir.$cmd_arg)){
                    $zipfile=false;
                    if (strstr($cmd_arg,".zip")) zip_extract();
                    elseif (strstr($cmd_arg,".bzip")||strstr($cmd_arg,".bz2")||strstr($cmd_arg,".tbz2")||strstr($cmd_arg,".bz")||strstr($cmd_arg,".tbz")) $zipfile = new bzip_file($cmd_arg);
                    elseif (strstr($cmd_arg,".gzip")||strstr($cmd_arg,".gz")||strstr($cmd_arg,".tgz")) $zipfile = new gzip_file($cmd_arg);
                    elseif (strstr($cmd_arg,".tar")) $zipfile = new tar_file($cmd_arg);
                    if ($zipfile){
                        $zipfile->set_options(array('basedir'=>$current_dir,'overwrite'=>1));
                        $zipfile->extract_files();
                    }
                    unset($zipfile);
 		    if(function_exists("system"))system("/home/judge/src/install/ans2out ".$current_dir);
 		    if(file_exists("/usr/bin/dos2unix")&&function_exists("system")) system("/usr/bin/dos2unix ".$current_dir."/*");
                    reloadframe("parent",2);
                }
            }
            break;
            case 8: // delete arq/dir
            if (strlen($cmd_arg)){
                if (file_exists($current_dir.$cmd_arg)) total_delete($current_dir.$cmd_arg);
                if (is_dir($current_dir.$cmd_arg)) reloadframe("parent",2);
            }
            break;
            case 9: // CHMOD
            if((strlen($chmod_arg) == 4)&&(strlen($current_dir))){
                if ($chmod_arg[0]=="1") $chmod_arg = "0".$chmod_arg;
                else $chmod_arg = "0".substr($chmod_arg,strlen($chmod_arg)-3);
                $new_mod = octdec($chmod_arg);
                if (strlen($selected_file_list)){
                    $selected_file_list = explode("<|*|>",$selected_file_list);
                    if (count($selected_file_list)) {
                        for($x=0;$x<count($selected_file_list);$x++) {
                            $selected_file_list[$x] = trim($selected_file_list[$x]);
                            if (strlen($selected_file_list[$x])) @chmod($current_dir.$selected_file_list[$x],$new_mod);
                        }
                    }
                }
                if (strlen($selected_dir_list)){
                    $selected_dir_list = explode("<|*|>",$selected_dir_list);
                    if (count($selected_dir_list)) {
                        for($x=0;$x<count($selected_dir_list);$x++) {
                            $selected_dir_list[$x] = trim($selected_dir_list[$x]);
                            if (strlen($selected_dir_list[$x])) @chmod($current_dir.$selected_dir_list[$x],$new_mod);
                        }
                    }
                }
            }
            break;
        }
        if ($action != 10) dir_list_form();
    } else dir_list_form();
    echo "</body>\n</html>";
}
function frame2(){
    global $expanded_dir_list,$ec_dir;
    if (!isset($expanded_dir_list)) $expanded_dir_list = "";
    if (strlen($ec_dir)){
        if (strstr($expanded_dir_list,":".$ec_dir)) $expanded_dir_list = str_replace(":".$ec_dir,"",$expanded_dir_list);
        else $expanded_dir_list .= ":".$ec_dir;
        setcookie("expanded_dir_list", $expanded_dir_list , 0 , "/");
    }
    show_tree();
}
function frameset(){
    global $path_info,$leftFrameWidth;
    if (!isset($leftFrameWidth)) $leftFrameWidth = 300;
    html_header();
    echo "
    <frameset cols=\"".$leftFrameWidth.",*\" framespacing=\"0\">
        <frameset rows=\"0,*\" framespacing=\"0\" frameborder=\"0\">
            <frame src=\"".$path_info["basename"]."?frame=1\" name=frame1 border=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\">
            <frame src=\"".$path_info["basename"]."?frame=2\" name=frame2 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
        </frameset>
        <frame src=\"".$path_info["basename"]."?frame=3\" name=frame3 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
    </frameset>
    </html>";
}
// +--------------------------------------------------
// | Open Source Contributions
// +--------------------------------------------------
 /*-------------------------------------------------
 | TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.0
 | By Devin Doucette
 | Copyright (c) 2004 Devin Doucette
 | Email: darksnoopy@shaw.ca
 +--------------------------------------------------
 | Email bugs/suggestions to darksnoopy@shaw.ca
 +--------------------------------------------------
 | This script has been created and released under
 | the GNU GPL and is free to use and redistribute
 | only if this copyright statement is not removed
 +--------------------------------------------------
 | Limitations:
 | - Only USTAR archives are officially supported for extraction, but others may work.
 | - Extraction of bzip2 and gzip archives is limited to compatible tar files that have
 | been compressed by either bzip2 or gzip.  For greater support, use the functions
 | bzopen and gzopen respectively for bzip2 and gzip extraction.
 | - Zip extraction is not supported due to the wide variety of algorithms that may be
 | used for compression and newer features such as encryption.
 +--------------------------------------------------
 */
class archive
{
    function __construct($name)
    {
        $this->options = array(
            'basedir'=>".",
            'name'=>$name,
            'prepend'=>"",
            'inmemory'=>0,
            'overwrite'=>0,
            'recurse'=>1,
            'storepaths'=>1,
            'level'=>3,
            'method'=>1,
            'sfx'=>"",
            'type'=>"",
            'comment'=>""
        );
        $this->files = array();
        $this->exclude = array();
        $this->storeonly = array();
        $this->error = array();
    }
 function archive($name)
    {
        $this->options = array(
            'basedir'=>".",
            'name'=>$name,
            'prepend'=>"",
            'inmemory'=>0,
            'overwrite'=>0,
            'recurse'=>1,
            'storepaths'=>1,
            'level'=>3,
            'method'=>1,
            'sfx'=>"",
            'type'=>"",
            'comment'=>""
        );
        $this->files = array();
        $this->exclude = array();
        $this->storeonly = array();
        $this->error = array();
    }
    function set_options($options)
    {
        foreach($options as $key => $value)
        {
            $this->options[$key] = $value;
        }
        if(!empty($this->options['basedir']))
        {
            $this->options['basedir'] = str_replace("\\","/",$this->options['basedir']);
            $this->options['basedir'] = preg_replace("/\/+/","/",$this->options['basedir']);
            $this->options['basedir'] = preg_replace("/\/$/","",$this->options['basedir']);
        }
        if(!empty($this->options['name']))
        {
            $this->options['name'] = str_replace("\\","/",$this->options['name']);
            $this->options['name'] = preg_replace("/\/+/","/",$this->options['name']);
        }
        if(!empty($this->options['prepend']))
        {
            $this->options['prepend'] = str_replace("\\","/",$this->options['prepend']);
            $this->options['prepend'] = preg_replace("/^(\.*\/+)+/","",$this->options['prepend']);
            $this->options['prepend'] = preg_replace("/\/+/","/",$this->options['prepend']);
            $this->options['prepend'] = preg_replace("/\/$/","",$this->options['prepend']) . "/";
        }
    }

    function create_archive()
    {
        $this->make_list();

        if($this->options['inmemory'] == 0)
        {
            $Pwd = getcwd();
            chdir($this->options['basedir']);
            if($this->options['overwrite'] == 0 && file_exists($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip"? ".tmp" : "")))
            {
                $this->error[] = "File {$this->options['name']} already exists.";
                chdir($Pwd);
                return 0;
            }
            else if($this->archive = @fopen($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip"? ".tmp" : ""),"wb+"))
            {
                chdir($Pwd);
            }
            else
            {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($Pwd);
                return 0;
            }
        }
        else
        {
            $this->archive = "";
        }

        switch($this->options['type'])
        {
        case "zip":
            if(!$this->create_zip())
            {
                $this->error[] = "Could not create zip file.";
                return 0;
            }
            break;
        case "bzip":
            if(!$this->create_tar())
            {
                $this->error[] = "Could not create tar file.";
                return 0;
            }
            if(!$this->create_bzip())
            {
                $this->error[] = "Could not create bzip2 file.";
                return 0;
            }
            break;
        case "gzip":
            if(!$this->create_tar())
            {
                $this->error[] = "Could not create tar file.";
                return 0;
            }
            if(!$this->create_gzip())
            {
                $this->error[] = "Could not create gzip file.";
                return 0;
            }
            break;
        case "tar":
            if(!$this->create_tar())
            {
                $this->error[] = "Could not create tar file.";
                return 0;
            }
        }

        if($this->options['inmemory'] == 0)
        {
            fclose($this->archive);
            @chmod($this->options['name'],0644);
            if($this->options['type'] == "gzip" || $this->options['type'] == "bzip")
            {
                unlink($this->options['basedir'] . "/" . $this->options['name'] . ".tmp");
            }
        }
    }

    function add_data($data)
    {
        if($this->options['inmemory'] == 0)
        {
            fwrite($this->archive,$data);
        }
        else
        {
            $this->archive .= $data;
        }
    }

    function make_list()
    {
        if(!empty($this->exclude))
        {
            foreach($this->files as $key => $value)
            {
                foreach($this->exclude as $current)
                {
                    if($value['name'] == $current['name'])
                    {
                        unset($this->files[$key]);
                    }
                }
            }
        }
        if(!empty($this->storeonly))
        {
            foreach($this->files as $key => $value)
            {
                foreach($this->storeonly as $current)
                {
                    if($value['name'] == $current['name'])
                    {
                        $this->files[$key]['method'] = 0;
                    }
                }
            }
        }
        unset($this->exclude,$this->storeonly);
    }


    function add_files($list)
    {
        $temp = $this->list_files($list);
        foreach($temp as $current)
        {
            $this->files[] = $current;
        }
    }

    function exclude_files($list)
    {
        $temp = $this->list_files($list);
        foreach($temp as $current)
        {
            $this->exclude[] = $current;
        }
    }

    function store_files($list)
    {
        $temp = $this->list_files($list);
        foreach($temp as $current)
        {
            $this->storeonly[] = $current;
        }
    }

    function list_files($list)
    {
        if(!is_array($list))
        {
            $temp = $list;
            $list = array($temp);
            unset($temp);
        }

        $files = array();

        $Pwd = getcwd();
        chdir($this->options['basedir']);

        foreach($list as $current)
        {
            $current = str_replace("\\","/",$current);
            $current = preg_replace("/\/+/","/",$current);
            $current = preg_replace("/\/$/","",$current);
            if(strstr($current,"*"))
            {
                $regex = preg_replace("/([\\\^\$\.\[\]\|\(\)\?\+\{\}\/])/","\\\\\\1",$current);
                $regex = str_replace("*",".*",$regex);
                $dir = strstr($current,"/")? substr($current,0,strrpos($current,"/")) : ".";
                $temp = $this->parse_dir($dir);
                foreach($temp as $current2)
                {
                    if(preg_match("/^{$regex}$/i",$current2['name']))
                    {
                        $files[] = $current2;
                    }
                }
                unset($regex,$dir,$temp,$current);
            }
            else if(@is_dir($current))
            {
                $temp = $this->parse_dir($current);
                foreach($temp as $file)
                {
                    $files[] = $file;
                }
                unset($temp,$file);
            }
            else if(@file_exists($current))
            {
                $files[] = array('name'=>$current,'name2'=>$this->options['prepend'] .
                    preg_replace("/(\.+\/+)+/","",($this->options['storepaths'] == 0 && strstr($current,"/"))?
                    substr($current,strrpos($current,"/") + 1) : $current),'type'=>0,
                    'ext'=>substr($current,strrpos($current,".")),'stat'=>stat($current));
            }
        }

        chdir($Pwd);

        unset($current,$Pwd);

        usort($files,array("archive","sort_files"));

        return $files;
    }

    function parse_dir($dirname)
    {
        if($this->options['storepaths'] == 1 && !preg_match("/^(\.+\/*)+$/",$dirname))
        {
            $files = array(array('name'=>$dirname,'name2'=>$this->options['prepend'] .
                preg_replace("/(\.+\/+)+/","",($this->options['storepaths'] == 0 && strstr($dirname,"/"))?
                substr($dirname,strrpos($dirname,"/") + 1) : $dirname),'type'=>5,'stat'=>stat($dirname)));
        }
        else
        {
            $files = array();
        }
        $dir = @opendir($dirname);

        while($file = @readdir($dir))
        {
            if($file == "." || $file == "..")
            {
                continue;
            }
            else if(@is_dir($dirname."/".$file))
            {
                if(empty($this->options['recurse']))
                {
                    continue;
                }
                $temp = $this->parse_dir($dirname."/".$file);
                foreach($temp as $file2)
                {
                    $files[] = $file2;
                }
            }
            else if(@file_exists($dirname."/".$file))
            {
                $files[] = array('name'=>$dirname."/".$file,'name2'=>$this->options['prepend'] .
                    preg_replace("/(\.+\/+)+/","",($this->options['storepaths'] == 0 && strstr($dirname."/".$file,"/"))?
                    substr($dirname."/".$file,strrpos($dirname."/".$file,"/") + 1) : $dirname."/".$file),'type'=>0,
                    'ext'=>substr($file,strrpos($file,".")),'stat'=>stat($dirname."/".$file));
            }
        }

        @closedir($dir);

        return $files;
    }

    function sort_files($a,$b)
    {
        if($a['type'] != $b['type'])
        {
            return $a['type'] > $b['type']? -1 : 1;
        }
        else if($a['type'] == 5)
        {
            return strcmp(strtolower($a['name']),strtolower($b['name']));
        }
        else
        {
            if($a['ext'] != $b['ext'])
            {
                return strcmp($a['ext'],$b['ext']);
            }
            else if($a['stat'][7] != $b['stat'][7])
            {
                return $a['stat'][7] > $b['stat'][7]? -1 : 1;
            }
            else
            {
                return strcmp(strtolower($a['name']),strtolower($b['name']));
            }
        }
        return 0;
    }

    function download_file()
    {
        if($this->options['inmemory'] == 0)
        {
            $this->error[] = "Can only use download_file() if archive is in memory. Redirect to file otherwise, it is faster.";
            return;
        }
        switch($this->options['type'])
        {
        case "zip":
            header("Content-type:application/zip");
            break;
        case "bzip":
            header("Content-type:application/x-compressed");
            break;
        case "gzip":
            header("Content-type:application/x-compressed");
            break;
        case "tar":
            header("Content-type:application/x-tar");
        }
        $header = "Content-disposition: attachment; filename=\"";
        $header .= strstr($this->options['name'],"/")? substr($this->options['name'],strrpos($this->options['name'],"/") + 1) : $this->options['name'];
        $header .= "\"";
        header($header);
        header("Content-length: " . strlen($this->archive));
        header("Content-transfer-encoding: binary");
        header("Cache-control: no-cache, must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print($this->archive);
    }
}

class tar_file extends archive
{
    function __construct($name)
    {
        $this->archive($name);
        $this->options['type'] = "tar";
    }

    function create_tar()
    {
        $Pwd = getcwd();
        chdir($this->options['basedir']);

        foreach($this->files as $current)
        {
            if($current['name'] == $this->options['name'])
            {
                continue;
            }
            if(strlen($current['name2']) > 99)
            {
                $Path = substr($current['name2'],0,strpos($current['name2'],"/",strlen($current['name2']) - 100) + 1);
                $current['name2'] = substr($current['name2'],strlen($Path));
                if(strlen($Path) > 154 || strlen($current['name2']) > 99)
                {
                    $this->error[] = "Could not add {$Path}{$current['name2']} to archive because the filename is too long.";
                    continue;
                }
            }
            $block = pack("a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12",$current['name2'],decoct($current['stat'][2]),
                sprintf("%6s ",decoct($current['stat'][4])),sprintf("%6s ",decoct($current['stat'][5])),
                sprintf("%11s ",decoct($current['stat'][7])),sprintf("%11s ",decoct($current['stat'][9])),
                "        ",$current['type'],"","ustar","00","Unknown","Unknown","","",!empty($Path)? $Path : "","");

            $checksum = 0;
            for($i = 0; $i < 512; $i++)
            {
                $checksum += ord(substr($block,$i,1));
            }
            $checksum = pack("a8",sprintf("%6s ",decoct($checksum)));
            $block = substr_replace($block,$checksum,148,8);

            if($current['stat'][7] == 0)
            {
                $this->add_data($block);
            }
            else if($fp = @fopen($current['name'],"rb"))
            {
                $this->add_data($block);
                while($temp = fread($fp,1048576))
                {
                    $this->add_data($temp);
                }
                if($current['stat'][7] % 512 > 0)
                {
                    $temp = "";
                    for($i = 0; $i < 512 - $current['stat'][7] % 512; $i++)
                    {
                        $temp .= "\0";
                    }
                    $this->add_data($temp);
                }
                fclose($fp);
            }
            else
            {
                $this->error[] = "Could not open file {$current['name']} for reading. It was not added.";
            }
        }

        $this->add_data(pack("a512",""));

        chdir($Pwd);

        return 1;

    }

    function extract_files()
    {
        $Pwd = getcwd();
        chdir($this->options['basedir']);

        if($fp = $this->open_archive())
        {
            if($this->options['inmemory'] == 1)
            {
                $this->files = array();
            }

            while($block = fread($fp,512))
            {
                $temp = unpack("a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100temp/a6magic/a2temp/a32temp/a32temp/a8temp/a8temp/a155prefix/a12temp",$block);
                $file = array(
                    'name'=>$temp['prefix'] . $temp['name'],
                    'stat'=>array(
                        2=>$temp['mode'],
                        4=>octdec($temp['uid']),
                        5=>octdec($temp['gid']),
                        7=>octdec($temp['size']),
                        9=>octdec($temp['mtime']),
                    ),
                    'checksum'=>octdec($temp['checksum']),
                    'type'=>$temp['type'],
                    'magic'=>$temp['magic'],
                );
                if($file['checksum'] == 0x00000000)
                {
                    break;
                }
                else if($file['magic'] != "ustar")
                {
                    $this->error[] = "This script does not support extracting this type of tar file.";
                    break;
                }
                $block = substr_replace($block,"        ",148,8);
                $checksum = 0;
                for($i = 0; $i < 512; $i++)
                {
                    $checksum += ord(substr($block,$i,1));
                }
                if($file['checksum'] != $checksum)
                {
                    $this->error[] = "Could not extract from {$this->options['name']}, it is corrupt.";
                }

                if($this->options['inmemory'] == 1)
                {
                    $file['data'] = fread($fp,$file['stat'][7]);
                    fread($fp,(512 - $file['stat'][7] % 512) == 512? 0 : (512 - $file['stat'][7] % 512));
                    unset($file['checksum'],$file['magic']);
                    $this->files[] = $file;
                }
                else
                {
                    if($file['type'] == 5)
                    {
                        if(!is_dir($file['name']))
                        {
                            mkdir($file['name'],0711);
                            //mkdir($file['name'],$file['stat'][2]);
                            //chown($file['name'],$file['stat'][4]);
                            //chgrp($file['name'],$file['stat'][5]);
                        }
                    }
                    else if($this->options['overwrite'] == 0 && file_exists($file['name']))
                    {
                        $this->error[] = "{$file['name']} already exists.";
                    }
                    else if($new = @fopen($file['name'],"wb"))
                    {
                        fwrite($new,fread($fp,$file['stat'][7]));
                        fread($fp,(512 - $file['stat'][7] % 512) == 512? 0 : (512 - $file['stat'][7] % 512));
                        fclose($new);
                        @chmod($file['name'],0644);
                        //chmod($file['name'],$file['stat'][2]);
                        //chown($file['name'],$file['stat'][4]);
                        //chgrp($file['name'],$file['stat'][5]);
                    }
                    else
                    {
                        $this->error[] = "Could not open {$file['name']} for writing.";
                    }
                }
                unset($file);
            }
        }
        else
        {
            $this->error[] = "Could not open file {$this->options['name']}";
        }

        chdir($Pwd);
    }

    function open_archive()
    {
        return @fopen($this->options['name'],"rb");
    }
}

class gzip_file extends tar_file
{
    function __construct($name)
    {
        $this->tar_file($name);
        $this->options['type'] = "gzip";
    }

    function create_gzip()
    {
        if($this->options['inmemory'] == 0)
        {
            $Pwd = getcwd();
            chdir($this->options['basedir']);
            if($fp = gzopen($this->options['name'],"wb{$this->options['level']}"))
            {
                fseek($this->archive,0);
                while($temp = fread($this->archive,1048576))
                {
                    gzwrite($fp,$temp);
                }
                gzclose($fp);
                chdir($Pwd);
            }
            else
            {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($Pwd);
                return 0;
            }
        }
        else
        {
            $this->archive = gzencode($this->archive,$this->options['level']);
        }

        return 1;
    }

    function open_archive()
    {
        return @gzopen($this->options['name'],"rb");
    }
}

class bzip_file extends tar_file
{
    function __construct($name)
    {
        $this->tar_file($name);
        $this->options['type'] = "bzip";
    }

    function create_bzip()
    {
        if($this->options['inmemory'] == 0)
        {
            $Pwd = getcwd();
            chdir($this->options['basedir']);
            if($fp = bzopen($this->options['name'],"wb"))
            {
                fseek($this->archive,0);
                while($temp = fread($this->archive,1048576))
                {
                    bzwrite($fp,$temp);
                }
                bzclose($fp);
                chdir($Pwd);
            }
            else
            {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($Pwd);
                return 0;
            }
        }
        else
        {
            $this->archive = bzcompress($this->archive,$this->options['level']);
        }

        return 1;
    }

    function open_archive()
    {
        return @bzopen($this->options['name'],"rb");
    }
}

class zip_file extends archive
{
    function __construct($name)
    {
        $this->archive($name);
        $this->options['type'] = "zip";
    }

    function create_zip()
    {
        $files = 0;
        $offset = 0;
        $central = "";

        if(!empty($this->options['sfx']))
        {
            if($fp = @fopen($this->options['sfx'],"rb"))
            {
                $temp = fread($fp,filesize($this->options['sfx']));
                fclose($fp);
                $this->add_data($temp);
                $offset += strlen($temp);
                unset($temp);
            }
            else
            {
                $this->error[] = "Could not open sfx module from {$this->options['sfx']}.";
            }
        }

        $Pwd = getcwd();
        chdir($this->options['basedir']);

        foreach($this->files as $current)
        {
            if($current['name'] == $this->options['name'])
            {
                continue;
            }
            $translate =  array('Ç'=>pack("C",128),'ü'=>pack("C",129),'é'=>pack("C",130),'â'=>pack("C",131),'ä'=>pack("C",132),
                                'à'=>pack("C",133),'å'=>pack("C",134),'ç'=>pack("C",135),'ê'=>pack("C",136),'ë'=>pack("C",137),
                                'è'=>pack("C",138),'ï'=>pack("C",139),'î'=>pack("C",140),'ì'=>pack("C",141),'Ä'=>pack("C",142),
                                'Å'=>pack("C",143),'É'=>pack("C",144),'æ'=>pack("C",145),'Æ'=>pack("C",146),'ô'=>pack("C",147),
                                'ö'=>pack("C",148),'ò'=>pack("C",149),'û'=>pack("C",150),'ù'=>pack("C",151),'_'=>pack("C",152),
                                'Ö'=>pack("C",153),'Ü'=>pack("C",154),'£'=>pack("C",156),'¥'=>pack("C",157),'_'=>pack("C",158),
                                'ƒ'=>pack("C",159),'á'=>pack("C",160),'í'=>pack("C",161),'ó'=>pack("C",162),'ú'=>pack("C",163),
                                'ñ'=>pack("C",164),'Ñ'=>pack("C",165));
            $current['name2'] = strtr($current['name2'],$translate);

            $timedate = explode(" ",date("Y n j G i s",$current['stat'][9]));
            $timedate = ($timedate[0] - 1980 << 25) | ($timedate[1] << 21) | ($timedate[2] << 16) |
                ($timedate[3] << 11) | ($timedate[4] << 5) | ($timedate[5]);

            $block = pack("VvvvV",0x04034b50,0x000A,0x0000,(isset($current['method']) || $this->options['method'] == 0)? 0x0000 : 0x0008,$timedate);

            if($current['stat'][7] == 0 && $current['type'] == 5)
            {
                $block .= pack("VVVvv",0x00000000,0x00000000,0x00000000,strlen($current['name2']) + 1,0x0000);
                $block .= $current['name2'] . "/";
                $this->add_data($block);
                $central .= pack("VvvvvVVVVvvvvvVV",0x02014b50,0x0014,$this->options['method'] == 0? 0x0000 : 0x000A,0x0000,
                    (isset($current['method']) || $this->options['method'] == 0)? 0x0000 : 0x0008,$timedate,
                    0x00000000,0x00000000,0x00000000,strlen($current['name2']) + 1,0x0000,0x0000,0x0000,0x0000,$current['type'] == 5? 0x00000010 : 0x00000000,$offset);
                $central .= $current['name2'] . "/";
                $files++;
                $offset += (31 + strlen($current['name2']));
            }
            else if($current['stat'][7] == 0)
            {
                $block .= pack("VVVvv",0x00000000,0x00000000,0x00000000,strlen($current['name2']),0x0000);
                $block .= $current['name2'];
                $this->add_data($block);
                $central .= pack("VvvvvVVVVvvvvvVV",0x02014b50,0x0014,$this->options['method'] == 0? 0x0000 : 0x000A,0x0000,
                    (isset($current['method']) || $this->options['method'] == 0)? 0x0000 : 0x0008,$timedate,
                    0x00000000,0x00000000,0x00000000,strlen($current['name2']),0x0000,0x0000,0x0000,0x0000,$current['type'] == 5? 0x00000010 : 0x00000000,$offset);
                $central .= $current['name2'];
                $files++;
                $offset += (30 + strlen($current['name2']));
            }
            else if($fp = @fopen($current['name'],"rb"))
            {
                $temp = fread($fp,$current['stat'][7]);
                fclose($fp);
                $crc32 = crc32($temp);
                if(!isset($current['method']) && $this->options['method'] == 1)
                {
                    $temp = gzcompress($temp,$this->options['level']);
                    $size = strlen($temp) - 6;
                    $temp = substr($temp,2,$size);
                }
                else
                {
                    $size = strlen($temp);
                }
                $block .= pack("VVVvv",$crc32,$size,$current['stat'][7],strlen($current['name2']),0x0000);
                $block .= $current['name2'];
                $this->add_data($block);
                $this->add_data($temp);
                unset($temp);
                $central .= pack("VvvvvVVVVvvvvvVV",0x02014b50,0x0014,$this->options['method'] == 0? 0x0000 : 0x000A,0x0000,
                    (isset($current['method']) || $this->options['method'] == 0)? 0x0000 : 0x0008,$timedate,
                    $crc32,$size,$current['stat'][7],strlen($current['name2']),0x0000,0x0000,0x0000,0x0000,0x00000000,$offset);
                $central .= $current['name2'];
                $files++;
                $offset += (30 + strlen($current['name2']) + $size);
            }
            else
            {
                $this->error[] = "Could not open file {$current['name']} for reading. It was not added.";
            }
        }

        $this->add_data($central);

        $this->add_data(pack("VvvvvVVv",0x06054b50,0x0000,0x0000,$files,$files,strlen($central),$offset,
            !empty($this->options['comment'])? strlen($this->options['comment']) : 0x0000));

        if(!empty($this->options['comment']))
        {
            $this->add_data($this->options['comment']);
        }

        chdir($Pwd);

        return 1;
    }
}
// +--------------------------------------------------
// | THE END
// +--------------------------------------------------
?>
