<?php

/**
 * the interface to modify resource 
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
 
 $user_id = $_SESSION['user_id'];
 $resource_id = addslashes(trim($_REQUEST['course_resource_id']));
 $course_id = addslashes(trim($_REQUEST['course_id']));
 $upload_file = $_FILES["file"]["tmp_name"]; 
 $message = addslashes(trim($_REQUEST['message']));
 $resource_type = addslashes($_REQUEST['resource_type']); 		//resource type I:内部资源 O:外部资源
 $resource_url= addslashes($_REQUEST['resource_url']); 
 
 $s = new SaeStorage();
 $conn=db_connect();
 $conn->autocommit(FALSE);

 do_html_header("修改资源结果");
 
 if(!is_course_teacher($user_id,$course_id,$conn)){
	echo '抱歉，您无权修改该课程的课件';
	exit;
 }
 
 
 
 //获取当前资源信息
 $resource_info = get_resource_info($resource_id,$conn);
 $resource_title = $resource_info->course_resource_title; 
 
 
 if($resource_type=='I') {
	
	 $resource_dir = $resource_info->course_resource_dir;
	
	 if(!$resource_dir){
		echo '资源文件不存在';
		exit;
	 }
	 // 上传新的资源文件
	 if($upload_file) {
		$s->delete($file_domain,$resource_dir);
		$file_size_max = MaxFileSize;						// 1M限制文件上传最大容量(bytes)  
		$upload_file_size=$_FILES["file"]["size"];
		$accept_overwrite = 1;								//是否允许覆盖相同文件
		
		// create a new  random upload file name and check format modify by Qiangrw
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
		$upload_file_name = "r_".$upload_file_name;		// add resource prefix
		if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
			echo "存在相同文件名的文件"; 
			exit;
		}
		$s->upload($file_domain,$upload_file_name,$upload_file);		//上传文件
		$dir = $file_dir . "/" . $upload_file_name;
		// 修改课程资源数据库
		if(!modify_resource($resource_id,$resource_type,$upload_file_name,$conn)){
			echo '修改资源数据库失败';
			exit;
		}
	  } else {
		echo '很抱歉，修改失败了，请添加修改的附件，有版权的电子书可能不能正确上传哦。';
		exit;
	  }
	} else if($resource_type=='O') {
	    if(!check_valid_url($resource_url)){
		   echo '外部资源URL格式错误，请确认URL的正确性. eg. http://www.download.com/file/temp.pdf';
		   echo '<br />您填写的内容为:',$resource_url;
		   exit;
	    }
		if(!$resource_url || strlen($resource_url)>100) {
			echo '请填写外部资源链接，应该在0-100字之间';
			exit;
		}
		if(!modify_resource($resource_id,$resource_type,$resource_url,$conn)){
		    echo '修改失败';
			exit;
		}
		$dir = $resource_url;
	}
    
    // 发布课程动态
	if($resource_type == 'I'){
		$download_url = "includes/interface/download_file.php?course_id=$course_id&download_id=$resource_id&kind=3";
	} else {
		$download_url = $resource_url;
	}
	$content="修改了资源:"."<a target='_blank' href='$download_url'>$resource_title</a>";
	$content = addslashes($content);
	if($message) $content.= "[ $message ] ";
    if(!upload_course_news($course_id,$content,$conn)){
		echo '发布课程动态失败';
		exit;
	}
    $conn->commit();
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
	var resourceId = <?php echo $resource_id;?>;
	var filePath = "<?php echo "$download_url";?>";
	var updateTime = "<?php echo "$update_time";?>";
	parent.$("a[name='resourceDir" + resourceId + "']").attr('href',filePath);
	parent.$("#updateTime"+resourceId).html(updateTime);
	parent.$.fancybox.close();
 </script>
<?php
 do_html_footer();
?>