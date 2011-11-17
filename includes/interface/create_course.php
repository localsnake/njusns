<?php

/**
 * Interface to create a new course, need to be teacher
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once('sns_fns.php');
  $user_id=$_SESSION['user_id'];
  
  $course_name= addslashes(trim($_REQUEST['course_name']));
  $course_term = addslashes(trim($_REQUEST['course_term']));
  $course_place= addslashes(trim($_REQUEST['course_place']));
  $course_book= addslashes(trim($_REQUEST['course_book']));
  $course_time= addslashes(trim($_REQUEST['course_time']));
  $course_stu_num= addslashes(trim($_REQUEST['course_stu_num']));
  $course_type= addslashes(trim($_REQUEST['course_type'])); 
  $course_introduction=addslashes(trim($_REQUEST['course_introduction']));
  $verify = addslashes(trim($_REQUEST['verify']));
  $password = addslashes(trim($_REQUEST['password']));
  
  $course_photo="default.jpg";
  
  if(!$course_name || !$course_type || !$course_introduction){
	echo '请填写好必填区域';
	exit;
  }
  if($course_stu_num && !is_numeric($course_stu_num)){
  	echo '上课人数必须是数字';
  	exit;
  }
  if($verify == 'C' && !$password){
	echo '使用密码认证时,认证码区域不能为空，且认证码必须在4-20字之间';
	exit;
  }
  if($verify == 'C'){
	if(strlen($password)<4 || strlen($password) > 20 ){
		echo '使用密码认证时,认证码区域不能为空，且认证码必须在4-20字之间';
		exit;
	}
  }
  if($verify != 'C') {	
	$password = "000000";
  }
  
  
  $conn=db_connect();
  $conn->autocommit(FALSE);
  if(!is_teacher($user_id,$conn)){
	echo '您无权创建课程';
	exit;
  }
  if(!check_time_format($course_time)) {
    echo "上课时间格式错误!";
    exit;
  }
  $course_id = create_new_course($course_name,$course_term,$course_time,$course_place,$course_stu_num,
			$course_book,$course_type,$course_photo,$verify,$password,$course_introduction,$conn);
  if($course_id < 0 ) {
    echo 'Course ID Error';
	$conn->rollback();
	exit;
  }	
  if(add_user_course_relation($user_id,$course_id,'M',$conn) ) {
	//以课程名命名的讨论区
  	$discussion_area_name = $course_name.'讨论区';
  	if(!create_course_discussion_area($course_id,$discussion_area_name,$conn)){
		echo '创建课程讨论区失败';
		$conn->rollback();
		exit;
	}
	save_user_news($user_id,"创建了课程: $course_name",$conn);
	$conn->commit();
    echo $course_id;
  } else {
	$conn->rollback();
  	echo '数据库错误,创建课程失败.';
  }
?>