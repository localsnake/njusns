<?php

/**
 * Interface to set course ftp
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
  $host = trim(addslashes($_POST['host']));
  $user = trim(addslashes($_POST['user']));
  $password = trim(addslashes($_POST['password']));
  
  if(!$host) {
	echo '主机名不能为空';
  }
  
  
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn)){
  	echo 'Permission Denied!';
  	exit;
  }
  $sel_query = "SELECT * FROM sns_course_ftp WHERE course_id = $course_id";
  $sel_results = $conn->query($sel_query);
  if($sel_results->num_rows > 0) {	// edit
  	$set_query = "UPDATE sns_course_ftp SET host='$host', username='$user',password='$password'
	  				WHERE course_id=$course_id";
  } else {  // modify
  	$set_query = "INSERT INTO sns_course_ftp (course_id,host,username,password) VALUES
	  				($course_id,'$host','$user','$password')";
  }
  $conn->query($set_query);
  if($conn->affected_rows == 1) {
  	echo 1;
  } else {
  	echo 'No Change';
  }
?>