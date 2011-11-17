<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>新建讨论区</title>
	<link type="text/css" href="css/frmCourseCreateDiscussion.css??" rel="stylesheet" />	
	<link type="text/css" href="css/fancyboxStyle.css" rel="stylesheet" />	
	<script src="lib/jquery.js" type="text/javascript"></script> 
	
	<script src="scripts/frmCourseCreateDiscussion.js" type="text/javascript"></script> 
	<script src="scripts/LocalSetting.js" type="text/javascript"></script>
<?php	$course_id = $_REQUEST['course_id'];	?>
</head>

<body class="fancyboxWrapper">
<!-- 不要忘记加上课程头像 -->
<div id="courseCreateDiscussion">
				<input id="courseDiscussionCourseId" type='hidden' value="<?php echo $course_id; ?>" ></input>
			<table id='box-table' class="designedTable">
				<tr><td>讨论区名:</td>
					<td><input 	id="courseDiscussionName"  name="courseDiscussionName"></input></td></tr>
			</table>
				<div class="errorWrapper">
					<p id="errorEcho">
						<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
					</p>
				</div>
			<div class="buttonWrapper">
				<input id='createBtn' type='submit' name="submit" value='' class="acceptBtn"/>
				<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
			</div>
</div>
<!--
<div class="errorWrapper">
</div>
-->
</body>
</html>
