<?php
error_reporting(0);
set_time_limit(0);

$msg = "";
$from		=	"";
$to		    =	"";
$to			=	base64_decode($_GET["to"]);	
$subject	=	"";
$html		=	"";

if($_POST){
$your_sendinblue_api_key = 'P65sKWJ97OhErXHj';
require_once 'Mailin.php';

	$from		=	trim(strip_tags(strtolower($_POST["from"])));
	$to		    =	trim(strip_tags(strtolower($_POST["to"])));
	$to_array   = 	explode(",",$to);	
	$subject	=	trim(strip_tags($_POST["subject"]));
	$html		=	$_POST["message"];
	foreach ($to_array as $email_to) {
		$email_to = trim($email_to);
		
		$mailin = new Mailin('https://api.sendinblue.com/v2.0',$your_sendinblue_api_key);
		
		
		$data = array( "to" => array($email_to => $email_to),
					"from" => array($from,$from),
					"subject" => $subject,
					"html" => $html
		);
		
		$result = $mailin->send_email($data);
		$msg = trim($result['message']);		


	}
	
	if($msg != "") $msg = "<h3>". $msg . "</h3>";

}
?>
<!DOCTYPE html>
<html class="w-mod-js w-mod-no-touch w-mod-video w-mod-no-ios wf-opensans-n3-active wf-opensans-i3-active wf-opensans-n4-active wf-opensans-i4-active wf-opensans-n6-active wf-opensans-i6-active wf-opensans-n7-active wf-opensans-i7-active wf-opensans-n8-active wf-opensans-i8-active wf-petitformalscript-n4-active wf-active"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Bulk Emails Sender</title>  
  <meta content="width=device-width, initial-scale=1" name="viewport"><meta content="Webflow" name="generator">
  <link href='https://fonts.googleapis.com/css?family=Aldrich' rel='stylesheet' type='text/css'>
  <link href="style.css" rel="stylesheet" type="text/css">
  </head>
  <body>
  <div class="bars-wrapper w-clearfix"><div class="bar"></div><div class="_2 bar"></div><div class="_3 bar"></div><div class="_4 bar"></div><div class="_5 bar"></div><div class="_6 bar"></div><div class="bar"></div></div>
  <div class="header-section"><div class="container w-container">
  <h1>Bulk Emails Sender</h1><p class="subtitle">Create & send emails, promote awesome products and strengthen important relationships</p>
  <?php if($msg != "") echo $msg; ?>
  <div class="sign-up-form w-form">
  <form class="w-clearfix" id="formSubmit" data-name="Signup Form" name="wf-form-signup-form" data-sticky="stickyID0" action="email.php" method="post">
	<input type="text" data-sticky="stickyID1" class="field w-input" name="to" value="<?php echo $to; ?>" placeholder="Enter email addresses (separated by commas)..." required>
	<input type="text" data-sticky="stickyID1" class="field w-input" name="from" value="<?php echo $from; ?>" placeholder="Enter your email address..." required>
	<input type="text" data-sticky="stickyID1" class="field w-input" name="subject" value="<?php echo $subject; ?>" placeholder="Enter email subject or title..." required>	
	<textarea class="field w-input" name="message" rows="10" placeholder="Enter email message (HTML or Text)..." required><?php echo $html; ?></textarea>

<input type="submit" value="Send Email" class="button w-button">	
</form>
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