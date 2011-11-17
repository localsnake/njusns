<?php
	$course_id = $_REQUEST['course_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css??" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseUploadResource.css?" rel="stylesheet" type="text/css"/>
  
  <script src="lib/jquery.js" type="text/javascript"></script>
  <script src="scripts/LocalSetting.js" type="text/javascript"></script> 
  <script src="scripts/frmCourseUploadResource.js" type="text/javascript"></script> 
</head>
<body class='fancyboxWrapper'>

<form id="courseUpload" enctype="multipart/form-data" method="post" action='includes/interface/upload_resource.php?course_id=<?php echo $course_id; ?>'>	
	<table class="designedTable">
		<input type="hidden" id="courseID" name="course_id" value="<?php echo $course_id; ?>" />
		<tr>
			<td>资源名:</td>
			<td><input type="text" name="course_resource_title" id="inputTitle" autocomplete="off"/></td>
		</tr>
		<tr>
			<td>资源类型:</td>
			<td>
				<input style="width:20px;" type="radio" name="resource_type" value="I" checked/>内部资源
				<input style="width:20px;" type="radio" name="resource_type" value="O"/>外部资源
			</td>
		</tr>
		<tr id="innerTr">
			<td><label for="file">添加附件:</label></td>
			<td><input type="file" name="file" id="file" /></td>
		</tr>
		<tr id="outerTr" style="display:none;">
			<td>URL链接:</td>
			<td><input type="text" name="course_resource_url" id="inputTitle" autocomplete="off"/></td>
		</tr>
	</table>
	<div class="errorWrapper">
		<p id="errorEcho">
			<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
		</p>
	</div>
	<div class="buttonWrapper" style="margin-top:20px;">
		<input id='uploadResource' type='submit' name="submit" value='' class="acceptBtn"/>
		<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
</form>
</body>
</html>
