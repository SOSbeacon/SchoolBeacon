<?
$students_school_id_sql = trim(addslashes($form_r[students_school_id]));
$students_label_id_sql = trim(addslashes($form_r[students_label_id]));
$students_first_name_sql = trim(addslashes($form_r[students_first_name]));
$students_last_name_sql = trim(addslashes($form_r[students_last_name]));
$students_dob_sql = trim(addslashes($form_r[students_dob]));
$students_gender_sql = trim(addslashes($form_r[students_gender]));
$students_date_created_sql = 'NOW()';
$students_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$students_date_updated_sql = 'NOW()';
$students_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$students_status_sql = trim(addslashes($form_r[students_status]));

$qry_user_insert = "INSERT INTO `softwebs_sosbeacon`.`sos_students` (
`id` ,
`students_school_id` ,
`students_label_id` ,
`students_first_name` ,
`students_last_name` ,
`students_dob` ,
`students_gender` ,
`students_date_created` ,
`students_date_created_user_id` ,
`students_date_updated` ,
`students_date_updated_user_id` ,
`students_status`
)
VALUES (
NULL,
'$students_school_id_sql',
'$students_label_id_sql',
'$students_first_name_sql',
'$students_last_name_sql',
'$students_dob_sql',
'$students_gender_sql',
$students_date_created_sql,
'$students_date_created_user_id_sql',
$students_date_updated_sql,
'$students_date_updated_user_id_sql',
'$students_status_sql'
);
";

$exec_qry_user_insert= mysql_query($qry_user_insert, $db) or die(mysql_error());

$student_id_sql = mysql_insert_id();


$reg_insert['student']=true;

$_REQUEST[id]=$student_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_students_update.inc';
?>