<?php

/**
 * Interface to set course teacher assistant
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
  session_start();
  require_once('sns_fns.php');
  $user_id=$_SESSION['user_id'];
  $course_id = addslashes(trim($_POST['course_id']));
  
  $ta_emails = array();
  $ta_emails[0] = addslashes(trim($_POST['email0']));
  $ta_emails[1] = addslashes(trim($_POST['email1']));
  $ta_emails[2] = addslashes(trim($_POST['email2']));
  $ta_emails[3] = addslashes(trim($_POST['email3']));
  $ta_emails[4] = addslashes(trim($_POST['email4']));
  
  $conn = db_connect(); 
  
  do_html_header("设置助教结果");
  if(!$course_id){
	echo '课程不存在';
	exit;
  }
  if(!is_course_teacher($user_id,$course_id,$conn)){
	echo '您无权设置该课程的助教，请与创立课程的老师联系';
	exit;
  }
  if(!$ta_emails[0] && !$ta_emails[1] && !$ta_emails[2] && !$ta_emails[3] && !$ta_emails[4] ) {
	del_user_course_ta($course_id,$conn);
	echo '所有以前的助教已经删除。<br />';
	exit;
  }
  ?>
		<script type="text/javascript">
			parent.$('#courseTAName').html('暂未指定');
		</script>
  <?php
  del_user_course_ta($course_id,$conn);
  echo '设定助教结果如下: <br />';
  $total_count = 0;
  for($i=0;$i<=4;$i++){
	$ta_email = $ta_emails[$i];
	if(!$ta_email || $ta_email == '' || $ta_email == null)	continue;
	$ta_id= get_user_id_by_email($ta_email,$conn);
	if($ta_id == 0){
		echo "用户账号:$ta_email 不存在 <br />";
		continue;
	}
	$relation = get_user_course_relation($ta_id,$course_id,$conn);
	if($relation == 'M'){
		echo "$ta_email 已经是该课程的老师了 <br />";
		continue;
	}
	if($relation == 'T'){
		echo "$ta_email 已经是该课程的助教了 <br />";
		continue;
	}
	if(set_user_course_relation($ta_id,$course_id,'T',$conn)){
		$ta_info = get_user_base_info($ta_id,$conn);
		$ta_name = $ta_info->user_name;
		echo "成功添加助教 $ta_name [$ta_email]<br />";
		$total_count ++;
		?>
		<script type="text/javascript">
			var ta_id = <?php echo $ta_id; ?>;
			var ta_name = "<?php echo $ta_name; ?>";
			var ta0Info = '<a  name ="' + ta_id + '" class = "userName">' + ta_name + '</a>';
			if(parent.$('#courseTAName').text() == '暂未指定'){
				parent.$('#courseTAName').html(ta0Info);
			} else {
				parent.$('#courseTAName').append('&nbsp;');
				parent.$('#courseTAName').append(ta0Info);
			}
		</script>
		<?php
	} else {
		echo "添加账号 $ta_name 失败，数据库错误<br />";
	}
  }
  echo "您总共设定了 $total_count 个助教 <br />";
  echo '请手动关闭该窗口<br />';
  do_html_footer();
?>