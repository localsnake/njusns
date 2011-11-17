<?php

/**
 * The interface to view discussion area release
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 session_start();
 require_once('sns_fns.php');
 if(!check_valid_user()){	//ÊÇ·ñµÇÂ¼
    echo 0;
    exit;
 }
 
 $discussion_area_id = addslashes(trim($_REQUEST['discussion_area_id']));
 $cur_page = addslashes($_REQUEST['cur_page']);
 $pagesize = ReleaseListPageSize;		// the pagesize of every refresh
 if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
 }
 $offset = $pagesize * ($cur_page - 1);
 
 $conn=db_connect();
 $query="SELECT * FROM sns_course_discussion_release 
 					WHERE discussion_area_id=$discussion_area_id 
					 ORDER BY discussion_release_id DESC LIMIT $offset,$pagesize";
 $result=$conn->query($query);
 $num_of_release=$result->num_rows;
 $release_area=array();
 for($i=0;$i<$num_of_release;$i++) {
   $element=$result->fetch_assoc();
   $release_area[$i]['discussion_area_id']=$element['discussion_area_id'];
   $release_area[$i]['discussion_release_id']=$element['discussion_release_id'];
   $release_area[$i]['discussion_release_time']=$element['discussion_release_time'];
   $release_area[$i]['discussion_release_title']=$element['discussion_release_title'];
   $release_area[$i]['discussion_release_content']=$element['discussion_release_content'];
   $release_area[$i]['discussion_response_num']=$element['discussion_response_num'];
   $user_id = $element['user_id'];
   $user_info = get_user_base_info($user_id,$conn);
   $release_area[$i]['discussion_release_user_id']=$user_id;
   $release_area[$i]['discussion_release_user']=$user_info->user_name;
   $release_area[$i]['discussion_release_user_photo'] = name_to_path_thumb($user_info->user_photo);
 }
 echo json_encode($release_area);
?>