<?php
        $OJ_CACHE_SHARE=false;
        $cache_time=30;
        require_once('./include/cache_start.php');
    	require_once('./include/db_info.inc.php');
	require_once("./include/my_func.inc.php");
        require_once('./include/setlang.php');
        require_once('./include/memcache.php');
        if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD){
		$now =  date('Y-m-d H:i', time());
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
	$param=array();
	if(isset($_GET['prefix'])){
		$prefix=$_GET['prefix'];
		$where="where user_id like ? and user_id not in (".$OJ_RANK_HIDDEN.") and defunct='N' ";
		array_push($param,$prefix."%");
	}else{	
		$where="where user_id not in (".$OJ_RANK_HIDDEN.") and defunct='N' ";
	}
	if(isset($_GET['group_name']) && !empty($_GET['group_name'])){
    		$group_name = $_GET['group_name'];
    		$where .= "and group_name like ? ";
		array_push($param,$group_name.'%');
	}
        $rank = 0;

                $sql = "SELECT count(1) as `mycount` FROM `users` where defunct='N' ";
                $result = mysql_query_cache($sql);
                $row=$result[0];
                $view_total=$row['mycount'];


        if(isset( $_GET ['start'] ))
                $rank = intval ( $_GET ['start'] );

                if(isset($OJ_LANG)){
                        require_once("./lang/$OJ_LANG.php");
                }
                $page_size=50;
                //$rank = intval ( $_GET ['start'] );
                if ($rank < 0)
                        $rank = 0;

                $sql = "SELECT `user_id`,`nick`,`solved`,`submit`,group_name,starred FROM `users` $where ORDER BY `solved` DESC,submit,reg_time  LIMIT  " . strval ( $rank ) . ",$page_size";

                if($scope){
                        $s="";
                        switch ($scope){
                                case 'd':
                                        $s=date('Y').'-'.date('m').'-'.date('d');
                                        break;
                                case 'w':
                                        $monday=mktime(0, 0, 0, date("m"),date("d")-(date("w")+6)%7, date("Y"));
                                        $s=date('Y-m-d',$monday);
                                        break;
                                case 'm':
                                        $s=date('Y').'-'.date('m').'-01';
                                        ;break;
                                default :
                                        $s=date('Y').'-01-01';
                        }
			$last_id=mysql_query_cache("select solution_id from solution where  in_date<str_to_date('$s','%Y-%m-%d') order by solution_id desc limit 1;");
			if(!empty($last_id)&&is_array( $last_id)) $last_id=$last_id[0][0];else $last_id=0;
			$view_total=mysql_query_cache("select count(distinct(user_id)) from solution where solution_id>$last_id")[0][0];
                        $sql="SELECT users.`user_id`,`nick`,s.`solved`,t.`submit`,group_name,starred FROM `users`
                                        inner join
                                        (select count(distinct (problem_id)) solved ,user_id from solution
                                               where solution_id>$last_id and user_id not in (".$OJ_RANK_HIDDEN.") and problem_id>0 and result=4
					       group by user_id order by solved desc limit " . strval ( $rank ) . ",$page_size) s
                                        on users.user_id=s.user_id
                                        inner join
                                        (select count( problem_id) submit ,user_id from solution
                                                where solution_id > $last_id
                                                group by user_id order by submit desc ) t
                                        on users.user_id=t.user_id
                                        and users.user_id not in (".$OJ_RANK_HIDDEN.") and defunct='N'
                                ORDER BY s.`solved` DESC,t.submit,reg_time  LIMIT  0,50
                         ";
//                      echo $sql;
                }


      
		
		if(!empty($param)){
			$result = pdo_query($sql,$param);
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
                         $view_rank[$i][1]=  "<a href='userinfo.php?user=" .htmlentities ( $row['user_id'],ENT_QUOTES,"UTF-8") . "'>" . $row['user_id'] . "</a>";
                        if(isset($row['starred']) && $row['starred'] >0 )   $view_rank[$i][1]="⭐".$view_rank[$i][1]."<span title='用同名账户给hustoj项目加星，可以点亮此星' >⭐</span>";     //github starred rewarding
			$view_rank[$i][2]=  "<div class=center>" . htmlentities ( $row['nick'] ,ENT_QUOTES,"UTF-8") ."</div>";
			$view_rank[$i][3]=  "<div class=center>" . htmlentities ( $row['group_name'] ,ENT_QUOTES,"UTF-8") ."</div>";
                        $view_rank[$i][4]=  "<div class=center><a href='status.php?user_id=" .htmlentities ( $row['user_id'],ENT_QUOTES,"UTF-8") ."&jresult=4'>" . $row['solved']."</a>"."</div>";
                        $view_rank[$i][5]=  "<div class=center><a href='status.php?user_id=" . htmlentities ($row['user_id'],ENT_QUOTES,"UTF-8") ."'>" . $row['submit'] . "</a>"."</div>";

                        if ($row['submit'] == 0)
                                $view_rank[$i][6]= "0.00%";
                        else
                                $view_rank[$i][6]= sprintf ( "%.02lf%%", 100 * $row['solved'] / $row['submit'] );

//                      $i++;
                }




/////////////////////////Template
require("template/".$OJ_TEMPLATE."/ranklist.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
        require_once('./include/cache_end.php');
?>
