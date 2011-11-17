<?php 
/**
  * the file to display set assignment form
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
	<title>设置作业FTP</title>
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
			if(data != ''){
				var host = data['host'];
				var user = data['user'];
				var password = data['password'];
				
				$('[name=host]').val(host);
				$('[name=user]').val(user);
				$('[name=password]').val(password);
			}
		}
	</script>
</head>
<body class="fancyboxWrapper">
  <div id="contentWrapper">
    <p>设置FTP后学生上传作业可以直接上传到您设置的FTP上<br/>
  	 您可以不设置密码，而选择让学生自己输入</p>
    <form enctype="multipart/form-data" action="includes/interface/set_course_ftp.php" method="post">
    <table class="designedTable">
    	<tr>
  		<td>主机名</td>
  		<td><input type="text" name="host" /> 
  			<input type="hidden" name="course_id" autocomplete='off' value="<?php echo $course_id;?>" />
  		</td>  
  	</tr> 
    	<tr>
  		<td>用户名</td>
  		<td><input type="text" name="user" autocomplete='off' /></td>  
  	</tr> 
  	<tr>
  		<td>密码</td>
  		<td><input type="password" name="password" autocomplete='off'/></td>  
  	</tr>
    </table>
	<div class="errorWrapper">
		<p id="errorEcho">
			<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
		</p>
	</div>

	<div class="buttonWrapper">
  		<input id='setFtp' type='submit' name="submit" value='' class="acceptBtn"/>
  		<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
  	</div>
   </form>
  </div>

</body>
</html>
