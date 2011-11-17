<?php
/**
 * the interface to get course info by id
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
	session_start();
	require_once('sns_fns.php');
   
 	$course_id = addslashes($_REQUEST['course_id']);
    $user_id=$_SESSION['user_id'];
	
	$conn = db_connect();
	$element = get_course_info($course_id,$conn);
    if(!$element) {
   		echo 0;
   		exit;
    }
	$course_info=array();
	$course_info['course_name']=$element->course_name;
	$course_info['course_term']=$element->course_term;
	$course_info['course_time']=$element->course_time;
	$course_info['course_place']=$element->course_place;
	$course_info['course_stu_number']=$element->course_stu_number;
	$course_info['course_book']=$element->course_book;
	$course_info['course_type']=$element->course_type;
	$course_info['course_photo']=name_to_path_thumb_course($element->course_photo);
	$course_info['course_photo_large'] = name_to_path_large_course($element->course_photo);
	$course_info['course_introduction']=$element->course_introduction;
	$course_info['verify']=$element->verify;
	$course_info['course_url']=$element->course_url;
	
	if($course_info['verify'] == 'C'){			//进入课程需要验证密码
		if(is_course_teacher($user_id,$course_id,$conn)){
			$course_info['password'] = $element->password;
		}
	}
	
	$teacher_list = get_course_teacher_list($course_id,$conn);
	$ta_count = 0;
	for($i=0;$i<count($teacher_list);$i++) {
		$teacher_id = $teacher_list[$i]->user_id;
		$teacher_info = get_user_base_info($teacher_id,$conn);
		if(is_teacher($teacher_id,$conn)) {
			$course_info['teacher_id'] = $teacher_id;
			$course_info['teacher_name'] = $teacher_info->user_name;
		} 
		/*else {
			if($ta_count == 0){
				$course_info['ta0_id'] = $teacher_id;
				$course_info['ta0_name'] = $teacher_info->user_name;
			} elseif($ta_count == 1) {
				$course_info['ta1_id'] = $teacher_id;
				$course_info['ta1_name'] = $teacher_info->user_name;
			} elseif($ta_count == 2) {
				$course_info['ta2_id'] = $teacher_id;
				$course_info['ta2_name'] = $teacher_info->user_name;
			}
			$ta_count ++;	//最多有三个助教
		}*/
	}
	$ta_list = get_course_ta_list($course_id,$conn);
	for($i=0;$i<count($ta_list);$i++) {
		$teacher_id = $ta_list[$i]->user_id;
		$teacher_info = get_user_base_info($teacher_id,$conn);
		if($ta_count == 0){
			$course_info['ta0_id'] = $teacher_id;
			$course_info['ta0_name'] = $teacher_info->user_name;
			$course_info['ta0_email'] = $teacher_info->user_email;
		} elseif($ta_count == 1) {
			$course_info['ta1_id'] = $teacher_id;
			$course_info['ta1_name'] = $teacher_info->user_name;
			$course_info['ta1_email'] = $teacher_info->user_email;
		} elseif($ta_count == 2) {
			$course_info['ta2_id'] = $teacher_id;
			$course_info['ta2_name'] = $teacher_info->user_name;
			$course_info['ta2_email'] = $teacher_info->user_email;
		} elseif($ta_count == 3) {
			$course_info['ta3_id'] = $teacher_id;
			$course_info['ta3_name'] = $teacher_info->user_name;
			$course_info['ta3_email'] = $teacher_info->user_email;
		} elseif($ta_count == 4) {
			$course_info['ta4_id'] = $teacher_id;
			$course_info['ta4_name'] = $teacher_info->user_name;
			$course_info['ta4_email'] = $teacher_info->user_email;
		}
		$ta_count ++;	//最多有五个助教
	}
	echo json_encode($course_info);
?>
  