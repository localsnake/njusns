<?php
/**
 * An Interface of user edit event info
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage event
 */
 
  require_once 'sns_fns.php';
  session_start();
 
  if(!check_valid_user()){
	echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $event_id = addslashes(trim($_POST['event_id']));
  $event_begintime = addslashes(trim($_POST['event_begintime']));
  $event_endtime = addslashes(trim($_POST['event_endtime']));
  $event_type = addslashes(trim($_POST['event_type']));
  $event_content = addslashes(trim($_POST['event_content']));
  
  // change datetime format here
  $event_begintime = date("Y-m-d H:i:s",strtotime($event_begintime));
  $event_endtime = date("Y-m-d H:i:s",strtotime($event_endtime));
  
  $conn = db_connect();
  $result = edit_event($event_id,$user_id,$event_begintime,$event_endtime,
  							$event_type,$event_content,$conn);
  echo $result;
?>