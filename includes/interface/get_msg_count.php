<?php
/**
 * An Interface of user get msg inbox unread msg counts
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage msg
 */
 
  session_start();
  require_once 'sns_fns.php';
  // not log in
  if(!check_valid_user()){
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  $type = $_REQUEST['type'];

  $conn = db_connect();
  $sel_query = "SELECT group_id as group_count FROM sns_msg_info 
  					WHERE receiver_id=$user_id AND receive_status=1 AND read_status=1 GROUP by group_id";
  if($type == 'inbox' || $type == 'inboxcount'){
	$sel_query = "SELECT group_id as group_count FROM sns_msg_info 
  					WHERE receiver_id=$user_id AND receive_status=1 GROUP by group_id";
  }
  if($type == 'sentmail'){
	$sel_query = "SELECT group_id as group_count FROM sns_msg_info 
  					WHERE sender_id=$user_id AND send_status=1 GROUP by group_id";
  }
  $sel_result = $conn->query($sel_query);
  if($sel_result){
  	$count = $sel_result->num_rows;
  	if(!$type) echo $count;
	elseif($type == 'inbox') {
		echo ( (int)($count/(InboxPageSize)) + (int)($count%(InboxPageSize)!=0));
	} elseif($type == 'inboxcount') {
		echo $count;
	} elseif($type == 'sentmail'){
		echo ( (int)($count/(SentMailPageSize)) + (int)($count%(SentMailPageSize)!=0));
	} else {
		echo 0;
	}
  } else {
  	echo 0;
  }
?>