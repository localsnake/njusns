<?php

/**
 * The interface to release discussion in discussion area
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
 
 //extract 
 $discussion_area_id = addslashes(trim($_POST['discussion_area_id']));
 $release_title = addslashes(trim($_POST['discussion_release_title']));
 $release_content = addslashes(trim($_POST['discussion_release_content']));
 
 $release_time=date("Y-m-d H:i:s");
 $release_resnum=0;
 $conn=db_connect();
 // 新帖子标题
 if(strlen($release_title) > 100){
	echo '标题过长，不能超过100字';
	exit;
 }
 
 $query="INSERT INTO sns_course_discussion_release
 			(discussion_area_id,discussion_release_time,discussion_release_title,
 		discussion_release_content,discussion_response_num,user_id) 
 	values( $discussion_area_id,'$release_time','$release_title',
   '$release_content',$release_resnum,$user_id)";
 $conn->query($query);
 $discussion_release_id = $conn->insert_id;
 
 if($conn->affected_rows == 1) {
 	//根据讨论区号找到对应课程好
 	$sel_result = $conn->query("SELECT * FROM sns_course_discussion WHERE discussion_area_id = $discussion_area_id");
	if($sel_result->num_rows == 0) {
		echo '课程不存在';
		exit;
	}
	$row = $sel_result->fetch_object();
	$course_id = $row->course_id;
	$discussion_area_name = $row->discussion_area_name;
 	//发布新帖成功,发布新鲜事通知关注这门课的学生 助教 老师
 	$content = '讨论区发布了新帖:'.$release_title.' [ '.$discussion_area_name.' ]';
	
 	$student_list = get_course_related_people_list($course_id,$conn);
    for($i=0;$i<count($student_list);$i++){
	  	$to_id = $student_list[$i]->user_id;
		$permission_no = 1;
		if($to_id == $user_id) $permission_no = 2;
		$freshmilk_content = "讨论区发布了新帖: <a onclick='javascript:jumpToRelease($course_id,$discussion_release_id,$permission_no);'>$release_title</a>";
		$freshmilk_content = addslashes($freshmilk_content);
	  	send_freshmilk($course_id,$to_id,'C',$freshmilk_content,$conn);
    }
	upload_course_news($course_id,$content,$conn);			//发布课程动态
 	echo 1;
 }else {
 	echo 'Database Error: ',$query;
 }

?>