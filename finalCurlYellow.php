<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
include('/var/www/html/includes/db.php');
include('/var/www/html/worker/libs/pixels.php');
include('/var/www/html/worker/libs/fbEmails.php');
include('curl.php');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);
$error_proxy = false;
ini_set('display_errors', 'On');
require_once("../ProxyModule/autoload.php");
use ProxyModule\ProxyModule;
$ProxyManager = ProxyModule::factory($con);
include('../function.php');
$function = new Extend;
$new_url    = $_POST['url'];
$pageNum    = $_POST['currentPage'];
$lastPage   = $_POST['lastPage'];
$query      = $_POST['query'];
$location   = $_POST['location'];
$user_id    = $_POST['user_id'];
$jobId      = $_POST['jobId'];
$group_listing   = $_POST['group_listing'];
$googleJobId     = $_POST['googleJobId'];
$cron_que_id     = $_POST['cron_que_id'];
$this_user_id     = $_POST['this_user_id'];
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



global $ProxyManager;
$finalUrl = $new_url.'&page='.$pageNum;
file_put_contents('test_file.log', "\n" . 'Inside Request ' .date('Y-m-d h:i:s'). " Request:- " .json_encode($_POST)." && URL :-  ".$finalUrl."\n", FILE_APPEND);
$curl = new Tasks;
$dom = new DOMDocument();
$page = "";
$page = $ProxyManager->request_curl($finalUrl,"GET");
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

    //file_put_contents('test_file.log', "\n" . 'JobID: '.$jobId.' & Page : ' .$pageNum. " & Query: ".$query." " .$location." & Title:- " .$title." \n", FILE_APPEND);

    $new_url_page  ="https://www.yellowpages.com".$visit_href;
    $cookie_content = "";
    $page2 = $ProxyManager->request_curl($new_url_page,"GET"); 
    

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


if($pageNum === $lastPage){
    //----------------------------------------------------------//
    $con->query("UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId);
    file_put_contents('test_file.log', "\n UPDATE yellow_home_jobrequest Set query_end_time = '".date('Y-m-d h:i:s')."' , status = 'COMPLETED' where id =  ".$jobId." \n", FILE_APPEND);
    //--------------------------------------------------------//
    //--------------------------------------------------------//
    /******************************Calling Complete Function ***************************************** */
    $function->completed_check_set($con,$function,$group_listing,$googleJobId,$cron_que_id,$user_id,$this_user_id);
    /******************************Calling Complete Function ***************************************** */     
}