<?php
/**
 * This file contains all the user friend operation functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage friend
 */

  /** Check if two users are friends
   * @param integer $user_id 
   * @param integer $friend_id
   * @param mixed $conn database connection
   * @return boolean whether they are friends
   */
  function is_friend($user_id,$friend_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_friend WHERE 
	  			user_id=$user_id AND friend_id=$friend_id");
	if($sel_result->num_rows > 0){
		return true;
	}	else {
		return false;
	}
  }
  
 /**
  * Check whether the apply has been sent 
  * @param integer $from_id
  * @param integer $to_id
  * @param mixed $conn database connection
  * @return boolean whether sent
  */
  function user_apply_sent($from_id,$to_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_apply WHERE 
	  			from_id=$from_id AND to_id=$to_id");
	if($sel_result->num_rows > 0){
		return true;
	}	else {
		return false;
	}
  }
  
  /**
   * The function to delete friend  by id from database
   * @param integer $user_id
   * @param integer $friend_id
   * @param mixed $conn database connection
   * @return boolean whether deleted
   */
   function del_friend($user_id,$friend_id,$conn){
     if(!$conn) $conn = db_connect();
     $conn->query("DELETE FROM sns_user_friend WHERE 
	 				(user_id=$user_id AND friend_id=$friend_id)  OR
					 (user_id=$friend_id AND friend_id=$user_id)
				 ");
	 if($conn->affected_rows == 2){
	 	return true;
	 }
	 return false;
   }
   
  /**
   * The function to search user using keyword in the database
   * @param integer $user_id
   * @param string $keyword
   * @param mixed $conn database connection
   * @param mixed the related friend id array
   */
  function search_friend($user_id,$keyword,$conn){
  	if(!$conn) $conn = db_connect();
	$searchResult = $conn->query("SELECT * FROM sns_user_friend WHERE 
									user_id=$user_id AND friend_id IN
									(SELECT user_id FROM sns_user_base WHERE
 										user_name LIKE '%$keyword%')");
	if(!$searchResult || $searchResult->num_rows < 1){
		return null;	// No Reuslt Found
	}
	return get_sel_object_array($searchResult);
  }
  
  
  /**
   * The function to get friend list from database 
   * @param integer $user_id
   * @param integer $offset		the offset of this selection
   * @param integer $pagesize 	the page size of each selection
   * @return mixed  my friend id array
   */
  function get_friend_list($user_id,$offset,$pagesize,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_friend 
	  							WHERE user_id=$user_id
	         					LIMIT $offset,$pagesize");
  	if(!$sel_result || $sel_result->num_rows < 1)
	  return null;	// No friend found
    $result = array();
    for($count=0; $row = $sel_result->fetch_object(); $count++) {
   	  $result[$count] = $row;
    }
    return $result;
  }
  
  function get_total_friend_list($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_friend 
	  							WHERE user_id=$user_id");
  	if(!$sel_result || $sel_result->num_rows < 1)
	  return null;	// No friend found
    $result = array();
    for($count=0; $row = $sel_result->fetch_object(); $count++) {
   	  $result[$count] = $row;
    }
    return $result;
  }

?>