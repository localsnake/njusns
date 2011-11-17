<?php
/**
 * An Interface of user reset and email password
 *  
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  session_start(); 
  
  require_once('sns_fns.php');	

  $email = addslashes(trim($_POST['email']));
  $vcode = addslashes(trim($_POST['vcode']));

  do_html_header('Result');
  // 防止脚本攻击, 通过安全测试发现
  if($_SESSION['vcode'] == null || $_SESSION['vcode']  == '') {
	echo '不要脚本攻击我';
	exit;
  }
  
  if( strcmp(strtolower($vcode) ,strtolower($_SESSION['vcode'])) != 0){
  	echo "验证码输入有误.";
  	exit;
  }
  
  if($email == 'njusnsadmin@nju.edu.cn'){
	echo '真是抱歉，这个地址是我们的管理员账号，请用别的账号注册吧';
	exit;
  }
  
  try{
  	$conn = db_connect();
  	$password = reset_password($email,$conn);
  	if($password != null && $password != ''){
  	  notify_password($email,$password);
	  echo "新密码已经发送到您的邮箱：$email, 请查收.";
  	}	else{
  		echo "新密码设置失败.";
  	}
  }	catch(Exception $ex){
  	echo "新密码设置失败.";
  }
  do_html_footer();
?>