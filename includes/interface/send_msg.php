<?php

/**
 * Interface to send a new msg or a msg apply
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 * @todo change receive_id to receive_ids then support muti receivers
 */
 
  session_start();
  require_once('sns_fns.php');
  if(!check_valid_user()){	
    echo 0;
    exit;
  }
  $user_id=$_SESSION['user_id'];
  
  $title = addslashes(trim($_POST['title']));
  $content = addslashes(trim($_POST['content']));
  $receiver_id = addslashes(trim($_POST['receiver_id']));
  $group_id = addslashes(trim($_POST['group_id']));
  
  //do_html_header('output');	//test
  if(!$title || !$content || !$receiver_id){
  	echo '内容不能为空';
  	exit;
  }
  if(strlen($title) > 100) {
	echo '抱歉，标题不能超过100字';
	exit;
  }
  if($user_id == $receiver_id){
  	echo '不能给自己发送站内信.';
  	exit;
  }
  
  $create_time = date("Y-m-d H:i:s");
  $conn = db_connect();
  $conn->autocommit(false);	//Turns off auto-commiting database modifications
  if(!check_user_exists($user_id,$conn)){
  	echo '该用户不存在';
  	exit;
  }
  
  $ins_query1 = "INSERT INTO sns_msg_info (title,content,create_time,sender_id,receiver_id) VALUES
  					('$title','$content','$create_time',$user_id,$receiver_id)";
  $ins_res1 = $conn->query($ins_query1);
  $msg_id = $conn->insert_id;
  if(!$group_id || $group_id=='') {
  	$group_id = $msg_id;	// a new msg
  	$update_res1 = true;
  }
  
  if(!is_numeric($group_id)) {
	echo '发送失败[group_id错误]，请刷新后重新尝试';
	$conn->rollback();
	exit;
  }
  
  /*else {	// a new reply 
  	$update_res1 = $conn->query("UPDATE sns_msg_info SET read_status=1 WHERE msg_id=$group_id");
  }*/
  $update_res2 = $conn->query("UPDATE sns_msg_info SET group_id = $group_id WHERE msg_id=$msg_id");
  
  if($ins_res1 && $update_res2){
  	$conn->commit();
  	echo $msg_id;
  } else {
  	$conn->rollback();
  	echo "Database Error";	 // database error
  }
  $conn->close();
?>