<?php
	$course_id = $_REQUEST['course_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css?" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseUploadLecture.css?" rel="stylesheet" type="text/css"/>
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <script type="text/javascript">
	  $().ready(function () {
			$('input[name=submitCancel]').bind('click',function(){
				parent.$.fancybox.close();	//关闭FancyBox
			});
		}
	  );
  </script>
</head>
<body class="fancyboxWrapper">
	<form id="courseUpload" enctype="multipart/form-data" method="post" action=	'includes/interface/upload_lecture.php?course_id=<?php echo $course_id; ?>'>
	<table class="designedTable">
	<tr>
		<th>课件标题</th>
		<td><input type="text" name="course_lecture_title" autocomplete="off"/></td>
	</tr>
	<tr>
		<th><label for="file">添加附件</label></th>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	</table>
	<div class="errorWrapper">
		<p id="errorEcho">
			<label style="font-size:12px;">添加失败，我也不知道是什么原因</label>
		</p>
	</div>

	<div class="buttonWrapper">
		<input id='uploadLecture' type='submit' name="submit" value='' class="acceptBtn"/>
		<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
	</form>
</body>
</html>
