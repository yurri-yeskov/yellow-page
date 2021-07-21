<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
 
file_put_contents('file.log', "\n" . 'ajaxfinal.php:   ' .date('Y-m-d h:i:s'). " : ". json_encode($_REQUEST) . "\n", FILE_APPEND);


ini_set('memory_limit', '1024M');
set_time_limit(30000);


 
include('../function.php');
$function = new Extend;
    // $db_name = 'accurateleads';
    // $db_host = '159.65.239.137';
    // $db_username = 'root';
    // $db_password = 'crome123';
$db_name = 'accurateleads';
$db_host = '159.65.239.137';
$db_username = 'yelp123';
$db_password = 'Yelp123!@#';

ini_set('display_errors', 'On');
// $db_name = 'scraper';
// $db_host = 'localhost';
// $db_username = 'root';
// $db_password = '';
  $url = $function->get_basic_url();

$db = mysqli_connect($db_host,$db_username,$db_password,$db_name) or die("could not connect to database");
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}


    $query = $_REQUEST['what'];
    $location = $_REQUEST['where'];
    $group_listing = $_REQUEST['group_listing'];


    $db->query("INSERT INTO yellow_home_jobrequest (search , location , status , group_listing , query_start_time) VALUES ('".$query."' , '".$location."' , 'IN_QUEUE','".$group_listing."' , '".date('Y-m-d h:i:s')."' ) ");



    
    $jobId = $db->insert_id;


    if($jobId != ''){

        // $url = 'http://64.227.35.142/yell/ajaxbackendFinal.php';
        $url = $url . 'yellowpages/ajaxbackendFinal.php';

        $curl = curl_init();                
        $post['what'] = $query;
        $post['where'] = $location;
        $post['jobId'] = $jobId;
        
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