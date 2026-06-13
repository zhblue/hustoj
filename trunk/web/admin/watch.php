<?php
require_once("../include/db_info.inc.php");
require_once ("../include/my_func.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
        exit(1);
}

                    $memory=array();
                    exec("free -m|awk '{print $2 }'|tail -2",$memory);
                    $total_mem=$memory[0];
                    $total_swap=$memory[1];
                    $delay=2;
                    $logfile="/dev/shm/".basename(__FILE__,"php")."log";
                    $history=@file_get_contents($logfile);
                    if($history!=""){
                        $history=json_decode($history);
                        // FIX: Validate and clean corrupted history data
                        // Valid timestamps should be between 2020-01-01 and 2035-01-01 (in ms)
                        $min_ts = 1577836800000; // 2020-01-01
                        $max_ts = 2051222400000; // 2035-01-01
                        if(is_array($history) && count($history) > 0){
                            $valid_history = array_filter($history, function($sample) use ($min_ts, $max_ts) {
                                return isset($sample[4]) && is_numeric($sample[4]) 
                                       && $sample[4] >= $min_ts && $sample[4] <= $max_ts;
                            });
                            // If more than 80% of data is corrupted, clear the log
                            if(count($valid_history) < count($history) * 0.2 && count($history) > 10){
                                $history = array();
                                @file_put_contents($logfile, json_encode($history));
                            } else {
                                $history = array_values($valid_history);
                            }
                        }
                    }else{
                        $history=array();
                    }
                    $HL=count($history)-1;
                    if($HL>=0){
                            $info=$history[$HL];
                    }
