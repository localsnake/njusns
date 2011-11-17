<?php
/**
  * The file of password frogot from
  * 
  * @author QiangRunwei <qiangrw@gmail.com>
  * @copyright LocalsNake Net League 2011
  * @package output
  */
   $course_id = addslashes(trim($_REQUEST['course_id']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>设置助教</title>
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/forgotPswSendMail.css" rel="stylesheet" type="text/css"/>
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <!-- Auto Complete -->
  <script type='text/javascript' src='lib/jquery.bgiframe.min.js'></script>
  <script type='text/javascript' src='lib/jquery.ajaxQueue.js'></script>
  <script type='text/javascript' src='lib/jquery.autocomplete.min.js'></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
  <script src="scripts/LocalSetting.js" type="text/javascript"></script>
  <script type="text/javascript">
	var courseId = <?php echo $course_id; ?>;
	$().ready(function() {
		$('.loginEmail').autocomplete( Root+'automail.php', {
			width: 195,
			selectFirst: false,
			scroll: false
		});
		$.getJSON(
			Root + 'get_courseinfo.php', {
				course_id: courseId
			}, 
			function(data){
				if(data['ta0_email'] != undefined) $('#email0').val(data['ta0_email']);
				if(data['ta1_email'] != undefined) $('#email1').val(data['ta1_email']);
				if(data['ta2_email'] != undefined) $('#email2').val(data['ta2_email']);
				if(data['ta3_email'] != undefined) $('#email3').val(data['ta3_email']);
				if(data['ta4_email'] != undefined) $('#email4').val(data['ta4_email']);
			}
		);
	});
  </script>
</head>
<body class="fancyboxWrapper">
	<p style="font-size:10px; color:red;">
	<ul>
		<li>设置助教以后，助教将拥有该课程的通知发布、学生管理、课件上传、资源上传、作业发布权限<br /></li>
		<li>您最多课设置5位助教，小于5位则留空</li>
		<li>删除所有助教只需要将所有区域留空后点击确定即可</li>
	</ul>		
	</p>
   <form action='includes/interface/set_course_ta.php' method='post'>
   <input type="hidden" name="course_id" value="<?php echo "$course_id"; ?>"/>
   <table class="designedTable">
	<tr> 
		<td>助教0注册邮箱:</td>
		<td><input id='email0' class="loginEmail" type='text' name='email0' autocomplete="off"/></td>
   </tr>
   <tr> 
		<td>助教1注册邮箱:</td>
		<td><input id='email1'  class="loginEmail" type='text' name='email1' autocomplete="off"/></td>
   </tr>
   <tr> 
		<td>助教2注册邮箱:</td>
		<td><input id='email2'  class="loginEmail" type='text' name='email2' autocomplete="off"/></td>
   </tr>
   <tr> 
		<td>助教3注册邮箱:</td>
		<td><input id='email3'  class="loginEmail" type='text' name='email3' autocomplete="off"/></td>
   </tr>
   <tr> 
		<td>助教4注册邮箱:</td>
		<td><input id='email4' class="loginEmail" type='text' name='email4' autocomplete="off"/></td>
   </tr>
   </table>
	<div class="buttonWrapper">		
		<input type='submit' class="acceptBtn" value=""  />
	</div>
</form>
</body>
</html>
