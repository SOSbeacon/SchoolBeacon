<?


$user_id_sql = trim(addslashes($_REQUEST[id]));
$passports_password_sql = trim(addslashes(md5($form_r[password])));



	 $qry_update = "UPDATE sos_passports
			   		SET 
			   		passports_password = '$passports_password_sql'
			   		WHERE passports_user_id = '$user_id_sql'
			   	 ";

$exec_qry_update = mysql_query($qry_update, $db) or die (mysql_error());



mail($_SESSION['email_address'], "Password Reset",
"As per your request, sosbeacon.com has reset your password,

User Name: $_SESSION[email_address]

Password: $form_r[password]

", "From: $_CFG[emailNotice] <$_CFG[emailNotice]>\r\n");


$_REQUEST['id'] = $_REQUEST['id'];

$reg_update['password'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_account_update.inc';

?>