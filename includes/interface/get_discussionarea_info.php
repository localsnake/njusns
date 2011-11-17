<?php

/**
 * The interface get all the course's discussion area
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
 
 $conn=db_connect(); 
 $relation = get_user_course_relation($user_id,$course_id,$conn);
 if($relation != 'A' && $relation != 'M' && $relation != 'T') {	//既不参加也不管理课程
 	echo '无权限查看该课程的资源信息';
 	exit;
 }
 
 $result = get_course_discussion_area_list($course_id,$conn);
 $diccussion_area=array();
 for($i=0;$i<count($result);$i++) {
    $element=$result[$i];
    $diccussion_area[$i]['discussion_area_id']=$element->discussion_area_id;
    $diccussion_area[$i]['discussion_area_name']=$element->discussion_area_name;
 }
 echo json_encode($diccussion_area[0]);
?>