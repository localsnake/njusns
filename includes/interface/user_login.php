<?php
/**
 * An Interface of user login
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
  $password = addslashes(trim($_POST['password']));	
  
  if($_SESSION['error_times'] > 8){
	$result['user_id'] = -1;
	$result['error'] = '尝试次数过多，请重启浏览器.';
	echo json_encode($result);
	exit;
  }
  $_SESSION['user_type'] = 'N';
  $conn = db_connect();
  $result = array();
  if($email!='' && $password!=''){
    // they have tried login
    $user_id = login($email,$password,$conn);
    $result['user_id'] = -1;
    if($user_id == -1){
    	$result['error'] = '您尚未激活，请到您的注册邮箱点击激活信';
    }	elseif($user_id == -2){
    	$result['error'] = '用户名或者密码错误';
    } elseif($user_id == 0){
    	$result['error'] = '数据库错误，请稍后再试';
    }else{
    	$_SESSION['user_id'] = $user_id;
    	$result['user_id'] = $user_id;
    	if(is_teacher($user_id,$conn)) {
    		$result['user_type'] = 'T';	// teacher
    		$_SESSION['user_type'] = 'T';
    	}	else {
    		$result['user_type'] = 'S';	//student
    		$_SESSION['user_type'] = 'S';
    	}
    }
  }else{
  	$result['error'] = '账户名和密码不能为空';
  }
  if($user_id <= 0){
    $_SESSION['error_times']  = $_SESSION['error_times'] + 1;
  }
  echo json_encode($result);		//	成功登陆,返回user id
?>