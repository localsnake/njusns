
<?php

/**
 * The interface to recommand course to user
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @author qianyu
 * @copyright 2011
 */

   session_start();
   require_once('sns_fns.php');
   do_html_header('hi');
   $user_id = $_SESSION['user_id'];
   
   $threshold_of_commonfirend=0;    // 好友推荐阈值
   $conn = db_connect();
   $recommand_friend=array();
   $num_of_recommand_friend=0;							  //推荐好友的个数
   
   // 推荐同院系好友
   $user_list=get_all_user_id($conn);
   $user_detail = get_user_detail_info($user_id,$conn);
   $department = $user_detail->user_department;
   for($i=0;$i<count($user_list);$i++) {
      $id=$user_list[$i]->user_id;
	  if(check_same_department($conn,$department,$id)) {
	    $recommand_friend[$num_of_recommand_friend]['user_id'] = $id;
	    $recommand_friend[$num_of_recommand_friend]['weight'] =  0;
	    $user_base = get_user_base_info($id,$conn);
	    $recommand_friend[$num_of_recommand_friend]['user_name'] = $user_base->user_name;
	    $recommand_friend[$num_of_recommand_friend]['user_gender'] = $user_base->user_gender;
		$recommand_friend[$num_of_recommand_friend]['user_type'] = $user_base->user_type;
		$recommand_friend[$num_of_recommand_friend]['user_hometown'] = $user_base->user_hometown;
	    $recommand_friend[$num_of_recommand_friend]['user_photo'] = name_to_path_thumb($user_base->user_photo);
	    $recommand_friend[$num_of_recommand_friend]['user_department'] = $user_detail->user_department;
		$recommand_friend[$num_of_recommand_friend]['user_major'] = $user_detail->user_major;
		$num_of_recommand_friend++;
	}
   }
   // 从共同好友出发推荐好友
   $friend_list = get_total_friend_list($user_id,$conn);  // 得到该用户的好友列表
   for($i=0;$i<count($friend_list);$i++) {
	  $friend_id = $friend_list[$i]->friend_id;
	  $friend_friend_list= get_total_friend_list($friend_id,$conn);   //好友的好友列表
	  for( $j=0; $j<count($friend_friend_list); $j++) {
	      $friend_friend_id = $friend_friend_list[$j]->friend_id;
		  
		  $num_of_samefriend=0;    // 记录有几个共同好友
		  for($k=0;$k<count($friend_list);$k++) {
		     $id=$friend_list[$k]->friend_id;
			 // 不是自己的好友但是好友的好友 
		     if(!is_friend($user_id,$friend_friend_id,$conn) && 
				is_friend($id,$friend_friend_id,$conn) && 
				$user_id != $friend_friend_id) {
			    $num_of_samefriend++;
			 }
		  }
		  if($num_of_samefriend > $threshold_of_commonfirend) {
		      $recommand_friend[$num_of_recommand_friend]['user_id'] = $friend_friend_id;
			  $recommand_friend[$num_of_recommand_friend]['weight'] =  $num_of_samefriend;
			  $user_base = get_user_base_info($friend_friend_id,$conn);
			  $recommand_friend[$num_of_recommand_friend]['user_name'] = $user_base->user_name;
			  $recommand_friend[$num_of_recommand_friend]['user_gender'] = $user_base->user_gender;
			  $recommand_friend[$num_of_recommand_friend]['user_type'] = $user_base->user_type;
			  $recommand_friend[$num_of_recommand_friend]['user_hometown'] = $user_base->user_hometown;
			  $recommand_friend[$num_of_recommand_friend]['user_photo'] = name_to_path_thumb($user_base->user_photo);
			  
			  $user_detail = get_user_detail_info($page_user_id,$conn);
			  $recommand_friend[$num_of_recommand_friend]['user_department'] = $user_detail->user_department;
			  $recommand_friend[$num_of_recommand_friend]['user_major'] = $user_detail->user_major;
			  
			  
			  $num_of_recommand_friend++;
          }
       }
	}
	echo json_encode($recommand_friend);
	
?>


