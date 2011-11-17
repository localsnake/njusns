<?php
/**
 * An Interface of user load event
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage event
 */

  session_start();			// start the session
  require_once 'sns_fns.php';
  
  if(!check_valid_user()){	
	echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $conn = db_connect();
  $user_event = get_event($user_id,$conn);
  $result = array();
  $event_count = 0;
  for($i=0;$i<count($user_event);$i++){
	$result[$event_count]['id'] = $user_event[$i]->event_id;
	$starttime = date('m/d/Y H:i',strtotime($user_event[$i]->event_begintime));
	$endtime= date('m/d/Y H:i',strtotime($user_event[$i]->event_endtime));
	$result[$event_count]['start_date'] = $starttime;
	$result[$event_count]['end_date'] = $endtime;
	$result[$event_count]['type'] = $user_event[$i]->event_type;
	$result[$event_count]['text'] = $user_event[$i]->event_content;
	$event_count++;
  }
  
  $array_course = get_course_list($user_id,$conn);
  for($i=0;$i<count($array_course);$i++) {
    $course = $array_course[$i];
    $course_id = $course->course_id;
    $user_course_relation = $course->relation;
	$course_info = get_course_info($course_id,$conn);
	$course_name = $course_info->course_name;
	$assignment_list = get_assignment_list($course_id,$conn);
	for($j=0;$j<count($assignment_list);$j++) {
		$assignment_info = $assignment_list[$j];
		$assignment_id = $assignment_info->course_assignment_id;
		$assignment_title = $assignment_info->course_assignment_title;
		$create_time = $assignment_info->create_time;
		$deadline = $assignment_info->course_assignment_deadline;  
		
		$event_type="AM-$assignment_id";			// assignment month plus id
		$event_content = "努力完成作业 $assignment_title ( $course_name )";
		$starttime = date('m/d/Y H:i',strtotime($create_time));
		$endtime= date('m/d/Y H:i',strtotime($deadline));
		/*$result[$event_count]['start_date'] = $starttime;
		$result[$event_count]['end_date'] = $endtime;
		$result[$event_count]['type'] = $event_type;
		$result[$event_count]['text'] = $event_content;
		$event_count++;*/
		
		$event_type="AW-$assignment_id";			// assignment week plus id
		$event_content = "作业:$assignment_title ($course_name)截止啦";
		$starttime = date('m/d/Y',strtotime($deadline));
		$starttime .= " 00:05";
		$endtime= date('m/d/Y H:i',strtotime($deadline));
		$result[$event_count]['start_date'] = $starttime;
		$result[$event_count]['end_date'] = $endtime;
		$result[$event_count]['type'] = $event_type;
		$result[$event_count]['text'] = $event_content;
		$event_count++;
	}
  }
  echo json_encode($result);
?>