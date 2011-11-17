<?php

/**
 * @author qianyu
 * @copyright 2011
 */

 session_start();
 require_once('sns_fns.php');
 $user_id=$_SESSION['user_id'];
 $suggestion = strip_tags(addslashes(trim($_POST['user_suggestion'])));
 if(!check_valid_user()){
    echo '您尚未登录';
    exit;
  }
 //do_html_header('谢谢您的建议');
 if(!$suggestion) {
	echo '内容不能为空';
	exit;
 }
 $flag='N';
 $datetime=date("Y-m-d H:i:s");
 $conn = db_connect();
 $query="insert into sns_suggestion(user_id,suggestion_content,suggestion_time,flag) values
    ($user_id,'$suggestion','$datetime','$flag')";
 $result=$conn->query($query);
 if($conn->affected_rows>0)
    echo 1;  //'建议发送成功了，谢谢您的参与。';
 else
    echo "数据库错误，请您稍后再试";
?>