if(function_exists('system')){
           date_default_timezone_set("PRC");
                if($HL<0||(isset($history[$HL][4]) && $history[$HL][4] <= (time()-$delay)*1000) ){
                        $info=array();
                           // system(" top -bn1 | grep \"Cpu\" | awk -F, '{print $4}' | awk '{print 100-$1}' ");
                            // FIX: Cross-distro compatible CPU extraction (Debian 13 & Ubuntu 24.04/26.04)
                            // Use sed to extract idle% then compute 100-idle%
                            // Works with both %Cpu(s): and other top output formats
                            exec("top -bn1 2>/dev/null | grep -E '^%Cpu' | head -1 | sed -n 's/.*, \([0-9.]*\) id.*/\1/p' | awk '{print 100-\$1}'",$info);
                            $info[count($info)-1]=floatval($info[count($info)-1]);
                            // Validate CPU value is reasonable (0-100)
                            if($info[count($info)-1] < 0 || $info[count($info)-1] > 100 || !is_numeric($info[count($info)-1])) {
                                $info[count($info)-1] = 0;
                            }
                            exec("free -m|grep Mem|awk '{print \$7 }'",$info);
                            $info[count($info)-1]=floatval($info[count($info)-1]);
                            exec("free -m|grep Swap|awk '{print \$3 }'",$info);
                            $info[count($info)-1]=floatval($info[count($info)-1]);
                            // FIX: Cross-distro TCP connections detection
                            // Try ss first (modern, works on Debian 13+), fall back to netstat
                            $tcp_out = @exec("ss -tan 2>/dev/null | grep -v '^State' | wc -l");
                            if(!is_numeric($tcp_out) || $tcp_out < 0 || $tcp_out > 1000000) {
                                // Fallback to netstat with robust awk parsing
                                $tcp_out = @exec("netstat -s 2>/dev/null | awk '/^[ ]*[0-9]+[ ]+connections established$/ {gsub(/[^0-9]/,\"\"); print; exit}'");
                            }
                            $info[] = floatval($tcp_out);
                            // Validate TCP value is reasonable (0-1000000)
                            if($info[count($info)-1] < 0 || $info[count($info)-1] > 1000000 || !is_numeric($info[count($info)-1])) {
                                $info[count($info)-1] = 0;
                            }

                            array_push($info,(time())*1000);

                            // FIX: More robust disk detection - find largest non-tmpfs partition
                            $disk_found = false;
                            $disk_output = @exec("df -m 2>/dev/null | awk 'NR>1 && \$6 !~ /shm|tmpfs/ {print \$3}' | sort -rn | head -1");
                            if(is_numeric($disk_output) && $disk_output > 0) {
                                $info[] = floatval($disk_output);
                                $disk_found = true;
                            }
                            if(!$disk_found) {
                                $info[] = 0;
                            }
                            // FIX: More robust NAS/network mount detection
                            $nas_found = false;
                            $nas_output = @exec("df -m 2>/dev/null | awk 'NR>1 && (\$6 ~ /\/mnt\/nfs/ || \$1 ~ /\/\/192\.|\/\/10\./) {print \$3}' | sort -rn | head -1");
                            if(is_numeric($nas_output) && $nas_output > 0) {
                                $info[] = floatval($nas_output);
                                $nas_found = true;
                            }
                            if(!$nas_found) {
                                $info[] = 0;
                            }
                            //echo json_encode($info);
                            array_push($history,$info);
                            while(count($history)>900) array_shift($history);
                            file_put_contents($logfile,json_encode($history));
                        //  echo json_encode($history);
                }
            $chart_cpu=array();
            $chart_mem=array();
            $chart_swap=array();
            $chart_tcp=array();
            foreach($history as $sample ){
                array_push($chart_cpu,array($sample[4],$sample[0]));
                array_push($chart_mem,array($sample[4],$sample[1]/$total_mem*100));
                array_push($chart_swap,array($sample[4],$total_swap?$sample[2]/$total_swap*100:0));
                array_push($chart_tcp,array($sample[4],$sample[3]/2));
            }
                if(isset($_GET['json'])){
                        echo json_encode(array($chart_cpu,$chart_mem,$chart_swap,$chart_tcp));
                        exit() ;
                }else{
                    $AG=array("_",".",":","i","!");
                    $HL=count($history)-1;
                    $cpu_ag="";
                    $al=9-strlen(strval($info[0]));
                    if($HL>$al){
                            for($i=$al;$i>=0;$i--){
                                $cpu_ag.= $AG[intval(log($history[$HL-$i][0],3))];
                            }
                    }
                ?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
</head><body bgcolor="white">
<?php if (!isset($_GET['notext'])){  ?>
<h1 style="color:#ffffff">
                    <span style="color:rgb(175,216,248)">CPU :<span id="cpu"><?php echo $info[0].$cpu_ag ?> </span></span><br>
                    <span style="color:rgb(237,194,64)">FREE:<span id="mem"><?php echo $info[1] ?>M</span></span><br>
                    <span style="color:rgb(77,167,77)">SWAP:<span id="swap"><?php echo $info[2] ?>M</span></span><br>
                    <span style="color:rgb(203,75,75)">TCP :<span id="tcp"><?php echo $info[3] ?></span>pairs </span> <br>
                    DISK:<span id="disk"><?php echo $info[5] ?></span>M  <br>
                    NAS :<span id="nas"><?php echo intval($info[6]/1024) ?></span>G  <br>
</h1>
<?php } ?>
        <script> //window.setTimeout("location.reload()",5000); </script>
        <script src="../template/bs3/jquery.min.js" > </script>
        <script src="/include/jquery.flot.js" > </script>
        <div id="panel" style="width:98%;height:180px" onclick='update()'>loading data ... </div>
        <script type="text/javascript">
                // FIX: Helper function to clamp values and filter extreme data points
                function clamp(v, min, max) { return v < min ? min : v > max ? max : v; }
                function filterExtremeY(data, maxVal) {
                    // Filter out data points with Y values > maxVal (likely timestamp or error)
                    return data.filter(p => p[1] <= maxVal);
                }
                function filterByTimeRange(data, minTime, maxTime) {
                    // Filter out data points with timestamps outside valid range
                    // minTime: 2020-01-01 in ms = 1577836800000
                    // maxTime: 2035-01-01 in ms = 2051222400000
                    return data.filter(p => p[0] >= minTime && p[0] <= maxTime);
                }
                
                function update(){
                        $.getJSON("<?php echo basename(__FILE__)?>?json",function(result){
                                let cpu=result[0];
                                let mem=result[1];
                                let swap=result[2];
                                let tcp=result[3];
                                
                                // FIX: Filter by valid timestamp range (2020-2035)
                                // This fixes X-axis showing 1975 due to corrupted log data
                                const MIN_TS = 1577836800000; // 2020-01-01
                                const MAX_TS = 2051222400000; // 2035-01-01
                                cpu = filterByTimeRange(cpu, MIN_TS, MAX_TS);
                                mem = filterByTimeRange(mem, MIN_TS, MAX_TS);
                                swap = filterByTimeRange(swap, MIN_TS, MAX_TS);
                                tcp = filterByTimeRange(tcp, MIN_TS, MAX_TS);
                                
                                // FIX: Filter extreme Y values after time filtering
                                // Max values: CPU 100%, Memory 100%, Swap 100%, TCP 100000
                                cpu = filterExtremeY(cpu, 100);
                                mem = filterExtremeY(mem, 100);
                                swap = filterExtremeY(swap, 100);
                                tcp = filterExtremeY(tcp, 100000);
                                
                                $.plot( $( "#panel" ), [ {
                                        label: "FREE:"+(<?php echo $memory[0]?>*mem[mem.length-1][1]/100).toFixed(0),data: mem,lines: {show: true}
                                }
                                ,{      label: "CPU:"+(cpu.length ? cpu[cpu.length-1][1] : 0)+"%",data: cpu,bars: {show: true}
                                }
                                ,{      label: "TCP:"+(tcp.length ? tcp[tcp.length-1][1]*2 : 0),data: tcp,lines: {show: true}
                                }
                                ,{      label: "SWAP:"+(<?php echo $memory[1]?>*swap[swap.length-1][1]/100).toFixed(0),data: swap,lines: {show: true}
                                }
                                ], {    grid: {
                                                backgroundColor: {
                                                        colors: [ "#aaaaee", "#ffffff" ]
                                                },
                                                color:"#00aa00",
                                                show:"true"
                                        },
                                        xaxis: {
                                                mode: "time" //,
                                        },
                                        // FIX: Add Y-axis range limits to prevent extreme values
                                        yaxis: {
                                                min: 0,
                                                max: 100
                                        },
                                        legend: {
                                                position: "nw"
                                        }
                                } );
                                // FIX: Clamp displayed values and handle empty arrays
                                $("#cpu").text(cpu.length ? clamp(cpu[cpu.length-1][1], 0, 100).toFixed(1)+"%" : "N/A");
                                $("#mem").text(mem.length ? clamp(mem[mem.length-1][1], 0, 100).toFixed(2)+"%" : "N/A");
                                $("#swap").text(swap.length ? clamp(swap[swap.length-1][1], 0, 100).toFixed(2)+"%" : "N/A");
                                $("#tcp").text(tcp.length ? clamp(tcp[tcp.length-1][1], 0, 1000000) : "N/A");
                        });

                }
                $(document).ready(function (){
                        window.setInterval("update()",<?php echo $delay*1000 ?>);
                });
        </script>

<?php                  }

   } ?>
<?php if (!isset($_GET['notext'])){  ?>
<br><br><br><br><br><br><br><br>
<?php } ?>
</body>
