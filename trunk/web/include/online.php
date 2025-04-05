<?php
/*
数据库
CREATE TABLE `online` (
  `hash` varchar(32) collate utf8_unicode_ci NOT NULL,
  `ip` varchar(20) character set utf8 NOT NULL default '',
  `ua` varchar(255) character set utf8 NOT NULL default '',
  `refer` varchar(255) collate utf8_unicode_ci default NULL,
  `lastmove` int(10) NOT NULL,
  `firsttime` int(10) default NULL,
  `uri` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`hash`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 */
/**
 * 判定多久未响应的用户为已经离开的用户
 * @var int
 */

define('ONLINE_DURATION', 600);

/**
 * 
 * 本类用来对在线用户进行统计
 * 
 * @package online
 * @author freefcw
 * @link http://www.missway.cn
 * 
 */
class online{
	/**
	 * database connect
	 * @var databse link
	 */
	protected $db;
	/**
	 * current user ip
	 * @var string
	 */
	protected $ip;
	/**
	 * current user agent
	 * @var string
	 */
	protected $ua;
	/**
	 * cureent user visit web uri
	 * @var string
	 */
	protected $uri;
	/**
	 * session id
	 * @var string
	 */
	protected $hash;
	/**
	 * cureent user refer uri
	 * @var string
	 */
	protected $refer;
	//can add function:
	//example click number count
	protected $click;

	/**
	 * construct fuction,init database link
	 * @return void
	 */
	function __construct()
	{
		global $OJ_NAME,$ip;
		/*
		$this->ip = ($_SERVER['REMOTE_ADDR']);
	         if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
	                    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR']               
	                    $tmp_ip=explode(',',$REMOTE_ADDR);
	                    $this->ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
	        }
		*/
		if(isset($_SESSION[$OJ_NAME.'_'.'user_id']))
			$this->ua = htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'],ENT_QUOTES,"UTF-8");
		else
			$this->ua ="guest";
		$this->ua .= "@".htmlentities($_SERVER['HTTP_USER_AGENT'],ENT_QUOTES,"UTF-8");
		$this->uri = ($_SERVER['PHP_SELF']);
		if(isset($_SERVER['HTTP_REFERER'])){
			$this->refer = (htmlentities($_SERVER['HTTP_REFERER'],ENT_QUOTES,"UTF-8"));
	    }
		$this->hash = md5(session_id().$this->ip);
		
		//check user existed!
		if($this->exist()){
			//update databse
			$this->update();
		}else if(!(strstr($this->ua,"bot")||strstr($this->ua,"spider"))){
			//if none, add this record
			$this->addRecord();
		}
		//clean the user who leave our site 
		$this->clean();
	}

	/**
	 * 
	 * return all record!
	 * 
	 * @return array
	 */
	function getAll()
	{
		
		$sql = 'SELECT * FROM online';
		$ret = pdo_query($sql);
		return $ret;
	}
	/**
	 * 
	 * return specfy record
	 * @var string ip
	 * @return object 
	 */
	function getRecord($ip)
	{
		$sql = "SELECT * FROM online WHERE ip = ?";
		$res = pdo_query($sql,$ip);
		if(count($res)){
			$ret = ($res[0]);
		}else{
			return false;
		}
		
		return $ret;
	}
	
	/**
	 * 
	 * get total count
	 * 
	 * @return int
	 */
	function get_num()
	{
		
		$sql = 'SELECT count(ip) as nums FROM online';
		$res = pdo_query($sql);
		$ret = 0;
		if($res){
			$ret = $res[0];
			$ret = $ret['nums'];
	    }
		return $ret;
	}
	/**
	 * check the record exist
	 *
	 * @return boolean 
	 */
	function exist()
	{
		
		$sql = "SELECT count(1) FROM online WHERE hash = ?";
		$res = pdo_query($sql,$this->hash);
		return $res[0][0];

	}
	/**
	 * add a record
	 *
	 * @return void
	 */
	function addRecord()
	{
		
		$now = time();
		$sql = "INSERT INTO online(hash, ip, ua, uri, refer, firsttime, lastmove)
				VALUES (?, ?,?, ?, ?, ?, ?)";
		pdo_query($sql,$this->hash,$this->ip, $this->ua,$this->uri,$this->refer,$now,$now);
	}

	/**
	 * update a record
	 *
	 * @return void
	 */
	function update()
	{
		
		$sql = "UPDATE online
				SET
					ua = ?,
					uri = ?,
					refer = ?,
					lastmove = ?,
					ip = ?
				WHERE
					hash = ?
				";
		pdo_query($sql,$this->ua,$this->uri,$this->refer,time(),$this->ip,$this->hash);
	}
	/**
	 * clean the duration user
	 *
	 * @return void
	 */
	function clean()
	{
		
		$sql = 'DELETE FROM online WHERE lastmove<?';
		pdo_query($sql,(time()-ONLINE_DURATION));
	}
}
