<?php

/**
 * The interface to edit response discussion in discussion area
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
 session_start();
 require_once('connectdb.php');
 $user_id = $_SESSION['user_id'];

 $discussion_response_id = addslashes(trim($_POST['discussion_response_id']));
 $response_content = addslashes(trim($_POST['discussion_response_content']));
 $response_time = date("Y-m-d H:i:s");

 $conn=connectdb();
 
 $query="UPDATE sns_course_discussion_response 
  		SET discussion_response_content='$response_content',
  		    discussion_response_time ='$response_time'
    	WHERE discussion_response_id = $discussion_response_id
		    AND user_id = $user_id";
 $stmt=$conn->query($query);
 if($conn->affected_rows != 1){
 	echo 'Database Error:',$query;
 } else {
 	echo 1;
 }
 
?>