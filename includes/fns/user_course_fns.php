<?php
/** 
 * This file contains all the user course operation functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage course
 */

  /**
   * get total course list
   * @param mixed $conn database connection
   * @return mixed all the related courses' info array
   */
  function get_school_total_course($conn){
	$searchResult = $conn->query("SELECT * FROM sns_course_info");
	if(!$searchResult || $searchResult->num_rows < 1){
		return null;	// No Reuslt Found
	}
	return get_sel_object_array($searchResult);
  }
 
  /**
   * get the user's course list that he/she attended
   * 
   * @param integer the user's id
   * @param mixed $conn
   * @return mixed the array of the course list
   */
  function get_course_list($user_id,$conn){
  	 if(!$conn) $conn = db_connect();
  	 $sel_result = $conn->query("SELECT * FROM sns_user_course WHERE user_id=$user_id");
  	 return get_sel_object_array($sel_result);
  }
  
  /**
   * get the user's jw course list that he/she attended
   * 
   * @param integer the user's id
   * @param mixed $conn
   * @return mixed the array of the course list
   */
  function get_jw_course_list($user_id,$conn){
  	 if(!$conn) $conn = db_connect();
  	 $sel_result = $conn->query("SELECT * FROM sns_jw_course_info WHERE user_id = $user_id");
  	 return get_sel_object_array($sel_result);
  }
  
  /**
   * get course teacher
   * @param integer $course_id
   * @param mixed $conn
   * @return mixed the course teacher list
   */
  function get_course_teacher_list($course_id,$conn){
  	$query = "SELECT * FROM sns_user_course where course_id=$course_id AND relation='M'";
    $sel_result = $conn->query($query);
    return get_sel_object_array($sel_result);
  }
  
  /**
   * get course related person : student,teacher,ta
   * @param integer $course_id
   * @param mixed $conn
   * @return mixed the course teacher list
   */
  function get_course_related_people_list($course_id,$conn){
  	$query = "SELECT * FROM sns_user_course where course_id=$course_id";
    $sel_result = $conn->query($query);
    return get_sel_object_array($sel_result);
  }
  
  /**
   * get course teacher
   * @param integer $course_id
   * @param mixed $conn
   * @return mixed the course teacher list
   */
  function get_course_ta_list($course_id,$conn){
  	$query = "SELECT * FROM sns_user_course where course_id=$course_id and relation='T'";
    $sel_result = $conn->query($query);
    return get_sel_object_array($sel_result);
  }
  
  /**
   * create new course, save course info to database
   * @param string $course_name
   * @param string $course_time
   * @param string $course_place
   * @param integer $course_stu_num
   * @param string $course_book
   * @param string $course_type
   * @param string $course_photo
   * @param string $course_introduction
   * @param mixed $conn
   * @return integer $course_id
   */
   function create_new_course($course_name,$course_term,$course_time,$course_place,$course_stu_num,
			$course_book,$course_type,$course_photo,$verify,$password,$course_introduction,$conn){
	if(!$course_time) $course_time = 'NULL';
	if(!$course_term) $course_term = 'Fall 2011';
	if(!$course_place) $course_place = 'NULL';
	if(!$course_stu_num) $course_stu_num = 0;
	if(!$course_book) $course_book = 'NULL';
	if(!$verify) $verify = 'Y';	//默认需要验证
	if(!$password) $password = '000000';
	
   	$query="INSERT INTO sns_course_info
		(course_name,course_term,course_time,course_place,course_stu_number,
			course_book,course_type,course_photo,verify,password,course_introduction)
		VALUES('$course_name','$course_term','$course_time','$course_place',$course_stu_num,
			'$course_book','$course_type','$course_photo','$verify','$password','$course_introduction')";
    $stmt=$conn->query($query);
	$course_id = $conn->insert_id;			//得到新插入的课程ID
	if($conn->affected_rows != 1) {
		echo 'Database Error In Insert Course Info:',$query;
		return -1;
	}
	return $course_id;
   }
   
   /**
    * del course
	* @param integer $course_id
	* @param mixed $conn database connection
	* @return bool whether deleted
	* @todo delete course resource, may delete in the back program
    */
   function del_course($course_id,$conn){
  	 $query = "DELETE FROM sns_course_info WHERE course_id = $course_id";
  	 $conn->query($query);
  	 if($conn->affected_rows == 1) {
  	 	$conn->query("DELETE FROM sns_user_course WHERE course_id=$course_id"); 		
  	 	return true;
  	 }
  	 else return false;
   }
  
  /**
   * edit course info
   * @param integer $course_id
   * @param string $course_name
   * @param string $course_term
   * @param string $course_place
   * @param string $course_book
   * @param string $course_time
   * @param string $course_stu_num
   * @param string $course_type
   * @param string $course_url
   * @param string $verify
   * @param string $password
   * @param string $course_introduction
   * @param mixed $conn database connection
   */
  function edit_course_info($course_id,$course_name,$course_term,$course_place,$course_book,$course_time,
  							$course_stu_num,$course_type,$course_url,$verify,$password,$course_introduction,$conn){
	if(!$course_time) $course_time = 'NULL';
	if(!$course_place) $course_place = 'NULL';
	if(!$course_stu_num) $course_stu_num = 0;
	if(!$course_book) $course_book = 'NULL';
	if(!$verify) $verify = 'Y';
    $query="UPDATE sns_course_info SET
			 course_name='$course_name' , 
			 course_term = '$course_term',
			 course_place='$course_place', 
			 course_book='$course_book', 
			 course_time='$course_time',
			 course_stu_number=$course_stu_num,
			  course_type='$course_type',
			  course_url = '$course_url',
		      verify = '$verify',
			  password = '$password',
			  course_introduction='$course_introduction' 
			  WHERE course_id=$course_id";
  	$conn->query($query);
  	if($conn->affected_rows == 1) return true;
  	else return false;
  }
 
  /** 
   * Check whether the apply has been sent
   * @param integer $from_id
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether apply sent
   */ 
  function course_apply_sent($from_id,$course_id,$conn){
  	 if(!$conn) $conn = db_connect();
  	 $sel_result = $conn->query("SELECT * FROM sns_course_apply WHERE 
	   			from_id=$from_id AND course_id=$course_id");
	 if($sel_result->num_rows > 0){
		return true;
	 }	else {
		return false;
	 }
  }

  /**
   * Check if the user is the course's teacher
   * @param integer $user_id
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether the user is the course's teacher 
   */
  function is_course_teacher($user_id,$course_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_course WHERE 
				course_id=$course_id AND user_id=$user_id");
	if($sel_result->num_rows > 0){
		$row = $sel_result->fetch_object();
		if($row->relation == 'M' || $row->relation == 'T') return true;
		else return false;
	}	else {
		return false;
	}
  }
  
  
  /**
   * get course news
   * @param integer $course_id
   * @param integer $offset the offset of the select
   * @param integer $pagesize the pagesize of the select
   * @param mixed $conn database connection
   * @return mixed the course news object array
   */
  function get_course_news($course_id,$offset,$pagesize,$conn)  {
  	 $query="SELECT * FROM sns_course_notice WHERE course_id=$course_id 
 			ORDER BY notice_time DESC LIMIT $offset,$pagesize";
	 $sel_result = $conn->query($query);
	 return get_sel_object_array($sel_result);
  }
  
   /**
    * upload course news
    * 
    * @param integer $course_id
    * @param string $content notice content
    * @param mixed $conn database connection
    * @return boolean whether uploaded
    */ 
  function upload_course_news($course_id,$content,$conn){
    $datetime = date("Y-m-d H:i:s");
	$query = "INSERT INTO sns_course_notice (course_id,notice_time,notice_content) VALUES ($course_id,'$datetime','$content')";
	$conn->query($query);
	if($conn->affected_rows == 1){
		return true;
	}
	echo $query;
	return false;
  }
  
  /**
   * del course news from database
   * @param integer $news_id
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether deleted
   */
  function del_course_news($news_id,$course_id,$conn){
 	if(!$conn) $conn = db_connect();
	$conn->query("DELETE FROM sns_course_notice 
						WHERE course_id=$course_id AND course_notice_id=$news_id");
 	if($conn->affected_rows == 1){
	 	return true;
 	}	else {
	 	return false;
 	}
  }
  
  /**
   * get student list who attend the course 
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return the student list array
   */ 
  function get_student_list($course_id,$conn){
    $query="SELECT * FROM sns_user_course 
			WHERE course_id=$course_id and relation='A' ORDER BY user_id DESC";
	$sel_result = $conn->query($query);
	return get_sel_object_array($sel_result);
  }
 
  /**
   * get student list who attend the course 
   * @param integer $course_id
   * @param mixed $conn database connection
   * @param integer $offset
   * @param integer $pagesize
   * @return the student list array
   */ 
  function get_student_list_page($course_id,$offset,$pagesize,$conn){
    $query="SELECT * FROM sns_user_course 
			WHERE course_id=$course_id and relation='A' 
			ORDER BY user_id DESC LIMIT $offset,$pagesize";
	$sel_result = $conn->query($query);
	return get_sel_object_array($sel_result);
  }
   
   /**
    * delelte course student by id
    * @param integer $course_id
    * @param integer $student_id
    * @param mixed $conn database connection
    * @return boolean whether deleted
    */ 
   function del_student($course_id,$student_id,$conn){
   	if(!$conn) $conn = db_connect();
   	$conn->query("DELETE from sns_user_course 
					WHERE course_id=$course_id AND user_id=$student_id");
	if($conn->affected_rows == 1) return true;
	else return false;
   }
   
  /**
   * Save Course Photo To Database
   * @param integer $course_id 
   * @param string $course_photo
   * @param mixed $conn database connection
   * @return boolean whether saved
   */
  function save_course_photo($course_id,$course_photo,$conn){
  	if(!$conn) $conn = db_connect();
    $update_result = $conn->query("UPDATE sns_course_info 
						SET course_photo='$course_photo' 
						WHERE course_id=$course_id");	// NOT TESTED YET
	if(!$update_result) return false;
	return true;
  }
  
  /**
   * The function to check the relation between user and course
   * @param integer $user_id 
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return string user course relation, A:Attend/M:Manage
   */
  function get_user_course_relation($user_id,$course_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT relation FROM sns_user_course 
	  							WHERE user_id=$user_id AND course_id=$course_id");
	if($sel_result && $sel_result->num_rows == 1){
		$row = $sel_result->fetch_object();
		return $row->relation;
	}
	return false;
  }
 
   
   /**
    * add user course relation attend or manager course
    * @param integer $user_id
    * @param integer $course_id
    * @param mixed $conn
    * @return boolean whether added
    */ 
   function add_user_course_relation($user_id,$course_id,$relation,$conn) {
   	//默认是Mount
   	$query="INSERT INTO sns_user_course VALUES($user_id,$course_id,'$relation','M')";	// Mount
	$conn->query($query);
	if($conn->affected_rows != 1) {
	  return false;
	}
	return true;
   }
   
   /**
    * the function to get course's ta count
	* @param integer $course_id
	* @return integer the ta count
	*/ 
  function get_course_ta_count($course_id,$conn){
    $sel_result = $conn->query("SELECT COUNT(user_id) as num FROM sns_user_course WHERE course_id=$course_id AND relation='T'");
	if($sel_result) return $sel_result->fetch_object()->num;
	else return 0;
  }
   
   /**
    * del user course relation attend or manager course
    * @param integer $course_id
    * @param mixed $conn
    * @return boolean whether added
    */ 
   function del_user_course_ta($course_id,$conn) {
   	//默认是Mount
   	$query="DELETE FROM sns_user_course WHERE course_id=$course_id AND relation='T'";
	$del_result = $conn->query($query);
	if($del_result) 	return true;
	else return false;
   }
   
   /**
    * add user course relation attend or manager course
    * @param integer $user_id
    * @param integer $course_id
    * @param mixed $conn
    * @return boolean whether set successfully
    */ 
   function set_user_course_relation($user_id,$course_id,$relation,$conn) {
	// if is not the course teacher or student
	if(!get_user_course_relation($user_id,$course_id,$conn)){
		return add_user_course_relation($user_id,$course_id,$relation,$conn);
	}
	// if has been a student of the course
   	$query="UPDATE sns_user_course SET relation='$relation' WHERE user_id=$user_id AND course_id = $course_id";
	$conn->query($query);
	if($conn->affected_rows != 1) {
	  return false;
	}
	return true;
   }
   
   /**
    * create course discussion ,save info to database
	*  
    * @param integer $course_id 
    * @param string $discussion_area_name
    * @param mixed $conn database connection
    * @return boolean whether saved
    */
   function create_course_discussion_area($course_id,$discussion_area_name,$conn){
   	 $query="INSERT INTO sns_course_discussion(course_id,discussion_area_name)
     			VALUES($course_id,'$discussion_area_name')";
	 $conn->query($query);
	 if($conn->affected_rows == 1){
	 	return true;
	 }else {
	 	return false;
	 }
   }
   
   
   /**
    * get assignment list info
    * 
    * @param integer $course_id
    * @param mixed $conn database connection
    * @return mixed the assignment info object array
    */
	function get_assignment_list($course_id,$conn){
	  $query = "SELECT * FROM sns_course_assignment where course_id=$course_id ORDER BY update_time DESC";
  	  $sel_result = $conn->query($query);
	  return get_sel_object_array($sel_result);
	}
	/**
    * get lecture list info
    * 
    * @param integer $course_id
    * @param mixed $conn database connection
    * @return mixed the assignment info object array
    */
	function get_lecture_list($course_id,$conn){
	  $query = "SELECT * FROM sns_course_lecture where course_id=$course_id ORDER BY update_time DESC";
	  $sel_result = $conn->query($query);
      return get_sel_object_array($sel_result);
	}
	/**
    * get resource list info
    * 
    * @param integer $course_id
    * @param mixed $conn database connection
    * @return mixed the assignment info object array
    */
	function get_resource_list($course_id,$conn){
	  $query = "SELECT * FROM sns_course_resource where course_id=$course_id ORDER BY update_time DESC";
	  $sel_result = $conn->query($query);
      return get_sel_object_array($sel_result);	
	}
   
    /**
    * get assignment info from database
    * 
	* @param integer $assignment_id
    * @param mixed $conn database connection
    * @return mixed assignment info object
    */
    function get_assignment_info($assignment_id,$conn){
		$query="SELECT * FROM sns_course_assignment WHERE course_assignment_id=$assignment_id";
		$result=$conn->query($query);
		if($result->num_rows != 1){
			return null;
		}
		return $result->fetch_object();
    }
    
   /**
    * get lecture info from database
    * 
	* @param integer $lecture_id
    * @param mixed $conn database connection
    * @return mixed assignment info object
    */
    function get_lecture_info($lecture_id,$conn){
		$query="SELECT * FROM sns_course_lecture WHERE course_lecture_id=$lecture_id";
		$result=$conn->query($query);
		if($result->num_rows != 1){
			return null;
		}
		return $result->fetch_object();
    }
    
   /**
    * get resource info from database
    * 
	* @param integer $resource_id
    * @param mixed $conn database connection
    * @return mixed assignment info object
    */
    function get_resource_info($resource_id,$conn){
		$query="SELECT * FROM sns_course_resource WHERE course_resource_id=$resource_id";
		$result=$conn->query($query);
		if($result->num_rows != 1){
			return null;
		}
		return $result->fetch_object();
    }
    
    /**
     * insert new assignment
     * @param integer $course_id
     * @param string $assignment_title
     * @param string $course_assignment_dir
     * @param datetime $assignment_deadline
     * @param mixed $conn database connection
     * @return integer assignment_id
     */
    function new_assignment($course_id,$assignment_title,$course_assignment_dir,$assignment_deadline,$create_time,$update_time,$conn) {
	  $query="INSERT INTO sns_course_assignment
		(course_id,course_assignment_title,course_assignment_dir,course_assignment_deadline,create_time,update_time) 
		  values
		($course_id,'$assignment_title','$course_assignment_dir','$assignment_deadline','$create_time','$update_time')";
		$result=$conn->query($query);
		$assignment_id = $conn->insert_id;	// last inserted auto inc id
		return $assignment_id;
	}
	
  /**
   * insert new lecture
   * @param integer $course_id
   * @param string $lecture_title
   * @param string $lecture_dir
   * @param integer $lecture_chapter
   * @param mixed $conn database connection
   * @return integer $lecture_id
   */
  function new_lecture($course_id,$lecture_title,$lecture_dir,$create_time,$update_time,$conn){
	$query="INSERT INTO sns_course_lecture
		(course_id,course_lecture_title,course_lecture_dir,create_time,update_time) VALUES
		($course_id,'$lecture_title','$lecture_dir','$create_time','$update_time')";
	$result=$conn->query($query);
	$lecture_id = $conn->insert_id;
	return $lecture_id;
  }
  
  /**
   * insert new resource info
   * @param integer $course_id
   * @param string $resource_title
   * @param string $resource_dir
   * @param mixed $conn database connection
   * @return integer $resource_id
   */
  function new_resource($course_id,$resource_title,$resource_dir,$resource_url,$resource_type,$create_time,$update_time,$conn) {
	if(!$resource_url) $resource_url = 'NULL';
  	$query="INSERT INTO sns_course_resource
	  (course_id,course_resource_title,course_resource_dir,course_resource_url,course_resource_type,create_time,update_time) VALUES
    			($course_id,'$resource_title','$resource_dir','$resource_url','$resource_type','$create_time','$update_time')";
    $result=$conn->query($query);
    return $conn->insert_id;
  }
    
    /**
     * modify assignment info 
     * @param integer $assignment_id
     * @param datetime $assignment_deadline
     * @param string $course_assignment_dir
     * @param mixed $conn database connection
     * @return boolean whether modified
     */
	function modify_assignment($assignment_id,$assignment_deadline,$course_assignment_dir,$conn){
	    $update_time=date("Y-m-d H:i:s");
	    $query="UPDATE sns_course_assignment SET 
	  			course_assignment_deadline= '$assignment_deadline',
				course_assignment_dir='$course_assignment_dir',
				update_time='$update_time'
					WHERE course_assignment_id=$assignment_id";
	    $result=$conn->query($query);
	    if($conn->affected_rows == 0){
	    	return false;
	    }
	    return true;
	}
	
	/**
	 * modify lecture info
	 * @param integer $lecture_id
	 * @param string $course_chapter
	 * @param string $course_lecture_dir
	 * @param mixed $conn database connection
     * @return boolean whether modified
	 */ 
	function modify_lecture($lecture_id,$course_lecture_dir,$conn){
	     $update_time=date("Y-m-d H:i:s");
		 $query="UPDATE sns_course_lecture SET 
		 	course_lecture_dir='$course_lecture_dir',
            update_time='$update_time'			
		 		WHERE course_lecture_id=$lecture_id";
	    $result=$conn->query($query);
	    if(!$result || $conn->affected_rows == 0){
	    	return false;
	    }
	    return true;
	}
	
	/**
	 * modify resource info from database
	 * @param integer $resource_id
	 * @param string $course_resource_dir
	 * @param mixed $conn database connection
  	 * @return boolean whether modified
	 */ 
	function modify_resource($resource_id,$resource_type,$new_name,$conn){
	  $update_time=date("Y-m-d H:i:s");
	  if($resource_type=='I') {
         $query="update sns_course_resource set course_resource_dir='$new_name',update_time='$update_time'
		 		 where course_resource_id=$resource_id";
	  } else if($resource_type=='O') {
	     $query="update sns_course_resource set course_resource_url='$new_name',update_time='$update_time'
		 		 where course_resource_id=$resource_id";
	  }
	  $result=$conn->query($query);
  	  if($conn->affected_rows != 1){
    	return false;
	  }
	  return true;
	}
	 
   
   /**
    * del assignment info from database
    * 
    * @param integer $assignment_id
    * @param mixed $conn database connection
    * @return boolean whether deleted
    */
   function del_assignment($assignment_id,$conn){
	 /*修改课程作业数据库,删除该表项*/
	 $query="DELETE FROM sns_course_assignment 
	 			WHERE course_assignment_id=$assignment_id";
	 $result=$conn->query($query);
	 if($conn->affected_rows != 1 ){
		return false;	//echo '数据库删除失败，请稍后再试';
	 }
	 return true;
   }
   
   /**
    * del lecture info from database
    * 
    * @param integer $lecture_id
    * @param mixed $conn database connection
    * @return boolean whether deleted
    */
   function del_lecture($lecture_id,$conn){
	 $query="DELETE FROM sns_course_lecture WHERE course_lecture_id=$lecture_id";
	 $result=$conn->query($query);
	 if($conn->affected_rows != 1 ){
		return false;	//echo '数据库删除失败，请稍后再试';
	 }
	 return true;
   }
   
  /**
    * del resource info from database
    * 
    * @param integer $resource_id
    * @param mixed $conn database connection
    * @return boolean whether deleted
    */
   function del_resource($resource_id,$conn){
	 $query="DELETE FROM sns_course_resource WHERE course_resource_id=$resource_id";
	 $result=$conn->query($query);
	 if($conn->affected_rows != 1 ){
		return false;	//echo '数据库删除失败，请稍后再试';
	 }
	 return true;
   }
   
  /**
   * The function to get course info 
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return mixed course info object
   */
  function get_course_info($course_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * from sns_course_info WHERE course_id=$course_id");
  	if(!$sel_result || $sel_result->num_rows != 1){
  		return null;
  	}
  	$row = $sel_result->fetch_object();
  	return $row;
  }
  
  /**
   * The function to approve user apply 
   * @param integer $apply_id
   * @param integer $from_id
   * @param integer $to_id
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether saved
   */
  function approve_course_apply($apply_id,$from_id,$to_id,$course_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$set_result = $conn->query("DELETE FROM sns_course_apply WHERE
	  							apply_id=$apply_id AND course_id=$course_id");
	if($conn->affected_rows == 1){	// Right
		$insert_result = 
				$conn->query("INSERT INTO sns_user_course (user_id,course_id,relation)
						VALUES($from_id,$course_id,'A')"); 	// Attend
		if(!$insert_result) return false;
		$course_info = get_course_info($course_id,$conn);
		$user_base_info = get_user_base_info($to_id,$conn);
		// assign freshmilk content
		$freshmilk_type = 'U';//"CA";		// Course Approved
		$user_name = $user_base_info->user_name;
		$course_name = $course_info->course_name;
		$freshmilk_content = "同意将您添加到课程" . 
		"<a href = 'javascript:jumpToCourse($course_id,\"A\");'>$course_name</a>";			
		$freshmilk_content = addslashes($freshmilk_content);
		return send_freshmilk($to_id,$from_id,$freshmilk_type,$freshmilk_content,$conn);
	} else {
		return false;
	}
  }
  
  /**
   * The function to ignore course apply
   * @param integer $apply_id
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether ignored
   */
  function ignore_course_apply($apply_id,$course_id,$conn){
  	// Just Delete the apply item from the database table
  	$set_result = $conn->query("DELETE FROM sns_course_apply WHERE
	  							apply_id=$apply_id AND course_id=$course_id");
	if($conn->affected_rows == 1){	// Right
		return true;
	} else {
		return false;
	}
  }
  
  /**
   * The function to ignore total course apply, when verify mode change 
   * @param integer $course_id
   * @param mixed $conn database connection
   * @return boolean whether ignored
   */
  function ignore_total_course_apply($course_id,$conn){
  	// Just Delete the apply item from the database table
  	$set_result = $conn->query("DELETE FROM sns_course_apply WHERE course_id=$course_id");
	if($set_result){	// Right
		return true;
	} else {
		return false;
	}
  }
  
  /**
   * the function to set user course status
   * @param integer $user_id
   * @param integer $course_id
   * @param string $status
   * @return boolean whetherd changed
   */
  function set_user_course_status($user_id,$course_id,$status,$conn){
    $conn->query("UPDATE sns_user_course SET status='$status'
					 WHERE user_id=$user_id AND course_id=$course_id");
    if($conn->affected_rows == 1) return true;
    else return false;
  }
  
  /**
   * the function to increase user's visits
   * @param integer kind
   * @return boolean  whether increased
   */
  function increase_visits($kind,$id,$conn){
  	if($kind == 1) {	//课件
  	  $conn->query( "UPDATE sns_course_lecture SET visits=visits+1 WHERE course_lecture_id = $id" );
  	  if($conn->affected_rows ==1 ) {
  	    return true;
  	  } else {
  	  	return false;
  	  }
  	}
  }
?>