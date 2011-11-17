<?php
/**
 * An Interface of friends search auto complete
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @author qianyu
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	interface
 * @subpackage search
 */
class my_Getpy
{
    var $_dat = 'py.dat';
    var $_fd  = false;

    function my_Getpy($pdat = '')
    {
        if ('' != $pdat)
            $this->_dat = $pdat;
    }

    function load($pdat = '')
    {
        if ('' == $pdat)
            $pdat = $this->_dat;

        $this->unload();
        $this->_fd = @fopen($pdat, 'rb');
        if (!$this->_fd)
        {
            trigger_error("unable to load PinYin data file `$pdat`", E_USER_WARNING);
            return false;
        }
        return true;
    }

    function unload()
    {
        if ($this->_fd)
        {
            @fclose($this->_fd);
            $this->_fd = false;
        }
    }

    function get($zh)
    {
        if (strlen($zh) != 2)
        {
            trigger_error("`$zh` is not a valid GBK hanzi", E_USER_WARNING);
            return false;
        }

        if (!$this->_fd && !$this->load())
            return false;

        $high = ord($zh[0]) - 0x81;
        $low  = ord($zh[1]) - 0x40;

        // º∆À„∆´“∆Œª÷√
        $nz = ($ord0 - 0x81);
        $off = ($high<<8) + $low - ($high * 0x40);

        // ≈–∂œ off ÷µ
        if ($off < 0)
        {
            trigger_error("`$zh` is not a valid GBK hanzi-2", E_USER_WARNING);
            return false;
        }

        fseek($this->_fd, $off * 8, SEEK_SET);
        $ret = fread($this->_fd, 8);
        $ret = unpack('a8py', $ret);
        return $ret['py'];
    }

    function _my_Getpy()
    {
        $this->_unload();
    }
}

  session_start();
  require_once ('sns_fns.php');
  if(!check_valid_user()){
 	return;
  }
  $user_id = $_SESSION['user_id'];
  $q = strtolower(addslashes($_REQUEST["q"]));
  if (!$q) return;
  
  $conn = db_connect();
  $friend_list = get_total_friend_list($user_id,$conn);
  $result = array();
  for($count = 0; $count<count($friend_list); $count++){
	  $find_friend_id =  $friend_list[$count]->friend_id;
	  $friend_info = get_user_base_info($find_friend_id,$conn);
	  $friend_name  = $friend_info->user_name;
	  $friend_name1 = iconv("UTF-8","gbk//TRANSLIT", $friend_name);
	  $friend_photo = name_to_path_thumb($friend_info->user_photo);
	  $friend_name_pinyin = strtolower(get_pinyin($friend_name1));
	 // echo "  ".$friend_name;
	  if (strpos($friend_name, $q) !== false  ||  search_match($friend_name_pinyin,$q)) {
			array_push($result, array(
				"name" => $friend_name,
				"photo" => $friend_photo,
			 	"id" => $find_friend_id,
			 	"relation" => 'F'	//friend
			));
	  }
  }
  
  //serch my course
  
  /*$course_list = get_course_list($user_id,$conn);
  for($count=0;$count<count($course_list);$count++){
  	$find_course_id = $course_list[$count]->course_id;
  	$relation = $course_list[$count]->relation;
  	$course_info = get_course_info($find_course_id,$conn);
  	$course_photo = name_to_path_thumb_course($course_info->course_photo);
  	$course_name = $course_info->course_name;
	$course_name1 = iconv("UTF-8","gbk//TRANSLIT", $course_name);
	$course_name_pinyin = strtolower(get_pinyin($course_name1));
	
  	if (strpos($course_name, $q) !== false || search_match($course_name_pinyin,$q)) {
			array_push($result, array(
				"name" => $course_name,
				"photo" => $course_photo,
			 	"id" => $find_course_id,
			 	"relation" => $relation	//friend
			));
    }
  }*/
  echo json_encode($result);
  
  




function get_pinyin($str) {
    $py = new my_Getpy;    
    $len = strlen($str);
    $ret = '';
    for ($i = 0; $i < $len; $i++)
    {
        if (ord($str[$i]) > 0x80)
        {
            $xx = $py->get(substr($str, $i, 2));
            $ret .= ($xx ?  $xx . ' ' : substr($str, $i, 2));    
            $i++;
        }
        else
        {
            $ret .= $str[$i];
        }
    }
    $py->unload();
    return $ret;
}

function search_match($string,$substring) {
    $num_of_string=strlen($string);
    $num_of_substring=strlen($substring);
    $ind=0;
    for($i=0;$i<$num_of_substring;$i++) {
       $flag=0;
       for($j=$ind;!$flag && $j<$num_of_string;$j++) {
           if($string[$j] == $substring[$i]) {
               $flag=1;
               $ind=$j;
               $ind++;
               break;
           }
       }   
       if($flag==0)
          break;
    }
    if($i==$num_of_substring)
	{
	   //echo 1;
       return 1;
	}
    else
	{
	   //echo 0;
       return 0;
	 }
}
   


?>