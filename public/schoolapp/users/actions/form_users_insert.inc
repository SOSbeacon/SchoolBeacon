<?
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

$passports_email_sql = trim(addslashes($form_r[user_email]));
$passports_password_sql = trim(addslashes(md5("password")));
$passports_name_sql = trim(addslashes($form_r[user_f_name].' '.$form_r[user_l_name]));


$qry_user_insert = "INSERT INTO `sos_users` (
`id`, 
`user_f_name`, 
`user_l_name`,
`user_title`,
`user_school_id`,
`user_phone_primary`,
`user_fax`,
`user_email`,
`user_address`,
`user_city`,
`user_state`,
`user_zip`,
`user_comments`,
`user_active`
)

VALUES (
'NULL',
'$user_f_name_sql', 
'$user_l_name_sql',
'$user_title_sql',
'$user_school_id_sql',
'$user_phone_primary_sql',
'$user_fax_sql',
'$user_email_sql',
'$user_address_sql',
'$user_city_sql',
'$user_state_sql',
'$user_zip_sql',
'$user_comments_sql',
'$user_active_sql')
";

$exec_qry_user_insert= mysql_query($qry_user_insert, $db) or die(mysql_error());

$user_id_sql = mysql_insert_id();

$qry_passports_insert = "INSERT INTO `sos_passports` (
`id`, 
`passports_user_id`,
`passports_name`, 
`passports_email`, 
`passports_password`, 
`passports_type`, 
`passports_date_created`
)

VALUES ('', 
'$user_id_sql', 
'$passports_name_sql', 
'$passports_email_sql', 
'$passports_password_sql', 
'2', 
NOW())
";

$exec_qry_passports_insert = mysql_query($qry_passports_insert, $db) or die(mysql_error());

$passports_id_sql = mysql_insert_id();

$reg_insert['user']=true;

$_REQUEST[id]=$user_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_users_update.inc';
?>