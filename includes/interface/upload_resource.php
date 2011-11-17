<?php

/**
 * The interface to upload resource
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
	$resource_type = addslashes($_POST['resource_type']);
	
	$resource_title = addslashes($_POST['course_resource_title']);
	$resource_url = addslashes($_POST['course_resource_url']);
	$upload_file=$_FILES["file"]["tmp_name"];  
	
	$s = new SaeStorage();
	$conn = db_connect();
    $conn->autocommit(FALSE);
	$create_time=date("Y-m-d H:i:s");
    $update_time=$create_time;
	do_html_header("上传资源结果");
	

	if(!$resource_title) {
		echo '表格未填写完整';
		exit;
	}
	if($resource_type == 'I' ) {
		if ($_FILES['file']['error'] > 0) {
			echo 'ERROR CODE:';
			echo $_FILES['file']['error'];
		}
	    if(!$upload_file) {
		   echo '请添加资料附件';
		   exit;
		}
		if(!is_course_teacher($user_id,$course_id,$conn)){
		   echo '抱歉，您无权上传该课程的课件';
		   exit;
		}
	}
	if($resource_type == 'O' && (!$resource_url || strlen($resource_url)>100) ) {
		echo '请填写外部资源链接，应该在0-100字之间';
		exit;
	}
	if($resource_type == 'O' && !check_valid_url($resource_url)){
		echo '外部资源URL格式错误，请确认URL的正确性. eg. http://www.download.com/file/temp.pdf';
		exit;
	}
	
	if($resource_type == 'I') {
		$resource_url = 'NULL';
		// 上传文件
		$file_size_max = MaxFileSize;			// 1M限制文件上传最大容量(bytes)  
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
		$upload_file_name = "r_".$upload_file_name;		// add resource prefix
		if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
			echo "存在相同文件名的文件"; 
			exit;
		}
	}
	
    // 修改课程资源数据库
    $resource_id = 
		new_resource($course_id,$resource_title,$upload_file_name,$resource_url,$resource_type,$create_time,$update_time,$conn);
    if(!$resource_id){
    	echo '插入新资源信息失败【数据库错误】';
		$conn->rollback();
    	exit;
    }
    // 发布课程动态
	if($resource_type == 'I'){
		$download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$resource_id&kind=3";
	} else {
		$download_url = $resource_url;
	}
	$content="发布了资源:"."<a target='_blank' href='$download_url'>$resource_title</a>";
	$content = addslashes($content);
	if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		$conn->rollback();
		exit;
	}
	
	// 向课程的学生发布新鲜事 并 发布系统日程
	$student_list = get_course_related_people_list($course_id,$conn);
	for($i=0; $i<count($student_list); $i++){
		$to_id = $student_list[$i]->user_id;
		$freshmilk_type='C';	//Course ID
		send_freshmilk($course_id,$to_id,$freshmilk_type,$content,$conn);
	}
	$conn->commit();
	// 物理上传文件
	if($resource_type == 'I') {
       $s->upload($file_domain,$upload_file_name,$upload_file);
	}
	echo "上传资源成功，窗口即将关闭";
?>
	<script type="text/javascript">
	 	parent.courseResourceInitPage();
		parent.$.fancybox.close();
	</script>
<?php
 do_html_footer();
?>