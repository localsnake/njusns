<?php

/**
 * The interface to get course assignment info
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
 session_start();
 require_once('sns_fns.php');
 
 $user_id = $_SESSION['user_id'];
 
 $course_id= addslashes($_REQUEST['course_id']);
 
 $conn = db_connect();
 $relation = get_user_course_relation($user_id,$course_id,$conn);
 if($relation != 'A' && $relation != 'M' && $relation!= 'T') {	//既不参加也不管理课程
 	echo '无权限查看该课程的资源信息';
 	exit;
 }
 
 $assignment_list = get_assignment_list($course_id,$conn);
 $course_assignment = array();
 for($i=0;$i<count($assignment_list);$i++) {
    $element = $assignment_list[$i];
	$assignment_id = $element->course_assignment_id;
	$course_assignment[$i]['course_assignment_id'] = $assignment_id;
    $course_assignment[$i]['course_assignment_title'] = $element->course_assignment_title;
    //$course_assignment[$i]['course_assignment_dir'] = $file_dir."/".$element->course_assignment_dir; 
	$course_assignment[$i]['course_assignment_dir'] =
	"includes/interface/download_file.php?course_id=$course_id&download_id=$assignment_id&kind=2";
    $course_assignment[$i]['course_assignment_deadline']=$element->course_assignment_deadline;  
	$course_assignment[$i]['create_time'] = date('Y-m-d H:i',strtotime($element->create_time));
	$course_assignment[$i]['update_time'] = date('Y-m-d H:i',strtotime($element->update_time));
	
 }
 echo json_encode($course_assignment);
?>