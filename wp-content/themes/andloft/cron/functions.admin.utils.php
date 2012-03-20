<?php

require_once( '../../../../wp-load.php' );
require_once ABSPATH . WPINC . '/class-phpmailer.php';
require_once ABSPATH . WPINC . '/class-smtp.php';

function is_english($str) {
	if ( strlen($str) != strlen( utf8_decode($str) ) ) {
		return false;
	} else {
		return true;
	}
}

function send_email(
	$subject = 'No subject', 
	$body = 'No body'
	) {

	// SMTP email sent
	$phpmailer = new PHPMailer();

	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = 'Username';
	$phpmailer->Password = 'password';

	$phpmailer->IsSMTP(); // telling the class to use SMTP
	$phpmailer->Host       = "hostname.com"; // SMTP server
	$phpmailer->FromName   = $_POST[your_email];
	$phpmailer->Subject    = $subject;
	$phpmailer->Body       = $body;                      //HTML Body
	// $phpmailer->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$phpmailer->WordWrap   = 50; // set word wrap
	// $phpmailer->MsgHTML($_POST[your_message]);
	// $phpmailer->AddAddress('support@wordpressapi.com/files/', 'Wordpress support');
	//$phpmailer->AddAttachment("images/phpmailer.gif");             // attachment
	
	if ( !$phpmailer->Send() ) {
	 echo "Mailer Error: " . $phpmailer->ErrorInfo;
	} else {
	 echo "Message sent!";
	}

}

?>