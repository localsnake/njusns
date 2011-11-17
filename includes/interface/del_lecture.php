<?php

/**
 * The interface to delete course lecture
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 	session_start();
	require_once('sns_fns.php');
	require_once $include_path."SaeStorage.php";
	
	$user_id=$_SESSION['user_id'];
	$s = new SaeStorage();
	$conn = db_connect();
	
	$lecture_id= addslashes($_REQUEST['course_lecture_id']);
	$course_id = addslashes($_REQUEST['course_id']);
	
	
	if(!is_course_teacher($user_id,$course_id,$conn)){
	  echo '抱歉,您无权删除该课程的课件';
	  exit;
	}
	
	// 删除课程讲义文件
	$lecture_info = get_lecture_info($lecture_id,$conn);
	$lecture_dir = $lecture_info->course_lecture_dir; 
	$lecture_title = $lecture_info->course_lecture_title;
	if($s->fileExists($file_domain,$lecture_dir)){
		$s->delete($file_domain,$lecture_dir);
	}
	
	// 修改课程作业数据库,删除该表项
    if(!del_lecture($lecture_id,$conn)){
    	echo '删除数据库信息失败,请稍后再试';
    	exit;
    }
   
	// 发布课程动态
    $content="删除了课件:".$lecture_title;
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
	echo 1;		// Succ
?>