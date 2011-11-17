<?php

/**
 * The interface to delete course resource
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author  <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */ 

	session_start();
	require_once('sns_fns.php');
	require_once $include_path."SaeStorage.php";

	$user_id=$_SESSION['user_id'];
	
	$resource_id = addslashes($_REQUEST['course_resource_id']);
	$course_id = addslashes($_REQUEST['course_id']);
	
	$s = new SaeStorage();
	$conn = db_connect();

	if(!is_course_teacher($user_id,$course_id,$conn)){
	  echo 0;		//无权删除
	  exit;
	}
    
	// 删除课程讲义文件
	$resource_info = get_resource_info($resource_id,$conn);
	$resource_dir = $resource_info->course_resource_dir;
	$resource_title = $resource_info->course_resource_title; 
	
	// 删除物理文件
    if($s->fileExists($file_domain,$resource_dir)){
		$s->delete($file_domain,$resource_dir);
	}
	// 修改课程资源数据库,删除该表项
    if(!del_resource($resource_id,$conn)){
    	echo '删除数据库信息失败,请稍后再试';
    	exit;
    }
    
    
	// 发布课程动态
    $content="删除了资源:".$resource_title;
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