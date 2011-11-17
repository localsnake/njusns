<?php

/**
 * the interface to modify course assignment by id
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
	$s = new SaeStorage();
	
	$assignment_id = addslashes(trim($_REQUEST['course_assignment_id']));
	$course_id = addslashes(trim($_REQUEST['course_id']));
	$assignment_deadline_date = addslashes(trim($_POST['course_assignment_deadline_date']));
	$assignment_deadline_time = addslashes(trim($_POST['course_assignment_deadline_time']));
	$message = addslashes(trim($_POST['message']));
	
	$upload_file=$_FILES["file"]["tmp_name"];  
	$upload_file_name=$_FILES["file"]["name"];
	$assignment_deadline = $assignment_deadline_date." ".$assignment_deadline_time.":00";
	$assignment_deadline_begin = $assignment_deadline_date." 00:05:00";

	do_html_header("修改课件结果");
	
	$conn = db_connect();
	
	if(!ereg('[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}', $assignment_deadline) ) {
		echo '日期格式错误',$assignment_deadline;
		exit;
	}
	if(!is_course_teacher($user_id,$course_id,$conn)){
		echo '抱歉，您无权修改该课程的课件';
		exit;
	}
	if(!$assignment_deadline){
		echo '截止时间不能为空';
		exit;
	}
	
	// 从数据库读取现有作业信息
	$assignment_info = get_assignment_info($assignment_id,$conn);
	if(!$assignment_info){
		echo '该作业不存在';
		exit;
	}
	$upload_file_name = $assignment_info->course_assignment_dir;
	$assignment_title = $assignment_info->course_assignment_title;
	$dir = $file_dir . "/" . $upload_file_name;
 
	// 有新的附件需要上传
	if($upload_file) {
		$s->delete($file_domain,$upload_file_name);					// delete former file
		$file_size_max = MaxFileSize;// 1M限制文件上传最大容量(bytes)  
		$upload_file_size=$_FILES["file"]["size"];
		$accept_overwrite = 1;//是否允许覆盖相同文件  // 
		
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
		if($upload_file_size > $file_size_max) {  
			echo "对不起，你的文件容量大于规定"; 
			exit;  
		}
		$upload_file_name = "a_".$upload_file_name;		// add assignment prefix
		if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
			echo "存在相同文件名的文件"; 
			exit;
		}
		$s->upload($file_domain,$upload_file_name,$upload_file);		//上传文件
		$dir = $file_dir . "/" . $upload_file_name;
	}
	
	/*修改课程作业数据库*/
	if(!modify_assignment($assignment_id,$assignment_deadline,$upload_file_name,$conn)){
		echo '您没有对作业做任何改动';
    	exit;
	}
	
	
	
	//发布课程动态
	$download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$assignment_id&kind=2";
	$content1="修改了作业:"."<a target='_blank' href='$download_url'>$assignment_title</a>";
	$content1 = addslashes($content1);
    $content2=",截止时间为:".$assignment_deadline;
    $content=$content1.$content2;
	
	if($message) $content.= "[ $message ] ";
    if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		exit;
	}
    
	
	//删除这些学生和该作业号相关的日程	move to load event
	//del_assignment_event($assignment_id);
	
    // 向课程的学生发布新鲜事 并 发布系统日程
    $course_info = get_course_info($course_id,$conn);
    $course_name = $course_info->course_name;
    $student_list = get_course_related_people_list($course_id,$conn);
    $datetime=date("Y-m-d H:i:s");	 // 当前时间
    for($i=0; $i<count($student_list); $i++){
    	$to_id = $student_list[$i]->user_id;
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
	echo "修改成功，窗口即将关闭";
	$update_time = date("Y-m-d H:i");
?>
  <script type="text/javascript">
	var assignmentId = <?php echo $assignment_id;?>;
	var filePath = "<?php echo "$download_url";?>";
	var updateTime = "<?php echo "$update_time";?>";
	var assignmentDeadline = "<?php echo "$assignment_deadline";?>";
	parent.$("#assignmentDeadline" + assignmentId).html(assignmentDeadline);
	parent.$("a[name='assignmentDir" + assignmentId + "']").attr('href',filePath);
	parent.$("#updateTime"+assignmentId).html(updateTime);
	parent.$.fancybox.close();
  </script>
<?php
  do_html_footer();
?>