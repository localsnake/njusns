<?php
/**
 * An Interface of user logout
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage auth
 */
   
  session_start();
  require_once('sns_fns.php');	
  $user_id = $_SESSION['user_id'];
  $conn = db_connect();
  if(logout($user_id,$conn)){
  	unset($_SESSION['user_id']);
	$result_dest = session_destroy();
	echo 1;			//注销成功
  } else {
  	echo 0;			//注销失败
  }
?>