<?php

/**
 * @todo add course id
 */ 
	
	require_once 'sns_fns.php';
	require_once $include_path."SaeStorage.php";
	do_html_header('FTP LOG');
	
	// set up variables - change these to suit application 
	/*$host = '172.25.49.66';
	$user = 'qdx';
	$password = '********';*/
	
	$host = trim($_POST['host']);
	$user = trim($_POST['user']);
	$password = trim($_POST['password']);
	$remotefile = trim($_POST['remotefile']);
	$file_size_max = MaxFileSize;
	$s = new SaeStorage();
	
	if(!filled_out($_POST)) {
		echo '表格未填写完整';
		exit;
	}
	
	$upload_file = $_FILES["file"]["tmp_name"]; 	#'C:\Users\qiangrw\hi.txt';
	echo "local: $upload_file <br/>";
	if(!$upload_file) {
		echo '请添加作业文件';
		exit;
	}
	$upload_file_size=$_FILES["file"]["size"];
	if($upload_file_size > $file_size_max) {  
		echo "对不起，文件容量限制为10M以下"; 
		exit;
	}
	$filename = basename($_FILES['file']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
	$remotefile .= ".$file_ext";
	
	/*// Create a new  random upload file name and check format modify by Qiangrw
	$random = strtotime(date('Y-m-d H:i:s'));
	$upload_file_name = $random;
	$filename = basename($_FILES['file']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
	$upload_file_name = $upload_file_name.".".$file_ext;	// add file type 
		if(!check_upload_file_format($file_ext)){
		echo '暂时不支持'.$file_ext."类型的文件上传,请压缩后再试";
		exit;
	}
	$upload_file_name = "temp_".$upload_file_name;
	
	if($s->fileExists($file_domain,$upload_file_name) && !$accept_overwrite) {  
		echo "存在相同文件名的文件"; 
		exit; 
	}
	$s->upload($file_domain,$upload_file_name,$upload_file);	
	$localfile = $upload_file_name;*/
	 
	$localfile = $upload_file; 
	// connect to host
	$conn = ftp_connect("$host"); 
	if (!$conn){
	  echo '[错误] 无法连接到FTP服务器<br>';
	  exit;
	}
	echo "* 成功连接到: $host.<br>";

	// log in to host
	@$result = ftp_login($conn, $user, $password);
	ftp_pasv($conn,1);
	if (!$result) {
	  echo "[错误] 无法以 $user 的身份登录<br>";
	  ftp_quit($conn);
	  exit;
	}
	echo "* 成功登录为: $user<br/>";
	echo "* 开始上传文件 请稍后 <br/>";

	
	// check file times to see if an update is required
	/*echo '*开始检查文件时间...<br>';
	if (file_exists($localfile))  {
	  $localtime = filemtime($localfile);
	  echo 'Local file last updated ';
	  echo date('G:i j-M-Y', $localtime);
	  echo '<br>';
	} else {
	  $localtime=0; 
	}
	$remotetime = ftp_mdtm($conn, $remotefile);
	if (!($remotetime >= 0))	{
	   // This doesn't mean the file's not there, server may not support mod time 
	   echo 'Can\'t access remote file time.<br>';  
	   $remotetime=$localtime+1;  // make sure of an update  
	}
	else	{
	  echo 'Remote file last updated ';
	  echo date('G:i j-M-Y', $remotetime);
	  echo '<br>';
	}*/

	// download file
	$local_path = $localfile; #$file_domain."/".$localfile;
	$fp = fopen ($local_path, 'r');
	if(!$fp) {
		echo "[ERROR] 打开文件[$localfile]失败 <br />";
		exit;
	}
	echo "* 上传 $local_path <br />";
	if (!$success = ftp_fput($conn, $remotefile, $fp, FTP_BINARY)) {
	  echo "[错误] 无法上传文件[$local_path]到FTP服务器 <br />"; 
	  ftp_quit($conn);
	  exit;
	}
	fclose($fp);
	echo "* 文件[$remotefile]上传成功 <br/>";
	echo "* 请关闭该窗口 <br/>";
	ftp_quit($conn);
	do_html_footer();
?>