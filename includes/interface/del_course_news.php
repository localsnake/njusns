<?php
/**
 * An Interface of del user freshmilk
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once 'sns_fns.php';
  if(!check_valid_user()){
  	echo 0;
  	exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $course_id = addslashes(trim($_REQUEST['course_id']));
  $news_id = addslashes(trim($_REQUEST['news_id']));
  
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn)){
  	echo '您无权删除';
  	exit;
  }
  
  if(del_course_news($news_id,$course_id,$conn)){
  	echo 1;		// echo 'Done';
  }	else{
  	echo 0;		// echo 'Fail';
  }
?>