<?php

error_reporting(E_ALL & ~E_WARNING  & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
include('/var/www/html/includes/db.php');
include('curl.php');
//include('contacts_scraper/findEmailsApi.php');
include('/var/www/html/worker/libs/pixels.php');
include('/var/www/html/worker/libs/fbEmails.php');

//include('functionAU.php'); 
include('/var/www/html/function.php');
$function = new Extend;


ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);
$error_proxy = false;


function getContent($url, $try){
	try{
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);    
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
        //curl_setopt($curl, CURLOPT_URL, "http://api.scraperapi.com/?api_key=999a584072f04b218cd8200946ad4b27&url=".urlencode($url));
    	curl_setopt($curl, CURLOPT_PROXY, 'http://p.webshare.io:80');
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'wntdqlvl-rotate:y2jtpysnibei');
    	
        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            $error_proxy = true;
        }
        curl_close($curl);	 
    } catch (Exception $e){

        $error_proxy = true;
    }

    if (($http_code != 200) && ($try < 10)) {    
        $try = $try + 1;    
        $result = getContent($url,$try);      
    } else {
        $result = $result;     
	}
	return $result;
}


function getElementsByClassName($dom, $ClassName, $tagName=null) {
    if($tagName){
        $Elements = $dom->getElementsByTagName($tagName);
    }else {
        $Elements = $dom->getElementsByTagName("*");
    }
    $Matched = array();
    for($i=0;$i<$Elements->length;$i++) {
        if($Elements->item($i)->attributes->getNamedItem('class')){
            if($Elements->item($i)->attributes->getNamedItem('class')->nodeValue == $ClassName) {
                $Matched[]=$Elements->item($i);
            }
        }
    }
    return $Matched;
}

