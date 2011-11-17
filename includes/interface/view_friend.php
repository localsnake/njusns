<?php
/**
 * An Interface of user view friend
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
  $cur_page = addslashes($_REQUEST['cur_page']);
  
  $pagesize = FriendPageSize;		// the pagesize of every refresh
  
  if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
  }
  $offset = $pagesize * ($cur_page - 1);
  
  $conn = db_connect();
  $friend_list = get_friend_list($user_id,$offset,$pagesize,$conn);
  if($friend_list == null){
  	echo '';	//echo 'No friend Yet';
  	exit;
  }
  $result = array();
  for($i=0; $i<count($friend_list); $i++){
  	$friend_id = $friend_list[$i]->friend_id;
  	$friend_base_info = get_user_base_info($friend_id,$conn);
  	// assign friend info here 
  	$result[$i]['friend_id'] = $friend_id;
  	$result[$i]['friend_name'] = $friend_base_info->user_name;
	$result[$i]['friend_email'] =  $friend_base_info->user_email;
  	$result[$i]['friend_photo'] = name_to_path_thumb($friend_base_info->user_photo);
  	$result[$i]['relation'] = $friend_list[$i]->relation;
  	$friend_detail_info = get_user_detail_info($friend_id,$conn);
  	$result[$i]['friend_department'] = $friend_detail_info->user_department;
  	$result[$i]['friend_major'] = $friend_detail_info->user_major;
  }
  echo json_encode($result);
?>