<?php
/**
 * An Interface of user view info
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
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];

  $page_user_id = addslashes(trim($_REQUEST['user_id']));
  $conn = db_connect();
  
  // check whether this user has permisson to view this page
  // only friends and himself can view the page
  if(is_friend($user_id,$page_user_id,$conn) || $user_id == $page_user_id){
  	$type = $_REQUEST['type'];		// get info type
	$user_base = get_user_base_info($page_user_id,$conn);
	$user_detail = get_user_detail_info($page_user_id,$conn);

  	if($type == 'all' || $type == 'base'){								//用户基本信息
  		$result['user_name'] = $user_base->user_name;
		$result['user_gender'] = $user_base->user_gender;
		$result['user_birthday'] = $user_base->user_birthday;
		$result['user_type'] = $user_base->user_type;
		$result['user_hometown'] = $user_base->user_hometown;
		$result['user_level'] = $user_base->user_level;
		$result['user_photo'] = name_to_path_thumb($user_base->user_photo);
		$result['user_photo_large'] = name_to_path_large($user_base->user_photo);
		$result['user_status'] = $user_base->user_status;
  	}
  	if($type == 'all' || $type == 'school'){		//用户学校信息
  		$result['user_department'] = $user_detail->user_department;
  		$result['user_major'] = $user_detail->user_major;
  		$result['user_dorm_no'] = $user_detail->user_dorm_no;
  	}
  	if($type == 'all' || $type == 'hobby'){			//用户兴趣信息
  		$result['user_hobby'] = $user_detail->user_hobby;
  		$result['user_music'] = $user_detail->user_music;
  		$result['user_films'] = $user_detail->user_films;
  		$result['user_sports'] = $user_detail->user_sports;
  		$result['user_books'] = $user_detail->user_books;
  	}
  	if($type == 'all' || $type == 'contact'){		//用户联系信息
  		$result['user_contact_email'] = $user_detail->user_contact_email;
  		$result['user_qq'] = $user_detail->user_qq;
  		$result['user_msn'] = $user_detail->user_msn;
  		$result['user_phone'] = $user_detail->user_phone;
  	}
	echo json_encode($result);
  } else{	// default 
  	$user_base = get_user_base_info($page_user_id,$conn);
  	$result['user_name'] = $user_base->user_name;
	$result['user_gender'] = $user_base->user_gender;
	$result['user_type'] = $user_base->user_type;
	$result['user_hometown'] = $user_base->user_hometown;
	$result['user_photo'] = name_to_path_thumb($user_base->user_photo);
	$result['user_photo_large'] = name_to_path_large($user_base->user_photo);
	echo json_encode($result);
  }  
?>