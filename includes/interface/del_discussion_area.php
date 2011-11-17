<?php
/** An Interface of user delete course discussion area
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
  $discussion_area_id = addslashes(trim($_REQUEST['discussion_area_id']));
  
  $conn = db_connect();
  if(!is_course_teacher($user_id,$course_id,$conn)){;
  	echo 'Permission Denied';
  	exit;
  }
  $count = get_discussion_area_release_count($discussion_area_id,$conn);
  if($count > 0) {
    echo '无权删除非空讨论区,目前讨论区还有',$count,'篇文章尚未删除';
    exit;
  }
  if(del_discussion_area($course_id,$discussion_area_id,$conn)){
  	echo 1;
  }	else {
  	echo '抱歉,数据库错误,请稍后再试';
  }
?>