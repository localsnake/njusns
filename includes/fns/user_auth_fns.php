<?php
/** 
 * This file contains all the user auth operation functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage auth
 */
 
   require_once ('db_fns.php');
   require_once ('mail_fns.php');
   
  /** The function to register a new user with the info given
   * 
   * @param string	$email		the registered email addr
   * @param string	$password	user's password
   * @param string	$username	user name
   * @param string	$gender		user' gender, should be M:Male/F:Female
   * @param date	$birthday	user's birthday format yyyy-mm-dd' 
   * @param string	$hometown	user's hometown
   * @param mixed $conn database connection
   * @return boolean whether registered successfully
   */
  function register_user($email,$password,$username,$gender,$birthday,$hometown,$conn){
    // Judge a teacher or a student
    if(ereg('^[a-zA-Z0-9_\.\-]+@nju.edu.cn$', $email)){
    	$type = 'T';	// Teacher
    } else {
    	$type = 'S';	// Student
    }

    $result = $conn->query("SELECT * FROM sns_user_base WHERE user_email='$email'");
    if($result->num_rows >= 1)	{
    	echo "抱歉，邮箱已经被注册过了";
    	return false;
    }
    
    $confirm = trim(get_random_word(9, 13));	// generate confirm number
    if($gender == 'M') {
    	$user_photo = 'mdefault.jpg';
    } else{
    	$user_photo = 'fdefault.jpg';
    }
	$create_time = date("Y-m-d H:i:s");
	$result = $conn->query("INSERT INTO sns_user_base (user_email,user_password,user_name,
						 	user_gender,user_birthday,user_type,
			 				user_hometown,user_level,user_photo,user_status,user_confirm,create_time)
		 			 VALUES('$email',sha1('$password'),'$username',
					  		'$gender','$birthday','$type',
					  		'$hometown',0,'$user_photo','U','$confirm','$create_time')");
	if(!$result){
		echo "数据库错误，请确认所有字段合法";
		return false;
	}
	return send_confirm_mail($conn,$email);
  }
  
  
  /** 
   * The function to get user id by email 
   * @param string $email
   * @param mixed $conn
   */    
  function get_user_id_by_email($email,$conn) {
	$sel_result = $conn->query("SELECT user_id FROM sns_user_base WHERE user_email='$email'");
	if($sel_result->num_rows == 1){
		return $sel_result->fetch_object()->user_id;
	} else {
		return 0;
	}
  }
  
  /**
   * send confirm mail to user so that he/she can activate the new account
   * @param mixed 	$conn 	the mysql database connection
   * @param string 	$email 	the user's email address
   * @return boolean 	whether 	the confirm mail send successfully
   */ 
  function send_confirm_mail($conn,$email){
  	$sel_result = $conn->query("SELECT * FROM sns_user_base 
                         where user_email='$email'");
 	$row = $sel_result->fetch_object();
	$user_id = $row->user_id;
	$confirm = $row->user_confirm;
	$url = ServerAddr . "includes/interface/confirm.php?user_id=$user_id&confirm=$confirm";
	$subject = 'NJU SNS Confirm Info';
	//$text = 'This is a email from NJU SNS'; 
	$html = "<html><body>
 				<p>Dear User:</p>
 				<p> &nbsp;&nbsp; &nbsp;Welcome NJU SNS WebSite , 
				Click the following link to activate your account:</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href=\"$url\">$url</a></p>
				<p>----</p><p>Best Regards!</p><p>Localsnake Net League</p>
			</body></html>";
	
	if(send_html_email($email,$subject,$text,$html)){		// 发布信件成功，注册流程成功
		return true;
	} else{				//注册失败，删除数据库内容，防止用户以后不能注册了
		echo '发送验证信失败了,请在登录界面再次发送确认邮件';
		return false;
	}
  }
  
  /**
   * The function to confirm user's registeration
   * @param integer $user_id   user's id
   * @param string 	$confirm   confirm string
   * @param mixed $conn database connection
   * @return boolean whether confirm successfully
   */
  function user_confirm($user_id,$confirm,$conn){
    if(!$conn) $conn = db_connect();
    $setResult = $conn->query("UPDATE sns_user_base SET user_status='O'
                         WHERE user_id = $user_id AND user_confirm = '$confirm'");
    if(!$setResult) {
  	  return false;
    } elseif( $conn->affected_rows != 1) {
   	  echo '您已经验证过了.';
  	  return true;
    } else {
   	  return true;
    }
  }
 
  /**
   * The function to check whether the user is a teacher
   * @param $user_id
   * @param mixed $conn database connection
   * @return boolean whethre the user is a teacher
   */ 
  function is_teacher($user_id,$conn){
  	if(!$conn) $conn = db_connect();
	$user_info = get_user_base_info($user_id,$conn);
	if($user_info->user_type == 'T') return true;
	else return false;
  }
  
  
  /**
   * The function to login the sns web site
   * 
   * @param string $email
   * @param string $password
   * @param mixed $conn database connection
   * @return the user_id' if $email and $password matched
   */
  function login($email,$password,$conn){
    //if(!$conn) $conn = db_connect();
    // check if username is unique
    $result = $conn->query("SELECT * FROM sns_user_base 
                         where user_email='$email'
                         and user_password = sha1('$password')");
    if (!$result)	return 0;			// database error
    if ($result->num_rows>0)		{	// user exists
	  $row = $result->fetch_assoc();
	  $user_id = $row['user_id'];
	  $user_status = $row['user_status'];
	  if($user_status == 'U') {		// haven't confirmed yet
	  	//echo "You have not confirmed yet, please check the confrim mail in your box";	
	  	return -1;
	  }
	  // set the user status as I : login
	  $conn->query("UPDATE sns_user_base SET user_status='I'
                         		WHERE user_id = $user_id");
	  return $user_id;		// login right
    }	else {						
      return -2;			// wrong username or password			
    }
  }
  
  /**
   * The function to logout the web site
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return boolean whether logout successfully
   */
  function logout($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	// set the user status as O : logout
  	$result = $conn->query("UPDATE sns_user_base SET user_status='O'  
                         WHERE user_id=$user_id");
    if($result) return true;
    else return false;
  }
  
  
  /**
   * The function to save user photo
   * @param integer $user_id user's id 
   * @param string $user_photo user's photo name withoutput prefix
   * @param mixed $conn database connection
   * @return boolean whether the user's photo saved to the database successfully
   */
  function save_user_photo($user_id,$user_photo,$conn){
  	if(!$conn) $conn = db_connect();
    $update_result = $conn->query("UPDATE sns_user_base SET user_photo='$user_photo' 
						WHERE user_id=$user_id");	// NOT TESTED YET
	if(!$update_result) return false;
	return true;
  }
  
  
  /**
   * The function to see if somebody is logged in and notify them if not
   * @return whether the user has logged in the web
   */
  function check_valid_user(){
	if (isset($_SESSION['user_id'])) {
	  return true;
	} else {
	  return false;
	}
  }
  
  
  /**
   * check whether password is right
   * @param integer $user_id
   * @param string $password
   * @param mixed $conn the database connection
   * @return boolean whether the user's password is right
   */ 
  function check_password($user_id,$password,$conn){
    if(!$conn) $conn = db_connect();
    $result = $conn->query("SELECT * FROM sns_user_base 
                         where user_id=$user_id
                         and user_password = sha1('$password')");
    if($result->num_rows == 1){
    	return true;
    }	else {
    	return false;
    }
  }
  
  
  /**
   * The function to change password for username/old_password to new_password
   * @param integer $user_id
   * @param string $old_password
   * @param string $new_password
   * @param mixed $conn database connection
   * @return boolean whether change successfully
   */
  function change_password($user_id, $old_password, $new_password,$conn){	
	if(!$conn) $conn = db_connect();
	$result = $conn->query( "UPDATE sns_user_base
	                        SET user_password = sha1('$new_password')
	                        where user_id = '$user_id' ");
	if (!$result)	return false;
	else	return true;  // changed successfully
  }
 
  
  /**
   * The function to set password for username to a random value
   * @param string $email the user's registered email address
   * @param mixed $conn database connection
   * @return string the new password or false on failure
   */
  function reset_password($email,$conn){ 
  	if(!$conn) $conn = db_connect();
    // get a random dictionary word b/w 6 and 13 chars in length
    $new_password = trim(get_random_word(6, 13));
    if($new_password==false)
      throw new Exception('Could not generate new password.');
      // add a number  between 0 and 999 to it to make it a slightly better password
      srand ((double) microtime() * 1000000);
      $rand_number = rand(0, 999); 
      $new_password .= $rand_number;
    
      // set user's password to this in database or return false
      $sel_result = $conn->query( "SELECT * FROM sns_user_base WHERE 
      								user_email = '$email'");
      if(!$sel_result || $sel_result->num_rows < 1) {
      	//echo 'User not exists';  // not changed 用户不存在
        return null;
      }
      $result = $conn->query( "UPDATE sns_user_base
                          SET user_password = sha1('$new_password')
                          WHERE user_email = '$email'");
      if (!$result)	{	// error or user not exits
        echo 'Change password error';  // not changed 修改密码失败
        return null;
      } else {
        return $new_password;  // changed successfully
      }
  }
  
  /**
   * The function to notify the user that their password has been changed
   * @param string $email 
   * @param string $password
   * @return boolean whether notified successfully
   */
  function notify_password($email, $password){
  	$subject = 'NJU SNS password info';
	$text = 'This is a email from NJU SNS'; 
	$html = "<html><body>
			 <p>Dear User:</p>
			 <p> &nbsp;&nbsp; &nbsp;Your nju sns website password has been changed to 
			 <font color=red>$password</font></p>
			 <p> &nbsp;&nbsp;&nbsp;&nbsp;Please change it next time you log in. </p>
		     <p>----</p>
			 <p>Best Regards!</p>
			 <p>Localsnake Net League</p>
			</body>
			</html>";
	return send_html_email($email,$subject,$text,$html);
  }
  
  
  /**
   * edit user base info 
   * @param integer $user_id
   * @param string $username
   * @param string $gender
   * @param date $birthday  1990-09-09
   * @param string $hometown
   * @param mixed $conn
   * @return boolean whether edited
   */
  function edit_user_base_info($user_id,$username,$gender,$birthday,$hometown,$conn){
  	$set_query = "UPDATE sns_user_base SET 
	  user_name='$username',user_gender='$gender',user_hometown='$hometown',user_birthday='$birthday' 
	  			  WHERE user_id=$user_id";
    $set_base_result = $conn->query($set_query);
    if(!$set_base_result)	return false;
    else return true;
  }
  
  /**
   * edit user school info
   * @param integer $user_id
   * @param string $department
   * @param string $major
   * @param string $dorm_no 
   * @param mixed $conn
   * @return boolean whether edited
   */ 
  function edit_user_school_info($user_id,$department,$major,$dorm_no,$conn){
  	$sel_result = $conn->query( "SELECT * FROM sns_user_detail WHERE user_id=$user_id");
	if(!$department) $department = 'unknown';
	if(!$major)  $major = 'unknown';
	if(!$dorm_no)  $dorm_no = 'unknown';
	if($sel_result->num_rows > 0){		// exists change
	$set_result = 
	   $conn->query("UPDATE sns_user_detail SET user_department='$department',
	   user_major='$major',user_dorm_no='$dorm_no' WHERE user_id = '$user_id' ");
	}	else{							// insert
	$set_result = 
	   $conn->query("INSERT INTO sns_user_detail (user_id,user_department,user_major,
				user_dorm_no) VALUES
				($user_id,'$department','$major','$dorm_no')");
	}
	if(!$set_result)	return false;
	return true;
  }
  
  /**
   * edit user hobby info
   * @param integer $user_id
   * @param string $hobby
   * @param string $music
   * @param string $films
   * @param string $sports
   * @param string $books
   * @param mixed $conn
   * @return boolean whether edited
   */ 
  function edit_user_hobby_info($user_id,$hobby,$music,$films,$sports,$books,$conn) {
	if(!$hobby) $hobby = 'unknown';
	if(!$music) $music = 'unknown';
	if(!$films) $films = 'unknown';
	if(!$sports) $sports = 'unknown';
	if(!$books) $books = 'unknown';
  	$sel_result = $conn->query( "SELECT * FROM sns_user_detail WHERE user_id=$user_id");	
	if($sel_result->num_rows > 0){		// exists change
	$set_result = 
	   $conn->query("UPDATE sns_user_detail SET 
	    user_hobby='$hobby',user_music='$music',user_films='$films',user_sports='$sports',user_books='$books'
		WHERE user_id = '$user_id'");
	}	else{							// insert
	$set_result = 
	   $conn->query("INSERT INTO sns_user_detail (user_id,user_hobby,user_music,user_films,user_sports)
	   			 	VALUES
					($user_id,'$hobby','$music','$films','$sports','$books')");
	}
	if(!$set_result)	return false;
	return true;
  }
  /**
   * edit user contatch info 
   * @param integer $user_id
   * @param string $contact_email
   * @param string $qq
   * @param string $msn
   * @param string $phone 
   * @param mixed $conn
   * @return boolean whether edited
   */ 
  function edit_user_contact_info($user_id,$contact_email,$qq,$msn,$phone,$conn){
	if(!$contact_email) $contact_email = 'unknown';
	if(!$qq) $qq = 'unknown';
	if(!$msn) $msn = 'unknown';
	if(!$phone) $phone = 'unknown';
  	$sel_result = $conn->query( "SELECT * FROM sns_user_detail WHERE user_id=$user_id");	
	if($sel_result->num_rows > 0){		// exists change
	$set_result = 
	   $conn->query("UPDATE sns_user_detail SET user_contact_email='$contact_email',
	   user_qq='$qq',user_msn='$msn',user_phone='$phone'
		WHERE user_id = '$user_id' ");
	}	else{							// insert
	$set_result = 
	   $conn->query("INSERT INTO sns_user_detail (user_id,user_contact_email,user_qq,user_msn,user_phone) 
	   				VALUES 
				   ($user_id,'$contact_email','$qq','$msn','$phone')");
	}
	if(!$set_result)	return false;
	return true;
  }
  
  /**
   * The function to edit the user's base info and detail info 
   * @param integer $user_id
   * @param string $username
   * @param string $gender
   * @param string $hometown
   * @param string $department
   * @param string $major
   * @param string $dorm_no
   * @param string $hobby
   * @param string $music
   * @param string $films
   * @param string $sports
   * @param string $books
   * @param string $contact_email
   * @param string $qq
   * @param string $msn
   * @param string $phone
   * @param mixed $conn database connection
   * @return boolean whether info edited successfully
   */
  function edit_user_info($user_id,$username,$gender,$birthday,$hometown,
  			$department,$major,$dorm_no,$hobby,$music,$films,$sports,$books,
  			$contact_email,$qq,$msn,$phone,$conn) {
	  if(!$conn) $conn = db_connect();
      if(! edit_user_base_info($user_id,$username,$gender,$birthday,$hometown,$conn) )	return false;
      $sel_result = $conn->query( "SELECT * FROM sns_user_detail WHERE 
      								user_id=$user_id");	
 	  if($sel_result->num_rows > 0){	// exists change
 	  	$set_result = 
		   $conn->query("UPDATE sns_user_detail SET user_department='$department',
		   user_major='$major',user_dorm_no='$dorm_no',user_hobby='$hobby',
		   user_music='$music',user_films='$films',user_sports='$sports',
		   user_books='$books',user_contact_email='$contact_email',
		   user_qq='$qq',user_msn='$msn',user_phone='$phone'
	 		WHERE user_id = '$user_id' ");
 	  }	else{							// insert
 	  	$set_result = 
		   $conn->query("INSERT INTO sns_user_detail (user_id,user_department,user_major,
					user_dorm_no,user_hobby,user_music,user_films,user_sports,
					user_books,user_contact_email,user_qq,user_msn,user_phone) VALUES
					($user_id,'$department','$major','$dorm_no',
			  		'$hobby','$music','$films','$sports','$books',
  			 		 '$contact_email','$qq','$msn','$phone')
		   			");
 	  }
 	  if(!$set_result)	return false;
 	  return true;
  }
  
  
  
  /**
   * The function to get user base info from database
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return mixed the user's info array
   */
  function get_user_base_info($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$base_result = $conn->query("SELECT * FROM sns_user_base WHERE user_id=$user_id");
  	if(!$base_result)
  		return null;
	$row = $base_result->fetch_object();
	return $row;
  }
  
  /**
   * The function to get user detail info from database
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return mixed the user's detail info
   */  
  function get_user_detail_info($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$base_result = $conn->query("SELECT * FROM sns_user_detail WHERE user_id=$user_id");
  	if(!$base_result)
  		return NULL;
	$row = $base_result->fetch_object();
	return $row;
  }
  
  /**
   * The function to search user using keyword in the database
   * @param string $keyword
   * @param mixed $conn database connection
   * @return mixed all the related users' info array
   */
  function search_user($keyword,$conn){
  	if(!$conn) $conn = db_connect();
	$searchResult = $conn->query("SELECT * FROM sns_user_base WHERE
 								user_name LIKE '%$keyword%'");
	if(!$searchResult || $searchResult->num_rows < 1){
		return null;	// No Reuslt Found
	}
	return get_sel_object_array($searchResult);
  }
  
  /**
   * search course whose name is related with keywords
   * @param string $keyword
   * @param mixed $conn database connection
   * @return mixed all the related courses' info array
   */
  function search_course($keyword,$conn){
  	if(!$conn) $conn = db_connect();
	$searchResult = $conn->query("SELECT * FROM sns_course_info WHERE
 								course_name LIKE '%$keyword%'");
	if(!$searchResult || $searchResult->num_rows < 1){
		return null;	// No Reuslt Found
	}
	return get_sel_object_array($searchResult);
  }
   
  /**
   * The function to save user news 
   * @param integer $user_id
   * @param string $news_content
   * @param mixed $conn database connection
   * @return boolean whether news saved
   */
  function save_user_news($user_id,$news_content,$conn){
  	if(!$conn) $conn = db_connect();
  	$news_time = date("Y-m-d H:i:s");
  	$insert_result = $conn->query("INSERT INTO sns_user_news (user_id,news_content,news_time)
	  				VALUES ($user_id,'$news_content','$news_time')");
	if(!$insert_result)	{
	  return false;
	}
 	return true;
  }
  
  /**
   * The function to get user news from database
   * @param integer $user_id
   * @param integer $offset  	the offset of this selection
   * @param integer $pagesize 	the pagesize of each selection
   * @param mixed $conn database connection
   * @return mixed the user's news array
   */
  function get_user_news($user_id,$offset,$pagesize,$conn){
	  if(!$conn) $conn = db_connect();
   	  $sel_result = $conn->query("SELECT * FROM sns_user_news WHERE
		 							user_id = $user_id 
									 ORDER BY news_time DESC LIMIT $offset,$pagesize");
	  if(!$sel_result || $sel_result->num_rows < 1){
	  	return null;
	  }
	  return get_sel_object_array($sel_result);
  }
  
  /**
   * The function to send freshmilk 
   * @param integer $from_id 	from user or course's id
   * @param integer $to_id 		to user's id
   * @param string 	$freshmilk_type		U:user/C:course
   * @param string	$freshmilk_content
   * @param mixed $conn the database connection
   * @return boolean whether sent
   */
  function send_freshmilk($from_id,$to_id,$freshmilk_type,$freshmilk_content,$conn){
  	if(!$conn) $conn = db_connect();
  	$freshmilk_time = date("Y-m-d H:i:s");
  	$insert_result = $conn->query("INSERT INTO sns_freshmilk
	  				 (from_id,to_id,freshmilk_type,freshmilk_content,freshmilk_time)
					   VALUES ($from_id,$to_id,'$freshmilk_type',
					   		'$freshmilk_content','$freshmilk_time')");
 	if(!$insert_result) return false;
 	return true;
  }
  
  /**
   * The function to send a user apply
   * @param integer $from_id
   * @param integer $to_id
   * @param string $apply_content
   * @param mixed $conn the database connection
   * @return boolean whether sent
   */
  function send_user_apply($from_id,$to_id,$apply_content,$conn){
  	if(!$conn) $conn = db_connect();
	$datetime = date("Y-m-d H:i:s");
  	if(!isSet($apply_content)){
  		$apply_content = 'Hello';
  	}
  	$insert_result = $conn->query("INSERT INTO sns_user_apply
	  					 (from_id,to_id,apply_time,apply_content)
	  					VALUES($from_id,$to_id,'$datetime','$apply_content')");
	if(!$insert_result){
		return false;
	}	else{
		return true;
	}
  }
  
  
  /**
   * The function to approve user apply 
   * @param integer $apply_id
   * @param integer $from_id
   * @param integer $to_id
   * @param mixed $conn database connection
   * @return boolean whether sent
   */
  function approve_user_apply($apply_id,$from_id,$to_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$del_query = "DELETE FROM sns_user_apply WHERE apply_id=$apply_id AND to_id=$to_id";
  	$del_result = $conn->query($del_query);
	if($conn->affected_rows == 1){	// Right
		$insert_ret1 = $conn->query("INSERT INTO sns_user_friend (user_id,friend_id)
						VALUES ($from_id,$to_id)");
		$insert_ret2 = $conn->query("INSERT INTO sns_user_friend (user_id,friend_id)
						VALUES ($to_id,$from_id)");
		/*if(!$insert_ret1 || !$insert_ret2){
			 	return false;	// error or have applied before
	    }*/
	    $conn->query("DELETE FROM sns_user_apply WHERE
	  							from_id=$to_id AND to_id=$from_id");
	  							
		// TO DO send freshmilk
		$freshmilk_type = 'U';//'FA';	// FriendApproved
		$user_base_info = get_user_base_info($to_id,$conn);
		$user_name = $user_base_info->user_name;
		$freshmilk_content = "同意将您添加为好友";
		if(!send_freshmilk($to_id,$from_id,$freshmilk_type,$freshmilk_content,$conn)){
			echo "send fremilk error: $to_id $from_id $freshmilk_type $freshmilk_content";
		} else {
			return true;
		}
	} else {
	    echo "Del Error:$del_query";
		return false;
	}
  }
  
  /**
   * The function to ignore user_apply 
   * @param integer $apply_id 
   * @param integer $to_id
   * @param mixed $conn database connection
   * @return boolean whether ignored
   */
  function ignore_user_apply($apply_id,$to_id,$conn){
  	// Just Delete the apply item from the database table
  	$del_result = $conn->query("DELETE FROM sns_user_apply WHERE
	  							apply_id=$apply_id AND to_id=$to_id");
	if($conn->affected_rows == 1){	// Right
		return true;
	} else {
		return false;
	}
  }
  
  /**
   * The function to send a course apply
   * @param integer $from_id
   * @param integer $course_id
   * @param string $apply_content
   * @param mixed $conn database connection
   * @return bolean whether sent
   */
  function send_course_apply($from_id,$course_id,$apply_content,$conn){
  	if(!$conn) $conn = db_connect();
	$datetime = date("Y-m-d H:i:s");
  	if(!$apply_content){
  		$apply_content = 'null';
  	}
  	$insert_result = $conn->query("INSERT INTO sns_course_apply
	  					 (from_id,course_id,apply_time,apply_content)
	  					VALUES($from_id,$course_id,'$datetime','$apply_content')");
	if(!$insert_result){
		return false;
	}	else{
		return true;
	}
  }
  
  
  
  /**
   * The function to check whether the user exists
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return boolean whether the user exists
   */
  function check_user_exists($user_id,$conn){
  	if(!$conn) $conn = db_connect();
  	$sel_result = $conn->query("SELECT * FROM sns_user_base WHERE user_id = $user_id");
  	if(!$sel_result || $sel_result->num_rows < 1){
  		return false;
  	} 
  	return true;
  }
  
  /**
   * The function to check whether the course exists
   * @param integer $course_id
   * @return boolean whether the course exists
   */
   function check_course_exists($course_id,$conn){
   	  if(!$conn) $conn = db_connect();
   	  $sel_result = $conn->query("SELECT course_id FROM sns_course_info WHERE 
		 							course_id = $course_id");
	  if(!$sel_result || $sel_result->num_rows != 1) return false;
  	  return true;
   }
   

   
   /**
    * The function to get user apply from the database
    * @param integer $user_id
    * @param mixed $conn database connection
    * @return mixed the user's apply object array
    */
   function get_user_apply($user_id,$conn){
   	  if(!$conn) $conn = db_connect();
   	  $sel_result = $conn->query("SELECT * FROM sns_user_apply WHERE
		 							to_id = $user_id");
	  if(!$sel_result || $sel_result->num_rows < 1){
	  	return null;
	  }
	  $result = array();
	  for($count = 0; $row = $sel_result->fetch_object(); $count++){
	  	$result[$count] = $row;
      }
      return $result;
   }
   
   /**
    * The function to get course apply from the database
    * @param integer $user_id
    * @param mixed $conn database connection
    * @return mixed the user's course object array
    */
   function get_course_apply($user_id,$conn){
     if(!$conn) $conn = db_connect();
     $sel_result = $conn->query
	 	("SELECT * FROM sns_course_apply WHERE
			(course_id IN 
			(SELECT course_id FROM sns_user_course
			  WHERE sns_user_course.user_id = $user_id
   			  AND (sns_user_course.relation='M' OR sns_user_course.relation='T')))
			  ORDER BY course_id DESC");
	 if(!$sel_result || $sel_result->num_rows < 1)
	 	return null;
     $result = array();
     for($count = 0; $row = $sel_result->fetch_object(); $count++){
	  	$result[$count] = $row;
     }
     return $result;
   }
   
   /**
    * The function to get user freshmilk from database
    * @param integer $user_id
    * @param integer $offset the offset of the select
    * @param integer $pagesize the pagesize of the select
    * @param mixed $conn database connection
    * @return mixed the fremilk object array
    */
  function get_freshmilk($user_id,$offset,$pagesize,$conn){
	if(!$conn) $conn = db_connect();
	$sel_result = $conn->query(
				"SELECT * FROM sns_freshmilk WHERE to_id=$user_id  
				ORDER BY freshmilk_id DESC LIMIT $offset,$pagesize");
	if(!$sel_result || $sel_result->num_rows < 1)
		return null;
	return get_sel_object_array($sel_result);
  }
  
  
  /**
    * The function to get all user_id from database
    * @param mixed $conn database connection
    * @return mixed the user_id array
    */
  function get_all_user_id($conn){
	if(!$conn) $conn = db_connect();
	$sel_result = $conn->query(
				"SELECT user_id FROM sns_user_base WHERE user_status != 'U'
				ORDER BY user_id DESC");
	if(!$sel_result)
		return null;
	return get_sel_object_array($sel_result);
  }
  
  /**
    * The function to check whether user belong to the department
    * @param mixed $conn database connection
	* @param string $department 
	* @param integer $user_id
    * @return boolean whether belong to
    */
  function check_same_department($conn,$department,$user_id){
	if(!$conn) $conn = db_connect();
	$sel_result = $conn->query(
				"SELECT user_id FROM sns_user_detail WHERE user_id =$user_id");
	if(!$sel_result)
		return false;
    else {
	   $element=$result->fetch_assoc();
	   $d=$element['user_department'];
	   if(!strcmp($d,$department))
	      return true;
    }
	return false;
  }
  
  /**
    * The function to get user freshmilk from database via type
    * @param integer $user_id
	* @param string $type
    * @param integer $offset the offset of the select
    * @param integer $pagesize the pagesize of the select
	* @param 
    * @param mixed $conn database connection
    * @return mixed the fremilk object array
    */
  function get_freshmilk_type($user_id,$type,$offset,$pagesize,$conn){
	if(!$conn) $conn = db_connect();
	$sel_result = $conn->query(
				"SELECT * FROM sns_freshmilk WHERE to_id=$user_id  AND freshmilk_type='$type' 
				ORDER BY freshmilk_id DESC LIMIT $offset,$pagesize");
	if(!$sel_result || $sel_result->num_rows < 1)
		return null;
	return get_sel_object_array($sel_result);
  }
  
  /**
   * The function to delete freshmilk from database
   * @param integer $freshmilk_id
   * @param integer $user_id
   * @param mixed $conn database connection
   * @return boolean whether deleted
   */
   function del_freshmilk($freshmilk_id,$user_id,$conn){
   	 if(!$conn) $conn = db_connect();
   	 $del_result = $conn->query("DELETE FROM sns_freshmilk WHERE
	  							freshmilk_id=$freshmilk_id AND to_id=$user_id");
	if($conn->affected_rows == 1){	// Right
		return true;
	} else {
		return false;
	}
   }
   
   /**
    * The function to delete user news from database
    * @param integer $news_id
	* @param integer $user_id
	* @param mixed $conn database connection
	* @return whether deleted
    */
   function del_user_news($news_id,$user_id,$conn){
   	 if(!$conn) $conn = db_connect();
   	 $del_result = $conn->query("DELETE FROM sns_user_news WHERE
	  							news_id=$news_id AND user_id=$user_id");
	if($conn->affected_rows == 1){	// Right
		return true;
	} else {
		return false;
	}
   }
      
   /**
    * The function to get the number of applys
	* @param integer $user_id 
	* @param mixed $conn database connection
	* @return integer the user's user apply count
	*/
   function get_user_apply_count($user_id,$conn){
   	 if(!$conn) $conn = db_connect();
   	 $result = $conn->query("SELECT * FROM sns_user_apply
								WHERE to_id = $user_id");
	 return $result->num_rows;
   }
   
   /**
    * The function to get the number of applys
	* @param integer $user_id 
	* @param mixed $conn database connection
	* @return integer the user's course apply count
	*/
   function get_course_apply_count($user_id,$conn){
   	 if(!$conn) $conn = db_connect();
   	 $result = $conn->query("SELECT apply_id FROM sns_course_apply
								WHERE course_id IN
									(SELECT course_id FROM sns_user_course
									 WHERE user_id=$user_id AND (relation='M' OR relation = 'T') )");
 	 return $result->num_rows;
   }
   
  /**
   * The function to grab a random word from dictionary between the two length 
   * @param integer $min_length
   * @param integer $max_length
   * @ignore
   */
  function get_random_word($min_length, $max_length){
	// generate a random word
	$len = ($min_length+$max_length) / 2;
	$chars = array( 
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",  
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",  
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",  
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",  
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",  
        "3", "4", "5", "6", "7", "8", "9" 
    );
    $charsLen = count($chars) - 1; 
    shuffle($chars);    // 将数组打乱 
    $output = ""; 
    for ($i=0; $i<$len; $i++) { 
        $output .= $chars[mt_rand(0, $charsLen)]; 
    }
    return $output;  
  }
   
?>