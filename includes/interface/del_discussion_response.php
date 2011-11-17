<?php
/** An Interface of user delete course discussion area response by id
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  require_once 'sns_fns.php';
  session_start();
  if(!check_valid_user()){	// check if is the valid user
 	echo 0;
 	exit;
  }
  $user_id = $_SESSION['user_id'];
  $discussion_response_id = addslashes(trim($_POST['discussion_response_id']));
  
  $conn = db_connect();
  if(del_discussion_response($discussion_response_id,$user_id,$conn)){
  	echo 1;
  } else {
  	echo 'Permission Denied';
  }
?>