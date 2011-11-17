<?php
/**
 * The interface of send course apply
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
  
  $from_id = addslashes(trim($_REQUEST['from_id']));
  $course_id = addslashes(trim($_REQUEST['course_id']));
  $apply_content = addslashes(trim($_REQUEST['apply_content']));
  
  // judge if the apply is legal
  if($user_id != $from_id){
  	echo 0;		//echo 'Error';
  	exit;
  }
  $conn = db_connect();
  if(!check_course_exists($course_id,$conn)){
  	echo 'Course not exists';
  	exit;
  }
  if(course_apply_sent($from_id,$course_id,$conn)){
  	echo "您已经申请过该课程，请等待对方确认";
  	exit;
  }
  if(get_user_course_relation($user_id,$course_id,$conn)){
  	echo '您已经加入该课程了';
  	exit;
  }
  $course_info = get_course_info($course_id,$conn);
  $verify = $course_info->verify;
  if($verify == 'N') {	// 不需要验证直接加为好友
    if(add_user_course_relation($user_id,$course_id,'A',$conn)) {
    	echo 1;	
    } else {
       echo 'database error';	
    }
    exit;
  }
  if($verify == 'C') {	// 不需要验证直接加为好友
	$password = addslashes(trim($_REQUEST['password']));
	$real_password = $course_info->password;
	if($password != $real_password){
		echo '密码错误,您是否打开了CapsLk键';
		exit;
	}
    if(add_user_course_relation($user_id,$course_id,'A',$conn)) {
    	echo 1;	
    } else {
       echo 'database error';	
    }
    exit;
  }
  // need verify
  if(send_course_apply($from_id,$course_id,$apply_content,$conn)){
  	echo 1;	//echo 'Succ';
  }else{
  	echo 0;	//echo 'Fail';
  }
  
?>