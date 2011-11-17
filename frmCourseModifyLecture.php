<?php
	$course_id = $_REQUEST['course_id'];
	$id = $_REQUEST['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
  <link href="css/frmCourseModifyLecture.css" rel="stylesheet" type="text/css"/>
  
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
	<form id="courseModify" enctype="multipart/form-data" method="post" action='includes/interface/modify_lecture.php?course_id=<?php echo $course_id;?>&course_lecture_id=<?php echo $id;?>'>
	<table class="designedTable">
	<!--<tr>
		<th>修改标题</th>
		<td><input type="text" name="course_lecture_title" /></td></tr>
	<tr>-->
		<!--<th>修改章节</th>
		<td><select name="course_chapter" width='50'>
			<option>0</option><option>1</option><option>2</option><option>3</option><option>4</option>
			<option>5</option><option>6</option><option>7</option><option>8</option><option>9</option>
			<option>10</option><option>11</option><option>12</option><option>13</option><option>14</option>
			<option>15</option><option>16</option><option>17</option><option>18</option><option>19</option>
			<option>20</option><option>21</option><option>22</option><option>23</option><option>24</option>
		</select></td>
	</tr>-->
	<tr>
		<th><label for="file">修改附件</label></th>
		<td><input type="file" name="file" id="file" /></td>
	</tr>
	<tr>
		<td>修改原因:</td>
		<td><textarea name="message" id="" cols="30" rows="10"></textarea></td>
	</tr>
	</table>
	<div class="buttonWrapper">
			<input id='modifyLecture' type='submit' name="submit" value='' class="acceptBtn"/>
			<input id="cancel" type="button" name="submitCancel" value='' class="cancelBtn"/>
	</div>
	</form>
</body>
</html>
