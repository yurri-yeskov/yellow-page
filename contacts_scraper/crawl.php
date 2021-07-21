<?php
error_reporting(0);
set_time_limit(0);

include("functions.php");
$download = "";
$download = $_POST['download'];
if($download != ""){
$darray = unserialize(base64_decode($download));	
header('Content-type: application/vnd.ms-excel');
header('Content-disposition: attachment; filename="report.xls"');
foreach ($darray as $value) {
	echo $value;
}
exit();	
}
$urls	=	trim(strip_tags($_POST['urls']));
$urls   =   str_replace(array("\n\r","\r\n","\n","\r"),"<br />",$urls);
$urls_array = explode("<br />",$urls);
$urls_array = array_filter($urls_array);
?>
<!DOCTYPE html>
<html class="w-mod-js w-mod-no-touch w-mod-video w-mod-no-ios wf-opensans-n3-active wf-opensans-i3-active wf-opensans-n4-active wf-opensans-i4-active wf-opensans-n6-active wf-opensans-i6-active wf-opensans-n7-active wf-opensans-i7-active wf-opensans-n8-active wf-opensans-i8-active wf-petitformalscript-n4-active wf-active"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Website Emails Finder</title>  
  <meta content="width=device-width, initial-scale=1" name="viewport"><meta content="Webflow" name="generator">
  <link href='https://fonts.googleapis.com/css?family=Aldrich' rel='stylesheet' type='text/css'>
  <link href="style.css" rel="stylesheet" type="text/css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript">		

		function find_emails( d, e){

			$.ajax({
			        type: "POST",
			        url: "findemails.php?domain=" + d,
			        success: function(data){
						
						var ele = $("." + e);

			        	ele.find("td.td_emails").html(data);


			        }
		    });
		}

		
  </script> 
  </head>
  <body>
  <div class="bars-wrapper w-clearfix"><div class="bar"></div><div class="_2 bar"></div><div class="_3 bar"></div><div class="_4 bar"></div><div class="_5 bar"></div><div class="_6 bar"></div><div class="bar"></div></div>
  <div class="header-section"><div class="container w-container">
  <h1>Website Emails Finder</h1><p class="subtitle">Find email addresses in seconds and connect with people that matter for your business</p>
  <div class="sign-up-form w-form">
<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0" class="box">
  <tr>
    <th width="70%" align="left">URL</th>  
    <th width="30%" align="center">Emails (click on email to send)</th>
 </tr>	
<?php


		// Checking empty values
		if(empty($urls_array)){
			echo '<tr><td colspan="2" align="center"><i>Please enter Urls!</i></td></tr>';
		}else{

				$count = 1;

				
			foreach ($urls_array as $u => $url){	
				$url = trim($url);
				if(substr($url, 0, 4) == "http"){

					
					?>
					<tr class="tr_<?php echo $count; ?>">
					<td align="left"><?php echo $url; ?></td>
					<td align="center" class="td_emails">...<script type="text/javascript">$(document).ready(function(){ find_emails("<?php echo base64_encode($url); ?>", "tr_<?php echo $count; ?>"); });</script></td>														
					</tr>
					<?php			

					$count++;		
				}			
			}
		}
		

?>

</table>
</div></div></div><div class="social-section">
<div class="w-container">
<h2>Opportunities are one email away!</h2>
<div class="refer">
Use the buttons below to share.
</div><div class="share-wrapper">
<div class="a2a_kit a2a_kit_size_32 a2a_default_style" id="my_centered_buttons">
    <a class="a2a_button_facebook"></a>
    <a class="a2a_button_twitter"></a>
    <a class="a2a_button_google_plus"></a>
    <a class="a2a_button_pinterest"></a>
    <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
</div>

<script async src="https://static.addtoany.com/menu/page.js"></script>

</div></div></div><div class="footer-section"><div class="w-container"><div class="w-row"><div class="w-col w-col-6 w-col-small-6"><div class="copyright">&copy;. All right reserved.&nbsp;</div></div></div></div></div>
</body></html>