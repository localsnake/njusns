<?php
/**
 * An Interface of user view msg by group
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage msg
 */
 
  session_start();
  require_once 'sns_fns.php';
  if(!check_valid_user()){
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  $group_id = addslashes(trim($_REQUEST['group_id']));
  $type = $_REQUEST['type'];
  if(!$group_id) {
  	echo 0;
  	exit;
  }
  
  $conn = db_connect();
  if($type == 'inbox') {
  	$sel_query = "SELECT * FROM sns_msg_info WHERE group_id=$group_id 
  				ORDER BY create_time ASC";
  } elseif($type == 'sentmail'){ 
  	$sel_query = "SELECT * FROM sns_msg_info WHERE group_id=$group_id 
  				ORDER BY create_time ASC";
  } else{
  	echo 'param error';
  	exit;
  }
  
  $sel_result = $conn->query($sel_query);
  $msg_group = get_sel_object_array($sel_result);
  $result = array();
  $j = 0;
  for($i=0;$i<count($msg_group);$i++){
  	$msg_info = $msg_group[$i];
  	$sender_id = $msg_info->sender_id;
  	$receiver_id = $msg_info->receiver_id;
	$receive_status = $msg_info->receive_status;
	$send_status = $msg_info->send_status;
	
  	if( ($sender_id == $user_id && $send_status) || ($receiver_id == $user_id && $receive_status) ) {
	  $result[$j]['msg_id'] = $msg_info->msg_id;
 	  $result[$j]['title'] = $msg_info->title;
 	  $result[$j]['content'] = $msg_info->content;
 	  $result[$j]['create_time'] = date('Y-m-d H:i',strtotime($msg_info->create_time)); 
 	  
 	  $result[$j]['sender_id'] =  $msg_info->sender_id;
	  $result[$j]['receiver_id'] =  $msg_info->receiver_id;
	  $sender_info = get_user_base_info($sender_id,$conn);
	  $receiver_info = get_user_base_info($receiver_id,$conn);
	  $result[$j]['sender_name'] =  $sender_info->user_name;
	  $result[$j]['receiver_name'] =  $receiver_info->user_name;
	  $result[$j]['sender_photo'] =  name_to_path_thumb($sender_info->user_photo);
	  $result[$j]['receiver_photo'] =  name_to_path_thumb($receiver_info->user_photo);
	  $j++;
  	}
  }
  
  // set msg read
  $update_query = "UPDATE sns_msg_info SET read_status=0 WHERE group_id=$group_id AND receiver_id=$user_id";
  $update_res = $conn->query($update_query);
  if(!$update_res) {
  	echo 'Database Error:',$update_query;
  	exit;
  }
  
  echo json_encode($result);
?>