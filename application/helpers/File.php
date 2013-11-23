<?php
class Sos_Helper_File
{
    public static function getAlertImagePath ()
    {
        return APPLICATION_PATH . '/../public/sosdata/images/';
    }
    
    public static function getAlertImageURL ()
    {
        return  '/sosdata/images/';
    }
    
    
    public static function getAlertAudioPath ()
    {
        return APPLICATION_PATH . '/../public/sosdata/audio/';
    }
    
    public static function getAlertAudioURL ()
    {
        return  '/sosdata/audio/';
    }
    

    
    /**
     * Generate the name with consequence to prevent existing file
     * @param fullPath: return fullpath or not
     * 
     */
	public static function generatePath ( $filePath, $fullPath=true)
    {
   	
    	$info = pathinfo($filePath);	
                                
        $index = 0;
        while (file_exists( $info['dirname'] . '/' . $info['filename'] . '_' . $index . '.' . $info['extension'] )) {
        	$index ++;
        }
        
     	if ($fullPath)
     		return $info['dirname'] . '/' . $info['filename'] . '_' . $index . '.' . $info['extension'];
     	else 
     		return $info['filename'] . '_' . $index . '.' . $info['extension'];
     		
     	                  
    }
    
   
}