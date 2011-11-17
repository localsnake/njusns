<?php

/**
 * The interface to veiw discussion area release response
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 session_start();
 require_once ('sns_fns.php');
 if(!check_valid_user()){	//ÊÇ·ñµÇÂ¼
    echo 0;
    exit;
 }
 $discussion_release_id = addslashes($_REQUEST['discussion_release_id']);
 $cur_page = addslashes($_REQUEST['cur_page']);
 $pagesize = ResponseListPageSize;		// the pagesize of every refresh
 if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
 }
 $offset = $pagesize * ($cur_page - 1);
 
 $conn=db_connect();
 
 $query="SELECT * FROM sns_course_discussion_response where discussion_release_id=$discussion_release_id
 		 ORDER BY discussion_response_id ASC LIMIT $offset,$pagesize";
 $result=$conn->query($query);
 $num_of_response=$result->num_rows;
 $response_area=array();
 $cur_floor = $offset + 1;
 for($i=0;$i<$num_of_response;$i++) {
    $element=$result->fetch_assoc();
    $response_area[$i]['discussion_release_id']=$element['discussion_release_id'];
    $response_area[$i]['discussion_response_id']=$element['discussion_response_id'];
    $response_area[$i]['discussion_response_time']=$element['discussion_response_time'];
    $response_area[$i]['discussion_response_content']=$element['discussion_response_content'];
    $user_id = $element['user_id'];
    $user_info = get_user_base_info($user_id,$connn);
    $response_area[$i]['discussion_response_user']=$user_info->user_name;
    $response_area[$i]['discussion_response_user_id']=$user_id;
	$response_area[$i]['discussion_response_user_photo'] = name_to_path_thumb($user_info->user_photo);
	$response_area[$i]['discussion_response_floor'] = $cur_floor++;
	
    //echo $response_area[$i]['discussion_response_content'];
 }
 echo json_encode($response_area);

?>