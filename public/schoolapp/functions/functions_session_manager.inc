<?
	/*---------------------------------------------------------------------------------------------------------------------------------------------
    SESSION CODEC
    0 => Guest
    1 => Root Admin
    2 => Property Admin
     */
    
    
//Set New Expiration Time
$expire = time() + 10000;
$expiry_killed = time() - 1000;


/*Create New Session*/
if(!isset($_SESSION['u_sid']) && !isset($_SESSION['u_id']))
{

 session_register("u_sid");
 $_SESSION['u_sid'] = session_id();
 session_register("expire");

 session_register("u_id");
 $_SESSION['u_id'] = 0;
 
  session_register("culture");
 $_SESSION['culture'] = 'eng';
 
  session_register("u_type");
 $_SESSION['u_type'] = 0;

   session_register("permissions");
 $_SESSION['permissions'] = 0;
 

 $exec_insert = mysql_query("REPLACE
						    INTO `sos_sessions` ( `sessions_sesskey` , `sessions_expire` , `sessions_passports_id`, `sessions_culture`, `sessions_type`, `sessions_permissions`)
							VALUES ('$_SESSION[u_sid]', '$expire', '$_SESSION[u_id]', '$_SESSION[culture]', '0', '0')
							", $db) or die(mysql_error());
 

						
}


/*Session Variables Exist, Verfify $_SESSION data */
elseif(isset($_SESSION['u_sid'])  && isset($_SESSION['u_id']))
{

/*Confirm $_SESSION{u_type] is correct*/	
switch ($_SESSION['u_type'])
	{	

		//Confirm Guest Session	
	    case 0:
	        $qry_validate_session = "SELECT *
									 FROM  sos_sessions
									 WHERE sessions_sesskey = '$_SESSION[u_sid]' AND sessions_type = '0'
									 LIMIT 1
									";
	        break;
	    
		    //Confirm Root Session   
	    case 1:
	        $qry_validate_session = "SELECT * 
									 FROM  sos_sessions
									 WHERE sessions_sesskey = '$_SESSION[u_sid]' AND sessions_type = '1'
									 LIMIT 1
									";
	        break;
	        
	                
	    //Confirm Propert Admin Session   
	    case 2:
	        $qry_validate_session = "SELECT *
									 FROM  sos_sessions
									 WHERE sessions_sesskey = '$_SESSION[u_sid]' AND sessions_type = '2'
									 LIMIT 1
									";
	        break;			
		

	    //Confirm TBD  
	    case 3:
	        $qry_validate_session = "SELECT *
									 FROM  sos_sessions
									 WHERE sessions_sesskey = '$_SESSION[u_sid]' AND sessions_type = '3'
									 LIMIT 1
									";
	        break;	

	    	

	        
		        	  
	   //No User Within Scope           	        	        
	   default:
	   	$qry_validate_session = "";
	   $session_error  = 'ERROR $_SESSION[u_type] NOT = 0,1,2,3';
	  break;
	        
	}
		

										
//Check Validation Query													
$exec_validate_session = mysql_query($qry_validate_session, $db) or die(mysql_error());
//Count Validation Query
$count_validate_session = mysql_num_rows($exec_validate_session);

//Session Found In MySQL
if($count_validate_session==1)
{

//Get variables from qry results	
$return_row = mysql_fetch_array($exec_validate_session);


//Update Session record
$qry_update_session = "REPLACE
					   INTO `sos_sessions` ( `sessions_sesskey`, `sessions_expire`, `sessions_passports_id`, `sessions_culture`, `sessions_type`, `sessions_permissions`)
					   VALUES ('$_SESSION[u_sid]', '$expire', '$_SESSION[u_id]', '$_SESSION[culture]', '$_SESSION[u_type]', '$_SESSION[permissions]')
					   ";

				
$exec_update_session = mysql_query($qry_update_session, $db) or die(mysql_error());


$qry_delete_sessions = "DELETE FROM  sos_sessions WHERE sessions_expire < ".$expiry_killed;
//Delete Expired Session variables
$exec_delete_sessions = mysql_query($qry_delete_sessions, $db) or die(mysql_error());

$qry_delete_sessions_empty = "DELETE FROM  sos_sessions WHERE sessions_sesskey = ''";
//Delete Expired Session variables
$exec_delete_sessions_empty = mysql_query($qry_delete_sessions_empty, $db) or die(mysql_error());

}
//Session Not Found In MySQL
elseif($count_validate_session!=1)
{

	switch ($_SESSION['u_type'])
	{	

		//Guest Session	
	    case 0:
		
		//Update Session record
		$qry_update_session = "REPLACE
					   INTO `sos_sessions` ( `sessions_sesskey` , `sessions_expire` , `sessions_passports_id`, `sessions_culture`, `sessions_type`, `sessions_permissions`)
					   VALUES ('$_SESSION[u_sid]', '$expire', '$_SESSION[u_id]', '$_SESSION[culture]', '$_SESSION[u_type]', '$_SESSION[permissions]')
					   ";

				
		$exec_update_session = mysql_query($qry_update_session, $db) or die(mysql_error());
	      break;
	        
	    //User Session Expired -> Redirect Login Page    
	    case 1:
	     HEADER('LOCATION: ' .$_CFG['rootURL']. 'login/');   
	        break;			
		
	    //Agency Session Expired -> Redirect Login Page    
	    case 2:
	     HEADER('LOCATION: ' .$_CFG['rootURL']. 'login/');   
	        break;


	   //Admin Session Expired -> Redirect Login Page    
	    case 3:
	     HEADER('LOCATION: ' .$_CFG['rootURL']. 'login/');   
	        break;
	        
	        
	}
	


}
	


}
/*Broken Session*/
elseif(!isset($_SESSION['u_sid']) && isset($_SESSION['u_id']))

{
	$session_error =  'no good session combination';
	

session_destroy();
	     HEADER('LOCATION: ' .$_CFG['rootURL']);   

}
?>