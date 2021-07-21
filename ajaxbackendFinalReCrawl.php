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

 
include('/var/www/html/function.php');
$function = new Extend;


ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);
$error_proxy = false;


function getContent($url, $referrer="", $cookie_content="", $try=1) {
    //$session = rand(1,100);
	$username = 'lum-customer-hl_f20865e0-zone-static';
	$password = 'v9f5trs93zzg';
	$port = 22225;
	$super_proxy = 'zproxy.lum-superproxy.io';
    try{

    	//replaced with scapper api

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);    
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 6);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
        //curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username:$password");
        // curl_setopt($curl, CURLOPT_URL, "http://api.scraperapi.com?api_key=e58c38f8a637e0a1ac36c54e012f1188&url=".$url);
    	// curl_setopt($curl, CURLOPT_URL, "http://api.scraperapi.com?api_key=88b994bfec0f5b7d9350f646e7939205&url=".$url);

    	curl_setopt($curl, CURLOPT_PROXY, 'http://p.webshare.io:80');
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'wntdqlvl-rotate:y2jtpysnibei');
    	
        $result = curl_exec($curl);

    
        
  //       $ch = curl_init();
    
		// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");

		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

		// curl_setopt($ch, CURLOPT_TIMEOUT, 120);

		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

		// curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__ .'/cookies.db');

		// curl_setopt($ch, CURLOPT_COOKIEFILE,  __DIR__ .'/cookies.db');
    
	 //    if ($cookie_content !== "") {
	 //        curl_setopt($ch, CURLOPT_COOKIE, $cookie_content);
	 //    }

	 //    curl_setopt($ch, CURLOPT_REFERER, $referrer);
	 //    curl_setopt($ch, CURLOPT_URL, "http://api.scraperapi.com?api_key=915aaa4636be4e44bb93e2d6272ef697&url=".$url);

	 //    $result = curl_exec($ch);

    	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            $error_proxy = true;
        }
        curl_close($curl);	 
    } catch (Exception $e){

        $error_proxy = true;
    }
 
    //-------------------------------------------//
    //-------------------------------------------//
    //-------------------------------------------//
        /*
     try{
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        
        curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__ .'/cookies.db');

        curl_setopt($ch, CURLOPT_COOKIEFILE,  __DIR__ .'/cookies.db');
        
        if ($cookie_content !== "") {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie_content);
        }

        curl_setopt($ch, CURLOPT_REFERER, $referrer);
        curl_setopt($ch, CURLOPT_URL, "http://api.scraperapi.com?api_key=88b994bfec0f5b7d9350f646e7939205&url=".$url);

        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
    } catch (Exception $e){

        file_put_contents('file.log', "\n" .  $e->getMessage() . "\n", FILE_APPEND);
    }
    */
    //-------------------------------------------//
    //-------------------------------------------//
    //-------------------------------------------//


    if (($http_code != 200) && ($try < 3)) {    
        $try = $try + 1;    
        $result = getContent($url,$try);      
    } else {
        $result = $result;     
    }  
	
	 

    return $result;
  
}


