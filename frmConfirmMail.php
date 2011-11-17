<?php
/**
  * The file of password frogot from
  * 
  * @author QiangRunwei <qiangrw@gmail.com>
  * @copyright LocalsNake Net League 2011
  * @package output
  */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>忘记密码</title>
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
	$().ready(function() {
		$('#loginEmail').autocomplete( Root+'automail.php', {
			width: 195,
			selectFirst: false,
			scroll: false
		});
	});
  </script>
</head>
<body class="fancyboxWrapper">
   <form action='includes/interface/send_confirm_mail.php' method='post'>
   <table class="designedTable">
   <tr>
		<td>注册邮箱:</td><td>
		<input id="loginEmail" type='text' name='email' autocomplete="off"/></td>
   </tr>
   <tr>
		<td>验证码:</td><td><input type="text" name="vcode" /></td>
   </tr>
   <tr>
		<td></td><td><img src="includes/interface/generate_vcode.php" /></td></tr>
   </table>
	<div class="buttonWrapper">		
		<input type='submit' class="acceptBtn" value=""  />
	</div>
</form>
</body>
</html>
