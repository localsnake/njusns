<?php
/** 
 * An Interface of user auto complete major
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
	"hi"
	"hi"
);
foreach ($items as $key) {
	if (strpos(strtolower($key), $q) !== false) {
		echo "$key\n";
	}
}

?>