
<?php

/**
 * The interface to recommand course to user
 * 
 * @author qianyu
 * @copyright 2011
 * @todo 推荐标准后期还可以修改，比如：考虑选修课的受欢迎程度等。
 */

   session_start();
   require_once('sns_fns.php');
   
   $user_id=$_SESSION['user_id'];
   
   $recommand_course=array();
   $num_of_recourse=0;
   $conn = db_connect();
   // 查找该同学上过的课程号
   $result1 = $conn->query("SELECT * FROM sns_user_course where user_id=$user_id and relation='A'");
   $num_of_courseid=$result1->num_rows;
   if($num_of_courseid>0){
     for($i=0;$i<$num_of_courseid;$i++){
        $element=$result1->fetch_assoc();
        $course_id=$element['course_id'];
		
        // 找到教这门课程的老师的id号
        $result2=$conn->query("SELECT * FROM sns_user_course where course_id=$course_id and relation='M'");
        $num_of_teacherid = $result2->num_rows;
        if($num_of_teacherid>0) {
            for($j=0;$j<$num_of_teacherid;$j++){
               $element1=$result2->fetch_assoc();
               $teacher_id=$element1['user_id'];
               $result3=$conn->query("SELECT * FROM sns_user_course where user_id=$teacher_id and relation='M'");
			   // 得到该老师教得所有的课程的id号
               $num_of_waitcourse = $result3->num_rows;
               if($num_of_waitcourse>0) {
                   for($k=0;$k<$num_of_waitcourse;$k++) {
                       $element2=$result3->fetch_assoc();
                       $waitcourse_id=$element2['course_id'];
					   $relation = get_user_course_relation($user_id,$waitcourse_id,$conn);
                       if(!$relation) {   //该学生没有选修过这门课程
					       for($count=0;$count<$num_of_recourse;$count++){
								if( $recommand_course[$count]['course_id'] == $waitcourse_id) 	break;
						   }
						   if($count == $num_of_recourse) {
								$recommand_course[$num_of_recourse]['course_id'] = $waitcourse_id;
								$course_info = get_course_info($waitcourse_id,$conn);
								$recommand_course[$num_of_recourse]['course_name'] = $course_info->course_name;
								$recommand_course[$num_of_recourse]['course_photo'] = name_to_path_thumb_course($course_info->course_photo);
								$recommand_course[$num_of_recourse]['user_id'] = $user_id;
								if($relation) 			//exists
									$recommand_course[$num_of_recourse]['relation'] = $relation;
								elseif(course_apply_sent($user_id,$recommand_course[$num_of_recourse]['course_id'],$conn)) 
									$recommand_course[$num_of_recourse]['relation'] = 'W';
								else
									$recommand_course[$num_of_recourse]['relation'] = 'N';
								$num_of_recourse++;
						   }
                       }
                   }
               }
            }
        }
     }
   }
   // @todo 好友的课程推荐
   
   // @todo 院系课程推荐
   echo json_encode($recommand_course);
    
   function alreadyChoose($conn,$user_id,$course_id) {
       $result= $conn->query("SELECT * FROM sns_user_course where user_id=$user_id and relation='A' and course_id=$course_id");
       $num=$result->num_rows;
       if($num>0)
          return 1;
       else
          return 0;
   }
   
   
   

?>
