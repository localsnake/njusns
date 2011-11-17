<?php
/**
 * An Interface of user view freshmilk
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage user
 */
 
  session_start();
  require_once 'sns_fns.php';
  
  if(!check_valid_user()){
    echo 0;
    exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $lang = addslashes($_REQUEST['lang']);
  if(!$lang)  $lang = 'zh-cn';
  
  $cur_page = addslashes($_REQUEST['cur_page']);
  $pagesize = FreshmilkPageSize;		// the pagesize of every refresh
  
  if(!isSet($cur_page) || $cur_page==null) {
  	$cur_page = 1;
  }
  $offset = $pagesize * ($cur_page - 1);
  
  $conn = db_connect();
  $freshmilk = get_freshmilk_type($user_id,'C',$offset,$pagesize,$conn);
  if($freshmilk == null){
  	echo '';	//'No Freshmilk Found';
  }else{
  	// assign the output here
  	$result = array();
	for($i=0;$i<count($freshmilk);$i++)	{
		$freshmilk_id = $freshmilk[$i]->freshmilk_id;
		$freshmilk_type = $freshmilk[$i]->freshmilk_type;
		$freshmilk_content = $freshmilk[$i]->freshmilk_content;
		$freshmilk_time =  $freshmilk[$i]->freshmilk_time;
		$from_id =  $freshmilk[$i]->from_id;
		
		$result[$i]['freshmilk_id'] = $freshmilk_id;
		$result[$i]['freshmilk_time'] =  date('Y-m-d H:i',strtotime($freshmilk_time));
		
		if($lang == 'en')	$freshmilk_content = translate_freshmilk($freshmilk_content);
		$result[$i]['freshmilk_content'] = $freshmilk_content;
		
		$result[$i]['freshmilk_type'] = $freshmilk_type;
		$result[$i]['from_id'] = $from_id;
		
		if($freshmilk_type == 'U'){
			$from_user_info = get_user_base_info($from_id,$conn);
			$result[$i]['from_name'] = $from_user_info->user_name;
			$result[$i]['from_photo'] = name_to_path_thumb($from_user_info->user_photo);
		}	else if($freshmilk_type == 'C'){			//Course Info
			$from_course_info = get_course_info($from_id,$conn);
			$result[$i]['from_name'] = $from_course_info->course_name;
			$result[$i]['from_photo'] = name_to_path_thumb_course($from_course_info->course_photo);
		}
	}
	echo json_encode($result);
  }
?>