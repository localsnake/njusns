<?php
/**
 * An Interface of del user news
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage user
 */
 
  require_once 'sns_fns.php';
  session_start();
  if(!check_valid_user()){
  	echo 0;
  	exit;
  }
  $user_id = $_SESSION['user_id'];
  
  $news_id = addslashes(trim($_REQUEST['news_id']));
  $conn = db_connect();
  if(del_user_news($news_id,$user_id,$conn)){
  	echo 1;	// echo 'Done';
  }	else{
  	echo 0;	// echo 'Fail';
  }
?>