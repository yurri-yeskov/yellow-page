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
    $userid         = $_REQUEST['userid'];
 
    $get_user_detail = $function->get_user_detail_with_id($connn,$this_user_id);
    if($get_user_detail['isWaiting'] == '1'){
   // if($allow_yellow == '0'){
        $status = 'PAUSE';
    }else{
        $status = 'IN_QUEUE';
    }

         $url = $function->get_basic_url();
         $url = $url . "yellowpages/ajaxbackendFinal.php";
         // echo $url;exit;

    $con->query("INSERT INTO yellow_home_jobrequest (search , location , status , group_listing , query_start_time,userid) VALUES ('".$query."' , '".$location."' , '$status','".$group_listing."' , '".date('Y-m-d h:i:s')."','$userid' ) ");    
    $jobId = $con->insert_id;

    if (!preg_match("@^[a-zA-Z0-9%+-_]*$@", $location)) { 
        $location = urlencode($location);
    }
    if (!preg_match("@^[a-zA-Z0-9%+-_]*$@", $query)) { 
        $query = urlencode($query);
    }
   
    if($jobId != '' && $status != 'PAUSE'){
		
        file_put_contents('file_yellow.log', "\n" . 'Inside Request ' .date('Y-m-d h:i:s'). " URL:- " .$url ."?what=$query&where=$location&jobId=$jobId&group_listing=$group_listing&user_id=$userid&this_user_id=$this_user_id&googleJobId=$googleJobId&cron_que_id=$cron_que_id\n", FILE_APPEND);
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
         if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            file_put_contents('php-error.log', "\n YP ajaxFinal curl error & Group Lisiting :- $group_listing & Error:-" . json_encode($error_msg) . "\n", FILE_APPEND);
            sleep(5);
            curlIfFailed($function,$query,$location,$jobId,$group_listing,$userid,$this_user_id,$googleJobId,$cron_que_id);
        }
         curl_close($curl);  

        echo $jobId;

    }
 
    function curlIfFailed($function,$query,$location,$jobId,$group_listing,$userid,$this_user_id,$googleJobId,$cron_que_id){
        $url = 'http://159.65.239.137/';
        $url = $url . "yellowpages/ajaxbackendFinal.php";

        if (!preg_match("@^[a-zA-Z0-9%+-_]*$@", $location)) { 
            $location = urlencode($location);
        }
        if (!preg_match("@^[a-zA-Z0-9%+-_]*$@", $query)) { 
            $query = urlencode($query);
        }

        file_put_contents('file_yellow.log', "\n" . 'Recurl ' .date('Y-m-d h:i:s'). " URL:- " .$url ."?what=$query&where=$location&jobId=$jobId&group_listing=$group_listing&user_id=$userid&this_user_id=$this_user_id&googleJobId=$googleJobId&cron_que_id=$cron_que_id\n", FILE_APPEND);
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
         curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
         curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
         curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
         curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 
         curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
         curl_exec($curl);   
         if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            file_put_contents('php-error.log', "\n Rcurl YP ajaxFinal curl error & Group Lisiting :- $group_listing & Error:-" . json_encode($error_msg) . "\n", FILE_APPEND);
            //sleep(5);
            //curlIfFailed($function,$query,$location,$jobId,$group_listing,$user_id,$this_user_id,$googleJobId,$cron_que_id);
        }
         curl_close($curl);  

        echo $jobId;
    }

die();