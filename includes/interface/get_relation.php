<?php
/**
 * An Interface of user get user-user or user-course relationship
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
  $type = trim($_REQUEST['type']);
  $id = addslashes(trim($_REQUEST['id']));
  
  $conn = db_connect();
  if($type == 'user'){
  	$relation = 'N';
  	if(user_apply_sent($user_id,$id,$conn)){
		 $relation = 'W';
	}
    // They are not friends or himself , Add link to send user apply
    if(is_friend($user_id,$id,$conn) && $id != $user_id) {	
      $relation = 'Y';
    }
    echo $relation;
  } else if($type == 'course'){
  	$relation = get_user_course_relation($user_id,$id,$conn);
  	if($relation != null) {
	  	echo $relation;
  	}elseif(course_apply_sent($user_id,$id,$conn)){ 
	  	echo 'W';
  	}else {
	  	echo 'N';
  	}
  } else {
  	echo 'ERROR';
  }
?>