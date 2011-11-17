<?php
/* The file of upload pic form
 * 
 * @version:@(#)navUploadPic.php
 * @author: Runwei Qiang  2011/6/12
 */
  /*require_once 'output_fns.php';			# This will be removed in the future and beautify the html page
  require_once 'LocalSettings.php';		
  */
  $type = $_REQUEST['type'];
  
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/frmUploadPhoto.css" rel="stylesheet" type="text/css"/>
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <!--<link type="text/css" href="frmCourseUploadAssignment.css">-->
  <title>上传头像</title>
  <script type="text/javascript">
  $().ready(function () {
		$('input[name=submitCancel]').bind('click',function(){
			parent.$.fancybox.close();	//关闭FancyBox
		});
	}
  );
  </script>
</head>
<body>

<?php  
if($type == 'user'){						# upload user photo
	/*do_html_header('上传用户头像');
      display_upload_pic_form($interface_path."upload_pic.php?act=upload");
      do_html_footer();*/
  ?>
	<body class="fancyboxWrapper">
		<form name="photo" enctype="multipart/form-data" 
			  method="post" action="includes/interface/upload_pic.php?act=upload">
		<table class="designedTable">
		<tr>
			<td><label for="file">File:</label></td>
			<td><input type="file" name="file" id="file" /></td>
		</tr>
		</table>
		<div class="buttonWrapper">
				<input id='userUploadPhoto' type='submit' name="submit" value='' class="acceptBtn"/>
				<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
		</div>
		</form>
	</body>
  <?php
  
  
  } else {									# upload course photo
	$course_id = $_REQUEST['course_id'];
  	/*do_html_header('上传课程头像');
    display_upload_pic_form($interface_path."upload_course_pic.php?act=upload&course_id=$course_id");
    do_html_footer();*/
    ?>
	<body class="fancyboxWrapper">
	<form name="photo" enctype="multipart/form-data" 
		  method="post" action="includes/interface/upload_course_pic.php?act=upload&course_id=<?php echo $course_id;?>">
	<table class="designedTable">
	<tr>
		<td><label for="file">File:</label></td>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	</table>
	<div class="buttonWrapper">
			<input id='courseUploadPhoto' type='submit' name="submit" value='' class="acceptBtn"/>
			<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
	</form>
	</body>
	<?php
  }
?>
</body></html>
