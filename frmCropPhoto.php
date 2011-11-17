<?php
/**
 * An IFrame of pic upload
 * 
 * @version:@(#)navCropPhoto.php
 * @author: Runwei Qiang  2011/6/12
 */
  session_start();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>截取小头像</title>
		<link type="text/css" href="css/fancyboxStyle.css" rel="stylesheet" />	
		<link type="text/css" href="css/cropPhoto.css" rel="stylesheet" />	
		<script type="text/javascript" src="lib/jquery.js"></script>
		<script type="text/javascript" src="lib/jquery-ui-1.8.13.custom.min.js"></script>
		<script type="text/javascript" src="lib/jquery.imgareaselect.pack.js"></script>
		<link type="text/css" href="css/redmond/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
		<link type="text/css" href="css/imgareaselect-default.css" rel="stylesheet" />
		<script src="scripts/LocalSetting.js" type="text/javascript"></script>
		
		
		<script type="text/javascript">
		/* Add init js here */
		$(function(){
			$('#errorEcho').hide();
			$('#example').imgAreaSelect(
				{ maxWidth: 500, 
					maxHeight: 500, 
					minWidth:60,
					mimHeight:60,
					aspectRatio: '1:1',
					handles: true ,
					onSelectEnd: function (img, selection) {
						$('input[name=x1]').val(selection.x1);
						$('input[name=y1]').val(selection.y1);
						$('input[name=x2]').val(selection.x2);
						$('input[name=y2]').val(selection.y2);
					}
				}
			);
			$('#submitBtn').bind('click', cropPicFrameSubmit);
		});
		function cropPicFrameSubmit() {
			$.post(
				Root+"upload_pic.php?act=process", {
					x1: $('#x1').val(), 
					x2: $('#x2').val(), 
					y1: $('#y1').val(), 
					y2: $('#y2').val()
				},
				function(data) {
					if (data < 0) {
						alert("没有返回正确的id，叫我如何是好？！");
					}
					else {
						$.getJSON(
							Root+"view_info.php?type=all&user_id=" + data,
							cropPicFrameSubmitCallback
						);
					}
				}
			);
		}
		
		/* 得到用户资料的返回信息 */
		function cropPicFrameSubmitCallback(data) {
			if (data == "0") {
				$('#errorEcho label').text("上传失败");
				$('#errorEcho').show();
			}
			else {
				parent.$('#imgLarge').attr('src', data['user_photo_large']);
				// parent.$('#imgSmall').attr('src', data['user_photo']);
				parent.initUserBaseInfo();			  //刷新用户信息条头像
				parent.$.fancybox.close(); 
				
			}
		}
		</script>
	</head>
	<body class="fancyboxWrapper" style="overflow:visible;">
		<p>请在大头像中拖动鼠标选取小头像</p>
		<p id="errorEcho" class="ui-state-error ui-corner-all err">
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<label></label>
		</p>
	  <img id="example" src="<?php echo $_SESSION['photo_path']; ?>"/>
	<!--<form method='post' onsubmit="JavaScript: cropPicFrameSubmit();">-->
	  <input type="hidden" id="x1" name="x1" value="" />
	  <input type="hidden" id="x2" name="x2" value="" />
	  <input type="hidden" id="y1" name="y1" value="" />
	  <input type="hidden" id="y2" name="y2" value="" /> <br /> <br />
	<div class="buttonWrapper">
		<input type="button" name="submit" id="submitBtn" class="acceptBtn"/>
	</div>
	 <!--</form>-->
	</body>
</html>
