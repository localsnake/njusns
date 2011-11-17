<?php

/**
 * Interface to get course ftp info
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once('sns_fns.php');
  if(!check_valid_user()){
  	echo 'Permission Denied';
  	exit;
  }
  $user_id=$_SESSION['user_id'];
  $course_id = trim(addslashes($_REQUEST['course_id']));
  
  $conn = db_connect();
  $relation = get_user_course_relation($user_id,$course_id,$conn);
  if(!$relation) {
  	echo 'Permission Denied';
  	exit;
  }
  $sel_query = "SELECT * FROM sns_course_ftp WHERE course_id = $course_id";
  $sel_results = $conn->query($sel_query);
  $result = array();
  if($sel_results->num_rows > 0) {	
  	$ftp_info = $sel_results->fetch_object();
  	$result['host'] = $ftp_info->host;
  	$result['user'] = $ftp_info->username;
  	$result['password'] = $ftp_info->password;
  	
  	$user_info = get_user_base_info($user_id,$conn);
  	$user_email = $user_info->user_email;
  	list($student_id,$server) = split('@',$user_email);
  	$result['student_id'] = $student_id;
  	echo json_encode($result);
  } else {
  	echo '';
  }

?>