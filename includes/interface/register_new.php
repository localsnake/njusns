<?php
/** An Interface of user registeration
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage auth
 */
  
  session_start();
  require_once('sns_fns.php');

  $email = trim(addslashes($_POST['email']));
  $password = trim(addslashes($_POST['password']));
  $password2 = trim(addslashes($_POST['password2']));
  $username = trim(addslashes($_POST['username']));
  $gender = trim(addslashes($_POST['gender']));
  $birthday = trim(addslashes($_POST['birthday']));
  $hometown = trim(addslashes($_POST['hometown']));
  
  $vcode = trim($_POST['vcode']);				//Verify Code
  $conn = db_connect();
  
  if(!filled_out($_POST)){
  	echo "表格未填写完整.";
  	exit;
  }
  
  // check the email
  if(!valid_email($email)){
    echo "邮箱格式错误.";
    exit;
  }
  // check the two passwords
  if($password != $password2){
  	echo "两次输入的密码不一致.";
  	exit;
  }
  if(!($gender == 'M' || $gender == 'F')){
  	echo '性别格式错误';
  	exit;
  }
  // 防止脚本攻击, 通过安全测试发现
  if($_SESSION['vcode'] == null || $_SESSION['vcode']  == '') {
	echo '不要脚本攻击我';
	exit;
  }
  if( strcmp(strtolower($vcode) ,strtolower($_SESSION['vcode'])) != 0){
  	echo "验证码输入有误.";
  	exit;
  }
  // check password length is ok
  if (strlen($password)<6 || strlen($password2) >20) {
    echo "密码必须在6-20位";
    exit;
  }
  if(register_user($email,$password,$username,$gender,$birthday,$hometown,$conn)) {
  	echo 1; //"注册成功,请到邮箱点击确认信确认注册";
  }	else {
 	echo "  注册失败"; 
  }
?>