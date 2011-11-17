<?php
/**
 * This file contains all the database functions
 * 
 * @author	qianyu <yangzhouqianyu@sina.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage db
 */
 
	require_once('LocalSettings.php');
	/* The function to conncet the database */
	function connectdb() {
	   $result = new mysqli(DBserver, DBuser, DBpassword, DBname); 
	   $result->set_charset("utf8");
	   if (!$result) 
		 echo '连接数据库失败';       // May change the error method here        
	   else 
		 return $result;
	}
  
?>