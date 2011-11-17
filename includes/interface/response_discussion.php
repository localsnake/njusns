<?php

/**
 * The interface to response discussion in discussion area
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
 if(!check_valid_user()){
  	echo 0;
  	exit;
 }
 
 $course_id = addslashes(trim($_POST['course_id']));
 $discussion_release_id = addslashes(trim($_POST['discussion_release_id']));
 $response_content = addslashes(trim($_POST['discussion_response_content']));
 $reponse_user_id = addslashes(trim($_POST['reponse_user_id']));
 
 $response_time=date("Y-m-d H:i:s");
 if(!filled_out($_POST)){
  	echo "Data Param Error.";
  	exit;
  }
 
 $conn=db_connect();
 $query="insert into sns_course_discussion_response(discussion_release_id,
 discussion_response_time,discussion_response_content,user_id) values( $discussion_release_id,'$response_time','$response_content',$user_id)";
 $stmt=$conn->query($query);
 if($conn->affected_rows != 1){
 	echo 'Database Error!';
 	exit;
 }
 $query="UPDATE sns_course_discussion_release set discussion_response_num=discussion_response_num+1
            where discussion_release_id=$discussion_release_id";
 if($reponse_user_id != 0 && $reponse_user_id != $user_id) {	//不是自己回自己
	$sel_result =
		$conn->query("SELECT * FROM sns_course_discussion_release WHERE discussion_release_id=$discussion_release_id");
	$release_info = $sel_result->fetch_object();
	$release_title = $release_info->discussion_release_title;
	$release_user_id = $release_info->user_id;
	$permission_no = 1;												//只读
	if($response_user_id == $release_user_id) $permission_no = 2;	//可修改
	$content = "讨论区中 有人在帖子<a onclick='javascript:jumpToRelease($course_id,$discussion_release_id,$permission_no);'>$release_title</a>中回复了你";
	$content = addslashes($content);
	send_freshmilk($course_id,$reponse_user_id,'C',$content,$conn);
 }
 $stmt=$conn->query($query);
 echo 1;

?>