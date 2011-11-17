<?php
  /** An Interface of user change password
    * 
	* @author	Runwei Qiang  <qiangrw@gmail.com>
	* @version	1.0
	* @copyright	LocalsNake Net League 2011
	* @package	interface
	* @subpackage user
	*/
  
  session_start();
  require_once 'sns_fns.php';
  if(!check_valid_user()){	
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  $old_password = addslashes(trim($_POST['old_password']));
  $new_password = addslashes(trim($_POST['new_password']));
  $new_password2 = addslashes(trim($_POST['new_password2']));
  
  if(!filled_out($_POST)){
  	echo "表格未填写完整.";
  	exit;
  }
  
  // check whether the new password is correct
  if(strcmp($new_password,$new_password2) != 0) {
    echo "两次密码输入不一致";
    exit;
  }
  
  // check password length is ok
  // ok if username truncates, but passwords will get
  // munged if they are too long.
  if (strlen($new_password)<6 || strlen($new_password) >20) {
    echo "密码必须在6-20位";
    exit;
  }
  
  
  $conn = db_connect();
  if(!check_password($user_id,$old_password,$conn)){
  	echo "原密码错误";
  	exit;
  }
  // submit change the password
  if(change_password($user_id,$old_password,$new_password,$conn)){
  	echo 1;			//"密码修改成功";
  }	else{
  	echo "数据库错误,请稍后再试";
  }
?>