<?php
/**
 * An Interface of user and course search
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage search
 */
 
  session_start();
  require_once 'sns_fns.php';
  if(!check_valid_user()){
 	echo 0;	// 您尚未登录，转至登录页面
 	exit;
  }
  $user_id = $_SESSION['user_id'];

  $type =  addslashes(trim($_REQUEST['type']));
  $keyword = addslashes(trim($_REQUEST['keyword']));
  
  if($keyword == ''){		//没有输入有效关键词
  	exit;
  }
  if(strlen($keyword) > 40){
  	echo '关键词不能超过40个';
  	exit;
  }
  
  $conn = db_connect();
  if($type == 'user'){
  	$user_array = search_user($keyword,$conn);
    if($user_array == null){
  	  echo '';
  	  exit;
	} else {
	  $result = array();
	  for($count = 0; $count<count($user_array); $count++){
	    $find_user_id = $user_array[$count]->user_id;
  		$find_user_photo = $user_array[$count]->user_photo;
		// assign result info
  		$result[$count]['user_id'] = $user_id;
  		$result[$count]['find_user_id'] = $find_user_id;
		$result[$count]['find_user_name'] = $user_array[$count]->user_name;
		//$result[$count]['find_user_email'] = $user_array[$count]->user_email;
		$result[$count]['find_user_hometown'] = $user_array[$count]->user_hometown;
		$result[$count]['find_user_photo'] = name_to_path_thumb($find_user_photo);
		$result[$count]['relation'] = 'N';
		if(user_apply_sent($user_id,$find_user_id,$conn)){
		  $result[$count]['relation'] = 'W';
		}
	    // They are not friends or himself , Add link to send user apply
	    if(is_friend($user_id,$find_user_id,$conn) && $find_user_id != $user_id) {	
	      $result[$count]['relation'] = 'Y';
	    }
		$user_detail = get_user_detail_info($find_user_id,$conn);
		$result[$count]['find_user_department'] = $user_detail->user_department;
		$result[$count]['find_user_major'] = $user_detail->user_major;
	  }
	  echo json_encode($result);
	}
  }	else if($type == 'course'){
  	$course_array = search_course($keyword,$conn);
  	if($course_array == null){
  		echo '';
  		exit;
  	}else{
  	  $result = array();
	  for($count = 0; $count<count($course_array); $count++){
	  	$row = $course_array[$count];
	  	$result[$count]['course_id'] = $row->course_id;
	  	$result[$count]['course_name'] = $row->course_name;
		$result[$count]['verify'] = $row->verify;
	  	$result[$count]['course_photo'] = name_to_path_thumb_course($row->course_photo);
	  	$result[$count]['user_id'] = $user_id;
	  	$relation = get_user_course_relation($user_id,$row->course_id,$conn);
	  	if($relation != null) 
		  	$result[$count]['relation'] = $relation;
	  	elseif(course_apply_sent($user_id,$result[$count]['course_id'],$conn)) 
		  	$result[$count]['relation'] = 'W';
	  	else
		  	$result[$count]['relation'] = 'N';
		$teacher_list = get_course_teacher_list($row->course_id,$conn);
		$teacher_id = 0;
		for($i=0;$i<count($teacher_list);$i++) {
			$teacher_id = $teacher_list[$i]->user_id;
			if(is_teacher($teacher_id,$conn)){
				$result[$count]['teacher_id'] = $teacher_id;
				break;
			}
		}
		$teacher_info = get_user_base_info($teacher_id,$conn);
		$result[$count]['teacher_name'] = $teacher_info->user_name;
  	  }
  	  echo json_encode($result);
  	}
  }
?>