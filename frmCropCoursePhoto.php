<?php
/* An IFrame of pic upload
 * Version:@(#)crop_pic_frame.php
 * Author: Runwei Qiang
 * Create Date: 2011/6/12
 * File Format: UTF8
 */
  session_start();
  $course_id = $_REQUEST['course_id'];
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>截取课程小头像</title>
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
				Root+"upload_course_pic.php?act=process", {
					course_id: <?php echo $course_id;?>,
					x1: $('input[name=x1]').val(), 
					x2: $('input[name=x2]').val(), 
					y1: $('input[name=y1]').val(), 
					y2: $('input[name=y2]').val()
				},
				function(data) {
					if (data < 0) {
						alert("没有返回正确的id，叫我如何是好？！");
					} 
					else {
						$.getJSON(
							Root+"get_courseinfo.php", {
								course_id: <?php echo $course_id;?>
							},
							cropPicFrameSubmitCallback
						);
					}
				}
			);
		}
		function cropPicFrameSubmitCallback(data) {
			if (data == "0") {
				$('#errorEcho label').text("上传失败");
				$('#errorEcho').show();
			}
			else {
				parent.$('#imgLarge').attr('src', data['course_photo_large']);
				//parent.$('#imgSmall').attr('src', data['course_photo']);
				parent.$.fancybox.close();
			}
		}
		</script>
	</head>
	<body class="fancyboxWrapper">
		<p>请在大头像中拖动鼠标选取小头像</p>
		<p id="errorEcho" class="ui-state-error ui-corner-all err">
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<label></label>
		</p>
	  <img id="example" src="<?php echo $_SESSION['photo_path']; ?>"/>
	  <input type="hidden" name="x1" value="" />
	  <input type="hidden" name="y1" value="" />
	  <input type="hidden" name="x2" value="" />
	  <input type="hidden" name="y2" value="" /> 
	  <br /><br />
	  <input type="button" name="submit" id="submitBtn" class="acceptBtn" />
	</body>
</html>
