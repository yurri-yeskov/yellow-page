<?php
error_reporting(E_ALL & ~E_WARNING  & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
include('/var/www/html/includes/db.php');
include('curl.php');
include('../function.php');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);
$error_proxy = false;
$function = new Extend;
ini_set('display_errors', 'On');

require_once("../ProxyModule/autoload.php");

use ProxyModule\ProxyModule;
$ProxyManager = ProxyModule::factory($con);





function get_scrape_data($new_url,$con,$query,$location,$pageNum,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username){
        global $ProxyManager;
        $curl = new Tasks;
        $dom = new DOMDocument();
        
        $con->query("UPDATE yellow_home_jobrequest Set running_time = '".date('Y-m-d h:i:s')."' , status = 'RUNNING' where id =  ".$jobId);
        $finalUrl = $new_url.'&page='.$pageNum;
        file_put_contents('test_file.log', "\n".date('Y-m-d h:i:s')." For ".$query." in ".$location." and Url is " . $finalUrl. "\n", FILE_APPEND);
        $page = "";
		$page = $ProxyManager->request_curl($finalUrl,"GET");
        @$dom->loadHTML($page);
        $finder = new DomXPath($dom);
        $pagination = $finder->query('//div[@class="pagination"]');

        foreach($pagination as $pagination){
            $arr = $pagination->getElementsByTagName('li');
            $total_array = array();
            foreach($arr as $item) { 
                $total_array[] = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
                                
            };
            $total_records =implode(' ',$total_array);
            $explode_Last_records = explode(" ", $total_records) ;
            $checkNext = end($explode_Last_records);
            $total_records = str_replace("Next","" , $total_records) ;
            $total_records = str_replace("Previous","" , $total_records) ;
            $total_records = trim($total_records) ;
            $explode_total_records = explode(" ", $total_records) ;
            $pageNums = end($explode_total_records);
            $con->query("Update yellow_home_jobrequest SET total_pages = '".$pageNums."',pages_scrape='0' WHERE group_listing = '$group_listing'");
        }
        
        if($pageNums != ''){
            for($i=1;$i<=$pageNums;$i++){
                $con->query("Update yellow_home_jobrequest SET total_pages = '".$pageNums."',pages_scrape='$i' WHERE group_listing = '$group_listing'");
                $request_url = $function->get_basic_url();
                $curl_url = $request_url . "yellowpages/finalCurlYellow.php";
                $curl = curl_init();                
                $post['currentPage'] = $i;
                $post['lastPage']    = $pageNums;
                $post['url']         = $new_url;
                $post['query']       = $query;
                $post['location']    = $location;
                $post['jobId']       = $jobId;
                $post['user_id']     = $logged_in_username;
                $post['group_listing']   = $group_listing;
                $post['googleJobId']     = $googleJobId;
                $post['cron_que_id']     = $cron_que_id;
                $post['this_user_id']     = $this_user_id;
                curl_setopt($curl, CURLOPT_URL, $curl_url);
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
            }

        } 
}

    $googleJobId = $_REQUEST['googleJobId'];
    $this_user_id = $_REQUEST['this_user_id'];
    $group_listing = $_REQUEST['group_listing'];
    $cron_que_id = $_REQUEST['cron_que_id'];
	$logged_in_username_detail = $function->get_user_detail_with_id($connn,$this_user_id);
    //$sql = "SELECT id , search , location FROM yellow_home_jobrequest where status = 'IN_QUEUE' limit 1";
    $sql = "SELECT id , search , location FROM yellow_home_jobrequest where group_listing = '$group_listing'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$what = $row['search'];
			$where = $row['location'];
			$pages = 200; //50;//20
			$jobId = $row['id'];
            $url = "https://www.yellowpages.com/search?search_terms=".urlencode($what)."&geo_location_terms=".urlencode($where);
            file_put_contents('test_file.log', "\n".date('Y-m-d h:i:s')." After Request:- ".json_encode($_REQUEST)."\n", FILE_APPEND);	
			$page = get_scrape_data($url,$con,$what,$where,$pages,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username_detail['username']);


		}

	}
die();





