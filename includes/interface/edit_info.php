<?php
/** 
 * An Interface of user edit info
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
  
  session_start();
  require_once('sns_fns.php');
  
  if(!check_valid_user()){	
  	echo 0;	//echo '您尚未登录';
  	exit;
  }
  $user_id = $_SESSION['user_id'];
  $type = $_POST['type'];		// get info type
  if(!$type) { 
  	$type = 'all';
  }
  if($type == 'all' || $type == 'base') {
	  $username = addslashes(trim($_POST['username'])); 
	  $gender = addslashes(trim($_POST['gender']));
	  $birthday = addslashes(trim($_POST['birthday']));
	  $hometown = addslashes(trim($_POST['hometown']));
	  if(!$username || !$gender || !$birthday || !$hometown) {
		echo '用户名，性别，生日，家乡信息均不能为空';
		exit;
	  }
	  if(!valid_date($birthday)){
	  	echo '生日格式错误，应该类似1990-01-01.';
	  	exit;
	  }
  }
  // school info
  if($type == 'all' || $type == 'school') {
	  $department = addslashes(trim($_POST['department']));
	  $major = addslashes(trim($_POST['major']));
	  $dorm_no = addslashes(trim($_POST['dorm_no']));
  }
  // hobby info
  if($type == 'all' || $type == 'hobby'){ 
	  $hobby = addslashes(trim($_POST['hobby']));
	  $music = addslashes(trim($_POST['music']));
	  $films = addslashes(trim($_POST['films']));
	  $sports = addslashes(trim($_POST['sports']));
	  $books = addslashes(trim($_POST['books']));
  }
  // contact info 
  if($type == 'all' || $type == 'contact') {
	  $contact_email = addslashes(trim($_POST['contact_email']));
	  $qq = addslashes(trim($_POST['qq']));
	  $msn = addslashes(trim($_POST['msn']));
	  $phone = addslashes(trim($_POST['phone']));
	  // Check format Here
	  if($contact_email && !valid_contact_email($contact_email)){
	  	echo "Email Format Error.";
	  	exit;
	  }
  }
  
  $conn = db_connect();
  switch($type){
  	case 'all':
  		if(edit_user_info($user_id,$username,$gender,$birthday,$hometown,
  			$department,$major,$dorm_no,$hobby,$music,$films,$sports,$books,
  			$contact_email,$qq,$msn,$phone,$conn)){
		  	echo 1;
        }	else {
			echo 'Database Error in edit_user_info';
	    }
  		break;
	case 'base':
		if(edit_user_base_info($user_id,$username,$gender,$birthday,$hometown,$conn)){
		  	echo 1;
        }	else {
			echo 'Database Error in edit_user_base_info';
	    }
  		break;
	case 'school':
		if(edit_user_school_info($user_id,$department,$major,$dorm_no,$conn)){
		  	echo 1;
        }	else {
			echo 'Database Error in edit_user_school_info';
	    }
		break;
	case 'hobby':
		if(edit_user_hobby_info($user_id,$hobby,$music,$films,$sports,$books,$conn)){
		  	echo 1;
        }	else {
			echo 'Database Error in edit_user_hobby_info';
	    }
		break;
	case 'contact':
		if(edit_user_contact_info($user_id,$contact_email,$qq,$msn,$phone,$conn)){
		  	echo 1;
        }	else {
			echo 'Database Error in edit_user_contact_info';
	    }
	    break;
  }