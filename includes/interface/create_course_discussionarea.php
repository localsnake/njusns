<?php

/**
 * The interface to create course discussion area
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright 2011
 * @package interface
 * @subpackage course
 */
 session_start();
 require_once 'sns_fns.php';
 $user_id = $_SESSION['user_id'];
 
 $course_id = addslashes($_REQUEST['course_id']);
 $discussion_area_name = addslashes($_POST['course_discussion_name']);
 
 $conn = db_connect();
 if(!isSet($discussion_area_name) || $discussion_area_name == ''){
 	echo '讨论区名不能为空';
 	exit;
 }
 if(!is_course_teacher($user_id,$course_id,$conn)){
 	echo '您无权创建该课程的讨论区';
 	exit;
 }
 if(get_discussion_area_count($course_id,$conn) >= 1){
 	echo '非常抱歉,一门课程最多创建1个讨论区.';
 	exit;
 }
 
 if(create_course_discussion_area($course_id,$discussion_area_name,$conn)) {
 	echo 1;
 } else {
 	echo '数据库错误,请稍后再试';
 }
 
?>