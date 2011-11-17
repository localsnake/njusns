<?php

/**
 * The interface to upload course notice
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage course
 */

  session_start();
  require_once('sns_fns.php');
  
  $user_id = $_SESSION['user_id'];
  $course_id = addslashes(trim($_POST['course_id']));
  $content = addslashes(trim($_POST['notice_content']));
  
  if(!$content || !$course_id){
  	echo '通知内容不能为空';
  	exit;
  }
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn)){
  	echo '您无权发布该课程的通知';
  	exit;
  }
  $content = "发布通知:".$content;
  $datetime = date("Y-m-d H:i:s");
  
  if(!upload_course_news($course_id,$content,$conn)){
  	echo '修改课程动态数据库失败';
  	exit;
  }
  
  $student_list = get_course_related_people_list($course_id,$conn);
  for($i=0;$i<count($student_list);$i++){
  	$to_id = $student_list[$i]->user_id;
  	send_freshmilk($course_id,$to_id,'C',$content,$conn);
  }
  echo 1;			// Done
?>