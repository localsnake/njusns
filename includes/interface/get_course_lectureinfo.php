<?php
/**
 * The interface to get course lecture info
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
 
 $course_id = addslashes($_REQUEST['course_id']);
  
 $conn = db_connect(); 
 $relation = get_user_course_relation($user_id,$course_id,$conn);
 if($relation != 'A' && $relation != 'M' && $relation!= 'T') {
 	echo '无权限查看该课程的资源信息';
 	exit;
 }
 
 $lecture_list = get_lecture_list($course_id,$conn);
 $course_lecture=array();
 for($i=0;$i<count($lecture_list);$i++) {
    $element = $lecture_list[$i];
	$lecture_id = $element->course_lecture_id;
	$course_lecture[$i]['course_lecture_id']=$lecture_id;
    $course_lecture[$i]['course_lecture_title']=$element->course_lecture_title;
    #$course_lecture[$i]['course_lecture_dir']=$file_dir."/".$element->course_lecture_dir;  
	$course_lecture[$i]['course_lecture_dir'] = 
	"includes/interface/download_file.php?course_id=$course_id&download_id=$lecture_id&kind=1";
    $course_lecture[$i]['visits'] = $element->visits;
	$course_lecture[$i]['create_time'] = date('Y-m-d H:i',strtotime($element->create_time));
	$course_lecture[$i]['update_time'] = date('Y-m-d H:i',strtotime($element->update_time)); 
 }
 echo json_encode($course_lecture);
?>