<?php
/**
 * An Interface of ignore user apply
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
  $apply_id = addslashes(trim($_REQUEST['apply_id']));
  
  $conn = db_connect();
  if(ignore_user_apply($apply_id,$user_id,$conn)){
  	echo 1;	// echo 'Done';
  }	else{
  	echo 0;	// echo 'Fail';
  }
 
?>