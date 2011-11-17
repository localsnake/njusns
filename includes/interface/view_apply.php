<?php
/**
 * An Interface of user view apply
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

  $type = addslashes($_REQUEST['type']);
  $conn = db_connect();
  if($type == 'user'){			// process user apply
  	$user_apply = get_user_apply($user_id,$conn);
  	if($user_apply == null){
  		echo '';		// No apply;
  		exit;
  	}
  	// assign return info
  	$result = array();
  	for($count=0;$count<count($user_apply);$count++){
  		$from_user_id = $user_apply[$count]->from_id;
  		$from_user_info = get_user_base_info($from_user_id,$conn);
  		$from_user_name = $from_user_info->user_name;
  		$from_user_photo = name_to_path_thumb($from_user_info->user_photo);
  		
  		//assign the necessary info , $user_id may removed in the future
  		$result[$count]['user_id'] = $user_id;
  		$result[$count]['from_user_id'] = $from_user_id;		// for view user's page
  		$result[$count]['from_user_name'] = $from_user_name;
  		$result[$count]['from_user_photo'] = $from_user_photo;
  		$result[$count]['apply_id'] = $user_apply[$count]->apply_id;
		$result[$count]['apply_time'] = $user_apply[$count]->apply_time;
  	}
  	echo json_encode($result);
  	exit;
  }	else if($type = 'course'){			// process course apply
  	$course_apply = get_course_apply($user_id,$conn);
  	if($course_apply == null){
  		echo '';
  		exit;
  	}
  	// assign return info
  	$result = array();
  	for($count=0;$count<count($course_apply);$count++){
  		$from_user_id = $course_apply[$count]->from_id;
  		$from_user_info = get_user_base_info($from_user_id,$conn);
  		$from_user_name = $from_user_info->user_name;
  		$from_user_photo = name_to_path_thumb($from_user_info->user_photo);
  		$course_id = $course_apply[$count]->course_id;
  		$course_info = get_course_info($course_id,$conn);
  		
  		//assign the necessary info , $user_id may removed in the future
  		$result[$count]['user_id'] = $user_id;
  		$result[$count]['from_user_id'] = $from_user_id;		// for view user's page
  		$result[$count]['from_user_name'] = $from_user_name;
  		$result[$count]['from_user_photo'] = $from_user_photo;
  		$result[$count]['apply_id'] = $course_apply[$count]->apply_id;
  		$result[$count]['course_id'] = $course_id;
  		$result[$count]['course_name'] = $course_info->course_name;
		$result[$count]['apply_time'] = date('Y-m-d H:i',strtotime( $course_apply[$count]->apply_time ));
  	}
  	echo json_encode($result);
  }
?>