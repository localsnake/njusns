<?php
/** 
 * An Interface of user reset and email password 
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage auth
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
  
  $conn = db_connect();
  if(send_confirm_mail($conn,$email)){
  	echo "发送成功，请到 $email 查收您的确认信";
  }else{
  	echo '发送失败,稍后再试';
  }
  do_html_footer();
?>