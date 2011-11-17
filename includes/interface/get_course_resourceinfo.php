<?php

/**
 * the interface to get resource info
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
 
 $course_id=$_REQUEST['course_id'];
 
 $conn = db_connect();
 $relation = get_user_course_relation($user_id,$course_id,$conn);
 if($relation != 'A' && $relation != 'M' && $relation!= 'T') {	//既不参加也不管理课程
 	echo '无权限查看该课程的资源信息';
 	exit;
 }
 
 $resource_list = get_resource_list($course_id,$conn);
 $course_resource=array();
 for($i=0;$i<count($resource_list);$i++) {
    $element = $resource_list[$i];
	$resource_id = $element->course_resource_id;
	$course_resource[$i]['course_resource_id']=$resource_id;
    $course_resource[$i]['course_resource_title']=$element->course_resource_title;
	//$course_resource[$i]['course_resource_dir']=$file_dir."/".$element->course_resource_dir;
	if($element->course_resource_type == 'I')  {
		$course_resource[$i]['course_resource_dir']=
	"includes/interface/download_file.php?course_id=$course_id&download_id=$resource_id&kind=3";
	}else if($element->course_resource_type == 'O') {
		$course_resource[$i]['course_resource_dir']=$element->course_resource_url;	//外部资源链接
	}
	$course_resource[$i]['course_resource_type'] = $element->course_resource_type; 
	$course_resource[$i]['create_time'] = date('Y-m-d H:i',strtotime($element->create_time));
	$course_resource[$i]['update_time'] =  date('Y-m-d H:i',strtotime($element->update_time));
 }
 echo json_encode($course_resource);
?>