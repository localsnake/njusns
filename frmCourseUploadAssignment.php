<?php
	$course_id = $_REQUEST['course_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css?" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseUploadAssignment.css?" rel="stylesheet" type="text/css"/>
  <link type="text/css" href="css/redmond/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
	
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <script type="text/javascript" src="lib/jquery-ui-1.8.13.custom.min.js"></script> 
<script>
	$(document).ready(function() {
		$("#datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
		$('input[name=submitCancel]').bind('click',function(){
			parent.$.fancybox.close();	//关闭FancyBox
		});
	});
</script>
</head>
<body class="fancyboxWrapper">
	<form id="courseUpload" enctype="multipart/form-data" method="post" action='includes/interface/upload_assignment.php?course_id=<?php echo $course_id; ?>'>
	<table class="designedTable">
	<tr>
		<td>作业名:</td>
		<td><input type="text" name="course_assignment_title" autocomplete="off"/></td>
	</tr>
	<tr>
		<td>截止日期:</td>
		<td><input type="text" id="datepicker" name="course_assignment_deadline_date" /></td>
	</tr>
	<tr>
		<td>截止时间:</td>
		<td><select name="course_assignment_deadline_time">
			<option>20:00</option>
			<option>23:55</option>
		</select></td>
	</tr>
	<tr>
		<td><label for="file">上传附件:</label></td>
		<td>

			<div id="fileInput">
				<input id="chooseFile" type="file" name="file" id="file" />
				<div id="fakefile">
				<!--	<input type=""><img src="./images/upload_choose.png" alt="upload" /></input> -->
				</div>
			</div>
		</td>
	</tr>
	</table>
		<div class="errorWrapper">
			<p id="errorEcho">
				<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
			</p>
		</div>

		<div class="buttonWrapper">
			<input id='courseCreateAssignment' type='submit' name="submit" value='' class="acceptBtn"/>
			<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
		</div>
</form>
		
</body>
</html>
