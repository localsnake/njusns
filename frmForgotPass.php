<?php
/* The file of password frogot from
 * Version:@(#)forgot_form.php
 * Author: Runwei Qiang
 * Create Date: 2011/5/30
 * File Format: UTF8
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>忘记密码</title>
  <link href="css/index.css" rel="stylesheet" type="text/css"/>
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
   <form action='includes/interface/forgot_password.php' method='post'>
   <table class="designedTable">
   <tr>
		<td>账号名称(邮箱):</td>
		<td><input id="loginEmail" type='text' name='email' autocomplete="off"/></td>
   </tr>
   <tr>
		<td>验证码:</td><td><input type="text" name="vcode" /></td>
   </tr>
   <tr>
		<td></td><td><img src="includes/interface/generate_vcode.php" /></td></tr>
   </table>

	<div class="buttonWrapper">		
		<input class="acceptBtn" type='submit' value="" />
	</div>
</form>
</body>
</html>
