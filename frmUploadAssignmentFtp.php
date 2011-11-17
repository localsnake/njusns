<?php 
/**
  * the file to display upload assignment form
  * 
  * @author QiangRunwei <qiangrw@gmail.com>
  * @copyright LocalsNake Net League 2011
  * @package output
  */
  
  $course_id = $_REQUEST['course_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>上传作业FTP</title>
	<script src="lib/jquery.js" type="text/javascript"></script>
	<script src="scripts/LocalSetting.js" type="text/javascript"></script>
	<link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
	<link href="css/frmSetAssignmentFtp.css" rel="stylesheet" type="text/css"/>
	
	<script type="text/javascript">
		courseId = <?php echo $course_id;?>;
		$().ready(function() {
			$('input[name=submitCancel]').bind('click',function(){
				parent.$.fancybox.close();	//关闭FancyBox
			});
			$.getJSON(
				Root + "get_course_ftp_info.php?course_id="+courseId,
				setFTPInfo
			);
		});
		function setFTPInfo(data) {
			if(data['host'] != ''){
				var host = data['host'];
				var user = data['user'];
				var password = data['password'];
				var student_id = data['student_id'];
				$('[name=host]').val(host);
				$('[name=user]').val(user);
				$('[name=password]').val(password);
				$('[name=remotefile]').val(student_id);
			}else {
				$('#note').html('课程老师尚无设置FTP信息，请您手动输入');
			}
		}
	</script>
</head>
<body class="fancyboxWrapper">
	<p id="note" style='font-size:50%;'></p>
	<form enctype="multipart/form-data" action="includes/interface/ftp_put.php" method="post">
	<table class="designedTable">
	<tr>
		<td>主机名</td>
		<td><input type="text" name="host" autocomplete='off'/></td>  
	</tr> 
	<tr>
		<td>用户名</td>
		<td><input type="text" name="user" autocomplete='off'/></td>  
	</tr> 
	<tr>
		<td>密码</td>
		<td><input type="password" name="password" autocomplete='off'/></td>  
	</tr>
	<tr>
		<td><label for="file">文件</label></th>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	<!-- 该项以后作为hidden选项 用学生的学号自动命名 -->
	<tr><td>学号</td><td><input type="text" name="remotefile" /></td> </tr>
	<!--<tr><td></td><td><input type='submit' name="submit" value='提交'/></td></tr>-->
	</table>
	<div class="errorWrapper">
		<p id="errorEcho">
			<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
		</p>
	</div>

	<div class="buttonWrapper">
		<input id='uploadAssignment' type='submit' name="submit" value='' class="acceptBtn"/>
		<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
</form>
</body>
</html>
