<?php
/**
 * This file contains all the output functions show html page, Just For My Test
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage output
 */

  /** 
   * The function to change page to $url
   * 
   * @param string $url the url header to
   * @param integer $seconds the delay time default to be 0
   */
  function goto_page($url,$seconds=0){
  	$seconds *= 1000;
	echo "<script language='javascript' type='text/javascript'>"; 
	echo "window.setTimeout(window.location.href='$url',$seconds);"; 
	echo "</script>"; 
  }

  /**
   * The function to print an HTML header
   * @param string $title The header title
   */
  function do_html_header($title) {
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	  <link href="../../css/fancyboxStyle.css" rel="stylesheet" type="text/css"/>
	  <link href="../../css/forgotPswSendMail.css" rel="stylesheet" type="text/css"/>
	  <title>
	  <?php echo $title;?>
	  </title>
	</head>
	<body class="fancyboxWrapper">
<?php
  }

  /**
   * print html footer
   */ 
  function do_html_footer(){
?>	</body>
	</html>
<?php
  }
?>
