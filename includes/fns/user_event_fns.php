<?php
/**
 * This file contains all the user event operation functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage event
 */
  
  /**
   * The function to get user's whole event list
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return mixed the user's event object array
   */
  function get_event($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_event WHERE user_id=$user_id");
  	if(!$sel_result || $sel_result->num_rows < 1)
  		return null;
	return get_sel_object_array($sel_result);
  }
  
  
  /**
   * The function to save user's event
   * @param integer $user_id
   * @param integer $event_begintime
   * @param integer $event_endtime
   * @param string 	$event_type		normal/AW-id/AM-id
   * @param integer $event_content
   * @param mixed $conn database connection
   * @return integer the inserted event id
   */
  function save_event($user_id,$event_begintime,$event_endtime,$event_type,$event_content,$conn){
  	if(!isset($event_type) || $event_type==""){
  		$event_type = "normal";
  	}
  	if(!$conn) $conn = db_connect();
  	$insert_result = $conn->query("INSERT INTO sns_event 
	  				(user_id,event_begintime,event_endtime,event_type,event_content) VALUES
			($user_id,'$event_begintime','$event_endtime','$event_type','$event_content')");
	if(!$conn || $conn->affected_rows != 1) return -1;
	return $conn->insert_id;
  }
  
  /**
   * edit some existed event
   * @param integer $event_id
   * @param integer $user_id
   * @param datetime $event_begintime
   * @param datetime $event_endtime
   * @param string $event_type
   * @param string $event_content
   * @param mixed $conn database connection
   * @return boolean whether edited successfully
   */ 
  function edit_event($event_id,$user_id,$event_begintime,$event_endtime,
  							$event_type,$event_content,$conn){
	if(!$conn) $conn = db_connect();
  	if(!isset($event_type) || $event_type==""){
  		$event_type = "normal";
  	}
  	$set_result = $conn->query("UPDATE sns_event SET
					  event_begintime='$event_begintime',
					  event_endtime='$event_endtime',
					  event_type='$event_type',
					  event_content='$event_content'
					  WHERE event_id=$event_id AND user_id=$user_id");
	if(!$conn || $conn->affected_rows != 1) return false;
	return true;
  }
  
  /**
   * delete event by id
   * @param integer $event_id
   * @param integer $user_id
   * @param integer $conn database connection
   * @return boolean whether deleted successfuly
   */ 
  function del_event($event_id,$user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$del_result = $conn->query("DELETE FROM sns_event 
	  						WHERE event_id=$event_id AND user_id=$user_id");
	if(!$del_result || $conn->affected_rows != 1){
		return false;
	}
	return true;
  }
  
  /**
   * The function to get event id
   * @param integer $user_id
   * @param datetime $event_begintime
   * @param datetime $event_endtime
   * @param string $event_type
   * @param string $event_content
   * @param mixed $conn database connection
   * @return integer the event id
   */
  function get_event_id($user_id,$event_begintime,$event_endtime,
  					$event_type,$event_content,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_event WHERE
	  							user_id=$user_id AND
								  event_begintime='$event_begintime' AND
								  event_endtime='$event_endtime' AND
								  event_content = '$event_content'");
    if(!$sel_result || $sel_result->num_rows < 1){
    	return -1;
    } else{
    	$row = $sel_result->fetch_object();
    	return $row->event_id;
    }
  }
  
  /**
   * The function to delete assigment event when teacher del some assigment
   * @param integer $assignment_id
   * @param mixed $conn database connection
   * @return boolean whether delete successfully
   */
  function del_assignment_event($assignment_id,$conn) {
	if(!$conn) $conn = db_connect();
	$type = "AM-$assignment_id";
	$conn->query("DELETE FROM sns_event WHERE event_type='$type'");
	$type = "AW-$assignment_id";
	$conn->query("DELETE FROM sns_event WHERE event_type='$type'");
	return true;
  }

?>