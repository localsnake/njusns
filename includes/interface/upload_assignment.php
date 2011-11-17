<?php

/**
 * the interface to upload assignment
 * 
 * @author qianyu <yangzhouqianyu@sina.com>  
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
	session_start();
	require_once('sns_fns.php');
	require_once $include_path."SaeStorage.php";
	$user_id=$_SESSION['user_id'];

	$course_id = addslashes($_REQUEST['course_id']);
	$assignment_id = addslashes($_REQUEST['course_assignment_id']);
	$assignment_title= addslashes($_POST['course_assignment_title']);
	$assignment_deadline_date = addslashes($_POST['course_assignment_deadline_date']);
	$assignment_deadline_time = addslashes($_POST['course_assignment_deadline_time']);
	$upload_file=$_FILES["file"]["tmp_name"]; 
	
	$assignment_deadline = $assignment_deadline_date." ".$assignment_deadline_time.":00";
	$assignment_deadline_begin = $assignment_deadline_date." 00:05:00";
	$create_time=date("Y-m-d H:i:s");
	$update_time=$create_time;
	
	$s = new SaeStorage();
	$conn = db_connect();
	$conn->autocommit(FALSE);

	do_html_header("上传作业结果");
	if(!ereg('[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}', $assignment_deadline) ) {
		echo '日期格式错误',$assignment_deadline;
		exit;
	}
	if(!$assignment_title || !$assignment_deadline_date) {
		echo '表格未填写完整';
		exit;
	} 
	if(strlen($assignment_title) > 50) {
		echo '作业标题长度不能大于50字';
		exit;
	}
	if(!is_course_teacher($user_id,$course_id,$conn)){
		echo '抱歉，您无权上传该课程的课件';
		exit;
	}
	if(!$upload_file) {
		echo '请添加作业附件,有版权的电子书可能不能正确上传哦';
		exit;
	}
 
	$file_size_max = MaxFileSize;	// 10M 限制文件上传最大容量(bytes)  
	$upload_file_size=$_FILES["file"]["size"];
	$accept_overwrite = 1;//是否允许覆盖相同文件  // 
	if($upload_file_size > $file_size_max) {  
		echo "对不起，文件容量限制为10M以下"; 
		exit;
	}

	// Create a new  random upload file name and check format modify by Qiangrw
	$random = strtotime(date('Y-m-d H:i:s'));
	$upload_file_name = $course_id."_".$random;
	$filename = basename($_FILES['file']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
	$upload_file_name = $upload_file_name.".".$file_ext;	// add file type 
	
	if(!check_upload_file_format($file_ext)){
		echo '暂时不支持'.$file_ext."类型的文件上传,请压缩后再试";
		exit;
	}
	$upload_file_name = "a_".$upload_file_name;		// add assignment prefix
	if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
		echo "存在相同文件名的文件"; 
		exit; 
	}
	// 添加作业信息到数据库
	$assignment_id = new_assignment($course_id,$assignment_title,
								$upload_file_name,$assignment_deadline,$create_time,$update_time,$conn);
	if(!$assignment_id) {
		echo '添加作业到数据库失败';
		$conn->rollback();
		exit;
	}
	
	
	// 发布课程动态
	$download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$assignment_id&kind=2";
	$content1="发布了作业:"."<a target='_blank' href='$download_url'>$assignment_title</a>";
	$content1 = addslashes($content1);
	
	$content2=",截止时间为:".$assignment_deadline;
	$content=$content1.$content2;
	
	
	if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		$conn->rollback();
		exit;
	}
	
    $course_info = get_course_info($course_id,$conn);
    $course_name = $course_info->course_name;
    // 向课程的学生、老师、助教发布新鲜事 并发布系统日程
    $related_people_list = get_course_related_people_list($course_id,$conn);
    $datetime=date("Y-m-d H:i:s");	 // 当前时间
    for($i=0; $i<count($related_people_list); $i++){
    	$to_id = $related_people_list[$i]->user_id;
    	$freshmilk_type='C';	//Course ID
        send_freshmilk($course_id,$to_id,$freshmilk_type,$content,$conn);
		// move to load event
		/*$event_type="AM-$assignment_id";			// assignment month plus id
		$event_content = "努力完成作业:$assignment_title ($course_name)";
		save_event($to_id,$datetime,$assignment_deadline,$event_type,$event_content);
		
		$event_type="AW-$assignment_id";			// assignment week plus id
		$event_content = "作业:$assignment_title ($course_name)截止";
		save_event($to_id,$assignment_deadline_begin,$assignment_deadline,
						$event_type,$event_content);	*/
   	}
	
	$conn->commit();
	//物理上传文件
	$s->upload($file_domain,$upload_file_name,$upload_file);
	echo "上传成功，窗口即将关闭";
?>
	<script type="text/javascript">
		parent.courseAssignmentInitPage();
		parent.$.fancybox.close();
	</script>
<?php
	do_html_footer();
?>