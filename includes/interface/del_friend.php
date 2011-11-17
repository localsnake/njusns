<?php
/**
 * An Interface of user delete friend
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage user
 */
 
  require_once 'sns_fns.php';
  session_start();
  if(!check_valid_user()){	// check if is the valid user
 	echo 0;
 	exit;
  }
  $user_id = $_SESSION['user_id'];
  $friend_id = addslashes(trim($_REQUEST['friend_id']));
  
  $conn = db_connect();
  if(!is_friend($user_id,$friend_id,$conn)){
  	echo 0;	// not friend
  	exit;
  }
  
  if(del_friend($user_id,$friend_id,$conn)){
  	echo 1;
  }	else {
  	echo 0;
  }
  
?>