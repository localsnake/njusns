<?php
/** 
 * This file contains all the course discussions functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage course
 */

  /**
   * the function to delete discussion area from database by id
   * 
   * @param integer $course_id
   * @param integer $discussion_area_id
   * @param mixed $conn database connection
   */    
  function del_discussion_area($course_id,$discussion_area_id,$conn){
	$conn->query("DELETE FROM sns_course_discussion WHERE course_id=$course_id AND discussion_area_id=$discussion_area_id");
	if($conn->affected_rows != 1) {
		return false;
	}
	return true;
  }
  
  /**
   * fuction to delete discussion area release by id
   * 
   * @param integer $discussion_release_id
   * @param integer $user_id
   * @param mixed $conn
   * @return boolean whether deleted
   */
  function del_discussion_release($discussion_release_id,$user_id,$conn){
  	$conn->query("DELETE FROM sns_course_discussion_release WHERE
	  				discussion_release_id=$discussion_release_id
					   AND user_id = $user_id");
    if($conn->affected_rows == 1) return true;
    else return false;
  }
  
  /**
   * the function to delete discussion response by id
   * @param integer $discussion_response_id
   * @param integer $user_id
   * @param mixed $conn
   * @return boolean whether deleted
   */ 
  function del_discussion_response($discussion_response_id,$user_id,$conn){
    $sel_result=$conn->query("SELECT * FROM sns_course_discussion_response WHERE
	  				discussion_response_id=$discussion_response_id
					   AND user_id = $user_id");
	if($sel_result->num_rows > 0) {
	  $sel_element=$sel_result->fetch_assoc();
	  $release_id=$sel_element['discussion_release_id'];
	}
	else return false;
  	$conn->query("DELETE FROM sns_course_discussion_response WHERE
	  				discussion_response_id=$discussion_response_id
					   AND user_id = $user_id");
    if($conn->affected_rows == 1) {
	    $sel_result=$conn->query("SELECT * FROM sns_course_discussion_release WHERE discussion_release_id=$release_id");	
		$sel_element=$sel_result->fetch_assoc();
		$num=$sel_element['discussion_response_num'];
		$conn->query("UPDATE sns_course_discussion_release SET discussion_response_num=$num-1 WHERE discussion_release_id=$release_id");
		return true;
	} else {
		return false;
	}
  }
  
  /**
    * get course_discussion_area_list by id
    * @param integer $course_id
    * @param mixed $conn database connection
    * @return mixed the discussion area info object array
    */
   function get_course_discussion_area_list($course_id,$conn){
     $query="SELECT * FROM sns_course_discussion where course_id=$course_id";
     $sel_result = $conn->query($query);
     return get_sel_object_array($sel_result);
   }
   
   /**
   * get discussion area release count 
   * @param integer $discussion_area_id
   * @param mixed $conn
   * @return integer discussion area count
   */ 
  function get_discussion_area_count($course_id,$conn){
	$query = "SELECT count(*) as my_count from sns_course_discussion 
							WHERE course_id=$course_id";
	$sel_result = $conn->query($query);
	$row = $sel_result->fetch_object();
	return $row->my_count;
  }
  
  
  /**
   * get discussion area release count 
   * @param integer $discussion_area_id
   * @param mixed $conn
   */ 
  function get_discussion_area_release_count($discussion_area_id,$conn){
	$query = "SELECT count(*) as my_count from sns_course_discussion_release where discussion_area_id = $discussion_area_id";
	$sel_result = $conn->query($query);
	$row = $sel_result->fetch_object();
	return $row->my_count;
  }
  
  /**
   * get discussion response release count 
   * @param integer $discussion_release_id
   * @param mixed $conn
   * @return integer discussion release count 
   */ 
  function get_discussion_area_response_count($discussion_release_id,$conn){
  	$query = "SELECT count(*) as my_count from sns_course_discussion_response where discussion_release_id=$discussion_release_id";
	$sel_result = $conn->query($query);
	$row = $sel_result->fetch_object();
	return $row->my_count;
  }
  
  
  
  
 ?>