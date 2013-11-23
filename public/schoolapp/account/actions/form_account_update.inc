<?

$user_id_sql = trim(addslashes($_SESSION[u_id]));
$user_f_name_sql = trim(addslashes($form_r[user_f_name]));
$user_l_name_sql = trim(addslashes($form_r[user_l_name]));
$user_title_sql = trim(addslashes($form_r[user_title]));
$user_school_id_sql = trim(addslashes($form_r[user_school_id]));
$user_phone_primary_sql = trim(addslashes($form[user_phone_primary]));
$user_fax_sql = trim(addslashes($form[user_fax]));
$user_email_sql = trim(addslashes($form_r[user_email]));
$user_address_sql = trim(addslashes($form[user_address]));
$user_city_sql = trim(addslashes($form[user_city]));
$user_state_sql = trim(addslashes($form[user_state]));
$user_zip_sql = trim(addslashes($form[user_zip]));
$user_comments_sql = trim(addslashes($form[user_comments]));
$user_active_sql = trim(addslashes($form_r[user_active]));

$qry_update = "UPDATE sos_users
			   	SET 
			   	user_f_name = '$user_f_name_sql', 
			   	user_l_name = '$user_l_name_sql', 
			   	user_title = '$user_title_sql',
			   	user_phone_primary = '$user_phone_primary_sql',
			   	user_fax = '$user_fax_sql',
			   	user_email = '$user_email_sql',
			   	user_address = '$user_address_sql',
			   	user_city = '$user_city_sql',
			   	user_state = '$user_state_sql',
			   	user_zip = '$user_zip_sql'
			   	WHERE id = '$user_id_sql'
			   ";

$exec_qry_update = mysql_query($qry_update, $db) or die (mysql_error());





$passports_email_sql = trim(addslashes($form_r[passports_email]));
$passports_password_sql = trim(addslashes(md5($form_r[password])));
$passports_name_sql = trim(addslashes($form_r[passports_name]));


	 $qry_update = "UPDATE sos_passports
			   		SET 
			   		passports_name = '$user_f_name_sql $user_l_name_sql', 
			   		passports_email = '$user_email_sql'
			   		WHERE passports_user_id = '$user_id_sql'
			   	 ";

$exec_qry_update = mysql_query($qry_update, $db) or die (mysql_error());




$_REQUEST['id'] = $_REQUEST['id'];
$reg_update['user'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_account_update.inc';
?>