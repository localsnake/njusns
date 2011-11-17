<?php
/**
  * An Interface of user delete event
  * 
  * @author QiangRunwei <qiangrw@gmail.com>
  * @copyright LocalsNake Net League 2011
  * @package interface
  * @subpackage event
  */
  session_start();
  require_once 'sns_fns.php';
  
  if(!check_valid_user()){
 	echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];

  $event_id = addslashes(trim($_REQUEST['event_id']));
  
  $conn = db_connect();
  $result = del_event($event_id,$user_id,$conn);
  if($result) echo 1;
  else echo 0;
?>