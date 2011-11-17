<?php
/** This file contains all the database functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage db
 */
 

/**
 * The function to conncet the database
 * @return the database connection 
 */
  function db_connect()	{
   @ $result = new mysqli(DBserver, DBuser, DBpassword, DBname); 
   $result->set_charset("utf8");
   if (!$result) {
     echo 'Database conn Fail';       // 连接数据库失败
   } else {
     return $result;
   }
  }

/**
 * get sel result object array
 * @param mixed $sel_result the database sel qeury result
 * @return mixed the sel result object array
 */
  function get_sel_object_array($sel_result){
  	$result = array();
    for($count=0;$row = $sel_result->fetch_object();$count++){
 	  $result[$count] = $row;
    }
    return $result;
  }
?>
