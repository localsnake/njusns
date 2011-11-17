<?php
/**
  * The file of verify course from
  * 
  * @author QiangRunwei <qiangrw@gmail.com>
  * @copyright LocalsNake Net League 2011
  * @package output
  */
  $course_id = addslashes(trim($_REQUEST['course_id']));
  $from_id = addslashes(trim($_REQUEST['from_id']));
  if(!$course_id || !$from_id) { exit;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>忘记密码</title>
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/forgotPswSendMail.css" rel="stylesheet" type="text/css"/>
  <script src="lib/jquery.js" type="text/javascript"></script> 
  <script src="scripts/LocalSetting.js" type="text/javascript"></script>
  <script type="text/javascript">
	$(document).ready(function(){
		courseId = <?php echo $course_id; ?>;
		fromId = <?php echo $from_id; ?>;
		$('.acceptBtn').bind('click', acceptBtnClick);
		$('.cancelBtn').bind('click',function(){
			parent.$.fancybox.close();
		});
	});
	function acceptBtnClick() {
		if($('#coursePassword').val() == ''){
			$('.errorWrapper').show();
			$('#errorEcho').html('验证密码不能为空');
			return;
		}
		$('.errorWrapper').hide();
		$.post(
			Root + 'send_course_apply.php', {
				from_id: fromId, 
				course_id: courseId,
				apply_content: '无',
				password: $('#coursePassword').val()
			},
			function(retData) {
				if(retData == '1'){
					$('.errorWrapper').hide();
					alert('恭喜，加入课程成功');
					parent.doHash();
					parent.$.fancybox.close();
				} else {
					$('.errorWrapper').show();
					$('#errorEcho').html(retData);
				}
			}
		);
	}
    
  </script>
</head>
<body class="fancyboxWrapper">
	<p>*加入该课程需要输入验证密码 <br /> *一般老师会在上课的时候将课程密码告诉同学们</p>
   <!--<form action='includes/interface/send_course_apply.php' method='post'>-->
   <table class="designedTable">
   <tr>
		<td>验证密码:</td><td>
		<input id="coursePassword" type='text' name='password' autocomplete="off"/></td>
   </tr>
   </table>
	<div class="buttonWrapper">		
		<input type='submit' class="acceptBtn" value=""  />
		<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
<!--</form>-->
 <div class="errorWrapper">
	<label id="errorEcho" style="font-size:12px;" >添加失败，我也不知道是什么原因</label>
</div>
</body>
</html>
