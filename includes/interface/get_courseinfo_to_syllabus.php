<?php

/**
 * The interface to get course info list to syllabus
 * 
 * @author qianyu <yangzhouqianyu@sina.com>
 * @author Qiang Runwei <qiangrw@gmail.com>
 * @todo When A course has many teachers , need to change code
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage course
 */
   
   session_start();
   require_once('sns_fns.php');
   $user_id=$_SESSION['user_id'];

   $conn=db_connect();
   $course_info=array();
   $array_course = get_course_list($user_id,$conn);
   for($i=0;$i<count($array_course);$i++) {
       $element = $array_course[$i];
       $course_id = $element->course_id;
       $user_course_relation = $element->relation;
	   $user_course_status = $element->status;
       // get course info
       $array_courseinfo = get_course_info($course_id,$conn);
       if($array_courseinfo) {
           $course_info[$i]['course_name'] = $array_courseinfo->course_name;
           $course_info[$i]['course_id']   = $course_id;
           $course_info[$i]['relation']    = $user_course_relation;		// add relation
           $course_info[$i]['course_place']= $array_courseinfo->course_place;
           $course_info[$i]['course_time'] = $array_courseinfo->course_time;
           $course_info[$i]['course_book'] = $array_courseinfo->course_book;
           $course_info[$i]['course_type'] = $array_courseinfo->course_type;
		   $course_info[$i]['course_status'] = $user_course_status;
           
           // 查找教师名称,首先查找教室id,再去查找教师名字
           $teacher_array = get_course_teacher_list($course_id,$conn);
           // 这里暂时没有处理有多个老师的情况
           if($teacher_array) {
     	     $teacher_id = $teacher_array[0]->user_id;
           	 $teacher_info = get_user_base_info($teacher_id,$conn);
           	 $course_info[$i]['course_teacher']=$teacher_info->user_name;
     	   } else {
     	   	 $course_info[$i]['course_teacher']='NULL';
     	   }
       }
   }
   echo json_encode($course_info);
?>
