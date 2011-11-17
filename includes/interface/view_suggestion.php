<?php

/**
 * An Interface of view suggestion
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage suggestion
 */
 require_once('sns_fns.php');
 //do_html_header('查看建议');
 $conn = db_connect();
 $query="SELECT * FROM sns_suggestion ORDER by suggestion_time DESC";
 $result=$conn->query($query);
 $num=$result->num_rows;
 $suggestion=array();

 //echo '<table>';
 for($i=0;$i<$num;$i++) {
    $element=$result->fetch_assoc();
    $suggestion[$i]['user_id']=$element['user_id'];
	$user_info = get_user_base_info($element['user_id'],$conn);
	$suggestion[$i]['user_name']=$user_info->user_name;
    $suggestion[$i]['suggestion_content']=$element['suggestion_content'];
    $suggestion[$i]['suggestion_time']=$element['suggestion_time'];
    $suggestion[$i]['flag']=$element['flag'];
	
	$user_name = $user_info->user_name;
    $suggestion_content = $element['suggestion_content'];
    $suggestion_time = $element['suggestion_time'];
	//echo "<tr><td> $user_name </td><td> $suggestion_content </td> <td> $suggestion_time </td></tr>";
 }
 //echo '</table>';
 echo json_encode($suggestion);
?>