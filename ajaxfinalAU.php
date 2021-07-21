<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
include('../includes/db.php');
include('../function.php');
$function = new Extend;

ini_set('memory_limit', '1024M');
set_time_limit(30000);


	file_put_contents('file_yellow.log', "\n" . 'Request ' .date('Y-m-d h:i:s'). " " .json_encode($_REQUEST) ."\n", FILE_APPEND);
    $query          = $_REQUEST['what'];
    $location       = $_REQUEST['where'];
    $group_listing  = $_REQUEST['group_listing'];
    $googleJobId    = $_REQUEST['googleJobId'];
    $this_user_id   = $_REQUEST['this_user_id'];
    $cron_que_id    = $_REQUEST['cron_que_id'];

    if($get_user_detail['isWaiting'] == '1'){
        $status = 'PAUSE';
    }else{
        $status = 'IN_QUEUE';
    }
	// Can't get url frm $_SERVER when script is called from cli.
         $url = $function->get_basic_url();
         $url = $url . "yellowpages/ajaxbackendFinalAU.php";
         // echo $url;exit;

    $con->query("INSERT INTO yellowau_home_jobrequest (search , location , status , group_listing , query_start_time,userid) VALUES ('".$query."' , '".$location."' , '$status','".$group_listing."' , '".date('Y-m-d h:i:s')."','$this_user_id' ) ");    
    $jobId = $con->insert_id;
    
    if($jobId != '' && $status != 'PAUSE'){
		
		  
         $curl = curl_init();                
         $post['what'] = $query;
         $post['where'] = $location;
         $post['jobId'] = $jobId;

         $post['group_listing'] = $group_listing; 
         $post['this_user_id'] = $this_user_id; 
         $post['googleJobId'] = $googleJobId; 
         $post['cron_que_id'] = $cron_que_id; 

         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt ($curl, CURLOPT_POST, TRUE);
         curl_setopt ($curl, CURLOPT_POSTFIELDS, $post); 
         curl_setopt($curl, CURLOPT_USERAGENT, 'api');
         curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
         curl_setopt($curl, CURLOPT_HEADER, 0);
         curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
         curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
         curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
         curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 
         curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
         curl_exec($curl);   
         curl_close($curl);  

        echo $jobId;

    }
 

die();
