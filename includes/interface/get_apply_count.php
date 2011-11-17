<?php
/**
 * An Interface of user get apply count
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
  
  $conn = db_connect();
  $count = get_user_apply_count($user_id,$conn) + get_course_apply_count($user_id,$conn);
  echo $count;
?>