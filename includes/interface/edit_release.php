<?php

/**
 * The interface to edit release discussion in discussion area
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
 
 session_start();
 require_once('connectdb.php');
 $user_id=$_SESSION['user_id'];

 $release_title = addslashes(trim($_POST['discussion_release_title']));
 $release_content = addslashes(trim($_POST['discussion_release_content']));
 $discussion_release_id = addslashes(trim($_POST['discussion_release_id']));
 $release_time = date("Y-m-d H:i:s");
 
 $conn=connectdb();
 $query="UPDATE sns_course_discussion_release
 			SET discussion_release_time='$release_time',
 			discussion_release_content = '$release_title',
 			discussion_release_content = '$release_content'
 			WHERE 
			 discussion_release_id = $discussion_release_id AND
			 user_id = $user_id";
 $stmt=$conn->query($query);
 if($stmt) {
 	echo 1;
 }else {
 	echo 'Database Error: ',$query;
 }
 
?>