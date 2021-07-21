<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");

file_put_contents('file.log', "\n" . 'ajaxbackendFinal.php:   ' .date('Y-m-d h:i:s'). " : ". json_encode($_REQUEST) . "\n", FILE_APPEND);

include('curl.php');
include('contacts_scraper/findEmailsApi.php');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);
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

$db = mysqli_connect($db_host,$db_username,$db_password,$db_name) or die("could not connect to database");
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}


function getContent($url, $try=1) {

file_put_contents('file.log', "\n" . 'ajaxbackendFinal.php:  in getContent ' .date('Y-m-d h:i:s'). " : ". json_encode($url) . " : ". json_encode($try) . "\n", FILE_APPEND);
    //$session = rand(1,100);
	$username = 'lum-customer-hl_f20865e0-zone-static';
	$password = 'v9f5trs93zzg';
	$port = 22225;
	$super_proxy = 'zproxy.lum-superproxy.io';
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
	//curl_setopt($curl, CURLOPT_URL, "http://api.scraperapi.com?api_key=e58c38f8a637e0a1ac36c54e012f1188&url=".$url);
	curl_setopt($curl, CURLOPT_PROXY, 'http://p.webshare.io:80');
    curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'bhzupnpr-rotate:im0bzuacqzyn');
	
    $result = curl_exec($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
    }
    curl_close($curl);
	
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




function get_scrape_data($new_url,$db,$query,$location,$pageNum,$jobId){

file_put_contents('file.log', "\n" . 'ajaxbackendFinal.php:  in get_scrape_data ' .date('Y-m-d h:i:s'). " : ". json_encode($new_url) . " :query ". json_encode($query)  . " :location ". json_encode($location)  . " :pageNum ". json_encode($pageNum) . " :jobId ". json_encode($jobId). "\n", FILE_APPEND);
    $curl = new Tasks;
    $dom = new DOMDocument();
    $db->query("UPDATE yellow_home_jobrequest Set running_time = '".date('Y-m-d h:i:s')."' , status = 'RUNNING' where id =  ".$jobId);


    for ($inc=1; $inc <= $pageNum ; $inc++) { 

        $finalUrl = $new_url.'&page='.$inc;
        $page = getContent($finalUrl);

        # Parse the HTML from Google.
        # The @ before the method call suppresses any warnings that
        # loadHTML might throw because of invalid HTML in the page.
        @$dom->loadHTML($page);

        $classname="info";
        $finder = new DomXPath($dom);
        $spaners = $finder->query('//div[@class="v-card"]');
        $className ="business-name";
        $add_listing ="locality";
        $cat_listings= 'categories';
        $address= 'street-address';
        $web_listing = 'track-visit-website';
        $total = 0;
      
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
            if (is_object($elementsBycontacts[0])) {
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


            $new_url_page  ="https://www.yellowpages.com".$visit_href;
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

              $remainingEmail = getAllEmailsApi($web);

              if(isset($remainingEmail) && $remainingEmail != ''){
                $email .= ', '.$remainingEmail;
              }

            }


            $ctitle = addslashes($title);
            $cAddress = addslashes($address);
            $cCategory = addslashes($categories);
            $cWebsite = addslashes($web);
            $cEmail = addslashes($email);

      
           if ($title !="") {
                  
                echo $sql_insert = "INSERT INTO yellow_home_listing (title , address, phone, category, website , email , job_request_id, facebook, linkedin, twitter, instagram, youtube ) VALUES ('".$ctitle."','".$cAddress."','".$phone."','".$cCategory."','".$cWebsite."','".$cEmail."' , '".$jobId."', '".$facebook."', '".$linkedin."' , '".$twitter."', '".$instagram."', '".$youtube."' );";
                $result_insert = $db->query($sql_insert);
            
            }
                        
        }

    }

    $db->query("UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
            
    return $jobId; 

}



        $sqlCount = "SELECT id , search , location FROM yellow_home_jobrequest where status = 'RUNNING'";
        $resultCount = $db->query($sqlCount);
        $queueCount = $resultCount->num_rows;

        if($queueCount < 3){

          $sql = "SELECT id , search , location FROM yellow_home_jobrequest where status = 'IN_QUEUE' limit 1";
            $result = $db->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {

                    $what = $row['search'];
                    $where = $row['location'];
                    $pages = 1;
                    $url = "https://www.yellowpages.com/search?search_terms=".urlencode($what)."&geo_location_terms=".urlencode($where);
                    $jobId = $row['id'];
                    $page = get_scrape_data($url,$db,$what,$where,$pages,$jobId);


                }

            }



        }
die();





