<?php
/**
 * An Interface of ignore course apply
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  require_once 'sns_fns.php';
  session_start();
  if(!check_valid_user()){
  	echo 0;
  	exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $apply_id = addslashes(trim($_REQUEST['apply_id']));
  $course_id = addslashes(trim($_REQUEST['course_id']));
  
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn)){			// Not the course teacher
  	echo 0;
  	exit;
  }
  if(ignore_course_apply($apply_id,$course_id,$conn)){
  	echo 1;	// echo 'Done';
  }	else{
  	echo 0;	// echo 'Fail';
  }
 
?>