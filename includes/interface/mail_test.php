<?php
require_once ('Mail.php');
require_once ('Mail/mime.php');
  


send_html_email('qiangrw@gmail.com','hi','hi','hi');
send_html_email('qiangrw888888888888888888888888888888888888888888@gmddail.com','hi','hi','hi');

function send_html_email($email,$subject,$text,$html){
	//$file = 'filename.htm'; 	// you can add attachment file
	// set mime
	$crlf = "\n";
	$mime = new Mail_mime();
	$mime->setTXTBody($text);
	$mime->setHTMLBody($html, false);
	//$mime->addAttachment($file, 'text/html');
	$hdrs = array( 
	          'From'    => 'NJUSNS<njusns@126.com>', 
	          'To'      => $email, 
	          'Subject' => $subject
	          );
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	
	$conf['mail'] = array(
      'host'     => 'smtp.126.com',   	//smtp服务器地址，可以用ip地址或者域名
      'auth'     => true,               //true表示smtp服务器需要验证，false代码不需要
      'username' => 'njusns',           //用户名 
      'password' => 'sns@189njxydad'    //密码
	);
	
	$mail_object = &Mail::factory('smtp', $conf['mail']);
	$mail_res = $mail_object->send($email, $hdrs, $body);
	if( PEAR::isError($mail_res) ){                         //检测错误
		//echo '发送验证信失败，SMTP服务器错误，请稍后再试';
	    $error_msg = $mail_res->getMessage();
		echo 'ERROR:';
		echo $error_msg;
	    return false;
	}
	echo 'SUCC';
	return true;
 }
?>