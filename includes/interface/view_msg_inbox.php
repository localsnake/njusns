<?php
/**
 * An Interface of user view msg inbox
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
  
  // process page
  $cur_page = addslashes($_REQUEST['cur_page']);
  $pagesize = InboxPageSize;		// the pagesize of every refresh
  if(!isSet($cur_page) || $cur_page==null) $cur_page = 1;
  $offset = $pagesize * ($cur_page - 1);

  $conn = db_connect();
  $sel_query = "SELECT group_id FROM sns_msg_info 
  					WHERE receiver_id=$user_id AND receive_status=1 GROUP by group_id 
  					ORDER BY group_id DESC
					  LIMIT $offset,$pagesize";
  $sel_result = $conn->query($sel_query);
  $group_msgs = get_sel_object_array($sel_result);
  $result = array();
  $j = 0;
  for($i=0;$i<count($group_msgs);$i++) {
  	$group_id = $group_msgs[$i]->group_id;
  	$msg_info = get_msg_info_by_id($group_id,$conn);
  	//if($msg_info->receive_status) {	// not deleted
	if($group_id){
  		$result[$j]['group_id'] = $msg_info->group_id;				//信件组号
		$result[$j]['sender_id'] =  $msg_info->sender_id;
		$result[$j]['receiver_id'] =  $msg_info->receiver_id;
		$sender_info = get_user_base_info($result[$j]['sender_id'],$conn);
		$receiver_info = get_user_base_info($result[$j]['receiver_id'],$conn);
		$result[$j]['sender_name'] =  $sender_info->user_name;
		$result[$j]['receiver_name'] =  $receiver_info->user_name;
		
  		$result[$j]['title'] = $msg_info->title;
  		$result[$j]['content_preview'] = mb_substr(strip_tags($msg_info->content),0,20,'utf-8'); //内容预览
		$result[$j]['read_status'] = get_inbox_read_status($group_id,$user_id,$conn);
		$result[$j]['create_time'] = date('Y-m-d H:i',strtotime($msg_info->create_time)); 
  		$result[$j]['group_count'] = get_group_count($group_id,$conn);
  		$j++;
	}
  	//}
  }
  echo json_encode($result);
?>