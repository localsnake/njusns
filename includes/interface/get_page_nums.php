<?php
/**
 * An Interface of getting page nums, 
 * including freshmilk,user news, course news, user friend
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  session_start();
  require_once 'sns_fns.php';
  
  if(!check_valid_user()){
  	echo 0;
  	exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $conn = db_connect();
  $type = $_REQUEST['type'];

  if($type == 'freshmilk'){
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_freshmilk WHERE to_id=$user_id AND freshmilk_type = 'U'");
  	$page = ((int)($conn->affected_rows/(FreshmilkPageSize)) + 
	  			(int)($conn->affected_rows%(FreshmilkPageSize)!=0) );
  }elseif($type == 'coursefeed'){
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_freshmilk WHERE to_id=$user_id AND freshmilk_type = 'C'");
  	$page = ((int)($conn->affected_rows/(FreshmilkPageSize)) + 
	  			(int)($conn->affected_rows%(FreshmilkPageSize)!=0) );
  }	elseif($type == 'user_news'){
  	$news_user_id = addslashes($_REQUEST['user_id']);
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_user_news WHERE user_id=$news_user_id");
  	$page =  ((int)($conn->affected_rows/(UserNewsPageSize)) + 
	  	(int)($conn->affected_rows%(UserNewsPageSize)!=0));
  }	elseif($type == 'course_news'){
  	$course_id = addslashes($_REQUEST['course_id']);
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_course_notice WHERE course_id=$course_id");
  	$page =  ((int)($conn->affected_rows/(CourseNewsPageSize)) + 
	  	(int)($conn->affected_rows%(CourseNewsPageSize)!=0));
  } elseif($type == 'user_friend'){
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_user_friend WHERE user_id=$user_id");
  	$page =  ((int)($conn->affected_rows/(FriendPageSize)) + 
	  	(int)($conn->affected_rows%(FriendPageSize)!=0));
  } elseif($type == 'course_student'){
  	$course_id = $_REQUEST['course_id'];
  	$countResult = 
	  	$conn->query("SELECT * FROM sns_user_course 
		  WHERE course_id=$course_id AND relation='A' ");
  	$page =  ((int)($conn->affected_rows/(StudentListPageSize)) + (int)($conn->affected_rows%(StudentListPageSize)!=0));
  } elseif($type == 'discussion_release'){
  	$discussion_area_id = $_REQUEST['discussion_area_id'];
  	$count = get_discussion_area_release_count($discussion_area_id,$conn);
  	$page =  ((int)($count/(ReleaseListPageSize)) + (int)($count%(ReleaseListPageSize)!=0));
  }elseif($type == 'discussion_response'){
  	$discussion_release_id = $_REQUEST['discussion_release_id'];
  	$count = get_discussion_area_response_count($discussion_release_id,$conn);
  	$page =  ( (int)($count/(ResponseListPageSize)) + (int)($count%(ResponseListPageSize)!=0));
  }else{
  	$page = 1;
  }
  if($page == 0)
  	$page = 1;
  echo $page;
?>