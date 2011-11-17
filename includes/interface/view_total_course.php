<?php
/**
 * An Interface to view total course of the school
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage course
 */
 
session_start();
require_once 'sns_fns.php';
if(!check_valid_user()){
	echo 0;	// 您尚未登录，转至登录页面
	exit;
}
$user_id = $_SESSION['user_id'];
$conn = db_connect();
$course_array = get_school_total_course($conn);
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
?>