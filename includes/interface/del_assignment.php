<?php

/**
 * The interface to delete assignment by id, The assignment related event will also be 
 * deleted
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 * 
 */

	session_start();
	require_once('sns_fns.php');
	require_once $include_path."SaeStorage.php";

	$user_id=$_SESSION['user_id'];
	$s = new SaeStorage();


	$course_id = addslashes(trim($_REQUEST['course_id']));
	$assignment_id = addslashes(trim($_REQUEST['course_assignment_id']));

	$conn = db_connect();
	if(!is_course_teacher($user_id,$course_id,$conn)){
	  echo '抱歉，您无权删除该课程的作业';
	  exit;
	}
    
    // 删除课程作业物理文件
	$assignment_info = get_assignment_info($assignment_id,$conn);
	if(!$assignment_info){
		echo '该作业不存在';
		exit;
	}
	$assignment_dir = $assignment_info->course_assignment_dir;
	$assignment_title = $assignment_info->course_assignment_title;
	if(!$assignment_dir) {
		echo '作业文件不存在';
	}
    // 删除物理文件
    if($s->fileExists($file_domain,$assignment_dir)){
		$s->delete($file_domain,$assignment_dir);			
	}
	// 修改课程作业数据库,删除该表项
	if(!del_assignment($assignment_id,$conn)){				
    	echo '删除数据库信息失败,请稍后再试';
    	exit;
    }
	

    // 发布课程动态
    $content="删除了作业:".$assignment_title;
	if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		exit;
	}
    
    // 向课程的学生发布新鲜事
    $student_list = get_student_list($course_id,$conn);
    for($i=0; $i<count($student_list); $i++){
    	$to_id = $student_list[$i]->user_id;
    	$freshmilk_type='C';	//Course ID
        send_freshmilk($course_id,$to_id,$freshmilk_type,$content,$conn);	
   	}
	// 删除这些学生和该作业号相关的日程 Move to load event
	// del_assignment_event($assignment_id,$conn);
 	echo 1;				//"删除成功，您可以关闭该窗口了";
?>