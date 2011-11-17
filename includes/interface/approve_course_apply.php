<?php
/** 
 * An Interface of approve course apply. if succ, let the user attend the course
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
  
  $apply_id = addslashes(trim($_REQUEST['apply_id']));
  $from_id = addslashes(trim($_REQUEST['from_id']));
  $course_id = addslashes(trim($_REQUEST['course_id']));
  
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn) ) {
  	echo 0;
  	exit;
  }
  if(approve_course_apply($apply_id,$from_id,$user_id,$course_id,$conn)){
  	echo 1;	//echo 'Done';
  } else {
  	echo 0;	//echo 'Fail';
  }
?>