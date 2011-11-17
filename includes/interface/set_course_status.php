<?php

/**
 * Interface to set user-course status such as Mount / UnMount 
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once('sns_fns.php');
  $user_id=$_SESSION['user_id'];
  $course_id = trim(addslashes($_POST['course_id']));
  $status = trim($_POST['status']);
  
  $conn = db_connect();
  $relation = get_user_course_relation($user_id,$course_id,$conn);
  if(!$relation) {
  	echo 'Course Not Exists';
  	exit;
  }
  if($status != 'M' && $status != 'U'){
  	echo 'Status Error';
  	exit;
  }
  if(set_user_course_status($user_id,$course_id,$status,$conn))
  	echo 1;
  else 
  	echo 0;
?>