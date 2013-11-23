<?


$agent_user_sql = trim(addslashes($form_r[email]));
$new_pswd =  trim(generatePassword(5,1));
$password_sql = addslashes(md5($new_pswd));

$agent_date_created_sql = date('Y-m-d');


$qry_update_passports = "UPDATE sos_passports SET passports_password = '$password_sql'
	     			  	 WHERE passports_email = '$agent_user_sql'
	     			  	 LIMIT 1
	    			 	 ";

$exec_update_passports = mysql_query($qry_update_passports, $db) or die (mysql_error());




mail('softwebstudios@gmail.com', "Password Reset",
"As per your request, sosbeacon.com has reset your password,

User Name: $form_r[email]

Password: $new_pswd

", "From: $_CFG[emailNotice] <$_CFG[emailNotice]>\r\n");





$content_fragment = './templates/'.$_SESSION[culture].'/form_success.inc';
?>
