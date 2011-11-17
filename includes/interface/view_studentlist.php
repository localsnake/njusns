<?php
/**
 * The interface to view student list
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */

 session_start();
 require_once('sns_fns.php');
 if(!check_valid_user()){	
    echo 0;
    exit;
  }
 $user_id = $_SESSION['user_id'];
 
 $course_id = addslashes(trim($_REQUEST['course_id']));
 $cur_page = addslashes(trim($_REQUEST['cur_page']));
 $pagesize = StudentListPageSize;		// the pagesize of every refresh
 
 
 if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
 }
 $offset = $pagesize * ($cur_page - 1);
 
 $conn = db_connect();
 $relation = get_user_course_relation($user_id,$course_id,$conn);
 if($relation != 'A' && $relation != 'M' && $relation!= 'T') {		//既不参加也不管理课程
 	echo '无权限查看该课程的资源信息';
 	exit;
 }

 $student_info_list = get_student_list_page($course_id,$offset,$pagesize,$conn);
 for($i=0;$i<count($student_info_list);$i++) {
    $user_id = $student_info_list[$i]->user_id;
    $user_info = get_user_base_info($user_id,$conn);
    $student_info[$i]['user_id'] = $user_id;
    $student_info[$i]['user_name']=$user_info->user_name;
    $student_info[$i]['user_email']=$user_info->user_email;
    $student_info[$i]['user_photo']=name_to_path_thumb($user_info->user_photo);
    $student_detail_info = get_user_detail_info($user_id,$conn);
    $student_info[$i]['user_department'] = $student_detail_info->user_department;
    $student_info[$i]['user_major'] = $student_detail_info->user_major;
 }
 echo json_encode($student_info);
?>