<?php
/**
 *   File Storage Class 
 * 
 * @author qiangrw
 * @version $Id$
 * @package my
 */

  class SaeStorage {
	/**
     * @var string
     */
    private $filePath= '';
	private $errMsg = '';
	private $errNum = 0;
	
	/**
     * Get File Url
     *
     * @param string $domain 
     * @param string $filename 
     * @return string
     * @author qiangrw
     */
	public function getUrl($domain,$filename){
		$domain = trim($domain);
		$filename = trim($filename);
		$this->filePath = "$domain/$filename";
		return $this->filePath;
	}
	private function setUrl( $domain , $filename) {
        $domain = trim($domain);
        $filename = trim($filename);
        $this->filePath = "$domain/$filename";
    }
	
	 /**
     * upload file to storage
     *
     * @param string $domain 
     * @param string $destFileName 
     * @param string $srcFileName 
     * @param array $attr  
     * @param bool $compress
     * @return string 
     * @author qiangrw
     */
    public function upload( $domain, $destFileName, $srcFileName, $attr = array(), $compress = false ){
		$domain = trim($domain);
        if ( Empty( $domain ) || Empty( $destFileName ) || Empty( $srcFileName ) ){
            $this->errMsg = 'the value of parameter (domain,destFile,srcFileName) can not be empty!';
            $this->errNum = -101;
            return false;
        }
		$this->setUrl( $domain, $destFileName );
		move_uploaded_file($srcFileName,$this->filePath);
		chmod($this->filePath, 0777);
		return $this->filePath;
	}
	
	/**
     * Check Whether File exists
     *
     * @param string $domain 
     * @param string $filename 
     * @return bool
     * @author qiangrw
     */
    public function fileExists( $domain, $filename ){
        $domain = trim($domain);
        if ( Empty( $domain ) || Empty( $filename ) )
        {
            $this->errMsg = 'the value of parameter (domain,filename) can not be empty!';
            $this->errNum = -101;
            return false;
        }

        if ( file_exists( $domain."/".$filename) ) {
            return true;
        } else {
            return false;
        }
    }
	
	/**
     * Delete Files
     *
     * @param string $domain 
     * @param string $filename 
     * @return bool
     * @author qiangrw
     */
    public function delete( $domain, $filename ){
        $domain = trim($domain);
        if ( Empty( $domain ) || Empty( $filename ) )
        {
            $this->errMsg = 'the value of parameter (domain,filename) can not be empty!';
            $this->errNum = -101;
            return false;
        }
		unlink( $domain."/".$filename );
		return true;
    }
  }