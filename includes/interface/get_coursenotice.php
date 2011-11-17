<?php

/**
 * The interface to get course news 
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
 	echo '您尚未登录';
 	exit;
 }
 $user_id = $_SESSION['user_id'];
 $lang = addslashes($_REQUEST['lang']);
 
 $course_id = addslashes($_REQUEST['course_id']);
 $cur_page = addslashes($_REQUEST['cur_page']);
 
 $conn = db_connect();
 if(!get_user_course_relation($user_id,$course_id,$conn)){
 	echo '您无权查看该页面';
 	exit;
 }
 $pagesize = CourseNewsPageSize;		// the pagesize of every refresh
  if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
 }
 $offset = $pagesize * ($cur_page - 1);
 
 $course_notice=array();
 $result = get_course_news($course_id,$offset,$pagesize,$conn);
 if(!$result) {
 	echo '';		//没有动态
 	exit;
 }
 $course_info = get_course_info($course_id,$conn);
 for($i=0;$i<count($result);$i++) {
    $element_notice=$result[$i];
    $course_notice[$i]['course_notice_id']= $element_notice->course_notice_id;
	$course_notice[$i]['course_name']    = $course_info->course_name;
	$course_notice[$i]['course_photo']   = name_to_path_thumb_course($course_info->course_photo);
    $course_notice[$i]['notice_time']    = $element_notice->notice_time;
    $content = $element_notice->notice_content;
    if($lang == 'en')	$content = translate_freshmilk($content);
    $course_notice[$i]['notice_content'] = $content;
 }
 echo json_encode($course_notice);
?>