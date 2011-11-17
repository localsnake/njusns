<?php

  /**
   * Interface to del a msg by id
   * 
   * @author QiangRunwei <qiangrw@gmail.com>
   * @copyright LocalsNake Net League 2011
   * @package interface
   * @subpackage msg
   */

  session_start();
  require_once ('sns_fns.php');
  if (!check_valid_user()) {
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  $msg_id = addslashes(trim($_REQUEST['msg_id']));
  $type = trim($_REQUEST['type']); // inbox or sentmail
  if (!$msg_id) {
    echo 0;
    exit;
  }
  $conn = db_connect();
  if ($type == 'inbox') {
  	$update_query = "UPDATE sns_msg_info SET receive_status=0 WHERE msg_id=$msg_id AND receiver_id=$user_id";
    
  } elseif ($type == 'sentmail') {
    $update_query = "UPDATE sns_msg_info SET send_status=0 WHERE msg_id=$msg_id AND sender_id=$user_id";
  }
  $update_res = $conn->query($update_query);
  if($update_res) {
  	$del_res = $conn->query("DELETE FROM sns_msg_info 
	  WHERE send_status=0 AND receive_status=0 AND msg_id=$msg_id");
  } else {
  	echo 'update error:',$update_query;
  	exit;
  }
  if($del_res) echo 1;
  else echo 'del error';
?>