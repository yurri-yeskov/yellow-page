<?php

function get_proxyList(){
	$options = array(
		CURLOPT_RETURNTRANSFER 	=> true,     			// return web page
		CURLOPT_HEADER         	=> false,    			// don't return headers
		// CURLOPT_PROXY 			=> $random_proxy,     		// the HTTP proxy to tunnel request through
		//CURLOPT_HTTPPROXYTUNNEL => 1,    				// tunnel through a given HTTP proxy			
		CURLOPT_FOLLOWLOCATION 	=> true,     			// follow redirects
		CURLOPT_ENCODING       	=> "",       			// handle compressed
		CURLOPT_USERAGENT      	=> 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0', 	// who am i
		CURLOPT_AUTOREFERER    	=> true,     			// set referer on redirect
		CURLOPT_SSL_VERIFYPEER  => false,
		CURLOPT_CONNECTTIMEOUT 	=> 20,      			// timeout on connect
		CURLOPT_TIMEOUT        	=> 20,      			// timeout on response
		CURLOPT_MAXREDIRS      	=> 10,       			// stop after 10 redirects
		CURLOPT_HTTPHEADER => array(
			"Authorization: Token 4161492ace48c23b415222b982d2267bd4ac313d",
			"Cookie: _tid=3184a319-1ff6-40f8-9cde-cc197a70c6fc"
		),
	);

	$ch      = curl_init( "https://proxy.webshare.io/api/proxy/list/" );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	curl_close( $ch );

	return $content;
}

function file_get_contents_curl_no_proxy($url) {
	// $random_proxy = $proxy_lists[rand(0,count($proxy_lists)-1)];
	// $proxyauth = 'cdxsekpu-dest:2vp6ppo5mt4z';
	$options = array(
		CURLOPT_RETURNTRANSFER 	=> true,     			// return web page
		CURLOPT_HEADER         	=> false,    			// don't return headers
		// CURLOPT_PROXY 			=> $random_proxy,     		// the HTTP proxy to tunnel request through
		//CURLOPT_HTTPPROXYTUNNEL => 1,    				// tunnel through a given HTTP proxy
		// CURLOPT_PROXYUSERPWD	=> $proxyauth,	
		// CURLOPT_FOLLOWLOCATION 	=> true,     			// follow redirects
		CURLOPT_ENCODING       	=> "",       			// handle compressed
		CURLOPT_USERAGENT      	=> 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36', 	// who am i
		CURLOPT_AUTOREFERER    	=> true,     			// set referer on redirect
		CURLOPT_SSL_VERIFYPEER  => false,
		CURLOPT_CONNECTTIMEOUT 	=> 20,      			// timeout on connect
		CURLOPT_TIMEOUT        	=> 20,      			// timeout on response
		CURLOPT_MAXREDIRS      	=> 10,       			// stop after 10 redirects
		CURLOPT_HTTPHEADER => array(
			"Authorization: Token 4161492ace48c23b415222b982d2267bd4ac313d",
			"Cookie: _tid=3184a319-1ff6-40f8-9cde-cc197a70c6fc"
		),
	);

	$ch      = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	curl_close( $ch );
	
	return $content;
}
?>