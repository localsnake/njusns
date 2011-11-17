<?php
/**
 * The interface of send user apply
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
  	echo false;
  	exit;
  }
  $user_id = $_SESSION['user_id'];

  $from_id = addslashes(trim($_REQUEST['from_id']));
  $to_id = addslashes(trim($_REQUEST['to_id']));
  $apply_content = addslashes(trim($_REQUEST['apply_content']));
  
  // judge if the apply is legal
  if($user_id == $to_id || $user_id != $from_id){
  	echo 0;		//'Error';
  	exit;
  }
  $conn = db_connect();
  if(!check_user_exists($to_id,$conn)){
  	echo 0;		//'User not exists';
  	exit;
  }
  if(is_friend($user_id,$to_id,$conn)){
  	echo 0;		//不能重复添加
  	exit;
  }
  if(user_apply_sent($user_id,$to_id,$conn)){
  	echo 0;		//您已经发送过申请了
  	exit;
  }
  if(send_user_apply($from_id,$to_id,$apply_content,$conn)){
  	echo 1;		//'Succ';
  }else{
  	echo 0;		//'Fail';
  }
?>