<?php

/**
 * The interface to upload course lecture info and files
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
 
  $course_id= addslashes($_REQUEST['course_id']);
  $lecture_title = addslashes($_POST['course_lecture_title']);
  $upload_file= $_FILES["file"]["tmp_name"];
 
  $conn = db_connect();
  $conn->autocommit(FALSE);
  $create_time=date("Y-m-d H:i:s");
  $update_time=$create_time;
  do_html_header("课件上传结果");
  if(!is_course_teacher($user_id,$course_id,$conn)){
	echo '抱歉，您无权上传该课程的课件';
	exit;
  }
  if(!$lecture_title){
  	echo "表格未填写完整.";
  	exit;
  }
  if(!$upload_file) {
	echo '请添加作业附件';
	exit;
  }
  if(strlen($lecture_title) > 50) {
	echo '课件标题长度不能大于50字';
	exit;
  }
  
  //上传文件
  $file_size_max = MaxFileSize;							// 10M限制文件上传最大容量(bytes)  
  $upload_file_size=$_FILES["file"]["size"];
  $accept_overwrite = 1;									//是否允许覆盖相同文件
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
  $upload_file_name = "l_".$upload_file_name;		// add lecture prefix
  if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
	echo "存在相同文件名的文件"; 
	exit; 
  }   
  //修改课程讲义数据库
  $lecture_id = new_lecture($course_id,$lecture_title,$upload_file_name,$create_time,$update_time,$conn);
  if(!$lecture_id) {
  	echo '抱歉，插入新课程讲义失败【数据库错误】';
	$conn->rollback();
  	exit;
  }
  //发布课程动态数
  $download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$lecture_id&kind=1";
  $content="发布了讲义:"."<a target='_blank' href='$download_url'>$lecture_title</a>";
  $content = addslashes($content);
  if(!upload_course_news($course_id,$content,$conn)){
	echo '抱歉，过程中发布课程动态失败【数据库错误】';
	$conn->rollback();
	exit;
  }
 
  // 向课程的学生，老师，助教发布新鲜事 并 发布系统日程
  $student_list = get_course_related_people_list($course_id,$conn);
  for($i=0; $i<count($student_list); $i++){
	$to_id = $student_list[$i]->user_id;
	$freshmilk_type='C';	//Course ID
    send_freshmilk($course_id,$to_id,$freshmilk_type,$content,$conn);
  }
  $conn->commit();
  $s->upload($file_domain,$upload_file_name,$upload_file);		//物理上传文件
  
  echo "上传课件成功，窗口即将关闭.";
  
?>
	<script type="text/javascript">
		parent.courseLectureInitPage();
		parent.$.fancybox.close();
	</script>
<?php
	do_html_footer();
?>