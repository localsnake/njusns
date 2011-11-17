<?php
/** 
 * This file contains all the user msg functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage msg
 */
	
  /**
   * the function to get msg info by id
   * @param integer $msg_id
   * @param mixed $conn
   * @return mixed the msg info object
   */ 
  function get_msg_info_by_id($msg_id,$conn){
  	$sel_result = $conn->query("SELECT * FROM sns_msg_info WHERE msg_id = $msg_id");
  	if($sel_result) return $sel_result->fetch_object();
  	else return null;
  }
  
  /**
   * the function to get msg info by group id
   * @param integer $group_id
   * @param mixed $conn
   * @return mixed the msg info object array
   */ 
  function get_msg_info_by_group($group_id,$conn){
  	$sel_result = $conn->query("SELECT * FROM sns_msg_info WHERE group_id = $group_id");
  	return get_sel_object_array($sel_result);
  }
  
  /**
   * the function to get group count
   * @param integer $gourp_id
   * @param mixed $conn
   * @return integer group_count
   */ 
  function get_group_count($group_id,$conn){
  	$sel_result = $conn->query("SELECT COUNT(*) as group_count FROM sns_msg_info WHERE group_id=$group_id");
  	return $sel_result->fetch_object()->group_count;
  }
  
  /**
   * get inbox msg read status by group_id and user_id
   * @param integer $group_id
   * @param integer $user_id
   * @param mixed $conn
   * @return integer whether read 1:no-read 0:read
   */ 
  function get_inbox_read_status($group_id,$user_id,$conn){
  	$sel_result = $conn->query("SELECT msg_id FROM sns_msg_info 
	  			WHERE group_id=$group_id AND receiver_id=$user_id AND read_status=1");
    if($sel_result->num_rows) return 1; 
	else return 0; 
  }
  
  
  
 ?>