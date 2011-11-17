<?php
/**
 * An Interface of user upload pic
 * 
 * @author QiangRunwei <qiangrw@gmail.com>
 * @copyright LocalsNake Net League 2011
 * @package interface
 * @subpackage user
 */

session_start();
require_once 'sns_fns.php';
require_once $include_path . "SaeStorage.php";

if (!check_valid_user()) {
    echo 0;
    exit;
}
$user_id = $_SESSION['user_id'];
$act = $_REQUEST['act'];

$large_image_prefix = "r_";
$thumb_image_prefix = "t_";
$s = new SaeStorage();
$conn = db_connect();
$user_info = get_user_base_info($user_id, $conn);
$photo_name = $user_info->user_photo;

// get user photo url
if ($act == 'get_path') {
    echo name_to_path_large($photo_name);
    exit;
}

// Upload A New Photo
if ($act == 'upload') { // reload the pic
    $del_large_image_name = $large_image_prefix . $photo_name;
    $del_thumb_image_name = $thumb_image_prefix . $photo_name;

    // generate a new photo name for saving
    $random = strtotime(date('Y-m-d H:i:s'));
    $photo_name = "U_" . $user_id . "_" . $random;
}

// Get the current large/thumb image file name and location
$large_image_name = $large_image_prefix . $photo_name;
$thumb_image_name = $thumb_image_prefix . $photo_name;
$large_image_location = $s->getUrl($user_photo_domain, $large_image_name);
$thumb_image_location = $s->getUrl($user_photo_domain, $thumb_image_name);

// define the restrictions
$max_file = 2;
$max_width = 200;
$max_height = 160;
$thumb_width = 60;
$thumb_height = 60;
// define the allowed uploaded file format
$allowed_image_types = array('image/pjpeg' => "jpg", 'image/jpeg' => "jpg",
    'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' =>
    "gif");
$allowed_image_ext = array_unique($allowed_image_types);
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext .= strtoupper($ext) . " ";
}

// process the upload pic action
if ($act == 'upload') { // upload a new big image
    $userfile_name = $_FILES['file']['name'];
    $userfile_tmp = $_FILES['file']['tmp_name'];
    $userfile_size = $_FILES['file']['size'];
    $userfile_type = $_FILES['file']['type'];
    $filename = basename($_FILES['file']['name']);
    $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

    //Only process if the file is a JPG, PNG or GIF and below the allowed limit
    if ((!empty($_FILES["file"])) && ($_FILES['file']['error'] == 0)) {
        foreach ($allowed_image_types as $mime_type => $ext) {
            //loop through the specified image types
            //and if they match the extension then break out
            //everything is ok so go and check file size
            if ($file_ext == $ext && $userfile_type == $mime_type) {
                $error = ""; // No Error Find Match
                break;
            } else {
                $error = "仅支持 " . $image_ext . " 格式!";
            }
        }
        //check if the file size is above the allowed limit
        if ($userfile_size > ($max_file * 1048576)) {
            $error .= "Images must be under " . $max_file . "MB in size";
        }
    } else {
        $error = "Select an image for upload";
    }
    if (strlen($error) == 0) {
        if (isset($_FILES['file']['name'])) {
            //this file could now has an unknown file extension
            //(we hope it's one of the ones set above!)
            $large_image_name = $large_image_name . "." . $file_ext;
            $thumb_image_name = $thumb_image_name . "." . $file_ext;
            $large_image_location = $large_image_location . "." . $file_ext; // a new pic name
            $thumb_image_location = $thumb_image_location . "." . $file_ext;
            //put the file ext in the session so we know what file to look for once its uploaded
            $_SESSION['user_file_ext'] = "." . $file_ext;

            //Scale the image if it is greater than the width set above
            $width = get_width($userfile_tmp); // get the real photo width
            $height = get_height($userfile_tmp);
            if ($width < $thumb_width || $height < $thumb_height) {
                echo "上传头像的高度或者长度必须都大于 $thumb_width 像素";
                exit;
            }
            $current_large_image_width = $width;
            $current_large_image_height = $height;
            $modi = false;
            if ($width > $max_width) {
                $scale = $max_width / $width;
                $uploaded = resize_image($userfile_tmp, $width, $height, $scale);
                $current_large_image_width = $max_width;
                $current_large_image_height = ceil($height * $scale);
                $modi = true;
            }
            if ($current_large_image_height > $max_height) {
                $scale = $max_height / $current_large_image_height;
                $uploaded = resize_image($userfile_tmp, $current_large_image_width, $current_large_image_height,
                    $scale);
                $current_large_image_height = $max_height;
                $current_large_image_width = ceil($current_large_image_width * $scale);
                $modi = true;
            }
            if($current_large_image_height < $thumb_height || $current_large_image_width < $thumb_width){
            	echo "抱歉，上传头像长宽比差距过大，为了更好的显示效果，请换一张图片上传.";
				exit;
            }
            if (!$modi) {
                $scale = 1;
                $uploaded = resize_image($userfile_tmp, $width, $height, $scale);
            }
            $s->upload($user_photo_domain, $large_image_name, $userfile_tmp);

            //Delete the thumbnail file so the user can create a new one
            /*if ($s->fileExists($user_photo_domain, $thumb_image_name)) {
                $s->delete($user_photo_domain, $thumb_image_name);
            }*/

            //做一个默认小头像
            $cropped = resize_thumbnail_image($thumb_image_location, $large_image_location,
                $thumb_width, $thumb_height, 0, 0, 1);
        }

        // save photo info to database
        // No prefix saved !
        $photo_name = $photo_name . "." . $file_ext; // Add Img Type
        //将新头像信息添加进数据库
        if (!save_user_photo($user_id, $photo_name, $conn)) {
            echo '存储图片失败，数据库错误，请稍后再试';
            exit;
        }
        //Delete the former large and thumb photo
        if (!strstr($del_large_image_name, 'default')) {
            $s->delete($user_photo_domain, $del_large_image_name); // remove the prev photo
        }
        if (!strstr($del_thumb_image_name, 'default')) {
            $s->delete($user_photo_domain, $del_thumb_image_name); // remove the prev photo
        }

        $large_image_name = $large_image_prefix . $photo_name;
        $large_image_location = $s->getUrl($user_photo_domain, $large_image_name);

        // $_SESSION['photo_path'] = $large_image_location;
        // temp use this url
        $_SESSION['photo_path'] = "user_photo/" . $large_image_name;
        goto_page($html_path . "frmCropPhoto.php");
        #echo 1;
    } else {
        do_html_header('错误');
        echo $error;
        do_html_footer();
    }

} else
    if ($act == 'process') { // Crop Photo
        $x1 = $_POST["x1"];
        $y1 = $_POST["y1"];
        $x2 = $_POST["x2"];
        $y2 = $_POST["y2"];
        $w = $x2 - $x1;
        $h = $y2 - $y1;

        //Scale the image to the thumb_width set above
        $scale = $thumb_width / $w;
        $cropped = resize_thumbnail_image($thumb_image_location, $large_image_location,
            $w, $h, $x1, $y1, $scale);
        // To be tested
        // send user news
        save_user_news($user_id, "修改了头像", $conn);

        // you can add send fresh milk here
        // DONOT SEND HERE  EDIT 8.16
        /*$friend_list = get_friend_list($user_id);
        for($i=0;$i<count($friend_list);$i++){
        $friend_id = $friend_list[$i]->friend_id;
        send_freshmilk($user_id,$friend_id,'U',"修改了头像");
        }*/
        echo $user_id; // echo "Success, Now You can close this frame";
    } else {
        echo 0; // Fail
    }

?>