<?php
/**
 * An Interface of user view user_news
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  session_start();
  require_once 'sns_fns.php';
  if(!check_valid_user()){	
    echo 0;
    exit;
  }
  $user_id = addslashes(trim($_REQUEST['user_id']));
  $lang = addslashes($_REQUEST['lang']);
  
  $conn = db_connect();
  if($user_id != $_SESSION['user_id'] 
  			&& !is_friend($user_id,$_SESSION['user_id'],$conn)){
  	echo 0;
  	exit;
  }
  
  $pagesize = UserNewsPageSize;		// the pagesize of every refresh
  $cur_page = addslashes($_REQUEST['cur_page']);
  if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
  }
  $offset = $pagesize * ($cur_page - 1);
  
  $user_news = get_user_news($user_id,$offset,$pagesize,$conn);
  if($user_news == null){
  	echo '';
  }	else{
  	// assign the result array
	$result = array();
	$user_base_info = get_user_base_info($user_id,$conn);
	for($i=0;$i<count($user_news);$i++)	{
		$result[$i]['news_id'] = $user_news[$i]->news_id;
		$content =  $user_news[$i]->news_content;
		if($lang == 'en')	$content = translate_freshmilk($content);
		$result[$i]['news_content'] = $content;
		$result[$i]['news_time'] = $user_news[$i]->news_time;
		$result[$i]['user_name'] = $user_base_info->user_name;
		$result[$i]['user_photo'] = name_to_path_thumb($user_base_info->user_photo);
	}
	echo json_encode($result);
  }
?>