function getElementsByClassName($doms, $ClassName, $tagName=null) {
    if($tagName){
        $Elements = $doms->getElementsByTagName($tagName);
    }else {
        $Elements = $doms->getElementsByTagName("*");
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



function get_scrape_data($new_url,$con,$query,$location,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username,$start_page,$pageNum){

    $curl = new Tasks;
    $dom = new DOMDocument();
    $con->query("UPDATE yellow_home_jobrequest Set running_time = '".date('Y-m-d h:i:s')."' , status = 'RUNNING' where id =  ".$jobId);

    for ($inc=$start_page; $inc <= $pageNum ; $inc++) { 
			
			
        $finalUrl = $new_url.'&page='.$inc;
        $page = getContent($finalUrl);

        // print_r($page);

        # Parse the HTML from Google.
        # The @ before the method call suppresses any warnings that
        # loadHTML might throw because of invalid HTML in the page.
        @$dom->loadHTML($page);

        $classname="info";
        $finder = new DomXPath($dom);
        $spaners = $finder->query('//div[@class="v-card"]');
        $pagination = $finder->query('//div[@class="pagination"]');
        $className ="business-name";
        $add_listing ="locality";
        $cat_listings= 'categories';
        $address= 'street-address';
        $web_listing = 'track-visit-website';
        $total = 0;

        $con->query("Update yellow_home_jobrequest SET total_pages = '".$pageNum."',pages_scrape='$inc' WHERE group_listing = '$group_listing'");
      
        foreach ($spaners as $spaner ) {    
	
            $values = $spaner;
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

           $elementsByTitle = getElementsByClassName($values, $className, 'a');


            if (is_object($elementsByTitle[0])) {
              $title = $elementsByTitle[0]->nodeValue;
              $visit_href = $elementsByTitle[0]->getAttribute("href");
            }

            $elementsBycontacts = getElementsByClassName($values, 'phones phone primary', 'div');   
            if ($elementsBycontacts && isset($elementsBycontacts[0]) && is_object($elementsBycontacts[0])) {
               $phone = $elementsBycontacts[0]->nodeValue;
            }
           
            $elementsBycat = getElementsByClassName($values, 'categories', 'div');
            if (is_object($elementsBycat)) {
                $categories =$elementsBycat[0]->nodeValue;
            }
            
            $elementsByadr = getElementsByClassName($values, 'street-address', 'span');
            if (is_object($elementsByadr )) {
                 $address =$elementsByadr[0]->nodeValue;
            }
           
           
            $arr = $values->getElementsByTagName('a');
          
            foreach($arr as $item) { 
                $text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
                if ($text =="Website") {
                  $href =  $item->getAttribute("href");
                  $web = $href;
                }
            }
			
			//if(strpos('australia', strtolower($location)) !== false){
		//		$au = '.au';
		//	} else{
				$au = '';
		//	}

            $new_url_page  ="https://www.yellowpages.com".$au.$visit_href;
            $cookie_content = "";
            $page2 = getContent($new_url_page, $cookie_content);

            preg_match('/mailto:(.*?)"/', $page2, $sellingPrice);


            if ($sellingPrice && count($sellingPrice) >= 2){
              $email = $sellingPrice[1];
            }

            preg_match('/"address":"(.*?)"/', $page2, $addressnew);

            if($addressnew && count($addressnew) >= 2){
              $address = $addressnew[1];
            }
			
            if(!empty($web)) {

              $getsocial = $curl->find_data($web);
              $email .=  ", ".$getsocial['emails'];
              $facebook = $getsocial['facebook'];
              $twitter = $getsocial['twitter'];
              $youtube = $getsocial['youtube'];
              $linkedin = $getsocial['linkedin'];
              $instagram = $getsocial['instagram'];

              //$remainingEmail = getAllEmailsApi($web);

              //if(isset($remainingEmail) && $remainingEmail != ''){
                //$email .= ', '.$remainingEmail;
              //}

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
			
			//$social_email_data = '';
           if ($title !="") {
                  $check_exist = $con->query("SELECT * FROM yellow_home_listing WHERE phone LIKE '%$check_mobile%' AND email LIKE '%$cAddress%' AND job_request_id = '$jobId'");
				if(mysqli_num_rows($check_exist) < 1){
					
					$sql_insert = "INSERT INTO yellow_home_listing (title , address, phone, category, website , email , job_request_id, facebook, linkedin, twitter, instagram, youtube,socialEmail,pixel) VALUES ('".$ctitle."','".$cAddress."','".$phone."','".$cCategory."','".$cWebsite."','".$cEmail."' , '".$jobId."', '".$facebook."', '".$linkedin."' , '".$twitter."', '".$instagram."', '".$youtube."' ,'".$social_email_data."','".$pixel_data_load."')";
					$result_insert = $con->query($sql_insert);
			
				}
            }
                        
        }
        file_put_contents('file.log', "\n".date('Y-m-d h:i:s')." ReCrwal For ".$query." in ".$location." and For:- ".$inc." out of pageNum:-".$pageNum." \n", FILE_APPEND);
        
        //if($inc == $pageNum || $error_proxy){
        if($inc == $pageNum || $inc === $pageNum){
            //----------------------------------------------------------//
            $con->query("UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
            file_put_contents('file.log', "\n Recrwal UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId." \n", FILE_APPEND);
            //--------------------------------------------------------//
            //--------------------------------------------------------//
            /******************************Calling Complete Function ***************************************** */
            $function->completed_check_set($con,$function,$group_listing,$googleJobId,$cron_que_id);
            /******************************Calling Complete Function ***************************************** */  
            

        }

    }

    //$con->query("UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
            
    return $jobId; 

}


    $googleJobId    = $_REQUEST['googleJobId'];
    $this_user_id   = $_REQUEST['this_user_id'];
    $group_listing  = $_REQUEST['group_listing'];
    $cron_que_id    = $_REQUEST['cron_que_id'];
    $start_page     = $_REQUEST['start_page'];
    $npages         = $_REQUEST['npages'];
	$logged_in_username_detail = $function->get_user_detail_with_id($connn,$this_user_id);
    //$sql = "SELECT id , search , location FROM yellow_home_jobrequest where status = 'IN_QUEUE' limit 1";
    $sql = "SELECT id , search , location FROM yellow_home_jobrequest where group_listing = '$group_listing'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$what = $row['search'];
			$where = $row['location'];
			$au = '';
			$jobId = $row['id'];
            $url = "https://www.yellowpages.com$au/search?search_terms=".urlencode($what)."&geo_location_terms=".urlencode($where);
          //  file_put_contents('file.log',"\n URL: ".$url." \n",FILE_APPEND);		
			$page = get_scrape_data($url,$con,$what,$where,$jobId,$this_user_id,$group_listing,$cron_que_id,$googleJobId,$error_proxy,$function,$logged_in_username_detail['username'],$start_page,$npages);


		}

	}
die();





