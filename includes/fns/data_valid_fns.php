<?php
/**
 * This file contains all the data validataion functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage data_valid
 */
 
  /**
   * translate freshmilk content from ch 2 en
   * @param string $content 
   * @return string string after replaced
   */
   function translate_freshmilk($freshmilk_content){
     $freshmilk_content = str_replace("修改了头像",'Changed Profile Picture',$freshmilk_content);
	 $freshmilk_content = str_replace("同意将您添加为好友",'Approved Your Friend Apply.',$freshmilk_content);
	 $freshmilk_content = str_replace("同意将您添加到课程",'Approved Your Course Apply To ',$freshmilk_content);
	 $freshmilk_content = str_replace("发布通知",'Uploaded Notice',$freshmilk_content);
	 $freshmilk_content = str_replace("删除了课件",'Deleted Slides',$freshmilk_content);
	 $freshmilk_content = str_replace("删除了资源",'Deleted Resource',$freshmilk_content);
	 $freshmilk_content = str_replace("删除了作业",'Deleted Assignment',$freshmilk_content);
	 $freshmilk_content = str_replace("发布了讲义",'Uploaded Slides',$freshmilk_content);
	 $freshmilk_content = str_replace("发布了资源",'Uploaded Resource',$freshmilk_content);
	 $freshmilk_content = str_replace("发布了作业",'Uploaded Assignment',$freshmilk_content);
	 $freshmilk_content = str_replace("修改了讲义",'Edited Slides',$freshmilk_content);
	 $freshmilk_content = str_replace("修改了资源",'Edited Resource',$freshmilk_content);
	 $freshmilk_content = str_replace("修改了作业",'Edited Assignment',$freshmilk_content);
	 $freshmilk_content = str_replace("讨论区发布了新帖",'Post:',$freshmilk_content);
	 $freshmilk_content = str_replace("创建了课程",'Created New Course',$freshmilk_content); 
	 $freshmilk_content = preg_replace('/和 ([^ ]*) 成为了好友/', 'Became friend with ${1}', $freshmilk_content);
	 return $freshmilk_content;
   }
 
 
  /** 
   * The function to check whether the $from_vars is filled
   * @param mixed $form_vars the from variable array
   * @return boolean whether filled out
   */
  function filled_out($form_vars){
  	foreach($form_vars as $key => $value) {
  		if(!isset($key) || ($value == '')){
  			return false;
  		}
  	}
  	return true;
  }
  
  /**
   * The function to validate whether the email is in the right form 
   * @param string $address	the email address
   * @return boolean whether whether the email is valid
   */
  function valid_email($address) {
    if (ereg('^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)*nju.edu.cn$', $address))
      return true;
    else 
      return false;
  }
  
  /**
   * The function to check a contact email address is possibly valid
   * @param string $address : the email address
   * @return boolean whether the email is valid
   */
  function valid_contact_email($address){
  	if(ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address))
  		return true;
	else
		return false;
  }
  
  /**
   * check valid birthday format
   * @param string $date
   * @return boolean whether valid
   */
   function valid_date($date) {
   	 if(ereg('^[0-9]{4,4}-[0-9]{1,2}-[0-9]{1,2}$',$date)) return true;
   	 else return false;
   }
  
  /**
   * the function to check a upload file format,
   * this will affect the availabe file formats for course assignemtns/lectures/resource files
   * 
   * @param string $file_ext file extentions
   * @return boolean whether available
   */ 
  function check_upload_file_format($file_ext) {
  	if($file_ext != 'pdf' && $file_ext != 'ppt' && $file_ext != 'pptx' 
		&& $file_ext!= 'doc' && $file_ext != 'docx' 
		&& $file_ext!= 'xls' && $file_ext != 'xlsx' 
		&& $file_ext != 'rar' && $file_ext != 'zip' && $file_ext != 'txt' ){
		echo '抱歉，仅支持txt,pdf,ppt,pptx,doc,docx,zip,rar格式的文件上传.';
		return false;
	} else {
		return true;
	}
  }
  
  /**
   * check time format,avoid text attack
   * @author qianyu<yangzhouqianyu@sina.com>
   * @param string $time
   * @return boolean whether format ok
   */
  function check_time_format($time) {
    $time_array=split(":",$time);
    while(list($k1,$t)=each($time_array)) {
        $component_time=split("-",$t);
        while(list($k2,$element)=each($component_time)) {
            $num=intval($element);
            if($num>30 || $num<0)
               return 0;
        }
    }
    return 1;
  }
  
  
  /**
   * the function to check valid resource url
   * @param string $url 
   */   
  function check_valid_url($url){
	//if(ereg('^(http|ftp):\/\/([\w-]+\.)+([\w-]+)+([- .\/?%&=\w]*)?(:8080)?$',$url)) return true;
	if(ereg("((http|https|ftp|telnet|news):\/\/)([a-z0-9_\-\/\.]+\.[][a-z0-9:;&#@=_~%\?\/\.\,\+\-]+)",$url)) return true;
	return false;
  }
   
?>