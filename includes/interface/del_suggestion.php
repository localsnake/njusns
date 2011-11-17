<?php

/**
 * @author qianyu
 * @copyright 2011
 */
 require_once('sns_fns.php');
 $conn = db_connect();
 $suggestion_id=$_REQUEST['suggestion_id'];
 $query="DELETE FROM sns_suggestion WHERE suggestion_id=$suggestion_id";
 $result=$conn->query($query);
 if($conn->affected_rows==1)
    echo 1;
 else
    echo 0;
?>