<?php

/**
 * The interface to edit course info
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
  
  session_start();
  
  require_once('connectdb.php');
  require_once('sns_fns.php');
  $user_id=$_SESSION['user_id'];
  
  $course_id= addslashes(trim($_REQUEST['course_id']));
  $course_name = addslashes(trim($_REQUEST['course_name']));
  $course_term = addslashes(trim($_REQUEST['course_term']));
  $course_place= addslashes(trim($_REQUEST['course_place']));
  $course_book= addslashes(trim($_REQUEST['course_book']));
  $course_time= addslashes(trim($_REQUEST['course_time']));
  $course_stu_num= addslashes(trim($_REQUEST['course_stu_num']));
  $course_type= addslashes(trim($_REQUEST['course_type']));
  $course_introduction=addslashes(trim($_REQUEST['course_introduction']));
  $verify = addslashes(trim($_REQUEST['verify']));
  $password = addslashes(trim($_REQUEST['password']));
  $course_url = addslashes(trim($_REQUEST['course_url']));
  
  if(!$course_name || !$course_type || !$course_introduction || !$course_term){
	echo '请填写好必填区域';
	exit;
  }
  if($course_stu_num && !is_numeric($course_stu_num)){
  	echo '上课人数必须是数字';
  	exit;
  }
  if(!$course_url) {
  	$course_url = '#';
  }
  if($verify == 'C' && !$password){
	echo '使用密码认证时,认证密码区域不能为空.';
	exit;
  }
  if($verify == 'C'){
	if(strlen($password)<4 || strlen($password) > 20 ){
		echo '验证密码必须在4-20字之间:',$password;
		exit;
	}
  }
  if($verify != 'C') {	
	$password = "000000";
  }
  if(strlen($course_name)>40){
	echo '课程名不能大于40';
	exit;
  }
  if(strlen($course_place)>40){
	echo '上课地点区域字数不能大于40';
	exit;
  }
  if(strlen($course_book)>100){
	echo '上课用书字数不能超过100';
	exit;
  }
  if(strlen($course_url)>255){
	echo '站外课程主页链接字数不能超过255';
	exit;
  }
 
  
  $conn = db_connect();
  $conn->autocommit(false);
  if(!is_course_teacher($user_id,$course_id,$conn)){
	echo '抱歉，您无权修改该课程信息';
	exit;
  }
  
  if(!edit_course_info($course_id,$course_name,$course_term,$course_place,$course_book,$course_time,
  					$course_stu_num,$course_type,$course_url,$verify,$password,$course_introduction,$conn)){
  				echo '您没有做出修改';
				$conn->rollback();
				exit;
  }else {
	if($verify != 'Y'){	 //删除原有所有申请，否则将死锁不能加入课程
		if(ignore_total_course_apply($course_id,$conn)){
			$conn->commit();
			echo 1;
		}	else{
			echo '删除原有的课程申请失败，这将导致修改验证方式前的学生无法正常加入课程,请稍后重试';
			$conn->rollback();
			exit;
		}
	}	else {
		$conn->commit();
		echo 1;
	}
  }
?>