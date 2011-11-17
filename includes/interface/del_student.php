<?php
/** An Interface of user delete course student
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  require_once 'sns_fns.php';
  session_start();
  if(!check_valid_user()){	// check if is the valid user
 	echo 0;
 	exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $course_id = addslashes(trim($_REQUEST['course_id']));
  $student_id = addslashes(trim($_REQUEST['student_id']));
  
  $conn = db_connect();
  // 既非课程老师 又 不是用户自己删除
  if(!is_course_teacher($user_id,$course_id,$conn) && $student_id != $user_id){;
  	echo 'Permission Denied';
  	exit;
  }
  if(del_student($course_id,$student_id,$conn)){
  	echo 1;
  }	else {
  	echo '抱歉,数据库错误,请稍后再试';
  }
?>