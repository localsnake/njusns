<?php
  session_start();
  header("Pragma: public");
  header("Expires: 0");
  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  $browser = $_SERVER['HTTP_USER_AGENT'];
  
  require_once 'sns_fns.php';
  $user_id = $_SESSION['user_id'];
  $course_id = addslashes($_REQUEST['course_id']);
  $download_id = addslashes($_REQUEST['download_id']);
  $kind = addslashes($_REQUEST['kind']);
  $conn = db_connect();

  $relation = get_user_course_relation($user_id, $course_id, $conn);
  if ($relation != 'A' && $relation != 'M' && $relation != 'T') {
	echo 'Permission Denied!';
    exit;
  }
   // 添加访问统计
  increase_visits($kind,$download_id,$conn);

  if ($kind == 2) { // 作业
    $query = "SELECT * FROM sns_course_assignment where course_assignment_id=$download_id";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      $element = $result->fetch_assoc();
      $my_path = addslashes("../../" . $file_dir . "/" . $element['course_assignment_dir']);
      $my_file = addslashes($element['course_assignment_dir']);
      $file_name = addslashes($element['course_assignment_title']);
      $position = strpos($my_file, ".");
      $temp = substr($my_file, $position);
      $my_file = $file_name . $temp;
    } else {
      exit;
    }
  } else
    if ($kind == 1) { // 课件
      $query = "SELECT * FROM sns_course_lecture where course_lecture_id=$download_id";
      $result = $conn->query($query);
      if ($result->num_rows > 0) {
        $element = $result->fetch_assoc();
        $my_path = addslashes("../../" . $file_dir . "/" . $element['course_lecture_dir']);
        $my_file = addslashes($element['course_lecture_dir']);
        $file_name = addslashes($element['course_lecture_title']);
        $position = strpos($my_file, ".");
        $temp = substr($my_file, $position);
        $my_file = $file_name . $temp;
      } else {
        exit;
      }
    } else
      if ($kind == 3) { // 资源
        $query = "SELECT * FROM sns_course_resource where course_resource_id=$download_id";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          $element = $result->fetch_assoc();
          $my_path = addslashes("../../" . $file_dir . "/" . $element['course_resource_dir']);
          $my_file = addslashes($element['course_resource_dir']);
          $file_name = addslashes($element['course_resource_title']);
          $position = strpos($my_file, ".");
          $temp = substr($my_file, $position);
          $my_file = $file_name . $temp;
        } else {
          exit;
        }
      } else {
        exit;
      }

      if (preg_match('/MSIE 5.5/', $browser) || preg_match('/MSIE 6.0/', $browser) ||
        preg_match('/MSIE 7.0/', $browser)) {
        header('Pragma: private');
        // the c in control is lowercase, didnt work for me with uppercase
        header('Cache-control: private, must-revalidate');
        // MUST be a number for IE
        header("Content-Length: " . filesize($my_path));
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename="' . $my_file . '"');
      } else {
        header("Content-Length: " . (string )(filesize($my_path)));
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename="' . $my_file . '"');
      }

      header('Content-Transfer-Encoding: binary');
  if (file_exists($my_path)) {
    if ($file = fopen($my_path, 'rb')) {
      while (!feof($file) and (connection_status() == 0)) {
        print (fread($file, filesize($my_path)));
        flush();
      }
      fclose($file);
    }
  } else {
    exit;
  }
 
?>