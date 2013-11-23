<?
		session_start();

		
        $_CFG['dbType']             =   "mysql";           // view the abstraction layer for more possibilities
        $_CFG['dbHost']             =   "localhost";
        $_CFG['dbUser']             =   "sosbeacon";
        $_CFG['dbPass']             =   "sosbeacon!@#";
        $_CFG['dbName']             =   "schools";
        $_CFG['dbPort']             =   "3306";
        
include('../functions/data_connect.inc');


$session_sql = trim(addslashes($_SESSION[u_sid]));

$qry_delete_session = "DELETE
				       FROM sos_sessions 
				       WHERE sessions_sesskey = '$session_sql'
				      ";
				 
$exec_qry_delete_session = mysql_query($qry_delete_session, $db) or die(mysql_error());

session_destroy();
HEADER('LOCATION: /schoolapp/login/');
?>