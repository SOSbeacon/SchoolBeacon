<?
//error_reporting(0);
	/*---------------------------------------------------------------------------------------------------------------------------------------------
    SESSION START */
	session_start();
	
	/*---------------------------------------------------------------------------------------------------------------------------------------------
    ARRAYS */ 
		$_CFG = array();
		$_DISPLAY = array();

	/*---------------------------------------------------------------------------------------------------------------------------------------------
    DEBUG SETTINGS */
        $_CFG['debug']=0;
	
		$_CFG['project_name'] = 'SOSbeacon';
    /*---------------------------------------------------------------------------------------------------------------------------------------------
    EMAIL SETTINGS */
        $_CFG['emailAdmin']			=	'admin@sosbeacon.org';
        $_CFG['emailSupport']		=	'support@sosbeacon.org';
        $_CFG['emailNotice']		=	'notice@sosbeacon.org';        
        $_CFG['emailInterest']		=	'interest@sosbeacon.org';
        $_CFG['emailMail']			=	'mail@sosbeacon.org';
        $_CFG['emailFriend']		=	'friend@sosbeacon.org';
        $_CFG['emailBilling']		=	'billing@sosbeacon.org';        
        $_CFG['emailAdvertising']	=	'advertising@sosbeacon.org';
                       
    /*---------------------------------------------------------------------------------------------------------------------------------------------
    DATABASE SETTINGS */
        $_CFG['dbType']             =   "mysql";           // view the abstraction layer for more possibilities
        $_CFG['dbHost']             =   "localhost";
        $_CFG['dbUser']             =   "sosbeacon";
        $_CFG['dbPass']             =   "sosbeacon!@#";
        $_CFG['dbName']             =   "schools";
        $_CFG['dbPort']             =   "3306";

    /*---------------------------------------------------------------------------------------------------------------------------------------------
    GENERAL SETTINGS */
        $_CFG['hostname']           =   "sosbeacon.org";
        $_CFG['connType']           =   "http"; //$_SERVER["HTTPS"]=="on" ? "https" : "http"
        $_CFG['rootDir']            =   "/home/sosbeacon/sosbeacon-api-v2/public/schoolapp/";
        $_CFG['rootDirAdmin']       =   "/home/sosbeacon/sosbeacon-api-v2/public/schoolapp/admin/";
        $_CFG['rootWeb']            =   "/schoolapp/";
        $_CFG['rootWebAdmin']       =   "/schoolapp/admin/";
        $_CFG['rootWebProfile']     =   "/profiles/";            
        $_CFG['rootURL']            =   $_CFG['connType']."://".$_CFG['hostname'].$_CFG['rootWeb'];
        $_CFG['rootURLAdmin']       =   $_CFG['connType']."://".$_CFG['hostname'].$_CFG['rootWebAdmin'];
        $_CFG['jsDir']              =   $_CFG['rootDir']."js/";
        $_CFG['jsWeb']              =   $_CFG['rootURL']."js/";
        $_CFG['cssDir']             =   $_CFG['rootDir']."css/";
        $_CFG['cssWeb']             =   $_CFG['rootURL']."css/";
        $_CFG['stylesDir']          =   $_CFG['rootDir']."styles/";
        $_CFG['stylesWeb']          =   $_CFG['rootURL']."styles/";
		$_CFG['bannersDir']         =   $_CFG['rootDir']."banner/";
        $_CFG['bannersWeb']         =   $_CFG['rootURL']."banner/";
        $_CFG['cssWebAdmin']        =   $_CFG['rootURLAdmin']."css/";
        $_CFG['cssDirAdmin']        =   $_CFG['rootDirAdmin']."css/";
        $_CFG['imgDir']             =   $_CFG['rootDir']."images/";
        $_CFG['imgWeb']             =   $_CFG['rootURL']."images/";
        $_CFG['imgDirAdmin']        =   $_CFG['rootDirAdmin']."images/";
        $_CFG['imgWebAdmin']        =   $_CFG['rootURLAdmin']."images/";
		$_CFG['functionWeb']        =   $_CFG['rootDir']."functions/";
		$_CFG['functionDir']        =   $_CFG['rootDir']."functions/";
		$_CFG['functionWebAdmin']   =   $_CFG['rootDirAdmin']."functions/";
		$_CFG['functionDirAdmin']   =   $_CFG['rootDirAdmin']."functions/";
		$_CFG['fragmentsWeb']   	=   $_CFG['rootDir']."fragments/";
		$_CFG['fragmentsDir']   	=   $_CFG['rootDir']."fragments/";
		$_CFG['fragmentsWebAdmin']  =   $_CFG['rootDirAdmin']."fragments/";
		$_CFG['fragmentsDirAdmin']  =   $_CFG['rootDirAdmin']."fragments/";		
		$_CFG['contentWeb']   		=   $_CFG['rootDir']."content/";
		$_CFG['contentDir']   		=   $_CFG['rootDir']."content/";
		$_CFG['navDir']        		=   $_CFG['rootDir']."nav/";
		$_CFG['navWeb']        		=   $_CFG['rootDir']."nav/";
		$_CFG['navDirAdmin']        =   $_CFG['rootDirAdmin']."nav/";
		$_CFG['navWebAdmin']        =   $_CFG['rootDirAdmin']."nav/";
		$_CFG['flashDir']           =   $_CFG['rootDir']."flash/";
		$_CFG['flashWeb']           =   $_CFG['rootDir']."flash/";
		$_CFG['includeWeb']         =   $_CFG['rootDir']."include/";
		$_CFG['includeDir']         =   $_CFG['rootDir']."include/";
		$_CFG['imgOriginal']        =   $_CFG['rootURL']."assets/original/";
		$_CFG['imgDirOriginal']     =   $_CFG['rootDir']."assets/original/";		
		$_CFG['imgPhotos']          =   $_CFG['rootURL']."assets/photos/";
		$_CFG['imgDirPhotos']       =   $_CFG['rootDir']."assets/photos/";
		$_CFG['imgThumbs']          =   $_CFG['rootURL']."assets/thumbs/";
		$_CFG['imgDirThumbs']       =   $_CFG['rootDir']."assets/thumbs/";
		$_CFG['imgMicros']          =   $_CFG['rootURL']."assets/micros/";
		$_CFG['imgDirMicros']       =   $_CFG['rootDir']."assets/micros/";

		
		$_CFG['bannerDir']        	=   $_CFG['rootDir']."banner/";
		$_CFG['bannerWeb']        	=   $_CFG['rootURL']."banner/";
		$_CFG['formsDir']        	=   $_CFG['rootDir']."forms/";
		$_CFG['formsWeb']        	=   $_CFG['rootDir']."forms/";
		$_CFG['frontendDir'] 		=   $_CFG['rootDir']."frontend/";
             
	/*---------------------------------------------------------------------------------------------------------------------------------------------
	 GLOBAL INCLUDES 	*/	
		require_once($_CFG['functionDir'].'data_connect.inc');
		require_once($_CFG['functionDir'].'functions.inc');
		require_once($_CFG['functionDir'].'arrays.inc');
		require_once($_CFG['functionDir'] .'objects.inc');

	/*---------------------------------------------------------------------------------------------------------------------------------------------
	SESSION MANAGER 	*/	
		require_once($_CFG['functionDir'] .'functions_session_manager.inc');

	/*---------------------------------------------------------------------------------------------------------------------------------------------
	$_DISPLAY VARIABLES 	*/	
		explodePathInfo($_SERVER['SCRIPT_NAME']);

		$_DISPLAY = $url_array;
	/*---------------------------------------------------------------------------------------------------------------------------------------------
    DEBUG MANAGER 	*/	
		require_once($_CFG['functionDir'] .'functions_debug.inc');
?>