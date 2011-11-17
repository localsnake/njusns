<?php
/**
 * the interface to save user's event
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage event
 */

  session_start();
  require_once 'sns_fns.php';
  
  if(!check_valid_user()){
 	exit;
  }
  $user_id = $_SESSION['user_id'];
  

  $event_begintime = addslashes(trim($_POST['event_begintime']));
  $event_endtime = addslashes(trim($_POST['event_endtime']));
  $event_type = addslashes(trim($_POST['event_type']));
  $event_content = addslashes(trim($_POST['event_content']));
  
  // change datetime format here
  $event_begintime = date("Y-m-d H:i:s",strtotime($event_begintime));
  $event_endtime = date("Y-m-d H:i:s",strtotime($event_endtime));
  
  $conn = db_connect();
  // 这里由负载测试发现错误，不能支持完全相同事件.
  if(get_event_id($user_id,$event_begintime,$event_endtime,$event_type,$event_content,$conn) != -1){
  	echo '暂时不支持创建完全相同的事件.';
  	exit;
  }
  $event_id  = save_event($user_id,$event_begintime,$event_endtime,$event_type,$event_content,$conn);
  echo $event_id;		// echo event id
?>