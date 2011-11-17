<?php
/** 
 * An Interface of approve user apply , become friends if succ
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
  $from_id = addslashes(trim($_REQUEST['from_id']));
  
  $conn = db_connect();
  if(approve_user_apply($apply_id,$from_id,$user_id,$conn)){
  	$user_info = get_user_base_info($from_id,$conn);
  	$user_name = $user_info->user_name;
  	$content = "和 $user_name 成为了好友";
  	if(save_user_news($user_id,$content,$conn)) echo 1;
  	else echo 'save user news error:',$content;
  } else {
  	echo "Database Error. in approve user apply:$apply_id $from_id $user_id";
  }
?>