<?php
/** 
 * An Interface of user auto complete mail
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
	"$name@smail.nju.edu.cn",
	"$name@nju.edu.cn",
);
foreach ($items as $key) {
	echo "$key\n";
}
?>