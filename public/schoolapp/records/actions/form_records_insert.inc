<?
$records_school_id_sql = trim(addslashes($form_r[records_school_id]));
$records_first_name_sql = trim(addslashes($form_r[records_first_name]));
$records_last_name_sql = trim(addslashes($form_r[records_last_name]));
$records_email_sql = trim(addslashes($form_r[records_email]));
$records_phone_mobile_sql = trim(addslashes($form[records_phone_mobile]));
$records_phone_work_sql = trim(addslashes($form[records_phone_work]));
$records_phone_home_sql = trim(addslashes($form[records_phone_home]));
$records_date_created_sql = 'NOW()';
$records_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$records_date_updated_sql = 'NOW()';
$records_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$records_status_sql = trim(addslashes($form_r[records_status]));

$qry_user_insert = "INSERT INTO sos_records (
`id` ,
`records_school_id` ,
`records_first_name` ,
`records_last_name` ,
`records_email` ,
`records_phone_mobile` ,
`records_phone_work`,
`records_phone_home`,
`records_date_created` ,
`records_date_created_user_id` ,
`records_date_updated` ,
`records_date_updated_user_id` ,
`records_status`
)
VALUES (
NULL,
'$records_school_id_sql',
'$records_first_name_sql',
'$records_last_name_sql',
'$records_email_sql',
'$records_phone_mobile_sql',
'$records_phone_work_sql',
'$records_phone_home_sql',
$records_date_created_sql,
'$records_date_created_user_id_sql',
$records_date_updated_sql,
'$records_date_updated_user_id_sql',
'$records_status_sql'
);
";

$exec_qry_user_insert= mysql_query($qry_user_insert, $db) or die(mysql_error());

$student_id_sql = mysql_insert_id();


$reg_insert['record']=true;

$_REQUEST[id]=$record_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_records_update.inc';
?>