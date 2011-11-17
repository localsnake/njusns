<?php
/**
 * An Interface of user view info
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  session_start();
  require_once('sns_fns.php');
  if(!check_valid_user()){
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  $conn = db_connect();
  $user_base = get_user_base_info($user_id,$conn);

  //用户基本信息
  $result['user_name'] = $user_base->user_name;
  $result['user_gender'] = $user_base->user_gender;
  $result['user_birthday'] = $user_base->user_birthday;
  $result['user_type'] = $user_base->user_type;
  $result['user_hometown'] = $user_base->user_hometown;
  $result['user_level'] = $user_base->user_level;
  $result['user_photo'] = name_to_path_thumb($user_base->user_photo);
  $result['user_photo_large'] = name_to_path_large($user_base->user_photo);
  $result['user_status'] = $user_base->user_status;
  
  $result['msg_unread_count'] = 0;
  $result['msg_total_count'] = 0;
  $result['apply_count'] = 0;
  
  $sel_query = "SELECT group_id as group_count FROM sns_msg_info 
  					WHERE receiver_id=$user_id AND receive_status=1 GROUP by group_id";
  $sel_result = $conn->query($sel_query);
  if($sel_result) $result['msg_total_count'] = $sel_result->num_rows;
  
  $sel_query = "SELECT group_id as group_count FROM sns_msg_info 
  					WHERE receiver_id=$user_id AND receive_status=1 AND read_status=1 GROUP by group_id";
  $sel_result = $conn->query($sel_query);
  if($sel_result) $result['msg_unread_count'] = $sel_result->num_rows;
  
  $result['apply_count']  = get_user_apply_count($user_id,$conn) + get_course_apply_count($user_id,$conn);
  echo json_encode($result);
?>