function get_scrape_data($new_url,$con,$query,$location,$pageNum,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username){

    $con->query("UPDATE yellowau_home_jobrequest Set running_time = '".date('Y-m-d h:i:s')."' , status = 'RUNNING' where id = '$jobId'");
    $first_content = getContent($new_url, 0);
    $pokemon_doc = new DOMDocument('1.0', 'UTF-8');
	libxml_use_internal_errors(TRUE); //disable libxml errors
	$pokemon_doc->loadHTML($first_content);
    libxml_clear_errors(); //remove errors for yucky html
    $rootElement = $pokemon_doc->documentElement;
	// $content_node=$dom->getElementById("content_node");
	$totalElements = getElementsByClassName($rootElement, 'emphasise', 'span');
	$total = $totalElements[0]->textContent;
    $total = explode("Results",trim(strip_tags($total)));

    file_put_contents('fileau.log', "\n" . 'Job Id:-  '.$jobId. " &&  total: ". $total[0] ."\n", FILE_APPEND);
    $pageNum = 1;
    for ($inc=1; $inc <= trim($total[0]) ; $inc=$inc+35) { 
        file_put_contents('fileau.log', "\n" . 'Job Id:-  '.$jobId. " &&  total: ". $total[0] ."\n", FILE_APPEND);

		$main_url = "https://www.yellowpages.com.au/search/listings?clue=".$query."&locationClue=".$location."&pageNumber=".$pageNum."&referredBy=UNKNOWN&eventType=pagination";	
        $content = getContent($main_url, 0);
		$doc = new DOMDocument();
		libxml_use_internal_errors(TRUE); //disable libxml errors 
        $doc->loadHTML($content);
        $rootElements = $doc->documentElement;
		$searchItems = getElementsByClassName($rootElements, 'cell in-area-cell find-show-more-trial   middle-cell', 'div');
        // echo $searchItems->length; exit;
        $con->query("Update yellowau_home_jobrequest SET total_pages = '".trim($total[0])."',pages_scrape='$inc' WHERE group_listing = '$group_listing'");
        foreach ($searchItems as $searchItem) {    
	
            $title = "";
            $phone = "";
            $categories= "";
            $address = "";
            $web = "";
            $email = "";
            $href = "";
            $facebook = "";
            $twitter = "";
            $youtube = "";
            $linkedin = "";
            $instagram = "";

            $eles = getElementsByClassName($searchItem, 'listing-name', 'a');
			$title = $eles[0]->textContent;
			
			$eles = getElementsByClassName($searchItem, 'listing-heading', 'p');
			$address = $eles[0]->getElementsByTagName('a')->item(0)->textContent;
			
			$eles = getElementsByClassName($searchItem, 'click-to-call contact contact-preferred contact-phone ', 'a');
			if ($eles){
				$phonenumber = $eles[0]->getAttribute('href');
				$phone = explode(":",$phonenumber)[1];
			}
			
			$eles = getElementsByClassName($searchItem, 'contact contact-main contact-email ', 'a');
			if ($eles){
				$email = $eles[0]->getAttribute('data-email');
			}

			$eles = getElementsByClassName($searchItem, 'contact contact-main contact-url ', 'a');
			if ($eles){
				$web = $eles[0]->getAttribute('href');
			}
			
            if(!empty($web)) {
              $curl = new Tasks;
              $getsocial = $curl->find_data($web);
              $email .=  ", ".$getsocial['emails'];
              $facebook = $getsocial['facebook'];
              $twitter = $getsocial['twitter'];
              $youtube = $getsocial['youtube'];
              $linkedin = $getsocial['linkedin'];
              $instagram = $getsocial['instagram'];

            }
 

            $ctitle = addslashes($title);
            $cAddress = addslashes($address);
            $cCategory = addslashes($categories);
            $cWebsite = addslashes($web);
            $cEmail = addslashes($email);
		
			$social_email = new FbEmailScraper($web); 
            $social_email_data = $social_email->crawl();
			
			$pixels = new PixelScraper($web);
			$pixel_data = $pixels->crawl();
			$pixel_data_load = implode(',',$pixel_data);
			
			
			$check_mobile = str_replace('+','',$phone);
			$check_mobile = str_replace('(','',$check_mobile);
			$check_mobile = str_replace(')','',$check_mobile);
			$check_mobile = str_replace('-','',$check_mobile);
			$check_mobile = str_replace(' ','',$check_mobile);
			$check_isStop = $function->getQueIsStop($con,'gogle_home_jobrequest',$user_id,$jobId);
            if($check_isStop == '1'){
                break;
            }
			//$social_email_data = '';
           if ($title !="") {
             //     $check_exist = $con->query("SELECT * FROM yellowau_home_listing WHERE phone LIKE '%$check_mobile%' AND email LIKE '%$cAddress%' AND job_request_id = '$jobId'");
			//	if(mysqli_num_rows($check_exist) < 1){
					
					$sql_insert = "INSERT INTO yellowau_home_listing (title , address, phone, category, website , email , job_request_id, facebook, linkedin, twitter, instagram, youtube,socialEmail,pixel) VALUES ('".$ctitle."','".$cAddress."','".$phone."','".$cCategory."','".$cWebsite."','".$cEmail."' , '".$jobId."', '".$facebook."', '".$linkedin."' , '".$twitter."', '".$instagram."', '".$youtube."' ,'".$social_email_data."','".$pixel_data_load."')";
					$result_insert = $con->query($sql_insert);
			
			//	}
            } 
        }
        file_put_contents('fileau.log', "\n" . 'Job Id:-  '.$jobId.' && group listing:- '.$group_listing.'  For '. $inc . "  total: ". $total[0] ."\n", FILE_APPEND);
        $pageNum++;
        if($inc == ($total[0] - 35) || $inc === ($total[0] - 35)){
            $check_us = $function->getQueData($con,$cron_que_id);
            if($check_us == '2' || $check_us === '2'){
                $con->query("UPDATE gogle_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$googleJobId);
                $con->query("DELETE FROM cron_que where id ='$cron_que_id'");
                file_put_contents("fileau.log","\n Date: ".date('d-m-Y H;:i:s')." DELETE FROM cron_que where id ='$cron_que_id' \n",FILE_APPEND);
            }
            $con->query("UPDATE yellowau_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
        }
    }

    $check_us = $function->getQueData($con,$cron_que_id);
    if($check_us == '2' || $check_us === '2'){
        $con->query("UPDATE gogle_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$googleJobId);
        $con->query("DELETE FROM cron_que where id ='$cron_que_id'");
        file_put_contents("fileau.log","\n Date: ".date('d-m-Y H;:i:s')." DELETE FROM cron_que where id ='$cron_que_id' \n",FILE_APPEND);
    }
    $con->query("UPDATE yellowau_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
    
    return $jobId; 

}


    $googleJobId = $_REQUEST['googleJobId'];
    $this_user_id = $_REQUEST['this_user_id'];
    $group_listing = $_REQUEST['group_listing'];
    $cron_que_id = $_REQUEST['cron_que_id'];
	$logged_in_username_detail = $function->get_user_detail_with_id($connn,$this_user_id);
    //$sql = "SELECT id , search , location FROM yellow_home_jobrequest where status = 'IN_QUEUE' limit 1";
    $sql = "SELECT id , search , location FROM yellowau_home_jobrequest where group_listing = '$group_listing'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$what = $row['search'];
			$where = $row['location'];
			$pages = 20; //50;//20

			$jobId = $row['id'];
            $url = "https://www.yellowpages.com.au/search/listings?clue=".$what."&locationClue=".$where."&lat=&lon=";	
            file_put_contents('fileau.log',"\n URL: ".$url." \n",FILE_APPEND);
			$page = get_scrape_data($url,$con,$what,$where,$pages,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username_detail['username']);


		}

	}
die();
