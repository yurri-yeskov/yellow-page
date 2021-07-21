<?php

ini_set('display_errors', 0);
error_reporting(0);

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);

include("functions.php");

function getAllEmailsApi($lurl){

        $parsedurl  = parse_url($lurl);
		$target_domain = $parsedurl['scheme'].'://'.$parsedurl['host'].'/';					
		$domain_home = get_domain_name($lurl);
		$url_home = "http://".$domain_home;
			$final_emails = "";
			$final_emails_html = "";
			$emails_array = array();					
			$content	=	file_get_contents_curl($lurl);	
			
				$emails = parseTextForEmail($content);
				foreach ($emails['valid_email'] as $email){		
					$email = trim($email);
					preg_match('#^@(.*?)#is',$email,$check);
					if(!$check[0]){
						$emails_array[] = trim($email);							
					}
				}
				
			
			$clink = "";
			preg_match_all('/<a(.*?)<\/a>/is',$content,$page_links);
			foreach ($page_links[0] as $link){
				
				
				preg_match('/<a(.*?)href=("|\')(.*?)("|\')(.*?)>(.*?)<\/a>/is',$link,$anchor_link);
				preg_match('/contact/is',$anchor_link[3],$check_link);
				if($check_link[0]){
				if(substr($anchor_link[3], 0, 4) != "http" && substr($anchor_link[3], 0, 2) != "//"){
					
					$clink = $target_domain.ltrim($anchor_link[3],"/");
				
				}else{
					if(substr($anchor_link[3], 0, 2) == "//"){
						$clink = str_replace("//",$parsedurl['scheme']."://",$anchor_link[3]);
					}else{		
						$clink = $anchor_link[3];
					}			
				}
				break;
				}
			}

			
			if($clink != ""){
				
				
				$content = file_get_contents_curl($clink);
				
				$emails = parseTextForEmail($content);
				foreach ($emails['valid_email'] as $email){		
					$email = trim($email);
					preg_match('#^@(.*?)#is',$email,$check);
					if(!$check[0]){

						$emails_array[] = trim($email);
						
						
						
					}
				}
				
				
			}

			
		$emails_array = array_unique($emails_array);

		foreach ($emails_array as $single_email) {
			$final_emails .= trim($single_email).", ";
		}

		return $final_emails;
	
}
	



$lurl = "";
$lurl = trim($_GET['domain']);
if($lurl != ""){

 print_r(getAllEmailsApi($lurl));
		

}
			



?>
