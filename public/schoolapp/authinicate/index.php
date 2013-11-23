<?

        $_CFG['dbType']             =   "mysql";           // view the abstraction layer for more possibilities
        $_CFG['dbHost']             =   "localhost";
        $_CFG['dbUser']             =   "sosbeacon";
        $_CFG['dbPass']             =   "sosbeacon!@#";
        $_CFG['dbName']             =   "schools";
        $_CFG['dbPort']             =   "3306";
        
include('../functions/data_connect.inc');
$form_r = $_REQUEST[form_r];
$array_keys_r = array_keys($form_r);


for($x=0;$x<sizeof($array_keys_r);$x++)
{
if(empty($form_r[$array_keys_r[$x]]))
{
 $error[$array_keys_r[$x]] = $form_r[$array_keys_r[$x]];
}

for($z=0;$z<sizeof($array_keys_r); $z++)
{
$form_r[$array_keys_r[$z]] = strip_tags($form_r[$array_keys_r[$z]]);
}

}

if(!isset($error))
{
//Check Login Credentials
$passports_name_sql = trim(addslashes($form_r[passports_name]));
$passports_password_sql = md5($form_r[password]);


	 	$crediential_check_qry = "SELECT sos_passports.passports_user_id AS passports_id, 
								  sos_passports.passports_email as email_address, 
								  sos_passports.passports_name AS screen_name, 
								  sos_passports.passports_type AS passports_type, 
								  sos_passports.passports_permissions AS passports_permissions
				    	  		  FROM sos_passports 
				    	  		  WHERE sos_passports.passports_email = '$passports_name_sql' 
				    	  		  AND sos_passports.passports_password = '$passports_password_sql'
				    			";
				 
$crediential_check_get = mysql_query($crediential_check_qry, $db) or die(mysql_error());
$data = mysql_fetch_row($crediential_check_get);
$passports_id_result = $data['0'];
$email_address_result = $data['1'];
$screen_name_result = $data['2'];
$passports_type_result = $data['3'];
$passports_permissions_result = $data['4'];
}


if(isset($error) || empty($passports_id_result))
{
$reg_error[authinicate]=true;
header('LOCATION: http://sosbeacon.org/schoolapp/login/?error=true');
}
else
{
$u_id = $passports_id_result;
session_register("u_id");
$_SESSION[u_id] = $passports_id_result;


$u_type = $passports_type_result;
session_register("u_type");
$_SESSION[u_type] = $u_type;


$screen_name = $screen_name_result;
session_register("screen_name");
$_SESSION[screen_name] = $screen_name;


$email_address = $email_address_result;
session_register("email_address");
$_SESSION[email_address] = $email_address;


$permissions = $passports_permissions_result;
session_register("permissions");
$_SESSION[permissions] = $permissions;

$expire = time() + 10000000;
session_register("expire");
$_SESSION[expire] = $expire;
$qry_update_session = "REPLACE INTO sos_sessions VALUES ('$_SESSION[u_sid]', '$expire', '$passports_id_result', '$_SESSION[culture]', '$passports_type_result', '$passports_permissions_result')
 				  	  ";
$exec_qry_update_session = mysql_query($qry_update_session, $db) or die(mysql_error());



$qry_update_last_active = "UPDATE sos_profiles SET profiles_last_active = NOW() WHERE profiles_passports_id = '$passports_id_result' LIMIT 1";

//$exec_qry_update_last_active = mysql_query($qry_update_last_active, $db) or die(mysql_error());

header('LOCATION: http://sosbeacon.org/schoolapp/dashboard/');

}

?>