<?php
	$course_id = $_REQUEST['course_id'];
	$id = $_REQUEST['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseModifyAssignment.css" rel="stylesheet" type="text/css"/>
  
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <link type="text/css" href="css/redmond/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
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
	<form id="courseAssignmentEdit" enctype="multipart/form-data" method="post" action='includes/interface/modify_assignment.php?course_id=<?php echo $course_id; ?>&course_assignment_id=<?php echo $id;?>'>
	<table class="designedTable">
	<tr>
		<td>修改截止日期:</td>
		<td><input type="text" id="datepicker" name="course_assignment_deadline_date" /></td>
	</tr>
	<tr>
		<td>修改截止时间:</td>
		<td><select name="course_assignment_deadline_time">
			<option>20:00</option>
			<option>23:55</option>
		</select></td>
	</tr>
	<tr>
		<td><label for="file">修改附件:</label></td>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	<tr>
		<td>修改原因:</td>
		<td><textarea name="message" id="" cols="30" rows="10"></textarea></td>
	</tr>
	</table>
		<div class="buttonWrapper">
			<input id='courseModifyAssignment' type='submit' name="submit" value='' class="acceptBtn"/>
			<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
		</div>
	</form>
</body>
</html>
