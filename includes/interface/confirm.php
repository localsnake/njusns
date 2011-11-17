<?php
/** 
 * An Interface of user confirm, let the user status be I/O if cofirm OK
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage user
 */
 
  require_once 'sns_fns.php';
  
  $user_id = addslashes(trim($_GET['user_id']));
  $confirm = addslashes(trim($_GET['confirm']));
  
  $conn = db_connect();
  $result = user_confirm($user_id,$confirm,$conn);
  do_html_header("用户验证");
  if($result) {
  	echo '验证成功，3秒后页面将自动跳转到登陆页面';
  	goto_page(ServerAddr."index.php",3);
  } else {
  	echo '验证失败了,您可以尝试再次注册';
  }
  do_html_footer();
?>