<?php
/** 
 * An Interface of user auto complete department
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */

$q = strtolower(trim($_GET["q"]));
if (!$q) return;
list($name,$email) = split('@',$q);
$items = array(
	"计算机科学与技术系",
	"物理学院"
);
foreach ($items as $key) {
	if (strpos(strtolower($key), $q) !== false) {
		echo "$key\n";
	}
}

?>