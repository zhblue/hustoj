<?php
        $OJ_CACHE_SHARE=false;
        $cache_time=30;
        require_once('../include/cache_start.php');
    	require_once('../include/db_info.inc.php');
	require_once("../include/my_func.inc.php");
        require_once('../include/setlang.php');
        require_once('../include/memcache.php');
        
        if(!isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}
        $now =  date('Y-m-d H:i', time());
        if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD){
		
        	$sql="select count(contest_id) from contest where start_time<'$now' and end_time>'$now' and title like '%$OJ_NOIP_KEYWORD%'";
		$row=pdo_query($sql);
		$cols=$row[0];
		//echo $sql;
		//echo $cols[0];
		if($cols[0]>0) {
			
		      $view_errors =  "<h2> $MSG_NOIP_WARNING </h2>";
		      require("template/".$OJ_TEMPLATE."/error.php");
		      exit(0);

		}
 	}
        $view_title= $MSG_RANKLIST;
	if(!isset($OJ_RANK_HIDDEN)) $OJ_RANK_HIDDEN="'admin','zhblue'";

        $scope="";
        if(isset($_GET['scope']))
                $scope=$_GET['scope'];
        if($scope!=""&&$scope!='d'&&$scope!='w'&&$scope!='m')
                $scope='y';
	$where="";

	if(isset($_GET['prefix'])){
		$prefix=$_GET['prefix'];
		$where="where user_id like ?";
	}else{	
		$where="where user_id not in (".$OJ_RANK_HIDDEN.") and defunct='N' ";
	}
        $rank = 0;
        if(isset( $_GET ['start'] ))
                $rank = intval ( $_GET ['start'] );

                if(isset($OJ_LANG)){
                        require_once("./lang/$OJ_LANG.php");
                }
                $page_size=5000;
                //$rank = intval ( $_GET ['start'] );
                if ($rank < 0)
                        $rank = 0;

                $sql = "SELECT `user_id`,`nick`,`solved`,`submit` FROM `users` $where ORDER BY `solved` DESC,submit,reg_time  LIMIT  " . strval ( $rank ) . ",$page_size";

                if($scope){
                        $s="";
                        switch ($scope){
                                case 'd':
                                        $s=date('Y').'-'.date('m').'-'.date('d');
                                        break;
                                case 'w':
                                        $monday=mktime(0, 0, 0, date("m"),date("d")-(date("w")+7)%8+1, date("Y"))                                                            ;
                                        //$monday->subDays(date('w'));
                                        $s= date('Y-m-d H:i', $monday);
                                        break;
                                case 'm':
                                        $s=date('Y').'-'.date('m').'-01';
                                        ;break;
                                default :
                                        $s=date('Y').'-01-01';
                        }
                        //echo $s."<-------------------------";
                        $sql="SELECT users.`user_id`,`nick`,s.`solved`,t.`submit` FROM `users`
                                        inner join
                                        (select count(distinct problem_id) solved ,user_id from solution 
						where in_date>str_to_date('$s','%Y-%m-%d') and result=4 
						group by user_id order by solved desc limit " . strval ( $rank ) . ",$page_size) s 
					on users.user_id=s.user_id
                                        inner join
                                        (select count( problem_id) submit ,user_id from solution 
						where in_date>str_to_date('$s','%Y-%m-%d') 
						group by user_id order by submit desc ) t 
					on users.user_id=t.user_id
                                ORDER BY s.`solved` DESC,t.submit,reg_time  LIMIT  0,50
                         ";
//                      echo $sql;
                }


      
		
		if(isset($_GET['prefix'])){
			if(is_valid_user_name($_GET['prefix'])){
				$result = pdo_query($sql,$_GET['prefix']."%");
			}else{
				 $view_errors =  "<h2>invalid user name prefix</h2>";
			         require("template/".$OJ_TEMPLATE."/error.php");
      				 exit(0);
			}
		}else{
                	$result = mysql_query_cache($sql) ;
		}
                if($result) $rows_cnt=count($result);
                else $rows_cnt=0;
                $view_rank=Array();
                $i=0;
				

                for ( $i=0;$i<$rows_cnt;$i++ ) {
					
                        $row=$result[$i];
                        
                        $rank ++;

                        $view_rank[$i][0]= $rank;
                        $view_rank[$i][1]=  $row['user_id'];
                        $view_rank[$i][2]=  htmlentities ( $row['nick'] ,ENT_QUOTES,"UTF-8");
                        $view_rank[$i][3]=  $row['solved'];
                        $view_rank[$i][4]=   $row['submit'];

                        if ($row['submit'] == 0)
                                $view_rank[$i][5]= "0.00%";
                        else
                                $view_rank[$i][5]= sprintf ( "%.02lf%%", 100 * $row['solved'] / $row['submit'] );

//                      $i++;
                }
				$exl_title = array($MSG_Number,$MSG_USER,$MSG_NICK,$MSG_SOVLED,$MSG_SUBMIT,$MSG_RATIO);
				$exl_value =[];
				//循环数据生成二维数组
				$exl_value=$view_rank;
				//foreach ($list as $k => $y){
					   // $exl_value[] = array($rank,$row['user_id'], htmlentities ( $row['nick'] ,ENT_QUOTES,"UTF-8"),$row['solved'],$row['submit'],$row['submit'] );
					// }
			  $file_name = $MSG_Number.'_'.time().'.csv';
			  $excel = new export_class();
			  $excel-> exportToExcel($file_name,$exl_title,$exl_value);
                $sql = "SELECT count(1) as `mycount` FROM `users`";
        //        $result = mysql_query ( $sql );
          // require("./include/memcache.php");
                $result = mysql_query_cache($sql);
                 $row=$result[0];
                $view_total=$row['mycount'];

class export_class
{

    /**
     * 导出excel csv格式
     * @param string $filename 文件名
     * @param array $tileArray 表头标题列表 格式一维数组 [标题1,标题2,标题3,标题n]
     * @param array $dataArray 数据列表数组 格式二维数组 [[1,2,3,n],[1,2,3,n]]
     */
    public function exportToExcel($filename='file', $tileArray=[], $dataArray=[]){
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=".$filename);
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        fputcsv($fp,$tileArray);
        $index = 0;
        foreach ($dataArray as $item) {
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$item);
        }

        ob_flush();
        flush();
        ob_end_clean();
    }
    
}


?>

