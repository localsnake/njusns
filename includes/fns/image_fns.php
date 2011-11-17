<?php
/** 
 * This file contains all the mail functions
 * 
 * @author	Runwei Qiang  <qiangrw@gmail.com>
 * @version	1.0
 * @copyright	LocalsNake Net League 2011
 * @package	fns
 * @subpackage image
 */
   
  /** 
    * The function to resize the image with scale
	* 
	* @param string $image 				the image file path
	* @param integer $width				the image width 
	* @param integer $height			the image height
	* @param float $scale				the image resize scale
	* @return string the resized image file path
	*/
  function resize_image($image,$width,$height,$scale) {
	return resize_thumbnail_image($image, $image, $width, $height,0,0, $scale);
  }
  
  /** 
    * The function to resize the image with scale and start position
    * 
    * @param string $thumb_image_name   the thumb image file path
    * @param string $image 				the image file path
	* @param integer $width				the image width 
	* @param integer $height			the image height
	* @param integer $start_width   	the image resization start width
	* @param integer $start_height		the image resization start height 
	* @param float $scale				the image resization scale
	* @return string  the resized image file path
    */
  function resize_thumbnail_image($thumb_image_name, $image, $width, $height, 
  										$start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $image_type) = getimagesize($image);
	$image_type = image_type_to_mime_type($image_type);
	$new_image_width = ceil($width * $scale);
	$new_image_height = ceil($height * $scale);
	$new_image = imagecreatetruecolor($new_image_width,$new_image_height);
	switch($image_type) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($new_image,$source,0,0,$start_width,$start_height,
							$new_image_width,$new_image_height,$width,$height);
	switch($image_type) {
		case "image/gif":
	  		imagegif($new_image,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($new_image,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($new_image,$thumb_image_name);  
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
  }
  
  /** The function to get image height
   * 
   * @param string $image the image file path
   * @return integer the image height
   */
  function get_height($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
  }
  
  /** The function to get image width
   * 
   * @param string $image the image file path
   * @return string $width the image width
   */
  function get_width($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
  }
  
  /**
   * The function to get photo url
   *
   * @param string $domain  	the domain to save the photo files
   * @param string $prefix 		the photo prefix
   * @param string $photo_name 	the original photo name
   * @return string  the url of the photo in the domain with prefix
   */   
  function get_photo_url($domain,$prefix,$photo_name) {
  	return "$domain/$prefix".$photo_name;
  }
     
  /**
   * @ignore
   * @deprecated using get_photo_url in the future
   */ 
  function name_to_path_thumb($photo_name){
  	$photo_dir = "user_photo";
	$upload_path = $photo_dir."/";		// the path to save the photo
	$thumb_image_prefix = "t_";
	$path = $upload_path.$thumb_image_prefix.$photo_name;
	return $path;
  }
  /**
   * @ignore
   * @deprecated using get_photo_url in the future
   */ 
  function name_to_path_large($photo_name){
  	$photo_dir = "user_photo";
	$upload_path = $photo_dir."/";		// the path to save the photo
	$large_image_prefix = "r_";
	$path = $upload_path.$large_image_prefix.$photo_name;
	return $path;
  }
  /**
   * @ignore
   * @deprecated using get_photo_url in the future
   */ 
  function name_to_path_thumb_course($photo_name){
  	$photo_dir = "course_photo";
	$upload_path = $photo_dir."/";		// the path to save the photo
	$thumb_image_prefix = "t_";
	$path = $upload_path.$thumb_image_prefix.$photo_name;
	return $path;
  }
  /**
   * @ignore
   * @deprecated using get_photo_url in the future
   */ 
  function name_to_path_large_course($photo_name){
  	$photo_dir = "course_photo";
	$upload_path = $photo_dir."/";		// the path to save the photo
	$large_image_prefix = "r_";
	$path = $upload_path.$large_image_prefix.$photo_name;
	return $path;
  }
?>