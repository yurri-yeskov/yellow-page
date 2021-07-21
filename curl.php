<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300000);

class Tasks
{

    public function __construct()
    {

    }

    public function email_validation($str) { 
        
        if (strpos($str, 'png') !== false || strpos($str,'jpg') !== false) {
            return FALSE;
        }
        else {
            return (!preg_match( 
                "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str)) 
                        ? FALSE : TRUE; 
        }
    } 

    public function getEmails($content)
    {
        $emails = array();
        preg_match_all('/([\w+\.]*\w+@[\w+\.]*\w+[\w+\-\w+]*\.\w+)/is', $content, $result);
        foreach($result[1] as $email) {
            if($this->email_validation($email)) {
                $emails[] = $email;
            }
        }
        return $emails;
    }
                
    public function file_get_contents_curl($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_URL, $url);
		   // curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'bhzupnpr-rotate:im0bzuacqzyn');
           // curl_setopt($ch, CURLOPT_PROXY, 'http://p.webshare.io:80');
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
    }

    public function find_data($domain)
            {
                
                $final_emails      = "";
                $emails_array      = array();
                $content           = $this->file_get_contents_curl($domain);
                preg_match_all("#href=('|\")(.*?)('|\")#is", $content, $all);
                $facebook  = "";
                $gplus     = "";
                $youtube   = "";
                $twitter   = "";
                $linkedin  = "";
                $pinterest = "";
                $instagram = "";
                foreach ($all[2] as $link) {
                    
                    $check = preg_match("#(.*?)facebook.com(.*?)#is", $link, $fb);
                    if ($check > 0 && $fb[0] != "") {
                        
                        $facebook = $link;
                        if (substr($facebook, 0, 2) == "//")
                            $facebook = "http:" . $link;
                        
                    }
                    
                    $check = preg_match("#(.*?)youtube.com(.*?)#is", $link, $yt);
                    if ($check > 0 && $yt[0] != "") {
                        
                        $youtube = $link;
                        if (substr($youtube, 0, 2) == "//")
                            $youtube = "http:" . $link;
                        
                    }
                    
                    $check = preg_match("#(.*?)twitter.com(.*?)#is", $link, $tw);
                    if ($check > 0 && $tw[0] != "") {
                        
                        $twitter = $link;
                        if (substr($twitter, 0, 2) == "//")
                            $twitter = "http:" . $link;
                        
                    }
                    
                    $check = preg_match("#(.*?)linkedin.com(.*?)#is", $link, $li);
                    if ($check > 0 && $li[0] != "") {
                        
                        $linkedin = $link;
                        if (substr($linkedin, 0, 2) == "//")
                            $linkedin = "http:" . $link;
                        
                    }
                    
                    $check = preg_match("#(.*?)instagram.com(.*?)#is", $link, $ig);
                    if ($check > 0 && $ig[0] != "") {
                        $instagram = $link;
                        if (substr($instagram, 0, 2) == "//")
                            $instagram = "http:" . $link;
                        
                    }
                    
                }
                
                $emails       = $this->getEmails($content);
                if(!empty($emails)) {
                    $emails_array = array_unique($emails);
                    foreach ($emails_array as $single_email) {
                        $final_emails .= trim($single_email) . ", ";
                    }
                    $final_emails = trim($final_emails, ", ");
                }
                
                $result       = array(
                    'emails' => $final_emails,
                    'facebook' => $facebook,
                    'twitter' => $twitter,
                    'youtube' => $youtube,
                    'linkedin' => $linkedin,
                    'instagram' => $instagram
                );
                return $result;

    }    

}