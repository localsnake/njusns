<?php

/**
 * the interface to modify lecture info
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
 
 
 $lecture_id = addslashes(trim($_REQUEST['course_lecture_id']));
 $course_id = addslashes(trim($_REQUEST['course_id']));
 $upload_file = $_FILES["file"]["tmp_name"]; 
 $message = addslashes(trim($_REQUEST['message']));
 
 $s = new SaeStorage();
 $conn = db_connect();
 
 do_html_header("修改课件结果");
 if(!is_course_teacher($user_id,$course_id,$conn)){
	echo '抱歉，您无权修改该课程的课件';
	exit;
 }
  
  // 获取当前课件信息 
  $lecture_info = get_lecture_info($lecture_id,$conn);
  $upload_file_name = $lecture_info->course_lecture_dir; 
  $lecture_title = $lecture_info->course_lecture_title;
  $dir = $file_dir . "/" . $upload_file_name;
  
 // 有新的课件需要上传
 if($upload_file) {  
	$s->delete($file_domain,$upload_file_name);					// delete former file
    $file_size_max = MaxFileSize;// 1M限制文件上传最大容量(bytes)  
    $upload_file_size=$_FILES["file"]["size"];
    $accept_overwrite = 1;//是否允许覆盖相同文件
    
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
    $upload_file_name = "l_".$upload_file_name;		// add lecture prefix
	if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
		echo "存在相同文件名的文件"; 
		exit;
	}
	$s->upload($file_domain,$upload_file_name,$upload_file);		//上传文件 
	$dir = $file_dir . "/" . $upload_file_name;
 } else {
	echo '很抱歉，修改失败了，请添加修改的附件，有版权的电子书可能不能正确上传哦';
	exit;
 }
 
 
 
	//修改课程讲义数据库
	if(!modify_lecture($lecture_id,$upload_file_name,$conn)){
		echo '您没有进行修改';
    	exit;
	}
    
    // 发布课程动态
	$download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$lecture_id&kind=1";
    $content="修改了讲义:"."<a target='_blank' href='$download_url'>$lecture_title</a>";
	$content = addslashes($content);
	 
	if($message) $content.= "[ $message ] ";
    if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		exit;
	}
    
    // 向课程的学生发布新鲜事 并 发布系统日程
    $student_list = get_course_related_people_list($course_id,$conn);
    for($i=0; $i<count($student_list); $i++){
    	$to_id = $student_list[$i]->user_id;
    	$freshmilk_type='C';	//Course ID
        send_freshmilk($course_id,$to_id,$freshmilk_type,$content,$conn);	
   	}
	echo "修改成功，窗口即将关闭";
	$update_time = date("Y-m-d H:i");

?>
 <script type="text/javascript">
	var lectureId = <?php echo $lecture_id;?>;
	var filePath = "<?php echo "$download_url";?>";
	var updateTime = "<?php echo "$update_time";?>";
	parent.$("a[name='lectureDir" + lectureId + "']").attr('href',filePath);
	parent.$("#updateTime"+lectureId).html(updateTime);
	parent.$.fancybox.close();
 </script>
<?php
 do_html_footer();
?>