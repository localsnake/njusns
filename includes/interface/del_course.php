<?php

/**
 * Interface to del a new course by id, need to be teacher
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once('sns_fns.php');
  $user_id=$_SESSION['user_id'];
  
  $course_id = addslashes(trim($_REQUEST['course_id']));
  
  if(!$course_id){
  	exit;
  }
  $conn=db_connect();
  if(is_course_teacher($user_id,$course_id,$conn) && is_teacher($user_id,$conn)){
  	if(del_course($course_id,$conn)){
  		echo 1;
  	} else {
  		echo 'Delete Error';
  	}
  } else {
  	echo 'Permission Denied';
  	exit;
  }
?>