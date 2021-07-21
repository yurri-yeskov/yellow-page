<?php
session_start();
set_time_limit(0);
error_reporting(1);
ini_set('max_execution_time', 0);

$total_data = 0;


function getContent($url, $try=1) {
    
    $content = "";

    $username = 'lum-customer-weloveparmo-zone-zone4-session-rand';
    $password = 'x6sm7pedttc3';
    $port = 22225;
    $super_proxy = 'zproxy.lum-superproxy.io';
    
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
    
    curl_setopt($ch, CURLOPT_URL, $url);

    // curl_setopt($ch, CURLOPT_PROXY, "http://$super_proxy:$port");
    // curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$username:$password");
    


    $output = curl_exec($ch);
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close ($ch);
    
    if (($http_code != 200) && ($try < 3)) {    
        $try = $try + 1;    
        $content = getContent($url, $try);      
    } else {
        $content = $output;     
    }   

    return $content;    
}


$db_name = 'scraper';

$db_host = 'localhost';

$db_username = 'root';

$db_password = '';
    $db = new mysqli($db_host, $db_username, $db_password, $db_name);
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
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

function get_scrape_data($new_url,$cookie_content,$db){


$page = getContent($new_url, $cookie_content);
$curl = new Tasks;
$dom = new DOMDocument();

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
       $categories="";
       $address ="";
       $web ="";
       $email = "";
       $href="";
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

        if(!empty($website)) {

          $getsocial = $curl->find_data($website);
          $email = $getsocial['emails'];
          $facebook = $getsocial['facebook'];
          $twitter = $getsocial['twitter'];
          $youtube = $getsocial['youtube'];
          $linkedin = $getsocial['linkedin'];
          $instagram = $getsocial['instagram'];

          $remainingEmail = getAllEmailsApi($website);

          if(isset($remainingEmail) && $remainingEmail != ''){
            $email .= ', '.$remainingEmail;
          }

        }

  
       if ($title !="") {
              
            $sql_insert = "INSERT INTO `yellow_home_listing` (`title`,`address`, `phone`, `category`, `website`,`email`) VALUES ('".$title."','".$address."','".$phone."','".$categories."','".$web."','".$email."');";
            $result_insert = $db->query($sql_insert);
        
           
        }
               

    }
        

}




// if(!isset($_POST['page']) &&(isset($_POST['what']) || isset($_POST['where']))){
    
//         $cookie_content = '';

// $new_url = "https://www.yellowpages.com/search?search_terms=".$_POST['what']."&geo_location_terms=".$_POST['where']."";
//  $totals = get_scrape_data($new_url,$cookie_content,$db);
//  echo $totals;
// exit();
        
// }

if(isset($_REQUEST['what']) && isset($_REQUEST['where'])){
    
$cookie_content = '';

  // echo  $new_url = "https://www.yellowpages.com/search?search_terms=gyms&geo_location_terms=Los%20Angeles,%20CA";
$new_url = "https://www.yellowpages.com/search?search_terms=".urlencode($_REQUEST['what'])."&geo_location_terms=".urlencode($_REQUEST['where'])."&page=".$_REQUEST['page']."";

 $totals = get_scrape_data($new_url,$cookie_content,$db);
 echo $totals;
 exit();
 
        
}




