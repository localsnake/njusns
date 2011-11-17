<?php
	$course_id = $_REQUEST['course_id'];
	$id = $_REQUEST['id'];
	$type = $_REQUEST['type'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseModifyResource.css" rel="stylesheet" type="text/css"/>
  
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
	<form id="courseResourceId" enctype="multipart/form-data" method="post" action='includes/interface/modify_resource.php?course_id=<?php echo $course_id; ?>&course_resource_id=<?php echo $id?>&resource_type=<?php echo $type; ?>'>
	<table class="designedTable">
	<?php if($type == 'O') { ?>
	<tr id="outerTr">
			<td>URL链接:</td>
			<td><input type="text" name="resource_url" id="inputTitle" autocomplete="off"/></td>
	</tr>
	<?php } else{ ?>
	<tr>
		<td><label for="file">修改附件</label></td>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	<?php } ?>
	<tr>
		<td>修改原因:</td>
		<td><textarea name="message" id="" cols="30" rows="10"></textarea></td>
	</tr>
	</table>
	<div class="buttonWrapper">
			<input id='uploadResource' type='submit' name="submit" value='' class="acceptBtn"/>
			<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
	</form>
</body>
</html>
